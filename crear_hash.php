<?php
// Para admin la contraseña es: admin123(Tomi02)
// Para miembro la contraseña es: miembro123(Homerin)
$passwordPlana = 'miembro123'; 

// Generar el hash de la contraseña
$hash = password_hash($passwordPlana, PASSWORD_DEFAULT);

// Mostrar el hash
echo "Contraseña Plana: " . htmlspecialchars($passwordPlana) . "<br>";
echo "Hash Generado: " . htmlspecialchars($hash);

?>