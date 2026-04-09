<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../../config/database.php';
require_once(__DIR__ . '/../../config/funciones.php');

// Si no está logueado, redirigimos al login
if (!isLoggedIn()) {
    header("Location: index.php");
    exit;
}

$id_animal = intval($_GET['id'] ?? 0);

if ($id_animal <= 0) {
    header("Location: apadrina_listado_animales.php");
    exit;
}

// Obtener datos del animal
$stmt = $pdo->prepare("
    SELECT *
    FROM animals_sponsor
    WHERE id = ?
");
$stmt->execute([$id_animal]);
$animal = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$animal) {
    header("Location: apadrina_listado_animales.php");
    exit;
}

// Obtenemos los padrinos del animal
$stmt = $pdo->prepare("
    SELECT 
        sa.id,
        sa.estado,
        sa.fecha_inicio,
        sa.fecha_cancelacion,
        s.nombre_apellidos,
        s.email,
        s.telefono,
        s.direccion,
        s.mensaje
    FROM sponsors_animals sa
    INNER JOIN sponsors s ON sa.sponsor_id = s.id
    WHERE sa.animal_id = ?
    ORDER BY sa.fecha_inicio DESC
");
$stmt->execute([$id_animal]);
$padrinos = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

// Procesamos el formulario
$errores = [];
$exito = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre           = trim($_POST['nombre'] ?? '');
    $especie_id       = intval($_POST['especie_id'] ?? 0);
    $raza_id          = !empty($_POST['raza_id']) ? intval($_POST['raza_id']) : null;
    $fecha_ingreso    = $_POST['fecha_ingreso'] ?? null;
    $mini_descripcion = trim($_POST['mini_descripcion'] ?? '');
    $historia         = trim($_POST['historia'] ?? '');
    $estado           = $_POST['estado'] ?? 'activo';

    if ($nombre === '') {
        $errores[] = "El nombre es obligatorio.";
    }

    if ($especie_id <= 0) {
        $errores[] = "Debes seleccionar una especie.";
    }

    if (empty($errores)) {

        $stmt = $pdo->prepare("
            UPDATE animals_sponsor
            SET nombre = ?, especie_id = ?, raza_id = ?, fecha_ingreso = ?, 
                mini_descripcion = ?, historia = ?, estado = ?, updated_at = NOW()
            WHERE id = ?
        ");

        $stmt->execute([
            $nombre,
            $especie_id,
            $raza_id,
            $fecha_ingreso ?: null,
            $mini_descripcion,
            $historia,
            $estado,
            $id_animal
        ]);

        $exito = true;

        // Refrescamos datos
        $stmt = $pdo->prepare("SELECT * FROM animals_sponsor WHERE id = ?");
        $stmt->execute([$id_animal]);
        $animal = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// Subida/reemplazo de imagen
if (!empty($_FILES['foto_principal']['name']) && $_FILES['foto_principal']['error'] === UPLOAD_ERR_OK) {

    $tmp = $_FILES['foto_principal']['tmp_name'];
    $ext = strtolower(pathinfo($_FILES['foto_principal']['name'], PATHINFO_EXTENSION));

    if (in_array($ext, ['jpg','jpeg','png','webp'])) {

        $carpeta = __DIR__ . '/../../uploads/apadrinamientos/' . $id_animal;

        if (!is_dir($carpeta)) {
            mkdir($carpeta, 0777, true);
        }

        $ruta_abs = $carpeta . '/foto_principal.' . $ext;
        $ruta_rel = 'uploads/apadrinamientos/' . $id_animal . '/foto_principal.' . $ext;

        if (move_uploaded_file($tmp, $ruta_abs)) {

            $stmt = $pdo->prepare("
                UPDATE animals_sponsor
                SET foto_principal = ?
                WHERE id = ?
            ");
            $stmt->execute([$ruta_rel, $id_animal]);

            $animal['foto_principal'] = $ruta_rel;
        }
    }
}

$pagina='apadrina_editar_animal';

include('../includes/header.php');
?>

    <main>
        <section>
            <div class="container">
                <h2>Editar animal apadrinable</h2>

                <?php if ($exito): ?>
                    <div class="alert alert-success">Cambios guardados correctamente.</div>
                <?php endif; ?>

                <?php if ($errores): ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach ($errores as $e): ?>
                                <li><?= htmlspecialchars($e) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post" enctype="multipart/form-data">

                    <div class="mb-3">
                        <label>Nombre *</label>
                        <input type="text" name="nombre" class="form-control"
                            value="<?= htmlspecialchars($animal['nombre']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label>Especie *</label>
                        <select name="especie_id" id="especie_id" class="form-control" required>
                            <option value="">Selecciona especie</option>
                            <?php foreach ($especies as $esp): ?>
                                <option value="<?= $esp['id'] ?>"
                                    <?= $animal['especie_id'] == $esp['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($esp['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Raza</label>
                        <select name="raza_id" id="raza_id" class="form-control">
                            <option value="">Selecciona raza</option>
                            <?php foreach ($razas as $raza): ?>
                                <option value="<?= $raza['id'] ?>"
                                        data-especie="<?= $raza['especie_id'] ?>"
                                        <?= $animal['raza_id'] == $raza['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($raza['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Fecha de ingreso</label>
                        <input type="date" name="fecha_ingreso" class="form-control"
                            value="<?= htmlspecialchars($animal['fecha_ingreso']) ?>">
                    </div>

                    <div class="mb-3">
                        <label>Mini descripción</label>
                        <input type="text" name="mini_descripcion" class="form-control"
                            value="<?= htmlspecialchars($animal['mini_descripcion']) ?>">
                    </div>

                    <div class="mb-3">
                        <label>Historia</label>
                        <div id="editor-descripcion" class="quill-editor">
                            <?= !empty($animal['historia']) ? $animal['historia'] : '<p></p>' ?>
                        </div>
                        <textarea id="descripcion" name="historia" class="editor-html form-control" style="display:none;">
                            <?= htmlspecialchars($animal['historia'] ?? '') ?>
                        </textarea>
                    </div>

                    <div class="mb-3">
                        <label>Estado</label>
                        <select name="estado" class="form-control">
                            <option value="activo" <?= $animal['estado'] === 'activo' ? 'selected' : '' ?>>Activo</option>
                            <option value="oculto" <?= $animal['estado'] === 'oculto' ? 'selected' : '' ?>>Oculto</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Foto principal</label><br>
                        <?php if ($animal['foto_principal']): ?>
                            <img
                                src="<?= asset($animal['foto_principal']) ?>"
                                data-img="<?= asset($animal['foto_principal']) ?>"
                                class="thumb-animal"
                            ><br><br>
                        <?php endif; ?>
                        <input type="file" name="foto_principal" class="form-control">
                    </div>

                    <button class="btn btn-primary">Guardar cambios</button>
                    <a href="apadrina_listado_animales.php" class="btn btn-volver">Volver</a>

                </form>

                <hr>

                <h2>Padrinos de este animal</h2>

                <?php if (empty($padrinos)): ?>
                    <p class="texto-secundario">Este animal todavía no tiene padrinos.</p>
                <?php else: ?>

                <table class="tabla">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Contacto</th>
                            <th>Mensaje</th>
                            <th>Inicio</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($padrinos as $p): ?>
                            <tr>

                                <td>
                                    <strong><?= htmlspecialchars($p['nombre_apellidos']) ?></strong><br>
                                    <small class="texto-secundario"><?= htmlspecialchars($p['direccion']) ?></small>
                                </td>

                                <td>
                                    <?= htmlspecialchars($p['email']) ?><br>
                                    <small><?= htmlspecialchars($p['telefono'] ?: '-') ?></small>
                                </td>

                                <td>
                                    <?= $p['mensaje'] ? nl2br(htmlspecialchars($p['mensaje'])) : '<span class="texto-secundario">—</span>' ?>
                                </td>

                                <td><?= htmlspecialchars($p['fecha_inicio']) ?></td>

                                <td>
                                    <?php if ($p['estado'] === 'activo'): ?>
                                        <span class="badge badge-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Cancelado</span><br>
                                        <small><?= htmlspecialchars($p['fecha_cancelacion']) ?></small>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if ($p['estado'] === 'activo'): ?>
                                        <button class="btn btn-warning"
                                                onclick="cancelarPadrino(<?= $p['id'] ?>)">
                                            <i class="fa-solid fa-ban"></i> Cancelar
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-success"
                                                onclick="reactivarPadrino(<?= $p['id'] ?>)">
                                            <i class="fa-solid fa-rotate-right"></i> Reactivar
                                        </button>
                                    <?php endif; ?>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php endif; ?>

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
    // Funcionalidad del modal para ver imagen en grande
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

        const selectEspecie = document.getElementById("especie_id");
        const selectRaza = document.getElementById("raza_id");

        function filtrarRazas() {
            const especieSeleccionada = selectEspecie.value;

            // Mostrar/ocultar opciones según especie
            for (const option of selectRaza.options) {
                if (option.value === "") continue; // opción "Selecciona una raza"

                const especieRaza = option.getAttribute("data-especie");

                option.style.display =
                    especieRaza === especieSeleccionada ? "block" : "none";
            }

            // Si la raza seleccionada no coincide → reset
            const selected = selectRaza.selectedOptions[0];
            if (selected && selected.getAttribute("data-especie") !== especieSeleccionada) {
                selectRaza.value = "";
            }
        }

        // Ejecutar al cargar para mostrar solo las razas correctas
        filtrarRazas();

        // Evento al cambiar especie
        selectEspecie.addEventListener("change", filtrarRazas);
    });

    // Script para cancelar/reactivar un apadrinamiento
    function cancelarPadrino(idRelacion) {
        if (!confirm("¿Seguro que quieres cancelar este apadrinamiento?")) return;

        fetch("ajax/apadrina_cancelar_padrino.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id: idRelacion })
        })
        .then(res => res.json())
        .then(data => {
            if (data.ok) {
                location.reload();
            } else {
                alert(data.error || "Error al cancelar el padrino");
            }
        })
        .catch(() => alert("Error de comunicación con el servidor"));
    }

    function reactivarPadrino(idRelacion) {
        if (!confirm("¿Deseas reactivar este apadrinamiento?")) return;

        fetch("ajax/apadrina_reactivar_padrino.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id: idRelacion })
        })
        .then(res => res.json())
        .then(data => {
            if (data.ok) {
                location.reload();
            } else {
                alert(data.error || "Error al reactivar el padrino");
            }
        })
        .catch(() => alert("Error de comunicación con el servidor"));
    }
</script>

<?php include('../includes/footer.php');