<?php  
	verificaPermissaoPagina(3);
?>

<?php 
	$pedidosAnoAtual = PedidoDetalhes::retornaQuantidadePedidosPorMesAnoAtual();
	$pedidosPorAno = PedidoDetalhes::retornaQuantidadePedidosAnos();
	$maisPedidoPorMesAnoAtual = PedidoDetalhes::retornaQuantidadeMaisPedidoPorMesAnoAtual();
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="<?php echo INCLUDE_PATH_PAINEL?>js/graficos.js"></script>

<div class="box-content">
	<h2><i class="fas fa-table"></i> Gráficos de Pedidos</h2>

    <div class="graficos">
	   <canvas id="pedidos-ano-atual"></canvas>
  	   <button id="export-pedidos-ano-atual" class="botao-export">Exportar Gráfico</button>
	</div>

    <div class="graficos">
	   <canvas id="pedidos-por-ano"></canvas>
  	   <button id="export-pedidos-por-ano" class="botao-export">Exportar Gráfico</button>
	</div>

	<div class="graficos">
	   <canvas id="pedidos-por-mes-ano-atual"></canvas>
  	   <button id="export-pedidos-por-mes-ano-atual" class="botao-export">Exportar Gráfico</button>
	</div>
  	<!-- Chame a função pedidosAnoAtual e passe os dados PHP como argumento -->
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			// Dados dos pedidos do ano atual (você precisa definir esses dados aqui)
			let dadosPedidosAnoAtual = <?php echo $pedidosAnoAtual; ?>;
			// Chame a função para criar o gráfico de pedidos do ano atual
			pedidosAnoAtual(dadosPedidosAnoAtual);

			// Dados de pedidos por ano (você precisa definir esses dados aqui)
			let dadosPedidosPorAno = <?php echo $pedidosPorAno; ?>;

			// Chame a função para criar o gráfico de pedidos por ano
			pedidosPorAno(dadosPedidosPorAno);

			// Dados de pedidos por mês (você precisa definir esses dados aqui)
			let mes = <?php echo $maisPedidoPorMesAnoAtual; ?>;

			// Chame a função para criar o gráfico de pedidos por mês
			pedidosPorMes(mes);
		});
	</script>
</div>