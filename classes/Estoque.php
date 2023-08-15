<?php 
	class Estoque {

		private $id;
		private $nome;
		private $quantidade;
		private $tipo;

		public function __construct($id, $nome, $quantidade, $tipo) {
			$this->setId($id);
			$this->setNome($nome);
			$this->setQuantidade($quantidade);
			$this->setTipo($tipo);
		}

		// Métodos SET para atribuir os valores aos atributos

		public function setId($id) {
			$this->id = $id;
		}

		public function setNome($nome) {
			$this->nome = $nome;
		}

		public function setQuantidade($quantidade) {
			$this->quantidade = $quantidade;
		}

		public function setTipo($tipo) {
			$this->tipo = $tipo;
		}

		// Métodos GET para recuperar os valores dos atributos

		public function getId() {
			return $this->id;
		}

		public function getNome() {
			return $this->nome;
		}

		public function getQuantidade() {
			return $this->quantidade;
		}

		public function getTipo() {
			return $this->tipo;
		}

		public static function validarEntradasCadastro() {
			$nome = filter_var($_POST['nome'], FILTER_SANITIZE_STRING);
			$quantidade = filter_var($_POST['quantidade'], FILTER_SANITIZE_NUMBER_INT);
			$tipo = filter_var($_POST["tipo"], FILTER_SANITIZE_NUMBER_INT);
			
			if($nome == ''){
				Painel::alert('erro', 'Campos nome vazio.');
			}

			else if($quantidade == ''){
				Painel::alert('erro', 'Campos quantidade vazio.');
			}

			else if($tipo == ''){
				Painel::alert('erro', 'Campos tipo vazio.');
			}

			else if($_POST["quantidade"] < 1 ) {
				Painel::alert('erro', 'Quantidade inválida');
			}
			
			else {
				$estoque = new Estoque(NULL, $nome, $quantidade, $tipo);
				if($estoque->cadastrarEstoque($estoque)){
					Painel::alert('sucesso', 'O cadastro foi realizado com sucesso');
				}
			}
		}

		public static function cadastrarEstoque(Estoque $estoque) {
			try {
				$sql = Mysql::conectar()->prepare("INSERT INTO estoque VALUES (DEFAULT, ?, ?, ?);");
				$sql->execute(
					array(
						$estoque->nome, $estoque->quantidade, $estoque->tipo
					)
				);

				return true;
			}

			catch(Exception $e) {
				Painel::alert("erro", "Erro ao realizar o cadastro do estoque");
			}
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
				$sql = Mysql::conectar()->prepare("SELECT * FROM estoque ORDER BY nome ASC");	
			}

			else {
				$comeco = filter_var($comeco, FILTER_SANITIZE_NUMBER_INT);
				$final = filter_var($final, FILTER_SANITIZE_NUMBER_INT);
				$sql = Mysql::conectar()->prepare("SELECT * FROM estoque ORDER BY id ASC LIMIT $comeco, $final");
			}
			$sql->execute();
			return $sql->fetchAll();
		}

		public static function retornaPeloTipo($tipo) {

			$sql = Mysql::conectar()->prepare("SELECT * FROM estoque WHERE tipo = ? ORDER BY nome ASC");
			$sql->execute(array($tipo));
			return $sql->fetchAll();
		}

		public static function returnData($data) {
			$sql = Mysql::conectar()->prepare("SELECT * FROM estoque WHERE nome LIKE '%$data%' ORDER BY id");	
			$sql->execute();
			return $sql->fetchAll();
		}

		public static function estoqueDisponivel($idProduto) {
			$sql = Mysql::conectar()->prepare("SELECT estoque.quantidade - sum(pedidos.quantidade_item) FROM pedidos INNER JOIN estoque ON estoque.id = pedidos.id_estoque WHERE pedidos.id_estoque = ? AND pedidos.finalizado = 0");
			$sql->execute(array($idProduto));
			return $sql->fetch();
		}


		public static function solicitadoPedido($idProduto) {
			$sql = Mysql::conectar()->prepare("SELECT pedidos.finalizado FROM pedidos WHERE pedidos.id_estoque = ? AND pedidos.finalizado = 0 LIMIT 1");
			$sql->execute(array($idProduto));
			
			if($sql->rowCount() == 1) {
				return true;
			}

			else {
				return false;
			}
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