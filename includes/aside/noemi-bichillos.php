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

        <div class="noemi-bichillos-container">
            <?php if ($noemibichillos): ?>
                <p class="noemi-bichillo">
                    <img src="<?= asset($noemibichillos) ?>" alt="Los bichillos de Noemí" />
                </p>
            <?php else: ?>
                <div class="noemi-bichillos-vacio">
                    <div class="noemi-bichillos-icono">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                    </div>
                    <h4 class="noemi-bichillos-titulo">Ups… ¡no hay bichillos!</h4>
                    <p class="noemi-bichillos-texto">
                        Parece que hoy están <span class="noemi-bichillos-pillin">tramando alguna travesura</span> por ahí…  
                        <i class="fa-solid fa-face-grin-wink"></i>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</aside>