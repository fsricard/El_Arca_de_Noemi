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

// Procesar acciones del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Eliminar mensaje
    if (isset($_POST['eliminar'])) {
        $stmt = $pdo->prepare("DELETE FROM mensajes_contacto WHERE id = ?");
        $stmt->execute([$_POST['id']]);

        header("Location: contacto.php?msg=eliminado");
        exit;
    }

    // Guardar cambios
    if (isset($_POST['guardar'])) {
        $stmt = $pdo->prepare("
            UPDATE mensajes_contacto 
            SET nombre = ?, email = ?, asunto = ?, mensaje = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $_POST['nombre'],
            $_POST['email'],
            $_POST['asunto'],
            trim($_POST['mensaje']),
            $_POST['id']
        ]);

        header("Location: contacto_editar.php?id=" . $_POST['id'] . "&msg=guardado");
        exit;
    }
}

// Validar ID recibido por GET
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='alert alert-danger'>ID de mensaje no válido.</div>";
    exit;
}

$id = intval($_GET['id']);

// Obtener datos del mensaje
$stmt = $pdo->prepare("SELECT * FROM mensajes_contacto WHERE id = ?");
$stmt->execute([$id]);
$mensaje = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$mensaje) {
    echo "<div class='alert alert-danger'>No se encontró el mensaje.</div>";
    exit;
}

$pagina = 'contacto_editar';

include('../../includes/header.php');
?>

<main>
    <section>
        <div class="container">
            <h2>Editar mensaje de contacto</h2>

            <?php if (isset($_GET['msg']) && $_GET['msg'] === 'guardado'): ?>
                <div class="alert alert-success">Cambios guardados correctamente.</div>
            <?php endif; ?>

            <form method="POST">

                <input type="hidden" name="id" value="<?= $mensaje['id'] ?>">

                <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" name="nombre" class="form-control"
                        value="<?= htmlspecialchars($mensaje['nombre']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control"
                        value="<?= htmlspecialchars($mensaje['email']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Asunto</label>
                    <input type="text" name="asunto" class="form-control"
                        value="<?= htmlspecialchars($mensaje['asunto']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Mensaje</label>
                    <textarea name="mensaje" class="form-control" rows="6" required><?= htmlspecialchars($mensaje['mensaje']) ?></textarea>
                </div>

                <br>

                <button type="submit" name="guardar" class="btn btn-success">Guardar</button>

                <a href="contacto.php" class="btn btn-volver">
                    <i class="fa-solid fa-arrow-left"></i> Volver
                </a>

                <button type="submit" name="eliminar" class="btn delete-user"
                    onclick="return confirm('¿Seguro que deseas eliminar este mensaje?')">
                    Eliminar
                </button>

            </form>

        </div>
    </section>
</main>

<?php include('../../includes/footer.php');
