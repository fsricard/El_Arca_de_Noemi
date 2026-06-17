<?php
require_once __DIR__ . '/../../../../config/database.php';
require_once __DIR__ . '/../../../../config/funciones.php';

header('Content-Type: application/json; charset=utf-8');

// Texto buscado
$term = trim($_GET['q'] ?? '');

if (strlen($term) < 2) {
    echo json_encode([]);
    exit;
}

$like = "%$term%";

// Consulta unificada usando la vista adoptantes_all
$stmt = $pdo->prepare("
    SELECT 
        id,
        nombre_completo,
        origen
    FROM adoptantes_all
    WHERE nombre_completo COLLATE utf8mb4_unicode_ci LIKE ?
       OR COALESCE(apellidos, '') COLLATE utf8mb4_unicode_ci LIKE ?
    ORDER BY nombre_completo ASC
    LIMIT 20
");

$stmt->execute([$like, $like]);

$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Filtrar resultados con id válido (evita IDs NULL)
$resultados = array_filter($resultados, fn($r) => !empty($r['id']));

// Devolver JSON
echo json_encode(array_values($resultados));
exit;
