<?php
	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."session.php");
	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."utils".DIRECTORY_SEPARATOR."envoieEmail.php");

	if(isset($_POST['login']) && strlen($_POST['login']) > 0){
		$login = filter_var($_POST['login'], FILTER_SANITIZE_STRING);
	}

	if(isset($login)){
		$utilisateur_reset = UtilisateurDao::getByLogin($login);
		if($utilisateur_reset != null){

			//////////////////////////
			//Génération du mail //
			//////////////////////////
			
			$sujet = "Reinitialisation de votre mot de passe";
			$crypter = new Crypter();
			$lien_reinitialisation = $GLOBALS['dns']."index.php?page=initialisation_mot_de_passe&jeton=".$crypter->crypte($utilisateur_reset->getLogin()."+".time());
			$passage_ligne = getSautDeLigneForEmail($utilisateur_reset->getEmail());
			//Génération du message text
			$message_text = "Bonjour ".$utilisateur_reset->getPrenom()." ".$utilisateur_reset->getNom()." ,".$passage_ligne;
			$message_text.= $passage_ligne;
			$message_text.= "Vous avez demandé la réinitialisation du mot de passe de votre compte ".$GLOBALS['nom_application'].$passage_ligne;
			$message_text.= "Pour pouvoir modifier votre mot de passe, veuillez copier le lien suivant dans votre navigateur :".$passage_ligne;
			$message_text.= $lien_reinitialisation.$passage_ligne;
			$message_text.= $passage_ligne;
			$message_text.= "Cordialement,".$passage_ligne;
			$message_text.= "L'équipe ".$GLOBALS['nom_application'].$passage_ligne;
			$message_text.= $passage_ligne;
			$message_text.= "Ce message a été envoyé automatiquement. Nous vous remercions de ne pas répondre.".$passage_ligne;
			$message_text.= "Si vous n'êtes pas à l'origine de cette demande, veuillez simplement ignorer ce courrier électronique.".$passage_ligne; 
			//Génération du message html
			$message_html = "Bonjour ".$utilisateur_reset->getPrenom()." ".$utilisateur_reset->getNom()." ,<br>";
			$message_html.= "<br>";
			$message_html.= "Vous avez demandé la réinitialisation du mot de passe de votre compte <a href=\"".$GLOBALS['dns']."\">".$GLOBALS['nom_application']."</a>.<br>";
			$message_html.= "Pour pouvoir modifier votre mot de passe, veuillez copier le lien suivant dans votre navigateur :<br>";
			$message_html.= "<a href=\"".$lien_reinitialisation."\">".$lien_reinitialisation."</a><br>";
			$message_html.= "<br>";
			$message_html.= "Cordialement,<br>";
			$message_html.= "L'équipe <a href=\"".$GLOBALS['dns']."\">".$GLOBALS['nom_application']."</a><br>";
			$message_html.= "<br>";
			$message_html.= "Ce message a été envoyé automatiquement. Nous vous remercions de ne pas répondre.<br>";
			$message_html.= "Si vous n'êtes pas à l'origine de cette demande, veuillez simplement ignorer ce courrier électronique.<br>"; 

			$result = envoieMail($utilisateur_reset->getEmail(), $sujet, $message_text, $message_html);

			echo $utilisateur;
			if($connecte && $utilisateur->isAdministrateur()){
				//Il s'agissant de la réinitialisation du mot de passe depuis l'administration
				if($result === false){
					header('Location: '.$GLOBALS['dns'].'index.php?page=administration&msg=Erreur lors de l\'envoi de l\'e-mail de réinitialisation de mot de passe à '.$utilisateur_reset->getPrenom().' '.$utilisateur_reset->getNom().'&msgType=erreur');
					die();
				}
				else{				
					header('Location: '.$GLOBALS['dns'].'index.php?page=administration&msg=Un e-mail de réinitialisation de mot de passe a été envoyé à '.$utilisateur_reset->getPrenom().' '.$utilisateur_reset->getNom().'&msgType=succes');
					die();
				}
			}
			else{
				//Il s'agissant de la réinitialisation du mot de passe depuis la page de connexion
				if($result === false){
					header('Location: '.$GLOBALS['dns'].'index.php?msg=reinitialisation_mail_erreur');
					die();
				}
				else{				
					header('Location: '.$GLOBALS['dns'].'index.php?msg=reinitialisation_succes');
					die();
				}
			}
		}

		header('Location: '.$GLOBALS['dns'].'index.php?msg=reinitialisation_succes');
		die();
	}
	else{
		header('Location: '.$GLOBALS['dns'].'index.php?msg=reinitialisation_champs_manquants');
		die();
	}
?>
