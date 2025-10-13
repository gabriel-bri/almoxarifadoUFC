<?php
	verificaPermissaoPagina(1);
?>

<div class="box-content">
	<?php 
		if(isset($_POST['acao'])) {
			$acaoNadaConsta = (int) filter_var($_POST["tipo"], FILTER_SANITIZE_NUMBER_INT);
			NadaConsta::geraNadaConsta($acaoNadaConsta);
	    }
	?>
	<h2> <i class="fas fa-file-alt"></i> Nada Consta</h2>
	<p>Esta função consiste em validar se não existem pendências ou registros negativos associados ao seu perfil.</p>

	<form method="post">		
		<div class="form-group">
			<label for="tipo">Escolha o que fazer com a declaração:</label>
			<select name="tipo" id="tipo">
				<option value="1" selected>Mostrar na tela</option>
				<option value="2">Enviar por e-mail</option>
			</select>
		</div>

		<div class="form-group">
			<input type="submit" value="Gerar Nada Consta" name="acao" class="botao-maior">
		</div>
	</form>
</div>