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
			$sql = Mysql::conectar()->prepare("SELECT COUNT(DISTINCT codigo_pedido) FROM pedidos WHERE aprovado = 0 AND finalizado = 0;");
			$sql->execute();
			return $sql->fetchAll();
		}

		public static function emprestimosParaDevolver() {
			$sql = Mysql::conectar()->prepare("SELECT COUNT(DISTINCT codigo_pedido) FROM pedidos WHERE aprovado = 1 AND finalizado = 0;");
			$sql->execute();
			return $sql->fetchAll();
		}

		public static function emprestimosFinalizados() {
			$sql = Mysql::conectar()->prepare("SELECT COUNT(DISTINCT codigo_pedido) FROM pedidos WHERE finalizado = 1 AND data_finalizado = ?;");
			// 2001-03-10 (the MySQL DATETIME format)
            $dataHoje = date("Y-m-d"); 
			$sql->execute(array($dataHoje));
			return $sql->fetchAll();	
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