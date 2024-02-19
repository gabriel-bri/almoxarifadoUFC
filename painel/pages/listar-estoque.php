<?php  
	verificaPermissaoPagina(2);
?>

<?php
	if(isset($_GET['pagina']) && (int)$_GET['pagina'] && $_GET['pagina'] > 0) {
		$paginaAtual = filter_var($_GET['pagina'], FILTER_SANITIZE_NUMBER_INT);
	}

	else {
		$paginaAtual = 1;
	}

	$porPagina = 10;
	$estoque = Estoque::selectAll(($paginaAtual - 1) * $porPagina, $porPagina);

	if($estoque == false && $paginaAtual != 1) {
		Painel::redirect(INCLUDE_PATH_PAINEL . 'listar-estoque');
	}
?>
<div class="box-content">
	<h2> <i class="fas fa-sync-alt"></i> Atualizar Estoque</h2>

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

            if(!empty(Estoque::returndata($data, $filtro))){
				if($filtro == "nome") {
					$estoque = Estoque::returndata($data, $filtro);
				}
            }
		}
	?>
	<div class="wraper-table">
		<table>
			<tr>
				<td>Nome</td>
				<td>Quantidade</td>
				<td>Tipo</td>
				<td>#</td>
				<td>#</td>
				<td>#</td>
			</tr>
			<?php
			if($estoque != false){
				for($i = 0; $i < count($estoque); $i++) {
			?>

			<tr>
				<td><?php echo htmlentities($estoque[$i]->getNome()); ?></td>

				<td><?php echo htmlentities($estoque[$i]->getQuantidade()); ?></td>

				<td><?php echo tipoEstoque(htmlentities($estoque[$i]->getTipo())); ?></td>
				
				<td><a href="<?php echo INCLUDE_PATH_PAINEL?>editar-estoque?id=<?php echo htmlentities($estoque[$i]->getId()); ?>" class="btn edit">Editar <i class="fa fa-pencil-alt"></i></a></td>
				
				<td><a class="btn order" href="<?php echo INCLUDE_PATH_PAINEL?>listar-depoimentos?order=up&id=<?php echo htmlentities($estoque[$i]->getId()); ?>"><i class="fa fa-angle-up"></i></a></td>
				
				<td><a class="btn order" href="<?php echo INCLUDE_PATH_PAINEL?>listar-depoimentos?order=down&id=<?php echo htmlentities($estoque[$i]->getId()); ?>"><i class="fa fa-angle-down"></i></a></td>
			</tr>
			<?php }} ?>
		</table>
	</div>

	<div class="paginacao">
		<?php 
			$totalPaginas = ceil(count(Estoque::selectAll()) / $porPagina);

			for($i = 1; $i <= $totalPaginas; $i++) {
				if($i == $paginaAtual) {
					echo '<a href="' . INCLUDE_PATH_PAINEL . 'listar-estoque?pagina=' . $i . '" class="page-selected">' . $i . '</a>';
				}

				else {
					echo '<a href="' . INCLUDE_PATH_PAINEL . 'listar-estoque?pagina=' . $i . '">' . $i . '</a>';
				}
			}
		?>
	</div>
</div>	