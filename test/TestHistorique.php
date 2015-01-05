<?php
	/**
	 * @author Raphaël Bideau - 3iL
	 * @package Test
	 *
	 * Fichier permettend de tester Historique et HistoriqueDao
	 */

	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."classloader.php");


	echo "<h1>Test historique</h1><br/>";

	$test_number = rand(0, 100);

	/* Test getAll*/
	echo "<br/>HistoriqueDao::getAll()<br/>";
	$array = HistoriqueDao::getAll();
	foreach ($array as $elem) {
		echo "$elem<br/>";
	}

	/* Test insert */
	$historique = new Historique("test", time(), 0);
	$historique->setSource(Historique::sourceWeb);
	$historique->setCommentaire("historique de test n$test_number");
	$historique = HistoriqueDao::insert($historique);
	echo "<br/>HistoriqueDao::insert(): $historique<br/>";

	/*Test getByLogin*/
	$historique = HistoriqueDao::getByUtilisateurAndTimestamp($historique->getLoginUtilisateur(), $historique->getTimestamp());
	echo "<br/>HistoriqueDao::getByUtilisateurAndTimestamp(): $historique<br/>";


	/*Test getAll*/
	echo "<br/>HistoriqueDao::getAll()<br/>";
	$array = HistoriqueDao::getAll();
	foreach ($array as $elem) {
		echo "$elem<br/>";
	}

	/*Test getAll*/
	echo "<br/>HistoriqueDao::getByUtilisateur()<br/>";
	$array = HistoriqueDao::getByUtilisateur($historique->getLoginUtilisateur());
	foreach ($array as $elem) {
		echo "$elem<br/>";
	}

	/*Test getAll*/
	echo "<br/>HistoriqueDao::getByFicheSecurite()<br/>";
	$array = HistoriqueDao::getByFicheSecurite($historique->getIdFicheSecurite());
	foreach ($array as $elem) {
		echo "$elem<br/>";
	}

	/*Suppression des inserts du test*/
	Dao::execute("DELETE FROM db_historique WHERE login_utilisateur = ? AND timestamp = ?",[$historique->getLoginUtilisateur(), $historique->getTimestamp()]);
	echo "<br/> *** <br/>Test Historique effectue avec succes<br/> ***<br/>";

?>