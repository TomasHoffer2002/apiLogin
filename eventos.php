<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require 'conexion.php';

try {
    // Si se pasa ?desde_id=X, traer solo eventos más nuevos que ese ID
    if (isset($_GET['desde_id'])) {
        $desde_id = (int)$_GET['desde_id'];
        $sql = "SELECT id, nombre, descripcion, fecha, horaInicio 
                FROM Eventos 
                WHERE id > :desde_id
                ORDER BY id DESC";
        
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':desde_id', $desde_id, PDO::PARAM_INT);
    } else {
        // Traer todos los eventos
        $sql = "SELECT id, nombre, descripcion, fecha, horaInicio 
                FROM Eventos 
                ORDER BY fecha DESC, horaInicio DESC";
        
        $stmt = $conexion->prepare($sql);
    }
    
    $stmt->execute();
    $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    http_response_code(200);
    echo json_encode($eventos);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["mensaje" => "Error en el servidor: " . $e->getMessage()]);
}
?>