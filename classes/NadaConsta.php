<?php

class NadaConsta {
    // Atributos da classe
    private $id; // Identificador único do registro
    private $data; // Data de emissão do nada consta
    private $codigo_seguranca; // Código de segurança para validação do documento
    private $id_usuario; // ID do usuário associado ao nada consta
    public $usuario; // Objeto do tipo Usuario associado ao nada consta
    private $valido; // Indicador de validade do nada consta

    // Construtor da classe
    public function __construct($id, $data, $codigo_seguranca, $id_usuario, $valido = NULL,  Usuario $usuario = NULL) {
        $this->setId($id);
        $this->setData($data);
        $this->setCodigoSeguranca($codigo_seguranca);
        $this->setIdUsuario($id_usuario);
        $this->usuario = $usuario;
        $this->setValido($valido);
    }

    // Método estático para invalidar um nada consta
    public static function invalidarNadaConsta($idUsuario) {
        // Prepara e executa uma consulta SQL para invalidar o nada consta de um usuário
        $sql = Mysql::conectar()->prepare("UPDATE nadaconsta SET valido = 0 WHERE id_usuario = ?");
        $sql->execute(array($idUsuario));

    }

    // Método para cadastrar um novo nada consta
    public function cadastrar(NadaConsta $nadaConsta){
        try {
            // Invalida todos os outros nada consta do usuário antes de cadastrar o novo
            NadaConsta::invalidarNadaConsta($nadaConsta->getIdUsuario());
            // Prepara e executa uma consulta SQL para cadastrar o novo nada consta
            $sql = Mysql::conectar()->prepare("INSERT INTO nadaconsta (id, data, codigo_seguranca, id_usuario) VALUES (DEFAULT, ?, ?, ?);");
            $sql->execute(
                array(
                    $nadaConsta->getData(), $nadaConsta->getCodigoSeguranca(),
                    $nadaConsta->getIdUsuario()
                )
            );

            return true;
        }

        catch(Exception $e) {
            // Em caso de erro, exibe uma mensagem de alerta
            Painel::alert("erro", "Erro ao se conectar ao banco de dados");
        }
    }

    // Método estático para selecionar um nada consta específico
    public static function select($query, $arr) {
        try {
            // Prepara e executa uma consulta SQL para selecionar o nada consta com base na condição fornecida
            $sql = Mysql::conectar()->prepare("SELECT * FROM nadaconsta WHERE $query");
            $sql->execute($arr);
    
            $dados = $sql->fetch();
    
            if(empty($dados)) {
                return false;
            }
    
            // Cria um objeto NadaConsta com os dados obtidos do banco de dados
            $nadaConsta = new NadaConsta(
                $dados['id'],
                $dados['data'],
                $dados['codigo_seguranca'],
                $dados['id_usuario'],
                (int) $dados['valido']
            );
    
            return $nadaConsta;
        }

        catch (Exception $e) {
            // Em caso de erro, exibe uma mensagem de alerta
            Painel::alert("erro", "Erro ao se conectar ao banco de dados");
        }
    }
    
    // Métodos Getters e Setters para acessar e modificar os atributos

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getData() {
        return $this->data;
    }

    public function setValido($valido) {
        $this->valido = $valido;
    }

    public function getValido() {
        return $this->valido;
    }

    public function setData($data) {
        $this->data = $data;
    }

    public function getCodigoSeguranca() {
        return $this->codigo_seguranca;
    }

    public function setCodigoSeguranca($codigo_seguranca) {
        $this->codigo_seguranca = $codigo_seguranca;
    }

    public function getIdUsuario() {
        return $this->id_usuario;
    }

    public function setIdUsuario($id_usuario) {
        $this->id_usuario = $id_usuario;
    }

    // Método estático para validar a emissão de um nada consta
    public static function validarEmissao(Usuario $usuario) {    
        // Verifica se o usuário tem pedidos ativos
        if (PedidoDetalhes::retornaPedidosAtivosUsuario()) {
            Painel::alert("erro", "Você tem pedidos ativos no momento.");
            return;
        } 
        
        // Verifica se o usuário tem pedidos pendentes
        if (PedidoDetalhes::retornaPedidosPendentesUsuario()) {
            Painel::alert("erro", "Você tem pedidos aguardando aprovação no momento.");
            return;
        } 
        
        // Verifica se o usuário está bloqueado de fazer pedidos
        if ($usuario->isBloqueado()) {
            Painel::alert("erro", "Você está bloqueado de fazer pedidos devido a pendências, contate o administrador do sistema para mais informações.");
            return;
        }
    
        return true;
    }

    // Método estático para validar um nada consta
    public static function validarNadaConsta() {
        // Verifica se foi fornecido um código de segurança válido
        if(!isset($_GET['codigo']) || strlen($_GET['codigo']) > 30 || strlen($_GET['codigo']) < 30 ) {
            Painel::alert("erro", "Código de segurança inválido, verifique e digite novamente");
            return;
        }

        // Filtra e sanitiza o código de segurança
        $codigoSeguranca = filter_var($_GET['codigo'], FILTER_SANITIZE_STRING);
        
        // Seleciona o nada consta com base no código de segurança fornecido
        $nadaConsta = NadaConsta::select('codigo_seguranca = ?', array($codigoSeguranca));
        
        // Verifica se o nada consta foi encontrado
        if($nadaConsta == false) {
            Painel::alert("erro", "O código de segurança não foi encontrado com relação a nenhum documento, tente novamente.");
            return;
        }

        // Verifica se o nada consta é válido
        if(!$nadaConsta->getValido()) {
            Painel::alert("erro", "Nada Consta inválido, é necessário a emissão de um novo.");
            return;
        }
        
        // Seleciona o usuário associado ao nada consta
        $usuario = Usuario::select('id = ?', array($nadaConsta->getIdUsuario()));

        // Verifica se houve alterações após a emissão do nada consta
        if(PedidoDetalhes::verificaPedidosPendentesID($usuario)) {
            Painel::alert("erro", "Houveram alterações após a emissão deste documento devido a pedidos pendentes de análise, verifique junto ao administrador.");
            NadaConsta::invalidarNadaConsta($nadaConsta->getIdUsuario());
            return;  
        }

        if(PedidoDetalhes::verificaPedidosAtivoID($usuario)) {
            Painel::alert("erro", "Houveram alterações após a emissão deste documento devido a pedidos ativos, verifique junto ao administrador.");
            NadaConsta::invalidarNadaConsta($nadaConsta->getIdUsuario());
            return;  
        }

        if($usuario->isBloqueado()) {
            Painel::alert("erro", "Houveram alterações após a emissão deste documento devido ao bloqueio da função de pedidos do usuário, verifique junto ao administrador."); 
            NadaConsta::invalidarNadaConsta($nadaConsta->getIdUsuario());
            return;  
        }

        // Converte a data do nada consta para o formato brasileiro
        $dataConvertida = implode("/",array_reverse(explode("-",htmlentities($nadaConsta->getData()))));
        // Exibe uma mensagem de sucesso indicando que o nada consta é válido
        Painel::alert(
            "sucesso", 
            "O documento emitido para " . htmlentities($usuario->getNome()) . 
            " " . htmlentities($usuario->getSobrenome()) . ", matrícula "
            . htmlentities($usuario->getMatricula()) . 
            ", no dia " . $dataConvertida . " é válido."
        );
    }
    
    // Método estático para gerar um nada consta
    public static function geraNadaConsta(int $acaoNadaConsta) {
        // Seleciona o usuário atualmente logado
        $usuario = Usuario::select('id = ?', array($_SESSION['id']));
        // Obtém a data atual
        $dataHoje = date("Y-m-d");
        
        // Valida a emissão do nada consta para o usuário atual
        if(NadaConsta::validarEmissao($usuario)) {
            // Gera um código de segurança aleatório
            $chave = bin2hex(random_bytes(15));

            // Cria um novo objeto NadaConsta
            $nadaConsta = new NadaConsta(NULL, $dataHoje, $chave, $usuario->getId(), NULL, $usuario);
            // Cadastra o novo nada consta no banco de dados
            $nadaConsta->cadastrar($nadaConsta);

            // Gera o PDF do nada consta
            $nadaConstaPDF = new NadaConstaPDF();
            $nadaConstaPDF->nadaconsta($nadaConsta, $acaoNadaConsta);

            // Envia o PDF por e-mail, se a ação for de envio
            if($acaoNadaConsta == 2) {
                $mail = new Email();
                
                $usuario = new Usuario(
                    NULL, $usuario->getNome(),
                    $usuario->getSobrenome(),
                    $usuario->getEmail(),
                    NULL, NULL, NULL, NULL
                );

                $mail->addAdress(
                    $usuario
                );
    
                $mail->EmailNadaConsta($nadaConsta);
                $mail->enviarEmail();

                // Exclui o nada consta após o envio do e-mail
                Painel::deleteNadaConsta($nadaConsta);
                // Exibe uma mensagem de sucesso
                Painel::alert("sucesso", "Tudo certo! O arquivo foi enviado para o seu e-mail.");
            }
        }
    }    
}
?>
