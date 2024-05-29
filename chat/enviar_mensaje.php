<?php
require_once '../metodos/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $chat_id = $_POST['chat_id'];
    $contenido = $_POST['contenido'];
    $autor_id = $_POST['autor_id'];

    insertarMensaje($chat_id, $contenido, $autor_id);

    header("Location: historial_chat.php?chat_id=$chat_id");
    exit;
} else {
    header("Location: error.php");
    exit;
}

function insertarMensaje($chat_id, $contenido, $autor_id) {
    $conexionBD = new ConexionBD();
    $conexion = $conexionBD->obtenerConexion();

    $stmt = $conexion->prepare("INSERT INTO mensaje (chat_id, contenido, autor_id) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $chat_id, $contenido, $autor_id);
    $stmt->execute();

    $conexionBD->cerrarConexion();
}
?>
