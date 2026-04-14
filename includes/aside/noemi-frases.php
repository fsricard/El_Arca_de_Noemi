<?php
require_once(__DIR__ . '/../../config/database.php');
require_once(__DIR__ . '/../../config/funciones.php');

$noemiFrase = noemi_frase_random($pdo);
?>
<aside class="sidebar">
    <section class="sidebar-block noemi-frases">
        <h3 class="sidebar-title">
            <i class="fa-solid fa-face-beam-hand-over-mouth"></i> Noemí dice...
        </h3>

        <div class="noemi-container">

            <?php if (!esMovilOtablet()): ?>
                <div class="noemi-avatar">
                    <img src="<?= asset('/img/logo_20260320_0002.png') ?>" alt="Logo de El Arca de Noemi">
                </div>
            <?php endif; ?>

            <p class="noemi-frase">
                <?= htmlspecialchars($noemiFrase) ?>
            </p>
        </div>
    </section>
</aside>