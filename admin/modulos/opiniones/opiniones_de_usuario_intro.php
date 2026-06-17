<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once(__DIR__ . '/../../../config/funciones.php');

// Si no está logueado, redirigimos al login
if (!isLoggedIn()) {
    header("Location: index.php");
    exit;
}

$errores = [];
$exito = false;

// Obtener contenido actual
$stmt = $pdo->query("SELECT * FROM intro_opinion LIMIT 1");
$opinion_intro = $stmt->fetch();

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contenido = trim($_POST['contenido'] ?? '');

    if ($contenido === '') {
        $errores[] = "El contenido no puede estar vacío.";
    } else {
        try {
            if ($opinion_intro) {
                $stmt = $pdo->prepare("UPDATE intro_opinion SET contenido = ? WHERE id = ?");
                $stmt->execute([$contenido, $opinion_intro['id']]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO intro_opinion (contenido) VALUES (?)");
                $stmt->execute([$contenido]);
            }
            $exito = true;
            $opinion_intro['contenido'] = $contenido;
        } catch (PDOException $e) {
            $errores[] = "Error al guardar: " . $e->getMessage();
        }
    }
}

$pagina='opinion_intro';

include('../../includes/header.php');
?>

    <main>
        <section>
            <div class="container">
                <h2>Introducción de la pagina de opiniones de los usuarios</h2>

                <?php if ($exito): ?>
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

                <form method="post" class="formulario">
                    <label>Texto invocatorio:</label>

                    <!-- Editor visual de Quill -->
                    <?= editor_quill('contenido', $opinion_intro['contenido'] ?? '') ?>

                    <button type="submit" id="btn-guardar" class="btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i> Guardar
                    </button>
                </form>
            </div>
        </section>
    </main>

<?php include('../../includes/footer.php');