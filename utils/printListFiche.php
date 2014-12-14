<?php

/**
 * Affiche la liste des fiches
 * Si un utilisateur connecté est lié à un moniteur, affiche d'abord la liste des fiches dont il est directeur de plongée puis les autres fiches dans un tableau séparé
 * @param  Utilisateur $utilisateur l'utilisateur courant pour qui la liste des fiches est affichée
 * @param  array $listeFichesSecurite          Liste des fiches à affichés
 * @param  boolean $iconeModificationSuppression A true affiche les icones de modification et suppression de fiche, a false affiche uniquement l'icone de loupe
 */
function printPageListFiche(Utilisateur $utilisateur, $listeFichesSecurite, $iconeModificationSuppression){
	$moniteurAssocie = $utilisateur->getMoniteurAssocie();

	if($moniteurAssocie != null){
		//Trie les fiches du moniteur
		$listeFichesSecuritePerso = array();
		$listeFichesSecuriteAutre = array();
		for($i = 0; $i < count($listeFichesSecurite); $i++){
			if($listeFichesSecurite[$i]->getDirecteurPlonge() != null && 
				$listeFichesSecurite[$i]->getDirecteurPlonge()->getId() == $moniteurAssocie->getId()){
				$listeFichesSecuritePerso[] = $listeFichesSecurite[$i];
			}
			else{
				$listeFichesSecuriteAutre[] = $listeFichesSecurite[$i];
			}
		}

		//Affiches d'abord la listes des fiches du moniteur
		?>			
			<div class='liste-fiche'>
				<div class="sous-titre">Mes fiches de sécurité</div>
				<?php printListFiche($listeFichesSecuritePerso, "dataTablePerso", $iconeModificationSuppression);?>
			</div>
		<?php

		$listeFichesSecurite = $listeFichesSecuriteAutre;
	}

	//Affichage des autres fiches
	?>
		<div class='liste-fiche'>
			<?php if($moniteurAssocie != null) echo '<div class="sous-titre">Autres fiches de sécurité</div>';?>
			<?php printListFiche($listeFichesSecurite, "dataTableAutre", $iconeModificationSuppression);?>
			<script type="text/javascript" language="javascript" class="init">
				$(document).ready(function() {
				    $('.display').dataTable( {
				        "info":     false,
				        "language": {
				            "lengthMenu": "Afficher _MENU_ fiches par page",
				            "zeroRecords": "Aucune fiche de sécurité correspondente",
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
		</div>
	<?php
}

/**
 * Fonction affichant à l'écran un tableau contenant toutes les fiches de sécurité ayant un état spécifié en paramètre
 * @param  array $listeFichesSecurite Liste des fiches de sécurité à affichés
 * @param  string $dataTableId id de la datatable contenant la liste des fiches
 * @param  boolean $iconeModificationSuppression A true affiche les icones de modification et suppression de fiche, a false affiche uniquement l'icone de loupe
 */
function printListFiche($listeFichesSecurite, $dataTableId, $iconeModificationSuppression) {
	?>	
		<table id="<?php echo $dataTableId;?>" class="display" cellspacing="0" width="100%">
			<thead>
				<tr>
					<?php 
						if($iconeModificationSuppression){
							?>
								<th class="th-button">Voir</th>
								<th class="th-button">Modifier</th>
								<th class="th-button">Supprimer</th>
							<?php
						}
						else{
							?>
								<th class="th-button">Voir</th>
							<?php
						}
					?>
					<th>Responsable</th>
					<th>Date</th>
					<th>Site</th>
					<th>Embarcation</th>
					<th>Palanquée(s)</th>
					<th>Plongeur(s)</th>
				</tr>
			</thead>
			<tbody>
				<?php
				if($listeFichesSecurite){
					foreach ($listeFichesSecurite as $fiche) {
						?>
							<tr>
								<td>
									<div class='icone-loupe' style='cursor: pointer;' onclick='window.location="index.php?page=consulter_fiche&id=<?php echo $fiche->getId();?>";'>
									</div>
								</td>
								<?php
									if($iconeModificationSuppression){
										?>
											<td>									
												<div class='icone-crayon' style='cursor: pointer;' onclick='window.location="index.php?page=modification_fiche&id=<?php echo $fiche->getId();?>";'>
												</div>
											</td>
											<td>
												<div class='icone-poubelle' style='cursor: pointer;' onclick='fs_delete(<?php echo $fiche->getId();?>)'>
												</div>
											</td>
										<?php
									}
								?>
								<td class='responsableColor bold'>
									<?php echo $fiche->getDirecteurPlonge()->getPrenom()." ".$fiche->getDirecteurPlonge()->getNom();?>
								</td>
								<td>
									<?php echo $fiche->getDate();?>
								</td>
								<td>
									<?php echo ($fiche->getSite() != null ? $fiche->getSite()->getNom() : ""); ?>
								</td>
								<td>
									<?php echo $fiche->getEmbarcation()->getLibelle();?>
								</td>
								<td>
									<?php echo count($fiche->getPalanques())?>
								</td>
								<td>
									<?php echo count($fiche->getPlongeurs());?>
								</td>					
							</tr>
						<?php
					}
				}
				?>
			</tbody>
		</table>
	<?php
}
?>
<script src="js/supprimer_fiche_securite.js" type="text/javascript"></script>