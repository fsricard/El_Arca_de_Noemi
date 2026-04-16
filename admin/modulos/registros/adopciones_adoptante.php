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

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre'] ?? '');
    $apellidos = trim($_POST['apellidos'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $ciudad = trim($_POST['ciudad'] ?? '');
    $provincia = trim($_POST['provincia'] ?? '');
    $codigo_postal = trim($_POST['codigo_postal'] ?? '');
    $notas = trim($_POST['notas'] ?? '');

    // Validaciones
    if ($nombre === '') {
        $errores[] = "El nombre no puede estar vacío.";
    }

    if ($telefono === '' && $email === '') {
        $errores[] = "Debes proporcionar al menos un teléfono o un email.";
    }

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El email no es válido.";
    }

    if (empty($errores)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO adoptantes 
                (nombre, apellidos, telefono, email, direccion, ciudad, provincia, codigo_postal, notas)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $nombre, $apellidos, $telefono, $email, $direccion,
                $ciudad, $provincia, $codigo_postal, $notas
            ]);

            // Redirección PRG
            header("Location: adopciones_adoptante.php?ok=1");
            exit;

        } catch (PDOException $e) {
            $errores[] = "Error al guardar: " . $e->getMessage();
        }
    }
}

$pagina='adopciones_adoptante';

include('../../includes/header.php');
?>

<main>
    <section>
        <div class="container">
            <h2>Incluir un nuevo adoptante</h2>

            <?php if (isset($_GET['ok'])): ?>
                <p class="exito"><i class="fa-regular fa-check-double"></i> Adoptante añadido correctamente.</p>
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
                    <label>Nombre:</label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
                </div>

                <div class="filtro">
                    <label>Apellidos:</label>
                    <input type="text" name="apellidos" value="<?= htmlspecialchars($_POST['apellidos'] ?? '') ?>">
                </div>

                <div class="filtro">
                    <label>Teléfono:</label>
                    <input type="text" name="telefono" value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>">
                </div>

                <div class="filtro">
                    <label>Email:</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>

                <div class="filtro">
                    <label>Dirección:</label>
                    <input type="text" name="direccion" value="<?= htmlspecialchars($_POST['direccion'] ?? '') ?>">
                </div>

                <div class="filtro">
                    <label>Ciudad:</label>
                    <input type="text" name="ciudad" value="<?= htmlspecialchars($_POST['ciudad'] ?? '') ?>">
                </div>

                <div class="filtro">
                    <label>Provincia:</label>
                    <input type="text" name="provincia" value="<?= htmlspecialchars($_POST['provincia'] ?? '') ?>">
                </div>

                <div class="filtro">
                    <label>Código postal:</label>
                    <input type="text" name="codigo_postal" value="<?= htmlspecialchars($_POST['codigo_postal'] ?? '') ?>">
                </div>

                <label>Notas internas:</label>
                <textarea name="notas" rows="4"><?= htmlspecialchars($_POST['notas'] ?? '') ?></textarea>

                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar adoptante
                </button>

            </form>
        </div>
    </section>
</main>

<?php include('../../includes/footer.php');