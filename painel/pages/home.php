<div class="box-content w100">
		<h2><i class="fa fa-home"></i> Painel de controle - <?php echo NOME_EMPRESA; ?></h2>
		<div class="box-metricas">
			<div class="box-metrica-single">
				<div class="box-metrica-wraper">
					<h2>Empréstimos pendentes</h2>
					<p>
						<?php 
							$quantidade = Painel::emprestimosPendentes();
							echo isset($quantidade[0][0]) ? $quantidade[0][0] : 0;
						?>		
					</p>
				</div>
			</div>

			<div class="box-metrica-single">
				<div class="box-metrica-wraper">
					<h2>Empréstimos a serem devolvidos</h2>
					<p>
						<?php 
							$quantidade = Painel::emprestimosParaDevolver();
							echo isset($quantidade[0][0]) ? $quantidade[0][0] : 0;
						?>
					</p>
				</div>
			</div>

			<div class="box-metrica-single">
				<div class="box-metrica-wraper">
					<h2>Empréstimos finalizados</h2>
					<p>
						<?php 
							$quantidade = Painel::emprestimosFinalizados();
							echo isset($quantidade[0][0]) ? $quantidade[0][0] : 0;
						?>
					</p>
				</div>
			</div>
			<div class="clear"></div>	
		</div>
</div>

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
				foreach ($usuariosOnline as $key => $value) {
			?>
			<div class="row">
				<div class="col">
					<span><?php echo $value['ip']; ?></span>
				</div>

				<div class="col">
					<span><?php echo date('d/m/Y H:i:m', strtotime( $value['ultima_acao'])); ?></span>
				</div>
				<div class="clear"></div>
			</div>
			<?php } ?>
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
			  	$usuariosPainel = Mysql::conectar()->prepare('SELECT * FROM  `usuarios`');
			  	$usuariosPainel->execute();
			  	$usuariosPainel = $usuariosPainel->fetchAll();
				foreach ($usuariosPainel as $key => $value) {
			?>
			<div class="row">
				<div class="col">
					<span><?php echo htmlentities($value['nome']); ?></span>
				</div>

				<div class="col">
					<span><?php echo pegaCargo($value['acesso']); ?></span>
				</div>
				<div class="clear"></div>
			</div>
			<?php } ?>
		</div>
</div>

<div class="clear"></div>