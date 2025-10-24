<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require 'conexion.php';

$filtro_estado = $_GET['estado'] ?? 'todos'; // 'todos' por defecto

$sql = "SELECT 
            c.id, 
            c.contenido, 
            c.fecha, 
            c.estado,
            u.usuario AS autor_usuario,  -- Nombre de usuario del autor
            u.email AS autor_email,    -- Email del autor
            i.title AS item_titulo     -- Título del ítem
        FROM 
            Comentarios c
        JOIN 
            Usuarios u ON c.usuario_id = u.id
        JOIN 
            Items i ON c.item_id = i.id";

$params = [];
if ($filtro_estado != 'todos' && in_array($filtro_estado, ['pendiente', 'aprobado', 'rechazado'])) {
    $sql .= " WHERE c.estado = :estado";
    $params[':estado'] = $filtro_estado;
}

$sql .= " ORDER BY c.fecha DESC"; // Mostrar los más nuevos primero

try {
    $stmt = $conexion->prepare($sql);
    $stmt->execute($params);
    $comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    http_response_code(200);
    echo json_encode($comentarios);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["mensaje" => "Error en el servidor: " . $e->getMessage()]);
}
?>