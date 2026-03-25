        <footer class="container-footer">
            <div class="footer">
                <div class="sections-footer">
                    <div class="sections-footer-left">
                        <h3>Sobre nosotros</h3>
                        <ul>
                            <li><a href="<?= asset('/contacto') ?>">Contacta con Noemí</a></li>
                            <li><a href="<?= asset('/politica-de-privacidad') ?>">Política de privacidad</a></li>
                        </ul>
                    </div>
        
                    <div class="sections-footer-center">
                        <h3>Estamos en redes</h3>
                        <ul>
                            <li><a href=""></a></li>
                            <li><a href=""></a></li>
                        </ul>
                    </div>
        
                    <div class="sections-footer-right">
                        <img src="<?= asset('/img/logo_20260317_0001.png') ?>" alt="Logotipo de el Arca de Noemí" class="footer-logo" />
                    </div>
                </div>
        
                <hr />
        
                <h4>
                    <?php
                        echo CopyrightRicardFS();
                    ?>
                </h4>
            </div>
        </footer>

        <!-- Contenedor para el botón "Volver arriba" -->
        <button id="noemi-top-btn" aria-label="Subir arriba">
            <i class="fa-solid fa-paw-claws"></i>
        </button>

        <!-- Contenedor para el aviso de cookies -->
        <div id="cookies-noemi" class="cookies-modal">
            <div class="cookies-content">

                <div class="cookies-header">
                    <img src="<?= asset('/img/logo_20260317_0001.png') ?>" alt="El Arca de Noemi" class="cookies-logo">
                </div>

                <h3 class="cookies-title">
                    Aviso de Cookies
                </h3>

                <p class="cookies-text">
                    En la web del Arca de Noemi utilizamos cookies para mejorar tu experiencia,
                    analizar el flujo de visitantes y mantener el sitio siempre actualizado.
                    Puedes leer más en nuestra
                    <a href="<?= asset('/politica-de-privacidad') ?>">Política de Privacidad</a>.
                </p>

                <button id="cookies-aceptar" class="cookies-btn">
                    Aceptar y continuar
                </button>

            </div>
        </div>

        <script>
            // Script para el menú responsive
            function toggleMobileMenu() {
                const menu = document.getElementById('menuMovil');
                menu.style.left = (menu.style.left === '0px') ? '-100%' : '0px';
            }
            
            // Script para el aviso de cookies
            document.addEventListener("DOMContentLoaded", () => {

                const modal = document.getElementById("cookies-noemi");
                const btnAceptar = document.getElementById("cookies-aceptar");

                // Mostrar solo si no se ha aceptado antes
                if (!localStorage.getItem("cookiesnoemiAceptadas")) {
                    modal.style.display = "flex";
                    setTimeout(() => modal.style.opacity = "1", 50);
                }

                btnAceptar.addEventListener("click", () => {
                    localStorage.setItem("cookiesnoemiAceptadas", "true");
                    modal.style.opacity = "0";
                    setTimeout(() => modal.style.display = "none", 500);
                });

            });

            // Script àra el botón "Volver arriba"
            (function() {
                const btn = document.getElementById('noemi-top-btn');

                window.addEventListener('scroll', () => {
                    if (window.scrollY > 300) {
                        btn.classList.add('visible');
                    } else {
                        btn.classList.remove('visible');
                    }
                });

                btn.addEventListener('click', () => {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            })();
        </script>
    </body>
</html>