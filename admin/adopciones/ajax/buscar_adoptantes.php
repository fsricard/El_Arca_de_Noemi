<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../config/funciones.php';

// Seguridad básica: solo permitir AJAX
header('Content-Type: application/json; charset=utf-8');

// Obtener texto buscado
$q = trim($_GET['q'] ?? '');

if (strlen($q) < 2) {
    echo json_encode([]);
    exit;
}

// Consulta
$stmt = $pdo->prepare("
    SELECT id,
           CONCAT(nombre, ' ', apellidos) AS nombre_completo
    FROM adoptantes
    WHERE nombre LIKE ? 
       OR apellidos LIKE ?
    ORDER BY nombre, apellidos
    LIMIT 20
");

$like = "%$q%";
$stmt->execute([$like, $like]);

$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Devolver JSON
echo json_encode($resultados);
exit;