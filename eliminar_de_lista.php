<?php
session_start();
require_once 'metodos/conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_POST['producto_id']) && isset($_POST['lista_id'])) {
    $producto_id = $_POST['producto_id'];
    $lista_id = $_POST['lista_id'];
    $usuario_id = $_SESSION['usuario_id'];

    $conexionBD = new ConexionBD();
    $conexion = $conexionBD->obtenerConexion();

    $stmt = $conexion->prepare("DELETE FROM lista_productos WHERE usuario_id = ? AND producto_id = ? AND lista_id = ?");
    $stmt->bind_param("iii", $usuario_id, $producto_id, $lista_id);

    if ($stmt->execute()) {
        echo "Producto eliminado de la lista correctamente.";
    } else {
        echo "Error al eliminar el producto de la lista.";
    }

    $stmt->close();
    $conexionBD->cerrarConexion();
} else {
    echo "Parámetros incorrectos.";
}
?>
