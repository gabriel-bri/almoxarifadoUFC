<?php
	verificaPermissaoPagina(2);
?>

<?php
    function redirect() {
	echo "<meta http-equiv='refresh' content='5;url=" . INCLUDE_PATH_PAINEL . "novos-emprestimos'>";
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
	$dadosBasicos = PedidoDetalhes::retornaDadosPedidoViaCodigoNovoEmprestimo($codigoPedido);

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

        <?php if($dadosBasicos->getAprovado() == 1): ?>
            <h3>Aprovado por:
                <?php
                $nomeAprovador = Usuario::getNomeCompletoById($dadosBasicos->getIdUsuarioAprovou());
                echo htmlentities($nomeAprovador ? $nomeAprovador : "Sem informação de aprovação");
                ?>
            </h3>
        <?php endif; ?>

        <?php if ($dadosBasicos->getFinalizado() == 1): ?>
            <h3>Finalizado por:
                <?php
                $nomeFinalizador = Usuario::getNomeCompletoById($dadosBasicos->getIdUsuarioFinalizou());
                echo htmlentities($nomeFinalizador ? $nomeFinalizador : "Sem informação de finalização");
                ?>
            </h3>
        <?php endif; ?>
    </h3>
<?php
    if (isset($_POST['feedback'])) {
        $feedback = filter_var($_POST['feedback'], FILTER_SANITIZE_STRING);
        $emprestimoEspecial = isset($_POST['emprestimo_especial']) ? 1 : 0;

        $dadosBasicos->setFeedback($feedback);
        $dadosBasicos->setEmprestimoEspecial($emprestimoEspecial);

        if (isset($_POST['rejeitar'])) {
            $dadosBasicos->setAprovado(0);
            $dadosBasicos->setFinalizado(1);
            PedidoDetalhes::mudarStatusPedido($dadosBasicos, $feedback, $_SESSION['id']);
            redirect();
        }

        if (isset($_POST['aprovar'])) {
            $dadosBasicos->setAprovado(1);
            $dadosBasicos->setFinalizado(0);
            PedidoDetalhes::mudarStatusPedido($dadosBasicos, $feedback, $_SESSION['id']);
            redirect();
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

    <?php if ($dadosBasicos->getAprovado() == 1): ?>
        <?php if ($dadosBasicos->getEmprestimoEspecial()) : ?>
            <h3 style="color: red;">Este é um Empréstimo Especial.</h3>
        <?php endif; ?>

        <?php if (!empty($dadosBasicos->getFeedback())) : ?>
            <h3>Feedback do Pedido:</h3>
            <p><?php echo nl2br(htmlentities($dadosBasicos->getFeedback())); ?></p>
        <?php endif; ?>
    <?php endif; ?>

	<?php if(($_SESSION['acesso'] == 2 || $_SESSION['acesso'] == 3) && $dadosBasicos->getAprovado() == 0 && $dadosBasicos->getFinalizado() == 0) { ?>

        <form method="post" action="" class="feedback">
            <div class="form-group">
                <label for="">Feedback do pedido:</label>
                <textarea name="feedback" placeholder="Seu feedback aqui..." rows="4" cols="50"><?php echo isset($_POST['feedback']) ? htmlentities($_POST['feedback']) : '';?></textarea>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="emprestimo_especial" value="1" <?php echo $dadosBasicos->getEmprestimoEspecial() ? 'checked' : ''; ?>>
                    Empréstimo Especial
                </label>
            </div>

            <div class="box-operacoes" style="display: flex; justify-content: space-between; gap: 10px; padding: 10px 0;">
                <button type="submit" name="rejeitar" style="background-color: #E05C4E; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; flex: 1;">
                    Rejeitar pedido <i class="fa fa-times"></i>
                </button>
                <button type="submit" name="aprovar" style="background-color: #50fa7b; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; flex: 1;">
                    Aprovar pedido <i class="fa fa-thumbs-up"></i>
                </button>
            </div>
        </form>
    <?php } ?>

</div>