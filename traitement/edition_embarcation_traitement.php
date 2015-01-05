<?php
	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."session.php");
	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."utils".DIRECTORY_SEPARATOR."DateStringUtils.php");

	//////////////////////////////////////////////////////
	//Vérification qu'un utilisateur est bien connecté //
	//////////////////////////////////////////////////////
	if(!$connecte || !$utilisateur || !$utilisateur->isAdministrateur()){
		header('Location: '.$GLOBALS['dns']);
	}

	//Récupération id
	if(filter_input(INPUT_POST, 'embarcation_id', FILTER_VALIDATE_INT)){
		$embarcation_id = intval($_POST['embarcation_id']);
		$operationMsg = "modifiée";
		$operationHistorique = "Modification";
	}
	else{
		$embarcation_id = null;
		$operationMsg = "ajoutée";
		$operationHistorique = "Ajout";
	}

	//Récupération version
	if(filter_input(INPUT_POST, 'embarcation_version', FILTER_VALIDATE_INT)){
		$embarcation_version = intval($_POST['embarcation_version']);
	}
	else{
		$embarcation_version = 0;
	}

	$embarcation = new Embarcation($embarcation_id, $embarcation_version);
	//Structure message de retour:
	//msgType: (erreur|succes)
	//msg: multiple message séparé par des ;
	

	$msg = "";
	//Libelle
	if(isset($_POST['embarcation_libelle']) && strlen($_POST['embarcation_libelle']) > 0){
		$embarcation_libelle = filter_var($_POST['embarcation_libelle'], FILTER_SANITIZE_STRING);
		$embarcation->setLibelle($embarcation_libelle);
	}
	else{
		$msgType = "erreur";
		$msg .="Erreur lors de la validation du formulaire Embarcation: Libelle invalide ou manquant";
	}

	//Maxpersonne
	if(filter_input(INPUT_POST, 'embarcation_maxpersonne', FILTER_VALIDATE_INT)){
		$embarcation_maxpersonne = intval($_POST['embarcation_maxpersonne']);
		$embarcation->setMaxpersonne($embarcation_maxpersonne);
	}
	else{
		$msgType = "erreur";
		$msg .=";Erreur lors de la validation du formulaire Embarcation: Pas de contenance maximum précisé";
	}

	//Commentaire
	if(isset($_POST['embarcation_commentaire']) && strlen($_POST['embarcation_commentaire']) > 0){
		$embarcation_commentaire = filter_var($_POST['embarcation_commentaire'], FILTER_SANITIZE_STRING);
		$embarcation->setCommentaire($embarcation_commentaire);
	}
	else{
		$msgType = "erreur";
		$msg .=";Erreur lors de la validation du formulaire Embarcation: Commentaire invalide ou manquant";
	}

	//Disponible
	if(isset($_POST['embarcation_disponible'])){
		$embarcation->setDisponible(true);
	}
	else{
		$embarcation->setDisponible(false);
	}

	//Erreur ? on envoie et on quitte
	if(strlen($msg) != 0){
		header('Location: '.$GLOBALS['dns'].'index.php?page=administration&msg='.$msg.'&msgType='.$msgType);
		die();
	}

	//Pas d'erreur, on continue
	if($embarcation->getId() != null)
		$embarcation = EmbarcationDao::update($embarcation);
	else
		$embarcation = EmbarcationDao::insert($embarcation);
	if($embarcation == null){
		//erreur bdd
		$msgType = "erreur";
		$msg .="Erreur lors de l'accès à la base de données";
		header('Location: '.$GLOBALS['dns'].'index.php?page=administration&msg='.$msg.'&msgType='.$msgType);
		die();
	}
	else{
		//Ajout de l'historique
		$historique = new Historique($utilisateur->getLogin(), time(), null);
		$historique->setSource(Historique::sourceWeb);
		$historique->setCommentaire($operationHistorique." de l'embarcation ".$embarcation->getLibelle()." (id: ".$embarcation->getId().")");
		$historique = HistoriqueDao::insert($historique);

		//Renvoi vers l'administration avec un message
		$msgType = "succes";
		$msg .="Embarcation ".$embarcation->getLibelle()." ".$operationMsg." avec succès";
		header('Location: '.$GLOBALS['dns'].'index.php?page=administration&msg='.$msg.'&msgType='.$msgType);
		die();
	}
	
?>