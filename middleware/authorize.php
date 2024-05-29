<?php
function verificarPermisos($tipo_requerido) {
    session_start();
    if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] !== $tipo_requerido) {
        echo "<script>alert('No tiene permisos para acceder a esta página.'); window.location.href = 'home.php';</script>";
        exit();
    }
}
