<?php
require_once(__DIR__ . '/../../../config/funciones.php');
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= asset('img/favicon.ico') ?>">
    <link rel="shortcut icon" href="<?= asset('img/favicon.ico') ?>" type="image/x-icon">

    <!-- FontAwesome 7.0.1 CSS -->
    <link href="<?= asset('/css/fontawesome/css/brands.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/chisel-regular.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/duotone.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/duotone-light.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/duotone-regular.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/duotone-thin.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/etch-solid.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/fontawesome.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/jelly-duo-regular.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/jelly-fill-regular.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/jelly-regular.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/light.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/notdog-duo-solid.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/notdog-solid.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/regular.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/sharp-duotone-light.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/sharp-duotone-regular.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/sharp-duotone-solid.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/sharp-duotone-thin.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/sharp-light.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/sharp-regular.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/sharp-solid.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/sharp-thin.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/slab-press-regular.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/slab-regular.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/solid.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/svg.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/svg-with-js.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/thin.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/thumbprint-light.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/v4-font-face.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/v4-shims.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/v5-font-face.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/whiteboard-semibold.css') ?>" rel="stylesheet" />

    <link rel="stylesheet" href="<?= asset('/uploads/pdf/pdf_introduccion_backend/css/style.css') ?>" />

    <title>Documentación Backend - El Arca de Noemí</title>
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
            <img src="<?= asset('/img/logo_20260320_0002.png') ?>" alt="Logo Arca de Noemí">
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

    <img src="<?= asset('/uploads/adopciones/1/foto_69d6c987d70f0.jpg') ?>" class="img-center">

    <h3>Crear un nuevo animal</h3>
    <p>
        Para crear un nuevo animal, pulsa el botón “Añadir Animal” y completa el formulario.
    </p>

    <div class="advertencia">
        Recuerda subir al menos una foto principal para que el animal aparezca correctamente en el frontend.
    </div>

</body>

</html>