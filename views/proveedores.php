<?php
error_reporting(0);
session_start();

?>



<?php include "../includes/header.php"; ?>

<body id="page-top">

    <!-- Begin Page Content -->
    <div class="container-fluid">


        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Lista de Proveedores</h6>
                <br>

                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#prov">
                    <span class="glyphicon glyphicon-plus"></span> Agregar <i class="fa fa-plus"></i> </a></button>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Razon Social</th>
                                <th>Direccion</th>
                                <th>Telefono</th>
                                <th>RFC</th>
                                <th>Correo</th>
                                <th>Fecha</th>
                                <th>Acciones.</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            require_once("../includes/db.php");
                            $result = mysqli_query($conexion, "SELECT * FROM proveedores ");
                            while ($fila = mysqli_fetch_assoc($result)) :
                            ?>
                                <tr>
                                    <td><?php echo $fila['name']; ?></td>
                                    <td><?php echo $fila['r_social']; ?></td>
                                    <td><?php echo $fila['direccion']; ?></td>
                                    <td><?php echo $fila['telefono']; ?></td>
                                    <td><?php echo $fila['rfc']; ?></td>
                                    <td><?php echo $fila['correo']; ?></td>
                                    <td><?php echo $fila['fecha']; ?></td>
                                    <td>
                                        <a href="editUser.php?accion=edit_users&id=<?php echo $fila['id'] ?>" class="btn btn-warning" id="editForm">
                                            <i class="fa fa-edit "></i> </a>
                                        <a href="../includes/functions.php?accion=delete_users&id=<?php echo $fila['id'] ?> " class="btn btn-danger btn-del" id="deleteForm">
                                            <i class="fa fa-trash "></i></a></button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <script>
                        $('.btn-del').on('click', function(e) {
                            e.preventDefault();
                            const href = $(this).attr('href')

                            Swal.fire({
                                title: 'Estas seguro de eliminar este registro?',
                                text: "¡No podrás revertir esto!!",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Si, eliminar!',
                                cancelButtonText: 'Cancelar!',
                            }).then((result) => {
                                if (result.value) {
                                    if (result.isConfirmed) {
                                        Swal.fire(
                                            'Eliminado!',
                                            'El registro fue eliminado.',
                                            'success'
                                        )
                                    }

                                    document.location.href = href;
                                }
                            })

                        })
                    </script>




                </div>
            </div>
        </div>

    </div>
    <!-- /.container-fluid -->

    </div>
    <!-- End of Main Content -->






    </div>
    <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->




</body>
<?php include "form_prov.php"; ?>
<?php include "../includes/footer.php"; ?>

</html>