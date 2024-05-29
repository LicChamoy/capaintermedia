<?php
require_once 'metodos/conexion.php';
$conexionBD = new ConexionBD();
$conexion = $conexionBD->obtenerConexion();
$stmt = $conexion->prepare("SELECT id, nombre, imagen1, precio, valoracion FROM productos WHERE cantidad_disponible > 0");
$stmt->execute();
$result = $stmt->get_result();
$productos = $result->fetch_all(MYSQLI_ASSOC);
$conexionBD->cerrarConexion();
?>


<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="styles/home.css">
        <title>Mi Tienda en Línea</title>
    </head>
    <body>
                <?php include_once 'navbar/navbar.html'; ?>
                <style>
                    <?php include 'navbar/styles.css'; ?>
                </style>
        <div class="container">
            <div class="ofertas">
                <h2>Productos disponibles</h2>
                <div class="productos-disponibles">
                    <?php foreach ($productos as $producto): ?>
                        <div class="producto">
                            <a href="detalle_producto.php?id=<?php echo $producto['id']; ?>" class="ver-detalles">
                                <?php
                                $imagen_data = $producto['imagen1'];
                                $imagen_base64 = 'data:image/jpeg;base64,' . base64_encode($imagen_data);
                                ?>
                                <img src="<?php echo $imagen_base64; ?>" alt="<?php echo $producto['nombre']; ?>">
                                <h3><?php echo $producto['nombre']; ?></h3>
                                <p>Calificación: <?php echo $producto['valoracion']; ?>/5</p>
                                <p>Precio: <?php echo $producto['precio']; ?></p>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="sugerencias">
                <h2>Sugerencias</h2>
                <p>Aquí se mostrarán las sugerencias de productos.</p>
            </div>
        </div>
    </body>
</html>
