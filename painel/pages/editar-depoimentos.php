<?php 
	if(isset($_GET['id'])) {
		$id = (int)$_GET['id'];
		$depoimento = Painel::select('tb_site.depoimentos', 'id = ?', array($id));
	}

	else {
		Painel::alert("erro", "Você precisa passar um ID");
		die();
	}
?>
<div class="box-content">
	<h2> <i class="fa fa-pencil-alt"></i>Editar Depoimentos</h2>

	<form method="post">
		<?php 
			if(isset($_POST['acao'])) {
				if(Painel::update($_POST)) {
					Painel::alert('sucesso', 'Depoimento atualizado com sucesso');
				$depoimento = Painel::select('tb_site.depoimentos', 'id = ?', array($id));
				}

				else {
					Painel::alert('erro', 'Campos vazios não permitidos');
				}

				// var_dump(Painel::update($_POST));
			}
		?>
		<div class="form-group">
			<label for="">Nome da pessoa:</label>
			<input type="text" name="nome" value="<?php echo $depoimento['nome'] ?>">
		</div>

		<div class="form-group">
			<label for="">Depoimento:</label>
			<textarea name="depoimentos"><?php echo $depoimento['depoimentos'] ?></textarea>
		</div>

		<div class="form-group">
			<label for="">Data:</label>
			<input formato="data" type="text" name="data" value="<?php echo $depoimento['data'] ?>">
		</div>
		

		<div class="form-group">
			<input type="submit" value="Atualizar" name="acao">
			<input type="hidden" name="id" value="<?php echo $id; ?>">
			<input type="hidden" value="tb_site.depoimentos" name="nome_tabela">
		</div>
	</form>
</div>