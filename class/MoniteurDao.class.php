<?php
	/**
	 * Ficher contenant la classe MoniteurDao
	 * @author Raphaël Bideau - 3iL
	 * @package Dao
	 */

	/**
	 * Classe permettant d'interagir avec la base de données concernant les Moniteur
	 */
	class MoniteurDao extends Dao {

		/* Public */

		/**
		 * Retourne tout les moniteurs de la base
		 * @return array
		 */
		public static function getAll(){
			return self::getByQuery("SELECT * FROM db_moniteur");
		}

		/**
		 * Retourne tout les moniteurs de la base qui sont actif
		 * @return array
		 */
		public static function getAllActif(){
			return self::getByQuery("SELECT * FROM db_moniteur WHERE actif = TRUE");
		}

		/**
		 * Retourne tout les moniteurs de la base qui peuvent être directeur de plongé et sont actif
		 * @return array
		 */
		public static function getAllActifDirecteurPlonge(){
			return self::getByQuery("SELECT * FROM db_moniteur WHERE directeur_plonge = TRUE AND actif = TRUE");
		}

		/**
		 * Renvoi le moniteur d'id spécifié
		 * @param  int $id
		 * @return Moniteur
		 */
		public static function getById($id){
			$result = self::getByQuery("SELECT * FROM db_moniteur WHERE id_moniteur = ?",[$id]);

			if($result != null && count($result) == 1)
				return $result[0];
			else
				return null;
		}

		/**
		 * Ajoute un moniteur dans la base de donnée
		 * @param  Moniteur $moniteur
		 * @return Moniteur
		 */
		public static function insert(Moniteur $moniteur){
			if($moniteur == null ||
				$moniteur->getNom() == null || strlen($moniteur->getNom()) == 0 ||
				$moniteur->getPrenom() == null || strlen($moniteur->getPrenom()) == 0
				)
				return null;

			if($moniteur->estActif() == null) $moniteur->setActif(false);
			if($moniteur->estDirecteurPlonge() == null) $moniteur->setDirecteurPlonge(false);

			$stmt = parent::getConnexion()->prepare("INSERT INTO db_moniteur (nom, prenom, aptitudes, directeur_plonge, actif, email, telephone) VALUES (?, ?, ?, ?, ?, ?, ?)");
			$result = $stmt->execute([
				$moniteur->getNom(),
				$moniteur->getPrenom(),
				Aptitude::AptitudesArrayToAptitudesString($moniteur->getAptitudes()),
				$moniteur->estDirecteurPlonge(),
				$moniteur->estActif(),
				$moniteur->getEmail(),
				$moniteur->getTelephone()
				]);

			if($result){
				$moniteur->setId(parent::getConnexion()->lastInsertId());
				return $moniteur;
			}
			else
				return null;
		}

		/**
		 * Met à jour un moniteur dans la base de donnée
		 * @param  Moniteur $moniteur
		 * @return Moniteur
		 */
		public static function update(Moniteur $moniteur){
			if($moniteur == null || $moniteur->getId() == null ||
				$moniteur->getNom() == null || strlen($moniteur->getNom()) == 0 ||
				$moniteur->getPrenom() == null || strlen($moniteur->getPrenom()) == 0 ||
				$moniteur->getVersion() === null
				)
				return null;

			if($moniteur->estActif() == null) $moniteur->setActif(false);
			if($moniteur->estDirecteurPlonge() == null) $moniteur->setDirecteurPlonge(false);			

			$moniteur->incrementeVersion();
			$stmt = parent::getConnexion()->prepare("UPDATE db_moniteur SET nom = ?, prenom = ?, aptitudes = ?, directeur_plonge = ?, actif = ?, email = ?, telephone = ?, version = ? WHERE id_moniteur = ?");
			$result = $stmt->execute([
				$moniteur->getNom(),
				$moniteur->getPrenom(),
				Aptitude::AptitudesArrayToAptitudesString($moniteur->getAptitudes()),
				$moniteur->estDirecteurPlonge(),
				$moniteur->estActif(),
				$moniteur->getEmail(),
				$moniteur->getTelephone(),
				$moniteur->getVersion(),
				$moniteur->getId()
				]);

			if($result)
				return $moniteur;
			else
				return null;
		}		

		/**
		 * Supprime le moniteur de la base dont l'id est passé en parametre
		 * @param  int $id_moniteur id du moniteur à supprimer
		 * @return boolean              true si le moniteur à bien été supprimé, false sinon
		 */
		public static function delete($id_moniteur){
			$stmt = parent::getConnexion()->prepare("DELETE FROM db_moniteur WHERE id_moniteur = ?");
			return $stmt->execute([$id_moniteur]);
		}

		/* Private */
		/**
		 * Execute la requere $query avec les parametres optionnels contenus dans le tableau $param.
		 * Renvoi un tableau de MoniteurDao.
		 * @param  string $query
		 * @param  array $param
		 * @return array
		 */
		private static function getByQuery($query, $param = null){

			$stmt = parent::getConnexion()->prepare($query);

			if($stmt->execute($param) && $stmt->rowCount() > 0){
				$arrayResultat = array();
				while($row = $stmt->fetch(PDO::FETCH_ASSOC)){

					$moniteur = new Moniteur($row['id_moniteur'], $row['version']);
					$moniteur->setNom($row['nom']);
					$moniteur->setPrenom($row['prenom']);
					$moniteur->setAptitudes(AptitudeDao::getByIds(Aptitude::aptitudesStringToAptitudesIdsArray($row['aptitudes'])));
					$moniteur->setDirecteurPlonge($row['directeur_plonge']);
					$moniteur->setEmail($row['email']);
					$moniteur->setTelephone($row['telephone']);
					$moniteur->setActif($row['actif']);

					$arrayResultat[] = $moniteur;
				}
				return $arrayResultat;
			}
			else{
				return null;
			}
		}
	}
?>