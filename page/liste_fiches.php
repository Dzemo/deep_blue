<div class="titre">Liste des Fiches de Sécurité</div>

<?php
	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."utils".DIRECTORY_SEPARATOR."printListFiche.php");
    $fiches = FicheSecuriteDao::getAllNonArchivee();
    printPageListFiche($utilisateur, $fiches,true);
?>

