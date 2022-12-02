<?php  
	verificaPermissaoPagina(1);
?>
<div class="box-content">
	<h2> <i class="fa fa-shopping-cart"></i>Editar carrinho</h2>
	<?php

		if(isset($_SESSION['carrinho'])) {
			if(isset($_GET['excluir'])) {
				$idArray = (int) $_GET['excluir'];
				array_splice($_SESSION['carrinho'], $idArray, 1);
				echo "<script>window.history.pushState('solicitar-emprestimo', 'Title', 'editar-emprestimo');</script>";
				Painel::alert("sucesso", "O item foi excluído do seu carrinho.");
			}

			if (isset($_POST['atualizar'])) {
				if(!isset($_POST['id_produto']) || !isset($_POST['qtd_' . $_SESSION['secret'] . "_" . $_SESSION['carrinho'][$_POST['id_produto']]['id']])) {
					Painel::alert("erro", "Algum parâmetro está ausente.");
				}

				else {
					$idProdutoArray = (int) filter_var($_POST['id_produto'], FILTER_SANITIZE_NUMBER_INT);
					$idProduto = $_SESSION['carrinho'][$idProdutoArray]['id'];
					$qtdProduto = (int) filter_var($_POST['qtd_' . $_SESSION['secret'] . "_" . $idProduto], FILTER_SANITIZE_NUMBER_INT);

					if($qtdProduto <= 0) {
						Painel::alert("erro", "Quantidade deve ser igual ou maior que 1");
					}

					else {
						$quantidadeDisponivel = Estoque::estoqueDisponivel(htmlentities($idProduto));

						if(is_null($quantidadeDisponivel[0])) {
							$limitePedido = Estoque::retornaQuantidade(htmlentities($idProduto));

							if($qtdProduto > $limitePedido['quantidade']) {
								Painel::alert("erro", "A quantidade está acima da disponível em nosso estoque.");		
							}

							else {
								$_SESSION['carrinho'][$idProdutoArray]['quantidade'] = $qtdProduto;
								Painel::alert("sucesso", "O seu carrinho foi atualizado.");
							}
						}

						else if($qtdProduto > $quantidadeDisponivel[0]) {
							Painel::alert("erro", "A quantidade está acima da disponível em nosso estoque.");
						}
					
						else {
							$_SESSION['carrinho'][$idProdutoArray]['quantidade'] = $qtdProduto;
							Painel::alert("sucesso", "O seu carrinho foi atualizado.");
						}
					}
				}
			}
		}

		else {
			Painel::alert("erro", "O seu carrinho está vazio");
			die();
		}
	?>
	<div class="wraper-table">
		<table>
			<tr>
				<td>Nome</td>
				<td>Quantidade</td>
				<td>#</td>
				<td>#</td>
				<td>#</td>
			</tr>
			<?php
				foreach ($_SESSION['carrinho'] as $chave => $row){
					$id = (int)$row['id'];
					$estoque = Estoque::select('id = ?', array($id));
					$quantidadeDisponivel = Estoque::estoqueDisponivel(htmlentities($row['id']));		
			?>

			<tr>
				<td><?php echo htmlentities($estoque['nome']); ?></td>
			
				<td><?php
						//Caso o id do produto não esteja na tabela de pedidos mostra a sua quantidade original.
				 		if(is_null($quantidadeDisponivel[0])) {
				 			echo htmlentities($estoque['quantidade']);
				 		}

				 		else {
							echo htmlentities($quantidadeDisponivel[0]);
				 		}
				 	?> 	
				</td>

				<form method="post">

					<td><input type="number" name="<?php echo "qtd_" . $_SESSION['secret'] . "_" . htmlentities($row['id']) ?>" value="<?php echo htmlentities($row['quantidade']); ?>"></td>

					<td><input type="submit" name="atualizar" value="Atualizar" class="cart"></td>

					<input type="hidden" name="id_produto" value="<?php echo htmlentities($chave) ?>">
				</form>

				<td><a actionBtn="apagarCarrinho" href="<?php echo INCLUDE_PATH_PAINEL ?>editar-emprestimo?excluir=<?php echo htmlentities($chave); ?>" class="btn delete">Excluir <i class="fa fa-times"></i></a></td>

			</tr>
			<?php } ?>
		</table>
	</div>
	<div class="box-usuario">
		<?php
			echo "<p>Status atual do carrinho: " . Pedido::statusCarrinho() . "</p>";
		?>
	</div>
	<div class="box-operacoes">
		<a href="<?php echo INCLUDE_PATH_PAINEL ?>solicitar-emprestimo?limpar" class="operacao">Limpar carrinho <i class="fa fa-times"></i></a>

		<a href="<?php echo INCLUDE_PATH_PAINEL ?>solicitar-emprestimo?concluir" class="operacao">Concluir pedido <i class="fa fa-thumbs-up"></i></a>

		<a href="<?php echo INCLUDE_PATH_PAINEL ?>solicitar-emprestimo?editar" class="operacao">Editar/ver carrinho <i class="fa fa-pencil-alt"></i></a>
	</div>
</div>