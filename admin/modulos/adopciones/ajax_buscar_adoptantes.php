<?php
require_once __DIR__ . '/../../../config/database.php';

$term = trim($_GET['term'] ?? '');

$stmt = $pdo->prepare("
    SELECT id, nombre, apellidos
    FROM adoptantes
    WHERE nombre LIKE ? OR apellidos LIKE ?
    ORDER BY nombre ASC
    LIMIT 10
");

$stmt->execute(["%$term%", "%$term%"]);

$resultado = [];

foreach ($stmt as $row) {
    $resultado[] = [
        'id' => $row['id'],
        'nombre_completo' => $row['nombre'] . ' ' . $row['apellidos']
    ];
}

header('Content-Type: application/json');
echo json_encode($resultado);