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

// Obtener plataformas activas
try {
    $stmt = $pdo->query("SELECT id, nombre, logo FROM crowdfunding_plataformas WHERE activo = 1 ORDER BY nombre ASC");
    $plataformas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $plataformas = [];
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $plataforma_id = $_POST['plataforma_id'];
    $cantidad_objetivo = $_POST['cantidad_objetivo'];
    $moneda = $_POST['moneda'];
    $cantidad_recaudada = !empty($_POST['cantidad_recaudada']) ? $_POST['cantidad_recaudada'] : null;
    $descripcion = $_POST['descripcion'] ?? "";
    $activa = isset($_POST['activa']) ? 1 : 0;
    $enlace = trim($_POST['enlace']);

    if (empty($plataforma_id) || empty($cantidad_objetivo) || empty($moneda) || empty($enlace)) {
        $mensaje = "Debes completar todos los campos obligatorios.";
        $tipo_mensaje = "error";
    } else {
        try {
            $sql = "INSERT INTO crowdfunding_recaudaciones 
                    (plataforma_id, cantidad_objetivo, moneda, cantidad_recaudada, enlace, descripcion, activa)
                    VALUES (:plataforma_id, :cantidad_objetivo, :moneda, :cantidad_recaudada, :enlace, :descripcion, :activa)";

            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(':plataforma_id', $plataforma_id, PDO::PARAM_INT);
            $stmt->bindParam(':cantidad_objetivo', $cantidad_objetivo);
            $stmt->bindParam(':moneda', $moneda);
            $stmt->bindParam(':cantidad_recaudada', $cantidad_recaudada);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':activa', $activa, PDO::PARAM_INT);
            $stmt->bindParam(':enlace', $enlace);

            if ($stmt->execute()) {
                $mensaje = "Recaudación creada correctamente.";
                $tipo_mensaje = "exito";
            } else {
                $mensaje = "Error al guardar la recaudación.";
                $tipo_mensaje = "error";
            }
        } catch (PDOException $e) {
            $mensaje = "Error en la base de datos: " . $e->getMessage();
            $tipo_mensaje = "error";
        }
    }
}

$pagina = 'crear_recaudacion';

include('../../includes/header.php');
?>

<main>
    <section>
        <div class="container">
            <h2>Crear nueva recaudación</h2>

            <?php if (!empty($mensaje)): ?>
                <div class="alerta <?= $tipo_mensaje ?>">
                    <?= $mensaje ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" class="formulario">

                <!-- Selección de plataforma -->
                <div class="campo">
                    <label for="plataforma_id">Plataforma:</label>
                    <select name="plataforma_id" id="plataforma_id" required>
                        <option value="">Selecciona una plataforma</option>
                        <?php foreach ($plataformas as $p): ?>
                            <option value="<?= $p['id'] ?>" data-logo="<?= $p['logo'] ?>">
                                <?= htmlspecialchars($p['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Vista previa del logo -->
                <div class="campo">
                    <label>Logo:</label>
                    <img id="preview_logo" src="" alt="Logo" style="max-width:150px; display:none; border:1px solid #ccc; padding:5px;">
                </div>

                <!-- Cantidad objetivo -->
                <div class="campo">
                    <label for="cantidad_objetivo">Cantidad objetivo:</label>
                    <input type="number" step="0.01" name="cantidad_objetivo" id="cantidad_objetivo" required>
                </div>

                <!-- Moneda -->
                <div class="campo">
                    <label for="moneda">Moneda:</label>
                    <select name="moneda" id="moneda" required>
                        <option value="EUR">Euros (€)</option>
                        <option value="USD">Dólares ($)</option>
                    </select>
                </div>

                <!-- Cantidad recaudada (opcional) -->
                <div class="campo">
                    <label for="cantidad_recaudada">Cantidad recaudada (opcional):</label>
                    <input type="number" step="0.01" name="cantidad_recaudada" id="cantidad_recaudada">
                </div>

                <!-- Enlace a la campaña -->
                <div class="campo">
                    <label for="enlace">Enlace a la campaña:</label>
                    <input type="url" name="enlace" id="enlace" placeholder="https://..." required>
                </div>

                <!-- Descripción con Quill -->
                <div class="campo">
                    <label>Descripción:</label>

                    <!-- Editor visual de Quill -->
                    <?= editor_quill('descripcion', $_POST['descripcion'] ?? '') ?>

                </div>

                <!-- Activa / Inactiva -->
                <div class="campo">
                    <label>
                        <input type="checkbox" name="activa" checked>
                        Recaudación activa
                    </label>
                </div>

                <div class="campo">
                    <button type="submit" class="btn-primario" id="btn-guardar">Crear recaudación</button>
                </div>

            </form>

        </div>
    </section>
</main>

<script>
    // Script para vista previa del logo
    document.getElementById('plataforma_id').addEventListener('change', function() {
        const logo = this.options[this.selectedIndex].dataset.logo;
        const img = document.getElementById('preview_logo');

        if (logo) {
            img.src = "../../../" + logo + "?v=" + Date.now();
            img.style.display = "block";
        } else {
            img.style.display = "none";
        }
    });
</script>

<?php include('../../includes/footer.php');
