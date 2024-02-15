<?php  
	verificaPermissaoPagina(2);
?>

<div class="box-content">
	<h2> <i class="fas fa-spinner"></i> Empréstimos Não Finalizados</h2>
	<form class="buscador">	
		<div class="form-group">
			<label for="campo">Não encontrou o que procura? Faz uma busca! <i class="fa fa-search"></i></label>
			<input type="text" name="busca" required="" placeholder="Ex: Fulano" id="campo">
			<div class="filtro">
				<input type="radio" id="opcao-nome" name="opcao" value="nome">
				<label for="opcao-nome">Nome</label>

				<input type="radio" id="opcao-matricula" name="opcao" value="matricula">
				<label for="opcao-matricula">Matrícula</label>

				<input type="radio" id="opcao-email" name="opcao" value="email">
				<label for="opcao-email">E-mail</label>

				<input type="radio" id="opcao-data" name="opcao" value="data">
				<label for="opcao-data">Data</label>
			</div>
			<input type="submit" name="buscar" value="Buscar">			
		</div>
	</form>
	<div class="wraper-table">
		<table>
			<tr>
				<td>Nome</td>
				<td>Sobrenome</td>
				<td>Matrícula</td>
				<td>Código</td>
				<td>Data</td>
				<td>#</td>
				<td>#</td>
			</tr>
			<?php
				$pedidoNaoFinalizados = PedidoDetalhes::retornaPedidosNaoFinalizados();
				
				if($pedidoNaoFinalizados != false){
				
					foreach ($pedidoNaoFinalizados as $pedidoNaoFinalizado) {
			?>

				<tr>
					<td><?php echo htmlentities($pedidoNaoFinalizado->usuario->getNome()); ?></td>

					<td><?php echo htmlentities($pedidoNaoFinalizado->usuario->getSobrenome()); ?></td>

					<td><?php echo htmlentities($pedidoNaoFinalizado->usuario->getMatricula()); ?></td>

					<td><?php echo htmlentities($pedidoNaoFinalizado->getCodigoPedido()); ?></td>

					<td>
                        <?php
                            $dataConvertida = htmlentities($pedidoNaoFinalizado->getDataPedido());
                            $dataConvertida = implode("/",array_reverse(explode("-",$dataConvertida)));
                            echo $dataConvertida; 
                        ?>
                    </td>
					<?php 
						$itensPedido = PedidoDetalhes::itensViaIDDetalhe($pedidoNaoFinalizado->getId());
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
							'nome' => $pedidoNaoFinalizado->usuario->getNome(),
							'sobrenome' => $pedidoNaoFinalizado->usuario->getSobrenome(),
							'matricula' => $pedidoNaoFinalizado->usuario->getMatricula(),
							'data' => $dataConvertida
						]); ?>'>
							Espiar pedido <i class="fa fa-eye"></i>
						</a>
    				</td>

					<td><a href="<?php echo INCLUDE_PATH_PAINEL?>fechar-pedido?codigo_pedido=<?php echo htmlentities($pedidoNaoFinalizado->getCodigoPedido()); ?>" class="btn edit">Visualizar pedido <i class="fa fa-eye"></i></a></td>
				</tr>
			<?php } }?>
		</table>
	</div>
</div>