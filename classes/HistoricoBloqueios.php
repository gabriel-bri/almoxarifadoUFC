<?php
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



    /**
    * Busca USUÁRIOS com histórico de bloqueio, com filtros e paginação.
    *
    * @param int|null $comeco O índice inicial para paginação (OFFSET).
    * @param int|null $final O número de registros a serem retornados (LIMIT).
    * @param string $termoBusca O texto para busca
    * @param string $filtroColuna A coluna onde buscar
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
                // $final aqui está sendo usado como a *quantidade* (ex: 10)
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
            if (array_key_exists($filtroColuna, $colunasPermitidas)) {
                $colunaSql = $colunasPermitidas[$filtroColuna];
            }
        
            if (!empty($termoBusca)) {
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
