<?php 
	if(isset($_GET['id'])) {
		$id = (int)$_GET['id'];
		$slides = Painel::select('tb_site.slides', 'id = ?', array($id));
	}

	else {
		Painel::alert("erro", "Você precisa passar um ID");
		die();
	}
?>
<div class="box-content">
	<h2> <i class="fa fa-pencil-alt"></i>Editar Slide</h2>

	<form method="post" enctype="multipart/form-data">
		<?php 
			if(isset($_POST['acao'])) {
				$nome = $_POST['nome'];
				$imagem = $_FILES['imagem'];
				$imagem_atual = $_POST['imagem_atual'];

				if($imagem['name'] != '') {
					if(Painel::imagemValida($imagem)) {
						Painel::deleteFile($imagem_atual);
						$imagem = Painel::uploadFile($imagem);
						$arr = ['nome' => $nome, 'slide' => $imagem, 'id' => $id, 'nome_tabela' => 'tb_site.slides'];
						Painel::update($arr);
						$slides = Painel::select('tb_site.slides', 'id = ?', array($id));
						Painel::alert("sucesso", "O slide foi editado junto com a imagem");
					}

					else {
						Painel::alert("erro", "O formato da imagem não é válido");
					}
				}

				else {
					Painel::alert("sucesso", "Informções salvas com sucesso");
					$imagem = $imagem_atual;
					$arr = ['nome' => $nome, 'slide' => $imagem, 'id' => $id, 'nome_tabela' => 'tb_site.slides'];
					Painel::update($arr);
				}
			}
		?>
		<div class="form-group">
			<label for="">Nome:</label>
			<input type="text" name="nome" required="" value="<?php echo $slides['nome'] ?>">
		</div>


		<div class="form-group">
			<label for="">Imagem:</label>
			<input type="file" name="imagem">
			<input type="hidden" name="imagem_atual" value="<?php echo $slides['slide'] ?>">
		</div>

		<div class="form-group">
			<input type="submit" value="Atualizar" name="acao">
		</div>
	</form>
</div>