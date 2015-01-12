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

		/////////////////////////////
		//     CREATION FICHE      //
		//                         //
		/////////////////////////////
	$fichesecurite = new FicheSecurite(666);
		$ficheSecuriteDao = new FicheSecuriteDao();
	//	$palanqueDao = new PalanqueDao();
		$embarcationDao = new EmbarcationDao();
		$embarcation = $embarcationDao->getById(1);
		$moniteurDao = new MoniteurDao();
		$moniteur = $moniteurDao->getById(5);
		$siteDao = new SiteDao();
		$site = $siteDao->getById(1);
	$fichesecurite->setEmbarcation($embarcation);
	$fichesecurite->setDirecteurPlonge($moniteur);
	$fichesecurite->setSite($site);
	$fichesecurite->setTimestamp(1420742072);
	$fichesecurite->setEtat("CREER");
	$fichesecurite->getDisponible(true);
	//	echo "<br><h1>ID de plongé :".$fichesecurite->getId()."</h1>";


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
	$palanque2->setDureePrevue(20);
	$palanque1->setNumero(1);

	$moniteur1 = new Moniteur(1);
	$moniteur1 = generateMoniteur();
	$moniteur1->ajouterAptitude(AptitudeDao::getByLibelleCourt("E-2"));
	$palanque1->setMoniteur($moniteur1);
	
	$plongeur11 = generatePlongeur();
	$plongeur11->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-12"));
	$plongeurs[] = $plongeur11;
	$palanque1->setPlongeurs($plongeurs);


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

		///////////////////////////////////////
		//     Test TECHNIQUE                //
		//   (effectif maximal + GP) 		 //
		///////////////////////////////////////
	
	echo "<h3>Test d'une plongée techniques en effectif maximal + GP (1 moniteur + 4 plongeurs + 1 GP)</h1>";
	$palanque3 = new Palanque(3);
	$palanque3->setTypeGaz(Palanque::gazAir);
	$palanque3->setTypePlonge(Palanque::plongeTechnique);
	$palanque3->setProfondeurPrevue(35.0);
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
	
	$palanque->setPlongeurs(array($plongeur31, $plongeur32, $plongeur33, $plongeur34, $plongeur35));

		///////////////////////////////////////
		//     Test TECHNIQUE                //
		//   (effectif maximal + P4) 		 //
		///////////////////////////////////////
		
	echo "<h3>Test d'une plongée techniques en effectif maximal + P-4 (1 moniteur + 4 plongeurs + 1 P-4)</h1>";
	$palanque4 = new Palanque(4);
	$palanque4->setTypeGaz(Palanque::gazAir);
	$palanque4->setTypePlonge(Palanque::plongeTechnique);
	$palanque4->setProfondeurPrevue(35.0);
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
	
	$palanque->setPlongeurs(array($plongeur41, $plongeur42, $plongeur43, $plongeur44, $plongeur45));

		///////////////////////////////////////
		//     Test TECHNIQUE                //
		//   (surreffectif 5 plongeurs)		 //
		///////////////////////////////////////
		
echo "<h3>Test d'une plongée techniques en sur-effectif(1 moniteur + 5 plongeurs (hors GP/P4))</h1>";
	$palanque5 = new Palanque(5);
	$palanque5->setTypeGaz(Palanque::gazAir);
	$palanque5->setTypePlonge(Palanque::plongeTechnique);
	$palanque5->setProfondeurPrevue(35.0);
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
	
	$palanque->setPlongeurs(array($plongeur51, $plongeur52, $plongeur53, $plongeur54, $plongeur55));

		///////////////////////////////////////////////
		//     Test TECHNIQUE         		         //
		//   (surreffectif 4 plongeurs + 2 P4)		 //
		///////////////////////////////////////////////

echo "<h3>Test d'une plongée techniques en sur-effectif (1 moniteur + 4 plongeurs + 2 P-4)</h1>";
	$palanque6 = new Palanque(6);
	$palanque6->setTypeGaz(Palanque::gazAir);
	$palanque6->setTypePlonge(Palanque::plongeTechnique);
	$palanque6->setProfondeurPrevue(35.0);
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
	
	$palanque->setPlongeurs(array($plongeur61, $plongeur62, $plongeur63, $plongeur64, $plongeur65, $plongeur66));

		///////////////////////////////////////
		//     Test TECHNIQUE         		 //
		//   (surreffectif profondeur 60m)	 //
		///////////////////////////////////////
		
echo "<h3>Test d'une plongée techniques en sur-effectif pour une profondeur de 60 (1 moniteur + 4 plongeurs + 1 GP)</h1>";
	$palanque7 = new Palanque(7);
	$palanque7->setTypeGaz(Palanque::gazAir);
	$palanque7->setTypePlonge(Palanque::plongeTechnique);
	$palanque7->setProfondeurPrevue(55.0);
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

	
	$palanque->setPlongeurs(array($plongeur71, $plongeur72, $plongeur73, $plongeur74, $plongeur75));


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
	$palanque8->setNumero(8);

	$moniteur8 = generateMoniteur();
	$moniteur8->ajouterAptitude(AptitudeDao::getByLibelleCourt("E-2"));
	$palanque8->setMoniteur($moniteur8);
	
	$plongeur81 = generatePlongeur();
	$plongeur81->ajouterAptitude(AptitudeDao::getByLibelleCourt("PE-12"));
	$palanque->setPlongeurs($plongeur81);

		///////////////////////////
		//     Test ENCADRE   	 //
		//   (effectif maximal)	 //
		///////////////////////////
		
echo "<h3>Test d'une plongée techniques en effectif maximal classique (4 plongeurs + 1 moniteur)</h1>";
	$palanque9 = new Palanque(9);
	$palanque9->setTypeGaz(Palanque::gazAir);
	$palanque9->setTypePlonge(Palanque::plongeEncadre);
	$palanque9->setProfondeurPrevue(35.0);
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

		///////////////////////////////////
		//     Test ENCADRE   			 //
		//   (effectif maximal + GP)	 //
		///////////////////////////////////

echo "<h3>Test d'une plongée encadré en effectif maximal + GP (1 moniteur + 4 plongeurs + 1 GP)</h1>";
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

		///////////////////////////////////////
		//     SET PALANQUE + INSERT         //
		//   (Fin de la création fiche)      //
		///////////////////////////////////////
	$fichesecurite->setPalanques(array($palanque1,$palanque2));
	echo $ficheSecuriteDao = FicheSecuriteDao::insert($fichesecurite);



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