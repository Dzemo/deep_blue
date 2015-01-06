<?php
		
	$GLOBALS['dns'] = "http://127.0.0.1/path_to_oxygen/";
	
	//Info base de données
	$dbhost="localhost";
	$dbuser="admin";
	$dbpass="password";
	$dbname="deep_blue";

	//Nombre de plongeur suggéré dans le formulaire d'édition
	$GLOBALS['nombre_plongeur_suggerer'] = 15;
	// Profondeur par défaut (en metres)
	$GLOBALS['plongeDefaultProf'] = 20;
	// Durée par défaut (en metre)
	$GLOBALS['plongeDefaultDuree'] = 40;

	//Info envoi de mail
	$GLOBALS['email']['nom_expediteur'] = "Oxygen";
	$GLOBALS['email']['adresse_expediteur'] = "contact@oxygen.com";
	$GLOBALS['duree_validite_lien_reinitialisation'] = 24*60*60; //en seconde, 24h pour l'instant
	$GLOBALS['nom_application'] = "Oxygen";

	error_reporting(E_ALL);
	ini_set('display_errors','1'); 
	ini_set("log_errors", '1');
	ini_set("error_log", dirname(__FILE__).DIRECTORY_SEPARATOR."log".DIRECTORY_SEPARATOR."erreur.txt");
	
	$force_css_compile= false;
?>