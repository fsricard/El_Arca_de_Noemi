<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../../config/database.php';
require_once(__DIR__ . '/../../../config/funciones.php');

// Si no está logueado, redirigimos al login
if (!isLoggedIn()) {
    header("Location: index.php");
    exit;
}

/* -----------------------------------------
   1. Filtros
----------------------------------------- */
$filtro_especie = intval($_GET['especie'] ?? 0);
$filtro_raza = intval($_GET['raza'] ?? 0);
$filtro_desde = $_GET['desde'] ?? '';
$filtro_hasta = $_GET['hasta'] ?? '';

/* ESTADO DEL ANIMAL */
$filtro_estado_animal = $_GET['estado_animal'] ?? '';

/* ESTADO DE ADOPCIÓN */
$filtro_estado_adopcion = $_GET['estado_adopcion'] ?? '';

/* -----------------------------------------
   2. Paginación
----------------------------------------- */
$por_pagina = 20;
$pagina_actual = max(1, intval($_GET['p'] ?? 1));
$offset = ($pagina_actual - 1) * $por_pagina;

/* -----------------------------------------
   3. Obtener especies y razas
----------------------------------------- */
$especies = $pdo->query("
    SELECT id, nombre
    FROM especies_animales
    WHERE activo = 1
    ORDER BY nombre
")->fetchAll(PDO::FETCH_ASSOC);

$razas = $pdo->query("
    SELECT id, nombre, especie_id
    FROM razas_animales
    WHERE activo = 1
    ORDER BY nombre
")->fetchAll(PDO::FETCH_ASSOC);

/* -----------------------------------------
   4. Consulta base
----------------------------------------- */
$query_base = "
    FROM animales a
    INNER JOIN razas_animales r ON a.id_raza = r.id
    INNER JOIN especies_animales e ON r.especie_id = e.id
    LEFT JOIN adopciones ad ON ad.id = a.id_adopcion
    WHERE 1
";

$params = [];

/* -----------------------------------------
   5. Aplicar filtros
----------------------------------------- */

/* Especie */
if ($filtro_especie > 0) {
    $query_base .= " AND e.id = ? ";
    $params[] = $filtro_especie;
}

/* Raza */
if ($filtro_raza > 0) {
    $query_base .= " AND r.id = ? ";
    $params[] = $filtro_raza;
}

/* Fecha rescate desde */
if ($filtro_desde !== '') {
    $query_base .= " AND a.fecha_rescate >= ? ";
    $params[] = $filtro_desde;
}

/* Fecha rescate hasta */
if ($filtro_hasta !== '') {
    $query_base .= " AND a.fecha_rescate <= ? ";
    $params[] = $filtro_hasta;
}

/* ESTADO DEL ANIMAL */
if ($filtro_estado_animal === 'adoptable') {
    $query_base .= " AND a.adoptable = 1 AND a.id_adopcion IS NULL ";
}
elseif ($filtro_estado_animal === 'no_adoptable') {
    $query_base .= " AND a.adoptable = 0 AND a.id_adopcion IS NULL ";
}

/* ESTADO DE ADOPCIÓN */
if ($filtro_estado_adopcion !== '') {
    $query_base .= " AND ad.estado = ? ";
    $params[] = $filtro_estado_adopcion;
}

/* -----------------------------------------
   6. Total registros
----------------------------------------- */
$stmt = $pdo->prepare("SELECT COUNT(*) " . $query_base);
$stmt->execute($params);
$total_registros = $stmt->fetchColumn();

/* -----------------------------------------
   7. Consulta final
----------------------------------------- */
$query = "
    SELECT 
        a.*,
        r.nombre AS raza_nombre,
        e.nombre AS especie,
        ad.estado AS estado_adopcion,
        ad.id AS id_adopcion,
        (SELECT ruta FROM animales_fotos 
         WHERE id_animal = a.id AND es_principal = 1 LIMIT 1) AS foto
    " . $query_base . "
    ORDER BY a.fecha_ingreso DESC
    LIMIT $offset, $por_pagina
";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$animales = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pagina='adopciones_listado';

include('../../includes/header.php');
?>

<main>
    <section>
        <div class="container">
            <h2>Listado de animales en adopción</h2>

            <!-- Filtros -->
            <form method="get" class="formulario filtros">

                <!-- ESPECIE -->
                <div class="filtro">
                    <label>Especie:</label>
                    <select name="especie" id="especie">
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
                <div class="filtro">
                    <label>Raza:</label>
                    <select name="raza" id="raza" <?= empty($filtro_especie) ? 'disabled' : '' ?>>
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

                <!-- ESTADO DEL ANIMAL -->
                <div class="filtro">
                    <label>Estado del animal:</label>
                    <select name="estado_animal">
                        <option value="">Todos</option>
                        <option value="adoptable" <?= $filtro_estado_animal === 'adoptable' ? 'selected' : '' ?>>Adoptable</option>
                        <option value="no_adoptable" <?= $filtro_estado_animal === 'no_adoptable' ? 'selected' : '' ?>>No adoptable</option>
                    </select>
                </div>

                <!-- ESTADO DE ADOPCIÓN -->
                <div class="filtro">
                    <label>Estado de adopción:</label>
                    <select name="estado_adopcion">
                        <option value="">Todos</option>
                        <option value="pendiente"   <?= $filtro_estado_adopcion === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                        <option value="en_proceso"  <?= $filtro_estado_adopcion === 'en_proceso' ? 'selected' : '' ?>>En proceso</option>
                        <option value="finalizada"  <?= $filtro_estado_adopcion === 'finalizada' ? 'selected' : '' ?>>Finalizada</option>
                        <option value="cancelada"   <?= $filtro_estado_adopcion === 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
                    </select>
                </div>

                <!-- FECHAS -->
                <div class="filtro">
                    <label>Rescatado desde:</label>
                    <input type="date" name="desde" value="<?= htmlspecialchars($filtro_desde) ?>">
                </div>

                <div class="filtro">
                    <label>Rescatado hasta:</label>
                    <input type="date" name="hasta" value="<?= htmlspecialchars($filtro_hasta) ?>">
                </div>

                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-filter"></i> Filtrar
                </button>

                <button type="button" onclick="window.location='sistema_adopciones_listado.php'">
                    <i class="fa-solid fa-rotate-left"></i> Limpiar filtros
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
                                <?php if ($animal['estado_adopcion']): ?>

                                    <?php
                                        $estado = $animal['estado_adopcion'];
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

                                    <?php if ($animal['adoptable']): ?>
                                        <span class="badge badge-info">
                                            <i class="fa-solid fa-paw"></i> Adoptable
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">
                                            <i class="fa-solid fa-ban"></i> No adoptable
                                        </span>
                                    <?php endif; ?>

                                <?php endif; ?>
                            </td>

                            <!-- ACCIONES -->
                            <td>

                                <!-- EDITAR ANIMAL (siempre disponible) -->
                                <button class="btn btn-success"
                                        onclick="window.location='sistema_adopciones_editar_animales.php?id=<?= $animal['id'] ?>'">
                                    <i class="fa-solid fa-dog"></i> Editar animal
                                </button>

                                <?php if ($animal['id_adopcion']): ?>

                                    <!-- EDITAR ADOPCIÓN -->
                                    <button class="btn btn-warning"
                                            onclick="window.location='sistema_adopciones_editar_adoptante.php?id=<?= $animal['id_adopcion'] ?>'">
                                        <i class="fa-solid fa-pen-to-square"></i> Editar adopción
                                    </button>

                                <?php else: ?>

                                    <!-- CREAR ADOPCIÓN -->
                                    <button class="btn update-user"
                                            onclick="window.location='sistema_adopciones_crear.php?id_animal=<?= $animal['id'] ?>'">
                                        <i class="fa-solid fa-heart"></i> Crear adopción
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

<!-- MODAL PARA VER IMAGEN EN GRANDE -->
<div id="modalAnimal" class="modal-bichillo">
    <span class="cerrar-modal">&times;</span>
    <img class="modal-contenido" id="imgModalAnimal">
</div>

<style>
    .select:disabled {
        background: #f0f0f0;
        color: #777;
        cursor: not-allowed;
    }
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

    // Filtrar razas según especie seleccionada
    document.addEventListener("DOMContentLoaded", () => {

        const selectEspecie = document.querySelector("select[name='especie']");
        const selectRaza = document.querySelector("select[name='raza']");

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
    });
</script>

<?php include('../../includes/footer.php');