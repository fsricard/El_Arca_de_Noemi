<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/funciones.php';

// Si no está logueado, redirigimos al login
if (!isLoggedIn()) {
    header("Location: index.php");
    exit;
}

$errores = [];
$exito = false;

// Obtener ID del padrino
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: apadrina_listado_padrinos.php");
    exit;
}

// Obtener datos del padrino
$stmt = $pdo->prepare("SELECT * FROM sponsors WHERE id = ?");
$stmt->execute([$id]);
$padrino = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$padrino) {
    header("Location: apadrina_listado_padrinos.php");
    exit;
}

/* ---------------------------------------------------------
   PROCESAR FORMULARIO DE EDICIÓN
--------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre_apellidos = trim($_POST['nombre_apellidos'] ?? '');
    $email            = trim($_POST['email'] ?? '');
    $telefono         = trim($_POST['telefono'] ?? '');
    $direccion        = trim($_POST['direccion'] ?? '');
    $ciudad           = trim($_POST['ciudad'] ?? '');
    $provincia        = trim($_POST['provincia'] ?? '');
    $codigo_postal    = trim($_POST['codigo_postal'] ?? '');
    $pais             = trim($_POST['pais'] ?? '');
    $mensaje          = trim($_POST['mensaje'] ?? '');

    // Validaciones básicas
    if ($nombre_apellidos === '') {
        $errores[] = 'El nombre y apellidos son obligatorios.';
    }

    if ($email === '') {
        $errores[] = 'El email es obligatorio.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'El email no tiene un formato válido.';
    }

    if (empty($errores)) {
        try {
            $stmt = $pdo->prepare("
                UPDATE sponsors
                SET nombre_apellidos = :nombre_apellidos,
                    email = :email,
                    telefono = :telefono,
                    direccion = :direccion,
                    ciudad = :ciudad,
                    provincia = :provincia,
                    codigo_postal = :codigo_postal,
                    pais = :pais,
                    mensaje = :mensaje,
                    updated_at = NOW()
                WHERE id = :id
            ");

            $stmt->execute([
                ':nombre_apellidos' => $nombre_apellidos,
                ':email'            => $email,
                ':telefono'         => $telefono ?: null,
                ':direccion'        => $direccion ?: null,
                ':ciudad'           => $ciudad ?: null,
                ':provincia'        => $provincia ?: null,
                ':codigo_postal'    => $codigo_postal ?: null,
                ':pais'             => $pais ?: null,
                ':mensaje'          => $mensaje ?: null,
                ':id'               => $id
            ]);

            $exito = true;

            // Refrescar datos
            $stmt = $pdo->prepare("SELECT * FROM sponsors WHERE id = ?");
            $stmt->execute([$id]);
            $padrino = $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $errores[] = 'Error al guardar los datos: ' . $e->getMessage();
        }
    }
}

/* ---------------------------------------------------------
   OBTENER APADRINAMIENTOS DEL PADRINO
--------------------------------------------------------- */
$stmt = $pdo->prepare("
    SELECT sa.*,
           a.nombre AS nombre_animal,
           a.foto_principal AS foto_animal,
           a.id AS id_animal
    FROM sponsors_animals sa
    LEFT JOIN animals_sponsor a ON sa.animal_id = a.id
    WHERE sa.sponsor_id = ?
    ORDER BY sa.fecha_inicio DESC
");
$stmt->execute([$id]);
$relaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pagina = 'apadrina_editar_padrino';

include('../includes/header.php');
?>

<main>
    <section>
        <div class="container">
            <h2>Editar padrino</h2>

            <?php if ($exito): ?>
                <div class="alert alert-success">Cambios guardados correctamente.</div>
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

            <form method="post" class="formulario">

                <div class="mb-3">
                    <label>Nombre y apellidos *</label>
                    <input type="text" name="nombre_apellidos" class="form-control"
                           value="<?= htmlspecialchars($padrino['nombre_apellidos'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label>Email *</label>
                    <input type="email" name="email" class="form-control"
                           value="<?= htmlspecialchars($padrino['email'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label>Teléfono</label>
                    <input type="text" name="telefono" class="form-control"
                           value="<?= htmlspecialchars($padrino['telefono'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label>Dirección</label>
                    <input type="text" name="direccion" class="form-control"
                           value="<?= htmlspecialchars($padrino['direccion'] ?? '') ?>">
                </div>

                <div class="mb-3" style="display:flex; gap:10px;">
                    <div style="flex:1;">
                        <label>Ciudad</label>
                        <input type="text" name="ciudad" class="form-control"
                               value="<?= htmlspecialchars($padrino['ciudad'] ?? '') ?>">
                    </div>
                    <div style="flex:1;">
                        <label>Provincia</label>
                        <input type="text" name="provincia" class="form-control"
                               value="<?= htmlspecialchars($padrino['provincia'] ?? '') ?>">
                    </div>
                    <div style="width:120px;">
                        <label>C.P.</label>
                        <input type="text" name="codigo_postal" class="form-control"
                               value="<?= htmlspecialchars($padrino['codigo_postal'] ?? '') ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label>País</label>
                    <input type="text" name="pais" class="form-control"
                           value="<?= htmlspecialchars($padrino['pais'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label>Mensaje</label>
                    <textarea name="mensaje" class="form-control" rows="4"><?= htmlspecialchars($padrino['mensaje'] ?? '') ?></textarea>
                </div>

                <div class="mb-3">
                    <label>Fecha de registro</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($padrino['fecha_registro'] ?? '') ?>" disabled>
                </div>

                <div style="display:flex; gap:10px; align-items:center; margin-top:12px;">
                    <button class="btn btn-primary">Guardar cambios</button>
                    <a href="apadrina_listado_padrinos.php" class="btn">Volver</a>

                    <!-- Exportar CSV -->
                    <button type="button" class="btn btn-outline-secondary" id="btnExportarCSV">
                        <i class="fa-solid fa-file-csv"></i> Exportar CSV
                    </button>

                    <!-- Eliminar padrino -->
                    <button type="button" class="btn btn-danger" id="btnEliminarPadrino">
                        <i class="fa-solid fa-trash"></i> Eliminar padrino
                    </button>
                </div>
            </form>

            <hr>

            <h3>Apadrinamientos de <?= htmlspecialchars($padrino['nombre_apellidos']) ?></h3>

            <?php if (empty($relaciones)): ?>
                <p class="texto-secundario">Este padrino no tiene apadrinamientos registrados.</p>
            <?php else: ?>

                <table class="tabla">
                    <thead>
                        <tr>
                            <th>Animal</th>
                            <th>Inicio</th>
                            <th>Cancelación</th>
                            <th>Estado</th>
                            <th>Paypal</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($relaciones as $r): ?>
                            <tr>
                                <td>
                                    <?php if ($r['id_animal']): ?>
                                        <strong><?= htmlspecialchars($r['nombre_animal'] ?: '—') ?></strong><br>
                                        <small class="texto-secundario">ID <?= (int)$r['id_animal'] ?></small>
                                    <?php else: ?>
                                        <span class="texto-secundario">Animal eliminado (ID <?= (int)$r['animal_id'] ?>)</span>
                                    <?php endif; ?>
                                </td>

                                <td><?= htmlspecialchars($r['fecha_inicio']) ?></td>
                                <td><?= $r['fecha_cancelacion'] ? htmlspecialchars($r['fecha_cancelacion']) : '<span class="texto-secundario">—</span>' ?></td>

                                <td>
                                    <?php if ($r['estado'] === 'activo'): ?>
                                        <span class="badge badge-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Cancelado</span>
                                    <?php endif; ?>
                                </td>

                                <td><?= htmlspecialchars($r['paypal_subscription_id'] ?? '-') ?></td>

                                <td>
                                    <button class="btn btn-sm btn-outline-primary"
                                        onclick="window.location='apadrina_editar_relacion.php?id=<?= (int)$r['id'] ?>'">
                                        <i class="fa-solid fa-pen"></i> Editar relación
                                    </button>

                                    <?php if ($r['id_animal']): ?>
                                        <button class="btn btn-sm"
                                            onclick="window.location='apadrina_editar_animal.php?id=<?= (int)$r['id_animal'] ?>'">
                                            <i class="fa-solid fa-paw"></i> Ver animal
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            <?php endif; ?>

        </div>
    </section>
</main>

<script>
    // Script para las confirmaciones
    document.addEventListener("DOMContentLoaded", function() {

        const idPadrino = <?= (int)$id ?>;

        // Exportar CSV
        document.getElementById("btnExportarCSV").addEventListener("click", function() {
            if (!confirm("¿Deseas descargar los datos de este padrino en formato CSV?")) return;
            // Redirigimos a endpoint que fuerza la descarga
            window.location = "ajax/apadrina_exportar_padrino.php?id=" + idPadrino;
        });

        // Eliminar padrino (seguro)
        document.getElementById("btnEliminarPadrino").addEventListener("click", function() {
            if (!confirm("Eliminar padrino: esta acción archivará sus datos y borrará el registro. ¿Continuar?")) return;

            fetch("ajax/apadrina_eliminar_padrino.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ id: idPadrino })
            })
            .then(res => res.json())
            .then(data => {
                if (data.ok) {
                    alert("Padrino eliminado y archivado correctamente.");
                    window.location = "apadrina_listado_padrinos.php";
                } else {
                    alert("No se pudo eliminar: " + (data.error || "Error desconocido"));
                }
            })
            .catch(() => alert("Error de comunicación con el servidor."));
        });

    });
</script>

<?php include('../includes/footer.php');