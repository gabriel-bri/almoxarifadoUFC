<?php
	session_start();
	date_default_timezone_set("America/Sao_Paulo");

	$autoload = function($class) {
		if($class == "Email") {
			require_once "classes/phpmailer/PHPMailerAutoload.php";
		}

		if($class == "Comprovante") {
			require_once('classes/tcpdf/tcpdf.php');
		}
		
		include 'classes/' . $class . '.php';
	};

	spl_autoload_register($autoload);

	//Variáveis do sistema de envio de e-mails.
	define('ENDERECO', 'smtp-mail.outlook.com');
	define('USERNAME', '');
	define('SENHA', '');
	define('NAME', 'Almoxarifado - UFC Quixadá.');
	
	define('INCLUDE_PATH', 'http://localhost/almoxarifado/');
	define('INCLUDE_PATH_PAINEL', INCLUDE_PATH . 'painel/');

	define('BASE_DIR_PAINEL', __DIR__. '/painel/');
	
	//Nome da empresa
	define("NOME_EMPRESA", "Controle de almoxarifado - UFC Quixadá");

	//Conexão banco de dados
	define('HOST', 'localhost');
	define('USER', 'root');
	define('PASSWORD', '');
	define('DATABASE', 'almoxarifado');
	
	function pegaCargo($indice) {
		return Painel::$acessos[$indice];
	}

	function tipoEstoque($indice) {
		return Estoque::$estoque[$indice];
	}

	function qualCurso($indice) {
		return Painel::$cursos[$indice];
	}
	
	function statusPedido($indice) {
		return Painel::$statusPedido[$indice];
	}

	function statusEmprestimo($indice) {
		return Painel::$statusEmprestimo[$indice];
	}

	function selecionadoMenu($par) {
		$url = explode('/', @$_GET['url'])[0];

		if($url == $par) {
			echo 'class="menu-active"';
		}
	}

	function verificaPermissaoMenu($permissao) {
		if($_SESSION['acesso'] >= $permissao) {
			return;
		}

		else {
			echo 'style="display: none;"';
		}
	}

	function verificaPermissaoPagina($permissao) {
		if($_SESSION['acesso'] >= $permissao) {
			return;
		}

		else {
			include('painel/pages/permissao-negada.php');
			die();
		}		
	}
?>