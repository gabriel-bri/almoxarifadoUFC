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

				if($_POST['user'] == '' || $_POST['password'] == '') {
					Painel::alert("erro", "Campos vazios não são permitidos.");
				}

				else {
					$usuario = filter_var($_POST['user'], FILTER_SANITIZE_STRING);
					$senha = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
				
					$sigaa = new SIGAA();
					$sigaa->chamarAPI();
			        $sigaa->passarDados($usuario, $senha);
			        $dadosSIGAA = $sigaa->pegarDados();
		      		if($dadosSIGAA != null) {
				      	if($dadosSIGAA->error) {
				        	Painel::alert("erro", "Dados não encontrados, verifique o login e/ou a senha.");
				        }

				        else if($dadosSIGAA->cadeiras[2]->local != 'Campus de Quixadá'){
				        	Painel::alert("erro", "Este sistema é de uso exclusivo para alunos da UFC Quixadá, caso necessite entre em contato com o administrador.");	
				        }

				        else if(Usuario::matriculaJaCadastrada($dadosSIGAA->matricula)) {
				        	Painel::alert("erro", "Matrícula já cadastrada no sistema");	
				        }

				        else {

		   					$dadosSIGAA->curso = str_replace(substr($dadosSIGAA->curso, -4), '', $dadosSIGAA->curso);

							$arrayNomeCurso = explode(" ", $dadosSIGAA->curso);

							if(count($arrayNomeCurso) == 3) {
							    $dadosSIGAA->curso = ucfirst(mb_strtolower($arrayNomeCurso[0])) . ' ' . strtolower($arrayNomeCurso[1]) . ' ' . ucfirst(mb_strtolower($arrayNomeCurso[2]));
							}

							if(count($arrayNomeCurso) == 2) {
							    $dadosSIGAA->curso = ucfirst(mb_strtolower($arrayNomeCurso[0])) . ' ' . ucfirst(mb_strtolower($arrayNomeCurso[1]));
							}

							$dadosAluno = [
								'matricula' => $dadosSIGAA->matricula,
								'curso' => array_search($dadosSIGAA->curso, Painel::$cursos)
							];

				        	if(!isset($_SESSION['continuar_cadastro'])) {
								$_SESSION['continuar_cadastro']  = true;
								$_SESSION['dados_aluno'] = $dadosAluno;
							}

				        	Painel::alert("sucesso", "Dados encontrados, o processo de cadastro irá continuar agora.");
				        	redirect();
				        }
		      		}

		      		else {
		      			Painel::alert("erro", "Problemas ao conectar com a API externa, tente novamente, se o erro persistir contate o administrador");
		      		}
				}

			}
		?>
		<h2>Entre com seu login e senha do SIGAA para continuar o cadastro</h2>
		<form method="post" action="">
			<input type="text" name="user" placeholder="Login...">
			<input type="password" name="password" placeholder="Senha...">
			<div class="form-group-login left">
				<input type="submit" name="acao" value="Continuar">
			</div>

			<div class="clear"></div>
		</form>

		<div class="termos-google">
			<p>O site não armazena seu login e senha do SIGAA, esses dados são usados apenas para validação de informações.</p>
    	</div>
	</div>
</body>
</html>