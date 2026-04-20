<?php
require_once __DIR__ . '/../config/database.php';

$temp_id = $_POST['temp_id'] ?? null;

if ($temp_id) {
    $sql = "UPDATE sponsors_temp SET estado='cancelado' WHERE id=? AND estado='pendiente'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$temp_id]);
}

echo json_encode(["ok" => true]);
