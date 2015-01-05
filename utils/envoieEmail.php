<?php
	
	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."config.php");
	
	/**
	 * Envoie un email en parametrant le header comme il faut
	 * @param  string $email        l'adresse e-mail à laquel envoyer cette e-mail
	 * @param  string $sujet        sujet du message
	 * @param  string $message_txt  message à envoyer au format text/plain
	 * @param  string $message_html message à envoyer au format text/html
	 * @return boolean              true si le mail à bien pu être envoyé, false sinon
	 */
	function envoieMail($email, $sujet, $message_txt, $message_html){
		$passage_ligne = getSautDeLigneForEmail($email);		
		 
		//=====Création de la boundary
		$boundary = "-----=".md5(rand());
		//==========
		
		//=====Création du header de l'e-mail.
		$header = "From: \"".$GLOBALS['email']['nom_expediteur']."\"<".$GLOBALS['email']['adresse_expediteur'].">".$passage_ligne;
		$header.= "Reply-to: \"".$GLOBALS['email']['nom_expediteur']."\" <".$GLOBALS['email']['adresse_expediteur'].">".$passage_ligne; 
		$header.= "X-Priority: 3".$passage_ligne;
		$header.= "MIME-Version: 1.0".$passage_ligne; 
		$header.= "Content-Type: multipart/alternative;".$passage_ligne." boundary=\"$boundary\"".$passage_ligne;
		//==========
	
		//=====Création du message.
		$message = $passage_ligne."--".$boundary.$passage_ligne;
		//=====Ajout du message au format texte.
		$message.= "Content-Type: text/plain; charset=\"ISO-8859-1\"".$passage_ligne;
		$message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
		$message.= $passage_ligne.$message_txt.$passage_ligne;
		//==========
		$message.= $passage_ligne."--".$boundary.$passage_ligne;
		//=====Ajout du message au format HTML
		$message.= "Content-Type: text/html; charset=\"ISO-8859-1\"".$passage_ligne;
		$message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
		$message.= $passage_ligne.$message_html.$passage_ligne;
		//==========
		$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
		$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
		//==========
		 
		//=====Envoi de l'e-mail.
		return mail($email,$sujet,$message,$header);
		//==========
	}
	/**
	 * Renvoi le type de saut de ligne à priviligier en fonction de l'adresse mail
	 * @param  string $email 
	 * @return string        "\r\n" ou "\n"
	 */
	function getSautDeLigneForEmail($email){
		if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $email)){
		    return "\r\n";
		}
		else{
		    return "\n";
		}
	}
?>