<?php  
	verificaPermissaoPagina(2);
?>

<div class="box-content">
	<h2> <i class="fas fa-plus-circle"></i> Adicionar Estoque</h2>

	<form method="post">
		<?php 
			if(isset($_POST['acao'])) {
				Estoque::validarEntradasCadastro($_POST);
			}
		?>
		<div class="form-group">
			<label for="nome">Nome do Item:</label>
			<input type="text" name="nome" placeholder="Nome do componente/equipamento" id="nome">
		</div>


		<div class="form-group">
			<label for="quantidade">Quantidade:</label>
			<input type="number" name="quantidade" min="1" value="1" id="quantidade" placeholder="Quantidade"></input>
		</div>

		<div class="form-group">
			<label for="tipo">Tipo:</label>
			<select name="tipo" id="tipo">
				<?php 
					foreach (Estoque::$estoque as $key => $value) {
						echo "$key | $value <br>";
						echo "<option value='$key'>$value</option>";
					}
				?>
			</select>
		</div>

		<div class="form-group">
			<input type="submit" value="Cadastrar" name="acao">
		</div>
	</form>
</div>