<?php
	/**
	 * @author Raphaël Bideau - 3iL
	 * @package Test
	 *
	 * Fichier permettend de tester Palanque et PalanqueDao
	 */

	require_once("../classloader.php");

	$test_number = rand(0, 100);

	/* Test insert */
	$palanque = new Palanque(null);
	$palanque->setIdFicheSecurite(2);
	$palanque->setMoniteur(null);
	$palanque->setNumero($test_number);
	$palanque->setTypePlonge(Palanque::plongeAutonome);
	$palanque->setTypeGaz(Palanque::gazAir);
	$palanque->setCommentaire("Palanque de test $test_number");
	$palanque->setProfondeurPrevue(6);
	$palanque->setProfondeurRealisee(null);
	$palanque->setDureePrevue(600);
	$palanque->setDureeRealisee(null);
	$palanque = PalanqueDao::insert($palanque);
	echo "<br/>PalanqueDao::insert() (version a ".$palanque->getVersion().")<br/>";

	/*Test getById*/
	$palanque = PalanqueDao::getById($palanque->getId());
	echo "<br/>PalanqueDao::getById(): $palanque<br/>";

	
	/*Test update*/
	$palanque->setIdFicheSecurite(2);
	$palanque->setNumero($test_number+1);
	$palanque->setMoniteur(MoniteurDao::getById(1));
	$palanque->setTypePlonge(Palanque::plongeEncadre);
	$palanque->setTypeGaz(Palanque::gazNitrox);
	$palanque->setCommentaire("Palanque de test $test_number update");
	$palanque->setProfondeurPrevue(6);
	$palanque->setProfondeurRealisee(5.5);
	$palanque->setDureePrevue(600);
	$palanque->setDureeRealisee(600);

	$palanque = PalanqueDao::update($palanque);
	echo "<br/>PalanqueDao::update() (version a ".$palanque->getVersion().")<br/>";

	/*Test getById*/
	$palanque = PalanqueDao::getById($palanque->getId());
	echo "<br/>PalanqueDao::getById(): $palanque<br/>";

	/* Test getByIdFicheSecurite*/
	echo "<br/>PalanqueDao::getByIdFicheSecurite()<br/>";
	$array = PalanqueDao::getByIdFicheSecurite(2);
	foreach ($array as $elem) {
		echo "$elem<br/>";
	}

	/*Suppression des inserts du test*/
	Dao::execute("DELETE FROM db_palanque WHERE id_palanque = ?",[$palanque->getId()]);
	echo "<br/> *** <br/>Test Palanque effectue avec succes<br/> ***<br/>";

?>