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

$mensaje = "";
$tipo_mensaje = "";

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre']);

    // Validación básica
    if (empty($nombre)) {
        $mensaje = "El nombre de la plataforma es obligatorio.";
        $tipo_mensaje = "error";

    } elseif (!isset($_FILES['logo']) || $_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
        $mensaje = "Debes subir un logotipo válido.";
        $tipo_mensaje = "error";

    } else {

        // Crear carpeta si no existe
        $carpeta = "../../../uploads/crowdfunding/" . limpiarNombreCarpeta($nombre) . "/";
        if (!file_exists($carpeta)) {
            mkdir($carpeta, 0777, true);
        }

        // Procesar imagen
        $extension = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        $nombre_archivo = "logo." . strtolower($extension);
        $ruta_destino = $carpeta . $nombre_archivo;

        if (move_uploaded_file($_FILES['logo']['tmp_name'], $ruta_destino)) {

            // Ruta relativa para la BD
            $ruta_bd = "uploads/crowdfunding/" . limpiarNombreCarpeta($nombre) . "/" . $nombre_archivo;

            try {
                // Insertar en BD con PDO
                $sql = "INSERT INTO crowdfunding_plataformas (nombre, logo, activo) 
                        VALUES (:nombre, :logo, 1)";
                $stmt = $pdo->prepare($sql);

                $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
                $stmt->bindParam(':logo', $ruta_bd, PDO::PARAM_STR);

                if ($stmt->execute()) {
                    $mensaje = "Plataforma añadida correctamente.";
                    $tipo_mensaje = "exito";
                } else {
                    $mensaje = "Error al guardar en la base de datos.";
                    $tipo_mensaje = "error";
                }

            } catch (PDOException $e) {
                $mensaje = "Error en la base de datos: " . $e->getMessage();
                $tipo_mensaje = "error";
            }

        } else {
            $mensaje = "No se pudo subir el archivo.";
            $tipo_mensaje = "error";
        }
    }
}

$pagina = 'incluir_plataforma_crowdfunding';

include('../../includes/header.php');
?>

<main>
    <section>
        <div class="container">
            <h2>Incluir una nueva plataforma de CrowdFunding</h2>

            <?php if (!empty($mensaje)): ?>
                <div class="alerta <?= $tipo_mensaje ?>">
                    <?= $mensaje ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data" class="formulario">

                <div class="campo">
                    <label for="nombre">Nombre de la plataforma:</label>
                    <input type="text" name="nombre" id="nombre" required>
                </div>

                <div class="campo">
                    <label for="logo">Logotipo de la plataforma:</label>
                    <input type="file" name="logo" id="logo" accept="image/*" required>
                </div>

                <div class="campo">
                    <button type="submit" class="btn-primario">Guardar plataforma</button>
                </div>

            </form>

        </div>
    </section>
</main>

<?php include('../../includes/footer.php');