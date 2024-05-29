<?php
require_once 'metodos/conexion.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$conexionBD = new ConexionBD();
$conexion = $conexionBD->obtenerConexion();

$usuario_id = $_SESSION['usuario_id'];

if (!isset($_SESSION['carrito']) || count($_SESSION['carrito']) === 0) {
    header("Location: carrito.php");
    exit;
}

$conexion->begin_transaction();

try {
    foreach ($_SESSION['carrito'] as $producto) {
        $carrito_id = $producto['carrito_id']; // Corregido aquí

        $stmt = $conexion->prepare("DELETE FROM carrito_compras WHERE id = ? AND usuario_id = ?");
        $stmt->bind_param("ii", $carrito_id, $usuario_id);
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            throw new Exception("No se pudo eliminar el producto del carrito.");
        }

        $producto_id = $producto['producto_id'];
        $precio = $producto['precio'];
        $cantidad = isset($producto['cantidad']) ? $producto['cantidad'] : 1;

        $stmt = $conexion->prepare("INSERT INTO historial_pedidos (usuario_id, producto_id, cantidad, precio) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiid", $usuario_id, $producto_id, $cantidad, $precio);
        $stmt->execute();

        $stmt = $conexion->prepare("SELECT vendedor_id FROM productos WHERE id = ?");
        $stmt->bind_param("i", $producto_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $producto_info = $result->fetch_assoc();
        $vendedor_id = $producto_info['vendedor_id'];

        $stmt = $conexion->prepare("INSERT INTO historial_ventas (vendedor_id, producto_id, cantidad, precio) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiid", $vendedor_id, $producto_id, $cantidad, $precio);
        $stmt->execute();
    }

    $conexion->commit();

    unset($_SESSION['carrito']);

    header("Location: confirmacion.php");
    exit;

} catch (Exception $e) {
    $conexion->rollback();

    echo "Hubo un error procesando tu pedido: " . $e->getMessage();
}

$conexionBD->cerrarConexion();
?>
