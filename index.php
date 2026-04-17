<?php
// ==========================================
//  ROUTER PRINCIPAL DEL FRONTEND
// ==========================================

// Sanitizar parámetros
$view  = isset($_GET['view'])  ? trim($_GET['view'])  : 'inicio';
$slug  = isset($_GET['slug'])  ? trim($_GET['slug'])  : null;
$extra = isset($_GET['extra']) ? trim($_GET['extra']) : null;

// ==========================================
//  LISTA DE VISTAS PERMITIDAS
// ==========================================

$rutas_validas = [
    'inicio',
    'contacto',
    'asi-es-noemi',
    'ficha-adopcion',
    'listado-adopciones',
    'listado-crowdfunding',
    'ficha-apadrinamiento',
    'formulario-adoptante',
    'politica-de-privacidad',
    'listado-apadrinamientos'
];

// Si la vista no existe → 404
if (!in_array($view, $rutas_validas)) {
    http_response_code(404);
    $GLOBALS['pagina_actual'] = '404';
    $view = '404';
}

// Define página actual para el header y el footer
$GLOBALS['pagina_actual'] = $view;
$GLOBALS['nombre_animal'] = '';

// ==========================================
//  CARGA PREVIA DE DATOS SEGÚN LA VISTA
// ==========================================

// Si entras a una ficha de adopción
if ($view === 'ficha-adopcion') {

    $idAnimal = $_GET['id'] ?? null;

    if ($idAnimal) {

        if (!isset($pdo)) {
            require_once(__DIR__ . '/config/database.php');
        }

        $sql = "SELECT nombre FROM animales WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idAnimal]);
        $animal = $stmt->fetch(PDO::FETCH_ASSOC);

        $GLOBALS['nombre_animal'] = $animal['nombre'] ?? '';
    }
}

// Si entras a una ficha de apadrinamientos
if ($view === 'ficha-apadrinamiento') {

    $idAnimal = isset($_GET['id']) ? (int) $_GET['id'] : null;

    if ($idAnimal) {

        if (!isset($pdo)) {
            require_once(__DIR__ . '/config/database.php');
        }

        $sql = "SELECT nombre FROM animals_sponsor WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idAnimal]);
        $animal = $stmt->fetch(PDO::FETCH_ASSOC);

        $GLOBALS['nombre_animal'] = $animal['nombre'] ?? '';
    }
}

// Si entras a el formulario de adopciones
if ($view === 'formulario-adoptante') {

    $idAnimal = isset($_GET['id']) ? (int) $_GET['id'] : null;

    if ($idAnimal) {

        if (!isset($pdo)) {
            require_once(__DIR__ . '/config/database.php');
        }

        $sql = "SELECT nombre FROM animales WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idAnimal]);
        $animal = $stmt->fetch(PDO::FETCH_ASSOC);

        $GLOBALS['nombre_animal'] = $animal['nombre'] ?? '';
    }
}

// ==========================================
//  CARGAR HEADER
// ==========================================
require_once __DIR__ . '/includes/header.php';

// ==========================================
//  LÓGICA DEL ROUTER
// ==========================================

// ---------------------------
//  PÁGINA DE INICIO
// ---------------------------
if ($view === 'inicio') {
    require __DIR__ . '/views/inicio.php';
}

// ---------------------------
//  CONTACTO
//  /contacto
// ---------------------------
elseif ($view === 'contacto') {
    require __DIR__ . '/views/contacto.php';
}

// ---------------------------
//  POLITICA DE PRIVACIDAD
//  /politica-de-privacidad
// ---------------------------
elseif ($view === 'politica-de-privacidad') {
    require __DIR__ . '/views/politica-de-privacidad.php';
}

// ---------------------------
//  ASÏ ES NOEMI
//  /asi-es-noemi
// ---------------------------
elseif ($view === 'asi-es-noemi') {
    require __DIR__ . '/views/asi-es-noemi.php';
}

// ---------------------------
//  FICHA DE ADOPCIÓN INDIVIDUAL
//  /ficha-adopcion
// ---------------------------
elseif ($view === 'ficha-adopcion') {
    require __DIR__ . '/views/ficha-adopcion.php';
}

// ---------------------------
//  FICHA DE APADRINAMIENTO INDIVIDUAL
//  /ficha-apadrinamiento
// ---------------------------
elseif ($view === 'ficha-apadrinamiento') {
    require __DIR__ . '/views/ficha-apadrinamiento.php';
}

// -------------------------------------
//  FICHA DE EL FORMULARIO DE ADOPCIONES
//  /formulario-adoptante
// -------------------------------------
elseif ($view === 'formulario-adoptante') {
    require __DIR__ . '/views/formulario-adoptante.php';
}

// ----------------------------------
//  FICHA DE EL LISTADO DE ADOPCIONES
//  /listado-adopciones
// ----------------------------------
elseif ($view === 'listado-adopciones') {
    require __DIR__ . '/views/listado-adopciones.php';
}

// ---------------------------------------
//  FICHA DE EL LISTADO DE APADRINAMIENTOS
//  /listado-apadrinamientos
// ---------------------------------------
elseif ($view === 'listado-apadrinamientos') {
    require __DIR__ . '/views/listado-apadrinamientos.php';
}

// ---------------------------------------
//  FICHA DE EL LISTADO DE CROWDFUNDING
//  /listado-crowdfunding
// ---------------------------------------
elseif ($view === 'listado-crowdfunding') {
    require __DIR__ . '/views/listado-crowdfunding.php';
}

// ---------------------------
//  404
// ---------------------------
else {
    require __DIR__ . '/views/404.php';
}

// ==========================================
//  CARGAR FOOTER
// ==========================================
require_once __DIR__ . '/includes/footer.php';