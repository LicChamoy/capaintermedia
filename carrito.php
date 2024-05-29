<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Carrito de Compras</title>
        <link rel="stylesheet" href="styles/carrito.css">
    </head>
    <body>
        <?php include_once 'navbar/navbar.html'; ?>
        <style>
            <?php include 'navbar/styles.css'; ?>
        </style>

        <div class="container">
            <div class="cart">
                <h1>Tu Carrito de Compras</h1>
                    <?php
                    session_start();
                    require_once 'metodos/conexion.php';

                    if (!isset($_SESSION['usuario_id'])) {
                        header("Location: login.php");
                        exit;
                    }

                    $usuario_id = $_SESSION['usuario_id'];

                    $conexionBD = new ConexionBD();
                    $conexion = $conexionBD->obtenerConexion();

                    $stmt = $conexion->prepare("SELECT c.id AS carrito_id, p.id AS producto_id, p.nombre, p.precio, c.cantidad FROM carrito_compras c INNER JOIN productos p ON c.producto_id = p.id WHERE c.usuario_id = ?");
                    $stmt->bind_param("i", $usuario_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $_SESSION['carrito'] = [];
                        while ($row = $result->fetch_assoc()) {
                            $_SESSION['carrito'][] = [
                                'carrito_id' => $row['carrito_id'],
                                'producto_id' => $row['producto_id'],
                                'nombre' => $row['nombre'],
                                'precio' => $row['precio'],
                                'cantidad' => $row['cantidad']
                            ];
                            echo "<div class='producto'>";
                            echo "<p><strong>{$row['nombre']}</strong></p>";
                            echo "<p>Precio: {$row['precio']} MXN</p>";
                            echo "<p>Cantidad: <input type='number' min='1' value='{$row['cantidad']}'></p>";
                            echo "<button onclick='eliminarProducto({$row['carrito_id']})'>Eliminar</button>";
                            echo "</div>";
                        }
                        echo "<button onclick='procederPago()'>Proceder al Pago</button>";
                    } else {
                        echo "<p>Tu carrito está vacío.</p>";
                    }

                    $stmt->close();
                    $conexionBD->cerrarConexion();
                    ?>


                <script>
                    
                    function procederPago() {
                        window.location.href = 'confirmacion_pago.php';
                    }
                </script>
                
                
                

    </body>
</html>
