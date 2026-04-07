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

$errores = [];
$exito = false;

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre'] ?? '');
    $especie = trim($_POST['especie'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');

    if ($nombre === '') {
        $errores[] = "El nombre de la raza no puede estar vacío.";
    }

    if ($especie === '') {
        $errores[] = "La especie no puede estar vacía.";
    }

    if (empty($errores)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO razas_animales (nombre, especie, descripcion)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$nombre, $especie, $descripcion]);

            $exito = true;

        } catch (PDOException $e) {
            $errores[] = "Error al guardar: " . $e->getMessage();
        }
    }
}

$pagina='adopciones_incluir_raza';

include('../includes/header.php');
?>

<main>
    <section>
        <div class="container">
            <h2>Incluir una nueva raza de animal</h2>

            <?php if ($exito): ?>
                <p class="exito"><i class="fa-regular fa-check-double"></i> Raza añadida correctamente.</p>
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

            <form method="post" class="formulario">

                <div class="filtro">
                    <label for="nombre">Raza:</label>
                    <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" />
                </div>

                <div class="filtro">
                    <label for="especie">Especie:</label>
                    <input type="text" name="especie" id="especie" value="<?= htmlspecialchars($_POST['especie'] ?? '') ?>" />
                </div>

                <label for="descripcion">Descripción (opcional):</label>
                <textarea name="descripcion" id="descripcion" rows="4"><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>

                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar raza
                </button>

            </form>
        </div>
    </section>
</main>

<?php include('../includes/footer.php');