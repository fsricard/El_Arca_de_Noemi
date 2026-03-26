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

                <?php
                    $stmt = $pdo->query("
                        SELECT id, titulo, contenido, actualizado
                        FROM asi_es_noemi
                        ORDER BY id DESC
                        LIMIT 1
                    ");

                    $noemi = $stmt->fetch(PDO::FETCH_ASSOC);

                    // Dividir el contenido en párrafos
                    $parrafos = preg_split('/<\/p>/', $noemi['contenido'], -1, PREG_SPLIT_NO_EMPTY);

                    // Obtener los 3 primeros párrafos
                    $primeros_tres = '';
                    for ($i = 0; $i < min(3, count($parrafos)); $i++) {
                        $primeros_tres .= $parrafos[$i] . '</p>';
                    }
                ?>

                <section class="destacados">

                    <article class="destacado-block">
                        <h2 class="destacado-title title-presentacion">
                            <?= htmlspecialchars($noemi['titulo']) ?>
                        </h2>

                        <div class="destacado-content content-presentacion">
                            <?= $primeros_tres ?>
                        </div>

                        <a href="<?= asset('/asi-es-noemi') ?>" class="btn">
                            Leer más...
                        </a>

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