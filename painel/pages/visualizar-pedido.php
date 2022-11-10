<?php  
	verificaPermissaoPagina(2);
?>
<div class="box-content">
	<h2> <i class="fa fa-pencil-alt"></i>Detalhes do pedido: <?php echo $_GET['codigo_pedido'] ?></h2>
    <?php
        $teste = Pedido::retornaPedidoPeloCodigo($_GET['codigo_pedido']);
        while($dadosPedidos = $teste->fetch(PDO::FETCH_ASSOC)) {
            echo "<pre>";
            var_dump($dadosPedidos);
            echo "</pre>";
        }
    ?>
	<form method="post">
		<?php 

		?>
		<div class="form-group">
			<label for="">Nome do usuário:</label>
            <p>Porra</p>
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