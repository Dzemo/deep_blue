<?php
	/**
	 * Ficher contenant la classe PlongeurDao
	 * @author Raphaël Bideau - 3iL
	 * @package Dao
	 */
	/**
	 * Classe permettant d'interagir avec la base de données concernant les Plongeur
	 */
	class PlongeurDao extends Dao {
		/* Public */
		/**
		 * Retourne tout les plongeurs de la base
		 * @return array
		 */
		public static function getAll(){
			return self::getByQuery("SELECT * FROM db_plongeur");
		}
		/**
		 * Retourne les dernier X plongeurs à être venu au club ou null si aucun ne sont venu
		 * @param  int $nombre Nombre de plongeur retourné
		 * @return array
		 */
		public static function getLastX($nombre){
			return self::getByQuery("SELECT tmp.* FROM (SELECT p.* FROM db_plongeur p, db_fiche_securite f	WHERE p.id_fiche_securite = f.id_fiche_securite ORDER BY p.version DESC LIMIT ?) AS tmp GROUP BY tmp.nom, tmp.prenom, tmp.date_naissance ORDER BY tmp.version DESC LIMIT ?",[$nombre*2, $nombre]);
		}
		/**
		 * Retourne tout les plongeurs appartenant à la palanqué spécifié.
		 * Si un moniteur est présent il sera en premier élément du tableau
		 * @param  int $id_palanque
		 * @return array
		 */
		public static function getByIdPalanque($id_palanque){
			return self::getByQuery("SELECT * FROM db_plongeur WHERE id_palanque = ? ORDER BY date_naissance ASC",[$id_palanque]);
		}
		/**
		 * Retourne tout les plongeurs dont la palanqué appartient à la fiche de sécurité de l'id spécifié
		 * Le tableau est trié par id_palanque croissant
		 * @param  int $id_fiche_securite
		 * @return array
		 */
		public static function getByIdFicheSecurite($id_fiche_securite){
			return self::getByQuery("SELECT * FROM db_plongeur WHERE id_fiche_securite = ? ORDER BY id_palanque ASC",[$id_fiche_securite]);
		}
		/**
		 * Renvoi le plongeur d'id spécifié
		 * @param  int $id
		 * @return Plongeur
		 */
		public static function getById($id){
			$result = self::getByQuery("SELECT * FROM db_plongeur WHERE id_plongeur = ?",[$id]);
			if($result != null && count($result) == 1)
				return $result[0];
			else
				return null;
		}
		/**
		 * Ajoute un plongeur dans la base de donnée
		 * Pour ajouter un plongeur d'une nouvelle fiche de sécurité, priviligier l'utilisation de FicheSecuriteDao::insert()
		 * @see FicheSecuriteDao::insert()
		 * @param  Plongeur $plongeur
		 * @return Plongeur
		 */
		public static function insert(Plongeur $plongeur){
			if($plongeur == null ||
				$plongeur->getNom() == null || strlen($plongeur->getNom()) == 0 ||
				$plongeur->getPrenom() == null || strlen($plongeur->getPrenom()) == 0 ||
				$plongeur->getDateNaissance() == null || strlen($plongeur->getDateNaissance()) == 0 ||
				$plongeur->getIdPalanque() == null ||
				$plongeur->getIdFicheSecurite() == null
				){
				return null;
			}
			if($plongeur->getTelephone() == null)$plongeur->setTelephone("");
			if($plongeur->getTelephoneUrgence() == null)$plongeur->setTelephoneUrgence("");
			$plongeur->updateVersion();

			$stmt = parent::getConnexion()->prepare("INSERT INTO db_plongeur (id_palanque, id_fiche_securite, nom, prenom, aptitudes, telephone, telephone_urgence, date_naissance, profondeur_realisee, duree_realisee, version) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
			$result = $stmt->execute([
				$plongeur->getIdPalanque(),
				$plongeur->getIdFicheSecurite(),
				$plongeur->getNom(),
				$plongeur->getPrenom(),
				Aptitude::AptitudesArrayToAptitudesString($plongeur->getAptitudes()),
				$plongeur->getTelephone(),
				$plongeur->getTelephoneUrgence(),
				$plongeur->getDateNaissance(),
				$plongeur->getProfondeurRealisee(),
				$plongeur->getDureeRealisee(),
				$plongeur->getVersion()
				]);
			if($result){
				$plongeur->setId(parent::getConnexion()->lastInsertId());
				return $plongeur;
			}
			else
				return null;
		}
		/**
		 * Met à jour un plongeur dans la base de donnée
		 * Pour la mise à jours des plongeurs, priviligier FicheSecuriteDao::update()
		 * @see FicheSecuriteDao::update()
		 * @param  Plongeur $plongeur
		 * @return Plongeur
		 */
		public static function update(Plongeur $plongeur){
			if($plongeur == null || $plongeur->getId() == null ||
				$plongeur->getNom() == null || strlen($plongeur->getNom()) == 0 ||
				$plongeur->getPrenom() == null || strlen($plongeur->getPrenom()) == 0 ||
				$plongeur->getDateNaissance() == null || strlen($plongeur->getDateNaissance()) == 0 ||
				$plongeur->getIdPalanque() == null ||
				$plongeur->getIdFicheSecurite() == null ||
				$plongeur->getVersion() === null
				)
				return null;
			if($plongeur->getTelephone() == null)$plongeur->setTelephone("");
			
			if($plongeur->getTelephoneUrgence() == null)$plongeur->setTelephoneUrgence("");
			$plongeur->updateVersion();
			
			$stmt = parent::getConnexion()->prepare("UPDATE db_plongeur SET id_palanque = ?, id_fiche_securite = ?, nom = ?, prenom = ?, aptitudes = ?, telephone = ?, telephone_urgence = ?, date_naissance = ?, profondeur_realisee = ?, duree_realisee = ?, version = ? WHERE id_plongeur = ?");
			$result = $stmt->execute([
				$plongeur->getIdPalanque(),
				$plongeur->getIdFicheSecurite(),
				$plongeur->getNom(),
				$plongeur->getPrenom(),
				Aptitude::AptitudesArrayToAptitudesString($plongeur->getAptitudes()),
				$plongeur->getTelephone(),
				$plongeur->getTelephoneUrgence(),
				$plongeur->getDateNaissance(),
				$plongeur->getProfondeurRealisee(),
				$plongeur->getDureeRealisee(),
				$plongeur->getVersion(),
				$plongeur->getId()
				]);
			if($result)
				return $plongeur;
			else
				return null;
		}		
		/**
		 * Met à jours les plongeurs de la palanqué passé en parametre :
		 * Supprime les plongeurs qui appartenait a la palanqué mais qui ne sont plus dans le tableau de plongeur de la palanqués et met à jours ou insert les autres
		 * Pour la mise à jours des plongeurs, priviligier FicheSecuriteDao::update()
		 * @see FicheSecuriteDao::update()
		 * @param  Palanque $palanque
		 * @return Palanque renvoi la palanqué ou null
		 */
		public static function updatePlongeursFromPalanque(Palanque $palanque){
			//Supprime les plongeurs qui appartenait a la palanqué mais qui ne sont pas dans le tableau
			$arrayParam = array();
			$arrayParam[] = $palanque->getId();
			$query = "DELETE FROM db_plongeur WHERE id_palanque = ?";
			for($i = 0; $i < count($palanque->getPlongeurs()); $i++) {
				$query = $query." AND id_plongeur != ?";
				$arrayParam[] = $palanque->getPlongeurs()[$i]->getId();
			}
			$stmt = parent::getConnexion()->prepare($query);
			$stmt->execute($arrayParam);
			
			//Met a jours les plongeurs dans le tableau
			foreach ($palanque->getPlongeurs() as $plongeur) {
				if($plongeur->getId() != null)
					self::update($plongeur);
				else
					self::insert($plongeur);
			}
		}
		/**
		 * Supprime le plongeur de la base dont l'id est passé en parametre
		 * @param  int $id_plongeur id du plongeur à supprimer
		 * @return boolean  true si le plongeur à bien été supprimé, false sinon
		 */
		public static function delete($id_plongeur){
			$stmt = parent::getConnexion()->prepare("DELETE FROM db_plongeur WHERE id_plongeur = ?");
			return $stmt->execute([$id_plongeur]);
		}
		/* Private */
		/**
		 * Execute la requere $query avec les parametres optionnels contenus dans le tableau $param.
		 * Renvoi un tableau de PlongeurDao.
		 * @param  string $query
		 * @param  array $param
		 * @return array
		 */
		private static function getByQuery($query, $param = null){
			$stmt = parent::getConnexion()->prepare($query);
			if($stmt->execute($param) && $stmt->rowCount() > 0){
				$arrayResultat = array();
				while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
					$plongeur = new Plongeur($row['id_plongeur'], $row['version']);
					$plongeur->setIdFicheSecurite($row['id_fiche_securite']);
					$plongeur->setIdPalanque($row['id_palanque']);
					$plongeur->setNom($row['nom']);
					$plongeur->setPrenom($row['prenom']);
					$plongeur->setAptitudes(AptitudeDao::getByIds(Aptitude::aptitudesStringToAptitudesIdsArray($row['aptitudes'])));
					$plongeur->setTelephone($row['telephone']);
					$plongeur->setTelephoneUrgence($row['telephone_urgence']);
					$plongeur->setDateNaissance($row['date_naissance']);
					$plongeur->setProfondeurRealisee($row['profondeur_realisee']);
					$plongeur->setDureeRealisee($row['duree_realisee']);
					$arrayResultat[] = $plongeur;
				}
				return $arrayResultat;
			}
			else{
				return null;
			}
		}
	}
?>