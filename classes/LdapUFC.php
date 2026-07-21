<?php

class LdapUFC {

    private $conn = null;
    private $dados = null;

    public function chamarLDAP() {
        $this->conn = ldap_connect(LDAP_HOST, LDAP_PORT);
        ldap_set_option($this->conn, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($this->conn, LDAP_OPT_REFERRALS, 0);

        if (!@ldap_bind($this->conn, BIND_DN, BIND_PASS)) {
          $this->conn = null;
         }

         return true;
    }

    public function pegarDadosLDAP($cpf) {
        if (!$this->conn) return ['erro' => 'conexao']; // conn falhou
    
        $cpf_escaped = ldap_escape($cpf, '', LDAP_ESCAPE_FILTER);
        $result = @ldap_search($this->conn, LDAP_BASE, "(brPersonCPF={$cpf_escaped})", ['*']);
        if (!$result) return ['erro' => 'busca']; // busca falhou
    
        $entries = ldap_get_entries($this->conn, $result);
        if ($entries['count'] === 0) return ['erro' => 'nao_encontrado']; // CPF não existe
    
        $this->dados = $entries[0];
        return $this->dados;
    }

    public function fecharConexao() {
        if ($this->conn) {
            ldap_close($this->conn);
            $this->conn = null;
        }
    }

    public static function validarEntradas() {
        if ($_POST['user'] == '') {
            Painel::alert("erro", "Campos vazios não são permitidos.");
            return;
        }
        
        if (!preg_match('/^[0-9]+$/', $_POST['user'])) {
            Painel::alert("erro", "CPF deve conter apenas números.");
            return;
        }
        
        if (strlen($_POST['user']) != 11) {
            Painel::alert("erro", "CPF deve conter 11 dígitos.");
            return;
        }

        $usuario = strip_tags($_POST['user']);

        $ldap = new LdapUFC();
        $ldap->chamarLDAP();
        $dadosLDAP = $ldap->pegarDadosLDAP($usuario);
        $ldap->fecharConexao();

        if (isset($dadosLDAP['erro'])) {
            switch ($dadosLDAP['erro']) {
                case 'conexao':
                case 'busca':
                    Painel::alert("erro", "Problemas ao conectar com o LDAP, tente novamente. Se o erro persistir contate o administrador.");
                    return;
                case 'nao_encontrado':
                    Painel::alert("erro", "CPF não encontrado. Verifique o CPF informado.");
                    return;
            }
        }
      
        if ($dadosLDAP === null) {
            Painel::alert("erro", "Problemas ao conectar com o LDAP, tente novamente. Se o erro persistir contate o administrador.");
            return;
        }

        if (empty($dadosLDAP)) {
            Painel::alert("erro", "CPF não encontrado. Este sistema é de uso exclusivo para usuários da UFC Quixadá.  Verifique o CPF informado.");
            return;
        }

        $matricula = $dadosLDAP['matricula'][0] ?? null;

        if (!$matricula) {
            Painel::alert("erro", "Não foi possível identificar a matrícula no LDAP.");
            return;
        }

        if (Usuario::matriculaJaCadastrada($matricula)) {
            Painel::alert("erro", "Matrícula já cadastrada no sistema.");
            return;
        }

         // Prepara os dados do aluno para serem armazenados na sessão
         $dadosAluno = [
            'matricula' => $matricula,
            'nome' => ucwords(strtolower($dadosLDAP['cn'][0])),
            'sobrenome' => ucwords(strtolower($dadosLDAP['sn'][0])),
            'curso' => array_search($dadosLDAP['curso'][0], Usuario::$cursos)
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
