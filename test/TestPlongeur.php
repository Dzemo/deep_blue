<?php
	/**
	 * @author Raphaël Bideau - 3iL
	 * @package Test
	 *
	 * Fichier permettend de tester Plongeur et PlongeurDao
	 */

	require_once("../classloader.php");

	$test_number = rand(0, 100);

	/* Test getAll*/
	echo "<br/>PlongeurDao::getAll()<br/>";
	$array = PlongeurDao::getAll();
	foreach ($array as $elem) {
		echo "$elem<br/>";
	}

	/* Test insert */
	$plongeur = new Plongeur(null);
	$plongeur->setIdPalanque(1);
	$plongeur->setIdFicheSecurite(2);
	$plongeur->setNom("test-$test_number-nom");
	$plongeur->setPrenom("test-$test_number-prenom");
	$plongeur->setAptitudes(array());
	$plongeur->setDateNaissance("01/01/1990");
	$plongeur = PlongeurDao::insert($plongeur);
	echo "<br/>PlongeurDao::insert() (version a ".$plongeur->getVersion().")<br/>";

	/*Test getById*/
	$plongeur = PlongeurDao::getById($plongeur->getId());
	echo "<br/>PlongeurDao::getById(): $plongeur<br/>";

	
	/*Test update*/
	$plongeur->setIdPalanque(2);
	$plongeur->setIdFicheSecurite(1);
	$plongeur->setNom("test-$test_number-nom-u");
	$plongeur->setPrenom("test-$test_number-prenom-u");
	$plongeur->setAptitudes(AptitudeDao::getByIds(["1;2"]));
	$plongeur->setDateNaissance("12/12/1990");
	$plongeur = PlongeurDao::update($plongeur);
	echo "<br/>PlongeurDao::update() (version a ".$plongeur->getVersion().")<br/>";

	/*Test getById*/
	$plongeur = PlongeurDao::getById($plongeur->getId());
	echo "<br/>PlongeurDao::getById(): $plongeur<br/>";

	/* Test getByIdPalanque*/
	echo "<br/>PlongeurDao::getByIdPalanque()<br/>";
	$array = PlongeurDao::getByIdPalanque(2);
	foreach ($array as $elem) {
		echo "$elem<br/>";
	}

	/* Test getByIdFicheSecurite*/
	echo "<br/>PlongeurDao::getByIdFicheSecurite()<br/>";
	$array = PlongeurDao::getByIdFicheSecurite(1);
	foreach ($array as $elem) {
		echo "$elem<br/>";
	}

	/*Suppression des inserts du test*/
	PlongeurDao::delete($plongeur->getId());
	echo "<br/> *** <br/>Test Plongeur effectue avec succes<br/> ***<br/>";

?>