<?php 
	if(isset($_GET['id']) && (int)$_GET['id'] && $_GET['id'] > 0) {
		$id = (int)$_GET['id'];
		$estoque = Estoque::select('id = ?', array($id));

		if($estoque != true) {
			Painel::alert("erro", "ID não encontrado");
			die();			
		}
	}

	else {
		Painel::alert("erro", "Você precisa passar um ID");
		die();
	}
?>
<div class="box-content">
	<h2> <i class="fa fa-pencil-alt"></i>Editar Estoque</h2>

	<form method="post">
		<?php 
			if(isset($_POST['acao'])) {
				if($_POST["nome"] == "" || $_POST["quantidade"] == "" || $_POST['tipo'] == ""){
					Painel::alert('erro', 'Campos vazios não são permitidos');
				}

				else if($_POST["quantidade"] < 1 ) {
					Painel::alert('erro', 'Quantidade inválida');
				}

				else{
					if(Estoque::update($_POST)){
						Painel::alert('sucesso', 'O estoque foi atualizado com sucesso');
						$estoque = Estoque::select('id = ?', array($id));
					}
				}
			}
		?>
		<div class="form-group">
			<label for="">Nome do equipamento:</label>
			<input type="text" name="nome" value="<?php echo htmlentities($estoque['nome']) ?>">
		</div>

		<div class="form-group">
			<label for="">Tipo:</label>
			<select name="tipo">
				<?php 
					foreach (Estoque::$estoque as $key => $value) {
						// echo "$key | $value <br>";
						if ($key == $estoque['tipo']) {
				?>

						<option value="<?php echo htmlentities($estoque['tipo'])?>" selected=""><?php echo tipoEstoque(htmlentities($estoque['tipo'])); ?></option>

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
			<label for="">Quantidade:</label>
			<input type="number" name="quantidade" value="<?php echo htmlentities($estoque['quantidade']) ?>" min="1">
		</div>

		<div class="form-group">
			<input type="submit" value="Atualizar" name="acao">
			<input type="hidden" name="id" value="<?php echo $id; ?>">
		</div>
	</form>
</div>