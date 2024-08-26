<?php
// Incluir las configuraciones y conexiones necesarias

$code = $insLogin->limpiarCadena($url[1] ?? '');

$datos = $insLogin->seleccionarDatos("Normal", "venta INNER JOIN cliente ON venta.cliente_id=cliente.cliente_id INNER JOIN usuario ON venta.usuario_id=usuario.usuario_id INNER JOIN caja ON venta.caja_id=caja.caja_id WHERE (venta_codigo='" . $code . "')", "*", 0);

if ($datos->rowCount() == 1) {
	$datos_venta = $datos->fetch();
	$detalles_venta = $insLogin->seleccionarDatos("Normal", "venta_detalle WHERE venta_codigo='" . $datos_venta['venta_codigo'] . "'", "*", 0)->fetchAll();
?>
	<div class="container is-fluid mb-6">
		<h1 class="title">Ventas</h1>
		<h2 class="subtitle"><i class="fas fa-shopping-bag fa-fw"></i> &nbsp; Información de venta</h2>
	</div>

	<div class="container pb-6 pt-6">
		<?php include "./app/views/inc/btn_back.php"; ?>

		<h2 class="title has-text-centered">Datos de la venta <?php echo " (" . $code . ")"; ?></h2>
		<div class="columns pb-6 pt-6">
			<div class="column">

				<div class="full-width sale-details text-condensedLight">
					<div class="has-text-weight-bold">Fecha</div>
					<span class="has-text-link"><?php echo date("d-m-Y", strtotime($datos_venta['venta_fecha'])) . " " . $datos_venta['venta_hora']; ?></span>
				</div>

				<div class="full-width sale-details text-condensedLight">
					<div class="has-text-weight-bold">Nro. de factura</div>
					<span class="has-text-link"><?php echo $datos_venta['venta_id']; ?></span>
				</div>

				<div class="full-width sale-details text-condensedLight">
					<div class="has-text-weight-bold">Código de venta</div>
					<span class="has-text-link"><?php echo $datos_venta['venta_codigo']; ?></span>
				</div>

				<div class="full-width sale-details text-condensedLight">
					<div class="has-text-weight-bold">Sucursal</div>
					<span class="has-text-link"><?php echo 0; ?></span>
				</div>

				<div class="full-width sale-details text-condensedLight">
					<div class="has-text-weight-bold">Punto de venta</div>
					<span class="has-text-link"><?php echo 0; ?></span>
				</div>

			</div>

			<div class="column">

				<div class="full-width sale-details text-condensedLight">
					<div class="has-text-weight-bold">Caja</div>
					<span class="has-text-link">Nro. <?php echo $datos_venta['caja_numero'] . " (" . $datos_venta['caja_nombre']; ?>)</span>
				</div>

				<div class="full-width sale-details text-condensedLight">
					<div class="has-text-weight-bold">Vendedor</div>
					<span class="has-text-link"><?php echo $datos_venta['usuario_nombre'] . " " . $datos_venta['usuario_apellido']; ?></span>
				</div>

				<div class="full-width sale-details text-condensedLight">
					<div class="has-text-weight-bold">Cliente</div>
					<span class="has-text-link"><?php echo $datos_venta['cliente_nombre'] . " " . $datos_venta['cliente_apellido']; ?></span>
				</div>

				<div class="full-width sale-details text-condensedLight">
					<div class="has-text-weight-bold">Documento</div>
					<span class="has-text-link"><?php echo $datos_venta['cliente_numero_documento']; ?></span>
				</div>

			</div>

			<div class="column">

				<div class="full-width sale-details text-condensedLight">
					<div class="has-text-weight-bold">Total</div>
					<span class="has-text-link"><?php echo MONEDA_SIMBOLO . number_format($datos_venta['venta_total'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . ' ' . MONEDA_NOMBRE; ?></span>
				</div>

				<div class="full-width sale-details text-condensedLight">
					<div class="has-text-weight-bold">Pagado</div>
					<span class="has-text-link"><?php echo MONEDA_SIMBOLO . number_format($datos_venta['venta_pagado'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . ' ' . MONEDA_NOMBRE; ?></span>
				</div>

				<div class="full-width sale-details text-condensedLight">
					<div class="has-text-weight-bold">Cambio</div>
					<span class="has-text-link"><?php echo MONEDA_SIMBOLO . number_format($datos_venta['venta_cambio'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . ' ' . MONEDA_NOMBRE; ?></span>
				</div>

			</div>

		</div>

		<div class="columns pb-6 pt-6">
			<p class="has-text-centered full-width">
				<?php
				echo '<button type="button" class="button is-link is-light is-medium" onclick="print_invoice(\'' . APP_URL . 'app/pdf/invoice.php?code=' . $datos_venta['venta_codigo'] . '\')" title="Imprimir factura Nro. ' . $datos_venta['venta_id'] . '" >
            <i class="fas fa-file-invoice-dollar fa-fw"></i> &nbsp; Imprimir Comprobante
            </button> &nbsp;&nbsp; 

            <button type="button" class="button is-link is-light is-medium" onclick="print_ticket(\'' . APP_URL . 'app/pdf/ticket.php?code=' . $datos_venta['venta_codigo'] . '\')" title="Imprimir ticket Nro. ' . $datos_venta['venta_id'] . '" ><i class="fas fa-receipt fa-fw"></i> &nbsp; Imprimir ticket</button> &nbsp;&nbsp;

            <button type="button" class="button is-success is-light is-medium" onclick="facturar()" title="Facturar venta Nro. ' . $datos_venta['venta_id'] . '" ><i class="fas fa-file-invoice fa-fw"></i> &nbsp; Facturar</button>';
				?>
			</p>
		</div>

		<div class="columns pb-6 pt-6">
			<div class="column">
				<div class="table-container">
					<table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
						<thead>
							<tr>
								<th class="has-text-centered">#</th>
								<th class="has-text-centered">Producto</th>
								<th class="has-text-centered">Cant.</th>
								<th class="has-text-centered">Precio</th>
								<th class="has-text-centered">Subtotal</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$detalle_venta = $insLogin->seleccionarDatos("Normal", "venta_detalle WHERE venta_codigo='" . $datos_venta['venta_codigo'] . "'", "*", 0);

							if ($detalle_venta->rowCount() >= 1) {

								$detalle_venta = $detalle_venta->fetchAll();
								$cc = 1;

								foreach ($detalle_venta as $detalle) {
							?>
									<tr class="has-text-centered">
										<td><?php echo $cc; ?></td>
										<td><?php echo $detalle['venta_detalle_descripcion']; ?></td>
										<td><?php echo $detalle['venta_detalle_cantidad']; ?></td>
										<td><?php echo MONEDA_SIMBOLO . number_format($detalle['venta_detalle_precio_venta'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE; ?></td>
										<td><?php echo MONEDA_SIMBOLO . number_format($detalle['venta_detalle_total'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE; ?></td>
									</tr>
								<?php
									$cc++;
								}
								?>
								<tr class="has-text-centered">
									<td colspan="3"></td>
									<td class="has-text-weight-bold">
										TOTAL
									</td>
									<td class="has-text-weight-bold">
										<?php echo MONEDA_SIMBOLO . number_format($datos_venta['venta_total'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE; ?>
									</td>
								</tr>
							<?php
							} else {
							?>
								<tr class="has-text-centered">
									<td colspan="8">
										No hay productos agregados
									</td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<script>
			function facturar() {
				// Mostrar un mensaje de carga
				Swal.fire({
					title: 'Procesando...',
					text: 'Por favor, espera mientras se registra la factura.',
					allowOutsideClick: false,
					didOpen: () => {
						Swal.showLoading();
					}
				});

				const datosVenta = <?php echo json_encode([
										'cliente' => [
											'codigoCliente' => $datos_venta['cliente_id'],
											'razonSocial' => $datos_venta['cliente_nombre'] . ' ' . $datos_venta['cliente_apellido'],
											'numeroDocumento' => $datos_venta['cliente_numero_documento'],
											'email' => $datos_venta['cliente_email'],
											'codigoTipoDocumentoIdentidad' => 5 // Asume que 5 es el código para NIT
										],
										'detalle' => array_map(function ($detalle) {
											return [
												'codigoProductoSin' => '83131', // Asume un código por defecto
												'codigoProducto' => $detalle['producto_id'],
												'descripcion' => $detalle['venta_detalle_descripcion'],
												'cantidad' => $detalle['venta_detalle_cantidad'],
												'unidadMedida' => 1, // Asume una unidad de medida por defecto
												'precioUnitario' => $detalle['venta_detalle_precio_venta'],
												'montoDescuento' => 0 // Asume que no hay descuento
											];
										}, $detalles_venta)
									]); ?>;

				const detalleString = datosVenta.detalle.map(item => `{
            codigoProductoSin: "${item.codigoProductoSin}",
            codigoProducto: "${item.codigoProducto}",
            descripcion: "${item.descripcion}",
            cantidad: ${item.cantidad},
            unidadMedida: ${item.unidadMedida},
            precioUnitario: ${item.precioUnitario},
            montoDescuento: ${item.montoDescuento}
        }`).join(', ');

				const mutation = `mutation FCV_REGISTRO_ONLINE {
            facturaCompraVentaCreate(
                entidad: {
                    codigoSucursal: 0,
                    codigoPuntoVenta: 0
                }
                input: {
                    cliente: {
                        codigoCliente: "${datosVenta.cliente.codigoCliente}",
                        razonSocial: "${datosVenta.cliente.razonSocial}",
                        numeroDocumento: "${datosVenta.cliente.numeroDocumento}",
                        email: "${datosVenta.cliente.email}",
                        codigoTipoDocumentoIdentidad: ${datosVenta.cliente.codigoTipoDocumentoIdentidad}
                    },
                    codigoExcepcion: 1,
                    actividadEconomica: "620000",
                    codigoMetodoPago: 1,
                    descuentoAdicional: 0,
                    codigoMoneda: 1,
                    tipoCambio: 1,
                    detalleExtra: "<p><strong>Detalle extra</strong></p>",
                    detalle: [${detalleString}]
                }
            ) {
                _id
                cafc
                cliente {
                    _id
                    apellidos
                    razonSocial
                    state
                    tipoDocumentoIdentidad {
                        codigoClasificador
                        descripcion
                    }
                    UpdatedAt
                    usucre
                    usumod
                }
                cuf
                razonSocialEmisor
                representacionGrafica {
                    pdf
                    rollo
                    sin
                    xml
                }
                state
                updatedAt
                usuario
                usucre
                usumod
            }
        }`;

				fetch('https://sandbox.isipass.net/api', {
						method: 'POST',
						headers: {
							'Content-Type': 'application/json',
							'Authorization': 'Bearer <?php echo $_SESSION['isipass_token'] ?? ''; ?>'
						},
						body: JSON.stringify({
							query: mutation
						})
					})
					.then(response => response.json())
					.then(result => {
						Swal.close(); // Cerrar el mensaje de carga

						if (result.errors) {
							// Extraer el mensaje de error de la respuesta
							const errorMessage = result.errors.map(error => error.message).join(' ');
							Swal.fire({
								icon: 'error',
								title: 'Error',
								text: `${errorMessage}`,
							});
						} else {
							const {
								pdf,
								rollo,
								sin,
								xml
							} = result.data.facturaCompraVentaCreate.representacionGrafica;

							Swal.fire({
								icon: 'success',
								title: '¡Éxito!',
								text: 'Factura registrada exitosamente',
								showCancelButton: true,
								confirmButtonText: '<a href="' + pdf + '" target="_blank">Ver PDF</a>',
								cancelButtonText: 'Cerrar',
								showCloseButton: true,
								didClose: () => {
									// Aquí puedes manejar lo que sucede cuando el usuario cierra el mensaje
								}
							});
						}
					})
					.catch(error => {
						console.error('Error al registrar la factura:', error);
						Swal.close(); // Cerrar el mensaje de carga
						Swal.fire({
							icon: 'error',
							title: 'Error',
							text: 'Hubo un problema al registrar la factura',
						});
					});
			}
		</script>


	<?php
	include "./app/views/inc/print_invoice_script.php";
} else {
	include "./app/views/inc/error_alert.php";
}
	?>
	</div>