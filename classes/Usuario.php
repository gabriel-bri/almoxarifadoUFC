<?php 
	class Usuario {

		public function atualizarUsuario($nome, $sobrenome, $email, $senha, $imagem) {
			$sql = Mysql::conectar()->prepare('UPDATE `usuarios` SET nome = ?, sobrenome = ?, email = ?, senha = ?, fotoperfil = ? WHERE usuario = ?');
			if($sql->execute(array($nome, $sobrenome, $email, $senha, $imagem, $_SESSION['usuario']))) {
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

		public static function select($query, $arr) {
			$sql = Mysql::conectar()->prepare("SELECT * FROM usuarios WHERE $query");
			$sql->execute($arr);
			return $sql->fetch();
		}

		public static function cadastrarUsuario($user, $senha,$nome, $sobrenome, $email, $imagem, $cargo, $matricula, $curso) {
			// Trabalhar depois nele
			$token_confirmacao = "10202003030300303";

			$sql = Mysql::conectar()->prepare('INSERT INTO `usuarios` (id, usuario, senha, nome, sobrenome, email, fotoperfil, acesso, token_confimarcao, matricula, curso)
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
	}
?>