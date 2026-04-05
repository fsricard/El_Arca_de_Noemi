<?php
require_once(__DIR__ . '/../config/database.php');
require_once 'includes/modelo_animales.php';

$idAnimal = intval($_GET['id']);

$animal = getAnimal($idAnimal);
if (!$animal) {
    die("Animal no encontrado.");
}

$raza  = getRaza($animal['id_raza']);
$fotos = getFotos($animal['id']);

// Iconos por especie (Font Awesome Pro 7.0.1)
$iconosEspecie = [
    'perro'   => 'fa-dog',
    'gato'    => 'fa-cat',
    'conejo'  => 'fa-rabbit-running',
    'ave'     => 'fa-dove',
    'hurón'   => 'fa-otter',
    'tortuga' => 'fa-turtle',
];

$icono = $iconosEspecie[strtolower($raza['especie'])] ?? 'fa-paw';

// Foto principal
$fotoPrincipal = null;
foreach ($fotos as $foto) {
    if ($foto['es_principal']) {
        $fotoPrincipal = $foto['ruta'];
        break;
    }
}

// Calcular días en refugio
$tiempoRefugio = null;
if (!empty($animal['fecha_rescate'])) {
    $fechaRescate = new DateTime($animal['fecha_rescate']);
    $hoy = new DateTime();
    $tiempoRefugio = $fechaRescate->diff($hoy)->days;
}

// Bloque personalidad
$palabrasClave = [
    'cariñoso','cariñosa','juguetón','juguetona','tranquilo','tranquila',
    'sociable','activo','activa','obediente','nervioso','nerviosa',
    'tímido','tímida','bueno con niños','bueno con perros','bueno con gatos'
];

$descripcion = strtolower($animal['descripcion'] ?? '');
$personalidad = [];

foreach ($palabrasClave as $palabra) {
    if (strpos($descripcion, strtolower($palabra)) !== false) {
        $personalidad[] = ucfirst($palabra);
    }
}
?>

<main class="layout-home">

    <section class="destacados">

        <article class="destacado-block">

            <!-- Título principal -->
            <h2 class="destacado-title ficha-titulo">
                <i class="fa-solid fa-paw"></i>
                <?= htmlspecialchars($animal['nombre']) ?> está deseando irse contigo.
            </h2>

            <div class="ficha-contenido">

                <!-- Imagen principal -->
                <div class="ficha-imagen-principal">
                    <img src="<?= htmlspecialchars($fotoPrincipal) ?>" 
                        alt="Foto de <?= htmlspecialchars($animal['nombre']) ?>">
                </div>

                <!-- Información -->
                <div class="ficha-info">

                    <!-- Nombre + especie/raza -->
                    <div class="ficha-identidad">
                        <h3 class="ficha-nombre">
                            <i class="fa-solid <?= $icono ?>"></i>
                            <?= htmlspecialchars($animal['nombre']) ?>
                        </h3>

                        <p class="ficha-especie-raza">
                            <i class="fa-solid fa-paw"></i>
                            <?= ucfirst($raza['especie']) ?> — <?= ucfirst($raza['nombre']) ?>
                        </p>
                    </div>

                    <!-- Fechas discretas -->
                    <div class="bloque-fechas">
                        <?php if (!empty($animal['fecha_ingreso'])): ?>
                            <p>Ingreso: <span><?= date('d/m/Y', strtotime($animal['fecha_ingreso'])) ?></span></p>
                        <?php endif; ?>

                        <?php if ($tiempoRefugio !== null): ?>
                            <p>Llevo <span><?= $tiempoRefugio ?></span> días en el santuario.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Datos condicionales -->
                    <div class="bloque-datos">
                        <?php if (!empty($animal['sexo'])): ?>
                            <p><i class="fa-solid fa-venus-mars"></i> <span>Sexo:</span> <?= ucfirst($animal['sexo']) ?></p>
                        <?php endif; ?>

                        <?php if (!empty($animal['edad'])): ?>
                            <p><i class="fa-solid fa-hourglass-half"></i> <span>Edad:</span> <?= htmlspecialchars($animal['edad']) ?></p>
                        <?php endif; ?>

                        <?php if (!empty($animal['fecha_nacimiento'])): ?>
                            <p><i class="fa-solid fa-cake-candles"></i> <span>Nacimiento:</span> <?= date('d/m/Y', strtotime($animal['fecha_nacimiento'])) ?></p>
                        <?php endif; ?>

                        <?php if (!empty($animal['tamano'])): ?>
                            <p><i class="fa-solid fa-ruler-vertical"></i> <span>Tamaño:</span> <?= ucfirst(str_replace('_',' ',$animal['tamano'])) ?></p>
                        <?php endif; ?>

                        <?php if (!empty($animal['peso'])): ?>
                            <p><i class="fa-solid fa-weight-scale"></i> <span>Peso:</span> <?= $animal['peso'] ?> kg</p>
                        <?php endif; ?>

                        <?php if (!empty($animal['estado_salud'])): ?>
                            <p><i class="fa-solid fa-heart-pulse"></i> <span>Salud:</span> <?= nl2br(htmlspecialchars($animal['estado_salud'])) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Booleanos -->
                    <div class="bloque-booleanos">
                        <p>
                            <i class="fa-solid fa-scissors <?= $animal['esterilizado'] ? 'icon-verde' : 'icon-rojo' ?>"></i>
                            <span>Esterilizado:</span> <?= $animal['esterilizado'] ? 'Sí' : 'No' ?>
                        </p>

                        <p>
                            <i class="fa-solid fa-syringe <?= $animal['vacunado'] ? 'icon-verde' : 'icon-rojo' ?>"></i>
                            <span>Vacunado:</span> <?= $animal['vacunado'] ? 'Sí' : 'No' ?>
                        </p>

                        <p>
                            <i class="fa-solid fa-bug-slash <?= $animal['desparasitado'] ? 'icon-verde' : 'icon-rojo' ?>"></i>
                            <span>Desparasitado:</span> <?= $animal['desparasitado'] ? 'Sí' : 'No' ?>
                        </p>

                        <p>
                            <i class="fa-solid fa-microchip <?= !empty($animal['microchip']) ? 'icon-verde' : 'icon-rojo' ?>"></i>
                            <span>Microchip:</span> <?= !empty($animal['microchip']) ? htmlspecialchars($animal['microchip']) : 'No' ?>
                        </p>
                    </div>

                    <!-- Datos de salud -->
                    <div class="bloque-datos">
                        <?php if (!empty($animal['estado_salud'])): ?>
                            <p><i class="fa-solid fa-heart-pulse"></i> <span>Salud:</span> <?= nl2br(htmlspecialchars($animal['estado_salud'])) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Personalidad -->
                    <?php if (!empty($personalidad)): ?>
                        <div class="personalidad">
                            <h4><i class="fa-solid fa-stars"></i> Personalidad</h4>
                            <div class="tags">
                                <?php foreach ($personalidad as $tag): ?>
                                    <span class="tag"><?= htmlspecialchars($tag) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Descripción -->
                    <?php if (!empty($animal['descripcion'])): ?>
                        <div class="descripcion-animal">
                            <?= $animal['descripcion'] ?>
                        </div>
                    <?php endif; ?>

                    <!-- CTA -->
                    <a href="formulario-adoptante.php?id=<?= $animal['id'] ?>" 
                    class="btn btn-adoptar">
                        <i class="fa-solid fa-heart"></i> 
                        Quiero adoptar a <?= htmlspecialchars($animal['nombre']) ?>
                    </a>

                </div>
            </div>

            <!-- Galería -->
            <?php if (count($fotos) > 1): ?>
                <div class="galeria-fotos-titulo">
                    <h4><i class="fa-classic fa-solid fa-images"></i> Galería de fotos de <?= htmlspecialchars($animal['nombre']) ?></h4>
                </div>
                <div class="galeria-fotos">
                    <?php foreach ($fotos as $foto): ?>
                        <img 
                            src="<?= htmlspecialchars($foto['ruta']) ?>" 
                            class="miniatura"
                            data-full="<?= htmlspecialchars($foto['ruta']) ?>"
                            alt="Foto de <?= htmlspecialchars($animal['nombre']) ?>"
                        >
                    <?php endforeach; ?>
                </div>

                <!-- Lightbox -->
                <div id="lightbox" class="lightbox">
                    <span class="cerrar">&times;</span>
                    <img class="lightbox-img" id="lightbox-img">
                </div>
            <?php endif; ?>

        </article>

    </section>

</main>

<script>
    // Script para ampliar las imágenes de la galería de fotos
    document.querySelectorAll('.miniatura').forEach(img => {
        img.addEventListener('click', () => {
            document.getElementById('lightbox-img').src = img.dataset.full;
            document.getElementById('lightbox').classList.add('activo');
        });
    });

    document.querySelector('.lightbox .cerrar').addEventListener('click', () => {
        document.getElementById('lightbox').classList.remove('activo');
    });
</script>