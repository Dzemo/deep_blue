<?php
		
	//Url complet du serveur sur lequel est installé l'application web
	$GLOBALS['dns'] = "http://www.oxygen.com/";
	
	//Info base de données
	$dbhost="localhost";	// Hôte de la base de données
	$dbuser="admin";		// Nom de connection à la base de données
	$dbpass="password";		// Mot de passe de connection à la base de données
	$dbname="deep_blue";	// Nom de la base de données

	//Nombre de plongeur suggéré dans le formulaire d'édition
	$GLOBALS['nombre_plongeur_suggerer'] = 15;
	// Profondeur par défaut (en metres)
	$GLOBALS['plongeDefaultProf'] = 20;
	// Durée par défaut (en metre)
	$GLOBALS['plongeDefaultDuree'] = 40;

	//Info envoi de mail
	$GLOBALS['email']['nom_expediteur'] = "Oxygen";					// Nom de l'expediteur des mails
	$GLOBALS['email']['adresse_expediteur'] = "contact@oxygen.com";	// Addresse de l'expedition des mails
	$GLOBALS['duree_validite_lien_reinitialisation'] = 24*60*60; 	// Durée de validité des liens de réinisialisation de mot de passe, en seconde (24h par défaut)
	$GLOBALS['nom_application'] = "Oxygen";							// Nom de l'application et interface web

	//Gestion des erreurs et affichage
	error_reporting(E_ALL);			// Affichage de toute les erreurs (Constante PHP)
	ini_set('display_errors','1'); 	// Affichages des erreurs à l'utilisateur ('1' pour activer, '0' pour désactiver)
	ini_set("log_errors", '1');		// Enregistrement des erreurs dans un fichier de log ('1' pour activer, '0' pour désactiver)
	ini_set("error_log", dirname(__FILE__).DIRECTORY_SEPARATOR."log".DIRECTORY_SEPARATOR."erreur.txt"); // Chemin du fichier de log relatif à ce fichier de configuration
?>