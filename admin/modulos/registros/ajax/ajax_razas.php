<?php
require_once '../../../../config/database.php';

$especie_id = intval($_GET['especie_id'] ?? 0);

$stmt = $pdo->prepare("SELECT id, nombre FROM razas_animales WHERE especie_id = ? ORDER BY nombre");
$stmt->execute([$especie_id]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));