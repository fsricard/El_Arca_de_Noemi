<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/funciones.php';

// Si no está logueado, redirigimos al login
if (!isLoggedIn()) {
    header("Location: index.php");
    exit;
}

$sponsor_id = intval($_GET['sponsor_id'] ?? 0);
if ($sponsor_id <= 0) {
    header("Location: apadrina_listado_padrinos.php");
    exit;
}

/* ---------------------------------------------------------
   Paginación
--------------------------------------------------------- */
$por_pagina = 20;
$pagina_actual = max(1, intval($_GET['p'] ?? 1));
$offset = ($pagina_actual - 1) * $por_pagina;

/* ---------------------------------------------------------
   Obtener datos del padrino
--------------------------------------------------------- */
$stmt = $pdo->prepare("SELECT * FROM sponsors WHERE id = ?");
$stmt->execute([$sponsor_id]);
$padrino = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$padrino) {
    header("Location: apadrina_listado_padrinos.php");
    exit;
}

/* ---------------------------------------------------------
   Contar total de relaciones (para paginador)
--------------------------------------------------------- */
$stmt = $pdo->prepare("SELECT COUNT(*) FROM sponsors_animals WHERE sponsor_id = ?");
$stmt->execute([$sponsor_id]);
$total_registros = (int)$stmt->fetchColumn();

/* ---------------------------------------------------------
   Obtener relaciones (paginadas) con datos del animal
--------------------------------------------------------- */
$relaciones = [];
$error_general = null;

try {
    $stmt = $pdo->prepare("
        SELECT sa.*,
               a.id AS animal_id,
               a.nombre AS animal_nombre,
               a.foto_principal AS animal_foto
        FROM sponsors_animals sa
        LEFT JOIN animals_sponsor a ON sa.animal_id = a.id
        WHERE sa.sponsor_id = ?
        ORDER BY sa.fecha_inicio DESC, sa.id DESC
        LIMIT ?, ?
    ");
    // bindValue para tipos correctos
    $stmt->bindValue(1, $sponsor_id, PDO::PARAM_INT);
    $stmt->bindValue(2, (int)$offset, PDO::PARAM_INT);
    $stmt->bindValue(3, (int)$por_pagina, PDO::PARAM_INT);
    $stmt->execute();
    $relaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_general = "Error al obtener apadrinamientos: " . $e->getMessage();
}

/* ---------------------------------------------------------
   Intentar calcular estadísticas de pagos (si existe tabla)
   - total_pagado (global)
   - total_pagos_count (global)
   - total por relación (si es posible)
--------------------------------------------------------- */
$total_pagado = null;
$total_pagos_count = null;
$importes_por_relacion = []; // clave: relation_id o 'paypal:SUBID'

try {
    // Comprobar si existe alguna tabla de pagos conocida
    $candidates = ['sponsor_payments','payments','sponsors_payments'];
    $paymentsTable = null;
    foreach ($candidates as $t) {
        $q = $pdo->prepare("
            SELECT COUNT(*) FROM information_schema.tables
            WHERE table_schema = DATABASE() AND table_name = ?
        ");
        $q->execute([$t]);
        if ((int)$q->fetchColumn() > 0) {
            $paymentsTable = $t;
            break;
        }
    }

    if ($paymentsTable !== null) {
        // Detectar columna de importe
        $possibleAmountCols = ['amount','total','importe','monto'];
        $amountColumn = null;
        foreach ($possibleAmountCols as $col) {
            $q = $pdo->prepare("
                SELECT COUNT(*) FROM information_schema.columns
                WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?
            ");
            $q->execute([$paymentsTable, $col]);
            if ((int)$q->fetchColumn() > 0) {
                $amountColumn = $col;
                break;
            }
        }

        if ($amountColumn !== null) {
            // Total pagado y número de pagos por sponsor
            $sqlTotal = "SELECT COALESCE(SUM(`$amountColumn`),0) FROM `$paymentsTable` WHERE sponsor_id = ?";
            $stmt = $pdo->prepare($sqlTotal);
            $stmt->execute([$sponsor_id]);
            $total_pagado = (float)$stmt->fetchColumn();

            $sqlCount = "SELECT COUNT(*) FROM `$paymentsTable` WHERE sponsor_id = ?";
            $stmt = $pdo->prepare($sqlCount);
            $stmt->execute([$sponsor_id]);
            $total_pagos_count = (int)$stmt->fetchColumn();

            // Intentar agrupar por relation_id si existe
            $hasRelationId = false;
            $q = $pdo->prepare("
                SELECT COUNT(*) FROM information_schema.columns
                WHERE table_schema = DATABASE() AND table_name = ? AND column_name = 'relation_id'
            ");
            $q->execute([$paymentsTable]);
            if ((int)$q->fetchColumn() > 0) {
                $hasRelationId = true;
            }

            if ($hasRelationId) {
                $sqlRel = "SELECT relation_id, COALESCE(SUM(`$amountColumn`),0) AS total_rel, COUNT(*) AS pagos_rel
                           FROM `$paymentsTable`
                           WHERE sponsor_id = ?
                           GROUP BY relation_id";
                $stmt = $pdo->prepare($sqlRel);
                $stmt->execute([$sponsor_id]);
                while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $importes_por_relacion[(int)$r['relation_id']] = [
                        'total_rel' => (float)$r['total_rel'],
                        'pagos_rel' => (int)$r['pagos_rel']
                    ];
                }
            } else {
                // Intentar agrupar por paypal_subscription_id si existe
                $hasPaypalCol = false;
                $q = $pdo->prepare("
                    SELECT COUNT(*) FROM information_schema.columns
                    WHERE table_schema = DATABASE() AND table_name = ? AND column_name = 'paypal_subscription_id'
                ");
                $q->execute([$paymentsTable]);
                if ((int)$q->fetchColumn() > 0) {
                    $hasPaypalCol = true;
                }

                if ($hasPaypalCol) {
                    $sqlRel = "SELECT paypal_subscription_id, COALESCE(SUM(`$amountColumn`),0) AS total_rel, COUNT(*) AS pagos_rel
                               FROM `$paymentsTable`
                               WHERE sponsor_id = ?
                               GROUP BY paypal_subscription_id";
                    $stmt = $pdo->prepare($sqlRel);
                    $stmt->execute([$sponsor_id]);
                    while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $importes_por_relacion['paypal:' . ($r['paypal_subscription_id'] ?? '')] = [
                            'total_rel' => (float)$r['total_rel'],
                            'pagos_rel' => (int)$r['pagos_rel']
                        ];
                    }
                }
            }
        }
    }
} catch (PDOException $e) {
    // No interrumpimos la vista por errores en estadísticas
    $error_general = $error_general ?? ("Error al calcular estadísticas de pagos: " . $e->getMessage());
}

/* ---------------------------------------------------------
   Adjuntar importes a las relaciones cargadas (paginadas)
--------------------------------------------------------- */
foreach ($relaciones as &$r) {
    $r['total_pagado_relacion'] = null;
    $r['pagos_count_relacion'] = null;

    $rid = (int)$r['id'];
    if (isset($importes_por_relacion[$rid])) {
        $r['total_pagado_relacion'] = $importes_por_relacion[$rid]['total_rel'];
        $r['pagos_count_relacion'] = $importes_por_relacion[$rid]['pagos_rel'];
        continue;
    }

    $key = 'paypal:' . ($r['paypal_subscription_id'] ?? '');
    if (isset($importes_por_relacion[$key])) {
        $r['total_pagado_relacion'] = $importes_por_relacion[$key]['total_rel'];
        $r['pagos_count_relacion'] = $importes_por_relacion[$key]['pagos_rel'];
    }
}
unset($r);

/* ---------------------------------------------------------
   Export CSV (si se solicita)
   - Exporta TODOS los apadrinamientos del padrino (no solo la página)
--------------------------------------------------------- */
if (isset($_GET['export']) && $_GET['export'] == '1') {

    // Reobtener todas las relaciones sin paginar para exportar
    $allRel = [];
    try {
        $stmt = $pdo->prepare("
            SELECT sa.*,
                   a.id AS animal_id,
                   a.nombre AS animal_nombre
            FROM sponsors_animals sa
            LEFT JOIN animals_sponsor a ON sa.animal_id = a.id
            WHERE sa.sponsor_id = ?
            ORDER BY sa.fecha_inicio DESC, sa.id DESC
        ");
        $stmt->execute([$sponsor_id]);
        $allRel = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Si falla, devolvemos error simple
        header('Content-Type: text/plain; charset=utf-8', true, 500);
        echo "Error al generar CSV: " . $e->getMessage();
        exit;
    }

    // Preparar importes globales por relación (si no se calcularon antes)
    // Reutilizamos $importes_por_relacion calculado arriba; si está vacío y existe tabla, intentamos recalcular por seguridad
    // (omitimos recalculo complejo para mantener rendimiento)

    // Cabeceras CSV
    $filename = 'apadrinamientos_sponsor_' . $sponsor_id . '_' . date('Ymd_His') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $out = fopen('php://output', 'w');
    // BOM para Excel
    fwrite($out, "\xEF\xBB\xBF");

    // Cabecera general
    fputcsv($out, ['Padrino', $padrino['nombre_apellidos'] ?? '', 'Email', $padrino['email'] ?? '']);
    fputcsv($out, []);
    fputcsv($out, ['Resumen']);
    fputcsv($out, ['Total apadrinamientos', $total_registros]);
    fputcsv($out, ['Total pagos registrados', $total_pagos_count === null ? 'No disponible' : $total_pagos_count]);
    fputcsv($out, ['Total aportado', $total_pagado === null ? 'No disponible' : number_format($total_pagado, 2, '.', '')]);
    fputcsv($out, []);
    fputcsv($out, ['ID relación','ID animal','Nombre animal','Inicio','Cancelación','Estado','Paypal subscription id','Nota','Pagos (count)','Total aportado']);

    foreach ($allRel as $r) {
        $rid = (int)$r['id'];
        $total_rel = null;
        $count_rel = null;
        if (isset($importes_por_relacion[$rid])) {
            $total_rel = $importes_por_relacion[$rid]['total_rel'];
            $count_rel = $importes_por_relacion[$rid]['pagos_rel'];
        } else {
            $key = 'paypal:' . ($r['paypal_subscription_id'] ?? '');
            if (isset($importes_por_relacion[$key])) {
                $total_rel = $importes_por_relacion[$key]['total_rel'];
                $count_rel = $importes_por_relacion[$key]['pagos_rel'];
            }
        }

        fputcsv($out, [
            $rid,
            (int)($r['animal_id'] ?? 0),
            $r['animal_nombre'] ?? '',
            $r['fecha_inicio'] ?? '',
            $r['fecha_cancelacion'] ?? '',
            $r['estado'] ?? '',
            $r['paypal_subscription_id'] ?? '',
            str_replace(["\r","\n"], [' ',' '], ($r['nota'] ?? '')),
            $count_rel === null ? '' : $count_rel,
            $total_rel === null ? '' : number_format($total_rel, 2, '.', '')
        ]);
    }

    fclose($out);
    exit;
}

$pagina = 'apadrina_ver_apadrinamientos';

include('../includes/header.php');
?>

<main>
    <section>
        <div class="container">
            <h2>Apadrinamientos de <?= htmlspecialchars($padrino['nombre_apellidos']) ?></h2>

            <div style="display:flex; gap:12px; align-items:center; margin-bottom:12px;">
                <a class="btn btn-success" href="apadrina_editar_padrino.php?id=<?= (int)$padrino['id'] ?>">
                    <i class="fa-solid fa-pen"></i> Editar padrino
                </a>

                <a class="btn update-user" href="apadrina_listado_padrinos.php">
                    <i class="fa-solid fa-arrow-left"></i> Volver al listado
                </a>

                <a class="btn btn-exportar-pdf" href="?sponsor_id=<?= (int)$padrino['id'] ?>&export=1">
                    <i class="fa-solid fa-file-csv"></i> Exportar CSV
                </a>
            </div>

            <?php if (!empty($error_general)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error_general) ?></div>
            <?php endif; ?>

            <div class="panel" style="margin-bottom:18px;">
                <strong>Resumen</strong>
                <div style="display:flex; gap:18px; margin-top:8px; flex-wrap:wrap;">
                    <div><strong>Total apadrinamientos:</strong> <?= (int)$total_registros ?></div>
                    <div>
                        <strong>Total pagos registrados:</strong>
                        <?= $total_pagos_count === null ? '<span class="texto-secundario">No disponible</span>' : (int)$total_pagos_count ?>
                    </div>
                    <div>
                        <strong>Total aportado:</strong>
                        <?php if ($total_pagado === null): ?>
                            <span class="texto-secundario">No disponible</span>
                        <?php else: ?>
                            <span><?= number_format($total_pagado, 2, ',', '.') ?> €</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if (empty($relaciones)): ?>
                <p class="texto-secundario">No hay apadrinamientos registrados para este padrino en esta página.</p>
            <?php else: ?>

                <table class="tabla">
                    <thead>
                        <tr>
                            <th>ID relación</th>
                            <th>Animal</th>
                            <th>Inicio</th>
                            <th>Cancelación</th>
                            <th>Estado</th>
                            <th>Paypal subs</th>
                            <th>Nota</th>
                            <th>Pagos (count)</th>
                            <th>Total aportado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($relaciones as $r): ?>
                            <tr>
                                <td><?= (int)$r['id'] ?></td>

                                <td>
                                    <?php if ($r['animal_id']): ?>
                                        <strong><?= htmlspecialchars($r['animal_nombre'] ?: '—') ?></strong><br>
                                        <small class="texto-secundario">ID <?= (int)$r['animal_id'] ?></small>
                                    <?php else: ?>
                                        <span class="texto-secundario">Animal eliminado (ID <?= (int)$r['animal_id'] ?>)</span>
                                    <?php endif; ?>
                                </td>

                                <td><?= htmlspecialchars($r['fecha_inicio'] ?? '-') ?></td>
                                <td><?= $r['fecha_cancelacion'] ? htmlspecialchars($r['fecha_cancelacion']) : '<span class="texto-secundario">—</span>' ?></td>

                                <td>
                                    <?php if ($r['estado'] === 'activo'): ?>
                                        <span class="badge badge-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Cancelado</span>
                                    <?php endif; ?>
                                </td>

                                <td><?= htmlspecialchars($r['paypal_subscription_id'] ?? '-') ?></td>

                                <td style="max-width:260px;">
                                    <?= $r['nota'] ? nl2br(htmlspecialchars($r['nota'])) : '<span class="texto-secundario">—</span>' ?>
                                </td>

                                <td>
                                    <?php
                                        if (isset($r['pagos_count_relacion'])) {
                                            echo (int)$r['pagos_count_relacion'];
                                        } else {
                                            echo '<span class="texto-secundario">—</span>';
                                        }
                                    ?>
                                </td>

                                <td>
                                    <?php
                                        if (isset($r['total_pagado_relacion'])) {
                                            if ($r['total_pagado_relacion'] === null) {
                                                echo '<span class="texto-secundario">—</span>';
                                            } else {
                                                echo number_format((float)$r['total_pagado_relacion'], 2, ',', '.') . ' €';
                                            }
                                        } else {
                                            echo '<span class="texto-secundario">—</span>';
                                        }
                                    ?>
                                </td>

                                <td>
                                    <button class="btn btn-success"
                                        onclick="window.location='apadrina_editar_relacion.php?id=<?= (int)$r['id'] ?>&sponsor_id=<?= (int)$sponsor_id ?>'">
                                        <i class="fa-solid fa-pen"></i> Editar
                                    </button>

                                    <?php if ($r['animal_id']): ?>
                                        <button class="btn btn-ver"
                                            onclick="window.location='apadrina_editar_animal.php?id=<?= (int)$r['animal_id'] ?>'">
                                            <i class="fa-solid fa-paw"></i> Ver animal
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?= paginador($total_registros, $por_pagina, $pagina_actual, $_GET); ?>

            <?php endif; ?>

        </div>
    </section>
</main>

<?php include('../includes/footer.php');