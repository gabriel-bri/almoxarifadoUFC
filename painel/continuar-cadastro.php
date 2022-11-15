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
			if(isset($_SESSION['continuar_cadastro'])) {
				if(isset($_POST['acao'])) {
					$login = filter_var($_POST['user'], FILTER_SANITIZE_STRING);
					$nome = filter_var($_POST['nome'], FILTER_SANITIZE_STRING);
					$sobrenome = filter_var($_POST['sobrenome'], FILTER_SANITIZE_STRING);
					$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
					$senha = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
					$dominio = explode("@", $email);

					if($login == '') {
						Painel::alert('erro', 'O login está vazio');
					}

					else if($nome == '') {
						Painel::alert('erro', 'O nome está vazio');
					}

					else if($sobrenome == '') {
						Painel::alert('erro', 'O sobrenome está vazio');

					}

					else if($email == '') {
						Painel::alert('erro', 'O e-mail está vazio');

					}

					else if($senha == '') {
						Painel::alert('erro', 'A senha está vazia');
					}

					else if($dominio[1] != "alu.ufc.br"){
						Painel::alert('erro', 'Alunos podem usar apenas e-mail @alu.ufc.br');
					}

					else {
						if(Usuario::userExist($login)) {
							Painel::alert('erro', 'Selecione um login diferente');	
						}

						else {
							$usuario = new Usuario();

							$usuario->cadastrarUsuario($login, $senha, $nome, $sobrenome, $email, "", 1, $_SESSION['dados_aluno']['matricula'], $_SESSION['dados_aluno']['curso']);
							Painel::alert("sucesso", "Usuário cadastrado com sucesso. Um e-mail de confirmação foi enviado. Redirecionando para o login.");
							unset($_SESSION['continuar_cadastro']);
							unset($_SESSION['dados_aluno']);
							redirectLogin();
						}
					}
				}
		?>
		<h2>Verifique os seus dados para continuar:</h2>
		<form method="post" action="">
			<input type="text" name="user" placeholder="Login...">
			<input type="password" name="password" placeholder="Senha...">
			<input type="text" name="nome" placeholder="Nome:">
			<input type="text" name="sobrenome" placeholder="Sobrenome">
			<input type="email" name="email" placeholder="E-mail institucional...">

			<div class="form-group-login left">
				<input type="submit" name="acao" value="Concluir">
			</div>

			<div class="clear"></div>
		</form>

		<div class="termos-google">
			<p>A partir de agora as informações pedidas são para acesso a este sistema e sem nenhuma relaçao com o SIGAA.</p>
    	</div>
    	<?php }

    		else {
    			Painel::alert('erro', 'Você não passou pelo processo de cadastro inicial. Redirecionando.');
    			redirect();
    		}
    	?>
	</div>
</body>
</html>