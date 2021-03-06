<?php
	/**
	 * Ficher contenant la classe EmbarcationDao
	 * @author Raphaël Bideau - 3iL
	 * @package Dao
	 */
	
	/**
	 * Classe permettant d'interagir avec la base de données concernant les Embarcation
	 */
	class EmbarcationDao extends Dao {
		
		/* Public */
		
		/**
		 * Renvoie toute les embarcations en base.
		 * @return array
		 */
		public static function getAll(){
			return self::getByQuery("SELECT * FROM db_embarcation");
		}

		/**
		 * Recherche une Dmbarcation par son id. Renvoie l'embarcation ou null si elle n'existe pas.
		 * @param  int $id
		 * @return Embarcation
		 */
		public static function getById($id){
			$result = self::getByQuery("SELECT * FROM db_embarcation WHERE id_embarcation = ?", [intval($id)]);
			if($result != null && count($result) == 1)
				return $result[0];
			else
				return null;
		}

		/**
		 * Renvoie toute les Embarcation disponible
		 * @return array
		 */
		public static function getAllDisponible(){
			return self::getByQuery("SELECT * FROM db_embarcation WHERE disponible = TRUE");
		}

		/**
		 * Renvoi toutes les embarcations dont la version est superieur à celle spécifié (strictement superieur pour version > 0 ou superieur égal pour version = 0)
		 * @param  int $versionMax 
		 * @return array             
		 */
		public static function getFromVersion($versionMax){
			//Quand versionMax vaut zero on veut inclure les version local à 0 car il s'agit de la première synchronisation pour une application
			return self::getByQuery("SELECT * FROM db_embarcation WHERE version ".($versionMax == 0 ? ">=" : ">")." ?",[$versionMax]);
		}

		/**
		 * Ajoute une Embarcation en base de données, avec pour version 0, et la renvoi ou renvoi null en cas d'erreur.
		 * @param  Embarcation $embarcation
		 * @return Embarcation
		 */
		public static function insert(Embarcation $embarcation){
			if($embarcation == null ||
				$embarcation->getLibelle() == null || strlen($embarcation->getLibelle()) == 0)
				return null;

			$embarcation->updateVersion();
			
			$stmt = parent::getConnexion()->prepare("INSERT INTO db_embarcation (libelle, maxpersonne, commentaire, disponible, version) VALUES (?, ?, ?, ?, ?)");
			$result = $stmt->execute([
								$embarcation->getLibelle(), 
								$embarcation->getMaxpersonne(), 
								$embarcation->getCommentaire(), 
								$embarcation->getDisponible(),
								$embarcation->getVersion()
								]);
			if($result){
				$embarcation->setId(parent::getConnexion()->lastInsertId());
				return $embarcation;
			}
			else
				return null;
		}
		/**
		 * Met à jour l'Embarcation passé en parametre, incrémente son numéro de version et la renvoi ou renvoi null en cas d'erreur.
		 * @param  Embarcation $embarcation
		 * @return Embarcation
		 */
		public static function update(Embarcation $embarcation){
			if($embarcation == null ||	$embarcation->getId() == null ||
				$embarcation->getLibelle() == null || strlen($embarcation->getLibelle()) == 0 ||
				$embarcation->getMaxpersonne() == null ||
				$embarcation->getDisponible() === null ||
				$embarcation->getVersion() === null)
				return null;
			
			$embarcation->updateVersion();

			$stmt = parent::getConnexion()->prepare("UPDATE db_embarcation SET libelle = ?, maxpersonne = ?, commentaire = ?, disponible = ?, version = ? WHERE id_embarcation = ?");
			$result = $stmt->execute([$embarcation->getLibelle(), 
							$embarcation->getMaxpersonne(),
							$embarcation->getCommentaire(), 
							$embarcation->getDisponible(), 
							$embarcation->getVersion(),
							$embarcation->getId()]);
			if($result)
				return $embarcation;
			else
				return null;
		}
		/* Private */
		/**
		 * Execute la requere $query avec les parametres optionnels contenus dans le tableau $param.
		 * Renvoi un tableau d'Embarcation.
		 * @param  string $query
		 * @param  array $param
		 * @return array
		 */
		private static function getByQuery($query,array $param = null){
			$stmt = parent::getConnexion()->prepare($query);
			if($stmt->execute($param) && $stmt->rowCount() > 0){
				while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
					$embarcation = new Embarcation($row['id_embarcation'], $row['version']);
					$embarcation->setLibelle($row['libelle']);
					$embarcation->setMaxpersonne($row['maxpersonne']);
					$embarcation->setCommentaire($row['commentaire']);
					$embarcation->setDisponible($row['disponible']);
					$arrayResultat[] = $embarcation;
				}
				return $arrayResultat;
			}
			else{
				return null;
			}
		}
	}
?>