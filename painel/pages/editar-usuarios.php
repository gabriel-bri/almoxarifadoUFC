<?php  
	verificaPermissaoPagina(2);
?>
<?php 
	// Verifica se o ID está presente na URL e é um número inteiro positivo
	if(!isset($_GET['id']) || !(int)$_GET['id'] || $_GET['id'] <= 0) {
		Painel::alert("erro", "Você precisa passar um ID");
		return;
	}

	// Obtém o ID da URL
	$id = (int)$_GET['id'];

	// Seleciona o usuário com o ID especificado
	$usuarios = Usuario::select('id = ?', array($id));

	// Se o usuário não for encontrado, exibe uma mensagem de erro e encerra o script
	if($usuarios != true) {
		Painel::alert("erro", "ID não encontrado");
		return;
	}

	// Se o usuário for um bolsista e o usuário logado também for um bolsista, 
	// exibe uma mensagem de erro e encerra o script
	if($usuarios->getAcesso() == 2 && $_SESSION['acesso'] == 2) {
		Painel::alert("erro", "Você não pode editar os dados de outro bolsista, consulte o administrador.");
		return;
	}

	// Se o usuário for um administrador e o usuário logado for um bolsista, 
	// exibe uma mensagem de erro e encerra o script
	if($usuarios->getAcesso() == 3 && $_SESSION['acesso'] == 2) {
		Painel::alert("erro", "Você não tem permissão para alterar os dados do administrador.");
		return;
	}
?>

<div class="box-content">
	<h2> <i class="fa fa-pencil-alt"></i>Editar Usuário</h2>

	<form method="post">
		<?php 
			if(isset($_POST['acao'])) {
				// Valida e atualiza os usuários
				Usuario::validarEntradasAtualizarUsuarios($usuarios, $_POST);
			}

			// Bloquear pedidos se o usuário logado for um administrador 
			// e a solicitação para bloquear pedidos existir
			if ($_SESSION['acesso'] == 3 && isset($_GET['bloquear-pedidos'])) {
				Usuario::bloquearPedidos($usuarios, 1);
			}

			// Liberar pedidos se o usuário logado for um administrador
			// e a solicitação para liberar pedidos existir
			if ($_SESSION['acesso'] == 3 && isset($_GET['liberar-pedidos'])) {
				Usuario::bloquearPedidos($usuarios, 0);
			}

			// Bloquear login se o usuário logado for um administrador 
			// e a solicitação para bloquear login existir
			if ($_SESSION['acesso'] == 3 && isset($_GET['bloquear-login'])) {
				Usuario::bloquearLogin($usuarios, 0);
			}

			// Liberar login se o usuário logado for um administrador 
			// e a solicitação para liberar login existir
			if ($_SESSION['acesso'] == 3 && isset($_GET['liberar-login'])) {
				Usuario::bloquearLogin($usuarios, 1);
			}

			// Reconfirmar conta se o usuário logado for um administrador e a solicitação para confirmar a conta existir
			if ($_SESSION['acesso'] == 3 && isset($_GET['confirmar-conta'])) {
				Usuario::reConfirmarConta($usuarios);
			}

		?>
		
		<div class="form-group">
			<p>
				Status da conta: <b><?php echo statusConta($usuarios->isAtivada());?></b>
			</p>

			<?php 
				if($usuarios->getAcesso() == 2 || $usuarios->getAcesso() == 1) {
			?>
				<p>
					Status de pedidos: <b><?php echo emprestimoBloqueado($usuarios->isBloqueado());?></b>
				</p>
			<?php 
				}
			?>
		</div>
		
		<div class="form-group">
			<label for="nome">Nome:</label>
			<input id="nome" type="text" name="nome" required="" value="<?php echo htmlentities($usuarios->getNome()); ?>" placeholder="Nome">
		</div>

		<div class="form-group">
			<label for="sobrenome">Sobrenome:</label>
			<input type="text" name="sobrenome" id="sobrenome" value="<?php echo htmlentities($usuarios->getSobrenome()) ?>" placeholder="Sobrenome" required="">
		</div>

		<div class="form-group">
			<label for="email">E-mail:</label>
			<input type="email" id="email" name="email" value="<?php echo htmlentities($usuarios->getEmail()) ?>" placeholder="E-mail" required="">
		</div>

		<?php  
			if($_SESSION['acesso'] == 3) {
		?>
		<div class="form-group">
			<label for="acesso">Cargo:</label>
			<select name="acesso" id="acesso">
				<?php 
					foreach (Usuario::$acessos as $key => $value) {
						if ($key == $usuarios->getAcesso()) {
				?>
						<option value="<?php echo htmlentities($usuarios->getAcesso())?>" selected=""><?php echo pegaCargo(htmlentities($usuarios->getAcesso())); ?></option>
				<?php
						}

						else {
							echo "<option value='$key'>$value</option>";
						}
					}
				?>
			</select>
		</div>
		<?php }?>

		<?php  
			if($usuarios->getAcesso() == 1 || $usuarios->getAcesso() == 2) {
		?>

		<div class="form-group">
			<label for="curso">Curso:</label>
			<select name="curso" id="curso">
				<?php 
					foreach (Usuario::$cursos as $key => $value) {
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
		<?php }?>
		
		<?php if($usuarios->getAcesso() == 1 || $usuarios->getAcesso() == 2) {?>
		<div class="form-group">
			<label for="matricula">Matrícula:</label>
			<input id="matricula" type="text" name="matricula" maxlength="6" pattern="([0-9]{6})" id="matricula" value="<?php echo htmlentities($usuarios->getMatricula()) ?>">
		</div>
		<?php }?>

		<div class="form-group">
			<input type="submit" value="Atualizar" name="acao">
			<input type="hidden" name="id" value="<?php echo htmlentities($usuarios->getId()); ?>">
		</div>
	</form>

	<div class="box-operacoes">
	<?php if($_SESSION['acesso'] == 3){?>
		<!-- Bloqueio de pedidos -->
		<?php if($usuarios->isBloqueado() == 0 && ($usuarios->getAcesso() == 2 || $usuarios->getAcesso() == 1)){?>
		<a href="<?php echo INCLUDE_PATH_PAINEL ?>editar-usuarios?bloquear-pedidos&id=<?php 
			echo htmlentities($usuarios->getId());
		?>" class="operacao">Bloquear pedidos <i class="fa fa-times"></i></a>
		<?php } ?>

		<?php if($usuarios->isBloqueado() == 1 && ($usuarios->getAcesso() == 2 || $usuarios->getAcesso() == 1)){?>
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
	<?php }?>
</div>