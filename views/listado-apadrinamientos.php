<?php
// Config filtros iniciales (para mantener estado si se entra con GET normal)
$filtro_especie = intval($_GET['especie'] ?? 0);
$filtro_raza    = intval($_GET['raza'] ?? 0);

// Especies
$especies = $pdo->query("
    SELECT id, nombre
    FROM especies_animales
    WHERE activo = 1
    ORDER BY nombre
")->fetchAll(PDO::FETCH_ASSOC);

// Razas
$razas = $pdo->query("
    SELECT id, nombre, especie_id
    FROM razas_animales
    WHERE activo = 1
    ORDER BY nombre
")->fetchAll(PDO::FETCH_ASSOC);

// URL real del archivo para AJAX
$ajaxUrl = basename(__FILE__);
?>

<main class="layout-home">

    <section class="destacados">

        <article class="destacado-block">
            <h2 class="destacado-title listado-apadrina-destacado-title">
                <i class="fa-classic fa-solid fa-hands-holding-child"></i>
                Apadrinando puedes salvar una vida
            </h2>

            <!-- Filtros -->
            <div class="listado-adopcion-filtros">
                <form id="filtros-apadrina" class="formulario filtros" method="get">

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
            <div id="listado-apadrina-list"></div>

            <!-- Paginador dinámico -->
            <div id="paginador-apadrina"></div>

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

        // AJAX filtros + paginador
        const formFiltros = document.getElementById('filtros-apadrina');
        const listado = document.getElementById('listado-apadrina-list');
        const paginadorDiv = document.getElementById('paginador-apadrina');
        const btnLimpiar = document.getElementById('btn-limpiar-filtros');

        const ajaxUrl = window.location.pathname.replace(/\/[^\/]*$/, "") + "/ajax/filtros_listado_apadrinamientos.php";
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
                    console.error('Error cargando listado apadrinamientos:', err);
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

        formFiltros.addEventListener('submit', function(e) {
            e.preventDefault();
            cargarPagina(1);
        });

        btnLimpiar.addEventListener('click', function() {
            formFiltros.reset();
            filtrarRazas();
            cargarPagina(1);
        });

        // Carga inicial
        cargarPagina(1);
    });
</script>