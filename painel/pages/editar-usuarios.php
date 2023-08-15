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
					Usuario::validarEntradasAtualizarUsuarios($usuarios, $_POST);
				}
			}

			if(isset($_GET['bloquear-pedidos'])) {
				Usuario::bloquearPedidos($usuarios, 1);
			}

			if(isset($_GET['liberar-pedidos'])) {
				Usuario::bloquearPedidos($usuarios, 0);
			}

			if(isset($_GET['bloquear-login'])) {
				Usuario::bloquearLogin($usuarios, 0);
			}

			if(isset($_GET['liberar-login'])) {
				Usuario::bloquearLogin($usuarios, 1);
			}

			if(isset($_GET['confirmar-conta'])) {
				Usuario::reConfirmarConta($usuarios);
			}
		?>
		<div class="form-group">
			<label for="nome">Nome:</label>
			<input id="nome" type="text" name="nome" required="" value="<?php echo htmlentities($usuarios->getNome()); ?>" placeholder="Nome">
		</div>

		<div class="form-group">
			<label for="sobrenome">Sobrenome:</label>
			<input type="text" name="sobrenome" id="sobrenome" value="<?php echo htmlentities($usuarios->getSobrenome()) ?>" placeholder="Sobrenome">
		</div>

		<div class="form-group">
			<label for="email">E-mail:</label>
			<input type="email" id="email" name="email" value="<?php echo htmlentities($usuarios->getEmail()) ?>" placeholder="E-mail">
		</div>

		<?php  
			if($usuarios->getAcesso() == 1) {
		?>

		<div class="form-group">
			<label for="curso">Curso:</label>
			<select name="curso" id="curso">
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

		<div class="form-group">
			<label for="matricula">Matrícula:</label>
			<input id="matricula" type="text" name="matricula" maxlength="6" pattern="([0-9]{6})" id="matricula" value="<?php echo htmlentities($usuarios->getMatricula()) ?>">
		</div>
		<?php }?>

		<div class="form-group">
			<input type="submit" value="Atualizar" name="acao">
			<input type="hidden" name="id" value="<?php echo $id; ?>">
		</div>
	</form>

	<div class="box-operacoes">
		
		<!-- Bloqueio de pedidos -->
		<?php if($usuarios->isBloqueado() == 0){?>
		<a href="<?php echo INCLUDE_PATH_PAINEL ?>editar-usuarios?bloquear-pedidos&id=<?php 
			echo htmlentities($usuarios->getId());
		?>" class="operacao">Bloquear pedidos <i class="fa fa-times"></i></a>
		<?php } ?>

		<?php if($usuarios->isBloqueado() == 1){?>
		<a href="<?php echo INCLUDE_PATH_PAINEL ?>editar-usuarios?liberar-pedidos&id=<?php 
			echo htmlentities($usuarios->getId());
		?>" class="operacao">Liberar pedidos <i class="fa fa-thumbs-up"></i></a>
		<?php } ?>
		
		<!-- Bloqueio de login -->
		<?php if($usuarios->isAtivada() == 0){?>
		<a href="<?php echo INCLUDE_PATH_PAINEL ?>editar-usuarios?confirmar-conta&id=<?php 
			echo htmlentities($usuarios->getId());
		?>" class="operacao">Reenviar link de confirmação <i class="fa fa-mail-bulk"></i></a>
		<?php } ?>

		<?php if($usuarios->isAtivada() == 1){?>
		<a href="<?php echo INCLUDE_PATH_PAINEL ?>editar-usuarios?bloquear-login&id=<?php 
			echo htmlentities($usuarios->getId());
		?>" class="operacao">Bloquear login <i class="fa fa-ban"></i></a>
		<?php } ?>

		<?php if($usuarios->isAtivada() == 0){?>
		<a href="<?php echo INCLUDE_PATH_PAINEL ?>editar-usuarios?liberar-login&id=<?php 
			echo htmlentities($usuarios->getId());
		?>" class="operacao">Liberar login <i class="fa fa-door-open"></i></a>
		<?php } ?>
	</div>
</div>