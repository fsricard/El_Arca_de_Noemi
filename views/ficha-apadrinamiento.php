<?php
require_once(__DIR__ . '/../config/database.php');
require_once 'includes/modelo_animales.php';

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

// Helper para calcular tiempo transcurrido desde una fecha
function calcularTiempoEnSantuario(?string $fechaIngreso)
{
    if (empty($fechaIngreso)) {
        return 'Fecha de ingreso desconocida';
    }

    try {
        $inicio = new DateTime($fechaIngreso);
        $ahora = new DateTime();
        $diff = $ahora->diff($inicio);

        $partes = [];
        if ($diff->y > 0) {
            $partes[] = $diff->y . ' año' . ($diff->y > 1 ? 's' : '');
        }
        if ($diff->m > 0) {
            $partes[] = $diff->m . ' mes' . ($diff->m > 1 ? 'es' : '');
        }
        if ($diff->y === 0 && $diff->m === 0) {
            $partes[] = $diff->d . ' día' . ($diff->d > 1 ? 's' : '');
        }

        return implode(' y ', $partes);
    } catch (Exception $e) {
        return 'Fecha inválida';
    }
}

// Recuperar id desde GET con casting a entero
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$animal = false;
if ($id > 0) {
    $sql = "SELECT id, nombre, especie_id, raza_id, fecha_ingreso, foto_principal, mini_descripcion, historia, estado
            FROM animals_sponsor
            WHERE id = :id
            LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $animal = $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
}

// Si no existe o no está activo se puede mostrar la 404
if (!$animal || ($animal['estado'] ?? '') !== 'activo') {
    http_response_code(404);
    // Opcional: mostrar una plantilla de "no encontrado"
    echo '<main class="layout-home"><section class="destacados"><article class="destacado-block"><h2 class="destacado-title">Animal no encontrado</h2><div class="destacado-content">El apadrinamiento solicitado no existe o no está disponible.</div></article></section></main>';
    exit;
}

// Asignar global para que mostrarTextoPersonalizado funcione
$GLOBALS['nombre_animal'] = $animal['nombre'] ?? '';

// Obtener especie y raza usando las funciones globales
$especie = null;
$raza = null;
if (!empty($animal['especie_id'])) {
    $especie = getEspecie((int)$animal['especie_id']);
}
if (!empty($animal['raza_id'])) {
    $raza = getRaza((int)$animal['raza_id']);
}

// Preparar variables para la plantilla
$nombre = htmlspecialchars($animal['nombre'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$nombreEspecie = strtolower($especie['nombre'] ?? '');
$icono = $iconosEspecie[$nombreEspecie] ?? 'fa-paw';
$nombreRaza = htmlspecialchars($raza['nombre'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$fechaIngreso = $animal['fecha_ingreso'] ?? null;
$tiempoEnSantuario = calcularTiempoEnSantuario($fechaIngreso);
$fotoPrincipal = $animal['foto_principal'] ?? null;
$miniDescripcion = htmlspecialchars($animal['mini_descripcion'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$historiaRaw = $animal['historia'] ?? '';
$historiaSegura = strip_tags($historiaRaw, '<p><br><strong><em><ul><ol><li><a><img><h2><h3><blockquote>');

// Ruta de la imagen
$fotoUrl = $fotoPrincipal ? ltrim($fotoPrincipal, '/') : '/assets/img/default-animal.jpg';

// Calculamos los padrinos de el animal
$padrinosActivos = 0;
$metaPadrinos = 10;

try {
    $sql = "
        SELECT COUNT(*) AS total
        FROM sponsors_animals
        WHERE animal_id = :id
          AND estado = 'activo'
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $padrinosActivos = (int) ($row['total'] ?? 0);
} catch (Exception $e) {
    $padrinosActivos = 0;
}

// Calcular porcentaje para la barra
$porcentaje = min(100, ($padrinosActivos / $metaPadrinos) * 100);

// Texto dinámico
if ($padrinosActivos >= $metaPadrinos) {
    $textoMeta = "$nombre es un animal muy querido por todos vosotros. ¡Gracias por tanto cariño!";
} else {
    $restantes = $metaPadrinos - $padrinosActivos;
    $textoMeta = "Nuestra meta es llegar a $metaPadrinos padrinos. Solo faltan $restantes para conseguirlo.";
}
?>

<main class="layout-home">

    <section class="destacados">

        <article class="destacado-block">
            <h2 class="destacado-title">
                <i class="fa-classic fa-solid fa-hands-holding-heart"></i>
                Apadrina a <strong><?php echo $nombre; ?></strong>, tu granito de arena puede cambiar una vida.
            </h2>

            <div class="destacado-content apadrinamiento-ficha-individual">

                <div class="ficha-media-individual">
                    <img src="<?php echo htmlspecialchars($fotoUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>" alt="Foto de <?php echo $nombre; ?>" class="ficha-imagen-individual">
                </div>

                <div class="ficha-datos-individual">
                    <h3><i class="fa <?php echo $icono; ?>"></i> <strong><?php echo $nombre; ?></strong></h3>

                    <div class="ficha-datos-individual-basicos">
                        <p class="ficha-especie-individual">Especie: <strong><?php echo htmlspecialchars($especie['nombre'] ?? 'Desconocida', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></strong></p>
                        <p class="ficha-raza-individual">Raza: <strong><?php echo $nombreRaza ?: 'No especificada'; ?></strong></p>
                        <p class="ficha-ingreso-individual">En el santuario desde: <strong><?php echo $fechaIngreso ? htmlspecialchars($fechaIngreso, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : 'Desconocida'; ?></strong> (<?php echo $tiempoEnSantuario; ?>)</p>
                    </div>

                    <?php if (!empty($miniDescripcion)): ?>
                        <p class="ficha-mini-individual"><?php echo $miniDescripcion; ?></p>
                    <?php endif; ?>

                    <div class="ficha-padrinos-individual">
                        <div class="padrinos-resumen">
                            <i class="fa fa-hands-holding-heart padrinos-icon" aria-hidden="true"></i>
                            <div class="padrinos-texto">
                                <p class="padrinos-numero"><strong><?= $padrinosActivos ?></strong></p>
                                <p class="padrinos-label">
                                    padrino<?= $padrinosActivos === 1 ? '' : 's' ?> activos
                                </p>
                            </div>
                        </div>

                        <div class="padrinos-meta-texto">
                            <?= htmlspecialchars($textoMeta, ENT_QUOTES, 'UTF-8') ?>
                        </div>

                        <div class="padrinos-progreso">
                            <div class="progreso-barra <?= $padrinosActivos >= $metaPadrinos ? 'progreso-completo' : '' ?>"
                                style="width: <?= $porcentaje ?>%;">
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="ficha-historia-individual">
                <h3><i class="fa-classic fa-solid fa-book-medical"></i> Historia</h3>
                <div class="historia-content-individual">
                    <?php echo $historiaSegura; ?>
                </div>
            </div>

            <div class="ficha-acciones-individual">

                <!-- Botón visual idéntico al tuyo -->
                <button id="btn-apadrinar" class="btn btn-acciones-individual">
                    <i class="fa-classic fa-solid fa-handshake"></i>
                    Quiero apadrinar a <?= htmlspecialchars($nombre) ?>
                </button>

                <!-- Contenedor donde PayPal inyectará su ventana -->
                <div id="paypal-container" style="margin-top:15px; display:none;"></div>

            </div>

        </article>

    </section>

</main>

<!-- SDK de PayPal -->
<script src="https://www.paypal.com/sdk/js?client-id=AdwHUt_L8WjpXKBboVmo0XtPvD8sr5CwaAP2vgHMapNbyejg80tO4nU9WyBp29jAJ5qKZS4BgcD5iFBo&vault=true&intent=subscription"></script>

<script>
    document.getElementById('btn-apadrinar').addEventListener('click', function() {

        document.getElementById('btn-apadrinar').style.display = 'none';
        document.getElementById('paypal-container').style.display = 'block';

        paypal.Buttons({
            style: {
                shape: 'pill',
                color: 'gold',
                layout: 'vertical',
                label: 'subscribe'
            },

            createSubscription: function(data, actions) {
                return actions.subscription.create({
                    plan_id: "P-9VB192991F117152NNHSAUYA",
                    custom_id: "animal_<?= $id ?>"
                });
            },

            onApprove: function(data, actions) {

                fetch("<?= asset('/admin/modulos/apadrinamientos/paypal_create_subscription.php') ?>", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({
                            subscription_id: data.subscriptionID,
                            animal_id: <?= $id ?>
                        })
                    })
                    .then(r => r.json())
                    .then(res => {
                        if (res.ok) {
                            window.location.href = "<?= asset('/gracias-apadrinamiento.php') ?>";
                        } else {
                            alert("Hubo un problema registrando la suscripción.");
                        }
                    });
            },

            onError: function(err) {
                console.error(err);
                alert("Hubo un error con PayPal. Inténtalo de nuevo.");
            }

        }).render('#paypal-container');

    });
</script>