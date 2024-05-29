<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

require_once 'metodos/conexion.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $lista_id = $_GET['id'];
    $usuario_id = $_SESSION['usuario_id'];

    $conexionBD = new ConexionBD();
    $conexion = $conexionBD->obtenerConexion();

    // Obtener la lista de favoritos
    $stmt = $conexion->prepare("SELECT * FROM listas_favoritos WHERE id = ?");
    $stmt->bind_param("i", $lista_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $lista = $result->fetch_assoc();

    // Si la lista no existe o es privada y el usuario no es el propietario, redirigirlo
    if (!$lista || ($lista['publica'] == 0 && $lista['usuario_id'] != $usuario_id)) {
        echo "<p>No tienes permiso para ver esta lista.</p>";
        exit;
    }

    // Obtener los productos de la lista
    $stmt_productos = $conexion->prepare("SELECT * FROM productos WHERE id = ?");
    $stmt_productos->bind_param("i", $lista_id);
    $stmt_productos->execute();
    $result_productos = $stmt_productos->get_result();
    $productos = $result_productos->fetch_all(MYSQLI_ASSOC);

    $conexionBD->cerrarConexion();
} else {
    echo "<p>ID de lista no válido.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Productos de la Lista</title>
        <link rel="stylesheet" href="styles/productos_lista.css">
    </head>
    <body>
        <?php include_once 'navbar/navbar.html'; ?>
        <style>
            <?php include 'navbar/styles.css'; ?>
        </style>
        <div class="container">
            <h1>Productos de la Lista: <?php echo htmlspecialchars($lista['nombre']); ?></h1>
            
            <div class="productos">
                <?php foreach ($productos as $producto): ?>
                    <div class="producto">
                        <a href="detalle_producto.php?id=<?php echo $producto['id']; ?>"><img src="data:image/jpeg;base64,<?php echo base64_encode($producto['imagen1']); ?>" alt="Imagen del Producto" style="max-width: 100px;"></a>
                        <h3><a href="detalle_producto.php?id=<?php echo $producto['id']; ?>"><?php echo htmlspecialchars($producto['nombre']); ?></a></h3>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </body>
</html>
