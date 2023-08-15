<?php  
	verificaPermissaoPagina(2);
?>
<div class="box-content">
	<h2> <i class="fas fa-user-plus"></i> Adicionar Usuário</h2>
	<p>Caso o usuário seja um administrador, os campos CURSO e MATRÍCULA são dispensados.</p>
	<form method="post" enctype="multipart/form-data">
		<?php 
			if(isset($_POST['acao'])) {
				Usuario::validarEntradasCadastro($_POST);
			}
		?>
		<div class="form-group">
			<label for="login">Login:</label>
			<input type="text" name="login" id="login" placeholder="Login">
		</div>

		<div class="form-group">
			<label for="nome">Nome:</label>
			<input type="text" name="nome" id="nome" placeholder="Nome">
		</div>
		
		<div class="form-group">
			<label for="sobrenome">Sobrenome:</label>
			<input type="text" name="sobrenome" id="sobrenome" placeholder="Sobrenome">
		</div>

		<div class="form-group">
			<label for="email">E-mail:</label>
			<input type="email" name="email" id="email" placeholder="E-mail">
		</div>

		<div class="form-group">
			<label for="password">Senha:</label>
			<input type="password" name="password" id="password" placeholder="Senha">
		</div>

		<div class="form-group">
			<label for="acesso">Cargo:</label>
			<select name="acesso" id="acesso">
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
			<label for="curso">Curso:</label>
			<select name="curso" id="curso">
				<?php 
					foreach (Painel::$cursos as $key => $value) {
						echo "$key | $value <br>";
						echo "<option value='$key'>$value</option>";
					}
				?>
			</select>
		</div>

		<div class="form-group">
			<label for="matricula">Matrícula:</label>
			<input type="text" name="matricula" maxlength="6" pattern="([0-9]{6})" id="matricula" placeholder="Matrícula">
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