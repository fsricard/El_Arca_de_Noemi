<?php
// Configuración y filtros
$por_pagina    = 10;
$param_pagina  = 'p';
$pagina_actual = max(1, intval($_GET[$param_pagina] ?? 1));
$offset        = ($pagina_actual - 1) * $por_pagina;

$filtro_especie = intval($_GET['especie'] ?? 0);
$filtro_raza    = intval($_GET['raza'] ?? 0);

// URL real del archivo para las peticiones AJAX (importante con URLs amigables)
$ajaxUrl = basename(__FILE__);

// Obtener especies
$especies = $pdo->query("
    SELECT id, nombre
    FROM especies_animales
    WHERE activo = 1
    ORDER BY nombre
")->fetchAll(PDO::FETCH_ASSOC);

// Obtner razas
$razas = $pdo->query("
    SELECT id, nombre, especie_id
    FROM razas_animales
    WHERE activo = 1
    ORDER BY nombre
")->fetchAll(PDO::FETCH_ASSOC);

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

// Endpoint AJAX (listado + paginador)
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {

    ob_start();

    if (empty($animales)) {
        echo '<div class="listado-empty">No hay animales disponibles para adopción.</div>';
    } else {
        foreach ($animales as $animal) {

            $especie_normalizada = strtolower(trim($animal['especie']));
            $especie_normalizada = str_replace(
                ['á', 'é', 'í', 'ó', 'ú'],
                ['a', 'e', 'i', 'o', 'u'],
                $especie_normalizada
            );
            $icono = $iconosEspecie[$especie_normalizada] ?? 'fa-paw';

            $imagen = $animal['imagen_principal'] ?: '/assets/img/no-image.png';
?>
            <div class="listado-adopcion-item">

                <!-- Imagen -->
                <div class="adopcion-imagen">
                    <img src="<?= asset($imagen) ?>"
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
                            <?= $descripcion ?>
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

    // Si no hay más de una página, no mostramos paginador
    if ($total_registros <= $por_pagina) {
        $paginador_html = '';
    } else {
        $paginador_html = paginador($total_registros, $por_pagina, $pagina_actual, [
            'especie' => $filtro_especie ?: '',
            'raza'    => $filtro_raza ?: '',
        ], $param_pagina);
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success'   => true,
        'listado'   => $listado_html,
        'paginador' => $paginador_html,
        'total'     => $total_registros,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
?>

<main class="layout-home">

    <section class="destacados">

        <article class="destacado-block">
            <h2 class="destacado-title listado-destacado-title">
                <i class="fa-solid fa-paw"></i> Listado de animales en adopción
            </h2>

            <div class="destacado-content listado-adopcion-content">

                <!-- Filtros -->
                <div class="listado-adopcion-filtros">
                    <form id="filtros-adopcion" class="formulario filtros" method="get">

                        <!-- ESPECIE -->
                        <div class="filtro listado-filtro-select">
                            <label for="filtro-especie">Especie:</label>
                            <select name="especie" id="filtro-especie">
                                <option value="">Todas</option>
                                <?php foreach ($especies as $esp): ?>
                                    <option value="<?= $esp['id'] ?>"
                                        <?= $filtro_especie == $esp['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($esp['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- RAZA -->
                        <div class="filtro listado-filtro-select">
                            <label for="filtro-raza">Raza:</label>
                            <select name="raza" id="filtro-raza" <?= empty($filtro_especie) ? 'disabled' : '' ?>>
                                <option value="">Todas</option>
                                <?php foreach ($razas as $raza): ?>
                                    <option value="<?= $raza['id'] ?>"
                                        data-especie="<?= $raza['especie_id'] ?>"
                                        <?= $filtro_raza == $raza['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($raza['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-filter"></i> Filtrar
                        </button>

                        <button type="button" class="btn btn-outline" id="btn-limpiar-filtros">
                            <i class="fa-solid fa-rotate-left"></i> Limpiar filtros
                        </button>

                    </form>
                </div>

                <!-- Listado dinámico -->
                <div id="listado-adopcion-list">
                    <div class="listado-empty">Selecciona filtros o espera a que cargue el listado...</div>
                </div>

                <!-- Paginador dinámico -->
                <div id="paginador-adopcion"></div>

            </div>

        </article>

    </section>

</main>

<script>
    document.addEventListener("DOMContentLoaded", () => {

        // Filtrar razas según especie
        const selectEspecie = document.querySelector("#filtro-especie");
        const selectRaza = document.querySelector("#filtro-raza");

        function filtrarRazas() {
            const especieSeleccionada = selectEspecie.value;

            if (especieSeleccionada === "") {
                selectRaza.disabled = true;
                selectRaza.value = "";
                return;
            }

            selectRaza.disabled = false;

            for (const option of selectRaza.options) {
                if (option.value === "") continue;

                const especieRaza = option.getAttribute("data-especie");

                option.style.display =
                    especieRaza === especieSeleccionada ? "block" : "none";
            }

            const selected = selectRaza.selectedOptions[0];
            if (selected && selected.style.display === "none") {
                selectRaza.value = "";
            }
        }

        filtrarRazas();
        selectEspecie.addEventListener("change", filtrarRazas);


        // AJAX para filtros + paginador
        const formFiltros = document.getElementById('filtros-adopcion');
        const listado = document.getElementById('listado-adopcion-list');
        const paginadorDiv = document.getElementById('paginador-adopcion');
        const btnLimpiar = document.getElementById('btn-limpiar-filtros');

        // Endpoint AJAX externo
        const ajaxUrl = window.location.pathname.replace(/\/[^\/]*$/, "") + "/ajax/filtros_listado_adopciones.php";
        const paramPagina = "p";

        function buildQuery(params) {
            return Object.keys(params)
                .filter(k => params[k] !== "" && params[k] !== null && params[k] !== undefined)
                .map(k => encodeURIComponent(k) + '=' + encodeURIComponent(params[k]))
                .join('&');
        }

        function leerFiltros() {
            const fd = new FormData(formFiltros);
            const obj = {};
            for (const [k, v] of fd.entries()) {
                obj[k] = v;
            }
            return obj;
        }

        function cargarPagina(pagina) {
            const filtros = leerFiltros();
            filtros.ajax = 1;
            if (pagina) filtros[paramPagina] = pagina;

            const qs = buildQuery(filtros);

            fetch(ajaxUrl + '?' + qs)
                .then(r => r.json())
                .then(data => {
                    if (!data.success) return;

                    listado.innerHTML = data.listado;
                    paginadorDiv.innerHTML = data.paginador;

                    engancharPaginador();
                })
                .catch(err => {
                    console.error('Error cargando listado:', err);
                });
        }

        function engancharPaginador() {
            if (!paginadorDiv) return;

            const enlaces = paginadorDiv.querySelectorAll('.btn-pag');
            if (!enlaces.length) return;

            enlaces.forEach(enlace => {
                enlace.addEventListener('click', function(e) {
                    e.preventDefault();

                    const url = new URL(this.href, window.location.origin);
                    const p = url.searchParams.get(paramPagina) || 1;

                    cargarPagina(p);

                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            });
        }

        // Eventos del formulario
        formFiltros.addEventListener('submit', function(e) {
            e.preventDefault();
            cargarPagina(1);
        });

        btnLimpiar.addEventListener('click', function() {
            formFiltros.reset();
            filtrarRazas();
            cargarPagina(1);
        });

        // Cargar listado inicial
        cargarPagina(1);
    });
</script>