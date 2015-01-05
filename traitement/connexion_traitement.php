<?php
	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."session.php");

	if(isset($_POST['login']) && strlen($_POST['login']) > 0){
		$login = filter_var($_POST['login'], FILTER_SANITIZE_STRING);
	}
	if(isset($_POST['mdp']) && strlen($_POST['mdp']) > 0){
		$mdp = filter_var($_POST['mdp'], FILTER_SANITIZE_STRING);
	}
	if(isset($login) && isset($mdp)){
		$utilisateur = UtilisateurDao::authenticate($login, md5($mdp));
		if($utilisateur != null){
			$_SESSION['utilisateur'] = $utilisateur;
			if(strcmp($login, $mdp) == 0)
				header('Location: '.$GLOBALS['dns'].'index.php?page=initialisation_mot_de_passe');
			else
				header('Location: '.$GLOBALS['dns'].'index.php');
		}
		else{
			header('Location: '.$GLOBALS['dns'].'index.php?msg=connection_identifiants_invalides');
		}
	}
	else{
		header('Location: '.$GLOBALS['dns'].'index.php?msg=connection_champs_manquants');
	}
?>