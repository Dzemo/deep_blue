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
	$ficheSecurite->setSite(SiteDao::getById(1));
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
	$site = new Site();$site->setNom("site-$test_number");$site->setCommentaire("Site de test de la fiche de sécurité ".$ficheSecurite->getId());
	$ficheSecurite->setSite($site);
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


	/* Test delete */
		/* Test insert */
		echo "<br/><br/><br/><h1>TEST DELETE</h1><br/>";
		echo "<br/><h3>Création fiche</h3><br/>";
		$ficheSecurite = new FicheSecurite(null);
		$ficheSecurite->setEmbarcation(EmbarcationDao::getById(1));
		$ficheSecurite->setDirecteurPlonge(MoniteurDao::getById(1));
		$ficheSecurite->setTimestamp(1412969800);
		$ficheSecurite->setSite(SiteDao::getById(3));
		$ficheSecurite->setEtat(FicheSecurite::etatCreer);
		$ficheSecurite = FicheSecuriteDao::insert($ficheSecurite, "test");
		echo "<br/>FicheSecuriteDao::insert() (version a ".$ficheSecurite->getVersion().")<br/>";


		// Création de 2 palanquées
				// Palanquée 1
				echo "<br/><h3>Création palanquée 1</h3><br/>";
				$test_number = rand(0, 100);
				$palanque = new Palanque(null);
				$palanque->setIdFicheSecurite($ficheSecurite->getId());
				$palanque->setMoniteur(null);
				$palanque->setNumero($test_number);
				$palanque->setTypePlonge(Palanque::plongeAutonome);
				$palanque->setTypeGaz(Palanque::gazAir);
				$palanque->setProfondeurPrevue(6);
				$palanque->setProfondeurRealisee(null);
				$palanque->setDureePrevue(600);
				$palanque->setDureeRealisee(null);
				$palanque = PalanqueDao::insert($palanque);
				echo "<br/>PalanqueDao::insert() (version a ".$palanque->getVersion().")<br/>";

						// Ajout d'un plongeur
						$test_number = rand(0, 100);
						echo "<br/><h3>Ajout plongeur 1</h3><br/>";
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
						echo "<br/><h3>Ajout plongeur 2</h3><br/>";
						$plongeur = new Plongeur(null);
						$plongeur->setIdPalanque($palanque->getId());
						$plongeur->setIdFicheSecurite(999999);
						$plongeur->setNom("test-$test_number-nom2");
						$plongeur->setPrenom("test-$test_number-prenom2");
						$plongeur->setAptitudes(array());
						$plongeur->setDateNaissance("01/01/1990");
						$plongeur = PlongeurDao::insert($plongeur);
						echo "<br/>PlongeurDao::insert() (version a ".$plongeur->getVersion().")<br/>";

				// Palanquée 2
				echo "<br/><h3>Création palanquée 2</h3><br/>";
				$test_number = rand(0, 100);
				$palanque = new Palanque(null);
				$palanque->setIdFicheSecurite($ficheSecurite->getId());
				$palanque->setMoniteur(null);
				$palanque->setNumero($test_number);
				$palanque->setTypePlonge(Palanque::plongeAutonome);
				$palanque->setTypeGaz(Palanque::gazAir);
				$palanque->setProfondeurPrevue(6);
				$palanque->setProfondeurRealisee(null);
				$palanque->setDureePrevue(600);
				$palanque->setDureeRealisee(null);
				$palanque = PalanqueDao::insert($palanque);
				echo "<br/>PalanqueDao::insert() (version a ".$palanque->getVersion().")<br/>";

						// Ajout d'un plongeur
						$test_number = rand(0, 100);
						echo "<br/><h3>Ajout plongeur 3</h3><br/>";
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
						echo "<br/><h3>Ajout plongeur 4</h3><br/>";
						$plongeur = new Plongeur(null);
						$plongeur->setIdPalanque($palanque->getId());
						$plongeur->setIdFicheSecurite(999999);
						$plongeur->setNom("test-$test_number-nom2");
						$plongeur->setPrenom("test-$test_number-prenom2");
						$plongeur->setAptitudes(array());
						$plongeur->setDateNaissance("01/01/1990");
						$plongeur = PlongeurDao::insert($plongeur);
						echo "<br/>PlongeurDao::insert() (version a ".$plongeur->getVersion().")<br/>";

		//Affichage de la fiche
		echo "<br/><h3>Affichage de la fiche (avant le delete)</h3><br/>";
		$ficheSecurite = FicheSecuriteDao::getById($ficheSecurite->getId());
		echo "<br/>FicheSecuriteDao::getByid(): $ficheSecurite<br/>";
		
		// Supression de la fiche
		FicheSecuriteDao::delete($ficheSecurite);
		 
		//Affichage de la fiche (apres supression) 
		echo "<br/><h3>Affichage de la fiche (APRES le delete)</h3><br/>";
		$ficheSecurite = FicheSecuriteDao::getById($ficheSecurite->getId());
		
		if($ficheSecurite != null)
			echo "<br/>FicheSecuriteDao::getById(): $ficheSecurite<br/>";
		else
			echo "La fiche n'existe pas";
?>