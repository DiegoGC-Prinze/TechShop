<?php
session_start();


unset($_SESSION['admin_logged']);
unset($_SESSION['admin_id']);

session_destroy();

header("Location: ../loginAdmin.php");
exit;
?>
