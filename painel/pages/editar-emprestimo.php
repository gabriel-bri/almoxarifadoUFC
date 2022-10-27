<?php  
	verificaPermissaoPagina(1);
?>

<?php
	$secret = "teste";
?>

<div class="box-content">
	<h2> <i class="fa fa-shopping-cart"></i>Editar carrinho</h2>
	<?php

		if(isset($_SESSION['carrinho'])) {
			if(isset($_GET['excluir'])) {
				$idArray = (int) $_GET['excluir'];
				array_splice($_SESSION['carrinho'], $idArray, 1);
				echo "<script>window.history.pushState('solicitar-emprestimo', 'Title', 'editar-emprestimo');</script>";
				Painel::alert("sucesso", "O item foi excluído do seu carrinho.");
			}

			if (isset($_POST['atualizar'])) {
				$idProdutoArray = $_POST['id_produto'];
				$idProduto = $_SESSION['carrinho'][$idProdutoArray]['id'];
				$qtdProduto = $_POST['qtd_' . $secret . "_" . $idProduto];
				$_SESSION['carrinho'][$idProdutoArray]['quantidade'] = $qtdProduto;
				Painel::alert("sucesso", "O seu carrinho foi atualizado.");
			}
		}

		else {
			Painel::alert("erro", "O seu carrinho está vazio");
			die();
		}
	?>

	<div class="wraper-table">
		<table>
			<tr>
				<td>Nome</td>
				<td>Quantidade</td>
				<td>#</td>
				<td>#</td>
				<td>#</td>
			</tr>
			<?php
				foreach ($_SESSION['carrinho'] as $chave => $row){
					$id = (int)$row['id'];
					$estoque = Estoque::select('id = ?', array($id));		
			?>

			<tr>
				<td><?php echo htmlentities($estoque['nome']); ?></td>
			
				<td><?php echo htmlentities($row['quantidade']); ?></td>

				<form method="post">

					<td><input type="number" name="<?php echo "qtd_" . $secret . "_" . htmlentities($row['id']) ?>" min=1 value="<?php echo htmlentities($row['quantidade']); ?>"></td>

					<td><input type="submit" name="atualizar" value="Atualizar" class="cart"></td>

					<input type="hidden" name="id_produto" value="<?php echo htmlentities($chave) ?>">
				</form>

				<td><a actionBtn="apagarCarrinho" href="<?php echo INCLUDE_PATH_PAINEL ?>editar-emprestimo?excluir=<?php echo htmlentities($chave); ?>" class="btn delete">Excluir <i class="fa fa-times"></i></a></td>

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