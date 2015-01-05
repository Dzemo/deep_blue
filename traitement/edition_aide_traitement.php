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
	if(filter_input(INPUT_POST, 'aide_id', FILTER_VALIDATE_INT)){
		$aide_id = intval($_POST['aide_id']);
		$operationMsg = "modifiée";
		$operationHistorique = "Modification";
	}
	else{
		$aide_id = null;
		$operationMsg = "ajoutée";
		$operationHistorique = "Ajout";
	}

	//Récupération version
	$aide = new Aide($aide_id);
	//Structure message de retour:
	//msgType: (erreur|succes)
	//msg: multiple message séparé par des ;
	

	$msg = "";
	//Question
	if(isset($_POST['aide_question']) && strlen($_POST['aide_question']) > 0){
		$aide_question = filter_var($_POST['aide_question'], FILTER_SANITIZE_STRING);
		$aide->setQuestion($aide_question);
	}
	else{
		$msgType = "erreur";
		$msg .="Erreur lors de la validation du formulaire Aide: Question invalide ou manquante";
	}

	//Réponse
	if(isset($_POST['aide_reponse']) && strlen($_POST['aide_reponse']) > 0){
		$aide_reponse = filter_var($_POST['aide_reponse'], FILTER_SANITIZE_STRING);
		$aide->setReponse($aide_reponse);
	}
	else{
		$msgType = "erreur";
		$msg .="Erreur lors de la validation du formulaire Aide: Réponse invalide ou manquante";
	}


	//Disponible
	if(isset($_POST['aide_disponible'])){
		$aide->setDisponible(true);
	}
	else{
		$aide->setDisponible(false);
	}

	//TAG
	if(isset($_POST['aide_tag']) && strlen($_POST['aide_tag']) > 0){
		$aide_tag = filter_var($_POST['aide_tag'], FILTER_SANITIZE_STRING);
		$aide->setTag($aide_tag);
	}
	else{
		$msgType = "erreur";
		$msg .="Erreur lors de la validation du formulaire Aide: Tag invalide ou manquant";
	}

	//Voir Aussi
	if(isset($_POST['aide_voir_aussi']) && strlen($_POST['aide_voir_aussi']) > 0){
		$aide_voir_aussi = filter_var($_POST['aide_voir_aussi'], FILTER_SANITIZE_STRING);
		$aide->setVoirAussi($aide_voir_aussi);
	}

	//Erreur ? on envoie et on quitte
	if(strlen($msg) != 0){
		header('Location: '.$GLOBALS['dns'].'index.php?page=administration&msg='.$msg.'&msgType='.$msgType);
		die();
	}

	//Pas d'erreur, on continue
	if($aide->getId() != null)
		$aide = AideDao::update($aide);
	else
		$aide = AideDao::insert($aide);
		
	if($aide == null){
		//erreur bdd
		$msgType = "erreur";
		$msg .="Erreur lors de l'accès à l'écriture de la base de données";
		//header('Location: '.$GLOBALS['dns'].'index.php?page=administration&msg='.$msg.'&msgType='.$msgType);
		die();
	}
	else{
		//Ajout de l'historique
		$historique = new Historique($utilisateur->getLogin(), time(), null);
		$historique->setSource(Historique::sourceWeb);
		$historique->setCommentaire($operationHistorique." de l'aide ".$aide->getQuestion()." (id: ".$aide->getId().")");
		$historique = HistoriqueDao::insert($historique);

		//Renvoi vers l'administration avec un message
		$msgType = "succes";
		$msg .="Aide ".$aide->getQuestion()." ".$operationMsg." avec succès";
		header('Location: '.$GLOBALS['dns'].'index.php?page=administration&msg='.$msg.'&msgType='.$msgType.'&active=4');
		die();
	}
	
?>