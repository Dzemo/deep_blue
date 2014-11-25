<?php
	//Initialisation ou réinitialisation de mot de passe

	require_once("../session.php");

	if(isset($_SESSION['initialisation_mdp_login'])){
		$login = $_SESSION['initialisation_mdp_login'];
	}
	else if($connecte && isset($utilisateur)){
		$login = $utilisateur->getLogin();
	}
	else{
		header('Location: '.$GLOBALS['dns'].'index.php');
	}

	if(isset($_POST['mdp']) && strlen($_POST['mdp']) > 0){
		$mdp = filter_var($_POST['mdp'], FILTER_SANITIZE_STRING);
	}
	if(isset($_POST['mdp_confirme']) && strlen($_POST['mdp_confirme']) > 0){
		$mdp_confirme = filter_var($_POST['mdp_confirme'], FILTER_SANITIZE_STRING);
	}

	//Vérification que les mots de passe sont bien présent
	if(isset($mdp) && isset($mdp_confirme)){

		//Vérification que les mots de passe sont bien identique
		if(strcmp($mdp, $mdp_confirme) == 0){

			//Vérification que le mot de passe est bien different du login
			if(strcmp($login, $mdp) != 0){

				//Mise à jours du mot de passe
				$utilisateur = UtilisateurDao::getByLogin($login);
				$utilisateur->setMotDePasse(md5($mdp));
				$utilisateur = UtilisateurDao::updateMotDePasse($utilisateur);

				//Ajout de l'historique
				$historique = new Historique($utilisateur->getLogin(), time(), null);
				$historique->setSource(Historique::sourceWeb);
				if(isset($_POST["reinitialisation"]) && bolval($_POST["reinitialisation"]))
					$historique->setCommentaire("Réinitialisation du mot de passe");
				else
					$historique->setCommentaire("Initialisation du mot de passe");
				$historique = HistoriqueDao::insert($historique);

				//Enregistrement dans la session et renvoi vers l'accueil
				$_SESSION['utilisateur'] = $utilisateur;
				header('Location: '.$GLOBALS['dns'].'index.php');
			}
			else{
				header('Location: '.$GLOBALS['dns'].'index.php?page=initialisation_mot_de_passe&msg=initialisation_mdp_defaut');
			}
		}
		else{
			header('Location: '.$GLOBALS['dns'].'index.php?page=initialisation_mot_de_passe&msg=initialisation_champs_differents');
		}
	}
	else{
		header('Location: '.$GLOBALS['dns'].'index.php?page=initialisation_mot_de_passe&msg=initialisation_champs_manquants');
	}
?>