<?php  
	verificaPermissaoPagina(1);
?>

<?php
	$secret = "teste";
?>

<div class="box-content">
	<h2> <i class="fa fa-shopping-cart"></i>Solicitar Empréstimo</h2>
	<?php
		$estoque = Estoque::selectAll();
		// var_dump($_SESSION['carrinho']);
		// array_splice($_SESSION['carrinho'], 1, 1);
		// var_dump($_SESSION['carrinho']);
		
	 	// foreach ($_SESSION['carrinho'] as $chave => $row){
	 	// 	// echo $row['qtd_'.$secret."_1"];
	 	// 	$id =  $row['id_produto'];
	 	// 	// var_dump($row);
	 	// 	// echo $row['qtd_' . $secret . '_' . $id];
	 	// 	$codigo = "Ab301201020303";
	 	// 	Pedido::cadastrarPedido($row['qtd_' . $secret . '_' . $id], $_SESSION['id'], $id, $codigo);
	 	// }
		// var_dump($_SESSION['carrinho']);
		if(isset($_POST['adicionar'])) {
			if(!isset($_POST['id_produto']) || !isset($_POST['qtd_' . $secret . "_" . $_POST['id_produto']])) {
				Painel::alert("erro", "Algum parâmetro está ausente.");
			}

			else {
				$idProduto = (int) filter_var($_POST['id_produto'], FILTER_SANITIZE_NUMBER_INT);
				$qtdProduto = filter_var($_POST['qtd_' . $secret . "_" . $idProduto], FILTER_SANITIZE_STRING);

				if($qtdProduto <= 0) {
					Painel::alert("erro", "Quantidade deve ser igual ou maior que 1");
				}
				
				else {		
					if(!isset($_SESSION['carrinho'])) {
						$_SESSION['carrinho'] = array();
					}
	
					$pedido = array("id" => $idProduto, "quantidade" => $qtdProduto);
	
					array_push($_SESSION['carrinho'], $pedido);
					Painel::alert("sucesso", "O item foi dicionado ao seu carrinho.");	
				}
			}

		}

		if(isset($_GET['limpar'])) {
			unset($_SESSION['carrinho']);
			echo "<script>window.history.pushState('solicitar-emprestimo', 'Title', 'solicitar-emprestimo');</script>";
			Painel::alert("sucesso", "O seu carrinho foi limpo.");	
		}

		if(isset($_GET['concluir'])) {
			if(isset($_SESSION['carrinho']) AND count($_SESSION['carrinho']) > 0) {
				foreach ($_SESSION['carrinho'] as $chave => $row){
		 			$idProduto =  $row['id'];
		 			$quantidade = $row['quantidade'];
		 			$codigo = "Ab301201020303";
		 			Pedido::cadastrarPedido($quantidade, $_SESSION['id'], $idProduto, $codigo);
	 			}
	 			echo "<script>window.history.pushState('solicitar-emprestimo', 'Title', 'solicitar-emprestimo');</script>";
	 			Painel::alert("sucesso", "O seu pedido foi realizado e você recebeu por e-mail uma notificação.");
				unset($_SESSION['carrinho']);
			}

			else {
				echo "<script>window.history.pushState('solicitar-emprestimo', 'Title', 'solicitar-emprestimo');</script>";
				Painel::alert("erro", "O seu carrinho está vazio.");
			}
		}
	?>

	<div class="wraper-table">
		<table>
			<tr>
				<td>Nome</td>
				<td>Disponível</td>
				<td>Tipo</td>
				<td>Quantidade</td>
				<td>#</td>
			</tr>
			<?php
				foreach ($estoque as $key => $value) {
				$quantidadeDisponivel = Estoque::estoqueDisponivel(htmlentities($value['id']));
			?>

			<tr>
				<td><?php echo htmlentities($value['nome']); ?></td>

				<td><?php
						//Caso o id do produto não esteja na tabela de pedidos mostra a sua quantidade original.
				 		if(is_null($quantidadeDisponivel[0])) {
				 			echo htmlentities($value['quantidade']);
				 		}

				 		else {
				 			if($quantidadeDisponivel[0] < 0) {
								echo "trabalhar aqui";
							}

							else {
								echo htmlentities($quantidadeDisponivel[0]);
							}
				 		}
				 	?></td>

				<td><?php echo tipoEstoque(htmlentities($value['tipo'])); ?></td>
				<form method="post">

					<td><input type="number" name="<?php echo "qtd_" . $secret . "_" . htmlentities($value['id']) ?>"></td>

					<input type="hidden" name="id_produto" value="<?php echo htmlentities($value['id']) ?>">

					<td><input type="submit" name="adicionar" value="Adicionar" class="cart"></td>
				</form>
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

		<a href="<?php echo INCLUDE_PATH_PAINEL ?>editar-emprestimo" class="operacao">Editar/ver carrinho <i class="fa fa-pencil-alt"></i></a>
	</div>
</div>