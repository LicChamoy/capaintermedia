<?php
require_once '../metodos/conexion.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

$conexionBD = new ConexionBD();
$conexion = $conexionBD->obtenerConexion();

$stmt = $conexion->prepare("SELECT nombres, apellidos, correo, fecha_nacimiento, foto_perfil FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $foto_perfil_base64 = base64_encode($row['foto_perfil']);
    echo json_encode([
        'nombres' => $row['nombres'],
        'apellidos' => $row['apellidos'],
        'correo' => $row['correo'],
        'fecha_nacimiento' => $row['fecha_nacimiento'],
        'foto_perfil' => $foto_perfil_base64,
    ]);
} else {
    echo json_encode(['error' => 'Usuario no encontrado']);
}

$stmt->close();
$conexionBD->cerrarConexion();
?>
