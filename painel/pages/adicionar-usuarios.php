<?php  
	verificaPermissaoPagina(2);
?>

<div class="box-content">
	<h2> <i class="fa fa-pencil-alt"></i>Adicionar Usuário</h2>

	<form method="post" enctype="multipart/form-data">
		<?php 
			if(isset($_POST['acao'])) {
				$login = $_POST['login'];
				$nome = $_POST['nome'];
				$sobrenome = $_POST['sobrenome'];
				$email = $_POST['email'];
				$senha = $_POST['password'];
				$imagem = $_FILES['imagem'];
				$cargo = $_POST['acesso'];

				$usuario = new Usuario();

				if($login == '') {
					Painel::alert('erro', 'O login está vazio');
				}

				else if($nome == '') {
					Painel::alert('erro', 'O nome está vazio');

				}

				else if($sobrenome == '') {
					Painel::alert('erro', 'O sobrenome está vazio');

				}

				else if($email == '') {
					Painel::alert('erro', 'O e-mail está vazio');

				}

				else if($senha == '') {
					Painel::alert('erro', 'A senha está vazia');
				}

				else if($cargo == '') {
					Painel::alert('erro', 'O cargo está vazio');
				}

				else if($imagem['name'] == '') {
					Painel::alert('erro', 'Selecione uma imagem');
				}

				else {
					if($cargo >= $_SESSION['acesso']) {
						Painel::alert('erro', 'É necessário selecionar um cargo menor que o seu');
					}

					else if(Painel::imagemValida($imagem) == false) {
						Painel::alert('erro', 'O formato especificado não é válido');
					}

					else if(Usuario::userExist($login)) {
						Painel::alert('erro', 'Selecione um login diferente');						
					}

					else {
						$usuario = new Usuario();

						$imagem = Painel::uploadFile($imagem);
						$usuario->cadastrarUsuario($login, $nome, $sobrenome, $email, $senha, $imagem, $cargo);
						Painel::alert("sucesso", "Usuário cadastrado com sucesso");
					}
				}


			}
		?>
		<div class="form-group">
			<label for="">Login:</label>
			<input type="text" name="login">
		</div>

		<div class="form-group">
			<label for="">Nome:</label>
			<input type="text" name="nome">
		</div>
		
		<div class="form-group">
			<label for="">Sobrenome:</label>
			<input type="text" name="sobrenome">
		</div>

		<div class="form-group">
			<label for="">E-mail:</label>
			<input type="email" name="email">
		</div>

		<div class="form-group">
			<label for="">Senha:</label>
			<input type="password" name="password">
		</div>

		<div class="form-group">
			<label for="">Cargo:</label>
			<select name="acesso">
				<?php 
					foreach (Painel::$acessos as $key => $value) {
						echo "$key | $value <br>";
						if($key <= $_SESSION['acesso']) {
							echo "<option value='$key'>$value</option>";
						}
					}
				?>
			</select>
		</div>

		<div class="form-group">
			<label for="">Imagem:</label>
			<input type="file" name="imagem">
		</div>

		<div class="form-group">
			<input type="submit" value="Cadastrar" name="acao">
		</div>
	</form>
</div>