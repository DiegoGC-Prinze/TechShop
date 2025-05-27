<?php
require '../config/config.php';
require '../config/database.php';
$db = new Database();
$con = $db->conectar();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $descripcion = $_POST['descripcion'];
    $descuento = $_POST['descuento'];
    $id_categoria = $_POST['id_categoria'];
    $activo = 1;

    if ($nombre != '' && is_numeric($precio) && is_numeric($descuento) && $descuento >= 0 && $descuento <= 100) {
        $sql = $con->prepare("INSERT INTO productos (nombre, descripcion, precio, descuento, id_categoria, activo) VALUES (?, ?, ?, ?, ?, ?)");
        $sql->execute([$nombre, $descripcion, $precio, $descuento, $id_categoria, $activo]);

       header("Location: adminproductos.php");
        exit;
    } else {
        $error = "Por favor, completa todos los campos correctamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>Agregar Producto</h2>

    <?php if (isset($error)) { ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php } ?>

<form method="post" action="">
    <div class="mb-3">
        <label for="nombre" class="form-label">Nombre del producto</label>
        <input type="text" name="nombre" id="nombre" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="descripcion" class="form-label">Descripción</label>
        <textarea name="descripcion" id="descripcion" class="form-control" rows="3" required></textarea>
    </div>
    <div class="mb-3">
        <label for="precio" class="form-label">Precio</label>
        <input type="number" name="precio" id="precio" class="form-control" step="0.01" required>
    </div>
    <div class="mb-3">
        <label for="descripcion" class="form-label">Descripción</label>
        <textarea name="descripcion" id="descripcion" class="form-control" rows="3" required></textarea>
    </div>

    <div class="mb-3">
        <label for="descuento" class="form-label">Descuento (%)</label>
        <input type="number" name="descuento" id="descuento" class="form-control" step="1" value="0">
    </div>
    <div class="mb-3">
        <label for="id_categoria" class="form-label">ID Categoría</label>
        <input type="number" name="id_categoria" id="id_categoria" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-success">Guardar</button>
    <a href="adminproductos.php" class="btn btn-secondary">Cancelar</a>
</form>

</div>
</body>
</html>
