<?php
// ==========================================
//  WEBHOOK PAYPAL – APADRINAMIENTOS
// ==========================================

require_once __DIR__ . '/../../../config/database.php';

// Leer el cuerpo de la petición
$body = file_get_contents("php://input");
$data = json_decode($body, true);

// Guardar log para depuración
file_put_contents(__DIR__ . "/paypal_webhook_log.txt", $body . "\n\n", FILE_APPEND);

// Si no hay datos → salir
if (!$data || !isset($data['event_type'])) {
    http_response_code(400);
    exit("Invalid payload");
}

$event_type = $data['event_type'];
$resource   = $data['resource'] ?? [];

// ==========================================
//  1. PROCESAR SUBSCRIPCIÓN ACTIVADA
// ==========================================
if ($event_type === "BILLING.SUBSCRIPTION.ACTIVATED") {

    // Extraer custom_id → "temp_17_animal_4"
    $custom_id = $resource['custom_id'] ?? null;

    if (!$custom_id) {
        http_response_code(200);
        exit("No custom_id");
    }

    // Parsear custom_id
    // Formato: temp_17_animal_4
    preg_match('/temp_(\d+)_animal_(\d+)/', $custom_id, $m);

    if (!$m) {
        http_response_code(200);
        exit("Invalid custom_id format");
    }

    $temp_id   = (int)$m[1];
    $animal_id = (int)$m[2];

    // Obtener datos temporales
    $sql = "SELECT * FROM sponsors_temp WHERE id = ? AND estado = 'pendiente'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$temp_id]);
    $temp = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$temp) {
        http_response_code(200);
        exit("Temp sponsor not found or already processed");
    }

    // Datos del suscriptor desde PayPal
    $paypal_sub_id = $resource['id'];
    $paypal_email  = $resource['subscriber']['email_address'] ?? null;
    $paypal_name   = trim(($resource['subscriber']['name']['given_name'] ?? '') . ' ' . ($resource['subscriber']['name']['surname'] ?? ''));

    // ==========================================
    // 2. CREAR O RECUPERAR PADRINO DEFINITIVO
    // ==========================================
    $sql = "SELECT id FROM sponsors WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$temp['email']]);
    $sponsor = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($sponsor) {
        $sponsor_id = $sponsor['id'];
    } else {
        $sql = "INSERT INTO sponsors (nombre_apellidos, email, telefono, direccion, mensaje)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $temp['nombre_apellidos'],
            $temp['email'],
            $temp['telefono'],
            $temp['direccion'],
            $temp['mensaje']
        ]);

        $sponsor_id = $pdo->lastInsertId();
    }

    // ==========================================
    // 3. CREAR RELACIÓN PADRINO–ANIMAL
    // ==========================================
    $sql = "INSERT INTO sponsors_animals (sponsor_id, animal_id, paypal_subscription_id, estado)
            VALUES (?, ?, ?, 'activo')";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$sponsor_id, $animal_id, $paypal_sub_id]);

    $relation_id = $pdo->lastInsertId();

    // ==========================================
    // 4. REGISTRAR PAGO INICIAL
    // ==========================================
    $amount = $resource['billing_info']['last_payment']['amount']['value'] ?? 0;
    $currency = $resource['billing_info']['last_payment']['amount']['currency_code'] ?? 'EUR';
    $sale_id = $resource['billing_info']['last_payment']['id'] ?? null;

    $sql = "INSERT INTO sponsor_payments (sponsor_id, relation_id, subscription_id, amount, currency, paypal_sale_id)
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $sponsor_id,
        $relation_id,
        $paypal_sub_id,
        $amount,
        $currency,
        $sale_id
    ]);

    // ==========================================
    // 5. MARCAR TEMPORAL COMO CONFIRMADO
    // ==========================================
    $sql = "UPDATE sponsors_temp SET estado='confirmado', paypal_subscription_id=? WHERE id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$paypal_sub_id, $temp_id]);

    // ==========================================
    // 6. ACTUALIZAR CONTADOR DE PADRINOS
    // ==========================================
    $sql = "UPDATE animals_sponsor
            SET updated_at = NOW()
            WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$animal_id]);

    http_response_code(200);
    exit("Subscription activated processed");
}

// ==========================================
//  7. PROCESAR CANCELACIÓN
// ==========================================
if ($event_type === "BILLING.SUBSCRIPTION.CANCELLED") {

    $paypal_sub_id = $resource['id'];

    $sql = "UPDATE sponsors_animals SET estado='cancelado', fecha_cancelacion=NOW()
            WHERE paypal_subscription_id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$paypal_sub_id]);

    http_response_code(200);
    exit("Subscription cancelled processed");
}

// ==========================================
//  8. PROCESAR PAGOS MENSUALES
// ==========================================
if ($event_type === "PAYMENT.SALE.COMPLETED") {

    $paypal_sub_id = $resource['billing_agreement_id'] ?? null;
    $amount = $resource['amount']['total'] ?? 0;
    $currency = $resource['amount']['currency'] ?? 'EUR';
    $sale_id = $resource['id'] ?? null;

    if ($paypal_sub_id) {

        // Obtener relación
        $sql = "SELECT sponsor_id, id AS relation_id FROM sponsors_animals WHERE paypal_subscription_id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$paypal_sub_id]);
        $rel = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($rel) {
            $sql = "INSERT INTO sponsor_payments (sponsor_id, relation_id, subscription_id, amount, currency, paypal_sale_id)
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $rel['sponsor_id'],
                $rel['relation_id'],
                $paypal_sub_id,
                $amount,
                $currency,
                $sale_id
            ]);
        }
    }

    http_response_code(200);
    exit("Payment processed");
}

// ==========================================
//  9. OTROS EVENTOS
// ==========================================
http_response_code(200);
echo "Event ignored: $event_type";
