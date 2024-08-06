<?php  
	verificaPermissaoPagina(1);
?>

<?php
	// Verifica se o código do pedido está presente e tem o comprimento correto
	if (!isset($_GET['codigo_pedido']) || strlen($_GET['codigo_pedido']) != 20) {
		Painel::alert("erro", "Você precisa passar o código do pedido");
		die();
	}

	// Filtra e obtém o código do pedido
	$codigoPedido = filter_var($_GET['codigo_pedido'], FILTER_SANITIZE_STRING);

	// Obtém os dados básicos do pedido pendente do usuário através do código do pedido
	$dadosBasicos = PedidoDetalhes::retornaDadosPedidosAnterioresUsuario($codigoPedido);

	// Verifica se os dados básicos do pedido não foram obtidos com sucesso
	if (!$dadosBasicos) {
		Painel::alert("erro", "Código do pedido não encontrado ou pedido já revisado.");
		die();
	}
?>
<div class="box-content">

<h2><i class="fa fa-pencil-alt"></i> Detalhes do pedido:
	<?php
	    echo htmlentities($dadosBasicos->getCodigoPedido());
	?>
</h2>

<h3>Pedido feito por <?php echo htmlentities($dadosBasicos->usuario->getNome() . " " . $dadosBasicos->usuario->getSobrenome()) ?> em
	<?php
		$dataHoraCompleta = htmlentities($dadosBasicos->getDataPedido());

		// Extrair apenas a parte da data
		$dataSomente = explode(' ', $dataHoraCompleta)[0]; // 'YYYY-MM-DD'
													
		// Converter o formato de 'YYYY-MM-DD' para 'DD/MM/YYYY'
		$dataConvertida = implode("/", array_reverse(explode("-", $dataSomente)));

		// Extrair apenas a parte da hora
		$horaCompleta = explode(' ', $dadosBasicos->getDataPedido())[1]; // 'HH:MM:SS'
		echo $dataConvertida . " às " . $horaCompleta;
	?>
	
	e finalizado em
	
	<?php
		$dataHoraCompleta = htmlentities($dadosBasicos->getDataFinalizado());

		// Extrair apenas a parte da data
		$dataSomente = explode(' ', $dataHoraCompleta)[0]; // 'YYYY-MM-DD'
													
		// Converter o formato de 'YYYY-MM-DD' para 'DD/MM/YYYY'
		$dataConvertida = implode("/", array_reverse(explode("-", $dataSomente)));

		// Extrair apenas a parte da hora
		$horaCompleta = explode(' ', $dadosBasicos->getDataFinalizado())[1]; // 'HH:MM:SS'
		echo $dataConvertida . " às " . $horaCompleta;
    ?>
</h3>

<?php 
    $itensPedido = PedidoDetalhes::itensViaIDDetalhe($dadosBasicos->getId());

    if(isset($_GET['repetir']) && isset($_GET['codigo_pedido']) && $_GET['codigo_pedido'] == $dadosBasicos->getCodigoPedido()) {
		if(isset($_SESSION['is_bloqueado'])) {
			Painel::alert("erro", "Você não está autorizado a fazer pedidos. Consulte o administrador para mais informações.");
		}
		else if(PedidoDetalhes::maisde3PedidosPendentes($_SESSION['id'])) {
			Painel::alert("erro", "Você pode ter no máximo 3 pedidos pendentes de análise. Entre em contato com o administrador para mais informações.");
		}		

		else {
        	Carrinho::refazerPedido($itensPedido);
		}
    }

?>

<div class="wraper-table">
	<table>
		<tr>
			<td>Item</td>
			<td>Quantidade</td>
			<td>Tipo</td>
		</tr>
		<?php
		foreach ($itensPedido as $itemPedido) {
		?>

			<tr>
				<td><?php echo htmlentities($itemPedido->estoque->getNome()); ?></td>

				<td><?php echo htmlentities($itemPedido->getQuantidadeItem()); ?></td>
				<td><?php echo htmlentities(tipoEstoque($itemPedido->estoque->getTipo())); ?></td>
			</tr>
		<?php } ?>
	</table>

    <div class="box-operacoes">
		<a href="<?php echo INCLUDE_PATH_PAINEL ?>pedidos-anteriores?repetir&codigo_pedido=<?php 
			echo htmlentities($dadosBasicos->getCodigoPedido())
		?>" class="operacao">Pedir novamente <i class="fas fa-redo"></i></a>
	</div>
</div>
</div>