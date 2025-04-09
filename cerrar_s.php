
<?php
session_start(); // Inicia la sesión

// Elimina todas las variables de sesión
session_unset();

// Destruye la sesión
session_destroy();

// Redirige al usuario a la página de login
header("Location: index.html");
exit(); // Asegúrate de que no se ejecute ningún código adicional después de la redirección
?>
