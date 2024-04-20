<?php
session_start();

//credenciales de acceso a la base de datos
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'loginphp';

// conexión a la base de datos
$conexion = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

if (mysqli_connect_error()) {
    // si se encuentra error en la conexión
    exit('Fallo en la conexión de MySQL:' . mysqli_connect_error());
}

// Se valida si se ha enviado información, con la función isset()
if (!isset($_POST['username'], $_POST['password'])) {
    // si no hay datos muestra error y redirecciona
    header('Location: index.html');
}

// evitar inyección SQL
if ($stmt = $conexion->prepare('SELECT id, password FROM accounts WHERE username = ?')) {
    // parámetros de enlace de la cadena s
    $stmt->bind_param('s', $_POST['username']);
    $stmt->execute();
}

// acá se valida si lo ingresado coincide con la base de datos
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->bind_result($id, $stored_password);
    $stmt->fetch();

    // Verificar si la contraseña ingresada coincide con la contraseña almacenada
    if (password_verify($_POST['password'], $stored_password)) {
        // Autenticación exitosa, crear sesión
        session_regenerate_id();
        $_SESSION['loggedin'] = TRUE;
        $_SESSION['name'] = $_POST['username'];
        $_SESSION['id'] = $id;
        header('Location: inicio.php');
    } else {
        // Contraseña incorrecta
        header('Location: index.html');
    }
} else {
    // Usuario no encontrado en la base de datos
    header('Location: index.html');
}

$stmt->close();
?>