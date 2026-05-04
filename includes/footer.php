        <div class="redes-footer">
            <a href="" title="El Arca de Noemí en Instagram" target="_blank">
                <i class="icon-redes-footer fa fa-instagram"></i>
            </a>
            <a href="" title="El Arca de Noemí en YouTube" target="_blank">
                <i class="icon-redes-footer fa fa-youtube"></i>
            </a>
            <a href="" title="El Arca de Noemí en Facebook" target="_blank">
                <i class="icon-redes-footer fa fa-facebook"></i>
            </a>
        </div>

        <footer class="container-footer">
            <div class="footer">
                <div class="sections-footer">
                    <div class="sections-footer-left">
                        <h3>Esas cosillas legales</h3>
                        <ul>
                            <li><a href="<?= asset('/asi-es-noemi') ?>">Así es Noemí</a></li>
                            <li><a href="<?= asset('/contacto') ?>">Contacta con Noemí</a></li>
                            <li><a href="<?= asset('/politica-de-privacidad') ?>">Política de privacidad</a></li>
                        </ul>
                    </div>

                    <div class="sections-footer-center">
                        <h3>Apoya a El Arca</h3>
                        <ul>
                            <li><a href="<?= asset('/danos-tu-opinion') ?>">Danos tu opinión</a></li>
                            <li><a href="<?= asset('/listado-adopciones') ?>">Adopta un bichi</a></li>
                            <li><a href="<?= asset('/listado-apadrinamientos') ?>">Apadrina un bichi</a></li>
                            <li><a href="<?= asset('/listado-crowdfunding') ?>">Colabora con El Arca</a></li>
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

            // Script para el Slider de las opiniones de los usuarios
            document.addEventListener("DOMContentLoaded", () => {

                const items = document.querySelectorAll(".opinion-item");
                let index = 0;
                let direccion = 1; // 1 = derecha→izquierda, -1 = izquierda→derecha

                if (items.length === 0) return;

                // Activar la primera
                items[index].classList.add("activa");

                function cambiarOpinion() {

                    let actual = items[index];
                    actual.classList.remove("activa");
                    actual.classList.add("saliendo");

                    // Calcular siguiente índice
                    index += direccion;

                    // Cambiar dirección si llegamos al final o al inicio
                    if (index === items.length - 1) direccion = -1;
                    else if (index === 0) direccion = 1;

                    let siguiente = items[index];
                    siguiente.classList.add("entrando");

                    // Limpiar clases después de la transición
                    setTimeout(() => {
                        actual.classList.remove("saliendo");
                        siguiente.classList.remove("entrando");
                        siguiente.classList.add("activa");
                    }, 1200);
                }

                // Cambiar cada 15 segundos
                setInterval(cambiarOpinion, 15000);
            });
        </script>
        </body>

        </html>