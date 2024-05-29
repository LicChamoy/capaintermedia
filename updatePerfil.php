<?php
require_once 'metodos/conexion.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

$nombres = $_POST['nombre'];
$apellidos = $_POST['apellido'];
$correo = $_POST['correo'];
$fecha_nacimiento = $_POST['fecha_nacimiento'];
$password = $_POST['password'];
$Cpassword = $_POST['Cpassword'];
$foto_perfil = null;

if ($_FILES['foto_perfil']['tmp_name']) {
    $foto_perfil = file_get_contents($_FILES['foto_perfil']['tmp_name']);
}

$conexionBD = new ConexionBD();
$conexion = $conexionBD->obtenerConexion();

if ($password && $password === $Cpassword) {
    $password_hashed = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $conexion->prepare("UPDATE usuarios SET nombres = ?, apellidos = ?, correo = ?, fecha_nacimiento = ?, password = ?, foto_perfil = ? WHERE id = ?");
    $stmt->bind_param("ssssssi", $nombres, $apellidos, $correo, $fecha_nacimiento, $password_hashed, $foto_perfil, $usuario_id);
} else {
    $stmt = $conexion->prepare("UPDATE usuarios SET nombres = ?, apellidos = ?, correo = ?, fecha_nacimiento = ?, foto_perfil = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $nombres, $apellidos, $correo, $fecha_nacimiento, $foto_perfil, $usuario_id);
}

if ($stmt->execute()) {
    header("Location: perfil.php?success=1");
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conexionBD->cerrarConexion();
?>
