<?php  
	verificaPermissaoPagina(2);
?>

<?php
	if(isset($_GET['excluir'])) {
		if(isset($_GET['excluir']) && (int)$_GET['excluir'] && $_GET['excluir'] > 0 && $_GET['excluir'] != $_SESSION['id']){
			$idExcluir = intval($_GET['excluir']);
			Usuario::deletar($idExcluir);
			Painel::redirect(INCLUDE_PATH_PAINEL . 'listar-usuarios');
		}
	}
?>

<?php
	if(isset($_GET['pagina']) && (int)$_GET['pagina'] && $_GET['pagina'] > 0) {
		$paginaAtual = filter_var($_GET['pagina'], FILTER_SANITIZE_NUMBER_INT);
	}

	else {
		$paginaAtual = 1;
	}

	$porPagina = 10;
	$usuarios = Usuario::selectAll(($paginaAtual - 1) * $porPagina, $porPagina);

	if($usuarios == false) {
		if($paginaAtual == 1){
			Painel::alert("erro", "Não há dados para serem exibidos");
			die();
		}

		else {		
			Painel::redirect(INCLUDE_PATH_PAINEL . 'listar-usuarios');
		}
	}
?>
<div class="box-content">
	<h2><i class="fas fa-table"></i> Editar Usuários</h2>
	<form class="buscador">	
		<div class="form-group">
			<label for="campo">Não encontrou o que procura? Faz uma busca! <i class="fa fa-search"></i></label>
			<input type="text" name="busca" required="" placeholder="Ex: Fulano" id="campo">
			<div class="filtro">
				<input type="radio" id="opcao-nome" name="opcao" value="nome">
				<label for="opcao-nome">Nome</label>

				<input type="radio" id="opcao-matricula" name="opcao" value="matricula">
				<label for="opcao-matricula">Matrícula</label>

				<input type="radio" id="opcao-email" name="opcao" value="email">
				<label for="opcao-email">E-mail</label>
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
					case 'matricula':
						$filtro = "matricula";
						break;
					
					case 'email':
						$filtro = "email";
						break;

					default:
						$filtro = "nome";
						break;
				}
			}

            if(!empty(Usuario::returndata($data, $filtro))){
                $usuarios = Usuario::returndata($data, $filtro);
            }
		}
	?>
	<div class="wraper-table">
		<table>
			<tr>
				<td>Nome</td>
				<td>Sobrenome</td>
				<td>Usuário</td>
				<td>Status</td>
				<td>#</td>
				<td>#</td>
				<td>#</td>
				<td>#</td>
			</tr>
			<?php
				for($i = 0; $i < count($usuarios); $i++) {
					
			?>

				<tr>
					<td><?php echo htmlentities($usuarios[$i]->getNome()); ?></td>

					<td><?php echo htmlentities($usuarios[$i]->getSobrenome()); ?></td>

					<td><?php echo htmlentities($usuarios[$i]->getUsuario()); ?></td>

					<td><?php echo pegaCargo(htmlentities($usuarios[$i]->getAcesso())); ?>
					
					<td><a href="<?php echo INCLUDE_PATH_PAINEL?>editar-usuarios?id=<?php echo htmlentities($usuarios[$i]->getId()); ?>" class="btn edit">Editar <i class="fa fa-pencil-alt"></i></a></td>

					<td><a actionBtn="delete" href="<?php echo INCLUDE_PATH_PAINEL ?>listar-usuarios?excluir=<?php echo htmlentities($usuarios[$i]->getId()); ?>" class="btn delete">Excluir <i class="fa fa-times"></i></a></td>

					<td><a class="btn order" href="<?php echo INCLUDE_PATH_PAINEL?>listar-depoimentos?order=up&id=<?php echo $usuarios[$i]->getId(); ?>"><i class="fa fa-angle-up"></i></a></td>
					
					<td><a class="btn order" href="<?php echo INCLUDE_PATH_PAINEL?>listar-depoimentos?order=down&id=<?php echo $usuarios[$i]->getId(); ?>"><i class="fa fa-angle-down"></i></a></td>
				</tr>
			<?php } ?>
		</table>
	</div>

	<div class="paginacao">
		<?php 
			$totalPaginas = ceil(count(Usuario::selectAll()) / $porPagina);

			for($i = 1; $i <= $totalPaginas; $i++) {
				if($i == $paginaAtual) {
					echo '<a href="' . INCLUDE_PATH_PAINEL . 'listar-usuarios?pagina=' . $i . '" class="page-selected">' . $i . '</a>';
				}

				else {
					echo '<a href="' . INCLUDE_PATH_PAINEL . 'listar-usuarios?pagina=' . $i . '">' . $i . '</a>';
				}


			}
		?>
	</div>
</div>