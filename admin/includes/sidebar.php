<nav id="sidebar" class="sidebar">

    <!-- Inicio -->
    <a href="<?= asset('admin/dashboard.php') ?>">
        <i class="fa-solid fa-house icon-inicio"></i> Inicio
    </a>

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