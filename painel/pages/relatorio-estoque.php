<?php  
	verificaPermissaoPagina(3);
?>

<div class="box-content">
	<h2> <i class="fa fa-file-pdf"></i> Gerar Relatório do Estoque</h2>

	<form method="post">
		<?php 
			if(isset($_POST['acao'])) {
				$tipo = (int) filter_var($_POST["tipo"], FILTER_SANITIZE_NUMBER_INT);
				$relatorio = new RelatorioEstoque();
				$relatorio->validarRelatorio($tipo);
	        }
		?>
		<div class="form-group">
			<label for="tipo">Escolha do que fazer o relatório:</label>
			<select name="tipo" id="tipo">
				<option value="3">Tudo</option>
				<?php 
					foreach (Estoque::$estoque as $key => $value) {
						echo "<option value='$key'>$value</option>";
					}
				?>
			</select>
		</div>

		<div class="form-group">
			<input type="submit" value="Gerar relatório" name="acao" class="botao-maior">
		</div>
	</form>
</div>