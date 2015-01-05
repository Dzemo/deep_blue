<div class="titre">Consulter la fiche de sécurité</div>
<?php
	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."utils".DIRECTORY_SEPARATOR."printFiche.php");
    printFiche($_GET['id']);
?>