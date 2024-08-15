<?php
    class PedidoDetalhes {
        private $id;
        private $id_usuario;
        private $aprovado;
        private $finalizado;
        private $data_pedido;
        private $data_finalizado;
        private $codigo_pedido;
        private $id_usuario_aprovou;
        private $id_usuario_finalizou;
        public $usuario;
        public $pedidos;

        public function __construct(
            $id_usuario, $codigo_pedido, $id = NULL,
            $aprovado = null, $finalizado = null,
            $data_pedido = NULL, $data_finalizado = NULL,
            Usuario $usuario = NULL, Pedido $pedidos = NULL) {
            $this->setIdUsuario($id_usuario);
            $this->setDataPedido($data_pedido);
            $this->setCodigoPedido($codigo_pedido);
            $this->setId($id);
            $this->setAprovado($aprovado);
            $this->setFinalizado($finalizado);
            $this->setDataFinalizado($data_finalizado);
            $this->usuario = $usuario;
            $this->pedidos = $pedidos;
        }

        public static function cadastrarPedido(PedidoDetalhes $pedidoDetalhes) {
            try {
                // 2001-03-10 (the MySQL DATETIME format)
                $dataHoje = date("Y-m-d H:i:s");
    
                $pedidoDetalhes->setDataPedido($dataHoje);

                $mail = new Email();
                
                $usuario = new Usuario(
                    NULL, $pedidoDetalhes->usuario->getNome(),
                    $pedidoDetalhes->usuario->getSobrenome(),
                    $pedidoDetalhes->usuario->getEmail(),
                    NULL, NULL, NULL, NULL
                );

                $mail->addAdress(
                    $usuario
                );
    
                $mail->EmailConfirmacaoPedido($pedidoDetalhes);
                $mail->enviarEmail();
			
                $sql = Mysql::conectar()->prepare('INSERT INTO `pedido_detalhes` (id, id_usuario, data_pedido, codigo_pedido) VALUES (null, ?, ?, ?) 
                ');

				$sql->execute(
                    array(
                        $pedidoDetalhes->getIdUsuario(), 
                        $pedidoDetalhes->getDataPedido(),
                        $pedidoDetalhes->getCodigoPedido()
                    )
                );
        
                // Obter o último ID inserido
                $ultimoIdInserido = Mysql::conectar()->lastInsertId();
                
                return $ultimoIdInserido;
			}

			catch(Exception $e) {
				Painel::alert("erro", "Erro ao conectar ao banco de dados");
			}
        }

        public static function retornaPedidosPendentes($comeco = null, $final = null) {
            try{
                if($comeco == null and $final == null) {
                    $sql = Mysql::conectar()->prepare('
                        SELECT
                        pedido_detalhes.codigo_pedido,
                        usuarios.nome,
                        usuarios.matricula,
                        usuarios.sobrenome,
                        pedido_detalhes.data_pedido,
                        pedido_detalhes.id
                        FROM
                            pedido_detalhes
                        JOIN
                            usuarios ON usuarios.id = pedido_detalhes.id_usuario
                        JOIN
                            pedidos ON pedidos.id_detalhes = pedido_detalhes.id
                        WHERE
                            pedido_detalhes.aprovado = 0 AND pedido_detalhes.finalizado = 0
                        GROUP BY
                            pedidos.id_detalhes
                        ORDER BY
                            pedido_detalhes.data_pedido DESC
                    ');
                }

                else {
                    $comeco = filter_var($comeco, FILTER_SANITIZE_NUMBER_INT);
					$final = filter_var($final, FILTER_SANITIZE_NUMBER_INT);
                    $sql = Mysql::conectar()->prepare("
                    SELECT
                    pedido_detalhes.codigo_pedido,
                    usuarios.nome,
                    usuarios.matricula,
                    usuarios.sobrenome,
                    pedido_detalhes.data_pedido,
                    pedido_detalhes.id
                    FROM
                        pedido_detalhes
                    JOIN
                        usuarios ON usuarios.id = pedido_detalhes.id_usuario
                    JOIN
                        pedidos ON pedidos.id_detalhes = pedido_detalhes.id
                    WHERE
                        pedido_detalhes.aprovado = 0 AND pedido_detalhes.finalizado = 0
                    GROUP BY
                        pedidos.id_detalhes
                    ORDER BY
                        pedido_detalhes.data_pedido DESC LIMIT $comeco, $final");
                }

                $sql->execute();
                
                $dados = $sql->fetchAll();

                if(empty($dados)) {
                    return false;
                }

                $resultados = array();

                foreach ($dados as $key => $value) {
                    $usuario = new Usuario(
                        NULL,
                        $value['nome'],
                        $value['sobrenome'],
                        NULL,
                        NULL,
                        NULL,
                        $value['matricula'],
                        NULL,
                        NULL,
                        NULL
                    );

                    $pedidoDetalhes = new PedidoDetalhes(
                        NULL,
                        $value['codigo_pedido'], 
                        $value['id'],
                        NULL,
                        NULL,
                        $value['data_pedido'],
                        NULL,
                        $usuario
                    );

                    $resultados[] = $pedidoDetalhes;
                }

                return $resultados;
            }

            catch(Exception $e) {
                Painel::alert("erro", "Erro ao se conectar ao banco de dados.");
            }
        }

        public static function retornaHistoricoPedidosPorIDUsuario($idUsuario, $comeco = null, $final = null) {
            try{
                if($comeco == null and $final == null) {
                    $sql = Mysql::conectar()->prepare("
                        SELECT
                        pedido_detalhes.codigo_pedido,
                        usuarios.nome,
                        usuarios.matricula,
                        usuarios.sobrenome,
                        pedido_detalhes.data_pedido,
                        pedido_detalhes.data_finalizado,
                        pedido_detalhes.aprovado,
                        pedido_detalhes.finalizado,
                        pedido_detalhes.id
                        FROM
                            pedido_detalhes
                        JOIN
                            usuarios ON usuarios.id = pedido_detalhes.id_usuario
                        JOIN
                            pedidos ON pedidos.id_detalhes = pedido_detalhes.id
                        WHERE
                            (pedido_detalhes.finalizado = 0 
                                OR 
                            pedido_detalhes.aprovado = 1 
                                AND 
                            pedido_detalhes.finalizado = 1
                            ) AND usuarios.id = $idUsuario
                        GROUP BY
                            pedidos.id_detalhes
                        ORDER BY
                            pedido_detalhes.data_pedido DESC
                    ");
                }

                else {
                    $comeco = filter_var($comeco, FILTER_SANITIZE_NUMBER_INT);
					$final = filter_var($final, FILTER_SANITIZE_NUMBER_INT);
                    $sql = Mysql::conectar()->prepare("
                    SELECT
                    pedido_detalhes.codigo_pedido,
                    usuarios.nome,
                    usuarios.matricula,
                    usuarios.sobrenome,
                    pedido_detalhes.data_pedido,
                    pedido_detalhes.data_finalizado,
                    pedido_detalhes.aprovado,
                    pedido_detalhes.finalizado,
                    pedido_detalhes.id
                    FROM
                        pedido_detalhes
                    JOIN
                        usuarios ON usuarios.id = pedido_detalhes.id_usuario
                    JOIN
                        pedidos ON pedidos.id_detalhes = pedido_detalhes.id
                    WHERE
                        (pedido_detalhes.finalizado = 0 
                            OR 
                        pedido_detalhes.aprovado = 1 
                            AND 
                        pedido_detalhes.finalizado = 1
                        ) AND usuarios.id = $idUsuario
                    GROUP BY
                        pedidos.id_detalhes
                    ORDER BY
                        pedido_detalhes.data_pedido DESC LIMIT $comeco, $final");
                }

                $sql->execute();
                
                $dados = $sql->fetchAll();

                if(empty($dados)) {
                    return false;
                }

                $resultados = array();

                foreach ($dados as $key => $value) {
                    $usuario = new Usuario(
                        NULL,
                        $value['nome'],
                        $value['sobrenome'],
                        NULL,
                        NULL,
                        NULL,
                        $value['matricula'],
                        NULL,
                        NULL,
                        NULL
                    );

                    $pedidoDetalhes = new PedidoDetalhes(
                        NULL,
                        $value['codigo_pedido'], 
                        $value['id'],
                        $value['aprovado'],
                        $value['finalizado'],
                        $value['data_pedido'],
                        $value['data_finalizado'],
                        $usuario
                    );

                    $resultados[] = $pedidoDetalhes;
                }

                return $resultados;
            }

            catch(Exception $e) {
                Painel::alert("erro", "Erro ao se conectar ao banco de dados.");
            }
        }

        public static function retornaHistoricoPedidosPorIDProduto($idEstoque, $comeco = null, $final = null) {
            try{
                if($comeco == null and $final == null) {
                    $sql = Mysql::conectar()->prepare("
                        SELECT
                        pedido_detalhes.codigo_pedido,
                        usuarios.nome,
                        usuarios.matricula,
                        usuarios.sobrenome,
                        pedido_detalhes.data_pedido,
                        pedido_detalhes.data_finalizado,
                        pedido_detalhes.aprovado,
                        pedido_detalhes.finalizado,
                        pedido_detalhes.id
                        FROM
                            pedido_detalhes
                        JOIN
                            usuarios ON usuarios.id = pedido_detalhes.id_usuario
                        JOIN
                            pedidos ON pedidos.id_detalhes = pedido_detalhes.id
                        WHERE
                            (pedido_detalhes.finalizado = 0 
                                OR 
                            pedido_detalhes.aprovado = 1 
                                AND 
                            pedido_detalhes.finalizado = 1
                            ) AND pedidos.id_estoque = $idEstoque
                        GROUP BY
                            pedidos.id_detalhes
                        ORDER BY
                            pedido_detalhes.data_pedido DESC
                    ");
                }

                else {
                    $comeco = filter_var($comeco, FILTER_SANITIZE_NUMBER_INT);
					$final = filter_var($final, FILTER_SANITIZE_NUMBER_INT);
                    $sql = Mysql::conectar()->prepare("
                    SELECT
                    pedido_detalhes.codigo_pedido,
                    usuarios.nome,
                    usuarios.matricula,
                    usuarios.sobrenome,
                    pedido_detalhes.data_pedido,
                    pedido_detalhes.data_finalizado,
                    pedido_detalhes.aprovado,
                    pedido_detalhes.finalizado,
                    pedido_detalhes.id
                    FROM
                        pedido_detalhes
                    JOIN
                        usuarios ON usuarios.id = pedido_detalhes.id_usuario
                    JOIN
                        pedidos ON pedidos.id_detalhes = pedido_detalhes.id
                    WHERE
                        (pedido_detalhes.finalizado = 0 
                            OR 
                        pedido_detalhes.aprovado = 1 
                            AND 
                        pedido_detalhes.finalizado = 1
                        ) AND pedidos.id_estoque = $idEstoque
                    GROUP BY
                        pedidos.id_detalhes
                    ORDER BY
                        pedido_detalhes.data_pedido DESC LIMIT $comeco, $final");
                }

                $sql->execute();
                
                $dados = $sql->fetchAll();

                if(empty($dados)) {
                    return false;
                }

                $resultados = array();

                foreach ($dados as $key => $value) {
                    $usuario = new Usuario(
                        NULL,
                        $value['nome'],
                        $value['sobrenome'],
                        NULL,
                        NULL,
                        NULL,
                        $value['matricula'],
                        NULL,
                        NULL,
                        NULL
                    );

                    $pedidoDetalhes = new PedidoDetalhes(
                        NULL,
                        $value['codigo_pedido'], 
                        $value['id'],
                        $value['aprovado'],
                        $value['finalizado'],
                        $value['data_pedido'],
                        $value['data_finalizado'],
                        $usuario
                    );

                    $resultados[] = $pedidoDetalhes;
                }

                return $resultados;
            }

            catch(Exception $e) {
                Painel::alert("erro", "Erro ao se conectar ao banco de dados.");
            }
        }

        public static function returnDataHistoricoPedidosPorIDUsuario($data, $filtro, $idUsuario) {
            try{
                $sql = Mysql::conectar()->prepare("
                    SELECT
                        pedido_detalhes.codigo_pedido,
                        usuarios.nome,
                        usuarios.matricula,
                        usuarios.sobrenome,
                        pedido_detalhes.data_pedido,
                        pedido_detalhes.data_finalizado,
                        pedido_detalhes.aprovado,
                        pedido_detalhes.finalizado,
                        pedido_detalhes.id
                        FROM
                            pedido_detalhes
                        JOIN
                            usuarios ON usuarios.id = pedido_detalhes.id_usuario
                        JOIN
                            pedidos ON pedidos.id_detalhes = pedido_detalhes.id
                        JOIN 
                            estoque ON estoque.id = pedidos.id_estoque
                        WHERE
                            $filtro LIKE '%$data%' AND
                            (pedido_detalhes.finalizado = 0 
                                OR 
                            pedido_detalhes.aprovado = 1 
                                AND 
                            pedido_detalhes.finalizado = 1
                            ) AND usuarios.id = $idUsuario
                        GROUP BY
                            pedidos.id_detalhes
                        ORDER BY
                            pedido_detalhes.data_pedido DESC
                ");

                $sql->execute();
                
                $dados = $sql->fetchAll();

                if(empty($dados)) {
                    return false;
                }

                $resultados = array();

                foreach ($dados as $key => $value) {
                    $usuario = new Usuario(
                        NULL,
                        $value['nome'],
                        $value['sobrenome'],
                        NULL,
                        NULL,
                        NULL,
                        $value['matricula'],
                        NULL,
                        NULL,
                        NULL
                    );

                    $pedidoDetalhes = new PedidoDetalhes(
                        NULL,
                        $value['codigo_pedido'], 
                        $value['id'],
                        $value['aprovado'],
                        $value['finalizado'],
                        $value['data_pedido'],
                        $value['data_finalizado'],
                        $usuario
                    );

                    $resultados[] = $pedidoDetalhes;
                }

                return $resultados;
            }

            catch(Exception $e) {
                Painel::alert("erro", "Erro ao se conectar ao banco de dados.");
            }
        }

        public static function returnDataHistoricoPedidosPorIDProduto($data, $filtro, $idEstoque) {
            try{
                $sql = Mysql::conectar()->prepare("
                    SELECT
                        pedido_detalhes.codigo_pedido,
                        usuarios.nome,
                        usuarios.matricula,
                        usuarios.sobrenome,
                        pedido_detalhes.data_pedido,
                        pedido_detalhes.data_finalizado,
                        pedido_detalhes.aprovado,
                        pedido_detalhes.finalizado,
                        pedido_detalhes.id
                        FROM
                            pedido_detalhes
                        JOIN
                            usuarios ON usuarios.id = pedido_detalhes.id_usuario
                        JOIN
                            pedidos ON pedidos.id_detalhes = pedido_detalhes.id
                        WHERE
                            $filtro LIKE '%$data%' AND
                            (pedido_detalhes.finalizado = 0 
                                OR 
                            pedido_detalhes.aprovado = 1 
                                AND 
                            pedido_detalhes.finalizado = 1
                            ) AND pedidos.id_estoque = $idEstoque
                        GROUP BY
                            pedidos.id_detalhes
                        ORDER BY
                            pedido_detalhes.data_pedido DESC
                ");

                $sql->execute();
                
                $dados = $sql->fetchAll();

                if(empty($dados)) {
                    return false;
                }

                $resultados = array();

                foreach ($dados as $key => $value) {
                    $usuario = new Usuario(
                        NULL,
                        $value['nome'],
                        $value['sobrenome'],
                        NULL,
                        NULL,
                        NULL,
                        $value['matricula'],
                        NULL,
                        NULL,
                        NULL
                    );

                    $pedidoDetalhes = new PedidoDetalhes(
                        NULL,
                        $value['codigo_pedido'], 
                        $value['id'],
                        $value['aprovado'],
                        $value['finalizado'],
                        $value['data_pedido'],
                        $value['data_finalizado'],
                        $usuario
                    );

                    $resultados[] = $pedidoDetalhes;
                }

                return $resultados;
            }

            catch(Exception $e) {
                Painel::alert("erro", "Erro ao se conectar ao banco de dados.");
            }
        }

        public static function returnDataPedidosPendentes($data, $filtro) {
            try{
                $sql = Mysql::conectar()->prepare("
                    SELECT
                    pedido_detalhes.codigo_pedido,
                    usuarios.nome,
                    usuarios.matricula,
                    usuarios.sobrenome,
                    pedido_detalhes.data_pedido,
                    pedido_detalhes.id
                    FROM
                        pedido_detalhes
                    JOIN
                        usuarios ON usuarios.id = pedido_detalhes.id_usuario
                    JOIN
                        pedidos ON pedidos.id_detalhes = pedido_detalhes.id
                    JOIN 
                        estoque ON estoque.id = pedidos.id_estoque
                    WHERE
                        $filtro LIKE '%$data%' AND
                        pedido_detalhes.aprovado = 0 AND pedido_detalhes.finalizado = 0
                    GROUP BY
                        pedidos.id_detalhes
                    ORDER BY
                        pedido_detalhes.data_pedido DESC
                ");

                $sql->execute();
                
                $dados = $sql->fetchAll();

                if(empty($dados)) {
                    return false;
                }

                $resultados = array();

                foreach ($dados as $key => $value) {
                    $usuario = new Usuario(
                        NULL,
                        $value['nome'],
                        $value['sobrenome'],
                        NULL,
                        NULL,
                        NULL,
                        $value['matricula'],
                        NULL,
                        NULL,
                        NULL
                    );

                    $pedidoDetalhes = new PedidoDetalhes(
                        NULL,
                        $value['codigo_pedido'], 
                        $value['id'],
                        NULL,
                        NULL,
                        $value['data_pedido'],
                        NULL,
                        $usuario
                    );

                    $resultados[] = $pedidoDetalhes;
                }

                return $resultados;
            }

            catch(Exception $e) {
                Painel::alert("erro", "Erro ao se conectar ao banco de dados.");
            }
        }

        public static function retornaPedidosNaoFinalizados($comeco = null, $final = null) {
            try{
                if($comeco == null and $final == null) {
                    $sql = Mysql::conectar()->prepare('
                    SELECT
                        pedido_detalhes.codigo_pedido,
                        usuarios.nome,
                        usuarios.matricula,
                        usuarios.sobrenome,
                        pedido_detalhes.data_pedido,
                        pedido_detalhes.id
                    FROM
                        pedido_detalhes
                    JOIN
                        usuarios ON usuarios.id = pedido_detalhes.id_usuario
                    JOIN
                        pedidos ON pedidos.id_detalhes = pedido_detalhes.id
                    WHERE
                        pedido_detalhes.aprovado = 1 AND pedido_detalhes.finalizado = 0
                    GROUP BY
                        pedidos.id_detalhes
                    ORDER BY
                        pedido_detalhes.data_pedido DESC
                    ');
                }

                else {
                    $comeco = filter_var($comeco, FILTER_SANITIZE_NUMBER_INT);
					$final = filter_var($final, FILTER_SANITIZE_NUMBER_INT);
                    $sql = Mysql::conectar()->prepare("
                        SELECT
                        pedido_detalhes.codigo_pedido,
                        usuarios.nome,
                        usuarios.matricula,
                        usuarios.sobrenome,
                        pedido_detalhes.data_pedido,
                        pedido_detalhes.id
                        FROM
                            pedido_detalhes
                        JOIN
                            usuarios ON usuarios.id = pedido_detalhes.id_usuario
                        JOIN
                            pedidos ON pedidos.id_detalhes = pedido_detalhes.id
                        WHERE
                            pedido_detalhes.aprovado = 1 AND pedido_detalhes.finalizado = 0
                        GROUP BY
                            pedidos.id_detalhes
                        ORDER BY
                            pedido_detalhes.data_pedido DESC LIMIT $comeco, $final
                    ");
                }

                $sql->execute();
                
                $dados = $sql->fetchAll();

                if(empty($dados)) {
                    return false;
                }

                $resultados = array();

                foreach ($dados as $key => $value) {
                    $usuario = new Usuario(
                        NULL,
                        $value['nome'],
                        $value['sobrenome'],
                        NULL,
                        NULL,
                        NULL,
                        $value['matricula'],
                        NULL,
                        NULL,
                        NULL
                    );

                    $pedidoDetalhes = new PedidoDetalhes(
                        NULL,
                        $value['codigo_pedido'], 
                        $value['id'],
                        NULL,
                        NULL,
                        $value['data_pedido'],
                        NULL,
                        $usuario
                    );

                    $resultados[] = $pedidoDetalhes;
                }

                return $resultados;
            }

            catch(Exception $e) {
                Painel::alert("erro", "Erro ao se conectar ao banco de dados.");
            }
        }

        public static function retornaPedidosFinalizadosHoje($comeco = null, $final = null) {
            try{
                if($comeco == null and $final == null) {
                    $sql = Mysql::conectar()->prepare('
                    SELECT
                        pedido_detalhes.codigo_pedido,
                        usuarios.nome,
                        usuarios.matricula,
                        usuarios.sobrenome,
                        pedido_detalhes.data_pedido,
                        pedido_detalhes.id
                    FROM
                        pedido_detalhes
                    JOIN
                        usuarios ON usuarios.id = pedido_detalhes.id_usuario
                    JOIN
                        pedidos ON pedidos.id_detalhes = pedido_detalhes.id
                    WHERE
                        pedido_detalhes.aprovado = 1 AND pedido_detalhes.finalizado = 1
                        AND DATE(pedido_detalhes.data_finalizado) = CURDATE()
                    GROUP BY
                        pedidos.id_detalhes
                    ORDER BY
                        pedido_detalhes.data_pedido DESC
                    ');
                }

                else {
                    $comeco = filter_var($comeco, FILTER_SANITIZE_NUMBER_INT);
					$final = filter_var($final, FILTER_SANITIZE_NUMBER_INT);
                    $sql = Mysql::conectar()->prepare("
                        SELECT
                            pedido_detalhes.codigo_pedido,
                            usuarios.nome,
                            usuarios.matricula,
                            usuarios.sobrenome,
                            pedido_detalhes.data_pedido,
                            pedido_detalhes.id
                        FROM
                            pedido_detalhes
                        JOIN
                            usuarios ON usuarios.id = pedido_detalhes.id_usuario
                        JOIN
                            pedidos ON pedidos.id_detalhes = pedido_detalhes.id
                        WHERE
                            pedido_detalhes.aprovado = 1 AND pedido_detalhes.finalizado = 1
                            AND DATE(pedido_detalhes.data_finalizado) = CURDATE()
                        GROUP BY
                            pedidos.id_detalhes
                        ORDER BY
                            pedido_detalhes.data_pedido DESC LIMIT $comeco, $final
                    ");
                }

                $sql->execute();
                
                $dados = $sql->fetchAll();

                if(empty($dados)) {
                    return false;
                }

                $resultados = array();

                foreach ($dados as $key => $value) {
                    $usuario = new Usuario(
                        NULL,
                        $value['nome'],
                        $value['sobrenome'],
                        NULL,
                        NULL,
                        NULL,
                        $value['matricula'],
                        NULL,
                        NULL,
                        NULL
                    );

                    $pedidoDetalhes = new PedidoDetalhes(
                        NULL,
                        $value['codigo_pedido'], 
                        $value['id'],
                        NULL,
                        NULL,
                        $value['data_pedido'],
                        NULL,
                        $usuario
                    );

                    $resultados[] = $pedidoDetalhes;
                }

                return $resultados;
            }

            catch(Exception $e) {
                Painel::alert("erro", "Erro ao se conectar ao banco de dados.");
            }
        }

        public static function returnDataPedidosFinalizadosHoje($data, $filtro) {
            try{
                $sql = Mysql::conectar()->prepare("
                    SELECT
                    pedido_detalhes.codigo_pedido,
                    usuarios.nome,
                    usuarios.matricula,
                    usuarios.sobrenome,
                    pedido_detalhes.data_pedido,
                    pedido_detalhes.id
                    FROM
                        pedido_detalhes
                    JOIN
                        usuarios ON usuarios.id = pedido_detalhes.id_usuario
                    JOIN
                        pedidos ON pedidos.id_detalhes = pedido_detalhes.id
					JOIN 
                        estoque ON estoque.id = pedidos.id_estoque                
                    WHERE
                        $filtro LIKE '%$data%' AND
                        pedido_detalhes.aprovado = 1 AND pedido_detalhes.finalizado = 1
                        AND DATE(pedido_detalhes.data_finalizado) = CURDATE()
                    GROUP BY
                        pedidos.id_detalhes
                    ORDER BY
                        pedido_detalhes.data_pedido DESC
                ");

                $sql->execute();
                
                $dados = $sql->fetchAll();

                if(empty($dados)) {
                    return false;
                }

                $resultados = array();

                foreach ($dados as $key => $value) {
                    $usuario = new Usuario(
                        NULL,
                        $value['nome'],
                        $value['sobrenome'],
                        NULL,
                        NULL,
                        NULL,
                        $value['matricula'],
                        NULL,
                        NULL,
                        NULL
                    );

                    $pedidoDetalhes = new PedidoDetalhes(
                        NULL,
                        $value['codigo_pedido'], 
                        $value['id'],
                        NULL,
                        NULL,
                        $value['data_pedido'],
                        NULL,
                        $usuario
                    );

                    $resultados[] = $pedidoDetalhes;
                }

                return $resultados;
            }

            catch(Exception $e) {
                Painel::alert("erro", "Erro ao se conectar ao banco de dados.");
            }
        }

        public static function returnDataPedidosNaoFinalizados($data, $filtro) {
            try{
                $sql = Mysql::conectar()->prepare("
                    SELECT
                    pedido_detalhes.codigo_pedido,
                    usuarios.nome,
                    usuarios.matricula,
                    usuarios.sobrenome,
                    pedido_detalhes.data_pedido,
                    pedido_detalhes.id
                    FROM
                        pedido_detalhes
                    JOIN
                        usuarios ON usuarios.id = pedido_detalhes.id_usuario
                    JOIN
                        pedidos ON pedidos.id_detalhes = pedido_detalhes.id
                    JOIN 
                        estoque ON estoque.id = pedidos.id_estoque
                    WHERE
                        $filtro LIKE '%$data%' AND
                        pedido_detalhes.aprovado = 1 AND pedido_detalhes.finalizado = 0
                    GROUP BY
                        pedidos.id_detalhes
                    ORDER BY
                        pedido_detalhes.data_pedido DESC
                ");

                $sql->execute();
                
                $dados = $sql->fetchAll();

                if(empty($dados)) {
                    return false;
                }

                $resultados = array();

                foreach ($dados as $key => $value) {
                    $usuario = new Usuario(
                        NULL,
                        $value['nome'],
                        $value['sobrenome'],
                        NULL,
                        NULL,
                        NULL,
                        $value['matricula'],
                        NULL,
                        NULL,
                        NULL
                    );

                    $pedidoDetalhes = new PedidoDetalhes(
                        NULL,
                        $value['codigo_pedido'], 
                        $value['id'],
                        NULL,
                        NULL,
                        $value['data_pedido'],
                        NULL,
                        $usuario
                    );

                    $resultados[] = $pedidoDetalhes;
                }

                return $resultados;
            }

            catch(Exception $e) {
                Painel::alert("erro", "Erro ao se conectar ao banco de dados.");
            }
        }

        public static function retornaUltimosCincoPedidos() {
            try{
                $sql = Mysql::conectar()->prepare('
                    SELECT
                    DISTINCT pedido_detalhes.codigo_pedido,
                    usuarios.nome,
                    usuarios.matricula,
                    usuarios.sobrenome,
                    pedido_detalhes.data_pedido,
                    pedido_detalhes.id
                    FROM
                        pedido_detalhes
                    JOIN
                        usuarios ON usuarios.id = pedido_detalhes.id_usuario
                    JOIN
                        pedidos ON pedidos.id_detalhes = pedido_detalhes.id
                    WHERE
                        pedido_detalhes.aprovado = 1 AND pedido_detalhes.finalizado = 0
                    ORDER BY pedido_detalhes.data_pedido DESC, pedido_detalhes.id DESC LIMIT 5
                ');

                $sql->execute();
                
                $dados = $sql->fetchAll();

                if(empty($dados)) {
                    return false;
                }

                $resultados = array();

                foreach ($dados as $key => $value) {
                    $usuario = new Usuario(
                        NULL,
                        $value['nome'],
                        $value['sobrenome'],
                        NULL,
                        NULL,
                        NULL,
                        $value['matricula'],
                        NULL,
                        NULL,
                        NULL
                    );

                    $pedidoDetalhes = new PedidoDetalhes(
                        NULL,
                        $value['codigo_pedido'], 
                        $value['id'],
                        NULL,
                        NULL,
                        $value['data_pedido'],
                        NULL,
                        $usuario
                    );

                    $resultados[] = $pedidoDetalhes;
                }

                return $resultados;
            }

            catch(Exception $e) {
                Painel::alert("erro", "Erro ao se conectar ao banco de dados.");
            }
        }

        public static function retornaQuantidadePedidosPorMesAnoAtual() {
            try {
                $sql = Mysql::conectar()->prepare('
                    SELECT 
                        MONTH(data_pedido) AS mes, 
                        COUNT(*) AS total_resultados 
                    FROM 
                        pedido_detalhes 
                    WHERE 
                        aprovado = 1 AND 
                        finalizado = 1 AND 
                        YEAR(data_pedido) = YEAR(CURRENT_DATE())
                    GROUP BY 
                        MONTH(data_pedido) 
                    ORDER BY 
                        mes ASC;
                ');

                $sql->execute();

                $dados = $sql->fetchAll();

                return json_encode($dados);
            } 
            
            catch(Exception $e) {
                Painel::alert("erro", "Erro ao se conectar ao banco de dados.");
            }
        }

        public static function retornaQuantidadePedidosAnos() {
            try {
                $sql = Mysql::conectar()->prepare('
                    SELECT 
                        YEAR(pedido_detalhes.data_pedido) AS ano, 
                            COUNT(*) AS total_resultados 
                    FROM 
                        pedido_detalhes 
                    WHERE 
                        pedido_detalhes.aprovado = 1 AND 
                        pedido_detalhes.finalizado = 1
                    GROUP BY 
                        YEAR(pedido_detalhes.data_pedido)
                    ORDER 
                        BY ano ASC
                ');

                $sql->execute();

                $dados = $sql->fetchAll();

                return json_encode($dados);
            } 
            
            catch(Exception $e) {
                Painel::alert("erro", "Erro ao se conectar ao banco de dados.");
            }
        }

        public static function retornaQuantidadeMaisPedidoPorMesAnoAtual() {
            try {
                $sql = Mysql::conectar()->prepare('
                    SELECT 
                        nome AS item_mais_pedido,
                        MONTH(pd.data_pedido) as mes,
                        SUM(quantidade_item) AS quantidade_total
                    FROM 
                        pedidos p
                    JOIN 
                        pedido_detalhes pd ON pd.id = p.id_detalhes
                    JOIN 
                        estoque ON estoque.id = p.id_estoque
                    WHERE 
                        pd.aprovado = 1 
                        AND pd.finalizado = 1
                        AND YEAR(pd.data_pedido) = YEAR(CURRENT_DATE())
                    GROUP BY 
                        mes, estoque.nome
                    HAVING 
                        SUM(p.quantidade_item) = (
                            SELECT 
                                SUM(p2.quantidade_item) AS total_pedidos
                            FROM 
                                pedidos p2
                            JOIN 
                                pedido_detalhes pd2 ON pd2.id = p2.id_detalhes
                            WHERE 
                                MONTH(pd2.data_pedido) = mes
                            GROUP BY 
                                p2.id_estoque
                            ORDER BY 
                                total_pedidos DESC
                            LIMIT 1
                        )
                    ORDER BY mes ASC
                ');

                $sql->execute();

                $dados = $sql->fetchAll();

                return json_encode($dados);
            } 
            
            catch(Exception $e) {
                Painel::alert("erro", "Erro ao se conectar ao banco de dados.");
            }
        }

        public static function retornaMaisPedidoPorAno() {
            try {
                $sql = Mysql::conectar()->prepare('
                    SELECT 
                        nome AS item_mais_pedido,
                        YEAR(pd.data_pedido) as ano,
                        SUM(quantidade_item) AS quantidade_total
                    FROM 
                        pedidos p
                    JOIN 
                        pedido_detalhes pd ON pd.id = p.id_detalhes
                    JOIN 
                        estoque ON estoque.id = p.id_estoque
                    WHERE 
                        pd.aprovado = 1 
                        AND pd.finalizado = 1
                    GROUP BY 
                        ano, estoque.nome
                    HAVING 
                        SUM(p.quantidade_item) = (
                            SELECT 
                                SUM(p2.quantidade_item) AS total_pedidos
                            FROM 
                                pedidos p2
                            JOIN 
                                pedido_detalhes pd2 ON pd2.id = p2.id_detalhes
                            WHERE 
                                YEAR(pd2.data_pedido) = ano
                            GROUP BY 
                                p2.id_estoque
                            ORDER BY 
                                total_pedidos DESC
                            LIMIT 1
                        )
                    ORDER BY ano ASC
                ');

                $sql->execute();

                $dados = $sql->fetchAll();

                return json_encode($dados);
            } 
            
            catch(Exception $e) {
                Painel::alert("erro", "Erro ao se conectar ao banco de dados.");
            }
        }

        public static function solicitadoPedido($idEstoque) {
            try{
                $sql = Mysql::conectar()->prepare('
                    SELECT 
                        pedidos.id_estoque
                    FROM 
                        pedidos
                    JOIN 
                        pedido_detalhes ON pedido_detalhes.id = pedidos.id_detalhes
                    WHERE
                        ((pedido_detalhes.aprovado = 1 && 
                        pedido_detalhes.finalizado = 0) OR
                        (pedido_detalhes.aprovado = 0 && 
                        pedido_detalhes.finalizado = 0)) 
                        AND 
                        pedidos.id_estoque = ?
                ');

                $sql->execute(array($idEstoque));
            
                return $sql->rowCount();
            }

            catch(Exception $e) {
                Painel::alert("erro", "Erro ao se conectar ao banco de dados.");
            }
        }
        
        public static function maisde3PedidosPendentes($idUsuario) {
            try{
                $sql = Mysql::conectar()->prepare('
                    SELECT 
                        count(*) as total_pedidos
                    from pedido_detalhes 
                    WHERE 
                        aprovado = 0 
                        AND 
                        finalizado = 0
                        and id_usuario = ?

                ');

                $sql->execute(array($idUsuario));
                
                $dados = $sql->fetch();
                
                return $dados['total_pedidos'] >= 3;
            }

            catch(Exception $e) {
                Painel::alert("erro", "Erro ao se conectar ao banco de dados.");
            }
        }

        public static function retornaDadosPedidoViaCodigo($codigo_pedido) {
            try{
                $sql = Mysql::conectar()->prepare('
                    SELECT
                    pedido_detalhes.codigo_pedido,
                    usuarios.nome,
                    usuarios.email,
                    usuarios.matricula,
                    usuarios.sobrenome,
                    usuarios.id as idUsuario,
                    pedido_detalhes.data_pedido,
                    pedido_detalhes.id,
                    pedido_detalhes.aprovado,
                    pedido_detalhes.finalizado,
                    pedido_detalhes.id_usuario_aprovou
                    FROM
                        pedido_detalhes
                    JOIN
                        usuarios ON usuarios.id = pedido_detalhes.id_usuario
                    JOIN
                        pedidos ON pedidos.id_detalhes = pedido_detalhes.id
                    WHERE
                        pedido_detalhes.codigo_pedido = ?
                    GROUP BY
                        pedidos.id_detalhes
                    ORDER BY
                        pedido_detalhes.data_pedido DESC
                ');

                $sql->execute(array($codigo_pedido));
                
                $dados = $sql->fetchAll();

                if(empty($dados)) {
                    return false;
                }

                $resultados = array();

                foreach ($dados as $key => $value) {
                    $usuario = new Usuario(
                        NULL,
                        $value['nome'],
                        $value['sobrenome'],
                        $value['email'],
                        NULL,
                        NULL,
                        $value['matricula'],
                        NULL,
                        NULL,
                        (int) $value['idUsuario']
                    );

                    $pedidoDetalhes = new PedidoDetalhes(
                        NULL,
                        $value['codigo_pedido'], 
                        $value['id'],
                        (int) $value['aprovado'],
                        (int) $value['finalizado'],
                        $value['data_pedido'],
                        NULL,
                        $usuario
                    );

                    $pedidoDetalhes->setIdUsuarioAprovou($value['id_usuario_aprovou']);

                    $resultados = $pedidoDetalhes;
                }

                return $resultados;
            }

            catch(Exception $e) {
                Painel::alert("erro", "Erro ao se conectar ao banco de dados.");
            }
        }

        public static function retornaDadosPedidoViaCodigoNovoEmprestimo($codigo_pedido) {
            try{
                $sql = Mysql::conectar()->prepare('
                    SELECT
                    pedido_detalhes.codigo_pedido,
                    usuarios.nome,
                    usuarios.email,
                    usuarios.matricula,
                    usuarios.sobrenome,
                    pedido_detalhes.aprovado,
                    pedido_detalhes.finalizado,
                    usuarios.id as idUsuario,
                    pedido_detalhes.data_pedido,
                    pedido_detalhes.data_finalizado,
                    pedido_detalhes.id,
                    pedido_detalhes.id_usuario_aprovou,
                    pedido_detalhes.id_usuario_finalizou
                    FROM
                        pedido_detalhes
                    JOIN
                        usuarios ON usuarios.id = pedido_detalhes.id_usuario
                    JOIN
                        pedidos ON pedidos.id_detalhes = pedido_detalhes.id
                    WHERE
                        pedido_detalhes.codigo_pedido = ?
                    GROUP BY
                        pedidos.id_detalhes
                    ORDER BY
                        pedido_detalhes.data_pedido DESC
                ');

                $sql->execute(array($codigo_pedido));
                
                $dados = $sql->fetchAll();

                if(empty($dados)) {
                    return false;
                }

                $resultados = array();

                foreach ($dados as $key => $value) {
                    $usuario = new Usuario(
                        NULL,
                        $value['nome'],
                        $value['sobrenome'],
                        $value['email'],
                        NULL,
                        NULL,
                        $value['matricula'],
                        NULL,
                        NULL,
                        (int) $value['idUsuario']
                    );

                    $pedidoDetalhes = new PedidoDetalhes(
                        NULL,
                        $value['codigo_pedido'], 
                        $value['id'],
                        $value['aprovado'],
                        $value['finalizado'],
                        $value['data_pedido'],
                        $value['data_finalizado'],
                        $usuario
                    );

                    $pedidoDetalhes->setIdUsuarioAprovou($value['id_usuario_aprovou']);
                    $pedidoDetalhes->setIdUsuarioFinalizou($value['id_usuario_finalizou']);

                    $resultados = $pedidoDetalhes;
                }

                return $resultados;
            }

            catch(Exception $e) {
                Painel::alert("erro", "Erro ao se conectar ao banco de dados.");
            }
        }

        public static function retornaDadosPedidosAtivoUsuario($codigo_pedido) {
            try{
                $sql = Mysql::conectar()->prepare('
                    SELECT
                    pedido_detalhes.codigo_pedido,
                    usuarios.nome,
                    usuarios.email,
                    usuarios.matricula,
                    usuarios.sobrenome,
                    usuarios.id as idUsuario,
                    pedido_detalhes.data_pedido,
                    pedido_detalhes.id
                    FROM
                        pedido_detalhes
                    JOIN
                        usuarios ON usuarios.id = pedido_detalhes.id_usuario
                    JOIN
                        pedidos ON pedidos.id_detalhes = pedido_detalhes.id
                    WHERE
                        pedido_detalhes.codigo_pedido = ?
                        AND pedido_detalhes.id_usuario = ?
                        AND pedido_detalhes.finalizado = 0
                    GROUP BY
                        pedidos.id_detalhes
                    ORDER BY
                        pedido_detalhes.data_pedido DESC
                ');

                $sql->execute(array($codigo_pedido, $_SESSION['id']));
                
                $dados = $sql->fetchAll();

                if(empty($dados)) {
                    return false;
                }

                $resultados = array();

                foreach ($dados as $key => $value) {
                    $usuario = new Usuario(
                        NULL,
                        $value['nome'],
                        $value['sobrenome'],
                        $value['email'],
                        NULL,
                        NULL,
                        $value['matricula'],
                        NULL,
                        NULL,
                        (int) $value['idUsuario']
                    );

                    $pedidoDetalhes = new PedidoDetalhes(
                        NULL,
                        $value['codigo_pedido'], 
                        $value['id'],
                        NULL,
                        NULL,
                        $value['data_pedido'],
                        NULL,
                        $usuario
                    );

                    $resultados = $pedidoDetalhes;
                }

                return $resultados;
            }

            catch(Exception $e) {
                Painel::alert("erro", "Erro ao se conectar ao banco de dados.");
            }
        }

        public static function retornaDadosFinalizadoHoje($codigo_pedido) {
            try {
                $sql = Mysql::conectar()->prepare('
            SELECT
                pedido_detalhes.codigo_pedido,
                usuarios.nome,
                usuarios.email,
                usuarios.matricula,
                usuarios.sobrenome,
                usuarios.id as idUsuario,
                pedido_detalhes.data_pedido,
                pedido_detalhes.id,
                pedido_detalhes.id_usuario_aprovou,
                pedido_detalhes.id_usuario_finalizou,
                pedido_detalhes.data_finalizado
            FROM
                pedido_detalhes
            JOIN
                usuarios ON usuarios.id = pedido_detalhes.id_usuario
            JOIN
                pedidos ON pedidos.id_detalhes = pedido_detalhes.id
            WHERE
                pedido_detalhes.codigo_pedido = ?
                AND pedido_detalhes.finalizado = 1
                AND pedido_detalhes.aprovado = 1
                AND DATE(pedido_detalhes.data_finalizado) = CURDATE()
            GROUP BY
                pedidos.id_detalhes
            ORDER BY
                pedido_detalhes.data_pedido DESC
        ');

                $sql->execute(array($codigo_pedido));

                $dados = $sql->fetch();

                if (!$dados) {
                    return false;
                }

                $usuario = new Usuario(
                    NULL,
                    $dados['nome'],
                    $dados['sobrenome'],
                    $dados['email'],
                    NULL,
                    NULL,
                    $dados['matricula'],
                    NULL,
                    NULL,
                    (int) $dados['idUsuario']
                );

                $pedidoDetalhes = new PedidoDetalhes(
                    NULL,
                    $dados['codigo_pedido'],
                    $dados['id'],
                    NULL,
                    NULL,
                    $dados['data_pedido'],
                    $dados['data_finalizado'],
                    $usuario
                );

                // Adicionar os IDs do usuário que aprovou e finalizou o pedido
                $pedidoDetalhes->setIdUsuarioAprovou($dados['id_usuario_aprovou']);
                $pedidoDetalhes->setIdUsuarioFinalizou($dados['id_usuario_finalizou']);

                return $pedidoDetalhes;
            } catch (Exception $e) {
                Painel::alert("erro", "Erro ao se conectar ao banco de dados.");
            }
        }

        public static function retornaDadosPedidosPendenteUsuario($codigo_pedido) {
            try{
                $sql = Mysql::conectar()->prepare('
                    SELECT
                    pedido_detalhes.codigo_pedido,
                    usuarios.nome,
                    usuarios.email,
                    usuarios.matricula,
                    usuarios.sobrenome,
                    usuarios.id as idUsuario,
                    pedido_detalhes.data_pedido,
                    pedido_detalhes.id
                    FROM
                        pedido_detalhes
                    JOIN
                        usuarios ON usuarios.id = pedido_detalhes.id_usuario
                    JOIN
                        pedidos ON pedidos.id_detalhes = pedido_detalhes.id
                    WHERE
                        pedido_detalhes.codigo_pedido = ?
                        AND pedido_detalhes.id_usuario = ?
                        AND pedido_detalhes.finalizado = 0
                        AND pedido_detalhes.aprovado = 0
                    GROUP BY
                        pedidos.id_detalhes
                    ORDER BY
                        pedido_detalhes.data_pedido DESC
                ');

                $sql->execute(array($codigo_pedido, $_SESSION['id']));
                
                $dados = $sql->fetchAll();

                if(empty($dados)) {
                    return false;
                }

                $resultados = array();

                foreach ($dados as $key => $value) {
                    $usuario = new Usuario(
                        NULL,
                        $value['nome'],
                        $value['sobrenome'],
                        $value['email'],
                        NULL,
                        NULL,
                        $value['matricula'],
                        NULL,
                        NULL,
                        (int) $value['idUsuario']
                    );

                    $pedidoDetalhes = new PedidoDetalhes(
                        NULL,
                        $value['codigo_pedido'], 
                        $value['id'],
                        NULL,
                        NULL,
                        $value['data_pedido'],
                        NULL,
                        $usuario
                    );

                    $resultados = $pedidoDetalhes;
                }

                return $resultados;
            }

            catch(Exception $e) {
                Painel::alert("erro", "Erro ao se conectar ao banco de dados.");
            }
        }

        public static function retornaDadosPedidosAnterioresUsuario($codigo_pedido) {
            try{
                $sql = Mysql::conectar()->prepare('
                    SELECT
                    pedido_detalhes.codigo_pedido,
                    usuarios.nome,
                    usuarios.email,
                    usuarios.matricula,
                    usuarios.sobrenome,
                    usuarios.id as idUsuario,
                    pedido_detalhes.data_pedido,
                    pedido_detalhes.data_finalizado,
                    pedido_detalhes.id
                    FROM
                        pedido_detalhes
                    JOIN
                        usuarios ON usuarios.id = pedido_detalhes.id_usuario
                    JOIN
                        pedidos ON pedidos.id_detalhes = pedido_detalhes.id
                    WHERE
                        pedido_detalhes.codigo_pedido = ?
                        AND pedido_detalhes.id_usuario = ?
                        AND pedido_detalhes.finalizado = 1
                        AND pedido_detalhes.aprovado = 1
                    GROUP BY
                        pedidos.id_detalhes
                    ORDER BY
                        pedido_detalhes.data_pedido DESC
                ');

                $sql->execute(array($codigo_pedido, $_SESSION['id']));
                
                $dados = $sql->fetchAll();

                if(empty($dados)) {
                    return false;
                }

                $resultados = array();

                foreach ($dados as $key => $value) {
                    $usuario = new Usuario(
                        NULL,
                        $value['nome'],
                        $value['sobrenome'],
                        $value['email'],
                        NULL,
                        NULL,
                        $value['matricula'],
                        NULL,
                        NULL,
                        (int) $value['idUsuario']
                    );

                    $pedidoDetalhes = new PedidoDetalhes(
                        NULL,
                        $value['codigo_pedido'], 
                        $value['id'],
                        NULL,
                        NULL,
                        $value['data_pedido'],
                        $value['data_finalizado'],
                        $usuario
                    );

                    $resultados = $pedidoDetalhes;
                }

                return $resultados;
            }

            catch(Exception $e) {
                Painel::alert("erro", "Erro ao se conectar ao banco de dados.");
            }
        }
        // Verifica existência de pedidos pendentes via ID do usuário.
        public static function verificaPedidosPendentesID(Usuario $usuario) {
            try{
                $sql = Mysql::conectar()->prepare('
                    SELECT
                    pedido_detalhes.codigo_pedido,
                    usuarios.nome,
                    usuarios.email,
                    usuarios.matricula,
                    usuarios.sobrenome,
                    usuarios.id as idUsuario,
                    pedido_detalhes.data_pedido,
                    pedido_detalhes.id
                    FROM
                        pedido_detalhes
                    JOIN
                        usuarios ON usuarios.id = pedido_detalhes.id_usuario
                    JOIN
                        pedidos ON pedidos.id_detalhes = pedido_detalhes.id
                    WHERE
                        pedido_detalhes.id_usuario = ?
                        AND pedido_detalhes.finalizado = 0
                        AND pedido_detalhes.aprovado = 0
                    GROUP BY
                        pedidos.id_detalhes
                    ORDER BY
                        pedido_detalhes.data_pedido DESC
                ');

                $sql->execute(array($usuario->getId()));
                
                $dados = $sql->fetchAll();

                if(empty($dados)) {
                    return false;
                }

                return true;
            }

            catch(Exception $e) {
                Painel::alert("erro", "Erro ao se conectar ao banco de dados.");
            }
        }

        // Verifica pedidos ativos via ID do usuário.
        public static function verificaPedidosAtivoID(Usuario $usuario) {
            try{
                $sql = Mysql::conectar()->prepare('
                    SELECT
                    pedido_detalhes.codigo_pedido,
                    usuarios.nome,
                    usuarios.email,
                    usuarios.matricula,
                    usuarios.sobrenome,
                    usuarios.id as idUsuario,
                    pedido_detalhes.data_pedido,
                    pedido_detalhes.id
                    FROM
                        pedido_detalhes
                    JOIN
                        usuarios ON usuarios.id = pedido_detalhes.id_usuario
                    JOIN
                        pedidos ON pedidos.id_detalhes = pedido_detalhes.id
                    WHERE
                        pedido_detalhes.id_usuario = ?
                        AND pedido_detalhes.finalizado = 0
                    GROUP BY
                        pedidos.id_detalhes
                    ORDER BY
                        pedido_detalhes.data_pedido DESC
                ');

                $sql->execute(array($usuario->getId()));
                
                $dados = $sql->fetchAll();

                if(empty($dados)) {
                    return false;
                }

                return true;
            }

            catch(Exception $e) {
                Painel::alert("erro", "Erro ao se conectar ao banco de dados.");
            }
        }

        public static function retornaEmprestimosMaisSeteDias() {
            try{
                $sql = Mysql::conectar()->prepare('
                    SELECT
                        pedido_detalhes.codigo_pedido,
                        usuarios.nome,
                        usuarios.matricula,
                        usuarios.sobrenome,
                        usuarios.email,
                        pedido_detalhes.data_pedido,
                        pedido_detalhes.id
                    FROM
                        pedido_detalhes
                    JOIN
                        usuarios ON usuarios.id = pedido_detalhes.id_usuario
                    JOIN
                        pedidos ON pedidos.id_detalhes = pedido_detalhes.id
                    WHERE
                        pedido_detalhes.aprovado = 1 AND pedido_detalhes.finalizado = 0
                        AND DATEDIFF(CURDATE(), pedido_detalhes.data_pedido) >= 7
                    GROUP BY
                        pedidos.id_detalhes
                    ORDER BY
                        pedido_detalhes.data_pedido DESC
                ');

                $sql->execute();
                
                $dados = $sql->fetchAll();

                if(empty($dados)) {
                    return false;
                }

                $resultados = array();

                foreach ($dados as $key => $value) {
                    $usuario = new Usuario(
                        NULL,
                        $value['nome'],
                        $value['sobrenome'],
                        $value['email'],
                        NULL,
                        NULL,
                        $value['matricula'],
                        NULL,
                        NULL,
                        NULL
                    );

                    $pedidoDetalhes = new PedidoDetalhes(
                        NULL,
                        $value['codigo_pedido'], 
                        $value['id'],
                        NULL,
                        NULL,
                        $value['data_pedido'],
                        NULL,
                        $usuario
                    );

                    $resultados[] = $pedidoDetalhes;
                }

                return $resultados;
            }

            catch(Exception $e) {
                Painel::alert("erro", "Erro ao se conectar ao banco de dados.");
            }
        }

        public static function retornaEmprestimosMaisDe1Hora() {
            try{
                $sql = Mysql::conectar()->prepare('
                    SELECT
                        pedido_detalhes.codigo_pedido,
                        usuarios.nome,
                        usuarios.matricula,
                        usuarios.sobrenome,
                        usuarios.email,
                        pedido_detalhes.data_pedido,
                        pedido_detalhes.id
                    FROM
                        pedido_detalhes
                    JOIN
                        usuarios ON usuarios.id = pedido_detalhes.id_usuario
                    JOIN
                        pedidos ON pedidos.id_detalhes = pedido_detalhes.id
                    WHERE
                        pedido_detalhes.aprovado = 0 AND pedido_detalhes.finalizado = 0
                        AND TIMESTAMPDIFF(HOUR, pedido_detalhes.data_pedido, NOW()) > 1
                    GROUP BY
                        pedidos.id_detalhes
                    ORDER BY
                        pedido_detalhes.data_pedido DESC
                ');

                $sql->execute();
                
                $dados = $sql->fetchAll();

                if(empty($dados)) {
                    return false;
                }

                $resultados = array();

                foreach ($dados as $key => $value) {
                    $usuario = new Usuario(
                        NULL,
                        $value['nome'],
                        $value['sobrenome'],
                        $value['email'],
                        NULL,
                        NULL,
                        $value['matricula'],
                        NULL,
                        NULL,
                        NULL
                    );

                    $pedidoDetalhes = new PedidoDetalhes(
                        NULL,
                        $value['codigo_pedido'], 
                        $value['id'],
                        NULL,
                        NULL,
                        $value['data_pedido'],
                        NULL,
                        $usuario
                    );

                    $resultados[] = $pedidoDetalhes;
                }

                return $resultados;
            }

            catch(Exception $e) {
                Painel::alert("erro", "Erro ao se conectar ao banco de dados.");
            }
        }

        public static function itensViaIDDetalhe($pedidoDetalheId) {
            try {
                $sql = Mysql::conectar()->prepare('
                    SELECT
                    pedidos.quantidade_item,
                    estoque.nome as nomeItem,
                    estoque.tipo,
                    estoque.id,
                    estoque.is_ativado
                    FROM
                        pedido_detalhes
                    JOIN
                        usuarios ON usuarios.id = pedido_detalhes.id_usuario
                    JOIN
                        pedidos ON pedidos.id_detalhes = pedido_detalhes.id
                    JOIN
                        estoque ON estoque.id = pedidos.id_estoque
                    WHERE
                        pedido_detalhes.id = ?
                    ORDER BY
                        estoque.nome ASC;
                ');

                $sql->execute(array($pedidoDetalheId));
                
                $dados = $sql->fetchAll();
                
                if(empty($dados)){
                    return false;
                }

                $resultados = array();

                foreach ($dados as $key => $value) {
                    $estoque = new Estoque(
                        $value['id'],
                        $value['nomeItem'],
                        0,
                        $value['tipo'],
                        $value['is_ativado']
                    );

                    $pedidos = new Pedido(
                        $value['quantidade_item'],
                        NULL,
                        NULL,
                        NULL,
                        $estoque
                    );
            
                    $resultados[] = $pedidos;
                }

                return $resultados;
            }

            catch(Exception $e) {
                Painel::alert("erro", "Erro ao se conectar ao banco de dados.");
            }
        }

        public static function verificaEmprestimoProduto($idProduto) {
            try {
                $sql = Mysql::conectar()->prepare('
                    SELECT
                        pedido_detalhes.finalizado
                    FROM
                        pedido_detalhes
                    JOIN
                        pedidos ON pedidos.id_detalhes = pedido_detalhes.id
                    JOIN
                        estoque ON estoque.id = pedidos.id_estoque
                    WHERE
                        pedidos.id_estoque = ? AND pedido_detalhes.finalizado = 0
                ');

                $sql->execute(array($idProduto));
                
                $dados = $sql->fetch();
                
                if(empty($dados)){
                    return false;
                }

                $pedidoDetalhes = new PedidoDetalhes(
                    NULL,
                    NULL,
                    NULL,
                    NULL, 
                    $dados['finalizado']
                );

                return $pedidoDetalhes;
            }

            catch(Exception $e) {
                Painel::alert("erro", "Erro ao se conectar ao banco de dados.");
            }
        }

        public static function retornaPedidosFinalizadosByData($dataInicial, $dataFinal) {
            try{ 
                $sql = Mysql::conectar()->prepare('
                    SELECT
                    pedido_detalhes.codigo_pedido,
                    usuarios.nome,
                    usuarios.email,
                    usuarios.matricula,
                    usuarios.sobrenome,
                    usuarios.id as idUsuario,
                    pedido_detalhes.data_pedido,
                    pedido_detalhes.data_finalizado,
                    pedido_detalhes.id
                    FROM
                        pedido_detalhes
                    JOIN
                        usuarios ON usuarios.id = pedido_detalhes.id_usuario
                    JOIN
                        pedidos ON pedidos.id_detalhes = pedido_detalhes.id
                    WHERE
                        pedido_detalhes.finalizado = 1
                        AND pedido_detalhes.aprovado = 1
                        AND pedido_detalhes.data_pedido BETWEEN ? AND ?
                    GROUP BY
                        pedidos.id_detalhes
                    ORDER BY
                        pedido_detalhes.data_pedido DESC
                ');

                $sql->execute(array($dataInicial, $dataFinal));
                
                $dados = $sql->fetchAll();

                if(empty($dados)) {
                    return false;
                }

                $resultados = array();

                foreach ($dados as $key => $value) {
                    $usuario = new Usuario(
                        NULL,
                        $value['nome'],
                        $value['sobrenome'],
                        $value['email'],
                        NULL,
                        NULL,
                        $value['matricula'],
                        NULL,
                        NULL,
                        (int) $value['idUsuario']
                    );

                    $pedidoDetalhes = new PedidoDetalhes(
                        NULL,
                        $value['codigo_pedido'], 
                        $value['id'],
                        NULL,
                        NULL,
                        $value['data_pedido'],
                        $value['data_finalizado'],
                        $usuario
                    );

                    $resultados[] = $pedidoDetalhes;
                }

                return $resultados;
            }

            catch(Exception $e) {
                Painel::alert("erro", "Erro ao se conectar ao banco de dados.");
            }
        }

        public static function retornaTodosPedidosFinalizados() {
            try{
                $sql = Mysql::conectar()->prepare('
                    SELECT
                    pedido_detalhes.codigo_pedido,
                    usuarios.nome,
                    usuarios.email,
                    usuarios.matricula,
                    usuarios.sobrenome,
                    usuarios.id as idUsuario,
                    pedido_detalhes.data_pedido,
                    pedido_detalhes.data_finalizado,
                    pedido_detalhes.id
                    FROM
                        pedido_detalhes
                    JOIN
                        usuarios ON usuarios.id = pedido_detalhes.id_usuario
                    JOIN
                        pedidos ON pedidos.id_detalhes = pedido_detalhes.id
                    WHERE
                        pedido_detalhes.finalizado = 1
                        AND pedido_detalhes.aprovado = 1
                    GROUP BY
                        pedidos.id_detalhes
                    ORDER BY
                        pedido_detalhes.data_pedido DESC
                ');

                $sql->execute();
                
                $dados = $sql->fetchAll();

                if(empty($dados)) {
                    return false;
                }

                $resultados = array();

                foreach ($dados as $key => $value) {
                    $usuario = new Usuario(
                        NULL,
                        $value['nome'],
                        $value['sobrenome'],
                        $value['email'],
                        NULL,
                        NULL,
                        $value['matricula'],
                        NULL,
                        NULL,
                        (int) $value['idUsuario']
                    );

                    $pedidoDetalhes = new PedidoDetalhes(
                        NULL,
                        $value['codigo_pedido'], 
                        $value['id'],
                        NULL,
                        NULL,
                        $value['data_pedido'],
                        $value['data_finalizado'],
                        $usuario
                    );

                    $resultados[] = $pedidoDetalhes;
                }

                return $resultados;
            }

            catch(Exception $e) {
                Painel::alert("erro", "Erro ao se conectar ao banco de dados.");
            }
        }

        public static function mudarStatusPedido(PedidoDetalhes $pedidoDetalhes, $feedback, $idUsuario) {
            try {
                $sql = Mysql::conectar()->prepare('UPDATE `pedido_detalhes` SET aprovado = ?, finalizado = ?, id_usuario_aprovou = ?, id_usuario_finalizou = ? WHERE codigo_pedido = ?');
                $sql->execute(
                    array(
                        $pedidoDetalhes->getAprovado(),
                        $pedidoDetalhes->getFinalizado(),
                        ($pedidoDetalhes->getAprovado() == 1) ? $idUsuario : NULL,
                        ($pedidoDetalhes->getFinalizado() == 1) ? $idUsuario : NULL,
                        $pedidoDetalhes->getCodigoPedido()
                    )
                );

                $mail = new Email();
                
                $destinatario = new Usuario(
                    NULL,
                    $pedidoDetalhes->usuario->getNome(), 
                    $pedidoDetalhes->usuario->getSobrenome(),
                    $pedidoDetalhes->usuario->getEmail(),
                    NULL, NULL, NULL, NULL
                );

                $mail->addAdress(
                    $destinatario
                );

                if($pedidoDetalhes->getAprovado() == 1) {
                    Painel::alert("sucesso", "O pedido foi aprovado, o usuário será notificado. Redirecionando.");

                    $gerarPDF = new Comprovante();
                    $gerarPDF->gerarPDF(
                        $pedidoDetalhes
                    );

                    $mail->EmailPedidoAprovado(
                        $pedidoDetalhes,
                        $feedback
                    );
                    
                    $mail->enviarEmail();

                    Painel::deleteComprovante($pedidoDetalhes->getCodigoPedido());
                }

                if($pedidoDetalhes->getAprovado() == 0) {
                    Painel::alert("sucesso", "O pedido foi rejeitado, o usuário será notificado. Redirecionando.");

                    $mail->EmailPedidoNegado(
                        $pedidoDetalhes,
                        $feedback
                    );

                    $mail->enviarEmail();
                }

                unset($_SESSION['feedback']);
            }

            catch(Exception $e) {
                Painel::alert("erro", "Erro ao se conectar ao banco de dados.");
            }
        }

        public static function marcarComoFinalizado(PedidoDetalhes $pedidoDetalhes, $idUsuario) {
            try {
                $sql = Mysql::conectar()->prepare('UPDATE `pedido_detalhes` SET finalizado = 1, data_finalizado = ?, id_usuario_finalizou = ? WHERE codigo_pedido = ?');

                // 2001-03-10 (the MySQL DATETIME format)
                $dataHoje = date("Y-m-d H:i:s");
                $pedidoDetalhes->setDataFinalizado($dataHoje);

                $sql->execute(array($pedidoDetalhes->getDataFinalizado(), $idUsuario, $pedidoDetalhes->getCodigoPedido()));

                $usuario = new Usuario(
                    NULL, $pedidoDetalhes->usuario->getNome(),
                    $pedidoDetalhes->usuario->getSobrenome(),
                    $pedidoDetalhes->usuario->getEmail(),
                    NULL, NULL, NULL, NULL
                );

                $mail = new Email();
                $mail->addAdress($usuario);
                $mail->EmailPedidoFinalizado($pedidoDetalhes);
                $mail->enviarEmail();

                return true;
            } catch (Exception $e) {
                Painel::alert("erro", "Erro ao se conectar ao banco de dados");
            }
        }

        //Notifica apenas os com mais de 7 dias.
        public static function notificarUsuariosPedidoAtivos() {
            
            $PedidoDetalhes = PedidoDetalhes::retornaEmprestimosMaisSeteDias();

            if($PedidoDetalhes != false) {
        
                foreach ($PedidoDetalhes as $PedidoDetalhe) {
                    $destinatario = new Usuario(
                        NULL,
                        $PedidoDetalhe->usuario->getNome(), 
                        $PedidoDetalhe->usuario->getSobrenome(),
                        $PedidoDetalhe->usuario->getEmail(),
                        NULL, NULL, NULL, NULL
                    );
        
                    $gerarPDF = new Comprovante();
                    $gerarPDF->gerarPDF(
                        $PedidoDetalhe
                    );
        
                    $mail = new Email();
                    
                    $mail->addAdress(
                        $destinatario
                    );
        
                    $mail->EmailPedidoAtivo(
                        $PedidoDetalhe
                    );
                
                    $mail->enviarEmail();
        
                    Painel::deleteComprovante($PedidoDetalhe->getCodigoPedido());
                }
            }
        }

        //Notifica apenas os com mais de 1 hora de pedido.
        public static function notificarUsuariosPedidoMaisde1Hora() {
            
            $PedidoDetalhes = PedidoDetalhes::retornaEmprestimosMaisDe1Hora();

            if($PedidoDetalhes != false) {
                try {
                    foreach ($PedidoDetalhes as $PedidoDetalhe) {
                    $destinatario = new Usuario(
                        NULL,
                        $PedidoDetalhe->usuario->getNome(), 
                        $PedidoDetalhe->usuario->getSobrenome(),
                        $PedidoDetalhe->usuario->getEmail(),
                        NULL, NULL, NULL, NULL
                    );
        
        
                    $mail = new Email();
                    
                    $mail->addAdress(
                        $destinatario
                    );
        
                    $mail->EmailPedidoInativo(
                        $PedidoDetalhe
                    );
                    
                    $PedidoDetalhe->setAprovado(0);
                    $PedidoDetalhe->setFinalizado(1);
                    $mail->enviarEmail();
                        $sql = Mysql::conectar()->prepare('UPDATE `pedido_detalhes` SET aprovado = ?, finalizado = ? WHERE codigo_pedido = ?');
                        $sql->execute(
                            array(
                                $PedidoDetalhe->getAprovado(), 
                                $PedidoDetalhe->getFinalizado(), 
                                $PedidoDetalhe->getCodigoPedido()
                            )
                        );
                    }
                }

                catch (Exception $e) {
                    Painel::alert("erro", "Erro ao notificar usuários");
                }
            }
        }
        
        public static function retornaPedidosAtivosUsuario() {
            try{
                $sql = Mysql::conectar()->prepare('
                    SELECT
                    pedido_detalhes.codigo_pedido,
                    usuarios.nome,
                    usuarios.matricula,
                    usuarios.sobrenome,
                    pedido_detalhes.data_pedido,
                    pedido_detalhes.id
                    FROM
                        pedido_detalhes
                    JOIN
                        usuarios ON usuarios.id = pedido_detalhes.id_usuario
                    JOIN
                        pedidos ON pedidos.id_detalhes = pedido_detalhes.id
                    WHERE
                        pedido_detalhes.aprovado = 1 AND pedido_detalhes.finalizado = 0
                        AND pedido_detalhes.id_usuario = ?
                    GROUP BY
                        pedidos.id_detalhes
                    ORDER BY
                        pedido_detalhes.data_pedido DESC
                ');

                $sql->execute(array($_SESSION['id']));
                
                $dados = $sql->fetchAll();

                if(empty($dados)) {
                    return false;
                }

                $resultados = array();

                foreach ($dados as $key => $value) {
                    $usuario = new Usuario(
                        NULL,
                        $value['nome'],
                        $value['sobrenome'],
                        NULL,
                        NULL,
                        NULL,
                        $value['matricula'],
                        NULL,
                        NULL,
                        NULL
                    );

                    $pedidoDetalhes = new PedidoDetalhes(
                        NULL,
                        $value['codigo_pedido'], 
                        $value['id'],
                        NULL,
                        NULL,
                        $value['data_pedido'],
                        NULL,
                        $usuario
                    );

                    $resultados[] = $pedidoDetalhes;
                }

                return $resultados;
            }

            catch(Exception $e) {
                Painel::alert("erro", "Erro ao se conectar ao banco de dados.");
            }
        }

        public static function retornaPedidosPendentesUsuario() {
            try{
                $sql = Mysql::conectar()->prepare('
                    SELECT
                    pedido_detalhes.codigo_pedido,
                    usuarios.nome,
                    usuarios.matricula,
                    usuarios.sobrenome,
                    pedido_detalhes.data_pedido,
                    pedido_detalhes.id
                    FROM
                        pedido_detalhes
                    JOIN
                        usuarios ON usuarios.id = pedido_detalhes.id_usuario
                    JOIN
                        pedidos ON pedidos.id_detalhes = pedido_detalhes.id
                    WHERE
                        pedido_detalhes.aprovado = 0 AND pedido_detalhes.finalizado = 0
                        AND pedido_detalhes.id_usuario = ?
                    GROUP BY
                        pedidos.id_detalhes
                    ORDER BY
                        pedido_detalhes.data_pedido DESC
                ');

                $sql->execute(array($_SESSION['id']));
                
                $dados = $sql->fetchAll();

                if(empty($dados)) {
                    return false;
                }

                $resultados = array();

                foreach ($dados as $key => $value) {
                    $usuario = new Usuario(
                        NULL,
                        $value['nome'],
                        $value['sobrenome'],
                        NULL,
                        NULL,
                        NULL,
                        $value['matricula'],
                        NULL,
                        NULL,
                        NULL
                    );

                    $pedidoDetalhes = new PedidoDetalhes(
                        NULL,
                        $value['codigo_pedido'], 
                        $value['id'],
                        NULL,
                        NULL,
                        $value['data_pedido'],
                        NULL,
                        $usuario
                    );

                    $resultados[] = $pedidoDetalhes;
                }

                return $resultados;
            }

            catch(Exception $e) {
                Painel::alert("erro", "Erro ao se conectar ao banco de dados.");
            }
        }

        public static function retornaPedidosAntigosUsuario($comeco = null, $final = null) {
            try{
                if($comeco == null and $final == null) {
                    $sql = Mysql::conectar()->prepare("
                        SELECT
                        pedido_detalhes.codigo_pedido,
                        usuarios.nome,
                        usuarios.matricula,
                        usuarios.sobrenome,
                        pedido_detalhes.data_pedido,
                        pedido_detalhes.data_finalizado,
                        pedido_detalhes.id
                        FROM
                            pedido_detalhes
                        JOIN
                            usuarios ON usuarios.id = pedido_detalhes.id_usuario
                        JOIN
                            pedidos ON pedidos.id_detalhes = pedido_detalhes.id
                        WHERE
                            pedido_detalhes.aprovado = 1 AND pedido_detalhes.finalizado = 1
                            AND pedido_detalhes.id_usuario = ?
                        GROUP BY
                            pedidos.id_detalhes
                        ORDER BY
                            pedido_detalhes.data_pedido DESC
                    ");
                }

                else {
                    $comeco = filter_var($comeco, FILTER_SANITIZE_NUMBER_INT);
                    $final = filter_var($final, FILTER_SANITIZE_NUMBER_INT);

                    $sql = Mysql::conectar()->prepare("
                        SELECT
                            pedido_detalhes.codigo_pedido,
                            usuarios.nome,
                            usuarios.matricula,
                            usuarios.sobrenome,
                            pedido_detalhes.data_pedido,
                            pedido_detalhes.data_finalizado,
                            pedido_detalhes.id
                        FROM
                            pedido_detalhes
                        JOIN
                            usuarios ON usuarios.id = pedido_detalhes.id_usuario
                        JOIN
                            pedidos ON pedidos.id_detalhes = pedido_detalhes.id
                        WHERE
                            pedido_detalhes.aprovado = 1 AND pedido_detalhes.finalizado = 1
                            AND pedido_detalhes.id_usuario = ?
                        GROUP BY
                            pedidos.id_detalhes
                        ORDER BY
                            pedido_detalhes.data_pedido DESC LIMIT $comeco, $final
                    ");
                }
                $sql->execute(array($_SESSION['id']));
                
                $dados = $sql->fetchAll();

                if(empty($dados)) {
                    return false;
                }

                $resultados = array();

                foreach ($dados as $key => $value) {
                    $usuario = new Usuario(
                        NULL,
                        $value['nome'],
                        $value['sobrenome'],
                        NULL,
                        NULL,
                        NULL,
                        $value['matricula'],
                        NULL,
                        NULL,
                        NULL
                    );

                    $pedidoDetalhes = new PedidoDetalhes(
                        NULL,
                        $value['codigo_pedido'], 
                        $value['id'],
                        NULL,
                        NULL,
                        $value['data_pedido'],
                        $value['data_finalizado'],
                        $usuario
                    );

                    $resultados[] = $pedidoDetalhes;
                }

                return $resultados;
            }

            catch(Exception $e) {
                Painel::alert("erro", "Erro ao se conectar ao banco de dados.");
            }
        }

        public static function returnDataPedidosAntigosUsuario($data, $filtro) {
            try{
                $sql = Mysql::conectar()->prepare("
                    SELECT
                        pedido_detalhes.codigo_pedido,
                        usuarios.nome,
                        usuarios.matricula,
                        usuarios.sobrenome,
                        pedido_detalhes.data_pedido,
                        pedido_detalhes.data_finalizado,
                        pedido_detalhes.id
                    FROM
                        pedido_detalhes
                    JOIN
                        usuarios ON usuarios.id = pedido_detalhes.id_usuario
                    JOIN
                        pedidos ON pedidos.id_detalhes = pedido_detalhes.id
                    JOIN 
                        estoque ON estoque.id = pedidos.id_estoque
                    WHERE
                        $filtro LIKE '%$data%' AND
                        pedido_detalhes.aprovado = 1 AND pedido_detalhes.finalizado = 1
                        AND pedido_detalhes.id_usuario = ?
                    GROUP BY
                        pedidos.id_detalhes
                    ORDER BY
                        pedido_detalhes.data_pedido DESC
                ");

                $sql->execute(array($_SESSION['id']));
                
                $dados = $sql->fetchAll();

                if(empty($dados)) {
                    return false;
                }

                $resultados = array();

                foreach ($dados as $key => $value) {
                    $usuario = new Usuario(
                        NULL,
                        $value['nome'],
                        $value['sobrenome'],
                        NULL,
                        NULL,
                        NULL,
                        $value['matricula'],
                        NULL,
                        NULL,
                        NULL
                    );

                    $pedidoDetalhes = new PedidoDetalhes(
                        NULL,
                        $value['codigo_pedido'], 
                        $value['id'],
                        NULL,
                        NULL,
                        $value['data_pedido'],
                        $value['data_finalizado'],
                        $usuario
                    );

                    $resultados[] = $pedidoDetalhes;
                }

                return $resultados;
            }

            catch(Exception $e) {
                Painel::alert("erro", "Erro ao se conectar ao banco de dados.");
            }
        }

        public function getId() {
            return $this->id;
        }

        public function setId($id) {
            $this->id = $id;
        }

        public function getIdUsuario() {
            return $this->id_usuario;
        }

        public function setIdUsuario($id_usuario) {
            $this->id_usuario = $id_usuario;
        }

        public function getAprovado() {
            return $this->aprovado;
        }

        public function setAprovado($aprovado) {
            $this->aprovado = $aprovado;
        }

        public function getFinalizado() {
            return $this->finalizado;
        }

        public function setFinalizado($finalizado) {
            $this->finalizado = $finalizado;
        }

        public function getDataPedido() {
            return $this->data_pedido;
        }

        public function setDataPedido($data_pedido) {
            $this->data_pedido = $data_pedido;
        }

        public function getDataFinalizado() {
            return $this->data_finalizado;
        }

        public function setDataFinalizado($data_finalizado) {
            $this->data_finalizado = $data_finalizado;
        }

        public function getCodigoPedido() {
            return $this->codigo_pedido;
        }

        public function setCodigoPedido($codigo_pedido) {
            $this->codigo_pedido = $codigo_pedido;
        }

        public function setIdUsuarioAprovou($id_usuario_aprovou) {
            $this->id_usuario_aprovou = $id_usuario_aprovou;
        }

        public function getIdUsuarioAprovou() {
            return $this->id_usuario_aprovou;
        }

        public function setIdUsuarioFinalizou($id_usuario_finalizou) {
            $this->id_usuario_finalizou = $id_usuario_finalizou;
        }

        public function getIdUsuarioFinalizou() {
            return $this->id_usuario_finalizou;
        }
    }
?>
