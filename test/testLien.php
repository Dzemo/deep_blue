<?php
	require_once("../classloader.php");
	require_once("../config.php");


	echo "<h1>Test lien</h1><br/>";

	$utilisateur = UtilisateurDao::getByLogin("admin");
	echo $utilisateur."<br>";

	$crypter = new Crypter();
	
	$lien_reinitialisation = $GLOBALS['dns']."index.php?page=initialisation_mot_de_passe&token=".$crypter->crypte($utilisateur->getLogin().time());
	echo "Lien: '".$lien_reinitialisation."'<br>";

	$hash = "ON6gMfWrlPKnbTplTUGN1QRqp7kZKOfAzR8EmOIGGJ4=";
	echo "Decrypte '".$hash."' => ".$crypter->decrypte($hash)."<br>";
?>