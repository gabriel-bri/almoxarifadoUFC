<?php 
	class Estoque {
		public static function insertEstoque($parametros) {
			$nome = filter_var($parametros['nome'], FILTER_SANITIZE_STRING);
			$quantidade = filter_var($parametros['quantidade'], FILTER_SANITIZE_NUMBER_INT);
			$tipo = filter_var($parametros["tipo"], FILTER_SANITIZE_NUMBER_INT);

			$query = "INSERT INTO estoque VALUES (DEFAULT, ?, ?, ?);";
			
			$sql = Mysql::conectar()->prepare($query);
			$sql->execute(array($nome, $quantidade, $tipo));

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
			$nome = filter_var($parametros["nome"], FILTER_SANITIZE_STRING); 
			$quantidade = filter_var($parametros["quantidade"], FILTER_SANITIZE_NUMBER_INT);
			$tipo = filter_var($parametros["tipo"], FILTER_SANITIZE_NUMBER_INT);
			$id = filter_var($parametros["id"], FILTER_SANITIZE_NUMBER_INT);

			$sql = Mysql::conectar()->prepare('UPDATE estoque SET nome = ?, quantidade = ?, tipo = ? WHERE id = ?');
			
			if($sql->execute(array($nome, $quantidade, $tipo, $id))) {
				return true;
			}

			else {
				return false;
			}
		}

		public static function selectAll($comeco = null, $final = null) {
			if($comeco == null and $final == null) {
				$sql = Mysql::conectar()->prepare("SELECT * FROM estoque ORDER BY id");	
			}

			else {
				$comeco = filter_var($comeco, FILTER_SANITIZE_NUMBER_INT);
				$final = filter_var($final, FILTER_SANITIZE_NUMBER_INT);
				$sql = Mysql::conectar()->prepare("SELECT * FROM estoque ORDER BY id ASC LIMIT $comeco, $final");
			}
			$sql->execute();
			return $sql->fetchAll();
		}

		public static function estoqueDisponivel($idProduto) {
			$sql = Mysql::conectar()->prepare("SELECT estoque.quantidade - sum(pedidos.quantidade_item) FROM pedidos INNER JOIN estoque ON estoque.id = pedidos.id_estoque WHERE pedidos.id_estoque = ? AND pedidos.finalizado = 0");
			$sql->execute(array($idProduto));
			return $sql->fetch();
		}

		public static function retornaQuantidade($idProduto) {
			$sql = Mysql::conectar()->prepare("SELECT quantidade FROM estoque WHERE id = ?");
			$sql->execute(array($idProduto));
			return $sql->fetch();
		}

		public static $estoque = [
			'1' => 'Equipamento',
			'2' => 'Componente'
	    ];

	}
?>