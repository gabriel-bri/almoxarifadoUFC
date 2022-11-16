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
            $sql = Mysql::conectar()->prepare('SELECT DISTINCT (pedidos.codigo_pedido), usuarios.nome FROM pedidos JOIN usuarios ON usuarios.id = pedidos.id_usuario WHERE pedidos.aprovado = 1 AND pedidos.finalizado = 0 ORDER BY pedidos.codigo_pedido, usuarios.nome DESC LIMIT 5');
            $sql->execute();
			return $sql;
        }

        public static function retornaPedidosPendentes() {
            $sql = Mysql::conectar()->prepare('SELECT DISTINCT (pedidos.codigo_pedido), usuarios.nome, usuarios.sobrenome, pedidos.codigo_pedido, pedidos.data_pedido FROM pedidos JOIN usuarios ON usuarios.id = pedidos.id_usuario WHERE pedidos.aprovado = 0 AND pedidos.finalizado = 0 ORDER BY pedidos.codigo_pedido, usuarios.nome DESC');
            $sql->execute();
			return $sql;
        }

        public static function retornaPedidosNaoFinalizados() {
            $sql = Mysql::conectar()->prepare('SELECT DISTINCT (pedidos.codigo_pedido), usuarios.nome, usuarios.sobrenome, pedidos.codigo_pedido, pedidos.data_pedido FROM pedidos JOIN usuarios ON usuarios.id = pedidos.id_usuario WHERE pedidos.aprovado = 1 AND pedidos.finalizado = 0 ORDER BY pedidos.codigo_pedido, usuarios.nome DESC');
            $sql->execute();
            return $sql;
        }

        public static function retornaDadosBasicosPedidoNaoFinalizados($codigoPedido) {
            $sql = Mysql::conectar()->prepare('SELECT DISTINCT (pedidos.codigo_pedido), usuarios.nome, usuarios.email, usuarios.sobrenome, pedidos.codigo_pedido, pedidos.data_pedido FROM pedidos JOIN usuarios ON usuarios.id = pedidos.id_usuario WHERE pedidos.aprovado = 1 AND pedidos.finalizado = 0 AND pedidos.codigo_pedido = ? ORDER BY pedidos.codigo_pedido, usuarios.nome');
            $sql->execute(array($codigoPedido));
            $dadosBasicos = $sql->fetch(PDO::FETCH_ASSOC);  
            return $dadosBasicos;
        }   

        public static function retornaDadosBasicosPedidoPendente($codigoPedido) {
            $sql = Mysql::conectar()->prepare('SELECT DISTINCT (pedidos.codigo_pedido), usuarios.nome, usuarios.email, usuarios.sobrenome, pedidos.codigo_pedido, pedidos.data_pedido FROM pedidos JOIN usuarios ON usuarios.id = pedidos.id_usuario WHERE pedidos.aprovado = 0 AND pedidos.finalizado = 0 AND pedidos.codigo_pedido = ? ORDER BY pedidos.codigo_pedido, usuarios.nome');
            $sql->execute(array($codigoPedido));
            $dadosBasicos = $sql->fetch(PDO::FETCH_ASSOC);  
            return $dadosBasicos;
        }   

        public static function retornaPedidoPeloCodigo($codigoPedido) {
            $sql = Mysql::conectar()->prepare('SELECT estoque.nome AS nome_estoque, estoque.tipo, pedidos.quantidade_item FROM pedidos JOIN usuarios ON usuarios.id = pedidos.id_usuario JOIN estoque ON estoque.id = pedidos.id_estoque WHERE pedidos.codigo_pedido = ? ORDER BY pedidos.codigo_pedido, usuarios.nome;');
            $sql->execute(array($codigoPedido));
			return $sql;
        }

        public static function retornaPedidosPendentesUsuario() {
            $sql = Mysql::conectar()->prepare('SELECT DISTINCT (pedidos.codigo_pedido), usuarios.nome, usuarios.sobrenome, pedidos.codigo_pedido, pedidos.data_pedido FROM pedidos JOIN usuarios ON usuarios.id = pedidos.id_usuario WHERE pedidos.aprovado = 0 AND pedidos.finalizado = 0 AND pedidos.id_usuario = ? ORDER BY pedidos.codigo_pedido, usuarios.nome DESC');
            $sql->execute(array($_SESSION['id']));
            return $sql;
        }

        public static function retornaDadosBasicosPedidoAtivoUsuario($codigoPedido) {
            $sql = Mysql::conectar()->prepare('SELECT DISTINCT (pedidos.codigo_pedido), usuarios.nome, usuarios.sobrenome, pedidos.codigo_pedido, pedidos.data_pedido FROM pedidos JOIN usuarios ON usuarios.id = pedidos.id_usuario WHERE pedidos.aprovado = 1 AND pedidos.finalizado = 0 AND pedidos.id_usuario = ? AND pedidos.codigo_pedido = ? ORDER BY pedidos.codigo_pedido, usuarios.nome');
            $sql->execute(array($_SESSION['id'], $codigoPedido));
            $dadosBasicos = $sql->fetch(PDO::FETCH_ASSOC);  
            return $dadosBasicos;
        }

        public static function retornaPedidosAtivosUsuario() {
            $sql = Mysql::conectar()->prepare('SELECT DISTINCT (pedidos.codigo_pedido), usuarios.nome, usuarios.sobrenome, pedidos.codigo_pedido, pedidos.data_pedido FROM pedidos JOIN usuarios ON usuarios.id = pedidos.id_usuario WHERE pedidos.aprovado = 1 AND pedidos.finalizado = 0 AND pedidos.id_usuario = ? ORDER BY pedidos.codigo_pedido, usuarios.nome DESC');
            $sql->execute(array($_SESSION['id']));
            return $sql;
        }

        public static function mudarStatusPedido($codigoPedido, $aprovado, $finalizado, $dataPedido, $nome, $sobrenome, $email, $feedback, $dadosPDF = NULL) {
            $sql = Mysql::conectar()->prepare('UPDATE `pedidos` SET aprovado = ?, finalizado = ? WHERE codigo_pedido = ?');
            $sql->execute(array($aprovado, $finalizado, $codigoPedido));

            $mail = new Email();
            $mail->addAdress($email, $nome . ' ' . $sobrenome);

            if($aprovado == 1) {
                $gerarPDF = new Comprovante();
                $gerarPDF->gerarPDF($codigoPedido, $dadosPDF);

                $mail->EmailPedidoAprovado($nome, $dataPedido, $codigoPedido, $feedback);
            }

            if($aprovado == 0) {
                $mail->EmailPedidoNegado($nome, $dataPedido, $codigoPedido, $feedback);
            }

            $mail->enviarEmail();

            return $sql;
        }

        public static function marcarComoFinalizado($codigoPedido, $finalizado, $dataPedido, $nome, $sobrenome, $email) {
            $sql = Mysql::conectar()->prepare('UPDATE `pedidos` SET finalizado = ?, data_finalizado = ? WHERE codigo_pedido = ?');

            // 2001-03-10 (the MySQL DATETIME format)
            $dataHoje = date("Y-m-d");
            $sql->execute(array($finalizado, $dataHoje, $codigoPedido));

            $dataHoje = $dataHoje;
            $dataHoje = implode("/",array_reverse(explode("-",$dataHoje)));

            $mail = new Email();
            $mail->addAdress($email, $nome . ' ' . $sobrenome);
            $mail->EmailPedidoAprovado($nome, $dataPedido, $codigoPedido);
            $mail->EmailPedidoFinalizado($nome, $dataPedido, $dataHoje, $codigoPedido);
            $mail->enviarEmail();

            return $sql;
        }
    }

?>