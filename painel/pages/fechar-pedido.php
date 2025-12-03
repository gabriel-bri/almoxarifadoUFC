<?php
	verificaPermissaoPagina(2);
?>

<?php
    function redirect() {
	echo "<meta http-equiv='refresh' content='5;url=" . INCLUDE_PATH_PAINEL . "nao-finalizados'>";
    }
?>

<?php
	// Verifica se o código do pedido está presente e tem o comprimento correto
	if (!isset($_GET['codigo_pedido']) || strlen($_GET['codigo_pedido']) != 20) {
		Painel::alert("erro", "Você precisa passar o código do pedido");
		die();
	}

	// Filtra e obtém o código do pedido
	$codigoPedido = filter_var($_GET['codigo_pedido'], FILTER_SANITIZE_STRING);

	// Obtém os dados básicos do pedido
	$dadosBasicos = PedidoDetalhes::retornaDadosPedidoViaCodigo($codigoPedido);

	// Verifica se os dados básicos do pedido não foram obtidos com sucesso
	if (!$dadosBasicos) {
		Painel::alert("erro", "Código do pedido não encontrado ou pedido já revisado.");
		die();
	}

	// Verifica se o usuário está tentando fazer movimentações em seu próprio pedido (apenas para usuários de nível 2)
	if ($dadosBasicos->usuario->getId() == $_SESSION['id'] && $_SESSION['acesso'] == 2) {
		Painel::alert("erro", "Você não pode fazer movimentações no seu próprio pedido.");
		die();
	}
?>

<?php
if (isset($_POST['salvarFeedback'])) {
    // Sanitiza o feedback
    $novoFeedback = trim(filter_var($_POST['feedback'], FILTER_SANITIZE_STRING));

    // Verifica se o checkbox foi marcado
    $emprestimoEspecial = isset($_POST['emprestimo_especial']) ? 1 : 0;

    if (empty($novoFeedback)) {
        $novoFeedback = null;
    }

    // Atualiza o feedback e o status de empréstimo especial no banco de dados
    $atualizado = PedidoDetalhes::atualizarFeedbackEEmprestimoEspecial($dadosBasicos->getId(), $novoFeedback, $emprestimoEspecial);

    // Exibe uma mensagem de sucesso ou erro com JavaScript alert
    if ($atualizado) {
        // Atualiza o objeto para refletir as mudanças na página
        $dadosBasicos->setFeedback($novoFeedback);
        $dadosBasicos->setEmprestimoEspecial($emprestimoEspecial);

        // Exibe o alert de sucesso
		Painel::alert("sucesso", "Dados atualizados com sucesso!");
    } else {
        // Exibe o alert de erro
		Painel::alert("erro", "Dados atualizados com sucesso!");
    }
}
?> 

<div class="box-content">

	<h2><i class="fa fa-pencil-alt"></i> Detalhes do pedido:
		<?php
			echo htmlentities($dadosBasicos->getCodigoPedido());
		?>
	</h2>

	<h3>Pedido feito por <?php echo htmlentities($dadosBasicos->usuario->getNome() . " " . $dadosBasicos->usuario->getSobrenome())?> em
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
    </h3>

    <h3>Aprovado por:
        <?php
        $nomeAprovador = Usuario::getNomeCompletoById($dadosBasicos->getIdUsuarioAprovou());
        echo htmlentities($nomeAprovador);
        ?>
    </h3>

    <?php
	if(isset($_GET['fechar']) && $_GET['codigo_pedido'] == $dadosBasicos->getCodigoPedido()) {
		PedidoDetalhes::marcarComoFinalizado($dadosBasicos, $_SESSION['id']);
		Painel::alert("sucesso", "O pedido foi finalizado, o usuário será notificado. Redirecionando.");
		redirect();
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
				$itensPedido = PedidoDetalhes::itensViaIDDetalhe($dadosBasicos->getId());
				foreach($itensPedido as $itemPedido){
			?>

			<tr>
				<td><?php echo htmlentities($itemPedido->estoque->getNome()); ?></td>

				<td><?php echo htmlentities($itemPedido->getQuantidadeItem()); ?></td>
				<td><?php echo htmlentities(tipoEstoque($itemPedido->estoque->getTipo())); ?></td>
			</tr>
			<?php } ?>
		</table>
	</div>

    <h3>Feedback:</h3>
    <form method="post" action="" style="width: 100%; max-width: 600px;">
        <textarea name="feedback" rows="4" cols="50"><?php echo htmlentities($dadosBasicos->getFeedback()); ?></textarea>
        <br><br>
        <label>
            <input type="checkbox" name="emprestimo_especial" value="1" <?php echo $dadosBasicos->getEmprestimoEspecial() ? 'checked' : ''; ?>>
            Empréstimo Especial
        </label>
        <br><br>

        <input type="submit" name="salvarFeedback" value="Salvar">
    </form>

    <?php
    if (isset($mensagemFeedback)) {
        echo "<p>" . htmlentities($mensagemFeedback) . "</p>";
    }
    ?>

	<div class="box-operacoes">
			<a href="<?php echo INCLUDE_PATH_PAINEL ?>fechar-pedido?fechar&codigo_pedido=<?php
			echo htmlentities($dadosBasicos->getCodigoPedido())
		?>" class="operacao">Fechar pedido <i class="fa fa-thumbs-up"></i></a>
	</div>
</div>
