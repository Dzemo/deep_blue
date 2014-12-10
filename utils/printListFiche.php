<?php
/**
 * Fonction permettant d'afficher à l'écran un tableau contenant toutes les fiches de sécurité ayant un état spécifié en paramètre
 * @param  [array] $$arrayFichesSecurite Liste des fiches de sécurité à affichés
 * @return [string] DataTables HTML affichant la liste de fiches de sécurités
 */
function printListFiche($arrayFichesSecurite) {
	?>
	<div class='liste-fiche'>
	<table id="example" class="display" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th colspan="3"></th>
				<th>Responsable</th>
				<th>Date</th>
				<th>Site</th>
				<th>Embarcation</th>
			</tr>
		</thead>
		<tbody>
			<?php
			if($arrayFichesSecurite){
				foreach ($arrayFichesSecurite as $fiche) {
				echo "<tr>";
					echo "<td><div class='icone-loupe' style='cursor: pointer;' onclick='window.location=\"index.php?page=consulter_fiche&id=".$fiche->getId()."\";'>
						</div>
						</td>
						<td>
						<div class='icone-crayon' style='cursor: pointer;' onclick='window.location=\"index.php?page=modification_fiche&id=".$fiche->getId()."\";'>
						</div></td>
						<td>
						<div class='icone-poubelle' style='cursor: pointer;' onclick='fs_delete(".$fiche->getId().")'>
						</div></td>";
					echo "<td class='responsableColor bold'>".$fiche->getDirecteurPlonge()->getPrenom()." ".$fiche->getDirecteurPlonge()->getNom()."</td>";
					echo "<td>".$fiche->getDate()."</td>";
					echo "<td>".($fiche->getSite() != null ? $fiche->getSite()->getNom() : "")."</td>";
					echo "<td>".$fiche->getEmbarcation()->getLibelle()."</td>";
				
					
				echo "</tr>";
				}
			}
			?>
		</tbody>
	</table>
</div>
<script src="js/supprimer_fiche_securite.js" type="text/javascript"></script>
<?php
}
?>