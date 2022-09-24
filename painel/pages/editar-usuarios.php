<?php 
	if(isset($_GET['id'])) {
		$id = (int)$_GET['id'];
		$usuarios = Usuario::select('id = ?', array($id));
	}

	else {
		Painel::alert("erro", "Você precisa passar um ID");
		die();
	}
?>
<div class="box-content">
	<h2> <i class="fa fa-pencil-alt"></i>Editar Usuário</h2>

	<form method="post">
		<?php 
			if(isset($_POST['acao'])) {
				if($_POST["nome"] == "" || $_POST["sobrenome"] == "" || $_POST['email'] == ""){
					Painel::alert('erro', 'Campos vazios não são permitidos');
				}

				else{
					$nome = $_POST['nome'];
					$sobrenome = $_POST['sobrenome'];
					$email = $_POST['email'];
					$id = $_POST['id'];
					if(Usuario::atualizarUsuarios($nome, $sobrenome, $email, $id)){
						Painel::alert('sucesso', 'O usuário foi atualizado com sucesso');
						$usuarios = Usuario::select('id = ?', array($id));
					}
				}
			}
		?>
		<div class="form-group">
			<label for="">Nome:</label>
			<input type="text" name="nome" required="" value="<?php echo $usuarios['nome'] ?>">
		</div>

		<div class="form-group">
			<label for="">Sobrenome:</label>
			<input type="text" name="sobrenome" required="" value="<?php echo $usuarios['sobrenome'] ?>">
		</div>

		<div class="form-group">
			<label for="">E-mail:</label>
			<input type="text" name="email" required="" value="<?php echo $usuarios['email'] ?>">
		</div>

		<div class="form-group">
			<input type="submit" value="Atualizar" name="acao">
			<input type="hidden" name="id" value="<?php echo $id; ?>">
		</div>
	</form>
</div>