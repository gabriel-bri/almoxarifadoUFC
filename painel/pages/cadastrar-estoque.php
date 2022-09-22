<div class="box-content">
	<h2> <i class="fa fa-pencil-alt"></i>Adicionar Equipamentos</h2>

	<form method="post">
		<?php 
			if(isset($_POST['acao'])) {
				if($_POST["nome"] == "" || $_POST["quantidade"] == ""){
					Painel::alert('erro', 'Campos vazios não são permitidos');
				}

				else if($_POST["quantidade"] < 1 ) {
					Painel::alert('erro', 'Quantidade inválida');
				}
				
				else {
					Painel::insertEstoque($_POST);
					Painel::alert('sucesso', 'O cadastro foi realizado com sucesso');
				}
			}
		?>
		<div class="form-group">
			<label for="">Nome do Equipamento:</label>
			<input type="text" name="nome">
		</div>

		<div class="form-group">
			<label for="">Quantidade do Equipamento:</label>
			<input type="number" name="quantidade" min="1" value="1"></input>
		</div>
	
		<div class="form-group">
			<input type="submit" value="Cadastrar" name="acao">
		</div>
	</form>
</div>