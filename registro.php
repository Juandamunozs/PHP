<?php
// Verificar si se han enviado datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Conectar a la base de datos
    $DATABASE_HOST = 'localhost';
    $DATABASE_USER = 'root';
    $DATABASE_PASS = '';
    $DATABASE_NAME = 'loginphp';
    
    $conexion = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

    // Verificar si la conexión fue exitosa
    if (mysqli_connect_error()) {
        exit('Fallo en la conexión de MySQL: ' . mysqli_connect_error());
    }

    // Obtener los datos del formulario y limpiarlos para evitar inyección SQL
    $username = limpiar_dato($_POST['username']);
    $password = cifrar_contrasena($_POST['password']);

    // Preparar la consulta para insertar el nuevo usuario en la base de datos
    $stmt = $conexion->prepare('INSERT INTO accounts (username, password) VALUES (?, ?)');
    $stmt->bind_param('ss', $username, $password);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        // Redirigir a la página de inicio de sesión después de un registro exitoso
        header('Location: index.html');
        exit;
    } else {
        // En caso de error, mostrar un mensaje
        echo 'Error al registrar el usuario.';
    }

    // Cerrar la conexión y liberar los recursos
    $stmt->close();
    $conexion->close();
}

// Función para escapar caracteres especiales para evitar la inyección SQL
function limpiar_dato($dato) {
    global $conexion;
    return mysqli_real_escape_string($conexion, $dato);
}

// Función para cifrar contraseñas
function cifrar_contrasena($contrasena) {
    return password_hash($contrasena, PASSWORD_DEFAULT);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Cuenta</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <div class="login">
        <h1>Registro de Cuenta</h1>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="username">
                <i class="fas fa-user"></i>
            </label>
            <input type="text" name="username" placeholder="Usuario" id="username" required>
            <label for="password">
                <i class="fas fa-lock"></i>
            </label>
            <input type="password" name="password" placeholder="Contraseña" id="password" required>
            <input type="submit" value="Crear Cuenta">
        </form>
        <p>¿Ya tienes una cuenta? <a href="index.html">Inicia sesión aquí</a>.</p>
    </div>
    
</body>
</html>