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

				Usuario::validarEntradasCadastro($login, $nome, $sobrenome, $email, $imagem, $cargo, $matricula, $curso, $senha);
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