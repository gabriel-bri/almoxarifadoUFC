<?php 
	class Mysql{
		private static $pdo;
		public static function conectar() {
			if(self::$pdo == NULL) {
				try{
					self::$pdo = new PDO('mysql:host='.HOST.';dbname='.DATABASE,USER,PASSWORD,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
					self::$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				}
				
				catch(Exception $e){
					echo '<h2>Erro ao conectar </h2>' . $e;
				}
			}

			return self::$pdo;
		}
	}
?>