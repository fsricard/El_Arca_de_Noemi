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

// Validar ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['mensaje'] = "ID inválido.";
    $_SESSION['tipo_mensaje'] = "error";
    header("Location: listado_recaudaciones.php");
    exit;
}

$id = (int)$_GET['id'];

// Comprobar si existe
$stmt = $pdo->prepare("SELECT id FROM crowdfunding_recaudaciones WHERE id = :id LIMIT 1");
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

if ($stmt->rowCount() === 0) {
    $_SESSION['mensaje'] = "La recaudación no existe.";
    $_SESSION['tipo_mensaje'] = "error";
    header("Location: listado_recaudaciones.php");
    exit;
}

// Eliminar
try {
    $delete = $pdo->prepare("DELETE FROM crowdfunding_recaudaciones WHERE id = :id");
    $delete->bindParam(':id', $id, PDO::PARAM_INT);

    if ($delete->execute()) {
        $_SESSION['mensaje'] = "Recaudación eliminada correctamente.";
        $_SESSION['tipo_mensaje'] = "exito";
    } else {
        $_SESSION['mensaje'] = "No se pudo eliminar la recaudación.";
        $_SESSION['tipo_mensaje'] = "error";
    }
} catch (PDOException $e) {
    $_SESSION['mensaje'] = "Error en la base de datos: " . $e->getMessage();
    $_SESSION['tipo_mensaje'] = "error";
}

header("Location: listado_recaudaciones.php");
exit;