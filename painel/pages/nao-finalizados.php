<?php  
	verificaPermissaoPagina(2);
?>

<div class="box-content">
	<h2> <i class="fa fa-pencil-alt"></i>Empréstimos Não Finalizados</h2>

	<div class="wraper-table">
		<table>
			<tr>
				<td>Nome</td>
				<td>Sobrenome</td>
				<td>Matrícula</td>
				<td>Código</td>
				<td>Data</td>
				<td>#</td>
			</tr>
			<?php
				$pedidosNaoFinalizados = Pedido::retornaPedidosNaoFinalizados();
				while($dadosPedidos = $pedidosNaoFinalizados->fetch(PDO::FETCH_ASSOC)) {
					
			?>

				<tr>
					<td><?php echo htmlentities($dadosPedidos['nome']); ?></td>

					<td><?php echo htmlentities($dadosPedidos['sobrenome']); ?></td>

					<td><?php echo htmlentities($dadosPedidos['matricula']); ?></td>

					<td><?php echo htmlentities($dadosPedidos['codigo_pedido']); ?></td>

					<td>
                        <?php
                            $dataConvertida = htmlentities($dadosPedidos['data_pedido']);
                            $dataConvertida = implode("/",array_reverse(explode("-",$dataConvertida)));
                            echo $dataConvertida; 
                        ?>
                    </td>
					
					<td><a href="<?php echo INCLUDE_PATH_PAINEL?>fechar-pedido?codigo_pedido=<?php echo htmlentities($dadosPedidos['codigo_pedido']); ?>" class="btn edit">Concluir pedido <i class="fa fa-thumbs-up"></i></a></td>
				</tr>
			<?php } ?>
		</table>
	</div>
</div>