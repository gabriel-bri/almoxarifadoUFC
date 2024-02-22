<?php  
	verificaPermissaoPagina(3);
?>

<?php 
	$itensEstoqueTipo = Estoque::retornaPeloTipoJson();
    $itensStatus = Estoque::retornaStatusItensJson();
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="<?php echo INCLUDE_PATH_PAINEL?>js/graficos.js"></script>

<div class="box-content">
	<h2><i class="fas fa-table"></i> Gráficos de Estoque</h2>

    <div class="graficos">
	   <canvas id="estoque-tipo"></canvas>
  	   <button id="export-estoque-tipo" class="botao-export">Exportar Gráfico</button>
	</div>

	<div class="graficos">
	   <canvas id="estoque-status"></canvas>
  	   <button id="export-estoque-status" class="botao-export">Exportar Gráfico</button>
	</div>

	<script>
		document.addEventListener('DOMContentLoaded', function() {
			// Dados estoque (você precisa definir esses dados aqui)
			let itemEstoqueTipo = <?php echo $itensEstoqueTipo; ?>;
			// Chame a função para criar o gráfico de estoque
			criarGraficoEstoque(itemEstoqueTipo);

            // Status do Estoque (você precisa definir esses dados aqui)
			let itemStatus = <?php echo $itensStatus; ?>;
			// Chame a função para criar o gráfico de status do estoque
			statusItem(itemStatus);
		});
	</script>
</div>