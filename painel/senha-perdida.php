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
</head>
<body>
	<div class="box-login">
		<?php
			if(isset($_GET['token_recuperacao']) and $_GET['token_recuperacao'] != '') {
				$token_recuperacao = filter_var($_GET['token_recuperacao'], FILTER_SANITIZE_STRING);
				$sql = Mysql::conectar()->prepare("SELECT * FROM `usuarios` WHERE token_recuperacao = ?");

				$sql->execute(array($token_recuperacao));
				if($sql->rowCount() == 1) {
					if(isset($_POST['password'])) {
						if($_POST['password'] != '') {
							$senha = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
							
							$opcoes = [
    							'cost' => 11
							];

							$senha = password_hash($senha, PASSWORD_BCRYPT, $opcoes);
							
							$sql = Mysql::conectar()->prepare('UPDATE `usuarios` SET token_recuperacao = ?, senha = ? WHERE token_recuperacao = ?');

							$sql->execute(array("", $senha, $token_recuperacao));
							Painel::alert("sucesso", "Sua senha foi atualizada, você será redirecionado para o login em instantes.");
							redirectLogin();
						}
				
						else {
							Painel::alert("erro", "A senha não pode ser vazia.");
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
		<?php  
				}

				else {
					Painel::alert("erro", "Este token é invalido ou já foi utilizado. Você será redirecionado em instantes.");
					redirectLogin();
				}
			}

			else {
				Painel::alert("erro", "O token não foi passado, você será redirecionado");
				redirectLogin();
			}
		?>

	</div>
</body>
</html>