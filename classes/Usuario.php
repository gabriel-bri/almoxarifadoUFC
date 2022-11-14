	<?php 
	class Usuario {

		public function atualizarUsuario($nome, $sobrenome, $email, $imagem) {
			$sql = Mysql::conectar()->prepare('UPDATE `usuarios` SET nome = ?, sobrenome = ?, email = ?, fotoperfil = ? WHERE usuario = ?');
			if($sql->execute(array($nome, $sobrenome, $email, $imagem, $_SESSION['usuario']))) {
				return true;
			}

			else {
				return false;
			}
		}

		public static function atualizarSenha($senha) {
			$sql = Mysql::conectar()->prepare('UPDATE `usuarios` SET senha = ? WHERE usuario = ?');

			$opcoes = [
    			'cost' => 11
			];

			$senha = password_hash($senha, PASSWORD_BCRYPT, $opcoes);

			if($sql->execute(array($senha, $_SESSION['usuario']))) {
				return true;
			}

			else {
				return false;
			}
		}

		public static function userExist($user) {
			$sql = Mysql::conectar()->prepare('SELECT `id` FROM `usuarios` WHERE usuario = ?');
			$sql->execute(array(filter_var($user, FILTER_SANITIZE_STRING)));

			if($sql->rowCount() == 1) {
				return true;
			}

			else {
				return false;
			}
		}

		public static function matriculaJaCadastrada($matricula) {
			$sql = Mysql::conectar()->prepare('SELECT `matricula` FROM `usuarios` WHERE matricula = ?');
			$sql->execute(array(filter_var($matricula, FILTER_SANITIZE_STRING)));

			if($sql->rowCount() == 1) {
				return true;
			}

			else {
				return false;
			}
		}

		public static function select($query, $arr) {
			$sql = Mysql::conectar()->prepare("SELECT * FROM usuarios WHERE $query");
			$sql->execute($arr);
			return $sql->fetch();
		}

		public static function cadastrarUsuario($user, $senha, $nome, $sobrenome, $email, $imagem, $cargo, $matricula, $curso) {

			$opcoes = [
    			'cost' => 11
			];

			$chave = bin2hex(random_bytes(30));
			$token_confirmacao = password_hash(time() . rand() . $chave, PASSWORD_BCRYPT, $opcoes) . bin2hex(random_bytes(98));

			$senha = password_hash($senha, PASSWORD_BCRYPT, $opcoes);

			$mail = new Email();
			$mail->addAdress($email, $nome . " " . $sobrenome);
			$mail->EmailConfirmacao($nome, $user, $token_confirmacao);
			$mail->enviarEmail();

			$sql = Mysql::conectar()->prepare('INSERT INTO `usuarios` (id, usuario, senha, nome, sobrenome, email, fotoperfil, acesso, token_confirmacao, matricula, curso)
			VALUES (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ');

			$sql->execute(array($user, $senha, $nome, $sobrenome, $email, $imagem, $cargo, $token_confirmacao, $matricula, $curso));
		}

		public static function atualizarUsuarios($nome, $sobrenome, $email, $matricula, $curso, $id) {
			$sql = Mysql::conectar()->prepare('UPDATE `usuarios` SET nome = ?, sobrenome = ?, email = ?, matricula = ?, curso = ? WHERE id = ?');
			if($sql->execute(array($nome, $sobrenome, $email, $matricula, $curso, $id))) {
				return true;
			}

			else {
				return false;
			}
		}

		public static function selectAll($comeco = null, $final = null) {
			if($comeco == null and $final == null) {
				$sql = Mysql::conectar()->prepare("SELECT * FROM usuarios ORDER BY id");
				$sql->execute();	
			}

			else {
				$comeco = filter_var($comeco, FILTER_SANITIZE_NUMBER_INT);
				$final = filter_var($final, FILTER_SANITIZE_NUMBER_INT);
				$sql = Mysql::conectar()->prepare("SELECT * FROM usuarios ORDER BY id ASC LIMIT $comeco, $final;");
			}
			$sql->execute();
			return $sql->fetchAll();
		}

		public static function deletar($id) {
			$sql = Mysql::conectar()->prepare('DELETE FROM `usuarios` WHERE id = ?');
			$sql->execute(array(filter_var($id, FILTER_SANITIZE_NUMBER_INT)));
		}

		public static function recuperarSenha($user) {
			$sql = Mysql::conectar()->prepare("SELECT * FROM `usuarios` WHERE usuario = ? AND is_ativada = 1");

			$sql->execute(array($user));

			if($sql->rowCount() == 1) {
				$info = $sql->fetch();
				
				$opcoes = [
    				'cost' => 11
				];

				$chave = bin2hex(random_bytes(30));
				$token_recuperacao = password_hash(time() . rand() . $chave, PASSWORD_BCRYPT, $opcoes) . bin2hex(random_bytes(98));

				$sql = Mysql::conectar()->prepare('UPDATE `usuarios` SET token_recuperacao = ? WHERE usuario = ?');
				$sql->execute(array($token_recuperacao, $user));

				$mail = new Email();
				$mail->addAdress(htmlentities($info['email']), htmlentities($info['nome'] . " " . $info['sobrenome']));
				$mail->EmailRecuperacao(htmlentities($info['nome']), $user, $token_recuperacao);
				$mail->enviarEmail();
				
				return true;
			}
		}
	}
?>