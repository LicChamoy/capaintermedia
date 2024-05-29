<?php
session_start();
require_once 'metodos/conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    echo "Usuario no autenticado.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $producto_id = $_POST['producto_id'];
    $lista_id = $_POST['lista_id'];
    $usuario_id = $_SESSION['usuario_id'];

    $conexionBD = new ConexionBD();
    $conexion = $conexionBD->obtenerConexion();

    // Verificar si el producto ya está en la lista
    $stmt = $conexion->prepare("SELECT * FROM lista_productos WHERE producto_id = ? AND lista_id = ?");
    $stmt->bind_param("ii", $producto_id, $lista_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $producto_en_lista = $result->fetch_assoc();

    if ($producto_en_lista) {
        // Eliminar el producto de la lista
        $stmt = $conexion->prepare("DELETE FROM lista_productos WHERE producto_id = ? AND lista_id = ?");
        $stmt->bind_param("ii", $producto_id, $lista_id);
        $stmt->execute();
        echo "Producto eliminado de la lista.";
    } else {
        // Guardar el producto en la lista
        $stmt = $conexion->prepare("INSERT INTO lista_productos (producto_id, lista_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $producto_id, $lista_id);
        $stmt->execute();
        echo "Producto agregado a la lista.";
    }

    $conexionBD->cerrarConexion();
} else {
    echo "Acceso no válido.";
}
?>
