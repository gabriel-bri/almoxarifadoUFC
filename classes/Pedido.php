<?php
    class Pedido {
        private $id_pedidos;
        private $quantidade_item;
        private $id_estoque;
        private $id_detalhes;
        public $estoque;

        // Cadastra um novo pedido.
        public static function cadastrarPedido(Pedido $pedido) {
            try {
                $sql = Mysql::conectar()->prepare('INSERT INTO `pedidos` (id_pedidos, quantidade_item, id_estoque, id_detalhes)
            VALUES (null, ?, ?, ?)');

                $sql->execute(
                    array(
                        $pedido->getQuantidadeItem(),
                        $pedido->getIdEstoque(),
                        $pedido->getIdDetalhes()
                    )
                );
            }

            catch(Exception $e) {
                Painel::alert("erro", "Erro ao se conectar ao banco de dados");
            }
        }

        public static function obterPedidosPorIdDetalhes($idDetalhes) {
            try {
                $sql = Mysql::conectar()->prepare('SELECT * FROM `pedidos` WHERE id_detalhes = ?');
                $sql->execute([$idDetalhes]);

                $dados = $sql->fetchAll();

                $pedidos = [];
                foreach ($dados as $dado) {
                    $pedidos[] = new Pedido(
                        $dado['quantidade_item'],
                        $dado['id_estoque'],
                        $dado['id_detalhes'],
                        $dado['id_pedidos']
                    );
                }

                return $pedidos;
            } catch(Exception $e) {
                Painel::alert("erro", "Erro ao obter os pedidos associados ao PedidoDetalhes.");
                return [];
            }
        }

        // Construtor
        public function __construct($quantidade_item, $id_estoque, $id_detalhes, $id_pedidos = NULL, $estoque = NULL) {
            $this->id_pedidos = $id_pedidos;
            $this->quantidade_item = $quantidade_item;
            $this->id_estoque = $id_estoque;
            $this->id_detalhes = $id_detalhes;
            $this->estoque = $estoque;
        }

        // Getter e Setter para id_pedidos
        public function getIdPedidos() {
            return $this->id_pedidos;
        }

        public function setIdPedidos($id_pedidos) {
            $this->id_pedidos = $id_pedidos;
        }

        // Getter e Setter para quantidade_item
        public function getQuantidadeItem() {
            return $this->quantidade_item;
        }

        public function setQuantidadeItem($quantidade_item) {
            $this->quantidade_item = $quantidade_item;
        }

        // Getter e Setter para id_estoque
        public function getIdEstoque() {
            return $this->id_estoque;
        }

        public function setIdEstoque($id_estoque) {
            $this->id_estoque = $id_estoque;
        }

        // Getter e Setter para id_detalhes
        public function getIdDetalhes() {
            return $this->id_detalhes;
        }

        public function setIdDetalhes($id_detalhes) {
            $this->id_detalhes = $id_detalhes;
        }
    }

?>