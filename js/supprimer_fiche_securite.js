/**
 * Annule l'édition ou la création d'une feuille de sécurité. Si il y a eu des 
 * m, affiche une popup d'alerte
 */
function fs_delete(id_fiche){
	if(confirm("Êtes-vous sûr de vouloir supprimer cette fiche ?")){
		document.location.href="traitement/suppression_fiche_traitement.php?id="+id_fiche;
	}
}