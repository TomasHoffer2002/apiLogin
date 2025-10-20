<?php
require 'vendor/autoload.php';
use Firebase\JWT\JWT;

// Tus encabezados CORS...
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require 'conexion.php';

$datos = json_decode(file_get_contents("php://input"));

if (!isset($datos->usuario) || !isset($datos->password)) {
    http_response_code(400);
    echo json_encode(["mensaje" => "Faltan datos de usuario o contraseña."]);
    exit();
}

$usuario_form = $datos->usuario;
$password_form = $datos->password;

try {
    $sql = "SELECT id, nombre, apellido, usuario, email, rol, contraseña FROM Usuarios WHERE usuario = :usuario";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':usuario', $usuario_form);
    $stmt->execute();
    
    $usuario_db = $stmt->fetch(PDO::FETCH_ASSOC);

    // Esta función compara de forma segura la contraseña enviada con el HASH guardado
    if ($usuario_db && password_verify($password_form, $usuario_db['contraseña'])) {
        
        $secret_key = "paraTokenLogin";
        $issuer_claim = "http://localhost";
        $audience_claim = "http://localhost";
        $issuedat_claim = time();
        $expire_claim = $issuedat_claim + 3600; // Expira en 1 hora

        $payload = array(
            "iss" => $issuer_claim,
            "aud" => $audience_claim,
            "iat" => $issuedat_claim,
            "exp" => $expire_claim,
            "data" => array(
                "id" => $usuario_db['id'],
                "nombre" => $usuario_db['nombre'],
                "usuario" => $usuario_db['usuario'],
                "email" => $usuario_db['email'],
                "rol" => $usuario_db['rol']
            )
        );

        $jwt = JWT::encode($payload, $secret_key, 'HS256');

        // Actualizamos el token en la base de datos
        $sqlUpdate = "UPDATE Usuarios SET token = :token WHERE id = :id";
        $stmtUpdate = $conexion->prepare($sqlUpdate);
        $stmtUpdate->bindParam(':token', $jwt);
        $stmtUpdate->bindParam(':id', $usuario_db['id']);
        $stmtUpdate->execute();

        http_response_code(200);
        echo json_encode(
            array(
                "mensaje" => "Login exitoso",
                "token" => $jwt,
                "usuario" => $payload['data']
            )
        );

    } else {
        http_response_code(401);
        echo json_encode(["mensaje" => "Nombre de usuario o contraseña incorrectos."]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["mensaje" => "Error en el servidor: " . $e->getMessage()]);
}
?>