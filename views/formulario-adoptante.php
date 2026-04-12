<?php
require_once(__DIR__ . '/../config/database.php');
require_once 'includes/modelo_animales.php';

$idAnimal = intval($_GET['id'] ?? 0);

$animal = getAnimal($idAnimal);
if (!$animal) {
    die("Animal no encontrado.");
}
?>

<main class="layout-home">

    <section class="destacados">

        <article class="destacado-block">
            <h2 class="destacado-title">

            </h2>

            <div class="destacado-content adopta-formulario-content">

                <div class="formulario-adoptante">

                    <h2 class="form-title">Formulario de adopción para <?= htmlspecialchars($animal['nombre']) ?></h2>

                    <form action="includes/procesar-formulario.php" method="POST" class="adopta-form">

                        <?php
                        if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                        ?>
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                        <input type="hidden" name="animal_id" value="<?= $animal['id'] ?>">

                        <!-- ============================
                            1. DATOS PERSONALES
                        ============================= -->
                        <fieldset>
                            <legend>Datos personales</legend>

                            <div class="form-group">
                                <label for="nombre_completo">Nombre y apellidos</label>
                                <input type="text" id="nombre_completo" name="nombre_completo" required>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="dni_pasaporte">DNI o Pasaporte</label>
                                    <input type="text" id="dni_pasaporte" name="dni_pasaporte">
                                </div>

                                <div class="form-group">
                                    <label for="edad">Edad</label>
                                    <input type="number" id="edad" name="edad">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="direccion">Domicilio completo</label>
                                <textarea id="direccion" name="direccion"></textarea>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="ciudad">Localidad</label>
                                    <input type="text" id="ciudad" name="ciudad">
                                </div>

                                <div class="form-group">
                                    <label for="codigo_postal">Código Postal</label>
                                    <input type="text" id="codigo_postal" name="codigo_postal">
                                </div>

                                <div class="form-group">
                                    <label for="provincia">Provincia</label>
                                    <input type="text" id="provincia" name="provincia">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="telefono">Teléfono</label>
                                    <input type="text" id="telefono" name="telefono">
                                </div>

                                <div class="form-group">
                                    <label for="email">Correo electrónico</label>
                                    <input type="email" id="email" name="email">
                                </div>
                            </div>
                        </fieldset>

                        <!-- ============================
                            2. ANIMAL A ADOPTAR
                        ============================= -->
                        <fieldset>
                            <legend>Animal a adoptar</legend>

                            <div class="form-group">
                                <label>Nombre del animal</label>
                                <input type="text" value="<?= htmlspecialchars($animal['nombre']) ?>" disabled>
                                <input type="hidden" name="animal_nombre" value="<?= htmlspecialchars($animal['nombre']) ?>">
                            </div>

                            <div class="form-group">
                                <label for="motivos_adopcion">Motivos para adoptar</label>
                                <textarea id="motivos_adopcion" name="motivos_adopcion"></textarea>
                            </div>
                        </fieldset>

                        <!-- ============================
                            3. ENTORNO FAMILIAR
                        ============================= -->
                        <fieldset>
                            <legend>Entorno familiar</legend>

                            <div class="form-group">
                                <label for="personas_en_casa">¿Cuántas personas viven en casa? Indica edad y relación.</label>
                                <textarea id="personas_en_casa" name="personas_en_casa"></textarea>
                            </div>

                            <div class="form-group checkbox-group">
                                <label class="checkbox-container">
                                    <input type="checkbox" name="familia_de_acuerdo" value="1">
                                    <span class="checkmark"></span>
                                    Todos los miembros están de acuerdo con adoptar
                                </label>
                            </div>

                            <div class="form-group">
                                <label for="responsable_principal">Responsable principal del animal</label>
                                <input type="text" id="responsable_principal" name="responsable_principal">
                            </div>

                            <div class="form-group checkbox-group">
                                <label class="checkbox-container">
                                    <input type="checkbox" name="ninos_tuvieron_animales" value="1">
                                    <span class="checkmark"></span>
                                    Los niños han tenido animales anteriormente
                                </label>
                            </div>

                            <div class="form-group">
                                <label for="convivencia_ninos_opinion">¿Crees que la convivencia entre animales y niños es positiva?</label>
                                <textarea id="convivencia_ninos_opinion" name="convivencia_ninos_opinion"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="plan_familia_impacto">¿Qué ocurrirá con el animal si tienes hijos en el futuro?</label>
                                <textarea id="plan_familia_impacto" name="plan_familia_impacto"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="alergias_en_casa">¿Hay alergias en casa?</label>
                                <textarea id="alergias_en_casa" name="alergias_en_casa"></textarea>
                            </div>

                            <div class="form-group checkbox-group">
                                <label class="checkbox-container">
                                    <input type="checkbox" name="capacidad_economica" value="1">
                                    <span class="checkmark"></span>
                                    Puedo asumir la manutención del animal
                                </label>
                            </div>

                            <div class="form-group checkbox-group">
                                <label class="checkbox-container">
                                    <input type="checkbox" name="asumir_gastos_vet" value="1">
                                    <span class="checkmark"></span>
                                    Puedo asumir gastos veterinarios en caso de enfermedad
                                </label>
                            </div>
                        </fieldset>

                        <!-- ============================
                            4. ANTECEDENTES CON ANIMALES
                        ============================= -->
                        <fieldset>
                            <legend>Antecedentes con animales</legend>

                            <div class="form-group checkbox-group">
                                <label class="checkbox-container">
                                    <input type="checkbox" name="ha_tenido_animales" value="1">
                                    <span class="checkmark"></span>
                                    He tenido animales anteriormente
                                </label>
                            </div>

                            <div class="form-group">
                                <label for="historia_animales_previos">Historia de tus animales anteriores</label>
                                <textarea id="historia_animales_previos" name="historia_animales_previos"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="otros_animales">¿Tienes otros animales actualmente?</label>
                                <textarea id="otros_animales" name="otros_animales"></textarea>
                            </div>

                            <div class="form-group checkbox-group">
                                <label class="checkbox-container">
                                    <input type="checkbox" name="chip_esterilizados" value="1">
                                    <span class="checkmark"></span>
                                    Chipados y/o esterilizados
                                </label>
                            </div>

                            <div class="form-group checkbox-group">
                                <label class="checkbox-container">
                                    <input type="checkbox" name="vacunas_en_regla" value="1">
                                    <span class="checkmark"></span>
                                    Cartilla y vacunas en regla
                                </label>
                            </div>
                        </fieldset>

                        <!-- ============================
                            5. VIVIENDA Y ENTORNO
                        ============================= -->
                        <fieldset>
                            <legend>Vivienda y entorno</legend>

                            <div class="form-group">
                                <label for="tipo_vivienda">Tipo de vivienda</label>
                                <select id="tipo_vivienda" name="tipo_vivienda">
                                    <option value="">Selecciona...</option>
                                    <option value="piso">Piso</option>
                                    <option value="casa_ciudad">Casa en ciudad</option>
                                    <option value="casa_rural">Casa en entorno rural</option>
                                    <option value="otros">Otros</option>
                                </select>
                            </div>

                            <div class="form-group checkbox-group">
                                <label class="checkbox-container">
                                    <input type="checkbox" name="vivienda_propiedad" value="1">
                                    <span class="checkmark"></span>
                                    Vivienda en propiedad
                                </label>
                            </div>

                            <div class="form-group checkbox-group">
                                <label class="checkbox-container">
                                    <input type="checkbox" name="permite_animales_en_alquiler" value="1">
                                    <span class="checkmark"></span>
                                    En caso de alquiler, se permiten animales
                                </label>
                            </div>

                            <div class="form-group">
                                <label for="patio_jardin_medidas">Patio o jardín (medidas de seguridad)</label>
                                <textarea id="patio_jardin_medidas" name="patio_jardin_medidas"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="interior_o_exterior">¿El animal viviría dentro o fuera?</label>
                                <input type="text" id="interior_o_exterior" name="interior_o_exterior">
                            </div>
                        </fieldset>

                        <!-- ============================
                            6. ESTADO LABORAL Y DEDICACIÓN
                        ============================= -->
                        <fieldset>
                            <legend>Estado laboral y dedicación</legend>

                            <div class="form-group">
                                <label for="profesion_situacion">Profesión y situación laboral</label>
                                <input type="text" id="profesion_situacion" name="profesion_situacion">
                            </div>

                            <div class="form-group">
                                <label for="quien_asume_gastos">Si estás en paro, ¿quién asumiría los gastos?</label>
                                <input type="text" id="quien_asume_gastos" name="quien_asume_gastos">
                            </div>

                            <div class="form-group">
                                <label for="tiempo_pasear">Tiempo disponible para pasear al perro</label>
                                <input type="text" id="tiempo_pasear" name="tiempo_pasear">
                            </div>

                            <div class="form-group">
                                <label for="horas_solo">Horas que estaría solo en casa</label>
                                <input type="text" id="horas_solo" name="horas_solo">
                            </div>

                            <div class="form-group">
                                <label for="lugares_paseo">¿Dónde pasearías con tu perro?</label>
                                <textarea id="lugares_paseo" name="lugares_paseo"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="mudanza_poblacion">Si te mudas a una población cercana</label>
                                <textarea id="mudanza_poblacion" name="mudanza_poblacion"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="mudanza_pais">Si te mudas a otro país</label>
                                <textarea id="mudanza_pais" name="mudanza_pais"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="vacaciones_cuidado">¿Quién cuida del animal en vacaciones?</label>
                                <textarea id="vacaciones_cuidado" name="vacaciones_cuidado"></textarea>
                            </div>
                        </fieldset>

                        <!-- ============================
                            7. OTRA INFORMACIÓN
                        ============================= -->
                        <fieldset>
                            <legend>Otra información</legend>

                            <div class="form-group">
                                <label for="por_que_adoptar">¿Por qué adoptar y no comprar?</label>
                                <textarea id="por_que_adoptar" name="por_que_adoptar"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="tiempo_busqueda">¿Cuánto tiempo llevas buscando adoptar?</label>
                                <input type="text" id="tiempo_busqueda" name="tiempo_busqueda">
                            </div>

                            <div class="form-group">
                                <label for="como_conocio">¿Cómo conociste la asociación?</label>
                                <input type="text" id="como_conocio" name="como_conocio">
                            </div>

                            <div class="form-group checkbox-group">
                                <label class="checkbox-container">
                                    <input type="checkbox" name="conoce_condiciones" value="1">
                                    <span class="checkmark"></span>
                                    Conozco las condiciones de adopción
                                </label>
                            </div>

                            <div class="form-group">
                                <label for="firma_nombre_dni">Firma (nombre y DNI)</label>
                                <input type="text" id="firma_nombre_dni" name="firma_nombre_dni">
                            </div>
                        </fieldset>

                        <button type="submit" class="btn">Enviar formulario</button>

                    </form>
                </div>

            </div>

        </article>

    </section>

</main>