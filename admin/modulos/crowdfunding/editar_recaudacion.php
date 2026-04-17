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

$pagina = 'editar_recaudacion';
$mensaje = "";
$tipo_mensaje = "";

// Validar ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID inválido.");
}

$id = (int)$_GET['id'];

// Obtener plataformas
$stmt_plat = $pdo->query("SELECT id, nombre, logo FROM crowdfunding_plataformas WHERE activo = 1 ORDER BY nombre ASC");
$plataformas = $stmt_plat->fetchAll(PDO::FETCH_ASSOC);

// Obtener datos actuales de la recaudación
$stmt = $pdo->prepare("SELECT * FROM crowdfunding_recaudaciones WHERE id = :id LIMIT 1");
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$recaudacion = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$recaudacion) {
    die("Recaudación no encontrada.");
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $plataforma_id = $_POST['plataforma_id'];
    $cantidad_objetivo = $_POST['cantidad_objetivo'];
    $moneda = $_POST['moneda'];
    $cantidad_recaudada = !empty($_POST['cantidad_recaudada']) ? $_POST['cantidad_recaudada'] : null;
    $descripcion = $_POST['descripcion'] ?? "";
    $activa = isset($_POST['activa']) ? 1 : 0;

    if (empty($plataforma_id) || empty($cantidad_objetivo) || empty($moneda)) {
        $mensaje = "Debes completar todos los campos obligatorios.";
        $tipo_mensaje = "error";
    } else {
        try {
            $sql = "UPDATE crowdfunding_recaudaciones SET
                        plataforma_id = :plataforma_id,
                        cantidad_objetivo = :cantidad_objetivo,
                        moneda = :moneda,
                        cantidad_recaudada = :cantidad_recaudada,
                        descripcion = :descripcion,
                        activa = :activa,
                        updated_at = NOW()
                    WHERE id = :id";

            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(':plataforma_id', $plataforma_id, PDO::PARAM_INT);
            $stmt->bindParam(':cantidad_objetivo', $cantidad_objetivo);
            $stmt->bindParam(':moneda', $moneda);
            $stmt->bindParam(':cantidad_recaudada', $cantidad_recaudada);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':activa', $activa, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $mensaje = "Recaudación actualizada correctamente.";
                $tipo_mensaje = "exito";

                // Recargar datos actualizados
                $stmt = $pdo->prepare("SELECT * FROM crowdfunding_recaudaciones WHERE id = :id LIMIT 1");
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $recaudacion = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $mensaje = "Error al actualizar la recaudación.";
                $tipo_mensaje = "error";
            }
        } catch (PDOException $e) {
            $mensaje = "Error en la base de datos: " . $e->getMessage();
            $tipo_mensaje = "error";
        }
    }
}

include('../../includes/header.php');
?>

<main>
    <section>
        <div class="container">
            <h2>Editar recaudación</h2>

            <?php if (!empty($mensaje)): ?>
                <div class="alerta <?= $tipo_mensaje ?>">
                    <?= $mensaje ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" class="formulario">

                <!-- Plataforma -->
                <div class="campo">
                    <label for="plataforma_id">Plataforma:</label>
                    <select name="plataforma_id" id="plataforma_id" required>
                        <option value="">Selecciona una plataforma</option>
                        <?php foreach ($plataformas as $p): ?>
                            <option value="<?= $p['id'] ?>"
                                data-logo="<?= $p['logo'] ?>"
                                <?= ($recaudacion['plataforma_id'] == $p['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Vista previa del logo -->
                <div class="campo">
                    <label>Logo:</label>
                    <img id="preview_logo"
                        src="../../../<?= obtenerLogoPlataforma($plataformas, $recaudacion['plataforma_id']) ?>?v=<?= time() ?>"
                        alt="Logo"
                        style="max-width:150px; border:1px solid #ccc; padding:5px;">
                </div>

                <!-- Cantidad objetivo -->
                <div class="campo">
                    <label for="cantidad_objetivo">Cantidad objetivo:</label>
                    <input type="number" step="0.01" name="cantidad_objetivo" id="cantidad_objetivo"
                        value="<?= $recaudacion['cantidad_objetivo'] ?>" required>
                </div>

                <!-- Moneda -->
                <div class="campo">
                    <label for="moneda">Moneda:</label>
                    <select name="moneda" id="moneda" required>
                        <option value="EUR" <?= $recaudacion['moneda'] === 'EUR' ? 'selected' : '' ?>>Euros (€)</option>
                        <option value="USD" <?= $recaudacion['moneda'] === 'USD' ? 'selected' : '' ?>>Dólares ($)</option>
                    </select>
                </div>

                <!-- Cantidad recaudada -->
                <div class="campo">
                    <label for="cantidad_recaudada">Cantidad recaudada:</label>
                    <input type="number" step="0.01" name="cantidad_recaudada" id="cantidad_recaudada"
                        value="<?= $recaudacion['cantidad_recaudada'] ?>">
                </div>

                <!-- Descripción -->
                <div class="campo">
                    <label>Descripción:</label>

                    <!-- Editor visual de Quill -->
                    <?= editor_quill('descripcion', $recaudacion['descripcion'] ?? '') ?>

                </div>

                <!-- Activa -->
                <div class="campo">
                    <label>
                        <input type="checkbox" name="activa" <?= $recaudacion['activa'] ? 'checked' : '' ?>>
                        Recaudación activa
                    </label>
                </div>

                <div class="campo">
                    <button type="submit" class="btn-primario" id="btn-guardar">Guardar cambios</button>
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
        }
    });
</script>

<?php include('../../includes/footer.php');