<?php

header("Access-Control-Allow-Origin: *"); 
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require 'conexion.php';

// Requerimos el ID del ítem desde la URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400); 
    echo json_encode(["mensaje" => "Se requiere un ID de ítem válido."]);
    exit();
}

$item_id = $_GET['id'];

try {
    $sql = "SELECT 
                c.id, 
                c.contenido, 
                c.fecha, 
                u.usuario AS autor_usuario
            FROM 
                Comentarios c
            JOIN 
                Usuarios u ON c.usuario_id = u.id
            WHERE 
                c.item_id = :item_id AND c.estado = 'aprobado'
            ORDER BY 
                c.fecha DESC"; 

    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
    $stmt->execute();
    
    $comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    http_response_code(200);
    echo json_encode($comentarios);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["mensaje" => "Error en el servidor: " . $e->getMessage()]);
}
?>