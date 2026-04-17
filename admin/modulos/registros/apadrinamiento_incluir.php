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

// Obtener especies activas
$especies = $pdo->query("
    SELECT id, nombre
    FROM especies_animales
    WHERE activo = 1
    ORDER BY nombre
")->fetchAll(PDO::FETCH_ASSOC);

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre           = trim($_POST['nombre'] ?? '');
    $especie_id       = (int)($_POST['especie_id'] ?? 0);
    $raza_id          = !empty($_POST['raza_id']) ? (int)$_POST['raza_id'] : null;
    $fecha_ingreso    = !empty($_POST['fecha_ingreso']) ? $_POST['fecha_ingreso'] : null;
    $mini_descripcion = trim($_POST['mini_descripcion'] ?? '');
    $historia         = trim($_POST['historia'] ?? '');
    $estado           = $_POST['estado'] ?? 'activo';

    if ($nombre === '') $errores[] = 'El nombre es obligatorio.';
    if ($especie_id <= 0) $errores[] = 'Debes seleccionar una especie.';

    $slug = generarSlug($nombre);

    if (empty($errores)) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM animals_sponsor WHERE slug = ?");
        $stmt->execute([$slug]);
        if ($stmt->fetchColumn() > 0) {
            $slug .= '-' . time();
        }
    }

    if (empty($errores)) {

        // 1️⃣ Insertamos el animal SIN foto
        $sql = "INSERT INTO animals_sponsor 
                (nombre, especie_id, raza_id, fecha_ingreso, foto_principal, mini_descripcion, historia, slug, estado, created_at, updated_at)
                VALUES 
                (:nombre, :especie_id, :raza_id, :fecha_ingreso, NULL, :mini_descripcion, :historia, :slug, :estado, NOW(), NOW())";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nombre' => $nombre,
            ':especie_id' => $especie_id,
            ':raza_id' => $raza_id,
            ':fecha_ingreso' => $fecha_ingreso ?: null,
            ':mini_descripcion' => $mini_descripcion,
            ':historia' => $historia,
            ':slug' => $slug,
            ':estado' => $estado
        ]);

        // 2️⃣ Obtenemos el ID del animal recién creado
        $id_animal = $pdo->lastInsertId();

        // 3️⃣ Procesamos la imagen si existe
        if (isset($_FILES['foto_principal']) && $_FILES['foto_principal']['error'] === UPLOAD_ERR_OK) {

            $tmp_name = $_FILES['foto_principal']['tmp_name'];
            $nombre_original = basename($_FILES['foto_principal']['name']);
            $ext = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));

            // Nombre final del archivo
            $nombre_fichero = 'foto_principal.' . $ext;

            // Carpeta individual del animal
            $ruta_carpeta = __DIR__ . '/../../../uploads/apadrinamientos/' . $id_animal;

            if (!is_dir($ruta_carpeta)) {
                mkdir($ruta_carpeta, 0775, true);
            }

            // Ruta final absoluta
            $ruta_abs = $ruta_carpeta . '/' . $nombre_fichero;

            // Ruta relativa para guardar en BD
            $ruta_rel = 'uploads/apadrinamientos/' . $id_animal . '/' . $nombre_fichero;

            if (move_uploaded_file($tmp_name, $ruta_abs)) {

                // 4️⃣ Actualizamos la foto en la BD
                $stmt = $pdo->prepare("UPDATE animals_sponsor SET foto_principal = ? WHERE id = ?");
                $stmt->execute([$ruta_rel, $id_animal]);

            } else {
                $errores[] = 'No se pudo guardar la imagen subida.';
            }
        }

        if (empty($errores)) {
            $exito = true;
        }
    }
}

$pagina = 'apadrinamiento_incluir';

include('../../includes/header.php');
?>

<main>
    <section>
        <div class="container">
            <h2>Incluir nuevo animal para apadrinar</h2>

            <?php if (!empty($errores)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errores as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($exito): ?>
                <div class="alert alert-success">
                    El animal se ha incluido correctamente.
                </div>
            <?php endif; ?>

            <form action="" method="post" enctype="multipart/form-data">

                <div class="mb-3">
                    <label>Nombre *</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="especie_id">Especie *</label>
                    <select name="especie_id" id="especie_id" class="form-control" required>
                        <option value="">Selecciona una especie</option>
                        <?php foreach ($especies as $esp): ?>
                            <option value="<?= $esp['id'] ?>"
                                <?= (!empty($especie_id) && $especie_id == $esp['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($esp['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="raza_id">Raza</label>
                    <select name="raza_id" id="raza_id" class="form-control">
                        <option value="">Selecciona una raza</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Fecha de ingreso</label>
                    <input type="date" name="fecha_ingreso" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Foto principal</label>
                    <input type="file" name="foto_principal" class="form-control" accept="image/*">
                </div>

                <div class="mb-3">
                    <label>Mini descripción</label>
                    <input type="text" name="mini_descripcion" class="form-control" maxlength="255">
                </div>

                <div class="mb-3">
                    <label>Historia</label>

                    <!-- Editor visual de Quill -->
                    <?= editor_quill('historia', $_POST['historia'] ?? '') ?>
                    
                </div>

                <div class="mb-3">
                    <label>Estado</label>
                    <select name="estado" class="form-control">
                        <option value="activo">Activo</option>
                        <option value="oculto">Oculto</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary" id="btn-guardar">Guardar animal</button>
            </form>

        </div>
    </section>
</main>

<script>
    // Script para cargar las razas dinamicamente
    document.addEventListener("DOMContentLoaded", () => {

        const selectEspecie = document.getElementById("especie_id");
        const selectRaza = document.getElementById("raza_id");

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