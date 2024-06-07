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
			$dataConvertida = htmlentities($dadosBasicos->getDataPedido());
            $dataConvertida = implode("/",array_reverse(explode("-",$dataConvertida)));
            echo $dataConvertida;  
        ?>
		
		<?php
			if($dadosBasicos->getDataFinalizado() != NULL) {
				$dataConvertida = htmlentities($dadosBasicos->getDataFinalizado());
				$dataConvertida = implode("/",array_reverse(explode("-",$dataConvertida)));
				echo "e finalizado em " . $dataConvertida;
			}  
        ?>
    </h3>
<?php
	if(($_SESSION['acesso'] == 2 || $_SESSION['acesso'] == 3) && $dadosBasicos->getFinalizado() == 0) {
		if(!isset($_SESSION['feedback'])) {
			$_SESSION['feedback'] = 'Nenhum comentário sobre o pedido foi passado.';
		}
	}

	if (isset($_POST['salvar']) && $dadosBasicos->getFinalizado() == 0 && ($_SESSION['acesso'] == 2 || $_SESSION['acesso'] == 3)) {
		if($_POST['feedback'] != '') {
			Painel::alert("sucesso", "Seu feedback foi salvo");
			$_SESSION['feedback']  = $_POST['feedback'];
		}
	}

	if(isset($_GET['rejeitar']) && isset($_GET['codigo_pedido']) && $_GET['codigo_pedido'] == $dadosBasicos->getCodigoPedido() && $dadosBasicos->getFinalizado() == 0 && ($_SESSION['acesso'] == 2 || $_SESSION['acesso'] == 3)) {
		$dadosBasicos->setAprovado(0);
		$dadosBasicos->setFinalizado(1);
		PedidoDetalhes::mudarStatusPedido($dadosBasicos, $_SESSION['feedback']);		
		redirect();
	}


	if(isset($_GET['aprovar']) && isset($_GET['codigo_pedido']) && $_GET['codigo_pedido'] == $dadosBasicos->getCodigoPedido() && $dadosBasicos->getFinalizado() == 0 && ($_SESSION['acesso'] == 2 || $_SESSION['acesso'] == 3)){
		$dadosBasicos->setAprovado(1);
		$dadosBasicos->setFinalizado(0);
		PedidoDetalhes::mudarStatusPedido($dadosBasicos, $_SESSION['feedback']);
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

	<?php if(($_SESSION['acesso'] == 2 || $_SESSION['acesso'] == 3) && $dadosBasicos->getFinalizado() == 0) { ?>

	<form method="post" action="" class="feedback">
		<div class="form-group">
			<label for="">Feedback do pedido:</label>
			<input type="text" name="feedback" placeholder="Seu feedback aqui..." value="<?php echo isset($_POST['feedback']) ? htmlentities($_POST['feedback']) : '';?>">
		</div>

		<div class="form-group-login left">
			<input type="submit" name="salvar" value="Salvar">
		</div>
		
	</form>
	<div class="box-operacoes">
		<a href="<?php echo INCLUDE_PATH_PAINEL ?>movimentar-pedido?rejeitar&codigo_pedido=<?php 
			echo htmlentities($dadosBasicos->getCodigoPedido())
		?>" class="operacao">Rejeitar pedido <i class="fa fa-times"></i></a>

		<a href="<?php echo INCLUDE_PATH_PAINEL ?>movimentar-pedido?aprovar&codigo_pedido=<?php 
			echo htmlentities($dadosBasicos->getCodigoPedido())
		?>" class="operacao">Aprovar pedido <i class="fa fa-thumbs-up"></i></a>
	</div>
	<?php } ?>

</div>