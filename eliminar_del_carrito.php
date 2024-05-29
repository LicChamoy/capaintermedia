<?php
if(isset($_GET['carrito_id']) && is_numeric($_GET['carrito_id'])) {
    $carrito_id = $_GET['carrito_id'];

    require_once 'metodos/conexion.php';
    session_start();
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: login.php");
        exit;
    }

    $usuario_id = $_SESSION['usuario_id'];
    $conexionBD = new ConexionBD();
    $conexion = $conexionBD->obtenerConexion();

    $stmt = $conexion->prepare("DELETE FROM carrito_compras WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("ii", $carrito_id, $usuario_id);
    $stmt->execute();

    if($stmt->affected_rows > 0) {
        header("Location: carrito.php");
        $stmt->close();
        $conexionBD->cerrarConexion();
        exit;
    } else {
        header("Location: carrito.php?error=1");
        $stmt->close();
        $conexionBD->cerrarConexion();
        exit;
    }

} else {
    header("Location: carrito.php?error=1");
    exit;
}
?>
