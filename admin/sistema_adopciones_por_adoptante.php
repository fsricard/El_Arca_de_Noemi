<?php
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../config/database.php';
require_once(__DIR__ . '/../config/funciones.php');

// Si no está logueado, redirigimos al login
if (!isLoggedIn()) {
    header("Location: index.php");
    exit;
}

$pagina='sistema_adopciones_por_adoptante';

include('includes/header.php');
?>

    <main>
        <section>
            <div class="container">
                <h2></h2>

                

            </div>
        </section>
    </main>

<?php include('includes/footer.php');