<?php
    class Pedido {
        public static function cadastrarPedido($qtd, $user, $prod, $cod) {

            $sql = Mysql::conectar()->prepare('INSERT INTO `pedidos` (id_pedidos, quantidade_item, id_usuario, id_estoque, data_pedido, codigo_pedido)
            VALUES (null, ?, ?, ?, ?, ?)');

            $data = date("Y-m-d");                   
            // 2001-03-10 17:16:18 (the MySQL DATETIME format)

            var_dump($sql);
            $sql->execute(array($qtd, $user, $prod, $data, $cod));
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