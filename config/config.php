<?php

define("KEY_TOKEN","APR.wqc-354*");
define("MONEDA", "$");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$num_cart = 0;
if(isset($_SESSION['carrito']['productos'])){
    $num_cart = count($_SESSION['carrito']['productos']);
}

?>