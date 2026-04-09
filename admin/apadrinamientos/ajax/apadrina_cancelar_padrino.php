<?php
require_once __DIR__ . '/../../../config/database.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = intval($data['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(['ok' => false]);
    exit;
}

$stmt = $pdo->prepare("
    UPDATE sponsors_animals
    SET estado = 'cancelado',
        fecha_cancelacion = NOW()
    WHERE id = ?
");
$stmt->execute([$id]);

echo json_encode(['ok' => true]);