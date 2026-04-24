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

// Filtros
$filtro_especie = intval($_GET['especie'] ?? 0);
$filtro_raza    = intval($_GET['raza'] ?? 0);
$filtro_estado  = $_GET['estado'] ?? '';

// Paginación
$por_pagina = 20;
$pagina_actual = max(1, intval($_GET['p'] ?? 1));
$offset = ($pagina_actual - 1) * $por_pagina;

// Obtenemos las especies
$especies = $pdo->query("
    SELECT id, nombre
    FROM especies_animales
    WHERE activo = 1
    ORDER BY nombre
")->fetchAll(PDO::FETCH_ASSOC);

// Obtenemos las razas
$razas = $pdo->query("
    SELECT id, nombre, especie_id
    FROM razas_animales
    WHERE activo = 1
    ORDER BY nombre
")->fetchAll(PDO::FETCH_ASSOC);

// Consulta base
$query_base = "
    FROM animals_sponsor a
    INNER JOIN especies_animales e ON a.especie_id = e.id
    LEFT JOIN razas_animales r ON a.raza_id = r.id
    WHERE 1
";

$params = [];

// Aplicamos los filtros
if ($filtro_especie > 0) {
    $query_base .= " AND e.id = ? ";
    $params[] = $filtro_especie;
}

if ($filtro_raza > 0) {
    $query_base .= " AND r.id = ? ";
    $params[] = $filtro_raza;
}

if ($filtro_estado !== '') {
    $query_base .= " AND a.estado = ? ";
    $params[] = $filtro_estado;
}

$stmt = $pdo->prepare("SELECT COUNT(*) " . $query_base);
$stmt->execute($params);
$total_registros = $stmt->fetchColumn();

// Consultamos y contamos los padrinos por animal
$query = "
    SELECT 
        a.*,
        e.nombre AS especie,
        r.nombre AS raza,
        (
            SELECT COUNT(*) 
            FROM sponsors_animals sa 
            WHERE sa.animal_id = a.id AND sa.estado = 'activo'
        ) AS total_padrinos
    " . $query_base . "
    ORDER BY a.fecha_ingreso DESC
    LIMIT $offset, $por_pagina
";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$animales = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pagina = 'apadrina_listado_animales';

include('../../includes/header.php');
?>

<main>
    <section>
        <div class="container">
            <h2>Listado de animales para apadrinar</h2>

            <!-- Filtros -->
            <form method="get" class="formulario filtros">

                <div class="filtro">
                    <label>Especie:</label>
                    <select name="especie">
                        <option value="">Todas</option>
                        <?php foreach ($especies as $esp): ?>
                            <option value="<?= $esp['id'] ?>" <?= $filtro_especie == $esp['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($esp['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filtro">
                    <label>Raza:</label>
                    <select name="raza" <?= empty($filtro_especie) ? 'disabled' : '' ?>>
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

                <div class="filtro">
                    <label>Estado:</label>
                    <select name="estado">
                        <option value="">Todos</option>
                        <option value="activo" <?= $filtro_estado === 'activo' ? 'selected' : '' ?>>Activo</option>
                        <option value="oculto" <?= $filtro_estado === 'oculto' ? 'selected' : '' ?>>Oculto</option>
                    </select>
                </div>

                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-filter"></i> Filtrar
                </button>

                <button type="button" onclick="window.location='apadrina_listado_animales.php'">
                    <i class="fa-solid fa-rotate-left"></i> Limpiar filtros
                </button>

            </form>

            <!-- Listado -->
            <table class="tabla">
                <thead>
                    <tr>
                        <th>Miniatura</th>
                        <th>Nombre</th>
                        <th>Especie / Raza</th>
                        <th>Ingreso</th>
                        <th>Padrinos</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($animales as $animal): ?>
                        <tr>

                            <td>

                                <?php
                                $cache_buster = filemtime(__DIR__ . '/../../../' . $animal['foto_principal']);
                                ?>

                                <img src="<?= asset($animal['foto_principal'] . '?v=' . $cache_buster ?: 'img/sin_foto.png') ?>"
                                    class="thumb-animal"
                                    data-img="<?= asset($animal['foto_principal'] . '?v=' . $cache_buster ?: 'img/sin_foto.png') ?>"
                                    alt="<?= htmlspecialchars($animal['nombre']) ?>, <?= htmlspecialchars($animal['especie']) ?>">
                            </td>

                            <td><?= htmlspecialchars($animal['nombre']) ?></td>

                            <td>
                                <?= htmlspecialchars($animal['especie']) ?><br>
                                <small class="texto-secundario"><?= htmlspecialchars($animal['raza'] ?? '-') ?></small>
                            </td>

                            <td><?= htmlspecialchars($animal['fecha_ingreso']) ?></td>

                            <td>
                                <span class="badge badge-info">
                                    <?= (int)$animal['total_padrinos'] ?> padrinos
                                </span>
                            </td>

                            <td>
                                <span class="badge <?= $animal['estado'] === 'activo' ? 'badge-success' : 'badge-warning' ?>">
                                    <?= ucfirst($animal['estado']) ?>
                                </span>
                            </td>

                            <td>
                                <button class="btn btn-success"
                                    onclick="window.location='apadrina_editar_animal.php?id=<?= $animal['id'] ?>'">
                                    <i class="fa-solid fa-pen"></i> Editar
                                </button>
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
        background-color: rgba(0, 0, 0, 0.85);
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

    .tabla th,
    .tabla td {
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

    .badge-success {
        background: #28a745;
    }

    .badge-info {
        background: #17a2b8;
    }

    .badge-warning {
        background: #ffc107;
        color: #000;
    }

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

    .filtros .fila>div {
        display: flex;
        flex-direction: column;
    }

    .filtros button {
        margin-top: 22px;
    }
</style>

<script>
    // Script para el modal de la imagen
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
