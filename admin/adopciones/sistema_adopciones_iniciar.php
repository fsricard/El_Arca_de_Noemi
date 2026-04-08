<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../../config/database.php';
require_once(__DIR__ . '/../../config/funciones.php');

// Si no está logueado, redirigimos al login
if (!isLoggedIn()) {
    header("Location: index.php");
    exit;
}

$errores = [];
$exito = false;

/* ---------------------------------------------------------
   OBTENER ANIMALES ADOPTABLES
--------------------------------------------------------- */
$animales = $pdo->query("
    SELECT 
        a.id,
        a.nombre,
        e.nombre AS especie,
        r.nombre AS raza
    FROM animales a
    INNER JOIN razas_animales r ON a.id_raza = r.id
    INNER JOIN especies_animales e ON r.especie_id = e.id
    WHERE a.adoptable = 1
    ORDER BY e.nombre, r.nombre, a.nombre
")->fetchAll(PDO::FETCH_ASSOC);

/* ---------------------------------------------------------
   OBTENER ADOPTANTES
--------------------------------------------------------- */
$adoptantes = $pdo->query("
    SELECT id, nombre, apellidos
    FROM adoptantes
    ORDER BY nombre, apellidos
")->fetchAll(PDO::FETCH_ASSOC);

/* ---------------------------------------------------------
   PROCESAR FORMULARIO
--------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_animal = intval($_POST['id_animal'] ?? 0);
    $id_adoptante = intval($_POST['id_adoptante'] ?? 0);
    $fecha_adopcion = $_POST['fecha_adopcion'] ?? '';
    $notas = trim($_POST['notas'] ?? '');

    // Validaciones
    if ($id_animal <= 0) $errores[] = "Debes seleccionar un animal.";
    if ($id_adoptante <= 0) $errores[] = "Debes seleccionar un adoptante.";
    if ($fecha_adopcion === '') $errores[] = "La fecha de adopción es obligatoria.";

    if (empty($errores)) {
        try {

            /* ---------------------------------------------------------
               1. CREAR ADOPCIÓN
            --------------------------------------------------------- */
            $stmt = $pdo->prepare("
                INSERT INTO adopciones (id_animal, id_adoptante, fecha_adopcion, notas)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$id_animal, $id_adoptante, $fecha_adopcion, $notas]);

            $id_adopcion = $pdo->lastInsertId();

            /* ---------------------------------------------------------
               2. MARCAR ANIMAL COMO ADOPTADO
            --------------------------------------------------------- */
            $stmt = $pdo->prepare("
                UPDATE animales 
                SET adoptable = 0, id_adopcion = ?
                WHERE id = ?
            ");
            $stmt->execute([$id_adopcion, $id_animal]);

            $exito = true;

        } catch (PDOException $e) {
            $errores[] = "Error al iniciar adopción: " . $e->getMessage();
        }
    }
}

$pagina='sistema_adopciones_iniciar';

include('../includes/header.php');
?>

    <main>
        <section>
            <div class="container">
                <h2>Vas a iniciar el proceso de un nueva adopción</h2>

                <?php if ($exito): ?>
                    <p class="exito"><i class="fa-regular fa-check-double"></i> Adopción iniciada correctamente.</p>
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

                <form method="post" class="formulario">

                    <div class="filtro">
                        <label for="id_animal">Animal:</label>
                        <select name="id_animal" id="id_animal">
                            <option value="">Selecciona un animal</option>
                            <?php foreach ($animales as $a): ?>
                                <option value="<?= $a['id'] ?>"
                                    <?= (($_POST['id_animal'] ?? '') == $a['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($a['nombre']) ?> 
                                    (<?= htmlspecialchars($a['especie']) ?> - <?= htmlspecialchars($a['raza']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filtro">
                        <label for="id_adoptante">Adoptante:</label>
                        <select name="id_adoptante" id="id_adoptante">
                            <option value="">Selecciona un adoptante</option>
                            <?php foreach ($adoptantes as $ad): ?>
                                <option value="<?= $ad['id'] ?>"
                                    <?= (($_POST['id_adoptante'] ?? '') == $ad['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($ad['nombre'] . ' ' . $ad['apellidos']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filtro">
                        <label for="fecha_adopcion">Fecha de inicio:</label>
                        <input type="date" name="fecha_adopcion" id="fecha_adopcion"
                            value="<?= htmlspecialchars($_POST['fecha_adopcion'] ?? date('Y-m-d')) ?>">
                    </div>

                    <label for="notas">Notas iniciales:</label>
                    <textarea name="notas" id="notas" rows="4"><?= htmlspecialchars($_POST['notas'] ?? '') ?></textarea>

                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-paw"></i> Iniciar adopción
                    </button>

                </form>
            </div>
        </section>
    </main>

<?php include('../includes/footer.php');