<?php  
	verificaPermissaoPagina(1);
?>

<?php
	if(!isset($_SESSION['secret'])) {
		$_SESSION['secret']  = bin2hex(random_bytes(8));
	}

	$estoque = Estoque::selectAll();
?>

<div class="box-content">
	<h2> <i class="fa fa-shopping-cart"></i>Solicitar Empréstimo</h2>
	<form class="buscador">	
		<div class="form-group">
			<label for="">Não encontrou o que procura? Faz uma busca! <i class="fa fa-search"></i></label>
			<input type="text" name="busca" required="" placeholder="Ex: Arduino">
			<input type="submit" name="buscar" value="Buscar">			
		</div>
	</form>
	<?php 
		if(isset($_GET['buscar'])) {
			$data = filter_var($_GET["busca"], FILTER_SANITIZE_STRING);

            if(!empty(Estoque::returndata($data))){
				$estoque = Estoque::returndata($data);
            }
		}
	?>

	<?php
		if(isset($_POST['adicionar'])) {
			if(!isset($_POST['id_produto']) || !isset($_POST['qtd_' . $_SESSION['secret'] . "_" . $_POST['id_produto']])) {
				Painel::alert("erro", "Algum parâmetro está ausente.");
			}

			else {
				$idProduto = (int) filter_var($_POST['id_produto'], FILTER_SANITIZE_NUMBER_INT);
				$qtdProduto = filter_var($_POST['qtd_' . $_SESSION['secret'] . "_" . $idProduto], FILTER_SANITIZE_STRING);

				if($qtdProduto <= 0) {
					Painel::alert("erro", "Quantidade deve ser igual ou maior que 1");
				}
				
				else {		
					if(!isset($_SESSION['carrinho'])) {
						$_SESSION['carrinho'] = array();
					}

					$quantidadeDisponivel = Estoque::estoqueDisponivel(htmlentities($idProduto));

					if(Pedido::jaNoCarrinho($idProduto)) {
						Painel::alert("erro", "O item já foi adicionado ao seu carrinho.");
					}

					else if(is_null($quantidadeDisponivel[0])) {
						$limitePedido = Estoque::retornaQuantidade(htmlentities($idProduto));

						if($qtdProduto > $limitePedido['quantidade']) {
							Painel::alert("erro", "A quantidade está acima da disponível em nosso estoque.");		
						}

						else {
							$pedido = array("id" => $idProduto, "quantidade" => $qtdProduto);
							array_push($_SESSION['carrinho'], $pedido);
							Painel::alert("sucesso", "O item foi dicionado ao seu carrinho.");
						}

					}

					else if($qtdProduto > $quantidadeDisponivel[0]) {
						Painel::alert("erro", "A quantidade está acima da disponível em nosso estoque.");
					}
					
					else {
						$pedido = array("id" => $idProduto, "quantidade" => $qtdProduto);
						array_push($_SESSION['carrinho'], $pedido);
						Painel::alert("sucesso", "O item foi dicionado ao seu carrinho.");
					}	
				}
			}

		}

		if(isset($_GET['limpar'])) {
			unset($_SESSION['carrinho']);
			$_SESSION['secret']  = bin2hex(random_bytes(8));
			echo "<script>window.history.pushState('solicitar-emprestimo', 'Title', 'solicitar-emprestimo');</script>";
			Painel::alert("sucesso", "O seu carrinho foi limpo.");	
		}

		if(isset($_GET['concluir'])) {
			if(isset($_SESSION['carrinho']) AND count($_SESSION['carrinho']) > 0) {
				$codigo = random_bytes(10);
				foreach ($_SESSION['carrinho'] as $chave => $row){
		 			$idProduto =  $row['id'];
		 			$quantidade = $row['quantidade'];
		 			Pedido::cadastrarPedido($quantidade, $_SESSION['id'], $idProduto, bin2hex($codigo));
	 			}
	 			echo "<script>window.history.pushState('solicitar-emprestimo', 'Title', 'solicitar-emprestimo');</script>";
	 			Painel::alert("sucesso", "O seu pedido foi realizado e você recebeu por e-mail uma notificação.");
				unset($_SESSION['carrinho']);
				$_SESSION['secret']  = bin2hex(random_bytes(8));
			}

			else {
				echo "<script>window.history.pushState('solicitar-emprestimo', 'Title', 'solicitar-emprestimo');</script>";
				Painel::alert("erro", "O seu carrinho está vazio.");
			}
		}
	?>

	<div class="wraper-table">
		<table>
			<tr>
				<td>Nome</td>
				<td>Disponível</td>
				<td>Tipo</td>
				<td>Quantidade</td>
				<td>#</td>
			</tr>
			<?php
				foreach ($estoque as $key => $value) {
				$quantidadeDisponivel = Estoque::estoqueDisponivel(htmlentities($value['id']));
			?>

			<tr>
				<td><?php echo htmlentities($value['nome']); ?></td>

				<td><?php
						//Caso o id do produto não esteja na tabela de pedidos mostra a sua quantidade original.
				 		if(is_null($quantidadeDisponivel[0])) {
				 			echo htmlentities($value['quantidade']);
				 		}

				 		else {
							echo htmlentities($quantidadeDisponivel[0]);
				 		}
				 	?></td>

				<td><?php echo tipoEstoque(htmlentities($value['tipo'])); ?></td>
				<form method="post">

					<td><input type="number" name="<?php echo "qtd_" . $_SESSION['secret'] . "_" . htmlentities($value['id']) ?>"></td>

					<input type="hidden" name="id_produto" value="<?php echo htmlentities($value['id']) ?>">

					<td><input type="submit" name="adicionar" value="Adicionar" class="cart"></td>
				</form>
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

		<a href="<?php echo INCLUDE_PATH_PAINEL ?>editar-emprestimo" class="operacao">Editar/ver carrinho <i class="fa fa-pencil-alt"></i></a>
	</div>
</div>