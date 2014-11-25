<div class="titre">Liste des Fiches de Sécurité</div>
<script type="text/javascript" language="javascript" class="init">
$(document).ready(function() {
    $('#example').dataTable( {
        "info":     false,
        "language": {
            "lengthMenu": "Afficher _MENU_ fiches par page",
            "zeroRecords": "Aucune Fiche trouvé",
            "info": "Afficher la page _PAGE_ sur _PAGES_",
            "infoEmpty": "Aucune Information Disponible",
            "infoFiltered": "(filtré parmis _MAX_ fiches)",
            "sSearch": "Rechercher",
            "paginate": {
                 "sNext": "Suivant",
                 "sPrevious" : "Précédent"
               }
        }
    } );
} );
</script>
<?php
    require_once("utils/printListFiche.php");
    $fiches = FicheSecuriteDao::getAllNonArchivee();
    printListFiche($fiches);
?>
