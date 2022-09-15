<?php 
	include ('../config.php');
// echo 'loguei nao carai kkkkk';
	if(Painel::logado() == false) {
		include('login.php');
	}

	else {
		include('main.php');
	}
?>