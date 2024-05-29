<?php

require_once 'metodos/conexion.php';
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Verificar si el carrito está vacío
if (!isset($_SESSION['carrito']) || count($_SESSION['carrito']) === 0) {
    // Si el carrito está vacío, redirigir al usuario al carrito
    header("Location: carrito.php");
    exit;
}

// Obtener las credenciales de PayPal (Sandbox)
$paypal_client_id = '';
$paypal_secret = '';
$paypal_sandbox_url = 'https://sandbox.paypal.com';

// URL de redirección después del pago (puedes cambiarlo según tu aplicación)
$redirect_url = 'home.php';
$cancel_url = 'home.php';

// Preparar los datos del producto para PayPal
$productos_paypal = [];
$total = 0;

foreach ($_SESSION['carrito'] as $producto) {
    $producto_id = $producto['producto_id'];
    $precio = $producto['precio'];
    $cantidad = $producto['cantidad'];

    // Agregar el producto a la lista para PayPal
    $productos_paypal[] = [
        'name' => $producto_id, // Aquí puedes poner el nombre del producto
        'unit_amount' => [
            'currency_code' => 'MXN',
            'value' => $precio,
        ],
        'quantity' => $cantidad,
    ];

    // Calcular el total del pedido
    $total += $precio * $cantidad;
}

// Crear la orden de PayPal
$data = [
    'intent' => 'CAPTURE',
    'purchase_units' => [
        [
            'amount' => [
                'currency_code' => 'MXN',
                'value' => $total,
            ],
            'items' => $productos_paypal,
        ]
    ]
];

// Realizar la solicitud a la API de PayPal para crear la orden
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$paypal_sandbox_url/v2/checkout/orders");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Basic ' . base64_encode("$paypal_client_id:$paypal_secret")
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

// Decodificar la respuesta de PayPal
$order = json_decode($response);

// Redirigir al sitio de PayPal para realizar el pago
header("Location: " . $order->links[1]->href); // El índice 1 es para el enlace de pago
exit;
?>
