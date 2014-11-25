<?php

	require_once("classloader.php");


	session_start();
	
	require_once("config.php");
	
	//ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'].'/deep_blue/session_storage');
	//ini_set('session.gc_maxlifetime', 3600);
	
	if(isset($_SESSION['utilisateur']) && $_SESSION['utilisateur'] != null && gettype($_SESSION['utilisateur']) != "boolean"){
		$utilisateur = $_SESSION['utilisateur'];
		$connecte = true;
	} else{
		$utilisateur = null;
		$connecte = false;
	}
?>