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

$pagina='incluir_plataforma_crowdfunding';

include('../../includes/header.php');
?>

    <main>
        <section>
            <div class="container">
                <h2>Incluir una nueva plataforma de CrowdFunding</h2>

                

            </div>
        </section>
    </main>

<?php include('../../includes/footer.php');