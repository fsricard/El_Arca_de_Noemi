<?php
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../config/database.php';
require_once(__DIR__ . '/../config/funciones.php');

if (!isLoggedIn()) {
    header("Location: index.php");
    exit;
}

// Validar ID
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    die("ID de animal no válido.");
}

// Obtener datos del animal
$stmt = $pdo->prepare("
    SELECT a.*, r.nombre AS raza_nombre, r.especie
    FROM animales a
    INNER JOIN razas_animales r ON a.id_raza = r.id
    WHERE a.id = ?
");
$stmt->execute([$id]);
$animal = $stmt->fetch();

if (!$animal) {
    die("Animal no encontrado.");
}

// Obtener especies para el selector
$especies = $pdo->query("
    SELECT DISTINCT especie
    FROM razas_animales
    WHERE activo = 1
    ORDER BY especie
")->fetchAll(PDO::FETCH_COLUMN);

// Obtener fotos del animal
$stmt = $pdo->prepare("SELECT * FROM animales_fotos WHERE id_animal = ? ORDER BY es_principal DESC, id ASC");
$stmt->execute([$id]);
$fotos = $stmt->fetchAll();

$errores = [];
$exito = false;

/* ---------------------------------------------------------
   ACCIONES: BORRAR FOTO
--------------------------------------------------------- */
if (isset($_GET['borrar_foto'])) {
    $id_foto = intval($_GET['borrar_foto']);

    $stmt = $pdo->prepare("SELECT ruta FROM animales_fotos WHERE id = ? AND id_animal = ?");
    $stmt->execute([$id_foto, $id]);
    $foto = $stmt->fetch();

    if ($foto) {
        @unlink(__DIR__ . '/../' . $foto['ruta']);
        $pdo->prepare("DELETE FROM animales_fotos WHERE id = ?")->execute([$id_foto]);
    }

    header("Location: adopciones_editar.php?id=$id");
    exit;
}

/* ---------------------------------------------------------
   ACCIONES: MARCAR COMO PRINCIPAL
--------------------------------------------------------- */
if (isset($_GET['principal'])) {
    $id_foto = intval($_GET['principal']);

    $pdo->prepare("UPDATE animales_fotos SET es_principal = 0 WHERE id_animal = ?")->execute([$id]);
    $pdo->prepare("UPDATE animales_fotos SET es_principal = 1 WHERE id = ?")->execute([$id_foto]);

    header("Location: adopciones_editar.php?id=$id");
    exit;
}

/* ---------------------------------------------------------
   PROCESAR FORMULARIO
--------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre'] ?? '');
    $especie = trim($_POST['especie'] ?? '');
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

    if ($nombre === '') $errores[] = "El nombre no puede estar vacío.";
    if ($especie === '') {
        $errores[] = "Debes seleccionar una especie.";
    } else {
        // Buscar una raza interna para esa especie
        $stmt = $pdo->prepare("
            SELECT id 
            FROM razas_animales 
            WHERE especie = ? AND activo = 1 
            ORDER BY id ASC 
            LIMIT 1
        ");
        $stmt->execute([$especie]);
        $id_raza = $stmt->fetchColumn();

        if (!$id_raza) {
            $errores[] = "No existe una raza interna para esta especie.";
        }
    }
    if ($fecha_ingreso === '') $errores[] = "La fecha de ingreso es obligatoria.";

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
            $carpeta = __DIR__ . '/../uploads/adopciones/' . $id;
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

            header("Location: adopciones_editar.php?id=$id&ok=1");
            exit;

        } catch (PDOException $e) {
            $errores[] = "Error al actualizar: " . $e->getMessage();
        }
    }
}

$pagina='adopciones_editar';

include('includes/header.php');
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

                <div class="filtro">
                    <label>Nombre:</label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($animal['nombre']) ?>">
                </div>

                <div class="filtro">
                    <label>Especie:</label>
                    <select name="especie" id="especie">
                        <option value="">Selecciona una especie</option>
                        <?php foreach ($especies as $esp): ?>
                            <option value="<?= htmlspecialchars($esp) ?>"
                                <?= ($animal['especie'] == $esp) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($esp) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filtro">
                    <label>Sexo:</label>
                    <select name="sexo">
                        <option value="desconocido" <?= $animal['sexo']=='desconocido'?'selected':'' ?>>Desconocido</option>
                        <option value="macho" <?= $animal['sexo']=='macho'?'selected':'' ?>>Macho</option>
                        <option value="hembra" <?= $animal['sexo']=='hembra'?'selected':'' ?>>Hembra</option>
                    </select>
                </div>

                <div class="filtro">
                    <label>Edad:</label>
                    <input type="text" name="edad" value="<?= htmlspecialchars($animal['edad']) ?>">
                </div>

                <div class="filtro">
                    <label>Fecha nacimiento:</label>
                    <input type="date" name="fecha_nacimiento" value="<?= htmlspecialchars($animal['fecha_nacimiento']) ?>">
                </div>

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

                <div class="filtro">
                    <label>Peso (kg):</label>
                    <input type="number" step="0.01" name="peso" value="<?= htmlspecialchars($animal['peso']) ?>">
                </div>

                <label>Estado de salud:</label>
                <textarea name="estado_salud" rows="3"><?= htmlspecialchars($animal['estado_salud']) ?></textarea>

                <div class="filtro">
                    <label><input type="checkbox" name="esterilizado" <?= $animal['esterilizado']?'checked':'' ?>> Esterilizado</label>
                </div>

                <div class="filtro">
                    <label><input type="checkbox" name="vacunado" <?= $animal['vacunado']?'checked':'' ?>> Vacunado</label>
                </div>

                <div class="filtro">
                    <label><input type="checkbox" name="desparasitado" <?= $animal['desparasitado']?'checked':'' ?>> Desparasitado</label>
                </div>

                <div class="filtro">
                    <label>Microchip:</label>
                    <input type="text" name="microchip" value="<?= htmlspecialchars($animal['microchip']) ?>">
                </div>

                <div class="filtro">
                    <label>Fecha ingreso:</label>
                    <input type="date" name="fecha_ingreso" value="<?= htmlspecialchars($animal['fecha_ingreso']) ?>">
                </div>

                <div class="filtro">
                    <label>Fecha rescate:</label>
                    <input type="date" name="fecha_rescate" value="<?= htmlspecialchars($animal['fecha_rescate']) ?>">
                </div>

                <div class="filtro">
                    <label><input type="checkbox" name="adoptable" <?= $animal['adoptable']?'checked':'' ?>> Disponible para adopción</label>
                </div>

                <label>Descripción:</label>
                <textarea name="descripcion" rows="4"><?= htmlspecialchars($animal['descripcion']) ?></textarea>

                <div class="filtro">
                    <label>Nuevas fotos:</label>
                    <input type="file" name="fotos[]" multiple accept="image/*">
                </div>

                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
                </button>

            </form>

            <hr>

            <h3>Fotos actuales</h3>

            <div class="galeria-fotos">
                <?php foreach ($fotos as $foto): ?>
                    <div class="foto-item">
                        <img src="../<?= htmlspecialchars($foto['ruta']) ?>"
                             class="thumb-animal"
                             data-img="../<?= htmlspecialchars($foto['ruta']) ?>">

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

</main>

<?php include('includes/footer.php');