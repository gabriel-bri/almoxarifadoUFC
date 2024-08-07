<?php  
	verificaPermissaoPagina(1);
?>

<div class="box-content">
	<h2> <i class="fas fa-handshake"></i> Meus Pedidos</h2>

	<div class="wraper-table">
		<table>
			<tr>
				<td>Código</td>
				<td>Data</td>
				<td>Status</td>
				<td>#</td>
				<td>#</td>
			</tr>
			<?php
				$meusPedidos = PedidoDetalhes::retornaPedidosAtivosUsuario();
				if($meusPedidos != false) {
					foreach ($meusPedidos as $meuPedido) {
					
			?>

				<tr>
					<td><?php echo htmlentities($meuPedido->getCodigoPedido()); ?></td>

					<td>
                        <?php
							$dataHoraCompleta = htmlentities($meuPedido->getDataPedido());

							// Extrair apenas a parte da data
							$dataSomente = explode(' ', $dataHoraCompleta)[0]; // 'YYYY-MM-DD'
																										
							// Converter o formato de 'YYYY-MM-DD' para 'DD/MM/YYYY'
							$dataConvertida = implode("/", array_reverse(explode("-", $dataSomente)));

							// Extrair apenas a parte da hora
							$horaCompleta = explode(' ', $meuPedido->getDataPedido())[1]; // 'HH:MM:SS'
				
							echo $dataConvertida;
                        ?>
                    </td>
					
					<td>Ativo</td>
					
					<?php 
						$itensPedido = PedidoDetalhes::itensViaIDDetalhe($meuPedido->getId());
					?>
					<td>
						<a href="#" class="btn delete espiar-pedido" 
						data-itensPedido='<?php echo json_encode(array_map(function($itemPedido) {
							return [
								'nome' => $itemPedido->estoque->getNome(),
								'quantidade' => $itemPedido->getQuantidadeItem(),
								'tipo' => tipoEstoque($itemPedido->estoque->getTipo())
							];
						}, $itensPedido)); ?>' 
						
						data-usuario='<?php echo json_encode([
							'nome' => $meuPedido->usuario->getNome(),
							'sobrenome' => $meuPedido->usuario->getSobrenome(),
							'matricula' => $meuPedido->usuario->getMatricula(),
							'data' => $dataConvertida,
							'hora' => $horaCompleta
						]); ?>'>
							Espiar pedido <i class="fa fa-eye"></i>
						</a>
    				</td>

					<td><a href="<?php echo INCLUDE_PATH_PAINEL?>visualizar-pedido?codigo_pedido=<?php echo htmlentities($meuPedido->getCodigoPedido()); ?>" class="btn edit">Visualizar pedido <i class="fa fa-eye"></i></a></td>
				</tr>
			<?php } } ?>
		</table>
	</div>
</div>