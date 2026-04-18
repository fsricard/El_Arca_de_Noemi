<?php
require_once __DIR__ . '/../../../config/database.php';

// Leer evento
$body = file_get_contents('php://input');
$event = json_decode($body, true);

// Guardar log para depuración
file_put_contents(__DIR__ . '/paypal_webhook_log.txt', $body . "\n\n", FILE_APPEND);

$type = $event['event_type'] ?? '';

switch ($type) {

    /* -----------------------------------------
       PAGO MENSUAL COMPLETADO
    ----------------------------------------- */
    case 'PAYMENT.SALE.COMPLETED':

        $subscription_id = $event['resource']['billing_agreement_id'] ?? null;
        $sale_id = $event['resource']['id'] ?? null;
        $amount = $event['resource']['amount']['total'] ?? 0;
        $currency = $event['resource']['amount']['currency'] ?? 'EUR';

        if ($subscription_id) {

            // Buscar relación
            $stmt = $pdo->prepare("SELECT * FROM sponsors_animals WHERE paypal_subscription_id = ?");
            $stmt->execute([$subscription_id]);
            $rel = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($rel) {
                // Registrar pago
                $stmt = $pdo->prepare("
                    INSERT INTO sponsor_payments
                    (sponsor_id, relation_id, subscription_id, amount, currency, paypal_sale_id)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $rel['sponsor_id'],
                    $rel['id'],
                    $subscription_id,
                    $amount,
                    $currency,
                    $sale_id
                ]);
            }
        }
        break;

    /* -----------------------------------------
       SUSCRIPCIÓN CANCELADA
    ----------------------------------------- */
    case 'BILLING.SUBSCRIPTION.CANCELLED':

        $subscription_id = $event['resource']['id'] ?? null;

        if ($subscription_id) {
            $stmt = $pdo->prepare("
                UPDATE sponsors_animals
                SET estado = 'cancelado', fecha_cancelacion = NOW()
                WHERE paypal_subscription_id = ?
            ");
            $stmt->execute([$subscription_id]);
        }
        break;
}

http_response_code(200);
echo "OK";
