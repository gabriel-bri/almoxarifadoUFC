<div class="box-content">
	<h2> <i class="fa fa-pencil-alt"></i>Editar Usuário</h2>

	<form method="post" enctype="multipart/form-data">
		<?php 
			if(isset($_POST['acao'])) {
				$nome = filter_var($_POST['nome'], FILTER_SANITIZE_STRING);
				$sobrenome = filter_var($_POST['sobrenome'], FILTER_SANITIZE_STRING);
				$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
				
				$imagem = $_FILES['imagem'];
				$imagem_atual = $_POST['imagem_atual'];

				$dominio = explode("@", $email);
					
				if($dominio[1] != "alu.ufc.br" && $_SESSION['acesso'] == 1) {
					Painel::alert('erro', 'Alunos podem usar apenas e-mail @alu.ufc.br');
				}

				else if(filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
					Painel::alert('erro', 'E-mai inválido, tente novamente.');
				}

				else if(Usuario::emailJaCadastrado($email)){
					Painel::alert('erro', 'E-mail já cadastrado.');
				}

				else {
					$usuario = new Usuario();
					if($imagem['name'] != '') {
						if(Painel::imagemValida($imagem)) {
							Painel::deleteFile($imagem_atual);
							$imagem = Painel::uploadFile($imagem);
							if($usuario->atualizarUsuario($nome, $sobrenome, $email, $imagem)){
								$_SESSION['fotoperfil'] = $imagem;
								$_SESSION['nome'] = $nome;
								Painel::alert("sucesso", "Atualização de dados junto com a imagem realizada com sucesso.");
							}

							else {
								Painel::alert("erro", "Erro durante a atualização de dados junto com a imagem.");
							}
						}

						else {
							Painel::alert("erro", "O formato da imagem não é válido");
						}
					}

					else {
						$imagem = $imagem_atual;
						if($usuario->atualizarUsuario($nome, $sobrenome, $email, $imagem)){
							Painel::alert("sucesso", "Atualização de dados realizada com sucesso");
						}

						else {
							Painel::alert("erro", "Erro durante a atualização de dados.");
						}
					}
				}


			}
		?>
		<div class="form-group">
			<label for="">Nome:</label>
			<input type="text" name="nome" required="" value="<?php echo $_SESSION['nome'] ?>">
		</div>

		<div class="form-group">
			<label for="">Sobrenome:</label>
			<input type="text" name="sobrenome" required="" value="<?php echo $_SESSION['sobrenome'] ?>">
		</div>

		<div class="form-group">
			<label for="">E-mail:</label>
			<input type="email" name="email" required="" value="<?php echo $_SESSION['email'] ?>">
		</div>
		
		<div class="form-group">
			<label for="">Imagem:</label>
			<input type="file" name="imagem">
			<input type="hidden" name="imagem_atual" value="<?php echo $_SESSION['fotoperfil'] ?>">
		</div>

		<div class="form-group">
			<input type="submit" value="Atualizar" name="acao">
		</div>
	</form>
</div>