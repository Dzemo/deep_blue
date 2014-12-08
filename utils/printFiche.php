<?php
require_once('utils/DateStringUtils.php');
/**
 * Fonction permettant d'afficher une fiche avec toutes ses caractéristiques, selon un numéro de fiche
 * @param  [mediumint] $numero_fiche
 * @return [string] Tableaux affichant les caractéristiques d'une fiche
 */
function printFiche($numero_fiche) {
	// Récupération de la fiche de sécurité en cours
	$ficheSecurite = FicheSecuriteDao::getById($numero_fiche);
	?>
	<div>
		<table class="TableStyle infoFiche">
			<tr>
				<th>Date</th>
				<th>Site</th>
				<th>Directeur de Plongé</th>
				<th>Embarcation</th>
				<th>Commentaire</th>
				<th>Etat Fiche</th>
			</tr>
			<tr>
				<td>
					<?php echo $ficheSecurite->getDate();?>
				</td>
				<td>
					<?php 
						if($ficheSecurite->getSite() != null)
							echo $ficheSecurite->getSite()->getNom();
					?>
				</td>
				<td class="responsableColor bold">
					<?php echo $ficheSecurite->getDirecteurPlonge()->getPrenom()." ".$ficheSecurite->getDirecteurPlonge()->getNom();?>
				</td>
				<td>
					<?php echo $ficheSecurite->getEmbarcation()->getLibelle();?>
				</td>
				<td>
					<?php echo $ficheSecurite->getEmbarcation()->getCommentaire();?>
				</td>
				<td>
					<?php echo $ficheSecurite->getEtat();?>
				</td>
			</tr>
		</table>
	</div>
	<table class="TableStyle">
		<thead>
			<tr>
				<th>Palanquée</th>
				<th>Prénom et Nom</th>
				<th>Contacts</th>
				<th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Aptitudes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
				<th>Rôle</th>
				<th>Type Plongée</th>
				<th>Gaz</th>
				<th>Profondeur <br>prévu (m)</th>
				<th>Profondeur <br>réalisé (m)</th>
				<th>Temps d'immersion <br>prévu</th>
				<th>Temps d'immersion <br>réalisé</th>
			</tr>
		</thead>
		<tbody>
			<?php
			
			///////////////
			// PALANQUEES //
			///////////////
			if($ficheSecurite->getPalanques() != null){
				foreach ($ficheSecurite->getPalanques() as $palanquee) {
					$firstIteration = TRUE;
					// On récupère le nombre de personnes dans la palanquées
						// Est-ce qu'il y a un moniteur ? Oui alors il y a 1 moniteur + des plongeurs
					if($palanquee->getMoniteur() != NULL){
						$nbPersonnes = count($palanquee->getPlongeurs()) + 1;
					}
						// Non ? Alors il y a seulement des plongeurs
					else {
						$nbPersonnes = count($palanquee->getPlongeurs());
					}
				
					// On affiche le numéro de palanquée avec un rowspan = nombre de personnes
					//echo "<td>Numero Palanquée</td>";
					echo "<tr>";
					echo "<td rowspan='".$nbPersonnes."'>".$palanquee->getNumero()."</td>";
					///////////////
					// MONITEUR //
					///////////////
					// On affiche une nouvelle ligne qui contient
						// Prénom et Nom du Moniteur
						if($palanquee->getMoniteur() != NULL){
							echo "<td class='moniteurColor'>".$palanquee->getMoniteur()->getPrenom()." ".$palanquee->getMoniteur()->getNom()."</td>";
						//Infos de contact (email et ou téléphone)
							$contact = "";
							if($palanquee->getMoniteur()->getTelephone() != null)
								$contact .= $palanquee->getMoniteur()->getTelephone();
							if($palanquee->getMoniteur()->getEmail()){
								if(strlen($contact) > 0) $contact .= "<br>";
								$contact .= $palanquee->getMoniteur()->getEmail();
							}
							echo "<td>".$contact."</td>";
						// Aptitudes
							echo "<td>";
							foreach ($palanquee->getMoniteur()->getAptitudes() as $aptitudes) {
								echo $aptitudes->getLibelleCourt()." ";
							}
							echo "</td>";
						// Sa fonction de moniteur
							echo "<td class='moniteurColor bold'>Moniteur</td>";
						
							if($firstIteration){
						// Type Plongée
							echo "<td rowspan='".$nbPersonnes."'>".typePlongeToString($palanquee->getTypePlonge())."</td>";
						// Gaz		
							echo "<td class='".strtolower($palanquee->getTypeGaz())."Color bold' rowspan='".$nbPersonnes."'>".$palanquee->getTypeGaz()."</td>";
						//Profondeur prévu
							echo "<td rowspan='".$nbPersonnes."'>".$palanquee->getProfondeurPrevue()."</td>";				
						//Profondeur réalisé
							echo "<td rowspan='".$nbPersonnes."'>".$palanquee->getProfondeurRealisee()."</td>";
						//Temps d'immersion prévu
							echo "<td rowspan='".$nbPersonnes."'>".convertToMinSec($palanquee->getDureePrevue())."</td>";
						//Temps d'immersion réalisé
							echo "<td rowspan='".$nbPersonnes."'>".convertToMinSec($palanquee->getDureeRealisee())."</td>";
							}
							echo "</tr>";
							$firstIteration = FALSE;
						}
					///////////////
					// PLONGEURS //
					///////////////
					if($palanquee->getPlongeurs() != null){
						foreach ($palanquee->getPlongeurs() as $plongeur) {
							// On affiche une nouvelle ligne qui contient
							// Prénom et Nom du Plongeur
							if(!$firstIteration){
								echo "<tr>";
							}
								echo "<td class='plongeurColor'>".$plongeur->getPrenom()." ".$plongeur->getNom()."</td>";
								//Infos de contact (email et ou téléphone)
								$contact = "tel:";
								if($plongeur->getTelephone() != null)
									$contact .= $plongeur->getTelephone();
								if($plongeur->getTelephoneUrgence()){
									if(strlen($contact) > 0) $contact .= "<br>tel urgence:";
									$contact .= $plongeur->getTelephoneUrgence();
								}
								echo "<td>".$contact."</td>";
								// Aptitudes
								echo "<td>";
								foreach ($plongeur->getAptitudes() as $aptitudes) {
									echo $aptitudes->getLibelleCourt()." ";
								}
								echo "</td>";
								// Sa fonction de plongeur
								echo "<td class='plongeurColor bold'>Plongeur</td>";
								
								if($firstIteration){
								// Type Plongé
								echo "<td rowspan='".$nbPersonnes."'>".typePlongeToString($palanquee->getTypePlonge())."</td>";
								// Gaz
								echo "<td class='".strtolower($palanquee->getTypeGaz())."Color bold' rowspan='".$nbPersonnes."'>".$palanquee->getTypeGaz()."</td>";
								//Profondeur prévu
								echo "<td rowspan='".$nbPersonnes."'>".$palanquee->getProfondeurPrevue()."</td>";				
								//Profondeur réalisé
								echo "<td rowspan='".$nbPersonnes."'>".$palanquee->getProfondeurRealisee()."</td>";
								//Temps d'immersion prévu
								echo "<td rowspan='".$nbPersonnes."'>".convertToMinSec($palanquee->getDureePrevue())."</td>";
								//Temps d'immersion réalisé
								echo "<td rowspan='".$nbPersonnes."'>".convertToMinSec($palanquee->getDureeRealisee())."</td>";
								}
							echo "</tr>";						
							$firstIteration = FALSE;
						}
					}
				}
			}
			?>
		</tbody>
	</table>
	<table id="bouttons">
		<tr>
			<td><span onclick="location.href='index.php?page=modification_fiche&id=<?php echo $numero_fiche;?>'" class="button purple save-button">Modifier Fiche</span></td>
			<td><span onclick="location.href='index.php?page=liste_fiches'" class="button orange save-button">Retour</span></td>
		</tr>
	</table>
	<?php
		$historiques = HistoriqueDao::getbyFicheSecurite($numero_fiche);
		$utilisateurs = array();
		if($historiques != null){
			?>
				<div class="sous-titre">Historique</div>
				<table class="TableStyle historique">
					<thead>
						<th>Date</th>
						<th>Commentaire</th>
						<th>Utilisateur</th>
						<th>Source</th>
					</thead>
					<?php			
						foreach ($historiques as $historique) {
							if(!isset($utilisateurs[$historique->getLoginUtilisateur()])){
								$db_utilisateur = UtilisateurDao::getByLogin($historique->getLoginUtilisateur());
								$utilisateurs[$historique->getLoginUtilisateur()] = $db_utilisateur->getPrenom()." ".$db_utilisateur->getNom();
							}
							echo "<tr>";
								echo "<td>".$historique->getDateLong()."</td>";
								echo "<td>".$historique->getCommentaire()."</td>";
								echo "<td>".$utilisateurs[$historique->getLoginUtilisateur()]."</td>";
								echo "<td>".$historique->getSource()."</td>";
							echo "</tr>";
						}
					?>
				</table>
			<?php
		}
	}
?>