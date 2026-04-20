<?php
require_once __DIR__ . '/../config/database.php';

$animal_id = $_POST['animal_id'] ?? null;
$nombre = trim($_POST['nombre_apellidos'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$direccion = trim($_POST['direccion'] ?? '');
$mensaje = trim($_POST['mensaje'] ?? '');

$sql = "INSERT INTO sponsors_temp (animal_id, nombre_apellidos, email, telefono, direccion, mensaje)
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $pdo->prepare($sql);
$stmt->execute([$animal_id, $nombre, $email, $telefono, $direccion, $mensaje]);

echo json_encode([
    "ok" => true,
    "temp_id" => $pdo->lastInsertId()
]);
