<?php

/**
 * Class Historico de bloqueios
 * 
 */
class HistoricoBloqueios {
    private $id;
    private $id_usuario;
    private $id_usuario_admin;
    private $id_usuario_admin_desbloqueio;
    private $data_bloqueio;
    private $data_desbloqueio;
    private $motivo_bloqueio;
    private $motivo_desbloqueio;
    
    
    public function __construct($id, $id_usuario, $id_usuario_admin, $id_usuario_admin_desbloqueio, $data_bloqueio, $data_desbloqueio, $motivo_bloqueio, $motivo_desbloqueio){
        $this->setIdBloqueio($id);
        $this->setIdUsuario($id_usuario);
        $this->setIdUsuarioAdmin($id_usuario_admin);
        $this->setIdUsuarioAdminDesbloqueio($id_usuario_admin_desbloqueio);
        $this->setDataBloqueio($data_bloqueio);
        $this->setDataDesbloqueio($data_desbloqueio);
        $this->setMotivoBloqueio($motivo_bloqueio);
        $this->setMotivoDesbloqueio($motivo_desbloqueio);
    }

    // duas funções novas pra amanhã
    //cadastrarmotivodesbloqueio cadastrarmotivodesbloqueio
    //ou pode ser so uma



    /**
    * Busca USUÁRIOS com histórico de bloqueio, com filtros e paginação.
    *
    * @param int|null $comeco O índice inicial para paginação (OFFSET).
    * @param int|null $final O número de registros a serem retornados (LIMIT).
    * @param string $termoBusca O texto para busca
    * @param string $filtroColuna A coluna onde buscar
    *
    * @return array|false Retorna um array associativo com os dados dos usuários.
    */
    public static function selectAll($comeco = null, $final = null, $termoBusca = "", $filtroColuna = "usuarios.nome"){
        try{
            $params = [];
            $whereClause = "";
            $limitClause = "";
 
            $colunasPermitidas = [
                'usuarios.nome' => 'u.nome',
                'usuarios.matricula' => 'u.matricula',
                'usuarios.email' => 'u.email'
            ];

            $colunaSql = $colunasPermitidas['usuarios.nome']; 
            if (array_key_exists($filtroColuna, $colunasPermitidas)) {
                $colunaSql = $colunasPermitidas[$filtroColuna];
            }
        
            if (!empty($termoBusca)) {
                $whereClause = "WHERE $colunaSql LIKE :termoBusca";
                $params[':termoBusca'] = "%$termoBusca%";
            }

            if ($comeco !== null && $final !== null) {
                $limitClause = "LIMIT :comeco, :final";
            }

            $sql = "
                SELECT
                    u.id,
                    u.nome,
                    u.sobrenome,
                    u.email,
                    u.matricula,
                    u.is_bloqueado,
                    COUNT(h.id) as total_bloqueios
                FROM
                    usuarios as u
                INNER JOIN
                    historico_bloqueios as h ON u.id = h.id_usuario
                $whereClause
                GROUP BY
                    u.id, u.nome, u.sobrenome, u.matricula, u.is_bloqueado
                ORDER BY
                    u.nome ASC
                $limitClause
            ";

            $stmt = Mysql::conectar()->prepare($sql);

            if (!empty($termoBusca)) {
                $stmt->bindParam(':termoBusca', $params[':termoBusca'], PDO::PARAM_STR);
            }

            if ($comeco !== null && $final !== null) {
                $stmt->bindValue(':comeco', (int)$comeco, PDO::PARAM_INT);
                $stmt->bindValue(':final', (int)$final, PDO::PARAM_INT);
            }

            $stmt->execute();

            $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if(empty($dados)){
                return false;
            }
            
    
            return $dados; 

        } catch(Exception $e){
            throw new Exception("Erro ao buscar histórico: " . $e->getMessage());
        }
    }



    /**
     * Conta o nuemro total de usuario unicos que corespondem a uma busca
     * usado para calcular também a paginação a page 'consultar-bloqueios'
     * @param string $termoBusca é matricula email ou nome do Aluno
     * @param string $filtroColuna é o filtro matricula emial ou nome que foi escolhido
     * @return int numero total de usuarios que foram encontrados na busca
     */
    public static function contarTotalBusca($termoBusca = "", $filtroColuna = "usuarios.nome") {
        try {
            $params = [];
            $whereClause = "";
            
            $colunasPermitidas = [
                'usuarios.nome' => 'u.nome',
                'usuarios.matricula' => 'u.matricula',
                'usuarios.email' => 'u.email'
            ];

            $colunaSql = $colunasPermitidas['usuarios.nome']; 
            if(array_key_exists($filtroColuna, $colunasPermitidas)) {
                $colunaSql = $colunasPermitidas[$filtroColuna];
            }
            
            if(!empty($termoBusca)) {
                $whereClause = "WHERE $colunaSql LIKE :termoBusca";
                $params[':termoBusca'] = "%$termoBusca%";
            }

            $sql = "
                SELECT
                    COUNT(DISTINCT u.id)
                FROM
                    usuarios as u
                INNER JOIN
                    historico_bloqueios as h ON u.id = h.id_usuario
                $whereClause
            ";

            $stmt = Mysql::conectar()->prepare($sql);

            if (!empty($termoBusca)) {
                $stmt->bindParam(':termoBusca', $params[':termoBusca'], PDO::PARAM_STR);
            }

            $stmt->execute();
            
            return $stmt->fetchColumn();

        } catch(Exception $e) {
            throw new Exception("Erro ao contar histórico: " . $e->getMessage());
        }
    }

    /**
     * Função que busca todas as informações uteis para um relatorio dos bloqueios que ele teve
     *
     * @param int $id_usuario
     * @return array|false retorna Array associativo caso de sucesso em caso erro, lança exeption ou retorna false
     */
    public static function getHistoricoCompleto($id_usuario){
        try {
            $sql ="
                SELECT 
                    hb.*, 
                    admin_bloqueio.nome AS admin_bloqueio_nome,
                    admin_bloqueio.sobrenome AS admin_bloqueio_sobrenome,

                    admin_desbloqueio.nome AS admin_desbloqueio_nome,
                    admin_desbloqueio.sobrenome AS admin_desbloqueio_sobrenome
                FROM 
                    historico_bloqueios AS hb
                JOIN 
                    usuarios AS admin_bloqueio ON hb.id_usuario_admin = admin_bloqueio.id
                LEFT JOIN 
                    usuarios AS admin_desbloqueio ON hb.id_usuario_admin_desbloqueio = admin_desbloqueio.id
                WHERE 
                    hb.id_usuario = :id_usuario
                ORDER BY 
                    hb.data_bloqueio DESC
            ";

            $stmt = Mysql::conectar()->prepare($sql);
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $stmt->execute();
            
            $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if(empty($dados)){
                return false;
            }

            return $dados;

        } catch(Exception $e){
            throw new Exception("Erro ao buscar historico do aluno", $e->getMessage());
        }
    }


    /**
     * Cria o registro de bloqueio para um aluno
     *
     * @param int $id_usuario
     * @param int $id_usuario_admin
     * @param string $motivo
     * @return bool|exeption Retorna true em caso de sucesso, false ou exeption em caso de falha
     */
    public static function criarBloqueio($id_usuario, $id_usuario_admin, $motivo){

        if($id_usuario <= 0 || $id_usuario_admin <= 0 || trim($motivo) === '') {
            return false;
        }
        try{
            $sql = Mysql::conectar()->prepare("
                INSERT INTO
                    historico_bloqueios
                    (id_usuario, id_usuario_admin, data_bloqueio, motivo_bloqueio)
                VALUES 
                    (?, ?, NOW(), ?)
            "
            );
            
            return $sql->execute([$id_usuario, $id_usuario_admin, $motivo]);


        } catch (Exception $e){
            throw new Exception("Erro ao criar registro do bloqueio: " . $e->getMessage());
        }
    }

    public static function finalizarBloqueio($id_bloqueio , $id_usuario_admin_desbloqueio, $motivo_bloqueio){
        if($id_bloqueio <= 0 || $id_usuario_admin_desbloqueio <= 0 || trim($motivo_bloqueio) === '') {
            return false;
        }
        try{
            $sql = Mysql::conectar()->prepare("
                UPDATE 
                    historico_bloqueios
                SET 
                    id_usuario_admin_desbloqueio = ?,
                    data_desbloqueio = NOW(),
                    motivo_desbloqueio = ?
                WHERE 
                    id = ?
            "
            );
            
            return $sql->execute([$id_usuario_admin_desbloqueio, $motivo_bloqueio, $id_bloqueio]);
            
            } catch (Exception $e){
            throw new Exception("Erro ao finalizar o bloqueio: " . $e->getMessage());
        }
    }


    /**
     * Pega o id do usuario e usa pra achar seu ultimo bloqueio comparando data desbloqueio
     * @param int $id_usuario o id do aluno que esta bloqueado
     * @return int|false Retorna o ID do bloqueio (ex: 42) ou false se não achar
     */
    public static function getIdBloqueioPendente($id_usuario){
        

        // ajustar aqui a condição
        if(!is_numeric($id_usuario) && empty($id_usuario)){
            return false;
        }
        
        try{
            $sql = Mysql::conectar()->prepare("
                SELECT 
                    id
                FROM
                    historico_bloqueios
                WHERE
                    id_usuario = ?
                AND
                    data_desbloqueio IS NULL

                ORDER BY id DESC
                LIMIT 1
            ");

            $sql->execute([$id_usuario]);

            $resultado = $sql->fetchColumn();
            
            return $resultado;

        } catch(Exception $e){
            throw new Exception($e->getMessage() . "Erro ao pegar id bloqueio");
        }

    }

    
    public function getIdBloqueio() {
        return $this->id;
    }

    public function setIdBloqueio($id) {
        $this->id = $id;
    }

    public function getIdUsuario() {
        return $this->id_usuario;
    }

    public function setIdUsuario($id_usuario) {
        $this->id_usuario = $id_usuario;
    }

    public function getIdUsuarioAdmin() {
        return $this->id_usuario_admin;
    }

    public function setIdUsuarioAdmin($id_usuario_admin) {
        $this->id_usuario_admin = $id_usuario_admin;
    }

    public function getIdUsuarioAdminDesbloqueio() {
        return $this->id_usuario_admin_desbloqueio;
    }

    public function setIdUsuarioAdminDesbloqueio($id_usuario_admin_desbloqueio) {
        $this->id_usuario_admin_desbloqueio = $id_usuario_admin_desbloqueio;
    }

    public function getDataBloqueio() {
        return $this->data_bloqueio;
    }

    public function setDataBloqueio($data_bloqueio) {
        $this->data_bloqueio = $data_bloqueio;
    }

    public function getDataDesbloqueio() {
        return $this->data_desbloqueio;
    }

    public function setDataDesbloqueio($data_desbloqueio) {
        $this->data_desbloqueio = $data_desbloqueio;
    }

    public function getMotivoBloqueio() {
        return $this->motivo_bloqueio;
    }

    public function setMotivoBloqueio($motivo_bloqueio) {
        $this->motivo_bloqueio = $motivo_bloqueio;
    }

    public function getMotivoDesbloqueio() {
        return $this->motivo_desbloqueio;
    }

    public function setMotivoDesbloqueio($motivo_desbloqueio) {
        $this->motivo_desbloqueio = $motivo_desbloqueio;
    }

   public function isBloqueado() {
        return empty($this->data_desbloqueio);
    } 


    //debug 
    public function __toString() {
        return "Bloqueio ID {$this->id} | Usuário: {$this->id_usuario} | " .
               "Admin Bloqueio: {$this->id_usuario_admin} | Data: {$this->data_bloqueio} | " .
               "Motivo: {$this->motivo_bloqueio} | " .
               ($this->data_desbloqueio ? "Desbloqueado em {$this->data_desbloqueio} por {$this->id_usuario_admin_desbloqueio}" : "Ainda bloqueado");
    }
}

?>
