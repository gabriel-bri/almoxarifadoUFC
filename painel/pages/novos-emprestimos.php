<?php  
	verificaPermissaoPagina(2);
?>

<div class="box-content">
	<h2> <i class="fas fa-plus-circle"></i> Novos Empréstimos</h2>
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
				$pedidoPendentes = PedidoDetalhes::retornaPedidosPendentes();

				if($pedidoPendentes != false) {
					foreach ($pedidoPendentes as $pedidoPendente) {
			?>

				<tr>
					<td><?php echo htmlentities($pedidoPendente->usuario->getNome()); ?></td>

					<td><?php echo htmlentities($pedidoPendente->usuario->getSobrenome()); ?></td>

					<td><?php echo htmlentities($pedidoPendente->usuario->getMatricula()); ?></td>

					<td><?php echo htmlentities($pedidoPendente->getCodigoPedido()); ?></td>

					<td>
                        <?php
                            $dataConvertida = htmlentities($pedidoPendente->getDataPedido());
                            $dataConvertida = implode("/",array_reverse(explode("-",$dataConvertida)));
                            echo $dataConvertida; 
                        ?>
                    </td>
					<?php 
						$itensPedido = PedidoDetalhes::itensViaIDDetalhe($pedidoPendente->getId());
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
							'nome' => $pedidoPendente->usuario->getNome(),
							'sobrenome' => $pedidoPendente->usuario->getSobrenome(),
							'matricula' => $pedidoPendente->usuario->getMatricula(),
							'data' => $dataConvertida
						]); ?>'>
							Espiar pedido <i class="fa fa-eye"></i>
						</a>
    				</td>

					<td><a href="<?php echo INCLUDE_PATH_PAINEL?>movimentar-pedido?codigo_pedido=<?php echo htmlentities($pedidoPendente->getCodigoPedido()); ?>" class="btn edit">Visualizar pedido <i class="fa fa-eye"></i></a></td>
				</tr>
			<?php } }?>
		</table>
	</div>
</div>