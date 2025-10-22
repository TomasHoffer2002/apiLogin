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

// Obtener los datos del cuerpo de la solicitud (enviados como JSON)
$data = json_decode(file_get_contents("php://input"));

// Validar datos
if (empty($data->title) || empty($data->category_value)) {
    http_response_code(400); // Bad Request
    echo json_encode(["mensaje" => "Faltan datos requeridos (título, categoría)."]);
    exit();
}

// Preparamos los arrays para guardarlos como JSON
$images_json = json_encode($data->images ?? []);
$tags_json = json_encode($data->tags ?? []);

try {
    $sql = "INSERT INTO Items 
                (title, category_value, description, longDescription, imageUrl, images, 
                discoveryDate, location, period, dimensions, weight, featured, tags) 
            VALUES 
                (:title, :category_value, :description, :longDescription, :imageUrl, :images, 
                :discoveryDate, :location, :period, :dimensions, :weight, :featured, :tags)";
    
    $stmt = $conexion->prepare($sql);

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
        $last_id = $conexion->lastInsertId();
        http_response_code(201); // Created
        echo json_encode([
            "mensaje" => "Elemento creado exitosamente.",
            "id" => $last_id
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["mensaje" => "Error al crear el elemento."]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["mensaje" => "Error en el servidor: " . $e->getMessage()]);
}
?>