<?php
	include ('../config.php');

	if(!Painel::logado()) {
		include('login.php');
	} else {
        $pedidos = Pedido::obterPedidosParaAviso();
        if(count($pedidos) > 0) {
            $dados_dos_emprestimos = '';
            foreach ($pedidos as $pedido) {
                $dados_dos_emprestimos .= '<p><a href="https://almoxarifadoec.quixada.ufc.br/almoxarifado/painel/visualizar-pedido?codigo_pedido=' . htmlentities($pedido['codigo_pedido']) . '">Pedido ' . htmlentities($pedido['id']) . '</a></p>';
            }
            $nome_do_aluno = htmlentities($_SESSION['nome'] . ' ' . $_SESSION['sobrenome']);
            
			include('aviso-atrasos.php'); 
        } else {
            include('main.php');
        }
	}
?>