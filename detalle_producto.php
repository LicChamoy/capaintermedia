<?php
require_once 'metodos/conexion.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $producto_id = $_GET['id'];
    $usuario_id = $_SESSION['usuario_id'];

    $conexionBD = new ConexionBD();
    $conexion = $conexionBD->obtenerConexion();

    $stmt_producto = $conexion->prepare("SELECT p.*, c.nombre AS categoria_nombre FROM productos p LEFT JOIN categorias c ON p.categoria_id = c.id WHERE p.id = ?");
    $stmt_producto->bind_param("i", $producto_id);
    $stmt_producto->execute();
    $result_producto = $stmt_producto->get_result();
    $producto = $result_producto->fetch_assoc();
    $stmt_producto->close();

    // Obtener las listas del usuario
    $stmt_listas = $conexion->prepare("SELECT * FROM listas_favoritos WHERE usuario_id = ?");
    $stmt_listas->bind_param("i", $usuario_id);
    $stmt_listas->execute();
    $result_listas = $stmt_listas->get_result();
    $listas = $result_listas->fetch_all(MYSQLI_ASSOC);
    $stmt_listas->close();

    // Obtener comentarios del producto
    $stmt_comentarios = $conexion->prepare("SELECT c.*, u.usuario AS nombre_usuario FROM comentarios c INNER JOIN usuarios u ON c.usuario_id = u.id WHERE c.producto_id = ?");
    $stmt_comentarios->bind_param("i", $producto_id);
    $stmt_comentarios->execute();
    $result_comentarios = $stmt_comentarios->get_result();
    $comentarios = $result_comentarios->fetch_all(MYSQLI_ASSOC);
    $stmt_comentarios->close();

    // Obtener valoraciones del producto
    $stmt_valoraciones = $conexion->prepare("SELECT valoracion FROM valoraciones WHERE producto_id = ?");
    $stmt_valoraciones->bind_param("i", $producto_id);
    $stmt_valoraciones->execute();
    $result_valoraciones = $stmt_valoraciones->get_result();
    $valoraciones = $result_valoraciones->fetch_all(MYSQLI_ASSOC);
    $stmt_valoraciones->close();

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
        <title>Detalles del Producto</title>
        <link rel="stylesheet" href="styles/detalle_producto.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    </head>
    <body>
        <?php include_once 'navbar/navbar.html'; ?>
        <style>
            <?php include 'navbar/styles.css'; ?>
        </style>
        <div class="container">
            <h2>Detalles del Producto</h2>
            <div class="producto-contenedor">
                <div class="imagenes-miniaturas">
                    <?php
                    $imagen1_base64 = 'data:image/jpeg;base64,' . base64_encode($producto['imagen1']);
                    $imagen2_base64 = 'data:image/jpeg;base64,' . base64_encode($producto['imagen2']);
                    $imagen3_base64 = 'data:image/jpeg;base64,' . base64_encode($producto['imagen3']);
                    $video_base64 = 'data:video/mp4;base64,' . base64_encode($producto['video']);
                    ?>
                    <img src="<?php echo $imagen1_base64; ?>" alt="Imagen 1" onclick="cambiarImagenPrincipal('<?php echo $imagen1_base64; ?>', 'image')">
                    <img src="<?php echo $imagen2_base64; ?>" alt="Imagen 2" onclick="cambiarImagenPrincipal('<?php echo $imagen2_base64; ?>', 'image')">
                    <img src="<?php echo $imagen3_base64; ?>" alt="Imagen 3" onclick="cambiarImagenPrincipal('<?php echo $imagen3_base64; ?>', 'image')">
                    <video width="100" height="100" onclick="cambiarImagenPrincipal('<?php echo $video_base64; ?>', 'video')">
                        <source src="<?php echo $video_base64; ?>" type="video/mp4">
                    </video>
                </div>
                <div class="imagen-principal">
                    <img id="imagenPrincipal" src="<?php echo $imagen1_base64; ?>" alt="Imagen Principal">
                    <video id="videoPrincipal" width="320" height="240" controls style="display: none;">
                        <source src="<?php echo $video_base64; ?>" type="video/mp4">
                    </video>
                </div>
                <div class="producto-info">
                    <h3><?php echo $producto['nombre']; ?></h3>
                    <p>Precio: <?php echo $producto['precio']; ?> MXN</p>
                    <p>Categoría: <?php echo $producto['categoria_nombre']; ?></p>
                    <p><?php echo $producto['descripcion']; ?></p>

                    <?php if ($_SESSION['usuario_id'] == $producto['vendedor_id']): ?>
                        <a href="editarproducto.php?id=<?php echo $producto_id; ?>"><button type="button">Editar Producto</button></a>
                    <?php else: ?>
                        <button type="button" id="agregarCarritoBtn">Añadir al carrito</button>
                    <?php endif; ?>
                    <a href="home.php"><button type="button" class="cancelar">Cancelar</button></a>

                    <!-- Agregar a Lista -->
                    <h4>Añadir a Lista de Favoritos</h4>
                    <select id="listaFavoritos">
                        <option value="0">No se seleccionó ninguna lista</option>
                        <?php foreach ($listas as $lista): ?>
                            <option value="<?php echo $lista['id']; ?>"><?php echo htmlspecialchars($lista['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>

                    <?php if (empty($listas)): ?>
                        <p>No se seleccionó ninguna lista</p>
                    <?php else: ?>
                        <?php
                        $conexionBD = new ConexionBD();
                        $conexion = $conexionBD->obtenerConexion();

                        $stmt = $conexion->prepare("SELECT * FROM lista_productos WHERE producto_id = ? AND lista_id = ?");
                        $stmt->bind_param("ii", $producto_id, $lista['id']);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $producto_en_lista = $result->fetch_assoc();

                        $conexionBD->cerrarConexion();

                        if ($producto_en_lista) {
                            echo '<button type="button" onclick="eliminarDeFavoritos(' . $producto_id . ', ' . $producto_en_lista['lista_id'] . ')">Eliminar de Lista</button>';
                        } else {
                            echo '<button type="button" onclick="agregarAFavoritos(' . $producto_id . ')">Añadir a Lista</button>';
                        }
                        ?>
                    <?php endif; ?>

                    <a href="../chat/contactar_vendedor.php?id=<?php echo $producto['vendedor_id']; ?>"><button type="button">Contactar al Vendedor</button></a>
                </div>
                </div>
        </div>
    </div>
    <div class="container">
        <div class="producto-contenedor">
            <div class="producto-info">
                <form method="post" action="guardar_valoracion_comentario.php">
                    <input type="hidden" name="producto_id" value="<?php echo $producto_id; ?>">
                    <h3>Valoración</h3>
                    <select name="valoracion">
                        <option value="1">1 Estrella</option>
                        <option value="2">2 Estrellas</option>
                        <option value="3">3 Estrellas</option>
                        <option value="4">4 Estrellas</option>
                        <option value="5">5 Estrellas</option>
                    </select>
                    <br><br>
                    <h3>Comentario</h3>
                    <textarea name="comentario" rows="4" cols="50"></textarea>
                    <br><br>
                    <input type="submit" value="Enviar">
                </form>
                <h3>Comentarios</h3>
                <div>
                    <?php foreach ($comentarios as $key => $comentario): ?>
                        <p><strong><?php echo $comentario['nombre_usuario']; ?></strong></p>
                        <p><strong>Comentario:</strong> <?php echo $comentario['comentario']; ?></p>
                        <hr>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        function cambiarImagenPrincipal(src, type) {
            var imagenPrincipal = document.getElementById('imagenPrincipal');
            var videoPrincipal = document.getElementById('videoPrincipal');

            if (type === 'image') {
                imagenPrincipal.src = src;
                imagenPrincipal.style.display = 'block';
                videoPrincipal.style.display = 'none';
            } else if (type === 'video') {
                videoPrincipal.src = src;
                videoPrincipal.style.display = 'block';
                imagenPrincipal.style.display = 'none';
            }
        }

        function agregarAFavoritos(producto_id) {
            var lista_id = document.getElementById('listaFavoritos').value;
            $.ajax
            ({
                url: 'guardar_en_lista.php',
                type: 'POST',
                data: { producto_id: producto_id, lista_id: lista_id },
                success: function(response) {
                    alert(response);
                    location.reload();
                },
                error: function() {
                    alert('Error al agregar el producto a la lista.');
                }
            });
        }

        function eliminarDeFavoritos(producto_id, lista_id) {
            if (confirm('¿Seguro que deseas eliminar este producto de la lista?')) {
                $.ajax({
                    url: 'guardar_en_lista.php',
                    type: 'POST',
                    data: { producto_id: producto_id, lista_id: lista_id },
                    success: function(response) {
                        alert(response);
                        location.reload();
                    },
                    error: function() {
                        alert('Error al eliminar el producto de la lista.');
                    }
                });
            }
        }
    </script>

    <script>
        $(document).ready(function() {
            $('#agregarCarritoBtn').click(function() {
                var producto_id = <?php echo $producto_id; ?>;
                $.ajax({
                    url: 'agregar_al_carrito.php',
                    type: 'POST',
                    data: { producto_id: producto_id },
                    success: function(response) {
                        alert(response);
                        location.reload();
                    },
                    error: function() {
                        alert('Error al agregar el producto al carrito.');
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            // Función para enviar la valoración y comentario mediante AJAX
            $("#formulario-valoracion").submit(function(event) {
                // Detener el envío del formulario normal
                event.preventDefault();

                // Obtener los valores del formulario
                var formData = $(this).serialize();

                // Enviar los datos mediante AJAX
                $.ajax({
                    type: "POST",
                    url: "guardar_valoracion_comentario.php", // Ruta al script PHP que procesará los datos
                    data: formData,
                    success: function(response) {
                        // Manejar la respuesta del servidor, por ejemplo, mostrar un mensaje de éxito
                        alert("Valoración y comentario guardados correctamente.");
                    },
                    error: function() {
                        // Manejar cualquier error que ocurra durante la solicitud AJAX
                        alert("Error al guardar la valoración y comentario.");
                    }
                });
            });
        });
    </script>
    </body>
</html>