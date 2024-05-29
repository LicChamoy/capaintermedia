<?php
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

    $stmt = $conexion->prepare("SELECT * FROM listas_favoritos WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("ii", $lista_id, $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $lista = $result->fetch_assoc();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $publica = isset($_POST['publica']) ? 1 : 0;
        $imagen = isset($_FILES['imagen']) ? file_get_contents($_FILES['imagen']['tmp_name']) : $lista['imagen'];

        $stmt = $conexion->prepare("UPDATE listas_favoritos SET nombre = ?, descripcion = ?, imagen = ?, publica = ? WHERE id = ? AND usuario_id = ?");
        $stmt->bind_param("sssiii", $nombre, $descripcion, $imagen, $publica, $lista_id, $usuario_id);
        $stmt->execute();
        
        header("Location: listas.php");
        exit;
    }

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
        <title>Editar Lista de Favoritos</title>
        <link rel="stylesheet" href="styles/listas.css">
    </head>
    <body>
        
        <?php include_once 'navbar/navbar.html'; ?>
        <style>
            <?php include 'navbar/styles.css'; ?>
        </style>
        <div class="container">
            <h1>Editar Lista de Favoritos</h1>
            
            <form method="POST" enctype="multipart/form-data">
                <div>
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($lista['nombre']); ?>" required>
                </div>
                <div>
                    <label for="descripcion">Descripción:</label>
                    <textarea id="descripcion" name="descripcion" rows="4"><?php echo htmlspecialchars($lista['descripcion']); ?></textarea>
                </div>
                <div>
                    <label for="imagen">Imagen:</label>
                    <?php
                    $imagen_data = $lista['imagen'];
                    $imagen_base64 = 'data:image/jpeg;base64,' . base64_encode($imagen_data);
                    ?>
                    <img id="previewImagen" src="<?php echo $imagen_base64; ?>" alt="Vista previa de la imagen" style="max-width: 200px;"><br>
                    <input type="file" id="imagen" name="imagen" accept="image/*">
                </div>
                <div>
                    <label for="publica">¿Lista pública?</label>
                    <input type="checkbox" id="publica" name="publica" <?php echo $lista['publica'] ? 'checked' : ''; ?>>
                </div>
                <div>
                    <button type="submit">Actualizar Lista</button>
                </div>
            </form>
        </div>
        <script>
            document.getElementById('imagen').addEventListener('change', function() {
                var preview = document.getElementById('previewImagen');
                var reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(this.files[0]);
            });
        </script>
    </body>
</html>
