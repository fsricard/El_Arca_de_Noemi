<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../../config/database.php';

header('Content-Type: application/json');

// Solo usuarios logueados pueden apadrinar
if (!isLoggedIn()) {
    echo json_encode(['ok' => false, 'error' => 'not_logged']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$subscription_id = $input['subscription_id'] ?? null;
$animal_id = intval($input['animal_id'] ?? 0);
$sponsor_id = $_SESSION['user_id'] ?? 0;

if (!$subscription_id || $animal_id <= 0 || $sponsor_id <= 0) {
    echo json_encode(['ok' => false, 'error' => 'invalid_data']);
    exit;
}

try {
    // Crear relación
    $stmt = $pdo->prepare("
        INSERT INTO sponsors_animals
        (sponsor_id, animal_id, estado, fecha_inicio, paypal_subscription_id)
        VALUES (?, ?, 'activo', NOW(), ?)
    ");
    $stmt->execute([$sponsor_id, $animal_id, $subscription_id]);

    echo json_encode(['ok' => true]);
} catch (PDOException $e) {
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}