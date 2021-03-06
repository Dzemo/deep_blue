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
		 * @param  int $id_palanquee 
		 * @return Palanque
		 */
		public static function getById($id_palanquee){
			$result = self::getByQuery("SELECT * FROM db_palanquee WHERE id_palanquee = ?", false, [$id_palanquee]);
			if($result != null && count($result) == 1)
				return $result[0];
			else
				return null;
		}
		/**
		 * Retourne un tableau contenant les palanqués de la fiche de sécurité d'id spécifié
		 * Le tableau est trié par ordre croissant de numéro de palanqué
		 * @param  int $id_fiche_securite 
                 * @param  boolean $avecDesactive Inclue les palanquées et plongeurs desactive
		 * @return array                    Tableau de palanqués
		 */
		public static function getByIdFicheSecurite($id_fiche_securite, $avecDesactive = false){
                    if($avecDesactive){
			return self::getByQuery("SELECT * FROM db_palanquee WHERE id_fiche_securite = ? ORDER BY numero ASC", true, [$id_fiche_securite]);
                    }
                    else{
			return self::getByQuery("SELECT * FROM db_palanquee WHERE id_fiche_securite = ? AND desactive = FALSE ORDER BY numero ASC", false, [$id_fiche_securite]);
                    }
		}
		/**
		 * Ajoute à la base la palanqué passé en parametre, et ses plongeur.
		 * Retourne la palanquee.
		 * Pour ajouter une palanquee d'une nouvelle fiche de sécurité, priviligier l'utilisation de FicheSecuriteDao::insert()
		 * @see FicheSecuriteDao::insert()
		 * @param  Palanque $palanquee 
		 * @return Palanque             
		 */
		public static function insert(Palanque $palanquee){
			if($palanquee == null || $palanquee->getIdFicheSecurite() == null)
                            return null;

			$palanquee->updateVersion();

			$stmt = parent::getConnexion()->prepare("INSERT INTO db_palanquee (id_fiche_securite, id_moniteur, numero, type_plonge, type_gaz, profondeur_prevue, duree_prevue, heure, profondeur_realisee_moniteur, duree_realisee_moniteur, version) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
			$result = $stmt->execute([$palanquee->getIdFicheSecurite(),
									($palanquee->getMoniteur() != null ? $palanquee->getMoniteur()->getId() : null),
									$palanquee->getNumero(),
									$palanquee->getTypePlonge(),
									$palanquee->getTypeGaz(),
									$palanquee->getProfondeurPrevue(),
									$palanquee->getDureePrevue(),
									$palanquee->getHeure(),
									$palanquee->getProfondeurRealiseeMoniteur(),
									$palanquee->getDureeRealiseeMoniteur(),
									$palanquee->getVersion()
								]);
			if($result){
				$palanquee->setId(parent::getConnexion()->lastInsertId());
                                
                                //Mise à jours des plongeurs
                                $arrayPlongeurs = $palanquee->getPlongeurs();
                                for($i = 0; $i < count($arrayPlongeurs) ; $i++){
                                        $arrayPlongeurs[$i]->setIdPalanque($palanquee->getId());
					$arrayPlongeurs[$i]->setIdFicheSecurite($palanquee->getIdFicheSecurite());
					$arrayPlongeurs[$i] = PlongeurDao::insert($arrayPlongeurs[$i]);
                                }
				$palanquee->setPlongeurs($arrayPlongeurs);
                                
				return $palanquee;
			}
			else
				return null;
		}
		/**
		 * Met à jorus une palanqué et ses plongeurs
		 * Pour la mise à jour des palanqués, priviligier l'utilisation de FicheSecuriteDao::update()
		 * @see FicheSecuriteDao::update()
		 * @param  Palanque $palanquee
		 * @return Palanque la palanqué mis à jours ou null en cas d'erreur         
		 */
		public static function update(Palanque $palanquee){
                                        
			if($palanquee == null || $palanquee->getId() == null ||
				$palanquee->getIdFicheSecurite() == null)
				return null;		

			$palanquee->updateVersion();
			$stmt = parent::getConnexion()->prepare("UPDATE db_palanquee SET id_fiche_securite = ?, id_moniteur = ?, numero = ?, type_plonge = ?, type_gaz = ?, profondeur_prevue = ?, duree_prevue = ?, heure = ?, profondeur_realisee_moniteur = ?, duree_realisee_moniteur = ?, version = ?, desactive = ? WHERE id_palanquee = ?");
			$result = $stmt->execute([$palanquee->getIdFicheSecurite(),
									($palanquee->getMoniteur() != null ? $palanquee->getMoniteur()->getId() : null),
									$palanquee->getNumero(),
									$palanquee->getTypePlonge(),
									$palanquee->getTypeGaz(),
									$palanquee->getProfondeurPrevue(),
									$palanquee->getDureePrevue(),
									$palanquee->getHeure(),
									$palanquee->getProfondeurRealiseeMoniteur(),
									$palanquee->getDureeRealiseeMoniteur(),
									$palanquee->getVersion(),
                                                                        $palanquee->getDesactive(),
									$palanquee->getId()
								]);
			if($result){
				return PlongeurDao::updatePlongeursFromPalanque($palanquee);
			}
			else
				return null;
		}
		/**
		 * Supprime une palanqué et ses plongeurs
		 * @param  Palanque $palanquee
		 * @return true ou null en cas d'erreur        
		 */
		public static function delete(Palanque $palanquee){
			// On vérifie l'existance de la palanquée
			if($palanquee == null || $palanquee->getId() == null ||
				$palanquee->getIdFicheSecurite() == null)
				return null;	

                        
			// Suppression des plongeurs
			for($i = 0; $i < count($palanquee->getPlongeurs()); $i++) {
				PlongeurDao::delete($palanquee->getPlongeurs()[$i]->getId());
			}

                        
                        $palanquee->updateVersion();
			// Supprimer la palanquée
			$stmt = parent::getConnexion()->prepare("UPDATE db_palanquee SET desactive = TRUE, version = ? WHERE id_palanquee = ?");
			return $stmt->execute([$palanquee->getVersion(), $palanquee->getId()]);
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
                        
			//Suppression des palanquées qui ont été supprimées de la fiche
			$arrayParam = array();
			$arrayParam[] = $ficheSecurite->getId();
			$query = "UPDATE db_palanquee SET desactive = TRUE WHERE id_fiche_securite = ? ";
			for($i = 0; $i < count($ficheSecurite->getPalanques()); $i++) {
				$query = $query." AND id_palanquee != ?";
				$arrayParam[] = $ficheSecurite->getPalanques()[$i]->getId();
			}
			$stmt = parent::getConnexion()->prepare($query);
			$stmt->execute($arrayParam);
			
			//Met a jours les palanquee dans le tableau
                        $arrayPalanquees = $ficheSecurite->getPalanques();
                        for($i = 0; $i < count($arrayPalanquees) ; $i++){
                            if($arrayPalanquees[$i]->getId() != null && $arrayPalanquees[$i]->getId() > 0){
                                $arrayPalanquees[$i] = self::update($arrayPalanquees[$i]);
                            }
                            else{
                                $arrayPalanquees[$i] = self::insert($arrayPalanquees[$i]);
                            }
                        }
                        $ficheSecurite->setPalanques($arrayPalanquees);
                        
			return $ficheSecurite;
		}
		/* Private */
		/**
		 * Execute la requere $query avec les parametres optionnels contenus dans le tableau $param.
		 * Renvoi un tableau de Palanque.
		 * @param  string $query
                 * @param  boolean $avecDesactive
		 * @param  array $param
		 * @return array
		 */
		private static function getByQuery($query, $avecDesactive = false, $param = null){
			$stmt = parent::getConnexion()->prepare($query);
			if($stmt->execute($param) && $stmt->rowCount() > 0){
				$arrayResultat = array();
				while($row = $stmt->fetch(PDO::FETCH_ASSOC)){

					$palanquee = new Palanque($row['id_palanquee'], $row['version']);
					$palanquee->setIdFicheSecurite($row['id_fiche_securite']);
					$palanquee->setNumero($row['numero']);
					$palanquee->setTypeGaz($row['type_gaz']);
					$palanquee->setTypePlonge($row['type_plonge']);
					$palanquee->setProfondeurPrevue($row['profondeur_prevue']);
					$palanquee->setDureePrevue($row['duree_prevue']);
					$palanquee->setHeure($row['heure']);
					$palanquee->setPlongeurs(PlongeurDao::getByIdPalanque($palanquee->getId(), $avecDesactive));
					$palanquee->setProfondeurRealiseeMoniteur($row['profondeur_realisee_moniteur']);
					$palanquee->setDureeRealiseeMoniteur($row['duree_realisee_moniteur']);
                                        $palanquee->setDesactive($row['desactive']);
					//Récupération du moniteur
					if($row['id_moniteur'] != null){
						$moniteur = MoniteurDao::getById($row['id_moniteur']);
						$palanquee->setMoniteur($moniteur);
					}
                                        
					$arrayResultat[] = $palanquee;
				}
				return $arrayResultat;
			}
			else{
				return null;
			}
		}
	}
?>