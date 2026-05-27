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

        .volver-indice {
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
            margin: 20px 0;
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
                    2.4. Contenido de la pádina de la política de privacidad
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
                <a href="#listado_noemi_dice_aprobar">
                    3.2.1. Aprobar las frases de "Noemí dice"
                </a>
            </div>
            <div class="subsubrama">
                <a href="#listado_noemi_dice_ocultar">
                    3.2.1.1. Ocultar las frases de "Noemí dice"
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
            <div class="subsubrama">
                <a href="#listado_bichillos_de_noemi_aprobar">
                    3.4.1. Aprobar los "Bichillos de Noemí"
                </a>
            </div>
            <div class="subsubrama">
                <a href="#listado_bichillos_de_noemi_ocultar">
                    3.4.1.1. Ocultar los "Bichillos de Noemí"
                </a>
            </div>
            <div class="subsubrama">
                <a href="#listado_bichillos_de_noemi_eliminar">
                    3.4.1.1.1. Eliminar los "Bichillos de Noemí"
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
                    4.5. Registrar una nueva plataforma de CrowdFunding
                </a>
            </div>
        </div>

        <!-- 5. Quinta sección - Plataformas de CrowdFunding -->
        <div class="rama" id="seccion-quinta">
            <div class="titulo-rama">
                <a href="#plataformas_crowdfunding">
                    5. Quinta Sección - Plataformas de CrowdFunding
                </a>
            </div>

            <div class="subrama">
                <a href="#plataforma_crear_nueva_recaudacion">
                    5.1. Crear una nueva recaudación de fondos en una plataforma de CrowdFunding
                </a>
            </div>

            <div class="subrama">
                <a href="#plataforma_listado">
                    5.2. Listado de las recaudaciones de fondos en las plataformas de CrowdFunding
                </a>
            </div>
            <div class="subsubrama">
                <a href="#plataforma_listado_ver">
                    5.2.1. Ver la página Web de la recaudación de fondos en la plataforma de CrowdFunding
                </a>
            </div>
            <div class="subsubrama">
                <a href="#plataforma_listado_editar">
                    5.2.1.1. Editar la recaudación de fondos de la plataforma de CrowdFunding
                </a>
            </div>
            <div class="subsubrama">
                <a href="#plataforma_listado_eliminar">
                    5.2.1.1.1. Eliminar la recaudación de fondos de la plataforma de CrowdFunding
                </a>
            </div>
        </div>
    </div>

    <!-- 1. Sección principal - Inicio de Sesión -->
    <div class="seccion" id="inicio_de_sesion">

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

        <h3 id="url_crear_usuario">
            1.1. Dirección Web para crear tu usuario
        </h3>

        <p>
            El primer paso que debes dar es acceder a la página desde la que vas a poder crear tu usuario y contraseña para después acceder al panel de administración. Copia y pega esta dirección web en tu navegador para acceder a la página de creación de usuario:
        </p>

        <div class="infoA">
            <a href="http://www.ricardfs.es.mialias.net/admin/PHP/crear_usuarios.php" target="_blank">Haz clic aquí y se abrirá la página en tu navegar web</a>
        </div>

        <p>
            Si es la primera vez que accedes a la página web desde ese navegador te pedirá que introduzcas un usuario y contraseña, como te dije antes tengo el servidor encriptado por seguridad, si no te había dado acceso antes te lo doy ahora:
        </p>

        <div class="page-break"></div>

        <div class="success">
            <h4>
                Usuario y contraseña para acceder al servidor de pruebas
            </h4>
            <ul>
                <li>Usuario:</li>
                <ul>
                    <li>ricard624</li>
                </ul>
                <li>Contraseña:</li>
                <ul>
                    <li>mNU1P4hZI36x</li>
                </ul>
            </ul>
        </div>

        <div class="volver-indice">
            <a href="#seccion-principal">Volver al índice</a>
        </div>

        <h3 id="crear_usuario">
            1.1.1. Crear tu usuario
        </h3>

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

        <h3 id="url_servidor_pruebas">
            1.2. Dirección Web del servidor de pruebas
        </h3>

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

        <div class="page-break"></div>

        <div class="success">
            <h4>
                Usuario y contraseña para acceder al servidor de pruebas
            </h4>
            <ul>
                <li>Usuario:</li>
                <ul>
                    <li>ricard624</li>
                </ul>
                <li>Contraseña:</li>
                <ul>
                    <li>mNU1P4hZI36x</li>
                </ul>
            </ul>
        </div>

        <div class="volver-indice">
            <a href="#seccion-principal">Volver al índice</a>
        </div>

        <h3 id="acceso_servidor_pruebas">
            1.2.1. Acceso al servidor de pruebas
        </h3>

        <img src="data:image/png;base64,<?= img64('/El_Arca_de_Noemi/pdf/img/1.2.1._Acceso_al_servidor_de_pruebas.png') ?>" class="img-left" />

        <p>
            La verdad es que esta sección la he añadido como un puro trámite porque no tiene mucho misterio, una vez hayas accedido a la URL que te deje más arriba verás una imagen como la de la izquierda. No te comas mucho la cabeza Noemí, pon el usuario y la contraseña que creaste en la primera sección, dale al botoncito y alucina con los colorines.
        </p>

        <div class="danger">
            Recuerda que no debes dejar ningún espacio en blanco ni en el campo del usuario ni tampoco en el campo de la contraseña, si lo haces te dará error y no podrás acceder al panel de administración, así que ten cuidado con eso.
        </div>

        <div style="clear: both;"></div>

        <div class="separador-seccion"></div>

    </div>

    <div class="separador"></div>

    <!-- 2. Segunda sección - Documentos -->
    <div class="seccion" id="documentos">

        <h2>2. Segunda Sección - Documentos</h2>

        <p>
            En esta segunda sección te voy a explicar cómo gestionar los documentos que aparecen en la página de contacto, en la página de presentación de Noemí, en la página de la política de privacidad y en la sección de las opiniones de los usuarios. En cada una de estas secciones podrás editar el contenido que aparece en el sitio web, así como también podrás eliminarlo o añadir nuevo contenido si lo deseas.
        </p>

        <div class="volver-indice">
            <a href="#seccion-segunda">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="contacto">
            2.1. Mensajes que llegan desde la página de contacto
        </h3>

        <div class="infoA">
            <a href="http://localhost:8081/El_Arca_de_Noemi/admin/modulos/contacto/contacto.php" target="_blank">Haz clic aquí y se abrirá la página en tu navegar web</a>
        </div>

        <div class="success">
            Recuerda que todo el contenido que vas a encontrar en los diferentes módulos que vamos a ver en la sección dos de este documento es solo contenido dummy, una vez que la web se pase al servidor de producción, todo este contenido estará vacío y tu tendrás que crearlo a tu gusto.
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

        <div class="volver-indice">
            <a href="#seccion-segunda">Volver al índice</a>
        </div>

        <div class="page-break"></div>

        <h3 id="ver_mensaje">2.1.1. Editar un mensaje de contacto</h3>

        <div class="infoA">
            <a href="http://localhost:8081/El_Arca_de_Noemi/admin/modulos/contacto/contacto_editar.php?id=11" target="_blank">Haz clic aquí y se abrirá la página en tu navegar web</a>
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

        <h3 id="contacto_intro">2.2. Contenido de la sección de contacto</h3>

        <div class="infoA">
            <a href="http://localhost:8081/El_Arca_de_Noemi/admin/modulos/contacto/contacto_intro.php" target="_blank">Haz clic aquí y se abrirá la página en tu navegar web</a>
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

        <h3 id="asi_es_noemi">2.3. Contenido de la página de presentación de Noemí</h3>

        <div class="infoA">
            <a href="http://localhost:8081/El_Arca_de_Noemi/admin/modulos/asi_es_noemi/asi_es_noemi.php" target="_blank">Haz clic aquí y se abrirá la página en tu navegar web</a>
        </div>

        <p>
            Aquí podrás editar el contenido que aparece en la página de presentación de Noemí, esta página es la que aparece cuando haces clic en el enlace "Así es Noemí" que se encuentra en el menú principal de la web. En esta página podrás contar la historia de Noemí, cómo surgió la idea de crear "El Arca de Noemí", cuáles son los objetivos de la asociación, quiénes forman parte del equipo ... En fin, aquí tienes total libertad para contar lo que quieras sobre Noemí y sobre la asociación.
        </p>

        <div class="volver-indice">
            <a href="#seccion-segunda">Volver al índice</a>
        </div>

        <div style="clear: both;"></div>

        <div class="separador-seccion"></div>

    </div>

    <div class="separador"></div>

    <!-- 3. Tercera sección - Sarcásmo y humor -->
    <div class="seccion" id="sarcasmo_y_humor">

        <h2></h2>


        <div style="clear: both;"></div>

        <div class="separador-seccion"></div>

    </div>

    <div class="separador"></div>

    <!-- 4. Cuarta sección - Registro -->
    <div class="seccion" id="registro">

        <h2></h2>


        <div style="clear: both;"></div>

        <div class="separador-seccion"></div>

    </div>

    <div class="separador"></div>

    <!-- 5. Quinta sección - Plataformas de CrowdFunding -->
    <div class="seccion" id="plataformas_crowdfunding">

        <h2></h2>


        <div style="clear: both;"></div>

        <div class="separador-seccion"></div>

    </div>

    <div class="separador"></div>

</body>

</html>