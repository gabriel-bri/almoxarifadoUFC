<?php  
	verificaPermissaoPagina(2);
?>

<?php
    function redirect() {
	echo "<meta http-equiv='refresh' content='5;url=" . INCLUDE_PATH_PAINEL . "/nao-finalizados'>";
    }
?>

<?php
	if(isset($_GET['codigo_pedido']) && strlen($_GET['codigo_pedido']) == 20) {
		$codigoPedido = filter_var($_GET['codigo_pedido'], FILTER_SANITIZE_STRING);
		$dadosBasicos = Pedido::retornaDadosBasicosPedidoNaoFinalizados($codigoPedido);

		if($dadosBasicos != true) {
			Painel::alert("erro", "Código do pedido não encontrado ou pedido já revisado.");
			die();			
		}
	}

	else {
		Painel::alert("erro", "Você precisa passar o código do pedido");
		die();
	}
?>
<div class="box-content">

	<h2><i class="fa fa-pencil-alt"></i> Detalhes do pedido: 
		<?php 
			echo htmlentities($dadosBasicos['codigo_pedido'])
		?> 
	</h2>

	<h3>Pedido feito por <?php echo htmlentities($dadosBasicos['nome'] . " " . $dadosBasicos['sobrenome'])?> em 
		<?php
			$dataConvertida = htmlentities($dadosBasicos['data_pedido']);
            $dataConvertida = implode("/",array_reverse(explode("-",$dataConvertida)));
            echo $dataConvertida;  
        ?>	
    </h3>
<?php  
	if(isset($_GET['fechar']) && isset($_GET['codigo_pedido']) && $_GET['codigo_pedido'] == $dadosBasicos['codigo_pedido']) {
		Pedido::marcarComoFinalizado(htmlentities($dadosBasicos['codigo_pedido']), 1, $dataConvertida, htmlentities($dadosBasicos['nome']), htmlentities($dadosBasicos['sobrenome']), htmlentities($dadosBasicos['email']));
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
        		$detalhesPedido = Pedido::retornaPedidoPeloCodigo($_GET['codigo_pedido']);
        		while($dadosPedidos = $detalhesPedido->fetch(PDO::FETCH_ASSOC)) {
			?>

			<tr>
				<td><?php echo htmlentities($dadosPedidos['nome_estoque']); ?></td>

				<td><?php echo htmlentities($dadosPedidos['quantidade_item']); ?></td>
				<td><?php echo htmlentities(tipoEstoque($dadosPedidos['tipo'])); ?></td>
			</tr>
			<?php } ?>
		</table>
	</div>

	<div class="box-operacoes">
			<a href="<?php echo INCLUDE_PATH_PAINEL ?>fechar-pedido?fechar&codigo_pedido=<?php 
			echo htmlentities($dadosBasicos['codigo_pedido'])
		?>" class="operacao">Fechar pedido <i class="fa fa-thumbs-up"></i></a>
	</div>
</div>