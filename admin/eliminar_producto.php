<?php
require '../config/config.php';
require '../config/database.php';

$db = new Database();
$con = $db->conectar();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID de producto no válido.";
    exit;
}

$id = $_GET['id'];

$sql = $con->prepare("SELECT id FROM productos WHERE id = ?");
$sql->execute([$id]);

if ($sql->rowCount() == 0) {
    echo "Producto no encontrado.";
    exit;
}

$sql = $con->prepare("UPDATE productos SET activo = 0 WHERE id = ?");
$resultado = $sql->execute([$id]);

if ($resultado) {
    header("Location: adminproductos.php");
    exit;
} else {
    echo "Error al desactivar el producto.";
}
?>
