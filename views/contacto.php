<?php
date_default_timezone_set('Europe/Madrid');

require_once(__DIR__ . '/../config/envLoader.php');
cargarEnv(__DIR__ . '/../config/.env');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mensaje_ok = null;
$mensaje_error = null;

if (isset($_POST['enviar'])) {

    $nombre  = trim($_POST['nombre']);
    $email   = trim($_POST['email']);
    $asunto  = trim($_POST['asunto']);
    $mensaje = trim($_POST['mensaje']);

    if ($nombre !== "" && $email !== "" && $asunto !== "" && $mensaje !== "") {

        try {
            // Guardar en la base de datos
            $stmt = $pdo->prepare("
                INSERT INTO mensajes_contacto (nombre, email, asunto, mensaje)
                VALUES (:nombre, :email, :asunto, :mensaje)
            ");

            $stmt->execute([
                ':nombre'  => $nombre,
                ':email'   => $email,
                ':asunto'  => $asunto,
                ':mensaje' => $mensaje
            ]);

            // Preparar datos para el email
            $fecha_envio = date('d/m/Y H:i');

            // Incluir PHPMailer (RUTAS CORRECTAS)
            require_once(__DIR__ . '/../includes/PHPMailer/PHPMailer.php');
            require_once(__DIR__ . '/../includes/PHPMailer/SMTP.php');
            require_once(__DIR__ . '/../includes/PHPMailer/Exception.php');

            // Crear instancia
            $mail = new PHPMailer(true);

            // Configurar PHPMailer
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';
            $mail->isSMTP();
            $mail->Host       = getenv('SMTP_HOST');
            $mail->SMTPAuth   = true;
            $mail->Username   = getenv('SMTP_USER_CONTACTO');
            $mail->Password   = getenv('SMTP_PASS_CONTACTO');
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = getenv('SMTP_PORT');

            // Opciones SSL para Laragon
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];

            // Remitente y destinatario
            $mail->setFrom(getenv('SMTP_USER_CONTACTO'), 'El Arca de Noemí');
            $mail->addAddress(getenv('SMTP_USER_CONTACTO'), 'Noemí');

            $mail->isHTML(true);
            $mail->Subject = 'Nuevo mensaje de contacto: ' . $asunto;

            // Incrustar imágenes CID (RUTAS CORRECTAS)
            $mail->addEmbeddedImage(__DIR__ . '/../img/header_20260320_0003.png', 'cid_header');
            $mail->addEmbeddedImage(__DIR__ . '/../img/logo_20260320_0002.png', 'cid_logo');

            // Renderizar plantilla (RUTA CORRECTA)
            ob_start();
            include __DIR__ . '/../includes/plantillas_email/contacto/plantilla_1.php';
            $mail->Body = ob_get_clean();

            // Enviar correo
            $mail->send();

            $mensaje_ok = "Tu mensaje ha sido enviado correctamente. Noemi lo revisará pronto y te contestará en breve.";
        } catch (Exception $e) {
            $mensaje_error = "!!Algo salió mal¡¡. Inténtalo más tarde.";
            // Para depurar:
            // echo $e->getMessage();
        }
    } else {
        $mensaje_error = "Todos los campos son obligatorios.";
    }
}
?>

<main class="layout-home">

    <section class="destacados">

        <article class="destacado-block">
            <h2 class="destacado-title">
                <i class="fa-solid fa-fire-flame-curved"></i> Contacta con Noemi
            </h2>

            <?php
            $stmt = $pdo->query("
                    SELECT contenido, actualizado
                    FROM intro_contacto
                    ORDER BY id DESC
                    LIMIT 1
                ");

            $intro_contacto = $stmt->fetch(PDO::FETCH_ASSOC);
            ?>

            <div class="destacado-content form-noemi">

                <?php
                if ($intro_contacto):
                ?>

                    <div class="destacado-content">
                        <?= $intro_contacto['contenido'] ?>
                    </div>

                <?php endif; ?>

                <form action="" method="POST">

                    <?php if (isset($mensaje_ok)): ?>
                        <div class="alert alert-ok">
                            <?= $mensaje_ok ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($mensaje_error)): ?>
                        <div class="alert alert-error">
                            <?= $mensaje_error ?>
                        </div>
                    <?php endif; ?>


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
                            <label for="asunto">Asunto</label>
                            <input type="text" id="asunto" name="asunto" required>
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