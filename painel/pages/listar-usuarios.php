<?php  
	verificaPermissaoPagina(2);
?>

<?php
	if(isset($_GET['excluir'])) {
		if(isset($_GET['excluir']) && (int)$_GET['excluir'] && $_GET['excluir'] > 0 && $_GET['excluir'] != $_SESSION['id']){
			$idExcluir = intval($_GET['excluir']);
			Usuario::deletar($idExcluir);
			Painel::redirect(INCLUDE_PATH_PAINEL . 'listar-usuarios');
		}
	}
?>

<?php
	if(isset($_GET['pagina']) && (int)$_GET['pagina'] && $_GET['pagina'] > 0) {
		$paginaAtual = filter_var($_GET['pagina'], FILTER_SANITIZE_NUMBER_INT);
	}

	else {
		$paginaAtual = 1;
	}

	$porPagina = 10;
	$usuarios = Usuario::selectAll(($paginaAtual - 1) * $porPagina, $porPagina);

	if($usuarios == false) {
		Painel::redirect(INCLUDE_PATH_PAINEL . 'listar-usuarios');
	}
?>
<div class="box-content">
	<h2> <i class="fa fa-pencil-alt"></i>Editar Usuários</h2>

	<div class="wraper-table">
		<table>
			<tr>
				<td>Nome</td>
				<td>Sobrenome</td>
				<td>Usuário</td>
				<td>Status</td>
				<td>#</td>
				<td>#</td>
				<td>#</td>
				<td>#</td>
			</tr>
			<?php
				foreach ($usuarios as $key => $value) {
					if($_SESSION['usuario'] !== $value['usuario']){
			?>

				<tr>
					<td><?php echo htmlentities($value['nome']); ?></td>

					<td><?php echo htmlentities($value['sobrenome']); ?></td>

					<td><?php echo htmlentities($value['usuario']); ?></td>

					<td><?php echo pegaCargo(htmlentities($value['acesso'])); ?>
					
					<td><a href="<?php echo INCLUDE_PATH_PAINEL?>editar-usuarios?id=<?php echo htmlentities($value['id']); ?>" class="btn edit">Editar <i class="fa fa-pencil-alt"></i></a></td>

					<td><a actionBtn="delete" href="<?php echo INCLUDE_PATH_PAINEL ?>listar-usuarios?excluir=<?php echo htmlentities($value['id']); ?>" class="btn delete">Excluir <i class="fa fa-times"></i></a></td>

					<td><a class="btn order" href="<?php echo INCLUDE_PATH_PAINEL?>listar-depoimentos?order=up&id=<?php echo $value['id']; ?>"><i class="fa fa-angle-up"></i></a></td>
					
					<td><a class="btn order" href="<?php echo INCLUDE_PATH_PAINEL?>listar-depoimentos?order=down&id=<?php echo $value['id']; ?>"><i class="fa fa-angle-down"></i></a></td>
				</tr>
			<?php }} ?>
		</table>
	</div>

	<div class="paginacao">
		<?php 
			$totalPaginas = ceil(count(Usuario::selectAll()) / $porPagina);

			for($i = 1; $i <= $totalPaginas; $i++) {
				if($i == $paginaAtual) {
					echo '<a href="' . INCLUDE_PATH_PAINEL . 'listar-usuarios?pagina=' . $i . '" class="page-selected">' . $i . '</a>';
				}

				else {
					echo '<a href="' . INCLUDE_PATH_PAINEL . 'listar-usuarios?pagina=' . $i . '">' . $i . '</a>';
				}


			}
		?>
	</div>
</div>