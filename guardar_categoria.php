<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombre"]);

    require_once 'metodos/conexion.php';
    $conexionBD = new ConexionBD();
    $conexion = $conexionBD->obtenerConexion();

    $stmt = $conexion->prepare("INSERT INTO categorias (nombre) VALUES (?)");
    $stmt->bind_param("s", $nombre);
    $stmt->execute();

    if ($stmt->execute()) {
        echo "<script>alert('Categoria registrada correctamente'); window.location.href = 'ventas.php';</script>";
    } else {
        echo "<script>alert('Error al registrar la categoria: " . $stmt->error . "'); window.location.href = 'ventas.php';</script>";
    }
    $conexionBD->cerrarConexion();
    exit();
}
?>
