<?php // aqui
verificaPermissaoPagina(1);

if (!isset($_POST['codigo_pedido']) || strlen($_POST['codigo_pedido']) != 20) {
    Painel::alert("erro", "Código do pedido inválido.");
    exit;
}

$codigoPedido = filter_var($_POST['codigo_pedido'], FILTER_SANITIZE_STRING);
$dadosPedido = PedidoDetalhes::retornaDadosPedidoViaCodigo($codigoPedido);

$sucesso = PedidoDetalhes::cancelarPedidoDoUsuario($dadosPedido);

echo "<div class='box-content'>";
if($sucesso) {
    echo "<h2>Sucesso</h2><p>Pedido cancelado com sucesso.</p>";
} else {
    echo "<h2>Ops</h2><p>Pedido já foi processado</p>";
}
echo "<br><a href='" . INCLUDE_PATH_PAINEL . "meus-pedidos' class='btn'>Voltar</a>";
echo "</div>";
?>
