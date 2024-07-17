<?php  
	verificaPermissaoPagina(3);
?>

<div class="box-content">
	<h2> <i class="fa fa-file-pdf"></i> Gerar Relatório de Pedidos</h2>

	<form method="post">
		<?php 
			if(isset($_POST['acao'])) {
				$relatorioPedidos = new RelatorioPedidos();
				$relatorioPedidos->validarRelatorio(1, $_POST);
	        }
		?>

		<?php 
			if(isset($_POST['acao-tudo'])) {
				$relatorioPedidos = new RelatorioPedidos();
				$relatorioPedidos->validarRelatorio(2, $_POST);
	   		}
		?>

		<?php 
			if(isset($_POST['acao-ativos'])) {
				$relatorioPedidos = new RelatorioPedidos();
				$relatorioPedidos->validarRelatorio(3, $_POST);
	   		}
		?>
		<div class="form-group">
			<label for="dataInicial">Escolha a data inicial:</label>
			<input type="date" name="dataInicial" value="<?php echo date("Y-m-d") ?>" id="dataInicial">
		</div>

		<div class="form-group">
			<label for="dataFinal">Escolha a data final:</label>
			<input type="date" name="dataFinal" value="<?php echo date("Y-m-d") ?>" id="dataFinal">
		</div>

		<div class="form-group">
			<input type="submit" value="Gerar relatório" name="acao" class="botao-maior">
		</div>

		<div class="form-group">
			<input type="submit" value="Todas as datas" name="acao-tudo" class="botao-maior">
		</div>

		<div class="form-group">
			<input type="submit" value="Pedidos ativos" name="acao-ativos" class="botao-maior">
		</div>
	</form>
</div>