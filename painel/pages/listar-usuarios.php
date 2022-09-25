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
			</tr>
			<?php
			  	$usuarios = Mysql::conectar()->prepare('SELECT * FROM  `usuarios`');
			  	$usuarios->execute();
			  	$usuarios = $usuarios->fetchAll();
				foreach ($usuarios as $key => $value) {
					if($_SESSION['usuario'] !== $value['usuario']){

			?>

				<tr>
					<td><?php echo htmlentities($value['nome']); ?></td>

					<td><?php echo htmlentities($value['sobrenome']); ?></td>

					<td><?php echo htmlentities($value['usuario']); ?></td>

					<td><?php echo pegaCargo(htmlentities($value['acesso'])); ?>
					
					<td><a href="<?php echo INCLUDE_PATH_PAINEL?>editar-usuarios?id=<?php echo htmlentities($value['id']); ?>" class="btn edit">Editar <i class="fa fa-pencil-alt"></i></a></td>
					
					<td><a class="btn order" href="<?php echo INCLUDE_PATH_PAINEL?>listar-depoimentos?order=up&id=<?php echo $value['id']; ?>"><i class="fa fa-angle-up"></i></a></td>
					
					<td><a class="btn order" href="<?php echo INCLUDE_PATH_PAINEL?>listar-depoimentos?order=down&id=<?php echo $value['id']; ?>"><i class="fa fa-angle-down"></i></a></td>
				</tr>
			<?php }} ?>
		</table>
	</div>
</div>