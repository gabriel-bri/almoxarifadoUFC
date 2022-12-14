<?php  
	verificaPermissaoPagina(2);
?>

<script type="text/javascript">
	$(document).ready(function() {
	  $("#matricula").keyup(function() {
	      $("#matricula").val(this.value.match(/[0-9]*/));
	  });
	});
</script>

<div class="box-content">
	<h2> <i class="fa fa-pencil-alt"></i>Adicionar Usuário</h2>
	<p>Caso o usuário seja um administrador, os campos CURSO e MATRÍCULA são dispensados.</p>
	<form method="post" enctype="multipart/form-data">
		<?php 
			if(isset($_POST['acao'])) {
				$login = filter_var($_POST['login'], FILTER_SANITIZE_STRING);
				$nome = filter_var($_POST['nome'], FILTER_SANITIZE_STRING);
				$sobrenome = filter_var($_POST['sobrenome'], FILTER_SANITIZE_STRING);
				$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
				$senha = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
				$imagem = $_FILES['imagem'];
				$cargo = filter_var($_POST['acesso'], FILTER_SANITIZE_NUMBER_INT);
				$curso = "";
				$matricula = "";
				$dominio = explode("@", $email);

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

				else if(filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
					Painel::alert('erro', 'E-mai inválido, tente novamente.');
				}

				else if(Usuario::emailJaCadastrado($email)){
					Painel::alert('erro', 'E-mail já cadastrado.');
				}

				else {

					if($cargo == 1) {
						$matricula = filter_var($_POST['matricula'], FILTER_SANITIZE_NUMBER_INT);
						$curso = filter_var($_POST['curso'], FILTER_SANITIZE_STRING);
					}

					if(Usuario::userExist($login)) {
						Painel::alert('erro', 'Selecione um login diferente');		
					}

					else if($cargo == 1 && $dominio[1] != "alu.ufc.br"){
						Painel::alert('erro', 'Alunos podem usar apenas e-mail @alu.ufc.br');
					}

				    else if($cargo == 1 && Usuario::matriculaJaCadastrada($matricula)) {
				        Painel::alert("erro", "Matrícula já cadastrada no sistema");	
				    }

					else if($cargo == 1 && (strlen($matricula) > 6 || strlen($matricula) < 6)) {
						Painel::alert('erro', 'A matrícula deve ter 6 dígitos');
					}

					else if($imagem['name'] != '' && Painel::imagemValida($imagem) == false) {
						Painel::alert('erro', 'O formato especificado não é válido');
					}

					else {
						$usuario = new Usuario();

						if($imagem['name'] != '') {
							$imagem = Painel::uploadFile($imagem);
						}

						else {
							$imagem = "";
						}

						$usuario->cadastrarUsuario($login, $senha, $nome, $sobrenome, $email, $imagem, $cargo, $matricula, $curso);
						Painel::alert("sucesso", "Usuário cadastrado com sucesso. Um e-mail de confirmação foi enviado.");
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
			<label for="">Curso:</label>
			<select name="curso">
				<?php 
					foreach (Painel::$cursos as $key => $value) {
						echo "$key | $value <br>";
						echo "<option value='$key'>$value</option>";
					}
				?>
			</select>
		</div>

		<div class="form-group">
			<label for="">Matrícula:</label>
			<input type="text" name="matricula" maxlength="6" pattern="([0-9]{6})" id="matricula">
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