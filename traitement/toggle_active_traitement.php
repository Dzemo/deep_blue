<?php
	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."session.php");
	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."utils".DIRECTORY_SEPARATOR."DateStringUtils.php");

	//////////////////////////////////////////////////////
	//Vérification qu'un utilisateur est bien connecté //
	//////////////////////////////////////////////////////
	if(!$connecte || !$utilisateur || !$utilisateur->isAdministrateur()){
		header('Location: '.$GLOBALS['dns']);
	}


	//Utilisateur
	if(isset($_POST['utilisateur_login']) && strlen($_POST['utilisateur_login']) > 0){
		$utilisateur_login = filter_var($_POST['utilisateur_login'], FILTER_SANITIZE_STRING);
		$utilisateur_toggle = UtilisateurDao::getBylogin($utilisateur_login);
		//1 pour actif, 0 pour inactif
		$actif = intval($_POST['utilisateur_actif']);
		//On ne peut pas se désactivé/activer sois même
		if(strcmp($utilisateur_toggle->getLogin(),$utilisateur->getLogin()) == 0){
			redirectMessage("Vous ne pouvez pas vous activer/desactiver vous-même", "erreur",0);	
		}
		if($utilisateur_toggle != null){
			$utilisateur_toggle->setActif($actif == 1);
			$utilisateur_toggle = UtilisateurDao::update($utilisateur_toggle);
			$actifMsg = $utilisateur_toggle->getActif() ? "actif" : "inactif";
			if($utilisateur_toggle != null){
				$historique = new Historique($utilisateur->getLogin(), time(), null);
				$historique->setSource(Historique::sourceWeb);
				$historique->setCommentaire("Utilisateur ".$utilisateur_toggle->getPrenom()." ".$utilisateur_toggle->getNom()." rendu ".$actifMsg);
				$historique = HistoriqueDao::insert($historique);
				//Renvoi vers l'administration avec un message
				redirectMessage("Utilisateur ".$utilisateur_toggle->getPrenom()." ".$utilisateur_toggle->getNom()." rendu ".$actifMsg, "succes",0);	
			}
			else{
				//erreur bdd
			redirectMessage("Erreur lors de l'accès à la base de données", "erreur",0);	
			}
		}
		else{
			//erreur bdd
			redirectMessage("Erreur lors de l'accès à la base de données", "erreur",0);			
		}
	}

	//Moniteur
	else if(filter_input(INPUT_POST, 'moniteur_id', FILTER_VALIDATE_INT)){
		$moniteur_id = intval($_POST['moniteur_id']);
		$moniteur = MoniteurDao::getById($moniteur_id);
		//1 pour actif, 0 pour inactif
		$actif = intval($_POST['moniteur_actif']);
		if($moniteur != null){
			$moniteur->setActif($actif == 1);
			$moniteur = MoniteurDao::update($moniteur);
			$actifMsg = $moniteur->estActif() ? "actif" : "inactif";
			if($moniteur != null){
				$historique = new Historique($utilisateur->getLogin(), time(), null);
				$historique->setSource(Historique::sourceWeb);
				$historique->setCommentaire("Moniteur ".$moniteur->getPrenom()." ".$moniteur->getNom()." rendu ".$actifMsg);
				$historique = HistoriqueDao::insert($historique);
				//Renvoi vers l'administration avec un message
				redirectMessage("Moniteur ".$moniteur->getPrenom()." ".$moniteur->getNom()." rendu ".$actifMsg, "succes",1);	
			}
			else{
				//erreur bdd
			redirectMessage("Erreur lors de l'accès à la base de données", "erreur",1);	
			}
		}
		else{
			//erreur bdd
			redirectMessage("Erreur lors de l'accès à la base de données", "erreur",1);	
		}
	}

	//Embarcation
	else if(filter_input(INPUT_POST, 'embarcation_id', FILTER_VALIDATE_INT)){
		$embarcation_id = intval($_POST['embarcation_id']);
		$embarcation = EmbarcationDao::getById($embarcation_id);
		//1 pour disponible, 0 pour indisponible
		$disponible = intval($_POST['embarcation_disponible']);
		if($embarcation != null){
			$embarcation->setDisponible($disponible == 1);
			$embarcation = EmbarcationDao::update($embarcation);
			$disponibleMsg = $embarcation->getDisponible() ? "disponible" : "indisponible";
			if($embarcation != null){
				$historique = new Historique($utilisateur->getLogin(), time(), null);
				$historique->setSource(Historique::sourceWeb);
				$historique->setCommentaire("Embarcation ".$embarcation->getLibelle()." rendu ".$disponibleMsg);
				$historique = HistoriqueDao::insert($historique);
				//Renvoi vers l'administration avec un message
				redirectMessage("Embarcation ".$embarcation->getLibelle()." rendu ".$disponibleMsg, "succes",2);	
			}
			else{
				//erreur bdd
			redirectMessage("Erreur lors de l'accès à la base de données", "erreur",2);	
			}
		}
		else{
			//erreur bdd
			redirectMessage("Erreur lors de l'accès à la base de données", "erreur",2);	
		}
	}

	//Site
	else if(filter_input(INPUT_POST, 'site_id', FILTER_VALIDATE_INT)){
		$site_id = intval($_POST['site_id']);
		
		$site = SiteDao::getById($site_id);
		if($site != null){
			SiteDao::delete($site_id);
			
			//Ajout de l'historique
			$historique = new Historique($utilisateur->getLogin(), time(), null);
			$historique->setSource(Historique::sourceWeb);
			$historique->setCommentaire("Site ".$site->getNom()." supprimé");
			$historique = HistoriqueDao::insert($historique);
			
			//Renvoi vers l'administration avec un message
			redirectMessage("Site ".$site->getNom()." supprimé", "succes",3);	
		
		}
		else{
			//erreur bdd
			redirectMessage("Erreur lors de l'accès à la base de données", "erreur",3);	
		}
	}

	//Aide
	else if(filter_input(INPUT_POST, 'aide_id', FILTER_VALIDATE_INT)){
		$aide_id = intval($_POST['aide_id']);
		$aide = AideDao::getById($aide_id);
		if($aide != null){
			if($aide->getDisponible() == FALSE)
				$aide->setDisponible(true);
			else
				$aide->setDisponible(false);
			$aide = AideDao::update($aide);
						
			//Ajout de l'historique
			$historique = new Historique($utilisateur->getLogin(), time(), null);
			$historique->setSource(Historique::sourceWeb);
			$historique->setCommentaire("Disponibilité Aide ".$aide->getQuestion()." mise à jour");
			$historique = HistoriqueDao::insert($historique);
			
			//Renvoi vers l'administration avec un message
			redirectMessage("Aide ".$aide->getQuestion()." mise à jours", "succes",4);	
		
		}
		else{
			//erreur bdd
			redirectMessage("Erreur lors de l'accès à la base de données", "erreur",4);	
		}
	}

	//Redirection si aucun id specifier
	else{
		redirectMessage("", "",0);	
	}
?>
<?php
	function redirectMessage($msg, $msgType, $activeIndex){
		header('Location: '.$GLOBALS['dns'].'index.php?page=administration&msg='.$msg.'&msgType='.$msgType.'&active='.$activeIndex);
		die();
	}
?>