<?php  
	verificaPermissaoPagina(2);
?>
<script src="<?php echo INCLUDE_PATH_PAINEL ?>js/auto-complete.js"></script>

<div class="box-content">
	<h2> <i class="fas fa-plus-circle"></i> Adicionar Estoque</h2>
	<form method="post">
		<?php 
			if(isset($_POST['acao'])) {
				Estoque::validarEntradasCadastro($_POST);
			}
		?>
		<div class="form-group">
			<label for="nome">Nome do Item:</label>
			<input type="text" name="nome" placeholder="Nome do componente/equipamento" id="nome" value="<?php echo isset($_POST['nome']) ? htmlentities($_POST['nome']) : ''; ?>" onkeyup="javascript:load_data(this.value)">
		</div>


		<div class="form-group">
			<label for="quantidade">Quantidade:</label>
			<input type="number" name="quantidade" min="1" id="quantidade" placeholder="Quantidade" value="<?php echo isset($_POST['quantidade']) ? htmlentities($_POST['quantidade']) : '1'; ?>"></input>
		</div>

		<div class="form-group">
			<label for="tipo">Tipo:</label>
			<select name="tipo" id="tipo">
				<?php 
					foreach (Estoque::$estoque as $key => $value) {
						$selected = ($key == $_POST['tipo']) ? 'selected' : ''; // Verifica se é o valor enviado pelo formulário
						echo "<option value='$key' $selected>$value</option>";
					}
				?>
			</select>
		</div>

		<div class="form-group">
			<input type="submit" value="Cadastrar" name="acao">
		</div>
	</form>

	<span id="search_result">
	<!-- <div class="list-group"><ul><li class="list-group-item list-group-item-action" "="">* <b>COMPONENTE 1</b>2 <br><br><a href="">Visualizar item <i class="fa fa-eye"></i></a></li></ul><p>Itens marcados com * já foram cadastrados.</p></div> -->
	</span>
</div>