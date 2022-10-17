<?php  
	verificaPermissaoPagina(1);
?>

<?php
	$estoque = Estoque::selectAll();
	if(isset($_GET['id'])) {
		echo "testando";
	}
?>
<div class="box-content">
	<h2> <i class="fa fa-shopping-cart"></i>Solicitar Empréstimo</h2>
	
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
				
				<td><a href="<?php echo INCLUDE_PATH_PAINEL?>solicitar-emprestimo?id=<?php echo htmlentities($value['id']); ?>" class="btn edit">Adicionar ao pedido <i class="fa fa-pencil-alt"></i></a></td>
				
				<td><a class="btn order" href="<?php echo INCLUDE_PATH_PAINEL?>listar-depoimentos?order=up&id=<?php echo $value['id']; ?>"><i class="fa fa-angle-up"></i></a></td>
				
				<td><a class="btn order" href="<?php echo INCLUDE_PATH_PAINEL?>listar-depoimentos?order=down&id=<?php echo $value['id']; ?>"><i class="fa fa-angle-down"></i></a></td>
			</tr>
			<?php } ?>
		</table>
	</div>
</div>