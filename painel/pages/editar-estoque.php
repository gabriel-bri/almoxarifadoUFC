<?php 
	if(isset($_GET['id'])) {
		$id = (int)$_GET['id'];
		$estoque = Estoque::select('id = ?', array($id));
	}

	else {
		Painel::alert("erro", "Você precisa passar um ID");
		die();
	}
?>
<div class="box-content">
	<h2> <i class="fa fa-pencil-alt"></i>Editar Estoque</h2>

	<form method="post">
		<?php 
			if(isset($_POST['acao'])) {
				if($_POST["nome"] == "" || $_POST["quantidade"] == ""){
					Painel::alert('erro', 'Campos vazios não são permitidos');
				}

				else if($_POST["quantidade"] < 1 ) {
					Painel::alert('erro', 'Quantidade inválida');
				}

				else{
					if(Estoque::update($_POST)){
						Painel::alert('sucesso', 'O estoque foi atualizado com sucesso');
						$estoque = Estoque::select('id = ?', array($id));
					}
				}
			}
		?>
		<div class="form-group">
			<label for="">Nome do equipamento:</label>
			<input type="text" name="nome" value="<?php echo $estoque['nome'] ?>">
		</div>

		<div class="form-group">
			<label for="">Quantidade:</label>
			<input type="number" name="quantidade" value="<?php echo $estoque['quantidade'] ?>" min="1">
		</div>

		<div class="form-group">
			<input type="submit" value="Atualizar" name="acao">
			<input type="hidden" name="id" value="<?php echo $id; ?>">
		</div>
	</form>
</div>