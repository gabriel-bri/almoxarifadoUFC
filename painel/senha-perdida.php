<?php
	require "../config.php";
?>

<?php
	function redirectLogin() {
		echo "<meta http-equiv='refresh' content='5;url=" . INCLUDE_PATH_PAINEL . "'>";
	}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="<?php echo INCLUDE_PATH_PAINEL;?>css/font-awesome.css">
	<title>Recuperar senha</title>
	<link rel="stylesheet" href="<?php echo INCLUDE_PATH_PAINEL ?>css/style.css">
 	<link href="<?php echo ICONE_SITE ?>" rel="icon">
  	<link href="<?php echo ICONE_SITE ?>" rel="apple-touch-icon">
</head>
<body>
	<div class="box-login">
		<?php
			// Verifica se o token de recuperação foi passado na URL e se não está vazio
			if(!isset($_GET['token_recuperacao']) || $_GET['token_recuperacao'] == '') {
				// Exibe uma mensagem de erro e redireciona o usuário para a página de login
				Painel::alert("erro", "O token não foi passado, você será redirecionado");
				redirectLogin();
				return; // Encerra o script para evitar a execução adicional
			}

			// Verifica se o token de recuperação é válido
			if(!Usuario::tokenRecuperacaoValido($_GET)) {
				// Exibe uma mensagem de erro e redireciona o usuário para a página de login
				Painel::alert("erro", "Este token é inválido ou já foi utilizado. Você será redirecionado em instantes.");
				redirectLogin();
				return; // Encerra o script para evitar a execução adicional
			}

			// Verifica se a senha foi enviada via POST
			if(isset($_POST['password'])) {
				// Verifica se a senha está vazia
				if($_POST['password'] == '') {
					// Exibe uma mensagem de erro se a senha estiver vazia
					Painel::alert("erro", "A senha não pode ser vazia.");
					return; // Encerra o script para evitar a execução adicional
				}

				// Tenta definir uma nova senha
				if(Usuario::novaSenha($_POST)){
					// Exibe uma mensagem de sucesso e redireciona o usuário para a página de login
					Painel::alert("sucesso", "Sua senha foi atualizada, você será redirecionado para o login em instantes.");
					redirectLogin();
				}
			}
		?>
		<h2>Digite a sua nova senha</h2>
		<form method="post" action="">
			<input type="password" name="password" placeholder="Nova senha">
			<div class="form-group-login left">
				<input type="submit" name="acao" value="Recuperar">
			</div>
		</form>
	</div>
</body>
</html>