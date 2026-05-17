<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Documentación Backend - Arca de Noemí</title>

    <style>

        /* ------------------------------
           CONFIGURACIÓN GLOBAL
        ------------------------------ */

        @page {
            margin: 120px 50px 80px 50px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }

        h1, h2, h3 {
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

        /* ------------------------------
           ENCABEZADO Y PIE DE PÁGINA
        ------------------------------ */

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

        /* ------------------------------
           PORTADA
        ------------------------------ */

        .portada {
            text-align: center;
            page-break-after: always;
        }

        .portada img {
            width: 180px;
            margin-top: 120px;
        }

        .subtitulo {
            font-size: 18px;
            margin-top: 20px;
            color: #555;
        }

        /* ------------------------------
           SEPARADORES
        ------------------------------ */

        .separador {
            width: 100%;
            height: 2px;
            background: #2b4c7e;
            margin: 40px 0;
        }

        /* ------------------------------
           BLOQUES DESTACADOS
        ------------------------------ */

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

        /* ------------------------------
           IMÁGENES ALINEADAS
        ------------------------------ */

        .img-center {
            display: block;
            margin: 20px auto;
            width: 70%;
        }

        .img-left {
            float: left;
            margin: 10px 20px 10px 0;
            width: 40%;
        }

        .img-right {
            float: right;
            margin: 10px 0 10px 20px;
            width: 40%;
        }

    </style>
</head>

<body>

    <!-- ENCABEZADO -->
    <header>
        <strong>Documentación Backend – Arca de Noemí</strong>
    </header>

    <!-- PIE DE PÁGINA -->
    <footer>
        Página <span class="pagenum"></span>
    </footer>

    <!-- PORTADA -->
    <div class="portada">
        <img src="{{ public_path('img/logo-arca.png') }}" alt="Logo Arca de Noemí">
        <h1>Documentación del Panel de Administración</h1>
        <div class="subtitulo">Guía completa del funcionamiento del backend</div>
    </div>

    <!-- ÍNDICE -->
    <h2>Índice</h2>
    <p>1. Introducción</p>
    <p>2. Gestión de Animales</p>
    <p>3. Gestión de Fotos</p>
    <p>4. Gestión de Especies y Razas</p>
    <p>5. Sistema de Paginación</p>
    <p>6. Seguridad y Roles</p>
    <p>7. Copias de Seguridad</p>

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

    <img src="{{ public_path('img/captura-animales.png') }}" class="img-center">

    <h3>Crear un nuevo animal</h3>
    <p>
        Para crear un nuevo animal, pulsa el botón “Añadir Animal” y completa el formulario.
    </p>

    <div class="advertencia">
        Recuerda subir al menos una foto principal para que el animal aparezca correctamente en el frontend.
    </div>

</body>
</html>
