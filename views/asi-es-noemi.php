<?php
    require 'config.php';

    $id = $_GET['id'] ?? null;

    if (!$id) {
        die("Contenido no encontrado");
    }

    $stmt = $pdo->prepare("
        SELECT titulo, contenido, actualizado
        FROM asi_es_noemi
        WHERE id = ?
        LIMIT 1
    ");
    $stmt->execute([$id]);
    $noemi = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$noemi) {
        die("Contenido no disponible");
    }
?>

<h1><?= htmlspecialchars($noemi['titulo']) ?></h1>

<div class="contenido-completo">
    <?= $noemi['contenido'] ?>
</div>