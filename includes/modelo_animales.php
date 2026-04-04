<?php
// Obtener datos del animal por ID
function getAnimal($id) {
    global $pdo;

    $sql = "SELECT * FROM animales WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Obtener datos de la raza (incluye especie)
function getRaza($idRaza) {
    global $pdo;

    $sql = "SELECT * FROM razas_animales WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $idRaza]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Obtener todas las fotos del animal (Incluye principal y galería)
function getFotos($idAnimal) {
    global $pdo;

    $sql = "SELECT * FROM animales_fotos 
            WHERE id_animal = :id
            ORDER BY es_principal DESC, fecha_subida ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $idAnimal]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
