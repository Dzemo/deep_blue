<?php
	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."session.php");
	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."utils".DIRECTORY_SEPARATOR."DateStringUtils.php");
	
	header('Content-Type: text/plain; charset=utf-8');
	
	//////////////////////////////////////////////////////
	//Vérification qu'un moniteur est bien connecté //
	//////////////////////////////////////////////////////
	if(!$connecte || $utilisateur == null || !$utilisateur->isAdministrateur()){
		header('Location: '.$GLOBALS['dns'].'index.php?');
	}

	//Récupération id
	if(filter_input(INPUT_POST, 'site_id', FILTER_VALIDATE_INT)){
		$site_id = intval($_POST['site_id']);
		$operationMsg = "modifié";
		$operationHistorique = "Modification";
	}
	else{
		$site_id = null;
		$operationMsg = "ajouté";
		$operationHistorique = "Ajout";
	}

	$site = new Site($site_id);
	//Structure message de retour:
	//msgType: (erreur|succes)
	//msg: multiple message séparé par des ;
	$msg = "";
	
	//Nom
	if(isset($_POST['site_nom']) && strlen($_POST['site_nom']) > 0){
		$site_nom = filter_var($_POST['site_nom'], FILTER_SANITIZE_STRING);
		$site->setNom($site_nom);
	}
	else{
		$msgType = "erreur";
		$msg .="Erreur lors de la validation du formulaire Site: Nom invalide ou manquant";
	}
	
	//Commentaire
	if(isset($_POST['site_commentaire']) && strlen($_POST['site_commentaire']) > 0){
		$site_commentaire = filter_var($_POST['site_commentaire'], FILTER_SANITIZE_STRING);
		$site->setCommentaire($site_commentaire);
	}
	
	//Erreur ? on envoie et on quitte
	if(strlen($msg) != 0){
		header('Location: '.$GLOBALS['dns'].'index.php?page=administration&msg='.$msg.'&msgType='.$msgType.'&active=3');
		die();
	}

	//Pas d'erreur, on continue
	if($site->getId() != null)
		$site = SiteDao::update($site);
	else{
		$site = SiteDao::insert($site);
	}

	if($site == null){
		//erreur bdd
		$msgType = "erreur";
		$msg .="Erreur lors de l'accès à la base de données";
		header('Location: '.$GLOBALS['dns'].'index.php?page=administration&msg='.$msg.'&msgType='.$msgType.'&active=3');
		die();
	}
	else{
		//Ajout de l'historique
		$historique = new Historique($utilisateur->getLogin(), time(), null);
		$historique->setSource(Historique::sourceWeb);
		$historique->setCommentaire($operationHistorique. " du site ".$site->getNom()." (Id: ".$site->getId().")");
		$historique = HistoriqueDao::insert($historique);

		//Renvoi vers l'administration avec un message
		$msgType = "succes";
		$msg .="Site ".$site->getNom()." ".$operationMsg." avec succès";
		header('Location: '.$GLOBALS['dns'].'index.php?page=administration&msg='.$msg.'&msgType='.$msgType.'&active=3');
		die();
	}
	
?>