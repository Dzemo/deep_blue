<?php

	
	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."config.php");


	/**
	 * Ficher contenant la classe Dao
	 * @author Raphaël Bideau - 3iL
	 * @package Dao
	 */
	
	/**
	 * Classe implémentent le pattern singleton pour la connexion à la base de données grâce
	 * au l'interface PDO. 
	 */
	class Dao{


		/**
		 * Singleton représentant une connection PDO
		 * @var PDO
		 */
		private static $_connexion;
		/**
		 * Permet d'executer sur la base la requete $query avec les parametre $param.
		 * Si il y a un resultat, renvoi l'array
		 * @param  string $query
		 * @param  array $param Optionnel
		 * @return array 
		 */
		public static function execute($query, array $param=null){
			$stmt = self::getConnexion()->prepare($query);
			if($stmt->execute($param) && $stmt->columnCount() > 0){
				try{
				return $stmt->fetchAll();
				}catch(PDOException $e){echo $e->getMessage();}
			}
			else{
				return null;
			}
		}
		/**		 
		 * @return PDO renvoie la connexion à la base de données.
		 */
		public static function getConnexion(){
			if(self::$_connexion){
				return self::$_connexion;
			}
			else{
				try{
					require(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."config.php");

					self::$_connexion = new PDO("mysql:charset=utf8;host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
	  				self::$_connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   	 				self::$_connexion->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				}
				catch (PDOException $e)
				{
				    echo 'Exception -> ';
				    var_dump($e->getMessage());
				}
				return self::$_connexion;
			}
		}
	}
?>