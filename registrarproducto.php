<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Registro de Productos</title>
        <link rel="stylesheet" href="styles/registroproductos.css">
    </head>
    <body>

        <?php include_once 'navbar/navbar.html'; ?>
        <style>
            <?php include 'navbar/styles.css'; ?>
        </style>

        <div class="container">
            <h1>Registro de Productos</h1>
            <form method="POST" enctype="multipart/form-data" action="registrar_producto.php">
                <div>
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                <div>
                    <label for="descripcion">Descripción:</label>
                    <textarea id="descripcion" name="descripcion" rows="4" required></textarea>
                </div>
                <div>
                    <label for="imagen1">Imagen 1:</label>
                    <input type="file" id="imagen1" name="imagen1" accept="image/*" required>
                </div>
                <div>
                    <label for="imagen2">Imagen 2:</label>
                    <input type="file" id="imagen2" name="imagen2" accept="image/*" required>
                </div>
                <div>
                    <label for="imagen3">Imagen 3:</label>
                    <input type="file" id="imagen3" name="imagen3" accept="image/*" required>
                </div>
                <div>
                    <label for="video">Video:</label>
                    <input type="file" id="video" name="video" accept="video/*" required>
                </div>
                <div>
                    <label for="categoria">Categoría:</label>
                    <select id="categoria" name="categoria" required>
                        <?php
                        require_once 'metodos/conexion.php';
                        $conexionBD = new ConexionBD();
                        $conexion = $conexionBD->obtenerConexion();
                        $result = $conexion->query("SELECT id, nombre FROM categorias");
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['id'] . "'>" . $row['nombre'] . "</option>";
                        }
                        $conexionBD->cerrarConexion();
                        ?>
                    </select>
                </div>
                <div>
                    <label for="tipo">Tipo (cotización/venta):</label>
                    <select id="tipo" name="tipo" required>
                        <option value="cotizacion">Cotización</option>
                        <option value="venta">Venta</option>
                    </select>
                </div>
                <div>
                    <label for="precio">Precio (MXN):</label>
                    <input type="number" id="precio" name="precio" min="0">
                </div>
                <div>
                    <label for="cantidad">Cantidad disponible:</label>
                    <input type="number" id="cantidad" name="cantidad" min="0" required>
                </div>
                <div>
                    <button type="submit">Registrar Producto</button>
                </div>
            </form>
        </div>
    </body>
</html>
