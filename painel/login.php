<?php  
	if(isset($_COOKIE['lembrar'])) {
		Painel::configurarCookieLembrar();
	}
	if(isset($_POST['acao'])) {
		$usuario = strip_tags($_POST['user'] ?? '');
    	$senha = strip_tags($_POST['password'] ?? '');
    	$token = $_POST['token'] ?? '';

		if($usuario == '' || $senha == '') {
			Painel::alert("erro", "Campos vazios não são permitidos");
		}
		else {
			Painel::login($usuario, $senha, $token);
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
    <script src="https://www.google.com/recaptcha/api.js?render=<?php echo RECAPTCHA_PUBLIC_KEY;?>"></script>
 	<link href="<?php echo ICONE_SITE ?>" rel="icon">
  	<link href="<?php echo ICONE_SITE ?>" rel="apple-touch-icon">
</head>
<body>
	<div class="box-login">
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
				<p><a href="<?php echo INCLUDE_PATH_PAINEL ?>recuperar-senha">Esqueceu a senha?</a></p>
				<p><a href="<?php echo INCLUDE_PATH_PAINEL ?>cadastro-aluno">Primeiro acesso?</a></p>
			</div>

			<input type="hidden" name="token" id="token">

			<div class="clear"></div>
		</form>

		<div class="termos-google">
			<p><a href="<?php echo INCLUDE_PATH; ?>guia/" id="help">Dúvidas?</a></p>
			<p>Este site é protegido pelo reCAPTCHA e pelo Google.
	    	<a href="https://policies.google.com/privacy" target="_blanck">Política de privacidade</a> e
	    	<a href="https://policies.google.com/terms" target="_blanck">Termos de serviço</a> se aplicam.</p>
    	</div>
	</div>
	<script>
		grecaptcha.ready(function() {
			grecaptcha.execute('<?php echo RECAPTCHA_PUBLIC_KEY;?>', {action: 'login'}).then(function(token) {
					console.log(token)
					document.getElementById("token").value = token;
			});
		});

		setInterval(function(){ 
			grecaptcha.ready(function() {
				grecaptcha.execute('<?php echo RECAPTCHA_PUBLIC_KEY;?>', {action: 'login'}).then(function(token) {
					console.log(token)
					document.getElementById("token").value = token;
				});
			});
		}, 90 * 1000);
  	</script>
</body>
</html>
