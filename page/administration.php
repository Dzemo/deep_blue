<div class="titre">Administration</div>

<?php
	//Affichage des messages de formulaire

	if(isset($_GET['msgType']) && isset($_GET['msg'])){
		$msgArray = explode(";",$_GET['msg']);
		?>
			<ul class="administration_<?php echo $_GET['msgType'];?>">
				<?php
					foreach($msgArray as $msg){
						if(strlen($msg) > 0)
							echo "<li>".$msg."</li>";
					}
				?>
			</ul>
		<?php
	}
?>


<div id="administrationContent">
	<ul>
		<li><a href="#adminUtilisateurs">Utilisateurs</a></li>
		<li><a href="#adminMoniteurs">Moniteurs</a></li>
		<li><a href="#adminEmbarcations">Embarcations</a></li>
		<li><a href="#adminSites">Sites de plongé</a></li>
	</ul>

	<!-- Utilisateurs -->
	<div id="adminUtilisateurs" class="tableAdmin">
		<div class="sous-titre">Utilisateurs</div>
		<table class="dataTable">
			<thead>
				<tr>
					<th>Login</th>
					<th>Nom</th>
					<th>Prénom</th>
					<th>Email</th>
					<th>Administrateur</th>
					<th>Actif</th>
					<th colspan="2">Modifier /<br>Desactiver</th>
					<th>Réinitialiser le <br>mot de passe</th>
					<th>Historique</th>
				<tr/>
			</thead>	
			<tbody>
				<?php
					$listeUtilisateurs = UtilisateurDao::getAll();
					for($i = 0; $i<count($listeUtilisateurs);$i++){
						$utilisateur = $listeUtilisateurs[$i];
						?>
							<tr>
								<td><?php echo $utilisateur->getLogin();?></td>
								<td><?php echo $utilisateur->getNom();?></td>
								<td><?php echo $utilisateur->getPrenom();?></td>
								<td><?php echo $utilisateur->getEmail();?></td>
								<td><?php printBool($utilisateur->isAdministrateur());?></td>
								<td><?php printBool($utilisateur->getActif());?></td>
								<td>
									<div 	class='icone-crayon' 
											style='cursor: pointer;' 
											onclick='$("#modal_edition_utilisateur_<?php echo $i;?>").bPopup()'
											>
									</div>
								</td>
								<td>
									<div 	class='<?php echo (!$utilisateur->getActif() ? 'icone-activer' : 'icone-poubelle');?>'
											style='cursor: pointer;' 
											onclick='$("#modal_desactivation_utilisateur_<?php echo $i;?>").bPopup()'
											>
									</div>
								</td>
								<td>
									<div 	class='icone-reset' 
											style='cursor: pointer;' 
											onclick='$("#modal_reinisialisation_mot_de_passe_utilisateur_<?php echo $i;?>").bPopup()'>
									</div>
								</td>
								<td>
									<div 	class='icone-loupe' 
											style='cursor: pointer;' 
											onclick='$("#modal_historique_<?php echo $i;?>").bPopup()'>
									</div>
								</td>
							</tr>
						<?php
					}
				?>
			</tbody>
		</table>
		<div class="buttons">
			<span 	class="button blue"
					style='cursor: pointer;' 
					onclick='$("#modal_edition_utilisateur_nouveau").bPopup()'
					>Ajouter un utilisateur</span>
		</div>
	</div>	

	<!-- Moniteurs -->
	<div id="adminMoniteurs" class="tableAdmin">
		<div class="sous-titre">Moniteurs</div>
		<table class="dataTable">
			<thead>
				<tr>
					<th>Nom</th>
					<th>Prénom</th>
					<th>Aptitudes</th>
					<th>Email</th>
					<th>Téléphone</th>
					<th>Directeur de plongée</th>
					<th>Actif</th>
					<th colspan="2">Modifier /<br>Desactiver</th>
				<tr/>
			</thead>	
			<tbody>
				<?php
					$listeMoniteur = MoniteurDao::getAll();
					for($i = 0; $i<count($listeMoniteur);$i++){
						$moniteur = $listeMoniteur[$i];
						?>
							<tr>
								<td><?php echo $moniteur->getNom();?></td>
								<td><?php echo $moniteur->getPrenom();?></td>
								<td><?php echo Aptitude::toLibelleString($moniteur->getAptitudes());?></td>
								<td><?php echo $moniteur->getEmail();?></td>
								<td><?php echo $moniteur->getTelephone();?></td>
								<td><?php printBool($moniteur->estDirecteurPlonge());?></td>
								<td><?php printBool($moniteur->estActif());?></td>
								<td>
									<div 	class='icone-crayon' 
											style='cursor: pointer;' 
											onclick='$("#modal_edition_moniteur_<?php echo $moniteur->getId();?>").bPopup()'
											></div>
								</td>
								<td>
									<div 	class='<?php echo (!$moniteur->estActif() ? 'icone-activer' : 'icone-poubelle');?>'
											style='cursor: pointer;' 
											onclick='$("#modal_desactivation_moniteur_<?php echo $moniteur->getId();?>").bPopup()'
											></div>
								</td>
							</tr>
						<?php
					}
				?>
			</tbody>
		</table>
		<div class="buttons">
			<span 	class="button blue"
					style='cursor: pointer;' 
					onclick='$("#modal_edition_moniteur_nouveau").bPopup()'
					>Ajouter un moniteur</span>
		</div>
	</div>

	<!-- Embarcations -->
	<div id="adminEmbarcations" class="tableAdmin">
		<div class="sous-titre">Embarcations</div>
		<table class="dataTable">
		<thead>
			<tr>
				<th>Libelle</th>
				<th>Commentaire</th>
				<th>Disponible</th>
				<th colspan="2">Modifier / Desactiver</th>
			<tr/>
			</thead>
			<tbody>
				<?php	
					$listeEmbarcation = EmbarcationDao::getAll();
					for($i = 0; $i<count($listeEmbarcation);$i++){
					$embarcation = $listeEmbarcation[$i];
						?>
							<tr>
								<td><?php echo $embarcation->getLibelle();?></td>
								<td><?php echo $embarcation->getCommentaire();?></td>
								<td><?php printBool($embarcation->getDisponible());?></td>
								<td>
									<div 	class='icone-crayon'
											style='cursor: pointer;'
											onclick='$("#modal_edition_embarcation_<?php echo $embarcation->getId();?>").bPopup()'
										></div>
								</td>
								<td>
									<div 	class='<?php echo (!$embarcation->getDisponible() ? 'icone-activer' : 'icone-poubelle');?>'
											style='cursor: pointer;'
											onclick='$("#modal_desactivation_embarcation_<?php echo $embarcation->getId();?>").bPopup()'
										></div>
								</td>
							</tr>
						<?php
					}
				?>	
			</tbody>
		</table>
		<div class="buttons">
			<span 	class="button blue"
					style='cursor: pointer;'
					onclick='$("#modal_edition_embarcation_nouvelle").bPopup()'
					>Ajouter une embarcation</span>
		</div>
	</div>

	<!-- Sites -->
	<div id="adminSites" class="tableAdmin">
		<div class="sous-titre">Site de plongée</div>
		<table class="dataTable">
			<thead>
				<tr>
					<th>Nom</th>
					<th>Commentaire</th>
					<th colspan="2">Modifier / Supprimer</th>
				<tr/>
			</thead>
			<tbody>
				<?php				
					$listeSite = SiteDao::getAll();
					foreach($listeSite as $site){
						?>
							<tr>
								<td><?php echo $site->getNom();?></td>
								<td><?php echo $site->getCommentaire();?></td>
								<td>
									<div 	class='icone-crayon' 
											style='cursor: pointer;' 
											onclick='$("#modal_edition_site_<?php echo $site->getId();?>").bPopup()'
											>
									</div>
								</td>
								<td>
									<div 	class='icone-poubelle'
											style='cursor: pointer;' 
											onclick='$("#modal_desactivation_site_<?php echo $site->getId();?>").bPopup()'
											>
									</div>
								</td>
							</tr>
						<?php
					}
				?>	
			</tbody>
		</table>
		<div class="buttons">
			<span 	class="button blue"
					style='cursor: pointer;' 
					onclick='$("#modal_edition_site_nouvelle").bPopup()'
					>Ajouter un site</span>
		</div>
	</div>

</div>

<script type="text/javascript">
	$(function(){
		$('#administrationContent').tabs(<?php if(isset($_GET['active'])) echo "{active:".$_GET['active']."}";?>);
	})
</script>

<?php 
	//Affichage des modaux
	
	//Utilisateur
	printModalEditionUtilisateur(null,null);
	for($i = 0; $i<count($listeUtilisateurs);$i++){
		printModalEditionUtilisateur($listeUtilisateurs[$i], $i);
		printModalHistorique($listeUtilisateurs[$i], $i);
		printModalDesactivationUtilisateur($listeUtilisateurs[$i], $i);
		printModalResetMdpUtilisateur($listeUtilisateurs[$i], $i);
	}
	
	//Moniteur
	$aptitudes = AptitudeDao::getAll();
	for($i = 0; $i<count($listeMoniteur);$i++){
		printModalEditionMoniteur($listeMoniteur[$i], $aptitudes);
		printModalDesactivationMoniteur($listeMoniteur[$i]);
	}
	printModalEditionMoniteur(null, $aptitudes);

	//Embarcation
	for($i = 0; $i<count($listeEmbarcation);$i++){
		printModalEditionEmbarcation($listeEmbarcation[$i]);
		printModalDesactivationEmbarcation($listeEmbarcation[$i]);
	}
	printModalEditionEmbarcation(null);

	//Site
	foreach($listeSite as $site){
		printModalEditionSite($site);
		printModalDesactivationSite($site);
	}
	printModalEditionSite(null);
	
?>
<script type="text/javascript">
	<?php
		for($i = 0; $i<count($listeMoniteur);$i++){
			echo "$('#moniteur_".$listeMoniteur[$i]->getId()."_aptitudes').SumoSelect()\n";
		}
		echo "$('#moniteur_nouveau_aptitudes').SumoSelect()\n";
	?>
</script>

<?php

	/**
	 * Affiche le modal de modification de l'utilisateur fourni en parametre ou le modal d'ajout d'un nouveau utilisateur
	 * @param  Utilisateur $u
	 * @param int          $index
	 */
	function printModalEditionUtilisateur($u, $index){
		?>
			<form id="modal_edition_utilisateur_<?php echo ($u ? $index : "nouveau");?>"
					method="POST"
					class="modal_form_administration"
					action="traitement/edition_utilisateur_traitement.php">
				<div class="sous-titre">
					<?php 
						if($u) echo "Modification de l'utilisateur '".$u->getLogin()."':";
						else   echo "Création d'un nouvel utilisateur:";
					?>
				</div>
				<input type="hidden" name="utilisateur_login" value="<?php if($u) echo $u->getLogin();?>">
				<input type="hidden" name="utilisateur_version" value="<?php if($u) echo $u->getVersion();?>">
				<table class="form_content">
					<tr>
						<td class="align_right">Nom </td>
						<td class="align_left">
							<input type="text" name="utilisateur_nom" value="<?php if($u) echo $u->getNom();?>">
						</td>
					</tr>
					<tr>
						<td class="align_right">Prenom </td>
						<td class="align_left">
							<input type="text" name="utilisateur_prenom" value="<?php if($u) echo $u->getPrenom();?>">
						</td>
					</tr>
					<tr>
						<td class="align_right">Email </td>
						<td class="align_left">
							<input type="text" name="utilisateur_email" value="<?php if($u) echo $u->getEmail();?>">
						</td>
					</tr>
					<tr>
						<td class="align_right">Administrateur </td>
						<td class="align_left">
							<input type="checkbox" name="utilisateur_administrateur" value="administrateur" <?php if($u && $u->isAdministrateur()) echo "checked";?>>
						</td>
					</tr>
					<tr>
						<td class="align_right">Actif </td>
						<td class="align_left">
							<input type="checkbox" name="utilisateur_actif" value="actif" <?php if($u && $u->getActif()) echo "checked";?>>
						</td>
					</tr>
				</table>
				<table class="form_buttons">
					<tr>
						<td>
							<span 	class="button green" 
									onclick="$('#modal_edition_utilisateur_<?php echo ($u ? $index : "nouveau");?>').submit()"
									>Enregistrer
							</span>
						</td>
						<td>
							<span 	class="button red" 
									onclick='$("#modal_edition_utilisateur_<?php echo ($u ? $index : "nouveau");?>").bPopup().close()'
									>Annuler
							</span>
						</td>
					</tr>
				</table>
			</form>
		<?php
	}

	/**
	 * Affiche le modal contenant le formulaire de desactivation/activation de l'utilisateur spécifié
	 * @param  Utilisateur $u 
	 */
	function printModalDesactivationUtilisateur($u, $index){
		if($u == null) return;
		?>
			<form id="modal_desactivation_utilisateur_<?php echo $index;?>"
					method="POST"
					class="modal_form_administration"
					action="traitement/toggle_active_traitement.php">
				<div class="">
					Voulez-vous marquer comme <span class="<?php echo (!$u->getActif() ? "ouiColor" :"nonColor");?>"><?php if($u->getActif()) echo "in";?>actif</span> l'utilisateur <?php echo $u->getPrenom()." ".$u->getNom();?> ?
				</div>
				<input type="hidden" name="utilisateur_login" value="<?php echo $u->getLogin();?>">
				<input type="hidden" name="utilisateur_actif" value="<?php echo (!$u->getActif() ? "1" : "0");?>">
				<table class="form_buttons">
					<tr>
						<td>
							<span 	class="button green" 
									onclick="$('#modal_desactivation_utilisateur_<?php echo $index;?>').submit()"
									>Rendre <?php if($u->getActif()) echo "in";?>actif
							</span>
						</td>
						<td>
							<span 	class="button red" 
									onclick='$("#modal_desactivation_utilisateur_<?php echo $index;?>").bPopup().close()'
									>Annuler
							</span>
						</td>
					</tr>
				</table>
			</form>
		<?php
	}

	/**
	 * Affiche le modal permettand d'envoyé un email de réinitialisation de mot de passe à un utilisateur
	 * @param  Utilisateur $u 
	 */
	function printModalResetMdpUtilisateur($u, $index){
		if($u == null) return;
		?>
			<form id="modal_reinisialisation_mot_de_passe_utilisateur_<?php echo $index;?>"
					method="POST"
					class="modal_form_administration"
					action="traitement/reinitialiser_mdp_envoie_traitement.php">
				<div class="">
					Voulez-vous envoyer un e-mail de réinitialisation de mot de passe à <?php echo $u->getPrenom()." ".$u->getNom();?> ?
				</div>
				<input type="hidden" name="login" value="<?php echo $u->getLogin();?>">
				<table class="form_buttons">
					<tr>
						<td>
							<span 	class="button green" 
									onclick="$('#modal_reinisialisation_mot_de_passe_utilisateur_<?php echo $index;?>').submit()"
									>Envoyer
							</span>
						</td>
						<td>
							<span 	class="button red" 
									onclick='$("#modal_reinisialisation_mot_de_passe_utilisateur_<?php echo $index;?>").bPopup().close()'
									>Annuler
							</span>
						</td>
					</tr>
				</table>
			</form>
		<?php
	}

	/**
	 * Affiche le modal de modification du moniteur fourni en parametre ou le modal d'ajout d'un nouveau moniteur
	 * @param  Moniteur $m 
	 * @param  array    $aptitudes Tableau de la liste des aptitudes dans base
	 */
	function printModalEditionMoniteur($m, $aptitudes){
		?>
			<form id="modal_edition_moniteur_<?php echo ($m ? $m->getId() : "nouveau");?>"
					method="POST"
					class="modal_form_administration"
					action="traitement/edition_moniteur_traitement.php">
				<div class="sous-titre">
					<?php 
						if($m) echo "Modification du moniteur '".$m->getPrenom()." ".$m->getNom()."':";
						else   echo "Création d'un nouveau moniteur:";
					?>
				</div>
				<input type="hidden" name="moniteur_id" value="<?php if($m) echo $m->getId();?>">
				<input type="hidden" name="moniteur_version" value="<?php if($m) echo $m->getVersion();?>">
				<table class="form_content">
					<tr>
						<td class="align_right">Nom </td>
						<td class="align_left">
							<input type="text" name="moniteur_nom" value="<?php if($m) echo $m->getNom();?>">
						</td>
					</tr>
					<tr>
						<td class="align_right">Prenom </td>
						<td class="align_left">
							<input type="text" name="moniteur_prenom" value="<?php if($m) echo $m->getPrenom();?>">
						</td>
					</tr>
					<tr>
						<td class="align_right">Aptitudes </td>
						<td class="align_left">
							<select id="moniteur_<?php echo ($m ? $m->getId() : "nouveau");?>_aptitudes"
									name="moniteur_aptitudes[]" 
									multiple="multiple" 
									>
									<?php printListeOptionsAptitudes($aptitudes, ($m ? $m->getAptitudes() : null));?>
							</select>
						</td>
					</tr>

					<tr>
						<td class="align_right">Email </td>
						<td class="align_left">
							<input type="text" name="moniteur_email" value="<?php if($m) echo $m->getEmail();?>">
						</td>
					</tr>
					<tr>
						<td class="align_right">Téléphone </td>
						<td class="align_left">
							<input type="text" name="moniteur_telephone" value="<?php if($m) echo $m->getTelephone();?>">
						</td>
					</tr>

					<tr>
						<td class="align_right">Directeur de plongé </td>
						<td class="align_left">
							<input type="checkbox" name="moniteur_directeur_plonge" value="directeur_plonge" <?php if($m && $m->estDirecteurPlonge()) echo "checked";?>>
						</td>
					</tr>
					<tr>
						<td class="align_right">Actif </td>
						<td class="align_left">
							<input type="checkbox" name="moniteur_actif" value="actif" <?php if($m && $m->estActif()) echo "checked";?>>
						</td>
					</tr>
				</table>
				<table class="form_buttons">
					<tr>
						<td>
							<span 	class="button green" 
									onclick="$('#modal_edition_moniteur_<?php echo ($m ? $m->getId() : "nouveau");?>').submit()"
									>Enregistrer
							</span>
						</td>
						<td>
							<span 	class="button red" 
									onclick='$("#modal_edition_moniteur_<?php echo ($m ? $m->getId() : "nouveau");?>").bPopup().close()'
									>Annuler
							</span>
						</td>
					</tr>
				</table>
			</form>
		<?php
	}

	/**
	 * Affiche le modal contenant le formulaire de desactivation/activation du moniteur spécifié
	 * @param  Moniteur $m 
	 */
	function printModalDesactivationMoniteur($m){
		if($m == null) return;
		?>
			<form id="modal_desactivation_moniteur_<?php echo $m->getId();?>"
					method="POST"
					class="modal_form_administration"
					action="traitement/toggle_active_traitement.php">
				<div class="">
					Voulez-vous marquer comme <span class="<?php echo (!$m->estActif() ? "ouiColor" :"nonColor");?>"><?php if($m->estActif()) echo "in";?>actif</span> le moniteur <?php echo $m->getPrenom()." ".$m->getNom();?> ?
				</div>
				<input type="hidden" name="moniteur_id" value="<?php echo $m->getId();?>">
				<input type="hidden" name="moniteur_actif" value="<?php echo (!$m->estActif() ? "1" : "0");?>">
				<table class="form_buttons">
					<tr>
						<td>
							<span 	class="button green" 
									onclick="$('#modal_desactivation_moniteur_<?php echo $m->getId();?>').submit()"
									>Rendre <?php if($m->estActif()) echo "in";?>actif
							</span>
						</td>
						<td>
							<span 	class="button red" 
									onclick='$("#modal_desactivation_moniteur_<?php echo $m->getId();?>").bPopup().close()'
									>Annuler
							</span>
						</td>
					</tr>
				</table>
			</form>
		<?php
	}

	/**
	 * Affiche le modal de modification de l'embarcation fourni en parametre ou le modal d'ajout d'une nouvelle embarcation
	 * @param  Embarcation $e 
	 */
	function printModalEditionEmbarcation($e){
		?>
			<form id="modal_edition_embarcation_<?php echo ($e ? $e->getId() : "nouvelle");?>"
					method="POST"
					class="modal_form_administration"
					action="traitement/edition_embarcation_traitement.php">
				<div class="sous-titre">
					<?php 
						if($e) echo "Modification de l'embarcation '".$e->getLibelle()."':";
						else   echo "Création d'une nouvelle embarcation:";
					?>
				</div>
				<input type="hidden" name="embarcation_id" value="<?php if($e) echo $e->getId();?>">
				<input type="hidden" name="embarcation_version" value="<?php if($e) echo $e->getVersion();?>">
				<table class="form_content">
					<tr>
						<td class="align_right">Libelle </td>
						<td class="align_left">
							<input type="text" name="embarcation_libelle" value="<?php if($e) echo $e->getLibelle();?>">
						</td>
					</tr>
					<tr>
						<td class="align_right">Contenance Maximum </td>
						<td class="align_left">
							<input type="text" name="embarcation_maxpersonne" value="<?php if($e) echo $e->getMaxpersonne();?>">
						</td>
					</tr>
					<tr>
						<td class="align_right">Commentaire </td>
						<td class="align_left">
							<input type="text" name="embarcation_commentaire" value="<?php if($e) echo $e->getCommentaire();?>">
						</td>
					</tr>
					<tr>
						<td class="align_right">Disponible </td>
						<td class="align_left">
							<input type="checkbox" name="embarcation_disponible" value="disponible" <?php if($e && $e->getDisponible()) echo "checked";?>>
						</td>
					</tr>
				</table>
				<table class="form_buttons">
					<tr>
						<td>
							<span 	class="button green" 
									onclick="$('#modal_edition_embarcation_<?php echo ($e ? $e->getId() : "nouvelle");?>').submit()"
									>Enregistrer
							</span>
						</td>
						<td>
							<span 	class="button red" 
									onclick='$("#modal_edition_embarcation_<?php echo ($e ? $e->getId() : "nouvelle");?>").bPopup().close()'
									>Annuler
							</span>
						</td>
					</tr>
				</table>
			</form>
		<?php
	}

	/**
	 * Affiche le modal contenant le formulaire de desactivation/activation d'embarcation spécifié
	 * @param  Embarcation $e 
	 */
	function printModalDesactivationEmbarcation($e){
		if($e == null) return;
		?>
			<form id="modal_desactivation_embarcation_<?php echo $e->getId();?>"
					method="POST"
					class="modal_form_administration"
					action="traitement/toggle_active_traitement.php">
				<div class="">
					Voulez-vous marquer comme <span class="<?php echo (!$e->getDisponible() ? "ouiColor" :"nonColor");?>"><?php if($e->getDisponible()) echo "in";?>disponible</span> l'embarcation '<?php echo $e->getLibelle();?>' ?
				</div>
				<input type="hidden" name="embarcation_id" value="<?php echo $e->getId();?>">
				<input type="hidden" name="embarcation_disponible" value="<?php echo (!$e->getDisponible() ? "1" : "0");?>">
				<table class="form_buttons">
					<tr>
						<td>
							<span 	class="button green" 
									onclick="$('#modal_desactivation_embarcation_<?php echo $e->getId();?>').submit()"
									>Rendre <?php if($e->getDisponible()) echo "in";?>disponible
							</span>
						</td>
						<td>
							<span 	class="button red" 
									onclick='$("#modal_desactivation_embarcation_<?php echo $e->getId();?>").bPopup().close()'
									>Annuler
							</span>
						</td>
					</tr>
				</table>
			</form>
		<?php
	}

	/**
	 * Affiche le modal de modification du site fourni en parametre ou le modal d'ajout d'un nouveau site
	 * @param  Site $e 
	 */
	function printModalEditionSite($s){
		?>
			<form id="modal_edition_site_<?php echo ($s ? $s->getId() : "nouvelle");?>"
					method="POST"
					class="modal_form_administration"
					action="traitement/edition_site_traitement.php">
				<div class="sous-titre">
					<?php 
						if($s) echo "Modification du site '".$s->getNom()."':";
						else   echo "Création d'un nouveau site:";
					?>
				</div>
				<input type="hidden" name="site_id" value="<?php if($s) echo $s->getId();?>">
				<table class="form_content">
					<tr>
						<td class="align_right">Nom </td>
						<td class="align_left">
							<input type="text" name="site_nom" value="<?php if($s) echo $s->getNom();?>">
						</td>
					</tr>
					<tr>
						<td class="align_right">Commentaire </td>
						<td class="align_left">
							<input type="text" name="site_commentaire" value="<?php if($s) echo $s->getCommentaire();?>">
						</td>
					</tr>
				</table>
				<table class="form_buttons">
					<tr>
						<td>
							<span 	class="button green" 
									onclick="$('#modal_edition_site_<?php echo ($s ? $s->getId() : "nouvelle");?>').submit()"
									>Enregistrer
							</span>
						</td>
						<td>
							<span 	class="button red" 
									onclick='$("#modal_edition_site_<?php echo ($s ? $s->getId() : "nouvelle");?>").bPopup().close()'
									>Annuler
							</span>
						</td>
					</tr>
				</table>
			</form>
		<?php
	}

	/**
	 * Affiche le modal contenant le formulaire de desactivation d'un site
	 * @param  Site $s 
	 */
	function printModalDesactivationSite($s){
		if($s == null) return;
		?>
			<form id="modal_desactivation_site_<?php echo $s->getId();?>"
					method="POST"
					class="modal_form_administration"
					action="traitement/toggle_active_traitement.php">
				<div class="">
					Voulez-vous supprimer le site   '<?php echo $s->getNom();?>' ?
				</div>
				<input type="hidden" name="site_id" value="<?php echo $s->getId();?>">
				<input type="hidden" name="site_disponible" value="0">
				<table class="form_buttons">
					<tr>
						<td>
							<span 	class="button green" 
									onclick="$('#modal_desactivation_site_<?php echo $s->getId();?>').submit()"
									>Supprimer
							</span>
						</td>
						<td>
							<span 	class="button red" 
									onclick='$("#modal_desactivation_site_<?php echo $s->getId();?>").bPopup().close()'
									>Annuler
							</span>
						</td>
					</tr>
				</table>
			</form>
		<?php
	}

	/**
	 * Affiche le modal contenant les historiques de l'utilisateur
	 * @param  Utilisateur $u
	 * @param  int         $index
	 */
	function printModalHistorique($u, $index){
		if($u == null) return;

		$historiques = HistoriqueDao::getByUtilisateur($u->getLogin());
		?>
			<div id="modal_historique_<?php echo $index;?>"
					method="POST"
					class="modal_form_administration"
					action="traitement/edition_utilisateur_traitement.php">
				<div class="sous-titre"><?php echo "Historique des actions de '".$u->getLogin()."':";?></div>
				<?php
					if($historiques == null || count($historiques) == 0){
						echo "Pas encore d'historique des actions de cette utilisateur<br>";
					}
					else{
						?>
						<div class="sous-titre">Historique</div>
						<table class="historique dataTable">
							<thead>
								<th>Date</th>
								<th>Commentaire</th>
								<th>Fiche de sécuriter</th>
								<th>Source</th>
							</thead>
							<tbody>
								<?php 
									foreach ($historiques as $historique) {
										echo "<tr>";
											echo "<td>".$historique->getDateLong()."</td>";
											echo "<td>".$historique->getCommentaire()."</td>";
											if($historique->getIdFicheSecurite() != null)
												echo "<td><a href=\"".$GLOBALS['dns']."index?php?page=consulter_fiche&id=".$historique->getIdFicheSecurite()."\">Voir la fiche</a></td>";
											else
												echo "<td>NA</td>";
											echo "<td>".$historique->getSource()."</td>";
										echo "</tr>";
									}
								?>
							</tbody>
						</table>
						<?php
					}
				?>				
				<span 	class="button blue" 
									onclick='$("#modal_historique_<?php echo $index;?>").bPopup().close()'
									>ok
							</span>
			</div>
		<?php
	}

	/**
	 * Affiche les balise HTML option pour les aptitudes de la base. Si un tableau est fourni, les aptitudes dans le tableau sont séléctionné
	 * @param  array $aptitudes 		Tout les aptitudes en base 
	 * @param  array $aptitudesPossede 	Aptitudes possédé par le plongeur ou le moniteur
	 */
	function printListeOptionsAptitudes($aptitudes, $aptitudesPossede){
		$result = "";
		if($aptitudes != null){
			foreach ($aptitudes as $aptitude) {
				$result .= "<option value=\"".$aptitude->getId()."\"";
				if(count($aptitudesPossede) > 0 && in_array($aptitude, $aptitudesPossede))
					$result .= " selected";
				$result .= ">".$aptitude->getLibelleCourt()."</option>";
			}
		}
		echo $result;
	}

	/**
	 * Permet d'afficher la valeur d'un boolean sous la form oui/non avec oui en gras
	 * @param  boolean $boolean 
	 */
	function printBool($boolean){
		if($boolean)
			echo "<span class='ouiColor'>Oui</span>";
		else
			echo "<span class='nonColor'>Non</span>";
	}
?>