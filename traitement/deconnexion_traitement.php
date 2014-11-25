<?php
	require_once("../session.php");

	unset($_SESSION['utilisateur']);

	header('Location: '.$GLOBALS['dns'].'index.php');
	
	exit();	
?>