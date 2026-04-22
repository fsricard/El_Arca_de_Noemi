<?php
// Obtener datos del animal por ID
function getAnimal(int $id)
{
    global $pdo;
    $sql = "SELECT * FROM animales WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
}

// Obtener datos de la raza por ID
function getRaza(int $idRaza)
{
    global $pdo;
    $sql = "SELECT * FROM razas_animales WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $idRaza]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
}

// Obtener datos de la especie por ID
function getEspecie(int $idEspecie)
{
    global $pdo;
    $sql = "SELECT * FROM especies_animales WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $idEspecie]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
}

// Obtener raza y su especie en una sola consulta
// Devuelve: ['raza' => [...], 'especie' => [...]] o false si no existe la raza
function getRazaConEspecie(int $idRaza)
{
    global $pdo;
    $sql = "
        SELECT
            r.id   AS raza_id,
            r.nombre AS raza_nombre,
            r.descripcion AS raza_descripcion,
            r.especie_id,
            r.fecha_creacion AS raza_fecha_creacion,
            r.activo AS raza_activo,
            e.id   AS especie_id,
            e.nombre AS especie_nombre,
            e.descripcion AS especie_descripcion,
            e.fecha_creacion AS especie_fecha_creacion,
            e.activo AS especie_activo
        FROM razas_animales r
        LEFT JOIN especies_animales e ON r.especie_id = e.id
        WHERE r.id = :id
        LIMIT 1
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $idRaza]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) return false;

    // Mapear a estructura clara
    $raza = [
        'id' => (int)$row['raza_id'],
        'nombre' => $row['raza_nombre'],
        'descripcion' => $row['raza_descripcion'],
        'especie_id' => $row['especie_id'],
        'fecha_creacion' => $row['raza_fecha_creacion'],
        'activo' => $row['raza_activo'],
    ];

    $especie = null;
    if (!empty($row['especie_id'])) {
        $especie = [
            'id' => (int)$row['especie_id'],
            'nombre' => $row['especie_nombre'],
            'descripcion' => $row['especie_descripcion'],
            'fecha_creacion' => $row['especie_fecha_creacion'],
            'activo' => $row['especie_activo'],
        ];
    }

    return ['raza' => $raza, 'especie' => $especie];
}

// Obtener todas las fotos del animal (Incluye principal y galería)
function getFotos(int $idAnimal)
{
    global $pdo;
    $sql = "SELECT * FROM animales_fotos 
            WHERE id_animal = :id
            ORDER BY es_principal DESC, fecha_subida ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $idAnimal]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
