<?php
	header('Content-Type: text/html; charset=utf-8');

	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."classloader.php");
	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."utils".DIRECTORY_SEPARATOR."validation_palanquee.php");

	$execution = false;

	echo "<h1>Test validation palanquee</h1><br/>";


	/* TEST 3 : Test des plongées techniques (enseignement) */
	echo "<h1>Test des plongées techniques (enseignement)</h1>";
	echo "<h3>Test d'une plongée techniques en effectif minimal</h3>";
	$palanque = new Palanque(666);
	$palanque->setTypeGaz(Palanque::gazAir);
	$palanque->setTypePlonge(Palanque::plongeTechnique);
	$palanque->setProfondeurPrevue(20.0);
	$palanque->setNumero(1);

	$moniteur = generateMoniteur();
	$moniteur->ajouterAptitude(AptitudeDao::getByLibelleCourt("E-2"));
	$palanque->setMoniteur($moniteur);
	
	$plongeur1 = generatePlongeur();
	$plongeur1->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-12"));
	$palanque->setPlongeurs($plongeur1);


	if($execution)
		testPalanque($palanque, true);

	echo "<h3>Test d'une plongée techniques en effectif maximal classique (4 plongeurs + 1 moniteur)</h1>";
	$palanque = new Palanque(rand(0,100));
	$palanque->setTypeGaz(Palanque::gazAir);
	$palanque->setTypePlonge(Palanque::plongeTechnique);
	$palanque->setProfondeurPrevue(35.0);
	$palanque->setNumero(1);

	$moniteur = generateMoniteur();
	$moniteur->ajouterAptitude(AptitudeDao::getByLibelleCourt("E-3"));
	$palanque->setMoniteur($moniteur);
	
	$plongeur1 = generatePlongeur();
	$plongeur1->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur2 = generatePlongeur();
	$plongeur2->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur3 = generatePlongeur();
	$plongeur3->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur4 = generatePlongeur();
	$plongeur4->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));

	$palanque->setPlongeurs(array($plongeur1, $plongeur2, $plongeur3, $plongeur4));

	testPalanque($palanque, true);

	echo "<h3>Test d'une plongée techniques en effectif maximal + GP (1 moniteur + 4 plongeurs + 1 GP)</h1>";
	$palanque = new Palanque(rand(0,100));
	$palanque->setTypeGaz(Palanque::gazAir);
	$palanque->setTypePlonge(Palanque::plongeTechnique);
	$palanque->setProfondeurPrevue(35.0);
	$palanque->setNumero(1);

	$moniteur = generateMoniteur();
	$moniteur->ajouterAptitude(AptitudeDao::getByLibelleCourt("E-3"));
	$palanque->setMoniteur($moniteur);
	
	$plongeur1 = generatePlongeur();
	$plongeur1->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur2 = generatePlongeur();
	$plongeur2->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur3 = generatePlongeur();
	$plongeur3->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur4 = generatePlongeur();
	$plongeur4->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));

	$plongeur5 = generatePlongeur();
	$plongeur5->ajouterAptitude(AptitudeDao::getByLibelleCourt("GP"));
	
	$palanque->setPlongeurs(array($plongeur1, $plongeur2, $plongeur3, $plongeur4, $plongeur5));

	testPalanque($palanque, true);

	echo "<h3>Test d'une plongée techniques en effectif maximal + P-4 (1 moniteur + 4 plongeurs + 1 P-4)</h1>";
	$palanque = new Palanque(rand(0,100));
	$palanque->setTypeGaz(Palanque::gazAir);
	$palanque->setTypePlonge(Palanque::plongeTechnique);
	$palanque->setProfondeurPrevue(35.0);
	$palanque->setNumero(1);

	$moniteur = generateMoniteur();
	$moniteur->ajouterAptitude(AptitudeDao::getByLibelleCourt("E-3"));
	$palanque->setMoniteur($moniteur);
	
	$plongeur1 = generatePlongeur();
	$plongeur1->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur2 = generatePlongeur();
	$plongeur2->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur3 = generatePlongeur();
	$plongeur3->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur4 = generatePlongeur();
	$plongeur4->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));

	$plongeur5 = generatePlongeur();
	$plongeur5->ajouterAptitude(AptitudeDao::getByLibelleCourt("P-4"));
	
	$palanque->setPlongeurs(array($plongeur1, $plongeur2, $plongeur3, $plongeur4, $plongeur5));

	testPalanque($palanque, true);

	echo "<h3>Test d'une plongée techniques en sur-effectif(1 moniteur + 5 plongeurs (hors GP/P4))</h1>";
	$palanque = new Palanque(rand(0,100));
	$palanque->setTypeGaz(Palanque::gazAir);
	$palanque->setTypePlonge(Palanque::plongeTechnique);
	$palanque->setProfondeurPrevue(35.0);
	$palanque->setNumero(1);

	$moniteur = generateMoniteur();
	$moniteur->ajouterAptitude(AptitudeDao::getByLibelleCourt("E-3"));
	$palanque->setMoniteur($moniteur);
	
	$plongeur1 = generatePlongeur();
	$plongeur1->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur2 = generatePlongeur();
	$plongeur2->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur3 = generatePlongeur();
	$plongeur3->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur4 = generatePlongeur();
	$plongeur4->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur5 = generatePlongeur();
	$plongeur5->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	
	$palanque->setPlongeurs(array($plongeur1, $plongeur2, $plongeur3, $plongeur4, $plongeur5));

	testPalanque($palanque, true);

	echo "<h3>Test d'une plongée techniques en sur-effectif (1 moniteur + 4 plongeurs + 2 P-4)</h1>";
	$palanque = new Palanque(rand(0,100));
	$palanque->setTypeGaz(Palanque::gazAir);
	$palanque->setTypePlonge(Palanque::plongeTechnique);
	$palanque->setProfondeurPrevue(35.0);
	$palanque->setNumero(1);

	$moniteur = generateMoniteur();
	$moniteur->ajouterAptitude(AptitudeDao::getByLibelleCourt("E-3"));
	$palanque->setMoniteur($moniteur);
	
	$plongeur1 = generatePlongeur();
	$plongeur1->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur2 = generatePlongeur();
	$plongeur2->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur3 = generatePlongeur();
	$plongeur3->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur4 = generatePlongeur();
	$plongeur4->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));

	$plongeur5 = generatePlongeur();
	$plongeur5->ajouterAptitude(AptitudeDao::getByLibelleCourt("P-4"));
	$plongeur6 = generatePlongeur();
	$plongeur6->ajouterAptitude(AptitudeDao::getByLibelleCourt("P-4"));
	
	$palanque->setPlongeurs(array($plongeur1, $plongeur2, $plongeur3, $plongeur4, $plongeur5, $plongeur6));

	testPalanque($palanque, true);

	echo "<h3>Test d'une plongée techniques en sur-effectif pour une profondeur de 60 (1 moniteur + 4 plongeurs + 1 GP)</h1>";
	$palanque = new Palanque(rand(0,100));
	$palanque->setTypeGaz(Palanque::gazAir);
	$palanque->setTypePlonge(Palanque::plongeTechnique);
	$palanque->setProfondeurPrevue(55.0);
	$palanque->setNumero(1);

	$moniteur = generateMoniteur();
	$moniteur->ajouterAptitude(AptitudeDao::getByLibelleCourt("E-4"));
	$palanque->setMoniteur($moniteur);
	
	$plongeur1 = generatePlongeur();
	$plongeur1->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-40"));
	$plongeur2 = generatePlongeur();
	$plongeur2->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-40"));
	$plongeur3 = generatePlongeur();
	$plongeur3->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-40"));
	$plongeur4 = generatePlongeur();
	$plongeur4->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-40"));

	$plongeur5 = generatePlongeur();
	$plongeur5->ajouterAptitude(AptitudeDao::getByLibelleCourt("P-4"));

	
	$palanque->setPlongeurs(array($plongeur1, $plongeur2, $plongeur3, $plongeur4, $plongeur5));

	testPalanque($palanque, true);

	/* TEST 4 : Test des plongées encadré (enseignement) */
	echo "<h3>Test des plongées encadré (exploration)</h1>";
	echo "<h3>Test d'une plongée encadré en effectif minimal</h1>";
	$palanque = new Palanque(rand(0,100));
	$palanque->setTypeGaz(Palanque::gazAir);
	$palanque->setTypePlonge(Palanque::plongeEncadre);
	$palanque->setProfondeurPrevue(12.0);
	$palanque->setNumero(1);

	$moniteur = generateMoniteur();
	$moniteur->ajouterAptitude(AptitudeDao::getByLibelleCourt("E-2"));
	$palanque->setMoniteur($moniteur);
	
	$plongeur1 = generatePlongeur();
	$plongeur1->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-12"));
	$palanque->setPlongeurs($plongeur1);

	testPalanque($palanque, true);

	echo "<h3>Test d'une plongée techniques en effectif maximal classique (4 plongeurs + 1 moniteur)</h1>";
	$palanque = new Palanque(rand(0,100));
	$palanque->setTypeGaz(Palanque::gazAir);
	$palanque->setTypePlonge(Palanque::plongeEncadre);
	$palanque->setProfondeurPrevue(35.0);
	$palanque->setNumero(1);

	$moniteur = generateMoniteur();
	$moniteur->ajouterAptitude(AptitudeDao::getByLibelleCourt("E-3"));
	$palanque->setMoniteur($moniteur);
	
	$plongeur1 = generatePlongeur();
	$plongeur1->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-40"));
	$plongeur2 = generatePlongeur();
	$plongeur2->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-40"));
	$plongeur3 = generatePlongeur();
	$plongeur3->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-40"));
	$plongeur4 = generatePlongeur();
	$plongeur4->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-40"));

	$palanque->setPlongeurs(array($plongeur1, $plongeur2, $plongeur3, $plongeur4));

	testPalanque($palanque, true);

	echo "<h3>Test d'une plongée techniques en effectif maximal + GP (1 moniteur + 4 plongeurs + 1 GP)</h1>";
	$palanque = new Palanque(rand(0,100));
	$palanque->setTypeGaz(Palanque::gazAir);
	$palanque->setTypePlonge(Palanque::plongeEncadre);
	$palanque->setProfondeurPrevue(35.0);
	$palanque->setNumero(1);

	$moniteur = generateMoniteur();
	$moniteur->ajouterAptitude(AptitudeDao::getByLibelleCourt("E-3"));
	$palanque->setMoniteur($moniteur);
	
	$plongeur1 = generatePlongeur();
	$plongeur1->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-40"));
	$plongeur2 = generatePlongeur();
	$plongeur2->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-40"));
	$plongeur3 = generatePlongeur();
	$plongeur3->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-40"));
	$plongeur4 = generatePlongeur();
	$plongeur4->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-40"));

	$plongeur5 = generatePlongeur();
	$plongeur5->ajouterAptitude(AptitudeDao::getByLibelleCourt("GP"));
	
	$palanque->setPlongeurs(array($plongeur1, $plongeur2, $plongeur3, $plongeur4, $plongeur5));

	testPalanque($palanque, true);

	echo "<h3>Test d'une plongée techniques en sur-effectif(1 moniteur + 5 plongeurs (hors GP/P4))</h1>";
	$palanque = new Palanque(rand(0,100));
	$palanque->setTypeGaz(Palanque::gazAir);
	$palanque->setTypePlonge(Palanque::plongeEncadre);
	$palanque->setProfondeurPrevue(19.0);
	$palanque->setNumero(1);

	$moniteur = generateMoniteur();
	$moniteur->ajouterAptitude(AptitudeDao::getByLibelleCourt("E-3"));
	$palanque->setMoniteur($moniteur);
	
	$plongeur1 = generatePlongeur();
	$plongeur1->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur2 = generatePlongeur();
	$plongeur2->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur3 = generatePlongeur();
	$plongeur3->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur4 = generatePlongeur();
	$plongeur4->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur5 = generatePlongeur();
	$plongeur5->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	
	$palanque->setPlongeurs(array($plongeur1, $plongeur2, $plongeur3, $plongeur4, $plongeur5));

	testPalanque($palanque, true);

	echo "<h3>Test d'une plongée techniques en sur-effectif (1 moniteur + 4 plongeurs + 2 P-4)</h1>";
	$palanque = new Palanque(rand(0,100));
	$palanque->setTypeGaz(Palanque::gazAir);
	$palanque->setTypePlonge(Palanque::plongeEncadre);
	$palanque->setProfondeurPrevue(19.0);
	$palanque->setNumero(1);

	$moniteur = generateMoniteur();
	$moniteur->ajouterAptitude(AptitudeDao::getByLibelleCourt("E-3"));
	$palanque->setMoniteur($moniteur);
	
	$plongeur1 = generatePlongeur();
	$plongeur1->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur2 = generatePlongeur();
	$plongeur2->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur3 = generatePlongeur();
	$plongeur3->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur4 = generatePlongeur();
	$plongeur4->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));

	$plongeur5 = generatePlongeur();
	$plongeur5->ajouterAptitude(AptitudeDao::getByLibelleCourt("P-4"));
	$plongeur6 = generatePlongeur();
	$plongeur6->ajouterAptitude(AptitudeDao::getByLibelleCourt("P-4"));
	
	$palanque->setPlongeurs(array($plongeur1, $plongeur2, $plongeur3, $plongeur4, $plongeur5, $plongeur6));

	testPalanque($palanque, true);

	echo "<h3>Test d'une plongée techniques en sur-effectif pour une profondeur de 60 (1 moniteur + 4 plongeurs + 1 GP)</h1>";
	$palanque = new Palanque(rand(0,100));
	$palanque->setTypeGaz(Palanque::gazAir);
	$palanque->setTypePlonge(Palanque::plongeEncadre);
	$palanque->setProfondeurPrevue(55.0);
	$palanque->setNumero(1);

	$moniteur = generateMoniteur();
	$moniteur->ajouterAptitude(AptitudeDao::getByLibelleCourt("E-4"));
	$palanque->setMoniteur($moniteur);
	
	$plongeur1 = generatePlongeur();
	$plongeur1->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-60"));
	$plongeur2 = generatePlongeur();
	$plongeur2->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-60"));
	$plongeur3 = generatePlongeur();
	$plongeur3->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-60"));
	$plongeur4 = generatePlongeur();
	$plongeur4->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-60"));

	$plongeur5 = generatePlongeur();
	$plongeur5->ajouterAptitude(AptitudeDao::getByLibelleCourt("GP"));

	
	$palanque->setPlongeurs(array($plongeur1, $plongeur2, $plongeur3, $plongeur4, $plongeur5));

	testPalanque($palanque, true);

	/* TEST 1 : Test des baptêmes (CAS PAR DEFAUT) */
	echo "<h3>Test des baptêmes</h1>";
	echo "<h3>Test d'un baptême par défaut</h1>";
	$palanque = new Palanque(rand(0,100));
	$palanque->setTypeGaz(Palanque::gazAir);
	$palanque->setTypePlonge(Palanque::plongeBapteme);
	$palanque->setProfondeurPrevue(6.0);
	$palanque->setNumero(1337);

	$plongeur1 = generatePlongeur();
	$plongeur1->ajouterAptitude(AptitudeDao::getByLibelleCourt("PA-20"));
	$palanque->setPlongeurs($plongeur1);

	$moniteur = generateMoniteur();
	$moniteur->ajouterAptitude(AptitudeDao::getByLibelleCourt("E-2"));
	$palanque->setMoniteur($moniteur);

	testPalanque($palanque, true);

	echo "<h3>Test d'un baptême en sureffectif</h1>";
	$palanque = new Palanque(rand(0,100));
	$palanque->setTypeGaz(Palanque::gazAir);
	$palanque->setTypePlonge(Palanque::plongeBapteme);
	$palanque->setProfondeurPrevue(6.0);
	$palanque->setNumero(1337);

	$plongeur1 = generatePlongeur();
	$plongeur1->ajouterAptitude(AptitudeDao::getByLibelleCourt("PA-20"));
	$plongeur2 = generatePlongeur();
	$plongeur2->ajouterAptitude(AptitudeDao::getByLibelleCourt("PA-20"));
	$palanque->setPlongeurs(array($plongeur1, $plongeur2));

	$moniteur = generateMoniteur();
	$moniteur->ajouterAptitude(AptitudeDao::getByLibelleCourt("E-2"));
	$palanque->setMoniteur($moniteur);

	testPalanque($palanque, true);

	/* TEST 2 : Test des plongées autonomes */
	echo "<h3>Test des plongées autonomes</h1>";
	echo "<h3>Test d'une plongée autonome en effectif maximal</h1>";
	$palanque = new Palanque(rand(0,100));
	$palanque->setTypeGaz(Palanque::gazAir);
	$palanque->setTypePlonge(Palanque::plongeAutonome);
	$palanque->setProfondeurPrevue(20.0);
	$palanque->setNumero(1);
	
	$plongeur1 = generatePlongeur();
	$plongeur1->ajouterAptitude(AptitudeDao::getByLibelleCourt("PA-20"));
	$plongeur2 = generatePlongeur();
	$plongeur2->ajouterAptitude(AptitudeDao::getByLibelleCourt("PA-20"));
	$plongeur3 = generatePlongeur();
	$plongeur3->ajouterAptitude(AptitudeDao::getByLibelleCourt("PA-20"));
	$palanque->setPlongeurs(array($plongeur1, $plongeur2, $plongeur3));

	testPalanque($palanque, true);

	echo "<h3>Test d'une plongée autonome en effectif minimal</h1>";
	$palanque = new Palanque(rand(0,100));
	$palanque->setTypeGaz(Palanque::gazAir);
	$palanque->setTypePlonge(Palanque::plongeAutonome);
	$palanque->setProfondeurPrevue(20.0);
	$palanque->setNumero(1);
	
	$plongeur1 = generatePlongeur();
	$plongeur1->ajouterAptitude(AptitudeDao::getByLibelleCourt("PA-20"));
	$palanque->setPlongeurs(array($plongeur1));

	testPalanque($palanque, true);

	echo "<h3>Test d'une plongée autonome en sur-effectif</h1>";
	$palanque = new Palanque(rand(0,100));
	$palanque->setTypeGaz(Palanque::gazAir);
	$palanque->setTypePlonge(Palanque::plongeAutonome);
	$palanque->setProfondeurPrevue(20.0);
	$palanque->setNumero(1);
	
	$plongeur1 = generatePlongeur();
	$plongeur1->ajouterAptitude(AptitudeDao::getByLibelleCourt("PA-20"));
	$plongeur2 = generatePlongeur();
	$plongeur2->ajouterAptitude(AptitudeDao::getByLibelleCourt("PA-20"));
	$plongeur3 = generatePlongeur();
	$plongeur3->ajouterAptitude(AptitudeDao::getByLibelleCourt("PA-20"));
	$plongeur4 = generatePlongeur();
	$plongeur4->ajouterAptitude(AptitudeDao::getByLibelleCourt("PA-20"));
	$palanque->setPlongeurs(array($plongeur1, $plongeur2, $plongeur3, $plongeur4));

	testPalanque($palanque, true);

	echo "<h3>Test d'une plongée autonome en sous-aptitude d'un des plongeurs</h1>";
	$palanque = new Palanque(rand(0,100));
	$palanque->setTypeGaz(Palanque::gazAir);
	$palanque->setTypePlonge(Palanque::plongeAutonome);
	$palanque->setProfondeurPrevue(20.0);
	$palanque->setNumero(1);
	
	$plongeur1 = generatePlongeur();
	$plongeur1->ajouterAptitude(AptitudeDao::getByLibelleCourt("PA-12"));
	$plongeur2 = generatePlongeur();
	$plongeur2->ajouterAptitude(AptitudeDao::getByLibelleCourt("PA-20"));
	$plongeur3 = generatePlongeur();
	$plongeur3->ajouterAptitude(AptitudeDao::getByLibelleCourt("PA-20"));

	$palanque->setPlongeurs(array($plongeur1, $plongeur2, $plongeur3));

	testPalanque($palanque, true);

	/* TEST 2 */
	$palanque = new Palanque(rand(0,100));
	$palanque->setTypeGaz(Palanque::gazAir);
	$palanque->setTypePlonge(Palanque::plongeEncadre);
	$palanque->setProfondeurPrevue(20.0);
	$palanque->setNumero(1);

	$moniteur = generateMoniteur();
	$moniteur->ajouterAptitude(AptitudeDao::getByLibelleCourt("E-2"));
	$palanque->setMoniteur($moniteur);

	$plongeur1 = generatePlongeur();
	$plongeur1->ajouterAptitude(AptitudeDao::getByLibelleCourt("PA-20"));
	$plongeur2 = generatePlongeur();
	$plongeur2->ajouterAptitude(AptitudeDao::getByLibelleCourt("PA-20"));
	$plongeur3 = generatePlongeur();
	$plongeur3->ajouterAptitude(AptitudeDao::getByLibelleCourt("PA-20"));
	$palanque->setPlongeurs(array($plongeur1, $plongeur2, $plongeur3));

	testPalanque($palanque, true);

	/* TEST 3 */
	$palanque = new Palanque(rand(0,100));
	$palanque->setTypeGaz(Palanque::gazAir);
	$palanque->setTypePlonge(Palanque::plongeAutonome);
	$palanque->setProfondeurPrevue(20.0);
	$palanque->setNumero(1);

	$moniteur = generateMoniteur();
	$moniteur->ajouterAptitude(AptitudeDao::getByLibelleCourt("E-2"));
	$palanque->setMoniteur($moniteur);

	$plongeur1 = generatePlongeur();
	$plongeur1->ajouterAptitude(AptitudeDao::getByLibelleCourt("PA-20"));
	$plongeur2 = generatePlongeur();
	$plongeur2->ajouterAptitude(AptitudeDao::getByLibelleCourt("PA-20"));
	$plongeur3 = generatePlongeur();
	$plongeur3->ajouterAptitude(AptitudeDao::getByLibelleCourt("PA-20"));
	$plongeur3 = generatePlongeur();
	$plongeur3->ajouterAptitude(AptitudeDao::getByLibelleCourt("PA-20"));
	$palanque->setPlongeurs(array($plongeur1, $plongeur2, $plongeur3));

	testPalanque($palanque, false);

	/* Pour memoire, affichage de toute les aptitudes en fin de test*/
	echo "<br><br>============<br>";
	foreach (AptitudeDao::getAll() as $aptitude) {
		echo "$aptitude";
	}

	/* FONCTION TEST UTILS */

	function generatePlongeur(){
		$plongeur = new Plongeur(rand(0,100));
		$plongeur->setNom(substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5));
		$plongeur->setPrenom(substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5));
		$plongeur->setDateNaissance("10/10/2010");
		$plongeur->setTelephone("06".substr(str_shuffle("0123456789"), 0, 8));
		$plongeur->setTelephoneUrgence("06".substr(str_shuffle("0123456789"), 0, 8));
		return $plongeur;
	}

	function generateMoniteur(){
		$moniteur = new Moniteur(rand(0,100));
		$moniteur->setNom(substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5));
		$moniteur->setPrenom(substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5));
		$moniteur->setTelephone("06".substr(str_shuffle("0123456789"), 0, 8));
		$moniteur->setActif(true);
		return $moniteur;
	}

	function testPalanque($palanque, $palanque_ok){
		if($palanque_ok){
			$color_ok = "green";
			$color_fail = "red";
		}
		else{
			$color_ok = "red";
			$color_fail = "green";
		}


		echo "<br>============<br>";
		echo $palanque;
		echo "<br>";

		$erreurs = validePalanquee($palanque);
		if(count($erreurs) == 0){
			echo "<span style='color:$color_ok'>Palanque valide</span><br>";
		}
		else{
			echo "<ul style='color:$color_fail'>";
			foreach($erreurs as $erreur){
				echo "<li>".$erreur['msg']."</li>";
			}
			echo "</ul><br>";
		}

	}
?>