<?php

require 'config/config.php';
require 'config/database.php';
require 'clases/clienteFunciones.php';

$db = new Database();
$con = $db->conectar();

$errors = [];

if(!empty($_POST)){

    $nombres =  trim($_POST['nombres']);
    $apellidos =  trim($_POST['apellidos']);
    $email =  trim($_POST['email']);
    $telefono =  trim($_POST['telefono']);
    $usuario =  trim($_POST['usuario']);
    $password =  trim($_POST['password']);
    $repassword =  trim($_POST['repassword']);

    if(esNulo([$nombres, $apellidos, $email, $telefono, $usuario, $password, $repassword])){
        $errors[] = "Debe llenar todos los campos";
    }
    if(!esEmail($email)){
        $errors[] = "El correo electronico no es valida"; 
    }
    if(!validaPassword($password, $repassword)){
        $errors[] = "Las contraseña no coinciden";
    }
    if(usuarioExiste($usuario, $con)){
        $errors[] = "Este $usuario ya existe";
    }
    if(emailExiste($email, $con)){
        $errors[] = "Este email 👉 $email ya existe";
    }

    if(count($errors) == 0){

    $id = registraCliente([$nombres, $apellidos, $email, $telefono], $con);

    if($id > 0){
        $pass_hash = password_hash($password, PASSWORD_DEFAULT);
        $token = generarToken();
        if(!registraUsuario([$usuario, $pass_hash, $token, $id], $con)){
            $errors[] = "error al registrar usuario";
        }
    } else{
        $errors[] = "error al registrar cliente";
    }
}

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechShop</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">

    <link href="css/estilos.css" rel="stylesheet">

</head>

<body>

<header>
    <div class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="#" class="navbar-brand">
                <strong>TechShop</strong>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarHeader" aria-controls="navbarHeader"
                    aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarHeader">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a href="#" class="nav-link active">Catálogo</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">Contacto</a>
                    </li>
                </ul>
                <a href="checkout.php" class="btn btn-cart position-relative">
                    <i class="bi bi-cart"></i> Carrito
                    <span id="num_cart" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        0
                    </span>
                </a>
            </div>
        </div>
    </div>
</header>
<!--contenido-->
<main>
    <div class="container my-4">
        <h2>Datos del cliente</h2>

        <?php mostrarMensajes($errors);?>

        <form class="row g-3" action="registro.php" method="post" autocomplete="off">
            <div class="col-md-6">
                <label for="nombres"><span class="text-danger">*</span> Nombres</label>
                <input type="text" name="nombres" id="nombres" class="form-control" requireda>
            </div>

             <div class="col-md-6">
                <label for="apellidos"><span class="text-danger">*</span> Apellidos</label>
                <input type="text" name="apellidos" id="apellidos" class="form-control" requireda>
            </div>

            <div class="col-md-6">
                <label for="email"><span class="text-danger">*</span> Correo Electrónico</label>
                <input type="email" name="email" id="email" class="form-control" requireda>
                 <span id="validaEmail" class="text-danger"></span>
            </div>

             <div class="col-md-6">
                <label for="telefono"><span class="text-danger">*</span> Telefono</label>
                <input type="tel" name="telefono" id="telefono" class="form-control" requireda>
            </div>   

             <div class="col-md-6">
                <label for="usuario"><span class="text-danger">*</span> Usuario</label>
                <input type="text" name="usuario" id="usuario" class="form-control" requireda>
                <span id="validaUsuario" class="text-danger"></span>
            </div>

             <div class="col-md-6">
                <label for="password"><span class="text-danger">*</span> Contraseña</label>
                <input type="password" name="password" id="password" class="form-control" requireda>
            </div>   

             <div class="col-md-6">
                <label for="repassword"><span class="text-danger">*</span> Repetir Contraseña</label>
                <input type="password" name="repassword" id="repassword" class="form-control" requireda>
            </div>

            <i><b>Nota:</b>Los campos con * son obligatorios</i>

            <div class="col-12">
                <button type="submit" class="btn btn-primary">Registrar</button>
            </div>

        </form>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
    crossorigin="anonymous"></script>

    <script>
        let txtUsuario = document.getElementById('usuario')
        txtUsuario.addEventListener("blur", function(){
            existeUsuario(txtUsuario.value)
        }, false)

        let txtEmail = document.getElementById('email')
        txtEmail.addEventListener("blur", function(){
            existeEmail(txtEmail.value)
        }, false)

        function existeUsuario(usuario){
            let url ="clases/clienteAjax.php"
            let formData = new FormData()
            formData.append("action", "existeUsuario")
            formData.append("usuario", usuario)

            fetch(url, {
                method: 'POST',
                body: formData
            }).then(response => response.json())
            .then(data => {

                if(data.ok){
                    document.getElementById('usuario').value = '' //validaUsuario
                    document.getElementById('validaUsuario').innerHTML = 'Usuario no disponible'
                }else{
                    document.getElementById('validaUsuario').innerHTML = ''
                }

            })

        }

        function existeEmail(email){
            let url ="clases/clienteAjax.php"
            let formData = new FormData()
            formData.append("action", "existeEmail")
            formData.append("email", email)

            fetch(url, {
                method: 'POST',
                body: formData
            }).then(response => response.json())
            .then(data => {

                if(data.ok){
                    document.getElementById('email').value = '' //validaEmail
                    document.getElementById('validaEmail').innerHTML = 'Email no disponible'
                }else{
                    document.getElementById('validaEmail').innerHTML = ''
                }

            })

        }
    </script>
</body>
</html>
