<?php
require '../config/config.php';
require '../config/database.php';

$db = new Database();
$con = $db->conectar();

$errors = [];

if (!empty($_POST)) {
    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['password']);
    $repassword = trim($_POST['repassword']);

    if ($usuario === '' || $password === '' || $repassword === '') {
        $errors[] = "Debe llenar todos los campos.";
    }

    if ($password !== $repassword) {
        $errors[] = "Las contraseñas no coinciden.";
    }

    $sql = $con->prepare("SELECT id FROM admin WHERE usuario = ?");
    $sql->execute([$usuario]);
    if ($sql->fetch()) {
        $errors[] = "El nombre de este admin ya existe.";
    }

    if (count($errors) === 0) {
        $passHash = password_hash($password, PASSWORD_DEFAULT);
        $sql = $con->prepare("INSERT INTO admin (usuario, password) VALUES (?, ?)");
        $sql->execute([$usuario, $passHash]);
        header("Location: loginAdmin.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5" style="max-width: 500px;">
    <h2>Registro de Administrador</h2>

    <?php if (!empty($errors)) { ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error) echo $error . '<br>'; ?>
        </div>
    <?php } ?>

    <form method="post">
        <div class="mb-3">
            <label for="usuario" class="form-label">Usuario</label>
            <input type="text" name="usuario" id="usuario" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="repassword" class="form-label">Repetir Contraseña</label>
            <input type="password" name="repassword" id="repassword" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Registrar</button>
        <a href="loginAdmin.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
