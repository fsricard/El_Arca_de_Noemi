<?php
// ===============================================
// GENERAR PDF FORMULARIO DE ADOPCIÓN
// Archivo: includes/generar_pdf_formulario.php
// ===============================================

require_once __DIR__ . '/../config/database.php';

// Cargar DOMPDF
require_once __DIR__ . "/dompdf/autoload.inc.php";

use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Genera el PDF del formulario de adopción
 * @param int $id_formulario
 * @param mysqli $conn
 * @return string|null Ruta relativa del PDF generado
 */
function generarPDFFormulario($id_formulario, $pdo)
{
    // 1. Obtener datos del formulario
    $stmt = $pdo->prepare("SELECT * FROM adoptantes_formulario WHERE id = ?");
    $stmt->execute([$id_formulario]);
    $datos = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$datos) {
        return null;
    }

    // 2. Crear HTML del PDF usando plantilla externa
    ob_start();
    $datos_formulario = $datos; // variable accesible en la plantilla
    include __DIR__ . "/plantillas_email/formulario/plantilla_pdf_formulario.php";
    $html = ob_get_clean();

    // 3. Configurar DOMPDF
    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $options->set('defaultFont', 'DejaVu Sans');

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // 4. Crear carpeta destino: uploads/pdf/formulario/NOMBRE_FECHA/
    $nombre = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $datos['nombre_completo']);
    $fecha = date("Ymd_His");

    $carpeta_relativa = "/uploads/pdf/formulario/{$nombre}_{$fecha}/";
    $carpeta_absoluta = __DIR__ . "/.." . $carpeta_relativa;

    if (!is_dir($carpeta_absoluta)) {
        mkdir($carpeta_absoluta, 0777, true);
    }

    // 5. Guardar PDF
    $ruta_relativa = $carpeta_relativa . "formulario_{$id_formulario}.pdf";
    $ruta_absoluta = __DIR__ . "/.." . $ruta_relativa;

    file_put_contents($ruta_absoluta, $dompdf->output());

    return $ruta_relativa;
}
