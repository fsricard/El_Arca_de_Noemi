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

/* ---------------------------------------------------------
   1. Obtener ID del animal
--------------------------------------------------------- */
$id_animal = intval($_GET['id_animal'] ?? 0);
if ($id_animal <= 0) {
    die("ID de animal no válido.");
}

/* ---------------------------------------------------------
   2. Obtener datos del animal
--------------------------------------------------------- */
$stmt = $pdo->prepare("
    SELECT a.*, r.especie, r.nombre AS raza
    FROM animales a
    INNER JOIN razas_animales r ON a.id_raza = r.id
    WHERE a.id = ?
");
$stmt->execute([$id_animal]);
$animal = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$animal) {
    die("Animal no encontrado.");
}

if ($animal['id_adopcion']) {
    die("Este animal ya tiene una adopción activa.");
}

$errores = [];
$exito = false;

/* ---------------------------------------------------------
   3. Procesar formulario
--------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_adoptante   = intval($_POST['id_adoptante'] ?? 0);
    $fecha_adopcion = $_POST['fecha_adopcion'] ?? '';
    $notas          = trim($_POST['notas'] ?? '');

    if ($id_adoptante <= 0) {
        $errores[] = "Debes seleccionar un adoptante válido.";
    }

    if ($fecha_adopcion === '') {
        $errores[] = "La fecha de adopción es obligatoria.";
    }

    if (empty($errores)) {
        try {
            $pdo->beginTransaction();

            /* Crear adopción */
            $stmt = $pdo->prepare("
                INSERT INTO adopciones (id_animal, id_adoptante, fecha_adopcion, estado, notas)
                VALUES (?, ?, ?, 'pendiente', ?)
            ");
            $stmt->execute([$id_animal, $id_adoptante, $fecha_adopcion, $notas]);

            $id_adopcion = $pdo->lastInsertId();

            /* Actualizar animal */
            $stmt = $pdo->prepare("
                UPDATE animales
                SET adoptable = 0, id_adopcion = ?
                WHERE id = ?
            ");
            $stmt->execute([$id_adopcion, $id_animal]);

            $pdo->commit();

            $exito = true;

            /* Redirigir a editar adopción */
            header("Location: sistema_adopciones_editar_adoptantes.php?id=" . $id_adopcion);
            exit;

        } catch (PDOException $e) {
            $pdo->rollBack();
            $errores[] = "Error al crear la adopción: " . $e->getMessage();
        }
    }
}

$pagina='sistema_adopciones_crear';

include('../includes/header.php');
?>

    <main>
        <section>
            <div class="container">

                <h2>Crear una nueva adopción para este animal</h2>

                <?php if (!empty($errores)): ?>
                    <div class="errores">
                        <ul>
                            <?php foreach ($errores as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- DATOS DEL ANIMAL -->
                <div class="container-info">
                    <div class="bloque-info">
                        <h3>Animal</h3>
                        <p>
                            <strong><?= htmlspecialchars($animal['nombre']) ?></strong><br>
                            <?= htmlspecialchars($animal['especie']) ?> - <?= htmlspecialchars($animal['raza']) ?><br>
                            Ingreso: <?= htmlspecialchars($animal['fecha_ingreso']) ?>
                        </p>
                    </div>
                </div>

                <!-- FORMULARIO -->
                <form method="post" class="formulario">

                    <!-- AUTOCOMPLETE ADOPTANTE -->
                    <div class="autocomplete-wrapper">
                        <label>Adoptante:</label>

                        <input type="text"
                            id="buscador"
                            name="buscador"
                            autocomplete="off"
                            placeholder="Escribe nombre o apellidos...">

                        <input type="hidden"
                            id="id_adoptante"
                            name="id_adoptante">

                        <div id="sugerencias" class="autocomplete-list"></div>
                    </div>

                    <!-- FECHA -->
                    <div class="filtro">
                        <label for="fecha_adopcion">Fecha de adopción:</label>
                        <input type="date" name="fecha_adopcion" id="fecha_adopcion" required>
                    </div>

                    <!-- NOTAS -->
                    <label for="notas">Notas:</label>
                    <textarea name="notas" id="notas" rows="5"></textarea>

                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-heart"></i> Crear adopción
                    </button>

                    <button type="button"
                            onclick="window.location='sistema_adopciones_listado.php'">
                        <i class="fa-solid fa-arrow-left"></i> Volver
                    </button>

                </form>

            </div>
        </section>
    </main>

<style>
    .autocomplete-wrapper {
        position: relative;
        margin-bottom: 20px;
    }
    .autocomplete-list {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 6px;
        max-height: 200px;
        overflow-y: auto;
        z-index: 50;
    }
    .item-sugerencia {
        padding: 8px 10px;
        cursor: pointer;
    }
    .item-sugerencia:hover {
        background: #f0f0f0;
    }
</style>

<script>
    // Script para el autocomplete de adoptantes
    document.addEventListener("DOMContentLoaded", () => {

        const input = document.getElementById("buscador");
        const inputID = document.getElementById("id_adoptante");
        const lista = document.getElementById("sugerencias");

        let timeout = null;

        input.addEventListener("input", function () {
            const texto = this.value.trim();

            clearTimeout(timeout);

            if (texto.length < 2) {
                lista.innerHTML = "";
                return;
            }

            timeout = setTimeout(() => {
                fetch("ajax/buscar_adoptantes.php?q=" + encodeURIComponent(texto))
                    .then(res => res.json())
                    .then(data => {
                        lista.innerHTML = "";

                        data.forEach(item => {
                            const div = document.createElement("div");
                            div.classList.add("item-sugerencia");
                            div.textContent = item.nombre_completo;
                            div.dataset.id = item.id;

                            div.addEventListener("click", () => {
                                input.value = item.nombre_completo;
                                inputID.value = item.id;
                                lista.innerHTML = "";
                            });

                            lista.appendChild(div);
                        });
                    });
            }, 200);
        });

    });
</script>

<?php include('../includes/footer.php');