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
$sql = $con->prepare("SELECT * FROM productos WHERE id = ?");
$sql->execute([$id]);
$producto = $sql->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    echo "Producto no encontrado.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $descuento = $_POST['descuento'];
    $id_categoria = $_POST['id_categoria'];

    if ($nombre != '' && is_numeric($precio)) {
        $sql = $con->prepare("UPDATE productos SET nombre=?, descripcion=?, precio=?, descuento=?, id_categoria=? WHERE id=?");
        $sql->execute([$nombre, $descripcion, $precio, $descuento, $id_categoria, $id]);
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
    <title>Editar Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>Editar Producto</h2>

    <?php if (isset($error)) { ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php } ?>

    <form method="post">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre del producto</label>
            <input type="text" name="nombre" id="nombre" class="form-control" required value="<?php echo $producto['nombre']; ?>">
        </div>
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea name="descripcion" id="descripcion" class="form-control" rows="3" required><?php echo $producto['descripcion']; ?></textarea>
        </div>
        <div class="mb-3">
            <label for="precio" class="form-label">Precio</label>
            <input type="number" name="precio" id="precio" class="form-control" step="0.01" required value="<?php echo $producto['precio']; ?>">
        </div>
        <div class="mb-3">
            <label for="descuento" class="form-label">Descuento (%)</label>
            <input type="number" name="descuento" id="descuento" class="form-control" step="1" value="<?php echo $producto['descuento']; ?>">
        </div>
        <div class="mb-3">
            <label for="id_categoria" class="form-label">ID Categoría</label>
            <input type="number" name="id_categoria" id="id_categoria" class="form-control" required value="<?php echo $producto['id_categoria']; ?>">
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="adminproductos.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
