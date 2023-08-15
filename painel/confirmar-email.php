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
	<title>Confirmação de e-mail</title>
	<link rel="stylesheet" href="<?php echo INCLUDE_PATH_PAINEL ?>css/style.css">
 	<link href="<?php echo ICONE_SITE ?>" rel="icon">
  	<link href="<?php echo ICONE_SITE ?>" rel="apple-touch-icon">
</head>
<body>
	<div class="box-login">
		<?php
			if(isset($_GET['token_confirmacao']) and $_GET['token_confirmacao'] != '') {
				if(Usuario::tokenConfirmacaoValido($_GET)) {
					if(Usuario::confirmaConta($_GET)){
						Painel::alert("sucesso", "Seu e-mail foi confirmado com sucesso, você será redirecionado para o login em instantes.");					
					}
				}

				else {
					Painel::alert("erro", "Este e-mail já foi confirmado ou o token é inválido. Você será redirecionado em instantes.");
				}

			}

			else {
				Painel::alert("erro", "O token não foi passado, você será redirecionado");
				
			}
			
			redirectLogin();
		?>

	</div>
</body>
</html>