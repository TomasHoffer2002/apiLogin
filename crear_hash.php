<?php
// conexión a la base de datos
require_once 'conexion.php';

// Para admin la contraseña es: admin123(Tomi02)
// Para miembro la contraseña es: miembro123(Homerin)

##Credenciales, lo obligatorio creo, ya me olvidé que iba y que no
$nombre = 'miembro';
$apellido = 'brillo';
$usuario = 'miembrillo';
$email = 'miembrillo123@gmiembro.com';
$passwordPlana = 'miembro123'; 

// Generar el hash de la contraseña
$hash = password_hash($passwordPlana, PASSWORD_DEFAULT);

//Conexión e Inserción en la BD
try {
    $sql = "INSERT INTO usuarios (nombre,apellido,usuario,passwordd, email) VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE passwordd = VALUES(passwordd), email = VALUES(email)";
    $stmt = $conexion->prepare($sql);//Preparar la plantilla de consulta SQL, con la forma de "$sql" de arriba
    $stmt->execute([$nombre, $apellido, $usuario, $hash, $email]);//Ejecutar la consulta con el array de valores a insertar, los de [$nombre, $apellido, $usuario, $hash, $email] en este caso, que es 1
    $mensaje_bd = "Hash para el usuario '{$usuario}' insertado/actualizado correctamente.";

} catch (PDOException $e) {
    $mensaje_bd = "ERROR al insertar en BD: " . $e->getMessage();
}


// Mostrar el hash
echo "Contraseña Plana: " . htmlspecialchars($passwordPlana) . "<br>\n";
echo "Hash Generado y agregado a la BD: " . htmlspecialchars($hash) . "<br>\n";
echo "Estado de BD: " . $mensaje_bd . "<br>\n";
?>