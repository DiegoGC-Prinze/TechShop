<?php
require '../config/config.php';
require '../config/database.php';
$db = new Database();
$con = $db->conectar();

// Obtener productos
$sql = $con->prepare("SELECT * FROM productos");
$sql->execute();
$productos = $sql->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Productos</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link href="../css/estilos.css" rel="stylesheet">
</head>
<body>
<?php include '../menu.php'; ?>
<main class="container my-4">
    <h2 class="mb-4">Administrar Productos</h2>
    <a href="form_agregar_producto.php" class="btn btn-success mb-3">Agregar Producto</a>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Descuento</th>
                    <th>Categoría</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($productos as $p) { ?>
                <tr>
                    <td><?php echo $p['id']; ?></td>
                    <td><?php echo htmlspecialchars($p['nombre']); ?></td>
                    <td>$<?php echo number_format($p['precio'], 2); ?></td>
                    <td><?php echo $p['descuento']; ?>%</td>
                    <td><?php echo $p['id_categoria']; ?></td>
                    <td>
                        <a href="form_editar_producto.php?id=<?php echo $p['id']; ?>" class="btn btn-primary btn-sm">Editar</a>
                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalEliminar" 
                        data-id="<?php echo $p['id']; ?>">Eliminar
                        </button>

                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

<div class="modal fade" id="modalEliminar" tabindex="-1" aria-labelledby="modalEliminarLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="modalEliminarLabel">Confirmar eliminación</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        ¿Estás seguro de que deseas eliminar este producto? Esta acción no se puede deshacer.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <a href="#" id="btnConfirmarEliminar" class="btn btn-danger">Eliminar</a>
      </div>
    </div>
  </div>

</div>

    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
        crossorigin="anonymous"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
    var modalEliminar = document.getElementById('modalEliminar');
    modalEliminar.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        var link = document.getElementById('btnConfirmarEliminar');
        link.href = 'eliminar_producto.php?id=' + id;
    });
});
</script>

</body>
</html>
