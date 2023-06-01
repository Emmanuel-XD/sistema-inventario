<?php
// terminarVenta.php

// Obtener las variables enviadas por el formulario
$granTotal = $_POST['granTotal'];
$usuario = $_POST['usuario'];
$clienteId = $_POST['id_cliente'];
$pago = $_POST['pago'];
$cambio = $_POST['cambio'];
$id_productos = isset($_POST['id_producto']) ? $_POST['id_producto'] : [];
$cantidades = isset($_POST['cantidad']) ? $_POST['cantidad'] : [];

// Obtener la fecha de hoy
$fechaHoy = date('Y-m-d');

// Realizar las operaciones necesarias con los datos

// Incluir el archivo de conexión a la base de datos
require_once("db.php");

// Verificar si ya existe una venta con los mismos detalles
$existente = false;
$selectVenta = "SELECT id_venta FROM ventas WHERE usuario = '$usuario' AND id_cliente = '$clienteId' AND pago = '$pago' AND cambio = '$cambio' AND fecha = '$fechaHoy'";
$resultadoVenta = mysqli_query($conexion, $selectVenta);

if ($resultadoVenta && mysqli_num_rows($resultadoVenta) > 0) {
    $rowVenta = mysqli_fetch_assoc($resultadoVenta);
    $idventa = $rowVenta['id_venta'];
    $existente = true;
} else {
    // Guardar los datos en la tabla de ventas
    $insert = "INSERT INTO ventas (total, usuario, id_cliente, pago, cambio, fecha) VALUES ('$granTotal', '$usuario', '$clienteId', '$pago', '$cambio', '$fechaHoy')";

    // Ejecutar la sentencia SQL
    $resultado = mysqli_query($conexion, $insert);

    if ($resultado) {
        // Obtener el ID de la última venta insertada
        $idventa = mysqli_insert_id($conexion);
    } else {
        echo "Error al insertar la venta: " . mysqli_error($conexion);
        exit;
    }
}

foreach ($id_productos as $key => $id_producto) {
    if (isset($cantidades[$key])) {
        $cantidad = $cantidades[$key];

        // Verificar si ya existe un registro con el mismo id_producto, id_venta y cantidad
        $selectProductoVendido = "SELECT id_producto FROM productos_vendidos WHERE id_producto = '$id_producto' AND id_venta = '$idventa' AND cantidad = '$cantidad'";
        $resultadoProductoVendido = mysqli_query($conexion, $selectProductoVendido);

        if ($resultadoProductoVendido && mysqli_num_rows($resultadoProductoVendido) > 0) {
            // Ya existe un registro con los mismos detalles, no se realiza ninguna acción
            continue;
        }

        // Verificar si el producto está agotado
        $sqlExistencia = "SELECT existencia FROM inventario WHERE id = '$id_producto'";
        $resultExistencia = mysqli_query($conexion, $sqlExistencia);

        if ($resultExistencia) {
            if (mysqli_num_rows($resultExistencia) > 0) {
                $rowExistencia = mysqli_fetch_assoc($resultExistencia);
                $existencia = $rowExistencia['existencia'];

                // Verificar si la cantidad a vender es mayor que la existencia actual
                if ($cantidad > $existencia) {
                    echo "Error: La cantidad a vender del producto con código $id_producto es mayor que la existencia actual en el inventario.";
                    continue;
                }

                // Insertar el producto vendido en la tabla productos_vendidos
                $insertProductoVendido = "INSERT INTO productos_vendidos (id_producto, cantidad, id_venta) VALUES ('$id_producto', '$cantidad', '$idventa')";
                $resultadoProductoVendido = mysqli_query($conexion, $insertProductoVendido);

                if ($resultadoProductoVendido) {
                    // Actualizar la existencia del producto en el inventario
                    $updateExistencia = "UPDATE inventario SET existencia = existencia - $cantidad WHERE id = '$id_producto'";
                    $resultadoExistencia = mysqli_query($conexion, $updateExistencia);

                    if (!$resultadoExistencia) {
                        // Manejar el error en la consulta de actualización del inventario
                        echo "Error al actualizar la existencia del producto en el inventario: " . mysqli_error($conexion);
                    }
                } else {
                    // Manejar el error en la consulta de inserción en productos_vendidos
                    echo "Error al insertar el nuevo registro de producto vendido: " . mysqli_error($conexion);
                }
            } else {
                echo "Error: El producto con código $id_producto no existe en el inventario.";
            }
        } else {
            // Manejar el error en la consulta SELECT
            echo "Error al obtener la existencia del producto en el inventario: " . mysqli_error($conexion);
        }
    }
}
?>
