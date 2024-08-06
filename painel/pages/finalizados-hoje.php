<?php  
	verificaPermissaoPagina(2);
?>

<?php 
	if(isset($_GET['pagina']) && (int)$_GET['pagina'] && $_GET['pagina'] > 0) {
		$paginaAtual = filter_var($_GET['pagina'], FILTER_SANITIZE_NUMBER_INT);
	}

	else {
		$paginaAtual = 1;
	}

	$porPagina = 10;

	$pedidosFinalizadosHoje = PedidoDetalhes::retornaPedidosFinalizadosHoje(($paginaAtual - 1) * $porPagina, $porPagina);

	if($pedidosFinalizadosHoje == false && $paginaAtual != 1) {
		Painel::redirect(INCLUDE_PATH_PAINEL . 'finalizados-hoje');
	}
?>

<div class="box-content">
	<h2> <i class="fas fa-check-circle"></i> Finalizados Hoje</h2>
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

				<input type="radio" id="opcao-nome" name="opcao" value="nome-estoque">
				<label for="opcao-nome">Nome do item</label>
			</div>
			<input type="submit" name="buscar" value="Buscar">			
		</div>
	</form>
	<?php 
		if(isset($_GET['buscar'])) {
			$data = filter_var($_GET["busca"], FILTER_SANITIZE_STRING);
			$filtro = "usuarios.nome" ;

			if(isset($_GET['opcao'])) {
				$filtro = filter_var($_GET["opcao"], FILTER_SANITIZE_STRING);
				
				switch ($filtro) {
					case 'matricula':
						$filtro = "usuarios.matricula";
						break;
				
						case 'nome-estoque':
						$filtro = "estoque.nome";
						break;
									
					case 'data':
						$filtro = "pedido_detalhes.data_pedido";
						break;

					case 'email':
						$filtro = "usuarios.email";
						break;

					default:
						$filtro = "usuarios.nome";
						break;
				}
			}

            if(!empty(PedidoDetalhes::returnDataPedidosFinalizadosHoje($data, $filtro))){
                $pedidosFinalizadosHoje = PedidoDetalhes::returnDataPedidosFinalizadosHoje($data, $filtro);
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
				<td>Data</td>
				<td>#</td>
				<td>#</td>
			</tr>
			<?php

				
				if($pedidosFinalizadosHoje != false){
				
					foreach ($pedidosFinalizadosHoje as $pedidoFinalizadosHoje) {
			?>

				<tr>
					<td><?php echo htmlentities($pedidoFinalizadosHoje->usuario->getNome()); ?></td>

					<td><?php echo htmlentities($pedidoFinalizadosHoje->usuario->getSobrenome()); ?></td>

					<td><?php echo htmlentities($pedidoFinalizadosHoje->usuario->getMatricula()); ?></td>

					<td><?php echo htmlentities($pedidoFinalizadosHoje->getCodigoPedido()); ?></td>

					<td>
                        <?php
							$dataHoraCompleta = htmlentities($pedidoFinalizadosHoje->getDataPedido());

							// Extrair apenas a parte da data
							$dataSomente = explode(' ', $dataHoraCompleta)[0]; // 'YYYY-MM-DD'

							// Extrair apenas a parte da hora
							$horaCompleta = explode(' ', $pedidoFinalizadosHoje->getDataPedido())[1]; // 'HH:MM:SS'

							// Converter o formato de 'YYYY-MM-DD' para 'DD/MM/YYYY'
							$dataConvertida = implode("/", array_reverse(explode("-", $dataSomente)));
							echo $dataConvertida; 
                        ?>
                    </td>
					<?php 
						$itensPedido = PedidoDetalhes::itensViaIDDetalhe($pedidoFinalizadosHoje->getId());
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
							'nome' => $pedidoFinalizadosHoje->usuario->getNome(),
							'sobrenome' => $pedidoFinalizadosHoje->usuario->getSobrenome(),
							'matricula' => $pedidoFinalizadosHoje->usuario->getMatricula(),
							'data' => $dataConvertida,
							'hora' => $horaCompleta
						]); ?>'>
							Espiar pedido <i class="fa fa-eye"></i>
						</a>
    				</td>

					<td><a href="<?php echo INCLUDE_PATH_PAINEL?>visualizar-finalizado?codigo_pedido=<?php echo htmlentities($pedidoFinalizadosHoje->getCodigoPedido()); ?>" class="btn edit">Visualizar pedido <i class="fa fa-eye"></i></a></td>
				</tr>
			<?php } }?>
		</table>
	</div>

	<div class="paginacao">
		<?php
			$totalPaginas = ceil(count(PedidoDetalhes::retornaPedidosFinalizadosHoje()) / $porPagina);

			for($i = 1; $i <= $totalPaginas; $i++) {
				if($i == $paginaAtual) {
					echo '<a href="' . INCLUDE_PATH_PAINEL . 'finalizados-hoje?pagina=' . $i . '" class="page-selected">' . $i . '</a>';
				}

				else {
					echo '<a href="' . INCLUDE_PATH_PAINEL . 'finalizados-hoje?pagina=' . $i . '">' . $i . '</a>';
				}
			}
		?>
	</div>
</div>