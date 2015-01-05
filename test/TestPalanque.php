<?php
	/**
	 * @author Raphaël Bideau - 3iL
	 * @package Test
	 *
	 * Fichier permettend de tester Palanque et PalanqueDao
	 */
	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."classloader.php");

	echo "<h1>Test palanquee</h1><br/>";

	$test_number = rand(0, 100);

	/* Test insert */
	$palanque = new Palanque(null);
	$palanque->setIdFicheSecurite(2);
	$palanque->setMoniteur(null);
	$palanque->setNumero($test_number);
	$palanque->setTypePlonge(Palanque::plongeAutonome);
	$palanque->setTypeGaz(Palanque::gazAir);
	$palanque->setProfondeurPrevue(6);
	$palanque->setDureePrevue(600);
	$palanque->setHeure("11:20");
	$palanque->setProfondeurRealiseeMoniteur(6.2);
	$palanque->setDureeRealiseeMoniteur(602);
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
	$palanque->setProfondeurPrevue(6);
	$palanque->setDureePrevue(600);
	$palanque->setHeure("16:20");
	$palanque->setProfondeurRealiseeMoniteur(6.1);
	$palanque->setDureeRealiseeMoniteur(601);
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
	Dao::execute("DELETE FROM db_palanquee WHERE id_palanquee = ?",[$palanque->getId()]);
	echo "<br/> *** <br/>Test Palanque effectue avec succes<br/> ***<br/>";

	/* Test Suppression de la palanquée */
	echo "<br/><br/><br/><h1>TEST SUPPRESSION</h1><br/>";
		// Création d'une fiche
		echo "<br/><h3>Création fiche</h1><br/>";
		$test_number = rand(0, 100);
		$palanque = new Palanque(null);
		$palanque->setIdFicheSecurite(999999);
		$palanque->setMoniteur(null);
		$palanque->setNumero($test_number);
		$palanque->setTypePlonge(Palanque::plongeAutonome);
		$palanque->setTypeGaz(Palanque::gazAir);
		$palanque->setProfondeurPrevue(6);
		$palanque->setDureePrevue(600);
		$palanque = PalanqueDao::insert($palanque);
		echo "<br/>PalanqueDao::insert() (version a ".$palanque->getVersion().")<br/>";

		// Ajout d'un plongeur
		$test_number = rand(0, 100);
		echo "<br/><h3>Ajout plongeur</h1><br/>";
		$plongeur = new Plongeur(null);
		$plongeur->setIdPalanque($palanque->getId());
		$plongeur->setIdFicheSecurite(999999);
		$plongeur->setNom("test-$test_number-nom");
		$plongeur->setPrenom("test-$test_number-prenom");
		$plongeur->setAptitudes(array());
		$plongeur->setDateNaissance("01/01/1990");
		$plongeur = PlongeurDao::insert($plongeur);
		echo "<br/>PlongeurDao::insert() (version a ".$plongeur->getVersion().")<br/>";
		// Ajout d'un 2eme plongeur
		$test_number = rand(0, 100);
		echo "<br/><h3>Ajout plongeur</h1><br/>";
		$plongeur = new Plongeur(null);
		$plongeur->setIdPalanque($palanque->getId());
		$plongeur->setIdFicheSecurite(999999);
		$plongeur->setNom("test-$test_number-nom2");
		$plongeur->setPrenom("test-$test_number-prenom2");
		$plongeur->setAptitudes(array());
		$plongeur->setDateNaissance("01/01/1990");
		$plongeur = PlongeurDao::insert($plongeur);
		echo "<br/>PlongeurDao::insert() (version a ".$plongeur->getVersion().")<br/>";

		//Affichage de la palanquée et de ses plongeurs
		echo "<br/><h3>Affichage</h1><br/>";
		$palanque = PalanqueDao::getById($palanque->getId());
		echo "<br/>PalanqueDao::getById(): $palanque<br/>";
		
		// Supression de la palanquée et de ses plongeurs
		echo "<br/><h3>Suppression palanquée</h1><br/>";
		PalanqueDao::delete($palanque);
		echo "<br/>PalanqueDao::delete($palanque)<br/>";
		 
		//Affichage de la palanquée et de ses plongeurs (apres supression) 
		echo "<br/><h3>Affichage</h1><br/>";
		$palanque = PalanqueDao::getById($palanque->getId());
		if($palanque != null)
			echo "<br/>PalanqueDao::getById(): $palanque<br/>";
		else
			echo "La palanqué n'existe pas";

?>