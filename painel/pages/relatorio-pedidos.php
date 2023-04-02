<?php  
	verificaPermissaoPagina(2);
?>

<div class="box-content">
	<h2> <i class="fa fa-file-pdf"></i> Gerar Relatório de Pedidos</h2>

	<form method="post">
		<?php 
			if(isset($_POST['acao'])) {

				$dataHoje = date("Y-m-d");

				if($_POST['dataInicial'] == '' || $_POST['dataFinal'] == '') {
					Painel::alert("erro", "As datas não podem ser vazias");
				}

				else if($_POST['dataInicial'] > $_POST['dataFinal']){
					Painel::alert("erro", "A data incial não pode ser maior que a data final");	
				}

				else if($_POST['dataFinal'] < $_POST['dataInicial']){
					Painel::alert("erro", "A data final não pode ser menor que a data incial");	
				}

				else if($_POST['dataFinal'] > $dataHoje) {
					var_dump($_POST['dataFinal'] > $dataHoje);
					Painel::alert("erro", "A data final não pode ser maior que hoje");
				}

				else {
					$validaDataInicial = DateTime::createFromFormat('Y-m-d', $_POST['dataInicial']);
					$validaDataFinal = DateTime::createFromFormat('Y-m-d', $_POST['dataFinal']);

					if(($validaDataInicial && $validaDataInicial->format('Y-m-d') === $_POST['dataInicial']) && ($validaDataFinal && $validaDataFinal->format('Y-m-d') === $_POST['dataFinal'])){

						$dataInicial = $_POST['dataInicial'];
						$dataFinal = $_POST['dataFinal'];

	   					$pedidos = Pedido::retornaPedidosByData($dataInicial, $dataFinal);



	   					$dadosPedidos = $pedidos->fetch(PDO::FETCH_ASSOC);

	   					if($dadosPedidos == false) {
	   						Painel::alert("erro", "Nenhum registro foi encontrado para o período informado");
	   					}

	   					else {

		   					$dataHoje = implode("/",array_reverse(explode("-",$dataHoje)));

		   					$dadosPDF = "<p>Relatório gerado em: {$dataHoje}<br>";

		   					$dataInicial = implode("/",array_reverse(explode("-",htmlentities($dataInicial))));
		   					$dataFinal = implode("/",array_reverse(explode("-",htmlentities($dataFinal))));

		   					$dadosPDF .= "Mostrando resultados entre {$dataInicial} e {$dataFinal}</p>";

	   						$contador = 1;
	   						while($dadosPedidos = $pedidos->fetch(PDO::FETCH_ASSOC)) {
	   						// Dados do usuário
	   						$nome = htmlentities($dadosPedidos['nome']);
	   						$sobrenome = htmlentities($dadosPedidos['sobrenome']);
	   				   		$matricula = htmlentities($dadosPedidos['matricula']);

	   				   		// Dados Referentes ao pedido
	   				   		$dataPedido = implode("/",array_reverse(explode("-",htmlentities($dadosPedidos['data_pedido']))));
	   				   		$dataFinalizado = implode("/",array_reverse(explode("-",htmlentities($dadosPedidos['data_finalizado']))));
	   				   		$codigoPedido = htmlentities($dadosPedidos['codigo_pedido']);

	   						$dadosPDF .= "<div class='wraper-table'>
	   							<hr>
				        		<table>
				            	<tr>
				            		<br>
				                	<td>Nome:</td>
					                <td>Sobrenome:</td>
					                <td>Matrícula:</td>
					        	</tr>
		    				";
		    				$dadosPDF .= "<tr>
						        <td>{$nome}</td>
								<td>{$sobrenome}</td>
								<td>{$matricula}</td>
						        </tr>
			            	";

			            	$dadosPDF.= "</table></div>";

							$dadosPDF .= "<div class='wraper-table'>
				        		<table>
				            	<tr>
				            		<br>
				                	<td>Item:</td>
					                <td>Quantidade:</td>
					                <td>Tipo:</td>
					        	</tr>
		    				";

		    				$detalhesPedido = Pedido::retornaPedidoPeloCodigo($codigoPedido);

					        while($descricao = $detalhesPedido->fetch(PDO::FETCH_ASSOC)) {
						        $nomeItem = htmlentities($descricao['nome_estoque']);
						        $quantidadeItem = htmlentities($descricao['quantidade_item']);
						        $tipoEstoque = htmlentities(tipoEstoque($descricao['tipo']));
						        $dadosPDF .= "<tr>
							        <td>{$nomeItem}</td>
							        <td>{$quantidadeItem}</td>
							        <td>{$tipoEstoque}</td>
							        </tr>
					            	";
					        }

					        $dadosPDF .= "<br>Data pedido: {$dataPedido}<br>";
					        $dadosPDF .= "Data finalização: {$dataFinalizado}<br>";

					        $dadosPDF .= "Código do pedido: {$codigoPedido}";					        
					        $dadosPDF.= "</table></div>";
					        $dadosPDF .= "<br><br>";

					        $contador++;
	   					}

	   					$dadosPDF .= "<br>";

	   					$contador = $contador - 1;
	   					$dadosPDF .= "<p> Ao todo foram <strong>{$contador} pedidos</strong> ao longo do período.</p>";
		    			
		    			$gerarPDF = new Relatorio();
		                $gerarPDF->gerarPDF($dataHoje, $dadosPDF, 1);

	   					}
					}

					else {
						Painel::alert("erro", "Data inválida");
					}
				}
	        }
		?>

		<?php 
			if(isset($_POST['acao-tudo'])) {

				$dataHoje = date("Y-m-d");

	   			$pedidos = Pedido::retornaTodosPedidosConcluidos();


	   			$dadosPedidos = $pedidos->fetch(PDO::FETCH_ASSOC);

	   			if($dadosPedidos == false) {
	   				Painel::alert("erro", "Nenhum registro foi encontrado para o período informado");
	   			}

	   			else {
	   				$dataHoje = implode("/",array_reverse(explode("-",$dataHoje)));

		   			$dadosPDF = "<p>Relatório gerado em: {$dataHoje}<br>";

		   			$dadosPDF .= "Mostrando resultados de todo o período.</p>";

	   				$contador = 1;
	   				while($dadosPedidos = $pedidos->fetch(PDO::FETCH_ASSOC)) {
	   						// Dados do usuário
	   						$nome = htmlentities($dadosPedidos['nome']);
	   						$sobrenome = htmlentities($dadosPedidos['sobrenome']);
	   				   		$matricula = htmlentities($dadosPedidos['matricula']);

	   				   		// Dados Referentes ao pedido
	   				   		$dataPedido = implode("/",array_reverse(explode("-",htmlentities($dadosPedidos['data_pedido']))));
	   				   		$dataFinalizado = implode("/",array_reverse(explode("-",htmlentities($dadosPedidos['data_finalizado']))));
	   				   		$codigoPedido = htmlentities($dadosPedidos['codigo_pedido']);

	   						$dadosPDF .= "<div class='wraper-table'>
	   							<hr>
				        		<table>
				            	<tr>
				            		<br>
				                	<td>Nome:</td>
					                <td>Sobrenome:</td>
					                <td>Matrícula:</td>
					        	</tr>
		    				";
		    				$dadosPDF .= "<tr>
						        <td>{$nome}</td>
								<td>{$sobrenome}</td>
								<td>{$matricula}</td>
						        </tr>
			            	";

			            	$dadosPDF.= "</table></div>";

							$dadosPDF .= "<div class='wraper-table'>
				        		<table>
				            	<tr>
				            		<br>
				                	<td>Item:</td>
					                <td>Quantidade:</td>
					                <td>Tipo:</td>
					        	</tr>
		    				";

		    				$detalhesPedido = Pedido::retornaPedidoPeloCodigo($codigoPedido);

					        while($descricao = $detalhesPedido->fetch(PDO::FETCH_ASSOC)) {
						        $nomeItem = htmlentities($descricao['nome_estoque']);
						        $quantidadeItem = htmlentities($descricao['quantidade_item']);
						        $tipoEstoque = htmlentities(tipoEstoque($descricao['tipo']));
						        $dadosPDF .= "<tr>
							        <td>{$nomeItem}</td>
							        <td>{$quantidadeItem}</td>
							        <td>{$tipoEstoque}</td>
							        </tr>
					            	";
					        }

					        $dadosPDF .= "<br>Data pedido: {$dataPedido}<br>";
					        $dadosPDF .= "Data finalização: {$dataFinalizado}<br>";

					        $dadosPDF .= "Código do pedido: {$codigoPedido}";					        
					        $dadosPDF.= "</table></div>";
					        $dadosPDF .= "<br><br>";

					        $contador++;
	   					}

	   					$dadosPDF .= "<br>";

	   					$contador = $contador - 1;
	   					$dadosPDF .= "<p> Ao todo foram <strong>{$contador} pedidos</strong> ao longo do período.</p>";
		    			
		    			$gerarPDF = new Relatorio();
		                $gerarPDF->gerarPDF($dataHoje, $dadosPDF, 1);
	   			}
	   		}
		?>
		<div class="form-group">
			<label for="">Escolha a data inicial:</label>
			<input type="date" name="dataInicial" value="<?php echo date("Y-m-d") ?>">
		</div>

		<div class="form-group">
			<label for="">Escolha a data final:</label>
			<input type="date" name="dataFinal" value="<?php echo date("Y-m-d") ?>">
		</div>

		<div class="form-group">
			<input type="submit" value="Gerar relatório" name="acao">
		</div>

		<div class="form-group">
			<input type="submit" value="Todas as datas" name="acao-tudo">
		</div>
	</form>
</div>