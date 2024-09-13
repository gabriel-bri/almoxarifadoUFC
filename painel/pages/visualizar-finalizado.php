<?php
	verificaPermissaoPagina(2);
?>

<?php
	// Verifica se o código do pedido está presente e possui o comprimento correto
	if (!isset($_GET['codigo_pedido']) || strlen($_GET['codigo_pedido']) != 20) {
		Painel::alert("erro", "Você precisa passar o código do pedido");
		die();
	}

	// Filtra e obtém o código do pedido
	$codigoPedido = filter_var($_GET['codigo_pedido'], FILTER_SANITIZE_STRING);

	// Obtém os dados básicos do pedido ativo do usuário através do código do pedido
	$dadosBasicos = PedidoDetalhes::retornaDadosFinalizadoHoje($codigoPedido);

	// Verifica se os dados básicos do pedido foram obtidos com sucesso
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

        <?php
        if($dadosBasicos->getDataFinalizado() != NULL) {
            $dataHoraCompleta = htmlentities($dadosBasicos->getDataFinalizado());

            // Extrair apenas a parte da data
            $dataSomente = explode(' ', $dataHoraCompleta)[0]; // 'YYYY-MM-DD'

            // Converter o formato de 'YYYY-MM-DD' para 'DD/MM/YYYY'
            $dataConvertida = implode("/", array_reverse(explode("-", $dataSomente)));

            // Extrair apenas a parte da hora
            $horaCompleta = explode(' ', $dadosBasicos->getDataFinalizado())[1]; // 'HH:MM:SS'
            echo "e finalizado em " . $dataConvertida . " às " . $horaCompleta;
        }
        ?>
	</h3>

    <h3>Aprovado por:
        <?php
        $nomeAprovador = Usuario::getNomeCompletoById($dadosBasicos->getIdUsuarioAprovou());
        echo htmlentities($nomeAprovador);
        ?>
    </h3>

    <h3>Finalizado por:
        <?php
        $nomeFinalizador = Usuario::getNomeCompletoById($dadosBasicos->getIdUsuarioFinalizou());
        echo htmlentities($nomeFinalizador);
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

    <?php if ($dadosBasicos->getEmprestimoEspecial()) : ?>
        <h3 style="color: red;">Este é um Empréstimo Especial.</h3>
    <?php endif; ?>

    <?php if (!empty($dadosBasicos->getFeedback())) : ?>
        <h3>Feedback do Pedido:</h3>
        <p><?php echo nl2br(htmlentities($dadosBasicos->getFeedback())); ?></p>
    <?php endif; ?>
</div>