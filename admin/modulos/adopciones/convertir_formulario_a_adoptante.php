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

    // 2. Crear adoptante real
    $sqlInsert = "
        INSERT INTO adoptantes (
            nombre_completo, telefono, email, direccion, ciudad, provincia,
            codigo_postal, fecha_creacion
        ) VALUES (
            :nombre_completo, :telefono, :email, :direccion, :ciudad, :provincia,
            :codigo_postal, NOW()
        )
    ";

    $stmt = $pdo->prepare($sqlInsert);
    $stmt->execute([
        ':nombre_completo' => $form['nombre_completo'],
        ':telefono'        => $form['telefono'],
        ':email'           => $form['email'],
        ':direccion'       => $form['direccion'],
        ':ciudad'          => $form['ciudad'],
        ':provincia'       => $form['provincia'],
        ':codigo_postal'   => $form['codigo_postal']
    ]);

    $idAdoptante = (int)$pdo->lastInsertId();

    // 3. Buscar adopción pendiente (antes creada automáticamente)
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

        // 3A. Si existe adopción pendiente → actualizarla
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

        // 3B. Si NO existe → crear adopción nueva
        $sqlInsertAdop = "
            INSERT INTO adopciones (id_animal, id_adoptante, fecha_adopcion, estado, notas)
            VALUES (:idAnimal, :idAdoptante, CURDATE(), 'en_proceso', 'Adopción creada al activar formulario')
        ";

        $stmt = $pdo->prepare($sqlInsertAdop);
        $stmt->execute([
            ':idAnimal'    => $form['animal_id'],
            ':idAdoptante' => $idAdoptante
        ]);
    }

    // 4. Marcar formulario como procesado
    $sqlProcesado = "UPDATE adoptantes_formulario SET procesado = 1 WHERE id = :id";
    $stmt = $pdo->prepare($sqlProcesado);
    $stmt->execute([':id' => $idFormulario]);

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
