<?php
require_once __DIR__ . '/../../../config/database.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    http_response_code(400);
    echo "ID inválido";
    exit;
}

// Obtener padrino
$stmt = $pdo->prepare("SELECT * FROM sponsors WHERE id = ?");
$stmt->execute([$id]);
$p = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$p) {
    http_response_code(404);
    echo "Padrino no encontrado";
    exit;
}

// Cabeceras para descarga
$filename = 'padrino_' . $id . '_' . date('Ymd_His') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Abrir salida
$out = fopen('php://output', 'w');

// BOM para Excel (UTF-8)
fwrite($out, "\xEF\xBB\xBF");

// Cabecera CSV
fputcsv($out, ['Campo', 'Valor']);

// Campos principales
$campos = [
    'id' => 'ID',
    'nombre_apellidos' => 'Nombre y apellidos',
    'email' => 'Email',
    'telefono' => 'Teléfono',
    'direccion' => 'Dirección',
    'ciudad' => 'Ciudad',
    'provincia' => 'Provincia',
    'codigo_postal' => 'Código postal',
    'pais' => 'País',
    'mensaje' => 'Mensaje',
    'fecha_registro' => 'Fecha registro'
];

foreach ($campos as $k => $label) {
    fputcsv($out, [$label, $p[$k] ?? '']);
}

// Añadir apadrinamientos del padrino
fputcsv($out, []);
fputcsv($out, ['Apadrinamientos']);
fputcsv($out, ['ID relación','ID animal','Nombre animal','Estado','Fecha inicio','Fecha cancelación','Paypal subscription id']);

$stmt = $pdo->prepare("
    SELECT sa.id, sa.animal_id, a.nombre AS nombre_animal, sa.estado, sa.fecha_inicio, sa.fecha_cancelacion, sa.paypal_subscription_id
    FROM sponsors_animals sa
    LEFT JOIN animals_sponsor a ON sa.animal_id = a.id
    WHERE sa.sponsor_id = ?
    ORDER BY sa.fecha_inicio DESC
");
$stmt->execute([$id]);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($out, [
        $row['id'],
        $row['animal_id'],
        $row['nombre_animal'] ?? '',
        $row['estado'],
        $row['fecha_inicio'],
        $row['fecha_cancelacion'],
        $row['paypal_subscription_id'] ?? ''
    ]);
}

fclose($out);
exit;