<?php
require_once __DIR__ . '/../../../../config/database.php';

header('Content-Type: application/json');

// Validar método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
    exit;
}

// Validar ID
$idFormulario = isset($_POST['id_formulario']) ? (int)$_POST['id_formulario'] : 0;

if ($idFormulario <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'ID de formulario inválido']);
    exit;
}

// Helper para checkboxes
function checkbox($name)
{
    return isset($_POST[$name]) ? 1 : 0;
}

try {
    $pdo->beginTransaction();

    // Campos de texto
    $campos = [
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

    foreach ($campos as $c) {
        $data[$c] = trim($_POST[$c] ?? '');
    }

    // Checkboxes
    $data['familia_de_acuerdo']          = checkbox('familia_de_acuerdo');
    $data['ninos_tuvieron_animales']     = checkbox('ninos_tuvieron_animales');
    $data['capacidad_economica']         = checkbox('capacidad_economica');
    $data['asumir_gastos_vet']           = checkbox('asumir_gastos_vet');
    $data['ha_tenido_animales']          = checkbox('ha_tenido_animales');
    $data['chip_esterilizados']          = checkbox('chip_esterilizados');
    $data['vacunas_en_regla']            = checkbox('vacunas_en_regla');
    $data['vivienda_propiedad']          = checkbox('vivienda_propiedad');
    $data['permite_animales_en_alquiler'] = checkbox('permite_animales_en_alquiler');
    $data['conoce_condiciones']          = checkbox('conoce_condiciones');

    // Construir SET dinámico
    $set = [];
    foreach ($data as $campo => $valor) {
        $set[] = "$campo = :$campo";
    }

    $sql = "
        UPDATE adoptantes_formulario
        SET " . implode(", ", $set) . "
        WHERE id = :id
    ";

    $stmt = $pdo->prepare($sql);

    // Añadir ID al array de datos
    $data['id'] = $idFormulario;

    $stmt->execute($data);

    $pdo->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'Formulario actualizado correctamente'
    ]);
    exit;
} catch (Exception $e) {

    $pdo->rollBack();

    echo json_encode([
        'status' => 'error',
        'message' => 'Error al guardar los cambios',
        'debug' => $e->getMessage()
    ]);
    exit;
}
