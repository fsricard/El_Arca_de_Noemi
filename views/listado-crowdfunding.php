<?php
// Obtener plataformas para el filtro
$stmt_plat = $pdo->query("SELECT id, nombre FROM crowdfunding_plataformas WHERE activo = 1 ORDER BY nombre ASC");
$plataformas = $stmt_plat->fetchAll(PDO::FETCH_ASSOC);

// Filtros
$filtros_sql = [];
$params = [];

// Filtro plataforma
if (!empty($_GET['plataforma_id'])) {
    $filtros_sql[] = "r.plataforma_id = :plataforma_id";
    $params[':plataforma_id'] = $_GET['plataforma_id'];
}

// Filtro estado
if (isset($_GET['estado']) && $_GET['estado'] !== '') {
    $filtros_sql[] = "r.activa = :estado";
    $params[':estado'] = $_GET['estado'];
}

// Filtro recaudación mínima
if (!empty($_GET['min_recaudado'])) {
    $filtros_sql[] = "r.cantidad_recaudada >= :min_recaudado";
    $params[':min_recaudado'] = $_GET['min_recaudado'];
}

// Filtro recaudación máxima
if (!empty($_GET['max_recaudado'])) {
    $filtros_sql[] = "r.cantidad_recaudada <= :max_recaudado";
    $params[':max_recaudado'] = $_GET['max_recaudado'];
}

// Construir WHERE dinámico
$where = "";
if (!empty($filtros_sql)) {
    $where = "WHERE " . implode(" AND ", $filtros_sql);
}

// Paginación
$por_pagina = 10;
$pagina_actual = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$inicio = ($pagina_actual - 1) * $por_pagina;

// Contar total de registros con filtros
$sql_count = "SELECT COUNT(*) 
              FROM crowdfunding_recaudaciones r
              INNER JOIN crowdfunding_plataformas p ON r.plataforma_id = p.id
              $where";

$stmt_count = $pdo->prepare($sql_count);
$stmt_count->execute($params);
$total_registros = $stmt_count->fetchColumn();

// Consulta principal
$sql = "SELECT r.*, p.nombre AS plataforma, p.logo AS logo_plataforma
        FROM crowdfunding_recaudaciones r
        INNER JOIN crowdfunding_plataformas p ON r.plataforma_id = p.id
        $where
        ORDER BY r.created_at DESC
        LIMIT :inicio, :por_pagina";

$stmt = $pdo->prepare($sql);

// Bind dinámico
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}

$stmt->bindValue(':inicio', $inicio, PDO::PARAM_INT);
$stmt->bindValue(':por_pagina', $por_pagina, PDO::PARAM_INT);

$stmt->execute();
$recaudaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="layout-home">

    <section class="destacados">

        <article class="destacado-block listado-crowdfunding">
            <h2 class="destacado-title title-crowdfunding">
                <i class="fa-solid fa-hand-holding-dollar"></i> Listado de campañas de CrowdFundig
            </h2>

            <div class="destacado-content">

                <!-- Listado -->
                <?php if (empty($recaudaciones)): ?>
                    <p>No hay recaudaciones que coincidan con los filtros.</p>
                <?php else: ?>

                    <hr>

                    <?php foreach ($recaudaciones as $r): ?>

                        <div class="destacado-content content-crowdfunding-block">

                            <div class="imagen-crowdfunding">
                                <img src="<?= $r['logo_plataforma'] ?>?v=<?= time() ?>">
                            </div>

                            <div class="crowdfunding-block-medio">

                                <h3 class="crowdfunding-plataforma">
                                    <i class="fa-solid fa-hand-holding-dollar"></i>
                                    <?= htmlspecialchars($r['plataforma']) ?>
                                </h3>

                                <div class="crowdfunding-descripcion">
                                    <?= $r['descripcion'] ?>
                                </div>

                                <div class="crowdfunding-cantidades">
                                    <span class="objetivo">
                                        Objetivo:
                                        <strong>
                                            <?= number_format($r['cantidad_objetivo'], 2) ?>
                                            <?= $r['moneda'] ?>
                                        </strong>
                                    </span>

                                    <?php if (!empty($r['cantidad_recaudada'])): ?>
                                        <span class="recaudado">
                                            Recaudado:
                                            <strong>
                                                <?= $r['cantidad_recaudada'] !== null
                                                    ? number_format($r['cantidad_recaudada'], 2) . ' ' . $r['moneda']
                                                    : '<span style="color:#999;">—</span>' ?>
                                            </strong>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <a href="<?= htmlspecialchars($r['enlace'] ?? '#'); ?>"
                                    target="_blank"
                                    class="btn adopcion-boton">
                                    Ir a la campaña
                                </a>

                            </div>

                        </div>

                        <hr>

                    <?php endforeach; ?>

                <?php endif; ?>

                <!-- Paginador -->
                <?php
                if ($total_registros > $por_pagina) {
                    echo paginador($total_registros, $por_pagina, $pagina_actual, $_GET, 'p');
                }
                ?>

            </div>

        </article>

    </section>

</main>