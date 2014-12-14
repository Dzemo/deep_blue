<div class="titre">Liste des Fiches de Sécurité</div>

<?php
    require_once("utils/printListFiche.php");
    $fiches = FicheSecuriteDao::getAllNonArchivee();
    printPageListFiche($utilisateur, $fiches,true);
?>

