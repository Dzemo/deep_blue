<?php
	require_once("../session.php");
	require_once("../utils/DateStringUtils.php");
	require_once("../utils/validation_palanquee.php");
	
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
	
	//Récupération du site d'abord par id sinon par nom
	if(filter_input(INPUT_POST, 'id_site', FILTER_VALIDATE_INT)){
		$site = SiteDao::getById($_POST['id_site']);
		$ficheSecurite->setSite($site);
	}

	else if(isset($_POST['nom_site']) && strlen($_POST['nom_site']) > 0){
		$nom_site = filter_var($_POST['nom_site'], FILTER_SANITIZE_STRING);
		$site = new Site();
		$site->setNom($nom_site);
		$ficheSecurite->setSite($site);
	}
	
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
				strcmp($post_palanque['plonge'], Palanque::plongeAutonome) == 0 ||
				strcmp($post_palanque['plonge'], Palanque::plongeBapteme) == 0))	{	
							
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
			
			//Vérification de l'heure
			//Vérification du type de plongé
			if(isset($post_palanque['heure']) && strlen($post_palanque['heure']) > 0 )	{
				if(!filter_var($post_palanque['heure'], FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^\s*((([01]\d)|(2[0-3])):[0-6]\d)\s*$/"))))
				{
					$erreur_variable = true;
					$erreurs[] = ['numero' => $numero, 'type' => 'val_abs', 'msg' => '<strong>Heure</strong> de plongé invalide (Format HH:MM attendu)'];
				}
				else{
					$heure = $post_palanque['heure'];
					$palanque->setHeure($heure);
				}
			}

			//Vérification de la présence d'un moniteur et de ses valeurs
			if(isset($post_palanque['moniteur_id']) && strlen($post_palanque['moniteur_id']) > 0){
				//Récupération du moniteur à partir de son id
				if(filter_var($post_palanque['moniteur_id'], FILTER_VALIDATE_INT)){
					$moniteur = MoniteurDao::getById(intval($post_palanque['moniteur_id']));
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
				// LA FONCTION VALIDEPALANQUEE VERIFIE LES REGLES DE GESTIONS. ELLE EST DANS LE FICHIER
				// utils/validation_palanquee.php
				$erreurs_gestion = validePalanquee($palanque);

				$erreurs = array_merge_recursive($erreurs, $erreurs_gestion);
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
			echo json_encode(['erreurs' => array(array('numero' => 0, 'type' => 'general', 'msg' => 'Erreur en base de données lors de l\'enregistrement de la fiche de sécurité'))]);
		}
	}
?>
