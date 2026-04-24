<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once 'modelo_animales.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Validar método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
    exit;
}

// Helper checkboxes
function checkboxToBool(string $name): int
{
    return isset($_POST[$name]) ? 1 : 0;
}

// ID animal
$idAnimal = isset($_POST['animal_id']) ? (int)$_POST['animal_id'] : 0;

if ($idAnimal <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Animal no válido']);
    exit;
}

$animal = getAnimal($idAnimal);
if (!$animal) {
    echo json_encode(['status' => 'error', 'message' => 'Animal no encontrado']);
    exit;
}

// Campos texto
$fields = [
    'nombre_completo',
    'dni_pasaporte',
    'edad',
    'direccion',
    'ciudad',
    'codigo_postal',
    'provincia',
    'telefono',
    'email',
    'animal_nombre',
    'motivos_adopcion',
    'personas_en_casa',
    'responsable_principal',
    'convivencia_ninos_opinion',
    'plan_familia_impacto',
    'alergias_en_casa',
    'historia_animales_previos',
    'otros_animales',
    'tipo_vivienda',
    'patio_jardin_medidas',
    'interior_o_exterior',
    'profesion_situacion',
    'quien_asume_gastos',
    'tiempo_pasear',
    'horas_solo',
    'lugares_paseo',
    'mudanza_poblacion',
    'mudanza_pais',
    'vacaciones_cuidado',
    'por_que_adoptar',
    'tiempo_busqueda',
    'como_conocio',
    'firma_nombre_dni'
];

$data = [];
foreach ($fields as $f) {
    $data[$f] = trim($_POST[$f] ?? '');
}

// Checkboxes
$checkboxes = [
    'familia_de_acuerdo',
    'ninos_tuvieron_animales',
    'capacidad_economica',
    'asumir_gastos_vet',
    'ha_tenido_animales',
    'chip_esterilizados',
    'vacunas_en_regla',
    'vivienda_propiedad',
    'permite_animales_en_alquiler',
    'conoce_condiciones'
];

foreach ($checkboxes as $c) {
    $data[$c] = checkboxToBool($c);
}

// Añadir animal_id
$data['animal_id'] = $idAnimal;

$pdo->beginTransaction();

try {

    // INSERT adoptantes_formulario
    $sql = "
        INSERT INTO adoptantes_formulario (
            nombre_completo, animal_id, dni_pasaporte, edad, direccion, ciudad,
            codigo_postal, provincia, telefono, email, animal_nombre, motivos_adopcion,
            personas_en_casa, familia_de_acuerdo, responsable_principal,
            ninos_tuvieron_animales, convivencia_ninos_opinion, plan_familia_impacto,
            alergias_en_casa, capacidad_economica, asumir_gastos_vet,
            ha_tenido_animales, historia_animales_previos, otros_animales,
            chip_esterilizados, vacunas_en_regla, tipo_vivienda, vivienda_propiedad,
            permite_animales_en_alquiler, patio_jardin_medidas, interior_o_exterior,
            profesion_situacion, quien_asume_gastos, tiempo_pasear, horas_solo,
            lugares_paseo, mudanza_poblacion, mudanza_pais, vacaciones_cuidado,
            por_que_adoptar, tiempo_busqueda, como_conocio, conoce_condiciones,
            firma_nombre_dni
        ) VALUES (
            :nombre_completo, :animal_id, :dni_pasaporte, :edad, :direccion, :ciudad,
            :codigo_postal, :provincia, :telefono, :email, :animal_nombre, :motivos_adopcion,
            :personas_en_casa, :familia_de_acuerdo, :responsable_principal,
            :ninos_tuvieron_animales, :convivencia_ninos_opinion, :plan_familia_impacto,
            :alergias_en_casa, :capacidad_economica, :asumir_gastos_vet,
            :ha_tenido_animales, :historia_animales_previos, :otros_animales,
            :chip_esterilizados, :vacunas_en_regla, :tipo_vivienda, :vivienda_propiedad,
            :permite_animales_en_alquiler, :patio_jardin_medidas, :interior_o_exterior,
            :profesion_situacion, :quien_asume_gastos, :tiempo_pasear, :horas_solo,
            :lugares_paseo, :mudanza_poblacion, :mudanza_pais, :vacaciones_cuidado,
            :por_que_adoptar, :tiempo_busqueda, :como_conocio, :conoce_condiciones,
            :firma_nombre_dni
        )
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);

    $pdo->commit();

    echo json_encode(['status' => 'success']);
    exit;
} catch (Exception $e) {

    $pdo->rollBack();

    echo json_encode([
        'status' => 'error',
        'message' => 'Error al procesar el formulario.',
        'debug' => $e->getMessage() // ← deja esto activado hasta que confirmemos que funciona
    ]);
    exit;
}
