<div class="box-content">
	<h2> <i class="fa fa-pencil-alt"></i>Atualizar Estoque</h2>

	<form method="post">
		<?php 
			if(isset($_POST['acao'])) {
				if(Painel::insert($_POST)) {
					Painel::alert('sucesso', 'O cadastro do serviço foi realizado com sucesso');
				}

				else {
					Painel::alert('erro', 'Campos vazios não são permitidos');
				}
			}
		?>

		<div class="form-group">
			<label for="">Descreva o serviço:</label>
			<textarea name="serviço"></textarea>
		</div>

		<div class="form-group">
			<input type="submit" value="Cadastrar" name="acao">
			<input type="hidden" value="0" name="order_id">
			<input type="hidden" value="tb_site.servicos" name="nome_tabela">
		</div>
	</form>
</div>