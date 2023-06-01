<?php

session_start();
error_reporting(0);

?>

<div class="modal fade" id="vender" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h3 class="modal-title" id="exampleModalLabel">Realizar Venta <i class="fa fa-shopping-cart" aria-hidden="true"></i></h3>
                <button type="button" class="btn btn-primary" data-dismiss="modal">
                    <i class="fa fa-times" aria-hidden="true"></i></button>
            </div>

            <div class="modal-body">

            <form action="../includes/terminarVenta.php" method="POST" name="form" onsubmit="return validarVenta()">
              
                    <input type="hidden" name="usuario" value="<?php echo $_SESSION['usuario']; ?>">
                    <input type="hidden" name="id_cliente" id="id_cliente">                   
                    <input type="hidden" name="id_producto[]" value="' + producto.id + '">


                    <div class="form-group">

                        <label for="cliente"><b> TOTAL: $</b></label>
                        <input class="outlinenone" name="granTotal" type="text" id="val2" value="<?php echo $granTotal; ?>">
                    </div>


                    <br>

                    <div class="form-group">

                        <label for="cliente"><b> EL CLIENTE PAGO CON:</b></label>
                        <input placeholder="Agrega con cuanto paga el cliente" step="any" type="number" onkeypress="comprueba(this)" min="0" pattern="^[0-9]+" id="pago" name="pago" class="form-control focus">
                    </div>

                    <br>

                    <div class="form-group">

                        <label for="cliente"> SU CAMBIO ES:</label>
                        <input step="any" min="0" type="number" name="cambio" id="cambio" class="form-control outlinenone" placeholder="Aqui se refleja el cambio">
                        <br>
                        <!-- <button type="button" class="btn btn-outline-secondary btn-sm" onclick="restar()">Calcular <i class="fa fa-plus-square"></i></button>-->
                    </div>

                    <br>
                    <style>
                        .outlinenone {
                            outline: none;
                            background-color: #fff;
                            border: 0;

                        }
                    </style>

                    <center>
                    <button type="submit" name="enviar_venta" id="enviar_venta" class="btn btn-primary btn-sm">COBRAR - ENTER</button>

                        <button type="button" name="enviar_venta" id="register" class="btn btn-warning btn-sm">COBRAR S/N TICKET - F2</button>
                        <!-- <button type="button" name="enviar_venta" id="register" class="btn btn-info btn-sm">PAGO EXACTO - F1</button>-->
                    </center>

            </div>
        </div>
    </div>
    </form>
    <script>
        
        let val1 = document.getElementById("pago")
        let val2 = document.getElementById("val2")

        document.getElementById("pago").addEventListener("input", () => {
            document.getElementById("cambio").value = val1.value - val2.value;

            console.log(`es : ${document.getElementById("cambio").value}`)
        })

        var cambio = document.getElementById('pago');

        function comprueba(valor) {
            if (valor.value < 0) {
                valor.value = 1;
            }
        }
        $(function() {
            $('#vender').on('shown.bs.modal', function(e) {
                $('.focus').focus();
            })
        });
    </script>
    <script>
        document.addEventListener('keydown', function(event) {
            if (event.key === "F2") {
                document.getElementById('register').click();
            }
        });
    </script>