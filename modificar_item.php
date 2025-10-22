<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS"); 
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require 'conexion.php';

$data = json_decode(file_get_contents("php://input"));

// Validar datos
if (empty($data->id) || empty($data->title) || empty($data->category_value)) {
    http_response_code(400);
    echo json_encode(["mensaje" => "Faltan datos requeridos (id, título, categoría)."]);
    exit();
}

$images_json = json_encode($data->images ?? []);
$tags_json = json_encode($data->tags ?? []);

try {
    $sql = "UPDATE Items SET 
                title = :title, 
                category_value = :category_value, 
                description = :description, 
                longDescription = :longDescription, 
                imageUrl = :imageUrl, 
                images = :images, 
                discoveryDate = :discoveryDate, 
                location = :location, 
                period = :period, 
                dimensions = :dimensions, 
                weight = :weight, 
                featured = :featured, 
                tags = :tags 
            WHERE 
                id = :id";
    
    $stmt = $conexion->prepare($sql);

    $stmt->bindParam(':id', $data->id, PDO::PARAM_INT);
    $stmt->bindParam(':title', $data->title);
    $stmt->bindParam(':category_value', $data->category_value);
    $stmt->bindParam(':description', $data->description);
    $stmt->bindParam(':longDescription', $data->longDescription);
    $stmt->bindParam(':imageUrl', $data->imageUrl);
    $stmt->bindParam(':images', $images_json);
    $stmt->bindParam(':discoveryDate', $data->discoveryDate);
    $stmt->bindParam(':location', $data->location);
    $stmt->bindParam(':period', $data->period);
    $stmt->bindParam(':dimensions', $data->dimensions);
    $stmt->bindParam(':weight', $data->weight);
    $stmt->bindParam(':featured', $data->featured, PDO::PARAM_BOOL);
    $stmt->bindParam(':tags', $tags_json);

    if ($stmt->execute()) {
        http_response_code(200); // OK
        echo json_encode(["mensaje" => "Elemento actualizado exitosamente."]);
    } else {
        http_response_code(500);
        echo json_encode(["mensaje" => "Error al actualizar el elemento."]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["mensaje" => "Error en el servidor: " . $e->getMessage()]);
}
?>