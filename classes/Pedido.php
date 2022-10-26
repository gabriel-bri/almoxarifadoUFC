<?php
    class Pedido {
        public static function cadastrarPedido($qtd, $user, $prod, $cod) {

            $sql = Mysql::conectar()->prepare('INSERT INTO `pedidos` (id_pedidos, quantidade_item, id_usuario, id_estoque, data_pedido, codigo_pedido)
            VALUES (null, ?, ?, ?, ?, ?)');

            // 2001-03-10 (the MySQL DATETIME format)
            $dataPedido = date("Y-m-d");                   

            $sql->execute(array($qtd, $user, $prod, $dataPedido, $cod));
        }

        public static function statusCarrinho() {
            if(!isset($_SESSION['carrinho'])) {
                return "VAZIO";
            }

            else {
                $mensagem = count($_SESSION['carrinho']) > 1 ? " itens adicionados." : " item adicionado.";
                return count($_SESSION['carrinho']) . $mensagem;
            }
        }
    }

?>