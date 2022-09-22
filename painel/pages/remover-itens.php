<?php  
	verificaPermissaoPagina(2);
?>

<div class="box-content">
	<h2> <i class="fa fa-pencil-alt"></i>Remover Itens</h2>

	<form method="post" enctype="multipart/form-data">
		<?php 
			if(isset($_POST['acao'])) {
				$nome = $_POST['nome'];
				$imagem = $_FILES['imagem'];
				
				if($nome == '') {
					Painel::alert('erro', 'O nome está vazio');
				}

				else {
					
					if(Painel::imagemValida($imagem) == false) {
						Painel::alert('erro', 'O formato especificado não é válido');
					}

					else {
						include('../classes/lib/WideImage.php');
						$imagem = Painel::uploadFile($imagem);
						WideImage::load('uploads/' . $imagem)->resize(100)->saveToFile('uploads/' . $imagem);
						$arr = ['nome' => $nome, 'slide' => $imagem, 'order_id' => '0', 'nome_tabela' => 'tb_site.slides'];
						Painel::insert($arr);
						Painel::alert("sucesso", "O slide foi cadastrado com sucesso");
					}
				}


			}
		?>

		<div class="form-group">
			<label for="">Nome:</label>
			<input type="text" name="nome">
		</div>
		

		<div class="form-group">
			<label for="">Imagem:</label>
			<input type="file" name="imagem">
		</div>

		<div class="form-group">
			<input type="submit" value="Cadastrar" name="acao">
		</div>
	</form>
</div>