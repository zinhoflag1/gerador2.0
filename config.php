<?php 

/**
 * 
 */
class Conexao
{

	public static $instance;

	
	public static function getInstance() {

		$host = 'localhost';
		$port = '3306';
		$user = 'root';
		$pass = '12345678';
		$db   = 'gerador';

		        if (!isset(self::$instance)) {

		            self::$instance = new PDO("mysql:host={$host};port={$port};dbname={$db}", "{$user}", "{$pass}", array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		            self::$instance->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_EMPTY_STRING);
		        }

	    return self::$instance;

	}


	public static function getDatabase(){

		$conexao = Conexao::getInstance();
		$sql = 'show databases';

		$result = $conexao->query($sql);

		return $result->fetchAll(2);

	}


	public static function getTabelas($db){

		$conexao = Conexao::getInstance();
		$sql = "SELECT TABLE_NAME FROM information_schema.tables where table_schema = '{$db}'";

		$result = $conexao->query($sql);

		return $result->fetchAll(2);

	}



	public static function getColunas($table, $db){

		$conexao = Conexao::getInstance();

		$sql = "SELECT * FROM information_schema.columns WHERE table_name='{$table}' and TABLE_SCHEMA = '{$db}'";

		$result = $conexao->query($sql);
        $result->execute();

        return $result->fetchAll(PDO::FETCH_OBJ);

	}




        


}

