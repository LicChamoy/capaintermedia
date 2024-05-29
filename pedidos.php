<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Pedidos</title>
    <link rel="stylesheet" href="styles/pedidos.css">
</head>
<body>
    <?php include_once 'navbar/navbar.html'; ?>
    <style>
        <?php include 'navbar/styles.css'; ?>
    </style>

    <div class="container">
        <h1>Historial de Pedidos</h1>
        <div class="historial">
            <?php
            require_once 'metodos/conexion.php';
            session_start();
            
            if (!isset($_SESSION['usuario_id'])) {
                header("Location: login.php");
                exit;
            }

            $usuario_id = $_SESSION['usuario_id'];

            $conexionBD = new ConexionBD();
            $conexion = $conexionBD->obtenerConexion();

            $stmt = $conexion->prepare("SELECT p.nombre, hp.cantidad, hp.fecha FROM historial_pedidos hp INNER JOIN productos p ON hp.producto_id = p.id WHERE hp.usuario_id = ?");
            $stmt->bind_param("i", $usuario_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='pedido'>";
                    echo "<p><strong>Producto:</strong> " . htmlspecialchars($row['nombre']) . "</p>";
                    echo "<p><strong>Cantidad:</strong> " . htmlspecialchars($row['cantidad']) . "</p>";
                    echo "<p><strong>Fecha:</strong> " . htmlspecialchars($row['fecha']) . "</p>";
                    echo "</div>";
                }
            } else {
                echo "<p>Parece que aún no has realizado ningún pedido. :C</p>";
                echo "<button onclick=\"location.href='home.php'\">Ir a la página de inicio</button>";
            }

            $stmt->close();
            $conexionBD->cerrarConexion();
            ?>
        </div>
    </div>
</body>
</html>
