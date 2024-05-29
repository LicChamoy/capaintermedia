<?php

require_once "metodos/conexion.php";

$query = "SELECT * FROM usuarios";
$resultado = mysqli_query($conexion, $query);

if ($resultado) {
    echo "La conexión a la base de datos y la consulta fueron exitosas.";
} else {
    echo "Error al ejecutar la consulta: " . mysqli_error($conexion);
}
mysqli_close($conexion);
