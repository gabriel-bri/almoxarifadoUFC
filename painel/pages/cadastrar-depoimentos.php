<div class="box-content">
	<h2> <i class="fa fa-pencil-alt"></i>Adicionar Depoimentos</h2>

	<form method="post">
		<?php 
			if(isset($_POST['acao'])) {
				if(Painel::insert($_POST)) {
					Painel::alert('sucesso', 'O cadastro foi realizado com sucesso');
				}

				else {
					Painel::alert('erro', 'Campos vazios não são permitidos');
				}
			}
		?>
		<div class="form-group">
			<label for="">Nome da pessoa:</label>
			<input type="text" name="nome">
		</div>

		<div class="form-group">
			<label for="">Depoimento:</label>
			<textarea name="depoimento"></textarea>
		</div>

		<div class="form-group">
			<label for="">Data:</label>
			<input formato="data" type="text" name="data">
		</div>
		

		<div class="form-group">
			<input type="submit" value="Cadastrar" name="acao">
			<input type="hidden" value="0" name="order_id">
			<input type="hidden" value="tb_site.depoimentos" name="nome_tabela">
		</div>
	</form>
</div>