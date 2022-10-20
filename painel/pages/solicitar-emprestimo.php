<?php  
	verificaPermissaoPagina(1);
?>

<?php
	$secret = "teste";
?>

<script type="text/javascript">
	$(document).ready(function(){
        $(".valor").on("input", function(){
            var textoDigitado = $(this).val();
            var inputCusto = $(this).attr("custo");
            $("#"+ inputCusto).val(textoDigitado);
        });
    });
</script>
<?php
	$estoque = Estoque::selectAll();
	if(isset($_GET['id'])) {
		$id = $_GET['id'];
	}
?>

<div class="box-content">
	<h2> <i class="fa fa-shopping-cart"></i>Solicitar Empréstimo</h2>
	
	<div class="wraper-table">
		<table>
			<tr>
				<td>Nome</td>
				<td>Estoque</td>
				<td>Tipo</td>
				<td>#</td>
			</tr>
			<?php
				foreach ($estoque as $key => $value) {
			?>

			<tr>
				<td><?php echo htmlentities($value['nome']); ?></td>

				<td><?php echo htmlentities($value['quantidade']); ?></td>

				<td><?php echo tipoEstoque(htmlentities($value['tipo'])); ?></td>

				<td><a href="<?php echo INCLUDE_PATH_PAINEL?>adicionar-carrinho?id=<?php echo htmlentities($value['id']); ?>" class="btn edit">Adicionar ao pedido <i class="fa fa-pencil-alt"></i></a></td>
			</tr>
			<?php } ?>
		</table>
	</div>

	<div class="box-usuario">
		<p>Status atual do carrinho: VAZIO</p>
	</div>
</div>