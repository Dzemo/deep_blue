<?php
	/**
	 * @author Raphaël Bideau - 3iL
	 * @package Test
	 *
	 * Fichier permettend de tester Site et SiteDao
	 */

	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."classloader.php");

	echo "<h1>Test site</h1><br/>";

	$test_number = rand(0, 100);

	/* Test getAll*/
	echo "<br/>SiteDao::getAll()<br/>";
	$array = SiteDao::getAll();
	foreach ($array as $elem) {
		echo "$elem<br/>";
	}

	/* Test insert */
	$site = new Site(null);
	$site->setNom("test-$test_number-nom");
	$site->setCommentaire("Site de test ($test_number)");
	$site = SiteDao::insert($site);
	echo "<br/>SiteDao::insert()<br/>";

	/*Test getById*/
	$site = SiteDao::getById($site->getId());
	echo "<br/>SiteDao::getById(): $site<br/>";

	
	/*Test update*/
	$site->setNom("test-$test_number-nom-update");
	$site->setCommentaire("Site de test ($test_number) mis à jours");
	$site = SiteDao::update($site);
	echo "<br/>SiteDao::update(): <br/>";

	/*Test getById*/
	$site = SiteDao::getById($site->getId());
	echo "<br/>SiteDao::getById(): $site<br/>";
	
	/*Suppression des inserts du test*/
	SiteDao::delete($site->getId());
	echo "<br/>SiteDao::delete(): <br/>";

	/*Test getById*/
	$site = SiteDao::getById($site->getId());
	echo "<br/>SiteDao::getById(): $site<br/>";

	/* Test getAll*/
	echo "<br/>SiteDao::getAll()<br/>";
	$array = SiteDao::getAll();
	foreach ($array as $elem) {
		echo "$elem<br/>";
	}

	echo "<br/> *** <br/>Test Site effectue avec succes<br/> ***<br/>";

?>