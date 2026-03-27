<nav id="sidebar" class="sidebar">

    <!-- Inicio -->
    <a href="<?= asset('admin/dashboard.php') ?>">
        <i class="fa-solid fa-house icon-inicio"></i> Inicio
    </a>

    <!-- Adopciones -->
    <div class="submenu">
        <button class="submenu-toggle">
            <img src="<?= asset('/img/adopciones_20260327_0002.png') ?>" class="icon-adopciones" /> Adopciones
            <i class="fa-solid fa-chevron-down flecha"></i>
        </button>

        <ul class="submenu-items">
            <li><a href="<?= asset('admin/adopciones_incluir.php') ?>"><i class="fa-solid fa-paw-simple icon-adopciones-raza"></i> Incluir nueva raza</a></li>
            <li><a href="<?= asset('admin/adopciones.php') ?>"><i class="fa-solid fa-file-circle-plus icon-adopciones-incluir"></i> Incluir nueva adopción</a></li>
            <li><a href="<?= asset('admin/adopciones_listado.php') ?>"><i class="fa-solid fa-list-check icon-adopciones-listado"></i> Listado de adopciones</a></li>
            <li><a href="<?= asset('admin/adopciones_adoptante.php') ?>"><i class="fa-solid fa-user-plus icon-adopciones-adoptante"></i> Incluir nuevo adoptante</a></li>
            <li><a href="<?= asset('admin/adopciones_listado_adoptantes.php') ?>"><i class="fa-solid fa-users-rectangle icon-adopciones-listado-adoptantes"></i> Listado de adoptantes</a></li>
        </ul>
    </div>

    <!-- Frases -->
    <div class="submenu">
        <button class="submenu-toggle">
            <i class="fa-solid fa-hand-horns icon-frases"></i> Sarcásmo y humor
            <i class="fa-solid fa-chevron-down flecha"></i>
        </button>

        <ul class="submenu-items">
            <li><a href="<?= asset('admin/noemi_dice.php') ?>"><i class="fa-solid fa-face-grin-tongue-wink icon-frases-loco"></i> Noemí dice</a></li>
            <li><a href="<?= asset('admin/noemi_dice_listado.php') ?>"><i class="fa-solid fa-scroll icon-frases-scroll"></i> El listado de Noemí dice</a></li>
            <li><a href="<?= asset('admin/noemi_bichillos.php') ?>"><i class="fa-solid fa-paw icon-frases-bichillos"></i> Los bichillos de Noemí</a></li>
            <li><a href="<?= asset('admin/noemi_bichillos_listado.php') ?>"><i class="fa-solid fa-images icon-frases-bichillos-listado"></i> El listado bichillos de Noemí</a></li>
        </ul>
    </div>

    <!-- Documentos -->
    <div class="submenu">
        <button class="submenu-toggle">
            <i class="fa-solid fa-book-open icon-documentos"></i> Documentos
            <i class="fa-solid fa-chevron-down flecha"></i>
        </button>

        <ul class="submenu-items">
            <li><a href="<?= asset('admin/contacto.php') ?>"><i class="fa-solid fa-envelope icon-doc-contacto"></i> Contacto</a></li>
            <li><a href="<?= asset('admin/contacto_intro.php') ?>"><i class="fa-solid fa-envelope icon-doc-contacto"></i> Contacto intro</a></li>
            <li><a href="<?= asset('admin/asi_es_noemi.php') ?>"><i class="fa-solid fa-person-burst icon-asi-es-noemi"></i> Así es Noemí</a></li>
            <li><a href="<?= asset('admin/politica_de_privacidad.php') ?>"><i class="fa-solid fa-shield-halved icon-doc-privacidad"></i> Política de privacidad</a></li>
        </ul>
    </div>

    <!-- Base de datos -->
    <div class="submenu">
        <button class="submenu-toggle">
            <i class="fa-solid fa-database icon-bd"></i> Base de datos
            <i class="fa-solid fa-chevron-down flecha"></i>
        </button>

        <ul class="submenu-items">
            <li><a href="<?= asset('admin/logs.php') ?>"><i class="fa-solid fa-file-lines icon-bd-logs"></i> Logs</a></li>
            <li><a href="<?= asset('admin/usuarios.php') ?>"><i class="fa-solid fa-users icon-bd-usuarios"></i> Usuarios</a></li>
            <li><a href="<?= asset('admin/tablas_de_datos.php') ?>"><i class="fa-solid fa-table icon-bd-tablas"></i> Tablas de datos</a></li>
        </ul>
    </div>

    <!-- Logout -->
    <a href="<?= asset('admin/logout.php') ?>">
        <i class="fa-solid fa-right-from-bracket icon-logout"></i> Cerrar sesión
    </a>

</nav>