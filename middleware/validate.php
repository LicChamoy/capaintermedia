<?php
function validarDatosProducto($data) {
    $errores = [];

    if (empty($data['nombre'])) {
        $errores[] = "El nombre es obligatorio.";
    }

    if (empty($data['descripcion'])) {
        $errores[] = "La descripción es obligatoria.";
    }

    if (empty($data['categoria'])) {
        $errores[] = "La categoría es obligatoria.";
    }

    if ($data['tipo'] !== 'cotizacion' && $data['tipo'] !== 'venta') {
        $errores[] = "El tipo de venta no es válido.";
    }

    if (!is_numeric($data['precio']) || $data['precio'] < 0) {
        $errores[] = "El precio debe ser un número positivo.";
    }

    if (!is_numeric($data['cantidad']) || $data['cantidad'] < 0) {
        $errores[] = "La cantidad debe ser un número positivo.";
    }

    return $errores;
}
?>