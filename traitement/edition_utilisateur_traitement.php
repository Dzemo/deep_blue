<?php
	require_once("../session.php");
	require_once("../utils/DateStringUtils.php");
	require_once("../utils/envoieEmail.php");
	
	//////////////////////////////////////////////////////
	//Vérification qu'un utilisateur est bien connecté //
	//////////////////////////////////////////////////////
	if(!$connecte || $utilisateur == null || !$utilisateur->isAdministrateur()){
		header('Location: '.$GLOBALS['dns'].'index.php?');
	}
	
	//Récupération id
	if(isset($_POST['utilisateur_login']) && strlen($_POST['utilisateur_login']) > 0){
		$utilisateur_login = filter_var($_POST['utilisateur_login'], FILTER_SANITIZE_STRING);
		$operationMsg = "modifiée";
		$operationHistorique = "Modification";
	}
	else{
		$utilisateur_login = null;
		$operationMsg = "ajoutée";
		$operationHistorique = "Ajout";
	}
	
	//Récupération version
	if(filter_input(INPUT_POST, 'utilisateur_version', FILTER_VALIDATE_INT)){
		$utilisateur_version = intval($_POST['utilisateur_version']);
	}
	else{
		$utilisateur_version = 0;
	}
	
	$utilisateur_edit = new Utilisateur($utilisateur_login, $utilisateur_version);
	
	//Structure message de retour:
	//msgType: (erreur|succes)
	//msg: multiple message séparé par des ;
	$msg = "";
	
	//Nom
	if(isset($_POST['utilisateur_nom']) && strlen($_POST['utilisateur_nom']) > 0){
		$utilisateur_nom = filter_var($_POST['utilisateur_nom'], FILTER_SANITIZE_STRING);
		$utilisateur_edit->setNom($utilisateur_nom);
	}
	else{
		$msgType = "erreur";
		$msg .="Erreur lors de la validation du formulaire Utilisateur: Nom invalide ou manquant";
	}
	
	//Prénom
	if(isset($_POST['utilisateur_prenom']) && strlen($_POST['utilisateur_prenom']) > 0){
		$utilisateur_prenom = filter_var($_POST['utilisateur_prenom'], FILTER_SANITIZE_STRING);
		$utilisateur_edit->setPrenom($utilisateur_prenom);
	}
	else{
		$msgType = "erreur";
		$msg .=";Erreur lors de la validation du formulaire Utilisateur: Prenom invalide ou manquant";
	}
	
	//Email
	if(isset($_POST['utilisateur_email']) && strlen($_POST['utilisateur_email']) > 0){
		$utilisateur_email = filter_var($_POST['utilisateur_email'], FILTER_SANITIZE_STRING);
		$utilisateur_edit->setEmail($utilisateur_email);
	}
	else{
		$msgType = "erreur";
		$msg .=";Erreur lors de la validation du formulaire Utilisateur: Email invalide ou manquant";
	}
	
	//Administrateur
	if(isset($_POST['utilisateur_administrateur'])){
		$utilisateur_edit->setAdministrateur(true);
	}
	else{
		$utilisateur_edit->setAdministrateur(false);
	}
	
	//Actif
	if(isset($_POST['utilisateur_actif'])){
		$utilisateur_edit->setActif(true);
	}
	else{
		$utilisateur_edit->setActif(false);
	}
	
	
	//Erreur ? on envoie et on quitte
	if(strlen($msg) != 0){
		header('Location: '.$GLOBALS['dns'].'index.php?page=administration&msg='.$msg.'&msgType='.$msgType.'&active=0');
		die();
	}
	
	//Pas d'erreur, on continue
	if($utilisateur_edit->getLogin() != null)
		$utilisateur_edit = UtilisateurDao::update($utilisateur_edit);
	else{
		//Génération du login et suppression des éventuelles caractères accentués dans le nom/prenom
		$login_nom = htmlentities($utilisateur_edit->getNom(),ENT_NOQUOTES,'UTF-8');
		$login_prenom = htmlentities($utilisateur_edit->getPrenom(),ENT_NOQUOTES,'UTF-8');
		$login_nom = preg_replace('/&([A-Za-z]{1,2})(grave|acute|circ|cedil|uml|lig|ring|h|H|slash|tilde);/','$1',$login_nom);
		$login_prenom = preg_replace('/&([A-Za-z]{1,2})(grave|acute|circ|cedil|uml|lig|ring|h|H|slash|tilde);/','$1',$login_prenom);	
		$login = strtolower(substr($login_prenom, 0, 1).".".$login_nom);
	
		//On doit vérifier que le login n'existe pas déjà en base
		if(UtilisateurDao::getByLogin($login) == null){
			$utilisateur_edit->setLogin($login);
		}
		else{
			$tentative = 1;
			$loginTentative = $login.$tentative;
			while(UtilisateurDao::getByLogin($loginTentative) != null){
				$tentative++;
				$loginTentative = $login.$tentative;
			}
			$utilisateur_edit->setLogin($loginTentative);
		}

		//Génération du mail si l'utilisateur à été créer actif
		if($utilisateur_edit->getActif())
			genererEtEnvoiMailCreationCompte($utilisateur_edit);

		$utilisateur_edit->setMotDePasse(md5($utilisateur_edit->getLogin()));

		$utilisateur_edit = UtilisateurDao::insert($utilisateur_edit);
	}
	if($utilisateur_edit == null){

		//erreur bdd
		$msgType = "erreur";
		$msg .="Erreur lors de l'accès à la base de données";
		header('Location: '.$GLOBALS['dns'].'index.php?page=administration&msg='.$msg.'&msgType='.$msgType.'&active=0');
		die();
	}
	else{

		//Ajout de l'historique
		$historique = new Historique($utilisateur->getLogin(), time(), null);
		$historique->setSource(Historique::sourceWeb);
		$historique->setCommentaire($operationHistorique. " de l'utilisateur ".$utilisateur_edit->getPrenom()." ".$utilisateur_edit->getNom()." (login: ".$utilisateur_edit->getLogin().")");
		$historique = HistoriqueDao::insert($historique);

		//Renvoi vers l'administration avec un message
		$msgType = "succes";
		$msg .="Utilisateur ".$utilisateur_edit->getPrenom()." ".$utilisateur_edit->getNom()." ".$operationMsg." avec succès";
		header('Location: '.$GLOBALS['dns'].'index.php?page=administration&msg='.$msg.'&msgType='.$msgType.'&active=0');
		die();
	}
	
?>
<?php
	/**
	 * Génère et envoi un mail de création de compte à l'utilisateur spécifié
	 * @param  Utilisateur $utilisateur
	 * @return boolean              le résultat de l'envoi du mail
	 */
	function genererEtEnvoiMailCreationCompte($utilisateur){
		//////////////////////////
		//Génération du mail //
		//////////////////////////
		
		$sujet = "Création de votre compte ".$GLOBALS['nom_application'];
		$passage_ligne = getSautDeLigneForEmail($utilisateur->getEmail());
		//Génération du message text
		$message_text = "Bonjour ".$utilisateur->getPrenom()." ".$utilisateur->getNom()." ,".$passage_ligne;
		$message_text.= $passage_ligne;
		$message_text.= "Un compte utilisateur sur le site ".$GLOBALS['nom_application']." vous a été créé.".$passage_ligne;
		$message_text.= "Vous pouvez accéder à ".$GLOBALS['nom_application']." à cette addresse: ".$GLOBALS['dns'].$passage_ligne;
		$message_text.= $passage_ligne;
		$message_text.= "Vos identifiants sont: ".$passage_ligne;
		$message_text.= "Login: ".$utilisateur->getLogin().$passage_ligne;
		$message_text.= "Mot de passe: ".$utilisateur->getLogin().$passage_ligne;
		$message_text.= "Lors de votre première connexion, il vous sera demandé de définir un nouveau mot de passe personnalisé".$passage_ligne;
		$message_text.= $passage_ligne;
		$message_text.= "Cordialement,".$passage_ligne;
		$message_text.= "L'équipe ".$GLOBALS['nom_application'].$passage_ligne;
		$message_text.= $passage_ligne;
		$message_text.= "Ce message a été envoyé automatiquement. Nous vous remercions de ne pas répondre.".$passage_ligne;
		$message_text.= "Si vous n'êtes pas le destinataire de ce message, veuillez simplement ignorer ce courrier électronique.".$passage_ligne; 
		//Génération du message html
		$message_html = "Bonjour ".$utilisateur->getPrenom()." ".$utilisateur->getNom()." ,<br>";
		$message_html.= "<br>";
		$message_html.= "Un compte utilisateur sur le site <a href=\"".$GLOBALS['dns']."\">".$GLOBALS['nom_application']."</a> vous a été créé.<br>";
		$message_html.= "Vous pouvez accéder à l'application <a href=\"".$GLOBALS['dns']."\">".$GLOBALS['nom_application']."</a> à cette addresse: <a href=\"".$GLOBALS['dns']."\">".$GLOBALS['dns']."</a><br>";
		$message_html.= "<br>";
		$message_html.= "Vos identifiants sont: <br>";
		$message_html.= "Login: ".$utilisateur->getLogin()."<br>";
		$message_html.= "Mot de passe: ".$utilisateur->getLogin()."<br>";
		$message_html.= "Lors de votre première connexion, il vous sera demandé de définir un nouveau mot de passe personnalisé<br>";
		$message_html.= "<br>";
		$message_html.= "Cordialement,<br>";
		$message_html.= "L'équipe <a href=\"".$GLOBALS['dns']."\">".$GLOBALS['nom_application']."</a><br>";
		$message_html.= "<br>";
		$message_html.= "Ce message a été envoyé automatiquement. Nous vous remercions de ne pas répondre.<br>";
		$message_html.= "Si vous n'êtes pas le destinataire de ce message, veuillez simplement ignorer ce courrier électronique.<br>"; 
		$result = envoieMail($utilisateur->getEmail(), $sujet, $message_text, $message_html);
	}
?>