  <?php 
	class Painel {
		// Define arrays estáticos para representar diferentes status de pedidos, 
		// empréstimos e bloqueio de empréstimos.
		public static $statusPedido = [
			'0' => 'NEGADO',
			'1' => 'APROVADO'
		];

		public static $statusEmprestimo = [
			'0' => 'EM ANDAMENTO',
			'1' => 'FINALIZADO'
		];

		public static $emprestimoBloqueado = [
			'0' => 'AUTORIZADO',
			'1' => 'BLOQUEADO'
		];

		// Verifica se o usuário está logado na sessão.
		public static function logado() {
			return isset($_SESSION['login']) ? true : false;
		}

		// Realiza o logout do usuário, destruindo a sessão
		// e redirecionando para a página inicial do painel.
		public static function logout() {
			setcookie('lembrar', true, time() - 1, '/');
			session_destroy();
			header("Location: " . INCLUDE_PATH_PAINEL);
		}

		// Carrega a página correspondente à URL fornecida. 
		// Se a página não existir, redireciona para a página inicial do painel.
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

		// Exibe um alerta na página com base no tipo ('sucesso' ou 'erro') e a mensagem fornecida.
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

		// Verifica se a imagem é válida com base na extensão, tipo MIME e tamanho.
		public static function imagemValida($imagem) {
			// Cria um objeto finfo para verificar o tipo MIME do arquivo.
			$verficador_mimes = new finfo(FILEINFO_MIME_TYPE);
			$tipo_arquivo = $verficador_mimes->file($imagem['tmp_name']);

			// Tipos MIME permitidos e extensões permitidas.
			$mimes_permitidos = array("image/jpeg", "image/jpg", "image/png");
			$extensoes_permitidas = array("jpeg", "jpg", "png");

			// Obtém a extensão do arquivo da sua nomeação.
			$imagem_array = explode(".", filter_var($imagem['name'], FILTER_SANITIZE_STRING));
			$extensao = strtolower($imagem_array[count($imagem_array) - 1]);

			// Verifica se a extensão e o tipo MIME estão na lista permitida
			//  e se o tamanho é menor que 300 KB.
			if (in_array($extensao, $extensoes_permitidas) && in_array($tipo_arquivo, $mimes_permitidos)) {
				$tamanho = intval($imagem['size'] / 1024); // Tamanho em KB.
				// Retorna verdadeiro se o tamanho da imagem for menor que 300KB, caso contrário, retorna falso.
				return $tamanho < 300;
			} 
			
			else {
				return false; // Tipo de arquivo ou extensão não permitidos.
			}
		}

		// Retorna a extensão da imagem com base no nome do arquivo.
		public static function retornaTipoImagem($imagem) {
			// Lógica semelhante ao método imagemValida para determinar a extensão da imagem.
			// Retorna a extensão se a imagem for válida, caso contrário, não retorna nada.
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
		
		// Faz o upload do arquivo de imagem para o diretório de uploads, com um nome único.
		public static function uploadFile($file) {
			// Obtém o formato do arquivo e gera um nome único para a imagem.
    		// Com base na extensão, cria uma cópia da imagem no diretório de uploads
			// com o nome único.
    
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

			// Retorna o nome único da imagem.
			return $imagemNome;
		}

		// Exclui a imagem do usuário no diretório de uploads
		//  quando é feita uma atualização de imagem 
		public static function deleteFile($file) {
			@unlink('uploads/'.$file);
		}

		public static function deleteComprovante($codigoPedido) {
			// Exclui o comprovante de empréstimo correspondente 
			// ao código do pedido do diretório de comprovantes.
			// O mesmo fica armazenado dentro de uma pasta chamada 'comprovantes',
			// e deve ser deletado após ser enviado por e-mail para o usuário.
			@unlink(BASE_DIR_PAINEL . '/comprovantes/' . $codigoPedido . '.pdf');
		}

		public static function deleteNadaConsta(NadaConsta $nadaConsta) {
			// Exclui o comprovante de Nada Consta correspondente 
			// a matrícula do usuário que gerou o arquivo do diretório de declaracoes.
			// O mesmo fica armazenado dentro de uma pasta chamada 'declaracoes',
			// e deve ser deletado após ser enviado por e-mail para o usuário.
			@unlink(BASE_DIR_PAINEL . "/declaracoes/declaracao_nada_consta_{$nadaConsta->usuario->getMatricula()}.pdf");
		}

		# Função desativada.
		// public static function selectAll($tabela, $start = null, $end = null) {
		// 	if($start == null and $end == null) {
		// 		$sql = Mysql::conectar()->prepare("SELECT * FROM `$tabela` ORDER BY order_id ");	
		// 	}

		// 	else {
		// 		$sql = Mysql::conectar()->prepare("SELECT * FROM `$tabela` ORDER BY order_id ASC LIMIT $start, $end");
		// 	}
		// 	$sql->execute();
		// 	return $sql->fetchAll();
		// }

		/**
		 * Retorna o número de empréstimos pendentes no banco de dados.
		 * @return int O número de empréstimos pendentes.
		 */
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

		/**
		 * Retorna o número de empréstimos a serem devolvidos no banco de dados.
		 * @return int O número de empréstimos a serem devolvidos.
		 */
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

		/**
		 * Retorna o número de empréstimos finalizados no banco de dados.
		 * @return int O número de empréstimos finalizados.
		 */
        public static function emprestimosFinalizados() {
            try {
                $sql = Mysql::conectar()->prepare("
                    SELECT COUNT(*) AS emprestimosFinalizados 
                    FROM pedido_detalhes 
                    WHERE aprovado = 1 AND finalizado = 1
                    AND DATE(data_finalizado) = ?
                ");

                $dataHoje = date("Y-m-d");
                $sql->execute(array($dataHoje));
                $dados = $sql->fetch(PDO::FETCH_ASSOC);
                return $dados['emprestimosFinalizados'];
            }

            catch(Exception $e) {
                return "Erro ao consultar o banco de dados.";
            }
        }

        /**
		 * Verifica se o token do Google reCAPTCHA é válido.
		 * @param string $token O token do Google reCAPTCHA a ser verificado.
		 * @return bool Retorna true se o token for válido, caso contrário, retorna false.
		 */
		public static function googleRecaptcha($token) {
			// URL para a verificação do token do Google reCAPTCHA.
			$url = "https://www.google.com/recaptcha/api/siteverify";

			// Dados para enviar na solicitação POST.
			$data = [
				'secret' => RECAPTCHA_PRIVATE_KEY, // Chave secreta do reCAPTCHA.
				'response' => $token, // Token fornecido pelo usuário.
			];

			// Opções para a solicitação POST.
			$options = array(
				'http' => array(
					'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
					'method'  => 'POST',
					'content' => http_build_query($data) // Constrói os dados como uma string de consulta.
				)
			);

			// Cria o contexto para a solicitação POST.
			$context  = stream_context_create($options);

			// Envia a solicitação POST e obtém a resposta.
			$response = file_get_contents($url, false, $context);

			// Decodifica a resposta JSON.
			$res = json_decode($response, true);

			// Retorna true se a verificação for bem-sucedida, caso contrário, retorna false.
			return $res['success'];
		}

		// Faz as configurações dos cookies de sessão caso o usuário tenha 
		// marcado a caixa "Lembrar-me"
		public static function configurarCookies($usuario) {
			setcookie('lembrar', true, time() + (60 * 60 * 24), '/', null, null, true);
			setcookie('user', $usuario, time() + (60 * 60 * 24), '/', null, null, true);
			$token_cookie = bin2hex(random_bytes(30));
			setcookie('token', $token_cookie, time() + (60 * 60 * 24), '/', null, null, true);
			$_SESSION['token_lembrar'] = password_hash($token_cookie, PASSWORD_BCRYPT);
		}

		/**
		 * Configura a sessão com base nas informações armazenadas em um cookie de "lembrar".
		 * Verifica se o token do cookie corresponde ao token de "lembrar" da sessão e,
		 * se corresponder, atualiza a sessão com os dados do usuário armazenados no cookie.
		 * @return void
		 */
		public static function configurarCookieLembrar() {
			// Verifica se o token do cookie corresponde ao token de "lembrar" da sessão.
			if(password_verify($_COOKIE['token'], $_SESSION['token_lembrar'])) {
				$usuario = $_COOKIE['usuario'];	

				try {
					// Consulta o banco de dados para obter informações do usuário com base no nome de usuário armazenado no cookie.
					$sql = Mysql::conectar()->prepare("SELECT * FROM `usuarios` WHERE usuario = ? LIMIT 1");
					$sql->execute(array($usuario));
					
					// Se encontrar um usuário, atualiza a sessão com as informações do usuário.
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
						
						// Se o usuário tem acesso de administrador e está bloqueado, define a flag 'is_bloqueado' na sessão.
						if($info['acesso'] == 1 && $info['is_bloqueado'] == 1) {
							$_SESSION['is_bloqueado'] = $info['is_bloqueado'];
						}

						// Redireciona para a página inicial do painel e encerra o script.
						header('Location: ' . INCLUDE_PATH_PAINEL);
						die();
					}
				} 
				
				catch(Exception $e) {
					// Exibe um alerta de erro se houver problemas ao conectar ao banco de dados.
					Painel::alert("erro", "Erro ao se conectar ao banco de dados.");
				}
			}
		}


		/**
		 * Atualiza os dados da sessão com as informações do usuário fornecido.
		 * @param Usuario $usuario O objeto do usuário contendo as informações a serem atualizadas na sessão.
		 * @return void
		 */
		public static function atualizarSessao(Usuario $usuario) {
			// Atualiza os dados da sessão com as informações do usuário fornecido.
			$_SESSION['nome'] = $usuario->getNome();
			$_SESSION['sobrenome'] = $usuario->getSobrenome();
			$_SESSION['email'] = $usuario->getEmail();
			$_SESSION['fotoperfil'] = $usuario->getFotoPerfil();
		}

		/**
		 * Realiza o processo de login do usuário.
		 * Verifica o token do Google reCAPTCHA, consulta o banco de dados para verificar as credenciais do usuário,
		 * e inicia uma sessão se as credenciais estiverem corretas e a conta estiver ativada.
		 * Se selecionada a opção "lembrar", configura cookies para manter o usuário logado.
		 * @param string $usuario O nome de usuário fornecido pelo usuário.
		 * @param string $senha A senha fornecida pelo usuário.
		 * @param string $token O token do Google reCAPTCHA.
		 * @return void
		 */
		public static function login($usuario, $senha, $token) {
			// Verifica o token do Google reCAPTCHA.
			if(Painel::googleRecaptcha($token) == false) {
				// Exibe uma mensagem de erro se o token do Google reCAPTCHA não for válido.
				Painel::alert("erro", "Ops! Você é realmente um humano? Nosso sistema acha que você é um robô, tente novamente.");
				return;
			}

			try {
				// Consulta o banco de dados para obter informações do usuário 
				// com base no nome de usuário fornecido.
				$sql = Mysql::conectar()->prepare("SELECT * FROM `usuarios` WHERE usuario = ? LIMIT 1");
				$sql->execute(array($usuario));
				$info = $sql->fetch();
				if($sql->rowCount() == 0 || password_verify($senha, $info['senha']) == false) {				
					// Exibe uma mensagem de erro se as credenciais estiverem incorretas.
					Painel::alert("erro", "Usuário e/ou senha incorretos");
					return;
				}

				if($info['is_ativada'] == 0) {
					// Exibe uma mensagem de erro se a conta não estiver ativada.
					Painel::alert("erro", "Conta não ativada, verifique o seu e-mail");
					return;
				}

				// Se encontrar um usuário e a senha estiver correta, 
				// e a conta estiver ativada, inicia uma sessão.
				$_SESSION['login'] = true;
				$_SESSION['id'] = $info['id'];
				$_SESSION['usuario'] = $usuario;
				$_SESSION['senha'] = $senha;
				$_SESSION['nome'] = $info['nome'];
				$_SESSION['sobrenome'] = $info['sobrenome'];
				$_SESSION['email'] = $info['email'];
				$_SESSION['fotoperfil'] = $info['fotoperfil'];
				$_SESSION['acesso'] = $info['acesso'];
				
				// Se o usuário é administrador ou moderador e está bloqueado, 
				// define a flag 'is_bloqueado' na sessão.
				if(($info['acesso'] == 1 || $info['acesso'] == 2) && $info['is_bloqueado'] == 1) {
					$_SESSION['is_bloqueado'] = $info['is_bloqueado'];
				}

				// Se a opção "lembrar" foi selecionada, configura cookies 
				// para manter o usuário logado.
				if(isset($_POST['lembrar'])) {
					Painel::configurarCookies($usuario);
				}
		
				// Redireciona para a página inicial do painel e encerra o script.
				header('Location: ' . INCLUDE_PATH_PAINEL);
				die();
			} 
			
			catch(Exception $e) {
				// Exibe um alerta de erro se houver problemas ao conectar ao banco de dados.
				Painel::alert("erro", "Erro ao conectar o banco de dados");
			}
		}


		// Redireciona o usuário com base na URL passada.
		public static function redirect($url) {
			echo '<script> location.href="'. $url .'"</script>';
			die();
		}

		# Função não utilizada.
		// 	public static function update($arr) {
		// 		$certo = true;
		// 		$first = false;
		// 		$nome_tabela = $arr['nome_tabela'];
		// 		$query = "UPDATE `$nome_tabela` SET ";
		// 		foreach ($arr as $key => $value) {
		// 			$nome = $key;
		// 			$valor = $value;
		// 			if($nome == 'acao' || $nome == 'nome_tabela' || $nome == 'id') {
		// 				continue;
		// 			}

		// 			if($value == '') {
		// 				$certo = false;
		// 				break;
		// 			}


		// 			if($first == false) {
		// 				$first = true;
		// 				$query .= "$nome = ?";					
		// 			}

		// 			else {
		// 				$query .= ", $nome = ?";	
		// 			}
		// 			$parametros[] = $value;
		// 		}

		// 		if($certo == true) {
		// 			$parametros[] = $arr['id'];
		// 			$sql = Mysql::conectar()->prepare($query . 'WHERE id = ?');
		// 			$sql->execute($parametros);
		// 		}
		// 		return $certo;
		// 	}

		// 	public static function orderItem($tabela, $orderType, $idItem) {
		// 		if($orderType == 'up') {
		// 			$infoItemAtual = Painel::select($tabela, 'id = ?', array($idItem));
		// 			$order_id = $infoItemAtual['order_id'];
		// 			$itemBefore = Mysql::conectar()->prepare("SELECT * FROM `$tabela` WHERE order_id < $order_id  ORDER BY order_id DESC LIMIT 1");
		// 			$itemBefore->execute();
		// 			if($itemBefore->rowCount() == 0)
		// 				return;
		// 			$itemBefore  = $itemBefore->fetch();
		// 			Painel::update(array('nome_tabela'=>$tabela, 'id' => $itemBefore['id'], 'order_id' => $infoItemAtual['order_id']));
		// 			Painel::update(array('nome_tabela'=>$tabela, 'id' => $infoItemAtual['id'], 'order_id' => $itemBefore['order_id']));
		// 		}

		// 		else if($orderType == 'down') {
		// 			$infoItemAtual = Painel::select($tabela, 'id = ?', array($idItem));
		// 			$order_id = $infoItemAtual['order_id'];
		// 			$itemBefore = Mysql::conectar()->prepare("SELECT * FROM `$tabela` WHERE order_id > $order_id  ORDER BY order_id ASC LIMIT 1");
		// 			$itemBefore->execute();
		// 			if($itemBefore->rowCount() == 0)
		// 				return;
		// 			$itemBefore  = $itemBefore->fetch();
		// 			Painel::update(array('nome_tabela'=>$tabela, 'id' => $itemBefore['id'], 'order_id' => $infoItemAtual['order_id']));
		// 			Painel::update(array('nome_tabela'=>$tabela, 'id' => $infoItemAtual['id'], 'order_id' => $itemBefore['order_id']));
		// 		}
		// 	}
	}
?>