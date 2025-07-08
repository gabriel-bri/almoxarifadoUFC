<?php
verificaPermissaoPagina(1);

if (!isset($_GET['codigo_pedido']) || strlen($_GET['codigo_pedido']) != 20) {
    Painel::alert("erro", "Código do pedido inválido.");
    exit;
}

$codigoPedido = filter_var($_GET['codigo_pedido'], FILTER_SANITIZE_STRING);


$sucesso = PedidoDetalhes::cancelarPedidoDoUsuario($codigoPedido);

echo "<div class='box-content'>";
if ($sucesso) {
    echo "<h2>Sucesso</h2><p>Pedido cancelado com sucesso.</p>";
} else {
    echo "<h2>Erro</h2><p>Erro ao cancelar o pedido ou o pedido já foi processado.</p>";
}
echo "<br><a href='" . INCLUDE_PATH_PAINEL . "meus-pedidos' class='btn'>Voltar</a>";
echo "</div>";
?>
