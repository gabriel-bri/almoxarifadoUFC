<?php  
	verificaPermissaoPagina(1);
?>
<div class="box-content">
	<h2> <i class="fa fa-shopping-cart"></i>Editar carrinho</h2>
	<?php
		if(!isset($_SESSION['carrinho']) || count($_SESSION['carrinho']) == 0) {
			Painel::alert("erro", "O seu carrinho está vazio");
			die();
		}

		if(isset($_GET['excluir'])) {
			$idArray = (int) $_GET['excluir'];
			Carrinho::removerProduto($idArray);
		}

		if (isset($_POST['atualizar'])) {
			Carrinho::atualizar($_POST);
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
			
				foreach ($_SESSION['carrinho'] as $idProduto => $quantidade){
					(int) $idProduto;
					$estoque = Estoque::estoqueDisponivelDetalhes($idProduto);		
			?>

			<tr>
				<td><?php echo htmlentities($estoque->getNome()); ?></td>
			
				<td><?php echo htmlentities($estoque->getQuantidade());?></td>

				<form method="post">

					<td><input type="number" name="<?php echo "qtd_" . $_SESSION['secret'] . "_" . htmlentities($idProduto) ?>" value="<?php echo htmlentities($quantidade); ?>"></td>

					<td><input type="submit" name="atualizar" value="Atualizar" class="cart"></td>

					<input type="hidden" name="id_produto" value="<?php echo htmlentities($idProduto) ?>">
				</form>

				<td><a actionBtn="apagarCarrinho" href="<?php echo INCLUDE_PATH_PAINEL ?>editar-emprestimo?excluir=<?php echo htmlentities($idProduto); ?>" class="btn delete">Excluir <i class="fa fa-times"></i></a></td>

			</tr>
			<?php } ?>
		</table>
	</div>
	<div class="box-usuario">
		<?php
			echo "<p>Status atual do carrinho: " . Carrinho::statusCarrinho() . "</p>";
		?>
	</div>
	<div class="box-operacoes">
		<a href="<?php echo INCLUDE_PATH_PAINEL ?>solicitar-emprestimo?limpar" class="operacao">Limpar carrinho <i class="fa fa-times"></i></a>

		<a href="<?php echo INCLUDE_PATH_PAINEL ?>solicitar-emprestimo?concluir" class="operacao">Concluir pedido <i class="fa fa-thumbs-up"></i></a>

		<a href="<?php echo INCLUDE_PATH_PAINEL ?>solicitar-emprestimo?editar" class="operacao">Editar/ver carrinho <i class="fa fa-pencil-alt"></i></a>
	</div>
</div>