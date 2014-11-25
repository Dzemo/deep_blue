<?php
	/**
	 * @author Raphaël Bideau - 3iL
	 * @package Test
	 *
	 * Fichier permettend de tester FicheSecurite et FicheSecuriteDao
	 */

	require_once("../classloader.php");

	$test_number = rand(0, 100);

	/* Test getAll*/
	echo "<br/>FicheSecurite::getAll()<br/>";
	$array = FicheSecuriteDao::getAll();
	foreach ($array as $elem) {
		echo "$elem<br/>";
	}

	/* Test insert */
	$ficheSecurite = new FicheSecurite(null);
	$ficheSecurite->setEmbarcation(EmbarcationDao::getById(1));
	$ficheSecurite->setDirecteurPlonge(MoniteurDao::getById(1));
	$ficheSecurite->setTimestamp(1412969841);
	$ficheSecurite->setSite("test-$test_number-site");
	$ficheSecurite->setEtat(FicheSecurite::etatCreer);
	$ficheSecurite = FicheSecuriteDao::insert($ficheSecurite, "test");
	echo "<br/>FicheSecuriteDao::insert() (version a ".$ficheSecurite->getVersion().")<br/>";

	/*Test getById*/
	$ficheSecurite = FicheSecuriteDao::getById($ficheSecurite->getId());
	echo "<br/>FicheSecuriteDao::getByid(): $ficheSecurite<br/>";

	/*Test update*/
	$ficheSecurite->setEmbarcation(EmbarcationDao::getById(1));
	$ficheSecurite->setDirecteurPlonge(MoniteurDao::getById(2));
	$ficheSecurite->setTimestamp(1412969841-267116);
	$ficheSecurite->setSite("test-$test_number-site-u");
	$ficheSecurite->setEtat(FicheSecurite::etatSynchronise);
	$ficheSecurite = FicheSecuriteDao::update($ficheSecurite);
	echo "<br/>FicheSecuriteDao::update() (version a ".$ficheSecurite->getVersion().")<br/>";
	
	/*Test getById*/
	$ficheSecurite = FicheSecuriteDao::getById($ficheSecurite->getId());
	echo "<br/>FicheSecuriteDao::getByid(): $ficheSecurite<br/>";

	/*Test getAllByEtat*/
	echo "<br/>FicheSecuriteDao::getAllByEtat()<br/>";
	$array = FicheSecuriteDao::getAllByEtat(FicheSecurite::etatSynchronise);
	foreach ($array as $elem) {
		echo "$elem<br/>";
	}

	/*Test des dates*/
	echo "<br>Test dates:<br>";
	echo "getDate: ".$ficheSecurite->getDate()."<br>";
	echo "getDateLong: ".$ficheSecurite->getDateLong()."<br>";
	echo "getTime: ".$ficheSecurite->getTime()."<br>";

	/*Suppression des inserts du test*/
	Dao::execute("DELETE FROM db_fiche_securite WHERE id_fiche_securite = ?",[$ficheSecurite->getId()]);
	echo "<br/> *** <br/>Test FicheSecurite effectue avec succes<br/> ***<br/>";


	/*Test getById*/
	$ficheSecurite = FicheSecuriteDao::getById(4);
	echo "<br/>FicheSecuriteDao::getByid(): $ficheSecurite<br/>";
?>