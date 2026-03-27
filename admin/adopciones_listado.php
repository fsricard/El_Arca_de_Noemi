<?php
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../config/database.php';
require_once(__DIR__ . '/../config/funciones.php');

if (!isLoggedIn()) {
    header("Location: index.php");
    exit;
}

// Obtener razas para el filtro
$razas = $pdo->query("SELECT id, nombre, especie FROM razas_animales WHERE activo = 1 ORDER BY especie, nombre")->fetchAll();

// Filtros
$filtro_raza = $_GET['raza'] ?? '';
$filtro_desde = $_GET['desde'] ?? '';
$filtro_hasta = $_GET['hasta'] ?? '';
$filtro_estado = $_GET['estado'] ?? '';

// Construcción dinámica de la consulta
$query = "
    SELECT a.*, r.nombre AS raza_nombre, r.especie,
    (SELECT ruta FROM animales_fotos WHERE id_animal = a.id AND es_principal = 1 LIMIT 1) AS foto
    FROM animales a
    INNER JOIN razas_animales r ON a.id_raza = r.id
    WHERE 1
";

$params = [];

// Filtro por raza
if ($filtro_raza !== '') {
    $query .= " AND a.id_raza = ? ";
    $params[] = $filtro_raza;
}

// Filtro por fecha de rescate
if ($filtro_desde !== '') {
    $query .= " AND a.fecha_rescate >= ? ";
    $params[] = $filtro_desde;
}

if ($filtro_hasta !== '') {
    $query .= " AND a.fecha_rescate <= ? ";
    $params[] = $filtro_hasta;
}

// Filtro por estado de adopción
if ($filtro_estado === 'adoptable') {
    $query .= " AND a.adoptable = 1 ";
} elseif ($filtro_estado === 'no_adoptable') {
    $query .= " AND a.adoptable = 0 ";
} elseif ($filtro_estado === 'adoptado') {
    $query .= " AND a.id_adopcion IS NOT NULL ";
}

$query .= " ORDER BY a.fecha_ingreso DESC ";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$animales = $stmt->fetchAll();

$pagina='adopciones_listado';

include('includes/header.php');
?>

<main>
    <section>
        <div class="container">
            <h2>Listado de animales en adopción</h2>

            <!-- Filtros -->
            <form method="get" class="formulario filtros">

                <div class="filtro">
                    <label for="raza">Raza / Especie:</label>
                    <select name="raza" id="raza">
                        <option value="">Todas</option>
                        <?php foreach ($razas as $raza): ?>
                            <option value="<?= $raza['id'] ?>" <?= $filtro_raza == $raza['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($raza['especie'] . " – " . $raza['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filtro">
                    <label for="desde">Rescatado desde:</label>
                    <input type="date" name="desde" id="desde" value="<?= htmlspecialchars($filtro_desde) ?>">
                </div>

                <div class="filtro">
                    <label for="hasta">Rescatado hasta:</label>
                    <input type="date" name="hasta" id="hasta" value="<?= htmlspecialchars($filtro_hasta) ?>">
                </div>

                <div class="filtro">
                    <label for="estado">Estado:</label>
                    <select name="estado" id="estado">
                        <option value="">Todos</option>
                        <option value="adoptable" <?= $filtro_estado === 'adoptable' ? 'selected' : '' ?>>Adoptable</option>
                        <option value="no_adoptable" <?= $filtro_estado === 'no_adoptable' ? 'selected' : '' ?>>No adoptable</option>
                        <option value="adoptado" <?= $filtro_estado === 'adoptado' ? 'selected' : '' ?>>Adoptado</option>
                    </select>
                </div>

                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-filter"></i> Filtrar
                </button>
            </form>

            <!-- LISTADO -->
            <table class="tabla">
                <thead>
                    <tr>
                        <th>Miniatura</th>
                        <th>Nombre</th>
                        <th>Especie / Raza</th>
                        <th>Ingreso</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($animales as $animal): ?>
                        <tr>

                            <!-- MINIATURA -->
                            <td>
                                <img src="<?= asset($animal['foto'] ?: 'img/sin_foto.png') ?>"
                                    alt="foto animal"
                                    class="thumb-animal"
                                    data-img="<?= asset($animal['foto'] ?: 'img/sin_foto.png') ?>">
                            </td>

                            <!-- NOMBRE -->
                            <td><?= htmlspecialchars($animal['nombre']) ?></td>

                            <!-- ESPECIE / RAZA -->
                            <td>
                                <?= htmlspecialchars($animal['especie']) ?><br>
                                <small class="texto-secundario"><?= htmlspecialchars($animal['raza_nombre']) ?></small>
                            </td>

                            <!-- FECHA INGRESO -->
                            <td><?= htmlspecialchars($animal['fecha_ingreso']) ?></td>

                            <!-- ESTADO -->
                            <td>
                                <?php if ($animal['id_adopcion']): ?>
                                    <span class="badge badge-success">
                                        <i class="fa-solid fa-heart"></i> Adoptado
                                    </span>
                                <?php elseif ($animal['adoptable']): ?>
                                    <span class="badge badge-info">
                                        <i class="fa-solid fa-paw"></i> Adoptable
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-warning">
                                        <i class="fa-solid fa-ban"></i> No adoptable
                                    </span>
                                <?php endif; ?>
                            </td>

                            <!-- ACCIONES -->
                            <td>
                                <button class="btn btn-warning"
                                        onclick="window.location='adopciones_editar.php?id=<?= $animal['id'] ?>'">
                                    <i class="fa-solid fa-pen-to-square"></i> Editar
                                </button>

                                <button class="btn delete-user"
                                        onclick="if(confirm('¿Eliminar este animal?')) window.location='eliminar_animal.php?id=<?= $animal['id'] ?>'">
                                    <i class="fa-solid fa-skull-crossbones"></i> Eliminar
                                </button>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>
    </section>
</main>

<!-- MODAL PARA VER IMAGEN EN GRANDE -->
<div id="modalAnimal" class="modal-bichillo">
    <span class="cerrar-modal">&times;</span>
    <img class="modal-contenido" id="imgModalAnimal">
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
    /* Miniaturas */
    .thumb-animal {
        width: 70px;
        height: 70px;
        object-fit: cover;
        border-radius: 6px;
        cursor: pointer;
        transition: transform 0.2s ease;
    }
    .thumb-animal:hover {
        transform: scale(1.05);
    }
    /* Tabla */
    .tabla {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    .tabla th, .tabla td {
        padding: 10px;
        border-bottom: 1px solid #ddd;
    }
    .tabla th {
        background: #f5f5f5;
        text-align: left;
    }
    /* Badges */
    .badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.85em;
        color: #fff;
    }
    .badge-success { background: #28a745; }
    .badge-info { background: #17a2b8; }
    .badge-warning { background: #ffc107; color: #000; }
    /* Texto secundario */
    .texto-secundario {
        color: #666;
        font-size: 0.85em;
    }
    /* Filtros */
    .filtros .fila {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
    }
    .filtros .fila > div {
        display: flex;
        flex-direction: column;
    }
    .filtros button {
        margin-top: 22px;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        const modal = document.getElementById("modalAnimal");
        const modalImg = document.getElementById("imgModalAnimal");
        const cerrar = document.querySelector(".cerrar-modal");

        document.querySelectorAll(".thumb-animal").forEach(img => {
            img.addEventListener("click", function() {
                modal.style.display = "block";
                modalImg.src = this.dataset.img;
            });
        });

        cerrar.addEventListener("click", function() {
            modal.style.display = "none";
        });

        modal.addEventListener("click", function(e) {
            if (e.target === modal) {
                modal.style.display = "none";
            }
        });

    });
</script>

<?php include('includes/footer.php');