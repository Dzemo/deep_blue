<?php
	header('Content-Type: text/html; charset=utf-8');

	require_once("../utils/validation_palanquee.php");
	require_once("../classloader.php");

	/* TEST 1 */
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