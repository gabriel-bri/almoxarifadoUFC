<?php 
	if(isset($_GET['logout'])) {
		Painel::logout();
	}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="<?php echo INCLUDE_PATH_PAINEL;?>css/font-awesome.css">
	<title>Painel de controle</title>
	<link rel="stylesheet" href="<?php echo INCLUDE_PATH_PAINEL ?>css/style.css">
 	<link href="<?php echo ICONE_SITE ?>" rel="icon">
  	<link href="<?php echo ICONE_SITE ?>" rel="apple-touch-icon">
</head>
<body>

<div class="menu">
	<div class="menu-wrapper">
	<div class="box-usuario">
		<?php
		 	if($_SESSION['fotoperfil'] == '') { 
		?>
		<div class="avatar-usuario">
			<i class="fa fa-user"></i>
		</div>

		<?php } else {
		?>

		<div class="imagem-usuario">
			<img src="<?php echo INCLUDE_PATH_PAINEL; ?>uploads/<?php echo $_SESSION['fotoperfil']; ?>" alt="">
		</div>

		<?php } ?>

		<div class="nome-usuario">
			<p><?php echo $_SESSION['nome']; ?></p>
			<p><?php echo pegaCargo($_SESSION['acesso']); ?></p>
		</div>
	</div>
	<div class="items-menu">
		<!--Gerenciamento do estoque  -->
		<h2 <?php verificaPermissaoMenu(2) ?>>Gerenciar Estoque</h2>

		<a <?php selecionadoMenu('cadastrar-estoque') ?>
		<?php verificaPermissaoMenu(2) ?>
		href="<?php echo INCLUDE_PATH_PAINEL ?>cadastrar-estoque">Cadastrar Estoque</a>

		<a <?php selecionadoMenu('listar-estoque') ?>
		<?php verificaPermissaoMenu(2) ?>
		href="<?php echo INCLUDE_PATH_PAINEL?>listar-estoque">Listar Estoque</a>

		<!--Gestão dos empréstimos.  -->
		<h2 <?php verificaPermissaoMenu(2) ?>>Gestão dos Empréstimos</h2>

		<a <?php selecionadoMenu('novos-emprestimos') ?>
		<?php verificaPermissaoMenu(2) ?> 
		href="<?php echo INCLUDE_PATH_PAINEL?>novos-emprestimos">Novos Empréstimos</a>

		<a <?php selecionadoMenu('nao-finalizados') ?>
		<?php verificaPermissaoMenu(2) ?> 
		href="<?php echo INCLUDE_PATH_PAINEL?>nao-finalizados">Não Finalizados</a>

		<!-- Alunos -->

		<?php if($_SESSION['acesso'] == 1){?>
		<h2 <?php verificaPermissaoMenu(1) ?>>Pedidos</h2>

		<a <?php selecionadoMenu('solicitar-emprestimo') ?>
		<?php verificaPermissaoMenu(1) ?> 
		href="<?php echo INCLUDE_PATH_PAINEL?>solicitar-emprestimo">Novo Empréstimo</a>

		<a <?php selecionadoMenu('meus-pedidos') ?>
		<?php verificaPermissaoMenu(1) ?> 
		href="<?php echo INCLUDE_PATH_PAINEL?>meus-pedidos">Meus Pedidos</a>

		<a <?php selecionadoMenu('aguardando-aprovacao') ?>
		<?php verificaPermissaoMenu(1) ?> 
		href="<?php echo INCLUDE_PATH_PAINEL?>aguardando-aprovacao">Aguardando Aprovação</a>

		<?php } ?>
		
		<!-- Administração -->
		<h2>Administração do painel</h2>

		<a <?php selecionadoMenu('editar-usuario') ?>  href="<?php echo INCLUDE_PATH_PAINEL ?>editar-usuario">Editar Usuário</a>

		<a <?php selecionadoMenu('mudar-senha') ?>  href="<?php echo INCLUDE_PATH_PAINEL ?>mudar-senha">Alterar Senha</a>

		<a <?php selecionadoMenu('adicionar-usuarios') ?> <?php verificaPermissaoMenu(2) ?> href="<?php echo INCLUDE_PATH_PAINEL ?>adicionar-usuarios">Adicionar Usuários</a>

		<a <?php selecionadoMenu('listar-usuarios') ?> <?php verificaPermissaoMenu(2) ?> href="<?php echo INCLUDE_PATH_PAINEL ?>listar-usuarios">Listar Usuários</a>

		<h2 <?php verificaPermissaoMenu(2) ?>>Configuração geral</h2>
		<a <?php selecionadoMenu('editar-site') ?> href="">Editar Site</a>

	</div>
	</div>
</div>
<header>
	<div class="center">
		<div class="menu-btn">
			<i class="fa fa-bars"></i>
		</div>

		<div class="logout">
			<a <?php if(@$_GET['url'] == '' ) { ?> style="background: #60727a; padding: 15px;" <?php } ?> href="<?php  echo INCLUDE_PATH_PAINEL; ?>"><i class="fa fa-home"></i> <span>Página incial</span></a>
			<a href="<?php  echo INCLUDE_PATH_PAINEL; ?>?logout"><i class="fa fa-window-close"></i> <span>Sair</span></a>
		</div>
		<div class="clear"></div>
	</div>
</header>
<div class="content">
	<?php Painel::carregarPagina(); ?>
</div>
<script src="<?php echo INCLUDE_PATH_PAINEL ?>js/jquery.js"></script>
<script src="<?php echo INCLUDE_PATH_PAINEL?>js/jquery.mask.js"></script>
<script src="<?php echo INCLUDE_PATH_PAINEL ?>js/main.js"></script>
</body>
</html>