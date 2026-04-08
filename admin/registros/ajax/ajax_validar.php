<?php
require_once '../../../config/database.php';

$tipo = $_GET['tipo'] ?? '';
$nombre = trim($_GET['nombre'] ?? '');
$especie_id = intval($_GET['especie_id'] ?? 0);

$response = ['existe' => false];

// Validar especie
if ($tipo === 'especie' && $nombre !== '') {
    $stmt = $pdo->prepare("SELECT id FROM especies_animales WHERE nombre = ?");
    $stmt->execute([$nombre]);
    $response['existe'] = $stmt->fetch() ? true : false;
}

// Validar raza dentro de una especie
if ($tipo === 'raza' && $nombre !== '' && $especie_id > 0) {
    $stmt = $pdo->prepare("SELECT id FROM razas_animales WHERE nombre = ? AND especie_id = ?");
    $stmt->execute([$nombre, $especie_id]);
    $response['existe'] = $stmt->fetch() ? true : false;
}

echo json_encode($response);