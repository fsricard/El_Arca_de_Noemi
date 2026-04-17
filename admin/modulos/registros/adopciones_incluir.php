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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Datos recibidos
    $especie_id = intval($_POST['especie_id'] ?? 0);
    $especie_nueva = trim($_POST['especie_nueva'] ?? '');

    $raza_id = intval($_POST['raza_id'] ?? 0);
    $raza_nueva = trim($_POST['raza_nueva'] ?? '');

    $descripcion = trim($_POST['descripcion'] ?? '');

    // -------------------------
    // 1) Validar especie
    // -------------------------
    if ($especie_id === 0 && $especie_nueva === '') {
        $errores[] = "Debes seleccionar o crear una especie.";
    }

    // Si crea especie nueva, comprobar duplicado
    if ($especie_nueva !== '') {
        $stmt = $pdo->prepare("SELECT id FROM especies_animales WHERE nombre = ?");
        $stmt->execute([$especie_nueva]);

        if ($stmt->fetch()) {
            $errores[] = "La especie '{$especie_nueva}' ya existe.";
        }
    }

    // -------------------------
    // 2) Validar raza
    // -------------------------
    if ($raza_id === 0 && $raza_nueva === '') {
        $errores[] = "Debes seleccionar o crear una raza.";
    }

    // -------------------------
    // Si no hay errores, procesar
    // -------------------------
    if (empty($errores)) {

        try {
            $pdo->beginTransaction();

            // Crear especie si es nueva
            if ($especie_nueva !== '') {
                $stmt = $pdo->prepare("INSERT INTO especies_animales (nombre) VALUES (?)");
                $stmt->execute([$especie_nueva]);
                $especie_id = $pdo->lastInsertId();
            }

            // Crear raza si es nueva
            if ($raza_nueva !== '') {

                // Comprobar duplicado dentro de la especie
                $stmt = $pdo->prepare("SELECT id FROM razas_animales WHERE nombre = ? AND especie_id = ?");
                $stmt->execute([$raza_nueva, $especie_id]);

                if ($stmt->fetch()) {
                    throw new Exception("La raza '{$raza_nueva}' ya existe para esta especie.");
                }

                $stmt = $pdo->prepare("
                    INSERT INTO razas_animales (especie_id, nombre, descripcion)
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$especie_id, $raza_nueva, $descripcion]);

            } else {
                // Si seleccionó raza existente, actualizar descripción opcionalmente
                if ($descripcion !== '') {
                    $stmt = $pdo->prepare("
                        UPDATE razas_animales SET descripcion = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([$descripcion, $raza_id]);
                }
            }

            $pdo->commit();
            $exito = true;

        } catch (Exception $e) {
            $pdo->rollBack();
            $errores[] = "Error: " . $e->getMessage();
        }
    }
}

$pagina = 'adopciones_incluir_raza';

include('../../includes/header.php');
?>

<main>
    <section>
        <div class="container">
            <h2>Incluir una nueva especie y/o raza</h2>

            <?php if ($exito): ?>
                <p class="exito">
                    <i class="fa-regular fa-check-double"></i>
                    Datos guardados correctamente.
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

            <form method="post" class="formulario">

                <!-- ESPECIE -->
                <div class="filtro">
                    <label for="especie_id">Especie existente:</label>
                    <select name="especie_id" id="especie_id">
                        <option value="">-- Selecciona una especie --</option>
                        <!-- Aquí se cargarán las especies por JS -->
                    </select>
                </div>

                <div class="filtro">
                    <label for="especie_nueva">O crear nueva especie:</label>
                    <input
                        type="text"
                        name="especie_nueva"
                        id="especie_nueva"
                        placeholder="Ej: Perro, Gato, Conejo..."
                        value="<?= htmlspecialchars($_POST['especie_nueva'] ?? '') ?>"
                    />
                    <p><small>Si rellenas este campo, se creará una nueva especie.</small></p>
                </div>

                <hr>

                <!-- RAZA -->
                <div class="filtro">
                    <label for="raza_id">Raza existente (según especie):</label>
                    <select name="raza_id" id="raza_id">
                        <option value="">-- Selecciona una raza --</option>
                        <!-- Aquí se cargarán las razas por JS según la especie -->
                    </select>
                </div>

                <div class="filtro">
                    <label for="raza_nueva">O crear nueva raza:</label>
                    <input
                        type="text"
                        name="raza_nueva"
                        id="raza_nueva"
                        placeholder="Ej: Pastor Alemán, Bichón Maltés..."
                        value="<?= htmlspecialchars($_POST['raza_nueva'] ?? '') ?>"
                    />
                    <p><small>Si rellenas este campo, se creará una nueva raza para la especie seleccionada o creada.</small></p>
                </div>

                <!-- DESCRIPCIÓN -->
                <div class="filtro">
                    <label for="descripcion">Descripción (opcional):</label>
                    <textarea
                        name="descripcion"
                        id="descripcion"
                        rows="4"
                    ><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
                </div>

                <button type="submit" class="btn-primary" id="btn-guardar"
                    <i class="fa-solid fa-floppy-disk"></i> Guardar
                </button>

            </form>
        </div>
    </section>
</main>

<script>
    // Script para cargar espercies y razas de la base de datos en el módulo "Incluir nueva especie animal"
    document.addEventListener("DOMContentLoaded", function () {

        const especieSelect = document.getElementById("especie_id");
        const razaSelect = document.getElementById("raza_id");

        // ============================
        // 1) Cargar especies al inicio
        // ============================
        cargarEspecies();

        function cargarEspecies() {
            fetch("ajax/ajax_especies.php")
                .then(response => response.json())
                .then(data => {
                    especieSelect.innerHTML = '<option value="">-- Selecciona una especie --</option>';

                    data.forEach(especie => {
                        const option = document.createElement("option");
                        option.value = especie.id;
                        option.textContent = especie.nombre;
                        especieSelect.appendChild(option);
                    });
                })
                .catch(error => console.error("Error cargando especies:", error));
        }

        // ==========================================
        // 2) Cuando cambia la especie → cargar razas
        // ==========================================
        especieSelect.addEventListener("change", function () {
            const especieId = this.value;

            // Si no hay especie seleccionada, vaciamos razas
            if (especieId === "") {
                razaSelect.innerHTML = '<option value="">-- Selecciona una raza --</option>';
                return;
            }

            cargarRazas(especieId);
        });

        function cargarRazas(especieId) {
            fetch("ajax/ajax_razas.php?especie_id=" + especieId)
                .then(response => response.json())
                .then(data => {
                    razaSelect.innerHTML = '<option value="">-- Selecciona una raza --</option>';

                    data.forEach(raza => {
                        const option = document.createElement("option");
                        option.value = raza.id;
                        option.textContent = raza.nombre;
                        razaSelect.appendChild(option);
                    });
                })
                .catch(error => console.error("Error cargando razas:", error));
        }

    });

    // Script para validar la creación de nuevas especies y razas en el módulo "Incluir nueva especie animal"
    document.addEventListener("DOMContentLoaded", function () {

        const especieNueva = document.getElementById("especie_nueva");
        const razaNueva = document.getElementById("raza_nueva");
        const especieSelect = document.getElementById("especie_id");

        // ============================
        // VALIDAR ESPECIE NUEVA
        // ============================
        especieNueva.addEventListener("blur", function () {
            const nombre = this.value.trim();
            if (nombre === "") return;

            fetch("ajax/ajax_validar.php?tipo=especie&nombre=" + encodeURIComponent(nombre))
                .then(r => r.json())
                .then(data => {
                    if (data.existe) {
                        mostrarModal(
                            "La especie ya existe",
                            "La especie <strong>" + nombre + "</strong> ya está registrada en el sistema."
                        );
                        this.value = "";
                    }
                });
        });

        // ============================
        // VALIDAR RAZA NUEVA
        // ============================
        razaNueva.addEventListener("blur", function () {
            const nombre = this.value.trim();
            if (nombre === "") return;

            const especieId = especieSelect.value;

            if (especieId === "") {
                mostrarModal(
                    "Selecciona una especie",
                    "Para crear una raza nueva primero debes seleccionar o crear una especie."
                );
                this.value = "";
                return;
            }

            fetch("ajax/ajax_validar.php?tipo=raza&nombre=" + encodeURIComponent(nombre) + "&especie_id=" + especieId)
                .then(r => r.json())
                .then(data => {
                    if (data.existe) {
                        mostrarModal(
                            "La raza ya existe",
                            "La raza <strong>" + nombre + "</strong> ya está registrada para esta especie."
                        );
                        this.value = "";
                    }
                });
        });

    });
</script>

<?php include('../../includes/footer.php');