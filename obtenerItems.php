<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Manejo de la solicitud OPTIONS pre-vuelo de CORS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require 'conexion.php'; // Tu conexión a la DB

try {
    // Consulta SQL con JOIN para obtener el 'label' de la categoría
    // Usamos LEFT JOIN por si algún ítem no tuviera categoría, igual aparezca.
    $sql = "SELECT 
                i.id, 
                i.title, 
                i.description, 
                i.longDescription, 
                i.imageUrl, 
                i.images, 
                i.discoveryDate, 
                i.location, 
                i.period, 
                i.dimensions, 
                i.weight, 
                i.featured, 
                i.tags,
                i.category_value, 
                c.label as category_label 
            FROM 
                Items i
            LEFT JOIN 
                Categorias c ON i.category_value = c.value
            ORDER BY 
                i.id ASC"; // Ordenamos por ID

    $stmt = $conexion->prepare($sql);
    $stmt->execute();

    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($items) {
        $items_procesados = [];
        foreach ($items as $item) {
            
            // --- ¡IMPORTANTE! ---
            // Decodificamos los campos JSON para enviarlos como arrays al frontend
            // MySQL los devuelve como un string, PHP los convierte a array.
            $item['images'] = json_decode($item['images']);
            $item['tags'] = json_decode($item['tags']);
            
            // Convertimos 'featured' (que es 0 o 1) a un booleano (true/false)
            // Esto es más amigable para JavaScript.
            $item['featured'] = (bool)$item['featured'];

            $items_procesados[] = $item;
        }

        http_response_code(200);
        // Enviamos el array de ítems procesados
        echo json_encode($items_procesados);
        
    } else {
        http_response_code(404);
        echo json_encode(["mensaje" => "No se encontraron ítems."]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["mensaje" => "Error en el servidor: " . $e->getMessage()]);
}
?>