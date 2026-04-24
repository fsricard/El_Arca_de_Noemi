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

// Validar ID
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    die("ID de animal no válido.");
}

/* ---------------------------------------------------------
   OBTENER DATOS DEL ANIMAL
--------------------------------------------------------- */
$stmt = $pdo->prepare("
    SELECT 
        a.*,
        r.nombre AS raza_nombre,
        r.especie_id,
        e.nombre AS especie_nombre
    FROM animales a
    INNER JOIN razas_animales r     ON a.id_raza = r.id
    INNER JOIN especies_animales e  ON r.especie_id = e.id
    WHERE a.id = ?
");
$stmt->execute([$id]);
$animal = $stmt->fetch();

if (!$animal) {
    die("Animal no encontrado.");
}

/* ---------------------------------------------------------
   OBTENER ESPECIES PARA EL SELECTOR
--------------------------------------------------------- */
$especies = $pdo->query("
    SELECT id, nombre
    FROM especies_animales
    WHERE activo = 1
    ORDER BY nombre
")->fetchAll(PDO::FETCH_ASSOC);

/* ---------------------------------------------------------
   OBTENER RAZAS PARA EL SELECTOR
--------------------------------------------------------- */
$razas = $pdo->query("
    SELECT id, nombre, especie_id
    FROM razas_animales
    WHERE activo = 1
    ORDER BY nombre
")->fetchAll(PDO::FETCH_ASSOC);

/* ---------------------------------------------------------
   OBTENER FOTOS DEL ANIMAL
--------------------------------------------------------- */
$stmt = $pdo->prepare("
    SELECT *
    FROM animales_fotos
    WHERE id_animal = ?
    ORDER BY es_principal DESC, id ASC
");
$stmt->execute([$id]);
$fotos = $stmt->fetchAll();

$errores = [];
$exito = false;

/* ---------------------------------------------------------
   ACCIONES: BORRAR FOTO
--------------------------------------------------------- */
if (isset($_GET['borrar_foto'])) {
    $id_foto = intval($_GET['borrar_foto']);

    $stmt = $pdo->prepare("
        SELECT ruta 
        FROM animales_fotos 
        WHERE id = ? AND id_animal = ?
    ");
    $stmt->execute([$id_foto, $id]);
    $foto = $stmt->fetch();

    if ($foto) {
        @unlink(__DIR__ . '/../../../' . $foto['ruta']);
        $pdo->prepare("DELETE FROM animales_fotos WHERE id = ?")->execute([$id_foto]);
    }

    header("Location: sistema_adopciones_editar_animales.php?id=$id");
    exit;
}

/* ---------------------------------------------------------
   ACCIONES: MARCAR COMO PRINCIPAL
--------------------------------------------------------- */
if (isset($_GET['principal'])) {
    $id_foto = intval($_GET['principal']);

    $pdo->prepare("UPDATE animales_fotos SET es_principal = 0 WHERE id_animal = ?")->execute([$id]);
    $pdo->prepare("UPDATE animales_fotos SET es_principal = 1 WHERE id = ?")->execute([$id_foto]);

    header("Location: sistema_adopciones_editar_animales.php?id=$id");
    exit;
}

/* ---------------------------------------------------------
   PROCESAR FORMULARIO
--------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre'] ?? '');
    $especie_id = intval($_POST['especie_id'] ?? 0);   // ← CAMBIO IMPORTANTE
    $sexo = $_POST['sexo'] ?? 'desconocido';
    $edad = trim($_POST['edad'] ?? '');
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? null;

    $tamano = $_POST['tamano'] ?? null;
    $peso = $_POST['peso'] ?? null;

    $estado_salud = trim($_POST['estado_salud'] ?? '');
    $esterilizado = isset($_POST['esterilizado']) ? 1 : 0;
    $vacunado = isset($_POST['vacunado']) ? 1 : 0;
    $desparasitado = isset($_POST['desparasitado']) ? 1 : 0;
    $microchip = trim($_POST['microchip'] ?? '');

    $fecha_ingreso = $_POST['fecha_ingreso'] ?? null;
    $fecha_rescate = $_POST['fecha_rescate'] ?? null;

    $adoptable = isset($_POST['adoptable']) ? 1 : 0;

    $descripcion = trim($_POST['descripcion'] ?? '');

    /* VALIDACIONES */
    if ($nombre === '') $errores[] = "El nombre no puede estar vacío.";

    if ($especie_id <= 0) {
        $errores[] = "Debes seleccionar una especie.";
    }

    $id_raza = intval($_POST['id_raza'] ?? 0);

    if ($id_raza <= 0) {
        $errores[] = "Debes seleccionar una raza.";
    }

    if ($fecha_ingreso === '') {
        $errores[] = "La fecha de ingreso es obligatoria.";
    }

    /* SI NO HAY ERRORES → ACTUALIZAR */
    if (empty($errores)) {
        try {
            $stmt = $pdo->prepare("
                UPDATE animales SET
                    nombre = ?, id_raza = ?, sexo = ?, edad = ?, fecha_nacimiento = ?,
                    tamano = ?, peso = ?, estado_salud = ?, esterilizado = ?, vacunado = ?,
                    desparasitado = ?, microchip = ?, fecha_ingreso = ?, fecha_rescate = ?,
                    adoptable = ?, descripcion = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $nombre, $id_raza, $sexo, $edad, $fecha_nacimiento,
                $tamano, $peso, $estado_salud, $esterilizado, $vacunado,
                $desparasitado, $microchip, $fecha_ingreso, $fecha_rescate,
                $adoptable, $descripcion, $id
            ]);

            /* SUBIDA DE NUEVAS FOTOS */
            $carpeta = __DIR__ . '/../../../uploads/adopciones/' . $id;
            if (!is_dir($carpeta)) mkdir($carpeta, 0777, true);

            if (!empty($_FILES['fotos']['name'][0])) {
                foreach ($_FILES['fotos']['tmp_name'] as $index => $tmpName) {
                    if ($_FILES['fotos']['error'][$index] === UPLOAD_ERR_OK) {

                        $nombreOriginal = basename($_FILES['fotos']['name'][$index]);
                        $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

                        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) continue;

                        $nuevoNombre = uniqid('foto_') . '.' . $extension;
                        $rutaDestino = $carpeta . '/' . $nuevoNombre;

                        if (move_uploaded_file($tmpName, $rutaDestino)) {

                            $stmt = $pdo->prepare("
                                INSERT INTO animales_fotos (id_animal, ruta, es_principal)
                                VALUES (?, ?, 0)
                            ");

                            $stmt->execute([
                                $id,
                                'uploads/adopciones/' . $id . '/' . $nuevoNombre
                            ]);
                        }
                    }
                }
            }

            $exito = true;

            header("Location: sistema_adopciones_editar_animales.php?id=$id&ok=1");
            exit;

        } catch (PDOException $e) {
            $errores[] = "Error al actualizar: " . $e->getMessage();
        }
    }
}

$pagina='sistema_adopciones_editar_animales';

include('../../includes/header.php');
?>

<main>
    <section>
        <div class="container">
            <h2>Editar datos del animal: <?= htmlspecialchars($animal['nombre']) ?></h2>

            <?php if ($exito): ?>
                <p class="exito"><i class="fa-regular fa-check-double"></i> Cambios guardados correctamente.</p>
            <?php endif; ?>

            <?php if (isset($_GET['ok'])): ?>
                <p class="exito"><i class="fa-regular fa-check-double"></i> Cambios guardados correctamente.</p>
            <?php endif; ?>

            <?php if (!empty($errores)): ?>
                <div class="errores">
                    <ul>
                        <?php foreach ($errores as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- FORMULARIO -->
            <form method="post" enctype="multipart/form-data" class="formulario">

                <!-- NOMBRE -->
                <div class="filtro">
                    <label>Nombre:</label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($animal['nombre']) ?>">
                </div>

                <!-- ESPECIE -->
                <div class="filtro">
                    <label>Especie:</label>
                    <select name="especie_id" id="especie_id">
                        <option value="">Selecciona una especie</option>

                        <?php foreach ($especies as $esp): ?>
                            <option value="<?= $esp['id'] ?>"
                                <?= ($animal['especie_id'] == $esp['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($esp['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- RAZA -->
                <div class="filtro">
                    <label for="id_raza">Raza:</label>
                    <select name="id_raza" id="id_raza">
                        <option value="">Selecciona una raza</option>

                        <?php foreach ($razas as $raza): ?>
                            <option value="<?= $raza['id'] ?>"
                                data-especie="<?= $raza['especie_id'] ?>"
                                <?= ($animal['id_raza'] == $raza['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($raza['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- SEXO -->
                <div class="filtro">
                    <label>Sexo:</label>
                    <select name="sexo">
                        <option value="desconocido" <?= $animal['sexo']=='desconocido'?'selected':'' ?>>Desconocido</option>
                        <option value="macho" <?= $animal['sexo']=='macho'?'selected':'' ?>>Macho</option>
                        <option value="hembra" <?= $animal['sexo']=='hembra'?'selected':'' ?>>Hembra</option>
                    </select>
                </div>

                <!-- EDAD -->
                <div class="filtro">
                    <label>Edad:</label>
                    <input type="text" name="edad" value="<?= htmlspecialchars($animal['edad']) ?>">
                </div>

                <!-- FECHA NACIMIENTO -->
                <div class="filtro">
                    <label>Fecha nacimiento:</label>
                    <input type="date" name="fecha_nacimiento" value="<?= htmlspecialchars($animal['fecha_nacimiento']) ?>">
                </div>

                <!-- TAMAÑO -->
                <div class="filtro">
                    <label>Tamaño:</label>
                    <select name="tamano">
                        <option value="">Selecciona</option>
                        <option value="pequeno" <?= $animal['tamano']=='pequeno'?'selected':'' ?>>Pequeño</option>
                        <option value="mediano" <?= $animal['tamano']=='mediano'?'selected':'' ?>>Mediano</option>
                        <option value="grande" <?= $animal['tamano']=='grande'?'selected':'' ?>>Grande</option>
                        <option value="muy_grande" <?= $animal['tamano']=='muy_grande'?'selected':'' ?>>Muy grande</option>
                    </select>
                </div>

                <!-- PESO -->
                <div class="filtro">
                    <label>Peso (kg):</label>
                    <input type="number" step="0.01" name="peso" value="<?= htmlspecialchars($animal['peso']) ?>">
                </div>

                <!-- ESTADO SALUD -->
                <label>Estado de salud:</label>
                <textarea name="estado_salud" rows="3"><?= htmlspecialchars($animal['estado_salud']) ?></textarea>

                <!-- CHECKBOXES -->
                <div class="filtro">
                    <label><input type="checkbox" name="esterilizado" <?= $animal['esterilizado']?'checked':'' ?>> Esterilizado</label>
                </div>

                <div class="filtro">
                    <label><input type="checkbox" name="vacunado" <?= $animal['vacunado']?'checked':'' ?>> Vacunado</label>
                </div>

                <div class="filtro">
                    <label><input type="checkbox" name="desparasitado" <?= $animal['desparasitado']?'checked':'' ?>> Desparasitado</label>
                </div>

                <!-- MICROCHIP -->
                <div class="filtro">
                    <label>Microchip:</label>
                    <input type="text" name="microchip" value="<?= htmlspecialchars($animal['microchip']) ?>">
                </div>

                <!-- FECHAS -->
                <div class="filtro">
                    <label>Fecha ingreso:</label>
                    <input type="date" name="fecha_ingreso" value="<?= htmlspecialchars($animal['fecha_ingreso']) ?>">
                </div>

                <div class="filtro">
                    <label>Fecha rescate:</label>
                    <input type="date" name="fecha_rescate" value="<?= htmlspecialchars($animal['fecha_rescate']) ?>">
                </div>

                <!-- ADOPTABLE -->
                <div class="filtro">
                    <label><input type="checkbox" name="adoptable" <?= $animal['adoptable']?'checked':'' ?>> Disponible para adopción</label>
                </div>

                <!-- DESCRIPCIÓN (QUILL) -->
                <label>Descripción:</label>

                <!-- Editor visual de Quill -->
                <?= editor_quill('descripcion', $animal['descripcion'] ?? '') ?>

                <!-- FOTOS -->
                <div class="filtro">
                    <label>Nuevas fotos:</label>
                    <input type="file" name="fotos[]" multiple accept="image/*">
                </div>

                <button type="submit" class="btn-primary" id="btn-guardar">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
                </button>

            </form>

            <hr>

            <h3>Fotos actuales</h3>

            <div class="galeria-fotos">
                <?php foreach ($fotos as $foto): ?>
                    <div class="foto-item">
                        <img src="<?= asset (htmlspecialchars($foto['ruta'])); ?>"
                             class="thumb-animal"
                             data-img="<?= asset (htmlspecialchars($foto['ruta'])) ?>">

                        <?php if ($foto['es_principal']): ?>
                            <span class="badge badge-success">Principal</span>
                        <?php else: ?>
                            <a class="btn-principal" href="?id=<?= $id ?>&principal=<?= $foto['id'] ?>">
                                <i class="fa-solid fa-star"></i> Principal
                            </a>
                        <?php endif; ?>

                        <a class="btn delete-user"
                           onclick="return confirm('¿Eliminar esta foto?')"
                           href="?id=<?= $id ?>&borrar_foto=<?= $foto['id'] ?>">
                            <i class="fa-solid fa-skull-crossbones"></i> Eliminar
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
    </section>

    <!-- MODAL PARA VER IMAGEN -->
    <div id="modalAnimal" class="modal-bichillo">
        <span class="cerrar-modal">&times;</span>
        <img class="modal-contenido" id="imgModalAnimal">
    </div>

    <style>
        .galeria-fotos {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .foto-item {
            text-align: center;
        }
        .thumb-animal {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 6px;
            cursor: pointer;
            transition: transform .2s;
        }
        .thumb-animal:hover {
            transform: scale(1.05);
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
        .btn-principal {
            background-color: #17a2b8;
            color: #fff;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 0.85em;
            border: none;
            cursor: pointer;
            transition: background-color .2s ease, transform .15s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-principal:hover {
            background-color: #138496;
            transform: scale(1.03);
        }
        .btn-principal i {
            font-size: 0.9em;
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
            const selectRaza = document.getElementById("id_raza");

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
    </script>

</main>

<?php include('../../includes/footer.php');