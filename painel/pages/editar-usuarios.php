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

				else if($usuarios['acesso'] == 1 && ($_POST["nome"] == "" || $_POST["sobrenome"] == "" || $_POST['email'] == "" || $_POST['matricula'] == ""|| $_POST['curso'] == "")) {
					Painel::alert('erro', 'Campos vazios não são permitidos');
				}

				else {
					$nome = filter_var($_POST['nome'], FILTER_SANITIZE_STRING);
					$sobrenome = filter_var($_POST['sobrenome'], FILTER_SANITIZE_STRING);
					$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
					$id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);

					$matricula = "";
					$curso = "";
					$dominio = explode("@", $email);

					if($usuarios['acesso'] == 1) {
						$matricula = filter_var($_POST['matricula'], FILTER_SANITIZE_NUMBER_INT);
						$curso = filter_var($_POST['curso'], FILTER_SANITIZE_STRING);
					}

					if($usuarios['acesso'] == 1 && $dominio[1] != "alu.ufc.br"){
						Painel::alert('erro', 'Alunos podem usar apenas e-mail @alu.ufc.br');
					}

					else if(filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
						Painel::alert('erro', 'E-mai inválido, tente novamente.');
					}
					
					else if($usuarios['acesso'] == 1 && (strlen($matricula) > 6 || strlen($matricula) < 6)) {
						Painel::alert('erro', 'A matrícula deve ter 6 dígitos');
					}
					
					else if(Usuario::emailJaCadastrado($email) && $usuarios['email'] != $email){
						Painel::alert('erro', 'E-mail já cadastrado.');
					}

					else{
						if(Usuario::atualizarUsuarios($nome, $sobrenome, $email, $matricula, $curso, $id)){
							Painel::alert('sucesso', 'O usuário foi atualizado com sucesso');
							$usuarios = Usuario::select('id = ?', array($id));
						}
					}
				}
			}
		?>
		<div class="form-group">
			<label for="">Nome:</label>
			<input type="text" name="nome" required="" value="<?php echo htmlentities($usuarios['nome']); ?>">
		</div>

		<div class="form-group">
			<label for="">Sobrenome:</label>
			<input type="text" name="sobrenome" value="<?php echo htmlentities($usuarios['sobrenome']) ?>">
		</div>

		<div class="form-group">
			<label for="">E-mail:</label>
			<input type="email" name="email" value="<?php echo htmlentities($usuarios['email']) ?>">
		</div>

		<?php  
			if($usuarios['acesso'] == 1) {
		?>

		<div class="form-group">
			<label for="">Curso:</label>
			<select name="curso">
				<?php 
					foreach (Painel::$cursos as $key => $value) {
						// echo "$key | $value <br>";
						if ($key == $usuarios['curso']) {
				?>

						<option value="<?php echo htmlentities($usuarios['curso'])?>" selected=""><?php echo qualCurso(htmlentities($usuarios['curso'])); ?></option>

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
			<input type="text" name="matricula" maxlength="6" pattern="([0-9]{6})" id="matricula" value="<?php echo htmlentities($usuarios['matricula']) ?>">
		</div>
		<?php }?>

		<div class="form-group">
			<input type="submit" value="Atualizar" name="acao">
			<input type="hidden" name="id" value="<?php echo $id; ?>">
		</div>
	</form>
</div>