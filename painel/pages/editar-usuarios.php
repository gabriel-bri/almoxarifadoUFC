<?php  
	verificaPermissaoPagina(2);
?>
<?php 
	if(isset($_GET['id']) && (int)$_GET['id'] && $_GET['id'] > 0) {
		$id = (int)$_GET['id'];
		$usuarios = Usuario::select('id = ?', array($id));

		if($usuarios != true) {
			Painel::alert("erro", "ID não encontrado");
			die();			
		}
	}

	else {
		Painel::alert("erro", "Você precisa passar um ID");
		die();
	}
?>

<div class="box-content">
	<h2> <i class="fa fa-pencil-alt"></i>Editar Usuário</h2>

	<form method="post">
		<?php 
			if(isset($_POST['acao'])) {
				if($_POST["nome"] == "" || $_POST["sobrenome"] == "" || $_POST['email'] == ""){
					Painel::alert('erro', 'Campos vazios não são permitidos');
				}
				
				else if($usuarios->getAcesso() == 1 && ($_POST["nome"] == "" || $_POST["sobrenome"] == "" || $_POST['email'] == "" || $_POST['matricula'] == ""|| $_POST['curso'] == "")) {
					Painel::alert('erro', 'Campos vazios não são permitidos');
				}

				else {
					$nome = filter_var($_POST['nome'], FILTER_SANITIZE_STRING);
					$sobrenome = filter_var($_POST['sobrenome'], FILTER_SANITIZE_STRING);
					$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
					$id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);

					$matricula = "";
					$curso = "";

					Usuario::validarEntradasAtualizarUsuarios($usuarios, $nome, $sobrenome, $email, $matricula, $curso, $id);

					$usuarios = Usuario::select('id = ?', array($id));
				}
			}
		?>
		<div class="form-group">
			<label for="">Nome:</label>
			<input type="text" name="nome" required="" value="<?php echo htmlentities($usuarios->getNome()); ?>">
		</div>

		<div class="form-group">
			<label for="">Sobrenome:</label>
			<input type="text" name="sobrenome" value="<?php echo htmlentities($usuarios->getSobrenome()) ?>">
		</div>

		<div class="form-group">
			<label for="">E-mail:</label>
			<input type="email" name="email" value="<?php echo htmlentities($usuarios->getEmail()) ?>">
		</div>

		<?php  
			if($usuarios->getAcesso() == 1) {
		?>

		<div class="form-group">
			<label for="">Curso:</label>
			<select name="curso">
				<?php 
					foreach (Painel::$cursos as $key => $value) {
						// echo "$key | $value <br>";
						if ($key == $usuarios->getCurso()) {
				?>

						<option value="<?php echo htmlentities($usuarios->getCurso())?>" selected=""><?php echo qualCurso(htmlentities($usuarios->getCurso())); ?></option>

				<?php
						}

						else {
							echo "<option value='$key'>$value</option>";
						}
					}
				?>
			</select>
		</div>
		<script type="text/javascript">
			$(document).ready(function() {
	  			$("#matricula").keyup(function() {
	      		$("#matricula").val(this.value.match(/[0-9]*/));
	  			});
			});
		</script>

		<div class="form-group">
			<label for="">Matrícula:</label>
			<input type="text" name="matricula" maxlength="6" pattern="([0-9]{6})" id="matricula" value="<?php echo htmlentities($usuarios->getMatricula()) ?>">
		</div>
		<?php }?>

		<div class="form-group">
			<input type="submit" value="Atualizar" name="acao">
			<input type="hidden" name="id" value="<?php echo $id; ?>">
		</div>
	</form>
</div>