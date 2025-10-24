<?php
// htdocs/apiLogin/modificar_estado_comentario.php
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

if (empty($data->id) || empty($data->nuevo_estado)) {
    http_response_code(400);
    echo json_encode(["mensaje" => "Faltan datos (id, nuevo_estado)."]);
    exit();
}

if (!in_array($data->nuevo_estado, ['aprobado', 'rechazado'])) {
    http_response_code(400);
    echo json_encode(["mensaje" => "El nuevo estado debe ser 'aprobado' o 'rechazado'."]);
    exit();
}

try {
    $sql = "UPDATE Comentarios SET estado = :nuevo_estado WHERE id = :id";
    $stmt = $conexion->prepare($sql);
    
    $stmt->bindParam(':nuevo_estado', $data->nuevo_estado);
    $stmt->bindParam(':id', $data->id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(["mensaje" => "Estado del comentario actualizado."]);
    } else {
        http_response_code(500);
        echo json_encode(["mensaje" => "Error al actualizar el comentario."]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["mensaje" => "Error en el servidor: " . $e->getMessage()]);
}
?>