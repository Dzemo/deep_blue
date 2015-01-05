<?php
	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."session.php");
	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."utils".DIRECTORY_SEPARATOR."DateStringUtils.php");

	//////////////////////////////////////////////////////

	//Vérification qu'un moniteur est bien connecté //

	//////////////////////////////////////////////////////

	if(!$connecte || $utilisateur == null || !$utilisateur->isAdministrateur()){

		header('Location: '.$GLOBALS['dns'].'index.php?');

	}

	//Récupération id

	if(filter_input(INPUT_POST, 'moniteur_id', FILTER_VALIDATE_INT)){

		$moniteur_id = intval($_POST['moniteur_id']);

		$operationMsg = "modifiée";

		$operationHistorique = "Modification";

	}

	else{

		$moniteur_id = null;

		$operationMsg = "ajoutée";

		$operationHistorique = "Ajout";

	}

	//Récupération version

	if(filter_input(INPUT_POST, 'moniteur_version', FILTER_VALIDATE_INT)){

		$moniteur_version = intval($_POST['moniteur_version']);

	}

	else{

		$moniteur_version = 0;

	}

	$moniteur = new Moniteur($moniteur_id, $moniteur_version);

	//Structure message de retour:

	//msgType: (erreur|succes)

	//msg: multiple message séparé par des ;

	$msg = "";

	

	//Nom

	if(isset($_POST['moniteur_nom']) && strlen($_POST['moniteur_nom']) > 0){

		$moniteur_nom = filter_var($_POST['moniteur_nom'], FILTER_SANITIZE_STRING);

		$moniteur->setNom($moniteur_nom);

	}

	else{

		$msgType = "erreur";

		$msg .="Erreur lors de la validation du formulaire Moniteur: Nom invalide ou manquant";

	}

	//Prénom

	if(isset($_POST['moniteur_prenom']) && strlen($_POST['moniteur_prenom']) > 0){

		$moniteur_prenom = filter_var($_POST['moniteur_prenom'], FILTER_SANITIZE_STRING);

		$moniteur->setPrenom($moniteur_prenom);

	}

	else{

		$msgType = "erreur";

		$msg .=";Erreur lors de la validation du formulaire Moniteur: Prenom invalide ou manquant";

	}

	//Aptitudes

	if(isset($_POST['moniteur_aptitudes']) && is_array($_POST['moniteur_aptitudes'])){

		$aptitudes = AptitudeDao::getAll();

		foreach ($_POST['moniteur_aptitudes'] as $aptitude_id) {

			$moniteur->ajouterAptitude($aptitudes[$aptitude_id]);

		}

	}

	//Email

	if(isset($_POST['moniteur_email']) && strlen($_POST['moniteur_email']) > 0){

		$moniteur_email = filter_var($_POST['moniteur_email'], FILTER_SANITIZE_EMAIL);

		$moniteur->setEmail($moniteur_email);

	}

	//Pas d'erreur en cas d'email manquant

	

	//Téléphone

	if(isset($_POST['moniteur_telephone']) && strlen($_POST['moniteur_telephone']) > 0){

		$moniteur_telephone = filter_var($_POST['moniteur_telephone'], FILTER_SANITIZE_STRING);

		$moniteur->setTelephone($moniteur_telephone);

	}

	//Pas d'erreur en cas de téléphone manquant

	//Administrateur

	if(isset($_POST['moniteur_directeur_plonge'])){

		$moniteur->setDirecteurPlonge(true);

	}

	else{

		$moniteur->setDirecteurPlonge(false);

	}

	//Actif

	if(isset($_POST['moniteur_actif'])){

		$moniteur->setActif(true);

	}

	else{

		$moniteur->setActif(false);

	}

	//Erreur ? on envoie et on quitte

	if(strlen($msg) != 0){

		header('Location: '.$GLOBALS['dns'].'index.php?page=administration&msg='.$msg.'&msgType='.$msgType.'&active=1');

		die();

	}

	//Pas d'erreur, on continue

	if($moniteur->getId() != null)

		$moniteur = MoniteurDao::update($moniteur);

	else{

		$moniteur = MoniteurDao::insert($moniteur);

	}

	if($moniteur == null){

		//erreur bdd

		$msgType = "erreur";

		$msg .="Erreur lors de l'accès à la base de données";

		header('Location: '.$GLOBALS['dns'].'index.php?page=administration&msg='.$msg.'&msgType='.$msgType.'&active=1');

		die();

	}

	else{

		//Ajout de l'historique

		$historique = new Historique($utilisateur->getLogin(), time(), null);

		$historique->setSource(Historique::sourceWeb);

		$historique->setCommentaire($operationHistorique. " du moniteur ".$moniteur->getPrenom()." ".$moniteur->getNom()." (Id: ".$moniteur->getId().")");

		$historique = HistoriqueDao::insert($historique);

		//Renvoi vers l'administration avec un message

		$msgType = "succes";

		$msg .="Moniteur ".$moniteur->getPrenom()." ".$moniteur->getNom()." ".$operationMsg." avec succès";

		header('Location: '.$GLOBALS['dns'].'index.php?page=administration&msg='.$msg.'&msgType='.$msgType.'&active=1');

		die();

	}

	

?>