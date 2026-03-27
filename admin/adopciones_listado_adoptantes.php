<?php
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../config/database.php';
require_once(__DIR__ . '/../config/funciones.php');

if (!isLoggedIn()) {
    header("Location: index.php");
    exit;
}

/* ---------------------------------------------------------
   1. Filtros
--------------------------------------------------------- */
$filtro_nombre = trim($_GET['nombre'] ?? '');
$filtro_id_adoptante = intval($_GET['id_adoptante'] ?? 0);
$filtro_estado = $_GET['estado'] ?? '';
$filtro_desde = $_GET['desde'] ?? '';
$filtro_hasta = $_GET['hasta'] ?? '';

/* ---------------------------------------------------------
   2. Construcción dinámica de la consulta
--------------------------------------------------------- */
$query = "
    SELECT ad.*, 
    (SELECT COUNT(*) FROM adopciones WHERE id_adoptante = ad.id) AS total_adopciones,
    (SELECT estado FROM adopciones WHERE id_adoptante = ad.id ORDER BY fecha_adopcion DESC LIMIT 1) AS ultimo_estado
    FROM adoptantes ad
    WHERE 1
";

$params = [];

/* --- Filtro por ID (si se seleccionó del autocomplete) --- */
if ($filtro_id_adoptante > 0) {
    $query .= " AND ad.id = ? ";
    $params[] = $filtro_id_adoptante;
}

/* --- Filtro por nombre (solo si NO hay ID seleccionado) --- */
if ($filtro_id_adoptante === 0 && $filtro_nombre !== '') {
    $query .= " AND (ad.nombre LIKE ? OR ad.apellidos LIKE ?) ";
    $params[] = "%$filtro_nombre%";
    $params[] = "%$filtro_nombre%";
}

/* --- Filtro por estado de adopción --- */
if ($filtro_estado !== '') {
    $query .= "
        AND ad.id IN (
            SELECT id_adoptante FROM adopciones WHERE estado = ?
        )
    ";
    $params[] = $filtro_estado;
}

/* --- Filtro por fechas --- */
if ($filtro_desde !== '') {
    $query .= "
        AND ad.id IN (
            SELECT id_adoptante FROM adopciones WHERE fecha_adopcion >= ?
        )
    ";
    $params[] = $filtro_desde;
}

if ($filtro_hasta !== '') {
    $query .= "
        AND ad.id IN (
            SELECT id_adoptante FROM adopciones WHERE fecha_adopcion <= ?
        )
    ";
    $params[] = $filtro_hasta;
}

$query .= " ORDER BY ad.nombre ASC, ad.apellidos ASC ";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$adoptantes = $stmt->fetchAll();

$pagina='adopciones_listado_adoptantes';

include('includes/header.php');
?>

<main>
    <section>
        <div class="container">
            <h2>Listado de todos los adoptantes que hay en la base de datos</h2>

            <!-- FILTROS -->
            <form method="get" class="filtros">
                <div class="fila">

                    <!-- AUTOCOMPLETE AVANZADO -->
                    <div class="autocomplete-wrapper">
                        <label>Nombre / Apellidos:</label>

                        <input type="text"
                               id="buscador"
                               name="nombre"
                               autocomplete="off"
                               value="<?= htmlspecialchars($filtro_nombre) ?>">

                        <!-- ID oculto del adoptante -->
                        <input type="hidden"
                               id="id_adoptante"
                               name="id_adoptante"
                               value="<?= $filtro_id_adoptante ?>">

                        <div id="sugerencias" class="autocomplete-list"></div>
                    </div>

                    <div>
                        <label>Estado adopción:</label>
                        <select name="estado">
                            <option value="">Todos</option>
                            <option value="pendiente"   <?= $filtro_estado==='pendiente'?'selected':'' ?>>Pendiente</option>
                            <option value="en_proceso"  <?= $filtro_estado==='en_proceso'?'selected':'' ?>>En proceso</option>
                            <option value="finalizada"  <?= $filtro_estado==='finalizada'?'selected':'' ?>>Finalizada</option>
                            <option value="cancelada"   <?= $filtro_estado==='cancelada'?'selected':'' ?>>Cancelada</option>
                        </select>
                    </div>

                    <div>
                        <label>Desde:</label>
                        <input type="date" name="desde" value="<?= htmlspecialchars($filtro_desde) ?>">
                    </div>

                    <div>
                        <label>Hasta:</label>
                        <input type="date" name="hasta" value="<?= htmlspecialchars($filtro_hasta) ?>">
                    </div>

                    <div>
                        <button type="submit">
                            <i class="fa-solid fa-filter"></i> Filtrar
                        </button>

                        <button type="button" onclick="window.location='adopciones_listado_adoptantes.php'">
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
                        <th>Último estado</th>
                        <th>Total adopciones</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($adoptantes as $a): ?>
                        <tr>

                            <td>
                                <strong><?= htmlspecialchars($a['nombre']) ?></strong><br>
                                <?= htmlspecialchars($a['apellidos']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($a['telefono']) ?><br>
                                <small><?= htmlspecialchars($a['email']) ?></small>
                            </td>

                            <td>
                                <?= htmlspecialchars($a['direccion']) ?><br>
                                <?= htmlspecialchars($a['ciudad']) ?> (<?= htmlspecialchars($a['provincia']) ?>)
                            </td>

                            <td>
                                <?php if ($a['ultimo_estado']): ?>
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
                                        <?= ucfirst(str_replace('_',' ', $estado)) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Sin adopciones</span>
                                <?php endif; ?>
                            </td>

                            <td><?= (int)$a['total_adopciones'] ?></td>

                            <td>
                                <button class="btn btn-warning"
                                        onclick="window.location='adopciones_editar_adoptante.php?id=<?= $a['id'] ?>'">
                                    <i class="fa-solid fa-pen-to-square"></i> Editar
                                </button>

                                <button class="btn update-user"
                                        onclick="window.location='adopciones_por_adoptante.php?id=<?= $a['id'] ?>'">
                                    <i class="fa-solid fa-paw"></i> Ver adopciones
                                </button>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

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
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
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

                div.innerHTML = resaltado;

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
                fetch("ajax_buscar_adoptantes.php?term=" + encodeURIComponent(texto))
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

<?php include('includes/footer.php');