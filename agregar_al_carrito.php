<?php

require_once 'metodos/conexion.php';
session_start();

if (isset($_POST['producto_id']) && isset($_SESSION['usuario_id'])) {
    $producto_id = $_POST['producto_id'];
    $usuario_id = $_SESSION['usuario_id'];

    $conexionBD = new ConexionBD();
    $conexion = $conexionBD->obtenerConexion();

    $stmt = $conexion->prepare("SELECT * FROM carrito_compras WHERE usuario_id = ? AND producto_id = ?");
    $stmt->bind_param("ii", $usuario_id, $producto_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0) {
        echo "El producto ya está en tu carrito.";
    } else {
        $stmt_insert = $conexion->prepare("INSERT INTO carrito_compras (usuario_id, producto_id) VALUES (?, ?)");
        $stmt_insert->bind_param("ii", $usuario_id, $producto_id);
        if ($stmt_insert->execute()) {
            echo "Producto agregado al carrito exitosamente.";
        } else {
            echo "Error al agregar el producto al carrito.";
        }
        $stmt_insert->close();
    }

    $conexionBD->cerrarConexion();
} else {
    echo "Error: No se proporcionó el ID del producto o el usuario no está autenticado.";
}
?>
