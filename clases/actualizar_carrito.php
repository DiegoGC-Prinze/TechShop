<?php

require '../config/config.php';
require '../config/database.php';

$datos = array();

if (isset($_POST['action'])) {

    $action = $_POST['action'];
    $id = isset($_POST['id']) ? $_POST['id'] : 0;
    $cantidad = isset($_POST['cantidad']) ? $_POST['cantidad'] : 0;

    if ($action == 'agregar') {
        $respuesta = agregar($id, $cantidad);  // Calcula subtotal producto
        if ($respuesta > 0) {
            $datos['ok'] = true;
        } else {
            $datos['ok'] = false;
        }

        $datos['sub'] = MONEDA . number_format($respuesta, 2, '.', ',');

        // 👉 Calcula total general del carrito
        $total = calcularTotalCarrito();
        $datos['total'] = MONEDA . number_format($total, 2, '.', ',');
    } else if($action == 'eliminar'){
            $datos['ok'] = eliminar($id);
    } else {
        $datos['ok'] = false;
    }
} else {
    $datos['ok'] = false;
}

echo json_encode($datos);

function agregar($id, $cantidad){
    $res = 0;
    if($id > 0 && $cantidad > 0 && is_numeric($cantidad)){
        if(isset($_SESSION['carrito']['productos'][$id])){
            $_SESSION['carrito']['productos'][$id] = $cantidad;

            $db = new Database();
            $con = $db->conectar();

            $sql = $con->prepare("SELECT precio, descuento FROM productos WHERE id=? AND activo = 1 LIMIT 1");
            $sql->execute([$id]);
            $row = $sql->fetch(PDO::FETCH_ASSOC);

            $precio = $row['precio'];
            $descuento = $row['descuento'];
            $precio_desc = $precio - (($precio * $descuento) / 100);
            $res = $cantidad * $precio_desc;

            return $res;
        }
    }
    return $res;
}

function calcularTotalCarrito() {
    $total = 0;
    if (isset($_SESSION['carrito']['productos'])) {
        $db = new Database();
        $con = $db->conectar();

        foreach($_SESSION['carrito']['productos'] as $id => $cantidad){
            $sql = $con->prepare("SELECT precio, descuento FROM productos WHERE id=? AND activo = 1 LIMIT 1");
            $sql->execute([$id]);
            $row = $sql->fetch(PDO::FETCH_ASSOC);

            $precio = $row['precio'];
            $descuento = $row['descuento'];
            $precio_desc = $precio - (($precio * $descuento) / 100);
            $total += $precio_desc * $cantidad;
        }
    }
    return $total;
}

function eliminar($id){
    if($id > 0){
        if (isset($_SESSION['carrito']['productos'][$id])) {
            unset($_SESSION['carrito']['productos'][$id]);
            return true;
        }
    }else{
        return false;
    }
}
