<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['loggedin'])) {
    header('Location: index.html');
    exit;
}

// Configuración de la base de datos
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'loginphp';

// Conexión a la base de datos
$conexion = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

if (mysqli_connect_error()) {
    exit('Fallo en la conexión de MySQL: ' . mysqli_connect_error());
}

// Obtener información del usuario de la base de datos
$stmt = $conexion->prepare('SELECT password, email FROM accounts WHERE id = ?');

$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$stmt->bind_result($password, $email);
$stmt->fetch();
$stmt->close();

// Inicializar $email en caso de que no se haya encontrado en la base de datos
$email = $email ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil Usuario</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body class="loggedin">
    <nav class="navtop">
        <h1 style="color:white;">CitaYa</h1>
        <a href="inicio.php" style="color:white;">Inicio</a>
        <a href="perfil.php" style="color:white;"><i class="fas fa-user-circle"></i>Información de Usuario</a>
        <a href="cerrar-sesion.php" style="color:white;"><i class="fas fa-sign-out-alt"></i>Cerrar Sesion</a>
    </nav>
    <div class="content">

        <h2>Información del Usuario</h2>
        <div>
            <p>La siguiente es la información registrada de tu cuenta</p>
            <table>
                <tr>
                    <td>Usuario:</td>
                    <td><?= $_SESSION['name'] ?></td>
                </tr>
                <tr>
                    <td>Email:</td>
                    <td><?= $email ?></td>
                </tr>
            </table>
        </div>

        <h2>Actualizar Datos</h2>
        <!-- Formulario para actualizar datos -->
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input type="text" name="username" placeholder="Nuevo Nombre" required>
            <input type="email" name="email" placeholder="Nuevo Correo Electrónico" required>
            <button type="submit" name="update_user">Actualizar Datos</button>
        </form>

        <?php
        // Verificar si se envió el formulario para actualizar la cuenta
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_user'])) {
            // Obtener datos del formulario
            $username = $_POST['username'];
            $email = $_POST['email'];

            // Actualizar información del usuario en la base de datos
            $stmt = $conexion->prepare('UPDATE accounts SET username = ?, email = ? WHERE id = ?');
            $stmt->bind_param('ssi', $username, $email, $_SESSION['id']);
            $stmt->execute();
            $stmt->close();

           
            header('Location: perfil.php');
        }
        ?>

        <h2>Eliminar Cuenta</h2>
        <!-- Formulario para eliminar cuenta -->
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <button type="submit" name="delete_user">Eliminar Mi Cuenta</button>
        </form>

        <?php
        // Verificar si se envió el formulario para eliminar la cuenta
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_user'])) {
    // Eliminar cuenta de la base de datos
    $stmt_delete = $conexion->prepare('DELETE FROM accounts WHERE id = ?');
    $stmt_delete->bind_param('i', $_SESSION['id']);
    $stmt_delete->execute();
    $stmt_delete->close();
    
    // Actualizar IDs restantes para que sean secuenciales
    $stmt_update = $conexion->prepare('UPDATE accounts SET id = id - 1 WHERE id > ?');
    $stmt_update->bind_param('i', $_SESSION['id']);
    $stmt_update->execute();
    $stmt_update->close();
    
    // Redirigir a la página de inicio después de eliminar la cuenta
    header('Location: cerrar-sesion.php');
             exit;
         }
         ?>
 
     </div>
 </body>
 </html>