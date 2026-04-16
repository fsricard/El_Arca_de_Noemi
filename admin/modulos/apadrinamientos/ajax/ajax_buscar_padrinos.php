<?php
require_once __DIR__ . '/../../../../config/database.php';

$term = trim($_GET['term'] ?? '');
if ($term === '' || strlen($term) < 2) {
    echo json_encode([]);
    exit;
}

$like = "%{$term}%";
$stmt = $pdo->prepare("
    SELECT id, CONCAT(nombre_apellidos, ' — ', email) AS nombre_completo, email
    FROM sponsors
    WHERE nombre_apellidos LIKE ? OR email LIKE ?
    ORDER BY nombre_apellidos
    LIMIT 10
");
$stmt->execute([$like, $like]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($rows);