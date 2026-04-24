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

// ID de adopción
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    die("ID de adopción no válido.");
}

/* ---------------------------------------------------------
   OBTENER DATOS DE LA ADOPCIÓN
--------------------------------------------------------- */
$stmt = $pdo->prepare("
    SELECT 
        adop.*,

        ani.id        AS id_animal,
        ani.nombre    AS nombre_animal,

        e.nombre      AS especie,
        r.nombre      AS raza,

        ado.id        AS id_adoptante,
        ado.nombre    AS nombre_adoptante,
        ado.apellidos AS apellidos_adoptante,
        ado.telefono,
        ado.email

    FROM adopciones adop
    INNER JOIN animales ani         ON adop.id_animal = ani.id
    INNER JOIN razas_animales r     ON ani.id_raza = r.id
    INNER JOIN especies_animales e  ON r.especie_id = e.id
    INNER JOIN adoptantes ado       ON adop.id_adoptante = ado.id
    WHERE adop.id = ?
");
$stmt->execute([$id]);
$adopcion = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$adopcion) {
    die("Adopción no encontrada.");
}

$errores = [];
$exito   = false;

/* ---------------------------------------------------------
   PROCESAR FORMULARIO
--------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $estado         = $_POST['estado'] ?? '';
    $fecha_adopcion = $_POST['fecha_adopcion'] ?? '';
    $notas          = trim($_POST['notas'] ?? '');

    $estados_validos = ['pendiente', 'en_proceso', 'finalizada', 'cancelada'];

    if (!in_array($estado, $estados_validos, true)) {
        $errores[] = "Estado de adopción no válido.";
    }

    if ($fecha_adopcion === '') {
        $errores[] = "La fecha de adopción es obligatoria.";
    }

    if (empty($errores)) {
        try {
            $pdo->beginTransaction();

            // Actualizar adopción
            $stmt = $pdo->prepare("
                UPDATE adopciones
                SET estado = ?, fecha_adopcion = ?, notas = ?
                WHERE id = ?
            ");
            $stmt->execute([$estado, $fecha_adopcion, $notas, $id]);

            // Actualizar estado del animal según la adopción
            if ($estado === 'cancelada') {
                // La adopción se cancela → el animal vuelve a estar adoptable
                $stmt = $pdo->prepare("
                    UPDATE animales
                    SET adoptable = 1, id_adopcion = NULL
                    WHERE id = ?
                ");
                $stmt->execute([$adopcion['id_animal']]);
            } else {
                // Cualquier otro estado → el animal queda bloqueado para adopción
                $stmt = $pdo->prepare("
                    UPDATE animales
                    SET adoptable = 0, id_adopcion = ?
                    WHERE id = ?
                ");
                $stmt->execute([$id, $adopcion['id_animal']]);
            }

            $pdo->commit();

            $exito = true;

            // Refrescar datos actualizados
            $stmt = $pdo->prepare("
                SELECT 
                    adop.*,
                    ani.id        AS id_animal,
                    ani.nombre    AS nombre_animal,
                    e.nombre      AS especie,
                    r.nombre      AS raza,
                    ado.id        AS id_adoptante,
                    ado.nombre    AS nombre_adoptante,
                    ado.apellidos AS apellidos_adoptante,
                    ado.telefono,
                    ado.email
                FROM adopciones adop
                INNER JOIN animales ani         ON adop.id_animal = ani.id
                INNER JOIN razas_animales r     ON ani.id_raza = r.id
                INNER JOIN especies_animales e  ON r.especie_id = e.id
                INNER JOIN adoptantes ado       ON adop.id_adoptante = ado.id
                WHERE adop.id = ?
            ");
            $stmt->execute([$id]);
            $adopcion = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $pdo->rollBack();
            $errores[] = "Error al actualizar la adopción: " . $e->getMessage();
        }
    }
}

$pagina = 'sistema_adopciones_editar_adopcion';

include('../../includes/header.php');
?>

<main>
    <section>
        <div class="container">
            <h2>Vas a editar el estado de una adopción</h2>

            <?php if ($exito): ?>
                <p class="exito">
                    <i class="fa-regular fa-check-double"></i>
                    Adopción actualizada correctamente.
                </p>
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

            <div class="container-info">
                <div class="bloque-info">
                    <h3>Datos del animal</h3>
                    <p>
                        <strong><?= htmlspecialchars($adopcion['nombre_animal']) ?></strong><br>
                        <?= htmlspecialchars($adopcion['especie']) ?> - <?= htmlspecialchars($adopcion['raza']) ?>
                    </p>
                </div>

                <div class="bloque-info">
                    <h3>Datos del adoptante</h3>
                    <p>
                        <strong><?= htmlspecialchars($adopcion['nombre_adoptante'] . ' ' . $adopcion['apellidos_adoptante']) ?></strong><br>
                        Tel: <?= htmlspecialchars($adopcion['telefono']) ?><br>
                        Email: <?= htmlspecialchars($adopcion['email']) ?>
                    </p>
                </div>
            </div>

            <form method="post" class="formulario">

                <div class="filtro">
                    <label for="fecha_adopcion">Fecha de adopción / inicio:</label>
                    <input type="date"
                        name="fecha_adopcion"
                        id="fecha_adopcion"
                        value="<?= htmlspecialchars($adopcion['fecha_adopcion']) ?>">
                </div>

                <div class="filtro">
                    <label for="estado">Estado de la adopción:</label>
                    <select name="estado" id="estado">
                        <option value="pendiente" <?= $adopcion['estado'] === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                        <option value="en_proceso" <?= $adopcion['estado'] === 'en_proceso' ? 'selected' : '' ?>>En proceso</option>
                        <option value="finalizada" <?= $adopcion['estado'] === 'finalizada' ? 'selected' : '' ?>>Finalizada</option>
                        <option value="cancelada" <?= $adopcion['estado'] === 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
                    </select>
                </div>

                <label for="notas">Notas / seguimiento:</label>
                <textarea name="notas" id="notas" rows="5"><?= htmlspecialchars($adopcion['notas'] ?? '') ?></textarea>

                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
                </button>

                <button type="button"
                    onclick="window.location='sistema_adopciones_por_adoptante.php?id=<?= $adopcion['id_adoptante'] ?>'">
                    <i class="fa-solid fa-paw"></i> Ver adopciones de este adoptante
                </button>

                <button type="button" class="btn btn-volver"
                    onclick="window.location='sistema_adopciones_listado_adoptantes.php'">
                    <i class="fa-solid fa-arrow-left"></i> Volver
                </button>

            </form>
        </div>
    </section>
</main>

<?php include('../../includes/footer.php');
