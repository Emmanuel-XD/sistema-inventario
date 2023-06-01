<?php
include_once "../includes/header.php";
?>

<!-- Incluir la biblioteca jQuery y jQuery UI Autocomplete -->

<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.0/themes/smoothness/jquery-ui.css">
<script src="../js/jquery-ui.js"></script>

<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="form-group">
                <h4 class="text-center">Datos del Cliente</h4>
            </div>
            <div class="card">
                <div class="card-body">
                    <form method="post" name="form_new_cliente_venta" id="form_new_cliente_venta">

                        <div class="row" id="datosCliente">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="text" name="cliente" id="cliente" class="form-control" required>
                                </div>
                            </div>

                            <!-- este es el id del cliente oculto -->
                            <input type="hidden" name="id_cliente" id="id" class="form-control">

                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Teléfono</label>
                                    <input type="number" name="telefono" id="telefono" class="form-control" disabled required>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Dirección</label>
                                    <input type="text" name="direccion" id="direccion" class="form-control" disabled required>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
            <br>

            <h4 class="text-center">Datos Venta</h4>
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> VENDEDOR</label>
                        <p style="font-size: 16px; text-transform: uppercase; color: blue;"><?php echo $_SESSION['usuario']; ?></p>
                    </div>
                </div>

            </div>
            <!-- BUSCADOR VENTA -->
            <form method="post" action="agregarAlCarrito.php" id="formBusqueda">
                <label for="codigo">Código de barras:</label>
                <input autocomplete="off" width="100%" autofocus class="form-control" name="codigo" required type="text" id="codigo" placeholder="Escanea o Escribe el código...">
            </form>
            <br>
            <div class="table-responsive">
                <table class="table table-striped" id="table_id" width="100%">
                    <thead>
                        <tr class="bg-dark" style="color: white;">
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Stock</th>
                            <th>Precio de venta</th>
                            <th>Cantidad</th>
                            <th>Total</th>
                            <th>Quitar</th>
                        </tr>
                    </thead>
                    <tbody id="resultadoBusqueda">
                        <!-- Aquí se mostrarán los productos encontrados -->
                    </tbody>
                </table>
            </div>

            <!-- Script de AJAX -->
            <script>
                $(document).ready(function() {
                    $('#formBusqueda').submit(function(e) {
                        e.preventDefault(); // Evitar que se recargue la psgina

                        var codigo = $('#codigo').val();
                        var cantidadInput = $('.cantidad');
                        var productoExistente = false;
                        var limiteAlcanzado = false;

                        // Verificar si el product ya está en el carrito
                        $('#resultadoBusqueda tr').each(function() {
                            var codigoProducto = $(this).find('td:eq(0)').text();

                            if (codigoProducto === codigo) {
                                var cantidadActual = parseFloat($(this).find('.cantidad').val());
                                var nuevaCantidad = cantidadActual + 1;

                                // Verificar si la nueva cantidad supera la existencia
                                var existencia = parseFloat($(this).find('td:eq(2)').text());
                                if (nuevaCantidad > existencia) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Producto Agotado',
                                        text: 'El producto seleccionado está agotado.',
                                    });
                                    limiteAlcanzado = true;
                                    return false; // Salir del bucle
                                }

                                $(this).find('.cantidad').val(nuevaCantidad);

                                // Calcular nuevo total
                                var venta = parseFloat($(this).find('td:eq(3)').text());
                                var total = nuevaCantidad * venta;
                                $(this).find('.total').text(total.toFixed(2));

                                productoExistente = true;

                                // Calcular gran total
                                var granTotal = 0;
                                $('.total').each(function() {
                                    granTotal += parseFloat($(this).text());
                                });
                                $('#granTotal').text(granTotal.toFixed(2));

                                return false; // Salir del bucle
                            }
                        });

                        if (!productoExistente && !limiteAlcanzado) {
                            // Realizar la búsqueda AJAX y mostrar los resultados
                            $.ajax({
                                type: 'POST',
                                url: 'agregarAlCarrito.php',
                                data: {
                                    buscador: true,
                                    codigo: codigo
                                },
                                dataType: 'json',
                                success: function(response) {
                                    if (response === 'error') {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Producto No Encontrado',
                                            text: 'No se encontró ningún producto con ese código.',
                                        });
                                    } else {
                                        var html = '';
                                        $.each(response, function(index, producto) {
                                            if (producto.existencia > 0) {
                                                html += '<tr>';
                                                html += '<input type="hidden" name="id_producto[]" value="' + producto.id + '">';
                                                html += '<td>' + producto.codigo + '</td>';
                                                html += '<td>' + producto.producto + '</td>';
                                                html += '<td>' + producto.existencia + '</td>';
                                                html += '<td>' + producto.venta + '</td>';
                                                html += '<td>';
                                                html += '<input name="indice" type="hidden" value="' + index + '">';
                                                html += '<input min="1" name="cantidad" class="form-control cantidad" required type="number" step="1" value="1">';
                                                html += '</td>';
                                                html += '<td class="total">' + (1 * producto.venta) + '</td>';
                                                html += '<td><a class="btn btn-danger btn-quitar" href="#"><i class="fa fa-trash"></i></a></td>';
                                                html += '</tr>';
                                            } else {
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: 'Producto Agotado',
                                                    text: 'El producto seleccionado está agotado.',
                                                });
                                            }
                                        });
                                        $('#resultadoBusqueda').append(html);
                                    }

                                    // Calcular gran total
                                    var granTotal = 0;
                                    $('.total').each(function() {
                                        granTotal += parseFloat($(this).text());
                                    });
                                    $('#granTotal').text(granTotal.toFixed(2));
                                    $('#val2').val(granTotal.toFixed(2)); // Actualizar el valor del campo oculto
                                }
                            });
                        }

                        // Limpiar el buscador
                        $('#codigo').val('');
                    });

                    // Calcular total al cambiar la cantidad
                    $(document).on('change', 'input[name="cantidad"]', function() {
                        var cantidad = parseFloat($(this).val());
                        var existencia = parseFloat($(this).closest('tr').find('td:eq(2)').text());
                        var venta = parseFloat($(this).closest('tr').find('td:eq(3)').text());
                        var total = 0;

                        if (cantidad <= existencia) {
                            total = cantidad * venta;
                        } else {
                            alert('¡Límite de stock alcanzado!');
                            $(this).val(existencia); // definirr la cantidad al valor máximo permitido
                            total = existencia * venta;
                        }

                        $(this).closest('tr').find('.total').text(total.toFixed(2));

                        // Calcular gran total
                        var granTotal = 0;
                        $('.total').each(function() {
                            granTotal += parseFloat($(this).text());
                        });
                        $('#granTotal').text(granTotal.toFixed(2));
                        $('#val2').val(granTotal.toFixed(2)); // Actualizar el valor del campo oculto
                    });

                    // Eliminar producto del carrito al presionar el botón de "quitar"
                    $(document).on('click', '.btn-quitar', function(e) {
                        e.preventDefault();
                        $(this).closest('tr').remove();

                        // Calcular gran total
                        var granTotal = 0;
                        $('.total').each(function() {
                            granTotal += parseFloat($(this).text());
                        });
                        $('#granTotal').text(granTotal.toFixed(2));
                        $('#val2').val(granTotal.toFixed(2)); // Actualizar el valor del campo oculto
                    });

                    // Capturar evento "keydown" del campo de código
                    $('#codigo').on('keydown', function(e) {
                        if (e.keyCode === 13) {
                            // Si se presiona Enter, realizar el mismo cálculo del gran total
                            var granTotal = 0;
                            $('.total').each(function() {
                                granTotal += parseFloat($(this).text());
                            });
                            $('#granTotal').text(granTotal.toFixed(2));
                            $('#val2').val(granTotal.toFixed(2)); // Actualizar el valor del campo oculto
                        }
                    });
                });

                $(document).on('click', '.btn-cancelar', function(e) {
                    e.preventDefault();
                    $('#resultadoBusqueda').empty(); // Vaciar el contenido del carrito

                    // Reiniciar el gran total
                    $('#granTotal').text('0');
                    $('#val2').val('0'); // Actualizar el valor del campo oculto
                });
            </script>
            <script>
                $(document).ready(function() {
                    // Función para actualizar el gran total en la ventana modal
                    function actualizarGranTotal() {
                        var granTotal = $('#granTotal').text();
                        var idCliente = $('#id').val(); // Obtener el ID del cliente seleccionado
                        $('#val2').val(granTotal);
                        $('#id_cliente').val(idCliente);
                        // Obtener los datos de los productos en el carrito
                        var productos = [];
                        $('.cantidad').each(function() {
                            var idProducto = $(this).closest('tr').find('input[name="id_producto[]"]').val();
                            var cantidad = $(this).val();
                            productos.push({
                                id_producto: idProducto,
                                cantidad: cantidad
                            });
                        });

                        // Agregar los datos al formulario de venta
                        $('input[name="id_producto"]').remove();
                        $.each(productos, function(index, producto) {
                            var input = $('<input>').attr({
                                type: 'hidden',
                                name: 'id_producto[]',
                                value: producto.id_producto
                            });
                            $('form[name="form"]').append(input);
                            $('form[name="form"]').append('<input type="hidden" name="cantidad[]" value="' + producto.cantidad + '">');
                        });

                    }

                    // Actualizar el gran total al cargar la ventana modal
                    $('#vender').on('shown.bs.modal', function(e) {
                        actualizarGranTotal();
                    });

                    // Actualizar el gran total al presionar el botón "Procesar"
                    $('#enviar_venta').on('click', function(e) {
                        actualizarGranTotal();
                    });

                    // Actualizar el gran total al presionar Enter en el campo de código
                    $('#codigo').on('keydown', function(e) {
                        if (e.keyCode === 13) {
                            actualizarGranTotal();
                        }
                    });
                });
            </script>


            <br>
            <h3>Total General: $<span id="granTotal">0</span></h3>
            <form action="../includes/terminarVenta.php" method="POST">


                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#vender">
                    <span class="glyphicon glyphicon-plus"></span> PROCESAR - F5 <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                </button>
                <a href="#" class="btn btn-danger btn-cancelar">CANCELAR <i class="fa fa-undo" aria-hidden="true"></i></a>
            </form>

        </div>
    </div>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->


<!--Aqui se obtiene el cliente-->
<script src="../js/searchcliente.js"></script>

<?php include "../includes/footer.php"; ?>
<?php include_once "ventana.php" ?>