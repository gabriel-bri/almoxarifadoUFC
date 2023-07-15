<?php 
	class Painel
	{
		
		public static $acessos = [
		'1' => 'Aluno',
		'2' => 'Administrador'];

		public static $cursos = [
		'EC' => 'Engenharia da Computação',
		'CC' => 'Ciência da Computação',
		'SI' => 'Sistemas de Informação',
		'RC' => 'Redes de Computadores',
		'DD' => 'Design Digital', 
		'ES' => 'Engenharia de Software'];

		public static $statusPedido = [
		'0' => 'NEGADO',
		'1' => 'APROVADO'];

		public static $statusEmprestimo = [
		'0' => 'EM ANDAMENTO',
		'1' => 'FINALIZADO'];

		public static function logado() {
			return isset($_SESSION['login']) ? true : false;
		}

		public static function logout() {
			setcookie('lembrar', true, time() - 1, '/');
			session_destroy();
			header("Location: " . INCLUDE_PATH_PAINEL);
		}

		public static function carregarPagina() {
			if(isset($_GET['url'])) {
				$url = explode('/', $_GET['url']);
				if(file_exists('pages/' . $url[0] . '.php')) {
					include 'pages/' . $url[0] . '.php';
				}

				else {
					header("Location: " . INCLUDE_PATH_PAINEL);
				}
			}

			else {
				include 'pages/home.php';
			}
		}

		public static function alert($tipo, $mensagem) {
			if($tipo == 'sucesso') {
				echo '<div class="box-alert sucesso"><i class="fa fa-check"></i> ' . $mensagem . '</div>';
			}

			else if($tipo == 'erro') {
				echo '<div class="box-alert erro"><i class="fa fa-times"></i> ' . $mensagem . '</div>';		
			}
		}

		// public static function imagemValida($imagem) {
		// 	if($imagem['type'] == 'image/jpeg' || $imagem['type'] == 'image/jpg' || $imagem['type'] == 'image/png') {

		// 		$tamanho = intval($imagem['size'] / 1024);
		// 		if($tamanho < 300) {
		// 			return true;	
		// 		}

		// 		else {
		// 			return false;
		// 		}
				
		// 	}

		// 	else {
		// 		return false;
		// 	}
		// }

		public static function imagemValida($imagem) {

			$verficador_mimes = new finfo(FILEINFO_MIME_TYPE);
			$tipo_arquivo = $verficador_mimes->file($imagem['tmp_name']);

			$mimes_permitidos = array("image/jpeg", "image/jpg", "image/png");

			$extensoes_permitidas = array("jpeg", "jpg", "png");
   			$imagem_array = explode(".", filter_var($imagem['name'], FILTER_SANITIZE_STRING));

			$extensao = strtolower($imagem_array[count($imagem_array) - 1]);

			if(in_array($extensao, $extensoes_permitidas) && in_array($tipo_arquivo, $mimes_permitidos)) {
				
				$tamanho = intval($imagem['size'] / 1024);
				
				if($tamanho < 300) {
					return true;	
				}

				else {
					return false;
				}
				
			}

			else {
				return false;
			}
		}

		public static function retornaTipoImagem($imagem) {

			$verficador_mimes = new finfo(FILEINFO_MIME_TYPE);
			$tipo_arquivo = $verficador_mimes->file($imagem['tmp_name']);

			$mimes_permitidos = array("image/jpeg", "image/jpg", "image/png");

			$extensoes_permitidas = array("jpeg", "jpg", "png");
   			$imagem_array = explode(".", filter_var($imagem['name'], FILTER_SANITIZE_STRING));

			$extensao = strtolower($imagem_array[count($imagem_array) - 1]);

			if(in_array($extensao, $extensoes_permitidas) && in_array($tipo_arquivo, $mimes_permitidos)){
				return $extensao;
			}
		}

		public static function uploadFile($file) {
			$formatoArquivo = explode('.', $file['name']);
			$imagemNome = uniqid() . '.' . $formatoArquivo[count($formatoArquivo) - 1];

			if(Painel::retornaTipoImagem($file) == 'jpeg' ) {
            	$img = imagecreatefromjpeg($file['tmp_name'] );
            	imagejpeg($img, BASE_DIR_PAINEL . '/uploads/' . $imagemNome, 100);
        	}

        	else if(Painel::retornaTipoImagem($file) == 'png') {
            	$img = imagecreatefrompng($file['tmp_name']);
            	imagepng($img, BASE_DIR_PAINEL . '/uploads/' . $imagemNome, 0);
        	}

        	else {
        		$img = imagecreatefromjpeg($file['tmp_name'] );
            	imagejpeg($img, BASE_DIR_PAINEL . '/uploads/' . $imagemNome, 100);	
        	}

        	imagedestroy($img);
			
			return $imagemNome;
		}

		public static function deleteFile($file) {
			@unlink('uploads/'.$file);
		}

		public static function deleteComprovante($codigoPedido) {
			@unlink(BASE_DIR_PAINEL . '/comprovantes/' . $codigoPedido . '.pdf');
		}

		public static function selectAll($tabela, $start = null, $end = null) {
			if($start == null and $end == null) {
				$sql = Mysql::conectar()->prepare("SELECT * FROM `$tabela` ORDER BY order_id ");	
			}

			else {
				$sql = Mysql::conectar()->prepare("SELECT * FROM `$tabela` ORDER BY order_id ASC LIMIT $start, $end");
			}
			$sql->execute();
			return $sql->fetchAll();
		}

		public static function emprestimosPendentes() {
			try {
				$sql = Mysql::conectar()->prepare("SELECT COUNT(aprovado) AS emprestimosPendentes FROM pedido_detalhes WHERE aprovado = 0 AND finalizado = 0;");
				$sql->execute();
				$dados = $sql->fetch(PDO::FETCH_ASSOC);
				return $dados['emprestimosPendentes'];
			}

			catch(Exception $e) {
				return "Erro ao consultar o banco de dados.";
			}
		}

		public static function emprestimosParaDevolver() {
			try {
				$sql = Mysql::conectar()->prepare("SELECT COUNT(aprovado) AS emprestimosParaDevolver FROM pedido_detalhes WHERE aprovado = 1 AND finalizado = 0;");
				$sql->execute();
				$dados = $sql->fetch(PDO::FETCH_ASSOC);
				return $dados['emprestimosParaDevolver'];
			}

			catch(Exception $e) {
				return "Erro ao consultar o banco de dados.";
			}
		}

		public static function emprestimosFinalizados() {
			try {
				$sql = Mysql::conectar()->prepare("SELECT COUNT(*) AS emprestimosFinalizados FROM pedido_detalhes WHERE finalizado = 1 AND data_finalizado = ?");
				// 2001-03-10 (the MySQL DATETIME format)
				$dataHoje = date("Y-m-d"); 

				$sql->execute(array($dataHoje));				
				$dados = $sql->fetch(PDO::FETCH_ASSOC);
				
				return $dados['emprestimosFinalizados'];
			}

			catch(Exception $e) {
				return "Erro ao consultar o banco de dados.";
			}
		}
		public static function googleRecaptcha($token) {
			$url = "https://www.google.com/recaptcha/api/siteverify";
			$data = [
				'secret' => RECAPTCHA_PRIVATE_KEY,
				'response' => $token,
			];

			$options = array(
				'http' => array(
				  'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				  'method'  => 'POST',
				  'content' => http_build_query($data)
				)
			  );

			$context  = stream_context_create($options);
			
			$response = file_get_contents($url, false, $context);

			$res = json_decode($response, true);

			return $res['success'];
		}

		public static function configurarCookies($usuario) {
			setcookie('lembrar', true, time() + (60 * 60 * 24), '/', null, null, true);
			setcookie('user', $usuario, time() + (60 * 60 * 24), '/', null, null, true);
			$token_cookie = bin2hex(random_bytes(30));
			setcookie('token', $token_cookie, time() + (60 * 60 * 24), '/', null, null, true);
			$_SESSION['token_lembrar'] = password_hash($token_cookie, PASSWORD_BCRYPT);
		}

		public static function configurarCookieLembrar() {
			if(password_verify($_COOKIE['token'], $_SESSION['token_lembrar'])){
				$usuario = $_COOKIE['usuario'];	
				try {
					$sql = Mysql::conectar()->prepare("SELECT * FROM `usuarios` WHERE usuario = ? LIMIT 1");
	
					$sql->execute(array($usuario));
		
					if($sql->rowCount() == 1) {
						$info = $sql->fetch();
		
						$_SESSION['id'] = $info['id'];
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

				catch(Exception $e) {
					Painel::alert("erro", "Erro ao se conectar ao banco de dados.");
				}
			}
		}
		    
		public static function login($usuario, $senha, $token) {
			if(Painel::googleRecaptcha($token)) {
				try {
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
								Painel::configurarCookies($usuario);
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

				catch(Exception $e) {
					Painel::alert("erro", "Erro ao conectar o banco de dados");
				}
			}

			else {
				Painel::alert("erro", "Ops! Você é realmente um humano? Nosso sistema acha que você é um robô, tente novamente.");
			}
		}

		public static function redirect($url) {
			echo '<script> location.href="'. $url .'"</script>';
			die();
		}

		public static function update($arr) {
			$certo = true;
			$first = false;
			$nome_tabela = $arr['nome_tabela'];
			$query = "UPDATE `$nome_tabela` SET ";
			foreach ($arr as $key => $value) {
				$nome = $key;
				$valor = $value;
				if($nome == 'acao' || $nome == 'nome_tabela' || $nome == 'id') {
					continue;
				}

				if($value == '') {
					$certo = false;
					break;
				}


				if($first == false) {
					$first = true;
					$query .= "$nome = ?";					
				}

				else {
					$query .= ", $nome = ?";	
				}
				$parametros[] = $value;
			}

			if($certo == true) {
				$parametros[] = $arr['id'];
				$sql = Mysql::conectar()->prepare($query . 'WHERE id = ?');
				$sql->execute($parametros);
			}
			return $certo;
		}

		public static function orderItem($tabela, $orderType, $idItem) {
			if($orderType == 'up') {
				$infoItemAtual = Painel::select($tabela, 'id = ?', array($idItem));
				$order_id = $infoItemAtual['order_id'];
				$itemBefore = Mysql::conectar()->prepare("SELECT * FROM `$tabela` WHERE order_id < $order_id  ORDER BY order_id DESC LIMIT 1");
				$itemBefore->execute();
				if($itemBefore->rowCount() == 0)
					return;
				$itemBefore  = $itemBefore->fetch();
				Painel::update(array('nome_tabela'=>$tabela, 'id' => $itemBefore['id'], 'order_id' => $infoItemAtual['order_id']));
				Painel::update(array('nome_tabela'=>$tabela, 'id' => $infoItemAtual['id'], 'order_id' => $itemBefore['order_id']));
			}

			else if($orderType == 'down') {
				$infoItemAtual = Painel::select($tabela, 'id = ?', array($idItem));
				$order_id = $infoItemAtual['order_id'];
				$itemBefore = Mysql::conectar()->prepare("SELECT * FROM `$tabela` WHERE order_id > $order_id  ORDER BY order_id ASC LIMIT 1");
				$itemBefore->execute();
				if($itemBefore->rowCount() == 0)
					return;
				$itemBefore  = $itemBefore->fetch();
				Painel::update(array('nome_tabela'=>$tabela, 'id' => $itemBefore['id'], 'order_id' => $infoItemAtual['order_id']));
				Painel::update(array('nome_tabela'=>$tabela, 'id' => $infoItemAtual['id'], 'order_id' => $itemBefore['order_id']));
			}
		}
	}
?>