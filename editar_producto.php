<?php
require_once 'metodos/conexion.php';
require_once 'middleware/auth.php';
require_once 'middleware/authorize.php';
require_once 'middleware/validate.php';

$id_producto = $_GET['id'];


$conexionBD = new ConexionBD();
$conexion = $conexionBD->obtenerConexion();

session_start();

verificarAutenticacion();
verificarPermisos('vendedor');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_producto = $_POST['id_producto'];

    $errores = validarDatosProducto($_POST);
    if (!empty($errores)) {
        foreach ($errores as $error) {
            echo "<p style='color: red;'>$error</p>";
        }
        exit();
    }

    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $categoria_id = $_POST['categoria'];
    $tipo = $_POST['tipo'];
    $precio = $_POST['precio'];
    $cantidad = $_POST['cantidad'];
    $vendedor_id = $_SESSION['usuario_id'];

    // Procesar imágenes y video
    $imagen1 = file_get_contents($_FILES['imagen1']['tmp_name']);
    $imagen2 = file_get_contents($_FILES['imagen2']['tmp_name']);
    $imagen3 = file_get_contents($_FILES['imagen3']['tmp_name']);
    $video = file_get_contents($_FILES['video']['tmp_name']);

    // Actualizar el producto en la base de datos
    $stmt = $conexion->prepare("UPDATE productos SET nombre=?, descripcion=?, imagen1=?, imagen2=?, imagen3=?, video=?, categoria_id=?, tipo_venta=?, precio=?, cantidad_disponible=? WHERE id=?");
    $stmt->bind_param("ssssssisdiii", $nombre, $descripcion, $imagen1, $imagen2, $imagen3, $video, $categoria_id, $tipo, $precio, $cantidad, $id_producto);

    if ($stmt->execute()) {
        echo "<script>alert('Producto actualizado correctamente'); window.location.href = 'home.php';</script>";
    } else {
        echo "<script>alert('Error al actualizar el producto: " . $stmt->error . "'); window.location.href = 'home.php';</script>";
    }
}

$conexionBD->cerrarConexion();
?>
