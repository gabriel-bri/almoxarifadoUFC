<?php
    class Pedido {
        public static function cadastrarPedido($qtd, $user, $prod, $cod) {

            $sql = Mysql::conectar()->prepare('INSERT INTO `pedidos` (id_pedidos, quantidade_item, id_usuario, id_estoque, data_pedido, codigo_pedido)
            VALUES (null, ?, ?, ?, ?, ?)');

            // 2001-03-10 (the MySQL DATETIME format)
            $dataPedido = date("Y-m-d");

            $mail = new Email();
            $mail->addAdress($_SESSION['email'], $_SESSION['nome'] . " " . $_SESSION['sobrenome']);
            $mail->EmailConfirmacaoPedido($_SESSION['nome'], date("d/m/Y"), $cod);
            $mail->enviarEmail();        
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

        public static function jaNoCarrinho($id){
            foreach ($_SESSION['carrinho'] as $chave => $row){
                $idProduto =  $row['id'];
                if($id == $idProduto) {
                    return true;
                }
            }

            return false;

        }

        public static function retornaUltimosPedidos() {
            $sql = Mysql::conectar()->prepare('SELECT DISTINCT (pedidos.codigo_pedido), usuarios.nome FROM pedidos JOIN usuarios ON usuarios.id = pedidos.id_usuario WHERE pedidos.aprovado = 0 AND pedidos.finalizado = 0 ORDER BY pedidos.codigo_pedido, usuarios.nome DESC LIMIT 5');
            $sql->execute();
			return $sql;
        }

        public static function retornaPedidosPendentes() {
            $sql = Mysql::conectar()->prepare('SELECT DISTINCT (pedidos.codigo_pedido), usuarios.nome, usuarios.sobrenome, pedidos.codigo_pedido, pedidos.data_pedido FROM pedidos JOIN usuarios ON usuarios.id = pedidos.id_usuario WHERE pedidos.aprovado = 0 AND pedidos.finalizado = 0 ORDER BY pedidos.codigo_pedido, usuarios.nome DESC LIMIT 5');
            $sql->execute();
			return $sql;
        }

        public static function retornaPedidoPeloCodigo($codigoPedido) {
            $sql = Mysql::conectar()->prepare('SELECT estoque.nome AS nomeestoque, usuarios.sobrenome, pedidos.data_pedido, usuarios.nome, estoque.tipo, pedidos.quantidade_item FROM pedidos JOIN usuarios ON usuarios.id = pedidos.id_usuario JOIN estoque ON estoque.id = pedidos.id_estoque WHERE pedidos.aprovado = 0 AND pedidos.finalizado = 0 AND pedidos.codigo_pedido = ? ORDER BY pedidos.codigo_pedido, usuarios.nome;');
            $sql->execute(array($codigoPedido));
			return $sql;
        }
    }

?>