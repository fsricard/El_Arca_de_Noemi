<?php
require_once(__DIR__ . '/../../config/database.php');
require_once(__DIR__ . '/../../config/funciones.php');

$noemibichillos = noemi_bichillos_random($pdo);
?>
<aside class="sidebar">
    <section class="sidebar-block noemi-bichillos">
        <h3 class="sidebar-title">
            <i class="fa-solid fa-alicorn"></i> Los bichillos de Noemí
        </h3>

        <div class="noemi-container">
            <p class="noemi-bichillo">
                <?= htmlspecialchars($noemibichillos) ?>
            </p>
        </div>
    </section>
</aside>