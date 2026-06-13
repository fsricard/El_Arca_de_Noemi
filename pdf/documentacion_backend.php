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
        h3,
        h4 {
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

        h4 {
            font-size: 14px;
            margin-top: 10px;
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
            width: 90%;
        }

        .img-left {
            float: left;
            margin: 10px 20px 10px 0;
            width: 45%;
        }

        .img-right {
            float: right;
            margin: 10px 0 10px 20px;
            width: 45%;
        }

        .img-left-fine {
            float: left;
            margin: 10px 20px 10px 0;
            width: 15%;
        }

        .img-right-fine {
            float: right;
            margin: 10px 0 10px 20px;
            width: 15%;
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
            border-left: 1px dashed #aaa;
        }

        .subsubsubrama {
            font-size: 12px;
            margin: 3px 0;
            color: #666;
            padding-left: 30px;
            border-left: 1px solid #bbb;
        }

        .indice a {
            color: #2b4c7e;
            text-decoration: none;
            font-weight: normal;
            display: inline-block;
            padding: 2px 0;
        }

        .indice a:hover {
            color: #1e3558;
        }

        .indice .titulo-rama a {
            font-size: 18px;
            font-weight: bold;
        }

        .indice .subrama a {
            font-size: 14px;
            padding-left: 5px;
        }

        .indice .subsubrama a {
            font-size: 13px;
            padding-left: 10px;
            color: #555;
        }

        .indice .subsubsubrama a {
            font-size: 12px;
            padding-left: 15px;
            color: #555;
        }

        .volver-indice {
            display: block;
            overflow: hidden;
            width: 100%;
            text-align: center;
            margin: 40px 0 20px 0;
        }

        .volver-indice a {
            display: inline-block;
            background: #2b4c7e;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 13px;
        }

        .volver-indice a:hover {
            background: #1e3558;
        }


        /* END indice */

        /* Sección */
        .seccion {
            margin: 40px auto;
            max-width: 700px;
            text-align: left;
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

        a.infoco {
            display: contents;
            text-decoration: none;
            color: #1E3558;
            font-weight: bold;
        }

        a.infoco:hover {
            color: #2c6dcf;
        }

        p,
        .pre,
        .info,
        .success,
        .warning,
        .danger {
            display: grid;
        }

        .infoA {
            background: #e4e13e;
            border-left: 4px solid #3ee4ce;
            padding: 10px 15px;
            margin: 0 0;
        }

        .infoA a {
            color: #2b4c7e;
            font-weight: bold;
            text-decoration: none;
        }

        .infoA a:hover {
            color: #1a2e4d;
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
            width: 90%;
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

        /* ================================
        PÁGINA FINAL DE CRÉDITOS
        ================================ */

        .creditos {
            text-align: left;
            max-width: 700px;
            margin: 40px auto;
            page-break-before: always;
        }

        .creditos h2 {
            font-size: 24px;
            color: #2b4c7e;
            margin-bottom: 15px;
        }

        .creditos h3 {
            font-size: 18px;
            color: #444;
            margin-top: 30px;
            margin-bottom: 10px;
        }

        .creditos ul {
            margin: 10px 0 10px 25px;
        }

        .creditos li {
            margin: 5px 0;
        }

        .creditos .separador-creditos {
            width: 100%;
            height: 2px;
            background: #2b4c7e;
            margin: 30px 0;
        }

        .creditos .frase-final {
            text-align: center;
            font-style: italic;
            font-size: 16px;
            margin-top: 40px;
            color: #333;
        }

        .creditos .autor {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #555;
        }
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

        <!-- 1. Sección principal - Inicio de Sesión -->
        <div class="rama" id="seccion-principal">
            <div class="titulo-rama">
                <a href="#inicio_de_sesion">
                    1. Sección Principal - Inicio de Sesión
                </a>
            </div>

            <div class="subrama">
                <a href="#url_crear_usuario">
                    1.1. Dirección Web para crear tu usuario
                </a>
            </div>
            <div class="subsubrama">
                <a href="#crear_usuario">
                    1.1.1. Crear tu usuario
                </a>
            </div>

            <div class="subrama">
                <a href="#url_servidor_pruebas">
                    1.2. Dirección Web del servidor de pruebas
                </a>
            </div>
            <div class="subsubrama">
                <a href="#acceso_servidor_pruebas">
                    1.2.1. Acceso al servidor de pruebas
                </a>
            </div>
        </div>

        <!-- 2. Segunda sección - Documentos -->
        <div class="rama page-break" id="seccion-segunda">
            <div class="titulo-rama">
                <a href="#documentos">
                    2. Segunda Sección - Documentos
                </a>
            </div>

            <div class="subrama">
                <a href="#contacto">
                    2.1. Mensajes que llegan desde la página de contacto
                </a>
            </div>
            <div class="subsubrama">
                <a href="#ver_mensaje">
                    2.1.1. Editar un mensaje de contacto
                </a>
            </div>
            <div class="subsubrama">
                <a href="#eliminar_mensaje">
                    2.1.1.1. Eliminar un mensaje de contacto
                </a>
            </div>

            <div class="subrama">
                <a href="#contacto_intro">
                    2.2. Contenido de la sección de contacto
                </a>
            </div>

            <div class="subrama">
                <a href="#asi_es_noemi">
                    2.3. Contenido de la página de presentación de Noemí
                </a>
            </div>

            <div class="subrama">
                <a href="#politica_de_privacidad">
                    2.4. Contenido de la página de la política de privacidad
                </a>
            </div>

            <div class="subrama">
                <a href="#opiniones_intro">
                    2.5. Contenido de la sección de las opiniones de los usuarios
                </a>
            </div>

            <div class="subrama">
                <a href="#opiniones_listado">
                    2.6. Listado de las opiniones de los usuarios
                </a>
            </div>
            <div class="subsubrama">
                <a href="#opiniones_editar">
                    2.6.1. Editar las opiniones de los usuarios
                </a>
            </div>
            <div class="subsubrama">
                <a href="#opniones_eliminar">
                    2.6.1.1. Eliminar las opiniones de los usuarios
                </a>
            </div>
        </div>

        <!-- 3. Tercera sección - Sarcásmo y humor -->
        <div class="rama" id="seccion-tercera">
            <div class="titulo-rama">
                <a href="#sarcasmo_y_humor">
                    3. Tercera Sección - Sarcásmo y Humor
                </a>
            </div>

            <div class="subrama">
                <a href="#noemi_dice">
                    3.1. Incluir las frases de "Noemí dice" en el sitio web
                </a>
            </div>

            <div class="subrama">
                <a href="#listado_noemi_dice">
                    3.2. Listado de las frases de "Noemí dice"
                </a>
            </div>
            <div class="subsubrama">
                <a href="#listado_noemi_dice_ocultar">
                    3.2.1. Ocultar las frases de "Noemí dice"
                </a>
            </div>
            <div class="subsubrama">
                <a href="#listado_noemi_dice_aprobar">
                    3.2.1.1. Aprobar las frases de "Noemí dice"
                </a>
            </div>
            <div class="subsubrama">
                <a href="#listado_noemi_dice_eliminar">
                    3.2.1.1.1. Eliminar las frases de "Noemí dice"
                </a>
            </div>

            <div class="subrama">
                <a href="#bichillos_de_noemi">
                    3.3. Incluir los "Bichillos de Noemí" en el sitio web
                </a>
            </div>

            <div class="subrama">
                <a href="#listado_bichillos_de_noemi">
                    3.4. Listado de los "Bichillos de Noemí"
                </a>
            </div>
        </div>

        <!-- 4. Cuarta sección - Registro -->
        <div class="rama" id="seccion-cuarta">
            <div class="titulo-rama">
                <a href="#registro">
                    4. Cuarta Sección - Registro
                </a>
            </div>

            <div class="subrama">
                <a href="#registro_especie_raza">
                    4.1. Registrar una nueva especie o raza de animal
                </a>
            </div>

            <div class="subrama">
                <a href="#registro_nuevo_animal_adopcion">
                    4.2. Registrar un nuevo animal en adopción
                </a>
            </div>

            <div class="subrama">
                <a href="#registro_nuevo_adoptante">
                    4.3. Registrar un nuevo posible adoptante
                </a>
            </div>

            <div class="subrama">
                <a href="#registro_nuevo_animal_apadrinar">
                    4.4. Registrar un nuevo animal para apadrinar
                </a>
            </div>

            <div class="subrama">
                <a href="#registro_nueva_plataforma_crowdfunding">
                    4.5. Registrar una nueva plataforma de Crowd Funding
                </a>
            </div>
        </div>

        <!-- 5. Quinta sección - Plataformas de Crowd Funding -->
        <div class="rama page-break" id="seccion-quinta">
            <div class="titulo-rama">
                <a href="#plataformas_crowdfunding">
                    5. Quinta Sección - Plataformas de Crowd Funding
                </a>
            </div>

            <div class="subrama">
                <a href="#plataforma_crear_nueva_recaudacion">
                    5.1. Crear una nueva recaudación de fondos en una plataforma de Crowd Funding
                </a>
            </div>

            <div class="subrama">
                <a href="#plataforma_listado">
                    5.2. Listado de las recaudaciones de fondos en las plataformas de Crowd Funding
                </a>
            </div>
            <div class="subsubrama">
                <a href="#plataforma_listado_editar">
                    5.2.1. Editar la recaudación de fondos de la plataforma de Crowd Funding
                </a>
            </div>
        </div>

        <!-- 6. Sexta sección - Adopciones -->
        <div class="rama page-break" id="seccion-sexta">
            <div class="titulo-rama">
                <a href="#adopciones">
                    6. Sexta Sección - Adopciones
                </a>
            </div>

            <div class="subrama">
                <a href="#iniciar_nueva_adopcion">
                    6.1. Iniciar el proceso de adopción de un animal
                </a>
            </div>

            <div class="subrama">
                <a href="#listado_de_adoptantes">
                    6.2. Listado de todos los adoptantes de la base de datos
                </a>
            </div>
            <div class="subsubrama">
                <a href="#filtros_listado_adoptantes">
                    6.2.1. Filtros para el listado de adoptantes
                </a>
            </div>
            <div class="subsubrama">
                <a href="#listado_adoptantes_origen">
                    6.2.1.1. Columna de origen del adoptante en el listado de adoptantes
                </a>
            </div>
            <div class="subsubrama">
                <a href="#listado_adoptantes_estado">
                    6.2.1.1.1. Columna de estado del adoptante en el listado de adoptantes
                </a>
            </div>
            <div class="subsubrama">
                <a href="#listado_adoptantes_editar_manual">
                    6.2.1.1.1.1. Editar adoptante de la columna de origen "Manual"
                </a>
            </div>
            <div class="subsubrama">
                <a href="#listado_adoptantes_editar_formulario">
                    6.2.1.1.1.1.1. Editar adoptante de la columna de origen "Formulario"
                </a>
            </div>
            <div class="subsubrama">
                <a href="#listado_adoptantes_ver_adopciones">
                    6.2.1.1.1.1.1.1. Ver las adopciones de un adoptante
                </a>
            </div>

            <div class="subrama">
                <a href="#listado_animales_adopcion">
                    6.3. Listado de todos los animales en adopción de la base de datos
                </a>
            </div>
            <div class="subsubrama">
                <a href="#listado_animales_adopcion_filtros">
                    6.3.1. Filtros para el listado de animales en adopción
                </a>
            </div>
            <div class="subsubrama">
                <a href="#listado_animales_adopcion_crear_adopcion">
                    6.3.1.1. Crear una adopción desde el listado de animales en adopción
                </a>
            </div>
            <div class="subsubrama">
                <a href="#listado_animales_adopcion_editar_adopcion">
                    6.3.1.1.1. Editar una adopción desde el listado de animales en adopción
                </a>
            </div>
            <div class="subsubrama">
                <a href="#listado_animales_adopcion_editar_animal">
                    6.3.1.1.1.1. Editar un animal en adopción desde el listado de animales en adopción
                </a>
            </div>
        </div>

        <!-- 7. Séptima sección - Apadrinamientos -->
        <div class="rama page-break" id="seccion-septima">
            <div class="titulo-rama">
                <a href="#apadrinamientos">
                    7. Séptima Sección - Apadrinamientos
                </a>
            </div>

            <div class="subrama">
                <a href="#listado_animales_apadrinar">
                    7.1. Listado de todos los animales para apadrinar de la base de datos
                </a>
            </div>
            <div class="subsubrama">
                <a href="#listado_animales_apadrinar_filtros">
                    7.1.1. Filtros para el listado de animales para apadrinar
                </a>
            </div>
            <div class="subsubrama">
                <a href="#listado_animales_apadrinar_editar">
                    7.1.1.1. Editar un animal para apadrinar
                </a>
            </div>

            <div class="subrama">
                <a href="#listado_padrinos">
                    7.2. Listado de todos los padrinos de la base de datos
                </a>
            </div>
            <div class="subsubrama">
                <a href="#listado_padrinos_filtros">
                    7.2.1. Filtros para el listado de padrinos
                </a>
            </div>
            <div class="subsubrama">
                <a href="#listado_padrinos_editar">
                    7.2.1.1. Editar un padrino desde el listado de padrinos
                </a>
            </div>

            <div class="subrama">
                <a href="#listado_padrinos_ver_apadrinamientos">
                    7.3. Ver los animales apadrinados por cada padrino
                </a>
            </div>

            <div class="subrama">
                <a href="#listado_padrinos_ultima_relacion">
                    7.4. Ver la última relación de apadrinamiento de cada padrino
                </a>
            </div>
        </div>

        <!-- 8. Octava sección - Base de datos -->
        <div class="rama page-break" id="seccion-octava">
            <div class="titulo-rama">
                <a href="#base_de_datos">
                    8. Octava Sección - Base de datos
                </a>
            </div>

            <div class="subrama">
                <a href="#base_de_datos_logs">
                    8.1. Logs de accesos al panel de administración
                </a>
            </div>

            <div class="subrama">
                <a href="#base_de_datos_usuarios">
                    8.2. Usuarios del panel de administración
                </a>
            </div>
            <div class="subsubrama">
                <a href="#base_de_datos_usuarios_crear">
                    8.2.1. Crear un nuevo usuario para el panel de administración
                </a>
            </div>

            <div class="subrama">
                <a href="#base_de_datos_usuarios_actualizar">
                    8.3. Actualizar los datos de un usuario del panel de administración
                </a>
            </div>

            <div class="subrama">
                <a href="base_de_datos_usuarios_eliminar">
                    8.4. Eliminar un usuario del panel de administración
                </a>
            </div>

            <div class="subrama">
                <a href="#base_de_datos_usuarios_tablas">
                    8.5. Tablas de la base de datos
                </a>
            </div>
            <div class="subsubrama">
                <a href="#base_de_datos_usuarios_tablas_ver">
                    8.5.1. Ver el contenido de una tabla de la base de datos
                </a>
            </div>
            <div class="subsubrama">
                <a href="#base_de_datos_usuarios_tablas_eliminar">
                    8.5.1.1. Eliminar el contenido de una tabla de la base de datos
                </a>
            </div>
        </div>
    </div>

    <!-- 1. Sección principal - Inicio de Sesión -->
    <div class="seccion page-break" id="inicio_de_sesion">

        <h2>1. Sección Principal - Inicio de Sesión</h2>

        <div class="info">
            No me conoces de nada (algo lógico, ya que solo hemos intercambiado algún que otro mensaje) pero debes saber que mi cerebro está algo "cascáo", así que no te asustes por el contenido que vas a ir viendo en las diferentes secciones del panel de administración. Cuando me entusiasmo con algo me da por destruir personalidades y flagelar el código que voy escribiendo, pero todo tiene remedio, también puedo ponerme serio (vaaaaale, puedo intentarlo, pero no prometo nada) y darle a la web un toque más distinguido si es necesario.
        </div>

        <p>
            En esta primera sección te llevaré de la mano para que puedas crear tu usuario y contraseña para el panel de administración, una vez creados podrás acceder al panel de administración del sitio web de "El Arca de Noemí" que he creado para tí en un servidor de pruebas. Aquí encontrarás toda la información necesaria para configurar tu cuenta y empezar a trastear con el BackEnd de tu sitio web.
        </p>

        <div class="warning">
            Debes tener en cuenta que esta página web está funcionando en un servidor que yo tengo alojado en CDMon y que solo sirve para realizar pruebas (por ese motivo lo tengo encriptado con usuario y contraseña), por lo que es posible que en algún momento el servidor deje de funcionar o que se realicen actualizaciones que puedan afectar al funcionamiento del sitio web. Si esto ocurre, no te preocupes, simplemente ponte en contacto conmigo y lo solucionaré lo antes posible.
        </div>

        <div class="volver-indice">
            <a href="#seccion-principal">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="url_crear_usuario">1.1. Dirección Web para crear tu usuario</h3>

        <p>
            El primer paso que debes dar es acceder a la página desde la que vas a poder crear tu usuario y contraseña para después acceder al panel de administración. Copia y pega esta dirección web en tu navegador para acceder a la página de creación de usuario:
        </p>

        <div class="infoA">
            <a href="http://www.ricardfs.es.mialias.net/admin/PHP/crear_usuarios.php" target="_blank">Haz clic aquí y se abrirá la página en tu navegar web</a>
        </div>

        <p>
            Si es la primera vez que accedes a la página web desde ese navegador te pedirá que introduzcas un usuario y contraseña, como te dije antes tengo el servidor encriptado por seguridad, si no te había dado acceso antes te lo doy ahora:
        </p>

        <div class="success">
            <h4>
                Usuario y contraseña para acceder al servidor de pruebas
            </h4>
            <p>Usuario: ricard624 | Contraseña: mNU1P4hZI36x</p>
        </div>

        <div class="volver-indice">
            <a href="#seccion-principal">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="crear_usuario">1.1.1. Crear tu usuario</h3>

        <p>Cuando accedas a la página verás un formulario como el que tienes en la imagen de abajo, no te asustes que es muy sencillo aquí te dejo los pasos a seguir:</p>

        <ol>
            <li>Nombre completo:</li>
            <ol>
                <li>Aquí debes poner el nombre de usuario con el que después accederás al panel de administración, te recomiendo que no utilices símbolos raros, puedes utilizar letras, números, guiones ... Pero sobre todo NUNCA dejes un espacio en blanco.</li>
            </ol>
            <li>Correo electrónico:</li>
            <ol>
                <li>Como este es el servidor de pruebas, el correo electrónico te lo puedes inventar si quieres.</li>
            </ol>
            <li>Contraseña:</li>
            <ol>
                <li>Este sí que es un paso importante, aquí puedes utilizar cualquier carácter que se te ocurra, además nadie aparte de tú puede saber tu contraseña, ya que al guardarse en la base de datos se encripta automáticamente y ya no se puede desencriptar, te recomiendo que te la anotes en algún lado por si se te olvida.</li>
            </ol>
            <li>Rol de usuario:</li>
            <ol>
                <li>Este es el campo más importante de todos, como verás este campo es diferente al resto, es un campo multi selección, en este caso dos "Visitante" y "Administrador", en tu caso debes elegir el rol "Administrador".</li>
            </ol>
        </ol>

        <div class="figura">
            <img src="data:image/png;base64,<?= img64('/El_Arca_de_Noemi/pdf/img/1.1._Dirección_Web_para_crear_tu_usuario.png') ?>" />
        </div>

        <div class="info">
            El rol "Administrador" tiene todos los privilegios activados, mientras que el rol "Visitante" solo puede ver algunas secciones, este rol no puede editar, eliminar, crear, bloquear, activar, ocultar ...
        </div>

        <div class="volver-indice">
            <a href="#seccion-principal">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="url_servidor_pruebas">1.2. Dirección Web del servidor de pruebas</h3>

        <p>
            Una vez que tengas creado tu usuario y contraseña en el servidor de pruebas ya no tendrás que repetir el proceso, bueno siempre que al pasar la web al servidor de producción quieras mantener el mismo usuario y contraseña, aunque como verás más adelante una vez lo hayas creado, en el panel de administración tendrás una sección dedicada exclusivamente a la edición de usuarios.
        </p>

        <p>
            Pero sigamos avanzando, ya tienes tus datos para acceder al panel de administración, ¿Entonces qué leches te hace fañta ahora? ... Coño Noemí la URL del panel de administración mujer, veeeenga copia y pega este enlace en tu navegador web:
        </p>

        <div class="infoA">
            <a href="http://www.ricardfs.es.mialias.net/admin/" target="_blank">Haz clic aquí y se abrirá la página en tu navegar web</a>
        </div>

        <div class="warning">
            La encriptación de mi servidor de pruebas se regenera cada "X" tiempo de forma automática para obligarnos a volver a introducir el usuario y la contraseña, de manera que cada dos por tres te los irá pidiendo, y como en el fondo parece que SÏ tengo algún sentimiento positivo por el ser humano (a muy pesar mio, que quede constancia), te voy a ir dejando el usuario y contraseña a lo largo de este documento para que no lo tengas que ir buscando.
        </div>

        <div class="success">
            <h4>
                Usuario y contraseña para acceder al servidor de pruebas
            </h4>
            <p>Usuario: ricard624 | Contraseña: mNU1P4hZI36x</p>
        </div>

        <div class="volver-indice">
            <a href="#seccion-principal">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="acceso_servidor_pruebas">1.2.1. Acceso al servidor de pruebas</h3>

        <img src="data:image/png;base64,<?= img64('/El_Arca_de_Noemi/pdf/img/1.2.1._Acceso_al_servidor_de_pruebas.png') ?>" class="img-left" />

        <p>
            La verdad es que esta sección la he añadido como un puro trámite porque no tiene mucho misterio, una vez hayas accedido a la URL que te deje más arriba verás una imagen como la de la izquierda. No te comas mucho la cabeza Noemí, pon el usuario y la contraseña que creaste en la primera sección, dale al botoncito y alucina con los colorines.
        </p>

        <div class="danger">
            Recuerda que no debes dejar ningún espacio en blanco ni en el campo del usuario ni tampoco en el campo de la contraseña, si lo haces te dará error y no podrás acceder al panel de administración, así que ten cuidado con eso.
        </div>

        <div class="volver-indice">
            <a href="#seccion-principal">Volver al índice</a>
        </div>

        <div style="clear: both;"></div>

        <div class="separador-seccion"></div>

        <div class="separador"></div>

    </div>

    <!-- 2. Segunda sección - Documentos -->
    <div class="seccion page-break" id="documentos">

        <h2>2. Segunda Sección - Documentos</h2>

        <p>
            En esta segunda sección te voy a explicar cómo gestionar los documentos que aparecen en la página de contacto, en la página de presentación de Noemí, en la página de la política de privacidad y en la sección de las opiniones de los usuarios. En cada una de estas secciones podrás editar el contenido que aparece en el sitio web, así como también podrás eliminarlo o añadir nuevo contenido si lo deseas.
        </p>

        <div class="volver-indice">
            <a href="#seccion-segunda">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="contacto">2.1. Mensajes que llegan desde la página de contacto</h3>

        <div class="warning">
            <p>
                Haz clic en el siguiente enlace, te pedirá el usuario y contraseña de la encriptación de la página web, te los dejo debajo del enlace. Una vez que hayas desencriptado la página web entrarás en la pantalla de login, utilizas tu usuario y contraseña e voila ... Ya puedes acceder a los enlaces de cada sección.
            </p>

            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/" target="_blank">Haz clic aquí para desencriptar la web y acceder al panel de administración</a>
            </div>

            <p>Usuario: ricard624 | Contraseña: mNU1P4hZI36x</p>

            <div class="danger">
                No te agobies Noemí, esto solo lo tienes que hacer cada vez que cierres el navegador y empieces de cero, no cada vez que quieras entrar a un módulo.
            </div>

            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/modulos/contacto/contacto.php" target="_blank">Haz clic aquí y se abrirá el módulo de esta sección</a>
            </div>
        </div>

        <p>
            Lo primero que veras al entrar en el módulo "Contacto" será algo parecido a la imagen que te dejo debajo de este texto. Lo que ves arriba no es un formulario para rellenar datos, en realidad es un sistema de filtros vinculante que te permite filtrar los mensajes que aparecen en el listado por el nombre de la persona, el E-mail de la persona, el día, mes y/o año que te fue enviado el mensaje.
        </p>

        <p>
            Lo bueno de este sistema de filtros es que lo he programado para que los puedas utilizar de forma individual, o también puedas combinarlos entre sí como a ti buenamente se te antoje, por ejemplo, puedes filtrar por el nombre de la persona y el día que te fue enviado el mensaje, o también puedes filtrar por el E-mail de la persona y el mes que te fue enviado el mensaje, o también puedes filtrar por el año que te fue enviado el mensaje, o también puedes filtrar por el nombre de la persona, el E-mail de la persona, el día, mes y año que te fue enviado el mensaje ... En fin, tú decides como quieres utilizarlo.
        </p>

        <div class="figura">
            <img src="data:image/png;base64,<?= img64('/El_Arca_de_Noemi/pdf/img/2.1._Mensajes_que_llegan_desde_la_pagina_de_contacto.png') ?>" />
        </div>

        <div class="success">
            Recuerda que todo el contenido que vas a encontrar en los diferentes módulos que vamos a ver en la sección dos de este documento es solo contenido dummy, una vez que la web se pase al servidor de producción, todo este contenido estará vacío y tu tendrás que crearlo a tu gusto.
        </div>

        <div class="volver-indice">
            <a href="#seccion-segunda">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="ver_mensaje">2.1.1. Editar un mensaje de contacto</h3>

        <div class="warning">
            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/" target="_blank">Haz clic aquí para desencriptar la web y acceder al panel de administración</a>
            </div>

            <p>Usuario: ricard624 | Contraseña: mNU1P4hZI36x</p>

            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/modulos/contacto/contacto_editar.php?id=11" target="_blank">Haz clic aquí y se abrirá el módulo de esta sección</a>
            </div>
        </div>

        <img src="data:image/png;base64,<?= img64('/El_Arca_de_Noemi/pdf/img/2.1.1._Editar_un_mensaje_de_contacto.png') ?>" class="img-right" />

        <p>
            Este módulo es sencillo de entender, en el listado cada mensaje tiene dos botones, el botón "Editar" y el botón "Eliminar", cuando hagas clic en el botón "Editar" de algún mensaje del listado entrarás en el módulo de edición del mensaje, algo como lo que ves en la imagen que te dejo a la derecha.
        </p>

        <p>
            Aquí puedes modificar cualquier dato del mensaje o del usuario que lo ha enviado, también tienes un botón con el que directamente puedes eliminar el mensaje.
        </p>

        <div class="volver-indice">
            <a href="#seccion-segunda">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="eliminar_mensaje">2.1.1.1. Eliminar un mensaje de contacto</h3>

        <p>
            Esto es más simple que el mecanismo de un botijo, cuando hagas clic en el botón "Eliminar" de algún mensaje del listado, el mensaje se eliminará directamente de la base de datos.
        </p>

        <div class="danger">
            <p>
                ¡¡CUIDADO NOEMÏ!!
            </p>

            <p>
                Debes tener en cuenta que la eliminación de un mensaje de la base de datos es algo irreversible, una vez que elimines un mensaje no podrás recuperarlo, así que ten mucho cuidado con eso, asegúrate de que realmente quieres eliminar el mensaje antes de darle al botón de eliminar.
            </p>
        </div>

        <div class="volver-indice">
            <a href="#seccion-segunda">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="contacto_intro">2.2. Contenido de la sección de contacto</h3>

        <div class="warning">
            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/" target="_blank">Haz clic aquí para desencriptar la web y acceder al panel de administración</a>
            </div>

            <p>Usuario: ricard624 | Contraseña: mNU1P4hZI36x</p>

            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/modulos/contacto/contacto_intro.php" target="_blank">Haz clic aquí y se abrirá el módulo de esta sección</a>
            </div>
        </div>

        <p>
            Este módulo es el encargado de gestionar el contenido que aparece en la sección de contacto de la web, aquí podrás editar el texto de introducción que aparece a la izquierda del formulario de contacto de la web.
        </p>

        <div class="info">
            Te encontrarás varios módulos parecidos a este en el panel de administración, a simple vista te puede parecer que es simplemente una caja donde puedes escribir texto, pero en realidad es un módulo mucho más complejo de lo que parece, este módulo tiene un editor de texto enriquecido que te permite dar formato al texto, también te permite insertar imágenes, enlaces, tablas ... En fin, es un módulo muy completo que te permite crear contenido de calidad para la web.

            <div class="success">
                Es más, si te fijas en la barra de herramientas del editor de texto enriquecido, podrás ver a la derecha del todo un icono con forma de gatito. Me he tomado la libertad de programar un pequeño módulo para el editor de texto enriquecido que te permitirá añadir emoticonos con forma de animalicos entre tus textos.
            </div>
        </div>

        <div class="figura">
            <img src="data:image/png;base64,<?= img64('/El_Arca_de_Noemi/pdf/img/2.2._Contenido_de_la_seccion_de_contacto.png') ?>" />
        </div>

        <div class="volver-indice">
            <a href="#seccion-segunda">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="asi_es_noemi">2.3. Contenido de la página de presentación de Noemí</h3>

        <div class="warning">
            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/" target="_blank">Haz clic aquí para desencriptar la web y acceder al panel de administración</a>
            </div>

            <p>Usuario: ricard624 | Contraseña: mNU1P4hZI36x</p>

            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/modulos/asi_es_noemi/asi_es_noemi.php" target="_blank">Haz clic aquí y se abrirá el módulo de esta sección</a>
            </div>
        </div>

        <p>
            Aquí podrás editar el contenido que aparece en la página de presentación de Noemí, esta página es la que aparece cuando haces clic en el enlace "Así es Noemí" que se encuentra en el menú principal de la web. En esta página podrás contar la historia de Noemí, cómo surgió la idea de crear "El Arca de Noemí", cuáles son los objetivos de la asociación, quiénes forman parte del equipo ... En fin, aquí tienes total libertad para contar lo que quieras sobre Noemí y sobre la asociación.
        </p>

        <div class="volver-indice">
            <a href="#seccion-segunda">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="politica_de_privacidad">2.4. Contenido de la página de la política de privacidad</h3>

        <div class="warning">
            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/" target="_blank">Haz clic aquí para desencriptar la web y acceder al panel de administración</a>
            </div>

            <p>Usuario: ricard624 | Contraseña: mNU1P4hZI36x</p>

            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/modulos/politica/politica_de_privacidad.php" target="_blank">Haz clic aquí y se abrirá el módulo de esta sección</a>
            </div>
        </div>

        <p>
            Aquí podrás editar el contenido que aparece en la página de la política de privacidad, esta página es la que aparece cuando haces clic en el enlace "Política de Privacidad" que se encuentra en el menú que se encuentra en el pie de página de la web. En esta página podrás contar la política de privacidad de la asociación, cómo se gestionan los datos personales de los usuarios, qué medidas de seguridad se toman para proteger los datos personales ... En fin, aquí tienes total libertad para contar lo que quieras sobre la política de privacidad de la asociación.
        </p>

        <div class="info">
            El contenido que te he dejado en este módulo es completamente funcional y legal, lo único que tienes que hacer es repasarlo para poner los datos del "Arca de Noemí" donde toque, esta página es completamente imprescindible en cualquier página web que se precie, sobre todo si es una institución legal.
        </div>

        <div class="volver-indice">
            <a href="#seccion-segunda">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="opiniones_intro">2.5. Contenido de la sección de las opiniones de los usuarios</h3>

        <div class="warning">
            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/" target="_blank">Haz clic aquí para desencriptar la web y acceder al panel de administración</a>
            </div>

            <p>Usuario: ricard624 | Contraseña: mNU1P4hZI36x</p>

            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/modulos/opiniones/opiniones_intro.php" target="_blank">Haz clic aquí y se abrirá el módulo de esta sección</a>
            </div>
        </div>

        <p>
            En este módulo encontrarás de nuevo un editor de texto enriquecido, aquí podrás editar el texto de introducción que aparece en la sección de las opiniones de los usuarios de la web, esta sección es la que aparece cuando haces clic en el enlace "Opiniones" que se encuentra en el menú principal de la web. En esta sección podrás contar a los usuarios lo importante que es para la asociación conocer su opinión, también puedes contarles cómo pueden dejar su opinión, qué tipo de opiniones se aceptan ... En fin, aquí tienes total libertad para contar lo que quieras sobre las opiniones de los usuarios.
        </p>

        <div class="volver-indice">
            <a href="#seccion-segunda">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="opiniones_listado">2.6. Listado de las opiniones de los usuarios</h3>

        <div class="warning">
            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/" target="_blank">Haz clic aquí para desencriptar la web y acceder al panel de administración</a>
            </div>

            <p>Usuario: ricard624 | Contraseña: mNU1P4hZI36x</p>

            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/modulos/opiniones/opiniones_de_usuario_listado.php" target="_blank">Haz clic aquí y se abrirá el módulo de esta sección</a>
            </div>
        </div>

        <p>
            En este módulo podrás ver el listado de las opiniones que han dejado los usuarios en la sección de las opiniones de los usuarios de la web, algo parecido a la imagen que te debajo de este texto, esta sección es la que aparece cuando haces clic en el enlace "Opiniones" que se encuentra en el menú principal de la web. En este listado podrás ver el nombre del usuario que ha dejado la opinión, su correo electrónico, la fecha en la que dejó la opinión, el mensaje con la opinión que dejó y también tendrás dos botones para cada opinión, el botón "Editar" y el botón "Eliminar".
        </p>

        <div class="figura">
            <img src="data:image/png;base64,<?= img64('/El_Arca_de_Noemi/pdf/img/2.6._Listado_de_las_opiniones_de_los_usuarios.png') ?>" />
        </div>

        <div class="danger">
            <p>
                ¡¡CUIDADO NOEMÏ!!
            </p>

            <p>
                Debes tener en cuenta que la eliminación de un mensaje de la base de datos es algo irreversible, una vez que elimines un mensaje no podrás recuperarlo, así que ten mucho cuidado con eso, asegúrate de que realmente quieres eliminar el mensaje antes de darle al botón de eliminar.
            </p>
        </div>

        <div class="volver-indice">
            <a href="#seccion-segunda">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="opiniones_editar">2.6.1. Editar las opiniones de los usuarios</h3>

        <div class="warning">
            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/" target="_blank">Haz clic aquí para desencriptar la web y acceder al panel de administración</a>
            </div>

            <p>Usuario: ricard624 | Contraseña: mNU1P4hZI36x</p>

            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/modulos/opiniones/opiniones_de_usuario_editar.php?id=3" target="_blank">Haz clic aquí y se abrirá el módulo de esta sección</a>
            </div>
        </div>

        <img src="data:image/png;base64,<?= img64('/El_Arca_de_Noemi/pdf/img/2.6.1._Editar -las_opiniones_de_los_usuarios.png') ?>" class="img-right" />

        <p>
            Este módulo es sencillo de entender, en el listado cada opinión tiene dos botones, el botón "Editar" y el botón "Eliminar", cuando hagas clic en el botón "Editar" de alguna opinión del listado entrarás en el módulo de edición de la opinión, algo como lo que ves en la imagen que te dejo a la derecha.
        </p>

        <div class="info">
            Debes tener en cuenta que si quieres modificar la imagen del usuario, antes de subir una nueva imagen debes marcar la casilla "Eliminar imagen actual", aunque no marques la casilla la imagen se modificará, pero para que se borre del servidor debes marcar la casilla "Eliminar imagen actual".
        </div>

        <div class="volver-indice">
            <a href="#seccion-segunda">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="opniones_eliminar">2.6.1.1. Eliminar las opiniones de los usuarios</h3>

        <div class="warning">
            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/" target="_blank">Haz clic aquí para desencriptar la web y acceder al panel de administración</a>
            </div>

            <p>Usuario: ricard624 | Contraseña: mNU1P4hZI36x</p>

            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/modulos/opiniones/opiniones_de_usuario_listado.php" target="_blank">Haz clic aquí y se abrirá el módulo de esta sección</a>
            </div>
        </div>

        <p>
            Esto es más simple que el mecanismo de un botijo, cuando hagas clic en el botón "Eliminar" de alguna opinión del listado, la opinión se eliminará directamente de la base de datos.
        </p>

        <div class="danger">
            <p>
                ¡¡CUIDADO NOEMÏ!!
            </p>

            <p>
                Debes tener en cuenta que la eliminación de un mensaje de la base de datos es algo irreversible, una vez que elimines un mensaje no podrás recuperarlo, así que ten mucho cuidado con eso, asegúrate de que realmente quieres eliminar el mensaje antes de darle al botón de eliminar.
            </p>
        </div>

        <div class="volver-indice">
            <a href="#seccion-segunda">Volver al índice</a>
        </div>

        <div style="clear: both;"></div>

        <div class="separador-seccion"></div>

        <div class="separador"></div>

    </div>

    <!-- 3. Tercera sección - Sarcásmo y humor -->
    <div class="seccion page-break" id="sarcasmo_y_humor">

        <h2>3. Tercera Sección - Sarcásmo y Humor</h2>

        <p>
            Esta sección la vamos a pasar por encima, no te preocupes Noemí, no es nada importante, es más, creo que es la sección más prescindible de todas las que vas a encontrar en el panel de administración, pero como me gusta el sarcasmo y el humor, he decidido dedicarle una sección exclusiva a esta temática, así que aquí te dejo toda la información que necesitas para gestionar el contenido de esta sección, aunque como te digo, no es nada importante, así que no te preocupes por ello.
        </p>

        <div class="volver-indice">
            <a href="#seccion-tercera">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="noemi_dice">3.1. Incluir las frases de "Noemí dice" en el sitio web</h3>

        <div class="warning">
            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/" target="_blank">Haz clic aquí para desencriptar la web y acceder al panel de administración</a>
            </div>

            <p>Usuario: ricard624 | Contraseña: mNU1P4hZI36x</p>

            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/modulos/noemi_dice/noemi_dice.php" target="_blank">Haz clic aquí y se abrirá el módulo de esta sección</a>
            </div>
        </div>

        <p>
            En este módulo encontrarás un editor de texto simple, aquí es donde podrás echarle imaginación y escribir las frases de "Noemí dice" que aparecen en la página de inicio de la web.
        </p>

        <p>
            Verás que lo he ideado para que sean frases cortas, como siempre he hecho un poco el gamba y he dado mi toque sarcástico a la sección, pero tú puedes escribir lo que quieras, frases graciosas, frases motivadoras, frases inspiradoras ... En fin, aquí tienes total libertad para escribir lo que quieras.
        </p>

        <div class="info">
            <p>
                Ten en cuenta que las frases de este bloque se muestran de forma aleatoria cada vez que un usuario accede a la página web, por lo que no debes preocuparte en el orden en el que escribas las frases, ya que el sistema se encargará de mostrarlas de forma aleatoria.
            </p>

            <p>
                Si por alguna razón quieres mantener las frases dummy que he creado yo, puedes hacerlo, solo tienes que decirlo y cuando haga el traslado de la base de datos al servidor de producción mantengo esta tabla.
            </p>
        </div>

        <div class="volver-indice">
            <a href="#seccion-tercera">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="listado_noemi_dice">3.2. Listado de las frases de "Noemí dice"</h3>

        <div class="warning">
            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/" target="_blank">Haz clic aquí para desencriptar la web y acceder al panel de administración</a>
            </div>

            <p>Usuario: ricard624 | Contraseña: mNU1P4hZI36x</p>

            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/modulos/noemi_dice/noemi_dice_listado.php" target="_blank">Haz clic aquí y se abrirá el módulo de esta sección</a>
            </div>
        </div>

        <p>
            En este módulo podrás ver un listado con todas las frases que has escrito en el módulo anterior, algo parecido a la imagen que te dejo debajo de este texto. Como en todos los módulos con listados de algún tipo que he creado en el panel de administración, en este también tienes un sistema de filtros vinculante para que puedas navegar entre tus frases de forma rápida y sencilla.
        </p>

        <p>
            Hay algunos botoncicos que aparecen en cada frase y que te voy a detallar después de la imagen.
        </p>

        <div class="figura">
            <img src="data:image/png;base64,<?= img64('/El_Arca_de_Noemi/pdf/img/3.2._Listado_de_las_frases_de_Noemi_dice.png') ?>" />
        </div>

        <div class="volver-indice">
            <a href="#seccion-tercera">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="listado_noemi_dice_ocultar">3.2.1. Ocultar las frases de "Noemí dice"</h3>

        <div class="warning">
            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/" target="_blank">Haz clic aquí para desencriptar la web y acceder al panel de administración</a>
            </div>

            <p>Usuario: ricard624 | Contraseña: mNU1P4hZI36x</p>

            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/modulos/noemi_dice/noemi_dice_listado.php" target="_blank">Haz clic aquí y se abrirá el módulo de esta sección</a>
            </div>
        </div>

        <p>
            Por defecto al crear una frase de "Noemí dice" está visible en la web, es decir que su estado predeterminado es "Aprobada", por lo tanto, después de crear las frases de "Noemí dice" y entrar al listado, verás que todas las frases tienen a su derecha un botón de color lila con la leyenda "Ocultar", si haces clic en ese botón la frase se ocultará en la web, es decir que su estado cambiará a "Oculta", por lo tanto, el botón de color lila con la leyenda "Ocultar" se convertirá en un botón de color verde con la leyenda "Aprobar".
        </p>

        <div class="volver-indice">
            <a href="#seccion-tercera">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="listado_noemi_dice_aprobar">3.2.1.1. Aprobar las frases de "Noemí dice"</h3>

        <div class="warning">
            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/" target="_blank">Haz clic aquí para desencriptar la web y acceder al panel de administración</a>
            </div>

            <p>Usuario: ricard624 | Contraseña: mNU1P4hZI36x</p>

            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/modulos/noemi_dice/noemi_dice_listado.php" target="_blank">Haz clic aquí y se abrirá el módulo de esta sección</a>
            </div>
        </div>

        <p>
            Si haces clic en el botón de color verde con la leyenda "Aprobar" de alguna frase del listado, la frase se aprobará en la web, es decir que su estado cambiará a "Aprobada", por lo tanto, el botón de color verde con la leyenda "Aprobar" se convertirá en un botón de color lila con la leyenda "Ocultar".
        </p>

        <div class="volver-indice">
            <a href="#seccion-tercera">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="listado_noemi_dice_eliminar">3.2.1.1.1. Eliminar las frases de "Noemí dice"</h3>

        <div class="warning">
            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/" target="_blank">Haz clic aquí para desencriptar la web y acceder al panel de administración</a>
            </div>

            <p>Usuario: ricard624 | Contraseña: mNU1P4hZI36x</p>

            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/modulos/noemi_dice/noemi_dice_listado.php" target="_blank">Haz clic aquí y se abrirá el módulo de esta sección</a>
            </div>
        </div>

        <p>
            Si haces clic en el botón de color rojo con la leyenda "Eliminar" de alguna frase del listado, la frase se eliminará directamente de la base de datos, por lo tanto, ten mucho cuidado con eso, asegúrate de que realmente quieres eliminar la frase antes de darle al botón de eliminar, ya que una vez que elimines una frase no podrás recuperarla.
        </p>

        <div class="danger">
            <p>
                ¡¡CUIDADO NOEMÏ!!
            </p>

            <p>
                Debes tener en cuenta que la eliminación de un mensaje de la base de datos es algo irreversible, una vez que elimines un mensaje no podrás recuperarlo, así que ten mucho cuidado con eso, asegúrate de que realmente quieres eliminar el mensaje antes de darle al botón de eliminar.
            </p>
        </div>

        <div class="volver-indice">
            <a href="#seccion-tercera">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="bichillos_de_noemi">3.3. Incluir los "Bichillos de Noemí" en el sitio web</h3>

        <div class="warning">
            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/" target="_blank">Haz clic aquí para desencriptar la web y acceder al panel de administración</a>
            </div>

            <p>Usuario: ricard624 | Contraseña: mNU1P4hZI36x</p>

            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/modulos/bichillos/noemi_bichillos.php" target="_blank">Haz clic aquí y se abrirá el módulo de esta sección</a>
            </div>
        </div>

        <p>
            Este módulo es bastante sencillo de entender, aquí te encontrarás simplemente con un campo que te permitirá subir hasta 10 imágenes cada vez para el bloque de "Los Bichillos de Noemí". Este bloque es que aparece a la izquierda del tercer módulo de la página de inicio, al igual que las frases de "Noemí dice", las imágenes de "Los Bichillos de Noemí" se muestran de forma aleatoria.
        </p>

        <p>
            No debes preocuparte por el tamaño de las imágenes, ya que está programado para redimensionarlas. Lo único que debes tener en cuenta es que el contenedor para la imagen es cuadrado, y si la imagen es rectangular el sistema la centrará y mostrará el contenido central de la imagen, lo suyo es que subas imágenes donde los bichillos estén centrados.
        </p>

        <div class="volver-indice">
            <a href="#seccion-tercera">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="listado_bichillos_de_noemi">3.4. Listado de los "Bichillos de Noemí"</h3>

        <div class="warning">
            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/" target="_blank">Haz clic aquí para desencriptar la web y acceder al panel de administración</a>
            </div>

            <p>Usuario: ricard624 | Contraseña: mNU1P4hZI36x</p>

            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/modulos/bichillos/noemi_bichillos_listado.php" target="_blank">Haz clic aquí y se abrirá el módulo de esta sección</a>
            </div>
        </div>

        <p>
            En este módulo no me voy a extender mucho porque es exactamente igual que el módulo de listado de las frases de "Noemí dice", aquí podrás ver un listado con todas las imágenes que has subido para el bloque de "Los Bichillos de Noemí", algo parecido a la imagen que te dejo debajo de este texto. Como en todos los módulos con listados de algún tipo que he creado en el panel de administración, en este también tienes un sistema de filtros vinculante para que puedas navegar entre tus imágenes de forma rápida y sencilla. En este módulo también tienes un botón para ocultar la imagen en la web, un botón para mostrar la imagen en la web y un botón para eliminar la imagen de la base de datos.
        </p>

        <div class="volver-indice">
            <a href="#seccion-tercera">Volver al índice</a>
        </div>

        <div style="clear: both;"></div>

        <div class="separador-seccion"></div>

        <div class="separador"></div>

    </div>

    <!-- 4. Cuarta sección - Registro -->
    <div class="seccion page-break" id="registro">

        <h2>4. Cuarta Sección - Registro</h2>

        <p>
            Hasta ahora hemos estado viendo algunos módulos de nivel medio/bajo, pero a partir de este momento nos vamos a meter en terreno pantanoso, ya que los próximos módulos que vamos a ir viendo SÍ son de interés medio/alto. En concreto, esta cuarta sección que dedico al bloque de los módulos de "Registro", para mí posiblemente sea la más importante de todas, ya que desde los módulos de este bloque comenzarás a construir realmente tu página web.
        </p>

        <div class="volver-indice">
            <a href="#seccion-cuarta">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="registro_especie_raza">4.1. Registrar una nueva especie o raza de animal</h3>

        <div class="warning">
            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/" target="_blank">Haz clic aquí para desencriptar la web y acceder al panel de administración</a>
            </div>

            <p>Usuario: ricard624 | Contraseña: mNU1P4hZI36x</p>

            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/modulos/registros/adopciones_incluir.php" target="_blank">Haz clic aquí y se abrirá el módulo de esta sección</a>
            </div>
        </div>

        <img src="data:image/png;base64,<?= img64('/El_Arca_de_Noemi/pdf/img/4.1._Registrar_una_nueva_especie_o_raza_de_animal.png') ?>" class="img-left" />

        <p>
            Este primer módulo es muuuuuy (con muchísimas us) importante, cuando me presentaste tu proyecto de "El Arca de Noemí" enseguida me dí cuenta de que a pesar de las semejanzas, realmente no tenía nada que ver con el proyecto de Míriam "La Gatopía de Míriam". Para mí el proyecto de Míriam fue relativamente sencillo, ya que me tuve que enfocar solo en dos tipos de animales, perros y peludos. Pero "El Arca de Noemí" es otra cosa, en el Arca no os dedicáis a una o dos especies animales sino que estáis abiertos a dar refugio a cualquier tipo de animal que lo necesite, y esto me hizo pensar bastante en cómo enfocar el principal proceso del proyecto ... La gestión de las especies y razas animales.
        </p>

        <p>
            Y así nació este módulo, un módulo completamente flexible, semi automático y me atrevería a decir que es casi inteligente, te cuento cómo funciona.
        </p>

        <p>
            Como siempre te dejo una imagen de referencia para que te hagas a la idea de lo que te encontrarás al entrar al módulo, el primer campo es un selector inteligente con todas las especies que hay en la base de datos, como será la primera vez que entres al módulo, este selector estará vacío y no cumplirá con su función, para ello tendrás el siguiente campo, un campo tipo input donde tendrás que escribir el nombre de la especie animal que quieres crear.
        </p>

        <p>
            Con las razas pasa exactamente lo mismo, tienes el campo selector inteligente que al ser la primera vez no funcionará, y debajo el campo input, donde tendrás que añadir la raza animal que quieras vincular a la especie que has puesto más arriba. Sé un poquita lógica, Noemí, si la primera vez creas la especie "Perro", no le vincules la raza "Siamés", hazme el favor.
        </p>

        <p>
            ¿Qué pasará en este módulo una vez que hayas creado varias especies y razas animales?, Pues que los campos de selección inteligentes empezarán a funcionar, y la magia hará acto de presencia. Al haber creado varias especies podrás abrir el campo de selección de las especies, verás que será abrumador porque tendrás un listado infinito de especies, tú tranquila Noemí, yo he pensado en todo, ¿Qué busca la especie "Cabra"?, sencillo, empieza a teclear C-A-B ... El selector inteligente te irá descartando especies hasta encontrar "Cabra", eso sí, siempre que exista.
        </p>

        <p>
            Y con las razas, ¿Qué pasa con las razas Ricardito?, pues esto es más chulo todavía, el selector de razas, a pesar de que hayas creado varias razas, estará bloqueado ... Hasta que escojas una especie en el selector de especies, en el momento en el que escojas una especie, por ejemplo "Perro", el selector de razas se activará, peeeeeeeero ... Solo mostrará las razas vinculadas a la especie "Perro".
        </p>

        <div class="warning">
            Y por último lo más divertido de todo, para que no puedas equivocarte y que el sistema se vuelva loco con duplicados, en el caso de que utilizaras el campo input, ya sea de especies o razas, para crear una nueva especie o raza, y esta ya existiera en la base de datos, te aparecería un mensaje advirtiéndote de que ya existe y no te la dejaría crear.
        </div>

        <div class="volver-indice">
            <a href="#seccion-cuarta">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="registro_nuevo_animal_adopcion">4.2. Registrar un nuevo animal en adopción</h3>

        <div class="warning">
            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/" target="_blank">Haz clic aquí para desencriptar la web y acceder al panel de administración</a>
            </div>

            <p>Usuario: ricard624 | Contraseña: mNU1P4hZI36x</p>

            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/modulos/registros/adopciones.php" target="_blank">Haz clic aquí y se abrirá el módulo de esta sección</a>
            </div>
        </div>

        <div class="warning">
            Pon atención porque este módulo es el más importante de todos los que vas a encontrar en el panel de administración, desde este módulo vas a dar de alta en tu página web a todos los animales que quieras poner en adopción.
        </div>

        <p>
            Pero tú no tienes que preocuparte por nada Noemí, yo ya he pensado en todo para facilitarte el trabajo lo máximo posible, para no hacer esta sección demasiado larga, voy a hacerte un listado con cada campo y su correspondiente función.
        </p>

        <div class="info">
            No obstante tú sabes mucho mejor que yo las necesidades del "Arca de Noemí", por lo tanto, si crees que es necesario añadir, o quitar, algún campo solo tienes que decirlo. Yo me he guiado por mi humilde conocimiento del tema ... Y no es mucho la verdad.
        </div>

        <table>
            <tr>
                <th>Campo</th>
                <th>Función</th>
            </tr>
            <tr>
                <td>Nombre:</td>
                <td>Sencillo, aquí solo debes poner el nombre del animal.</td>
            </tr>
            <tr>
                <td>Especie:</td>
                <td>Este selecctor funciona igual que el del módulo anterior, mostrará las especies que hayas creado anteriormente.</td>
            </tr>
            <tr>
                <td>Raza:</td>
                <td>Exactamente lo mismo que en el módulo anterior, una vez elegida una especie aparecerán las razas vinculadas a ella.</td>
            </tr>
            <tr>
                <td>Sexo:</td>
                <td>Está más que claro, bueno si rescatas algún caracol siempre puedo añadir "Hermafrodita".</td>
            </tr>
            <tr>
                <td>Edad:</td>
                <td>Está claro que la edad siempre será una estimación, pero es un campo necesario, sobre todo para los visitantes.</td>
            </tr>
            <tr>
                <td>Fecha de nacimiento:</td>
                <td>Este campo es solo para los animales nacidos en "El Arca de Noemí".</td>
            </tr>
            <tr>
                <td>Tamaño:</td>
                <td>He pensado que dar una estimación del tamaño de cada animal puede ayudar a la toma de decisión para adoptar.</td>
            </tr>
            <tr>
                <td>Peso:</td>
                <td>No tiene mucho misterio, es el peso aproximado del animal.</td>
            </tr>
            <tr>
                <td>Estado de salud:</td>
                <td>Este campo sirve para dejar un breve comentario sobre el historial clínico del animal.</td>
            </tr>
            <tr>
                <td>Campos de vacunación:</td>
                <td>Es muy importante añadir todos estos datos.</td>
            </tr>
            <tr>
                <td>Microchip:</td>
                <td>Saber si tiene el microchip ya puesto es un empujoncito más para la toma de decisiones.</td>
            </tr>
            <tr>
                <td>Fecha de ingreso:</td>
                <td>Este campo es para tomarlo como referencia de la cuarentena que pasa cada animal, por eso lo he llamado "Fecha de ingreso".</td>
            </tr>
            <tr>
                <td>Fecha de rescate:</td>
                <td>Está más que claro, este campo es para poner la fecha en la que fue rescatado el animal.</td>
            </tr>
            <tr>
                <td>Disponible para adopción:</td>
                <td>Este checkbox es sumamente importante, aunque añadas un animal desde este módulo, puedes decidir si quieres que aparezca en la página web o no marcando este campo. Y de la misma manera, cuando un animal se vincula a un adoptante, este campo se desmarca de forma automática.</td>
            </tr>
            <tr>
                <td>Descripción:</td>
                <td>Todo animal tiene una historia que contar y para eso es este editor de texto enriquecido.</td>
            </tr>
            <tr>
                <td>Fotos del animal:</td>
                <td>En la página individual de cada animal hay una galería de fotos, desde este campo puedes subir tantas imágenes como quieras, eso sí, de diez en diez.</td>
            </tr>
        </table>

        <div class="volver-indice">
            <a href="#seccion-cuarta">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="registro_nuevo_adoptante">4.3. Registrar un nuevo posible adoptante</h3>

        <div class="warning">
            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/" target="_blank">Haz clic aquí para desencriptar la web y acceder al panel de administración</a>
            </div>

            <p>Usuario: ricard624 | Contraseña: mNU1P4hZI36x</p>

            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/modulos/registros/adopciones_adoptante.php" target="_blank">Haz clic aquí y se abrirá el módulo de esta sección</a>
            </div>
        </div>

        <img src="data:image/png;base64,<?= img64('/El_Arca_de_Noemi/pdf/img/4.3._Registrar_un_nuevo_posible_adoptante.png') ?>" class="img-left" />

        <p>
            Este módulo habla por sí mismo, desde aquí es desde donde podrás añadir a la base de datos todos los adoptantes que hayas tenido, que tengas en proceso, o que estén en espera de posible adopción, realmente verás que es un módulo muy básico, esto es simplemente porque yo he pensado en los datos básicos de una persona, imagino que seguramente tu necesitarás muchísimos más datos de un posible adoptante, y quiero que sepas que no hay absolutamente ningún problema, mientras la página web esté en mi servidor de pruebas encriptado podemos hacer y deshacer todo lo que quieras, por lo tanto, si es como yo imagino y en este módulo se han de incluir muchos más campos solo tienes que decirlo, y yo los añado.
        </p>

        <div style="width: 100%; display: block; overflow: hidden;"></div>

        <div class="info">
            De todas maneras más adelante veremos el módulo de los adoptantes que proceden del formulario que he creado en la página web, si quieres echarle un vistazo podrás ver que este formulario es muchísimo más extenso que este módulo, te dejo aquí un enlace directo al formulario de la página web.

            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/formulario-adoptante?id=6" target="_blank">Haz clic aquí y se abrirá la página web con el formulario de adopciones</a>
            </div>
        </div>

        <div class="volver-indice">
            <a href="#seccion-cuarta">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="registro_nuevo_animal_apadrinar">4.4. Registrar un nuevo animal para apadrinar</h3>

        <div class="warning">
            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/" target="_blank">Haz clic aquí para desencriptar la web y acceder al panel de administración</a>
            </div>

            <p>Usuario: ricard624 | Contraseña: mNU1P4hZI36x</p>

            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/modulos/registros/apadrinamiento_incluir.php" target="_blank">Haz clic aquí y se abrirá el módulo de esta sección</a>
            </div>
        </div>

        <img src="data:image/png;base64,<?= img64('/El_Arca_de_Noemi/pdf/img/4.4._Registrar_un_nuevo_animal_para_apadrinar.png') ?>" class="img-right" />

        <p>
            Como ya hablamos en su momento aquí te presento el módulo para incluir animales para apadrinar en la base de datos de la página web, quizá te preguntes por qué separo adopciones de apadrinamientos en vez de hacerlo todo junto, pero es muy sencillo, no todos los animales que tengas en adopción puedes ponerlos para apadrinar. ¿Pero por qué?, por una sencilla razón, si tienes un peludo Siamés puesto en adopción y al mismo tiempo en apadrinamiento ... ¿Qué haces con los apadrinamientos si alguien adopta al peludo?, es de lógica, en el momento en que un animal es adoptado ya no puede ser apadrinado y si estuviera en ambos lugares tendrías un pequeño follón del copón. Cuando el peludo Siamés fuera adoptado, deberías comunicar personalmente a todos los padrinos que ha encontrado un hogar y que vas a cancelar sus suscripciones (evidentemente podrías venderles la moto de que pueden apadrinar a otro peludo ...). Pero yo creo que lo más lógico y sensato es mantener separadas las adopciones de los apadrinamientos.
        </p>

        <p>
            Como siempre en este módulo encontrarás algo parecido a la imagen de la derecha, creo que ya no hace falta explicarte cómo funcionan los campos de selección inteligentes de las especies y las razas, su funcionamiento es el mismo en todos los módulos. La descripción breve se utiliza para que puedas poner detalles sobresalientes del animal, es un pequeño trastito, le gusta mordisquear los dedos de los pies ... En este caso el campo para subir las imágenes funciona de manera diferente al de las adopciones, para los apadrinamientos no he programado una galería de fotos por lo que solo puedes subir una imagen por animal, pero ya sabes estamos de pruebas, puedo programar la galería si la quieres.
        </p>

        <p>
            El sistema de suscripciones lo he programado con PayPal, es la manera más flexible y sencilla, sobre todo para ti, y mucho más si eres asociación, el sistema de apadrinamientos está completamente operativo, pero de la misma manera que la página web está alojada en mi servidor de pruebas, el sistema de suscripciones funciona con mi cuenta de PayPal Sandbox Developer, con eso quiero decir que funciona perfectamente porque está en un modo de pruebas que PayPal nos facilita a los desarrolladores. Si quieres ver qué pasa en el panel de administración y en la ficha del animal cuando alguien decide apadrinar a uno de los "Bichillos de Noemí", entra en cualquier ficha de uno de los animales en apadrinamiento que he creado y haz clic en el botón "Quiero apadirnar a ...", Inventade los datos y cuando aparezca la pasarela de pago de PayPal utiliza la tarjeta que te dejo aquí debajo.
        </p>

        <div class="info">
            <ul>
                <li>Visa</li>
                <li>4020 0249 1752 0940</li>
                <li>Fecha caducidad - 01/2031</li>
                <li>CVC - 123</li>
            </ul>
        </div>

        <div class="volver-indice">
            <a href="#seccion-cuarta">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="registro_nueva_plataforma_crowdfunding">4.5. Registrar una nueva plataforma de Crowd Funding</h3>

        <div class="warning">
            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/" target="_blank">Haz clic aquí para desencriptar la web y acceder al panel de administración</a>
            </div>

            <p>Usuario: ricard624 | Contraseña: mNU1P4hZI36x</p>

            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/modulos/crowdfunding/incluir_plataforma_crowdfunding.php" target="_blank">Haz clic aquí y se abrirá el módulo de esta sección</a>
            </div>
        </div>

        <p>
            Doy más que por sentado que sabes lo que son las plataformas de Crowd Funding, y como me dijiste que en Facebook tenías una buena cantidad de seguidores, también doy más que por sentado que haces uso de estas plataformas a menudo, por eso he programado este y sus módulos vinculantes.
        </p>

        <div class="figura">
            <img src="data:image/png;base64,<?= img64('/El_Arca_de_Noemi/pdf/img/4.5._Registrar_una_nueva_plataforma_de_CrowdFunding.png') ?>" />
        </div>

        <p>
            Como puedes ver en la imagen de arriba, este módulo es muy sencillo, este módulo sirve para que puedas crear las plataformas de Crowd Funding con las que tú trabajes habitualmente, o con las que trabajes en el futuro. Es tan sencillo como añadir el nombre de la plataforma y subir el logotipo con el campo que tienes para subir imágenes, lo único que tendrás que hacer tú, es buscar el logotipo de la plataforma en cuestión, pero en nuestro alabado Google Images lo puedes encontrar todo.
        </p>

        <div class="info">
            No te preocupes por las plataformas que vas a tener creadas en la base de datos, en la siguiente sección, que es la sección cinco, nos metemos con las plataformas de Crowd Funding de lleno.
        </div>

        <div class="volver-indice">
            <a href="#seccion-cuarta">Volver al índice</a>
        </div>

        <div style="clear: both;"></div>

        <div class="separador-seccion"></div>

        <div class="separador"></div>

    </div>

    <!-- 5. Quinta sección - Plataformas de Crowd Funding -->
    <div class="seccion page-break" id="plataformas_crowdfunding">

        <h2>5. Quinta Sección - Plataformas de Crowd Funding</h2>

        <p>
            Y por fin hemos llegado a lo que nos interesa, el parné, la guita, la pasta, los pavos, la plata ... Ya sabes el dinerico. Ahora, nos metemos de lleno con las plataformas de Crowd Funding, aunque un poco más arriba le dimos un par de pinceladas a este tema, es en este bloque donde vamos a repasar todos los módulos que componen el sistema de flujo de trabajo que te he programado para que puedas gestionar de la manera más flexible y sencilla tus plataformas de Crowd Funding.
        </p>

        <div class="info">
            Recuerda que más arriba, en la sección "<a href="#registro_nueva_plataforma_crowdfunding" class="infoco">4.5. Registrar una nueva plataforma de Crowd Funding</a>" Estuvimos repasando el módulo para registrar las nuevas plataformas de Crowd Funding en la base de datos, por lo tanto, ya partes con la base de que ya tienes las plataformas creadas.
        </div>

        <div class="volver-indice">
            <a href="#seccion-quinta">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="plataforma_crear_nueva_recaudacion">5.1. Crear una nueva recaudación de fondos en una plataforma de Crowd Funding</h3>

        <div class="warning">
            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/" target="_blank">Haz clic aquí para desencriptar la web y acceder al panel de administración</a>
            </div>

            <p>Usuario: ricard624 | Contraseña: mNU1P4hZI36x</p>

            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/modulos/crowdfunding/crear_recaudacion.php" target="_blank">Haz clic aquí y se abrirá el módulo de esta sección</a>
            </div>
        </div>

        <img src="data:image/png;base64,<?= img64('/El_Arca_de_Noemi/pdf/img/5.1._Crear_una_nueva_recaudacion_de_fondos.png') ?>" class="img-left" />

        <p>
            En este módulo vas a crear todas tus futuras recaudaciones de fondos Noemí, como puedes ver en la imagen de la izquierda, el primer campo es un selector inteligente, al desplegarse verás las plataformas que tú hayas creado en el módulo de la sección <a href="#registro_nueva_plataforma_crowdfunding" class="infoco">"4.5. Registrar una nueva plataforma de Crowd Funding"</a>.
        </p>

        <p>
            Hagamos una pequeña paradita en el campo "Cantidad recaudada", este campo es opcional, es para que los usuarios de la página web puedan ver cómo van las recaudaciones sin necesidad de ir a la plataforma. Lo bueno de este campo es que si no lo utilizas no pasa nada, la página web no lo echará de menos.
        </p>

        <div class="volver-indice">
            <a href="#seccion-quinta">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="plataforma_listado">5.2. Listado de las recaudaciones de fondos en las plataformas de Crowd Funding</h3>

        <div class="warning">
            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/" target="_blank">Haz clic aquí para desencriptar la web y acceder al panel de administración</a>
            </div>

            <p>Usuario: ricard624 | Contraseña: mNU1P4hZI36x</p>

            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/modulos/crowdfunding/listado_recaudaciones.php" target="_blank">Haz clic aquí y se abrirá el módulo de esta sección</a>
            </div>
        </div>

        <div class="figura">
            <img src="data:image/png;base64,<?= img64('/El_Arca_de_Noemi/pdf/img/5.2._Listado_de_las_recaudaciones.png') ?>" />
        </div>

        <p>
            Bien Noemí, ya hemos visto cómo incluir nuevas plataformas a la base de datos y cómo crear nuevas recaudaciones de fondos para la página web, ahora nos toca gestionar todo esto desde el panel de administración, y para hacer eso tenemos un listado con todas las recaudaciones de fondos que tengas en marcha. Será algo como lo que puedes ver en la imagen que te dejo encima de este texto
        </p>

        <p>
            Como en todos los listados que te he preparado, lo primero que tienes es el sistema de filtros inteligentes vinculados al paginado. Este sistema te permite filtrar por la plataforma de Crowd Funding que quieras, por el estado de las recaudaciones de fondos (activa/inactiva), y por el mínimo o el máximo que lleven recaudado. Estos dos parámetros son un poco delicados, el sistema de filtros solo mostrará en los resultados las recaudaciones en las que tú hayas incluido la cantidad recaudada hasta el momento, las recaudaciones en las que hayas dejado este campo en blanco se omitirán del resultado del filtrado.
        </p>

        <p>
            En la quinta columna del listado podrás ver un botón con la leyenda "Ver campaña", al hacer clic sobre este botón se abre una nueva ventana en tu navegador con la página web de la recaudación de fondos en cuestión, es simplemente para que lo tengas a mano.
        </p>

        <p>
            Y por último, como en todos los listados, verás los dos típicos botones, "Editar" y "Eliminar", el botón "Editar" lo vemos en el siguiente punto, y bueno el botón "Eliminar" ya sabes lo que hace, no tiene mucho misterio.
        </p>

        <div class="danger">
            <p>
                ¡¡CUIDADO NOEMÏ!!
            </p>

            <p>
                Debes tener en cuenta que la eliminación de un mensaje de la base de datos es algo irreversible, una vez que elimines un mensaje no podrás recuperarlo, así que ten mucho cuidado con eso, asegúrate de que realmente quieres eliminar el mensaje antes de darle al botón de eliminar.
            </p>
        </div>

        <div class="volver-indice">
            <a href="#seccion-quinta">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="plataforma_listado_editar">5.2.1. Editar la recaudación de fondos de la plataforma de Crowd Funding</h3>

        <div class="warning">
            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/" target="_blank">Haz clic aquí para desencriptar la web y acceder al panel de administración</a>
            </div>

            <p>Usuario: ricard624 | Contraseña: mNU1P4hZI36x</p>

            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/modulos/crowdfunding/editar_recaudacion.php?id=5" target="_blank">Haz clic aquí y se abrirá el módulo de esta sección</a>
            </div>
        </div>

        <img src="data:image/png;base64,<?= img64('/El_Arca_de_Noemi/pdf/img/5.2.1._Editar_la_recaudacion_de_fondos.png') ?>" class="img-right" />

        <p>
            Como puedes ver en la imagen que te dejo a la derecha de este texto, el módulo de edición de las recaudaciones de fondos es prácticamente igual que el módulo de creación, la única diferencia es que en este módulo debes estar completamente segura de lo que haces, ya que cualquier modificación errónea en una recaudación de fondos activa, puede hacer que los datos de tu página web y los datos de la página web de la recaudación de fondos no cuadren, y esos son los pequeños errores que marcan la distinción entre un trabajo bien hecho ... O un trabajo hecho rápido y al tun tún.
        </p>

        <div class="volver-indice">
            <a href="#seccion-quinta">Volver al índice</a>
        </div>

        <div style="clear: both;"></div>

        <div class="separador-seccion"></div>

        <div class="separador"></div>

    </div>

    <!-- 6. Sexta sección - Adopciones -->
    <div class="seccion page-break" id="adopciones">

        <h2>6. Sexta Sección - Adopciones</h2>

        <p>
            Y ya hemos llegado a una de los bloques de módulos más importantes de todo el panel de administración, vamos a meterle mano a todo el flujo de trabajo del sistema de adopciones que he programado exclusivamente para ti, creo que es muuuuuy (con muchísimas Us) que repases bien todos los puntos de este bloque, me gustaría que entendieras a la perfección cómo funciona todo el sistema de adopciones que he programado para ti, más que nada porque yo no sé hasta qué punto he podido llegar a satisfacer todas las necesidades que tú puedas tener en "El Arca de Noemí" a la hora de gestionar las adopciones. Quizá necesites recopilar más datos, incluir módulos nuevos, eliminar/modificar alguno existente.
        </p>

        <div class="success">
            Ya se que lo repito muchas veces Noemí, pero estamos en mi servidor de pruebas, y mientras la web esté aquí solo es visible para quien tenga el usuario y contrasena des encriptación, por lo tanto, mientras la página web esté en mi servidor podemos hacer lo que quieras con ella, y en este caso yo si puedo hacer realidad tus sueños ... Así que sueña y cuéntame que has soñado.
        </div>

        <div class="volver-indice">
            <a href="#seccion-sexta">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="iniciar_nueva_adopcion">6.1. Iniciar el proceso de adopción de un animal</h3>

        <div class="warning">
            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/" target="_blank">Haz clic aquí para desencriptar la web y acceder al panel de administración</a>
            </div>

            <p>Usuario: ricard624 | Contraseña: mNU1P4hZI36x</p>

            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/modulos/adopciones/sistema_adopciones_iniciar.php" target="_blank">Haz clic aquí y se abrirá el módulo de esta sección</a>
            </div>
        </div>

        <div class="figura">
            <img src="data:image/png;base64,<?= img64('/El_Arca_de_Noemi/pdf/img/6.1._Iniciar_el_proceso_de_adopcion_de_un_animal.png') ?>" />
        </div>

        <p>
            Este módulo también es uno de los más importantes de todos los que te encontrarás en todo el panel de administración, desde este módulo es desde el que podrás vincular un animal con una persona/adoptante.
        </p>

        <p>
            El primer campo es un selector inteligente que te muestra todos los animales que hay disponibles en adopción en la base de datos de la página web, ¡¡OJO!!, solo te mostrará los que estén marcados como disponibles en adopción, los animales que ya estén vinculados a otros adoptantes no se mostrarán en este listado, a no ser que la adopción se haya marcado como "Cancelada". Para que lo tengas más fácil a la hora de buscar el animal en cuestión en el selector, solo debes empezar a teclear su nombre tal cual lo escribiste en la base de datos, el selector hará el resto por ti.
        </p>

        <p>
            El segundo campo también es un selector inteligente, su funcionamiento es exactamente el mismo que el selector anterior, pero en este caso lo que te da a elegir son los adoptantes que haya en la base de datos. ¡¡OJO!!, En este caso, el selector sí te mostrará todos los adoptantes que haya en la base de datos, estén vinculados ya a otros animales o no. Esto es así porque en el caso anterior un animal no puede tener dos adoptantes, pero en este caso un mismo adoptante sí puede hacer varias adopciones.
        </p>

        <div class="warning">
            Yo he puesto los campos que me han parecido básicos, estoy más que seguro de que tú necesitarás muchos más datos a la hora de crear una adopción, pero ya lo sabes, te lo he dicho muchas veces ... Ahora podemos añadir/modificar/eliminar lo que tú necesites.
        </div>

        <div class="volver-indice">
            <a href="#seccion-sexta">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="listado_de_adoptantes">6.2. Listado de todos los adoptantes de la base de datos</h3>

        <div class="warning">
            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/" target="_blank">Haz clic aquí para desencriptar la web y acceder al panel de administración</a>
            </div>

            <p>Usuario: ricard624 | Contraseña: mNU1P4hZI36x</p>

            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/modulos/adopciones/sistema_adopciones_listado_adoptantes.php" target="_blank">Haz clic aquí y se abrirá el módulo de esta sección</a>
            </div>
        </div>

        <p>
            Y ahora que ya sabes cómo crear nuevas adopciones lo más lógico es que te enseñe a gestionarlas y para eso tenemos que irnos al listado de adopciones. En este listado verás algo como lo que te dejo en la imagen de debajo de este texto, pero como este listado es algo más complejo que los que hemos visto hasta el ahora voy a desglosarlo punto por punto.
        </p>

        <div class="figura">
            <img src="data:image/png;base64,<?= img64('/El_Arca_de_Noemi/pdf/img/6.2._Listado_de_todos_los_adoptantes.png') ?>" />
        </div>

        <div class="volver-indice">
            <a href="#seccion-sexta">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="filtros_listado_adoptantes">6.2.1. Filtros para el listado de adoptantes</h3>

        <div class="warning">
            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/" target="_blank">Haz clic aquí para desencriptar la web y acceder al panel de administración</a>
            </div>

            <p>Usuario: ricard624 | Contraseña: mNU1P4hZI36x</p>

            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/modulos/adopciones/sistema_adopciones_listado_adoptantes.php" target="_blank">Haz clic aquí y se abrirá el módulo de esta sección</a>
            </div>
        </div>

        <div class="figura">
            <img src="data:image/png;base64,<?= img64('/El_Arca_de_Noemi/pdf/img/6.2.1._Filtros_para_el_listado_de_adoptantes.png') ?>" />
        </div>

        <p>
            Y como siempre, lo primero que tienes es el sistema de filtros inteligentes vinculado al paginado. El primer campo es un input inteligente para buscar adoptantes por nombre o apellido, conforme vayas escribiendo el nombre o el apellido del adoptante en cuestión el campo inteligente te irá mostrando las opciones más cercanas.
        </p>

        <p>
            El resto de campos no tienen mucho que explicar, el segundo es filtrar por según el estado de la adopción, y los otros dos son simplemente por si quieres filtrar a partir de una fecha mínima o máxima.
        </p>

        <div class="volver-indice">
            <a href="#seccion-sexta">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="listado_adoptantes_origen">6.2.1.1. Columna de origen del adoptante en el listado de adoptantes</h3>

        <div class="warning">
            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/" target="_blank">Haz clic aquí para desencriptar la web y acceder al panel de administración</a>
            </div>

            <p>Usuario: ricard624 | Contraseña: mNU1P4hZI36x</p>

            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/modulos/adopciones/sistema_adopciones_listado_adoptantes.php" target="_blank">Haz clic aquí y se abrirá el módulo de esta sección</a>
            </div>
        </div>

        <img src="data:image/png;base64,<?= img64('/El_Arca_de_Noemi/pdf/img/6.2.1.1._Columna_de_origen_del_adoptante.png') ?>" class="img-left-fine" />

        <p>
            Ahora te voy a hablar de la columna "Origen" del listado de adoptantes, encontrarás esta columna en la cuarta posición del listado, y en sus celdas podrás encontrar dos valores diferentes: "Manual", en color azul verdoso, y "Formulario", en color verde, exactamente algo como lo que puedes ver en la imagen que te dejo a la izquierda de este texto.
        </p>

        <p>
            Las filas que estén marcadas como "Manual", son las adopciones que tú misma has creado desde el módulo que vimos anteriormente y que puedes ver de nuevo haciendo clic <a href="#iniciar_nueva_adopcion" class="infoco">"aquí"</a>.
        </p>

        <p>
            En cambio, las adopciones marcadas como "Formulario", son las que proceden de la página web, es decir que estas adopciones las han enviado los mismos usuarios desde el formulario que te he creado en la página web, lo bueno de este tipo de adopciones es que están completamente automatizadas. Para que un usuario pueda acceder al formulario para poder rellenarlo y enviarlo, primero debe navegar entre todos los animales que tengas en la página web para adopción, cuando vea algún animal con el que se sienta identificado podrá hacer clic en el botón "Quiero adoptar a ..." que hay en la ficha individual de cada animal, ese botón lo lleva directamente al formulario que ya queda vinculado automáticamente con el animal en cuestión, el usuario rellena todos los datos, envía el formulario, a ti te llega un E-mail con el formulario adjunto, y además se agrega al listado de adopciones marcado como "Formulario". Y lo más importante, la adopción siempre queda marcada como "En espera" hasta que tú actúes sobre ella, y mientras esté marcada así, el animal sigue estando disponible para adopción siempre y cuando tú no le des al botón "Activar" del listado.
        </p>

        <div class="volver-indice">
            <a href="#seccion-sexta">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="listado_adoptantes_estado">6.2.1.1.1. Columna de estado del adoptante en el listado de adoptantes</h3>

        <img src="data:image/png;base64,<?= img64('/El_Arca_de_Noemi/pdf/img/6.2.1.1.1._Columna_de_estado_del_adoptante.png') ?>" class="img-right-fine" />

        <p>
            La quinta columna del listado es que la que te marca el estado de la adopción, y tiene cinco opciones diferenciadas por cinco colores:

        <ol>
            <li style="background-color: #FFC107; padding: 8px 10px; width: max-content; border-radius: 10px;">En espera</li>
            <p>
                Las adopciones que provienen del formulario de la página web.
            </p>
            <li style="background-color: #FFC107; padding: 8px 10px; width: max-content; border-radius: 10px;">Pendiente</li>
            <p>
                Cuando creas una adopción este es el estado predeterminado en el que se queda, a no ser que tú lo cambies.
            </p>
            <li style="background-color: #17A2B8; padding: 8px 10px; width: max-content; border-radius: 10px;">En proceso</li>
            <p>
                Cuando ya has tomado la decisión de que un adoptante es apto, este es el estado que debes marcar para su adopción.
            </p>
            <li style="background-color: #28A745; padding: 8px 10px; width: max-content; border-radius: 10px;">Finalizada</li>
            <p>
                Este es lógico cuando finalices una adopción las marcas con este estado para tenerlas a mano.
            </p>
            <li style="background-color: #D9534F; padding: 8px 10px; width: max-content; border-radius: 10px;">Cancelada</li>
            <p>
                Este es muy importante si pones una adopción en proceso y al final resulta que el adoptante se echa para atrás, o tú decides que no es apto, marca la adopción como "Cancelada", de esta manera el animal se activará como disponible para adopción automáticamente.
            </p>
        </ol>
        </p>

        <div class="volver-indice">
            <a href="#seccion-sexta">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="listado_adoptantes_editar_manual">6.2.1.1.1.1. Editar adoptante de la columna de origen "Manual"</h3>

        <div class="warning">
            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/" target="_blank">Haz clic aquí para desencriptar la web y acceder al panel de administración</a>
            </div>

            <p>Usuario: ricard624 | Contraseña: mNU1P4hZI36x</p>

            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/modulos/adopciones/sistema_adopciones_editar_adoptante.php?id=3" target="_blank">Haz clic aquí y se abrirá el módulo de esta sección</a>
            </div>
        </div>

        <img src="data:image/png;base64,<?= img64('/El_Arca_de_Noemi/pdf/img/6.2.1.1.1.1._Editar_adoptante_Manual.png') ?>" class="img-left" />

        <p>
            Como puedes ver en la imagen de la derecha, este es el módulo para editar las adopciones manuales es decir, las que hayas creado tú desde el módulo que puedes repasar haciendo clic <a href="#iniciar_nueva_adopcion" class="infoco">"aquí"</a>, la verdad es que no tiene mucho misterio, puedes cambiar la fecha de la adopción, el estado y modificar tus notas personales. También he añadido un botón para que puedas ver todas las adopciones del adoptante en cuestión, por si tiene más de una claro.
        </p>

        <div class="info">
            Recuerda que tal y como te dije en el módulo de creación de adopciones manual, que puedes repasar haciendo clic <a href="#iniciar_nueva_adopcion" class="infoco">"aquí"</a>, si en ese módulo decides añadir/modificar/eliminar campos, en este también se verán reflejados esos cambios.
        </div>

        <div class="volver-indice">
            <a href="#seccion-sexta">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="listado_adoptantes_editar_formulario">6.2.1.1.1.1.1. Editar adoptante de la columna de origen "Formulario"</h3>

        <div class="warning">
            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/" target="_blank">Haz clic aquí para desencriptar la web y acceder al panel de administración</a>
            </div>

            <p>Usuario: ricard624 | Contraseña: mNU1P4hZI36x</p>

            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/modulos/adopciones/sistema_adopciones_editar_formulario.php?id=6" target="_blank">Haz clic aquí y se abrirá el módulo de esta sección</a>
            </div>
        </div>

        <img src="data:image/png;base64,<?= img64('/El_Arca_de_Noemi/pdf/img/6.2.1.1.1.1.1._Editar_adoptante_Formulario.png') ?>" class="img-right" />

        <p>
            Este módulo de edición es exclusivamente para los adoptantes que provienen del formulario de la página web, al entrar en este módulo te encontrarás algo parecido a la imagen de la derecha, como puedes ver es un sistema de pestañas inteligentes. Como en el formulario de la página web hay muchos más campos que en el manual he creado este sistema de pestañas para que puedas navegar por los datos separados en familias, cuando hagas clic en una pestaña esta se abrirá y la que tengas abierta se cerrará, de esta manera podrás trabajar con la ventana limpia y sin distracciones de ningún tipo.
        </p>

        <div class="danger">
            Desde aquí puedes modificar todos los datos que el posible adoptante te ha enviado desde el formulario de la página web, así que ve con un poco de tacto, ya que estos son datos bastante sensibles y personales, a no ser de estar totalmente segura no toquetees nada que te conozco Noemí.
        </div>

        <div class="volver-indice">
            <a href="#seccion-sexta">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="listado_adoptantes_ver_adopciones">6.2.1.1.1.1.1.1. Ver las adopciones de un adoptante</h3>

        <div class="warning">
            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/" target="_blank">Haz clic aquí para desencriptar la web y acceder al panel de administración</a>
            </div>

            <p>Usuario: ricard624 | Contraseña: mNU1P4hZI36x</p>

            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/modulos/adopciones/sistema_adopciones_por_adoptante.php?id=6" target="_blank">Haz clic aquí y se abrirá el módulo de esta sección</a>
            </div>
        </div>

        <p>
            Este módulo no tiene muchas complicaciones de manera que lo vamos a pasar un poco por encima, este es el listado individual de las adopciones de cada adoptante, tienes dos botones para acceder a este listado, uno en el listado de adoptantes y el otro dentro del módulo de edición de adoptantes.
        </p>

        <p>
            Como puedes ver en la imagen que te dejo debajo de este texto, desde este módulo puedes ver todos los datos y editar la o las, si las hubiera adopciones que quisieras.
        </p>

        <div class="figura">
            <img src="data:image/png;base64,<?= img64('/El_Arca_de_Noemi/pdf/img/6.2.1.1.1.1.1.1._Ver_las_adopciones_de_un_adoptante.png') ?>" />
        </div>

        <div class="volver-indice">
            <a href="#seccion-sexta">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="listado_animales_adopcion">6.3. Listado de todos los animales en adopción de la base de datos</h3>

        <div class="warning">
            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/" target="_blank">Haz clic aquí para desencriptar la web y acceder al panel de administración</a>
            </div>

            <p>Usuario: ricard624 | Contraseña: mNU1P4hZI36x</p>

            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/modulos/adopciones/sistema_adopciones_listado.php" target="_blank">Haz clic aquí y se abrirá el módulo de esta sección</a>
            </div>
        </div>

        <p>
            E igual que tenemos un listado con todos los adoptantes y sus adopciones, también tenemos un listado con todos los animales que tienes en la base de datos puestos en adopción, sea cual sea su estado.
        </p>

        <p>
            En este listado verás lo mismo que en el listado anterior, pero con algunas modificaciones bastantes significativas que ahora pasaremos a ver una por una.
        </p>

        <div class="figura">
            <img src="data:image/png;base64,<?= img64('/El_Arca_de_Noemi/pdf/img/6.3._Listado_de_todos_los_animales_en_adopcion.png') ?>" />
        </div>

        <div class="volver-indice">
            <a href="#seccion-sexta">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="listado_animales_adopcion_filtros">6.3.1. Filtros para el listado de animales en adopción</h3>

        <div class="warning">
            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/" target="_blank">Haz clic aquí para desencriptar la web y acceder al panel de administración</a>
            </div>

            <p>Usuario: ricard624 | Contraseña: mNU1P4hZI36x</p>

            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/modulos/adopciones/sistema_adopciones_listado.php" target="_blank">Haz clic aquí y se abrirá el módulo de esta sección</a>
            </div>
        </div>

        <img src="data:image/png;base64,<?= img64('/El_Arca_de_Noemi/pdf/img/6.3.1._Filtros_listado_animales_en_adopcion.png') ?>" class="img-left" />

        <p>
            Como siempre empezamos por el sistema de filtros inteligentes vinculados al paginado, te encontrarás algo como lo que ves en la imagen que te dejo a la izquierda de este texto.
        </p>

        <p>
            Los dos primeros campos ya los conoces más que de sobra, son los famosos selectores inteligentes de "Especie" y "Raza", el selector de especie te muestra todas las especies que tengas en la base de datos, ya sabes que para facilitarte el trabajo solo tienes que empezar a teclear el nombre de la especie y el selector hará el resto. El selector de raza simplemente mostrará las razas de la especie elegida, su funcionamiento inteligente es igual al campo anterior.
        </p>

        <p>
            El tercer campo es simplemente para filtrar por animales marcados como "Adoptables" o "No adoptables", los marcados como adoptables son los que se visualizarán en la página web, los otros serán los que solo serán visibles para ti en el panel de administración, como por ejemplo los que estén en proceso de adopción, o ya adoptados.
        </p>

        <p>
            El cuarto campo ya sabes cómo funciona, ya que es el mismo que te presenté en el listado de adoptantes. Y el resto hablan por sí mismos, por lo que no hace falta explicar nada más.
        </p>

        <div class="volver-indice">
            <a href="#seccion-sexta">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="listado_animales_adopcion_crear_adopcion">6.3.1.1. Crear una adopción desde el listado de animales en adopción</h3>

        <div class="warning">
            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/" target="_blank">Haz clic aquí para desencriptar la web y acceder al panel de administración</a>
            </div>

            <p>Usuario: ricard624 | Contraseña: mNU1P4hZI36x</p>

            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/modulos/adopciones/sistema_adopciones_crear.php?id_animal=2" target="_blank">Haz clic aquí y se abrirá el módulo de esta sección</a>
            </div>
        </div>

        <div class="figura">
            <img src="data:image/png;base64,<?= img64('/El_Arca_de_Noemi/pdf/img/6.3.1.1._Crear_adopcion_desde_listado_animales.png') ?>" />
        </div>

        <p>
            Los animales del listado que estén disponibles para adopción tendrán a su izquierda un botón de color azul con la leyenda "Crear adopción", este botón te llevará a este módulo desde el cual podrás crear una nueva adopción para este animal. El primer campo es inteligente, tú solo empieza a escribir el nombre del adoptante que quieres buscar y el sistema hará el resto por ti.
        </p>

        <div class="volver-indice">
            <a href="#seccion-sexta">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="listado_animales_adopcion_editar_adopcion">6.3.1.1.1. Editar una adopción desde el listado de animales en adopción</h3>

        <div class="warning">
            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/" target="_blank">Haz clic aquí para desencriptar la web y acceder al panel de administración</a>
            </div>

            <p>Usuario: ricard624 | Contraseña: mNU1P4hZI36x</p>

            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/modulos/adopciones/sistema_adopciones_editar_adoptante.php?id=4" target="_blank">Haz clic aquí y se abrirá el módulo de esta sección</a>
            </div>
        </div>

        <p>
            En este módulo encontrarás algo como la imagen que te dejo debajo de este texto, podrás ver los del animal y del adoptante, lógicamente podrás modificar todos los datos de la adopción.
        </p>

        <div class="warning">
            Recuerda que estos son los campos básicos que a mí se me han ocurrido, seguramente tú necesitarás muchos más campos para crear una adopción, y esos los añadiremos en el módulo que vimos <a href="#iniciar_nueva_adopcion" class="infoco">"aquí"</a>, como el resto de módulos van vinculados a ese los campos aparecerán de forma automática.
        </div>

        <div class="figura">
            <img src="data:image/png;base64,<?= img64('/El_Arca_de_Noemi/pdf/img/6.3.1.1.1._Editar_adopcion_desde_listado_animales.png') ?>" />
        </div>

        <div class="volver-indice">
            <a href="#seccion-sexta">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="listado_animales_adopcion_editar_animal">6.3.1.1.1.1. Editar un animal en adopción desde el listado de animales en adopción</h3>

        <div class="warning">
            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/" target="_blank">Haz clic aquí para desencriptar la web y acceder al panel de administración</a>
            </div>

            <p>Usuario: ricard624 | Contraseña: mNU1P4hZI36x</p>

            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/modulos/adopciones/sistema_adopciones_editar_animales.php?id=6" target="_blank">Haz clic aquí y se abrirá el módulo de esta sección</a>
            </div>
        </div>

        <p>
            Este módulo lo vamos a pasar por alto, ya que es exactamente igual que el módulo que ya vimos <a href="#registro_nuevo_animal_adopcion" class="infoco">"aquí"</a>, la única diferencia entre aquel módulo y este es que uno es de creación y el otro es el de edición.
        </p>

        <p>
            En el de creación (el módulo que vimos anteriormente), tenías que añadir las imágenes, pero no las veías, en este módulo si las puedes ver, tal y como puedes comprobar en la imagen que te dejo debajo de este texto, además de poder subir más imágenes a la galería del animal, también puedes eliminar las presentes y cambiar la imagen principal.
        </p>

        <div class="figura">
            <img src="data:image/png;base64,<?= img64('/El_Arca_de_Noemi/pdf/img/6.3.1.1.1.1._Listado_animales_editar_animal.png') ?>" />
        </div>

        <div class="info">
            La imagen que está marcada como "Principal" es la que se muestra en los bloques sueltos de la página web, por ejemplo el bloque que hay en la página principal, o el bloque que hay en el listado de todos los animales. El resto de imágenes solo son visibles en la página individual de cada animal a modo de galería.
        </div>

        <div class="volver-indice">
            <a href="#seccion-sexta">Volver al índice</a>
        </div>

        <div style="clear: both;"></div>

        <div class="separador-seccion"></div>

        <div class="separador"></div>

    </div>

    <!-- 7. Séptima sección - Apadrinamientos -->
    <div class="seccion page-break" id="apadrinamientos">

        <h2>7. Séptima Sección - Apadrinamientos</h2>

        <p>
            Bienvenida a la sección de apadrinamientos, a pesar de que esta va a ser una sección bastante escueta creo que también es muy importante, ya que el sistema de flujo de trabajo de los apadrinamientos vinculados a un sistema de suscripciones de PayPal debe estar rigurosamente bien programado, y sobre todo bien explicado para que a ti te quede clarinete como un patinete recién comprado.
        </p>

        <div class="warning">
            Como siempre te digo Noemí, este es otro de esos casos en los que es muuuuuy (con muchísimas Us) posible que tu necesites recopilar más datos en cada registro de un apadrinamiento. No quiero ser cansino ... Bueno, que coño, si quiero ser cansino, estamos en un entorno de pruebas controlado, es en este momento en el que se tienen que hacer los cambios que a ti se te pasen por la cabeza, no una vez en el servidor de producción.
        </div>

        <div class="volver-indice">
            <a href="#seccion-septima">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="listado_animales_apadrinar">7.1. Listado de todos los animales para apadrinar de la base de datos</h3>

        <div class="warning">
            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/" target="_blank">Haz clic aquí para desencriptar la web y acceder al panel de administración</a>
            </div>

            <p>Usuario: ricard624 | Contraseña: mNU1P4hZI36x</p>

            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/modulos/apadrinamientos/apadrina_listado_animales.php" target="_blank">Haz clic aquí y se abrirá el módulo de esta sección</a>
            </div>
        </div>

        <div class="figura">
            <img src="data:image/png;base64,<?= img64('/El_Arca_de_Noemi/pdf/img/7.1._Listado_animales_apadrinar.png') ?>" />
        </div>

        <p>
            Este primer módulo lo pasamos por encima Noemí, es como todos los listados que hemos ido viendo hasta ahora, no tiene más misterio, es algo como lo que tienes en la imagen que te dejo encima de este texto, puedes ver el sistema de filtros inteligentes vinculados al paginado y la lista de animales que haya en la base de datos para apadrinar. Lo que sí que vamos a hacer es desglosar uno por uno cada punto de este listado para que todo quede bien claro.
        </p>

        <div class="volver-indice">
            <a href="#seccion-septima">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="listado_animales_apadrinar_filtros">7.1.1. Filtros para el listado de animales para apadrinar</h3>

        <div class="warning">
            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/" target="_blank">Haz clic aquí para desencriptar la web y acceder al panel de administración</a>
            </div>

            <p>Usuario: ricard624 | Contraseña: mNU1P4hZI36x</p>

            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/modulos/apadrinamientos/apadrina_listado_animales.php" target="_blank">Haz clic aquí y se abrirá el módulo de esta sección</a>
            </div>
        </div>

        <img src="data:image/png;base64,<?= img64('/El_Arca_de_Noemi/pdf/img/7.1.1._Listado_animales_apadrinar_filtros.png') ?>" class="img-right" />

        <p>
            Este sistema de filtros es muy sencillo, a no ser que tú me pidas algo más sofisticado yo creo que con esto es más que suficiente, los selectores inteligentes de la especie y la raza ya sabes cómo funcionan, luego tienes el campo estado que simplemente te muestra los animales que están activos u ocultos.
        </p>

        <div class="info">
            <p>
                Los animales marcados como "Activo" son los que se ven en la página web.
            </p>

            <p>
                Los animales marcados como "Ocultos" solo son visibles en el panel de administración.
            </p>
        </div>

        <div class="volver-indice">
            <a href="#seccion-septima">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="listado_animales_apadrinar_editar">7.1.1.1. Editar un animal para apadrinar</h3>

        <div class="warning">
            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/" target="_blank">Haz clic aquí para desencriptar la web y acceder al panel de administración</a>
            </div>

            <p>Usuario: ricard624 | Contraseña: mNU1P4hZI36x</p>

            <div class="infoA">
                <a href="http://www.ricardfs.es.mialias.net/admin/modulos/apadrinamientos/apadrina_editar_animal.php?id=4" target="_blank">Haz clic aquí y se abrirá el módulo de esta sección</a>
            </div>
        </div>

        <p>
            Este módulo también lo vamos a pasar por encima, ya que es exactamente igual que el que ya vimos <a href="#registro_nuevo_animal_apadrinar" class="infoco">"aquí"</a>, por lo tanto, no hace falta que nos distraigamos en este también que me está quedando un PDF largo de cojones.
        </p>

        <div class="figura">
            <img src="data:image/png;base64,<?= img64('/El_Arca_de_Noemi/pdf/img/7.1.1.1._Editar_un_animal_para_apadrinar.png') ?>" />
        </div>

        <p>
            La única diferencia entre el módulo que ya vimos anteriormente y este es lo que ves en la imagen que te dejo encima de este texto, es que en este tienes un listado con todos los padrinos que tiene cada animal y te da la posibilidad de cancelar el apadrinamiento de cualquiera de forma individual.
        </p>

        <div class="volver-indice">
            <a href="#seccion-septima">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <div style="clear: both;"></div>

        <div class="separador-seccion"></div>

        <div class="separador"></div>

    </div>

    <!-- 8. Octava sección - Base de datos -->
    <div class="seccion" id="base_de_datos">

        <h2></h2>


        <div style="clear: both;"></div>

        <div class="separador-seccion"></div>

        <div class="separador"></div>

    </div>

    <div class="page-break"></div>

    <div class="creditos">

        <h2>Contacto</h2>

        <ul>
            <li>Ricard FS – Desarrollo y arquitectura</li>
            <li>Noemí – Coordinación y validación funcional</li>
        </ul>

        <div class="separador-creditos"></div>

        <h2> Autor</h2>

        <p><strong>Ricard FS</strong>, creador de:</p>

        <ul>
            <li><strong>El Arca de Noemí</strong> (En desarrollo) – GitHub</li>
            <li><strong>El Huerto de la Gatopía</strong> (En desarrollo) – GitHub</li>
            <li><strong>El Diablillo Sarcástico</strong> (En desarrollo) – GitHub</li>
            <li><strong>La Gatopía de Miriam</strong> (En producción) – Web</li>
            <li><strong>Global License</strong> (En producción) – GitHub</li>
            <li><strong>Lanzador Pro – Arca de Noemí</strong> (En producción) – GitHub</li>
            <li><strong>Lanzador Pro – Huerto de la Gatopía</strong> (En producción) – GitHub</li>
            <li><strong>Lanzador Pro – Ricard FS WEB</strong> (En producción) – GitHub</li>
            <li><strong>Maya Refined</strong> – Theme para Directory Opus – GitHub</li>
        </ul>

        <div class="separador-creditos"></div>

        <h2> Licencia</h2>

        <p>Este proyecto se distribuye bajo licencia MIT.</p>

        <div class="separador-creditos"></div>

        <div class="frase-final">
            “No soy malo… solo estoy programado así.”
        </div>

        <div class="autor">
            © <?= date('Y') ?> Ricard FS — Todos los derechos reservados
        </div>

    </div>

</body>

</html>