<div class="box-content">
	<h2> <i class="fas fa-shield-alt"></i> Alterar Senha</h2>

	<form method="post" enctype="multipart/form-data">
		<?php 
			if(isset($_POST['acao'])) {
				if(isset($_POST['password'])) {
					$senha = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
					if(Usuario::atualizarSenha($senha)) {
						Painel::alert('sucesso', 'A sua senha foi alterada com sucesso.');
					}
				}

				else {
					Painel::alert('erro', 'A senha não foi passada.');
				}
			}
		?>
		<div class="form-group">
			<label for="">Senha:</label>
			<input type="password" name="password" required="">
		</div>

		<div class="form-group">
			<input type="submit" value="Atualizar" name="acao">
		</div>
	</form>
</div>