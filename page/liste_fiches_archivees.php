<div class="titre">Archives des Fiches de Sécurité</div>

<?php
	require_once("utils/printListFiche.php");
	printPageListFiche($utilisateur, FicheSecuriteDao::getAllByEtat(FicheSecurite::etatArchive), false);
?>