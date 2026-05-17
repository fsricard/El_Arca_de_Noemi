<?php

require_once __DIR__ . '/../../../includes/dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Configuración de DomPDF
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);

$dompdf = new Dompdf($options);

// Cargar la plantilla Blade ya renderizada
// Si NO usas Laravel, simplemente carga el HTML como string
$html = file_get_contents(__DIR__ . '/documentacion.blade.php');

// Si usas variables dinámicas, aquí podrías reemplazarlas manualmente
// $html = str_replace('{{ variable }}', $valor, $html);

$dompdf->loadHtml($html);

// Tamaño y orientación
$dompdf->setPaper('A4', 'portrait');

// Renderizar
$dompdf->render();

// Descargar o mostrar en navegador
$dompdf->stream('documentacion_backend.pdf', [
    'Attachment' => false // true = descarga, false = vista previa
]);
