<?php
session_start();
require_once __DIR__ . '/../../../config/database.php';

header('Content-Type: application/json');

// Validar método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
    exit;
}

// Validar parámetro
$idFormulario = isset($_POST['id_formulario']) ? (int)$_POST['id_formulario'] : 0;

if ($idFormulario <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'ID de formulario inválido']);
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Obtener datos del formulario
    $sql = "SELECT * FROM adoptantes_formulario WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $idFormulario]);
    $form = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$form) {
        throw new Exception("Formulario no encontrado");
    }

    if ((int)$form['procesado'] === 1) {
        throw new Exception("El formulario ya fue procesado");
    }

    if (!isset($form['animal_id'])) {
        throw new Exception("El formulario no contiene el ID del animal");
    }

    // 2. Separar nombre y apellidos
    $partes = explode(' ', trim($form['nombre_completo']), 2);
    $nombre = $partes[0];
    $apellidos = $partes[1] ?? '';

    // 3. Crear adoptante real
    $sqlInsert = "
        INSERT INTO adoptantes (
            nombre, apellidos, telefono, email, direccion, ciudad, provincia,
            codigo_postal, fecha_creacion
        ) VALUES (
            :nombre, :apellidos, :telefono, :email, :direccion, :ciudad, :provincia,
            :codigo_postal, NOW()
        )
    ";

    $stmt = $pdo->prepare($sqlInsert);
    $stmt->execute([
        ':nombre'        => $nombre,
        ':apellidos'     => $apellidos,
        ':telefono'      => $form['telefono'],
        ':email'         => $form['email'],
        ':direccion'     => $form['direccion'],
        ':ciudad'        => $form['ciudad'],
        ':provincia'     => $form['provincia'],
        ':codigo_postal' => $form['codigo_postal']
    ]);

    $idAdoptante = (int)$pdo->lastInsertId();

    // 4. Buscar adopción pendiente
    $sqlBuscar = "
        SELECT id 
        FROM adopciones
        WHERE id_animal = :idAnimal
          AND id_adoptante IS NULL
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sqlBuscar);
    $stmt->execute([':idAnimal' => $form['animal_id']]);
    $adopcion = $stmt->fetchColumn();

    if ($adopcion) {

        // 4A. Actualizar adopción existente
        $sqlUpdate = "
            UPDATE adopciones
            SET id_adoptante = :idAdoptante,
                estado = 'en_proceso'
            WHERE id = :id
        ";

        $stmt = $pdo->prepare($sqlUpdate);
        $stmt->execute([
            ':idAdoptante' => $idAdoptante,
            ':id'          => $adopcion
        ]);
    } else {

        // 4B. Crear adopción nueva
        $sqlInsertAdop = "
            INSERT INTO adopciones (id_animal, id_adoptante, fecha_adopcion, estado, notas)
            VALUES (:idAnimal, :idAdoptante, CURDATE(), 'en_proceso', 'Adopción creada al activar formulario')
        ";

        $stmt = $pdo->prepare($sqlInsertAdop);
        $stmt->execute([
            ':idAnimal'    => $form['animal_id'],
            ':idAdoptante' => $idAdoptante
        ]);

        $adopcion = (int)$pdo->lastInsertId();
    }

    // 5. Marcar formulario como procesado
    $sqlProcesado = "UPDATE adoptantes_formulario SET procesado = 1 WHERE id = :id";
    $stmt = $pdo->prepare($sqlProcesado);
    $stmt->execute([':id' => $idFormulario]);

    // 6. Marcar animal como NO adoptable
    $sqlAnimal = "
        UPDATE animales
        SET adoptable = 0
        WHERE id = :idAnimal
    ";

    $stmt = $pdo->prepare($sqlAnimal);
    $stmt->execute([':idAnimal' => $form['animal_id']]);

    $pdo->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'Adoptante convertido correctamente',
        'id_adoptante' => $idAdoptante
    ]);
    exit;
} catch (Exception $e) {

    $pdo->rollBack();

    echo json_encode([
        'status' => 'error',
        'message' => 'Error al convertir adoptante',
        'debug' => $e->getMessage()
    ]);
    exit;
}
