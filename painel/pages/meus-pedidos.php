<?php  
	verificaPermissaoPagina(1);
?>

<div class="box-content">
	<h2> <i class="fa fa-pencil-alt"></i> Meus Pedidos</h2>

	<div class="wraper-table">
		<table>
			<tr>
				<td>Código</td>
				<td>Data</td>
				<td>Status</td>
				<td>#</td>
			</tr>
			<?php
				$pedidosNovos = Pedido::retornaPedidosAtivosUsuario();
				while($dadosPedidos = $pedidosNovos->fetch(PDO::FETCH_ASSOC)) {
					
			?>

				<tr>
					<td><?php echo htmlentities($dadosPedidos['codigo_pedido']); ?></td>

					<td>
                        <?php
                            $dataConvertida = htmlentities($dadosPedidos['data_pedido']);
                            $dataConvertida = implode("/",array_reverse(explode("-",$dataConvertida)));
                            echo $dataConvertida; 
                        ?>
                    </td>
					
					<td>Ativo</td>
					
					<td><a href="<?php echo INCLUDE_PATH_PAINEL?>visualizar-pedido?codigo_pedido=<?php echo htmlentities($dadosPedidos['codigo_pedido']); ?>" class="btn edit">Visualizar pedido <i class="fa fa-eye"></i></a></td>
				</tr>
			<?php } ?>
		</table>
	</div>
</div>