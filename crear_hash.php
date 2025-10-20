<?php
// Para admin la contraseña es: admin123(Tomi02)
// Para miembro la contraseña es: miembro123(Homerin)
$contraseñaPlana = 'miembro123'; 

// Generar el hash de la contraseña
$hash = password_hash($contraseñaPlana, PASSWORD_DEFAULT);

// Mostrar el hash
echo "Contraseña Plana: " . htmlspecialchars($contraseñaPlana) . "<br>";
echo "Hash Generado: " . htmlspecialchars($hash);

?>