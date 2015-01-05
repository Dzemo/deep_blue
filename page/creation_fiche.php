<div class="titre">Créer une nouvelle fiche de sécurité:</div>
<?php
	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."utils".DIRECTORY_SEPARATOR."printFormFicheSecurite.php");
	printFormFicheSecurite('index.php?page=liste_fiches', null);
?>