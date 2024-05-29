<?php
session_start();

if (isset($_SESSION["usuario"])) {
    header("Location: home.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include_once "metodos/conexion.php";
    $conexionBD = new ConexionBD();
    $conexion = $conexionBD->obtenerConexion();


    $usuario_ingresado = $_POST["nombre_usuario"];
    $contraseña_ingresada = $_POST["password"];

    $sql = "SELECT * FROM usuarios WHERE usuario = '$usuario_ingresado' AND password = '$contraseña_ingresada'";
    $query = "SELECT tipo_usuario, id FROM usuarios WHERE usuario = '$usuario_ingresado' AND password = '$contraseña_ingresada'";

    $resultado = mysqli_query($conexion, $sql);
    $result = mysqli_query($conexion, $query);
    $conexionBD->cerrarConexion();
    if (mysqli_num_rows($resultado) == 1) {
        $_SESSION["usuario"] = $usuario_ingresado;
        $row = mysqli_fetch_assoc($result);
        $_SESSION["tipo_usuario"] = $row["tipo_usuario"];
        $_SESSION['usuario_id'] = $row['id'];
        header("Location: home.php");
        exit;
    } else {
        $error_message = "Usuario y/o contraseña incorrectos";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="styles/login.css">
</head>
<body>
    <div class="container">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="login-container">
            <div class="data" style="justify-content: center; align-content:center; text-align: center;">
                <br>
                <p>Inicio de Sesión</p>
                <br>
                <input type="text" placeholder="Usuario" id="nombre_usuario" name="nombre_usuario" value="<?php echo isset($_SESSION["usuario"]) ? $_SESSION["usuario"] : ''; ?>">
                <br>
                <input type="password" placeholder="Contraseña" id="password" name="password">
                <br>
                <?php if (isset($error_message)) echo "<p style='color: red;'>$error_message</p>"; ?>
            </div>
            <br> 
            <input type="submit" value="Login">
            <br>
            <p>¿No tienes una cuenta?<br><br><a href="registro.php"> Registrate</a></p>
        </form>
    </div>
</body>
</html>
