<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../config/funciones.php';

if (!isLoggedIn()) {
    header("Location: index.php");
    exit;
}

/* ============================
   FILTROS
============================ */
$filtro_nombre        = trim($_GET['nombre'] ?? '');
$filtro_id_adoptante = intval($_GET['id_adoptante'] ?? 0);
$filtro_estado       = $_GET['estado'] ?? '';
$filtro_desde        = $_GET['desde'] ?? '';
$filtro_hasta        = $_GET['hasta'] ?? '';

/* ============================
   PAGINACIÓN
============================ */
$por_pagina = 20;
$pagina_actual = max(1, intval($_GET['p'] ?? 1));
$offset = ($pagina_actual - 1) * $por_pagina;

/* ============================
   CONSULTA TOTAL
============================ */
$query_total = "
    SELECT COUNT(*) 
    FROM adoptantes_all ad
    WHERE 1
";

$params_total = [];

/* --- ESTADO INACTIVO --- */
if ($filtro_estado === 'inactivo') {
    $query_total .= " 
        AND ad.origen COLLATE utf8mb4_unicode_ci = 'formulario'
        AND ad.activo = 0
    ";
}

/* --- FILTRO POR ID --- */
if ($filtro_id_adoptante > 0) {
    $query_total .= " AND ad.id = ? ";
    $params_total[] = $filtro_id_adoptante;
}

/* --- FILTRO POR NOMBRE --- */
if ($filtro_id_adoptante === 0 && $filtro_nombre !== '') {
    $query_total .= "
        AND (
            ad.nombre_completo COLLATE utf8mb4_unicode_ci LIKE ?
            OR COALESCE(ad.apellidos, '') COLLATE utf8mb4_unicode_ci LIKE ?
        )
    ";
    $params_total[] = "%$filtro_nombre%";
    $params_total[] = "%$filtro_nombre%";
}

/* --- ESTADOS DE ADOPCIÓN (excepto inactivo) --- */
if ($filtro_estado !== '' && $filtro_estado !== 'inactivo') {
    $query_total .= "
        AND ad.id IN (
            SELECT id_adoptante 
            FROM adopciones 
            WHERE estado COLLATE utf8mb4_unicode_ci = ?
        )
    ";
    $params_total[] = $filtro_estado;
}

/* --- FECHAS --- */
if ($filtro_desde !== '') {
    $query_total .= "
        AND ad.id IN (
            SELECT id_adoptante 
            FROM adopciones 
            WHERE fecha_adopcion >= ?
        )
    ";
    $params_total[] = $filtro_desde;
}

if ($filtro_hasta !== '') {
    $query_total .= "
        AND ad.id IN (
            SELECT id_adoptante 
            FROM adopciones 
            WHERE fecha_adopcion <= ?
        )
    ";
    $params_total[] = $filtro_hasta;
}

/* --- EJECUTAR TOTAL --- */
$stmt = $pdo->prepare($query_total);
$stmt->execute($params_total);
$total_registros = $stmt->fetchColumn();

/* ============================
   CONSULTA PRINCIPAL
============================ */
$query = "
    SELECT 
        ad.*,

        (SELECT COUNT(*) 
         FROM adopciones 
         WHERE id_adoptante = ad.id) AS total_adopciones,

        (SELECT estado 
         FROM adopciones 
         WHERE id_adoptante = ad.id 
         ORDER BY fecha_adopcion DESC 
         LIMIT 1) AS ultimo_estado,

        (SELECT id 
         FROM adopciones 
         WHERE id_adoptante = ad.id 
         ORDER BY fecha_adopcion DESC 
         LIMIT 1) AS id_ultima_adopcion

    FROM adoptantes_all ad
    WHERE 1
";

$params = [];

/* --- ESTADO INACTIVO --- */
if ($filtro_estado === 'inactivo') {
    $query .= " 
        AND ad.origen COLLATE utf8mb4_unicode_ci = 'formulario'
        AND ad.activo = 0
    ";
}

/* --- FILTRO POR ID --- */
if ($filtro_id_adoptante > 0) {
    $query .= " AND ad.id = ? ";
    $params[] = $filtro_id_adoptante;
}

/* --- FILTRO POR NOMBRE --- */
if ($filtro_id_adoptante === 0 && $filtro_nombre !== '') {
    $query .= "
        AND (
            ad.nombre_completo COLLATE utf8mb4_unicode_ci LIKE ?
            OR COALESCE(ad.apellidos, '') COLLATE utf8mb4_unicode_ci LIKE ?
        )
    ";
    $params[] = "%$filtro_nombre%";
    $params[] = "%$filtro_nombre%";
}

/* --- ESTADOS DE ADOPCIÓN (excepto inactivo) --- */
if ($filtro_estado !== '' && $filtro_estado !== 'inactivo') {
    $query .= "
        AND ad.id IN (
            SELECT id_adoptante 
            FROM adopciones 
            WHERE estado COLLATE utf8mb4_unicode_ci = ?
        )
    ";
    $params[] = $filtro_estado;
}

/* --- FECHAS --- */
if ($filtro_desde !== '') {
    $query .= "
        AND ad.id IN (
            SELECT id_adoptante 
            FROM adopciones 
            WHERE fecha_adopcion >= ?
        )
    ";
    $params[] = $filtro_desde;
}

if ($filtro_hasta !== '') {
    $query .= "
        AND ad.id IN (
            SELECT id_adoptante 
            FROM adopciones 
            WHERE fecha_adopcion <= ?
        )
    ";
    $params[] = $filtro_hasta;
}

/* --- ORDEN + PAGINACIÓN --- */
$query .= "
    ORDER BY ad.nombre_completo ASC
    LIMIT $offset, $por_pagina
";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$adoptantes = $stmt->fetchAll();

$pagina = 'sistema_adopciones_listado_adoptantes';

include('../../includes/header.php');
?>

<main>
    <section>
        <div class="container">
            <h2>Listado de todos los adoptantes que hay en la base de datos</h2>

            <!-- FILTROS -->
            <form method="get" class="filtros">
                <div class="fila">

                    <!-- AUTOCOMPLETE -->
                    <div class="autocomplete-wrapper">
                        <label>Filtrar por Nombre/Apellidos:</label>

                        <input type="text"
                            id="buscador"
                            name="nombre"
                            autocomplete="off"
                            value="<?= htmlspecialchars($filtro_nombre) ?>">

                        <input type="hidden"
                            id="id_adoptante"
                            name="id_adoptante"
                            value="<?= $filtro_id_adoptante ?>">

                        <div id="sugerencias" class="autocomplete-list"></div>
                    </div>

                    <!-- ESTADO -->
                    <div>
                        <label>Filtrar por estado de adopción:</label>
                        <select name="estado">
                            <option value="">Todos</option>
                            <option value="inactivo" <?= $filtro_estado === 'inactivo' ? 'selected' : '' ?>>En espera</option>
                            <option value="pendiente" <?= $filtro_estado === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                            <option value="en_proceso" <?= $filtro_estado === 'en_proceso' ? 'selected' : '' ?>>En proceso</option>
                            <option value="finalizada" <?= $filtro_estado === 'finalizada' ? 'selected' : '' ?>>Finalizada</option>
                            <option value="cancelada" <?= $filtro_estado === 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
                        </select>
                    </div>

                    <!-- FECHA DESDE -->
                    <div>
                        <label>Filtrar por fecha de adopción:</label>
                        <label>Desde:</label>
                        <input type="date" name="desde" value="<?= htmlspecialchars($filtro_desde) ?>">
                    </div>

                    <!-- FECHA HASTA -->
                    <div>
                        <label>Hasta:</label>
                        <input type="date" name="hasta" value="<?= htmlspecialchars($filtro_hasta) ?>">
                    </div>

                    <!-- BOTONES -->
                    <div>
                        <button type="submit">
                            <i class="fa-solid fa-filter"></i> Filtrar
                        </button>

                        <button type="button" onclick="window.location='sistema_adopciones_listado_adoptantes.php'">
                            <i class="fa-solid fa-rotate-left"></i> Limpiar filtros
                        </button>
                    </div>

                </div>
            </form>

            <!-- LISTADO -->
            <table class="tabla">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Contacto</th>
                        <th>Dirección</th>
                        <th>Origen</th>
                        <th>Estado</th>
                        <th>PDF</th>
                        <th>Total adopciones</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($adoptantes as $a): ?>
                        <tr>

                            <!-- NOMBRE -->
                            <td>
                                <strong><?= htmlspecialchars($a['nombre_completo']) ?></strong><br>

                                <?php if (!empty($a['apellidos'])): ?>
                                    <small><?= htmlspecialchars($a['apellidos']) ?></small><br>
                                <?php endif; ?>
                            </td>

                            <!-- CONTACTO -->
                            <td>
                                <?= htmlspecialchars($a['telefono']) ?><br>
                                <small><?= htmlspecialchars($a['email']) ?></small>
                            </td>

                            <!-- DIRECCIÓN -->
                            <td>
                                <?= htmlspecialchars($a['direccion']) ?><br>
                                <?= htmlspecialchars($a['ciudad']) ?> (<?= htmlspecialchars($a['provincia']) ?>)
                            </td>

                            <!-- ORIGEN -->
                            <td>
                                <?php if ($a['origen'] === 'manual'): ?>
                                    <span class="badge badge-info">Manual</span>
                                <?php else: ?>
                                    <span class="badge badge-success">Formulario</span>
                                <?php endif; ?>
                            </td>

                            <!-- ESTADO -->
                            <td>
                                <?php if ($a['origen'] === 'formulario' && $a['activo'] == 0): ?>

                                    <span class="badge badge-warning">En espera</span>

                                <?php elseif ($a['ultimo_estado']): ?>

                                    <?php
                                    $estado = $a['ultimo_estado'];
                                    $clase = [
                                        'pendiente'   => 'badge-warning',
                                        'en_proceso'  => 'badge-info',
                                        'finalizada'  => 'badge-success',
                                        'cancelada'   => 'badge-danger'
                                    ][$estado] ?? 'badge-secondary';
                                    ?>
                                    <span class="badge <?= $clase ?>">
                                        <?= ucfirst(str_replace('_', ' ', $estado)) ?>
                                    </span>

                                <?php else: ?>

                                    <span class="badge badge-secondary">Sin adopciones</span>

                                <?php endif; ?>
                            </td>

                            <!-- PDFs -->
                            <?php if ($a['origen'] === 'formulario'): ?>
                                <td>

                                    <?php if (!empty($a['ruta_pdf'])): ?>
                                        <a href="<?= asset( htmlspecialchars($a['ruta_pdf']) ) ?>"
                                            class="btn btn-exportar-pdf"
                                            target="_blank">
                                            <i class="fa-solid fa-file-pdf"></i> PDF
                                        </a>
                                    <?php else: ?>
                                        <span style="color:#999;">Sin PDF</span>
                                    <?php endif; ?>

                                </td>
                            <?php else: ?>
                                <td>
                                    <span style="color:#999;">Sin PDF</span>
                                </td>
                            <?php endif; ?>

                            <!-- TOTAL ADOPCIONES -->
                            <td><?= (int)$a['total_adopciones'] ?></td>

                            <!-- ACCIONES -->
                            <td>

                                <?php if ($a['origen'] === 'formulario' && $a['activo'] == 0): ?>

                                    <!-- 1) BOTÓN ACTIVAR -->
                                    <button class="btn btn-success"
                                        onclick="activarFormulario(<?= $a['id_formulario'] ?>)">
                                        <i class="fa-solid fa-check"></i> Activar
                                    </button>

                                    <!-- 2) BOTÓN EDITAR (versión formulario) -->
                                    <button class="btn btn-warning"
                                        onclick="window.location='sistema_adopciones_editar_formulario.php?id=<?= $a['id_formulario'] ?>'">
                                        <i class="fa-solid fa-pen-to-square"></i> Editar
                                    </button>

                                    <!-- OCULTAR botones normales -->
                                    <!-- (no se muestran Editar adopción ni Ver adopciones) -->

                                <?php else: ?>

                                    <!-- ADOPTANTE MANUAL O FORMULARIO YA ACTIVADO -->

                                    <!-- BOTÓN EDITAR ADOPCIÓN -->
                                    <?php if ($a['id_ultima_adopcion']): ?>
                                        <button class="btn btn-warning"
                                            onclick="window.location='sistema_adopciones_editar_adoptante.php?id=<?= $a['id_ultima_adopcion'] ?>'">
                                            <i class="fa-solid fa-pen-to-square"></i> Editar
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-warning" disabled>
                                            <i class="fa-solid fa-pen-to-square"></i> Editar
                                        </button>
                                    <?php endif; ?>

                                    <!-- BOTÓN VER ADOPCIONES -->
                                    <button class="btn update-user"
                                        onclick="window.location='sistema_adopciones_por_adoptante.php?id=<?= $a['id'] ?>'">
                                        <i class="fa-solid fa-paw"></i> Ver adopciones
                                    </button>

                                <?php endif; ?>

                            </td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?= paginador($total_registros, $por_pagina, $pagina_actual, $_GET); ?>

        </div>
    </section>
</main>

<style>
    /* Wrapper para el autocomplete */
    .autocomplete-wrapper {
        position: relative;
        display: flex;
        flex-direction: column;
    }

    .autocomplete-list {
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        background: #fff;
        border: 1px solid #ddd;
        border-top: none;
        max-height: 220px;
        overflow-y: auto;
        display: none;
        z-index: 9999;
        border-radius: 0 0 6px 6px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .autocomplete-item {
        padding: 10px 12px;
        cursor: pointer;
        transition: background 0.15s ease;
    }

    .autocomplete-item:hover,
    .autocomplete-item.active {
        background: #f0f0f0;
    }

    .autocomplete-highlight {
        font-weight: bold;
        color: #0077cc;
    }
</style>

<script>
    // Script para activar adoptantes
    function activarFormulario(idFormulario) {

        if (!confirm("¿Activar este adoptante y convertirlo en adoptante real?")) {
            return;
        }

        fetch("convertir_formulario_a_adoptante.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "id_formulario=" + encodeURIComponent(idFormulario)
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === "success") {
                    alert("Adoptante activado correctamente");
                    location.reload();
                } else {
                    alert("Error: " + data.message);
                    console.error(data.debug);
                }
            })
            .catch(err => console.error("Error:", err));
    }

    // Script para el select autocomplete de los nombres
    document.addEventListener("DOMContentLoaded", () => {

        const input = document.getElementById("buscador");
        const lista = document.getElementById("sugerencias");
        const inputID = document.getElementById("id_adoptante");

        let indiceActivo = -1;
        let sugerencias = [];
        let debounceTimer = null;

        function debounce(func, delay) {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(func, delay);
        }

        function renderLista() {
            lista.innerHTML = "";

            if (!sugerencias || sugerencias.length === 0) {
                lista.style.display = "none";
                return;
            }

            lista.style.display = "block";

            sugerencias.forEach((item, index) => {
                const div = document.createElement("div");
                div.classList.add("autocomplete-item");

                if (index === indiceActivo) {
                    div.classList.add("active");
                }

                const texto = input.value.trim().toLowerCase();
                const nombre = item.nombre_completo;
                const regex = new RegExp("(" + texto.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ")", "gi");
                const resaltado = nombre.replace(regex, "<span class='autocomplete-highlight'>$1</span>");

                // Mostrar origen
                const origenBadge = item.origen === "manual" ?
                    "<span class='badge badge-info'>Manual</span>" :
                    "<span class='badge badge-success'>Formulario</span>";

                div.innerHTML = `
                <div class="autocomplete-line">
                    ${resaltado}
                    <span class="autocomplete-origen">${origenBadge}</span>
                </div>
            `;

                div.onclick = () => {
                    input.value = item.nombre_completo;
                    inputID.value = item.id;
                    lista.innerHTML = "";
                    lista.style.display = "none";
                };

                lista.appendChild(div);
            });
        }

        input.addEventListener("keyup", (e) => {

            // Reset ID si el usuario escribe manualmente
            inputID.value = "";

            if (e.key === "ArrowDown") {
                if (indiceActivo < sugerencias.length - 1) indiceActivo++;
                renderLista();
                return;
            }

            if (e.key === "ArrowUp") {
                if (indiceActivo > 0) indiceActivo--;
                renderLista();
                return;
            }

            if (e.key === "Enter") {
                if (indiceActivo >= 0 && sugerencias[indiceActivo]) {
                    input.value = sugerencias[indiceActivo].nombre_completo;
                    inputID.value = sugerencias[indiceActivo].id;
                }
                lista.innerHTML = "";
                lista.style.display = "none";
                return;
            }

            const texto = input.value.trim();

            if (texto.length < 2) {
                lista.innerHTML = "";
                lista.style.display = "none";
                sugerencias = [];
                indiceActivo = -1;
                return;
            }

            debounce(() => {
                fetch("ajax/buscar_adoptantes.php?term=" + encodeURIComponent(texto))
                    .then(res => res.json())
                    .then(data => {
                        sugerencias = data || [];
                        indiceActivo = -1;
                        renderLista();
                    })
                    .catch(err => console.error("Error en autocomplete:", err));
            }, 200);
        });

        document.addEventListener("click", (e) => {
            if (!e.target.closest(".autocomplete-wrapper")) {
                lista.innerHTML = "";
                lista.style.display = "none";
                sugerencias = [];
                indiceActivo = -1;
            }
        });

    });
</script>

<?php include('../../includes/footer.php');
