<?php
error_reporting(E_ALL & ~E_NOTICE);

require_once 'metodos/conexion.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Crear una nueva lista de favoritos
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nueva_lista'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $publica = isset($_POST['publica']) ? 1 : 0;
    $imagen = isset($_FILES['imagen']) ? file_get_contents($_FILES['imagen']['tmp_name']) : null;

    $conexionBD = new ConexionBD();
    $conexion = $conexionBD->obtenerConexion();

    $stmt = $conexion->prepare("INSERT INTO listas_favoritos (usuario_id, nombre, descripcion, imagen, publica) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isssi", $usuario_id, $nombre, $descripcion, $imagen, $publica);
    $stmt->execute();

    $conexionBD->cerrarConexion();
}

// Obtener las listas del usuario
$conexionBD = new ConexionBD();
$conexion = $conexionBD->obtenerConexion();
$stmt = $conexion->prepare("SELECT * FROM listas_favoritos WHERE usuario_id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$listas_result = $stmt->get_result();
$listas = $listas_result->fetch_all(MYSQLI_ASSOC);


// Obtener las listas públicas (excluyendo las del usuario)
$stmt_publicas = $conexion->prepare("SELECT * FROM listas_favoritos WHERE usuario_id != ? AND publica = 1");
$stmt_publicas->bind_param("i", $usuario_id);
$stmt_publicas->execute();
$listas_publicas_result = $stmt_publicas->get_result();
$listas_publicas = $listas_publicas_result->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Listas de Favoritos</title>
    <link rel="stylesheet" href="styles/listas.css">
</head>
<body>
    <?php include_once 'navbar/navbar.html'; ?>
    <style>
        <?php include 'navbar/styles.css'; ?>
    </style>
    <div class="container">
        <h1>Mis Listas de Favoritos</h1>
        
        <form method="POST" enctype="multipart/form-data">
            <h2>Crear Nueva Lista</h2>
            <div>
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>
            <div>
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" rows="4"></textarea>
            </div>
            <div>
                <label for="imagen">Imagen:</label>
                <input type="file" id="imagen" name="imagen" accept="image/*">
            </div>
            <div>
                <label for="publica">¿Lista pública?</label>
                <input type="checkbox" id="publica" name="publica">
            </div>
            <div>
                <button type="submit" name="nueva_lista">Crear Lista</button>
            </div>
        </form>
        
        <h2>Mis Listas</h2>
        <div class="listas">
            <?php foreach ($listas as $lista): ?>
                <div class="lista">
                    <a href="productos_lista.php?id=<?php echo $lista['id']; ?>"><img src="data:image/jpeg;base64,<?php echo base64_encode($lista['imagen']); ?>" alt="Imagen de la Lista" style="max-width: 100px;"></a>
                    <h3><?php echo htmlspecialchars($lista['nombre']); ?></h3>
                    <p><?php echo htmlspecialchars($lista['descripcion']); ?></p>
                    <p><?php echo $lista['publica'] ? 'Pública' : 'Privada'; ?></p>
                    <a href="editar_lista.php?id=<?php echo $lista['id']; ?>"><button type="button">Editar</button></a>
                </div>
            <?php endforeach; ?>
        </div>

        <br>

        <h2>Listas Públicas</h2>
        <div class="listas">
            <!-- Mostrar las listas públicas -->
            <?php foreach ($listas_publicas as $lista): ?>
                <div class="lista">
                    <a href="productos_lista.php?id=<?php echo $lista['id']; ?>">
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($lista['imagen']); ?>" alt="Imagen de la Lista" style="max-width: 100px;">
                    </a>
                    <h3><?php echo htmlspecialchars($lista['nombre']); ?></h3>
                    <p><?php echo htmlspecialchars($lista['descripcion']); ?></p>
                    <p><?php echo $lista['publica'] ? 'Pública' : 'Privada'; ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
