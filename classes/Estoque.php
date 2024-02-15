<?php 
	class Estoque {

		public static $estoque = [
			'1' => 'Equipamento',
			'2' => 'Componente'
	    ];

		public static $statusItem = [
			'0' => 'DESATIVADO',
			'1' => 'ATIVADO'
		];

		private $id;
		private $nome;
		private $quantidade;
		private $tipo;
		private $is_ativado;

		public function __construct($id, $nome, int $quantidade, int $tipo, int $is_ativado = NULL) {
			$this->setId($id);
			$this->setNome($nome);
			$this->setQuantidade($quantidade);
			$this->setTipo($tipo);
			$this->setAtivado($is_ativado);
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

	    public function setAtivado($is_ativado) {
	        $this->is_ativado = $is_ativado;
	    }

		// Métodos GET para recuperar os valores dos atributos
	    public function isAtivado() {
	        return $this->is_ativado;
	    }

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

		// Valida o cadastro de novos itens.
		public static function validarEntradasCadastro() {
			$nome = filter_var($_POST['nome'], FILTER_SANITIZE_STRING);
			$quantidade = filter_var($_POST['quantidade'], FILTER_SANITIZE_NUMBER_INT);
			$tipo = filter_var($_POST["tipo"], FILTER_SANITIZE_NUMBER_INT);
			
			if($nome == ''){
				Painel::alert('erro', 'Campos nome vazio.');
				return;
			}

			if($quantidade == ''){
				Painel::alert('erro', 'Campos quantidade vazio.');
				return;
			}

			if($tipo == ''){
				Painel::alert('erro', 'Campos tipo vazio.');
				return;
			}

			if($quantidade < 1 ) {
				Painel::alert('erro', 'Quantidade inválida');
				return;
			}
			
			$estoque = new Estoque(NULL, $nome, $quantidade, $tipo);
			if($estoque->cadastrarEstoque($estoque)){
				Painel::alert('sucesso', 'O cadastro foi realizado com sucesso');
			}
		}

		// Realiza o cadastro.
		public static function cadastrarEstoque(Estoque $estoque) {
			try {
				$sql = Mysql::conectar()->prepare("INSERT INTO estoque (id, nome, quantidade, tipo) VALUES (DEFAULT, ?, ?, ?);");
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
		}

		// Seleciona item específico.
		public static function select($query, $arr) {
			try {
				$sql = Mysql::conectar()->prepare("SELECT * FROM estoque WHERE $query");
				$sql->execute($arr);
	
				$dados = $sql->fetch();
	
				if(empty($dados)) {
					return false;
				}
	
				$estoque = new Estoque(
					$dados['id'],
					$dados['nome'],
					$dados['quantidade'],
					$dados['tipo'],
					$dados['is_ativado']
				);
	
				return $estoque;
			}

			catch (Exception $e) {
				Painel::alert("erro", "Erro ao se conectar ao banco de dados");
			}
		}

		// Realiza o update do item do estoque.
		public static function update(Estoque $estoque) {
			try {
				$sql = Mysql::conectar()->prepare('UPDATE estoque SET nome = ?, quantidade = ?, tipo = ?, is_ativado = ? WHERE id = ?');

				$sql->execute(
					array(
						$estoque->getNome(),
						$estoque->getQuantidade(), 
						$estoque->getTipo(),
						$estoque->isAtivado(),
						$estoque->getId()
					)
				);
				
				return true;
			}

			catch(Exception $e) {
				Painel::alert("erro", "Erro ao atualizar o estoque");
			}
		}

		// Função de bloqueio do item, recebe um objeto Estoque e o status de bloqueio.
		public static function desativarItem(Estoque $estoque, $status) {
			// 0 -> Item desativado.
			// 1 -> Item ativado.

			// Realiza a ativação do item.
			if($status == 1) {
				$estoque->setAtivado($status);
				if($estoque->update($estoque)){
					Painel::alert("sucesso", "O item foi reativado e já está disponível para empréstimos.");
				}
			}

			// Realiza a desativação do item.
			else if($status == 0) {
				$estoque->setAtivado($status);
				if($estoque->update($estoque)){
					Painel::alert("sucesso", "O item foi desativado e encontra-se indisponível para empréstimos.");
				}
			}
		}

		// Valida a atualização do item
		public static function validarEntradasAtualização($estoque) {
			$nome = filter_var($_POST["nome"], FILTER_SANITIZE_STRING); 
			$quantidade = filter_var($_POST["quantidade"], FILTER_SANITIZE_NUMBER_INT);
			$tipo = filter_var($_POST["tipo"], FILTER_SANITIZE_NUMBER_INT);
			$id = filter_var($_POST["id"], FILTER_SANITIZE_NUMBER_INT);

			if($nome == "" || $quantidade == "" || $tipo == ""){
				Painel::alert('erro', 'Campos vazios não são permitidos');
				return;
			}

			if($quantidade < 1 ) {
				Painel::alert('erro', 'Quantidade inválida');
				return;
			}

			$estoque->setNome($nome);
			$estoque->setQuantidade($quantidade);
			$estoque->setTipo($tipo);
			$estoque->setId($id);

			if(Estoque::update($estoque)){
				Painel::alert('sucesso', 'O estoque foi atualizado com sucesso');
			}
		}

		// Retorna todos os itens
		public static function selectAll($comeco = null, $final = null) {
			try {
				// As varíaveis $começo e $final são responsáveis por trabalhar 
				// a paginação dos resultados.
				if($comeco == null and $final == null) {
					$sql = Mysql::conectar()->prepare("SELECT * FROM estoque ORDER BY nome ASC");	
				}
				
				// Ocorre aqui a limitação de resultados.
				else {
					$comeco = filter_var($comeco, FILTER_SANITIZE_NUMBER_INT);
					$final = filter_var($final, FILTER_SANITIZE_NUMBER_INT);
					$sql = Mysql::conectar()->prepare("SELECT * FROM estoque ORDER BY nome ASC LIMIT $comeco, $final");
				}
				$sql->execute();
				
				$dados = $sql->fetchAll();
					
				if(empty($dados)) {
					return false;
				}
	
				foreach ($dados as $key => $value) {
					$estoque[$key] = new Estoque(
						(int) $value['id'],
						$value['nome'],
						$value['quantidade'],
						$value['tipo'],
						$value['is_ativado']
					);
				}
	
				return $estoque;
			}

			catch(Exception $e) {
				Painel::alert("erro", "Erro ao se conectar ao banco de dados");
			}
		}
		
		// Função chamada somente quando um bolsista ou aluno busca fazer um pedido,
		// mostrando apenas os itens ativos para empréstimo.
		public static function itensDisponiveis($comeco = null, $final = null) {
			try {
				if($comeco == null and $final == null) {
					$sql = Mysql::conectar()->prepare("
						SELECT 
							estoque.id,
							estoque.nome,
							estoque.tipo,
							estoque.is_ativado,
							MAX(estoque.quantidade) - COALESCE(SUM(pedidos.quantidade_item * (pd.finalizado = 0)), 0) AS quantidade_disponivel
						FROM 
							estoque
						LEFT JOIN 
							pedidos ON estoque.id = pedidos.id_estoque
						LEFT JOIN 
							pedido_detalhes pd ON pedidos.id_detalhes = pd.id
						WHERE
							estoque.is_ativado = 1
						GROUP BY 
							estoque.id
						ORDER BY
							estoque.nome
					");
				}
	
				else {
					$comeco = filter_var($comeco, FILTER_SANITIZE_NUMBER_INT);
					$final = filter_var($final, FILTER_SANITIZE_NUMBER_INT);
					$sql = Mysql::conectar()->prepare("
						SELECT 
							estoque.id,
							estoque.nome,
							estoque.tipo,
							estoque.is_ativado,
							MAX(estoque.quantidade) - COALESCE(SUM(pedidos.quantidade_item * (pd.finalizado = 0)), 0) AS quantidade_disponivel
						FROM 
							estoque
						LEFT JOIN 
							pedidos ON estoque.id = pedidos.id_estoque
						LEFT JOIN 
							pedido_detalhes pd ON pedidos.id_detalhes = pd.id
						WHERE
							estoque.is_ativado = 1
						GROUP BY 
							estoque.id
						ORDER BY
							estoque.nome
						LIMIT $comeco, $final
					");
				}
	
				$sql->execute();
					
				$dados = $sql->fetchAll();
	
				if(empty($dados)) {
					return false;
				}
	
				foreach ($dados as $key => $value) {
					$estoque[$key] = new Estoque(
						(int) $value['id'],
						$value['nome'],
						$value['quantidade_disponivel'],
						$value['tipo'],
						$value['is_ativado']
					);
				}
	
				return $estoque;
			}

			catch (Exception $e) {
				Painel::alert("erro", "Erro ao se conectar ao banco de dados");
			}
		}

		// Retorna pelo o tipo do item.
		public static function retornaPeloTipo($tipo) {
			try {
				$sql = Mysql::conectar()->prepare("SELECT * FROM estoque WHERE tipo = ? ORDER BY nome ASC");
				$sql->execute(array($tipo));
	
				$dados = $sql->fetchAll();
	
				if(empty($dados)) {
					return false;
				}
	
				foreach ($dados as $key => $value) {
					$estoque[$key] = new Estoque(
						(int) $value['id'],
						$value['nome'],
						$value['quantidade'],
						$value['tipo'],
						$value['is_ativado']
					);
				}
	
				return $estoque;
			}

			catch(Exception $e) {
				Painel::alert("erro", "Erro ao se conectar ao banco de dados");
			}
		}

		// Retorna pelo o tipo do item no empréstimo.
		public static function retornaPeloTipoEmprestimo($tipo) {
			try {
				$sql = Mysql::conectar()->prepare("SELECT * FROM estoque WHERE tipo = ? AND  is_ativo = 1 ORDER BY nome ASC");
				$sql->execute(array($tipo));
	
				$dados = $sql->fetchAll();
	
				if(empty($dados)) {
					return false;
				}
	
				foreach ($dados as $key => $value) {
					$estoque[$key] = new Estoque(
						(int) $value['id'],
						$value['nome'],
						$value['quantidade'],
						$value['tipo'],
						$value['is_ativado']
					);
				}
	
				return $estoque;
			}

			catch(Exception $e) {
				Painel::alert("erro", "Erro ao se conectar ao banco de dados");
			}
		}

		// Chamado apenas da listagem de estoque. 
		public static function returnData($data, $filtro) {
			$sql = Mysql::conectar()->prepare("SELECT * FROM estoque WHERE $filtro LIKE '%$data%' ORDER BY nome");	
			$sql->execute();

			$dados = $sql->fetchAll();

			if(empty($dados)) {
				return false;
			}

			foreach ($dados as $key => $value) {
				$estoque[$key] = new Estoque(
					(int) $value['id'],
					$value['nome'],
					$value['quantidade'],
					$value['tipo'],
					$value['is_ativado']
				);
			}

			return $estoque;
		}

		// Chamado na solicitação de empréstimo.
		public static function returnDataEmprestimo($data, $filtro) {
			$sql = Mysql::conectar()->prepare("SELECT * FROM estoque WHERE $filtro LIKE '%$data%' AND is_ativado = 1 ORDER BY nome ");	
			$sql->execute();

			$dados = $sql->fetchAll();

			if(empty($dados)) {
				return false;
			}

			foreach ($dados as $key => $value) {
				$estoque[$key] = new Estoque(
					(int) $value['id'],
					$value['nome'],
					$value['quantidade'],
					$value['tipo'],
					$value['is_ativado']
				);
			}

			return $estoque;
		}

		// Mostra os itens disponíveis no estoque para empréstimo.
		public static function estoqueDisponivelProduto($idProduto) {
			try {
				$sql = Mysql::conectar()->prepare("SELECT 
						estoque.id,
						estoque.nome,
						estoque.tipo,
						estoque.is_ativado,
						MAX(estoque.quantidade) - COALESCE(SUM(pedidos.quantidade_item * (pd.finalizado = 0)), 0) AS quantidade_disponivel
					FROM 
						estoque
					LEFT JOIN 
						pedidos ON estoque.id = pedidos.id_estoque
					LEFT JOIN 
						pedido_detalhes pd ON pedidos.id_detalhes = pd.id
					WHERE
						estoque.is_ativado = 1 AND estoque.id = ?
					GROUP BY 
						estoque.id
					ORDER BY
						estoque.nome
				");

				$sql->execute(array($idProduto));

				$dados = $sql->fetch();

				if(empty($dados)) {
					return false;
				}

				$estoque = new Estoque(
					NULL,
					NULL,
					$dados['quantidade_disponivel'],
					0
				);

				return $estoque;
			}

			catch(Exception $e) {
				Painel::alert("erro", "Erro ao se conectar ao banco de dados");
			}
		}

		public static function estoqueDisponivelDetalhes($idProduto){
			try {
				$sql = Mysql::conectar()->prepare("
					SELECT 
					estoque.id,
					estoque.nome,
					estoque.tipo,
					estoque.is_ativado,
					MAX(estoque.quantidade) - COALESCE(SUM(pedidos.quantidade_item * (pd.finalizado = 0)), 0) AS quantidade_disponivel
				FROM 
					estoque
				LEFT JOIN 
					pedidos ON estoque.id = pedidos.id_estoque
				LEFT JOIN 
					pedido_detalhes pd ON pedidos.id_detalhes = pd.id
				WHERE
					estoque.is_ativado = 1 AND estoque.id = ?
				GROUP BY 
					estoque.id
				ORDER BY
					estoque.nome
				");

				$sql->execute(array($idProduto));

				$dados = $sql->fetch();

				if(empty($dados)) {
					return false;
				}

				$estoque = new Estoque(
					$dados['id'],
					$dados['nome'],
					$dados['quantidade_disponivel'],
					$dados['tipo']
				);

				return $estoque;
			}

			catch(Exception $e) {
				Painel::alert("erro", "Erro ao se conectar ao banco de dados");
			}
		}
		// Preciso ver depois
		// public static function solicitadoPedido($idProduto) {
		// 	$sql = Mysql::conectar()->prepare("SELECT pedidos.finalizado FROM pedidos WHERE pedidos.id_estoque = ? AND pedidos.finalizado = 0 LIMIT 1");
		// 	$sql->execute(array($idProduto));
			
		// 	if($sql->rowCount() == 1) {
		// 		return true;
		// 	}

		// 	else {
		// 		return false;
		// 	}
		// }

		// public static function retornaQuantidade($idProduto) {
		// 	$sql = Mysql::conectar()->prepare("SELECT quantidade FROM estoque WHERE id = ?");
		// 	$sql->execute(array($idProduto));
		// 	return $sql->fetch();
		// }
	}
?>