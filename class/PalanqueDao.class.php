<?php
	/**
	 * Ficher contenant la classe PalanqueDao
	 * @author Raphaël Bideau - 3iL
	 * @package Dao
	 */
	/**
	 * Classe permettant d'interagir avec la base de données concernant les Palanques
	 */
	class PalanqueDao extends Dao {
		/* Public */
		/**
		 * Retourne la palanqué d'id spécifié
		 * @param  int $id_palanque 
		 * @return Palanque
		 */
		public static function getById($id_palanque){
			$result = self::getByQuery("SELECT * FROM db_palanque WHERE id_palanque = ?", [$id_palanque]);
			if($result != null && count($result) == 1)
				return $result[0];
			else
				return null;
		}
		/**
		 * Retourne un tableau contenant les palanqués de la fiche de sécurité d'id spécifié
		 * Le tableau est trié par ordre croissant de numéro de palanqué
		 * @param  int $id_fiche_securite 
		 * @return array                    Tableau de palanqués
		 */
		public static function getByIdFicheSecurite($id_fiche_securite){
			return self::getByQuery("SELECT * FROM db_palanque WHERE id_fiche_securite = ? ORDER BY numero ASC", [$id_fiche_securite]);
		}
		/**
		 * Ajoute à la base la palanqué passé en parametre, et ses plongeur.
		 * Retourne la palanque.
		 * Pour ajouter une palanque d'une nouvelle fiche de sécurité, priviligier l'utilisation de FicheSecuriteDao::insert()
		 * @see FicheSecuriteDao::insert()
		 * @param  Palanque $palanque 
		 * @return Palanque             
		 */
		public static function insert(Palanque $palanque){
			if($palanque == null ||
				$palanque->getIdFicheSecurite() == null ||
				$palanque->getNumero() == null ||
				$palanque->getTypePlonge() == null || strlen($palanque->getTypePlonge()) == 0 ||
				$palanque->getTypeGaz() == null || strlen($palanque->getTypeGaz()) == 0 
				)
				return null;
			$stmt = parent::getConnexion()->prepare("INSERT INTO db_palanque (id_fiche_securite, id_moniteur, numero, type_plonge, type_gaz, profondeur_prevue, profondeur_realisee, duree_prevue, duree_realisee) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
			$result = $stmt->execute([$palanque->getIdFicheSecurite(),
									($palanque->getMoniteur() != null ? $palanque->getMoniteur()->getId() : null),
									$palanque->getNumero(),
									$palanque->getTypePlonge(),
									$palanque->getTypeGaz(),
									$palanque->getProfondeurPrevue(),
									$palanque->getProfondeurRealisee(),
									$palanque->getDureePrevue(),
									$palanque->getDureeRealisee()
								]);
			if($result){
				$palanque->setId(parent::getConnexion()->lastInsertId());
				foreach ($palanque->getPlongeurs() as $plongeur) {
					$plongeur->setIdPalanque($palanque->getId());
					$plongeur->setIdFicheSecurite($palanque->getIdFicheSecurite());
					PlongeurDao::insert($plongeur);
				}
				return $palanque;
			}
			else
				return null;
		}
		/**
		 * Met à jorus une palanqué et ses plongeurs
		 * Pour la mise à jour des palanqués, priviligier l'utilisation de FicheSecuriteDao::update()
		 * @see FicheSecuriteDao::update()
		 * @param  Palanque $palanque
		 * @return Palanque la palanqué mis à jours ou null en cas d'erreur         
		 */
		public static function update(Palanque $palanque){
			if($palanque == null || $palanque->getId() == null ||
				$palanque->getIdFicheSecurite() == null ||
				$palanque->getNumero() == null ||
				$palanque->getTypePlonge() == null || strlen($palanque->getTypePlonge()) == 0 ||
				$palanque->getTypeGaz() == null || strlen($palanque->getTypeGaz()) == 0 ||
				$palanque->getVersion() === null
				)
				return null;				
	
			$palanque->updateVersion();
			$stmt = parent::getConnexion()->prepare("UPDATE db_palanque SET id_fiche_securite = ?, id_moniteur = ?, numero = ?, type_plonge = ?, type_gaz = ?, profondeur_prevue = ?, profondeur_realisee = ?, duree_prevue = ?, duree_realisee = ?, version = ? WHERE id_palanque = ?");
			$result = $stmt->execute([$palanque->getIdFicheSecurite(),
									($palanque->getMoniteur() != null ? $palanque->getMoniteur()->getId() : null),
									$palanque->getNumero(),
									$palanque->getTypePlonge(),
									$palanque->getTypeGaz(),
									$palanque->getProfondeurPrevue(),
									$palanque->getProfondeurRealisee(),
									$palanque->getDureePrevue(),
									$palanque->getDureeRealisee(),
									$palanque->getVersion(),
									$palanque->getId()
								]);
			if($result){
				PlongeurDao::updatePlongeursFromPalanque($palanque);
				return $palanque;
			}
			else
				return null;
		}
		/**
		 * Met à jours les palanqué de la fiche de sécurité passé en parametre:
		 * Supprime les palanqué qui appartenait a la fiche de sécurité mais qui ne sont plus dans le tableau de palanqués de la fiche de sécurité et met à jours ou insert les autres
		 * Pour la mise à jour des palanqués, priviligier l'utilisation de FicheSecuriteDao::update()
		 * @see FicheSecuriteDao::update()
		 * @param  FicheSecurite $ficheSecurite 
		 * @return FicheSecurite Renvoi la fiche de sécurité ou null
		 */
		public static function updatePalanquesFromFicheSecurite(FicheSecurite $ficheSecurite){
			if(count($ficheSecurite->getPalanques()) == 0)
				return;
			
			//Supprime les plongeurs qui appartenait a la palanqué mais qui ne sont pas dans le tableau
			$arrayParam = array();
			$arrayParam[] = $ficheSecurite->getId();
			$arrayParam[] = $ficheSecurite->getPalanques()[0]->getId();
			$query = "DELETE FROM db_palanque WHERE id_fiche_securite = ? AND id_palanque != ?";
			for($i = 1; $i < count($ficheSecurite->getPalanques()); $i++) {
				$query = $query." AND id_palanque != ?";
				$arrayParam[] = $ficheSecurite->getPalanques()[$i]->getId();
			}
			$stmt = parent::getConnexion()->prepare($query);
			$stmt->execute($arrayParam);
			
			//Met a jours les plongeurs dans le tableau
			foreach ($ficheSecurite->getPalanques() as $palanque) {
				if($palanque->getId() != null)
					self::update($palanque);
				else
					self::insert($palanque);
			}
			return $ficheSecurite;
		}
		/* Private */
		/**
		 * Execute la requere $query avec les parametres optionnels contenus dans le tableau $param.
		 * Renvoi un tableau de Palanque.
		 * @param  string $query
		 * @param  array $param
		 * @return array
		 */
		private static function getByQuery($query, $param = null){
			$stmt = parent::getConnexion()->prepare($query);
			if($stmt->execute($param) && $stmt->rowCount() > 0){
				$arrayResultat = array();
				while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
					$palanque = new Palanque($row['id_palanque'], $row['version']);
					$palanque->setIdFicheSecurite($row['id_fiche_securite']);
					$palanque->setNumero($row['numero']);
					$palanque->setTypeGaz($row['type_gaz']);
					$palanque->setTypePlonge($row['type_plonge']);
					$palanque->setProfondeurPrevue($row['profondeur_prevue']);
					$palanque->setProfondeurRealisee($row['profondeur_realisee']);
					$palanque->setDureePrevue($row['duree_prevue']);
					$palanque->setDureeRealisee($row['duree_realisee']);
					$palanque->setPlongeurs(PlongeurDao::getByIdPalanque($palanque->getId()));
					//Récupération du moniteur
					if($row['id_moniteur'] != null){
						$moniteur = MoniteurDao::getById($row['id_moniteur']);
						$palanque->setMoniteur($moniteur);
					}
					$arrayResultat[] = $palanque;
				}
				return $arrayResultat;
			}
			else{
				return null;
			}
		}
	}
?>