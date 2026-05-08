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

// --- Filtros ---
$filtros = [];
$where = [];
$params = [];

// Nombre
if (!empty($_GET['nombre'])) {
    $where[] = "nombre LIKE :nombre";
    $params[':nombre'] = "%" . $_GET['nombre'] . "%";
    $filtros['nombre'] = $_GET['nombre'];
}

// Email
if (!empty($_GET['email'])) {
    $where[] = "email LIKE :email";
    $params[':email'] = "%" . $_GET['email'] . "%";
    $filtros['email'] = $_GET['email'];
}

// Fecha desde
if (!empty($_GET['fecha_desde'])) {
    $where[] = "DATE(fecha) >= :fecha_desde";
    $params[':fecha_desde'] = $_GET['fecha_desde'];
    $filtros['fecha_desde'] = $_GET['fecha_desde'];
}

// Fecha hasta
if (!empty($_GET['fecha_hasta'])) {
    $where[] = "DATE(fecha) <= :fecha_hasta";
    $params[':fecha_hasta'] = $_GET['fecha_hasta'];
    $filtros['fecha_hasta'] = $_GET['fecha_hasta'];
}

$where_sql = "";
if (!empty($where)) {
    $where_sql = "WHERE " . implode(" AND ", $where);
}

// --- Paginación ---
$por_pagina = 10;
$pagina_actual = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$offset = ($pagina_actual - 1) * $por_pagina;

// Total de registros filtrados
$stmt_total = $pdo->prepare("SELECT COUNT(*) FROM opiniones_usuarios $where_sql");
$stmt_total->execute($params);
$total_mensajes = $stmt_total->fetchColumn();

// Obtener registros paginados
$sql = "SELECT * FROM opiniones_usuarios $where_sql ORDER BY fecha DESC LIMIT :offset, :limit";
$stmt = $pdo->prepare($sql);

foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}

$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit', $por_pagina, PDO::PARAM_INT);

$stmt->execute();
$opiniones = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pagina = 'opiniones_de_usuario_listado';

include('../../includes/header.php');
?>

<main>
    <section>
        <div class="container">
            <h2>Listado de opiniones de usuarios</h2>

            <form method="GET" class="filtros">
                <div>
                    <label>Nombre:</label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($_GET['nombre'] ?? '') ?>">
                </div>

                <div>
                    <label>Email:</label>
                    <input type="text" name="email" value="<?= htmlspecialchars($_GET['email'] ?? '') ?>">
                </div>

                <div>
                    <label>Fecha desde:</label>
                    <input type="date" name="fecha_desde" value="<?= htmlspecialchars($_GET['fecha_desde'] ?? '') ?>">
                </div>

                <div>
                    <label>Fecha hasta:</label>
                    <input type="date" name="fecha_hasta" value="<?= htmlspecialchars($_GET['fecha_hasta'] ?? '') ?>">
                </div>

                <button type="submit">
                    <i class="fa-solid fa-filter"></i> Filtrar
                </button>
                <button type="button" id="resetFiltros">
                    <i class="fa-solid fa-rotate-left"></i> Limpiar filtros
                </button>
            </form>

            <?php if (empty($opiniones)): ?>
                <p>No hay opiniones que coincidan con los filtros.</p>
            <?php else: ?>
                <table class="tabla-opiniones">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Mensaje</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($opiniones as $op): ?>
                            <tr>
                                <td><?= $op['id'] ?></td>

                                <td>
                                    <?php if (!empty($op['imagen'])): ?>
                                        <img src="<?= asset(htmlspecialchars($op['imagen'])) ?>"
                                            alt="Foto de <?= htmlspecialchars($op['nombre']) ?>"
                                            style="width:60px; height:60px; object-fit:cover; border-radius:6px;">
                                    <?php else: ?>
                                        <span style="opacity:0.5;">Sin imagen</span>
                                    <?php endif; ?>
                                </td>

                                <td><?= htmlspecialchars($op['nombre']) ?></td>
                                <td><?= htmlspecialchars($op['email']) ?></td>
                                <td><?= nl2br(htmlspecialchars($op['mensaje'])) ?></td>
                                <td><?= date("d/m/Y H:i", strtotime($op['fecha'])) ?></td>
                                <td>
                                    <button class="btn btn-success"
                                        onclick="window.location='opiniones_de_usuario_editar.php?id=<?= $op['id'] ?>'">
                                        Ver mensaje
                                    </button>

                                    <a href="opiniones_de_usuario_eliminar.php?id=<?= $op['id'] ?>" onclick="return confirm('¿Seguro que deseas eliminar este mensaje?');">
                                        <button class="btn delete-user"><i class="fa-solid fa-skull-crossbones"></i> Eliminar</button>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            <?php endif; ?>

            <?= paginador($total_mensajes, $por_pagina, $pagina_actual, $_GET, 'pagina'); ?>

        </div>
    </section>
</main>

<script>
    // Script para el botón Limpiar filtros
    document.getElementById('resetFiltros').addEventListener('click', function() {
        window.location.href = 'opiniones_de_usuario_listado.php';
    });
</script>

<?php include('../../includes/footer.php');
