<div class="box-content">
	<h2> <i class="fas fa-user-edit"></i> Editar Usuário</h2>

	<form method="post" enctype="multipart/form-data">
		<?php 
			if(isset($_POST['acao'])) {
				Usuario::validarEntradasAtualizarUsuario(
					$_POST
				);
			}
		?>
		<div class="form-group">
			<label for="nome">Nome:</label>
			<input id="nome" type="text" name="nome" required="" value="<?php echo $_SESSION['nome'] ?>" placeholder="Nome">
		</div>

		<div class="form-group">
			<label for="">Sobrenome:</label>
			<input id="sobrenome" type="text" name="sobrenome" required="" value="<?php echo $_SESSION['sobrenome'] ?>" placeholder="Sobrenome">
		</div>

		<div class="form-group">
			<label for="">E-mail:</label>
			<input id="email" type="email" name="email" required="" value="<?php echo $_SESSION['email'] ?>" placeholder="E-mail">
		</div>
		
		<div class="form-group">
			<label for="imagem">Imagem:</label>
			<input id="imagem" type="file" name="imagem">
			<input type="hidden" name="imagem_atual" value="<?php echo $_SESSION['fotoperfil'] ?>">
		</div>

		<div class="form-group">
			<input type="submit" value="Atualizar" name="acao">
		</div>
	</form>
</div>