<?php
    require 'config/config.php';
    require 'config/database.php';
    $db = new Database();
    $con = $db->conectar();

    $productos = isset($_SESSION['carrito']['productos']) ? $_SESSION['carrito']['productos'] : null;

    $lista_carrito = array();

    if($productos != null){
        foreach($productos as $clave => $cantidad){
            $sql = $con->prepare("SELECT id, nombre, precio, descuento, $cantidad AS cantidad FROM productos WHERE 
            id=? AND activo = 1");
            $sql->execute([$clave]);
            $lista_carrito []= $sql->fetch(PDO::FETCH_ASSOC);
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechShop</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" 
    rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">

    <link href="css/estilos.css" rel="stylesheet">
</head>

<body>

<?php include 'menu.php'; ?>

<main> 
    <div class="container">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php  if($lista_carrito == null){
                        echo '<tr><td colspan="5" class="text-center"><b>Lista vacía</b></td></tr>';
                    }else{
                        $total = 0;
                        foreach($lista_carrito as $producto){
                            $_id = $producto['id'];
                            $nombre = $producto['nombre'];
                            $precio = $producto['precio'];
                            $descuento = $producto['descuento'];
                            $cantidad = $producto['cantidad'];
                            $precio_desc = $precio - (($precio * $descuento) / 100);
                            $subtotal = $cantidad * $precio_desc;
                            $total += $subtotal;
                            ?>
                    <tr>
                        <td><?php echo $nombre; ?></td>
                        <td><?php echo MONEDA . number_format($precio_desc,2, '.', ','); ?></td>
                        <td>
                            <input type="number" min="1" max="10" step="1" value="<?php echo $cantidad ?>"
                            size="5" id="cantidad_<?php echo $_id; ?>" onchange="actualizaCantidad(this.value, <?php echo $_id; ?>)">
                        </td>
                        <td>
                            <div id="subtotal_<?php echo $_id; ?>" name="subtotal[]"><?php echo MONEDA . number_format($subtotal,2, '.', ','); ?></div>
                        </td>
                        <td><a href="#" id="eliminar" class="btn btn-warning btn-sm" data-bs-id="<?php echo $_id; ?>" data-bs-toggle="modal" data-bs-target="#eliminaModal">Eliminar</a></td>
                    </tr>
                    <?php } ?> 
                    <tr>
                        <td colspan="3"></td>
                        <td colspan="2">
                            <p class="h3" id="total"><?php echo MONEDA . number_format($total, 2, '.', ','); ?></p>
                        </td>
                    </tr>
                </tbody>
                <?php } ?>
            </table>
        </div>
        <?php if ($lista_carrito !=null){ ?>
        <div class="row">
            <div class="col-md-5 offset-md-7 d-grid gap 2">
                <?php if (isset($_SESSION['user_cliente'])){ ?>
                <button class="btn btn-primary btn-lg" id="btn-pago">Realizar pago</button>
                <?php }else { ?>
                <a href="login.php" class="btn btn-primary btn-lg"> pago</a>    
                <?php } ?>
            </div>
        </div>
        <?php } ?>
    </div>
</main>

<!-- Modal eliminar producto -->
<div class="modal fade" id="eliminaModal" tabindex="-1" aria-labelledby="eliminaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eliminaModalLabel">Alerta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                ¿Desea eliminar este producto?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button id="btn-elimina" type="button" class="btn btn-danger" onclick="eliminar()">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación de Pago -->
<div class="modal fade" id="modalPago" tabindex="-1" aria-labelledby="modalPagoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-white" style="background-color: #111122; border: 2px solid #00ffcc;">
      <div class="modal-header border-0">
        <h5 class="modal-title" id="modalPagoLabel">¡Pago realizado!</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body text-center">
        <i class="bi bi-check-circle-fill" style="font-size: 3rem; color: #00ffcc;"></i>
        <p class="mt-3">Gracias por tu compra. Tu pago ha sido procesado correctamente.</p>
      </div>
      <div class="modal-footer border-0 justify-content-center">
        <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" 
integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" 
crossorigin="anonymous"></script>

<script>
let eliminaModal = document.getElementById('eliminaModal')
eliminaModal.addEventListener('show.bs.modal', function(event){
    let button = event.relatedTarget
    let id = button.getAttribute('data-bs-id')
    let buttonElimina = eliminaModal.querySelector('.modal-footer #btn-elimina')
    buttonElimina.value = id
})

function actualizaCantidad(cantidad, id){
    let url ='clases/actualizar_carrito.php'
    let formData = new FormData()
    formData.append('action', 'agregar')
    formData.append('id', id)
    formData.append('cantidad', cantidad)

    fetch(url, {
        method: 'POST',
        body: formData,
        mode: 'cors'
    }).then(response => response.json())
    .then(data => {
        if(data.ok){
            let divsubtotal = document.getElementById('subtotal_' + id)
            divsubtotal.innerHTML = data.sub
            let divtotal = document.getElementById('total')
            divtotal.innerHTML = data.total
        }
    })
}

function eliminar(){
    let botonElimina = document.getElementById('btn-elimina')
    let id = botonElimina.value

    let url ='clases/actualizar_carrito.php'
    let formData = new FormData()
    formData.append('action', 'eliminar')
    formData.append('id', id)

    fetch(url, {
        method: 'POST',
        body: formData,
        mode: 'cors'
    }).then(response => response.json())
    .then(data => {
        if(data.ok){
            location.reload()
        }
    })
}

const btnPago = document.getElementById('btn-pago');
if (btnPago) {
    btnPago.addEventListener('click', function () {
        fetch('procesar_compra.php', {
            method: 'POST',
            credentials: 'same-origin'
        }).then(async response => {
            const text = await response.text();
            console.log("📦 Respuesta cruda del servidor:", text);

            try {
                const data = JSON.parse(text);

                if (data.ok) {
                    let modalPago = new bootstrap.Modal(document.getElementById('modalPago'));
                    modalPago.show();

                    setTimeout(() => {
                        document.querySelector('tbody').innerHTML = '<tr><td colspan="5" class="text-center"><b>Lista vacía</b></td></tr>';
                        document.getElementById('total').innerHTML = '$0.00';
                    }, 2000);
                } else {
                    alert("⚠️ Error del servidor: " + (data.error || "No se pudo procesar la compra."));
                }
            } catch (e) {
                console.error("❌ No se pudo interpretar como JSON. Texto recibido:", text);
                alert("⚠️ Error inesperado del servidor. Revisa la consola.");
            }
        }).catch(error => {
            console.error("❌ Error de red o servidor:", error);
            alert("Error en el servidor o conexión.");
        });
    });
}



</script>

</body>
</html>
