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

// Validar entrada: necesita ID y estado 'featured' (booleano)
if (!isset($data->id) || !is_numeric($data->id) || !isset($data->featured) || !is_bool($data->featured)) {
    http_response_code(400); 
    echo json_encode(["mensaje" => "Se requiere un ID de ítem válido y el estado 'featured' (true/false)."]);
    exit();
}

$id = $data->id;
$featured = $data->featured; // Será true o false

try {
    $sql = "UPDATE Items SET featured = :featured WHERE id = :id";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':featured', $featured, PDO::PARAM_BOOL); 

    if ($stmt->execute()) {
        http_response_code(200); 
        echo json_encode(["mensaje" => "Estado destacado actualizado exitosamente."]);
    } else {
        http_response_code(500);
        echo json_encode(["mensaje" => "Error al actualizar el estado destacado."]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["mensaje" => "Error en el servidor: " . $e->getMessage()]);
}
?>