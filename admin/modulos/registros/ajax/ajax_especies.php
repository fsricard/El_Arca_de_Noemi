<?php
require_once '../../../../config/database.php';

$stmt = $pdo->query("SELECT id, nombre FROM especies_animales ORDER BY nombre");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));