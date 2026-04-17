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

$pagina = 'listado_recaudaciones';

include('../../includes/header.php');
?>

<main>
    <section>
        <div class="container">
            <h2>Listado de todas las campañas de recaudaciones</h2>

            <!-- Filtros -->
            <form method="GET" class="filtros">

                <div class="campo">
                    <label>Plataforma:</label>
                    <select name="plataforma_id">
                        <option value="">Todas</option>
                        <?php foreach ($plataformas as $p): ?>
                            <option value="<?= $p['id'] ?>"
                                <?= (isset($_GET['plataforma_id']) && $_GET['plataforma_id'] == $p['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="campo">
                    <label>Estado:</label>
                    <select name="estado">
                        <option value="">Todos</option>
                        <option value="1" <?= (isset($_GET['estado']) && $_GET['estado'] === '1') ? 'selected' : '' ?>>Activa</option>
                        <option value="0" <?= (isset($_GET['estado']) && $_GET['estado'] === '0') ? 'selected' : '' ?>>Inactiva</option>
                    </select>
                </div>

                <div class="campo">
                    <label>Mín. recaudado:</label>
                    <input type="number" step="0.01" name="min_recaudado"
                        value="<?= $_GET['min_recaudado'] ?? '' ?>">
                </div>

                <div class="campo">
                    <label>Máx. recaudado:</label>
                    <input type="number" step="0.01" name="max_recaudado"
                        value="<?= $_GET['max_recaudado'] ?? '' ?>">
                </div>

                <div class="campo">
                    <button type="submit">
                        <i class="fa-solid fa-filter"></i> Filtrar
                    </button>
                    
                    <button type="button" onclick="window.location='listado_recaudaciones.php'">
                        <i class="fa-solid fa-rotate-left"></i> Limpiar filtros
                    </button>
                </div>

            </form>

            <!-- Listado -->
            <?php if (empty($recaudaciones)): ?>
                <p>No hay recaudaciones que coincidan con los filtros.</p>
            <?php else: ?>

                <table class="tabla-admin">
                    <thead>
                        <tr>
                            <th>Logo</th>
                            <th>Plataforma</th>
                            <th>Objetivo</th>
                            <th>Recaudado</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($recaudaciones as $r): ?>
                            <tr>

                                <td>
                                    <img src="../../../<?= $r['logo_plataforma'] ?>?v=<?= time() ?>"
                                        style="width:60px; border:1px solid #ccc; padding:3px;">
                                </td>

                                <td><?= htmlspecialchars($r['plataforma']) ?></td>

                                <td><?= number_format($r['cantidad_objetivo'], 2) ?> <?= $r['moneda'] ?></td>

                                <td>
                                    <?= $r['cantidad_recaudada'] !== null
                                        ? number_format($r['cantidad_recaudada'], 2) . ' ' . $r['moneda']
                                        : '<span style="color:#999;">—</span>' ?>
                                </td>

                                <td><?= substr(strip_tags($r['descripcion']), 0, 80) ?>...</td>

                                <td>
                                    <?= $r['activa'] == 1
                                        ? '<span style="color:green; font-weight:bold;">Activa</span>'
                                        : '<span style="color:red; font-weight:bold;">Inactiva</span>' ?>
                                </td>

                                <td>
                                    <a href="editar_recaudacion.php?id=<?= $r['id'] ?>" class="btn btn-success">Editar</a>
                                    <a href="eliminar_recaudacion.php?id=<?= $r['id'] ?>"
                                        class="btn delete-user"
                                        onclick="return confirm('¿Seguro que deseas eliminar esta recaudación?');">
                                        <i class="fa-solid fa-skull-crossbones"></i> Eliminar
                                    </a>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Paginador -->
                <?= paginador($total_registros, $por_pagina, $pagina_actual, $_GET); ?>

            <?php endif; ?>

        </div>
    </section>
</main>

<?php include('../../includes/footer.php');