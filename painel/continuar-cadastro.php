<?php  
	include "../config.php";
?>

<?php
    function redirect() {
		echo "<meta http-equiv='refresh' content='5;url=" . INCLUDE_PATH_PAINEL . "cadastro-aluno'>";
    }

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
	<title>Continuar cadastro.</title>
	<link rel="stylesheet" href="<?php echo INCLUDE_PATH_PAINEL ?>css/style.css">
 	<link href="<?php echo ICONE_SITE ?>" rel="icon">
  	<link href="<?php echo ICONE_SITE ?>" rel="apple-touch-icon">
</head>
<body>
	<div class="box-login">
		<?php  
			if(!isset($_SESSION['continuar_cadastro'])) {
				Painel::alert('erro', 'Você não passou pelo processo de cadastro inicial. Redirecionando.');
    			redirect();
				return;
			}
			if(isset($_POST['acao'])) {
				Usuario::validarEntradasAutoCadastro($_POST);
			}
		?>
		<h2>Verifique os seus dados para continuar:</h2>
		<form method="post" action="">
			<input type="text" name="user" placeholder="Login..." value="<?php echo isset($_POST['user']) ? htmlentities($_POST['user']) : ''; ?>">
			<input type="password" name="password" placeholder="Senha..." value="<?php echo isset($_POST['password']) ? htmlentities($_POST['password']) : ''; ?>">
			<input type="text" name="nome" placeholder="Nome:" value="<?php echo isset($_POST['nome']) ? htmlentities($_POST['nome']) : ''; ?>">
			<input type="text" name="sobrenome" placeholder="Sobrenome" value="<?php echo isset($_POST['sobrenome']) ? htmlentities($_POST['sobrenome']) : ''; ?>">
			<input type="email" name="email" placeholder="E-mail institucional..." value="<?php echo isset($_POST['email']) ? htmlentities($_POST['email']) : ''; ?>">

			<div class="form-group-login left">
				<input type="submit" name="acao" value="Concluir">
			</div>

			<div class="clear"></div>
		</form>

		<div class="termos-google">
			<p>A partir de agora as informações pedidas são para acesso a este sistema e sem nenhuma relaçao com o SIGAA.</p>
    	</div>
	</div>
</body>
</html>