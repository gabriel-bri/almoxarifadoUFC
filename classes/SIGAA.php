<?php  
   
   class SIGAA {
      private $curl;
      private $dados;

      public function chamarAPI() {
         $url = "http://almoxarifadoec.quixada.ufc.br:3000/sigaa";
     
         // Inicializa uma requisição cURL para a URL da API
         $this->curl = curl_init($url);
         curl_setopt($this->curl, CURLOPT_URL, $url);
         curl_setopt($this->curl, CURLOPT_POST, true); // Define o método POST
         curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true); // Habilita o retorno da resposta
     
         // Define os cabeçalhos da requisição
         $headers = array(
            "Content-Type: application/json",
         );
         
         curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);
     }
     
     public function passarDados($user, $senha) {
         // Prepara os dados para serem enviados na requisição POST
         $data = '{"login": "' . $user . '", "senha": "' . $senha . '"}';
         curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
     
         // Configurações para desativar a verificação SSL (apenas para debug)
         curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);
         curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
      }
     
     public function pegarDados() {
         // Executa a requisição e obtém a resposta
         $resp = curl_exec($this->curl);
         curl_close($this->curl); // Fecha a conexão cURL
         // Decodifica a resposta JSON para um objeto PHP
         $this->dados = json_decode($resp);
         return $this->dados;
      }
     
     public static function validarEntradas() {
         // Verifica se os campos de usuário e senha foram preenchidos no formulário
         if($_POST['user'] == '' || $_POST['password'] == '') {
             Painel::alert("erro", "Campos vazios não são permitidos.");
             return;
         }
     
         // Filtra e sanitiza os dados do formulário para evitar ataques de injeção
         $usuario = filter_var($_POST['user'], FILTER_SANITIZE_STRING);
         $senha = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
     
         // Instancia a classe SIGAA para interagir com a API
         $sigaa = new SIGAA();
         $sigaa->chamarAPI();
         $sigaa->passarDados($usuario, $senha);
         $dadosSIGAA = $sigaa->pegarDados();
     
         // Verifica se houve problemas ao conectar com a API
         if($dadosSIGAA == null) {
            Painel::alert("erro", "Problemas ao conectar com a API externa, tente novamente, se o erro persistir contate o administrador");
            return;
         }
     
         // Verifica se os dados retornados da API indicam erro ou 
         // se não há cadeiras disponíveis
         if(count($dadosSIGAA->cadeiras) == 0 || $dadosSIGAA->error) {
            Painel::alert("erro", "Dados não encontrados, verifique o login e/ou a senha.");
            return;
         }
     
         // Verifica se o aluno pertence à UFC Quixadá
         if(strpos($dadosSIGAA->cadeiras[0]->local, 'Quixadá') === false){
            Painel::alert("erro", "Este sistema é de uso exclusivo para alunos da UFC Quixadá, caso necessite entre em contato com o administrador.");
            return;	
         }
     
         // Verifica se a matrícula do aluno já está cadastrada no sistema
         if(Usuario::matriculaJaCadastrada($dadosSIGAA->matricula)) {
            Painel::alert("erro", "Matrícula já cadastrada no sistema");
            return;	
         }
     
         // Tratamento do nome do curso retornado pela API
         $dadosSIGAA->curso = str_replace(substr($dadosSIGAA->curso, -4), '', $dadosSIGAA->curso);
         $arrayNomeCurso = explode(" ", $dadosSIGAA->curso);
         // Corrige o formato do nome do curso
         if(count($arrayNomeCurso) == 3) {
            $dadosSIGAA->curso = ucfirst(mb_strtolower($arrayNomeCurso[0])) . ' ' . strtolower($arrayNomeCurso[1]) . ' ' . ucfirst(mb_strtolower($arrayNomeCurso[2]));
         }
      
         if(count($arrayNomeCurso) == 2) {
            $dadosSIGAA->curso = ucfirst(mb_strtolower($arrayNomeCurso[0])) . ' ' . ucfirst(mb_strtolower($arrayNomeCurso[1]));
         }
     
         // Prepara os dados do aluno para serem armazenados na sessão
         $dadosAluno = [
            'matricula' => $dadosSIGAA->matricula,
            'curso' => array_search($dadosSIGAA->curso, Usuario::$cursos)
         ];
     
         // Armazena os dados do aluno na sessão
         if(!isset($_SESSION['continuar_cadastro'])) {
            $_SESSION['continuar_cadastro']  = true;
            $_SESSION['dados_aluno'] = $dadosAluno;
         }
     
         // Exibe uma mensagem de sucesso e redireciona o usuário
         Painel::alert("sucesso", "Dados encontrados, o processo de cadastro irá continuar agora.");
         redirect();
     }
     
   }
?>