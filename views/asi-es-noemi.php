<?php
$stmt = $pdo->query("
    SELECT id, titulo, contenido, actualizado
    FROM asi_es_noemi
    ORDER BY id DESC
    LIMIT 1
");

$noemi = $stmt->fetch(PDO::FETCH_ASSOC);
?>

            <main class="layout-home">

                <section class="destacados">

                    <article class="destacado-block">
                        <h2 class="destacado-title">
                            <?= htmlspecialchars($noemi['titulo']) ?>
                        </h2>

                        <div class="destacado-content">
                            <?= $noemi['contenido'] ?>
                        </div>

                    </article>

                </section>

            </main>