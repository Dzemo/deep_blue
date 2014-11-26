<?php
	require_once("../session.php");
	require_once("../utils/DateStringUtils.php");
	
	//////////////////////////////////////////////////////
	//Vérification qu'un utilisateur est bien connecté //
	//////////////////////////////////////////////////////
	if(!$connecte){
		echo json_encode(['erreurs' => ['numero' => 0, 'type' => 'general', 'msg' => 'Session expiré, veuillez vous reconnecté']]);
		die();
	}
	
	//Tableau contenant les éventuelles erreurs
	//Pour la gestion des erreurs:
	//type: val_abs|gestion|general
	//numero: 0:fiche_securite|1-X:palanque numero X
	//subnumero: 1-X:plongeur numeor X (null pour fiche securite)
	//msg: message de l'erreur
	$erreurs = array();
	
	/////////////////////////////////////////////////////////////
	//Vérification que toutes les valeurs $_POST sont présent //
	/////////////////////////////////////////////////////////////
	
	//Récupération fiche securite
	//Récupération de l'id
	if(filter_input(INPUT_POST, 'fiche_securite_id', FILTER_VALIDATE_INT)){
		$fiche_securite_id = intval($_POST['fiche_securite_id']);
	}
	else{
		$fiche_securite_id = null;
	}
	
	//Récupération de la version
	if(filter_input(INPUT_POST, 'fiche_securite_version', FILTER_VALIDATE_INT)){
		$fiche_securite_version = intval($_POST['fiche_securite_version']);
	}
	else{
		$fiche_securite_version = 0;
	}
	
	//Création de la fiche pour remplir les valeurs
	$ficheSecurite = new FicheSecurite($fiche_securite_id, $fiche_securite_version);
	if($fiche_securite_id == null)
		$ficheSecurite->setEtat(FicheSecurite::etatCreer);
	else
		$ficheSecurite->setEtat(FicheSecurite::etatModifie);
	
	//Récupération date/heure/minute
	//Utilisation de flag binaire pour repéré les champs du timestamps manquant
	$erreur_timestamps = 0;
	if(!filter_input(INPUT_POST, 'date', FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^\s*(0[1-9]|1\d|2\d|30|31)\/(0[1-9]|1[0-2])\/(2\d\d\d)\s*$/"))))
	{
		$erreurs[] = ['numero' => 0, 'type' => 'val_abs', 'msg' => '<strong>Date</strong> de plongé manquante ou invalide (Format JJ/MM/AAAA attendu)'];
	}
	if(count($erreurs) == 0){
		//Si pas d'erreur jusque la et donc sur date/heure/minute, on calcul le timestamps du match
		list($jour, $mois, $annee) = explode('/',trim($_POST['date']));
		$timestamp = mktime(intval($mois), intval($jour), intval($annee));
		$ficheSecurite->setTimestamp($timestamp);
	}
	
	//Récupération de l'embarcation
	if(filter_input(INPUT_POST, 'embarcation_id', FILTER_VALIDATE_INT)){
		$ficheSecurite->setEmbarcation(EmbarcationDao::getById(intval($_POST['embarcation_id'])));
	}
	//Pas d'erreur en cas d'absence de l'embarcation
	
	//Récupération du site
	if(isset($_POST['site']) && strlen($_POST['site']) > 0){
		$site = filter_var($_POST['site'], FILTER_SANITIZE_STRING);
		$ficheSecurite->setSite($site);
	}
	/*else{ // MIS EN COMMENTAIRE LE 26/11/14 par FLAVIO | Cause : Site est non obligatoire à la création
		$erreurs[] = ['numero' => 0, 'type' => 'val_abs', 'msg' => '<strong>Site</strong> de plongé manquant'];
	}*/
	
	//Récupération du directeur de plongé
	if(filter_input(INPUT_POST, 'directeur_plonge_id', FILTER_VALIDATE_INT)){
		$directeur_plonge_id = intval($_POST['directeur_plonge_id']);
		$ficheSecurite->setDirecteurPlonge(MoniteurDao::getById($directeur_plonge_id));
	}
	else{
		$erreurs[] = ['numero' => 0, 'type' => 'val_abs', 'msg' => '<strong>Directeur de plongé</strong> de plongé manquant ou invalide'];
	}
	
	//Validation des palanques
	//Récupération des aptitudes dans un tableau pour le réutilisé à chaque palanqué/plongeurs
	if(isset($_POST['liste_palanques']) && count($_POST['liste_palanques']) > 0){
		$aptitudes = AptitudeDao::getAll();
		$palanques = array();
		foreach($_POST['liste_palanques'] as $post_palanque){
			//Permet de savoir si la palanqué à des erreur par rapport à des variables manquantes (par opposition au erreurs du aux règles de gestion)
			//pour ne pas faire la vérification de gestion si il y en a
			$erreur_variable = false;
			
			//Récupération de l'id
			if(filter_var($post_palanque['id'], FILTER_VALIDATE_INT)){
				$id = intval($post_palanque['id']);
			}
			else{
				$id = null;
			}
			
			//Récupération de la version
			if(filter_var($post_palanque['version'], FILTER_VALIDATE_INT)){
				$version = intval($post_palanque['version']);
			}
			else{
				$version = 0;
			}
			
			//Récupération du numero
			if(filter_var($post_palanque['numero'], FILTER_VALIDATE_INT)){
				$numero = intval($post_palanque['numero']);
			}
			else{
				//Cas d'erreur non géréer, le numéro de palanqué devrait toujours être présent
				$numero = 0;
			}
			
			//Création de la palanqué pour remplir les valeurs
			$palanque = new Palanque($id, $version);
			$palanque->setNumero($numero);
			$palanque->setIdFicheSecurite($ficheSecurite->getId());
			
			//Vérification du gaz
			if(isset($post_palanque['gaz']) && 
				strlen($post_palanque['gaz']) > 0 && 
				(strcmp($post_palanque['gaz'], Palanque::gazAir) == 0 ||
				strcmp($post_palanque['gaz'], Palanque::gazNitrox) == 0))	{	
				$gaz = filter_var($post_palanque['gaz'], FILTER_SANITIZE_STRING);
				$palanque->setTypeGaz($gaz);
			}
			else{
				$erreur_variable = true;
				$erreurs[] = ['numero' => $numero, 'type' => 'val_abs', 'msg' => 'Veuillez séléctionner un <strong>Type de gaz</strong>'];
			}
			
			//Vérification du type de plongé
			if(isset($post_palanque['plonge']) && 
				strlen($post_palanque['plonge']) > 0 && 
				(strcmp($post_palanque['plonge'], Palanque::plongeTechnique) == 0 ||
				strcmp($post_palanque['plonge'], Palanque::plongeEncadre) == 0 ||
				strcmp($post_palanque['plonge'], Palanque::plongeAutonome) == 0))	{	
							
				$plonge = filter_var($post_palanque['plonge'], FILTER_SANITIZE_STRING);
				$palanque->setTypePlonge($plonge);
			}
			else{
				$erreur_variable = true;
				$erreurs[] = ['numero' => $numero, 'type' => 'val_abs', 'msg' => 'Veuillez séléctionner un <strong>Type de plongé</strong>'];
			}
			
			//Vérification de la profondeur
			if(filter_var($post_palanque['profondeur_prevue'], FILTER_VALIDATE_FLOAT)){
				$profondeur_prevue = floatval($post_palanque['profondeur_prevue']);
				$palanque->setProfondeurPrevue($profondeur_prevue);
			}
			else{
				$erreur_variable = true;
				$erreurs[] = ['numero' => $numero, 'type' => 'val_abs', 'msg' => '<strong>Profondeur prévue</strong> de plongé manquante ou invalide'];
			}
			
			//Vérification de la durée
			if(filter_var($post_palanque['duree_prevue'], FILTER_VALIDATE_INT)){
				$duree_prevue = intval($post_palanque['duree_prevue']);
				$palanque->setDureePrevue($duree_prevue);
			}
			//Pas d'erreur en cas de durée absente
			
			//Vérification de la présence d'un moniteur et de ses valeurs
			if(isset($post_palanque['moniteur'])){
				//Récupération du moniteur à partir de son id
				if(filter_var($post_palanque['moniteur']['id'], FILTER_VALIDATE_INT)){
					$moniteur = MoniteurDao::getById(intval($post_palanque['moniteur']['id']));
				}
				else{
					$moniteur = null;
				}
				
				$palanque->setMoniteur($moniteur);
			//Fin de la récupération du moniteur
			}
			//Pas d'erreur en cas de moniteur absent, si il en faut un par rapport au type de plongé
			//cela sera vérifier lors de la vérification des règles de gestion
			
			//Récupération des plongeurs
			if(isset($post_palanque['plongeurs'])){
				$plongeurs = array();
				
				//Map le numéro de plongeur avec ça position dans le tableau post_palanque pour pouvoir
				//le récupérer dans les messages d'erreurs
				//$plongeurNumeroMap = array();
				//$i = 0;
				foreach($post_palanque['plongeurs'] as $post_plongeur){
					
					//Récupération de l'id
					if(filter_var($post_plongeur['id'], FILTER_VALIDATE_INT)){
						$id = intval($post_plongeur['id']);
					}
					else{
						$id = null;
					}
					
					//Récupération de la version
					if(filter_var($post_plongeur['version'], FILTER_VALIDATE_INT)){
						$version = intval($post_plongeur['version']);
					}
					else{
						$version = 0;
					}
					
					//Récupération du numero
					if(filter_var($post_plongeur['numero'], FILTER_VALIDATE_INT)){
						$numero = intval($post_plongeur['numero']);
					}
					else{
						//Cas d'erreur non géréer, le numéro de plongeur devrait toujours être présent
						die();
					}
					$plongeur = new Plongeur($id, $version);
					$plongeur->setIdPalanque($palanque->getId());
					$plongeur->setIdFicheSecurite($ficheSecurite->getId());
					
					//Récupération du nom
					if(isset($post_plongeur['nom']) && strlen($post_plongeur['nom']) > 0){
						$nom = filter_var($post_plongeur['nom'], FILTER_SANITIZE_STRING);
						$plongeur->setNom($nom);
					}
					else{
						$erreur_variable = true;
						$erreurs[] = ['numero' => $palanque->getNumero(), 'subnumero' => $numero, 'type' => 'val_abs', 'msg' => '<strong>Nom</strong> du plongeur manquant'];
					}
					
					//Récupération du prénom
					if(isset($post_plongeur['prenom']) && strlen($post_plongeur['prenom']) > 0){
						$prenom = filter_var($post_plongeur['prenom'], FILTER_SANITIZE_STRING);
						$plongeur->setPrenom($prenom);
					}
					else{
						$erreurs[] = ['numero' => $palanque->getNumero(), 'subnumero' => $numero, 'type' => 'val_abs', 'msg' => '<strong>Prenom</strong> du plongeur manquant'];
					}
					
					//Récupération du téléphone
					if(isset($post_plongeur['telephone']) && strlen($post_plongeur['telephone']) > 0){
						$telephone = filter_var($post_plongeur['telephone'], FILTER_SANITIZE_STRING);
						$plongeur->setTelephone($telephone);
					}
					
					//Récupération du téléphone d'urgence
					if(isset($post_plongeur['telephone_urgence']) && strlen($post_plongeur['telephone_urgence']) > 0){
						$telephone_urgence = filter_var($post_plongeur['telephone_urgence'], FILTER_SANITIZE_STRING);
						$plongeur->setTelephoneUrgence($telephone_urgence);
					}
					
					//Récupération de la date de naissance
					if(filter_var($post_plongeur['date_naissance'], FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^\s*(0[1-9]|1\d|2\d|30|31)\/(0[1-9]|1[0-2])\/([1-2]\d\d\d)\s*$/")))){
						$date_naissance = filter_var($post_plongeur['date_naissance'], FILTER_SANITIZE_STRING);
						$plongeur->setDateNaissance($date_naissance);
					}
					else{
						$erreur_variable = true;
						$erreurs[] = ['numero' => $palanque->getNumero(), 'subnumero' => $numero, 'type' => 'val_abs', 'msg' => '<strong>Date de naissance</strong> du plongeur manquante  (Format JJ/MM/AAAA attendu)'];
					}
					
					//Récupération des aptitudes
					if(isset($post_plongeur['aptitudes']) && is_array($post_plongeur['aptitudes'])){
						foreach ($post_plongeur['aptitudes'] as $aptitude_id) {
							$plongeur->ajouterAptitude($aptitudes[$aptitude_id]);
						}
					}
					
					//Pas d'erreur en cas d'absence d'aptitudes pour plongeur, si il en faut par rapport au type de plongé
					//cela sera vérifier lors de la vérification des règles de gestion
					$plongeurs[] = $plongeur;
				}
			$palanque->setPlongeurs($plongeurs);
			//Fin de la récupération des plongeurs
			}
			//Pas d'erreur en cas d'absence de plongeur, si il en faut par rapport au type de plongé
			//cela sera vérifier lors de la vérification des règles de gestion
				
			



			if($erreur_variable){
				//Si des erreurs sur la palanqué sont présente, on ne peut pas faire la vérification des règles de gestion
				$erreurs[] = ['numero' => $palanque->getNumero(), 'type' => 'gestion', 'msg' => 'La validation de la palanquée par rapport aux règles du code du sport n\'a pas pu être effectuée'];
			}
			else{				
				////////////////////////////////////////////////////////////////////////////////
				//Vérification que la palanqué est valide par rapport aux règle de gestion //
				////////////////////////////////////////////////////////////////////////////////
				
				//Plan de vérification :
				//Vérification du type de plongé :
				//	ce type de plongé est bien possible à cette profondeur et pour ce gaz (0-6 pas d'encadré au nitrox ni d'autonome, baptême que 0-6)
				//	présence d'un moniteur
				//	nombre de plongeur (avec l'eventuelle ajout en fonction de la profondeur)
				//	
				//Vérification du type de plongé et du gazen fonction de la profondeur
				//	les plongeurs peuvent bien plongé à cette profondeur avec ce type de plongé
				//	si il y a un moniteur, il peut bien encadré ce type de plongé à cette profondeur avec ce gaz
				//	les plongeurs peuvent bien plongé à cette profondeur avec ce gaz
				//Vérification du type de plongé :
				//	ce type de plongé est bien possible à cette profondeur et pour ce gaz (0-6 pas d'encadré au nitrox ni d'autonome, baptême que 0-6)
				if($palanque->getProfondeurPrevue() <= 6){
					//0-6 pas d'encadré au nitrox ni d'autonome
					if($palanque->getTypePlonge() == Palanque::plongeAutonome){
						$erreurs[] = ['numero' => $palanque->getNumero(), 'type' => 'gestion', 'msg' => 'Impossible de plongé en <strong>autonomie</strong> entre 0 et 6 mètre de profondeur'];
					}
					else if($palanque->getTypePlonge() == Palanque::plongeEncadre && $palanque->getTypeGaz() == Palanque::gazNitrox){
						$erreurs[] = ['numero' => $palanque->getNumero(), 'type' => 'gestion', 'msg' => 'Impossible de plongé en <strong>exploration encadré</strong> avec du <strong>nitrox</strong> entre 0 et 6 mètre de profondeur'];
					}
				}
				else if($palanque->getTypePlonge() == Palanque::plongeBapteme){
					$erreurs[] = ['numero' => $palanque->getNumero(), 'type' => 'gestion', 'msg' => 'Impossible de faire un <strong>baptême</strong> à <strong>'.$palanque->getProfondeurPrevue().' mètres</strong> de profondeur, maximum 6 mètres'];
				}
				//	présence d'un moniteur
				if($palanque->getTypePlonge() != Palanque::plongeAutonome){
					if($palanque->getMoniteur() == null){
						$erreurs[] = ['numero' => $palanque->getNumero(), 'type' => 'gestion', 'msg' => 'Il est necessaire d\'avoir un <strong>moniteur</strong> pour une plongé '.typePlongeToString($palanque->getTypePlonge())];
					}
				}
				//	nombre de plongeurs (avec l'eventuelle ajout en fonction de la profondeur)
				if($palanque->getTypePlonge() == Palanque::plongeBapteme){
					$max_plongeur_non_bonus = 1;
					if(count($palanque->getPlongeurs()) > $max_plongeur_non_bonus){
						//Si il y a plus de 1 plongeurs, il faut vérifié que les plongeurs supplémentaire ont les aptitude requise
						$plongeurs_non_bonus = 0;
						foreach($palanque->getPlongeurs() as $plongeur){
							if(!peutAjouterPlongeurProfondeurGaz($plongeur, $palanque->getProfondeurPrevue(), $palanque->getTypeGaz()))
								$plongeurs_non_bonus++;
						}
						if($plongeurs_non_bonus > $max_plongeur_non_bonus){
							$erreurs[] = ['numero' => $palanque->getNumero(), 'type' => 'gestion', 'msg' => 'Il y a trop de plongeur étant données leurs aptitudes pour une plongé '.typePlongeToString($palanque->getTypePlonge()).' et à une profondeur prévu de '. $palanque->getProfondeurPrevue.' mètres'];
						}
					}
				}
				else if($palanque->getTypePlonge() == Palanque::plongeTechnique || $palanque->getTypePlonge() == Palanque::plongeEncadre){
					$max_plongeur_non_bonus = 4;
					if(count($palanque->getPlongeurs()) > $max_plongeur_non_bonus){
						//Si il y a plus de 4 plongeurs, il faut vérifié que les plongeurs supplémentaire ont les aptitude requise
						$plongeurs_non_bonus = 0;
						foreach($palanque->getPlongeurs() as $plongeur){
							if(!peutAjouterPlongeurProfondeurGaz($plongeur, $palanque->getProfondeurPrevue(), $palanque->getTypeGaz()))
								$plongeurs_non_bonus++;
						}
						if($plongeurs_non_bonus > $max_plongeur_non_bonus){
							$erreurs[] = ['numero' => $palanque->getNumero(), 'type' => 'gestion', 'msg' => 'Il y a trop de plongeur étant données leurs aptitudes pour une plongé '.typePlongeToString($palanque->getTypePlonge()).' et à une profondeur prévu de '. $palanque->getProfondeurPrevue().' mètres'];
						}
					}
				}
				else{
					//Plongé autonome, minimum 3 plongeurs
					if(count($palanque->getPlongeurs()) > 3){
						$erreurs[] = ['numero' => $palanque->getNumero(), 'type' => 'gestion', 'msg' => 'Il faut au maximum <stong>3</strong> plongeurs pour une plongé '.typePlongeToString($palanque->getTypePlonge())];
					}
				}
				
				//Vérification du type de plongé en fonction de la profondeur
				//	les plongeurs peuvent bien plongé à cette profondeur avec ce type de plongé
				$plongeur_index = 0;
				foreach ($palanque->getPlongeurs() as $plongeur) {
					if(!peutPlongerPlongeurProfondeurPlonge($plongeur, $palanque->getProfondeurPrevue(), $palanque->getTypePlonge())){
						$msg = $plongeur->getPrenom()." ".$plongeur->getNom()." ne peut pas faire une plongé <strong>".typePlongeToString($palanque->getTypePlonge())."</strong> à une profondeur de <strong>".$palanque->getProfondeurPrevue()." mètres</strong> avec ces <strong>aptitudes</strong>";
						$erreurs[] = ['numero' => $palanque->getNumero(), 'subnumero' => $post_palanque['plongeurs'][$plongeur_index]['numero'], 'type' => 'gestion', 'msg' => $msg];
					}
					$plongeur_index++;
				}
				//	si il y a un moniteur, il peut bien encadré ce type de plongé à cette profondeur avec ce gaz
				if($palanque->getMoniteur() != null){
					if($palanque->getMoniteur()->getAptitudes() == null){
						$erreurs[] = ['numero' => $palanque->getNumero(), 'subnumero' => 0, 'type' => 'gestion', 'msg' => $palanque->getMoniteur()->getPrenom().' '.$palanque->getMoniteur()->getNom().' n\'a pas les aptitudes nécéssaires pour encadrer cette palanquée'];
					}
					else{
						//Si le gaz n'est pas nitrox, alors true donc pas besoin de vérifier
						$enseignement_nitrox_ok = $palanque->getTypeGaz() != Palanque::gazNitrox || $palanque->getTypePlonge() != Palanque::plongeTechnique;
						//Meme chose pour air
						$enseignement_air_ok = $palanque->getTypeGaz() != Palanque::gazAir || $palanque->getTypePlonge() != Palanque::plongeTechnique;
						
						//Si l'encadrement n'est pas plongeEncadre, alor à true (donc pas besoin de vérifié)
						$encadrement_ok = $palanque->getTypePlonge() != Palanque::plongeEncadre;
						
						//true si le type de gaz est pas nitrox
						$gaz_ok = $palanque->getTypeGaz() != Palanque::gazNitrox;
						
						//On admet que le moniteur peut plongé au profondeur ou il peut enseigner
						$profondeur = ceil($palanque->getProfondeurPrevue());
						
						foreach ($palanque->getMoniteur()->getAptitudes() as $aptitude) {

							if(!$enseignement_nitrox_ok && $aptitude->getEnseignementNitroxMax() >= $profondeur)
								$enseignement_nitrox_ok = true;

							if(!$enseignement_air_ok && $aptitude->getEnseignementAirMax() >= $profondeur)
								$enseignement_air_ok = true;

							if(!$encadrement_ok && $aptitude->getEncadrementMax() >= $profondeur)
								$encadrement_ok = true;

							if(!$gaz_ok && $aptitude->getNitroxMax() >= $profondeur)
								$gaz_ok = true;						
						}					

						//Si tout n'est pas ok on construit le message
						if(!$enseignement_nitrox_ok || !$enseignement_air_ok || !$encadrement_ok || !$gaz_ok){
							if(!$enseignement_nitrox_ok)
								$raison = "ne peut pas enseigner à une palanquée utilisant du nitrox à cette profondeur";
							else if(!$enseignement_air_ok)
								$raison = "ne peut pas enseigner à une palanquée à cette profondeur";
							else if(!$encadrement_ok){
								if(!$gaz_ok)
									$raison = "ne peut pas encadrer une palanquée utilisant du nitrox à cette profondeur";
								else
									$raison = "ne peut pas encadrer une palanquée à cette profondeur";
							}
							else
								$raison = "ne peut pas encadrer une palanquée utilisant du nitrox";
							$erreurs[] = ['numero' => $palanque->getNumero(), 'subnumero' =>0, 'type' => 'gestion', 'msg' => $palanque->getMoniteur()->getPrenom().' '.$palanque->getMoniteur()->getNom().' '.$raison];
						}
					}
				}
				//	les plongeurs peuvent bien plongé à cette profondeur avec ce gaz
				if($palanque->getTypeGaz() == Palanque::gazNitrox){
					$plongeur_index = 0;
					foreach ($palanque->getPlongeurs() as $plongeur) {
						if(!peutPlongerPlongeurProfondeurNitrox($plongeur, $palanque->getProfondeurPrevue())){
							$erreurs[] = ['numero' => $palanque->getNumero(), 'subnumero' => $post_palanque['plongeurs'][$plongeur_index]['numero'], 'type' => 'gestion', 'msg' => $plongeur->getPrenom()." ".$plongeur->getNom()." ne peut pas plonger avec du <strong>nitrox</strong> à une profondeur de <strong>".$palanque->getProfondeurPrevue()." mètres</strong>"];
						}
						$plongeur_index++;
					}
				}
			}
			$palanques[] = $palanque;
		//Fin du parcours des palanqué
		}
		$ficheSecurite->setPalanques($palanques);
	}
	
	//Vérification du nombre de personne par rapport à la contenance de l'embarcation si elle est spécifié
	if($ficheSecurite && $ficheSecurite->getEmbarcation() && $ficheSecurite->getEmbarcation()->getMaxpersonne() > 0){
		$nombre_personne_sur_bateau = 1;//Le directeur de plongé

		foreach ($ficheSecurite->getPalanques() as $palanque) {
			if($palanque->getMoniteur())
				$nombre_personne_sur_bateau++;
			$nombre_personne_sur_bateau += count($palanque->getPlongeurs());
		}

		//Erreur si le nombre de personne sur le bateau est superieur au maximum
		if($nombre_personne_sur_bateau > $ficheSecurite->getEmbarcation()->getMaxpersonne()){
			$erreurs[] = ['numero' => 0, 'type' => 'gestion', 'msg' => 'L\'embarcation selectionnée ne peut contenir que <strong>'.$ficheSecurite->getEmbarcation()->getMaxpersonne().'</strong> personnes ('.$nombre_personne_sur_bateau.' actuellement)'];
		}
	}


	//Pas d'erreur si il n'y a pas de palanqué
	//Si il y a des erreurs on les envois
	if(count($erreurs) > 0){
		echo json_encode(["erreurs" => $erreurs]);
	}
	
	//Si il n'y a pas d'erreurs, on enregistre la fiche et ajoute un historique
	else{
		if($ficheSecurite->getId() != null){
			//$ficheSecurite->setEtat(FicheSecurite::etatModifie);
			$ficheSecurite = FicheSecuriteDao::update($ficheSecurite);
			$message = "Fiche de sécurité mise à jours";
		}
		else{
			$ficheSecurite->setEtat(FicheSecurite::etatCreer);
			$ficheSecurite = FicheSecuriteDao::insert($ficheSecurite);
			$message = "Fiche de sécurité créée";
		}
		
		if($ficheSecurite != null){
			$historique = new Historique($utilisateur->getLogin(), time(), $ficheSecurite->getId());
			$historique->setSource(Historique::sourceWeb);
			$historique->setCommentaire($message);
			$historique = HistoriqueDao::insert($historique);
			
			echo json_encode(["succes" => ['redirect' => 'index.php?page=consulter_fiche&id='.$ficheSecurite->getId(), 'msg' => $message]]);
		}
		else{
			echo json_encode(['erreurs' => ['numero' => 0, 'type' => 'general', 'msg' => 'Erreur lors de l\'enregistrement de la fiche de sécurité']]);
		}
	}
?>
<?php
	
	/**
	 * Vérifie si ce plongeur peut s'ajouter à cette palanqué en temps que plongeur supplémentaire
	 * pour cette profondeur et ce type de gaz
	 * @param  Plongeur $plongeur    
	 * @param  float $profondeur  
	 * @param  string $gaz
	 * @return boolean true si le plongeur peut s'ajouter, false sinon
	 */
	function peutAjouterPlongeurProfondeurGaz($plongeur, $profondeur, $gaz){
		$profondeur_ok = false;
		$gaz_ok = $gaz != Palanque::gazNitrox;
		$profondeur = ceil($profondeur);
		if($plongeur->getAptitudes() == null)
			return false;
		else{
			foreach($plongeur->getAptitudes() as $aptitude){
				if($aptitude->getAjoutMax() >= $profondeur){
					$profondeur_ok = true;
					if($gaz_ok)
						return true;
				}
				if($aptitude->getNitroxMax() >= 20){
					//On considère qu'il s'agit d'un PN-C
					$gaz_ok = true;
					if($profondeur_ok)
						return true;
				}
			}
			return false;
		}
	}
	/**
	 * Vérifie si ce plongeur peut plongé à cette profondeur pour ce type de plongé
	 * @param  Plongeur $plongeur   
	 * @param  float $profondeur 
	 * @param  string $plonge     
	 * @return boolean             true si le plongeur peut plonger, false sinon
	 */
	function peutPlongerPlongeurProfondeurPlonge($plongeur, $profondeur, $plonge){
		$profondeur = ceil($profondeur);
		if($profondeur <= 6)
			return true;
		if($plongeur->getAptitudes() == null && $profondeur > 6)
			return false;
		else{
			foreach($plongeur->getAptitudes() as $aptitude){
				switch($plonge){
					case Palanque::plongeAutonome:
						if($aptitude->getAutonomeMax() >= $profondeur) return true;
						break;
					case Palanque::plongeEncadre:
						if($aptitude->getEncadreeMax() >= $profondeur) return true;
						break;
					case Palanque::plongeTechnique:
						if($aptitude->getTechniqueMax() >= $profondeur) return true;
						break;
					case Palanque::plongeBapteme:
						return true;
						break;
					default:
						break;
				}
			}
			return false;
		}
	}
	/**
	 * Vérifie si ce plongeur peut plongé à cette profondeur avec du nitrox
	 * @param  Plongeur $plongeur   
	 * @param  float $profondeur 
	 * @return boolean             true si le plongeur peut plonger, false sinon
	 */
	function peutPlongerPlongeurProfondeurNitrox($plongeur, $profondeur){
		$profondeur = ceil($profondeur);
		if($profondeur <= 6)
			return true;
		if($plongeur->getAptitudes() == null && $profondeur > 6)
			return false;
		else{
			foreach($plongeur->getAptitudes() as $aptitude){
				if($aptitude->getNitroxMax() >= $profondeur)
					return true;
			}
			return false;
		}
	}
?>