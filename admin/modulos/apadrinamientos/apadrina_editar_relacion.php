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

/* ---------------------------------------------------------
   Obtener ID de la relación y datos básicos
--------------------------------------------------------- */
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: apadrina_listado_padrinos.php");
    exit;
}

/* Obtener relación con datos del padrino y del animal */
$stmt = $pdo->prepare("
    SELECT sa.*,
           s.id AS sponsor_id, s.nombre_apellidos AS sponsor_nombre, s.email AS sponsor_email,
           a.id AS animal_id, a.nombre AS animal_nombre, a.foto_principal AS animal_foto
    FROM sponsors_animals sa
    LEFT JOIN sponsors s ON sa.sponsor_id = s.id
    LEFT JOIN animals_sponsor a ON sa.animal_id = a.id
    WHERE sa.id = ?
    LIMIT 1
");
$stmt->execute([$id]);
$relacion = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$relacion) {
    header("Location: apadrina_listado_padrinos.php");
    exit;
}

/* ---------------------------------------------------------
   Función auxiliar: normalizar fechas de datetime-local
   Convierte:
     - "YYYY-MM-DDTHH:MM" -> "YYYY-MM-DD HH:MM:SS"
     - "YYYY-MM-DD" -> "YYYY-MM-DD 00:00:00"
--------------------------------------------------------- */
function normalizar_fecha_local($s) {
    $s = trim((string)$s);
    if ($s === '') return '';
    // Reemplazar T por espacio si existe
    $s = str_replace('T', ' ', $s);
    // Si viene sin segundos (YYYY-MM-DD HH:MM) añadir :00
    if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $s)) {
        $s .= ':00';
    }
    // Si viene solo fecha YYYY-MM-DD, convertir a datetime completo
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $s)) {
        $s .= ' 00:00:00';
    }
    return $s;
}

/* ---------------------------------------------------------
   Procesar formulario de edición
   Campos editables: estado, fecha_inicio, fecha_cancelacion, paypal_subscription_id, nota
--------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $estado_raw = $_POST['estado'] ?? 'activo';
    $estado = in_array($estado_raw, ['activo', 'cancelado']) ? $estado_raw : 'activo';

    $fecha_inicio_raw = trim($_POST['fecha_inicio'] ?? '');
    $fecha_cancelacion_raw = trim($_POST['fecha_cancelacion'] ?? '');
    $paypal_subscription_id_raw = trim($_POST['paypal_subscription_id'] ?? '');
    $nota_raw = trim($_POST['nota'] ?? '');

    // Normalizar fechas (acepta formatos con 'T' de datetime-local)
    $fecha_inicio = normalizar_fecha_local($fecha_inicio_raw);
    $fecha_cancelacion = normalizar_fecha_local($fecha_cancelacion_raw);

    // Sanitizar / truncar campos
    $paypal_subscription_id = $paypal_subscription_id_raw !== '' ? mb_substr($paypal_subscription_id_raw, 0, 255) : null;
    $nota = $nota_raw !== '' ? mb_substr($nota_raw, 0, 2000) : null;

    // Validaciones
    if ($fecha_inicio !== '' && !preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $fecha_inicio)) {
        $errores[] = "La fecha de inicio debe tener formato YYYY-MM-DD HH:MM:SS.";
    }

    if ($estado === 'cancelado' && $fecha_cancelacion === '') {
        $errores[] = "Si marcas la relación como cancelada debes indicar la fecha de cancelación.";
    }

    if ($fecha_cancelacion !== '' && !preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $fecha_cancelacion)) {
        $errores[] = "La fecha de cancelación debe tener formato YYYY-MM-DD HH:MM:SS.";
    }

    if (empty($errores)) {
        try {
            // Actualizamos sin tocar columnas que puedan no existir (p.ej. updated_at)
            $stmt = $pdo->prepare("
                UPDATE sponsors_animals
                SET estado = :estado,
                    fecha_inicio = :fecha_inicio,
                    fecha_cancelacion = :fecha_cancelacion,
                    paypal_subscription_id = :paypal_subscription_id,
                    nota = :nota
                WHERE id = :id
            ");

            $stmt->execute([
                ':estado' => $estado,
                ':fecha_inicio' => $fecha_inicio !== '' ? $fecha_inicio : null,
                ':fecha_cancelacion' => $fecha_cancelacion !== '' ? $fecha_cancelacion : null,
                ':paypal_subscription_id' => $paypal_subscription_id,
                ':nota' => $nota,
                ':id' => $id
            ]);

            $exito = true;

            // Refrescar datos de la relación
            $stmt = $pdo->prepare("
                SELECT sa.*,
                       s.id AS sponsor_id, s.nombre_apellidos AS sponsor_nombre, s.email AS sponsor_email,
                       a.id AS animal_id, a.nombre AS animal_nombre, a.foto_principal AS animal_foto
                FROM sponsors_animals sa
                LEFT JOIN sponsors s ON sa.sponsor_id = s.id
                LEFT JOIN animals_sponsor a ON sa.animal_id = a.id
                WHERE sa.id = ?
                LIMIT 1
            ");
            $stmt->execute([$id]);
            $relacion = $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $errores[] = "Error al guardar la relación: " . $e->getMessage();
        }
    }
}

$pagina = 'apadrina_editar_relacion';

include('../../includes/header.php');
?>

<main>
    <section>
        <div class="container">
            <h2>Editar relación de apadrinamiento</h2>

            <?php if ($exito): ?>
                <div class="alert alert-success">Relación actualizada correctamente.</div>
            <?php endif; ?>

            <?php if (!empty($errores)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errores as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="container-info">
                <div class="bloque-info">
                    <h4>Padrino</h4>
                    <p>
                        <strong><?= htmlspecialchars($relacion['sponsor_nombre'] ?? '—') ?></strong><br>
                        <small><?= htmlspecialchars($relacion['sponsor_email'] ?? '-') ?></small><br>
                        <small class="texto-secundario">ID <?= (int)($relacion['sponsor_id'] ?? 0) ?></small>
                    </p>
                    <p>
                        <a class="btn btn-success" href="apadrina_editar_padrino.php?id=<?= (int)($relacion['sponsor_id'] ?? 0) ?>">
                            <i class="fa-solid fa-pen"></i> Editar padrino
                        </a>
                    </p>
                </div>

                <div class="bloque-info">
                    <h4>Animal</h4>
                    <?php if ($relacion['animal_id']): ?>
                        <p>
                            <img src="<?= asset($relacion['animal_foto'] ?: 'img/sin_foto.png') ?>" class="thumb-animal" style="width:80px;height:80px;object-fit:cover;border-radius:6px;"><br>
                            <strong><?= htmlspecialchars($relacion['animal_nombre'] ?? '—') ?></strong><br>
                            <small class="texto-secundario">ID <?= (int)$relacion['animal_id'] ?></small>
                        </p>
                        <p>
                            <a class="btn btn-success" href="apadrina_editar_animal.php?id=<?= (int)$relacion['animal_id'] ?>">
                                <i class="fa-solid fa-paw"></i> Ver animal
                            </a>
                        </p>
                    <?php else: ?>
                        <p class="texto-secundario">Animal eliminado (ID <?= (int)$relacion['animal_id'] ?>)</p>
                    <?php endif; ?>
                </div>
            </div>

            <form method="post" class="formulario">

                <div class="mb-3">
                    <label>Estado</label>
                    <select name="estado" class="form-control">
                        <option value="activo" <?= ($relacion['estado'] === 'activo') ? 'selected' : '' ?>>Activo</option>
                        <option value="cancelado" <?= ($relacion['estado'] === 'cancelado') ? 'selected' : '' ?>>Cancelado</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Fecha inicio</label>
                    <input type="datetime-local" name="fecha_inicio" class="form-control"
                           value="<?= $relacion['fecha_inicio'] ? date('Y-m-d\TH:i', strtotime($relacion['fecha_inicio'])) : '' ?>">
                </div>

                <div class="mb-3">
                    <label>Fecha cancelación</label>
                    <input type="datetime-local" name="fecha_cancelacion" class="form-control"
                           value="<?= $relacion['fecha_cancelacion'] ? date('Y-m-d\TH:i', strtotime($relacion['fecha_cancelacion'])) : '' ?>">
                </div>

                <div class="mb-3">
                    <label>Paypal subscription id</label>
                    <input type="text" name="paypal_subscription_id" class="form-control"
                           value="<?= htmlspecialchars($relacion['paypal_subscription_id'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label>Nota interna</label>
                    <textarea name="nota" class="form-control" rows="4"><?= htmlspecialchars($relacion['nota'] ?? '') ?></textarea>
                </div>

                <div style="display:flex; gap:10px; align-items:center;">
                    <button class="btn btn-primary">Guardar cambios</button>

                    <a href="paypal_cancel_subscription.php?id=<?= $relacion['paypal_subscription_id'] ?>" class="btn btn-peligro">
                        Cancelar suscripción PayPal
                    </a>

                    <a href="apadrina_listado_padrinos.php" class="btn btn-volver">Volver al listado</a>
                </div>
            </form>

            <hr>

            <h3>Historial de la relación</h3>
            <table class="tabla">
                <thead>
                    <tr>
                        <th>Evento</th>
                        <th>Valor</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Creación</td>
                        <td>Estado inicial</td>
                        <td><?= htmlspecialchars($relacion['created_at'] ?? $relacion['fecha_inicio'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <td>Última actualización</td>
                        <td>—</td>
                        <td><?= htmlspecialchars($relacion['updated_at'] ?? '-') ?></td>
                    </tr>
                    <?php if (!empty($relacion['fecha_cancelacion'])): ?>
                        <tr>
                            <td>Cancelación</td>
                            <td><?= htmlspecialchars($relacion['estado']) ?></td>
                            <td><?= htmlspecialchars($relacion['fecha_cancelacion']) ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

        </div>
    </section>
</main>

<?php include('../../includes/footer.php');