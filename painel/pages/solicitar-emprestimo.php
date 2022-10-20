<?php  
	verificaPermissaoPagina(1);
?>

<?php
	$secret = "teste";
?>

<?php
	$estoque = Estoque::selectAll();

	$_SESSION['a'];
	if(isset($_POST['adicionar'])) {
		var_dump($_POST);
		var_dump($_SESSION['a']);
		array_push($_SESSION['a'], $_POST);
		//$_SESSION['a'] = "";
		var_dump($_SESSION['a']);
		// session_unset($_SESSION['a']);
	}
?>

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
		<p>Status atual do carrinho: VAZIO</p>
	</div>
</div>