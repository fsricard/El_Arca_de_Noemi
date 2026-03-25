            <main class="layout-home">

                <!-- Módulo adopciones gatos -->
                <section class="destacados noemi-gatos">

                    <article class="destacado-block">
                        <h2 class="destacado-title">

                        </h2>

                        <div class="destacado-content">

                        </div>

                    </article>

                    <?php
                        if (!esSoloMovil()){
                            include('includes/aside/noemi-frases.php');
                        }
                    ?>

                </section>

                <!-- Módulo presentación -->
                <section class="destacados">

                    <article class="destacado-block">
                        <h2 class="destacado-title">

                        </h2>

                        <div class="destacado-content">

                        </div>

                    </article>

                </section>

                <!-- Módulo adopciones perros -->
                <section class="destacados noemi-perros">

                    <?php
                        if (!esSoloMovil()){
                            include('includes/aside/noemi-bichillos.php');
                        }
                    ?>

                    <article class="destacado-block">
                        <h2 class="destacado-title">

                        </h2>

                        <div class="destacado-content">

                        </div>

                    </article>

                </section>

            </main>