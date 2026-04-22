<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/modelo_animales.php';

session_start();

// Comprobar método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido');
}

// Validar CSRF
if (empty($_POST['csrf_token']) || empty($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    exit('Token CSRF inválido');
}

// Helper para checkboxes
function checkboxToBool(string $name): int
{
    return isset($_POST[$name]) && $_POST[$name] === '1' ? 1 : 0;
}

// Recoger datos básicos
$idAnimal = isset($_POST['animal_id']) ? (int)$_POST['animal_id'] : 0;

if ($idAnimal <= 0) {
    exit('Animal no válido');
}

$animal = getAnimal($idAnimal);
if (!$animal) {
    exit('Animal no encontrado');
}

// Datos personales
$nombre_completo   = trim($_POST['nombre_completo'] ?? '');
$dni_pasaporte     = trim($_POST['dni_pasaporte'] ?? '');
$edad              = $_POST['edad'] !== '' ? (int)$_POST['edad'] : null;
$direccion         = trim($_POST['direccion'] ?? '');
$ciudad            = trim($_POST['ciudad'] ?? '');
$codigo_postal     = trim($_POST['codigo_postal'] ?? '');
$provincia         = trim($_POST['provincia'] ?? '');
$telefono          = trim($_POST['telefono'] ?? '');
$email             = trim($_POST['email'] ?? '');

// Animal y motivos
$animal_nombre     = trim($_POST['animal_nombre'] ?? '');
$motivos_adopcion  = trim($_POST['motivos_adopcion'] ?? '');

// Entorno familiar
$personas_en_casa          = trim($_POST['personas_en_casa'] ?? '');
$familia_de_acuerdo        = checkboxToBool('familia_de_acuerdo');
$responsable_principal     = trim($_POST['responsable_principal'] ?? '');
$ninos_tuvieron_animales   = checkboxToBool('ninos_tuvieron_animales');
$convivencia_ninos_opinion = trim($_POST['convivencia_ninos_opinion'] ?? '');
$plan_familia_impacto      = trim($_POST['plan_familia_impacto'] ?? '');
$alergias_en_casa          = trim($_POST['alergias_en_casa'] ?? '');
$capacidad_economica       = checkboxToBool('capacidad_economica');
$asumir_gastos_vet         = checkboxToBool('asumir_gastos_vet');

// Antecedentes con animales
$ha_tenido_animales      = checkboxToBool('ha_tenido_animales');
$historia_animales_previos = trim($_POST['historia_animales_previos'] ?? '');
$otros_animales          = trim($_POST['otros_animales'] ?? '');
$chip_esterilizados      = checkboxToBool('chip_esterilizados');
$vacunas_en_regla        = checkboxToBool('vacunas_en_regla');

// Vivienda y entorno
$tipo_vivienda               = trim($_POST['tipo_vivienda'] ?? '');
$vivienda_propiedad          = checkboxToBool('vivienda_propiedad');
$permite_animales_en_alquiler = checkboxToBool('permite_animales_en_alquiler');
$patio_jardin_medidas        = trim($_POST['patio_jardin_medidas'] ?? '');
$interior_o_exterior         = trim($_POST['interior_o_exterior'] ?? '');

// Estado laboral y dedicación
$profesion_situacion = trim($_POST['profesion_situacion'] ?? '');
$quien_asume_gastos  = trim($_POST['quien_asume_gastos'] ?? '');
$tiempo_pasear       = trim($_POST['tiempo_pasear'] ?? '');
$horas_solo          = trim($_POST['horas_solo'] ?? '');
$lugares_paseo       = trim($_POST['lugares_paseo'] ?? '');
$mudanza_poblacion   = trim($_POST['mudanza_poblacion'] ?? '');
$mudanza_pais        = trim($_POST['mudanza_pais'] ?? '');
$vacaciones_cuidado  = trim($_POST['vacaciones_cuidado'] ?? '');

// Otra información
$por_que_adoptar   = trim($_POST['por_que_adoptar'] ?? '');
$tiempo_busqueda   = trim($_POST['tiempo_busqueda'] ?? '');
$como_conocio      = trim($_POST['como_conocio'] ?? '');
$conoce_condiciones = checkboxToBool('conoce_condiciones');
$firma_nombre_dni  = trim($_POST['firma_nombre_dni'] ?? '');

// Empezar transacción
$pdo->beginTransaction();

try {

    // Crear/adaptar adoptante en tabla "adoptantes"
    $sqlAdoptante = "
        INSERT INTO adoptantes (nombre, telefono, email, fecha_creacion)
        VALUES (:nombre, :telefono, :email, NOW())
    ";
    $stmt = $pdo->prepare($sqlAdoptante);
    $stmt->execute([
        ':nombre' => $nombre_completo,
        ':telefono'        => $telefono,
        ':email'           => $email,
    ]);
    $idAdoptante = (int)$pdo->lastInsertId();

    // Insertar en adoptantes_formulario
    $sqlFormulario = "
        INSERT INTO adoptantes_formulario (
            adoptante_id,
            nombre_completo,
            dni_pasaporte,
            edad,
            direccion,
            ciudad,
            codigo_postal,
            provincia,
            telefono,
            email,
            animal_nombre,
            motivos_adopcion,
            personas_en_casa,
            familia_de_acuerdo,
            responsable_principal,
            ninos_tuvieron_animales,
            convivencia_ninos_opinion,
            plan_familia_impacto,
            alergias_en_casa,
            capacidad_economica,
            asumir_gastos_vet,
            ha_tenido_animales,
            historia_animales_previos,
            otros_animales,
            chip_esterilizados,
            vacunas_en_regla,
            tipo_vivienda,
            vivienda_propiedad,
            permite_animales_en_alquiler,
            patio_jardin_medidas,
            interior_o_exterior,
            profesion_situacion,
            quien_asume_gastos,
            tiempo_pasear,
            horas_solo,
            lugares_paseo,
            mudanza_poblacion,
            mudanza_pais,
            vacaciones_cuidado,
            por_que_adoptar,
            tiempo_busqueda,
            como_conocio,
            conoce_condiciones,
            firma_nombre_dni
        ) VALUES (
            :adoptante_id,
            :nombre_completo,
            :dni_pasaporte,
            :edad,
            :direccion,
            :ciudad,
            :codigo_postal,
            :provincia,
            :telefono,
            :email,
            :animal_nombre,
            :motivos_adopcion,
            :personas_en_casa,
            :familia_de_acuerdo,
            :responsable_principal,
            :ninos_tuvieron_animales,
            :convivencia_ninos_opinion,
            :plan_familia_impacto,
            :alergias_en_casa,
            :capacidad_economica,
            :asumir_gastos_vet,
            :ha_tenido_animales,
            :historia_animales_previos,
            :otros_animales,
            :chip_esterilizados,
            :vacunas_en_regla,
            :tipo_vivienda,
            :vivienda_propiedad,
            :permite_animales_en_alquiler,
            :patio_jardin_medidas,
            :interior_o_exterior,
            :profesion_situacion,
            :quien_asume_gastos,
            :tiempo_pasear,
            :horas_solo,
            :lugares_paseo,
            :mudanza_poblacion,
            :mudanza_pais,
            :vacaciones_cuidado,
            :por_que_adoptar,
            :tiempo_busqueda,
            :como_conocio,
            :conoce_condiciones,
            :firma_nombre_dni
        )
    ";

    $stmt = $pdo->prepare($sqlFormulario);
    $stmt->execute([
        ':adoptante_id'             => $idAdoptante,
        ':nombre_completo'          => $nombre_completo,
        ':dni_pasaporte'            => $dni_pasaporte,
        ':edad'                     => $edad,
        ':direccion'                => $direccion,
        ':ciudad'                   => $ciudad,
        ':codigo_postal'            => $codigo_postal,
        ':provincia'                => $provincia,
        ':telefono'                 => $telefono,
        ':email'                    => $email,
        ':animal_nombre'            => $animal_nombre,
        ':motivos_adopcion'         => $motivos_adopcion,
        ':personas_en_casa'         => $personas_en_casa,
        ':familia_de_acuerdo'       => $familia_de_acuerdo,
        ':responsable_principal'    => $responsable_principal,
        ':ninos_tuvieron_animales'  => $ninos_tuvieron_animales,
        ':convivencia_ninos_opinion' => $convivencia_ninos_opinion,
        ':plan_familia_impacto'     => $plan_familia_impacto,
        ':alergias_en_casa'         => $alergias_en_casa,
        ':capacidad_economica'      => $capacidad_economica,
        ':asumir_gastos_vet'        => $asumir_gastos_vet,
        ':ha_tenido_animales'       => $ha_tenido_animales,
        ':historia_animales_previos' => $historia_animales_previos,
        ':otros_animales'           => $otros_animales,
        ':chip_esterilizados'       => $chip_esterilizados,
        ':vacunas_en_regla'         => $vacunas_en_regla,
        ':tipo_vivienda'            => $tipo_vivienda,
        ':vivienda_propiedad'       => $vivienda_propiedad,
        ':permite_animales_en_alquiler' => $permite_animales_en_alquiler,
        ':patio_jardin_medidas'     => $patio_jardin_medidas,
        ':interior_o_exterior'      => $interior_o_exterior,
        ':profesion_situacion'      => $profesion_situacion,
        ':quien_asume_gastos'       => $quien_asume_gastos,
        ':tiempo_pasear'            => $tiempo_pasear,
        ':horas_solo'               => $horas_solo,
        ':lugares_paseo'            => $lugares_paseo,
        ':mudanza_poblacion'        => $mudanza_poblacion,
        ':mudanza_pais'             => $mudanza_pais,
        ':vacaciones_cuidado'       => $vacaciones_cuidado,
        ':por_que_adoptar'          => $por_que_adoptar,
        ':tiempo_busqueda'          => $tiempo_busqueda,
        ':como_conocio'             => $como_conocio,
        ':conoce_condiciones'       => $conoce_condiciones,
        ':firma_nombre_dni'         => $firma_nombre_dni,
    ]);

    // Crear adopción en estado 'pendiente'
    $sqlAdopcion = "
        INSERT INTO adopciones (id_animal, id_adoptante, fecha_adopcion, estado, notas)
        VALUES (:id_animal, :id_adoptante, CURDATE(), 'pendiente', :notas)
    ";
    $stmt = $pdo->prepare($sqlAdopcion);
    $stmt->execute([
        ':id_animal'   => $idAnimal,
        ':id_adoptante' => $idAdoptante,
        ':notas'       => 'Solicitud de adopción vía formulario web',
    ]);

    // Importante: NO tocamos animales.adoptable (se queda en 1)

    $pdo->commit();

    echo json_encode(['status' => 'success']);
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error al procesar el formulario.']);
    exit;
}
