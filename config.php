<?php
	session_start();
	date_default_timezone_set("America/Sao_Paulo");
	$autoload = function($class) {
		if($class == "Email") {
			require_once "classes/phpmailer/PHPMailerAutoload.php";
		}

		include 'classes/' . $class . '.php';
	};


	spl_autoload_register($autoload);

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