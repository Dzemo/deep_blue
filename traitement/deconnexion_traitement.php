<?php
	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."session.php");

	unset($_SESSION['utilisateur']);

	header('Location: '.$GLOBALS['dns'].'index.php');
	
	exit();	
?>