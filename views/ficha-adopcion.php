<?php
require_once(__DIR__ . '/../config/database.php');
require_once 'includes/modelo_animales.php';

$idAnimal = intval($_GET['id'] ?? 0);

$animal = getAnimal($idAnimal);
if (!$animal) {
    die("Animal no encontrado.");
}

// Obtener raza + especie en una sola llamada
$razaEspecie = getRazaConEspecie((int)$animal['id_raza']);
$raza    = $razaEspecie['raza'] ?? null;
$especie = $razaEspecie['especie'] ?? null;

$fotos = getFotos($animal['id']);

// Iconos por especie (Font Awesome Pro 7.0.1)
$iconosEspecie = [
    'perro'   => 'fa-dog',
    'gato'    => 'fa-cat',
    'conejo'  => 'fa-rabbit-running',
    'ave'     => 'fa-dove',
    'hurón'   => 'fa-otter',
    'huron'   => 'fa-otter',
    'tortuga' => 'fa-turtle',
];

$nombreEspecie = strtolower($especie['nombre'] ?? '');
$icono = $iconosEspecie[$nombreEspecie] ?? 'fa-paw';

// Foto principal: preferir la marcada, si no usar la primera disponible
$fotoPrincipal = null;
foreach ($fotos as $foto) {
    if (!empty($foto['es_principal'])) {
        $fotoPrincipal = $foto['ruta'];
        break;
    }
}
if (!$fotoPrincipal && !empty($fotos[0]['ruta'])) {
    $fotoPrincipal = $fotos[0]['ruta'];
}

// Calcular días en refugio (si hay fecha_rescate)
$tiempoRefugio = null;
if (!empty($animal['fecha_rescate'])) {
    try {
        $fechaRescate = new DateTime($animal['fecha_rescate']);
        $hoy = new DateTime();
        $tiempoRefugio = $fechaRescate->diff($hoy)->days;
    } catch (Exception $e) {
        $tiempoRefugio = null;
    }
}

// Bloque personalidad (detección simple por palabras clave)
$palabrasClave = [
    'cariñoso','cariñosa','juguetón','juguetona','tranquilo','tranquila',
    'sociable','activo','activa','obediente','nervioso','nerviosa',
    'tímido','tímida','timido','timida','bueno con niños','bueno con perros','bueno con gatos',
    'personas mayores', 'otros animales'
];

$descripcion = mb_strtolower($animal['descripcion'] ?? '', 'UTF-8');
$personalidad = [];
foreach ($palabrasClave as $palabra) {
    if ($palabra === '') continue;
    if (mb_stripos($descripcion, $palabra, 0, 'UTF-8') !== false) {
        $personalidad[] = mb_convert_case($palabra, MB_CASE_TITLE, "UTF-8");
    }
}

// Normalizar salidas seguras
$nombreAnimal = htmlspecialchars($animal['nombre']);
$razaNombre   = htmlspecialchars($raza['nombre'] ?? 'Raza desconocida');
$especieNombre= htmlspecialchars($especie['nombre'] ?? 'Especie desconocida');
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

                    <!-- Identidad y metadatos -->
                    <div class="ficha-identidad">
                        <h3 class="ficha-nombre">
                            <i class="fa-solid <?= $icono ?>"></i>
                            <?= $nombreAnimal ?>
                        </h3>

                        <p class="ficha-especie-raza">
                            <i class="fa-solid fa-paw"></i>
                            <span class="especie"><?= $especieNombre ?></span> — <span class="raza"><?= $razaNombre ?></span>
                        </p>
                    </div>

                    <!-- Fechas discretas (estilo discreto) -->
                    <div class="bloque-fechas">
                        <?php if (!empty($animal['fecha_ingreso'])): ?>
                            <p>Ingreso: <span><?= date('d/m/Y', strtotime($animal['fecha_ingreso'])) ?></span></p>
                        <?php endif; ?>

                        <?php if ($tiempoRefugio !== null): ?>
                            <p>Lleva <span><?= intval($tiempoRefugio) ?></span> días en el santuario.</p>
                        <?php endif; ?>
                    </div>

                    <div class="adopta-bloque-datos">
                        <!-- Datos condicionales -->
                        <div class="bloque-datos">
                            <?php if (!empty($animal['sexo'])): ?>
                                <p><i class="fa-solid fa-venus-mars"></i> <span class="label">Sexo:</span> <span class="valor"><?= htmlspecialchars(ucfirst($animal['sexo'])) ?></span></p>
                            <?php endif; ?>

                            <?php if (!empty($animal['edad'])): ?>
                                <p><i class="fa-solid fa-hourglass-half"></i> <span class="label">Edad:</span> <span class="valor"><?= htmlspecialchars($animal['edad']) ?></span></p>
                            <?php endif; ?>

                            <?php if (!empty($animal['fecha_nacimiento'])): ?>
                                <p><i class="fa-solid fa-cake-candles"></i> <span class="label">Nacimiento:</span> <span class="valor"><?= date('d/m/Y', strtotime($animal['fecha_nacimiento'])) ?></span></p>
                            <?php endif; ?>

                            <?php if (!empty($animal['tamano'])): ?>
                                <p><i class="fa-solid fa-ruler-vertical"></i> <span class="label">Tamaño:</span> <span class="valor"><?= htmlspecialchars(ucfirst(str_replace('_',' ',$animal['tamano']))) ?></span></p>
                            <?php endif; ?>

                            <?php if (!empty($animal['peso'])): ?>
                                <p><i class="fa-solid fa-weight-scale"></i> <span class="label">Peso:</span> <span class="valor"><?= htmlspecialchars($animal['peso']) ?> kg</span></p>
                            <?php endif; ?>
                        </div>

                        <!-- Datos booleanos -->
                        <div class="bloque-booleanos">
                            <p>
                                <i class="fa-solid fa-scissors <?= !empty($animal['esterilizado']) ? 'icon-verde' : 'icon-rojo' ?>"></i>
                                <span class="label">Esterilizado:</span> <span class="valor"><?= !empty($animal['esterilizado']) ? 'Sí' : 'No' ?></span>
                            </p>

                            <p>
                                <i class="fa-solid fa-syringe <?= !empty($animal['vacunado']) ? 'icon-verde' : 'icon-rojo' ?>"></i>
                                <span class="label">Vacunado:</span> <span class="valor"><?= !empty($animal['vacunado']) ? 'Sí' : 'No' ?></span>
                            </p>

                            <p>
                                <i class="fa-solid fa-bug-slash <?= !empty($animal['desparasitado']) ? 'icon-verde' : 'icon-rojo' ?>"></i>
                                <span class="label">Desparasitado:</span> <span class="valor"><?= !empty($animal['desparasitado']) ? 'Sí' : 'No' ?></span>
                            </p>

                            <p>
                                <i class="fa-solid fa-microchip <?= !empty($animal['microchip']) ? 'icon-verde' : 'icon-rojo' ?>"></i>
                                <span class="label">Microchip:</span> <span class="valor"><?= !empty($animal['microchip']) ? htmlspecialchars($animal['microchip']) : 'No' ?></span>
                            </p>
                        </div>
                    </div>

                    <!-- Estado de salud (si existe) -->
                    <?php if (!empty($animal['estado_salud'])): ?>
                        <div class="descripcion-animal descripcion-salud">
                            <h4><i class="fa-solid fa-heart-pulse"></i> Estado de salud</h4>
                            <div class="texto-salud"><?= nl2br(htmlspecialchars($animal['estado_salud'])) ?></div>
                        </div>
                    <?php endif; ?>

                    <!-- Personalidad (etiquetas) -->
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

                    <!-- Descripción general -->
                    <?php if (!empty($animal['descripcion'])): ?>
                        <div class="descripcion-animal descripcion-general">
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