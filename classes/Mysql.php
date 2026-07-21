<?php 
class Mysql{
    private static $pdo;

    public static function conectar() {
        if(self::$pdo == NULL) {
            try{
                self::$pdo = new PDO('mysql:host='.HOST.';dbname='.DATABASE.';charset=utf8', USER, PASSWORD);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            }
            
            catch(Exception $e){
                echo '<h2>Erro ao conectar </h2>';
            }
        }

        return self::$pdo;
    }
}
?>