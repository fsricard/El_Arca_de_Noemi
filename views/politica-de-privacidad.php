            <main class="layout-home">

                <section class="destacados">

                    <?php
                        $stmt = $pdo->query("
                            SELECT contenido, actualizado
                            FROM politica_privacidad
                            ORDER BY id DESC
                            LIMIT 1
                        ");

                        $politica = $stmt->fetch(PDO::FETCH_ASSOC);
                    ?>

                    <article class="destacado-block destacado-politica-de-privacidad">
                        <h2 class="destacado-title">
                            <i class="fa-solid fa-user-secret"></i> Aviso legal:
                        </h2>

                        <?php if($politica): ?>

                        <div class="destacado-content">
                            <?= $politica['contenido'] ?>
                        </div>

                        <?php endif; ?>

                    </article>

                </section>

            </main>