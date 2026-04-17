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

$errores = [];
$exito = false;

// Obtener especies
$especies = $pdo->query("
    SELECT id, nombre
    FROM especies_animales
    WHERE activo = 1
    ORDER BY nombre
")->fetchAll(PDO::FETCH_ASSOC);

/* ---------------------------------------------------------
   2. PROCESAR FORMULARIO
--------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre'] ?? '');
    $especie_id = intval($_POST['especie_id'] ?? 0); // <- CAMBIO
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

    /* ---------------------------------------------------------
       VALIDACIONES
    --------------------------------------------------------- */
    if ($nombre === '') {
        $errores[] = "El nombre del animal no puede estar vacío.";
    }

    if ($especie_id <= 0) {
        $errores[] = "Debes seleccionar una especie.";
    }

    $id_raza = intval($_POST['id_raza'] ?? 0);

    if ($id_raza <= 0) {
        $errores[] = "Debes seleccionar una raza.";
    }

    if ($fecha_ingreso === null || $fecha_ingreso === '') {
        $errores[] = "La fecha de ingreso es obligatoria.";
    }

    // Validación de fotos
    if (!empty($_FILES['fotos']['name'][0])) {
        foreach ($_FILES['fotos']['error'] as $error) {
            if ($error !== UPLOAD_ERR_OK) {
                $errores[] = "Error al subir una de las fotos.";
                break;
            }
        }
    }

    /* ---------------------------------------------------------
       SI NO HAY ERRORES → INSERTAR ANIMAL
    --------------------------------------------------------- */
    if (empty($errores)) {
        try {

            $stmt = $pdo->prepare("
                INSERT INTO animales 
                (nombre, id_raza, sexo, edad, fecha_nacimiento, tamano, peso, estado_salud, esterilizado, vacunado, desparasitado, microchip, fecha_ingreso, fecha_rescate, adoptable, descripcion)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $nombre, $id_raza, $sexo, $edad, $fecha_nacimiento, $tamano, $peso,
                $estado_salud, $esterilizado, $vacunado, $desparasitado, $microchip,
                $fecha_ingreso, $fecha_rescate, $adoptable, $descripcion
            ]);

            $id_animal = $pdo->lastInsertId();

            /* ---------------------------------------------------------
               SUBIDA DE FOTOS
            --------------------------------------------------------- */
            $carpeta = __DIR__ . '/../../../uploads/adopciones/' . $id_animal;

            if (!is_dir($carpeta)) {
                mkdir($carpeta, 0777, true);
            }

            if (!empty($_FILES['fotos']['name'][0])) {

                foreach ($_FILES['fotos']['tmp_name'] as $index => $tmpName) {

                    if ($_FILES['fotos']['error'][$index] === UPLOAD_ERR_OK) {

                        $nombreOriginal = basename($_FILES['fotos']['name'][$index]);
                        $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

                        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
                            continue;
                        }

                        $nuevoNombre = uniqid('foto_') . '.' . $extension;
                        $rutaDestino = $carpeta . '/' . $nuevoNombre;

                        if (move_uploaded_file($tmpName, $rutaDestino)) {

                            $stmt = $pdo->prepare("
                                INSERT INTO animales_fotos (id_animal, ruta, es_principal)
                                VALUES (?, ?, ?)
                            ");

                            $stmt->execute([
                                $id_animal,
                                'uploads/adopciones/' . $id_animal . '/' . $nuevoNombre,
                                $index === 0 ? 1 : 0
                            ]);
                        }
                    }
                }
            }

            $exito = true;

        } catch (PDOException $e) {
            $errores[] = "Error al guardar: " . $e->getMessage();
        }
    }
}

$pagina='adopciones_incluir_animal';

include('../../includes/header.php');
?>

<main>
    <section>
        <div class="container">
            <h2>Incluir nuevo animal en adopción</h2>

            <?php if ($exito): ?>
                <p class="exito"><i class="fa-regular fa-check-double"></i> Animal añadido correctamente.</p>
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

            <form method="post" enctype="multipart/form-data" class="formulario">

                <div class="filtro">
                    <label for="nombre">Nombre del animal:</label>
                    <input type="text" name="nombre" id="nombre"
                        value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" />
                </div>

                <!-- ESPECIE -->
                <div class="filtro">
                    <label for="especie_id">Especie:</label>
                    <select name="especie_id" id="especie_id">
                        <option value="">Selecciona una especie</option>
                        <?php foreach ($especies as $esp): ?>
                            <option value="<?= $esp['id'] ?>"
                                <?= (($_POST['especie_id'] ?? '') == $esp['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($esp['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- RAZA -->
                <div class="filtro">
                    <label for="id_raza">Raza:</label>
                    <select name="id_raza" id="raza_id">
                        <option value="">Selecciona una raza</option>
                        <!-- Se cargan por AJAX -->
                    </select>
                </div>

                <div class="filtro">
                    <label for="sexo">Sexo:</label>
                    <select name="sexo" id="sexo">
                        <option value="desconocido">Desconocido</option>
                        <option value="macho">Macho</option>
                        <option value="hembra">Hembra</option>
                    </select>
                </div>

                <div class="filtro">
                    <label for="edad">Edad:</label>
                    <input type="text" name="edad" id="edad"
                        value="<?= htmlspecialchars($_POST['edad'] ?? '') ?>" />
                </div>

                <div class="filtro">
                    <label for="fecha_nacimiento">Fecha de nacimiento:</label>
                    <input type="date" name="fecha_nacimiento" id="fecha_nacimiento"
                        value="<?= htmlspecialchars($_POST['fecha_nacimiento'] ?? '') ?>" />
                </div>

                <div class="filtro">
                    <label for="tamano">Tamaño:</label>
                    <select name="tamano" id="tamano">
                        <option value="">Selecciona</option>
                        <option value="pequeno">Pequeño</option>
                        <option value="mediano">Mediano</option>
                        <option value="grande">Grande</option>
                        <option value="muy_grande">Muy grande</option>
                    </select>
                </div>

                <div class="filtro">
                    <label for="peso">Peso (kg):</label>
                    <input type="number" step="0.01" name="peso" id="peso"
                        value="<?= htmlspecialchars($_POST['peso'] ?? '') ?>" />
                </div>

                <label for="estado_salud">Estado de salud:</label>
                <textarea name="estado_salud" id="estado_salud" rows="3">
                    <?= htmlspecialchars($_POST['estado_salud'] ?? '') ?>
                </textarea>

                <div class="filtro">
                    <label><input type="checkbox" name="esterilizado"
                        <?= isset($_POST['esterilizado']) ? 'checked' : '' ?> /> Esterilizado</label>
                </div>

                <div class="filtro">
                    <label><input type="checkbox" name="vacunado"
                        <?= isset($_POST['vacunado']) ? 'checked' : '' ?> /> Vacunado</label>
                </div>

                <div class="filtro">
                    <label><input type="checkbox" name="desparasitado"
                        <?= isset($_POST['desparasitado']) ? 'checked' : '' ?> /> Desparasitado</label>
                </div>

                <div class="filtro">
                    <label for="microchip">Microchip:</label>
                    <input type="text" name="microchip" id="microchip"
                        value="<?= htmlspecialchars($_POST['microchip'] ?? '') ?>" />
                </div>

                <div class="filtro">
                    <label for="fecha_ingreso">Fecha de ingreso:</label>
                    <input type="date" name="fecha_ingreso" id="fecha_ingreso"
                        value="<?= htmlspecialchars($_POST['fecha_ingreso'] ?? '') ?>" />
                </div>

                <div class="filtro">
                    <label for="fecha_rescate">Fecha de rescate:</label>
                    <input type="date" name="fecha_rescate" id="fecha_rescate"
                        value="<?= htmlspecialchars($_POST['fecha_rescate'] ?? '') ?>" />
                </div>

                <div class="filtro">
                    <label><input type="checkbox" name="adoptable"
                        <?= isset($_POST['adoptable']) ? 'checked' : '' ?> /> Disponible para adopción</label>
                </div>

                <label for="descripcion">Descripción:</label>

                <!-- Editor visual de Quill -->
                <?= editor_quill('descripcion', $_POST['descripcion'] ?? '') ?>

                <div class="filtro">
                    <label for="fotos">Fotos del animal:</label>
                    <input type="file" name="fotos[]" id="fotos" multiple accept="image/*">
                </div>

                <button type="submit" class="btn-primary" id="btn-guardar">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar animal
                </button>

            </form>
        </div>
    </section>
</main>

<script>
    // Script para cargar las razas según la especie elegida
    document.addEventListener("DOMContentLoaded", () => {

        const selectEspecie = document.getElementById("especie_id");
        const selectRaza = document.getElementById("raza_id");

        // Cuando cambia la especie → cargar razas por AJAX
        selectEspecie.addEventListener("change", function () {

            const especieId = this.value;

            if (especieId === "") {
                selectRaza.innerHTML = '<option value="">Selecciona una raza</option>';
                return;
            }

            fetch("ajax/ajax_razas.php?especie_id=" + especieId)
                .then(response => response.json())
                .then(data => {

                    selectRaza.innerHTML = '<option value="">Selecciona una raza</option>';

                    data.forEach(raza => {
                        const option = document.createElement("option");
                        option.value = raza.id;
                        option.textContent = raza.nombre;
                        selectRaza.appendChild(option);
                    });

                })
                .catch(err => console.error("Error cargando razas:", err));
        });

    });
</script>

<?php include('../../includes/footer.php');