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
				Usuario::validarRecuperarSenha($_POST);
			}
		?>
		<h2>Digite seu usuário para recuperar a senha</h2>
		<form method="post" action="">
			<input type="text" name="user" placeholder="Login...">
			<div class="form-group-login left">
				<input type="submit" name="acao" value="Recuperar">
			</div>

			<div class="form-group-login right ">
				<p><a href="<?php echo INCLUDE_PATH_PAINEL ?>recuperar-usuario">Esqueceu o usuário?</a></p>
			</div>
		</form>

	</div>
</body>
</html>