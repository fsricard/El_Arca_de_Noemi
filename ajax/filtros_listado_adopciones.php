<?php
// -----------------------------------------
// AJAX: Listado de animales en adopción
// -----------------------------------------

// Cargar conexión a la base de datos
require_once(__DIR__ . '/../config/database.php');
require_once(__DIR__ . '/../config/funciones.php');

// Configuración
$por_pagina    = 10;
$param_pagina  = 'p';

$filtro_especie = intval($_GET['especie'] ?? 0);
$filtro_raza    = intval($_GET['raza'] ?? 0);
$pagina_actual  = max(1, intval($_GET[$param_pagina] ?? 1));
$offset         = ($pagina_actual - 1) * $por_pagina;

// Iconos por especie
$iconosEspecie = [
    'perro'   => 'fa-dog',
    'gato'    => 'fa-cat',
    'conejo'  => 'fa-rabbit-running',
    'ave'     => 'fa-dove',
    'huron'   => 'fa-otter',
    'hurón'   => 'fa-otter',
    'tortuga' => 'fa-turtle',
];

// Consulta base
$query_base = "
    FROM animales a
    INNER JOIN razas_animales r ON a.id_raza = r.id
    INNER JOIN especies_animales e ON r.especie_id = e.id
    WHERE a.adoptable = 1
      AND a.id_adopcion IS NULL
";

$params = [];

// Aplicar filtros
if ($filtro_especie > 0) {
    $query_base .= " AND e.id = ? ";
    $params[] = $filtro_especie;
}

if ($filtro_raza > 0) {
    $query_base .= " AND r.id = ? ";
    $params[] = $filtro_raza;
}

// Total registros
$stmt = $pdo->prepare("SELECT COUNT(*) " . $query_base);
$stmt->execute($params);
$total_registros = (int)$stmt->fetchColumn();

// Consulta final
$query = "
    SELECT 
        a.id,
        a.nombre,
        a.descripcion,
        r.nombre AS raza,
        e.nombre AS especie,
        (SELECT ruta FROM animales_fotos 
         WHERE id_animal = a.id AND es_principal = 1 LIMIT 1) AS imagen_principal
    " . $query_base . "
    ORDER BY a.fecha_ingreso DESC
    LIMIT $offset, $por_pagina
";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$animales = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Renderizar listado (HTML)
ob_start();

if (empty($animales)) {
    echo '<div class="listado-empty">No hay animales disponibles para adopción.</div>';
} else {
    foreach ($animales as $animal) {

        $especie_normalizada = strtolower(trim($animal['especie']));
        $especie_normalizada = str_replace(
            ['á','é','í','ó','ú'],
            ['a','e','i','o','u'],
            $especie_normalizada
        );

        $icono = $iconosEspecie[$especie_normalizada] ?? 'fa-paw';
        $imagen = $animal['imagen_principal'] ?: '/assets/img/no-image.png';
        ?>

        <div class="listado-adopcion-item">

            <!-- Imagen -->
            <div class="adopcion-imagen">
                <img src="<?= htmlspecialchars($imagen) ?>"
                     alt="Foto de <?= htmlspecialchars($animal['nombre']) ?>">
            </div>

            <!-- Información -->
            <div class="adopcion-info">

                <h3 class="adopcion-nombre">
                    <i class="fa-solid <?= $icono ?>"></i>
                    <?= htmlspecialchars($animal['nombre']) ?>
                </h3>

                <?php if (!empty($animal['raza'])): ?>
                    <p class="adopcion-raza">
                        <?= htmlspecialchars($animal['especie']) ?> · <?= htmlspecialchars($animal['raza']) ?>
                    </p>
                <?php endif; ?>

                <?php
                $descripcion = $animal['descripcion'] ?? '';

                if (esSoloMovil()) {
                    $descripcion = limitar_palabras($descripcion, 30);
                }
                ?>

                <?php if (!empty($descripcion)): ?>
                    <div class="adopcion-descripcion">
                        <?= $descripcion = limitar_palabras($descripcion, 100); ?>
                    </div>
                <?php endif; ?>

                <a href="<?= asset('/ficha-adopcion?id=' . $animal['id']) ?>" class="btn adopcion-boton">
                    Ir a la ficha individual
                </a>

            </div>

        </div>

        <?php
    }
}

$listado_html = ob_get_clean();

// Paginador (solo si hay más de una página)
if ($total_registros > $por_pagina) {
    require_once __DIR__ . '/../helpers/paginador.php';

    $paginador_html = paginador(
        $total_registros,
        $por_pagina,
        $pagina_actual,
        [
            'especie' => $filtro_especie ?: '',
            'raza'    => $filtro_raza ?: '',
        ],
        $param_pagina
    );
} else {
    $paginador_html = '';
}

// Respuesta JSON
header('Content-Type: application/json; charset=utf-8');

echo json_encode([
    'success'   => true,
    'listado'   => $listado_html,
    'paginador' => $paginador_html,
    'total'     => $total_registros,
], JSON_UNESCAPED_UNICODE);

exit;