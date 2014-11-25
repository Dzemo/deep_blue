<?php
	/**
	 * @author Raphaël Bideau - 3iL
	 * @package Test
	 *
	 * Test Aptitude et AptitudeDao
	 */

	require_once("../classloader.php");

	$test_number = rand(1,100);

	/* Test getAll*/
	echo "<br/>AptitudeDao::getAll()<br/>";
	$array = AptitudeDao::getAll();
	foreach ($array as $elem) {
		echo "$elem<br/>";
	}

	/* Test insert */
	$aptitude = new Aptitude(null);
	$aptitude->setLibelleCourt("AT-$test_number");
	$aptitude->setLibelleLong("Aptitude Test $test_number");
	$aptitude = AptitudeDao::insert($aptitude);
	echo "<br/>AptitudeDao::insert() (version: ".$aptitude->getVersion()."<br/>";

	/*Test getById*/
	$aptitude = AptitudeDao::getById($aptitude->getId());
	echo "<br/>AptitudeDao::getById(): $aptitude<br/>";

	/*Test update*/
	$aptitude->setLibelleCourt("AT-".$test_number."u");
	$aptitude->setLibelleLong("Aptitude Test $test_number update");
	$aptitude = AptitudeDao::update($aptitude);
	echo "<br/>AptitudeDao::update() (version: ".$aptitude->getVersion().")<br/>";

	/*Test getById*/
	$aptitude = AptitudeDao::getById($aptitude->getId());
	echo "<br/>AptitudeDao::getById(): $aptitude<br/>";

	/*Test getDisponible*/
	echo "<br/>AptitudeDao::getByIds()<br/>";
	$array = AptitudeDao::getByIds([1, $aptitude->getId()]);
	foreach ($array as $elem) {
		echo "$elem<br/>";
	}

	/*Suppression des inserts du test*/
	Dao::execute("DELETE FROM db_aptitude WHERE id_aptitude = ?",[$aptitude->getId()]);
	echo "<br/> *** <br/>Test Aptitude effectue avec succes<br/> ***<br/>";

	$result = "";
	foreach (AptitudeDao::getAll() as $aptitude) {
		$result .= "<option value=\"".$aptitude->getId()."\"";

		if(count(null) > 0 && in_array($aptitude, null))
			$result .= " selected";

		$result .= ">".$aptitude->getLibelleCourt()."</option>";
	}

	echo "<br>".$result;
?>