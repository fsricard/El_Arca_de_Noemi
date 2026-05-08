<nav id="sidebar" class="sidebar">

    <!-- Inicio -->
    <a href="<?= asset('admin/dashboard.php') ?>">
        <i class="fa-solid fa-house icon icon-inicio"></i> Inicio
    </a>

    <!-- Documentos -->
    <div class="submenu">
        <button class="submenu-toggle">
            <i class="fa-solid fa-book-open icon icon-documentos"></i> Documentos
            <i class="fa-solid fa-chevron-down flecha"></i>
        </button>

        <ul class="submenu-items">
            <li><a href="<?= asset('admin/modulos/contacto/contacto.php') ?>"><i class="fa-solid fa-envelope icon icon-doc-contacto"></i> Contacto</a></li>
            <li><a href="<?= asset('admin/modulos/contacto/contacto_intro.php') ?>"><i class="fa-solid fa-envelope icon icon-doc-contacto"></i> Contacto intro</a></li>
            <li><a href="<?= asset('admin/modulos/asi_es_noemi/asi_es_noemi.php') ?>"><i class="fa-solid fa-person-burst icon icon-asi-es-noemi"></i> Así es Noemí</a></li>
            <li><a href="<?= asset('admin/modulos/politica/politica_de_privacidad.php') ?>"><i class="fa-solid fa-shield-halved icon icon-doc-privacidad"></i> Política de privacidad</a></li>
            <li><a href="<?= asset('admin/modulos/opiniones/opiniones_de_usuario.php') ?>"><i class="fa-solid fa-user-doctor-message icon icon-opiniones-listado"></i> Opiniones de usuarios</a></li>

            <!-- Frases -->
            <li class="submenu-nested">
                <button class="submenu-toggle">
                    <i class="fa-solid fa-hand-horns icon icon-frases"></i> Sarcásmo y humor
                    <i class="fa-solid fa-chevron-down flecha"></i>
                </button>

                <ul class="submenu-items-nested">
                    <li><a href="<?= asset('admin/modulos/noemi_dice/noemi_dice.php') ?>"><i class="fa-solid fa-face-grin-tongue-wink icon icon-frases-loco"></i> Noemí dice</a></li>
                    <li><a href="<?= asset('admin/modulos/noemi_dice/noemi_dice_listado.php') ?>"><i class="fa-solid fa-scroll icon icon-frases-scroll"></i> El listado de Noemí dice</a></li>
                    <li><a href="<?= asset('admin/modulos/bichillos/noemi_bichillos.php') ?>"><i class="fa-solid fa-paw icon icon-frases-bichillos"></i> Los bichillos de Noemí</a></li>
                    <li><a href="<?= asset('admin/modulos/bichillos/noemi_bichillos_listado.php') ?>"><i class="fa-solid fa-images icon icon-frases-bichillos-listado"></i> El listado bichillos de Noemí</a></li>
                </ul>
            </li>

        </ul>
    </div>

    <!-- Registros -->
    <div class="submenu">
        <button class="submenu-toggle">
            <i class="fa-classic fa-solid fa-cash-register icon icon-registro"></i> Registros
            <i class="fa-solid fa-chevron-down flecha"></i>
        </button>

        <ul class="submenu-items">
            <li><a href="<?= asset('admin/modulos/registros/adopciones_incluir.php') ?>"><i class="fa-solid fa-paw-simple icon icon-registros-raza"></i> Incluir nueva raza</a></li>
            <li><a href="<?= asset('admin/modulos/registros/adopciones.php') ?>"><i class="fa-solid fa-file-circle-plus icon icon-registros-incluir"></i> Incluir animal para adoptar</a></li>
            <li><a href="<?= asset('admin/modulos/registros/adopciones_adoptante.php') ?>"><i class="fa-solid fa-user-plus icon icon-registros-adoptante"></i> Incluir un nuevo adoptante</a></li>
            <li><a href="<?= asset('admin/modulos/registros/apadrinamiento_incluir.php') ?>"><i class="fa-solid fa-file-circle-plus icon icon-registros-incluir-apadrinar"></i> Incluir animal para apadrinar</a></li>
            <li><a href="<?= asset('admin/modulos/crowdfunding/incluir_plataforma_crowdfunding.php') ?>"><i class="fa-classic icon fa-solid fa-piggy-bank icon-incluir-crowdfunding"></i> Incluir plataforma de CrowdFunding</a></li>
        </ul>
    </div>

    <!-- CrowdFunding -->
    <div class="submenu">
        <button class="submenu-toggle">
            <i class="fa-duotone fa-solid fa-money-bill-1-wave icon icon-crowdfunding"></i> CrowdFunding
            <i class="fa-solid fa-chevron-down flecha"></i>
        </button>

        <ul class="submenu-items">
            <li><a href="<?= asset('admin/modulos/crowdfunding/crear_recaudacion.php') ?>"><i class="fa-classic fa-solid fa-bin-bottles-recycle icon icon-crear-crowdfunding"></i> Crear recaudación</a></li>
            <li><a href="<?= asset('admin/modulos/crowdfunding/listado_recaudaciones.php') ?>"><i class="fa-classic fa-solid fa-toilet-paper icon icon-listado-crowdfunding"></i> Listado de recaudaciones</a></li>
        </ul>
    </div>

    <!-- Adopciones -->
    <div class="submenu">
        <button class="submenu-toggle">
            <i class="fa-solid fa-family icon icon-adopciones"></i> Adopciones
            <i class="fa-solid fa-chevron-down flecha"></i>
        </button>

        <ul class="submenu-items">
            <li><a href="<?= asset('admin/modulos/adopciones/sistema_adopciones_iniciar.php') ?>"><i class="fa-classic fa-solid fa-gears icon icon-iniciar-adopcion"></i> Iniciar nueva adopción</a></li>
            <li><a href="<?= asset('admin/modulos/adopciones/sistema_adopciones_listado_adoptantes.php') ?>"><i class="fa-solid fa-users-rectangle icon icon-listado-adoptantes"></i> Listado de adoptantes</a></li>
            <li><a href="<?= asset('admin/modulos/adopciones/sistema_adopciones_listado.php') ?>"><i class="fa-solid fa-list-check icon icon-listado-animales-adoptantes"></i> Listado de animales en adopción</a></li>
        </ul>
    </div>

    <!-- Apadrinamientos -->
    <div class="submenu">
        <button class="submenu-toggle">
            <i class="fa-classic fa-solid fa-flower-tulip icon icon-apadrinamientos"></i> Apadrinamientos
            <i class="fa-solid fa-chevron-down flecha"></i>
        </button>

        <ul class="submenu-items">
            <li><a href="<?= asset('admin/modulos/apadrinamientos/apadrina_listado_animales.php') ?>"><i class="fa-classic fa-solid fa-magnifying-glass-play icon icon-apadrina-listado-animales"></i> Listado de animales</a></li>
            <li><a href="<?= asset('admin/modulos/apadrinamientos/apadrina_listado_padrinos.php') ?>"><i class="fa-classic fa-solid fa-person icon icon-apadrina-listado-padrinos"></i> Listado de padrinos</a></li>
        </ul>
    </div>

    <!-- Base de datos -->
    <div class="submenu">
        <button class="submenu-toggle">
            <i class="fa-solid fa-database icon icon-bd"></i> Base de datos
            <i class="fa-solid fa-chevron-down flecha"></i>
        </button>

        <ul class="submenu-items">
            <li><a href="<?= asset('admin/modulos/logs/logs.php') ?>"><i class="fa-solid fa-file-lines icon icon-bd-logs"></i> Logs</a></li>
            <li><a href="<?= asset('admin/modulos/usuarios/usuarios.php') ?>"><i class="fa-solid fa-users icon icon-bd-usuarios"></i> Usuarios</a></li>
            <li><a href="<?= asset('admin/modulos/tablas_base_datos/tablas_de_datos.php') ?>"><i class="fa-solid fa-table icon icon-bd-tablas"></i> Tablas de datos</a></li>
        </ul>
    </div>

    <!-- Logout -->
    <a href="<?= asset('admin/logout.php') ?>">
        <i class="fa-solid fa-right-from-bracket icon icon-logout"></i> Cerrar sesión
    </a>

</nav>

<script>
    // Script para abrir/cerrar los submenús
    (function () {
    const IGNORE_MS = 350;

    function closeAll() {
        document.querySelectorAll('.submenu.open, .submenu-nested.open')
        .forEach(el => {
            el.classList.remove('open');
            el.classList.remove('just-opened');
        });
    }

    // Delegado pointerdown: touch + mouse
    document.addEventListener('pointerdown', function (e) {
        const btn = e.target.closest('.submenu-toggle');

        // Si pulsamos un toggle (nivel 1 o nested)
        if (btn) {
        e.preventDefault();
        e.stopPropagation();

        // buscar primero nested, luego principal
        const container = btn.closest('.submenu-nested') || btn.closest('.submenu');
        if (!container) return;

        const willOpen = !container.classList.contains('open');

        // No cerramos el padre cuando abrimos un nested; solo toggle en el contenedor encontrado
        container.classList.toggle('open', willOpen);

        if (willOpen) {
            container.classList.add('just-opened');
            setTimeout(() => container.classList.remove('just-opened'), IGNORE_MS);
        } else {
            container.classList.remove('just-opened');
        }

        return;
        }

        // Si el pointerdown ocurre dentro de cualquier .submenu o .submenu-nested,
        // no cerramos nada: permitimos que los enlaces reciban el evento normalmente.
        if (e.target.closest('.submenu') || e.target.closest('.submenu-nested')) {
        return;
        }

        // Si no es ni toggle ni dentro de un submenu, cerramos todo
        closeAll();
    }, { passive: false });

    // Evitar navegación accidental en el primer click tras abrir
    document.addEventListener('click', function (e) {
        const link = e.target.closest('.submenu-items a, .submenu-items-nested a');
        if (!link) return;

        const parentSub = link.closest('.submenu, .submenu-nested');
        if (parentSub && parentSub.classList.contains('just-opened')) {
        // primer click tras abrir: evitar navegación accidental
        e.preventDefault();
        e.stopPropagation();
        parentSub.classList.remove('just-opened');
        return;
        }
        // si no está "just-opened", dejamos que el enlace navegue
    }, true); // captura para interceptar antes que otros handlers

    // Hover en desktop (no sustituye al click)
    function enableDesktopHover() {
        const isDesktop = window.matchMedia('(min-width: 769px)').matches;
        document.querySelectorAll('.submenu').forEach(s => {
        s.onmouseenter = isDesktop ? () => s.classList.add('open') : null;
        s.onmouseleave = isDesktop ? () => s.classList.remove('open') : null;
        });
        document.querySelectorAll('.submenu-nested').forEach(s => {
        s.onmouseenter = isDesktop ? () => s.classList.add('open') : null;
        s.onmouseleave = isDesktop ? () => s.classList.remove('open') : null;
        });
    }
    enableDesktopHover();
    window.addEventListener('resize', enableDesktopHover);
    })();
</script>