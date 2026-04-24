<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../config/funciones.php';

// Si no está logueado redirigimos al login
if (!isLoggedIn()) {
    header("Location: index.php");
    exit;
}

// Obtener ID del formulario
$idFormulario = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($idFormulario <= 0) {
    echo "<div class='container'><div class='alert alert-danger'>ID de formulario inválido.</div></div>";
    include('../../includes/footer.php');
    exit;
}

// Obtener datos del formulario
$stmt = $pdo->prepare("SELECT * FROM adoptantes_formulario WHERE id = :id");
$stmt->execute([':id' => $idFormulario]);
$form = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$form) {
    echo "<div class='container'><div class='alert alert-danger'>Formulario no encontrado.</div></div>";
    include('../../includes/footer.php');
    exit;
}

$pagina = 'sistema_adopciones_editar_formulario';

include('../../includes/header.php');
?>

<main>
    <section>
        <div class="container">

            <h2>Estás editando un adoptante proveniente del FrontEnd</h2>

            <form id="form-editar" method="POST">
                <input type="hidden" name="id_formulario" value="<?= $form['id'] ?>">

                <!-- Datos personales -->
                <button class="accordion-header">Datos personales</button>
                <div class="accordion-content">

                    <div class="form-group">
                        <label>Nombre completo</label>
                        <input type="text" name="nombre_completo" class="form-control"
                            value="<?= htmlspecialchars($form['nombre_completo']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>DNI / Pasaporte</label>
                        <input type="text" name="dni_pasaporte" class="form-control"
                            value="<?= htmlspecialchars($form['dni_pasaporte']) ?>">
                    </div>

                    <div class="form-group">
                        <label>Edad</label>
                        <input type="text" name="edad" class="form-control"
                            value="<?= htmlspecialchars($form['edad']) ?>">
                    </div>

                </div>

                <!-- Contacto -->
                <button class="accordion-header">Contacto</button>
                <div class="accordion-content">

                    <div class="form-group">
                        <label>Teléfono</label>
                        <input type="text" name="telefono" class="form-control"
                            value="<?= htmlspecialchars($form['telefono']) ?>">
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control"
                            value="<?= htmlspecialchars($form['email']) ?>">
                    </div>

                </div>

                <!-- Dirección -->
                <button class="accordion-header">Dirección</button>
                <div class="accordion-content">

                    <div class="form-group">
                        <label>Dirección</label>
                        <input type="text" name="direccion" class="form-control"
                            value="<?= htmlspecialchars($form['direccion']) ?>">
                    </div>

                    <div class="form-group">
                        <label>Ciudad</label>
                        <input type="text" name="ciudad" class="form-control"
                            value="<?= htmlspecialchars($form['ciudad']) ?>">
                    </div>

                    <div class="form-group">
                        <label>Provincia</label>
                        <input type="text" name="provincia" class="form-control"
                            value="<?= htmlspecialchars($form['provincia']) ?>">
                    </div>

                    <div class="form-group">
                        <label>Código postal</label>
                        <input type="text" name="codigo_postal" class="form-control"
                            value="<?= htmlspecialchars($form['codigo_postal']) ?>">
                    </div>

                </div>

                <!-- Información del animal -->
                <button class="accordion-header">Información del animal</button>
                <div class="accordion-content">

                    <div class="form-group">
                        <label>Nombre del animal</label>
                        <input type="text" name="animal_nombre" class="form-control"
                            value="<?= htmlspecialchars($form['animal_nombre']) ?>">
                    </div>

                    <div class="form-group">
                        <label>Motivos para adoptar</label>
                        <textarea name="motivos_adopcion" class="form-control"><?= htmlspecialchars($form['motivos_adopcion']) ?></textarea>
                    </div>

                </div>

                <!-- Situación familiar -->
                <button class="accordion-header">Situación familiar</button>
                <div class="accordion-content">

                    <div class="form-group">
                        <label>Personas en casa</label>
                        <input type="text" name="personas_en_casa" class="form-control"
                            value="<?= htmlspecialchars($form['personas_en_casa']) ?>">
                    </div>

                    <div class="checkbox-row">
                        <label>La familia está de acuerdo</label>
                        <input type="checkbox" name="familia_de_acuerdo"
                            <?= $form['familia_de_acuerdo'] ? 'checked' : '' ?>>
                    </div>

                    <div class="form-group">
                        <label>Responsable principal</label>
                        <input type="text" name="responsable_principal" class="form-control"
                            value="<?= htmlspecialchars($form['responsable_principal']) ?>">
                    </div>

                    <div class="checkbox-row">
                        <label>Los niños han tenido animales</label>
                        <input type="checkbox" name="ninos_tuvieron_animales"
                            <?= $form['ninos_tuvieron_animales'] ? 'checked' : '' ?>>
                    </div>

                    <div class="form-group">
                        <label>Opinión sobre convivencia con niños</label>
                        <textarea name="convivencia_ninos_opinion" class="form-control"><?= htmlspecialchars($form['convivencia_ninos_opinion']) ?></textarea>
                    </div>

                </div>

                <!-- Vivienda -->
                <button class="accordion-header">Vivienda</button>
                <div class="accordion-content">

                    <div class="form-group">
                        <label>Tipo de vivienda</label>
                        <input type="text" name="tipo_vivienda" class="form-control"
                            value="<?= htmlspecialchars($form['tipo_vivienda']) ?>">
                    </div>

                    <div class="checkbox-row">
                        <label>Vivienda en propiedad</label>
                        <input type="checkbox" name="vivienda_propiedad"
                            <?= $form['vivienda_propiedad'] ? 'checked' : '' ?>>
                    </div>

                    <div class="checkbox-row">
                        <label>El alquiler permite animales</label>
                        <input type="checkbox" name="permite_animales_en_alquiler"
                            <?= $form['permite_animales_en_alquiler'] ? 'checked' : '' ?>>
                    </div>

                    <div class="form-group">
                        <label>Patio / jardín y medidas</label>
                        <textarea name="patio_jardin_medidas" class="form-control"><?= htmlspecialchars($form['patio_jardin_medidas']) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Interior o exterior</label>
                        <input type="text" name="interior_o_exterior" class="form-control"
                            value="<?= htmlspecialchars($form['interior_o_exterior']) ?>">
                    </div>

                </div>

                <!-- Experiencia previa -->
                <button class="accordion-header">Experiencia previa</button>
                <div class="accordion-content">

                    <div class="checkbox-row">
                        <label>Ha tenido animales antes</label>
                        <input type="checkbox" name="ha_tenido_animales"
                            <?= $form['ha_tenido_animales'] ? 'checked' : '' ?>>
                    </div>

                    <div class="form-group">
                        <label>Historia con animales previos</label>
                        <textarea name="historia_animales_previos" class="form-control"><?= htmlspecialchars($form['historia_animales_previos']) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Otros animales en casa</label>
                        <textarea name="otros_animales" class="form-control"><?= htmlspecialchars($form['otros_animales']) ?></textarea>
                    </div>

                    <div class="checkbox-row">
                        <label>Chip / esterilizados</label>
                        <input type="checkbox" name="chip_esterilizados"
                            <?= $form['chip_esterilizados'] ? 'checked' : '' ?>>
                    </div>

                    <div class="checkbox-row">
                        <label>Vacunas en regla</label>
                        <input type="checkbox" name="vacunas_en_regla"
                            <?= $form['vacunas_en_regla'] ? 'checked' : '' ?>>
                    </div>

                </div>

                <!-- Rutinas y cuidados -->
                <button class="accordion-header">Rutinas y cuidados</button>
                <div class="accordion-content">

                    <div class="form-group">
                        <label>Profesión / situación</label>
                        <input type="text" name="profesion_situacion" class="form-control"
                            value="<?= htmlspecialchars($form['profesion_situacion']) ?>">
                    </div>

                    <div class="form-group">
                        <label>Quién asume los gastos</label>
                        <input type="text" name="quien_asume_gastos" class="form-control"
                            value="<?= htmlspecialchars($form['quien_asume_gastos']) ?>">
                    </div>

                    <div class="checkbox-row">
                        <label>Capacidad económica suficiente</label>
                        <input type="checkbox" name="capacidad_economica"
                            <?= $form['capacidad_economica'] ? 'checked' : '' ?>>
                    </div>

                    <div class="checkbox-row">
                        <label>Asume gastos veterinarios</label>
                        <input type="checkbox" name="asumir_gastos_vet"
                            <?= $form['asumir_gastos_vet'] ? 'checked' : '' ?>>
                    </div>

                    <div class="form-group">
                        <label>Tiempo para pasear</label>
                        <input type="text" name="tiempo_pasear" class="form-control"
                            value="<?= htmlspecialchars($form['tiempo_pasear']) ?>">
                    </div>

                    <div class="form-group">
                        <label>Horas que estará solo</label>
                        <input type="text" name="horas_solo" class="form-control"
                            value="<?= htmlspecialchars($form['horas_solo']) ?>">
                    </div>

                    <div class="form-group">
                        <label>Lugares de paseo</label>
                        <textarea name="lugares_paseo" class="form-control"><?= htmlspecialchars($form['lugares_paseo']) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Mudanza a otra población</label>
                        <input type="text" name="mudanza_poblacion" class="form-control"
                            value="<?= htmlspecialchars($form['mudanza_poblacion']) ?>">
                    </div>

                    <div class="form-group">
                        <label>Mudanza a otro país</label>
                        <input type="text" name="mudanza_pais" class="form-control"
                            value="<?= htmlspecialchars($form['mudanza_pais']) ?>">
                    </div>

                    <div class="form-group">
                        <label>Vacaciones y cuidado del animal</label>
                        <textarea name="vacaciones_cuidado" class="form-control"><?= htmlspecialchars($form['vacaciones_cuidado']) ?></textarea>
                    </div>

                </div>

                <!-- Motivación -->
                <button class="accordion-header">Motivación</button>
                <div class="accordion-content">

                    <div class="form-group">
                        <label>¿Por qué adoptar?</label>
                        <textarea name="por_que_adoptar" class="form-control"><?= htmlspecialchars($form['por_que_adoptar']) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Tiempo buscando adoptar</label>
                        <input type="text" name="tiempo_busqueda" class="form-control"
                            value="<?= htmlspecialchars($form['tiempo_busqueda']) ?>">
                    </div>

                    <div class="form-group">
                        <label>¿Cómo nos conoció?</label>
                        <input type="text" name="como_conocio" class="form-control"
                            value="<?= htmlspecialchars($form['como_conocio']) ?>">
                    </div>

                    <div class="checkbox-row">
                        <label>Conoce las condiciones de adopción</label>
                        <input type="checkbox" name="conoce_condiciones"
                            <?= $form['conoce_condiciones'] ? 'checked' : '' ?>>
                        
                    </div>

                </div>

                <!-- Firma -->
                <button class="accordion-header">Firma</button>
                <div class="accordion-content">

                    <div class="form-group">
                        <label>Firma (nombre y DNI)</label>
                        <input type="text" name="firma_nombre_dni" class="form-control"
                            value="<?= htmlspecialchars($form['firma_nombre_dni']) ?>">
                    </div>

                </div>

                <hr>

                <!-- Botones -->
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-save"></i> Guardar cambios
                </button>

                <?php if ($form['procesado'] == 0): ?>
                    <button type="button" class="btn btn-success"
                        onclick="activarFormulario(<?= $form['id'] ?>)">
                        <i class="fa-solid fa-check"></i> Activar adoptante
                    </button>
                <?php endif; ?>

                <a href="sistema_adopciones_listado_adoptantes.php" class="btn btn-volver">
                    <i class="fa-solid fa-arrow-left"></i> Volver
                </a>

            </form>

        </div>
    </section>
</main>

<script>
    // Script para activar adoptantes
    function activarFormulario(idFormulario) {

        if (!confirm("¿Activar este adoptante y convertirlo en adoptante real?")) {
            return;
        }

        fetch("convertir_formulario_a_adoptante.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "id_formulario=" + encodeURIComponent(idFormulario)
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === "success") {
                    alert("Adoptante activado correctamente");
                    location.reload();
                } else {
                    alert("Error: " + data.message);
                    console.error(data.debug);
                }
            })
            .catch(err => console.error("Error:", err));
    }
    
    // Script para el acordeón
    document.addEventListener('DOMContentLoaded', () => {

        const headers = document.querySelectorAll(".accordion-header");
        const contents = document.querySelectorAll(".accordion-content");

        if (!headers.length) return;

        // 1) Cerrar TODAS las secciones al cargar
        contents.forEach(c => c.style.display = 'none');
        headers.forEach(h => h.classList.remove('active'));

        // 2) Abrir SOLO la primera
        const firstHeader = headers[0];
        const firstContent = contents[0];

        firstHeader.classList.add('active');
        firstContent.style.display = 'block';

        // 3) Comportamiento normal del acordeón
        headers.forEach(header => {
            header.addEventListener("click", (e) => {
                e.preventDefault();
                e.stopPropagation();

                // Cerrar todas
                headers.forEach(h => h.classList.remove("active"));
                contents.forEach(c => c.style.display = "none");

                // Abrir la seleccionada
                header.classList.add("active");
                header.nextElementSibling.style.display = "block";
            });
        });

    });
    
    // Script para guardar el formulario
    document.getElementById("form-editar").addEventListener("submit", function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch("ajax/guardar_formulario_editado.php", {
                method: "POST",
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.status === "success") {
                    alert("Cambios guardados correctamente");
                    location.reload();
                } else {
                    alert("Error: " + data.message);
                    console.error(data.debug);
                }
            })
            .catch(err => console.error(err));
    });
</script>

<?php include('../../includes/footer.php');
