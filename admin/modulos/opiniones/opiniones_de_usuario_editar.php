<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../../config/database.php';
require_once(__DIR__ . '/../../../config/funciones.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: opiniones_de_usuario_listado.php?error=ID inválido");
    exit;
}

$id = intval($_GET['id']);

$stmt = $pdo->prepare("SELECT * FROM opiniones_usuarios WHERE id = :id");
$stmt->execute([':id' => $id]);
$opinion = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$opinion) {
    header("Location: opiniones_de_usuario_listado.php?error=No encontrado");
    exit;
}

// Si no está logueado, redirigimos al login
if (!isLoggedIn()) {
    header("Location: index.php");
    exit;
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre  = trim($_POST['nombre']);
    $email   = trim($_POST['email']);
    $mensaje = trim($_POST['mensaje']);

    // Imagen actual en BD
    $imagen_actual = $opinion['imagen'];
    $nueva_imagen  = $imagen_actual;

    // ¿Quiere eliminar la imagen actual?
    $eliminar_imagen = isset($_POST['eliminar_imagen']) && $_POST['eliminar_imagen'] == '1';

    // Ruta física base
    $base_path = __DIR__ . '/../../../';

    // Si marca eliminar imagen, la borramos del disco y de la BD
    if ($eliminar_imagen && !empty($imagen_actual)) {
        $ruta_fisica = $base_path . $imagen_actual;
        if (file_exists($ruta_fisica)) {
            unlink($ruta_fisica);
        }
        $nueva_imagen = null;
    }

    // Si sube una nueva imagen, borramos la anterior (si existe) y guardamos la nueva
    if (!empty($_FILES['imagen']['name'])) {

        // Borrar anterior si existe
        if (!empty($imagen_actual)) {
            $ruta_fisica_anterior = $base_path . $imagen_actual;
            if (file_exists($ruta_fisica_anterior)) {
                unlink($ruta_fisica_anterior);
            }
        }

        // Directorio basado en el nombre
        $nombre_limpio = preg_replace('/[^a-zA-Z0-9_-]/', '_', $nombre);
        $directorio_relativo = "uploads/opiniones/" . $nombre_limpio;
        $directorio_absoluto = $base_path . $directorio_relativo;

        if (!is_dir($directorio_absoluto)) {
            mkdir($directorio_absoluto, 0777, true);
        }

        $nombreArchivo = time() . "_" . basename($_FILES['imagen']['name']);
        $ruta_relativa = $directorio_relativo . "/" . $nombreArchivo;
        $ruta_absoluta = $base_path . $ruta_relativa;

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_absoluta)) {
            $nueva_imagen = $ruta_relativa;
        }
    }

    // Actualizar en BD
    $stmt = $pdo->prepare("
        UPDATE opiniones_usuarios
        SET nombre = :nombre,
            email = :email,
            mensaje = :mensaje,
            imagen = :imagen
        WHERE id = :id
    ");

    $stmt->execute([
        ':nombre' => $nombre,
        ':email'  => $email,
        ':mensaje' => $mensaje,
        ':imagen' => $nueva_imagen,
        ':id'     => $id
    ]);

    header("Location: opiniones_de_usuario_editar.php?id=$id&msg=Guardado");
    exit;
}

$pagina = 'opiniones_de_usuario_editar';

include('../../includes/header.php');
?>

<main>
    <section>
        <div class="container">
            <h2>Edición de la opinión de un usuario</h2>

            <?php if (isset($_GET['msg'])): ?>
                <p class="alert-success">Cambios guardados correctamente.</p>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">

                <label>Nombre:</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($opinion['nombre']) ?>" required>

                <label>Email:</label>
                <input type="email" name="email" value="<?= htmlspecialchars($opinion['email']) ?>" required>

                <label>Mensaje:</label>
                <textarea name="mensaje" required><?= htmlspecialchars($opinion['mensaje']) ?></textarea>

                <label>Imagen actual:</label><br>
                <?php if (!empty($opinion['imagen'])): ?>
                    <img src="<?= asset($opinion['imagen']) ?>"
                        style="width:100px; height:100px; object-fit:cover; border-radius:6px;"><br>

                    <label>
                        <input type="checkbox" name="eliminar_imagen" value="1">
                        Eliminar imagen actual
                    </label>
                <?php else: ?>
                    <p>No hay imagen (se mostrará la imagen por defecto en el frontend).</p>
                <?php endif; ?>

                <br><br>

                <label>Subir nueva imagen (opcional):</label>
                <input type="file" name="imagen" accept="image/*">

                <br><br>

                <button class="btn btn-success">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
                </button>
                <a href="opiniones_de_usuario_listado.php" class="btn btn-volver">
                    <i class="fa-solid fa-arrow-left"></i> Volver
                </a>

            </form>

        </div>
    </section>
</main>

<?php include('../../includes/footer.php');
