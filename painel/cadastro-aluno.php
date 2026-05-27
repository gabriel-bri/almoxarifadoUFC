<?php  
	include "../config.php";
?>

<?php
    function redirect() {
	echo "<meta http-equiv='refresh' content='5;url=" . INCLUDE_PATH_PAINEL . "continuar-cadastro'>";
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="<?php echo INCLUDE_PATH_PAINEL;?>css/font-awesome.css">
	<title>Cadastro próprio do usuário.</title>
	<link rel="stylesheet" href="<?php echo INCLUDE_PATH_PAINEL ?>css/style.css">
 	<link href="<?php echo ICONE_SITE ?>" rel="icon">
  	<link href="<?php echo ICONE_SITE ?>" rel="apple-touch-icon">
</head>
<body>
	<div class="box-login">
		<?php 
			if(isset($_POST['acao'])) {
				LdapUFC::validarEntradas($_POST);
			}
		?>
		<h2>Entre com seu CPF da BASE CENTRALIZADA para continuar o cadastro</h2>
		<form method="post" action="">
			<input type="text" name="user" placeholder="CPF..." minlength="11" maxlength="11">
			<div class="form-group-login left">
				<input type="submit" name="acao" value="Continuar">
			</div>

			<div class="clear"></div>
		</form>

		<div class="termos-google">
			<p>O site não armazena seu login da BASE CENTRALIZADA, esses dados são usados apenas para validação de informações.</p>
    	</div>
	</div>
</body> 
</html>
