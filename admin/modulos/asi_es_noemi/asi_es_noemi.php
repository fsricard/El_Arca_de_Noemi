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

// Obtener contenido actual
$stmt = $pdo->query("SELECT * FROM asi_es_noemi LIMIT 1");
$asi_es_noemi = $stmt->fetch();

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $contenido = trim($_POST['contenido'] ?? '');

    if ($titulo === '') {
        $errores[] = "El título no puede estar vacío.";
    }

    if ($contenido === '') {
        $errores[] = "El contenido no puede estar vacío.";
    }

    if (empty($errores)) {
        try {
            if ($asi_es_noemi) {
                $stmt = $pdo->prepare("UPDATE asi_es_noemi SET titulo = ?, contenido = ? WHERE id = ?");
                $stmt->execute([$titulo, $contenido, $asi_es_noemi['id']]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO asi_es_noemi (titulo, contenido) VALUES (?, ?)");
                $stmt->execute([$titulo, $contenido]);
            }

            $exito = true;
            $asi_es_noemi['titulo'] = $titulo;
            $asi_es_noemi['contenido'] = $contenido;

        } catch (PDOException $e) {
            $errores[] = "Error al guardar: " . $e->getMessage();
        }
    }
}

$pagina='asi_es_noemi';

include('../../includes/header.php');
?>

    <main>
        <section>
            <div class="container">
                <h2>Bloque "Así es Noemí" pagina de inicio</h2>

                <?php if ($exito): ?>
                    <p class="exito"><i class="fa-regular fa-check-double"></i> Cambios guardados correctamente.</p>
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
                        <label for="titulo">Título:</label>
                        <input type="text" name="titulo" id="titulo" value="<?= htmlspecialchars($asi_es_noemi['titulo'] ?? '') ?>" />
                    </div>
                    
                    <label for="descripcion">Contenido:</label>
                    <div id="editor-descripcion" class="quill-editor">
                        <?= !empty($asi_es_noemi['contenido']) ? $asi_es_noemi['contenido'] : '<p></p>' ?>
                    </div>
                    <textarea id="descripcion" name="contenido" class="editor-html" style="display:none;">
                        <?= htmlspecialchars($asi_es_noemi['contenido'] ?? '') ?>
                    </textarea>

                    <button type="submit" id="btn-guardar" class="btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i> Guardar
                    </button>
                </form>
            </div>
        </section>
    </main>

<?php include('../../includes/footer.php');