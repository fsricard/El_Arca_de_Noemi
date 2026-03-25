<?php
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/funciones.php';

// Si no está logueado, redirigimos al login
if (!isLoggedIn()) {
    header("Location: index.php");
    exit;
}

$mensaje = "";

// ---------------------------------------------------------
// ACCIONES: activar, ocultar, eliminar
// ---------------------------------------------------------
if (isset($_GET['accion'], $_GET['id'])) {
    $id = intval($_GET['id']);

    if ($_GET['accion'] === 'activar') {
        $stmt = $pdo->prepare("UPDATE noemi_bichillos SET activo = 1 WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $mensaje = mostrarAlerta('Imagen activada correctamente.', 'success');
    }

    if ($_GET['accion'] === 'ocultar') {
        $stmt = $pdo->prepare("UPDATE noemi_bichillos SET activo = 0 WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $mensaje = mostrarAlerta('Imagen ocultada.', 'warning');
    }

    if ($_GET['accion'] === 'eliminar') {

        // Obtener ruta para borrar archivo físico
        $stmt = $pdo->prepare("SELECT bichillo FROM noemi_bichillos WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $img = $stmt->fetchColumn();

        if ($img && file_exists(__DIR__ . '/../' . $img)) {
            unlink(__DIR__ . '/../' . $img);
        }

        // Eliminar registro
        $stmt = $pdo->prepare("DELETE FROM noemi_bichillos WHERE id = :id");
        $stmt->execute([':id' => $id]);

        $mensaje = mostrarAlerta('Imagen eliminada definitivamente.', 'warning');
    }
}

// ---------------------------------------------------------
// FILTROS
// ---------------------------------------------------------
$filtro_estado = $_GET['estado'] ?? '';
$filtro_desde  = $_GET['desde'] ?? '';
$filtro_hasta  = $_GET['hasta'] ?? '';

$where = [];
$params = [];

// Estado
if ($filtro_estado !== '' && ($filtro_estado === '0' || $filtro_estado === '1')) {
    $where[] = "activo = :estado";
    $params[':estado'] = $filtro_estado;
}

// Fecha desde
if (!empty($filtro_desde)) {
    $where[] = "creado_en >= :desde";
    $params[':desde'] = $filtro_desde . " 00:00:00";
}

// Fecha hasta
if (!empty($filtro_hasta)) {
    $where[] = "creado_en <= :hasta";
    $params[':hasta'] = $filtro_hasta . " 23:59:59";
}

$where_sql = $where ? "WHERE " . implode(" AND ", $where) : "";

// ---------------------------------------------------------
// PAGINACIÓN
// ---------------------------------------------------------
$por_pagina = 12;
$pagina_actual = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;
$offset = ($pagina_actual - 1) * $por_pagina;

// Total de registros
$stmt = $pdo->prepare("SELECT COUNT(*) FROM noemi_bichillos $where_sql");
$stmt->execute($params);
$total_registros = $stmt->fetchColumn();

$total_paginas = ceil($total_registros / $por_pagina);

// Obtener imágenes
$sql = "SELECT * FROM noemi_bichillos $where_sql ORDER BY creado_en DESC LIMIT :offset, :limit";
$stmt = $pdo->prepare($sql);

foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}

$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit', $por_pagina, PDO::PARAM_INT);

$stmt->execute();
$bichillos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pagina = 'noemi_bichillos_listado';

include('includes/header.php');
?>

<main>
    <section>
        <div class="container">
            <h2>Gestión de los bichillos de Noemí</h2>

            <?= $mensaje ?>

            <!-- FILTROS -->
            <form method="get" class="filtros">
                <div class="fila">
                    <div>
                        <label>Desde:</label>
                        <input type="date" name="desde" value="<?= htmlspecialchars($filtro_desde) ?>">
                    </div>

                    <div>
                        <label>Hasta:</label>
                        <input type="date" name="hasta" value="<?= htmlspecialchars($filtro_hasta) ?>">
                    </div>

                    <div>
                        <label>Estado:</label>
                        <select name="estado">
                            <option value="">Todos</option>
                            <option value="1" <?= $filtro_estado === '1' ? 'selected' : '' ?>>Activos</option>
                            <option value="0" <?= $filtro_estado === '0' ? 'selected' : '' ?>>Ocultos</option>
                        </select>
                    </div>

                    <div>
                        <button type="submit">
                            <i class="fa-solid fa-filter"></i> Filtrar
                        </button>
                        <button type="button" onclick="window.location='noemi_bichillos_listado.php'">
                            <i class="fa-solid fa-rotate-left"></i> Limpiar filtros
                        </button>
                    </div>
                </div>
            </form>

            <!-- LISTADO -->
            <table class="tabla">
                <thead>
                    <tr>
                        <th>Miniatura</th>
                        <th>Ruta</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($bichillos as $b): ?>
                        <tr>
                            <td>
                                <img src="../<?= htmlspecialchars($b['bichillo']) ?>"
                                    alt="bichillo"
                                    class="thumb-bichillo"
                                    data-img="../<?= htmlspecialchars($b['bichillo']) ?>"
                                    style="width:70px; height:70px; object-fit:cover; border-radius:6px; cursor:pointer;">
                            </td>

                            <td><?= htmlspecialchars($b['bichillo']) ?></td>

                            <td><?= $b['creado_en'] ?></td>

                            <td>
                                <?= $b['activo']
                                    ? '<span class="badge badge-success">Activo</span>'
                                    : '<span class="badge badge-warning">Oculto</span>' ?>
                            </td>

                            <td>
                                <?php if ($b['activo']): ?>
                                    <button class="btn btn-warning"
                                            onclick="window.location='?accion=ocultar&id=<?= $b['id'] ?>'">
                                        <i class="fa-solid fa-eye-slash"></i> Ocultar
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-success"
                                            onclick="window.location='?accion=activar&id=<?= $b['id'] ?>'">
                                        <i class="fa-solid fa-check-circle"></i> Activar
                                    </button>
                                <?php endif; ?>

                                <button class="btn delete-user"
                                        onclick="if(confirm('¿Eliminar imagen definitivamente?')) window.location='?accion=eliminar&id=<?= $b['id'] ?>'">
                                    <i class="fa-solid fa-skull-crossbones"></i> Eliminar
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- PAGINADOR -->
            <?php
                echo paginador($total_registros, $por_pagina, $pagina_actual, $_GET, 'p');
            ?>

        </div>
    </section>

    <!-- MODAL PARA VER IMAGEN EN GRANDE -->
    <div id="modalImagen" class="modal-bichillo">
        <span class="cerrar-modal">&times;</span>
        <img class="modal-contenido" id="imgModal">
    </div>

    <style>
        .modal-bichillo {
            display: none;
            position: fixed;
            z-index: 9999;
            padding-top: 60px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.85);
        }
        .modal-contenido {
            margin: auto;
            display: block;
            max-width: 90%;
            max-height: 85vh;
            border-radius: 10px;
            box-shadow: 0 0 20px #000;
        }
        .cerrar-modal {
            position: absolute;
            top: 25px;
            right: 35px;
            color: #fff;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
        }
        .cerrar-modal:hover {
            color: #ccc;
        }
    </style>

</main>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        const modal = document.getElementById("modalImagen");
        const modalImg = document.getElementById("imgModal");
        const cerrar = document.querySelector(".cerrar-modal");

        // Abrir modal al hacer clic en miniatura
        document.querySelectorAll(".thumb-bichillo").forEach(img => {
            img.addEventListener("click", function() {
                modal.style.display = "block";
                modalImg.src = this.dataset.img;
            });
        });

        // Cerrar modal al pulsar la X
        cerrar.addEventListener("click", function() {
            modal.style.display = "none";
        });

        // Cerrar modal al hacer clic fuera de la imagen
        modal.addEventListener("click", function(e) {
            if (e.target === modal) {
                modal.style.display = "none";
            }
        });

    });
</script>

<?php include('includes/footer.php');