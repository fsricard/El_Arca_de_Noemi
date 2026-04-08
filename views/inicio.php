            <main class="layout-home">

                <!-- Módulo adopciones -->
                <?php
                $animal = obtener_animal_adopcion_random($pdo);

                if (!$animal): ?>

                    <section class="destacados noemi-adopciones-inicio">
                        <article class="destacado-block">
                            <div class="vacio-adopciones">
                                <div class="vacio-wrapper">
                                    <div class="vacio-icono">
                                        <i class="fa-solid fa-paw"></i>
                                    </div>

                                    <h2 class="vacio-titulo">
                                        ¡Ups… hoy no hay peludetes en adopción!
                                    </h2>

                                    <p class="vacio-texto">
                                        <i class="fa-solid fa-heart"></i>
                                        Pero tranqui, Noemí está por ahí <strong>rescatando, bañando, achuchando</strong>  
                                        y preparando nuevas historias de amor.  
                                        <br>
                                        <span class="vacio-pillin">
                                            (Y tú no te escapes… que te tengo fichado para la próxima adopción 😉)
                                        </span>
                                    </p>

                                    <div class="vacio-cta">
                                        <a href="<?= asset('/contacto') ?>" class="btn">
                                            <i class="fa-solid fa-hand-holding-heart"></i>  
                                            ¿Quieres ayudar mientras tanto?
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </article>

                        <?php include('includes/aside/noemi-frases.php'); ?>
                    </section>

                <?php else: ?>

                    <?php
                    // Iconos por especie
                    $iconosEspecie = [
                        'perro'     => 'fa-dog',
                        'gato'      => 'fa-cat',
                        'conejo'    => 'fa-rabbit-running',
                        'ave'       => 'fa-dove',
                        'huron'     => 'fa-otter',
                        'hurón'     => 'fa-otter',
                        'tortuga'   => 'fa-turtle',
                    ];

                    $especie_normalizada = strtolower(trim($animal['especie']));
                    $especie_normalizada = str_replace(['á','é','í','ó','ú'], ['a','e','i','o','u'], $especie_normalizada);

                    $icono = $iconosEspecie[$especie_normalizada] ?? 'fa-paw';
                    ?>

                    <section class="destacados noemi-adopciones-inicio">

                        <article class="destacado-block">
                            
                            <h2 class="destacado-title">
                                <i class="fa-solid fa-paw"></i> En adopción
                            </h2>

                            <div class="destacado-content noemi-adopcion-item">

                                <!-- Imagen -->
                                <div class="adopcion-imagen">
                                    <img src="<?= asset($animal['imagen_principal']) ?>"
                                        alt="Foto de <?= htmlspecialchars($animal['nombre']) ?>">
                                </div>

                                <!-- Información -->
                                <div class="adopcion-info">

                                    <h3 class="adopcion-nombre">
                                        <i class="fa-solid <?= $icono ?>"></i>
                                        <?= htmlspecialchars($animal['nombre']) ?>
                                    </h3>

                                    <?php if (!empty($animal['raza'])): ?>
                                        <p class="adopcion-raza">
                                            <?= htmlspecialchars($animal['especie']) ?> · <?= htmlspecialchars($animal['raza']) ?>
                                        </p>
                                    <?php endif; ?>

                                    <?php if (!empty($animal['descripcion'])): ?>
                                        <div class="adopcion-descripcion">
                                            <?= limitar_palabras($animal['descripcion'], 20) ?>
                                        </div>
                                    <?php endif; ?>

                                    <a href="<?= asset('/ficha-adopcion?id=' . $animal['id']) ?>" class="btn adopcion-boton">
                                        Ir a la ficha individual
                                    </a>

                                </div>

                            </div>

                        </article>

                        <?php include('includes/aside/noemi-frases.php'); ?>

                    </section>

                <?php endif; ?>

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

                    // Obtener los 2 primeros párrafos
                    $primeros_dos = '';
                    for ($i = 0; $i < min(2, count($parrafos)); $i++) {
                        $primeros_dos .= $parrafos[$i] . '</p>';
                    }
                ?>

                <section class="destacados">

                    <article class="destacado-block">

                        <h2 class="destacado-title title-presentacion">
                            <?= htmlspecialchars($noemi['titulo']) ?>
                        </h2>

                        <div class="destacado-content content-presentacion">
                            <?= $primeros_dos ?>
                        </div>

                        <a href="<?= asset('/asi-es-noemi') ?>" class="btn">
                            Leer más...
                        </a>

                    </article>

                </section>

                <!-- Módulo apadrinamientos -->
                <section class="destacados noemi-apadrinamientos-inicio">

                    <?php include('includes/aside/noemi-bichillos.php'); ?>

                    <article class="destacado-block">

                        <h2 class="destacado-title">

                        </h2>

                        <div class="destacado-content">

                        </div>

                    </article>

                </section>

            </main>