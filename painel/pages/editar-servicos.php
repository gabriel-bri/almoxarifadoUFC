<?php 
	if(isset($_GET['id'])) {
		$id = (int)$_GET['id'];
		$servicos = Painel::select('tb_site.servicos', 'id = ?', array($id));
	}

	else {
		Painel::alert("erro", "Você precisa passar um ID");
		die();
	}
?>
<div class="box-content">
	<h2> <i class="fa fa-pencil-alt"></i>Editar Serviços</h2>

	<form method="post">
		<?php 
			if(isset($_POST['acao'])) {
				if(Painel::update($_POST)) {
					Painel::alert('sucesso', 'Serviços atualizado com sucesso');
					$servicos = Painel::select('tb_site.servicos', 'id = ?', array($id));
				}

				else {
					Painel::alert('erro', 'Campos vazios não permitidos');
				}

				// var_dump(Painel::update($_POST));
			}
		?>

		<div class="form-group">
			<label for="">Servicos:</label>
			<textarea name="servicos"><?php echo $servicos['servicos'] ?></textarea>
		</div>

		

		<div class="form-group">
			<input type="submit" value="Atualizar" name="acao">
			<input type="hidden" name="id" value="<?php echo $id; ?>">
			<input type="hidden" value="tb_site.servicos" name="nome_tabela">
		</div>
	</form>
</div>