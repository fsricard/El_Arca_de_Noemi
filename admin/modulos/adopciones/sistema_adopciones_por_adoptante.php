<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../../config/database.php';
require_once(__DIR__ . '/../../../config/funciones.php');

// Si no está logueado, redirigimos al login
if (!isLoggedIn()) {
    header("Location: index.php");
    exit;
}

/* ---------------------------------------------------------
   1. Obtener ID del adoptante
--------------------------------------------------------- */
$id_adoptante = intval($_GET['id'] ?? 0);
if ($id_adoptante <= 0) {
    die("ID de adoptante no válido.");
}

/* ---------------------------------------------------------
   2. Obtener datos del adoptante
--------------------------------------------------------- */
$stmt = $pdo->prepare("
    SELECT *
    FROM adoptantes
    WHERE id = ?
");
$stmt->execute([$id_adoptante]);
$adoptante = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$adoptante) {
    die("Adoptante no encontrado.");
}

/* ---------------------------------------------------------
   3. Paginación
--------------------------------------------------------- */
$por_pagina = 20;
$pagina_actual = max(1, intval($_GET['p'] ?? 1));
$offset = ($pagina_actual - 1) * $por_pagina;

/* ---------------------------------------------------------
   4. Total de adopciones
--------------------------------------------------------- */
$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM adopciones
    WHERE id_adoptante = ?
");
$stmt->execute([$id_adoptante]);
$total_registros = $stmt->fetchColumn();

/* ---------------------------------------------------------
   5. Obtener adopciones del adoptante
--------------------------------------------------------- */
$stmt = $pdo->prepare("
    SELECT 
        a.*,
        ani.nombre AS nombre_animal,
        e.nombre  AS especie,
        r.nombre  AS raza
    FROM adopciones a
    INNER JOIN animales ani         ON a.id_animal = ani.id
    INNER JOIN razas_animales r     ON ani.id_raza = r.id
    INNER JOIN especies_animales e  ON r.especie_id = e.id
    WHERE a.id_adoptante = ?
    ORDER BY a.fecha_adopcion DESC
    LIMIT $offset, $por_pagina
");
$stmt->execute([$id_adoptante]);
$adopciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pagina = 'sistema_adopciones_por_adoptante';

include('../../includes/header.php');
?>

<main>
    <section>
        <div class="container">
            <h2>Adopciones de <?= htmlspecialchars($adoptante['nombre'] . ' ' . $adoptante['apellidos']) ?></h2>

            <div class="bloque-info" style="margin-bottom: 25px;">
                <h3>Datos del adoptante</h3>
                <p>
                    <strong><?= htmlspecialchars($adoptante['nombre'] . ' ' . $adoptante['apellidos']) ?></strong><br>
                    Tel: <?= htmlspecialchars($adoptante['telefono']) ?><br>
                    Email: <?= htmlspecialchars($adoptante['email']) ?><br>
                    <?= htmlspecialchars($adoptante['direccion']) ?>,
                    <?= htmlspecialchars($adoptante['ciudad']) ?> (<?= htmlspecialchars($adoptante['provincia']) ?>)
                </p>
            </div>

            <?php if (empty($adopciones)): ?>

                <p class="alerta">Este adoptante no tiene adopciones registradas.</p>

            <?php else: ?>

                <table class="tabla">
                    <thead>
                        <tr>
                            <th>Animal</th>
                            <th>Especie / Raza</th>
                            <th>Fecha adopción</th>
                            <th>Estado</th>
                            <th>Notas</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($adopciones as $a): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($a['nombre_animal']) ?></strong></td>

                                <td><?= htmlspecialchars($a['especie']) ?> - <?= htmlspecialchars($a['raza']) ?></td>

                                <td><?= htmlspecialchars($a['fecha_adopcion']) ?></td>

                                <td>
                                    <?php
                                    $estado = $a['estado'];
                                    $clase = [
                                        'pendiente'   => 'badge-warning',
                                        'en_proceso'  => 'badge-info',
                                        'finalizada'  => 'badge-success',
                                        'cancelada'   => 'badge-danger'
                                    ][$estado] ?? 'badge-secondary';
                                    ?>
                                    <span class="badge <?= $clase ?>">
                                        <?= ucfirst(str_replace('_', ' ', $estado)) ?>
                                    </span>
                                </td>

                                <td>
                                    <?php if ($a['notas']): ?>
                                        <?= nl2br(htmlspecialchars(substr($a['notas'], 0, 80))) ?>...
                                    <?php else: ?>
                                        <em>Sin notas</em>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <button class="btn btn-warning"
                                        onclick="window.location='sistema_adopciones_editar_adoptante.php?id=<?= $a['id'] ?>'">
                                        <i class="fa-solid fa-pen-to-square"></i> Editar
                                    </button>

                                    <button type="button" class="btn btn-volver"
                                        onclick="window.location='sistema_adopciones_listado_adoptantes.php'">
                                        <i class="fa-solid fa-arrow-left"></i> Volver
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- PAGINADOR -->
                <div style="margin-top: 20px;">
                    <?= paginador($total_registros, $por_pagina, $pagina_actual, $_GET); ?>
                </div>

            <?php endif; ?>
        </div>
    </section>
</main>

<?php include('../../includes/footer.php');
