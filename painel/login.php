	<?php  
	if(isset($_COOKIE['lembrar'])) {
		if(password_verify($_COOKIE['token'], $_SESSION['token_lembrar'])){
			$usuario = $_COOKIE['usuario'];	
			$sql = Mysql::conectar()->prepare("SELECT * FROM `usuarios` WHERE usuario = ? LIMIT 1");

			$sql->execute(array($usuario));

			if($sql->rowCount() == 1) {
				$info = $sql->fetch();

				$_SESSION['login'] = true;
				$_SESSION['usuario'] = $usuario;
				$_SESSION['nome'] = $info['nome'];
				$_SESSION['sobrenome'] = $info['sobrenome'];
				$_SESSION['email'] = $info['email'];
				$_SESSION['fotoperfil'] = $info['fotoperfil'];
				$_SESSION['acesso'] = $info['acesso'];
				
				header('Location: ' . INCLUDE_PATH_PAINEL);
				die();	
			}
		}
	}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="<?php echo INCLUDE_PATH_PAINEL;?>css/font-awesome.css">
	<title>Login - Painel de controle</title>
	<link rel="stylesheet" href="<?php echo INCLUDE_PATH_PAINEL ?>css/style.css">
</head>
<body>
	<div class="box-login">
		<?php 
			if(isset($_POST['acao'])) {
				$usuario = filter_var($_POST['user'], FILTER_SANITIZE_STRING);
				$senha = filter_var($_POST['password'], FILTER_SANITIZE_STRING);

				$sql = Mysql::conectar()->prepare("SELECT * FROM `usuarios` WHERE usuario = ? LIMIT 1");

				$sql->execute(array($usuario));
				$info = $sql->fetch();

				if($sql->rowCount() == 1 AND password_verify($senha, $info['senha'])) {

					if($info['is_ativada'] != 0) {
						$_SESSION['login'] = true;
						$_SESSION['id'] = $info['id'];
						$_SESSION['usuario'] = $usuario;
						$_SESSION['senha'] = $senha;
						$_SESSION['nome'] = $info['nome'];
						$_SESSION['sobrenome'] = $info['sobrenome'];
						$_SESSION['email'] = $info['email'];
						$_SESSION['fotoperfil'] = $info['fotoperfil'];
						$_SESSION['acesso'] = $info['acesso'];

						if(isset($_POST['lembrar'])) {
							setcookie('lembrar', true, time() + (60 * 60 * 24), '/', null, null, true);
							setcookie('user', $usuario, time() + (60 * 60 * 24), '/', null, null, true);
							$token_cookie = bin2hex(random_bytes(30));
							setcookie('token', $token_cookie, time() + (60 * 60 * 24), '/', null, null, true);

							$_SESSION['token_lembrar'] = password_hash($token_cookie, PASSWORD_BCRYPT);
						}
			
						header('Location: ' . INCLUDE_PATH_PAINEL);
						die();
					}

					else {
						echo "<div class='erro-box'><i class='fa fa-times'></i> Conta não ativada, verifique o seu e-mail</div>";
					}
				}

				else {
					echo "<div class='erro-box'><i class='fa fa-times'></i> Usuário e/ou senha incorretos</div>";
				}
			}
		?>
		<h2>Efetue login para continuar</h2>
		<form method="post" action="">
			<input type="text" name="user" placeholder="Login...">
			<input type="password" name="password" placeholder="Senha...">
			<div class="form-group-login left">
				<input type="submit" name="acao" value="Logar">
			</div>

			<div class="form-group-login right ">
				<label for="">Lembrar-me</label>
				<input type="checkbox" name="lembrar">
				<p><a href="recuperar-senha">Esqueceu a senha?</a></p>
			</div>

			<div class="clear"></div>
		</form>

		<div class="termos-google">
			<p>Este site é protegido pelo reCAPTCHA e pelo Google.
	    	<a href="https://policies.google.com/privacy" target="_blanck">Política de privacidade</a> e
	    	<a href="https://policies.google.com/terms" target="_blanck">Termos de serviço</a> se aplicam.</p>
    	</div>
	</div>
</body>
</html>