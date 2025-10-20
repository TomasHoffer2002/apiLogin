<?php
// Desactivar la notificación de todos los errores de PHP
error_reporting(0);

// Encabezados para permitir el acceso desde cualquier origen (CORS)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");
header("Content-Type: application/json; charset=UTF-8");

// Datos de conexión a la base de datos
$servidor = "localhost";
$usuario_db = "root"; // Tu usuario de MySQL
$contrasena_db = "tomas2025!"; // Tu contraseña de MySQL
$nombre_db = "museo"; // El nombre de tu base de datos

try {
    // Crear la conexión PDO
    $conexion = new PDO("mysql:host=$servidor;dbname=$nombre_db", $usuario_db, $contrasena_db);
    // Configurar PDO para que lance excepciones en caso de error
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Enviar una respuesta de error si la conexión falla
    http_response_code(500);
    echo json_encode(["mensaje" => "Error en la conexión a la base de datos: " . $e->getMessage()]);
    exit; // Detener la ejecución del script
}
?>