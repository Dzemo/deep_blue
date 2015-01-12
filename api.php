<?php
	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."config.php");
	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."classloader.php");

	//Si pas de données json, on repond 'no-data'
	if(!isset($_POST['data'])){		
		printResponse("no-data");
		die();
	}
	
	//Tableau contenant les données envoyées par l'application
	$arrayRequest = getRequestData();

	//Tableau contenant les données envoyées en réponse
	$arrayResponse = array(	'utilisateurs' => null,
							'fichesSecurite' => null,
							'aptitudes' => null,
							'embarcations' => null,
							'sites' => null,
							'moniteurs' => null,
							'fichesOk' => null,
							'historiquesOk' => null
							);
	
	//////////////////////////////////////////////////
	//Traitement de la version max des utilisateur //
	//////////////////////////////////////////////////
	if(isset($arrayRequest['utilisateurMaxVersion'])){
		$utilisateurMaxVersion = intval($arrayRequest['utilisateurMaxVersion']);
		$utilisateurs = UtilisateurDao::getFromVersion($utilisateurMaxVersion);
		$arrayResponse['utilisateurs'] = $utilisateurs;
	}

	///////////////////////////////////////////////////
	//Récupération de l'utilisateur qui synchronise //
	///////////////////////////////////////////////////
	$utilisateurSynch = null;
	if(isset($arrayRequest['utilisateurLogin']) && strlen($arrayRequest['utilisateurLogin']) > 0){
		$utilisateurSynch = UtilisateurDao::getbyLogin($arrayRequest['utilisateurLogin']);

		//Ajout d'un historique pour indiquée que l'utilisateur à synchroniser son appareil
		$historique = new Historique($utilisateurSynch->getLogin(), time(), null);
		$historique->setSource(Historique::sourceSynchronize);
		$historique->setCommentaire("Synchronisation de l'appareil");
		$historique = HistoriqueDao::insert($historique);
	}

	//Si l'utilisateur est null (existe pas) ou est désactivé, on arrete la
	if($utilisateurSynch == null || !$utilisateurSynch->getActif()){
		printResponse(json_encode($arrayResponse));
		die();
	}

	///////////////////////////////////////////////////////////////////////////////
	//Enregistrement des fiches de sécurité si présente et de leurs historiques //
	///////////////////////////////////////////////////////////////////////////////
	$arrayResponseFicheHistorique = enregistreFichesEtHistoriqueAvecJson($arrayRequest, $utilisateurSynch);
	$arrayResponse['fichesOk'] = $arrayResponseFicheHistorique['fichesOk'];
	$arrayResponse['historiquesOk'] = $arrayResponseFicheHistorique['historiquesOk'];


	/////////////////////////////////////////////////////////////
	// Envoi des aptitudes, embarcations, moniteurs et sites  //
	/////////////////////////////////////////////////////////////

	//Envoi des nouvelles aptitudes (aptitudeMaxVersion)
	if(isset($arrayRequest['aptitudeMaxVersion'])){
		$aptitudeMaxVersion = intval($arrayRequest['aptitudeMaxVersion']);
		$aptitudes = AptitudeDao::getFromVersion($aptitudeMaxVersion);
		//Comme le tableau des aptitudes renvoyé par le dao est indexé par leur id, il faut le renvoyer dans un tableau non indexé
		if($aptitudes != null){
			$aptitudesResponse = array();
			foreach ($aptitudes as $aptitude) {
				$aptitudesResponse[] = $aptitude;
			}
			$arrayResponse['aptitudes'] = $aptitudesResponse;
		}
	}

	//Envoi des nouvelles embarcations (embarcationMaxVersion)
	if(isset($arrayRequest['embarcationMaxVersion'])){
		$embarcationMaxVersion = intval($arrayRequest['embarcationMaxVersion']);
		$arrayResponse['embarcations']  = EmbarcationDao::getFromVersion($embarcationMaxVersion);
	}

	//Envoi des nouveaux moniteurs (moniteurMaxVersion)
	if(isset($arrayRequest['moniteurMaxVersion'])){
		$moniteurMaxVersion = intval($arrayRequest['moniteurMaxVersion']);
		$arrayResponse['moniteurs'] = MoniteurDao::getFromVersion($moniteurMaxVersion);
	}

	//Envoi des nouveaux sites (siteMaxVersion)
	if(isset($arrayRequest['siteMaxVersion'])){
		$siteMaxVersion = intval($arrayRequest['siteMaxVersion']);
		$sites = SiteDao::getFromVersion($siteMaxVersion);
		//Comme le tableau des sites renvoyé par le dao est indexé par leur id, il faut le renvoyer dans un tableau non indexé
		if($sites != null){
			$sitesResponse = array();
			foreach ($sites as $site) {
				$sitesResponse[] = $site;
			}
			$arrayResponse['sites'] = $sitesResponse;
		}
	}	

	///////////////////////////////////////////////////////////////
	//Récupération et envoie des nouvelles fiches de sécuritées //
	///////////////////////////////////////////////////////////////

	//Récupération des paramètres de récupération des fiches (synchRetrieveLength)
	if(isset($arrayRequest['synchRetrieveLength'])){
		$synchRetrieveLength = intval($arrayRequest['synchRetrieveLength']);
	} else{
		$synchRetrieveLength = 0;//TODO config
	}
	//Calcul du max timestamps pour la récupération des fiches en fonction de synchRetrieveLength
	$timestampsHeureActuel = time() % (24*60*60);
	$timestampsJourActuel = time() - $timestampsHeureActuel;
	$minTimestamps = $timestampsJourActuel;
	$maxTimestamps = $timestampsJourActuel + (24*60*60*($synchRetrieveLength+1));

	//Récupération des paramètres de récupération des fiches (synchRetrieveTypeAll)
	if(isset($arrayRequest['synchRetrieveTypeAll'])){
		$synchRetrieveTypeAll = intval($arrayRequest['synchRetrieveTypeAll']);
	} else{
		$synchRetrieveTypeAll = false;//TODO config
	}
	//Récupération du moniteur associé à l'utilisateur courant si il existe et synchRetrieveTypeAll = false
	$idMoniteurAssocie = null;
	if($synchRetrieveTypeAll == false && $utilisateurSynch->getMoniteurAssocie() != null){
		$idMoniteurAssocie = $utilisateurSynch->getMoniteurAssocie()->getId();
	}

	//Récupération des fiches (ficheSecuriteMaxVersion) et envoi
	if(isset($arrayRequest['ficheSecuriteMaxVersion'])){
		$ficheSecuriteMaxVersion = intval($arrayRequest['ficheSecuriteMaxVersion']);
		$arrayResponse['fichesSecurite'] = FicheSecuriteDao::getFromVersionIdDpTimestamps($ficheSecuriteMaxVersion, $idMoniteurAssocie, $minTimestamps, $maxTimestamps);
		if($arrayResponse['fichesSecurite'] != null){
			foreach ($arrayResponse['fichesSecurite'] as $ficheSecurite) {
				//Met à jours l'état de la fiche et la renvoi
				$ficheSecurite = FicheSecuriteDao::updateEtat($ficheSecurite, FicheSecurite::etatSynchronise);

				//Enregistrement de l'historique de syncrhonisation de la fiche
				$historique = new Historique($utilisateurSynch->getLogin(), time(), $ficheSecurite->getId());
				$historique->setSource(Historique::sourceSynchronize);
				$historique->setCommentaire("Synchronisation de la fiche");
				$historique = HistoriqueDao::insert($historique);
			}
		}
	}

	/////////////////////////
	//Envoi de la réponse //
	/////////////////////////
	printResponse(json_encode($arrayResponse));

	///////////////////////////////
	//Fin de la synchronisation //
	///////////////////////////////

?>

<?php

function getRequestData(){
	return json_decode($_POST['data'], true);
}

function printResponse($response){
	echo $response;
}

/**
 * Récupère les fiches et les historiques dans le tableau contenant la request json et les enregistres
 * Renvoi un tableau contenant de boolean ['fichesOk', 'historiquesOk'] indiquant le succès de l'enregistrement
 * @param  array $arrayRequest     
 * @param  Utilisateur $utilisateurSynch 
 * @return array
 */
function enregistreFichesEtHistoriqueAvecJson($arrayRequest, $utilisateurSynch){

	//Enregistre le résultat de la synchronisation des fiches et historiques, retourné a la fin
	$arrayResponseFicheHistorique = array('fichesOk' => array(),	'historiquesOk' => array());

	//Tableau mappant les id des fiches local avec l'id json, pour récupérer les historiques
	$arrayMapIdsFiche = array();

	if($utilisateurSynch == null)
		return $arrayResponseFicheHistorique;

	//Enregistrement des fiches de sécurité si présente et de leurs historiques
	if(isset($arrayRequest['fichesSecuriteValidees'])){
		$arrayFichesJson = $arrayRequest['fichesSecuriteValidees'];

		for($i = 0; $i < count($arrayFichesJson) ; $i++){
			$ficheJson = $arrayFichesJson[$i];
			$erreurRecuperationFiche = "";

			//Récupération de l'id
			if(isset($ficheJson['idWeb']) && intval(isset($ficheJson['idWeb'])) > 0){
				$idFiche = intval($ficheJson['idWeb']);
			} else{
				$idFiche = -1;
			}

			$ficheSecurite = new FicheSecurite($idFiche, $ficheJson['version']);

			//Récupération de l'embarcation
			if(isset($ficheJson['embarcation']) && isset($ficheJson['embarcation']['idWeb'])){
				$ficheSecurite->setEmbarcation(EmbarcationDao::getById($ficheJson['embarcation']['idWeb']));
			} else{
				$erreurRecuperationFiche .= ";embarcation absente";
			}

			//Récupération du directeur de plongée
			if(isset($ficheJson['directeurPlonge']) && isset($ficheJson['directeurPlonge']['idWeb'])){
				$ficheSecurite->setDirecteurPlonge(MoniteurDao::getById($ficheJson['directeurPlonge']['idWeb']));
			} else{
				$erreurRecuperationFiche .= ";directeurPlonge absente";
			}

			//Récupération des palanquees
			if(isset($ficheJson['palanquees'])){
				$palanquees = array();
				for($j = 0; $j < count($ficheJson['palanquees']); $j++){
					$palanqueeJson = $ficheJson['palanquees'][$j];

					//Récupération de l'id
					if(isset($palanqueeJson['idWeb']) && intval(isset($palanqueeJson['idWeb'])) > 0){
						$idPalanquee = intval($palanqueeJson['idWeb']);
					} else{
						$idPalanquee = -1;
					}

					$palanquee = new Palanque($idPalanquee, $palanqueeJson['version']);
					$palanquee->setIdFicheSecurite($ficheSecurite->getId());

					//Récupération du numéro
					if(isset($palanqueeJson['numero'])){
						$palanquee->setNumero($palanqueeJson['numero']);
					} else{
						$erreurRecuperationFiche .= ";numero de palanquee absent";
					}

					//Récupération du type de gaz
					if(isset($palanqueeJson['typeGaz'])){
						$palanquee->setTypeGaz($palanqueeJson['typeGaz']);
					} else{
						$erreurRecuperationFiche .= ";typeGaz absent pour la palanquee numero ".$palanquee->getNumero();
					}

					//Récupération du type de plongée
					if(isset($palanqueeJson['typePlonge'])){
						$palanquee->setTypePlonge($palanqueeJson['typePlonge']);
					} else{
						$erreurRecuperationFiche .= ";typePlonge absent pour la palanquee numero ".$palanquee->getNumero();
					}

					//Récupération de la profondeur prévue
					if(isset($palanqueeJson['profondeurPrevue'])){
						$palanquee->setProfondeurPrevue($palanqueeJson['profondeurPrevue']);
					} else{
						$erreurRecuperationFiche .= ";profondeurPrevue absent pour la palanquee numero ".$palanquee->getNumero();
					}

					//Récupération de la durée prévue
					if(isset($palanqueeJson['dureePrevue'])){
						$palanquee->setDureePrevue($palanqueeJson['dureePrevue']);
					} else{
						$erreurRecuperationFiche .= ";dureePrevue absent pour la palanquee numero ".$palanquee->getNumero();
					}

					//Récupération de l'heure'
					if(isset($palanqueeJson['heure'])){
						$palanquee->setHeure($palanqueeJson['heure']);
					} else{
						$erreurRecuperationFiche .= ";heure absent pour la palanquee numero ".$palanquee->getNumero();
					}

					//Récupération de la profondeur réalisée par le moniteur
					if(isset($palanqueeJson['profondeurRealiseeMoniteur'])){
						$palanquee->setProfondeurRealiseeMoniteur($palanqueeJson['profondeurRealiseeMoniteur']);
					} else{
						$erreurRecuperationFiche .= ";profondeurRealiseeMoniteur absent pour la palanquee numero ".$palanquee->getNumero();
					}

					//Récupération de la durée réalisée par le moniteur
					if(isset($palanqueeJson['dureeRealiseeMoniteur'])){
						$palanquee->setDureeRealiseeMoniteur($palanqueeJson['dureeRealiseeMoniteur']);
					} else{
						$erreurRecuperationFiche .= ";dureeRealiseeMoniteur absent pour la palanquee numero ".$palanquee->getNumero();
					}

					//Récupération des plongeurs
					if(isset($palanqueeJson['plongeurs'])){
						$plongeurs = array();
						for($k = 0; $k < count($palanqueeJson['plongeurs']); $k++){
							$plongeurJson = $palanqueeJson['plongeurs'][$k];

							//Récupération de l'id
							if(isset($plongeurJson['idWeb']) && intval(isset($plongeurJson['idWeb'])) > 0){
								$idPlongeur = intval($plongeurJson['idWeb']);
							} else{
								$idPlongeur = -1;
							}

							$plongeur = new Plongeur($idPlongeur, $plongeurJson['version']);
							$plongeur->setIdFicheSecurite($ficheSecurite->getId());
							$plongeur->setIdPalanque($palanquee->getId());

							//Récupération du nom
							if(isset($plongeurJson['nom'])){
								$plongeur->setNom($plongeurJson['nom']);
							} else{
								$erreurRecuperationFiche .= ";nom du plongeur absent pour la palanquee numero ".$palanquee->getNumero();
							}

							//Récupération du prénom
							if(isset($plongeurJson['prenom'])){
								$plongeur->setPrenom($plongeurJson['prenom']);
							} else{
								$erreurRecuperationFiche .= ";prenom du plongeur absent pour la palanquee numero ".$palanquee->getNumero();
							}

							//Récupération des aptitudes
							if(isset($plongeurJson['aptitudes'])){
								for($l = 0; $l < count($plongeurJson['aptitudes']); $l++){
									$aptitudeJson = $plongeurJson['aptitudes'][$l];

									//Récupération de l'id
									if(isset($aptitudeJson['idWeb'])){
										$aptitude = AptitudeDao::getById($aptitudeJson['idWeb']);
									}

									$plongeur->ajouterAptitude($aptitude);
								}
							} else{
								$erreurRecuperationFiche .= ";aptitudes du plongeur absent pour la palanquee numero ".$palanquee->getNumero();
							}

							//Récupération du téléphone
							if(isset($plongeurJson['telephone'])){
								$plongeur->setTelephone($plongeurJson['telephone']);
							} else{
								$erreurRecuperationFiche .= ";telephone du plongeur absent pour la palanquee numero ".$palanquee->getNumero();
							}

							//Récupération du téléphone d'urgence
							if(isset($plongeurJson['telephoneUrgence'])){
								$plongeur->setTelephoneUrgence($plongeurJson['telephoneUrgence']);
							} else{
								$erreurRecuperationFiche .= ";telephoneUrgence du plongeur absent pour la palanquee numero ".$palanquee->getNumero();
							}

							//Récupération de la date de naissance
							if(isset($plongeurJson['dateNaissance'])){
								$plongeur->setDateNaissance($plongeurJson['dateNaissance']);
							} else{
								$erreurRecuperationFiche .= ";dateNaissance du plongeur absent pour la palanquee numero ".$palanquee->getNumero();
							}

							//Récupération de la profondeur réalisée
							if(isset($plongeurJson['profondeurRealisee'])){
								$plongeur->setProfondeurRealisee($plongeurJson['profondeurRealisee']);
							} else{
								$erreurRecuperationFiche .= ";profondeurRealisee du plongeur absent pour la palanquee numero ".$palanquee->getNumero();
							}

							//Récupération de la durée réalisée
							if(isset($plongeurJson['dureeRealisee'])){
								$plongeur->setDureeRealisee($plongeurJson['dureeRealisee']);
							} else{
								$erreurRecuperationFiche .= ";dureeRealisee du plongeur absent pour la palanquee numero ".$palanquee->getNumero();
							}

							$plongeurs[] = $plongeur;

							//Fin de la récupération des plongeurs
						}

						$palanquee->setPlongeurs($plongeurs);
					}

					$palanquees[] = $palanquee;

					//Fin de la récupération des palanquées
				}
				
				$ficheSecurite->setPalanques($palanquees);
			}

			//Récupération du timestamp
			if(isset($ficheJson['timestamp'])){
				$ficheSecurite->setTimestamp($ficheJson['timestamp']);
			} else{
				$erreurRecuperationFiche .= ";timestamp absent";
			}

			//Récupération du site
			if(isset($ficheJson['site'])){
				if(isset($ficheJson['site']['idWeb']) && intval($ficheJson['site']['idWeb']) > 0){
					$ficheSecurite->setSite(SiteDao::getById($ficheJson['site']['idWeb']));
				} else{
					$site = new Site(-1, $ficheJson['site']['version']);
					$site->setNom($ficheJson['site']['nom']);
					$site->setCommentaire($ficheJson['site']['commentaire']);
					$site->setDesactive(false);
					$ficheSecurite->setSite($site);
				}
			} else{
				$erreurRecuperationFiche .= ";site absent";
			}

			if(isset($ficheJson['id']) && intval($ficheJson['id']) > 0){
				$idDistantFicheSecurite = intval($ficheJson['id']);
			} else{
				$erreurRecuperationFiche .= ";id distant absent";
			}

			//Si la fiche a bien été récupérée on l'enregistre et on ajoute son id distant au tableau des fiches récupérés
			if(strlen($erreurRecuperationFiche) == 0){
				$arrayResponseFicheHistorique['fichesOk'][] = $idDistantFicheSecurite;

				//Mise à jours de l'état à archive
				$ficheSecurite->setEtat(FicheSecurite::etatArchive);

				//Enregistrement de la fiche
				if($ficheSecurite->getId() > 0){
					$ficheSecurite = FicheSecuriteDao::update($ficheSecurite);
					$commentaireHistorique = "Archivage de la fiche (fiche créée depuis l'interface web)";
				} else{
					$ficheSecurite = FicheSecuriteDao::insert($ficheSecurite);
					$commentaireHistorique = "Archivage de la fiche (fiche créée depuis l'application mobile')";
				}

				//Enregistrement de l'historique de la fiche
				$historique = new Historique($utilisateurSynch->getLogin(), time(), $ficheSecurite->getId());
				$historique->setSource(Historique::sourceSynchronize);
				$historique->setCommentaire($commentaireHistorique);
				$historique = HistoriqueDao::insert($historique);

				//Map de l'id de la fiche issue de l'application avec l'id local, pour l'enregistrement des historiques
				$idFicheSecuriteJson = $idDistantFicheSecurite;
				$arrayMapIdsFiche[$idFicheSecuriteJson] = $ficheSecurite->getId();
			} else{
				//TODO ajouter au log les erreurs de récupération de fiche contenu dans $erreurRecuperationFiche
			}
		}
	}

	//Récupération des historiques
	if(isset($arrayRequest['historiques'])){

		for($i = 0; $i < count($arrayRequest['historiques']); $i++){
			$historiqueJson = $arrayRequest['historiques'][$i];
			$historiqueBienRecuperee = true;

			//Récupération de l'id distant de l'historique
			if(isset($historiqueJson['idHistorique'])){
				$idHistorique = $historiqueJson['idHistorique'];
			} else{
				$historiqueBienRecuperee = false;
			}

			//Récupération du login
			if(isset($historiqueJson['loginUtilisateur'])){
				$loginHistorique = $historiqueJson['loginUtilisateur'];
			} else{
				$historiqueBienRecuperee = false;
			}

			//Récupération du timestamp
			if(isset($historiqueJson['timestamp'])){
				$timestampHistorique = $historiqueJson['timestamp'];
			} else{
				$historiqueBienRecuperee = false;
			}

			//Récupération de la fiche
			if(isset($historiqueJson['idFicheSecurite'])){
				if(array_key_exists($historiqueJson['idFicheSecurite'], $arrayMapIdsFiche)){
					$idFicheSecuriteHistorique = $arrayMapIdsFiche[$historiqueJson['idFicheSecurite']];
				} else{
					//Historique associé à une fiche qui n'a pas été récupérés dont on le récupère pas
					$historiqueBienRecuperee = false;
				}
			}
			else{
				//Historique qui n'est pas associé à une fiche
				$idFicheSecuriteHistorique = null;
			}

			//Récupération du commentaire
			if(isset($historiqueJson['commentaire'])){
				$commentaireHistorique = $historiqueJson['commentaire'];
			} else{
				$historiqueBienRecuperee = false;
			}

			if($historiqueBienRecuperee){
				//Enregistrement de l'historique
				$historique = new Historique($loginHistorique, $timestampHistorique, $idFicheSecuriteHistorique);
				$historique->setSource(Historique::sourceMobile);
				$historique->setCommentaire($commentaireHistorique);
				$historique = HistoriqueDao::insert($historique);

				$arrayResponseFicheHistorique['historiquesOk'][] = $idHistorique;
			}

		//Fin de la récupération des historiques
		}
	
	}

	return $arrayResponseFicheHistorique;
}

?>