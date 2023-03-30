<?php  
	verificaPermissaoPagina(2);
?>

<div class="box-content">
	<h2> <i class="fa fa-file-pdf"></i> Gerar Relatório do Estoque</h2>

	<form method="post">
		<?php 
			if(isset($_POST['acao'])) {

				$tipo = (int) filter_var($_POST["tipo"], FILTER_SANITIZE_NUMBER_INT);

				if(is_int($tipo)) {

					if($tipo == 1 || $tipo == 2 || $tipo == 3) {

						$dataHoje = date("d/m/Y");
						
						if($tipo == 3) {
							$estoque = Estoque::selectAll();
						}

						else {
							$estoque = Estoque::retornaPeloTipo($tipo);
						}

						$dadosPDF = "
				    		<p>Relatório gerado em: {$dataHoje}</p>
				    		<div class='wraper-table'>
				        		<table>
				            	<tr>
				                	<td>Item:</td>
					                <td>Quantidade:</td>
					                <td>Tipo:</td>

					        </tr>
					        <br>
		    			";

		    			foreach ($estoque as $key => $value) {
		    				$quantidadeDisponivel = Estoque::estoqueDisponivel(htmlentities($value['id']));

							//Caso o id do produto não esteja na tabela de pedidos mostra a sua quantidade original.
		    				if(is_null($quantidadeDisponivel[0])) {
						 		$emprestimoAtivo = "";
						 	}

						 	else {
								$emprestimoAtivo = "*";
						 	}

		    				$nomeItem = htmlentities($value['nome']);
		    				$tipoEstoque = tipoEstoque(htmlentities($value['tipo']));
 						 	$quantidadeItem = htmlentities($value['quantidade']);
		    				
		    				$dadosPDF .= "<tr>
						        <td>{$nomeItem} {$emprestimoAtivo}</td>
						        <td>{$quantidadeItem}</td>
						        <td>{$tipoEstoque}</td>
						        </tr>
			            	";
		    			}

		    			$dadosPDF.= "</table>
		    			<p>Itens marcados com um <b>* (asterisco)</b> estão com empréstimos ativos ou com revisão pendente.</p>
		    			</div>";

		    			$gerarPDF = new Relatorio();
		                $gerarPDF->gerarPDF($dataHoje, $dadosPDF);
					}

					else {
						Painel::alert("erro", "Não foi possível gerar o relatório.");
					}
				}

				else {
					Painel::alert("erro", "Não foi possível gerar o relatório.");
				}
	        }
		?>
		<div class="form-group">
			<label for="">Escolha do que fazer o relatório:</label>
			<select name="tipo">
				<option value="3">Tudo</option>
				<?php 
					foreach (Estoque::$estoque as $key => $value) {
						echo "$key | $value <br>";
						echo "<option value='$key'>$value</option>";
					}
				?>
			</select>
		</div>

		<div class="form-group">
			<input type="submit" value="Gerar relatório" name="acao">
		</div>
	</form>
</div>