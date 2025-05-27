<?php
session_start();
require 'config/config.php';
require 'config/database.php';

$db = new Database();
$con = $db->conectar();

$errors = [];

if (!empty($_POST)) {
    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['password']);

    if ($usuario === '' || $password === '') {
        $errors[] = "Debe llenar todos los campos";
    } else {
        $sql = $con->prepare("SELECT id, password FROM admin WHERE usuario = ?");
        $sql->execute([$usuario]);
        $admin = $sql->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_logged'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            header("Location: admin/adminproductos.php");
            exit;
        } else {
            $errors[] = "Usuario o contraseña incorrectos";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - TechShop</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link href="css/estilos.css" rel="stylesheet">
</head>

<body>
<header>
    <div class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="#" class="navbar-brand">
                <strong>TechShop Admin</strong>
            </a>
        </div>
    </div>
</header>

<main class="form-login m-auto pt-4" style="max-width: 400px;">
    <h2>Iniciar sesión como administrador</h2>

    <?php if (!empty($errors)) { ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error) echo $error . '<br>'; ?>
        </div>
    <?php } ?>

    <form class="row g-3" action="loginAdmin.php" method="post" autocomplete="off">
        <div class="form-floating">
            <input class="form-control" type="text" name="usuario" id="usuario" placeholder="Usuario" required>
            <label for="usuario">Usuario</label>
        </div>

        <div class="form-floating">
            <input class="form-control" type="password" name="password" id="password" placeholder="Contraseña" required>
            <label for="password">Contraseña</label>
        </div>

        <div class="d-grid gap-3 col-12">
            <button type="submit" class="btn btn-primary">Ingresar</button>
        </div>
        <hr>
        <div class="col-12">
            ¿Eres nuevo administrador? <a href="admin/registroAdmin.php">Regístrate aquí</a>
        </div>
    </form>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
        crossorigin="anonymous"></script>
</body>
</html>
