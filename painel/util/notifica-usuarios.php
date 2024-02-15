<?php 
    require '../../config.php';
    
    // Este arquivo é chamada através de um cronjob
    // definido no sistema operacional, que chama a função
    // responsável por notificar usuários com pedidos ativos.
    PedidoDetalhes::notificarUsuariosPedidoAtivos();
?>