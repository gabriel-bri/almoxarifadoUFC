<?php  
	verificaPermissaoPagina(1);
?>

<div class="box-content">
	<h2> <i class="fa fa-shopping-cart"></i>Editar carrinho</h2>
	<?php

		if(isset($_SESSION['carrinho'])) {
			echo "oi";
			var_dump($_SESSION['carrinho']);
		}

		else {
			Painel::alert("erro", "O seu carrinho está vazio");
			die();
		}

		// if(isset($_POST['adicionar'])) {

		// 	if(!isset($_SESSION['carrinho'])){
	    //      	$_SESSION['carrinho'] = array();
	    // 	}

		// 	$idProduto = $_POST['id_produto'];
		// 	$qtdProduto = $_POST['qtd_' . $secret . "_" . $idProduto];
		// 	$pedido = array("id" => $idProduto, "quantidade" => $qtdProduto);

		// 	array_push($_SESSION['carrinho'], $pedido);
		// 	Painel::alert("sucesso", "O item foi dicionado ao seu carrinho.");
		// }

		// if(isset($_GET['limpar'])) {
		// 	unset($_SESSION['carrinho']);
		// 	echo "<script>window.history.pushState('solicitar-emprestimo', 'Title', 'solicitar-emprestimo');</script>";
		// 	Painel::alert("sucesso", "O seu carrinho foi limpo.");	
		// }

		// if(isset($_GET['concluir'])) {
		// 	if(isset($_SESSION['carrinho'])) {
		// 		foreach ($_SESSION['carrinho'] as $chave => $row){
		//  			$idProduto =  $row['id'];
		//  			$quantidade = $row['quantidade'];
		//  			$codigo = "Ab301201020303";
		//  			Pedido::cadastrarPedido($quantidade, $_SESSION['id'], $idProduto, $codigo);
	 	// 		}
	 	// 		echo "<script>window.history.pushState('solicitar-emprestimo', 'Title', 'solicitar-emprestimo');</script>";
	 	// 		Painel::alert("sucesso", "O seu pedido foi realizado e você recebeu por e-mail uma notificação.");
		// 		unset($_SESSION['carrinho']);
		// 	}

		// 	else {
		// 		echo "<script>window.history.pushState('solicitar-emprestimo', 'Title', 'solicitar-emprestimo');</script>";
		// 		Painel::alert("erro", "O seu carrinho está vazio.");
		// 	}
		// }
	?>

	<div class="wraper-table">
		<table>
			<tr>
				<td>Nome</td>
				<td>Quantidade</td>
				<td>#</td>
				<td>#</td>
			</tr>
			<?php
				foreach ($_SESSION['carrinho'] as $chave => $row){
					$id = (int)$row['id'];
					$estoque = Estoque::select('id = ?', array($id));
					echo htmlentities($estoque['nome']);		
			?>

			<tr>
				<td><?php echo htmlentities($row['id']); ?></td>
				
				<td><?php echo htmlentities($row['quantidade']); ?></td>

				<form method="post">

					<td><input type="submit" name="Atualizar" value="Atualizar" class="cart"></td>
				</form>

				<td><a actionBtn="delete" href="<?php echo INCLUDE_PATH_PAINEL ?>editar-emprestimo?excluir=<?php echo htmlentities($chave); ?>" class="btn delete">Excluir <i class="fa fa-times"></i></a></td>

			</tr>
			<?php } ?>
		</table>
	</div>

	<div class="box-usuario">
		<?php
			echo "<p>Status atual do carrinho: " . Pedido::statusCarrinho() . "</p>";
		?>
	</div>
	<div class="box-operacoes">
		<a href="<?php echo INCLUDE_PATH_PAINEL ?>solicitar-emprestimo?limpar" class="operacao">Limpar carrinho <i class="fa fa-times"></i></a>

		<a href="<?php echo INCLUDE_PATH_PAINEL ?>solicitar-emprestimo?concluir" class="operacao">Concluir pedido <i class="fa fa-thumbs-up"></i></a>

		<a href="<?php echo INCLUDE_PATH_PAINEL ?>solicitar-emprestimo?editar" class="operacao">Editar/ver carrinho <i class="fa fa-pencil-alt"></i></a>
	</div>
</div>