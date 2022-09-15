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
			$sql->execute(array($user));

			if($sql->rowCount() == 1) {
				return true;
			}

			else {
				return false;
			}
		}

		public static function cadastrarUsuario($user, $nome, $sobrenome, $email, $senha, $imagem, $cargo) {
			$sql = Mysql::conectar()->prepare('INSERT INTO `usuarios` VALUES (null, ?, ?, ?, ?, ?, ?, ?) ');
			$sql->execute(array($user, $nome, $sobrenome, $email, $senha, $imagem, $cargo));
		}
	}
?>