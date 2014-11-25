<?php
	function classLoader($classe)
	{
	  require $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'deep_blue'.DIRECTORY_SEPARATOR.'class'.DIRECTORY_SEPARATOR.$classe.'.class.php'; // On inclut la classe correspondante au paramètre passé.
	}
	spl_autoload_register('classLoader'); // On enregistre la fonction en autoload pour qu'elle soit appelée dès qu'on instanciera une classe non déclarée.
?>