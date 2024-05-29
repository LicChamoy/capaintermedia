<?php
require_once '../metodos/conexion.php';

session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $vendedor_id = $_GET['id'];
} else {
    echo "Error: No se proporcionó el ID del vendedor.";
    exit;
}

function enviarMensaje($vendedor_id, $comprador_id, $mensaje) {
    $conexionBD = new ConexionBD();
    $conexion = $conexionBD->obtenerConexion();

    $stmt = $conexion->prepare("INSERT INTO mensaje (chat_id, autor_id, contenido) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $chat_id, $autor_id, $contenido);

    $stmt_chat = $conexion->prepare("SELECT id FROM chat WHERE (vendedor_id = ? AND comprador_id = ?) OR (vendedor_id = ? AND comprador_id = ?)");
    $stmt_chat->bind_param("iiii", $vendedor_id, $comprador_id, $comprador_id, $vendedor_id);
    $stmt_chat->execute();
    $result_chat = $stmt_chat->get_result();

    if ($result_chat->num_rows > 0) {
        $chat = $result_chat->fetch_assoc();
        $chat_id = $chat['id'];
    } else {
        $stmt_nuevo_chat = $conexion->prepare("INSERT INTO chat (vendedor_id, comprador_id) VALUES (?, ?)");
        $stmt_nuevo_chat->bind_param("ii", $vendedor_id, $comprador_id);
        $stmt_nuevo_chat->execute();
        $chat_id = $stmt_nuevo_chat->insert_id;
    }
    $autor_id = $comprador_id;
    $contenido = $mensaje;

    $stmt->execute();

    $conexionBD->cerrarConexion();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mensaje = $_POST['mensaje'];
    $comprador_id = $_SESSION['usuario_id'];
    enviarMensaje($vendedor_id, $comprador_id, $mensaje);
    echo "Mensaje enviado correctamente.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviar Mensaje al Vendedor</title>
</head>
<body>
    <?php include_once '../navbar/navbar.html'; ?>
    <style>
        <?php include '../navbar/styles.css'; ?>
    </style>
    <h1>Enviar Mensaje al Vendedor</h1>
    <form method="POST">
        <textarea name="mensaje" rows="5" cols="50" placeholder="Escribe tu mensaje aquí" required></textarea>
        <br>
        <button type="submit">Enviar Mensaje</button>
    </form>
</body>
</html>
