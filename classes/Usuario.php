<?php 
	class Usuario {
		// Define os níveis de acesso da aplicação
		public static $acessos = [
			'1' => 'Aluno',
			'2' => 'Bolsista / Administrador',
			'3' => 'Super Administrador'
		];

		// Define os cursos disponíveis atualmente na instituição,
		// sendo 5 no total. Para mais informações dos cursos acesse:
		// https://www.quixada.ufc.br/cursos/
		public static $cursos = [
			'EC' => 'Engenharia da Computação',
			'CC' => 'Ciência da Computação',
			'SI' => 'Sistemas de Informação',
			'RC' => 'Redes de Computadores',
			'DD' => 'Design Digital', 
			'ES' => 'Engenharia de Software'
		];

		// Define o status da conta, caso seja 0, 
		// o usuário não pode fazer login na aplicação
		public static $statusConta = [
			'0' => 'DESATIVADA',
			'1' => 'ATIVADA'
		];

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
			if($senha == NULL) {
				return;
			}

			// Define o custo computacional para criar o hash da senha.
			$opcoes = [
				'cost' => 11
			];
				
			// A variável senha recebe o valor da senha passada em formato hash.
			$senha = password_hash($senha, PASSWORD_BCRYPT, $opcoes);
			$this->senha = $senha;
			$this->senha = $senha;
		}

	    public function getNome() {
	        return $this->nome;
	    }

		public function getIsAtivada(){
			return $this->is_ativada;
		}

		public function getIsBloqueada(){
			return $this->is_bloqueado;
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
			// Define as opções para o algoritmo de hash de senha.
			$opcoes = [
				'cost' => 11
			];

			// Gera uma chave aleatória usando 30 bytes de dados binários e a converte em uma string hexadecimal.
			$chave = bin2hex(random_bytes(30));

			// Gera um token de confirmação único e seguro, combinando o tempo atual, um número aleatório e a chave gerada anteriormente.
			// O token é criptografado usando o algoritmo de hash de senha bcrypt, com as opções definidas anteriormente.
			$token_confirmacao = password_hash(time() . rand() . $chave, PASSWORD_BCRYPT, $opcoes) . bin2hex(random_bytes(98));

			// Atribui o token de confirmação gerado ao atributo 'token_confirmacao' do objeto atual.
			$this->token_confirmacao = $token_confirmacao;

			return $this;
	    }

	    public function getTokenRecuperacao() {
	        return $this->token_recuperacao;
	    }

	    public function setTokenRecuperacao() {
			// Define as opções para o algoritmo de hash de senha.
			$opcoes = [
				'cost' => 11
			];

			// Gera uma chave aleatória usando 30 bytes de dados binários e a converte em uma string hexadecimal.
			$chave = bin2hex(random_bytes(30));

			// Gera um token de recuperação de senha único e seguro, combinando o tempo atual, um número aleatório e a chave gerada anteriormente.
			// O token é criptografado usando o algoritmo de hash de senha bcrypt, com as opções definidas anteriormente.
			$token_recuperacao = password_hash(time() . rand() . $chave, PASSWORD_BCRYPT, $opcoes) . bin2hex(random_bytes(98));

			// Atribui o token de recuperação gerado ao atributo 'token_recuperacao' do objeto atual.
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

	    // Atualiza o usuário.
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

		// Atualiza a senha
		public static function atualizarSenha($senha) {
			try {
				// Faz a alteração com base naquele usuário.
				$sql = Mysql::conectar()->prepare('UPDATE `usuarios` SET senha = ? WHERE usuario = ?');

				// Define o custo computacional
				$opcoes = [
					'cost' => 11
				];
	
				// Cria o hash da senha passada.
				$senha = password_hash($senha, PASSWORD_BCRYPT, $opcoes);
	
				$sql->execute(array($senha, $_SESSION['usuario']));

				return true;
			}

			catch(Exception $e) {
				Painel::alert("erro", "Erro ao atualizar a senha.");
			}
		}

		// Verifica se já existe um mesmo usuário cadastrado no sistema.
		public static function userExist($user) {
			try {
				$sql = Mysql::conectar()->prepare('SELECT `id` FROM `usuarios` WHERE usuario = ?');
				$sql->execute(array(filter_var($user, FILTER_SANITIZE_STRING)));
	
				return $sql->rowCount() == 1;
			}

			catch(Exception $e) {
				Painel::alert("erro", "Erro ao se conectar ao banco de dados");
			} 
		}

		// Verifica se já existe uma mesma matrícula já cadastrada no sistema.
		public static function matriculaJaCadastrada($matricula) {
			try {
				$sql = Mysql::conectar()->prepare('SELECT `matricula` FROM `usuarios` WHERE matricula = ?');
				$sql->execute(array(filter_var($matricula, FILTER_SANITIZE_STRING)));

				return $sql->rowCount() == 1;
			}

			catch(Exception $e) {
				Painel::alert("erro", "Erro ao se conectar ao banco de dados");
			} 
		}

		public static function returnData($data, $filtro) {
			$sql = Mysql::conectar()->prepare("SELECT nome, sobrenome, usuario, acesso, id FROM usuarios WHERE $filtro LIKE '%$data%' ORDER BY nome");	
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
					NULL,
					NULL,
					$value['acesso'],
					NULL,
					NULL,
					NULL,
					$value['id']
				);
			}
			
			return $usuario;
		}

		// Verifica se já existe um mesmo e-mail cadastrado no sistema.
		public static function emailJaCadastrado($email) {
			try {
				$sql = Mysql::conectar()->prepare('SELECT `email` FROM `usuarios` WHERE email = ?');
				$sql->execute(array(filter_var($email, FILTER_SANITIZE_EMAIL)));

				return $sql->rowCount() == 1;
			}

			catch(Exception $e) {
				Painel::alert("erro", "Erro ao se conectar ao banco de dados");
			} 
		}

		// Seleciona usuário específico.
		public static function select($query, $arr) {
			try {			
				$sql = Mysql::conectar()->prepare("SELECT usuario, nome, sobrenome, email, acesso, matricula, curso, id, is_ativada, is_bloqueado FROM usuarios WHERE $query");
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
					NULL,
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

			catch(Exception $e) {
				Painel::alert("erro", "Não foi possível atualizar o usuário.");
			}
		}

		// Envia o e-mail de confirmação.
		public function enviarEmailConfirmacao(Usuario $usuario) {
			try {
				// Defina as configurações de envio.
				$mail = new Email();
				$mail->addAdress($usuario);
				$mail->EmailConfirmacao($usuario);
				$mail->enviarEmail();
			}

			catch(Exception $e) {
				Painel::alert("erro", "Erro ao enviar o e-mail de confirmação, tente novamente mais tarde.");
			}
		}

		// Cadastra novos usuários
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
		
		// Válida as entradadas de cadastro.
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

			if(preg_match('/\s/', $login)) {
				Painel::alert('erro', 'O login não deve conter espaços');
				return;
			}

			if($login == '') {
				Painel::alert('erro', 'O login está vazio');
				return;
			}

			if($nome == '') {
				Painel::alert('erro', 'O nome está vazio');
				return;
			}

			if($sobrenome == '') {
				Painel::alert('erro', 'O sobrenome está vazio');
				return;
			}

			if($email == '') {
				Painel::alert('erro', 'O e-mail está vazio');
				return;
			}

			if($senha == '') {
				Painel::alert('erro', 'A senha está vazia');
				return;
			}

			if($cargo == '') {
				Painel::alert('erro', 'O cargo está vazio');
				return;
			}

			if(filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
				Painel::alert('erro', 'E-mai inválido, tente novamente.');
				return;
			}

			if(Usuario::emailJaCadastrado($email)){
				Painel::alert('erro', 'E-mail já cadastrado.');
				return;
			}

			if(Usuario::userExist($login)) {
				Painel::alert('erro', 'Selecione um login diferente');		
				return;
			}

			// Usuários de cargo Bolsista ou aluno devem ter obrigatoriamente matrícula e curso.
			if($cargo == 1 || $cargo == 2) {
				$matricula = filter_var($_POST['matricula'], FILTER_SANITIZE_NUMBER_INT);
				$curso = filter_var($_POST['curso'], FILTER_SANITIZE_STRING);
			}
			
			// Usuários de cargo Bolsista ou aluno devem ter obrigatoriamente e-mail @alu.ufc.br.
			if(($cargo == 1 || $cargo == 2) && $dominio[1] != "alu.ufc.br"){
				Painel::alert('erro', 'Alunos podem usar apenas e-mail @alu.ufc.br');
				return;
			}

			// Verifica duplicidade na mátricula, caso o cargo seja igual a Bolsista ou aluno.
			if(($cargo == 1 || $cargo == 2) && Usuario::matriculaJaCadastrada($matricula)) {
				Painel::alert("erro", "Matrícula já cadastrada no sistema");	
				return;
			}

			// Usuários de cargo Bolsista ou aluno devem ter 
			// obrigatoriamente uma matrícula de 6 dígitos.
			if(($cargo == 1 || $cargo == 2) && (strlen($matricula) > 6 || strlen($matricula) < 6)) {
				Painel::alert('erro', 'A matrícula deve ter 6 dígitos');
				return;
			}

			// Verifica se alguma imagem foi passada e se ela é válida
			if($imagem['name'] != '' && Painel::imagemValida($imagem) == false) {
				Painel::alert('erro', 'O formato especificado não é válido');
				return;
			}

			// Verifica se o nome do arquivo de imagem não está vazio. Se não estiver vazio, faz o upload do arquivo usando a função Painel::uploadFile(),
			// caso contrário, define a variável $imagem como uma string vazia
			$imagem['name'] != '' ? $imagem = Painel::uploadFile($imagem) : $imagem = "";

			$usuario = new Usuario($login, $nome, $sobrenome, $email, $imagem, $cargo, $matricula, $curso, $senha);


			if($usuario->cadastrarUsuario($usuario)){
				Painel::alert("sucesso", "Usuário cadastrado com sucesso. Um e-mail de confirmação foi enviado.");
			}
		}

		public static function validarEntradasAutoCadastro() {
			// Filtra e sanitiza as entradas do formulário
			$login = filter_var($_POST['user'], FILTER_SANITIZE_STRING);
			$nome = filter_var($_POST['nome'], FILTER_SANITIZE_STRING);
			$sobrenome = filter_var($_POST['sobrenome'], FILTER_SANITIZE_STRING);
			$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
			$senha = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
			$dominio = explode("@", $email);
			
			// Verifica se há espaços no login
			if(preg_match('/\s/', $login)) {
				Painel::alert('erro', 'O login não deve conter espaços');
				return;
			}

			// Verifica se o login está vazio
			if($login == '') {
				Painel::alert('erro', 'O login está vazio');
				return;
			}

			// Verifica se o nome está vazio
			if($nome == '') {
				Painel::alert('erro', 'O nome está vazio');
				return;
			}

			// Verifica se o sobrenome está vazio
			if($sobrenome == '') {
				Painel::alert('erro', 'O sobrenome está vazio');
				return;
			}

			// Verifica se o e-mail está vazio
			if($email == '') {
				Painel::alert('erro', 'O e-mail está vazio');
				return;
			}

			// Verifica se a senha está vazia
			if($senha == '') {
				Painel::alert('erro', 'A senha está vazia');
				return;
			}

			// Verifica se o e-mail possui um formato válido
			if(filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
				Painel::alert('erro', 'E-mail inválido, tente novamente.');
				return;
			}
			
			// Verifica se o domínio do e-mail é válido
			if($dominio[1] != "alu.ufc.br"){
				Painel::alert('erro', 'Alunos podem usar apenas e-mail @alu.ufc.br');
				return;
			}

			// Verifica se o e-mail já está cadastrado no sistema
			if(Usuario::emailJaCadastrado($email)){
				Painel::alert('erro', 'E-mail já cadastrado.');
				return;
			}
			
			// Verifica se o usuário já existe no sistema
			if(Usuario::userExist($login)) {
				Painel::alert('erro', 'Selecione um login diferente');
				return;    
			}
			
			// Cria um objeto de usuário com as informações fornecidas
			$usuario = new Usuario(
				$login,
				$nome,
				$sobrenome,
				$email,
				'',
				1,
				$_SESSION['dados_aluno']['matricula'], 
				$_SESSION['dados_aluno']['curso'],
				$senha
			);

			// Cadastra o usuário no sistema
			$usuario->cadastrarUsuario($usuario);

			// Emite um alerta de sucesso e redireciona para a página de login
			Painel::alert("sucesso", "Usuário cadastrado com sucesso. Um e-mail de confirmação foi enviado. Redirecionando para o login.");
			
			// Limpa algumas variáveis de sessão
			unset($_SESSION['continuar_cadastro']);
			unset($_SESSION['dados_aluno']);

			// Redireciona para a página de login
			redirectLogin();
		}


		public static function validarEntradasAtualizarUsuarios(Usuario $usuario){
			$nome = filter_var($_POST['nome'], FILTER_SANITIZE_STRING);
			$sobrenome = filter_var($_POST['sobrenome'], FILTER_SANITIZE_STRING);
			$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
			$id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);

			$cargo = $usuario->getAcesso();

			$matricula = "";
			$curso = "";
			
			// Pega o domínio do e-mail do usuário.
			$dominio = explode("@", $email);

			if($usuario->getAcesso() == 1 || $usuario->getAcesso() == 2) {
				$matricula = filter_var($_POST['matricula'], FILTER_SANITIZE_NUMBER_INT);
				$curso = filter_var($_POST['curso'], FILTER_SANITIZE_STRING);
			}

			// Verifica se o campo "nome" está vazio
			if ($nome == "") {
				Painel::alert('erro', 'Campo nome não pode estar vazio');
				return;
			}

			// Verifica se o campo "sobrenome" está vazio
			if ($sobrenome == "") {
				Painel::alert('erro', 'Campo sobrenome não pode estar vazio');
				return;
			}

			// Verifica se o campo "email" está vazio
			if ($email == "") {
				Painel::alert('erro', 'Campo email não pode estar vazio');
				return;
			}

			if(($usuario->getAcesso() == 1 || $usuario->getAcesso() == 2) && $matricula == '')  {
				Painel::alert("erro", "Matrícula vazia");
				return;
			}

			if(($usuario->getAcesso() == 1 || $usuario->getAcesso() == 2) && $dominio[1] != "alu.ufc.br"){
				Painel::alert('erro', 'Alunos ou bolsistas podem usar apenas e-mail @alu.ufc.br');
				return;
			}

			if(filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
				Painel::alert('erro', 'E-mai inválido, tente novamente.');
				return;
			}
			
			if(($usuario->getAcesso() == 1 || $usuario->getAcesso() == 2) && (strlen($matricula) > 6 || strlen($matricula) < 6)) {
				Painel::alert('erro', 'A matrícula deve ter 6 dígitos');
				return;
			}
			
			if(Usuario::emailJaCadastrado($email) && $usuario->getEmail() != $email){
				Painel::alert('erro', 'E-mail já cadastrado.');
				return;
			}

			if($_SESSION['acesso'] == 3) {
				$cargo = filter_var($_POST['acesso'], FILTER_SANITIZE_NUMBER_INT);
			}

			$usuario->setNome($nome);
			$usuario->setSobrenome($sobrenome);
			$usuario->setEmail($email);
			$usuario->setMatricula($matricula);
			$usuario->setCurso($curso);
			$usuario->setId($id);
			$usuario->setAcesso($cargo);

			if($usuario->atualizarUsuarios($usuario)){
				Painel::alert('sucesso', 'O usuário foi atualizado com sucesso');
			}
		}

		public static function validarEntradasAtualizarUsuario() {
			$nome = filter_var($_POST['nome'], FILTER_SANITIZE_STRING);
			$sobrenome = filter_var($_POST['sobrenome'], FILTER_SANITIZE_STRING);
			$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
			
			$imagem = $_FILES['imagem'];
			$imagem_atual = $_POST['imagem_atual'];
	
			$dominio = explode("@", $email);
					
			if($dominio[1] != "alu.ufc.br" && ($_SESSION['acesso'] == 1 || $_SESSION['acesso'] == 2)) {
				Painel::alert('erro', 'Alunos podem usar apenas e-mail @alu.ufc.br');
				return;
			}

			if(filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
				Painel::alert('erro', 'E-mai inválido, tente novamente.');
				return;
			}

			if(Usuario::emailJaCadastrado($email) && $_SESSION['email'] != $email){
				Painel::alert('erro', 'E-mail já cadastrado.');
				return;
			}

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
						// Atualiza a sessão atual do usuario após confirmar o botão.
						Painel::atualizarSessao($usuario);
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
					// Atualiza a sessão atual do usuario após confirmar o botão.
					Painel::atualizarSessao($usuario);
				}

				else {
					Painel::alert("erro", "Erro durante a atualização de dados.");
				}
			}
		}
		
		// Função responsável por atualizar os demais usuários.
		public static function atualizarUsuarios(Usuario $usuario) {
			try {
				$sql = Mysql::conectar()->prepare('UPDATE `usuarios` SET nome = ?, sobrenome = ?, email = ?, matricula = ?, curso = ?, is_ativada = ?, is_bloqueado = ?, acesso = ? WHERE id = ?');
		
				$sql->execute(array(
					$usuario->getNome(), 
					$usuario->getSobrenome(), 
					$usuario->getEmail(),
					$usuario->getMatricula(), 
					$usuario->getCurso(), 
					$usuario->isAtivada(), 
					$usuario->isBloqueado(), 
					$usuario->getAcesso(), 
					$usuario->getId()
				));

				return true;
			}

			catch(Exception $e) {
				Painel::alert("erro", "Erro ao atualizar o usuário");
			}
		}

		// Caso o usuário logado seja Super Administrador, retorna todos os usuários. 
		public static function selectAll($comeco = null, $final = null) {
			try {
				if($comeco == null and $final == null) {
					$sql = Mysql::conectar()->prepare("SELECT nome, sobrenome, usuario, acesso, id FROM usuarios WHERE id != ? ORDER BY nome");
				}

				else {
					$comeco = filter_var($comeco, FILTER_SANITIZE_NUMBER_INT);
					$final = filter_var($final, FILTER_SANITIZE_NUMBER_INT);
					$sql = Mysql::conectar()->prepare("SELECT nome, sobrenome, usuario, acesso, id FROM usuarios WHERE id != ? ORDER BY nome ASC LIMIT $comeco, $final;");
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
						NULL,
						NULL,
						$value['acesso'],
						NULL,
						NULL,
						NULL,
						$value['id']
					);
				}
				
				return $usuario;
			}
			
			catch(Exception $e) {
				Painel::alert("erro", "Erro ao se conectar ao banco de dados");
			}
		}

		// Seleciona usuários de baixo nível quando o usuário logado for 
		// Bolsista
		public static function selectBaixoAcesso($comeco = null, $final = null) {
			try {
				// As varíaveis $começo e $final são responsáveis por trabalhar 
				// a paginação dos resultados.
				if($comeco == null and $final == null) {
					$sql = Mysql::conectar()->prepare("SELECT nome, sobrenome, usuario, acesso, id FROM usuarios WHERE id != ? AND acesso != 3 ORDER BY nome");
				}

				// Ocorre aqui a limitação de resultados.
				else {
					$comeco = filter_var($comeco, FILTER_SANITIZE_NUMBER_INT);
					$final = filter_var($final, FILTER_SANITIZE_NUMBER_INT);
					$sql = Mysql::conectar()->prepare("SELECT nome, sobrenome, usuario, acesso, id FROM usuarios WHERE id != ? AND acesso != 3 ORDER BY nome ASC LIMIT $comeco, $final;");
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
						NULL,
						NULL,
						$value['acesso'],
						NULL,
						NULL,
						NULL,
						$value['id']
					);
				}
				
				return $usuario;
			}
			
			catch(Exception $e) {
				Painel::alert("erro", "Erro ao se conectar ao banco de dados");
			}
		}

		// Lista todos os usuários.
		public static function listaTodosUsuarios() {
			try {
				$sql = Mysql::conectar()->prepare("SELECT nome, acesso FROM usuarios ORDER BY nome");
			
				$sql->execute();
				$dados = $sql->fetchAll();
					
				if(empty($dados)) {
					return false;
				}

				foreach ($dados as $key => $value) {
					$usuario[$key] = new Usuario(
						NULL,
						$value['nome'],
						NULL,
						NULL,
						NULL,
						$value['acesso'],
						NULL,
						NULL,
						NULL,
						NULL
					);
				}
				
				return $usuario;
			}

			catch(Exception $e) {
				Painel::alert("erro", "Erro ao se conectar ao banco de dados");
			}
		}

		// Função de bloqueio de pedidos, recebe um objeto Usuário e o status de bloqueio.
		public static function bloquearPedidos(Usuario $usuario, $status) {
			// 0 -> Bloqueio de pedido desativado.
			// 1 -> Bloqueio de pedido ativo.

			// Realiza o bloqueio de pedido.
			if($status == 1) {
				$usuario->setBloqueado(1);
				if($usuario->atualizarUsuarios($usuario)){
					Painel::alert("sucesso", "Usuário está bloqueado de fazer pedidos. Uma notificação foi enviada.");
				}
			}

			// Realiza o desbloqueio de pedidos.
			else if($status == 0) {
				$usuario->setBloqueado(0);
				if($usuario->atualizarUsuarios($usuario)){
					Painel::alert("sucesso", "Usuário autorizado a fazer pedidos. Uma notificação foi enviada.");
				}
			}

			// Envia o e-mail a respeito do status de pedidos. 
			$mail = new Email();
			$mail->addAdress($usuario);
			$mail->EmailStatusPedidos($usuario);
			$mail->enviarEmail();
		}

		// Função de bloqueio de login, recebe um objeto Usuário e o status de bloqueio.
		public static function bloquearLogin(Usuario $usuario, $status) {
			// 0 -> Bloqueio de login desativado.
			// 1 -> Bloqueio de login ativo.

			// Realiza o bloqueio de login.
			if($status == 1) {
				$usuario->setAtivada(1);
				if($usuario->atualizarUsuarios($usuario)){
					Painel::alert("sucesso", "Usuário está autorizado a fazer login. Uma notificação foi enviada.");
				}
			}

			// Realiza o desbloqueio de login
			else if($status == 0) {
				$usuario->setAtivada(0);
				if($usuario->atualizarUsuarios($usuario)){
					Painel::alert("sucesso", "Usuário bloqueado de fazer login. Uma notificação foi enviada.");
				}
			}
			// Envia o e-mail a respeito do status de login. 
			$mail = new Email();
			$mail->addAdress($usuario);
			$mail->EmailStatusLogin($usuario);
			$mail->enviarEmail();
		}

		// Verifica a reconfirmação de conta.
		public static function reConfirmarConta(Usuario $usuario) {
			$usuario->setTokenConfirmacao();
			// Retorna em caso positivo a respeito do novo link.
			if(Usuario::novaConfirmacao($usuario)){
				Painel::alert("sucesso", "Um novo link de confirmação foi enviado");
			}
		}

		// Envia o nvo link de confirmação para o usuário, 
		// apenas o Super Adminsitrador pode fazer isso
		public static function novaConfirmacao(Usuario $usuario) {
			try {
				// Atualiza com um novo token de confirmação no usuário passado.
				$sql = Mysql::conectar()->prepare('UPDATE `usuarios` SET token_confirmacao = ? WHERE usuario = ?');
				
				// Envia a confirmação.
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
		
		// Faz a confirmação da conta.
		public static function confirmaConta() {
			// Limpa o token
			$token_confirmacao = filter_var($_GET['token_confirmacao'], FILTER_SANITIZE_STRING);

			try {
				// Procura pelo o toekn
				$sql = Mysql::conectar()->prepare('UPDATE `usuarios` SET token_confirmacao = ?, is_ativada = ? WHERE token_confirmacao = ?');

				// Limpa o token de confirmação e define a conta como ativada.
				$sql->execute(array("", 1, $token_confirmacao));
				return true;
			}

			catch(Exception $e) {
				Painel::alert("erro", "Erro ao conectar ao banco de dados");
			}
		}

		// Verifica se o token de validação é valido.
		public static function tokenConfirmacaoValido() {
			try{
				// Limpa o token
				$token_confirmacao = filter_var($_GET['token_confirmacao'], FILTER_SANITIZE_STRING);
				
				// Realiza a busca pelo o token.
				$sql = Mysql::conectar()->prepare("SELECT token_confirmacao FROM `usuarios` WHERE token_confirmacao = ?");
				$sql->execute(array($token_confirmacao));

				// Retorna verdade caso enconte o resultado.
				return $sql->rowCount() == 1;
			}

			catch(Exception $e) {
				Painel::alert("erro", "Erro ao conectar ao banco de dados");
			}
		}

		// Verifica se o token de recuperação é valido.
		public static function tokenRecuperacaoValido() {
			try {

				// Limpa o token
				$token_recuperacao = filter_var($_GET['token_recuperacao'], FILTER_SANITIZE_STRING);

				// Realiza a busca pelo o token.
				$sql = Mysql::conectar()->prepare("SELECT token_recuperacao FROM `usuarios` WHERE token_recuperacao = ?");
				$sql->execute(array($token_recuperacao));

				// Retorna verdade caso enconte o resultado.
				return $sql->rowCount() == 1;
			}

			catch(Exception $e) {
				Painel::alert("erro", "Erro ao conectar ao banco de dados");
			}
		}
		
		// Valida a recuperação de senha. 
		public static function validarRecuperarSenha() {
			// Verifica se o usuário está vazio.
			if($_POST['user'] == '') {
				Painel::alert("erro", "O usuário não foi passado. Tente novamente.");
				return;
			}
			
			// Limpa o user passado via POST
			$user = filter_var($_POST['user'], FILTER_SANITIZE_STRING);
			
			// Verifica se aquele user realmente existe e tenta recuperar a senha.
			if(Usuario::recuperarSenha(trim($user))) {
				Painel::alert("sucesso", "Se tudo estiver correto um e-mail com instruções será enviado para você.");
			} 
			
			else {
				Painel::alert("erro", "Esta conta pode não existir ou ela ainda não foi ativada.");
			}
		}
		
		// Valida a recuperação de usuário 
		public static function validarRecuperarUsuario() {
			// Verifica se o valor não é vazio.
			if($_POST['email'] == '') {
				Painel::alert("erro", "O e-mail não foi passado. Tente novamente.");
				return;
			}

			// Sanatiza o valor.
			$email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
			
			// Valida se o e-mail é valido.
			if(filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
				Painel::alert('erro', 'E-mai inválido, tente novamente.');
				return;
			}

			// Verifica se a conta realmente existe.
			if(Usuario::recuperarUsuario($email)) {
				Painel::alert("sucesso", "Se tudo estiver correto um e-mail com instruções será enviado para você.");
			}

			else {
				Painel::alert("erro", "Esta conta pode não existir ou ela ainda não foi ativada.");

			}
		}

		// Envia o e-mail de recuperação.
		public static function enviarRecuperacaoEmail(Usuario $usuario) {
			try {			
				// Define as configurações de envio do e-mail.
				$mail = new Email();
				$mail->addAdress($usuario);
				$mail->EmailRecuperacao($usuario);
				$mail->enviarEmail();
				
				return true;
			}

			catch(Exception $e) {
				Painel::alert("erro", "Erro ao enviar o e-mail, tente novamente mais tarde");
			}
		}
		
		// Recuperação de senha.
		public static function recuperarSenha($email) {
			try {
				// Verifica se encontra um único registro.
				$sql = Mysql::conectar()->prepare("SELECT usuario, nome, sobrenome, email FROM `usuarios` WHERE usuario = ? AND is_ativada = 1");

				$sql->execute(array($email));
				//Caso seja positivo, começa o processo de recuperação. 
				if($sql->rowCount() == 1) {
					$info = $sql->fetch();
					
					$usuario = new Usuario(
						$info['usuario'],
						$info['nome'], $info['sobrenome'], $info['email'],
						NULL, NULL, NULL, NULL
					);

					// Seta o token de recuperação.
					$usuario->setTokenRecuperacao();

					// Verifica se foi possível enviar o e-mail de recuperação.
					if(Usuario::enviarRecuperacaoEmail($usuario)) {
						// Atualiza o token de recuperação.
						$sql = Mysql::conectar()->prepare('UPDATE `usuarios` SET token_recuperacao = ? WHERE usuario = ?');
					
						$sql->execute(
							array(
								$usuario->getTokenRecuperacao(), 
								$usuario->getUsuario()
							)
						);
						// Return verdade caso seja bem-sucedido a operação. 
						return true;
					}
				}

				// Caso contrário retorna false. 
				return false;
			}

			catch(Exception $e) {
				Painel::alert("erro", "Erro ao conectar ao banco de dados");
			}
		}
		
		// Função responsável por recuperar o usuário. 
		public static function recuperarUsuario($email) {
			try {
				// Verifica se encontra 1 registro com o e-mail passado. 
				$sql = Mysql::conectar()->prepare("SELECT usuario, nome, sobrenome, email FROM `usuarios` WHERE email = ? AND is_ativada = 1");

				$sql->execute(array($email));

				// Caso retorna apenas 1 linha, então realiza o processo de recuperação.
				if($sql->rowCount() == 1) {
					$info = $sql->fetch();
					
					$usuario = new Usuario(
						$info['usuario'],
						$info['nome'], $info['sobrenome'], $info['email'],
						NULL, NULL, NULL, NULL
					);

					// Envia o e-mail com o usuário.
					$mail = new Email();
					$mail->addAdress($usuario);
					$mail->EmailRecuperarUsuario($usuario);
					$mail->enviarEmail();

					// Caso seja positivo, retorna true para a chamada.
					return true;

				}
				// Do contrário retorna falso. 
				return false;
			}

			catch(Exception $e) {
				Painel::alert("erro", "Erro ao conectar ao banco de dados");
			}
		}

		// Função responsável por gerar o hash da nova senha
		public static function novaSenha() {
			// Pega o valor da nova senha
			$senha = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
			// Filtra o valor do token_recuperação.
			$token_recuperacao = filter_var($_GET['token_recuperacao'], FILTER_SANITIZE_STRING);
			
			$usuario = new Usuario(
				NULL, NULL, NULL,
				NULL, NULL, NULL, NULL, NULL, $senha
			);

			// Seta o token de recuperação.
			$usuario->token_recuperacao = $token_recuperacao;
			
			try {
				// Procura por registros com aquele único token
				$sql = Mysql::conectar()->prepare('UPDATE `usuarios` SET token_recuperacao = ?, senha = ? WHERE token_recuperacao = ?');
				//Atualiza a nova senha. 
				$sql->execute(array("", $usuario->getSenha(), $usuario->getTokenRecuperacao()));

				return true;
			}

			catch(Exception $e) {
				Painel::alert("erro", "Erro ao conectar ao banco de dados");
			}
		}

		public static function getNomeCompletoById($id) {
			try {
				$sql = Mysql::conectar()->prepare('SELECT nome, sobrenome FROM usuarios WHERE id = ?');
				$sql->execute(array($id));
				$dados = $sql->fetch();

				if ($dados) {
					return $dados['nome'] . ' ' . $dados['sobrenome'];
				} else {
					return "Informação de usuário não encontrada.";
				}
			} catch (Exception $e) {
				Painel::alert("erro", "Erro ao se conectar ao banco de dados.");
				return false;
			}
		}


		# Função não utilizada no momento.
		// public static function deletar($id) {
		// 	try {
		// 		$sql = Mysql::conectar()->prepare('DELETE FROM `usuarios` WHERE id = ?');
		// 		$sql->execute(array(filter_var($id, FILTER_SANITIZE_NUMBER_INT)));
		// 	}

		// 	catch(Exception $e) {
		// 		Painel::alert("erro", "Erro ao deletar usuário do banco de dados");
		// 	}
		// }
	}
?>
