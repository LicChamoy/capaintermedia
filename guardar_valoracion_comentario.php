<?php
require_once 'metodos/conexion.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit; 
}

if (!isset($_POST['producto_id']) || !isset($_POST['valoracion']) || !isset($_POST['comentario'])) {
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$producto_id = $_POST['producto_id'];
$valoracion = intval($_POST['valoracion']);
$comentario = $_POST['comentario'];

if ($valoracion < 1 || $valoracion > 5) {
    exit;
}

// Conectar a la base de datos
$conexionBD = new ConexionBD();
$conexion = $conexionBD->obtenerConexion();

// Insertar la valoración y el comentario en la base de datos
$stmt_valoracion = $conexion->prepare("INSERT INTO valoraciones (producto_id, usuario_id, valoracion) VALUES (?, ?, ?)");
$stmt_valoracion->bind_param("iii", $producto_id, $usuario_id, $valoracion);
$stmt_valoracion->execute();

$stmt_comentario = $conexion->prepare("INSERT INTO comentarios (producto_id, usuario_id, comentario) VALUES (?, ?, ?)");
$stmt_comentario->bind_param("iis", $producto_id, $usuario_id, $comentario);
$stmt_comentario->execute();

$stmt_valoracion->close();
$stmt_comentario->close();

// Actualizar la valoración promedio en la tabla de productos
$stmt_update = $conexion->prepare("UPDATE productos SET valoracion = ? WHERE id = ?");
$stmt_update->bind_param("di", $promedio, $producto_id);
$stmt_update->execute();

$stmt_update->close();

$conexionBD->cerrarConexion();

header("Location: detalle_producto.php?id=$producto_id");

?>
