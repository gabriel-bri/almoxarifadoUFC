<?php 
	class Usuario {
		private $id;
		private $usuario;
    	private $senha;
	    private $nome;
	    private $sobrenome;
	    private $email;
	    private $fotoperfil;
	    private $acesso;
	    private $token_confirmacao;
	    private $token_recuperacao;
	    private $is_ativada;
	    private $matricula;
	    private $curso;

	    public function __construct($usuario,$nome, $sobrenome, $email, $fotoperfil, $acesso, $matricula, $curso, $senha = NULL, $id = NULL) {
	        $this->setUsuario($usuario);
	        $this->setSenha($senha);
	        $this->setNome($nome);
	        $this->setSobrenome($sobrenome);
	        $this->setEmail($email);
	        $this->setFotoPerfil($fotoperfil);
	        $this->setAcesso($acesso);
	        $this->setMatricula($matricula);
	        $this->setCurso($curso);
			$this->setId($id);
	    }

		public function getUsuario() {
	        return $this->usuario;
	    }

	    public function setUsuario($usuario) {
	        $this->usuario = $usuario;
	    }

		public function getId() {
	        return $this->id;
	    }

	    public function setId($id) {
	        $this->id = $id;
	    }

	    public function getSenha() {
	        return $this->senha;
	    }

	    public function setSenha($senha) {
	        $this->senha = $senha;
	    }

	    public function getNome() {
	        return $this->nome;
	    }

	    public function setNome($nome) {
	        $this->nome = $nome;
	    }

	    public function getSobrenome() {
	        return $this->sobrenome;
	    }

	    public function setSobrenome($sobrenome) {
	        $this->sobrenome = $sobrenome;
	    }

	    public function getEmail() {
	        return $this->email;
	    }

	    public function setEmail($email) {
	        $this->email = $email;
	    }

	    public function getFotoPerfil() {
	        return $this->fotoperfil;
	    }

	    public function setFotoPerfil($fotoperfil) {
	        $this->fotoperfil = $fotoperfil;
	    }

	    public function getAcesso() {
	        return $this->acesso;
	    }

	    public function setAcesso($acesso) {
	        $this->acesso = $acesso;
	    }

	    public function getTokenConfirmacao() {
	        return $this->token_confirmacao;
	    }

	    public function setTokenConfirmacao($token_confirmacao) {
	        $this->token_confirmacao = $token_confirmacao;
	    }

	    public function getTokenRecuperacao() {
	        return $this->token_recuperacao;
	    }

	    public function setTokenRecuperacao($token_recuperacao) {
	        $this->token_recuperacao = $token_recuperacao;
	    }

	    public function isAtivada() {
	        return $this->is_ativada;
	    }

	    public function setAtivada($is_ativada) {
	        $this->is_ativada = $is_ativada;
	    }

	    public function getMatricula() {
	        return $this->matricula;
	    }

	    public function setMatricula($matricula) {
	        $this->matricula = $matricula;
	    }

	    public function getCurso() {
	        return $this->curso;
	    }

	    public function setCurso($curso) {
	        $this->curso = $curso;
	    }
	    
		// public function atualizarUsuario($nome, $sobrenome, $email, $imagem) {
		// 	$sql = Mysql::conectar()->prepare('UPDATE `usuarios` SET nome = ?, sobrenome = ?, email = ?, fotoperfil = ? WHERE usuario = ?');
		// 	if($sql->execute(array($nome, $sobrenome, $email, $imagem, $_SESSION['usuario']))) {
		// 		return true;
		// 	}

		// 	else {
		// 		return false;
		// 	}
		// }

		// public static function atualizarSenha($senha) {
		// 	$sql = Mysql::conectar()->prepare('UPDATE `usuarios` SET senha = ? WHERE usuario = ?');

		// 	$opcoes = [
    	// 		'cost' => 11
		// 	];

		// 	$senha = password_hash($senha, PASSWORD_BCRYPT, $opcoes);

		// 	if($sql->execute(array($senha, $_SESSION['usuario']))) {
		// 		return true;
		// 	}

		// 	else {
		// 		return false;
		// 	}
		// }

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

		// public static function returnData($data) {
		// 	$sql = Mysql::conectar()->prepare("SELECT * FROM usuarios WHERE nome LIKE '%$data%' ORDER BY id");	
		// 	$sql->execute();
		// 	return $sql->fetchAll();
		// }

		public static function emailJaCadastrado($email) {
			$sql = Mysql::conectar()->prepare('SELECT `email` FROM `usuarios` WHERE email = ?');
			$sql->execute(array(filter_var($email, FILTER_SANITIZE_EMAIL)));

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

			$dados = $sql->fetch();

			if(empty($dados)) {
				return false;
			}

			$usuario = new Usuario(
				$dados['usuario'],
				$dados['nome'],
				$dados['sobrenome'],
				$dados['email'],
				$dados['fotoperfil'],
				$dados['acesso'],
				$dados['matricula'],
				$dados['curso'],
				NULL,
				$dados['id']
			);

			return $usuario;
		}
		
		public function gerarTokenConfirmacao(Usuario $usuario) {
			$opcoes = [
    			'cost' => 11
			];

			$chave = bin2hex(random_bytes(30));
			$usuario->setTokenConfirmacao(password_hash(time() . rand() . $chave, PASSWORD_BCRYPT, $opcoes) . bin2hex(random_bytes(98)));

			$usuario->setSenha(password_hash($usuario->getSenha(), PASSWORD_BCRYPT, $opcoes));
		}

		public function enviarEmailConfirmacao(Usuario $usuario) {
			try {
				$mail = new Email();
				$mail->addAdress($usuario->getEmail(), $usuario->getNome() . " " . $usuario->getSobrenome());
				$mail->EmailConfirmacao($usuario->getNome(), $usuario->getUsuario(), $usuario->getTokenConfirmacao());
				$mail->enviarEmail();
			}

			catch(Exception $e) {
				Painel::alert("erro", "Erro ao enviar o e-mail de confirmação, tente novamente mais tarde.");
			}
		}

		public static function cadastrarUsuario(Usuario $usuario) {
			try {
				$sql = Mysql::conectar()->prepare('INSERT INTO `usuarios` (id, usuario, senha, nome, sobrenome, email, fotoperfil, acesso, token_confirmacao, matricula, curso)
			VALUES (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ');
			
				$usuario->gerarTokenConfirmacao($usuario);
				$usuario->enviarEmailConfirmacao($usuario);

				$sql->execute(
					array(
						trim($usuario->getUsuario()), $usuario->getSenha(), ucwords($usuario->getNome()), 
						ucwords($usuario->getSobrenome()), trim($usuario->getEmail()), $usuario->getFotoPerfil(), $usuario->getAcesso(), 
						$usuario->getTokenConfirmacao(), trim($usuario->getMatricula()), $usuario->getCurso()
					)
				);

				return true;
			}

			catch(Exception $e) {
				Painel::alert("erro", "Não foi possível inserir dados no banco.");
			}
		}
		
		public static function validarEntradasCadastro($login, $nome, $sobrenome, $email, $imagem, $cargo, $matricula, $curso, $senha) {

			$dominio = explode("@", $email);

			if($login == '') {
				Painel::alert('erro', 'O login está vazio');
			}

			else if($nome == '') {
				Painel::alert('erro', 'O nome está vazio');
			}

			else if($sobrenome == '') {
				Painel::alert('erro', 'O sobrenome está vazio');
			}

			else if($email == '') {
				Painel::alert('erro', 'O e-mail está vazio');
			}

			else if($senha == '') {
				Painel::alert('erro', 'A senha está vazia');
			}

			else if($cargo == '') {
				Painel::alert('erro', 'O cargo está vazio');
			}

			else if(filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
				Painel::alert('erro', 'E-mai inválido, tente novamente.');
			}

			else if(Usuario::emailJaCadastrado($email)){
				Painel::alert('erro', 'E-mail já cadastrado.');
			}

			else {
				if($cargo == 1) {
					$matricula = filter_var($_POST['matricula'], FILTER_SANITIZE_NUMBER_INT);
					$curso = filter_var($_POST['curso'], FILTER_SANITIZE_STRING);
				}

				if(Usuario::userExist($login)) {
					Painel::alert('erro', 'Selecione um login diferente');		
				}

				else if($cargo == 1 && $dominio[1] != "alu.ufc.br"){
					Painel::alert('erro', 'Alunos podem usar apenas e-mail @alu.ufc.br');
				}

				else if($cargo == 1 && Usuario::matriculaJaCadastrada($matricula)) {
					Painel::alert("erro", "Matrícula já cadastrada no sistema");	
				}

				else if($cargo == 1 && (strlen($matricula) > 6 || strlen($matricula) < 6)) {
					Painel::alert('erro', 'A matrícula deve ter 6 dígitos');
				}

				else if($imagem['name'] != '' && Painel::imagemValida($imagem) == false) {
					Painel::alert('erro', 'O formato especificado não é válido');
				}

				else {
					$imagem['name'] != '' ? $imagem = Painel::uploadFile($imagem) : $imagem = "";

					$usuario = new Usuario($login, $nome, $sobrenome, $email, $imagem, $cargo, $matricula, $curso, $senha);

					if($usuario->cadastrarUsuario($usuario)){
						Painel::alert("sucesso", "Usuário cadastrado com sucesso. Um e-mail de confirmação foi enviado.");
					}
				}
			}
		}

		public static function validarEntradasAtualizarUsuarios(Usuario $usuario, $nome, $sobrenome, $email, $matricula, $curso, $id){
			
			$dominio = explode("@", $email);

			if($usuario->getAcesso() == 1) {
				$matricula = filter_var($_POST['matricula'], FILTER_SANITIZE_NUMBER_INT);
				$curso = filter_var($_POST['curso'], FILTER_SANITIZE_STRING);
			}

			if($usuario->getAcesso() == 1 && $dominio[1] != "alu.ufc.br"){
				Painel::alert('erro', 'Alunos podem usar apenas e-mail @alu.ufc.br');
			}

			else if(filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
				Painel::alert('erro', 'E-mai inválido, tente novamente.');
			}
			
			else if($usuario->getAcesso() == 1 && (strlen($matricula) > 6 || strlen($matricula) < 6)) {
				Painel::alert('erro', 'A matrícula deve ter 6 dígitos');
			}
			
			else if(Usuario::emailJaCadastrado($email) && $usuario->getEmail() != $email){
				Painel::alert('erro', 'E-mail já cadastrado.');
			}

			else{
				$usuario = new Usuario(
					NULL, $nome, $sobrenome, $email, NULL, NULL, $matricula, $curso, NULL, $id
				);

				if($usuario->atualizarUsuarios($usuario)){
					Painel::alert('sucesso', 'O usuário foi atualizado com sucesso');
				}
			}
		}

		public static function atualizarUsuarios(Usuario $usuario) {
			try {
				$sql = Mysql::conectar()->prepare('UPDATE `usuarios` SET nome = ?, sobrenome = ?, email = ?, matricula = ?, curso = ? WHERE id = ?');
				$sql->execute(array(
					$usuario->getNome(), $usuario->getSobrenome(), $usuario->getEmail(),
					$usuario->getMatricula(), $usuario->getCurso(), $usuario->getId()
				));

				return true;
			}

			catch(Exception $e) {
				Painel::alert("erro", "Erro ao atualizar o usuário");
			}
		}

		public static function selectAll($comeco = null, $final = null) {
			if($comeco == null and $final == null) {
				$sql = Mysql::conectar()->prepare("SELECT * FROM usuarios WHERE id != ? ORDER BY nome");
			}

			else {
				$comeco = filter_var($comeco, FILTER_SANITIZE_NUMBER_INT);
				$final = filter_var($final, FILTER_SANITIZE_NUMBER_INT);
				$sql = Mysql::conectar()->prepare("SELECT * FROM usuarios WHERE id != ? ORDER BY nome ASC LIMIT $comeco, $final;");
			}
			$sql->execute(array($_SESSION['id']));
			$dados = $sql->fetchAll();
				
			if(empty($dados)) {
				return false;
			}

			foreach ($dados as $key => $value) {
				$usuario[$key] = new Usuario(
					$value['usuario'],
					$value['nome'],
					$value['sobrenome'],
					$value['email'],
					$value['fotoperfil'],
					$value['acesso'],
					$value['matricula'],
					$value['curso'],
					NULL,
					$value['id']
				);
			}
			
			return $usuario;
		}

		// public static function deletar($id) {
		// 	$sql = Mysql::conectar()->prepare('DELETE FROM `usuarios` WHERE id = ?');
		// 	$sql->execute(array(filter_var($id, FILTER_SANITIZE_NUMBER_INT)));
		// }

		// public static function recuperarSenha($user) {
		// 	$sql = Mysql::conectar()->prepare("SELECT * FROM `usuarios` WHERE usuario = ? AND is_ativada = 1");

		// 	$sql->execute(array($user));

		// 	if($sql->rowCount() == 1) {
		// 		$info = $sql->fetch();
				
		// 		$opcoes = [
    	// 			'cost' => 11
		// 		];

		// 		$chave = bin2hex(random_bytes(30));
		// 		$token_recuperacao = password_hash(time() . rand() . $chave, PASSWORD_BCRYPT, $opcoes) . bin2hex(random_bytes(98));

		// 		$sql = Mysql::conectar()->prepare('UPDATE `usuarios` SET token_recuperacao = ? WHERE usuario = ?');
		// 		$sql->execute(array($token_recuperacao, $user));

		// 		$mail = new Email();
		// 		$mail->addAdress(htmlentities($info['email']), htmlentities($info['nome'] . " " . $info['sobrenome']));
		// 		$mail->EmailRecuperacao(htmlentities($info['nome']), $user, $token_recuperacao);
		// 		$mail->enviarEmail();
				
		// 		return true;
		// 	}
		// }
	}
?>