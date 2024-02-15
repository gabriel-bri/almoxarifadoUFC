<?php
	require "../config.php";
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="<?php echo INCLUDE_PATH_PAINEL;?>css/font-awesome.css">
	<title>Validar Nada Consta</title>
	<link rel="stylesheet" href="<?php echo INCLUDE_PATH_PAINEL ?>css/style.css">
 	<link href="<?php echo ICONE_SITE ?>" rel="icon">
  	<link href="<?php echo ICONE_SITE ?>" rel="apple-touch-icon">
</head>
<body>
	<div class="box-login">
		<?php
			if(isset($_GET['acao'])) {
				NadaConsta::validarNadaConsta($_GET);
			}
		?>

		<h2>Validar Documento.</h2>
		<form method="get" action="">
			<input type="text" name="codigo_seguranca" placeholder="Digite o código de 30 caracteres." value="<?php echo isset($_GET['codigo_seguranca']) ? htmlentities($_GET['codigo_seguranca']) : ''; ?>">
			<div class="form-group-login left">
				<input type="submit" name="acao" value="Validar">
			</div>
		</form>
	</div>
</body>
</html>