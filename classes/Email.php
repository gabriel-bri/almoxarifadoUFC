<?php
	class Email {
		private $mailer;

		// classes/phpmailer/templates/ -> os templates de e-mail se encontram aqui
		// Configurações para o envio de e-mail.
		public function __construct(){
			$this->mailer = new PHPMailer;
			$this->mailer->SMTPDebug = 3;

			$this->mailer->isSMTP(); // Set mailer to use SMTP
			$this->mailer->Host = ENDERECO; // Specify main and backup SMTP servers
			$this->mailer->SMTPAuth = true; // Enable SMTP authentication
			$this->mailer->Username = USERNAME; // SMTP username
			$this->mailer->Password = SENHA; // SMTP password
			$this->mailer->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
			$this->mailer->Port = 587; // TCP port to connect to

			$this->mailer->setFrom(USERNAME, NAME);
			$this->mailer->isHTML(true); // Set email format to HTML
			$this->mailer->CharSet = 'UTF-8';
		}

		/**
		 * Adiciona um endereço de e-mail ao destinatário do e-mail.
		 * Este método adiciona o endereço de e-mail e o nome completo
		 * do usuário fornecido como destinatário do e-mail.
		 * @param Usuario $usuario O objeto do usuário contendo
		 * as informações do destinatário.
		 * @return void
		 */
		public function addAdress(Usuario $usuario) {
			$this->mailer->addAddress(
				$usuario->getEmail(), 
				$usuario->getNome() . ' ' . $usuario->getSobrenome()
			);		
		}

		/**
		 * Prepara e envia um e-mail de confirmação de e-mail para o usuário.
		 * Este método monta o corpo do e-mail de confirmação com base em um modelo HTML,
		 * substituindo as variáveis ​​de marcação pelo conteúdo apropriado, 
		 * como o nome do usuário e a URL de confirmação.
		 * @param Usuario $usuario O objeto do usuário para o qual o 
		 * e-mail de confirmação está sendo enviado.
		 * @return void
		 */
		public function EmailConfirmacao(Usuario $usuario) {
			// Código para preparar e enviar o e-mail de confirmação
			$url = INCLUDE_PATH_PAINEL . "confirmar-email?token_confirmacao=" . $usuario->getTokenConfirmacao();
			$this->mailer->Subject = 'Confirmação de e-mail.';
			$message = file_get_contents(__DIR__ . '/phpmailer/templates/confirmacao.html');
			$message = str_replace('%endereco_site%', INCLUDE_PATH, $message);
			$message = str_replace('%ano_atual%', date('Y'), $message);
			$message = str_replace('%nome_empresa%', NOME_EMPRESA, $message);
			$message = str_replace('%login_url%', INCLUDE_PATH_PAINEL, $message);
			$message = str_replace('%username%', $usuario->getUsuario(), $message);
			$message = str_replace('%nome%', $usuario->getNome(), $message);
			$message = str_replace('action_url', $url, $message);
			$this->mailer->msgHTML($message, __DIR__);
		}
		/**
		 * Prepara e envia um e-mail de recuperação de senha para o usuário.
		 * Este método monta o corpo do e-mail de recuperação com base em 
		 * um modelo HTML, substituindo as variáveis de marcação pelo conteúdo apropriado, 
		 * como o nome do usuário e a URL de recuperação de senha.
		 * @param Usuario $usuario O objeto do usuário para o qual 
		 * o e-mail de recuperação está sendo enviado.
		 * @return void
		 */
		public function EmailRecuperacao(Usuario $usuario) {
			// Código para preparar e enviar o e-mail de recuperação
			$url = INCLUDE_PATH_PAINEL . "senha-perdida?token_recuperacao=" . $usuario->getTokenRecuperacao();
			$this->mailer->Subject = 'Sua ajuda acaba de chegar.';
			$message = file_get_contents(__DIR__ . '/phpmailer/templates/recuperacao.html');
			$message = str_replace('%endereco_site%', INCLUDE_PATH, $message);
			$message = str_replace('%ano_atual%', date('Y'), $message);
			$message = str_replace('%nome_empresa%', NOME_EMPRESA, $message);
			$message = str_replace('%login_url%', INCLUDE_PATH_PAINEL, $message);
			$message = str_replace('%username%', $usuario->getUsuario(), $message);
			$message = str_replace('%nome%', $usuario->getNome(), $message);
			$message = str_replace('action_url', $url, $message);

			$this->mailer->msgHTML($message, __DIR__);
		}

		/**
		 * Prepara e envia um e-mail para recuperar o nome de usuário do usuário.
		 * Este método monta o corpo do e-mail com base em um modelo HTML,
		 * substituindo as variáveis ​​de marcação pelo conteúdo apropriado, 
		 * como o nome do usuário e o link de login.
		 * @param Usuario $usuario O objeto do usuário para o qual 
		 * o e-mail de recuperação do nome de usuário está sendo enviado.
		 * @return void
		 */
		public function EmailRecuperarUsuario(Usuario $usuario) {
			    // Código para preparar e enviar o e-mail de recuperação do nome de usuário

			$this->mailer->Subject = 'Aqui está o seu usuário.';
			$message = file_get_contents(__DIR__ . '/phpmailer/templates/recuperar-usuario.html');
			$message = str_replace('%endereco_site%', INCLUDE_PATH, $message);
			$message = str_replace('%ano_atual%', date('Y'), $message);
			$message = str_replace('%nome_empresa%', NOME_EMPRESA, $message);
			$message = str_replace('%login_url%', INCLUDE_PATH_PAINEL, $message);
			$message = str_replace('%username%', $usuario->getUsuario(), $message);
			$message = str_replace('%nome%', $usuario->getNome(), $message);
			$message = str_replace('action_url', INCLUDE_PATH_PAINEL, $message);

			$this->mailer->msgHTML($message, __DIR__);
		}

		/**
		 * Prepara e envia um e-mail sobre o status do login do usuário.
		 * Este método monta o corpo do e-mail com base em um modelo HTML,
		 * substituindo as variáveis de marcação pelo conteúdo apropriado, 
		 * como o nome do usuário e o status do login.
		 * @param Usuario $usuario O objeto do usuário para o qual 
		 * o e-mail de status do login está sendo enviado.
		 * @return void
		 */
		public function EmailStatusLogin(Usuario $usuario) {
			    // Código para preparar e enviar o e-mail sobre o status do login

			$this->mailer->Subject = 'Atualizações a respeito da sua conta.';
			$message = file_get_contents(__DIR__ . '/phpmailer/templates/bloqueio-login.html');
			$message = str_replace('%endereco_site%', INCLUDE_PATH, $message);
			$message = str_replace('%ano_atual%', date('Y'), $message);
			$message = str_replace('%nome_empresa%', NOME_EMPRESA, $message);
			$message = str_replace('%nome%', $usuario->getNome(), $message);
			$message = str_replace('%status_login%', statusConta($usuario->isAtivada()), $message);
			
			$this->mailer->msgHTML($message, __DIR__);
		}

		/**
		 * Prepara e envia um e-mail sobre o status dos pedidos do usuário.
		 * Este método monta o corpo do e-mail com base em um modelo HTML,
		 * substituindo as variáveis de marcação pelo conteúdo apropriado, 
		 * como o nome do usuário e o status dos pedidos.
		 * @param Usuario $usuario O objeto do usuário para o qual 
		 * o e-mail de status dos pedidos está sendo enviado.
		 * @return void
		 */
		public function EmailStatusPedidos(Usuario $usuario) {
			    // Código para preparar e enviar o e-mail sobre o status dos pedidos

			$this->mailer->Subject = 'Atualizações a respeito da sua conta.';
			$message = file_get_contents(__DIR__ . '/phpmailer/templates/bloqueio-pedido.html');
			$message = str_replace('%endereco_site%', INCLUDE_PATH, $message);
			$message = str_replace('%ano_atual%', date('Y'), $message);
			$message = str_replace('%nome_empresa%', NOME_EMPRESA, $message);
			$message = str_replace('%nome%', $usuario->getNome(), $message);
			$message = str_replace('action_url', INCLUDE_PATH_PAINEL, $message);
			$message = str_replace('%username%', $usuario->getUsuario(), $message);
			$message = str_replace('%login_url%', INCLUDE_PATH_PAINEL, $message);
			$message = str_replace('%status_pedidos%', emprestimoBloqueado($usuario->isBloqueado()), $message);
			
			$this->mailer->msgHTML($message, __DIR__);
		}
		/**
		 * Prepara e envia um e-mail de confirmação de pedido para o usuário.
		 * Este método monta o corpo do e-mail com base em um modelo HTML,
		 * substituindo as variáveis de marcação pelo conteúdo apropriado, 
		 * como o nome do usuário e os detalhes do pedido.
		 * @param PedidoDetalhes $pedidoDetalhes O objeto dos detalhes 
		 * do pedido para os quais o e-mail de confirmação do pedido está sendo enviado.
		 * @return void
		 */
		public function EmailConfirmacaoPedido(PedidoDetalhes $pedidoDetalhes) {
			// Código para preparar e enviar o e-mail de confirmação do pedido
			$this->mailer->Subject = 'Informações importantes do seu pedido.';
			$message = file_get_contents(__DIR__ . '/phpmailer/templates/pedido-confirmado.html');
			$message = str_replace('%ano_atual%', date('Y'), $message);
			$message = str_replace('%nome_empresa%', NOME_EMPRESA, $message);
			$message = str_replace('%endereco_site%', INCLUDE_PATH, $message);
			$message = str_replace('%nome%', $pedidoDetalhes->usuario->getNome(), $message);
			$message = str_replace('%codigo_pedido%', $pedidoDetalhes->getCodigoPedido(), $message);
			$message = str_replace('%data_hoje%', implode("/",array_reverse(explode("-",$pedidoDetalhes->getDataPedido()))), $message);

			$this->mailer->msgHTML($message, __DIR__);
		}

		/**
		 * Prepara e envia um e-mail de aprovação de pedido para o usuário.
		 * Este método monta o corpo do e-mail com base em um modelo HTML,
		 * substituindo as variáveis de marcação pelo conteúdo apropriado, 
		 * como o nome do usuário e os detalhes do pedido.
		 * @param PedidoDetalhes $pedidoDetalhes O objeto dos detalhes do pedido
		 *  para os quais o e-mail de aprovação do pedido está sendo enviado.
		 * @param string $feedback O feedback do pedido.
		 * @return void
		 */
		public function EmailPedidoAprovado(PedidoDetalhes $pedidoDetalhes, $feedback) {
    		// Código para preparar e enviar o e-mail de aprovação do pedido
			$this->mailer->Subject = 'Seu pedido foi aprovado.';
			$message = file_get_contents(__DIR__ . '/phpmailer/templates/pedido-aprovado.html');
			$message = str_replace('%ano_atual%', date('Y'), $message);
			$message = str_replace('%nome_empresa%', NOME_EMPRESA, $message);
			$message = str_replace('%nome%', $pedidoDetalhes->usuario->getNome(), $message);
			$message = str_replace('%codigo_pedido%', $pedidoDetalhes->getCodigoPedido(), $message);
			$message = str_replace('%data_hoje%', implode("/",array_reverse(explode("-",$pedidoDetalhes->getDataPedido()))), $message);
			$message = str_replace('%status_pedido%', statusPedido(1), $message);
			$message = str_replace('%status_emprestimo%', statusEmprestimo(0), $message);
			$message = str_replace('%endereco_site%', INCLUDE_PATH, $message);
			$message = str_replace('%feedback%', $feedback, $message);
			$this->mailer->addAttachment(BASE_DIR_PAINEL . '/comprovantes/' . $pedidoDetalhes->getCodigoPedido() . '.pdf');
			$this->mailer->msgHTML($message, __DIR__);
		}

		/**
		 * Prepara e envia um e-mail de notificação de Nada Consta para o usuário.
		 * Este método monta o corpo do e-mail com base em um modelo HTML,
		 * substituindo as variáveis de marcação pelo conteúdo apropriado, 
		 * como o nome do usuário e o link para a validação do Nada Consta.
		 * @param NadaConsta $nadaConsta O objeto NadaConsta para o qual
		 *  o e-mail de notificação está sendo enviado.
		 * @return void
		 */
		public function EmailNadaConsta(NadaConsta $nadaConsta) {
			// Código para preparar e enviar o e-mail de notificação de Nada Consta
			$this->mailer->Subject = 'Seu Nada Consta está pronto.';
			$url = INCLUDE_PATH_PAINEL . "validar-documento?codigo_seguranca=" . $nadaConsta->getCodigoSeguranca();
			$url2 = INCLUDE_PATH_PAINEL . "validar-documento";
			$message = file_get_contents(__DIR__ . '/phpmailer/templates/nada-consta.html');
			$message = str_replace('%ano_atual%', date('Y'), $message);
			$message = str_replace('%nome_empresa%', NOME_EMPRESA, $message);
			$message = str_replace('action_url', $url, $message);
			$message = str_replace('pagina_validacao', $url2, $message);
			$message = str_replace('%nome%', $nadaConsta->usuario->getNome(), $message);
			$message = str_replace('%codigo_validacao%', $nadaConsta->getCodigoSeguranca(), $message);
			$message = str_replace('%data_emissao%', implode("/",array_reverse(explode("-",$nadaConsta->getData()))), $message);
			$message = str_replace('%endereco_site%', INCLUDE_PATH, $message);
			$this->mailer->addAttachment(BASE_DIR_PAINEL . "/declaracoes/declaracao_nada_consta_{$nadaConsta->usuario->getMatricula()}.pdf");
			$this->mailer->msgHTML($message, __DIR__);
		}
		
		/**
		 * Prepara e envia um e-mail de notificação sobre um pedido ativo para o usuário.
		 * Este método monta o corpo do e-mail com base em um modelo HTML,
		 * substituindo as variáveis de marcação pelo conteúdo apropriado, 
		 * como o nome do usuário e os detalhes do pedido.
		 * @param PedidoDetalhes $pedidoDetalhes O objeto dos detalhes do 
		 * pedido para os quais o e-mail de notificação está sendo enviado.
		 * @return void
		 */
		public function EmailPedidoAtivo(PedidoDetalhes $pedidoDetalhes) {
			// Código para preparar e enviar o e-mail de notificação 
			// sobre um pedido ativo
			$this->mailer->Subject = 'Notificação sobre pedidos ativos.';
			$message = file_get_contents(__DIR__ . '/phpmailer/templates/notifica-usuario.html');
			$message = str_replace('%ano_atual%', date('Y'), $message);
			$message = str_replace('%nome_empresa%', NOME_EMPRESA, $message);
			$message = str_replace('action_url', INCLUDE_PATH_PAINEL, $message);
			$message = str_replace('%nome%', $pedidoDetalhes->usuario->getNome(), $message);
			$message = str_replace('%codigo_pedido%', $pedidoDetalhes->getCodigoPedido(), $message);
			$message = str_replace('%data_hoje%', implode("/",array_reverse(explode("-",$pedidoDetalhes->getDataPedido()))), $message);
			$message = str_replace('%status_pedido%', statusPedido(1), $message);
			$message = str_replace('%status_emprestimo%', statusEmprestimo(0), $message);
			$message = str_replace('%endereco_site%', INCLUDE_PATH, $message);
			$this->mailer->addAttachment(BASE_DIR_PAINEL . '/comprovantes/' . $pedidoDetalhes->getCodigoPedido() . '.pdf');
			$this->mailer->msgHTML($message, __DIR__);
		}
		/**
		 * Prepara e envia um e-mail informando que um pedido foi negado para o usuário.
		 * Este método monta o corpo do e-mail com base em um modelo HTML,
		 * substituindo as variáveis de marcação pelo conteúdo apropriado, 
		 * como o nome do usuário, os detalhes do pedido e o feedback.
		 * @param PedidoDetalhes $pedidoDetalhes O objeto dos 
		 * detalhes do pedido para os quais o e-mail de notificação está sendo enviado.
		 * @param string $feedback O feedback sobre o motivo da negação do pedido.
		 * @return void
		 */
		public function EmailPedidoNegado(PedidoDetalhes $pedidoDetalhes, $feedback) {
	    // Código para preparar e enviar o e-mail de notificação sobre um pedido negado

			$this->mailer->Subject = 'Seu pedido foi negado.';
			$message = file_get_contents(__DIR__ . '/phpmailer/templates/pedido-negado.html');
			$message = str_replace('%ano_atual%', date('Y'), $message);
			$message = str_replace('%nome_empresa%', NOME_EMPRESA, $message);
			$message = str_replace('%nome%', $pedidoDetalhes->usuario->getNome(), $message);
			$message = str_replace('%codigo_pedido%', $pedidoDetalhes->getCodigoPedido(), $message);
			$message = str_replace('%data_hoje%', implode("/",array_reverse(explode("-",$pedidoDetalhes->getDataPedido()))), $message);
			$message = str_replace('%status_pedido%', statusPedido(0), $message);
			$message = str_replace('%feedback%', $feedback, $message);
			$message = str_replace('%endereco_site%', INCLUDE_PATH, $message);
			$message = str_replace('%status_emprestimo%', statusEmprestimo(1), $message);
			$this->mailer->msgHTML($message, __DIR__);
		}

		/**
		 * Prepara e envia um e-mail informando que um pedido foi finalizado para o usuário.
		 * Este método monta o corpo do e-mail com base em um modelo HTML,
		 * substituindo as variáveis de marcação pelo conteúdo apropriado, 
		 * como o nome do usuário, os detalhes do pedido e a data de finalização.
		 * @param PedidoDetalhes $pedidoDetalhes O objeto dos detalhes do pedido 
		 * para os quais o e-mail de notificação está sendo enviado.
		 * @return void
		 */
		public function EmailPedidoFinalizado(PedidoDetalhes $pedidoDetalhes) {
			// Código para preparar e enviar o e-mail de 
			// notificação sobre um pedido finalizado
			$this->mailer->Subject = 'Seu pedido foi finalizado.';
			$message = file_get_contents(__DIR__ . '/phpmailer/templates/pedido-finalizado.html');
			$message = str_replace('%ano_atual%', date('Y'), $message);
			$message = str_replace('%nome_empresa%', NOME_EMPRESA, $message);
			$message = str_replace('%nome%', $pedidoDetalhes->usuario->getNome(), $message);
			$message = str_replace('%codigo_pedido%', $pedidoDetalhes->getCodigoPedido(), $message);
			$message = str_replace('%data_hoje%', $pedidoDetalhes->getDataFinalizado(), $message);
			$message = str_replace('%data_pedido%', $pedidoDetalhes->getDataPedido(), $message);
			$message = str_replace('%endereco_site%', INCLUDE_PATH, $message);
			$message = str_replace('%status_pedido%', statusPedido(1), $message);
			$message = str_replace('%status_emprestimo%', statusEmprestimo(1), $message);
			$this->mailer->msgHTML($message, __DIR__);
		}

		/**
		 * Envia o e-mail preparado.
		 * @return bool Retorna true se o e-mail for enviado com sucesso, 
		 * caso contrário, retorna false.
		 */
		public function enviarEmail() {
			// Código para enviar o e-mail preparado
			if ($this->mailer->send()) {
				return true;
			} 
			
			else {
				return false;
			}
		}
	}
?>