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

                <?php
                $cache_buster = filemtime(__DIR__ . '/../' . $animal['foto_principal']);
                ?>

                <div class="ficha-media-individual">
                    <img src="<?php echo htmlspecialchars($fotoUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '?v=' . $cache_buster ?>" alt="Foto de <?php echo $nombre; ?>" class="ficha-imagen-individual">
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
                <button id="btnQuieroApadrinar" class="btn btn-acciones-individual">
                    <i class="fa-classic fa-solid fa-handshake"></i>
                    Quiero apadrinar a <?= htmlspecialchars($animal['nombre']) ?>
                </button>
            </div>

        </article>

    </section>

    <!-- Modal Apadrinamiento -->
    <div class="modal" id="modalApadrinar" style="opacity:0; pointer-events:none;">
        <div class="modal-content">
            <span class="close" id="cerrarModal">&times;</span>

            <h2 class="modal-title">Apadrinar a <?= htmlspecialchars($animal['nombre']) ?></h2>

            <form id="formApadrinar">
                <input type="hidden" name="animal_id" value="<?= $animal['id'] ?>">

                <label>Nombre y apellidos</label>
                <input type="text" name="nombre_apellidos" required>

                <label>Email</label>
                <input type="email" name="email" required>

                <label>Teléfono</label>
                <input type="text" name="telefono">

                <label>Dirección</label>
                <input type="text" name="direccion" required>

                <label>Mensaje (opcional)</label>
                <textarea name="mensaje"></textarea>

                <button type="submit" id="btnEnviarDatos" class="modal-btn">Guardar y continuar</button>
            </form>
        </div>
    </div>

    <!-- Contenedor PayPal oculto -->
    <div id="paypal-container" style="display:none; margin-top:20px;"></div>

    <!-- Modal de agradecimiento -->
    <div class="modal" id="modalGracias" style="opacity:0; pointer-events:none;">
        <div class="modal-content">
            <span class="close" id="cerrarGracias">&times;</span>

            <h2 class="modal-title">¡Gracias por tu apoyo!</h2>

            <p id="mensajeGracias" style="color:white; font-size:1.2rem; text-align:center; margin-top:1rem;">
                <!-- Aquí insertaremos el mensaje dinámico -->
            </p>
        </div>
    </div>

</main>

<!-- SDK de PayPal -->
<script src="https://www.paypal.com/sdk/js?client-id=AdwHUt_L8WjpXKBboVmo0XtPvD8sr5CwaAP2vgHMapNbyejg80tO4nU9WyBp29jAJ5qKZS4BgcD5iFBo&vault=true&intent=subscription"></script>

<script>
    /* --------------------------------------
    1. Abrir/cerrar modal para recoger datos
    -------------------------------------- */
    const modal = document.getElementById("modalApadrinar");

    document.getElementById("btnQuieroApadrinar").onclick = function() {
        modal.style.opacity = "1";
        modal.style.pointerEvents = "auto";
    };

    document.getElementById("cerrarModal").onclick = function() {
        modal.style.opacity = "0";
        modal.style.pointerEvents = "none";
    };

    /* ------------------------------------
       2. Abrir/cerrar modal agradecimiento
    ------------------------------------ */
    function mostrarModalAgradecimiento() {

        const nombre = document.querySelector("input[name='nombre_apellidos']").value;
        const animal = "<?= htmlspecialchars($animal['nombre']) ?>";

        document.getElementById("mensajeGracias").innerHTML =
            `Gracias <strong>${nombre}</strong> por apadrinar a <strong>${animal}</strong>. Tu ayuda cambia vidas.`;

        const modal = document.getElementById("modalGracias");
        modal.style.opacity = "1";
        modal.style.pointerEvents = "auto";
    }

    document.getElementById("cerrarGracias").onclick = function() {
        const modal = document.getElementById("modalGracias");
        modal.style.opacity = "0";
        modal.style.pointerEvents = "none";
    };

    /* ---------------------------------
       3. Guardar datos en sponsors_temp
    --------------------------------- */
    document.getElementById("formApadrinar").onsubmit = function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch("<?= asset('/ajax/ajax_guardar_sponsor_temp.php') ?>", {
                method: "POST",
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.ok) {
                    window.tempSponsorId = data.temp_id;

                    document.getElementById("modalApadrinar").style.display = "none";
                    document.getElementById("paypal-container").style.display = "block";

                    iniciarBotonPayPal(data.temp_id, formData.get("animal_id"));
                } else {
                    alert("Error guardando los datos.");
                }
            });
    };

    /* -----------------------------
       4. Botón PayPal dinámico
    ------------------------------ */
    function iniciarBotonPayPal(tempId, animalId) {

        paypal.Buttons({

            style: {
                shape: 'pill',
                color: 'gold',
                layout: 'vertical',
                label: 'subscribe'
            },

            createSubscription: function(data, actions) {
                return actions.subscription.create({
                    plan_id: "P-4PX43315HR267680ENHTFGQA",
                    custom_id: "temp_" + tempId + "_animal_" + animalId
                });
            },

            onApprove: function(data, actions) {
                mostrarModalAgradecimiento();
            },

            onCancel: function() {
                fetch("<?= asset('/ajax/ajax_cancelar_sponsor_temp.php') ?>", {
                    method: "POST",
                    body: new URLSearchParams({
                        temp_id: tempId
                    })
                });
            },

            onError: function(err) {
                console.error(err);
                alert("Hubo un error con PayPal.");
            }

        }).render("#paypal-container");
    }
</script>