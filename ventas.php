<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ventas</title>
        <link rel="stylesheet" href="styles/ventas.css">
    </head>
    <body>
        <?php include_once 'navbar/navbar.html'; ?>
        <style>
            <?php include 'navbar/styles.css'; ?>
        </style>

        <div class="container">
            <div class="cart">
                <h1>Registro de Ventas</h1>
                <div class="ventas-list">
                    <?php
                    require_once 'metodos/conexion.php';
                    session_start();

                    if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'vendedor') {
                        header("Location: login.php");
                        exit;
                    }

                    $vendedor_id = $_SESSION['usuario_id'];

                    $conexionBD = new ConexionBD();
                    $conexion = $conexionBD->obtenerConexion();

                    $stmt = $conexion->prepare("SELECT p.nombre, hv.cantidad, hv.fecha FROM historial_ventas hv INNER JOIN productos p ON hv.producto_id = p.id WHERE hv.vendedor_id = ?");
                    $stmt->bind_param("i", $vendedor_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<div class='venta'>";
                            echo "<p><strong>Producto:</strong> " . htmlspecialchars($row['nombre']) . "</p>";
                            echo "<p><strong>Cantidad:</strong> " . htmlspecialchars($row['cantidad']) . "</p>";
                            echo "<p><strong>Fecha:</strong> " . htmlspecialchars($row['fecha']) . "</p>";
                            echo "</div>";
                        }
                    } else {
                        echo "<p>Parece que no se ha concretado ninguna venta.</p>";
                    }

                    $stmt->close();
                    $conexionBD->cerrarConexion();
                    ?>
                </div>
            </div>
            
            <div class="actions">
                <ul>
                    <li><a href="registrarproducto.php">Registrar Nuevo Producto</a></li>
                </ul>
                <ul>
                    <li><a href="registrarcategoria.php">Registrar Nueva Categoría</a></li>
                </ul>
            </div>
        </div>
    </body>
</html>
