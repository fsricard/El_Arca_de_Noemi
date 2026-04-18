<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../../config/database.php';

// Solo admins
if (!isLoggedIn()) {
    die("No autorizado");
}

$subscription_id = $_GET['id'] ?? null;

if (!$subscription_id) {
    die("Falta el ID de suscripción");
}

// Credenciales Sandbox
$client_id = "AdwHUt_L8WjpXKBboVmo0XtPvD8sr5CwaAP2vgHMapNbyejg80tO4nU9WyBp29jAJ5qKZS4BgcD5iFBo";
$secret     = "ELedpbNLERvzkXN1Os6XE9nkoFIbNRXf7547e9ACPke8vc2Mc0MF6_EzSZmfBmMXPLJtCPDG13Y2huhC";

// Obtener token OAuth2
$ch = curl_init("https://api-m.sandbox.paypal.com/v1/oauth2/token");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Accept: application/json",
    "Accept-Language: en_US"
]);
curl_setopt($ch, CURLOPT_USERPWD, "$client_id:$secret");
curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$token_response = curl_exec($ch);
curl_close($ch);

$token_data = json_decode($token_response, true);
$access_token = $token_data['access_token'] ?? null;

if (!$access_token) {
    die("Error obteniendo token PayPal");
}

// Cancelar suscripción
$ch = curl_init("https://api-m.sandbox.paypal.com/v1/billing/subscriptions/$subscription_id/cancel");

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $access_token"
]);

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    "reason" => "Cancelado desde el panel de administración"
]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Si PayPal lo canceló correctamente
if ($http_code == 204) {

    // Actualizar BD
    $stmt = $pdo->prepare("
        UPDATE sponsors_animals
        SET estado = 'cancelado', fecha_cancelacion = NOW()
        WHERE paypal_subscription_id = ?
    ");
    $stmt->execute([$subscription_id]);

    echo "Suscripción cancelada correctamente.";
} else {
    echo "Error cancelando en PayPal: $response";
}
