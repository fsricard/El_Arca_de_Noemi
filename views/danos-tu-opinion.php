<?php
require_once(__DIR__ . '/../config/database.php');

// Recuperar la introducción de la sección "Danos tu opinión"
$stmt = $pdo->query("
                    SELECT contenido, actualizado
                    FROM intro_opinion
                    ORDER BY id DESC
                    LIMIT 1
                ");

$intro_opinion = $stmt->fetch(PDO::FETCH_ASSOC);

// Procesamos el formulario
if (isset($_POST['enviar'])) {

    $nombre  = trim($_POST['nombre']);
    $email   = trim($_POST['email']);
    $mensaje = trim($_POST['mensaje']);

    // Validación básica
    if ($nombre !== "" && $email !== "" && $mensaje !== "") {

        // --- Manejo de la imagen ---
        $rutaImagen = null;

        if (!empty($_FILES['imagen']['name'])) {

            $directorio = "uploads/opiniones/" . preg_replace('/[^a-zA-Z0-9_-]/', '_', $nombre);

            if (!is_dir($directorio)) {
                mkdir($directorio, 0777, true);
            }

            $nombreArchivo = time() . "_" . basename($_FILES['imagen']['name']);
            $rutaDestino = $directorio . "/" . $nombreArchivo;

            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
                $rutaImagen = $rutaDestino;
            }
        }

        // --- Guardar en la base de datos ---
        $stmt = $pdo->prepare("
            INSERT INTO opiniones_usuarios (nombre, email, imagen, mensaje)
            VALUES (:nombre, :email, :imagen, :mensaje)
        ");

        $stmt->execute([
            ':nombre' => $nombre,
            ':email'  => $email,
            ':imagen' => $rutaImagen,
            ':mensaje' => $mensaje
        ]);

        $mensaje_exito = "¡Gracias por tu opinión!";
    }
}
?>

<main class="layout-home">

    <section class="destacados">

        <article class="destacado-block">
            <h2 class="destacado-title">
                <i class="fa-solid fa-user-doctor-message"></i> Danos tu opinión
            </h2>

            <div class="destacado-content form-noemi">

                <?php if (!empty($mensaje_exito)): ?>
                    <div class="alert-success">
                        <?= $mensaje_exito ?>
                    </div>
                <?php endif; ?>

                <?php if ($intro_opinion): ?>

                    <div class="destacado-content">
                        <?= $intro_opinion['contenido'] ?>
                    </div>

                <?php endif; ?>

                <form action="" method="POST" enctype="multipart/form-data">

                    <div class="form-noemi-container">
                        <div class="form-group">
                            <label for="nombre">Tu nombre</label>
                            <input type="text" id="nombre" name="nombre" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Tu email</label>
                            <input type="email" id="email" name="email" required>
                        </div>

                        <div class="form-group">
                            <label for="asunto">Imagen</label>
                            <input type="file" id="imagen" name="imagen" required>
                        </div>

                        <div class="form-group">
                            <label for="mensaje">Mensaje</label>
                            <textarea id="mensaje" name="mensaje" rows="6" required></textarea>
                        </div>

                        <button type="submit" name="enviar" class="btn">
                            Enviar
                        </button>
                    </div>

                </form>

            </div>

        </article>

    </section>

</main>