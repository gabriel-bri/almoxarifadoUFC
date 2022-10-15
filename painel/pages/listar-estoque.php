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

	if($estoque == false) {
		Painel::redirect(INCLUDE_PATH_PAINEL . 'listar-estoque');
	}
?>
<div class="box-content">
	<h2> <i class="fa fa-pencil-alt"></i>Atualizar Estoque</h2>

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
			?>

			<tr>
				<td><?php echo htmlentities($value['nome']); ?></td>

				<td><?php echo htmlentities($value['quantidade']); ?></td>

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