<?php
require_once 'metodos/conexion.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    // Obtener el ID del producto
    $producto_id = $_GET['id'];

    $conexionBD = new ConexionBD();
    $conexion = $conexionBD->obtenerConexion();

    $stmt = $conexion->prepare("SELECT * FROM productos WHERE id = ?");
    $stmt->bind_param("i", $producto_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $producto = $result->fetch_assoc();

    $conexionBD->cerrarConexion();
} else {
    echo "<p>El ID del producto no es válido.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Editar Producto</title>
        <link rel="stylesheet" href="styles/editarproductos.css">
    </head>
    <body>
        <?php include_once 'navbar/navbar.html'; ?>
        <style>
            <?php include 'navbar/styles.css'; ?>
        </style>
        <div class="container">
            <h1>Editar Producto</h1>
            <div class="producto-contenedor">
                <form method="POST" enctype="multipart/form-data" action="editar_producto.php">
                    <div>
                        <label for="nombre">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" value="<?php echo $producto['nombre']; ?>">
                    </div>
                    <div>
                        <label for="descripcion">Descripción:</label>
                        <textarea id="descripcion" name="descripcion" rows="4" required><?php echo $producto['descripcion']; ?></textarea>
                    </div>


                    <div>
                        <label for="imagen1">Imagen 1:</label><br>
                        <?php
                        $imagen_data = $producto['imagen1'];
                        $imagen1_base64 = 'data:image/jpeg;base64,' . base64_encode($imagen_data);
                        ?>
                        <img id="previewImagen1" src="<?php echo $imagen1_base64; ?>" alt="Vista previa de la imagen 1" style="max-width: 200px;"><br>
                        <input type="file" id="imagen1" name="imagen1" accept="image/*">
                    </div>

                    <div>
                        <label for="imagen2">Imagen 2:</label><br>
                        <?php
                        $imagen_data = $producto['imagen2'];
                        $imagen2_base64 = 'data:image/jpeg;base64,' . base64_encode($imagen_data);
                        ?>
                        <img id="previewImagen2" src="<?php echo $imagen2_base64; ?>" alt="Vista previa de la imagen 2" style="max-width: 200px;"><br>
                        <input type="file" id="imagen2" name="imagen2" accept="image/*">
                    </div>

                    <div>
                        <label for="imagen3">Imagen 3:</label><br>
                        <?php
                        $imagen_data = $producto['imagen3'];
                        $imagen3_base64 = 'data:image/jpeg;base64,' . base64_encode($imagen_data);
                        ?>
                        <img id="previewImagen3" src="<?php echo $imagen3_base64; ?>" alt="Vista previa de la imagen 3" style="max-width: 200px;"><br>
                        <input type="file" id="imagen3" name="imagen3" accept="image/*">
                    </div>

                    <div>
                        <label for="video">Video:</label><br>
                        <video id="previewVideo" width="320" height="240" controls>
                            <?php
                            $video_data = $producto['video'];
                            $video_base64 = 'data:video/mp4;base64,' . base64_encode($video_data);
                            ?>
                            <source src="<?php echo $video_base64; ?>" type="video/mp4">
                            Your browser does not support the video tag.
                        </video><br>
                        <input type="file" id="video" name="video" accept="video/*">
                    </div>

                    <div>
                        <label for="categoria">Categoría:</label>
                        <select id="categoria" name="categoria" required>
                            <?php
                            $conexionBD = new ConexionBD();
                            $conexion = $conexionBD->obtenerConexion();
                            $result = $conexion->query("SELECT id, nombre FROM categorias");
                            while ($row = $result->fetch_assoc()) {
                                $selected = ($row['id'] == $producto['categoria_id']) ? 'selected' : '';
                                echo "<option value='" . $row['id'] . "' $selected>" . $row['nombre'] . "</option>";
                            }
                            $conexionBD->cerrarConexion();
                            ?>
                        </select>
                    </div>
                    <div>
                        <label for="tipo">Tipo (cotización/venta):</label>
                        <select id="tipo" name="tipo" required>
                            <option value="cotizacion" <?php echo ($producto['tipo_venta'] == 'cotizacion') ? 'selected' : ''; ?>>Cotización</option>
                            <option value="venta" <?php echo ($producto['tipo_venta'] == 'venta') ? 'selected' : ''; ?>>Venta</option>
                        </select>
                    </div>
                    <div>
                        <label for="precio">Precio (MXN):</label>
                        <input type="number" id="precio" name="precio" min="0" value="<?php echo $producto['precio']; ?>">
                    </div>
                    <div>
                        <label for="cantidad">Cantidad disponible:</label>
                        <input type="number" id="cantidad" name="cantidad" min="0" required value="<?php echo $producto['cantidad_disponible']; ?>">
                    </div>

                    <div>
                        <button type="submit">Actualizar Producto</button>
                        <a href="home.php"><button type="button">Cancelar</button></a>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function previewImage(input, previewId) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var preview = document.getElementById(previewId);
                        preview.src = e.target.result;
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }

            function previewVideo(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var video = document.getElementById('previewVideo');
                        video.src = e.target.result;
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }

            document.getElementById('imagen1').addEventListener('change', function() {
                previewImage(this, 'previewImagen1');
            });
            document.getElementById('imagen2').addEventListener('change', function() {
                previewImage(this, 'previewImagen2');
            });
            document.getElementById('imagen3').addEventListener('change', function() {
                previewImage(this, 'previewImagen3');
            });
            document.getElementById('video').addEventListener('change', function() {
                previewVideo(this);
            });
        </script>
    </body>
</html>