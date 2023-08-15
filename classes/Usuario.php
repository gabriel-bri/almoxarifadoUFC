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
		private $is_bloqueado;

	    public function __construct($usuario,$nome, $sobrenome, $email, $fotoperfil, $acesso, $matricula, $curso, $senha = NULL, $id = NULL, $is_ativada = NULL, $is_bloqueado = NULL) {
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
			$this->setAtivada($is_ativada);
			$this->setBloqueado($is_bloqueado);
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

	    public function setTokenConfirmacao() {
			$opcoes = [
				'cost' => 11
			];

			$chave = bin2hex(random_bytes(30));
			$token_confirmacao = password_hash(time() . rand() . $chave, PASSWORD_BCRYPT, $opcoes) . bin2hex(random_bytes(98));

			$this->token_confirmacao = $token_confirmacao;
			return $this;
	    }

	    public function getTokenRecuperacao() {
	        return $this->token_recuperacao;
	    }

	    public function setTokenRecuperacao() {
			$opcoes = [
				'cost' => 11
			];

			$chave = bin2hex(random_bytes(30));
			$token_recuperacao = password_hash(time() . rand() . $chave, PASSWORD_BCRYPT, $opcoes) . bin2hex(random_bytes(98));

			$this->token_recuperacao = $token_recuperacao;
			return $this;
		}

	    public function isAtivada() {
	        return $this->is_ativada;
	    }

	    public function setAtivada($is_ativada) {
	        $this->is_ativada = $is_ativada;
	    }

		public function isBloqueado() {
	        return $this->is_bloqueado;
	    }

	    public function setBloqueado($is_bloqueado) {
	        $this->is_bloqueado = $is_bloqueado;
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
	    
		public static function atualizarUsuario(Usuario $usuario) {
			try {
				$sql = Mysql::conectar()->prepare('UPDATE `usuarios` SET nome = ?, sobrenome = ?, email = ?, fotoperfil = ? WHERE usuario = ?');
				
				$sql->execute(
					array(
						$usuario->getNome(), $usuario->getSobrenome(),
						$usuario->getEmail(), $usuario->getFotoPerfil(),
						$_SESSION['usuario']
					)
				);
			
				return true;
			}

			catch(Exception $e) {
				Painel::alert("erro", "Erro ao atualizar o usuário");
			}
		}

		public static function atualizarSenha($senha) {
			try {
				$sql = Mysql::conectar()->prepare('UPDATE `usuarios` SET senha = ? WHERE usuario = ?');

				$opcoes = [
					'cost' => 11
				];
	
				$senha = password_hash($senha, PASSWORD_BCRYPT, $opcoes);
	
				$sql->execute(array($senha, $_SESSION['usuario']));

				return true;
			}

			catch(Exception $e) {
				Painel::alert("erro", "Erro ao atualizar a senha.");
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

		public static function returnData($data, $filtro) {
			$sql = Mysql::conectar()->prepare("SELECT * FROM usuarios WHERE $filtro LIKE '%$data%' ORDER BY nome");	
			$sql->execute();

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
				$dados['id'],
				$dados['is_ativada'],
				$dados['is_bloqueado']
			);

			return $usuario;
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
			
				$usuario->setTokenConfirmacao();
				$usuario->enviarEmailConfirmacao($usuario);

				$sql->execute(
					array(
						trim($usuario->getUsuario()), $usuario->getSenha(), trim(ucwords($usuario->getNome())), 
						trim(ucwords($usuario->getSobrenome())), trim($usuario->getEmail()), $usuario->getFotoPerfil(), $usuario->getAcesso(), 
						$usuario->getTokenConfirmacao(), trim($usuario->getMatricula()), $usuario->getCurso()
					)
				);

				return true;
			}

			catch(Exception $e) {
				Painel::alert("erro", "Não foi possível inserir dados no banco.");
			}
		}
		
		public static function validarEntradasCadastro() {
			$login = filter_var($_POST['login'], FILTER_SANITIZE_STRING);
			$nome = filter_var($_POST['nome'], FILTER_SANITIZE_STRING);
			$sobrenome = filter_var($_POST['sobrenome'], FILTER_SANITIZE_STRING);
			$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
			$senha = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
			$imagem = $_FILES['imagem'];
			$cargo = filter_var($_POST['acesso'], FILTER_SANITIZE_NUMBER_INT);
			$curso = "";
			$matricula = "";
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

		public static function validarEntradasAtualizarUsuarios(Usuario $usuario){

			$nome = filter_var($_POST['nome'], FILTER_SANITIZE_STRING);
			$sobrenome = filter_var($_POST['sobrenome'], FILTER_SANITIZE_STRING);
			$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
			$id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);

			$matricula = "";
			$curso = "";
			
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
				$usuario->setNome($nome);
				$usuario->setSobrenome($sobrenome);
				$usuario->setEmail($email);
				$usuario->setMatricula($matricula);
				$usuario->setCurso($curso);
				$usuario->setId($id);
				if($usuario->atualizarUsuarios($usuario)){
					Painel::alert('sucesso', 'O usuário foi atualizado com sucesso');
				}
			}
		}


		public static function validarEntradasAtualizarUsuario() {
			
			$nome = filter_var($_POST['nome'], FILTER_SANITIZE_STRING);
			$sobrenome = filter_var($_POST['sobrenome'], FILTER_SANITIZE_STRING);
			$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
			
			$imagem = $_FILES['imagem'];
			$imagem_atual = $_POST['imagem_atual'];

			$dominio = explode("@", $email);
					
			if($dominio[1] != "alu.ufc.br" && $_SESSION['acesso'] == 1) {
				Painel::alert('erro', 'Alunos podem usar apenas e-mail @alu.ufc.br');
			}

			else if(filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
				Painel::alert('erro', 'E-mai inválido, tente novamente.');
			}

			else if(Usuario::emailJaCadastrado($email) && $_SESSION['email'] != $email){
				Painel::alert('erro', 'E-mail já cadastrado.');
			}

			else {
				if($imagem['name'] != '') {
					if(Painel::imagemValida($imagem)) {
						Painel::deleteFile($imagem_atual);
						$imagem = Painel::uploadFile($imagem);
						
						$usuario = new Usuario(
							NULL, $nome, $sobrenome, $email,
							$imagem, NULL, NULL, NULL
						);

						if($usuario->atualizarUsuario($usuario)){
							Painel::alert("sucesso", "Atualização de dados junto com a imagem realizada com sucesso.");
						}

						else {
							Painel::alert("erro", "Erro durante a atualização de dados junto com a imagem.");
						}
					}

					else {
						Painel::alert("erro", "O formato da imagem não é válido");
					}
				}

				else {
					$usuario = new Usuario(
						NULL, $nome, $sobrenome, $email,
						$imagem_atual, NULL, NULL, NULL
					);

					if($usuario->atualizarUsuario($usuario)){
						Painel::alert("sucesso", "Atualização de dados realizada com sucesso");
					}

					else {
						Painel::alert("erro", "Erro durante a atualização de dados.");
					}
				}

				Painel::atualizarSessao($usuario);
			}
		}
		
		public static function atualizarUsuarios(Usuario $usuario) {
			try {
				$sql = Mysql::conectar()->prepare('UPDATE `usuarios` SET nome = ?, sobrenome = ?, email = ?, matricula = ?, curso = ?, is_ativada = ?, is_bloqueado = ? WHERE id = ?');
		
				$sql->execute(array(
					$usuario->getNome(), $usuario->getSobrenome(), $usuario->getEmail(),
					$usuario->getMatricula(), $usuario->getCurso(), $usuario->isAtivada(), $usuario->isBloqueado(), $usuario->getId()
				));

				return true;
			}

			catch(Exception $e) {
				Painel::alert("erro", "Erro ao atualizar o usuário" . $e);
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

		public static function bloquearPedidos(Usuario $usuario, $status) {
			if($status == 1) {
				$usuario->setBloqueado(1);
				if($usuario->atualizarUsuarios($usuario)){
					Painel::alert("sucesso", "Usuário está bloqueado de fazer pedidos. Uma notificação foi enviada.");
				}
			}

			else if($status == 0) {
				$usuario->setBloqueado(0);
				if($usuario->atualizarUsuarios($usuario)){
					Painel::alert("sucesso", "Usuário autorizado a fazer pedidos. Uma notificação foi enviada.");
				}
			}
		}

		public static function bloquearLogin(Usuario $usuario, $status) {
			if($status == 1) {
				$usuario->setAtivada(1);
				if($usuario->atualizarUsuarios($usuario)){
					Painel::alert("sucesso", "Usuário está autorizado a fazer login. Uma notificação foi enviada.");
				}
			}

			else if($status == 0) {
				$usuario->setAtivada(0);
				if($usuario->atualizarUsuarios($usuario)){
					Painel::alert("sucesso", "Usuário bloqueado de fazer login. Uma notificação foi enviada.");
				}
			}
		}

		public static function reConfirmarConta(Usuario $usuario) {
			$usuario->setTokenConfirmacao();
			if(Usuario::novaConfirmacao($usuario)){
				Painel::alert("sucesso", "Um novo link de confirmação foi enviado");
			}
		}

		public static function novaConfirmacao(Usuario $usuario) {
			try {
				$sql = Mysql::conectar()->prepare('UPDATE `usuarios` SET token_confirmacao = ? WHERE usuario = ?');
				
				$usuario->enviarEmailConfirmacao($usuario);
				
				$sql->execute(
					array(
						$usuario->getTokenConfirmacao(), 
						$usuario->getUsuario()
					)
				);
				
				return true;
			}

			catch(Exception $e) {
				Painel::alert("erro", "Erro ao se conectar ao banco de dados");
			}
		}
		public static function confirmaConta() {
			$token_confirmacao = filter_var($_GET['token_confirmacao'], FILTER_SANITIZE_STRING);

			try {
				$sql = Mysql::conectar()->prepare('UPDATE `usuarios` SET token_confirmacao = ?, is_ativada = ? WHERE token_confirmacao = ?');

				$sql->execute(array("", 1, $token_confirmacao));
				return true;
			}

			catch(Exception $e) {
				Painel::alert("erro", "Erro ao conectar ao banco de dados");
			}
		}
		
		public static function tokenConfirmacaoValido() {
			try{
				$token_confirmacao = filter_var($_GET['token_confirmacao'], FILTER_SANITIZE_STRING);
				$sql = Mysql::conectar()->prepare("SELECT token_confirmacao FROM `usuarios` WHERE token_confirmacao = ?");

				$sql->execute(array($token_confirmacao));

				return $sql->rowCount() == 1;
			}

			catch(Exception $e) {
				Painel::alert("erro", "Erro ao conectar ao banco de dados");
			}
		}

		public static function tokenRecuperacaoValido() {
			try {
				$token_recuperacao = filter_var($_GET['token_recuperacao'], FILTER_SANITIZE_STRING);
				$sql = Mysql::conectar()->prepare("SELECT token_recuperacao FROM `usuarios` WHERE token_recuperacao = ?");
	
				$sql->execute(array($token_recuperacao));
				return $sql->rowCount() == 1;
			}

			catch(Exception $e) {
				Painel::alert("erro", "Erro ao conectar ao banco de dados");
			}
		}

		public static function validarRecuperarSenha() {
			if($_POST['user'] != '') {
				$user = filter_var($_POST['user'], FILTER_SANITIZE_STRING);
				
				if(Usuario::recuperarSenha(trim($user))) {
					Painel::alert("sucesso", "Se tudo estiver correto um e-mail com instruções será enviado para você.");
				}

				else {
					Painel::alert("erro", "Esta conta pode não existir ou ela ainda não foi ativada.");
				}
			}

			else{
				Painel::alert("erro", "O usuário não foi passado. Tente novamente.");
			}
		}

		public static function enviarRecuperacaoEmail(Usuario $usuario) {
			try {			
				$mail = new Email();
				$mail->addAdress(htmlentities($usuario->getEmail()), htmlentities($usuario->getNome() . " " . $usuario->getSobrenome()));
				$mail->EmailRecuperacao(htmlentities($usuario->getNome()), $usuario->getUsuario(), $usuario->getTokenRecuperacao());
				$mail->enviarEmail();
				
				return true;
			}

			catch(Exception $e) {
				Painel::alert("erro", "Erro ao enviar o e-mail, tente novamente mais tarde");
			}
		}
		
		public static function recuperarSenha($email) {
			try {
				$sql = Mysql::conectar()->prepare("SELECT * FROM `usuarios` WHERE usuario = ? AND is_ativada = 1");

				$sql->execute(array($email));

				if($sql->rowCount() == 1) {
					$info = $sql->fetch();
					
					$usuario = new Usuario(
						$info['usuario'],
						$info['nome'], $info['sobrenome'], $info['email'],
						NULL, NULL, NULL, NULL
					);

					$usuario->setTokenRecuperacao();

					if(Usuario::enviarRecuperacaoEmail($usuario)) {
						$sql = Mysql::conectar()->prepare('UPDATE `usuarios` SET token_recuperacao = ? WHERE usuario = ?');
					
						$sql->execute(
							array(
								$usuario->getTokenRecuperacao(), 
								$usuario->getUsuario()
							)
						);
	
						return true;
					}
				}

				return false;
			}

			catch(Exception $e) {
				Painel::alert("erro", "Erro ao conectar ao banco de dados");
			}
		}

		public static function novaSenha() {
			$senha = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
			$token_recuperacao = filter_var($_GET['token_recuperacao'], FILTER_SANITIZE_STRING);

			$opcoes = [
    			'cost' => 11
			];

			$senha = password_hash($senha, PASSWORD_BCRYPT, $opcoes);
			
			try {		
				$sql = Mysql::conectar()->prepare('UPDATE `usuarios` SET token_recuperacao = ?, senha = ? WHERE token_recuperacao = ?');

				$sql->execute(array("", $senha, $token_recuperacao));

				return true;
			}

			catch(Exception $e) {
				Painel::alert("erro", "Erro ao conectar ao banco de dados");
			}
		}

		public static function deletar($id) {
			try {
				$sql = Mysql::conectar()->prepare('DELETE FROM `usuarios` WHERE id = ?');
				$sql->execute(array(filter_var($id, FILTER_SANITIZE_NUMBER_INT)));
			}

			catch(Exception $e) {
				Painel::alert("erro", "Erro ao deletar usuário do banco de dados");
			}
		}
	}
?>