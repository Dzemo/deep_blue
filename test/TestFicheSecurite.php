<?php
	/**
	 * @author Raphaël Bideau - 3iL
	 * @package Test
	 *
	 * Fichier permettend de tester FicheSecurite et FicheSecuriteDao
	 */

	require_once("../classloader.php");


	echo "<h1>Test fiche securite</h1><br/>";

	$test_number = rand(0, 100);

	/* Test getAll*/
	echo "<br/><h3>FicheSecurite::getAll()</h3><br/>";
	$array = FicheSecuriteDao::getAll();
	foreach ($array as $elem) {
		echo "$elem<br/>";
	}

	/* Test insert */
	$ficheSecurite = new FicheSecurite(null);
	$ficheSecurite->setEmbarcation(EmbarcationDao::getById(1));
	$ficheSecurite->setDirecteurPlonge(MoniteurDao::getById(1));
	$ficheSecurite->setTimestamp(1412969841);
	$ficheSecurite->setSite(SiteDao::getById(1));
	$ficheSecurite->setEtat(FicheSecurite::etatCreer);
	$ficheSecurite = FicheSecuriteDao::insert($ficheSecurite, "test");
	echo "<br/><h3>FicheSecuriteDao::insert() (version a ".$ficheSecurite->getVersion().")</h3><br/>";

	/*Test getById*/
	$ficheSecurite = FicheSecuriteDao::getById($ficheSecurite->getId());
	echo "<br/><h3>FicheSecuriteDao::getByid()</h3>: $ficheSecurite<br/>";

	/*Test update*/
	$ficheSecurite->setEmbarcation(EmbarcationDao::getById(1));
	$ficheSecurite->setDirecteurPlonge(MoniteurDao::getById(2));
	$ficheSecurite->setTimestamp(1412969841-267116);
	$site = new Site();$site->setNom("site-$test_number");$site->setCommentaire("Site de test de la fiche de sécurité ".$ficheSecurite->getId());
	$ficheSecurite->setSite($site);
	$ficheSecurite->setEtat(FicheSecurite::etatModifie);
	$ficheSecurite = FicheSecuriteDao::update($ficheSecurite);
	echo "<br/><h3>FicheSecuriteDao::update() </h3>(version a ".$ficheSecurite->getVersion().")<br/>";
	
	/*Test getById*/
	$ficheSecurite = FicheSecuriteDao::getById($ficheSecurite->getId());
	echo "<br/><h3>FicheSecuriteDao::getByid()</h3>: $ficheSecurite<br/>";

	/*Test getAllByEtat*/
	echo "<br/><h3>FicheSecuriteDao::getAllByEtat()</h3><br/>";
	$array = FicheSecuriteDao::getAllByEtat(FicheSecurite::etatModifie);
	foreach ($array as $elem) {
		echo "$elem<br/>";
	}

	/*Test updateEtat*/
	$ficheSecurite = FicheSecuriteDao::updateEtat($ficheSecurite, FicheSecurite::etatSynchronise);

	/*Test getById*/
	$ficheSecurite = FicheSecuriteDao::getById($ficheSecurite->getId());
	echo "<br/><h3>FicheSecuriteDao::getByid()</h3>: $ficheSecurite<br/>";

	/*Test des dates*/
	echo "<br><h3>Test dates:</h3><br>";
	echo "getDate: ".$ficheSecurite->getDate()."<br>";
	echo "getDateLong: ".$ficheSecurite->getDateLong()."<br>";

	/*Test getFromVersionIdDpTimestamps*/
	$ficheSecuriteMaxVersion = 0;
	$idDirecteurPlongee = 1;
	$minTimestamps = 0;
	$maxTimestamps = time() + 2000000;
	echo "<br><h3>Test getFromVersionIdDpTimestamps</h3> (ficheSecuriteMaxVersion=$ficheSecuriteMaxVersion, idDirecteurPlongee=$idDirecteurPlongee, minTimestamps=$minTimestamps, maxTimestamps=$maxTimestamps)<br>";
	$array = FicheSecuriteDao::getFromVersionIdDpTimestamps($ficheSecuriteMaxVersion, $idDirecteurPlongee, $minTimestamps, $maxTimestamps);
	foreach ($array as $elem) {
		echo "$elem<br/>";
	}

	/*Suppression des inserts du test*/
	Dao::execute("DELETE FROM db_fiche_securite WHERE id_fiche_securite = ?",[$ficheSecurite->getId()]);
	echo "<br/> *** <br/>Test FicheSecurite effectue avec succes<br/> ***<br/>";


	/*Test getById*/
	$ficheSecurite = FicheSecuriteDao::getById(4);
	echo "<br/>FicheSecuriteDao::getByid(): $ficheSecurite<br/>";


	/* Test delete */
		/* Test insert */
		echo "<br/><br/><br/><h3>TEST DELETE</h3>";
		$ficheSecurite = new FicheSecurite(null);
		$ficheSecurite->setEmbarcation(EmbarcationDao::getById(1));
		$ficheSecurite->setDirecteurPlonge(MoniteurDao::getById(1));
		$ficheSecurite->setTimestamp(1412969800);
		$ficheSecurite->setSite(SiteDao::getById(3));
		$ficheSecurite->setEtat(FicheSecurite::etatCreer);
		$ficheSecurite = FicheSecuriteDao::insert($ficheSecurite, "test");


		// Création de 2 palanquées
				// Palanquée 1
				$test_number = rand(0, 100);
				$palanque = new Palanque(null);
				$palanque->setIdFicheSecurite($ficheSecurite->getId());
				$palanque->setMoniteur(null);
				$palanque->setNumero($test_number);
				$palanque->setTypePlonge(Palanque::plongeAutonome);
				$palanque->setTypeGaz(Palanque::gazAir);
				$palanque->setProfondeurPrevue(6);
				$palanque->setDureePrevue(600);
				$palanque = PalanqueDao::insert($palanque);

						// Ajout d'un plongeur
						$test_number = rand(0, 100);
						$plongeur = new Plongeur(null);
						$plongeur->setIdPalanque($palanque->getId());
						$plongeur->setIdFicheSecurite(999999);
						$plongeur->setNom("test-$test_number-nom");
						$plongeur->setPrenom("test-$test_number-prenom");
						$plongeur->setAptitudes(array());
						$plongeur->setDateNaissance("01/01/1990");
						$plongeur = PlongeurDao::insert($plongeur);
						// Ajout d'un 2eme plongeur
						$test_number = rand(0, 100);
						$plongeur = new Plongeur(null);
						$plongeur->setIdPalanque($palanque->getId());
						$plongeur->setIdFicheSecurite(999999);
						$plongeur->setNom("test-$test_number-nom2");
						$plongeur->setPrenom("test-$test_number-prenom2");
						$plongeur->setAptitudes(array());
						$plongeur->setDateNaissance("01/01/1990");
						$plongeur = PlongeurDao::insert($plongeur);

				// Palanquée 2
				$test_number = rand(0, 100);
				$palanque = new Palanque(null);
				$palanque->setIdFicheSecurite($ficheSecurite->getId());
				$palanque->setMoniteur(null);
				$palanque->setNumero($test_number);
				$palanque->setTypePlonge(Palanque::plongeAutonome);
				$palanque->setTypeGaz(Palanque::gazAir);
				$palanque->setProfondeurPrevue(6);
				$palanque->setDureePrevue(600);
				$palanque = PalanqueDao::insert($palanque);

						// Ajout d'un plongeur
						$test_number = rand(0, 100);
						$plongeur = new Plongeur(null);
						$plongeur->setIdPalanque($palanque->getId());
						$plongeur->setIdFicheSecurite(999999);
						$plongeur->setNom("test-$test_number-nom");
						$plongeur->setPrenom("test-$test_number-prenom");
						$plongeur->setAptitudes(array());
						$plongeur->setDateNaissance("01/01/1990");
						$plongeur = PlongeurDao::insert($plongeur);
						// Ajout d'un 2eme plongeur
						$test_number = rand(0, 100);
						$plongeur = new Plongeur(null);
						$plongeur->setIdPalanque($palanque->getId());
						$plongeur->setIdFicheSecurite(999999);
						$plongeur->setNom("test-$test_number-nom2");
						$plongeur->setPrenom("test-$test_number-prenom2");
						$plongeur->setAptitudes(array());
						$plongeur->setDateNaissance("01/01/1990");
						$plongeur = PlongeurDao::insert($plongeur);

		//Affichage de la fiche
		echo "<h3>Affichage de la fiche (avant le delete)</h3>";
		$ficheSecurite = FicheSecuriteDao::getById($ficheSecurite->getId());
		echo "FicheSecuriteDao::getByid(): $ficheSecurite<br/>";
		
		// Supression de la fiche
		FicheSecuriteDao::delete($ficheSecurite);
		 
		//Affichage de la fiche (apres supression) 
		echo "<h3>Affichage de la fiche (APRES le delete)</h3>";
		$ficheSecurite = FicheSecuriteDao::getById($ficheSecurite->getId());
		
		if($ficheSecurite != null)
			echo "FicheSecuriteDao::getById(): $ficheSecurite<br/>";
		else
			echo "La fiche n'existe pas";
?>