<?php
require_once(__DIR__ . '/../../../config/funciones.php');

// Helper para mostrar valores vacíos
function v($campo)
{
    return isset($campo) && trim($campo) !== '' ? htmlspecialchars($campo) : '—';
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 0.8rem;
            color: #333;
            margin: 0;
            padding: 25px;
        }

        h1 {
            text-align: center;
            color: #b3d5ee;
            font-size: 1.8rem;
            margin-bottom: 5px;
            text-shadow: 2px 2px 0 #2A6432;
        }

        h2 {
            color: #B3D5EE;
            background: #BDC453;
            padding: 6px 10px;
            font-size: 1.2rem;
            margin-top: 25px;
            border-left: 4px solid #c27ba0;
            text-shadow: 2px 2px 0 #2A6432;
        }

        .logo {
            text-align: center;
            margin-bottom: 10px;
        }

        .logo img {
            width: 140px;
        }

        .campo {
            margin-bottom: 6px;
        }

        .label {
            font-weight: bold;
            width: 200px;
            display: inline-block;
        }

        .bloque {
            margin-bottom: 15px;
        }

        .linea {
            border-bottom: 1px solid #ddd;
            margin: 15px 0;
        }

        .copyrigth {
            font-size: 0.6rem;
            color: #000000;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>

<body>

    <div class="logo">
        <img src="<?= asset('/img/logo_20260320_0001.png') ?>" alt="Arca de Noemí">
    </div>

    <h1>Formulario de Adopción</h1>

    <div class="linea"></div>

    <!-- Datos personales-->
    <h2>Datos personales</h2>

    <div class="campo"><span class="label">Nombre completo:</span> <?= v($datos_formulario['nombre_completo']) ?></div>
    <div class="campo"><span class="label">DNI / Pasaporte:</span> <?= v($datos_formulario['dni_pasaporte']) ?></div>
    <div class="campo"><span class="label">Edad:</span> <?= v($datos_formulario['edad']) ?></div>
    <div class="campo"><span class="label">Dirección:</span> <?= v($datos_formulario['direccion']) ?></div>
    <div class="campo"><span class="label">Ciudad:</span> <?= v($datos_formulario['ciudad']) ?></div>
    <div class="campo"><span class="label">Código Postal:</span> <?= v($datos_formulario['codigo_postal']) ?></div>
    <div class="campo"><span class="label">Provincia:</span> <?= v($datos_formulario['provincia']) ?></div>
    <div class="campo"><span class="label">Teléfono:</span> <?= v($datos_formulario['telefono']) ?></div>
    <div class="campo"><span class="label">Email:</span> <?= v($datos_formulario['email']) ?></div>

    <!-- Datos del animal -->
    <h2>Animal a adoptar</h2>

    <div class="campo"><span class="label">Nombre del animal:</span> <?= v($datos_formulario['animal_nombre']) ?></div>

    <!-- Motivación y entorno -->
    <h2 style="page-break-before: always; page-break-inside: avoid;">
        Motivación y entorno familiar
    </h2>

    <div class="campo"><span class="label">Motivos para adoptar:</span> <?= v($datos_formulario['motivos_adopcion']) ?></div>
    <div class="campo"><span class="label">Personas en casa:</span> <?= v($datos_formulario['personas_en_casa']) ?></div>
    <div class="campo"><span class="label">Familia de acuerdo:</span> <?= $datos_formulario['familia_de_acuerdo'] ? 'Sí' : 'No' ?></div>
    <div class="campo"><span class="label">Responsable principal:</span> <?= v($datos_formulario['responsable_principal']) ?></div>
    <div class="campo"><span class="label">Niños tuvieron animales:</span> <?= $datos_formulario['ninos_tuvieron_animales'] ? 'Sí' : 'No' ?></div>
    <div class="campo"><span class="label">Opinión convivencia con niños:</span> <?= v($datos_formulario['convivencia_ninos_opinion']) ?></div>
    <div class="campo"><span class="label">Impacto en la familia:</span> <?= v($datos_formulario['plan_familia_impacto']) ?></div>
    <div class="campo"><span class="label">Alergias en casa:</span> <?= v($datos_formulario['alergias_en_casa']) ?></div>

    <!-- Experiencia con animales -->
    <h2>Experiencia previa con animales</h2>

    <div class="campo"><span class="label">Capacidad económica:</span> <?= $datos_formulario['capacidad_economica'] ? 'Sí' : 'No' ?></div>
    <div class="campo"><span class="label">Asumir gastos veterinarios:</span> <?= $datos_formulario['asumir_gastos_vet'] ? 'Sí' : 'No' ?></div>
    <div class="campo"><span class="label">Ha tenido animales:</span> <?= $datos_formulario['ha_tenido_animales'] ? 'Sí' : 'No' ?></div>
    <div class="campo"><span class="label">Historia animales previos:</span> <?= v($datos_formulario['historia_animales_previos']) ?></div>
    <div class="campo"><span class="label">Otros animales en casa:</span> <?= v($datos_formulario['otros_animales']) ?></div>
    <div class="campo"><span class="label">Chip y esterilización:</span> <?= $datos_formulario['chip_esterilizados'] ? 'Sí' : 'No' ?></div>
    <div class="campo"><span class="label">Vacunas al día:</span> <?= $datos_formulario['vacunas_en_regla'] ? 'Sí' : 'No' ?></div>

    <!-- Vivienda -->
    <h2 style="page-break-before: always; page-break-inside: avoid;">
        Vivienda
    </h2>

    <div class="campo"><span class="label">Tipo de vivienda:</span> <?= v($datos_formulario['tipo_vivienda']) ?></div>
    <div class="campo"><span class="label">Vivienda en propiedad:</span> <?= $datos_formulario['vivienda_propiedad'] ? 'Sí' : 'No' ?></div>
    <div class="campo"><span class="label">Permiten animales (alquiler):</span> <?= $datos_formulario['permite_animales_en_alquiler'] ? 'Sí' : 'No' ?></div>
    <div class="campo"><span class="label">Patio / jardín:</span> <?= v($datos_formulario['patio_jardin_medidas']) ?></div>
    <div class="campo"><span class="label">Interior o exterior:</span> <?= v($datos_formulario['interior_o_exterior']) ?></div>

    <!-- Vida diaria -->
    <h2>Vida diaria</h2>

    <div class="campo"><span class="label">Profesión / situación:</span> <?= v($datos_formulario['profesion_situacion']) ?></div>
    <div class="campo"><span class="label">Quién asume los gastos:</span> <?= v($datos_formulario['quien_asume_gastos']) ?></div>
    <div class="campo"><span class="label">Tiempo para pasear:</span> <?= v($datos_formulario['tiempo_pasear']) ?></div>
    <div class="campo"><span class="label">Horas solo al día:</span> <?= v($datos_formulario['horas_solo']) ?></div>
    <div class="campo"><span class="label">Lugares de paseo:</span> <?= v($datos_formulario['lugares_paseo']) ?></div>

    <!-- Cambios y vacaciones -->
    <h2>Cambios y vacaciones</h2>

    <div class="campo"><span class="label">Mudanza dentro de la población:</span> <?= v($datos_formulario['mudanza_poblacion']) ?></div>
    <div class="campo"><span class="label">Mudanza a otro país:</span> <?= v($datos_formulario['mudanza_pais']) ?></div>
    <div class="campo"><span class="label">Cuidado en vacaciones:</span> <?= v($datos_formulario['vacaciones_cuidado']) ?></div>

    <!-- Motivación final -->
    <h2 style="page-break-before: always; page-break-inside: avoid;">
        Motivación final
    </h2>

    <div class="campo"><span class="label">¿Por qué adoptar?:</span> <?= v($datos_formulario['por_que_adoptar']) ?></div>
    <div class="campo"><span class="label">Tiempo buscando adoptar:</span> <?= v($datos_formulario['tiempo_busqueda']) ?></div>
    <div class="campo"><span class="label">Cómo conoció la asociación:</span> <?= v($datos_formulario['como_conocio']) ?></div>
    <div class="campo"><span class="label">Conoce las condiciones:</span> <?= $datos_formulario['conoce_condiciones'] ? 'Sí' : 'No' ?></div>

    <!-- Firma -->
    <h2>Firma</h2>

    <div class="campo"><span class="label">Firma (nombre y DNI):</span> <?= v($datos_formulario['firma_nombre_dni']) ?></div>

    <div class="linea"></div>

    <div class="copyrigth">
        <?php
        echo CopyrightRicardFS();
        ?>
    </div>

</body>

</html>