<?php  
	verificaPermissaoPagina(2);
?>
<script>
    $(document).ready(function() {
      // Ao alterar o valor do radio
      $('input[type="radio"]').change(function() {
        var radioValue = $(this).val();

        // Verifica o valor do radio e muda o tipo do input correspondente
        if (radioValue === 'nome') {
		  $('#campo').val('');
		  $('#campo').attr('placeholder', 'Ex: Fulano');
          $('#campo').attr('type', 'text');
		  $('#campo').removeAttr('maxlength');
		  $('#campo').removeAttr('pattern');
		  $('#campo').off();
        }

		else if (radioValue === 'professor') {
		  $('#campo').val('');
		  $('#campo').attr('placeholder', 'Ex: Girafales');
          $('#campo').attr('type', 'text');
		  $('#campo').removeAttr('maxlength');
		  $('#campo').removeAttr('pattern');
		  $('#campo').off();
        }

		else if (radioValue === 'email') {
		  $('#campo').val('');
		  $('#campo').attr('placeholder', 'Ex: fulano@alu.ufc.br');
          $('#campo').attr('type', 'email');
		  $('#campo').removeAttr('maxlength');
		  $('#campo').removeAttr('pattern');
		  $('#campo').off();
        }
		
		else if (radioValue === 'data') {
		  $('#campo').val('');
          $('#campo').attr('type', 'date');
		  $('#campo').removeAttr('maxlength');
		  $('#campo').removeAttr('pattern');
		  $('#campo').val(getFormattedDate());
		  $('#campo').off();
        }
		
		else if (radioValue === 'matricula') {
		  $('#campo').val('');
		  $('#campo').attr('placeholder', 'Ex: 000000');
          $('#campo').attr('type', 'text');
		  $('#campo').attr('maxlength', '6');
		  $('#campo').attr('pattern', '([0-9]{6})');
		  $('#campo').on('input', function() {
			this.value = this.value.replace(/[^0-9]/g, ''); // Remove caracteres não numéricos
            this.value = this.value.slice(0, 6); // Limita o valor a 6 dígitos
          });
        }
      });
	        // Função para obter a data de hoje no formato "YYYY-MM-DD"
	function getFormattedDate() {
        var today = new Date();
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0');
        var yyyy = today.getFullYear();
        return yyyy + '-' + mm + '-' + dd;
      }
    });
</script>

<div class="box-content">
	<h2> <i class="fas fa-spinner"></i> Empréstimos Não Finalizados</h2>
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

				<input type="radio" id="opcao-professor" name="opcao" value="professor">
				<label for="opcao-professor">Professor</label>

				<input type="radio" id="opcao-data" name="opcao" value="data">
				<label for="opcao-data">Data</label>
			</div>
			<input type="submit" name="buscar" value="Buscar">			
		</div>
	</form>
	<div class="wraper-table">
		<table>
			<tr>
				<td>Nome</td>
				<td>Sobrenome</td>
				<td>Matrícula</td>
				<td>Código</td>
				<td>Data</td>
				<td>#</td>
			</tr>
			<?php
				$pedidosNaoFinalizados = Pedido::retornaPedidosNaoFinalizados();
				while($dadosPedidos = $pedidosNaoFinalizados->fetch(PDO::FETCH_ASSOC)) {
					
			?>

				<tr>
					<td><?php echo htmlentities($dadosPedidos['nome']); ?></td>

					<td><?php echo htmlentities($dadosPedidos['sobrenome']); ?></td>

					<td><?php echo htmlentities($dadosPedidos['matricula']); ?></td>

					<td><?php echo htmlentities($dadosPedidos['codigo_pedido']); ?></td>

					<td>
                        <?php
                            $dataConvertida = htmlentities($dadosPedidos['data_pedido']);
                            $dataConvertida = implode("/",array_reverse(explode("-",$dataConvertida)));
                            echo $dataConvertida; 
                        ?>
                    </td>
					
					<td><a href="<?php echo INCLUDE_PATH_PAINEL?>fechar-pedido?codigo_pedido=<?php echo htmlentities($dadosPedidos['codigo_pedido']); ?>" class="btn edit">Concluir pedido <i class="fa fa-thumbs-up"></i></a></td>
				</tr>
			<?php } ?>
		</table>
	</div>
</div>