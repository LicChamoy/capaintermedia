<?php
require_once '../metodos/conexion.php';

session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['chat_id'])) {
    $chat_id = $_GET['chat_id'];
} else {
    echo "Error: No se proporcionó el ID del chat.";
    exit;
}

function obtenerMensajesChat($chat_id) {
    $conexionBD = new ConexionBD();
    $conexion = $conexionBD->obtenerConexion();

    $stmt = $conexion->prepare("SELECT m.*, u.usuario FROM mensaje m JOIN usuarios u ON m.autor_id = u.id WHERE m.chat_id = ? ORDER BY m.timestamp");
    $stmt->bind_param("i", $chat_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $mensajes = $result->fetch_all(MYSQLI_ASSOC);

    $conexionBD->cerrarConexion();

    return $mensajes;
}

$mensajes = obtenerMensajesChat($chat_id);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Chat</title>
    <link rel="stylesheet" href="../styles/historial_chat.css">
</head>
<body>
    <?php include_once '../navbar/navbar.html'; ?>
    <style>
        <?php include '../navbar/styles.css'; ?>
    </style>
    <div class="container">
        <h1>Historial de Chat</h1>
        <div class="mensajes">
            <?php foreach ($mensajes as $mensaje): ?>
                <div class="mensaje">
                    <p><strong><?php echo $mensaje['usuario']; ?>:</strong> <?php echo $mensaje['contenido']; ?></p>
                    <p class="timestamp"><?php echo $mensaje['timestamp']; ?></p>
                </div>
            <?php endforeach; ?>
            <form action="enviar_mensaje.php" method="POST">
                <input type="hidden" name="chat_id" value="<?php echo $chat_id; ?>">
                <input type="hidden" name="autor_id" value="<?php echo $_SESSION['usuario_id']; ?>">
                <textarea name="contenido" rows="3" placeholder="Escribe tu mensaje aquí" required></textarea>
                <button type="submit">Enviar Mensaje</button>
            </form>
        </div>
    </div>
</body>
</html>
