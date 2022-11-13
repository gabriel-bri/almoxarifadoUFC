<?php  
	verificaPermissaoPagina(1);
?>

<?php
    function redirect() {
	echo "<meta http-equiv='refresh' content='5;url=" . INCLUDE_PATH_PAINEL . "/novos-emprestimos'>";
    }
?>

<?php
	if(isset($_GET['codigo_pedido']) && strlen($_GET['codigo_pedido']) == 20) {
		$codigoPedido = filter_var($_GET['codigo_pedido'], FILTER_SANITIZE_STRING);

		if($_SESSION['acesso'] == 2) {
			$dadosBasicos = Pedido::retornaDadosBasicosPedidoPendente($codigoPedido);
		}

		if($_SESSION['acesso'] == 1) {
			$dadosBasicos = Pedido::retornaDadosBasicosPedidoAtivoUsuario($codigoPedido);
		}

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
	if(isset($_GET['rejeitar']) && isset($_GET['codigo_pedido']) && $_GET['codigo_pedido'] == $dadosBasicos['codigo_pedido'] && $_SESSION['acesso'] == 2) {
		Pedido::mudarStatusPedido(htmlentities($dadosBasicos['codigo_pedido']), 0, 1, $dataConvertida, htmlentities($dadosBasicos['nome']), htmlentities($dadosBasicos['sobrenome']), htmlentities($dadosBasicos['email']));
		Painel::alert("sucesso", "O pedido foi rejeitado, o usuário será notificado. Redirecionando.");
		redirect();
	}

	if(isset($_GET['aprovar']) && isset($_GET['codigo_pedido']) && $_GET['codigo_pedido'] == $dadosBasicos['codigo_pedido'] && $_SESSION['acesso'] == 2) {

		$nomeCompleto = htmlentities($dadosBasicos['nome'] . ' ' . $dadosBasicos['sobrenome']);
		$dadosPDF = "
		    <p>Pedido feito por: {$nomeCompleto}</p>
		    <p>Data do pedido {$dataConvertida}</p>
	    		<div class='wraper-table'>
	        		<table>
	            	<tr>
	                	<td>Item</td>
		                <td>Quantidade</td>
		                <td>Tipo</td>
		        </tr>
    		";
		
		$detalhesPedido = Pedido::retornaPedidoPeloCodigo($codigoPedido);
        	while($dadosPedidos = $detalhesPedido->fetch(PDO::FETCH_ASSOC)) {
	        	$nomeItem = htmlentities($dadosPedidos['nome_estoque']);
	        	$quantidadeItem = htmlentities($dadosPedidos['quantidade_item']);
	        	$tipoEstoque = htmlentities(tipoEstoque($dadosPedidos['tipo']));
	        	$dadosPDF .= "<tr>
		                <td>{$nomeItem}</td>
		                <td>{$quantidadeItem}</td>
		                <td>{$tipoEstoque}</td>
		            	</tr>
            		";
        	}

    		$dadosPDF.= "</table></div>";
		Pedido::mudarStatusPedido(htmlentities($dadosBasicos['codigo_pedido']), 1, 0, $dataConvertida, $dadosBasicos['nome'], htmlentities($dadosBasicos['sobrenome']), htmlentities($dadosBasicos['email']), $dadosPDF);
		Painel::alert("sucesso", "O pedido foi aprovado, o usuário será notificado. Redirecionando.");
		Painel::deleteComprovante($codigoPedido);
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

	<?php if($_SESSION['acesso'] == 2) { ?>
	<div class="box-operacoes">
		<a href="<?php echo INCLUDE_PATH_PAINEL ?>visualizar-pedido?rejeitar&codigo_pedido=<?php 
			echo htmlentities($dadosBasicos['codigo_pedido'])
		?>" class="operacao">Rejeitar pedido <i class="fa fa-times"></i></a>

		<a href="<?php echo INCLUDE_PATH_PAINEL ?>visualizar-pedido?aprovar&codigo_pedido=<?php 
			echo htmlentities($dadosBasicos['codigo_pedido'])
		?>" class="operacao">Concluir pedido <i class="fa fa-thumbs-up"></i></a>
	</div>

	<?php } ?>

</div>