<?php  
	verificaPermissaoPagina(2);
?>
<?php
    if(!(isset($_GET['id']) && (int)$_GET['id'] && $_GET['id'] > 0)){
        Painel::alert("erro", "O ID do Usuário não foi encontrado.");
        return;
    }

	if(isset($_GET['pagina']) && (int)$_GET['pagina'] && $_GET['pagina'] > 0) {
		$paginaAtual = filter_var($_GET['pagina'], FILTER_SANITIZE_NUMBER_INT);
	}

	else {
		$paginaAtual = 1;
	}

	$porPagina = 10;

    $idUsuario = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

	$historicoUsuario = PedidoDetalhes::retornaHistoricoPedidosPorIDUsuario($idUsuario,($paginaAtual - 1) * $porPagina, $porPagina);

	if($historicoUsuario == false && $paginaAtual != 1) {
		Painel::redirect(INCLUDE_PATH_PAINEL . 'historico-usuario?id=' . $idUsuario);
	}
?>
<div class="box-content">
	<h2> <i class="fas fa-history"></i> Histórico de Pedidos</h2>
	<form class="buscador">	
		<div class="form-group">
			<label for="campo">Não encontrou o que procura? Faz uma busca! <i class="fa fa-search"></i></label>
			<input type="date" name="busca" required="" placeholder="Ex: Fulano" id="campo">
			<div class="filtro">
                <input type="hidden" name="id" value="<?php echo $idUsuario; ?>">

				<input type="radio" id="opcao-data" name="opcao" value="data-solicitado">
				<label for="opcao-data">Data de solicitção</label>

                <input type="radio" id="opcao-data" name="opcao" value="data-finalizado">
				<label for="opcao-data">Data de finalização</label>
			</div>
			<input type="submit" name="buscar" value="Buscar">			
		</div>
	</form>
	<?php 
		if(isset($_GET['buscar'])) {
			$data = filter_var($_GET["busca"], FILTER_SANITIZE_STRING);
            $filtro = "pedido_detalhes.data_pedido";
			if(isset($_GET['opcao'])) {
				$filtro = filter_var($_GET["opcao"], FILTER_SANITIZE_STRING);
				
				switch ($filtro) {
					case 'data-finalizado':
						$filtro = "pedido_detalhes.data_finalizado";
						break;

					case 'data-pedido':
						$filtro = "pedido_detalhes.data_pedido";
						break;

					default:
                        $filtro = "pedido_detalhes.data_pedido";
                        break;
				}
			}

            if(!empty(PedidoDetalhes::returnDataHistoricoPedidosPorIDUsuario($data, $filtro, $idUsuario))){
                $historicoUsuario = PedidoDetalhes::returnDataHistoricoPedidosPorIDUsuario($data, $filtro, $idUsuario);
            }
		}
	?>
	<div class="wraper-table">
		<table>
			<tr>
				<td>Nome</td>
				<td>Sobrenome</td>
				<td>Matrícula</td>
				<td>Código</td>
				<td>Solicitação</td>
				<td>Finalização</td>
				<td>#</td>
                <td>#</td>
			</tr>
			<?php
				if($historicoUsuario != false) {
					foreach ($historicoUsuario as $historico) {
			?>

				<tr>
					<td><?php echo htmlentities($historico->usuario->getNome()); ?></td>

					<td><?php echo htmlentities($historico->usuario->getSobrenome()); ?></td>

					<td><?php echo htmlentities($historico->usuario->getMatricula()); ?></td>

					<td><?php echo htmlentities($historico->getCodigoPedido()); ?></td>

					<td>
                        <?php
							$dataHoraCompleta = htmlentities($historico->getDataPedido());

							// Extrair apenas a parte da data
							$dataSomente = explode(' ', $dataHoraCompleta)[0]; // 'YYYY-MM-DD'
																			
							// Converter o formato de 'YYYY-MM-DD' para 'DD/MM/YYYY'
							$dataConvertida = implode("/", array_reverse(explode("-", $dataSomente)));
							echo $dataConvertida; 
                        ?>
                    </td>

					<td>
                        <?php
                            if($historico->getAprovado() == 0 && $historico->getFinalizado() == 0) {
                                echo "EM ANÁLISE";
                            }

                            if($historico->getAprovado() == 1 && $historico->getFinalizado() == 0) {
                                echo "EM ANDAMENTO";
                            }

                            if($historico->getDataFinalizado() != NULL) {
								$dataHoraCompleta = htmlentities($historico->getDataFinalizado());

								// Extrair apenas a parte da data
								$dataSomente = explode(' ', $dataHoraCompleta)[0]; // 'YYYY-MM-DD'
																				
								// Converter o formato de 'YYYY-MM-DD' para 'DD/MM/YYYY'
								$dataConvertida = implode("/", array_reverse(explode("-", $dataSomente)));
								echo $dataConvertida; 
                            }
                        ?>
                    </td>

					<?php 
						$itensPedido = PedidoDetalhes::itensViaIDDetalhe($historico->getId());
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
							'nome' => $historico->usuario->getNome(),
							'sobrenome' => $historico->usuario->getSobrenome(),
							'matricula' => $historico->usuario->getMatricula(),
							'data' => $dataConvertida
						]); ?>'>
							Espiar pedido <i class="fa fa-eye"></i>
						</a>
    				</td>

					<td><a href="<?php echo INCLUDE_PATH_PAINEL?>movimentar-pedido?codigo_pedido=<?php echo htmlentities($historico->getCodigoPedido()); ?>" class="btn edit">Visualizar pedido <i class="fa fa-eye"></i></a></td>
				</tr>
			<?php } }?>
		</table>
	</div>
	<div class="paginacao">
		<?php
			$totalPaginas = ceil(count(PedidoDetalhes::retornaHistoricoPedidosPorIDUsuario($idUsuario)) / $porPagina);

			for($i = 1; $i <= $totalPaginas; $i++) {
				if($i == $paginaAtual) {
					echo '<a href="' . INCLUDE_PATH_PAINEL . 'historico-usuario?id=' . $idUsuario . '&pagina=' . $i . '" class="page-selected">' . $i . '</a>';
				}

				else {
					echo '<a href="' . INCLUDE_PATH_PAINEL . 'historico-usuario?id=' . $idUsuario . '&pagina=' . $i . '">' . $i . '</a>';
				}
			}
		?>
	</div>
</div>