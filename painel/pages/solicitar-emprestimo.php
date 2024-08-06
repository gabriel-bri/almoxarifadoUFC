<?php  
	verificaPermissaoPagina(1);

	if(isset($_SESSION['is_bloqueado'])) {
		Painel::alert("erro", "Você não está autorizado a fazer pedidos. Consulte o administrador para mais informações.");
		die();
	}

	if(PedidoDetalhes::maisde3PedidosPendentes($_SESSION['id'])) {
		Painel::alert("erro", "Você pode ter no máximo 3 pedidos pendentes de análise. Entre em contato com o administrador para mais informações.");
		die();
	}		
?>

<?php
	Carrinho::gerarSegredo();
?>

<?php
	if(isset($_GET['pagina']) && (int)$_GET['pagina'] && $_GET['pagina'] > 0) {
		$paginaAtual = filter_var($_GET['pagina'], FILTER_SANITIZE_NUMBER_INT);
	}

	else {
		$paginaAtual = 1;
	}

	$porPagina = 10;

	$estoque = Estoque::itensDisponiveis(($paginaAtual - 1) * $porPagina, $porPagina);
	
	if($estoque == false && $paginaAtual != 1) {
		Painel::redirect(INCLUDE_PATH_PAINEL . 'solicitar-emprestimo');
	}
?>

<div class="box-content">
	<h2> <i class="fa fa-shopping-cart"></i>Solicitar Empréstimo</h2>
	<form class="buscador">	
		<div class="form-group">
			<label for="campo">Não encontrou o que procura? Faz uma busca! <i class="fa fa-search"></i></label>
			<input type="text" name="busca" placeholder="Ex: Fulano" id="campo">
			<div class="filtro">
				<input type="radio" id="opcao-nome" name="opcao" value="nome">
				<label for="opcao-nome">Nome</label>

				<input type="radio" id="opcao-equipamento" name="opcao" value="1">
				<label for="opcao-equipamento">Equipamento</label>

				<input type="radio" id="opcao-componente" name="opcao" value="2">
				<label for="opcao-componente">Componente</label>
			</div>
			<input type="submit" name="buscar" value="Buscar">			
		</div>
	</form>

	<?php 
		if(isset($_GET['buscar'])) {
			$data = filter_var($_GET["busca"], FILTER_SANITIZE_STRING);
			$filtro = "nome" ;

			if(isset($_GET['opcao'])) {
				$filtro = filter_var($_GET["opcao"], FILTER_SANITIZE_STRING);
				
				switch ($filtro) {
					case '1':
					case '2':
						$estoque = Estoque::retornaPeloTipo($filtro);
						break;

					default:
						$filtro = "nome";
						break;
				}
			}

            if(!empty(Estoque::returnDataEmprestimo($data, $filtro))){
				if($filtro == "nome") {
					$estoque = Estoque::returnDataEmprestimo($data, $filtro);
				}
            }
		}
	?>

	<?php
		if(isset($_POST['adicionar'])) {
			Carrinho::validarCarrinho($_POST);
		}

		if(isset($_GET['limpar'])) {
			Carrinho::esvaziarCarrinho();
		}

		if(isset($_GET['concluir'])) {
			Carrinho::fecharCarrinho();
			$estoque = Estoque::itensDisponiveis(($paginaAtual - 1) * $porPagina, $porPagina);
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
			if($estoque != false) {
				for($i = 0; $i < count($estoque); $i++) {
			?>

			<tr>
				<td><?php echo htmlentities($estoque[$i]->getNome()); ?></td>

				<td><?php echo htmlentities($estoque[$i]->getQuantidade()); ?></td>

				<td><?php echo tipoEstoque(htmlentities($estoque[$i]->getTipo())); ?></td>
				<form method="post">

					<td><input type="number" name="<?php echo "qtd_" . $_SESSION['secret'] . "_" . htmlentities($estoque[$i]->getId()) ?>"></td>

					<input type="hidden" name="id_produto" value="<?php echo htmlentities($estoque[$i]->getId()) ?>">

					<td><input type="submit" name="adicionar" value="Adicionar" class="cart"></td>
				</form>
			</tr>
			<?php }} ?>
		</table>
	</div>

	<div class="box-usuario">
		<?php
			echo "<p>Status atual do carrinho: " . Carrinho::statusCarrinho() . "</p>";
		?>
	</div>

	<div class="box-operacoes">
		<a href="<?php echo INCLUDE_PATH_PAINEL ?>solicitar-emprestimo?limpar" class="operacao">Limpar carrinho <i class="fa fa-times"></i></a>

		<a href="<?php echo INCLUDE_PATH_PAINEL ?>solicitar-emprestimo?concluir" class="operacao">Concluir pedido <i class="fa fa-thumbs-up"></i></a>

		<a href="<?php echo INCLUDE_PATH_PAINEL ?>editar-emprestimo" class="operacao">Editar/ver carrinho <i class="fa fa-pencil-alt"></i></a>
	</div>

	<div class="paginacao">
		<?php 
			$totalPaginas = ceil(count(Estoque::itensDisponiveis()) / $porPagina);

			for($i = 1; $i <= $totalPaginas; $i++) {
				if($i == $paginaAtual) {
					echo '<a href="' . INCLUDE_PATH_PAINEL . 'solicitar-emprestimo?pagina=' . $i . '" class="page-selected">' . $i . '</a>';
				}

				else {
					echo '<a href="' . INCLUDE_PATH_PAINEL . 'solicitar-emprestimo?pagina=' . $i . '">' . $i . '</a>';
				}
			}
		?>
	</div>
</div>