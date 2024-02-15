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
	$dadosBasicos = PedidoDetalhes::retornaDadosPedidosPendenteUsuario($codigoPedido);

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
	$dataConvertida = htmlentities($dadosBasicos->getDataPedido());
	$dataConvertida = implode("/", array_reverse(explode("-", $dataConvertida)));
	echo $dataConvertida;
	?>
</h3>

<div class="wraper-table">
	<table>
		<tr>
			<td>Item</td>
			<td>Quantidade</td>
			<td>Tipo</td>
		</tr>
		<?php
		$itensPedido = PedidoDetalhes::itensViaIDDetalhe($dadosBasicos->getId());
		foreach ($itensPedido as $itemPedido) {
		?>

			<tr>
				<td><?php echo htmlentities($itemPedido->estoque->getNome()); ?></td>

				<td><?php echo htmlentities($itemPedido->getQuantidadeItem()); ?></td>
				<td><?php echo htmlentities(tipoEstoque($itemPedido->estoque->getTipo())); ?></td>
			</tr>
		<?php } ?>
	</table>
</div>


</div>