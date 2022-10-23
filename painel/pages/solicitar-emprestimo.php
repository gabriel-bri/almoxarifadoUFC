<?php  
	verificaPermissaoPagina(1);
?>

<?php
	$secret = "teste";
?>

<?php
	$estoque = Estoque::selectAll();
	// var_dump($_SESSION['carrinho']);
	// array_splice($_SESSION['carrinho'], 1, 1);
	// var_dump($_SESSION['carrinho']);
	
	if(!isset($_SESSION['carrinho'])){
         $_SESSION['carrinho'] = array();
    }

	if(isset($_POST['adicionar'])) {
			$_SESSION['carrinho'];
			array_push($_SESSION['carrinho'], $_POST);
		// var_dump($_POST);
		// var_dump($_SESSION['carrinho']);
		Painel::alert("sucesso", "O item foi dicionado ao seu carrinho.");
		var_dump($_SESSION['carrinho']);
	}

	if(isset($_GET['limpar'])) {
		unset($_SESSION['carrinho']);
		var_dump($_SESSION);
	}
?>

<style type="text/css">

</style>
<div class="box-content">
	<h2> <i class="fa fa-shopping-cart"></i>Solicitar Empréstimo</h2>
	
	<div class="wraper-table">
		<table>
			<tr>
				<td>Nome</td>
				<td>Estoque</td>
				<td>Tipo</td>
				<td>Quantidade</td>
				<td>#</td>
			</tr>
			<?php
				foreach ($estoque as $key => $value) {
			?>

			<tr>
				<td><?php echo htmlentities($value['nome']); ?></td>

				<td><?php echo htmlentities($value['quantidade']); ?></td>

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

		<a href="<?php echo INCLUDE_PATH_PAINEL ?>solicitar-emprestimo?limpar" class="operacao">Concluir pedido <i class="fa fa-thumbs-up"></i></a>

		<a href="<?php echo INCLUDE_PATH_PAINEL ?>solicitar-emprestimo?limpar" class="operacao">Editar pedido <i class="fa fa-pencil-alt"></i></a>
	</div>
</div>