<?php
require_once '../metodos/conexion.php';

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$tipo_usuario = $_SESSION['tipo_usuario'];

function obtenerChats($usuario_id, $tipo_usuario) {
    $conexionBD = new ConexionBD();
    $conexion = $conexionBD->obtenerConexion();

    // Consulta SQL para obtener los chats del usuario según su tipo
    if ($tipo_usuario === 'vendedor') {
        $stmt = $conexion->prepare("SELECT c.id, u.usuario AS otro_usuario
                                    FROM chat c
                                    INNER JOIN usuarios u ON c.comprador_id = u.id
                                    WHERE c.vendedor_id = ?");
    } elseif ($tipo_usuario === 'comprador') {
        $stmt = $conexion->prepare("SELECT c.id, u.usuario AS otro_usuario
                                    FROM chat c
                                    INNER JOIN usuarios u ON c.vendedor_id = u.id
                                    WHERE c.comprador_id = ?");
    }
    
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $chats = [];
    while ($row = $result->fetch_assoc()) {
        $chats[] = $row;
    }

    $conexionBD->cerrarConexion();

    return $chats;
}
function obtenerUltimoMensaje($chat_id) {
    $conexionBD = new ConexionBD();
    $conexion = $conexionBD->obtenerConexion();

    $stmt = $conexion->prepare("SELECT * FROM mensaje WHERE chat_id = ? ORDER BY timestamp DESC LIMIT 1");
    $stmt->bind_param("i", $chat_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $ultimo_mensaje = $result->fetch_assoc();
        return $ultimo_mensaje['contenido'];
    } else {
        return "No hay mensajes en este chat.";
    }


}

$chats = obtenerChats($usuario_id, $tipo_usuario);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Chats</title>
    <link rel="stylesheet" href="../styles/lista_chats.css">
</head>
<body>
    <?php include_once '../navbar/navbar.html'; ?>
    <style>
        <?php include '../navbar/styles.css'; ?>
    </style>
    <h1>Lista de Chats</h1>
    <div class="container">
        <div class="chats">
            <?php foreach ($chats as $chat): ?>
                <div class="chat">
                    <p><a href="historial_chat.php?chat_id=<?php echo $chat['id']; ?>"><?php echo $chat['otro_usuario']; ?></a></p>
                    <p><?php 
                        $ultimo_mensaje = obtenerUltimoMensaje($chat['id']);
                        if ($ultimo_mensaje !== "No hay mensajes en este chat.") {
                            echo "<p>$ultimo_mensaje</p>";
                        } else {
                            echo "<p>No hay mensajes en este chat.</p>";
                        }
                    ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
