<?php 
	class Estoque {
		public static function insertEstoque($parametros) {
			// var_dump($parametros);
			$nome = $parametros['nome'];
			$quantidade = $parametros['quantidade'];
			$query = "INSERT INTO estoque VALUES (DEFAULT, ?, ?);";
			
			$sql = Mysql::conectar()->prepare($query);
			$sql->execute(array($nome, $quantidade));

			return $query;
			// $certo = true;
			// $nome_tabela = $arr['nome_tabela'];
			// $query = "INSERT INTO `$nome_tabela` VALUES (null";
			// foreach ($arr as $key => $value) {
			// 	$nome = $key;
			// 	$valor = $value;
			// 	if($nome == 'acao' || $nome == 'nome_tabela') {
			// 		continue;
			// 	}

			// 	if($value == '') {
			// 		$certo = false;
			// 		break;
			// 	}

			// 	$query .= ", ?";
			// 	$parametros[] = $value;
			// }

			// $query .= " ) ";
			
			// if($certo == true) {
			// 	$sql = Mysql::conectar()->prepare($query);
			// 	$sql->execute($parametros);
			// 	$lastId = Mysql::conectar()->lastInsertId();
			// 	$sql = Mysql::conectar()->prepare("UPDATE `$nome_tabela` SET order_id = ? WHERE id = $lastId");
			// 	$sql->execute(array($lastId));
			// }
			// return $query;
		}

		public static function select($query, $arr) {
			$sql = Mysql::conectar()->prepare("SELECT * FROM estoque WHERE $query");
			$sql->execute($arr);
			return $sql->fetch();
		}

		public static function update($parametros) {
			$sql = Mysql::conectar()->prepare('UPDATE estoque SET nome = ?, quantidade = ? WHERE id = ?');
			if($sql->execute(array($parametros["nome"], $parametros["quantidade"], $parametros["id"]))) {
				return true;
			}

			else {
				return false;
			}
		}

	}
?>