<div class="titre">Archives des Fiches de Sécurité</div>

<?php
	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."utils".DIRECTORY_SEPARATOR."printListFiche.php");
	printPageListFiche($utilisateur, FicheSecuriteDao::getAllByEtat(FicheSecurite::etatArchive), false);
?>