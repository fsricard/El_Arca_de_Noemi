<?php
// -----------------------------------------
// AJAX: Listado de animales en apadrinamiento
// -----------------------------------------

require_once(__DIR__ . '/../config/database.php');
require_once(__DIR__ . '/../config/funciones.php');

$por_pagina    = 10;
$param_pagina  = 'p';

$filtro_especie = intval($_GET['especie'] ?? 0);
$filtro_raza    = intval($_GET['raza'] ?? 0);
$pagina_actual  = max(1, intval($_GET[$param_pagina] ?? 1));
$offset         = ($pagina_actual - 1) * $por_pagina;

// Iconos por especie
$iconosEspecieApadrina = [
    'perro'   => 'fa-dog',
    'gato'    => 'fa-cat',
    'conejo'  => 'fa-rabbit-running',
    'ave'     => 'fa-dove',
    'huron'   => 'fa-otter',
    'hurón'   => 'fa-otter',
    'tortuga' => 'fa-turtle',
];

// Consulta base (sin padrinos, para contar bien)
$query_base = "
    FROM animals_sponsor a
    INNER JOIN especies_animales e ON a.especie_id = e.id
    LEFT JOIN razas_animales r ON a.raza_id = r.id
    WHERE a.estado = 'activo'
";

$params = [];

// Filtros
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

// Consulta final con contador de padrinos
$query = "
    SELECT
        a.id,
        a.nombre,
        a.historia,
        a.foto_principal,
        e.nombre AS especie,
        r.nombre AS raza,
        (
            SELECT COUNT(*)
            FROM sponsors_animals sa
            WHERE sa.animal_id = a.id
              AND sa.estado = 'activo'
        ) AS total_padrinos
    " . $query_base . "
    ORDER BY a.fecha_ingreso DESC, a.id DESC
    LIMIT $offset, $por_pagina
";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$animales = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Render listado
ob_start();

if (empty($animales)) {
    echo '<div class="listado-empty">No hay animales disponibles para apadrinar.</div>';
} else {
    foreach ($animales as $animal) {

        $especie_normalizada = strtolower(trim($animal['especie']));
        $especie_normalizada = str_replace(
            ['á','é','í','ó','ú'],
            ['a','e','i','o','u'],
            $especie_normalizada
        );

        $icono = $iconosEspecieApadrina[$especie_normalizada] ?? 'fa-paw';

        $imagen = $animal['foto_principal'] ?: '/assets/img/no-image.png';

        $historia = $animal['historia'] ?? '';

        if (esSoloMovil()) {
            $historia = limitar_palabras($historia, 40);
        } else {
            $historia = limitar_palabras($historia, 80);
        }

        $count = intval($animal['total_padrinos']);
        $label = ($count === 1) ? 'Padrino' : 'Padrinos';
        ?>

        <article class="listado-apadrina-item">

            <!-- Información -->
            <div class="apadrina-info">

                <h3 class="apadrina-nombre">
                    <i class="fa-solid <?= $icono ?>"></i>
                    <?= htmlspecialchars($animal['nombre']) ?>
                </h3>

                <p class="apadrina-especie-raza">
                    <?= htmlspecialchars($animal['especie']) ?>
                    <?php if (!empty($animal['raza'])): ?>
                        · <?= htmlspecialchars($animal['raza']) ?>
                    <?php endif; ?>
                </p>

                <?php if (!empty($historia)): ?>
                    <p class="apadrina-historia">
                        <?= nl2br(htmlspecialchars($historia)) ?>
                    </p>
                <?php endif; ?>

                <?php if ($count >= 0): ?>
                    <div class="apadrinamientos-padrinos"
                         role="status"
                         aria-live="polite"
                         aria-atomic="true"
                         aria-label="<?= $count . ' ' . $label ?>">
                        <span class="badge" aria-hidden="true">
                            <span class="count"><?= $count ?></span>
                            <span class="label"><?= $label ?></span>
                        </span>
                    </div>
                <?php endif; ?>

                <a href="<?= asset('/ficha-apadrinamiento?id=' . $animal['id']) ?>"
                   class="btn adopcion-boton">
                    Ir a la ficha individual
                </a>

            </div>

            <!-- Imagen -->
            <div class="apadrina-imagen">
                <img src="<?= htmlspecialchars($imagen) ?>"
                     alt="Foto de <?= htmlspecialchars($animal['nombre']) ?>">
            </div>

        </article>

        <?php
    }
}

$listado_html = ob_get_clean();

// Paginador
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

header('Content-Type: application/json; charset=utf-8');

echo json_encode([
    'success'   => true,
    'listado'   => $listado_html,
    'paginador' => $paginador_html,
    'total'     => $total_registros,
], JSON_UNESCAPED_UNICODE);

exit;