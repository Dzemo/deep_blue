<?php

		/////////////////////////////
		//     INTRODUCTION        //
		//     Utils Debut         //
		/////////////////////////////
	header('Content-Type: text/html; charset=utf-8');

	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."classloader.php");
	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."utils".DIRECTORY_SEPARATOR."validation_palanquee.php");

	$execution = true;

	echo "<h1>Test validation palanquee</h1><br/>";

		//////////////////////////////
		//     CREATION FICHE  1    //
		//           TECHNIQUE      //
		//////////////////////////////

		/// DAO
		$ficheSecuriteDao = new FicheSecuriteDao();
		$embarcationDao = new EmbarcationDao();
		$moniteurDao = new MoniteurDao();
		$siteDao = new SiteDao();

		/// FICHE
		$fichesecurite1 = new FicheSecurite(1);
		
		/// EMBARCATION & MONITEUR & SITE
		$embarcation1 = $embarcationDao->getById(1);
		$moniteur1 = $moniteurDao->getById(8); // moniteur TECHNIQUE = 8
		$site1 = $siteDao->getById(1);

		/// CARACTERISTIQUES
		$fichesecurite1->setEmbarcation($embarcation1);
		$fichesecurite1->setDirecteurPlonge($moniteur1);
		$fichesecurite1->setSite($site1);
		$fichesecurite1->setTimestamp(1421712000);
		$fichesecurite1->setEtat("CREER");
		$fichesecurite1->getDisponible(true);

		/////////////////////////////
		//     Test TECHNIQUE      //
		//   (effectif minimal)    //
		/////////////////////////////
		
	/* TEST 3 : Test des plongées techniques (enseignement) */
	echo "<h1>Test des plongées techniques (enseignement)</h1>";
	echo "<h3>Test d'une plongée techniques en effectif minimal</h3>";
	$palanque1 = new Palanque(1);
	$palanque1->setTypeGaz(Palanque::gazAir);
	$palanque1->setTypePlonge(Palanque::plongeTechnique);
	$palanque1->setProfondeurPrevue(20.0);
	$palanque1->setDureePrevue(20);
	$palanque1->setNumero(1);

	$moniteur1 = new Moniteur(1);
	$moniteur1 = generateMoniteur();
	$moniteur1->ajouterAptitude(AptitudeDao::getByLibelleCourt("E-2"));
	$palanque1->setMoniteur($moniteur1);
	
	$plongeur11 = generatePlongeur();
	$plongeur11->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-12"));
	$plongeurs[] = $plongeur11;
	$palanque1->setPlongeurs($plongeurs);

	testPalanque($palanque1, true);

		///////////////////////////////////////
		//     Test TECHNIQUE                //
		//   (effectif maximal classique)    //
		///////////////////////////////////////
	
	echo "<h3>Test d'une plongée techniques en effectif maximal classique (4 plongeurs + 1 moniteur)</h1>";
	$palanque2 = new Palanque(2);
	$palanque2->setTypeGaz(Palanque::gazAir);
	$palanque2->setTypePlonge(Palanque::plongeTechnique);
	$palanque2->setProfondeurPrevue(35.0);
	$palanque2->setDureePrevue(20);
	$palanque2->setNumero(2);

	$moniteur2 = new Moniteur(2);
	$moniteur2 = generateMoniteur();
	$moniteur2->ajouterAptitude(AptitudeDao::getByLibelleCourt("E-3"));
	$palanque2->setMoniteur($moniteur2);
	
	// Les plongeurs sont codés avec $variable [numéro de palanquée][numéro du plongeur] ex: $plongeur21 et le 
	// 1er plongeur de la palanquée 2
	$plongeur21 = generatePlongeur();
	$plongeur21->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur22 = generatePlongeur();
	$plongeur22->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur23 = generatePlongeur();
	$plongeur23->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur24 = generatePlongeur();
	$plongeur24->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));

	$palanque2->setPlongeurs(array($plongeur21, $plongeur22, $plongeur23, $plongeur24));

	testPalanque($palanque2, true);

		///////////////////////////////////////
		//     Test TECHNIQUE                //
		//   (effectif maximal + GP) 		 //
		///////////////////////////////////////
	
	echo "<h3>Test d'une plongée techniques en effectif maximal + GP (1 moniteur + 4 plongeurs + 1 GP)</h1>";
	$palanque3 = new Palanque(3);
	$palanque3->setTypeGaz(Palanque::gazAir);
	$palanque3->setTypePlonge(Palanque::plongeTechnique);
	$palanque3->setProfondeurPrevue(35.0);
	$palanque3->setDureePrevue(20);
	$palanque3->setNumero(3);

	$moniteur3 = generateMoniteur();
	$moniteur3->ajouterAptitude(AptitudeDao::getByLibelleCourt("E-3"));
	$palanque3->setMoniteur($moniteur3);
	
	$plongeur31 = generatePlongeur();
	$plongeur31->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur32 = generatePlongeur();
	$plongeur32->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur33 = generatePlongeur();
	$plongeur33->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur34 = generatePlongeur();
	$plongeur34->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));

	$plongeur35 = generatePlongeur();
	$plongeur35->ajouterAptitude(AptitudeDao::getByLibelleCourt("GP"));
	
	$palanque3->setPlongeurs(array($plongeur31, $plongeur32, $plongeur33, $plongeur34, $plongeur35));

	testPalanque($palanque3, true);

		///////////////////////////////////////
		//     Test TECHNIQUE                //
		//   (effectif maximal + P4) 		 //
		///////////////////////////////////////
		
	echo "<h3>Test d'une plongée techniques en effectif maximal + P-4 (1 moniteur + 4 plongeurs + 1 P-4)</h1>";
	$palanque4 = new Palanque(4);
	$palanque4->setTypeGaz(Palanque::gazAir);
	$palanque4->setTypePlonge(Palanque::plongeTechnique);
	$palanque4->setProfondeurPrevue(35.0);
	$palanque4->setDureePrevue(20);
	$palanque4->setNumero(4);

	$moniteur4 = generateMoniteur();
	$moniteur4->ajouterAptitude(AptitudeDao::getByLibelleCourt("E-3"));
	$palanque4->setMoniteur($moniteur4);
	
	$plongeur41 = generatePlongeur();
	$plongeur41->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur42 = generatePlongeur();
	$plongeur42->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur43 = generatePlongeur();
	$plongeur43->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur44 = generatePlongeur();
	$plongeur44->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));

	$plongeur45 = generatePlongeur();
	$plongeur45->ajouterAptitude(AptitudeDao::getByLibelleCourt("P-4"));
	
	$palanque4->setPlongeurs(array($plongeur41, $plongeur42, $plongeur43, $plongeur44, $plongeur45));

	testPalanque($palanque4, true);

		///////////////////////////////////////
		//     Test TECHNIQUE                //
		//   (surreffectif 5 plongeurs)		 //
		///////////////////////////////////////
		
echo "<h3>Test d'une plongée techniques en sur-effectif(1 moniteur + 5 plongeurs (hors GP/P4))</h1>";
	$palanque5 = new Palanque(5);
	$palanque5->setTypeGaz(Palanque::gazAir);
	$palanque5->setTypePlonge(Palanque::plongeTechnique);
	$palanque5->setProfondeurPrevue(35.0);
	$palanque5->setDureePrevue(20);
	$palanque5->setNumero(5);

	$moniteur5 = generateMoniteur();
	$moniteur5->ajouterAptitude(AptitudeDao::getByLibelleCourt("E-3"));
	$palanque5->setMoniteur($moniteur5);
	
	$plongeur51 = generatePlongeur();
	$plongeur51->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur52 = generatePlongeur();
	$plongeur52->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur53 = generatePlongeur();
	$plongeur53->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur54 = generatePlongeur();
	$plongeur54->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur55 = generatePlongeur();
	$plongeur55->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	
	$palanque5->setPlongeurs(array($plongeur51, $plongeur52, $plongeur53, $plongeur54, $plongeur55));

	testPalanque($palanque5, true);

		///////////////////////////////////////////////
		//     Test TECHNIQUE         		         //
		//   (surreffectif 4 plongeurs + 2 P4)		 //
		///////////////////////////////////////////////

echo "<h3>Test d'une plongée techniques en sur-effectif (1 moniteur + 4 plongeurs + 2 P-4)</h1>";
	$palanque6 = new Palanque(6);
	$palanque6->setTypeGaz(Palanque::gazAir);
	$palanque6->setTypePlonge(Palanque::plongeTechnique);
	$palanque6->setProfondeurPrevue(35.0);
	$palanque6->setDureePrevue(20);
	$palanque6->setNumero(6);

	$moniteur6 = generateMoniteur();
	$moniteur6->ajouterAptitude(AptitudeDao::getByLibelleCourt("E-3"));
	$palanque6->setMoniteur($moniteur6);
	
	$plongeur61 = generatePlongeur();
	$plongeur61->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur62 = generatePlongeur();
	$plongeur62->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur63 = generatePlongeur();
	$plongeur63->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur64 = generatePlongeur();
	$plongeur64->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));

	$plongeur65 = generatePlongeur();
	$plongeur65->ajouterAptitude(AptitudeDao::getByLibelleCourt("P-4"));
	$plongeur66 = generatePlongeur();
	$plongeur66->ajouterAptitude(AptitudeDao::getByLibelleCourt("P-4"));
	
	$palanque6->setPlongeurs(array($plongeur61, $plongeur62, $plongeur63, $plongeur64, $plongeur65, $plongeur66));

	testPalanque($palanque6, true);

		///////////////////////////////////////
		//     Test TECHNIQUE         		 //
		//   (surreffectif profondeur 60m)	 //
		///////////////////////////////////////
		
echo "<h3>Test d'une plongée techniques en sur-effectif pour une profondeur de 60 (1 moniteur + 4 plongeurs + 1 GP)</h1>";
	$palanque7 = new Palanque(7);
	$palanque7->setTypeGaz(Palanque::gazAir);
	$palanque7->setTypePlonge(Palanque::plongeTechnique);
	$palanque7->setProfondeurPrevue(55.0);
	$palanque7->setDureePrevue(20);
	$palanque7->setNumero(7);

	$moniteur7 = generateMoniteur();
	$moniteur7->ajouterAptitude(AptitudeDao::getByLibelleCourt("E-4"));
	$palanque7->setMoniteur($moniteur7);
	
	$plongeur71 = generatePlongeur();
	$plongeur71->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-40"));
	$plongeur72 = generatePlongeur();
	$plongeur72->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-40"));
	$plongeur73 = generatePlongeur();
	$plongeur73->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-40"));
	$plongeur74 = generatePlongeur();
	$plongeur74->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-40"));

	$plongeur75 = generatePlongeur();
	$plongeur75->ajouterAptitude(AptitudeDao::getByLibelleCourt("P-4"));

	
	$palanque7->setPlongeurs(array($plongeur71, $plongeur72, $plongeur73, $plongeur74, $plongeur75));

	testPalanque($palanque7, true);

		//////////////////////////////
		//     CREATION FICHE  2    //
		//           ENCADRE        //
		//////////////////////////////

		/// FICHE
		$fichesecurite2 = new FicheSecurite(1);
		
		/// EMBARCATION & MONITEUR & SITE
		$embarcation2 = $embarcationDao->getById(1);
		$moniteur2 = $moniteurDao->getById(9); // moniteur TECHNIQUE = 9
		$site2 = $siteDao->getById(1);

		/// CARACTERISTIQUES
		$fichesecurite2->setEmbarcation($embarcation2);
		$fichesecurite2->setDirecteurPlonge($moniteur2);
		$fichesecurite2->setSite($site2);
		$fichesecurite2->setTimestamp(1421712000);
		$fichesecurite2->setEtat("CREER");
		$fichesecurite2->getDisponible(true);


		///////////////////////////
		//     Test ENCADRE   	 //
		//   (effectif mininal)	 //
		///////////////////////////
		
	echo "<h3>Test des plongées encadré (exploration)</h1>";
	echo "<h3>Test d'une plongée encadré en effectif minimal</h1>";
	$palanque8 = new Palanque(8);
	$palanque8->setTypeGaz(Palanque::gazAir);
	$palanque8->setTypePlonge(Palanque::plongeEncadre);
	$palanque8->setProfondeurPrevue(12.0);
	$palanque8->setDureePrevue(20);
	$palanque8->setNumero(8);

	$moniteur8 = generateMoniteur();
	$moniteur8->ajouterAptitude(AptitudeDao::getByLibelleCourt("E-2"));
	$palanque8->setMoniteur($moniteur8);
	
	$plongeur81 = generatePlongeur();
	$plongeur81->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-12"));
	$palanque8->setPlongeurs(array($plongeur81));

	testPalanque($palanque8, true);

		///////////////////////////
		//     Test ENCADRE   	 //
		//   (effectif maximal)	 //
		///////////////////////////
		
echo "<h3>Test d'une plongée techniques en effectif maximal classique (4 plongeurs + 1 moniteur)</h1>";
	$palanque9 = new Palanque(9);
	$palanque9->setTypeGaz(Palanque::gazAir);
	$palanque9->setTypePlonge(Palanque::plongeEncadre);
	$palanque9->setProfondeurPrevue(35.0);
	$palanque9->setDureePrevue(20);
	$palanque9->setNumero(9);

	$moniteur9 = generateMoniteur();
	$moniteur9->ajouterAptitude(AptitudeDao::getByLibelleCourt("E-3"));
	$palanque9->setMoniteur($moniteur9);
	
	$plongeur91 = generatePlongeur();
	$plongeur91->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-40"));
	$plongeur92 = generatePlongeur();
	$plongeur92->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-40"));
	$plongeur93 = generatePlongeur();
	$plongeur93->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-40"));
	$plongeur94 = generatePlongeur();
	$plongeur94->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-40"));

	$palanque9->setPlongeurs(array($plongeur91, $plongeur92, $plongeur93, $plongeur94));

	testPalanque($palanque9, true);

		///////////////////////////////////
		//     Test ENCADRE   			 //
		//   (effectif maximal + GP)	 //
		///////////////////////////////////

echo "<h3>Test d'une plongée encadré en effectif maximal + GP (1 moniteur + 4 plongeurs + 1 GP)</h1>";
	$palanque10 = new Palanque(10);
	$palanque10->setTypeGaz(Palanque::gazAir);
	$palanque10->setTypePlonge(Palanque::plongeEncadre);
	$palanque10->setProfondeurPrevue(35.0);
	$palanque10->setDureePrevue(20);
	$palanque10->setNumero(10);

	$moniteur10 = generateMoniteur();
	$moniteur10->ajouterAptitude(AptitudeDao::getByLibelleCourt("E-3"));
	$palanque10->setMoniteur($moniteur10);
	
	$plongeur101 = generatePlongeur();
	$plongeur101->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-40"));
	$plongeur102 = generatePlongeur();
	$plongeur102->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-40"));
	$plongeur103 = generatePlongeur();
	$plongeur103->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-40"));
	$plongeur104 = generatePlongeur();
	$plongeur104->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-40"));

	$plongeur105 = generatePlongeur();
	$plongeur105->ajouterAptitude(AptitudeDao::getByLibelleCourt("GP"));
	
	$palanque10->setPlongeurs(array($plongeur101, $plongeur102, $plongeur103, $plongeur104, $plongeur105));

	testPalanque($palanque10, true);

		///////////////////////////////////
		//     Test ENCADRE   			 //
		//   (sureffectif encadré)	     //
		///////////////////////////////////

echo "<h3>Test d'une plongée techniques en sur-effectif(1 moniteur + 5 plongeurs (hors GP/P4))</h1>";
	$palanque11 = new Palanque(11);
	$palanque11->setTypeGaz(Palanque::gazAir);
	$palanque11->setTypePlonge(Palanque::plongeEncadre);
	$palanque11->setProfondeurPrevue(19.0);
	$palanque11->setDureePrevue(20);
	$palanque11->setNumero(11);

	$moniteur11 = generateMoniteur();
	$moniteur11->ajouterAptitude(AptitudeDao::getByLibelleCourt("E-3"));
	$palanque11->setMoniteur($moniteur11);
	
	$plongeur111 = generatePlongeur();
	$plongeur111->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur112 = generatePlongeur();
	$plongeur112->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur113 = generatePlongeur();
	$plongeur113->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur114 = generatePlongeur();
	$plongeur114->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur115 = generatePlongeur();
	$plongeur115->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	
	$palanque11->setPlongeurs(array($plongeur111, $plongeur112, $plongeur113, $plongeur114, $plongeur115));

	testPalanque($palanque11, true);

		///////////////////////////////////
		//     Test ENCADRE   			 //
		//   (sureffectif encadré 2 P4)  //
		///////////////////////////////////

echo "<h3>Test d'une plongée encadré en sur-effectif (1 moniteur + 4 plongeurs + 2 P-4)</h1>";
	$palanque12 = new Palanque(12);
	$palanque12->setTypeGaz(Palanque::gazAir);
	$palanque12->setTypePlonge(Palanque::plongeEncadre);
	$palanque12->setProfondeurPrevue(19.0);
	$palanque12->setDureePrevue(20);
	$palanque12->setNumero(12);

	$moniteur12 = generateMoniteur();
	$moniteur12->ajouterAptitude(AptitudeDao::getByLibelleCourt("E-3"));
	$palanque12->setMoniteur($moniteur12);
	
	$plongeur121 = generatePlongeur();
	$plongeur121->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur122 = generatePlongeur();
	$plongeur122->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur123 = generatePlongeur();
	$plongeur123->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));
	$plongeur124 = generatePlongeur();
	$plongeur124->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-20"));

	$plongeur125 = generatePlongeur();
	$plongeur125->ajouterAptitude(AptitudeDao::getByLibelleCourt("P-4"));
	$plongeur126 = generatePlongeur();
	$plongeur126->ajouterAptitude(AptitudeDao::getByLibelleCourt("P-4"));
	
	$palanque12->setPlongeurs(array($plongeur121, $plongeur122, $plongeur123, $plongeur124, $plongeur125, $plongeur126));

	testPalanque($palanque12, true);

		/////////////////////////////////////
		//     Test ENCADRE   		       //
		//   (sureffectif profondeur 60m)  //
		/////////////////////////////////////

echo "<h3>Test d'une plongée encadré en sur-effectif pour une profondeur de 60 (1 moniteur + 4 plongeurs + 1 GP)</h1>";
	$palanque13 = new Palanque(13);
	$palanque13->setTypeGaz(Palanque::gazAir);
	$palanque13->setTypePlonge(Palanque::plongeEncadre);
	$palanque13->setProfondeurPrevue(55.0);
	$palanque13->setDureePrevue(20);
	$palanque13->setNumero(13);

	$moniteur13 = generateMoniteur();
	$moniteur13->ajouterAptitude(AptitudeDao::getByLibelleCourt("E-4"));
	$palanque13->setMoniteur($moniteur13);
	
	$plongeur131 = generatePlongeur();
	$plongeur131->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-60"));
	$plongeur132 = generatePlongeur();
	$plongeur132->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-60"));
	$plongeur133 = generatePlongeur();
	$plongeur133->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-60"));
	$plongeur134 = generatePlongeur();
	$plongeur134->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-60"));

	$plongeur135 = generatePlongeur();
	$plongeur135->ajouterAptitude(AptitudeDao::getByLibelleCourt("GP"));

	$palanque13->setPlongeurs(array($plongeur131, $plongeur132, $plongeur133, $plongeur134, $plongeur135));

	testPalanque($palanque13, true);

		//////////////////////////////
		//     CREATION FICHE  3    //
		//           BAPTEME        //
		//////////////////////////////

		/// FICHE
		$fichesecurite3 = new FicheSecurite(1);
		
		/// EMBARCATION & MONITEUR & SITE
		$embarcation3 = $embarcationDao->getById(1);
		$moniteur3 = $moniteurDao->getById(7); // moniteur BAPTEME = 7
		$site3 = $siteDao->getById(1);

		/// CARACTERISTIQUES
		$fichesecurite3->setEmbarcation($embarcation3);
		$fichesecurite3->setDirecteurPlonge($moniteur3);
		$fichesecurite3->setSite($site3);
		$fichesecurite3->setTimestamp(1421712000);
		$fichesecurite3->setEtat("CREER");
		$fichesecurite3->getDisponible(true);

		///////////////////////
		//     Test BAPTEME  //
		//   (cas standard)  //
		///////////////////////

	echo "<h3>Test des baptêmes</h1>";
	echo "<h3>Test d'un baptême par défaut</h1>";
	$palanque14 = new Palanque(14);
	$palanque14->setTypeGaz(Palanque::gazAir);
	$palanque14->setTypePlonge(Palanque::plongeBapteme);
	$palanque14->setProfondeurPrevue(6.0);
	$palanque14->setDureePrevue(20);
	$palanque14->setNumero(14);

	$plongeur141 = generatePlongeur();
	$plongeur141->ajouterAptitude(AptitudeDao::getByLibelleCourt("PA-20"));
	$palanque14->setPlongeurs(array($plongeur141));

	$moniteur14 = generateMoniteur();
	$moniteur14->ajouterAptitude(AptitudeDao::getByLibelleCourt("E-2"));
	$palanque14->setMoniteur($moniteur14);

	testPalanque($palanque14, true);

		///////////////////////
		//     Test BAPTME   //
		//   (sureffectif )  //
		///////////////////////
		
	echo "<h3>Test d'un baptême en sureffectif</h1>";
	$palanque15 = new Palanque(15);
	$palanque15->setTypeGaz(Palanque::gazAir);
	$palanque15->setTypePlonge(Palanque::plongeBapteme);
	$palanque15->setProfondeurPrevue(6.0);
	$palanque15->setDureePrevue(20);
	$palanque15->setNumero(15);

	$plongeur151 = generatePlongeur();
	$plongeur151->ajouterAptitude(AptitudeDao::getByLibelleCourt("PA-20"));
	$plongeur152 = generatePlongeur();
	$plongeur152->ajouterAptitude(AptitudeDao::getByLibelleCourt("PA-20"));
	$palanque15->setPlongeurs(array($plongeur151, $plongeur152));

	$moniteur15 = generateMoniteur();
	$moniteur15->ajouterAptitude(AptitudeDao::getByLibelleCourt("E-2"));
	$palanque15->setMoniteur($moniteur15);

	testPalanque($palanque15, true);

		//////////////////////////////
		//     CREATION FICHE  4    //
		//           ENCADRE        //
		//////////////////////////////

		/// FICHE
		$fichesecurite4 = new FicheSecurite(1);
		
		/// EMBARCATION & MONITEUR & SITE
		$embarcation4 = $embarcationDao->getById(1);
		$moniteur4 = $moniteurDao->getById(6); // moniteur AUTONOME = 6
		$site4 = $siteDao->getById(1);

		/// CARACTERISTIQUES
		$fichesecurite4->setEmbarcation($embarcation4);
		$fichesecurite4->setDirecteurPlonge($moniteur4);
		$fichesecurite4->setSite($site4);
		$fichesecurite4->setTimestamp(1421712000);
		$fichesecurite4->setEtat("CREER");
		$fichesecurite4->getDisponible(true);


		//////////////////////////
		//     Test AUTONOME    //
		//   (effectif max )    //
		//////////////////////////
		
	echo "<h3>Test des plongées autonomes</h1>";
	echo "<h3>Test d'une plongée autonome en effectif maximal</h1>";
	$palanque16 = new Palanque(16);
	$palanque16->setTypeGaz(Palanque::gazAir);
	$palanque16->setTypePlonge(Palanque::plongeAutonome);
	$palanque16->setProfondeurPrevue(20.0);
	$palanque16->setDureePrevue(20);
	$palanque16->setNumero(16);
	
	$plongeur161 = generatePlongeur();
	$plongeur161->ajouterAptitude(AptitudeDao::getByLibelleCourt("PA-20"));
	$plongeur162 = generatePlongeur();
	$plongeur162->ajouterAptitude(AptitudeDao::getByLibelleCourt("PA-20"));
	$plongeur163 = generatePlongeur();
	$plongeur163->ajouterAptitude(AptitudeDao::getByLibelleCourt("PA-20"));
	$palanque16->setPlongeurs(array($plongeur161, $plongeur162, $plongeur163));

	testPalanque($palanque16, true);

		//////////////////////////
		//     Test AUTONOME    //
		//   (effectif min )    //
		//////////////////////////
		
	echo "<h3>Test d'une plongée autonome en effectif minimal</h1>";
	$palanque17 = new Palanque(17);
	$palanque17->setTypeGaz(Palanque::gazAir);
	$palanque17->setTypePlonge(Palanque::plongeAutonome);
	$palanque17->setProfondeurPrevue(20.0);
	$palanque17->setDureePrevue(20);
	$palanque17->setNumero(17);
	
	$plongeur171 = generatePlongeur();
	$plongeur171->ajouterAptitude(AptitudeDao::getByLibelleCourt("PA-20"));
	$palanque17->setPlongeurs(array($plongeur171));

	testPalanque($palanque17, true);

		//////////////////////////
		//     Test AUTONOME    //
		//   (sureffectif )     //
		//////////////////////////

echo "<h3>Test d'une plongée autonome en sur-effectif</h1>";
	$palanque18 = new Palanque(18);
	$palanque18->setTypeGaz(Palanque::gazAir);
	$palanque18->setTypePlonge(Palanque::plongeAutonome);
	$palanque18->setProfondeurPrevue(20.0);
	$palanque18->setDureePrevue(20);
	$palanque18->setNumero(18);
	
	$plongeur181 = generatePlongeur();
	$plongeur181->ajouterAptitude(AptitudeDao::getByLibelleCourt("PA-20"));
	$plongeur182 = generatePlongeur();
	$plongeur182->ajouterAptitude(AptitudeDao::getByLibelleCourt("PA-20"));
	$plongeur183 = generatePlongeur();
	$plongeur183->ajouterAptitude(AptitudeDao::getByLibelleCourt("PA-20"));
	$plongeur184 = generatePlongeur();
	$plongeur184->ajouterAptitude(AptitudeDao::getByLibelleCourt("PA-20"));
	$palanque18->setPlongeurs(array($plongeur181, $plongeur182, $plongeur183, $plongeur184));

	testPalanque($palanque18, true);

		//////////////////////////
		//     Test AUTONOME    //
		//   (sous aptitudes )  //
		//////////////////////////
		
echo "<h3>Test d'une plongée autonome en sous-aptitude d'un des plongeurs</h1>";
	$palanque19 = new Palanque(19);
	$palanque19->setTypeGaz(Palanque::gazAir);
	$palanque19->setTypePlonge(Palanque::plongeAutonome);
	$palanque19->setProfondeurPrevue(20.0);
	$palanque19->setDureePrevue(20);
	$palanque19->setNumero(19);
	
	$plongeur191 = generatePlongeur();
	$plongeur191->ajouterAptitude(AptitudeDao::getByLibelleCourt("PA-12"));
	$plongeur192 = generatePlongeur();
	$plongeur192->ajouterAptitude(AptitudeDao::getByLibelleCourt("PA-20"));
	$plongeur193 = generatePlongeur();
	$plongeur193->ajouterAptitude(AptitudeDao::getByLibelleCourt("PA-20"));

	$palanque19->setPlongeurs(array($plongeur191, $plongeur192, $plongeur193));

	testPalanque($palanque19, true);

		///////////////////////////////////////
		//     SET PALANQUE + INSERT         //
		//   (Fin de la création fiche)      //
		///////////////////////////////////////
	$fichesecurite1->setPalanques(array($palanque1,$palanque2,$palanque3,$palanque4,$palanque5,$palanque6,$palanque7));
	$fichesecurite2->setPalanques(array($palanque8,$palanque9,$palanque10,$palanque11,$palanque12,$palanque13));
	$fichesecurite3->setPalanques(array($palanque14,$palanque15));
	$fichesecurite4->setPalanques(array($palanque16,$palanque17,$palanque18,$palanque19));

	echo $ficheSecuriteDao = FicheSecuriteDao::insert($fichesecurite1);
	echo $ficheSecuriteDao = FicheSecuriteDao::insert($fichesecurite2);
	echo $ficheSecuriteDao = FicheSecuriteDao::insert($fichesecurite3);
	echo $ficheSecuriteDao = FicheSecuriteDao::insert($fichesecurite4);

		///////////////////////////////////////
		//     FONCTION UTILES       		 //
		//   								 //
		///////////////////////////////////////

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