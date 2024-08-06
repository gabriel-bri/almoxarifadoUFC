<?php  
	verificaPermissaoPagina(1);
?>

<?php 
	if(isset($_GET['pagina']) && (int)$_GET['pagina'] && $_GET['pagina'] > 0) {
		$paginaAtual = filter_var($_GET['pagina'], FILTER_SANITIZE_NUMBER_INT);
	}

	else {
		$paginaAtual = 1;
	}

	$porPagina = 10;

	$meusPedidos = PedidoDetalhes::retornaPedidosAntigosUsuario(($paginaAtual - 1) * $porPagina, $porPagina);
	if($meusPedidos == false && $paginaAtual != 1) {
		Painel::redirect(INCLUDE_PATH_PAINEL . 'historico-pedidos');
	}
?>

<div class="box-content">
	<h2> <i class="fas fa-history"></i> Histórico De Pedidos</h2>
	<form class="buscador">	
		<div class="form-group">
			<label for="campo">Não encontrou o que procura? Faz uma busca! <i class="fa fa-search"></i></label>
			<input type="text" name="busca" required="" placeholder="Ex: Fulano" id="campo">
			<div class="filtro">
			<input type="radio" id="opcao-data" name="opcao" value="data-solicitado">
				<label for="opcao-data">Data de solicitação</label>

                <input type="radio" id="opcao-data" name="opcao" value="data-finalizado">
				<label for="opcao-data">Data de finalização</label>

                <input type="radio" id="opcao-nome" name="opcao" value="nome">
				<label for="opcao-nome">Nome do item</label>
			</div>
			<input type="submit" name="buscar" value="Buscar">			
		</div>
	</form>
	<?php 
		if(isset($_GET['buscar'])) {
			$data = filter_var($_GET["busca"], FILTER_SANITIZE_STRING);
			$filtro = "estoque.nome";

			if(isset($_GET['opcao'])) {
				$filtro = filter_var($_GET["opcao"], FILTER_SANITIZE_STRING);
				
				switch ($filtro) {
					case 'matricula':
						$filtro = "usuarios.matricula";
						break;
					
					case 'data-solicitado':
						$filtro = "pedido_detalhes.data_pedido";
						break;

					case 'data-finalizado':
						$filtro = "pedido_detalhes.data_finalizado";
						break;

					case 'nome':
						$filtro = "estoque.nome";
						break;

					default:
						$filtro = "estoque.nome";
						break;
				}
			}

            if(!empty(PedidoDetalhes::returnDataPedidosAntigosUsuario($data, $filtro))){
                $meusPedidos = PedidoDetalhes::returnDataPedidosAntigosUsuario($data, $filtro);
            }
		}
	?>
	<div class="wraper-table">
		<table>
			<tr>
				<td>Código</td>
				<td>Data do Pedido</td>
				<td>Data de Finalização</td>
				<td>#</td>
				<td>#</td>
			</tr>
			<?php
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
							echo $dataConvertida; 
                        ?>
                    </td>

					<td>
                        <?php
							$dataHoraCompleta = htmlentities($meuPedido->getDataFinalizado());

							// Extrair apenas a parte da data
							$dataSomente = explode(' ', $dataHoraCompleta)[0]; // 'YYYY-MM-DD'
							
							// Extrair apenas a parte da hora
							$horaCompleta = explode(' ', $meuPedido->getDataPedido())[1]; // 'HH:MM:SS'																			
							
							// Converter o formato de 'YYYY-MM-DD' para 'DD/MM/YYYY'
							$dataConvertida = implode("/", array_reverse(explode("-", $dataSomente)));
							echo $dataConvertida; 
                        ?>
                    </td>
					
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

					<td><a href="<?php echo INCLUDE_PATH_PAINEL?>pedidos-anteriores?codigo_pedido=<?php echo htmlentities($meuPedido->getCodigoPedido()); ?>" class="btn edit">Visualizar pedido <i class="fa fa-eye"></i></a></td>
				</tr>
			<?php } } ?>
		</table>
	</div>

	<div class="paginacao">
		<?php
			$totalPaginas = ceil(count(PedidoDetalhes::retornaPedidosAntigosUsuario()) / $porPagina);
	
			for($i = 1; $i <= $totalPaginas; $i++) {
				if($i == $paginaAtual) {
					echo '<a href="' . INCLUDE_PATH_PAINEL . 'historico-pedidos?pagina=' . $i . '" class="page-selected">' . $i . '</a>';
				}

				else {
					echo '<a href="' . INCLUDE_PATH_PAINEL . 'historico-pedidos?pagina=' . $i . '">' . $i . '</a>';
				}
			}
		?>
	</div>
</div>