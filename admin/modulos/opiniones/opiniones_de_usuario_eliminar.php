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

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: opiniones_de_usuario_listado.php?error=ID inválido");
    exit;
}

$id = intval($_GET['id']);

// Obtener la imagen antes de borrar
$stmt = $pdo->prepare("SELECT imagen FROM opiniones_usuarios WHERE id = :id");
$stmt->execute([':id' => $id]);
$opinion = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$opinion) {
    header("Location: opiniones_de_usuario_listado.php?error=No encontrado");
    exit;
}

// Eliminar registro
$stmt = $pdo->prepare("DELETE FROM opiniones_usuarios WHERE id = :id");
$stmt->execute([':id' => $id]);

// Eliminar imagen si existe
if (!empty($opinion['imagen'])) {
    $ruta = __DIR__ . '/../../../' . $opinion['imagen'];
    if (file_exists($ruta)) {
        unlink($ruta);
    }
}

header("Location: opiniones_de_usuario_listado.php?msg=Eliminado correctamente");
exit;
