<?php
require_once __DIR__ . '/../../../../config/database.php';
require_once __DIR__ . '/../../../../config/funciones.php';

header('Content-Type: application/json; charset=utf-8');

// Texto buscado
$term = trim($_GET['term'] ?? '');

if (strlen($term) < 2) {
    echo json_encode([]);
    exit;
}

// Consulta unificada usando la vista adoptantes_all
$stmt = $pdo->prepare("
    SELECT 
        id,
        nombre_completo,
        origen
    FROM adoptantes_all
    WHERE nombre_completo LIKE ?
       OR apellidos LIKE ?
    ORDER BY nombre_completo ASC
    LIMIT 20
");

$like = "%$term%";
$stmt->execute([$like, $like]);

$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Devolver JSON
echo json_encode($resultados);
exit;
