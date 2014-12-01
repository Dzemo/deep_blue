<?php
	/**
	 * Ficher contenant la classe AptitudeDao
	 * @author Raphaël Bideau - 3iL
	 * @package Dao
	 */
	/**
	 * Classe permettant d'interagir avec la base de données concernant les Templates
	 */
	class AptitudeDao extends Dao {
		/* Public */
		/**
		 * Renvoi toute les aptitudes dans la base dans un tableau indexé par leur id
		 * @return array Tableau d'aptitudes indexés par leur id
		 */
		public static function getAll(){
			return self::getByQuery("SELECT * FROM db_aptitude");
		}
		/**
		 * Recherche l'aptitude par son id
		 * @param  int $id
		 * @return Aptitude
		 */
		public static function getById($id){
			$result = self::getByQuery("SELECT * FROM db_aptitude WHERE id_aptitude = ?",[$id]);
			if($result != null && count($result) == 1)
				return $result[$id];
			else
				return null;
		}
		/**
		 * Renvoi toutes les aptitudes dont l'id est dans le tableau passé en parametre dans un tableau indexé par leur id
		 * @param  array $ids 
		 * @return array      Tableau d'aptitudes indexés par leur id
		 */
		public static function getByIds($ids){
			if($ids == null || count($ids) == 0)
				return null;
			$stringIds = "id_aptitude = ?";
			for($i = 1; $i < count($ids); $i++) {
				$stringIds = $stringIds." OR id_aptitude = ?";
			}
			return self::getByQuery("SELECT * FROM db_aptitude WHERE ".$stringIds, $ids);
		}
		/**
		 * Ajoute une aptitude dans la base et la retourne ou renvoi null en cas d'erreur
		 * @param  Aptitude $aptitude
		 * @return Aptitude
		 */
		public static function insert(Aptitude $aptitude){
			if($aptitude == null ||
				$aptitude->getLibelleCourt() == null || strlen($aptitude->getLibelleCourt()) == 0)
				return null;
			if($aptitude->getLibelleLong() == null)
				$aptitude->setLibelleLong("");
			$stmt = parent::getConnexion()->prepare("INSERT INTO db_aptitude (libelle_court, libelle_long, technique_max, encadree_max, autonome_max, nitrox_max, ajout_max, enseignement_air_max, enseignement_nitrox_max, encadremement_max) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
			$result = $stmt->execute([$aptitude->getLibelleCourt(),
							$aptitude->getLibelleLong(),
							$aptitude->getTechniqueMax(),
							$aptitude->getEncadreeMax(),
							$aptitude->getAutonomeMax(),
							$aptitude->getNitroxMax(),
							$aptitude->getAjoutMax(),
							$aptitude->getEnseignementAirMax(),
							$aptitude->getEnseignementNitroxMax(),
							$aptitude->getEncadrementMax()
							]);
			if($result){
				$aptitude->setId(parent::getConnexion()->lastInsertId());
				return $aptitude;
			}
			else
				return null;
		}
		/**
		 * Met a jours une Aptitude et la renvoi ou renvoi null en cas d'erreur
		 * @param  Aptitude $aptitude
		 * @return Aptitude
		 */
		public static function update(Aptitude $aptitude){
			
			if($aptitude == null ||
				$aptitude->getId() == null ||
				$aptitude->getLibelleCourt() == null || strlen($aptitude->getLibelleCourt()) == 0 ||
				$aptitude->getVersion() === null)
				return null;
			if($aptitude->getLibelleLong() == null)
				$aptitude->setLibelleLong("");

			$aptitude->updateVersion();

			$stmt = parent::getConnexion()->prepare("UPDATE db_aptitude SET libelle_court = ?, libelle_long = ?, technique_max = ?, encadree_max = ?, autonome_max = ?, nitrox_max = ?, ajout_max = ?, enseignement_air_max = ?, enseignement_nitrox_max = ?, encadremement_max = ?, version = ? WHERE id_aptitude = ?");			
			$result = $stmt->execute([$aptitude->getLibelleCourt(),
							$aptitude->getLibelleLong(),
							$aptitude->getTechniqueMax(),
							$aptitude->getEncadreeMax(),
							$aptitude->getAutonomeMax(),
							$aptitude->getNitroxMax(),
							$aptitude->getAjoutMax(),
							$aptitude->getEnseignementAirMax(),
							$aptitude->getEnseignementNitroxMax(),
							$aptitude->getEncadrementMax(),
							$aptitude->getVersion(),
							$aptitude->getId()
							]);
			if($result)
				return $aptitude;
			else
				return null;
		}
		/* Private */
		/**
		 * Execute la requere $query avec les parametres optionnels contenus dans le tableau $param.
		 * Renvoi un tableau d'Aptidue'.
		 * @param  string $query
		 * @param  array $param
		 * @return array         Tableau d'aptitudes indexés par leur id
		 */
		private static function getByQuery($query, $param = null){
			$stmt = parent::getConnexion()->prepare($query);
			if($stmt->execute($param) && $stmt->rowCount() > 0){
				$arrayResultat = array();
				while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
					$aptitude = new Aptitude($row['id_aptitude'], $row['version']);
					$aptitude->setLibelleCourt($row['libelle_court']);
					$aptitude->setLibelleLong($row['libelle_long']);
					$aptitude->setTechniqueMax($row['technique_max']);
					$aptitude->setEncadreeMax($row['encadree_max']);
					$aptitude->setAutonomeMax($row['autonome_max']);
					$aptitude->setNitroxMax($row['nitrox_max']);
					$aptitude->setAjoutMax($row['ajout_max']);
					$aptitude->setEnseignementAirMax($row['enseignement_air_max']);
					$aptitude->setEnseignementNitroxMax($row['enseignement_nitrox_max']);
					$aptitude->setEncadrementMax($row['encadremement_max']);
					$arrayResultat[$aptitude->getId()] = $aptitude;
				}
				return $arrayResultat;
			}
			else{
				return null;
			}
		}
	}
?>