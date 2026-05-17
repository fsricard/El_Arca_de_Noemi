<?php
require_once(__DIR__ . '/../../../config/funciones.php');
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
            margin-top: 200px;
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

        /* END indice */
    </style>
</head>

<body>

    <!-- ENCABEZADO -->
    <header>
        <strong>Documentación Backend - Arca de Noemí</strong>
    </header>

    <!-- PIE DE PÁGINA -->
    <footer>
        Página <span class="pagenum"></span>
    </footer>

    <!-- PORTADA -->
    <div class="portada">
        <div class="portada-logo">
            <img src="{{ public_path('img/logo_20260320_0002.png') }}" alt="Logo Arca de Noemí">
        </div>

        <h1 class="portada-titulo">Documentación del Backend</h1>

        <h2 class="portada-subtitulo">
            Guía oficial del panel de administración
        </h2>

        <div class="portada-franja">
            <span>Arca de Noemí · 2026 · Versión 1.0</span>
        </div>
    </div>

    <!-- ÍNDICE -->
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

        <div class="rama">
            <div class="titulo-rama">2. Sección Principal B</div>

            <div class="subrama">2.1 Subapartado B1</div>
            <div class="subsubrama">2.1.1 Subnivel B1-a</div>
            <div class="subsubrama">2.1.2 Subnivel B1-b</div>

            <div class="subrama">2.2 Subapartado B2</div>
        </div>

        <div class="rama">
            <div class="titulo-rama">3. Sección Principal C</div>

            <div class="subrama">3.1 Subapartado C1</div>
            <div class="subsubrama">3.1.1 Subnivel C1-a</div>
            <div class="subsubrama">3.1.2 Subnivel C1-b</div>
            <div class="subsubrama">3.1.3 Subnivel C1-c</div>
        </div>
    </div>

    <div class="separador"></div>

    <!-- SECCIÓN EJEMPLO -->
    <h2>1. Introducción</h2>
    <p>
        En este documento encontrarás toda la información necesaria para manejar el panel de administración del Arca de Noemí.
    </p>

    <div class="nota">
        Esta guía está diseñada para que cualquier persona, incluso sin experiencia técnica, pueda gestionar el sistema.
    </div>

    <h2>2. Gestión de Animales</h2>
    <p>
        Desde esta sección podrás crear, editar y eliminar fichas de animales.
    </p>

    <img src="{{ public_path('uploads/adopciones/1/foto_69d6c987d70f0.jpg') }}" class="img-center">

    <h3>Crear un nuevo animal</h3>
    <p>
        Para crear un nuevo animal, pulsa el botón “Añadir Animal” y completa el formulario.
    </p>

    <div class="advertencia">
        Recuerda subir al menos una foto principal para que el animal aparezca correctamente en el frontend.
    </div>

</body>

</html>