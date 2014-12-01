<?php
	/**
	 * @author Raphaël Bideau - 3iL
	 * @package Test
	 *
	 * Test Embarcation et EmbarcationDao
	 */

	require_once("../classloader.php");

	/* Test getAll*/
	echo "<br/>EmbarcationDao::getAll()<br/>";
	$array = EmbarcationDao::getAll();
	foreach ($array as $elem) {
		echo "$elem<br/>";
	}

	/* Test insert */
	$embarcation = new Embarcation(null);
	$embarcation->setLibelle('EMB-3');
	$embarcation->setCommentaire('Embarcation-3, creer lors d\'un test, indisponible');
	$embarcation->setMaxpersonne(20);
	$embarcation->setDisponible(false);
	$embarcation = EmbarcationDao::insert($embarcation);
	echo "<br/>EmbarcationDao::insert() (version: ".$embarcation->getVersion()."<br/>";

	/*Test getById*/
	$embarcation = EmbarcationDao::getById($embarcation->getId());
	echo "<br/>EmbarcationDao::getById(): $embarcation<br/>";

	/*Test update*/
	$embarcation->setLibelle('EMB-3 bis');
	$embarcation->setCommentaire('Embarcation-3, creer lors d\'un test, disponible');
	$embarcation->setDisponible(true);
	$embarcation = EmbarcationDao::update($embarcation);
	echo "<br/>EmbarcationDao::update() (version: ".$embarcation->getVersion().")<br/>";

	/*Test getById*/
	$embarcation = EmbarcationDao::getById($embarcation->getId());
	echo "<br/>EmbarcationDao::getById(): $embarcation<br/>";

	/*Test getDisponible*/
	echo "<br/>EmbarcationDao::getAllDisponible()<br/>";
	$array = EmbarcationDao::getAllDisponible();
	foreach ($array as $elem) {
		echo "$elem<br/>";
	}

	/*Suppression des inserts du test*/
	Dao::execute("DELETE FROM db_embarcation WHERE id_embarcation = ?",[$embarcation->getId()]);
	echo "<br/> *** <br/>Test Embarcation effectue avec succes<br/> ***<br/>";

?>