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

if (empty($data->id) || !is_numeric($data->id)) {
    http_response_code(400);
    echo json_encode(["mensaje" => "Se requiere un ID válido."]);
    exit();
}

try {
    $sql = "DELETE FROM Comentarios WHERE id = :id";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':id', $data->id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(["mensaje" => "Comentario eliminado exitosamente."]);
    } else {
        http_response_code(500);
        echo json_encode(["mensaje" => "Error al eliminar el comentario."]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["mensaje" => "Error en el servidor: " . $e->getMessage()]);
}
?>