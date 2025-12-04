<?php
	ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

	verificaPermissaoPagina(3);
?>
<div class="box-content">
	<h2> <i class="fas fa-user-plus"></i> Adicionar Usuário</h2>
	<p>Caso o usuário seja SUPER ADMINISTRADOR, os campos CURSO e MATRÍCULA são dispensados.</p>
	<form method="post" enctype="multipart/form-data">
		<?php 
			if(isset($_POST['acao'])) {
				Usuario::validarEntradasCadastro($_POST);
			}
		?>
		<div class="form-group">
			<label for="login">Login:</label>
			<input type="text" name="login" id="login" placeholder="Login" value="<?php echo isset($_POST['login']) ? htmlentities($_POST['login']) : ''; ?>">
		</div>

		<div class="form-group">
			<label for="nome">Nome:</label>
			<input type="text" name="nome" id="nome" placeholder="Nome" value="<?php echo isset($_POST['nome']) ? htmlentities($_POST['nome']) : ''; ?>">
		</div>
		
		<div class="form-group">
			<label for="sobrenome">Sobrenome:</label>
			<input type="text" name="sobrenome" id="sobrenome" placeholder="Sobrenome" value="<?php echo isset($_POST['sobrenome']) ? htmlentities($_POST['sobrenome']) : ''; ?>">
		</div>

		<div class="form-group">
			<label for="email">E-mail:</label>
			<input type="email" name="email" id="email" placeholder="E-mail" value="<?php echo isset($_POST['email']) ? htmlentities($_POST['email']) : ''; ?>">
		</div>

		<div class="form-group">
			<label for="password">Senha:</label>
			<input type="password" name="password" id="password" placeholder="Senha" value="<?php echo isset($_POST['password']) ? htmlentities($_POST['password']) : ''; ?>">
		</div>

		<div class="form-group">
			<label for="acesso">Cargo:</label>
			<select name="acesso" id="acesso">
				<?php 
					foreach (Usuario::$acessos as $key => $value) {
						$selected = ($key == $_POST['acesso']) ? 'selected' : ''; // Verifica se é o valor enviado pelo formulário
						if($key <= $_SESSION['acesso']) {
							echo "<option value='$key' $selected>$value</option>";
						}
					}
				?>
			</select>
		</div>

		<div class="form-group">
			<label for="curso">Curso:</label>
			<select name="curso" id="curso">
				<?php 
					foreach (Usuario::$cursos as $key => $value) {
						$selected = ($key == $_POST['curso']) ? 'selected' : ''; // Verifica se é o valor enviado pelo formulário
						echo "<option value='$key' $selected>$value</option>";
					}
				?>
			</select>
		</div>

		<div class="form-group">
			<label for="matricula">Matrícula:</label>
			<input type="text" name="matricula" maxlength="6" pattern="([0-9]{6})" id="matricula" placeholder="Matrícula" value="<?php echo isset($_POST['matricula']) ? htmlentities($_POST['matricula']) : ''; ?>">
		</div>

		<div class="form-group">
			<label for="imagem">Imagem:</label>
			<input type="file" name="imagem" id="imagem">
		</div>

		<div class="form-group">
			<input type="submit" value="Cadastrar" name="acao">
		</div>
	</form>
</div>