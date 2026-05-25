<?php

require_once __DIR__ . '/../includes/dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Renderizar la plantilla PHP (IMPORTANTE)
ob_start();
include __DIR__ . '/documentacion.backend.php';
$html = ob_get_clean();

// Configuración DomPDF
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Mostrar en navegador
$dompdf->stream('documentacion_backend.pdf', ['Attachment' => false]);
