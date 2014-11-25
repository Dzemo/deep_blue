<?php
	/**
	 * @author Raphaël Bideau - 3iL
	 * @package Test
	 *
	 * Fichier permettend de tester Moniteur et MoniteurDao
	 */

	require_once("../classloader.php");

	$test_number = rand(0, 100);

	/* Test getAll*/
	echo "<br/>MoniteurDao::getAll()<br/>";
	$array = MoniteurDao::getAll();
	foreach ($array as $elem) {
		echo "$elem<br/>";
	}

	/* Test insert */
	$moniteur = new Moniteur(null);
	$moniteur->setNom("test-$test_number-nom");
	$moniteur->setPrenom("test-$test_number-prenom");
	$moniteur->setAptitudes(array());
	$moniteur->setActif(false);
	$moniteur->setEmail("test@email.com");
	$moniteur->setTelephone("04 50 12 56 30");
	$moniteur->setDirecteurPlonge(false);
	$moniteur = MoniteurDao::insert($moniteur);
	echo "<br/>MoniteurDao::insert() (version a ".$moniteur->getVersion().")<br/>";

	/*Test getById*/
	$moniteur = MoniteurDao::getById($moniteur->getId());
	echo "<br/>MoniteurDao::getById(): $moniteur<br/>";

	
	/*Test update*/
	$moniteur->setNom("test-$test_number-nom-u");
	$moniteur->setPrenom("test-$test_number-prenom-u");
	$moniteur->setAptitudes(AptitudeDao::getByIds(["1;2"]));
	$moniteur->setActif(true);
	$moniteur->setEmail("update@email.com");
	$moniteur->setTelephone("06 89 32 16 47");
	$moniteur->setDirecteurPlonge(true);
	$moniteur = MoniteurDao::update($moniteur);
	echo "<br/>MoniteurDao::update() (version a ".$moniteur->getVersion().")<br/>";

	/*Test getById*/
	$moniteur = MoniteurDao::getById($moniteur->getId());
	echo "<br/>MoniteurDao::getById(): $moniteur<br/>";

	/* Test getAllActifDirecteurPlonge*/
	echo "<br/>MoniteurDao::getAllActifDirecteurPlonge()<br/>";
	$array = MoniteurDao::getAllActifDirecteurPlonge();
	foreach ($array as $elem) {
		echo "$elem<br/>";
	}

	/*Suppression des inserts du test*/
	MoniteurDao::delete($moniteur->getId());
	echo "<br/> *** <br/>Test Moniteur effectue avec succes<br/> ***<br/>";

?>