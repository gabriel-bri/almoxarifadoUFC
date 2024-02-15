<div class="box-content w100">
		<h2><i class="fa fa-home"></i> Painel de controle - <?php echo NOME_EMPRESA; ?></h2>
		<?php if($_SESSION['acesso'] == 1): ?>

			<h2 style="text-align: center; margin-top: 2%;">Olá, seja bem vindo(a), <?php echo $_SESSION['nome'];?></h2>
		<?php endif ?>
		<?php if ($_SESSION['acesso'] == 2 || $_SESSION['acesso'] == 3): ?>
		<div class="box-metricas">
			<div class="box-metrica-single">
				<a href="<?php echo INCLUDE_PATH_PAINEL?>novos-emprestimos">
					<div class="box-metrica-wraper">
						<h2><i class="fas fa-exclamation-circle"></i> Empréstimos pendentes</h2>
						<p>
							<?php 
								echo Painel::emprestimosPendentes();
							?>		
						</p>
					</div>
				</a>
			</div>

			<div class="box-metrica-single">
				<a href="<?php echo INCLUDE_PATH_PAINEL?>nao-finalizados">
					<div class="box-metrica-wraper">
						<h2><i class="fas fas fa-hand-holding"></i> Empréstimos a serem devolvidos</h2>
						<p>
							<?php 
								echo Painel::emprestimosParaDevolver();
							?>
						</p>
					</div>
				</a>
			</div>

			<div class="box-metrica-single">
				<div class="box-metrica-wraper">
					<h2><i class="fas fa-flag-checkered"></i> Empréstimos finalizados</h2>
					<p>
						<?php 
							echo Painel::emprestimosFinalizados();
						?>
					</p>
				</div>
			</div>
			<div class="clear"></div>	
		</div>
		<?php endif ?>
</div>

<?php if ($_SESSION['acesso'] == 3): ?>
<div class="box-content w50 left">
		<h2><i class="fa fa-rocket"></i> Últimos 5 empréstimos </h2>

		<div class="table-responsive">
			<div class="row">
				<div class="col">
					<span>Nome</span>
				</div>

				<div class="col">
					<span>Pedido</span>
				</div>
				<div class="clear"></div>
			</div>
			
			<?php
				$ultmosCincoPedidos = PedidoDetalhes::retornaUltimosCincoPedidos();

				if($ultmosCincoPedidos != false) {
					foreach ($ultmosCincoPedidos as $ultmoCincoPedido) {
			?>
			<div class="row">
				<div class="col">
					<span><?php echo $ultmoCincoPedido->usuario->getNome(); ?></span>
				</div>

				<div class="col">
					<span><?php echo $ultmoCincoPedido->getCodigoPedido() ?></span>
				</div>
				<div class="clear"></div>
			</div>
			<?php  }}?>
		</div>
</div>

<div class="box-content w50 right">
		<h2><i class="fa fa-rocket"></i> Usuários do sistema </h2>

		<div class="table-responsive">
			<div class="row">
				<div class="col">
					<span>Usuários</span>
				</div>

				<div class="col">
					<span>Cargo</span>
				</div>
				<div class="clear"></div>
			</div>
			
			<?php
			  	$usuariosPainel = Usuario::listaTodosUsuarios();
				
				for($i = 0; $i < count($usuariosPainel); $i++) {
			?>
			<div class="row">
				<div class="col">
					<span><?php echo $i + 1 . " - " . htmlentities($usuariosPainel[$i]->getNome()); ?></span>
				</div>

				<div class="col">
					<span><?php echo pegaCargo($usuariosPainel[$i]->getAcesso()); ?></span>
				</div>
				<div class="clear"></div>
			</div>

			<?php 
				} 
			?>
		</div>
</div>
<?php endif ?>
<div class="clear"></div>