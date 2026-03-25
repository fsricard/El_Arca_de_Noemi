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

$errores = [];
$exito = false;

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_FILES['bichillos']) || empty($_FILES['bichillos']['name'][0])) {
        $errores[] = "Debes seleccionar al menos una imagen.";
    } else {

        $total = count($_FILES['bichillos']['name']);
        $rutaBase = __DIR__ . '/../uploads/bichillos/';

        // Crear carpeta si no existe
        if (!is_dir($rutaBase)) {
            mkdir($rutaBase, 0777, true);
        }

        for ($i = 0; $i < $total; $i++) {

            $nombreOriginal = $_FILES['bichillos']['name'][$i];
            $tmp = $_FILES['bichillos']['tmp_name'][$i];
            $error = $_FILES['bichillos']['error'][$i];

            if ($error !== UPLOAD_ERR_OK) {
                $errores[] = "Error al subir el archivo: $nombreOriginal";
                continue;
            }

            // Validar extensión
            $ext = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));
            $permitidas = ['jpg', 'jpeg', 'png', 'webp'];

            if (!in_array($ext, $permitidas)) {
                $errores[] = "El archivo $nombreOriginal no es una imagen válida.";
                continue;
            }

            // Crear nombre único
            $nuevoNombre = time() . "_" . uniqid() . "." . $ext;
            $rutaDestino = $rutaBase . $nuevoNombre;

            // Mover archivo
            if (move_uploaded_file($tmp, $rutaDestino)) {

                // Guardar ruta en BD
                $rutaBD = "uploads/bichillos/" . $nuevoNombre;

                $stmt = $pdo->prepare("INSERT INTO noemi_bichillos (bichillo) VALUES (?)");
                $stmt->execute([$rutaBD]);

            } else {
                $errores[] = "No se pudo guardar la imagen $nombreOriginal.";
            }
        }

        if (empty($errores)) {
            $exito = true;
        }
    }
}

$pagina = 'noemi_bichillos';

include('includes/header.php');
?>

<main>
    <section>
        <div class="container">
            <h2>Subir bichillos de Noemí</h2>

            <?php if ($exito): ?>
                <p class="exito"><i class="fa-regular fa-check-double"></i> Imágenes subidas correctamente.</p>
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
                <label>Selecciona una o varias imágenes:</label>
                <input type="file" name="bichillos[]" accept="image/*" multiple required>

                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-upload"></i> Subir imágenes
                </button>
            </form>
        </div>
    </section>
</main>

<?php include('includes/footer.php');