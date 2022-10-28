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
</head>
<body>
	<div class="box-login">
		<?php
			if(isset($_GET['token_confirmacao']) and $_GET['token_confirmacao'] != '') {
				$token_confirmacao = $_GET['token_confirmacao'];
				$sql = Mysql::conectar()->prepare("SELECT * FROM `usuarios` WHERE token_confirmacao = ?");

				$sql->execute(array($token_confirmacao));
				if($sql->rowCount() == 1) {
					$sql = Mysql::conectar()->prepare('UPDATE `usuarios` SET token_confirmacao = ?, is_ativada = ? WHERE token_confirmacao = ?');

					$sql->execute(array("", 1, $token_confirmacao));
					Painel::alert("sucesso", "Seu e-mail foi confirmado com sucesso, você será redirecionado para o login em instantes.");					
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