<div class="box-content">
	<h2> <i class="fa fa-pencil-alt"></i>Atualizar Estoque</h2>

	<form method="post">
		<?php 
			if(isset($_POST['acao'])) {
				if(Painel::insert($_POST)) {
					Painel::alert('sucesso', 'O cadastro do serviço foi realizado com sucesso');
				}

				else {
					Painel::alert('erro', 'Campos vazios não são permitidos');
				}
			}
		?>
	<div class="wraper-table">
		<table>
			<tr>
				<td>Nome</td>
				<td>Quantidade</td>
				<td>#</td>
				<td>#</td>
				<td>#</td>
			</tr>
			<?php
			  	$estoque = Mysql::conectar()->prepare('SELECT * FROM  `estoque`');
			  	$estoque->execute();
			  	$estoque = $estoque->fetchAll();
				foreach ($estoque as $key => $value) {
			?>

			<tr>
				<td><?php echo $value['nome']; ?></td>

				<td><?php echo $value['quantidade']; ?></td>
				
				<td><a href="<?php echo INCLUDE_PATH_PAINEL?>editar-estoque?id=<?php echo $value['id']; ?>" class="btn edit">Editar <i class="fa fa-pencil-alt"></i></a></td>
				
				<td><a class="btn order" href="<?php echo INCLUDE_PATH_PAINEL?>listar-depoimentos?order=up&id=<?php echo $value['id']; ?>"><i class="fa fa-angle-up"></i></a></td>
				
				<td><a class="btn order" href="<?php echo INCLUDE_PATH_PAINEL?>listar-depoimentos?order=down&id=<?php echo $value['id']; ?>"><i class="fa fa-angle-down"></i></a></td>
			</tr>
			<?php } ?>
		</table>
	</div>
</div>