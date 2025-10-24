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


if (empty($data->item_id) || empty($data->usuario_id) || empty($data->contenido)) {
    http_response_code(400);
    echo json_encode(["mensaje" => "Faltan datos (item_id, usuario_id, contenido)."]);
    exit();
}

try {
    $sql = "INSERT INTO Comentarios (item_id, usuario_id, contenido) VALUES (:item_id, :usuario_id, :contenido)";
    $stmt = $conexion->prepare($sql);
    
    $stmt->bindParam(':item_id', $data->item_id, PDO::PARAM_INT);
    $stmt->bindParam(':usuario_id', $data->usuario_id, PDO::PARAM_INT);
    $stmt->bindParam(':contenido', $data->contenido);

    if ($stmt->execute()) {
        http_response_code(201); // Created
        echo json_encode(["mensaje" => "Comentario enviado. Queda pendiente de moderación."]);
    } else {
        http_response_code(500);
        echo json_encode(["mensaje" => "Error al guardar el comentario."]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["mensaje" => "Error en el servidor: " . $e->getMessage()]);
}
?>