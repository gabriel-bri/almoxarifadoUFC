<?php
	class Email {
		private $mailer;
		public function __construct(){
			$this->mailer = new PHPMailer;
			$this->mailer->SMTPDebug = 0;

			$this->mailer->isSMTP();                                      // Set mailer to use SMTP
			$this->mailer->Host = ENDERECO;  				  // Specify main and backup SMTP servers
			$this->mailer->SMTPAuth = true;                               // Enable SMTP authentication
			$this->mailer->Username = USERNAME;                 // SMTP username
			$this->mailer->Password = SENHA;                           // SMTP password
			$this->mailer->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
			$this->mailer->Port = 587;                                    // TCP port to connect to

			$this->mailer->setFrom(USERNAME, NAME);
			$this->mailer->isHTML(true);                                  // Set email format to HTML
			$this->mailer->CharSet = 'UTF-8';

		}

		public function addAdress($email, $nome) {
			$this->mailer->addAddress($email,$nome);		
		}

		public function EmailConfirmacao($nome, $login, $token_confirmacao) {
			$url = INCLUDE_PATH_PAINEL . "confirmar-email?token_confirmacao=" . $token_confirmacao;
			$this->mailer->Subject = 'Confirmação de e-mail.';
			$message = file_get_contents(__DIR__ . '/phpmailer/confirmacao.html');
			$message = str_replace('%ano_atual%', date('Y'), $message);
			$message = str_replace('%nome_empresa%', NOME_EMPRESA, $message);
			$message = str_replace('%login_url%', INCLUDE_PATH_PAINEL, $message);
			$message = str_replace('%username%', $login, $message);
			$message = str_replace('%nome%', $nome, $message);
			$message = str_replace('action_url', $url, $message);
			$this->mailer->msgHTML($message, __DIR__);
		}

		public function EmailRecuperacao($nome, $login, $token_recuperacao) {
			$url = INCLUDE_PATH_PAINEL . "senha-perdida?token_recuperacao=" . $token_recuperacao;
			$this->mailer->Subject = 'Sua ajuda acaba de chegar.';
			$message = file_get_contents(__DIR__ . '/phpmailer/recuperacao.html');
			$message = str_replace('%ano_atual%', date('Y'), $message);
			$message = str_replace('%nome_empresa%', NOME_EMPRESA, $message);
			$message = str_replace('%login_url%', INCLUDE_PATH_PAINEL, $message);
			$message = str_replace('%username%', $login, $message);
			$message = str_replace('%nome%', $nome, $message);
			$message = str_replace('action_url', $url, $message);

			$this->mailer->msgHTML($message, __DIR__);
		}

		public function EmailConfirmacaoPedido($nome, $dataPedido, $codigoPedido) {
			$this->mailer->Subject = 'Informações importantes do seu pedido.';
			$message = file_get_contents(__DIR__ . '/phpmailer/pedido-confirmado.html');
			$message = str_replace('%ano_atual%', date('Y'), $message);
			$message = str_replace('%nome_empresa%', NOME_EMPRESA, $message);
			$message = str_replace('%nome%', $nome, $message);
			$message = str_replace('%codigo_pedido%', $codigoPedido, $message);
			$message = str_replace('%data_hoje%', $dataPedido, $message);

			$this->mailer->msgHTML($message, __DIR__);
		}

		public function EmailPedidoAprovado($nome, $dataPedido, $codigoPedido) {
			$this->mailer->Subject = 'Seu pedido foi aprovado.';
			$message = file_get_contents(__DIR__ . '/phpmailer/pedido-aprovado.html');
			$message = str_replace('%ano_atual%', date('Y'), $message);
			$message = str_replace('%nome_empresa%', NOME_EMPRESA, $message);
			$message = str_replace('%nome%', $nome, $message);
			$message = str_replace('%codigo_pedido%', $codigoPedido, $message);
			$message = str_replace('%data_hoje%', $dataPedido, $message);
			$message = str_replace('%status_pedido%', statusPedido(1), $message);
			$message = str_replace('%status_emprestimo%', statusEmprestimo(0), $message);
			$this->mailer->addAttachment(BASE_DIR_PAINEL . '/comprovantes/' . $codigoPedido . '.pdf');
			$this->mailer->msgHTML($message, __DIR__);
		}

		public function EmailPedidoNegado($nome, $dataPedido, $codigoPedido) {
			$this->mailer->Subject = 'Seu pedido foi negado.';
			$message = file_get_contents(__DIR__ . '/phpmailer/pedido-negado.html');
			$message = str_replace('%ano_atual%', date('Y'), $message);
			$message = str_replace('%nome_empresa%', NOME_EMPRESA, $message);
			$message = str_replace('%nome%', $nome, $message);
			$message = str_replace('%codigo_pedido%', $codigoPedido, $message);
			$message = str_replace('%data_hoje%', $dataPedido, $message);
			$message = str_replace('%status_pedido%', statusPedido(0), $message);
			$message = str_replace('%status_emprestimo%', statusEmprestimo(1), $message);
			$this->mailer->msgHTML($message, __DIR__);
		}

		public function EmailPedidoFinalizado($nome, $dataPedido, $dataHoje, $codigoPedido) {
			$this->mailer->Subject = 'Seu pedido foi finalizado.';
			$message = file_get_contents(__DIR__ . '/phpmailer/pedido-finalizado.html');
			$message = str_replace('%ano_atual%', date('Y'), $message);
			$message = str_replace('%nome_empresa%', NOME_EMPRESA, $message);
			$message = str_replace('%nome%', $nome, $message);
			$message = str_replace('%codigo_pedido%', $codigoPedido, $message);
			$message = str_replace('%data_hoje%', $dataHoje, $message);
			$message = str_replace('%data_pedido%', $dataPedido, $message);
			$message = str_replace('%status_pedido%', statusPedido(1), $message);
			$message = str_replace('%status_emprestimo%', statusEmprestimo(1), $message);
			$this->mailer->msgHTML($message, __DIR__);
		}

		public function enviarEmail() {
			if ($this->mailer->send()) {
				return true;
			}

			else {
				return false;
			}
		}
	}
?>