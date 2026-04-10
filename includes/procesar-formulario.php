<?php
// includes/procesar-formulario.php
// Requisitos: 
// - config/database.php debe inicializar $pdo (PDO).
// - includes/PHPMailer (autoload o files) y includes/fpdf (fpdf.php) presentes.
// - Sesión activa para CSRF y mensajes flash.

session_start();
require_once __DIR__ . '/../config/database.php';

// PHPMailer (ajusta rutas si usas autoload)
require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';

// FPDF
require_once __DIR__ . '/fpdf/fpdf.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ---------- CONFIGURACIÓN (ajusta antes de desplegar) ----------
$ADMIN_EMAIL = 'noemi@tuasociacion.org';
$FROM_EMAIL = 'no-reply@tuasociacion.org';
$FROM_NAME  = 'El Arca de Noemí';

$SMTP_ENABLED = true;
$SMTP_HOST = 'smtp.tuservidor.com';
$SMTP_PORT = 587;
$SMTP_USER = 'smtp_user';
$SMTP_PASS = 'smtp_password';
$SMTP_SECURE = 'tls';

$REDIRECT_SUCCESS = '/gracias.php';
$REDIRECT_ERROR   = '/formulario-adoptante.php?id=' . urlencode($_POST['animal_id'] ?? '');

// Carpeta temporal para PDFs
$TMP_DIR = __DIR__ . '/../uploads/tmp';
if (!is_dir($TMP_DIR)) mkdir($TMP_DIR, 0755, true);

// ---------- FUNCIONES AUXILIARES ----------
function flash_errors(array $errors) {
    $_SESSION['form_errors'] = $errors;
}
function flash_success(string $msg) {
    $_SESSION['form_success'] = $msg;
}
function chk($name) {
    return isset($_POST[$name]) && ($_POST[$name] === '1' || $_POST[$name] === 'on' || $_POST[$name] === 'true') ? 1 : 0;
}
function sanitize_text($s) {
    if ($s === null) return null;
    $s = trim($s);
    return $s === '' ? null : $s;
}

// ---------- CSRF: comprobar token ----------
$token_post = $_POST['csrf_token'] ?? '';
$token_sess = $_SESSION['csrf_token'] ?? '';
if (empty($token_post) || empty($token_sess) || !hash_equals($token_sess, $token_post)) {
    flash_errors(['Token CSRF inválido. Por seguridad, recarga la página e inténtalo de nuevo.']);
    header('Location: ' . $REDIRECT_ERROR);
    exit;
}

// ---------- DEFINICIÓN DE CAMPOS Y SANEADO ----------
$fields = [
    'animal_id' => FILTER_VALIDATE_INT,
    'animal_nombre' => FILTER_SANITIZE_STRING,
    'nombre_completo' => FILTER_SANITIZE_STRING,
    'dni_pasaporte' => FILTER_SANITIZE_STRING,
    'edad' => FILTER_VALIDATE_INT,
    'direccion' => FILTER_UNSAFE_RAW,
    'ciudad' => FILTER_SANITIZE_STRING,
    'codigo_postal' => FILTER_SANITIZE_STRING,
    'provincia' => FILTER_SANITIZE_STRING,
    'telefono' => FILTER_SANITIZE_STRING,
    'email' => FILTER_VALIDATE_EMAIL,
    'motivos_adopcion' => FILTER_UNSAFE_RAW,
    'personas_en_casa' => FILTER_UNSAFE_RAW,
    'responsable_principal' => FILTER_SANITIZE_STRING,
    'convivencia_ninos_opinion' => FILTER_UNSAFE_RAW,
    'plan_familia_impacto' => FILTER_UNSAFE_RAW,
    'alergias_en_casa' => FILTER_UNSAFE_RAW,
    'patio_jardin_medidas' => FILTER_UNSAFE_RAW,
    'interior_o_exterior' => FILTER_SANITIZE_STRING,
    'profesion_situacion' => FILTER_SANITIZE_STRING,
    'quien_asume_gastos' => FILTER_SANITIZE_STRING,
    'tiempo_pasear' => FILTER_SANITIZE_STRING,
    'horas_solo' => FILTER_SANITIZE_STRING,
    'lugares_paseo' => FILTER_UNSAFE_RAW,
    'mudanza_poblacion' => FILTER_UNSAFE_RAW,
    'mudanza_pais' => FILTER_UNSAFE_RAW,
    'vacaciones_cuidado' => FILTER_UNSAFE_RAW,
    'por_que_adoptar' => FILTER_UNSAFE_RAW,
    'tiempo_busqueda' => FILTER_SANITIZE_STRING,
    'como_conocio' => FILTER_SANITIZE_STRING,
    'firma_nombre_dni' => FILTER_SANITIZE_STRING
];

$input = filter_input_array(INPUT_POST, $fields);

// Mapear checkboxes
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
foreach ($checkboxes as $cb) {
    $input[$cb] = chk($cb);
}

// Validaciones mínimas
$errors = [];
if (empty($input['nombre_completo'])) $errors[] = 'El nombre es obligatorio.';
if (!empty($_POST['email']) && $input['email'] === false) $errors[] = 'El email no tiene un formato válido.';
if (!empty($_POST['edad']) && $input['edad'] === false) $errors[] = 'La edad no es válida.';
if (empty($input['animal_id']) || $input['animal_id'] === false) $errors[] = 'Falta el identificador del animal.';

if (!empty($errors)) {
    flash_errors($errors);
    header('Location: ' . $REDIRECT_ERROR);
    exit;
}

// Normalizar algunos campos
foreach ($input as $k => $v) {
    if (is_string($v)) $input[$k] = sanitize_text($v);
}

// ---------- INSERCIÓN EN BD Y VINCULACIÓN ----------
try {
    // Buscar coincidencia en adoptantes por dni o email
    $adoptante_id = null;
    if (!empty($input['dni_pasaporte']) || !empty($input['email'])) {
        $sqlCheck = "SELECT id FROM adoptantes WHERE (dni_pasaporte = :dni OR email = :email) LIMIT 1";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->execute([
            ':dni' => $input['dni_pasaporte'] ?? '',
            ':email' => $input['email'] ?? ''
        ]);
        $row = $stmtCheck->fetch(PDO::FETCH_ASSOC);
        if ($row) $adoptante_id = (int)$row['id'];
    }

    $sql = "INSERT INTO adoptantes_formulario (
        adoptante_id, nombre_completo, dni_pasaporte, edad, direccion, ciudad, codigo_postal, provincia,
        telefono, email, animal_nombre, motivos_adopcion, personas_en_casa, familia_de_acuerdo,
        responsable_principal, ninos_tuvieron_animales, convivencia_ninos_opinion, plan_familia_impacto,
        alergias_en_casa, capacidad_economica, asumir_gastos_vet, ha_tenido_animales, historia_animales_previos,
        otros_animales, chip_esterilizados, vacunas_en_regla, tipo_vivienda, vivienda_propiedad,
        permite_animales_en_alquiler, patio_jardin_medidas, interior_o_exterior, profesion_situacion,
        quien_asume_gastos, tiempo_pasear, horas_solo, lugares_paseo, mudanza_poblacion, mudanza_pais,
        vacaciones_cuidado, por_que_adoptar, tiempo_busqueda, como_conocio, conoce_condiciones,
        firma_nombre_dni
    ) VALUES (
        :adoptante_id, :nombre_completo, :dni_pasaporte, :edad, :direccion, :ciudad, :codigo_postal, :provincia,
        :telefono, :email, :animal_nombre, :motivos_adopcion, :personas_en_casa, :familia_de_acuerdo,
        :responsable_principal, :ninos_tuvieron_animales, :convivencia_ninos_opinion, :plan_familia_impacto,
        :alergias_en_casa, :capacidad_economica, :asumir_gastos_vet, :ha_tenido_animales, :historia_animales_previos,
        :otros_animales, :chip_esterilizados, :vacunas_en_regla, :tipo_vivienda, :vivienda_propiedad,
        :permite_animales_en_alquiler, :patio_jardin_medidas, :interior_o_exterior, :profesion_situacion,
        :quien_asume_gastos, :tiempo_pasear, :horas_solo, :lugares_paseo, :mudanza_poblacion, :mudanza_pais,
        :vacaciones_cuidado, :por_que_adoptar, :tiempo_busqueda, :como_conocio, :conoce_condiciones,
        :firma_nombre_dni
    )";

    $stmt = $pdo->prepare($sql);

    $params = [
        ':adoptante_id' => $adoptante_id,
        ':nombre_completo' => $input['nombre_completo'] ?? null,
        ':dni_pasaporte' => $input['dni_pasaporte'] ?? null,
        ':edad' => $input['edad'] ?? null,
        ':direccion' => $input['direccion'] ?? null,
        ':ciudad' => $input['ciudad'] ?? null,
        ':codigo_postal' => $input['codigo_postal'] ?? null,
        ':provincia' => $input['provincia'] ?? null,
        ':telefono' => $input['telefono'] ?? null,
        ':email' => $input['email'] ?? null,
        ':animal_nombre' => $input['animal_nombre'] ?? null,
        ':motivos_adopcion' => $input['motivos_adopcion'] ?? null,
        ':personas_en_casa' => $input['personas_en_casa'] ?? null,
        ':familia_de_acuerdo' => $input['familia_de_acuerdo'],
        ':responsable_principal' => $input['responsable_principal'] ?? null,
        ':ninos_tuvieron_animales' => $input['ninos_tuvieron_animales'],
        ':convivencia_ninos_opinion' => $input['convivencia_ninos_opinion'] ?? null,
        ':plan_familia_impacto' => $input['plan_familia_impacto'] ?? null,
        ':alergias_en_casa' => $input['alergias_en_casa'] ?? null,
        ':capacidad_economica' => $input['capacidad_economica'],
        ':asumir_gastos_vet' => $input['asumir_gastos_vet'],
        ':ha_tenido_animales' => $input['ha_tenido_animales'],
        ':historia_animales_previos' => $input['historia_animales_previos'] ?? null,
        ':otros_animales' => $input['otros_animales'] ?? null,
        ':chip_esterilizados' => $input['chip_esterilizados'],
        ':vacunas_en_regla' => $input['vacunas_en_regla'],
        ':tipo_vivienda' => $input['tipo_vivienda'] ?? null,
        ':vivienda_propiedad' => $input['vivienda_propiedad'],
        ':permite_animales_en_alquiler' => $input['permite_animales_en_alquiler'],
        ':patio_jardin_medidas' => $input['patio_jardin_medidas'] ?? null,
        ':interior_o_exterior' => $input['interior_o_exterior'] ?? null,
        ':profesion_situacion' => $input['profesion_situacion'] ?? null,
        ':quien_asume_gastos' => $input['quien_asume_gastos'] ?? null,
        ':tiempo_pasear' => $input['tiempo_pasear'] ?? null,
        ':horas_solo' => $input['horas_solo'] ?? null,
        ':lugares_paseo' => $input['lugares_paseo'] ?? null,
        ':mudanza_poblacion' => $input['mudanza_poblacion'] ?? null,
        ':mudanza_pais' => $input['mudanza_pais'] ?? null,
        ':vacaciones_cuidado' => $input['vacaciones_cuidado'] ?? null,
        ':por_que_adoptar' => $input['por_que_adoptar'] ?? null,
        ':tiempo_busqueda' => $input['tiempo_busqueda'] ?? null,
        ':como_conocio' => $input['como_conocio'] ?? null,
        ':conoce_condiciones' => $input['conoce_condiciones'],
        ':firma_nombre_dni' => $input['firma_nombre_dni'] ?? null
    ];

    $stmt->execute($params);
    $insertId = $pdo->lastInsertId();

} catch (PDOException $e) {
    error_log("DB error al insertar formulario: " . $e->getMessage());
    flash_errors(['Error interno al procesar el formulario. Inténtalo más tarde.']);
    header('Location: ' . $REDIRECT_ERROR);
    exit;
}

// ---------- GENERAR PDF RESUMEN (FPDF) ----------
$pdfFile = $TMP_DIR . '/formulario_' . time() . '_' . $insertId . '.pdf';
try {
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(0,10, 'Formulario de adopcion - Resumen', 0, 1, 'C');
    $pdf->Ln(4);
    $pdf->SetFont('Arial','',11);

    $addLine = function($label, $value) use ($pdf) {
        $pdf->SetFont('Arial','B',11);
        $pdf->Cell(50,7, $label, 0, 0);
        $pdf->SetFont('Arial','',11);
        $pdf->MultiCell(0,7, $value ?? '-', 0, 1);
    };

    $addLine('ID formulario:', $insertId);
    $addLine('Animal:', $input['animal_nombre'] . ' (ID: ' . ($input['animal_id'] ?? '') . ')');
    $addLine('Nombre:', $input['nombre_completo']);
    $addLine('DNI/Pasaporte:', $input['dni_pasaporte']);
    $addLine('Email:', $input['email']);
    $addLine('Teléfono:', $input['telefono']);
    $addLine('Dirección:', $input['direccion']);
    $addLine('Motivos adopción:', $input['motivos_adopcion']);
    $addLine('Personas en casa:', $input['personas_en_casa']);
    $addLine('Profesión / situación:', $input['profesion_situacion']);
    $addLine('Firma:', $input['firma_nombre_dni']);

    $pdf->Output('F', $pdfFile);
} catch (Exception $e) {
    error_log("Error generando PDF: " . $e->getMessage());
    // No abortamos el proceso por un fallo en el PDF; continuamos sin adjunto
    $pdfFile = null;
}

// ---------- ENVIAR CORREO CON PHPMailer ----------
try {
    $mail = new PHPMailer(true);

    if ($SMTP_ENABLED) {
        $mail->isSMTP();
        $mail->Host = $SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = $SMTP_USER;
        $mail->Password = $SMTP_PASS;
        $mail->SMTPSecure = $SMTP_SECURE;
        $mail->Port = $SMTP_PORT;
    } else {
        $mail->isMail();
    }

    $mail->setFrom($FROM_EMAIL, $FROM_NAME);
    $mail->addAddress($ADMIN_EMAIL); // a Noemí
    if (!empty($input['email'])) {
        $mail->addAddress($input['email']); // copia al usuario (si existe)
    }

    // Asunto y cuerpo (texto y HTML simple)
    $subject = "Nuevo formulario de adopción: " . ($input['nombre_completo'] ?? 'Sin nombre');
    $mail->Subject = $subject;

    // Plantilla HTML básica
    $html = '<h2>Nuevo formulario de adopción recibido</h2>';
    $html .= '<p><strong>Animal:</strong> ' . htmlspecialchars($input['animal_nombre'] ?? '') . '</p>';
    $html .= '<p><strong>Nombre:</strong> ' . htmlspecialchars($input['nombre_completo'] ?? '') . '</p>';
    $html .= '<p><strong>Email:</strong> ' . htmlspecialchars($input['email'] ?? '') . '</p>';
    $html .= '<p>Se ha adjuntado un resumen en PDF con los datos del formulario.</p>';
    $html .= '<p>Para ver todos los detalles, accede al panel de administración.</p>';

    $text = "Nuevo formulario de adopción recibido.\n\n";
    $text .= "Animal: " . ($input['animal_nombre'] ?? '') . "\n";
    $text .= "Nombre: " . ($input['nombre_completo'] ?? '') . "\n";
    $text .= "Email: " . ($input['email'] ?? '') . "\n";
    $text .= "ID formulario: " . $insertId . "\n\n";
    $text .= "Accede al panel de administración para ver todos los datos.";

    $mail->isHTML(true);
    $mail->Body = $html;
    $mail->AltBody = $text;

    // Adjuntar PDF si se generó
    if (!empty($pdfFile) && file_exists($pdfFile)) {
        $mail->addAttachment($pdfFile, 'formulario_adopcion_' . $insertId . '.pdf');
    }

    $mail->send();

} catch (Exception $e) {
    error_log("PHPMailer error: " . $e->getMessage());
    // No abortamos: el formulario ya está guardado. Informamos al admin por log.
}

// ---------- LIMPIEZA TEMPORAL ----------
if (!empty($pdfFile) && file_exists($pdfFile)) {
    // opcional: eliminar el PDF tras enviar
    @unlink($pdfFile);
}

// ---------- ÉXITO ----------
flash_success('Formulario enviado correctamente. Gracias por tu interés.');
header('Location: ' . $REDIRECT_SUCCESS);
exit;