<?php  
	verificaPermissaoPagina(2);
?>

<?php 
	// Verifica se o ID está presente na URL e é um número inteiro positivo
	if(!isset($_GET['id']) || !(int)$_GET['id'] || $_GET['id'] <= 0) {
		Painel::alert("erro", "Você precisa passar um ID");
		die();
	}

	// Obtém o ID da URL
	$id = (int)$_GET['id'];

	// Seleciona o estoque com o ID especificado
	$estoque = Estoque::select('id = ?', array($id));

	// Se o estoque não for encontrado, exibe uma mensagem de erro e encerra o script
	if($estoque != true) {
		Painel::alert("erro", "ID não encontrado");
		die();            
	}

	// if(Estoque::solicitadoPedido($id)) {
	//     Painel::alert("erro", "Produto com empréstimo pendente ou em andamento. Tente novamente mais tarde.");
	//     die();    
	// }
?>
<div class="box-content">
	<h2> <i class="fa fa-pencil-alt"></i> Editar Estoque</h2>

	<form method="post">
		<?php 
			// Verifica se o formulário de atualização foi submetido
			if(isset($_POST['acao'])) {
				// Valida as entradas e atualiza o estoque
				Estoque::validarEntradasAtualização($estoque, $_POST);
			}

			// Verifica se há uma solicitação para ativar um item
			if(isset($_GET['ativar-item'])) {
				// Ativa o item no estoque
				Estoque::desativarItem($estoque, 1);
			}

			// Verifica se há uma solicitação para desativar um item
			if(isset($_GET['desativar-item'])) {
				// Desativa o item no estoque
				Estoque::desativarItem($estoque, 0);
			}
		?>
		<div class="form-group">
			<p>
				Status do item: <b><?php echo statusItem($estoque->isAtivado());?></b>
			</p>
		</div>

		<div class="form-group">
			<label for="nome">Nome do equipamento:</label>
			<input type="text" name="nome" id="nome" value="<?php echo htmlentities($estoque->getNome()) ?>">
		</div>

		<div class="form-group">
			<label for="tipo">Tipo:</label>
			<select name="tipo" id="tipo">
				<?php 
					foreach (Estoque::$estoque as $key => $value) {
						if ($key == $estoque->getTipo()) {
				?>

						<option value="<?php echo htmlentities($estoque->getTipo())?>" selected=""><?php echo tipoEstoque(htmlentities($estoque->getTipo())); ?></option>

				<?php
						}

						else {
							echo "<option value='$key'>$value</option>";
						}
					}
				?>
			</select>
		</div>

		<div class="form-group">
			<label for="quantidade">Quantidade:</label>
			<input type="number" id="quantidade" name="quantidade" value="<?php echo htmlentities($estoque->getQuantidade()) ?>" min="1">
		</div>

		<div class="form-group">
			<input type="submit" value="Atualizar" name="acao">
			<input type="hidden" name="id" value="<?php echo htmlentities($estoque->getId()); ?>">
		</div>
	</form>

	<div class="box-operacoes">	
		<!-- Desativar/ativar item do estoque -->
		<?php if($estoque->isAtivado() == 0){?>
		<a href="<?php echo INCLUDE_PATH_PAINEL ?>editar-estoque?ativar-item&id=<?php 
			echo htmlentities($estoque->getId());
		?>" class="operacao" id="ativar">Ativar item <i class="fas fa-check"></i></i></a>
		<?php } ?>

		<?php if($estoque->isAtivado() == 1){?>
		<a href="<?php echo INCLUDE_PATH_PAINEL ?>editar-estoque?desativar-item&id=<?php 
			echo htmlentities($estoque->getId());
		?>" class="operacao" id="desativar">Desativar item <i class="fas fa-power-off"></i></a>
		<?php } ?>
	</div>
</div>