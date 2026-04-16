<?php
require_once __DIR__ . '/../../../../config/database.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$id = intval($input['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(['ok' => false, 'error' => 'ID inválido']);
    exit;
}

try {
    // 1) Comprobar que existe el padrino
    $stmt = $pdo->prepare("SELECT * FROM sponsors WHERE id = ?");
    $stmt->execute([$id]);
    $p = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$p) {
        echo json_encode(['ok' => false, 'error' => 'Padrino no encontrado']);
        exit;
    }

    // 2) Comprobar si tiene apadrinamientos activos
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM sponsors_animals WHERE sponsor_id = ? AND estado = 'activo'");
    $stmt->execute([$id]);
    $activos = (int)$stmt->fetchColumn();

    if ($activos > 0) {
        echo json_encode(['ok' => false, 'error' => 'El padrino tiene apadrinamientos activos. Cancela las relaciones antes de eliminar.']);
        exit;
    }

    // 3) Insertar en tabla de respaldo (crear si no existe)
    $pdo->beginTransaction();

    // Insertar copia en sponsors_deleted (datos JSON con todo por si hace falta)
    $stmt = $pdo->prepare("
        INSERT INTO sponsors_deleted
            (id, nombre_apellidos, email, telefono, direccion, ciudad, provincia, codigo_postal, pais, mensaje, fecha_registro, datos_json)
        VALUES
            (:id, :nombre_apellidos, :email, :telefono, :direccion, :ciudad, :provincia, :codigo_postal, :pais, :mensaje, :fecha_registro, :datos_json)
        ON DUPLICATE KEY UPDATE fecha_eliminacion = NOW(), datos_json = :datos_json_upd
    ");

    $datos_json = json_encode($p, JSON_UNESCAPED_UNICODE);

    $stmt->execute([
        ':id' => $p['id'],
        ':nombre_apellidos' => $p['nombre_apellidos'],
        ':email' => $p['email'],
        ':telefono' => $p['telefono'],
        ':direccion' => $p['direccion'],
        ':ciudad' => $p['ciudad'],
        ':provincia' => $p['provincia'],
        ':codigo_postal' => $p['codigo_postal'],
        ':pais' => $p['pais'],
        ':mensaje' => $p['mensaje'],
        ':fecha_registro' => $p['fecha_registro'],
        ':datos_json' => $datos_json,
        ':datos_json_upd' => $datos_json
    ]);

    // 4) Borrar el registro original (eliminación física)
    $stmt = $pdo->prepare("DELETE FROM sponsors WHERE id = ?");
    $stmt->execute([$id]);

    // 5) Opcional: también podríamos borrar relaciones huérfanas (si existen) o marcarlas
    // En este flujo asumimos que no hay relaciones activas (comprobado), pero puede haber relaciones canceladas.
    // Decidimos mantener las relaciones históricas o eliminarlas según política. Aquí las mantenemos.

    $pdo->commit();

    echo json_encode(['ok' => true]);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(['ok' => false, 'error' => 'Error en la base de datos']);
}