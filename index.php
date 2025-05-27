<?php

require 'config/config.php';
require 'config/database.php';
$db = new Database();
$con = $db->conectar();

$sql = $con->prepare("SELECT id, nombre, precio FROM productos WHERE activo = 1");
$sql->execute();
$resultado = $sql->fetchAll(PDO::FETCH_ASSOC);

//print_r($_SESSION)
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechShop</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">

    <!-- Custom CSS -->
    <link href="css/estilos.css" rel="stylesheet">

</head>

<body>

<?php include 'menu.php'; ?>
<!--contenido -->
<main>
    <div class="container my-4">
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
            <?php foreach ($resultado as $row) { ?>
                <div class="col">
                    <div class="card shadow-custom h-100">
                        <?php
                            $id = $row['id'];
                            $dir = "images/productos/" . $id;
                            $imagen = "images/no-photo.jpg"; 
                            $extensiones_validas = ['jpg', 'jpeg', 'png', 'webp'];

                            if (is_dir($dir)) {
                                $archivos = scandir($dir);
                                foreach ($archivos as $archivo) {
                                    $ext = pathinfo($archivo, PATHINFO_EXTENSION);
                                    if (in_array(strtolower($ext), $extensiones_validas)) {
                                        $imagen = $dir . "/" . $archivo;
                                        break;
                                    }
                                }
                            }
                        ?>
                        <img src="<?php echo $imagen; ?>" class="card-img-top img-producto" alt="<?php echo $row['nombre']; ?>">

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo $row['nombre']; ?></h5>
                            <p class="card-text">$<?php echo number_format($row['precio'], 2, '.', ','); ?></p>
                            <div class="mt-auto d-flex justify-content-between">
                                <a href="detalles.php?id=<?php echo $row['id']; ?>&token=<?php echo hash_hmac('sha1', $row['id'], KEY_TOKEN); ?>" class="btn btn-details">
                                    Detalles
                                </a>
                                <button class="btn btn-add" type="button" onclick="addProducto(<?php echo $row['id']; ?>, '<?php echo hash_hmac('sha1', $row['id'], KEY_TOKEN); ?>')">
                                    Agregar al carrito
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
    crossorigin="anonymous"></script>
<script>
    function addProducto(id, token) {
        let url = 'clases/carrito.php'
        let formData = new FormData()
        formData.append('id', id)
        formData.append('token', token)

        fetch(url, {
            method: 'POST',
            body: formData,
            mode: 'cors'
        }).then(response => response.json())
        .then(data => {
            if (data.ok) {
                let elemento = document.getElementById("num_cart")
                elemento.innerHTML = data.numero
            }
        })
    }
</script>
</body>
</html>
