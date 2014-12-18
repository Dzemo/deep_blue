<?php
	require_once("config.php");
	require_once("classloader.php");

	//Si pas de données json, on repond 'no-data'
	if(!isset($_POST['data'])){		
		printResponse("no-data");
		die();
	}
	
	$arrayRequest = getRequestData();
	$arrayResponse = array(	'utilisateurs' => null,
							'fichesSecurite' => null,
							'aptitudes' => null,
							'embarcations' => null,
							'sites' => null,
							'moniteurs' => null,
							'fichesOk' => false,
							'historiquesOk' => false
							);
	
	//Traitement de la version max des utilisateur
	if(isset($arrayRequest['utilisateurMaxVersion'])){
		$utilisateurMaxVersion = intval($arrayRequest['utilisateurMaxVersion']);
		$utilisateurs = UtilisateurDao::getFromVersion($utilisateurMaxVersion);
		//$arrayResponse['utilisateurs'] = $utilisateurs;
	}

	//Récupération de l'utilisateur qui synchronise
	$utilisateurSynch = null;
	if(isset($arrayRequest['utilisateurLogin']) && strlen($arrayRequest['utilisateurLogin']) > 0){
		$utilisateurSynch = UtilisateurDao::getbyLogin($arrayRequest['utilisateurLogin']);

		//Si l'utilisateur est null (existe pas) ou est désactivé, on arrete la
		if($utilisateurSynch == null || !$utilisateurSynch->getActif()){
			printResponse(json_encode($arrayResponse));
			die();
		}

		//Ajout d'un historique pour indiquée que l'utilisateur à synchroniser son appareil
		$historique = new Historique($utilisateurSynch->getLogin(), time(), null);
		$historique->setSource(Historique::sourceSynchronize);
		$historique->setCommentaire("Synchronisation de l'appareil");
		$historique = HistoriqueDao::insert($historique);
	}

	//Enregistrement des fiches de sécurité si présente et de leurs historiques
	$arrayResponseFicheHistorique = enregistreFichesEtHistoriqueAvecJson($arrayRequest, $utilisateurSynch);
	$arrayResponse['fichesOk'] = $arrayResponseFicheHistorique['fichesOk'];
	$arrayResponse['historiquesOk'] = $arrayResponseFicheHistorique['historiquesOk'];


	
	//Fin de la synchronisation
	printResponse(json_encode($arrayResponse));
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
	$arrayResponseFicheHistorique = array('fichesOk' => false,	'historiquesOk' => false);

	//Tableau mappant les id des fiches local avec l'id json, pour récupérer les historiques
	$arrayMapIdsFiche = array();

	if($utilisateurSynch == null)
		return $arrayResponseFicheHistorique;

	//Enregistrement des fiches de sécurité si présente et de leurs historiques
	if(isset($arrayRequest['fichesSecuriteValidees'])){
		$arrayFichesJson = $arrayRequest['fichesSecuriteValidees'];

		for($i = 0; $i < count($arrayFichesJson) ; $i++){
			$ficheJson = $arrayFichesJson[$i];

			//Récupération de l'id
			if(isset($ficheJson['idWeb']) && intval(isset($ficheJson['idWeb'])) > 0){
				$idFiche = intval($ficheJson['idWeb']);
			}
			else{
				$idFiche = -1;
			}

			$ficheSecurite = new FicheSecurite($idFiche, $ficheJson['version']);

			//Récupération de l'embarcation
			if(isset($ficheJson['embarcation']) && isset($ficheJson['embarcation']['idWeb'])){
				$ficheSecurite->setEmbarcation(EmbarcationDao::getById($ficheJson['embarcation']['idWeb']));
			}

			//Récupération du directeur de plongée
			if(isset($ficheJson['directeurPlonge']) && isset($ficheJson['directeurPlonge']['idWeb'])){
				$ficheSecurite->setDirecteurPlonge(MoniteurDao::getById($ficheJson['directeurPlonge']['idWeb']));
			}

			//Récupération des palanquees
			if(isset($ficheJson['palanquees'])){
				$palanquees = array();
				for($j = 0; $j < count($ficheJson['palanquees']); $j++){
					$palanqueeJson = $ficheJson['palanquees'][$j];

					//Récupération de l'id
					if(isset($palanqueeJson['idWeb']) && intval(isset($palanqueeJson['idWeb'])) > 0){
						$idPalanquee = intval($palanqueeJson['idWeb']);
					}
					else{
						$idPalanquee = -1;
					}

					$palanquee = new Palanque($idPalanquee, $palanqueeJson['version']);
					$palanquee->setIdFicheSecurite($ficheSecurite->getId());

					//Récupération du numéro
					if(isset($palanqueeJson['numero'])){
						$palanquee->setNumero($palanqueeJson['numero']);
					}

					//Récupération du type de gaz
					if(isset($palanqueeJson['typeGaz'])){
						$palanquee->setTypeGaz($palanqueeJson['typeGaz']);
					}

					//Récupération du type de plongée
					if(isset($palanqueeJson['typePlonge'])){
						$palanquee->setTypePlonge($palanqueeJson['typePlonge']);
					}

					//Récupération de la profondeur prévue
					if(isset($palanqueeJson['profondeurPrevue'])){
						$palanquee->setProfondeurPrevue($palanqueeJson['profondeurPrevue']);
					}

					//Récupération de la durée prévue
					if(isset($palanqueeJson['dureePrevue'])){
						$palanquee->setDureePrevue($palanqueeJson['dureePrevue']);
					}

					//Récupération de l'heure'
					if(isset($palanqueeJson['heure'])){
						$palanquee->setHeure($palanqueeJson['heure']);
					}

					//Récupération de la profondeur réalisée par le moniteur
					if(isset($palanqueeJson['profondeurRealiseeMoniteur'])){
						$palanquee->setProfondeurRealiseeMoniteur($palanqueeJson['profondeurRealiseeMoniteur']);
					}

					//Récupération de la durée réalisée par le moniteur
					if(isset($palanqueeJson['dureeRealiseeMoniteur'])){
						$palanquee->setDureeRealiseeMoniteur($palanqueeJson['dureeRealiseeMoniteur']);
					}

					//Récupération des plongeurs
					if(isset($palanqueeJson['plongeurs'])){
						$plongeurs = array();
						for($k = 0; $k < count($palanqueeJson['plongeurs']); $k++){
							$plongeurJson = $palanqueeJson['plongeurs'][$k];

							//Récupération de l'id
							if(isset($plongeurJson['idWeb']) && intval(isset($plongeurJson['idWeb'])) > 0){
								$idPlongeur = intval($plongeurJson['idWeb']);
							}
							else{
								$idPlongeur = -1;
							}

							$plongeur = new Plongeur($idPlongeur, $plongeurJson['version']);
							$plongeur->setIdFicheSecurite($ficheSecurite->getId());
							$plongeur->setIdPalanque($palanquee->getId());

							//Récupération du nom
							if(isset($plongeurJson['nom'])){
								$plongeur->setNom($plongeurJson['nom']);
							}

							//Récupération du prénom
							if(isset($plongeurJson['prenom'])){
								$plongeur->setPrenom($plongeurJson['prenom']);
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
							}

							//Récupération du téléphone
							if(isset($plongeurJson['telephone'])){
								$plongeur->setTelephone($plongeurJson['telephone']);
							}

							//Récupération du téléphone d'urgence
							if(isset($plongeurJson['telephoneUrgence'])){
								$plongeur->setTelephoneUrgence($plongeurJson['telephoneUrgence']);
							}

							//Récupération de la date de naissance
							if(isset($plongeurJson['dateNaissance'])){
								$plongeur->setDateNaissance($plongeurJson['dateNaissance']);
							}

							//Récupération de la profondeur réalisée
							if(isset($plongeurJson['profondeurRealisee'])){
								$plongeur->setProfondeurRealisee($plongeurJson['profondeurRealisee']);
							}

							//Récupération de la durée réalisée
							if(isset($plongeurJson['dureeRealisee'])){
								$plongeur->setDureeRealisee($plongeurJson['dureeRealisee']);
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
			}

			//Récupération du site
			if(isset($ficheJson['site'])){
				if(isset($ficheJson['site']['idWeb']) && intval($ficheJson['site']['idWeb']) > 0){
					$ficheSecurite->setSite(SiteDao::getById($ficheJson['site']['idWeb']));
				}
				else{
					$site = new Site(-1, $ficheJson['site']['version']);
					$site->setNom($ficheJson['site']['nom']);
					$site->setCommentaire($ficheJson['site']['commentaire']);
					$site->setDesactive($ficheJson['site']['desactive']);
					$ficheSecurite->setSite($site);
				}
			}


			//Mise à jours de l'état à archive
			$ficheSecurite->setEtat(FicheSecurite::etatArchive);

			//Enregistrement de la fiche
			if($ficheSecurite->getId() > 0){
				$ficheSecurite = FicheSecuriteDao::update($ficheSecurite);
				$commentaireHistorique = "Archivage de la fiche (fiche créer depuis l'interface web)";
			}
			else{
				$ficheSecurite = FicheSecuriteDao::insert($ficheSecurite);
				$commentaireHistorique = "Archivage de la fiche (fiche créer depuis l'application mobile')";
			}

			//Enregistrement de l'historique de la fiche
			$historique = new Historique($utilisateurSynch->getLogin(), time(), $ficheSecurite->getId());
			$historique->setSource(Historique::sourceSynchronize);
			$historique->setCommentaire($commentaireHistorique);
			$historique = HistoriqueDao::insert($historique);

			//Map de l'id de la fiche issue de l'application avec l'id local
			if(isset($ficheJson['id'])){
				$idFicheSecuriteJson = $ficheJson['id'];
				$arrayMapIdsFiche[$idFicheSecuriteJson] = $ficheSecurite->getId();
			}
		}
		
		$arrayResponseFicheHistorique['fichesOk'] = true;
		//Fin de la récupération de la fiche de sécurité
	}


	//Récupération des historiques
	if(isset($arrayRequest['historiques'])){
		for($i = 0; $i < count($arrayRequest['historiques']); $i++){
			$historiqueJson = $arrayRequest['historiques'][$i];

			//Récupération du login
			if(isset($historiqueJson['loginUtilisateur'])){
				$loginHistorique = $historiqueJson['loginUtilisateur'];
			}

			//Récupération du timestamp
			if(isset($historiqueJson['timestamp'])){
				$timestampHistorique = $historiqueJson['timestamp'];
			}

			//Récupération de la fiche
			if(isset($historiqueJson['idFicheSecurite']) && array_key_exists($historiqueJson['idFicheSecurite'], $arrayMapIdsFiche)){
				$idFicheSecuriteHistorique = $arrayMapIdsFiche[$historiqueJson['idFicheSecurite']];
			}
			else{
				$idFicheSecuriteHistorique = null;
				//TODO erreur enregistrement des historiques
			}

			//Récupération du commentaire
			if(isset($historiqueJson['commentaire'])){
				$commentaireHistorique = $historiqueJson['commentaire'];
			}

			//Enregistrement de l'historique
			$historique = new Historique($loginHistorique, $timestampHistorique, $idFicheSecuriteHistorique);
			$historique->setSource(Historique::sourceMobile);
			$historique->setCommentaire($commentaireHistorique);
			$historique = HistoriqueDao::insert($historique);
		}

		$arrayResponseFicheHistorique['historiquesOk'] = true;
		//Fin de la récupération des historiques	
	}

	return $arrayResponseFicheHistorique;
}

?>