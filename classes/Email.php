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