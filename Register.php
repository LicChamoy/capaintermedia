<?php
session_start();
include_once "metodos/conexion.php";
$conexionBD = new ConexionBD();
$conexion = $conexionBD->obtenerConexion();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibe los datos del formulario
    $nombres = $_POST["nombre"];
    $apellidos = $_POST["apellidos"];
    $correo = $_POST["correo"];
    $usuario = $_POST["usuario"];
    $password = $_POST["confirmarContra"];
    $tipo_usuario = $_POST["tipo_usuario"];
    $sexo = $_POST["sexo"];
    $fecha_nacimiento = $_POST["fecha_nacimiento"];

    // Inserta los datos del usuario en la base de datos
    $query = "INSERT INTO usuarios (nombres, apellidos, correo, usuario, password, tipo_usuario, sexo, fecha_nacimiento) 
              VALUES ('$nombres', '$apellidos', '$correo', '$usuario', '$password', '$tipo_usuario', '$sexo', '$fecha_nacimiento')";

    if (mysqli_query($conexion, $query)) {
        // Redirecciona a la página de inicio de sesión si la inserción fue exitosa
        header("Location: login.php");
        exit;
    } else {
        // Muestra un mensaje de error si la inserción falla
        echo "Error: " . mysqli_error($conexion);
    }
}

$conexionBD->cerrarConexion();
?>
