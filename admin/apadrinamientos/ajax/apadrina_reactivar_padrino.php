<?php
require_once __DIR__ . '/../../../config/database.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = intval($data['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(['ok' => false, 'error' => 'ID inválido']);
    exit;
}

try {
    // Reactivar: marcar activo y limpiar fecha_cancelacion
    $stmt = $pdo->prepare("
        UPDATE sponsors_animals
        SET estado = 'activo',
            fecha_cancelacion = NULL
        WHERE id = ? AND estado = 'cancelado'
    ");
    $stmt->execute([$id]);

    if ($stmt->rowCount() === 0) {
        echo json_encode(['ok' => false, 'error' => 'No se pudo reactivar (ya activo o no existe)']);
    } else {
        echo json_encode(['ok' => true]);
    }
} catch (PDOException $e) {
    echo json_encode(['ok' => false, 'error' => 'Error en la base de datos']);
}