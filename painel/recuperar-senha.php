<?php
	require "../config.php";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="<?php echo INCLUDE_PATH_PAINEL;?>css/font-awesome.css">
	<title>Recuperar senha.</title>
	<link rel="stylesheet" href="<?php echo INCLUDE_PATH_PAINEL ?>css/style.css">
 	<link href="<?php echo ICONE_SITE ?>" rel="icon">
  	<link href="<?php echo ICONE_SITE ?>" rel="apple-touch-icon">
</head>
<body>
	<div class="box-login">
		<?php
			if(isset($_POST['acao'])) {
				if($_POST['user'] != '') {
					$user = filter_var($_POST['user'], FILTER_SANITIZE_STRING);
					
					if(Usuario::recuperarSenha($user)) {
						Painel::alert("sucesso", "Se tudo estiver correto um e-mail com instruções será enviado para você.");
					}

					else {
						Painel::alert("erro", "Esta conta pode não existir ou ela ainda não foi ativada.");
					}
				}

				else{
					Painel::alert("erro", "O usuário não foi passado. Tente novamente.");
				}

			}
		?>
		<h2>Digite seu usuário para recuperar a senha</h2>
		<form method="post" action="">
			<input type="text" name="user" placeholder="Login...">
			<div class="form-group-login left">
				<input type="submit" name="acao" value="Recuperar">
			</div>
		</form>

	</div>
</body>
</html>