<?php  
	verificaPermissaoPagina(2);
?>

<?php
	if(isset($_GET['pagina']) && (int)$_GET['pagina'] && $_GET['pagina'] > 0) {
		$paginaAtual = filter_var($_GET['pagina'], FILTER_SANITIZE_NUMBER_INT);
	}

	else {
		$paginaAtual = 1;
	}

	$porPagina = 10;
	$estoque = Estoque::selectAll(($paginaAtual - 1) * $porPagina, $porPagina);
?>
<div class="box-content">
	<h2> <i class="fas fa-sync-alt"></i> Atualizar Estoque</h2>

	<form class="buscador">	
		<div class="form-group">
			<label for="">Não encontrou o que procura? Faz uma busca! <i class="fa fa-search"></i></label>
			<input type="text" name="busca" required="" placeholder="Ex: Arduino">
			<input type="submit" name="buscar" value="Buscar">			
		</div>
	</form>

	<?php 
		if(isset($_GET['buscar'])) {
			$data = filter_var($_GET["busca"], FILTER_SANITIZE_STRING);

            if(!empty(Estoque::returndata($data))){
				$estoque = Estoque::returndata($data);
            }
		}
	?>
	<div class="wraper-table">
		<table>
			<tr>
				<td>Nome</td>
				<td>Quantidade</td>
				<td>Tipo</td>
				<td>#</td>
				<td>#</td>
				<td>#</td>
			</tr>
			<?php
				foreach ($estoque as $key => $value) {
				$quantidadeDisponivel = Estoque::estoqueDisponivel(htmlentities($value['id']));
			?>

			<tr>
				<td><?php echo htmlentities($value['nome']); ?></td>

				<td><?php
						//Caso o id do produto não esteja na tabela de pedidos mostra a sua quantidade original.
				 		if(is_null($quantidadeDisponivel[0])) {
				 			echo htmlentities($value['quantidade']);
				 		}

				 		else {
							echo htmlentities($quantidadeDisponivel[0]);
				 		}
				 	?>		
				 </td>

				<td><?php echo tipoEstoque(htmlentities($value['tipo'])); ?></td>
				
				<td><a href="<?php echo INCLUDE_PATH_PAINEL?>editar-estoque?id=<?php echo htmlentities($value['id']); ?>" class="btn edit">Editar <i class="fa fa-pencil-alt"></i></a></td>
				
				<td><a class="btn order" href="<?php echo INCLUDE_PATH_PAINEL?>listar-depoimentos?order=up&id=<?php echo $value['id']; ?>"><i class="fa fa-angle-up"></i></a></td>
				
				<td><a class="btn order" href="<?php echo INCLUDE_PATH_PAINEL?>listar-depoimentos?order=down&id=<?php echo $value['id']; ?>"><i class="fa fa-angle-down"></i></a></td>
			</tr>
			<?php } ?>
		</table>
	</div>

	<div class="paginacao">
		<?php 
			$totalPaginas = ceil(count(Estoque::selectAll()) / $porPagina);

			for($i = 1; $i <= $totalPaginas; $i++) {
				if($i == $paginaAtual) {
					echo '<a href="' . INCLUDE_PATH_PAINEL . 'listar-estoque?pagina=' . $i . '" class="page-selected">' . $i . '</a>';
				}

				else {
					echo '<a href="' . INCLUDE_PATH_PAINEL . 'listar-estoque?pagina=' . $i . '">' . $i . '</a>';
				}


			}
		?>
	</div>
</div>	