<?php
	/**
	 * @author Raphaël Bideau - 3iL
	 * @package Test
	 *
	 * Fichier permettend de tester Utilisateur et UtilisateurDao
	 */
	require_once("../classloader.php");

	echo "<h1>Test utilisateur</h1><br/>";

	$test_number = rand(0, 100);
	$password_hash = md5("password");
	/* Test getAll*/
	echo "<br/>UtilisateurDao::getAll()<br/>";
	$array = UtilisateurDao::getAll();
	foreach ($array as $elem) {
		echo "$elem<br/>";
		echo "Administrateur?".": ".($elem->isAdministrateur() ? "oui" : "non")."<br/>";
	}

	/* Test insert */
	$utilisateur = new Utilisateur("test-$test_number");
	$utilisateur->setNom("test-$test_number-nom");
	$utilisateur->setPrenom("test-$test_number-prenom");
	$utilisateur->setMotDePasse($password_hash);
	$utilisateur->setAdministrateur(false);
	$utilisateur->setEmail("test-$test_number-email");
	$utilisateur->setActif(false);
	$utilisateur->setMoniteurAssocie(null);
	$utilisateur = UtilisateurDao::insert($utilisateur);
	echo "<br/>UtilisateurDao::insert() (version a ".$utilisateur->getVersion().")<br/>";

	/*Test getByLogin*/
	$utilisateur = UtilisateurDao::getByLogin($utilisateur->getLogin());
	echo "<br/>UtilisateurDao::getByLogin(): $utilisateur<br/>";
	
	/*Test update*/
	$utilisateur->setNom("test-$test_number-nom-u");
	$utilisateur->setPrenom("test-$test_number-prenom-u");
	$utilisateur->setMotDePasse($password_hash);
	$utilisateur->setAdministrateur(true);
	$utilisateur->setEmail("test-$test_number-email-u");
	$utilisateur->setActif(true);
	$utilisateur->setMoniteurAssocie(MoniteurDao::getAll()[0]);
	$utilisateur = UtilisateurDao::update($utilisateur);
	echo "<br/>UtilisateurDao::update() (version a ".$utilisateur->getVersion().")<br/>";

	/*Test Connection erreur*/
	$resultConnectionErreur = UtilisateurDao::authenticate($utilisateur->getLogin(), md5("wrong_password"));
	echo "<br>Mauvaise authentification : ".($resultConnectionErreur == null ? "non connecte" : "<strong>connecte</strong>");

	/*Test Connection succces*/
	$resultConnectionSucces = UtilisateurDao::authenticate($utilisateur->getLogin(), $password_hash);
	echo "<br>Bonne authentification : ".($resultConnectionSucces == null ? "<strong>non connecte</strong>" : "connecte")."<br/>";

	/*Test getByLogin*/
	$utilisateur = UtilisateurDao::getByLogin($utilisateur->getLogin());
	echo "<br/>UtilisateurDao::getByLogin(): $utilisateur<br/>";

	/*Test getAllActif*/
	echo "<br/>UtilisateurDao::getAllActif()<br/>";
	$array = UtilisateurDao::getAllActif();
	foreach ($array as $elem) {
		echo "$elem<br/>";
	}
	
	/*Suppression des inserts du test*/
	Dao::execute("DELETE FROM db_utilisateur WHERE login = ?",[$utilisateur->getLogin()]);
	echo "<br/> *** <br/>Test Utilisateur effectue avec succes<br/> ***<br/>";
?>