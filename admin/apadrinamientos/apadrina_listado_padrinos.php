<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/funciones.php';

if (!isLoggedIn()) {
    header("Location: index.php");
    exit;
}

/* ---------------------------------------------------------
   1. Filtros (recogida)
--------------------------------------------------------- */
$filtro_nombre = trim($_GET['nombre'] ?? '');
$filtro_id_padrino = intval($_GET['id_padrino'] ?? 0);
$filtro_estado = $_GET['estado'] ?? ''; // '' | 'activo' | 'cancelado'
$filtro_desde = $_GET['desde'] ?? '';
$filtro_hasta = $_GET['hasta'] ?? '';

/* ---------------------------------------------------------
   2. Paginación
--------------------------------------------------------- */
$por_pagina = 20;
$pagina_actual = max(1, intval($_GET['p'] ?? 1));
$offset = ($pagina_actual - 1) * $por_pagina;

/* ---------------------------------------------------------
   3. Consulta total (para paginador)
   - Filtrado por ID o por nombre/email
   - Filtrado por estado: tiene al menos una relación activa o no
   - Filtrado por fecha de registro
--------------------------------------------------------- */
$query_total = "SELECT COUNT(*) FROM sponsors s WHERE 1";
$params_total = [];

if ($filtro_id_padrino > 0) {
    $query_total .= " AND s.id = ? ";
    $params_total[] = $filtro_id_padrino;
}

if ($filtro_id_padrino === 0 && $filtro_nombre !== '') {
    $query_total .= " AND (s.nombre_apellidos LIKE ? OR s.email LIKE ?) ";
    $params_total[] = "%{$filtro_nombre}%";
    $params_total[] = "%{$filtro_nombre}%";
}

if ($filtro_estado !== '') {
    if ($filtro_estado === 'activo') {
        $query_total .= " AND s.id IN (SELECT sponsor_id FROM sponsors_animals WHERE estado = 'activo') ";
    } else {
        $query_total .= " AND s.id NOT IN (SELECT sponsor_id FROM sponsors_animals WHERE estado = 'activo') ";
    }
}

if ($filtro_desde !== '') {
    $query_total .= " AND s.fecha_registro >= ? ";
    $params_total[] = $filtro_desde;
}
if ($filtro_hasta !== '') {
    $query_total .= " AND s.fecha_registro <= ? ";
    $params_total[] = $filtro_hasta;
}

$stmt = $pdo->prepare($query_total);
$stmt->execute($params_total);
$total_registros = (int)$stmt->fetchColumn();

/* ---------------------------------------------------------
   4. Consulta final con datos y paginación
   - total_apadrinamientos: número total de relaciones
   - ultimo_estado: estado más reciente por fecha_inicio
   - id_ultima_relacion: id de la última relación
--------------------------------------------------------- */
$query = "
    SELECT 
        s.*,
        (SELECT COUNT(*) FROM sponsors_animals sa WHERE sa.sponsor_id = s.id) AS total_apadrinamientos,
        (SELECT sa.estado FROM sponsors_animals sa WHERE sa.sponsor_id = s.id ORDER BY sa.fecha_inicio DESC LIMIT 1) AS ultimo_estado,
        (SELECT sa.id FROM sponsors_animals sa WHERE sa.sponsor_id = s.id ORDER BY sa.fecha_inicio DESC LIMIT 1) AS id_ultima_relacion
    FROM sponsors s
    WHERE 1
";

$params = [];

if ($filtro_id_padrino > 0) {
    $query .= " AND s.id = ? ";
    $params[] = $filtro_id_padrino;
}

if ($filtro_id_padrino === 0 && $filtro_nombre !== '') {
    $query .= " AND (s.nombre_apellidos LIKE ? OR s.email LIKE ?) ";
    $params[] = "%{$filtro_nombre}%";
    $params[] = "%{$filtro_nombre}%";
}

if ($filtro_estado !== '') {
    if ($filtro_estado === 'activo') {
        $query .= " AND s.id IN (SELECT sponsor_id FROM sponsors_animals WHERE estado = 'activo') ";
    } else {
        $query .= " AND s.id NOT IN (SELECT sponsor_id FROM sponsors_animals WHERE estado = 'activo') ";
    }
}

if ($filtro_desde !== '') {
    $query .= " AND s.fecha_registro >= ? ";
    $params[] = $filtro_desde;
}
if ($filtro_hasta !== '') {
    $query .= " AND s.fecha_registro <= ? ";
    $params[] = $filtro_hasta;
}

$query .= " ORDER BY s.nombre_apellidos ASC LIMIT $offset, $por_pagina";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$padrinos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pagina = 'apadrina_listado_padrinos';

include('../includes/header.php');
?>

    <main>
        <section>
            <div class="container">
                <h2>Listado de todos los padrinos que hay en la base de datos</h2>

                <!-- FILTROS -->
                <form method="get" class="filtros">
                    <div class="fila">

                        <!-- AUTOCOMPLETE -->
                        <div class="autocomplete-wrapper">
                            <label>Nombre / Email:</label>

                            <input type="text"
                                id="buscador"
                                name="nombre"
                                autocomplete="off"
                                value="<?= htmlspecialchars($filtro_nombre) ?>">

                            <input type="hidden"
                                id="id_padrino"
                                name="id_padrino"
                                value="<?= $filtro_id_padrino ?>">

                            <div id="sugerencias" class="autocomplete-list"></div>
                        </div>

                        <div>
                            <label>Estado:</label>
                            <select name="estado">
                                <option value="">Todos</option>
                                <option value="activo" <?= $filtro_estado === 'activo' ? 'selected' : '' ?>>Con apadrinamientos activos</option>
                                <option value="cancelado" <?= $filtro_estado === 'cancelado' ? 'selected' : '' ?>>Sin apadrinamientos activos</option>
                            </select>
                        </div>

                        <div>
                            <label>Registrado desde:</label>
                            <input type="date" name="desde" value="<?= htmlspecialchars($filtro_desde) ?>">
                        </div>

                        <div>
                            <label>Registrado hasta:</label>
                            <input type="date" name="hasta" value="<?= htmlspecialchars($filtro_hasta) ?>">
                        </div>

                        <div>
                            <button type="submit">
                                <i class="fa-solid fa-filter"></i> Filtrar
                            </button>

                            <button type="button" onclick="window.location='apadrina_listado_padrinos.php'">
                                <i class="fa-solid fa-rotate-left"></i> Limpiar filtros
                            </button>
                        </div>

                    </div>
                </form>

                <!-- TABLA -->
                <table class="tabla">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Contacto</th>
                            <th>Apadrinamientos</th>
                            <th>Último estado</th>
                            <th>Registrado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($padrinos)): ?>
                            <tr>
                                <td colspan="7" class="texto-secundario">No se han encontrado padrinos con esos filtros.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($padrinos as $p): ?>
                                <tr>
                                    <td><?= (int)$p['id'] ?></td>

                                    <td>
                                        <strong><?= htmlspecialchars($p['nombre_apellidos']) ?></strong><br>
                                        <small class="texto-secundario"><?= htmlspecialchars($p['direccion'] ?: '-') ?></small>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($p['email']) ?><br>
                                        <small><?= htmlspecialchars($p['telefono'] ?: '-') ?></small>
                                    </td>

                                    <td>
                                        <span class="badge badge-info"><?= (int)$p['total_apadrinamientos'] ?> apadrinamientos</span>
                                    </td>

                                    <td>
                                        <?php if ($p['ultimo_estado'] === 'activo'): ?>
                                            <span class="badge badge-success">Activo</span>
                                        <?php elseif ($p['ultimo_estado'] === 'cancelado'): ?>
                                            <span class="badge badge-warning">Cancelado</span>
                                        <?php else: ?>
                                            <span class="texto-secundario">—</span>
                                        <?php endif; ?>
                                    </td>

                                    <td><?= htmlspecialchars($p['fecha_registro']) ?></td>

                                    <td>
                                        <button class="btn btn-success btn-sm"
                                            onclick="window.location='apadrina_editar_padrino.php?id=<?= $p['id'] ?>'">
                                            <i class="fa-solid fa-pen"></i> Editar
                                        </button>

                                        <button class="btn btn-secondary btn-sm"
                                            onclick="window.location='apadrina_ver_apadrinamientos.php?sponsor_id=<?= $p['id'] ?>'">
                                            <i class="fa-solid fa-list"></i> Ver apadrinamientos
                                        </button>

                                        <?php if (!empty($p['id_ultima_relacion'])): ?>
                                            <button class="btn btn-outline-primary btn-sm"
                                                onclick="window.location='apadrina_editar_relacion.php?id=<?= $p['id_ultima_relacion'] ?>'">
                                                <i class="fa-solid fa-link"></i> Última relación
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <?= paginador($total_registros, $por_pagina, $pagina_actual, $_GET); ?>

            </div>
        </section>
    </main>

    <script>
        // Script para el select autocomplete de los padrinos
        document.addEventListener("DOMContentLoaded", () => {

            const input = document.getElementById("buscador");
            const lista = document.getElementById("sugerencias");
            const inputID = document.getElementById("id_padrino");

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

                    div.innerHTML = resaltado + "<br><small class='texto-secundario'>" + (item.email || '') + "</small>";

                    div.onclick = () => {
                        input.value = item.nombre_completo;
                        inputID.value = item.id; // ← GUARDAMOS EL ID
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
                    fetch("ajax/ajax_buscar_padrinos.php?term=" + encodeURIComponent(texto))
                        .then(res => res.json())
                        .then(data => {
                            sugerencias = data || [];
                            indiceActivo = -1;
                            renderLista();
                        })
                        .catch(err => console.error("Error en autocomplete padrinos:", err));
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

<?php include('../includes/footer.php');