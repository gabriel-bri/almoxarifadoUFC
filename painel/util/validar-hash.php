<?php
require '../../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigoPedido = filter_input(INPUT_POST, 'codigoPedido', FILTER_SANITIZE_STRING);
    $hashRecebido = filter_input(INPUT_POST, 'hash', FILTER_SANITIZE_STRING);

    try {
        // Verificar se o pedido existe no banco de dados
        $pedidoDetalhes = PedidoDetalhes::retornaDadosPedidoViaCodigo($codigoPedido);

        if (!$pedidoDetalhes) {
            // Se o pedido não existir, redireciona com erro
            header('Location: /validar-hash.php?status=not_found');
            exit;
        }

        // Calcular o hash esperado
        $hashEsperado = $pedidoDetalhes->gerarHash();

        // Validar o hash
        if ($hashRecebido === $hashEsperado) {
            header('Location: /validar-hash.php?status=valid');
        } else {
            header('Location: /validar-hash.php?status=invalid');
        }
    } catch (Exception $e) {
        // Redireciona com erro genérico
        header('Location: /validar-hash.php?status=error');
    }
} else {
    // Se o método não for POST, redireciona para a página inicial
    header('Location: /index.html');
}
