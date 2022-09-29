<?php  
	verificaPermissaoPagina(2);
?>

<?php 
	if(isset($_GET['id']) && (int)$_GET['id'] && $_GET['id'] > 0) {
		$id = (int)$_GET['id'];
		$usuarios = Usuario::select('id = ?', array($id));

		if($usuarios != true) {
			Painel::alert("erro", "ID não encontrado");
			die();			
		}
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
					$nome = filter_var($_POST['nome'], FILTER_SANITIZE_STRING);
					$sobrenome = filter_var($_POST['sobrenome'], FILTER_SANITIZE_STRING);
					$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
					$id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
					if(Usuario::atualizarUsuarios($nome, $sobrenome, $email, $id)){
						Painel::alert('sucesso', 'O usuário foi atualizado com sucesso');
						$usuarios = Usuario::select('id = ?', array($id));
					}
				}
			}
		?>
		<div class="form-group">
			<label for="">Nome:</label>
			<input type="text" name="nome" required="" value="<?php echo htmlentities($usuarios['nome']); ?>">
		</div>

		<div class="form-group">
			<label for="">Sobrenome:</label>
			<input type="text" name="sobrenome" required="" value="<?php echo htmlentities($usuarios['sobrenome']) ?>">
		</div>

		<div class="form-group">
			<label for="">E-mail:</label>
			<input type="text" name="email" required="" value="<?php echo htmlentities($usuarios['email']) ?>">
		</div>

		<div class="form-group">
			<input type="submit" value="Atualizar" name="acao">
			<input type="hidden" name="id" value="<?php echo $id; ?>">
		</div>
	</form>
</div>