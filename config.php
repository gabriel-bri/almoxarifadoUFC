<?php
	// Inicia uma sessão PHP para armazenar variáveis de sessão
	session_start();

	// Define o fuso horário padrão para 'America/Sao_Paulo'
	date_default_timezone_set("America/Sao_Paulo");

	// Função de autocarregamento de classes (autoload)
	$autoload = function($class) {
		// Se a classe for "Email", inclui o arquivo do PHPMailer
		if($class == "Email") {
			require_once "classes/phpmailer/PHPMailerAutoload.php";
		}

		// Classes que requerem arquivos adicionais
		$classesRequeridas = [
			"Comprovante", 
			"RelatorioEstoque", 
			"NadaConstaPDF",
			"RelatorioPedidos"
		];

		// Verifica se a classe requerida está na lista de classes especiais
		if(in_array($class, $classesRequeridas)) {
			// Inclui o arquivo TCPDF para as classes especiais
			require_once('classes/tcpdf/tcpdf.php');
		}
		
		// Inclui o arquivo da classe principal com base no nome da classe fornecido
		include 'classes/' . $class . '.php';
	};

	// Registra a função de autocarregamento definida acima
	spl_autoload_register($autoload);

	//Variáveis do sistema de envio de e-mails.
    define('ENDERECO', '');
	define('USERNAME', '');
	define('SENHA', '');
	define('NAME', 'Almoxarifado - UFC Quixadá.');

    // Email de cópia
    define('EMAIL_COPIA', '');

	// Chaves do recaptcha
    define('RECAPTCHA_PUBLIC_KEY', '6LdFPRAjAAAAALKtqEPvxw4ban7xzjUkbExD2qGO');
    define('RECAPTCHA_PRIVATE_KEY', '6LdFPRAjAAAAACWhBRZNFCifC-6cdaYp-WhPmM1i');

	// Define as constantes para URLs e diretórios relevantes
	define('INCLUDE_PATH', 'http://localhost/almoxarifado/');
	define('INCLUDE_PATH_PAINEL', INCLUDE_PATH . 'painel/');
	define('ICONE_SITE', INCLUDE_PATH . 'assets/img/favicon.ico'); // Ícone do site
	define('BASE_DIR_PAINEL', __DIR__. '/painel/'); // Diretório base do painel
	define("NOME_EMPRESA", "Controle de almoxarifado - UFC Quixadá"); // Nome da empresa

	// Define as credenciais de banco de dados
	define('HOST', 'localhost');
	define('USER', 'root');
	define('PASSWORD', '');
	define('DATABASE', 'almoxarifado');

	// Funções auxiliares para retornar informações específicas com base em índices
	function pegaCargo($indice) {
		return Usuario::$acessos[$indice];
	}

	function tipoEstoque($indice) {
		return Estoque::$estoque[$indice];
	}

	function qualCurso($indice) {
		return Usuario::$cursos[$indice];
	}

	function statusPedido($indice) {
		return Painel::$statusPedido[$indice];
	}

	function statusEmprestimo($indice) {
		return Painel::$statusEmprestimo[$indice];
	}

	function statusConta($indice) {
		return Usuario::$statusConta[$indice];
	}

	function emprestimoBloqueado($indice) {
		return Painel::$emprestimoBloqueado[$indice];
	}

	function statusItem($indice) {
		return Estoque::$statusItem[$indice];
	}

	// Função para adicionar a classe 'menu-active' se
	// o parâmetro fornecido corresponder ao URL atual
	function selecionadoMenu($par) {
		$url = explode('/', @$_GET['url'])[0];

		if($url == $par) {
			echo 'class="menu-active"';
		}
	}

	// Verifica se o usuário tem permissão para exibir um determinado menu
	// com base no nível de acesso
	function verificaPermissaoMenu($permissao) {
		if($_SESSION['acesso'] >= $permissao) {
			return;
		} 
		
		else {
			echo 'style="display: none;"';
		}
	}

	// Verifica se o usuário tem permissão para acessar uma determinada página
	// com base no nível de acesso
	// Se não tiver permissão, exibe uma página de permissão negada
	// e termina a execução do script
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
