<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'config/config.php';
require 'config/database.php';

// ✅ Validación de sesión para FETCH (sin redirigir)
if (!isset($_SESSION['user_cliente'])) {
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'error' => 'No autenticado']);
    exit;
}

$db = new Database();
$con = $db->conectar();

// Recuperar datos del cliente
$id_cliente = $_SESSION['user_cliente'];
$productos = isset($_SESSION['carrito']['productos']) ? $_SESSION['carrito']['productos'] : null;

if ($productos == null) {
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'error' => 'Carrito vacío']);
    exit;
}

// Calcular total
$total = 0;
foreach($productos as $clave => $cantidad){
    $sql = $con->prepare("SELECT precio, descuento FROM productos WHERE id=? AND activo=1");
    $sql->execute([$clave]);
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    $precio = $row['precio'];
    $descuento = $row['descuento'];
    $precio_final = $precio - (($precio * $descuento) / 100);
    $total += $precio_final * $cantidad;
}

// Insertar en comp
$sql = $con->prepare("INSERT INTO comp (id_cliente, fecha_compra, total, estatus) VALUES (?, NOW(), ?, 'pagado')");
$sql->execute([$id_cliente, $total]);
$id_compra = $con->lastInsertId();

// Insertar en detalle_com
foreach($productos as $clave => $cantidad){
    $sql = $con->prepare("SELECT precio, descuento FROM productos WHERE id=? AND activo=1");
    $sql->execute([$clave]);
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    $precio = $row['precio'];
    $descuento = $row['descuento'];
    $precio_final = $precio - (($precio * $descuento) / 100);

    $sql_detalle = $con->prepare("INSERT INTO detalle_com (id_compra, id_producto, cantidad, precio) VALUES (?, ?, ?, ?)");
    $sql_detalle->execute([$id_compra, $clave, $cantidad, $precio_final]);
}

// Vaciar carrito
unset($_SESSION['carrito']);

header('Content-Type: application/json');
echo json_encode(['ok' => true]);
?>
