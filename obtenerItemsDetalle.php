<?php
// Encabezados CORS
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require 'conexion.php'; // Tu conexión a la DB

// Obtener el ID de la URL (Query Parameter)
// Comprobamos que el ID exista
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400); // Bad Request
    echo json_encode(["mensaje" => "Se requiere un ID de ítem válido."]);
    exit();
}

$id = $_GET['id'];

try {
    // Consulta SQL para filtrar por ID
    $sql = "SELECT 
                i.id, i.title, i.description, i.longDescription, 
                i.imageUrl, i.images, i.discoveryDate, i.location, 
                i.period, i.dimensions, i.weight, i.featured, i.tags,
                i.category_value, c.label as category_label 
            FROM 
                Items i
            LEFT JOIN 
                Categorias c ON i.category_value = c.value
            WHERE 
                i.id = :id"; 

    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($item) {   
        $item['images'] = json_decode($item['images']);
        $item['tags'] = json_decode($item['tags']);
        $item['featured'] = (bool)$item['featured'];

        http_response_code(200);
        // Enviamos solo el objeto del ítem
        echo json_encode($item);
        
    } else {
        http_response_code(404); // Not Found
        echo json_encode(["mensaje" => "No se encontró ningún ítem con ese ID."]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["mensaje" => "Error en el servidor: " . $e->getMessage()]);
}
?>