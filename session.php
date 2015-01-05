<?php
	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."classloader.php");
	session_start();	
	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."config.php");
	if(isset($_SESSION['utilisateur']) && $_SESSION['utilisateur'] != null && gettype($_SESSION['utilisateur']) != "boolean"){
		$utilisateur = $_SESSION['utilisateur'];
		$connecte = true;
	} else{
		$utilisateur = null;
		$connecte = false;
	}
?>