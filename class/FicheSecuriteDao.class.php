<?php
	/**
	 * Ficher contenant la classe FicheSecuriteDao
	 * @author Raphaël Bideau - 3iL
	 * @package Dao
	 */
	
	/**
	 * Classe permettant d'interagir avec la base de données concernant les FicheSecurite
	 * Dans le cas d'une fiche de sécurité, le directeur de plongé est un utilisateur
	 */
	class FicheSecuriteDao extends Dao {
		/* Public */
		/**
		 * Renvoi un tableau contenant toute les fiches de sécurité dans la base trié par date décroissant
		 * @return array
		 */
		public static function getAll(){
			return self::getByQuery("SELECT * FROM db_fiche_securite ORDER BY timestamp DESC WHERE disponible = 1");
		}

		/**
		 * Renvoi toutes les fiches de sécurité dont la version est superieur à celle spécifié
		 * @param  int $versionMax 
		 * @return array             
		 */
		public static function getFromVersion($versionMax){
			return self::getByQuery("SELECT * FROM db_fiche_securite WHERE disponible = 1 AND version > ? ",[$versionMax] );
		}
		
		/**
		 * Renvoi un tableau contenant toute les fiches de sécurité dans la base trié par date décroissant
		 * dont l'état est $state
		 * @param string $etat
		 * @return array
		 */
		public static function getAllByEtat($etat){
			return self::getByQuery("SELECT * FROM db_fiche_securite WHERE disponible = 1 AND etat = ? ORDER BY timestamp DESC", [$etat]);
		}

		public static function getAllNonArchivee(){
			return self::getByQuery("SELECT * FROM db_fiche_securite WHERE disponible = 1 AND etat != '".FicheSecurite::etatArchive."' ORDER BY timestamp DESC");
		}

		/**
		 * Recherche une fiche de sécurité par id
		 * @param  int $id
		 * @return Objet FicheSecurite contenant des Objets Palanquées - Plongeurs - Embarcation
		 */
		public static function getbyId($id){
			$result = self::getByQuery("SELECT * FROM db_fiche_securite WHERE disponible = 1 AND id_fiche_securite = ?", [$id]);
			if($result != null && count($result) == 1)
				return $result[0];
			else
				return null;
		}

		/**
		 * Récupère les fiches dont la version est supérieur à ficheSecuriteMaxVersion et le timestamps compris entre minTimesptas et maxTimestamps. Si un idDirecteurPlongee est spécifié, alors ne récupère que les fiches dont le directeur de plongee correspond à cet id
		 * Utilisé pour envoyé les nouvelles fiches lors de la synchronisation
		 * @param  int $ficheSecuriteMaxVersion (strictement superieur pour ficheSecuriteMaxVersion > 0 ou superieur égal pour ficheSecuriteMaxVersion = 0)
		 * @param  int $idDirecteurPlongee      
		 * @param  int $minTimestamps           
		 * @param  int $maxTimestamps           
		 * @return array                          
		 */
		public static function getFromVersionIdDpTimestamps($ficheSecuriteMaxVersion, $idDirecteurPlongee, $minTimestamps, $maxTimestamps){
			//Quand ficheSecuriteMaxVersion vaut zero on veut inclure les version local à 0 car il s'agit de la première synchronisation pour une application
			$query = "SELECT * FROM db_fiche_securite WHERE disponible = 1 AND etat != '".FicheSecurite::etatArchive."' AND version ".($ficheSecuriteMaxVersion == 0 ? ">=" : ">")." ? AND timestamp > ? AND timestamp < ?";
			$params = [$ficheSecuriteMaxVersion, $minTimestamps, $maxTimestamps];

			if($idDirecteurPlongee != null){
				$query .= " AND id_directeur_plonge = ?";
				$params[] = $idDirecteurPlongee;
			}

			$result = self::getByQuery($query, $params);
			return $result;
		}

		/**
		 * Enregistre une fiche de sécurite en base et la renvoi. Renvoi null en cas d'erreur. 
		 * Si la fiche a été créer sur l'application mobile et est maintenant synchroniser vers le pc, le login sera alors null.
		 * 
		 * Créer également les palanqués et les plongeurs de la fiche de sécurité. C'est la méthode à priviligier pour
		 * enregistrer les informations d'une nouvelle fiche de sécurité
		 * Créer le site si il n'est pas enregistré
		 * 
		 * @param  FicheSecurite $ficheSecurite
		 * @param  boolean $fromSynchronisation Indique si la fiche est créer lors d'une syncronisation
		 * @return FicheSecurite
		 */
		public static function insert(FicheSecurite $ficheSecurite, $fromSynchronisation = false){
			if($ficheSecurite == null ||
				$ficheSecurite->getDirecteurPlonge() == null || $ficheSecurite->getDirecteurPlonge()->getId() == null || 
				$ficheSecurite->getTimestamp() == null ||
				$ficheSecurite->getEtat() == null || strlen($ficheSecurite->getEtat()) == 0 ||
				$ficheSecurite->getDisponible() == 0)
				return null;

			//Enregistrement du site si il existe et n'a pas d'id
			if($ficheSecurite->getSite() != null && $ficheSecurite->getSite()->getId() == null)
				$ficheSecurite->setSite(SiteDao::insert($ficheSecurite->getSite()));

			$ficheSecurite->updateVersion();

			$stmt = parent::getConnexion()->prepare("INSERT INTO db_fiche_securite (id_embarcation, id_directeur_plonge, timestamp, id_site, etat, version, disponible) VALUES (?, ?, ?, ?, ?, ?, ?)");
			$result = $stmt->execute([$ficheSecurite->getEmbarcation()->getId(),
							$ficheSecurite->getDirecteurPlonge()->getId(),
							$ficheSecurite->getTimestamp(),
							$ficheSecurite->getSite() != null ? $ficheSecurite->getSite()->getId() : null,
							$ficheSecurite->getEtat(),
							$ficheSecurite->getVersion(),
							$ficheSecurite->getDisponible()
							]);

			if($result){
				$ficheSecurite->setId(parent::getConnexion()->lastInsertId());
				
				foreach ($ficheSecurite->getPalanques() as $palanque) {
					if($palanque != null){

					$palanque->setIdFicheSecurite($ficheSecurite->getId());
					PalanqueDao::insert($palanque);
					}					
				}
				return $ficheSecurite;
			}
			else
				return null;
		}

		/**
		 * Met à jour la FicheSecurite passé en parametre et met à jour sa version puis la renvoi ou
		 * renvoi null en cas d'erreur. Met également à jours les palanqués et plongeurs de la fiche de sécurité.
		 * C'est la méthode à priviligier pour mettre à jours les informations d'une fiche de sécurité.
		 * @param  FicheSecurite $ficheSecurite
		 * @return FicheSecurite
		 */
		public static function update(FicheSecurite $ficheSecurite){
			if($ficheSecurite == null || $ficheSecurite->getId() == null ||
				$ficheSecurite->getDirecteurPlonge() == null || $ficheSecurite->getDirecteurPlonge()->getId() == null || 
				$ficheSecurite->getTimestamp() == null ||
				$ficheSecurite->getEtat() == null || strlen($ficheSecurite->getEtat()) == 0 ||
				$ficheSecurite->getVersion() === null)
				return null;

			//Enregistrement du site si il existe et n'a pas d'id
			if($ficheSecurite->getSite() != null && $ficheSecurite->getSite()->getId() == null)
				$ficheSecurite->setSite(SiteDao::insert($ficheSecurite->getSite()));

			$ficheSecurite->updateVersion();

			$stmt = parent::getConnexion()->prepare("UPDATE db_fiche_securite SET id_embarcation = ?, id_directeur_plonge = ?, timestamp = ?, id_site = ?, etat = ?, version = ?, disponible = ? WHERE id_fiche_securite = ?");
			$result = $stmt->execute([$ficheSecurite->getEmbarcation()->getId(),
							$ficheSecurite->getDirecteurPlonge()->getId(),
							$ficheSecurite->getTimestamp(),
							$ficheSecurite->getSite() != null ? $ficheSecurite->getSite()->getId() : null,
							$ficheSecurite->getEtat(),
							$ficheSecurite->getVersion(),
							$ficheSecurite->getDisponible(),
							$ficheSecurite->getId()
							]);
			if($result){
				PalanqueDao::updatePalanquesFromFicheSecurite($ficheSecurite);
				return $ficheSecurite;
			}
			else
				return null;
		}

		/**
		 * Met à jour uniquement l'état de la FicheSecurite passé en parametre et met à jour sa version puis la renvoi ou
		 * renvoi null en cas d'erreur.
		 * @param  FicheSecurite $ficheSecurite
		 * @return FicheSecurite
		 */
		public static function updateEtat(FicheSecurite $ficheSecurite, $etat){
			if($ficheSecurite == null || $ficheSecurite->getId() == null ||
				$etat == null || strlen($etat) == 0 ||
				$ficheSecurite->getVersion() === null)
				return null;


			$ficheSecurite->updateVersion();
			$ficheSecurite->setEtat($etat);

			$stmt = parent::getConnexion()->prepare("UPDATE db_fiche_securite SET etat = ?, version = ? WHERE id_fiche_securite = ?");
			$result = $stmt->execute([
							$ficheSecurite->getEtat(),
							$ficheSecurite->getVersion(),
							$ficheSecurite->getId()
							]);
			if($result){
				return $ficheSecurite;
			}
			else
				return null;
		}

		/**
		 * Supprime la FicheSecurite passé en parametre ainsi que l'ensemble de ses contenus ou
		 * renvoi null en cas d'erreur. Supprime également les palanqués et plongeurs de la fiche de sécurité.
		 * C'est la méthode à priviligier pour supprimer une fiche de sécurité.
		 * @param  FicheSecurite $ficheSecurite
		 * @return FicheSecurite
		 */
		public static function delete(FicheSecurite $ficheSecurite){
			// On vérifie l'existance de la fiche
			if($ficheSecurite == null || $ficheSecurite->getId() == null)
				return null;

			// Passer la fiche en indisponible
			$stmt = parent::getConnexion()->prepare("UPDATE db_fiche_securite SET disponible = 0 WHERE id_fiche_securite = ?");
			return $stmt->execute([$ficheSecurite->getId()]);
		}

		/* Private */

		/**
		 * Execute la requere $query avec les parametres optionnels contenus dans le tableau $param.
		 * Renvoi un tableau de FicheSecurite.
		 * @param  string $query
		 * @param  array $param
		 * @return array
		 */
		private static function getByQuery($query, $param = null){
			$stmt = parent::getConnexion()->prepare($query);
			if($stmt->execute($param) && $stmt->rowCount() > 0){
				$arrayResultat = array();
				
				while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
					
					//initialisation des objets
					$ficheSecurite = new FicheSecurite($row['id_fiche_securite'],$row['version']);
					$ficheSecurite->setEmbarcation(EmbarcationDao::getById($row['id_embarcation']));
					$ficheSecurite->setDirecteurPlonge(MoniteurDao::getById($row['id_directeur_plonge']));
					$ficheSecurite->setPalanques(PalanqueDao::getByIdFicheSecurite($ficheSecurite->getId()));
					$ficheSecurite->setTimestamp($row['timestamp']);
					$ficheSecurite->setSite(SiteDao::getById($row['id_site']));
					$ficheSecurite->setEtat($row['etat']);
					$ficheSecurite->setDisponible($row['disponible']);
					$arrayResultat[] = $ficheSecurite;
					
				}
				
				return $arrayResultat;
			}
			else{
				return null;
			}
		}
	}
?>