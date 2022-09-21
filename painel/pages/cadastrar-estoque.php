<div class="box-content">
	<h2> <i class="fa fa-pencil-alt"></i>Adicionar Equipamentos</h2>

	<form method="post">
		<?php 
			if(isset($_POST['acao'])) {
				if(Painel::insertEstoque($_POST)) {
					Painel::alert('sucesso', 'O cadastro foi realizado com sucesso');
				}

				else {
					Painel::alert('erro', 'Campos vazios não são permitidos');
				}
			}
		?>
		<div class="form-group">
			<label for="">Nome do Equipamento:</label>
			<input type="text" name="nome">
		</div>

		<div class="form-group">
			<label for="">Quantidade do Equipamento:</label>
			<input type="number" name="quantidade"></input>
		</div>
	
		<div class="form-group">
			<input type="submit" value="Cadastrar" name="acao">
		</div>
	</form>
</div>