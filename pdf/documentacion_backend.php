<?php
require_once(__DIR__ . '/../config/funciones.php');
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <title>Documentación Backend - El Arca de Noemí</title>

    <style>
        /* Confiración global */
        @page {
            margin: 120px 50px 80px 50px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }

        h1,
        h2,
        h3 {
            color: #2b4c7e;
            margin-bottom: 10px;
        }

        h1 {
            font-size: 28px;
            text-align: center;
        }

        h2 {
            font-size: 20px;
            margin-top: 40px;
        }

        h3 {
            font-size: 16px;
            margin-top: 25px;
        }

        p {
            line-height: 1.5;
            margin-bottom: 12px;
        }

        .page-break {
            page-break-after: always;
        }

        .no-break {
            page-break-inside: avoid;
        }

        /* END confiración global */

        /* Encabezado y pie de página */
        header {
            position: fixed;
            top: -90px;
            left: 0;
            right: 0;
            height: 70px;
            text-align: center;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
        }

        footer {
            position: fixed;
            bottom: -60px;
            left: 0;
            right: 0;
            height: 50px;
            text-align: center;
            border-top: 1px solid #ccc;
            padding-top: 10px;
            font-size: 11px;
        }

        .pagenum:before {
            content: counter(page);
        }

        /* END encabezado y pie de página */

        /* Portada */
        .portada {
            text-align: center;
            page-break-after: always;
            padding-top: 120px;
        }

        .portada-logo img {
            width: 220px;
            margin-bottom: 60px;
        }

        .portada-titulo {
            font-size: 34px;
            color: #2b4c7e;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }

        .portada-subtitulo {
            font-size: 18px;
            color: #555;
            margin-bottom: 200px;
            font-weight: normal;
        }

        .portada-franja {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: #2b4c7e;
            color: white;
            padding: 18px 0;
            font-size: 14px;
            letter-spacing: 0.5px;
        }

        /* END portada */

        /* Separadores */
        .separador {
            width: 100%;
            height: 2px;
            background: #2b4c7e;
            margin: 40px 0;
        }

        /* END separadores */

        /* Bloques destacados */
        .nota {
            background: #e8f0fe;
            border-left: 4px solid #2b4c7e;
            padding: 10px 15px;
            margin: 20px 0;
        }

        .advertencia {
            background: #fff3cd;
            border-left: 4px solid #ff9800;
            padding: 10px 15px;
            margin: 20px 0;
        }

        /* END bloques destacados */

        /* Imágenes alineadas */
        .img-center {
            display: block;
            margin: 20px auto;
            width: 50%;
        }

        .img-left {
            float: left;
            margin: 10px 20px 10px 0;
            width: 35%;
        }

        .img-right {
            float: right;
            margin: 10px 0 10px 20px;
            width: 35%;
        }

        /* END imágenes alineadas */

        /* Indice */
        .indice {
            text-align: center;
            margin-top: 80px;
        }

        .indice h1 {
            font-size: 28px;
            color: #2b4c7e;
            margin-bottom: 40px;
            letter-spacing: 1px;
        }

        .rama {
            display: block;
            width: 100%;
            max-width: 600px;
            margin: 25px auto;
            text-align: left;
            border-left: 2px solid #2b4c7e;
            padding-left: 20px;
        }

        .titulo-rama {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #2b4c7e;
        }

        .subrama {
            font-size: 14px;
            margin: 4px 0;
            color: #444;
            padding-left: 10px;
            border-left: 1px solid #aaa;
        }

        .subsubrama {
            font-size: 13px;
            margin: 3px 0;
            color: #666;
            padding-left: 20px;
            border-left: 1px dashed #bbb;
        }

        /* Sección */
        .seccion {
            margin: 40px auto;
            max-width: 700px;
            text-align: left;
            page-break-inside: avoid;
        }

        ul,
        ol {
            margin: 12px 0 12px 25px;
            padding: 0;
        }

        ul li {
            list-style-type: disc;
            margin: 4px 0;
        }

        ul ul li {
            list-style-type: circle;
        }

        ol li {
            list-style-type: decimal;
            margin: 4px 0;
        }

        ol ol li {
            list-style-type: lower-alpha;
        }

        blockquote {
            border-left: 4px solid #2b4c7e;
            background: #f5f7fb;
            padding: 12px 18px;
            margin: 20px 0;
            font-style: italic;
            color: #333;
        }

        code {
            background: #eee;
            padding: 2px 4px;
            font-family: DejaVu Sans Mono, monospace;
            font-size: 12px;
            border-radius: 3px;
        }

        pre {
            background: #eee;
            padding: 12px;
            font-family: DejaVu Sans Mono, monospace;
            font-size: 12px;
            border-radius: 3px;
            white-space: pre-wrap;
            margin: 15px 0;
            border: 1px solid #ccc;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table th {
            background: #2b4c7e;
            color: white;
            padding: 8px;
            font-size: 13px;
        }

        table td {
            border: 1px solid #ccc;
            padding: 8px;
            font-size: 12px;
        }

        table tr:nth-child(even) {
            background: #f7f9fc;
        }

        .info {
            background: #e8f4ff;
            border-left: 4px solid #2196f3;
            padding: 10px 15px;
            margin: 20px 0;
        }

        .success {
            background: #e8fce8;
            border-left: 4px solid #4caf50;
            padding: 10px 15px;
            margin: 20px 0;
        }

        .warning {
            background: #fff8e1;
            border-left: 4px solid #ff9800;
            padding: 10px 15px;
            margin: 20px 0;
        }

        .danger {
            background: #ffebee;
            border-left: 4px solid #f44336;
            padding: 10px 15px;
            margin: 20px 0;
        }

        .destacado {
            background: #f0f0f0;
            padding: 10px 15px;
            border-left: 4px solid #2b4c7e;
            margin: 20px 0;
        }

        .figura {
            text-align: center;
            margin: 20px 0;
        }

        .figura img {
            width: 70%;
            margin-bottom: 8px;
        }

        .figura .pie {
            font-size: 11px;
            color: #666;
        }

        .consola {
            background: #1e1e1e;
            color: #dcdcdc;
            padding: 12px;
            font-family: DejaVu Sans Mono, monospace;
            font-size: 12px;
            border-radius: 3px;
            white-space: pre-wrap;
            margin: 15px 0;
        }

        .cmd {
            background: #333;
            color: #fff;
            padding: 4px 6px;
            font-family: DejaVu Sans Mono, monospace;
            border-radius: 3px;
        }

        .kbd {
            background: #eee;
            border: 1px solid #ccc;
            padding: 2px 5px;
            border-radius: 3px;
            font-family: DejaVu Sans Mono, monospace;
            font-size: 11px;
        }

        /* END sección */

        /* END indice */
    </style>
</head>

<body>

    <!-- Encabezado -->
    <header>
        <strong>Documentación Backend - Arca de Noemí</strong>
    </header>

    <!-- Pie de página -->
    <footer>
        Página <span class="pagenum"></span>
    </footer>

    <!-- Portada -->
    <div class="portada">
        <div class="portada-logo">
            <img src="data:image/png;base64,<?= img64('/El_Arca_de_Noemi/pdf/img/logo_20260320_0002.png') ?>" alt="Logo Arca de Noemí">
        </div>

        <h1 class="portada-titulo">Documentación del Backend</h1>

        <h2 class="portada-subtitulo">
            Guía oficial del panel de administración
        </h2>

        <div class="portada-franja">
            <span>Arca de Noemí · 2026 · Versión 1.0</span>
        </div>
    </div>

    <!-- índice -->
    <div class="indice">
        <h1>Índice</h1>

        <div class="rama">
            <div class="titulo-rama">1. Sección Principal A</div>

            <div class="subrama">1.1 Subapartado A1</div>
            <div class="subsubrama">1.1.1 Subnivel A1-a</div>
            <div class="subsubrama">1.1.2 Subnivel A1-b</div>

            <div class="subrama">1.2 Subapartado A2</div>
            <div class="subsubrama">1.2.1 Subnivel A2-a</div>

            <div class="subrama">1.3 Subapartado A3</div>
        </div>
    </div>

    <div class="separador"></div>

    <!-- Sección 1 -->
    <div class="seccion">

        <h2></h2>


        <div style="clear: both;"></div>

        <div class="separador-seccion"></div>

    </div>

    <div class="separador"></div>

</body>

</html>