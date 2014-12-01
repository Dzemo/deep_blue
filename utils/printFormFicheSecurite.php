<?php
require_once("config.php");
require_once("utils/DateStringUtils.php");
/**
 * Affiche le formulaire de modification ou de création d'une fiche de sécurité
 * @param  string 			$redirect	l'addresse à laquel rediriger l'utilisateur quand il clique sur le bouton annulé
 * @param  FicheSecurite 	$f 			la fiche à modifié, null dans le cas d'une création
 */
function printFormFicheSecurite($redirect, FicheSecurite $f = null){
	$aptitudes = AptitudeDao::getAll();
	// On va créer une variable qui définit s'il s'agit d'une création ou d'une modification, la variable $m
	if($f != null)
		$m = true;
	else
		$m = false;
	?>
		<form id="form_fiche_securite" 
			  method="post" 
			  action="traitement/enregistrer_fiche_traitement.php">
			<div id="form_fiche_palanque">
				<div class="ajouter-palanque">
					<span onclick="fs_ajouter_palanque()" class="button blue">Ajouter une palanquée</span>
				</div>
				<ul>
					<li><a href="#form_fiche_general">Infos</a></li>
					<?php 
					if($m){
						foreach ($f->getPalanques() as $palanque) {
							echo '<li id="li_pal'.$palanque->getNumero().'"><a href="#pal'.$palanque->getNumero().'">Palanquée '.$palanque->getNumero().'</a></li>';
						}
					} 
					?>
				 </ul>
				<!-- Premier Onglet : Infos -->
				<div id="form_fiche_general">
					<ul id="fiche_erreur" class="erreur pas_erreur">
					</ul>
					<input 	type="hidden" 
							id="fiche_securite_id"
							name="fiche_securite_id" 
							value="<?php if($m) echo $f->getId();?>" />
					<input 	type="hidden" 
							id="fiche_securite_version"
							name="fiche_securite_version" 
							value="<?php if($m) echo $f->getVersion();?>" />
					
					<table class="panel-wrapper">
						<tr>
							<td>
								<div id="site_dp" class="panel-form">
									<table>
										
										<tr id="embarcation">
											<td><label for="id_embarcation">Embarcation</label></td>
											<td>
												<select name="id_embarcation"
														id="id_embarcation"
														>
														<?php printEmbarcationOptions($f); ?>
												</select>
											</td>
										</tr>
										
										<tr>
											<td><label for="site">Site</label></td>
											<td>
												<input 	type="hidden" 
														id="id_site" 
														name="id_site" 
														value="<?php if($m && $f->getSite() != null) echo $f->getSite()->getId();?>"/>
												<input 	type="text" 
														id="nom_site" 
														name="nom_site" 
														value="<?php if($m && $f->getSite() != null) echo $f->getSite()->getNom();?>"/>
											</td>
										</tr>
										
										<tr>
											<td><label for="directeur_plonge">Directeur de plongé</label></td>
											<td>
												<input 	type="text" 
														id="directeur_plonge" 
														name="directeur_plonge" 
														autocomplete="off" 
														value="<?php if($m) echo $f->getDirecteurPlonge()->getPrenom()." ".$f->getDirecteurPlonge()->getNom();?>"/>
												<input 	type="hidden" 
														id="id_directeur_plonge" 
														name="id_directeur_plonge" 
														value="<?php if($m) echo $f->getDirecteurPlonge()->getId();?>"/>
											</td>
										</tr>
									</table>
								</div>
							</td>
							<td>
								<div id="date_heure">
									<table class="panel-form">
										<tr>
											<td><label for="date">Date</label></td>
											<td colspan="3">
												<input 	type="text"
												 		id="date" 
												 		name="date" 
														placeholder="JJ/MM/AAAA"
												 		value="<?php if($m) echo $f->getDate(); else echo tmspToDate(time());?>"/>
												<div id="datepicker"></div>
											</td>
										</tr>
									</table>
								</div>
							</td>
						</tr>		
					</table>
				</div>
				<!-- Début de la liste des palanqués -->
				<?php
					//Affichage des palanquée
					if($m){
						foreach ($f->getPalanques() as $palanque) {
							printFormFicheSecuritePalanque($aptitudes, $palanque);
						}
					}
				?>
				<!-- Fin de la liste des palanqués -->
			</div>
			<table id="bouttons">
				<tr>
					<td><span onclick="fs_enregistrer()" class="button green save-button">Enregister</span></td>
					<td><span onclick="fs_annule('<?php echo $redirect; ?>')" class="button orange save-button">Annuler</span></td>
				</tr>
			</table>
			<?php
				//print de la palanquée modèle
				printFormFicheSecuritePalanque($aptitudes, null);
				//print du plongeur modèle
				?><table><?php
				printFormFicheSecuritePalanquePlongeur($aptitudes, null, null, null);
				?></table><?php
			?>
			<div id="modal_enregistrer" class="modal">
				<div id="lancement">
					Vérification des données
					<div id="progressbar"></div>
				</div>
				<div id="resultat_succes">
					Succes
				</div>
				<div id="resultat_erreur">
					Erreur
				</div>
			</div>
		</form>
		<div id="autocomplte_source" style="display:none">
			<div id="source_moniteur">
			</div>
		</div>
		<script type="text/javascript">
			///////////////////////
			//Initialisation Js //
			///////////////////////
			///
			var tabs;
			///
			$(function() {

				//Initialisation du système d'onglet
				tabs = $('#form_fiche_palanque').tabs({ collapsible: false });

				//Ajout du date picker
		        $( "#date" ).datepicker();

		        //Ajout de la progresse bar pour l'envoi des données
		        $( "#progressbar" ).progressbar({
		      		value: false
		        });

		        //Ajout de l'autocomplete sur le directeur de plongé			        
			    $("#directeur_plonge").autocomplete({
			        source: [<?php printDirecteurPlongeSource() ?>],
			        minLength: 0,
			        dataType: 'json',
			        change: function(event, ui) {
			            $("#id_directeur_plonge").val(ui.item ? ui.item.id : "");
			        },
			        select: function(event, ui) {
			            $("#id_directeur_plonge").val(ui.item ? ui.item.id : "");
			        }
			    }).focus(function(){     
		            $(this).data("uiAutocomplete").search($(this).val());
		        });

		        //Ajout de l'autocomplete sur le site        
			    $("#nom_site").autocomplete({
			        source: [<?php printSiteSource() ?>],
			        minLength: 0,
			        dataType: 'json',
			        change: function(event, ui) {
			            $("#id_site").val(ui.item ? ui.item.id : "");
			        },
			        select: function(event, ui) {
			            $("#id_site").val(ui.item ? ui.item.id : "");
			        }
			    }).focus(function(){     
		            $(this).data("uiAutocomplete").search($(this).val());
		        });

			    //Ajout du sumoselect sur l'embarcation
			   	$('#id_embarcation').SumoSelect();

		        //Ajout des sumoselect sur les aptitudes des moniteurs et des plongeurs si il y en a
		        <?php
		        	if($m){
		        		foreach ($f->getPalanques() as $palanque) {
		        			?>
				   			var numero_palanque = <?php echo $palanque->getNumero();?>;
		        			$('#pal'+numero_palanque+'_type_plonge').change(fs_toggle_form_moniteur);
		        			//Ajout du SumoSelect sur le type de plongé et type de gaz
		        			$('#pal'+numero_palanque+'_moniteur_aptitudes').SumoSelect();
		        			$('#pal'+numero_palanque+'_type_plonge').SumoSelect();
		        			$('#pal'+numero_palanque+'_type_gaz').SumoSelect();
		        			//Ajout de l'autocomplete sur profondeur prévu et durée prévu
		        			$('#pal'+numero_palanque+'_profondeur_prevue').autocomplete(autocompleteProfondeur)
		        														.focus(function(){
		        															$(this).data('uiAutocomplete').search($(this).val());
		        														});
		        			$('#pal'+numero_palanque+'_duree_prevue').autocomplete(autocompleteDuree)
		        														.focus(function(){
		        															$(this).data('uiAutocomplete').search($(this).val());
		        														});	
		        			//Ajout de l'autocomplete sur nom et prenom du moniteur
		        			$('#pal'+numero_palanque+'_moniteur_nom').autocomplete(getOptionAutocompleteMoniteur(numero_palanque, "nom"));
		        			$('#pal'+numero_palanque+'_moniteur_prenom').autocomplete(getOptionAutocompleteMoniteur(numero_palanque, "prenom"));
		        			<?php
		        			for($i = 0; $i < count($palanque->getPlongeurs()); $i++){
		        				?>
		        				var numero_plongeur = <?php echo ($i+1);?>;
		        				//Ajout du SumoSelect sur aptitudes du plongeur
		        				$('#pal'+numero_palanque+'_plon'+numero_plongeur+'_plongeur_aptitudes').SumoSelect();
		        				//Ajout de l'autocomplete sur nom/prenom/datenaissance du plongeur
		        				$('#pal'+numero_palanque+'_plon'+numero_plongeur+'_plongeur_nom').autocomplete(getOptionAutocompletePlongeur(numero_palanque, numero_plongeur, "nom"));
		        				$('#pal'+numero_palanque+'_plon'+numero_plongeur+'_plongeur_prenom').autocomplete(getOptionAutocompletePlongeur(numero_palanque, numero_plongeur, "prenom"));
		        				$('#pal'+numero_palanque+'_plon'+numero_plongeur+'_plongeur_date_naissance').autocomplete(getOptionAutocompletePlongeur(numero_palanque, numero_plongeur, "date_naissance"));
		        				<?php
		        			}
		        		}
		        	}
				
				?>
			});
			/////////////////
			//Fonction Js //
			/////////////////
			/**
			 * Ajoute une palanque vierge à la fiche de sécurité courante
			 */
			function fs_ajouter_palanque(){
				//Récupération du numéro max des palanquée
				var prochainNumeroPalanque = 0;
				$('#form_fiche_palanque').children().each(function(){
					if($(this).data('numpalanque') > prochainNumeroPalanque)
						prochainNumeroPalanque = $(this).data('numpalanque');
				});
				prochainNumeroPalanque++;
				var palanque = $('#pal_js_clonable').clone(true);
				//Mise à jours des infos de la palanquée
				palanque.data('numpalanque', prochainNumeroPalanque);
				palanque.attr('id','pal'+prochainNumeroPalanque);
				palanque.find('.palanque_title').html('Palanquée numéro '+prochainNumeroPalanque);
				//Rempalcement des pal_ et numero_palanque
				palanque.html(palanque.html().replace(/pal_/g,"pal"+prochainNumeroPalanque+"_"));
				palanque.html(palanque.html().replace(/numero_palanque/g,prochainNumeroPalanque));
				
				//Ajouter le LI des tabs et du Contenu	
				  var tabTemplate = "<li id='li"+prochainNumeroPalanque+"'><a href='#{href}'>#{label}</a></li>";
			      var label = "Palanquée "+prochainNumeroPalanque,
			        id = "pal" + prochainNumeroPalanque,
			        li = $( tabTemplate.replace( /#\{href\}/g, "#" + id ).replace( /#\{label\}/g, label ) );
			 	  
			      tabs.find( ".ui-tabs-nav" ).append( li );
			      tabs.append(palanque);
			      tabs.tabs( "refresh" );
			      tabs.tabs( "option", "active", prochainNumeroPalanque );
			    fs_ajouter_plongeur(prochainNumeroPalanque);
			    					
				//Ajout des handler
			    //Permet de vérifier si le formulaire à été modifier avant de quitter
			    $("input").change(fs_modification_form);
			    $("select").change(fs_modification_form);
			    //Permet d'afficher le moniteur uniquement pour les palanquée en plongé technique ou encadree
				$('#pal'+prochainNumeroPalanque+' .types .plonge select').change(fs_toggle_form_moniteur);
				//Ajout des sumoSelect sur le type de plongé et le gaz
				$('#pal'+prochainNumeroPalanque+' .types .plonge select').SumoSelect();
				$('#pal'+prochainNumeroPalanque+' .types .gaz select').SumoSelect();
				//Ajout de l'autocomplete sur la profondeur et durée prévu
				$('#pal'+prochainNumeroPalanque+'_profondeur_prevue').autocomplete(autocompleteProfondeur)
    														.focus(function(){
    															$(this).data('uiAutocomplete').search($(this).val());
    														});
    			$('#pal'+prochainNumeroPalanque+'_duree_prevue').autocomplete(autocompleteDuree)
    														.focus(function(){
    															$(this).data('uiAutocomplete').search($(this).val());
    														});
    			//Ajout de l'autocomplete sur nom et prenom du moniteur
    			$('#pal'+prochainNumeroPalanque+'_moniteur_nom').autocomplete(getOptionAutocompleteMoniteur(prochainNumeroPalanque, "nom"));
		        $('#pal'+prochainNumeroPalanque+'_moniteur_prenom').autocomplete(getOptionAutocompleteMoniteur(prochainNumeroPalanque, "prenom"));
			}
			/**
			 * Ajoute un plongeur a la palanque 
			 * @param  {int} numero_palanque palanquée a laquel ajouter un plongeur
			 */
			function fs_ajouter_plongeur(numero_palanque){
				//Récupération du numéro max des plongeurs
				var prochainNumeroPlongeur = 0;
				$('#pal'+numero_palanque+'_plongeurs .plongeur').each(function(){
					if($(this).data('numplongeur') > prochainNumeroPlongeur)
						prochainNumeroPlongeur = $(this).data('numplongeur');
				});
				prochainNumeroPlongeur++;
				var plongeur = $('#plon_js_clonable').clone(true);
				//Mise à jours des infos du plongeur
				plongeur.data('numplongeur', prochainNumeroPlongeur);
				plongeur.attr('id','pal'+numero_palanque+'_plon'+prochainNumeroPlongeur);
				//Rempalcement des plon_ et numero_plongeur
				plongeur.html(plongeur.html().replace(/pal_/g,"pal"+numero_palanque+"_"));
				plongeur.html(plongeur.html().replace(/plon_/g,"plon"+prochainNumeroPlongeur+"_"));
				plongeur.html(plongeur.html().replace(/numero_palanque/g,numero_palanque));
				plongeur.html(plongeur.html().replace(/numero_plongeur/g,prochainNumeroPlongeur));
				
				plongeur.appendTo('#pal'+numero_palanque+'_plongeurs');
				//Ajout des handler
			    //Permet de vérifier si le formulaire à été modifier avant de quitter
			    $("input").change(fs_modification_form);
			    $("select").change(fs_modification_form);
			    //Ajout du SumoSelect sur les aptitudes du plongeur
				$('#pal'+numero_palanque+'_plon'+prochainNumeroPlongeur+'_plongeur_aptitudes').SumoSelect();
				//Ajout de l'autocomplete sur nom/prenom/datenaissance du plongeur
				$('#pal'+numero_palanque+'_plon'+prochainNumeroPlongeur+'_plongeur_nom').autocomplete(getOptionAutocompletePlongeur(numero_palanque, prochainNumeroPlongeur, "nom"));
				$('#pal'+numero_palanque+'_plon'+prochainNumeroPlongeur+'_plongeur_prenom').autocomplete(getOptionAutocompletePlongeur(numero_palanque, prochainNumeroPlongeur, "prenom"));
				$('#pal'+numero_palanque+'_plon'+prochainNumeroPlongeur+'_plongeur_date_naissance').autocomplete(getOptionAutocompletePlongeur(numero_palanque, prochainNumeroPlongeur, "date_naissance"));
			}
			//////////////////////////////
			//Variable autocomplete //
			//////////////////////////////
			
			 //Options de l'autocomplete pour la profondeur prévu, à ajouter à toute les palanquée
			var autocompleteProfondeur = {
		        source: [	{value:  6, label: " 6 mètres"},
		        			{value: 12, label: "12 mètres"},
		        			{value: 20, label: "20 mètres"},
		        			{value: 40, label: "40 mètres"},
		        			{value: 60, label: "60 mètres"}
		        		],
		        minLength: 0,
		        dataType: 'json'
		    };
		     //Options de l'autocomplete pour la durée prévu, à ajouter à toute les palanquée
		    var autocompleteDuree = {
		        source: [	{value:  20, label: "20 min"},
		        			{value:  40, label: "40 min"},
		        			{value:  60, label: "1h"},
		        			{value:  90, label: "1h30"},
		        			{value: 120, label: "2h"},
		        		],
		        minLength: 0,
		        dataType: 'json'
		    };
		    //Options de l'autocomplete pour le nom/prénom du moniteur, à ajouter à toute les palanquée
		    function getOptionAutocompletePlongeur(numero_palanque, numero_plongeur, champs){
		    	var rawSource = [<?php printRawSourceAutocompletePlongeur();?>];
		    	var source_val = [];
		    	for(var i = 0; i < rawSource.length; i++){
		    		source_val[i] = {	value: "",
		    							id: rawSource[i].id, 
		    							nom: rawSource[i].nom, 
		    							prenom:rawSource[i].prenom, 
		    							aptitudes:rawSource[i].aptitudes, 
		    							date_naissance: rawSource[i].date_naissance,
		    							label: rawSource[i].label
		    						};
		    		if(champs == "nom") source_val[i].value = rawSource[i].nom;
		    		if(champs == "prenom") source_val[i].value = rawSource[i].prenom;
		    		if(champs == "date_naissance") source_val[i].value = rawSource[i].date_naissance;			    		
		    	}
		    	return {
			        source: source_val,
			        minLength: 0,
			        dataType: 'json',
			        change: function(event, ui) {
			            //$("#pal"+numero_palanque+"_plon"+numero_plongeur+"_plongeur_id").val(ui.item ? ui.item.id : "");
			            //$("#pal"+numero_palanque+"_plon"+numero_plongeur+"_plongeur_nom").val(ui.item ? ui.item.nom : "");
			            //$("#pal"+numero_palanque+"_plon"+numero_plongeur+"_plongeur_prenom").val(ui.item ? ui.item.prenom : "");
			            //$("#pal"+numero_palanque+"_plon"+numero_plongeur+"_plongeur_date_naissance").val(ui.item ? ui.item.date_naissance : "");
			           	//setSelectValue("pal"+numero_palanque+"_plon"+numero_plongeur+"_plongeur_aptitudes", (ui.item ? ui.item.aptitudes : []));
			        },
			        select: function(event, ui) {
			            $("#pal"+numero_palanque+"_plon"+numero_plongeur+"_plongeur_id").val(ui.item ? ui.item.id : "");
			            $("#pal"+numero_palanque+"_plon"+numero_plongeur+"_plongeur_nom").val(ui.item ? ui.item.nom : "");
			            $("#pal"+numero_palanque+"_plon"+numero_plongeur+"_plongeur_prenom").val(ui.item ? ui.item.prenom : "");
			            $("#pal"+numero_palanque+"_plon"+numero_plongeur+"_plongeur_date_naissance").val(ui.item ? ui.item.date_naissance : "");
			           	setSelectValue("pal"+numero_palanque+"_plon"+numero_plongeur+"_plongeur_aptitudes", (ui.item ? ui.item.aptitudes : []));
			        }
		    	};
		    }
		    //Options de l'autocomplete pour le nom/prénom du moniteur, à ajouter à toute les palanquée
		    function getOptionAutocompleteMoniteur(numero_palanque, champs){
		    	var rawSource =  [<?php printRawSourceAutocompleteMoniteur();?>];
		    	var source_val = [];
		    	for(var i = 0; i < rawSource.length; i++){
		    		source_val[i] = {	value: "",
		    							id: rawSource[i].id, 
		    							nom: rawSource[i].nom, 
		    							prenom:rawSource[i].prenom, 
		    							telephone:rawSource[i].telephone,
		    							email:rawSource[i].email,
		    							aptitudes:rawSource[i].aptitudes,
		    							label: rawSource[i].label
		    						};
		    		if(champs == "nom") source_val[i].value = rawSource[i].nom;
		    		if(champs == "prenom") source_val[i].value = rawSource[i].prenom;	    		
		    	}
		    	return {
			        source: source_val,
			        minLength: 0,
			        dataType: 'json',
			        change: function(event, ui) {				        	
			            $("#pal"+numero_palanque+"_moniteur_id").val(ui.item ? ui.item.id : "");
			            $("#pal"+numero_palanque+"_moniteur_nom").val(ui.item ? ui.item.nom : "");
			            $("#pal"+numero_palanque+"_moniteur_prenom").val(ui.item ? ui.item.prenom : "");
			            $("#pal"+numero_palanque+"_moniteur_aptitudes").val(ui.item ? ui.item.aptitudes : "");
			            $("#pal"+numero_palanque+"_moniteur_email").val(ui.item ? ui.item.email : "");
			            $("#pal"+numero_palanque+"_moniteur_telephone").val(ui.item ? ui.item.telephone : "");
			        },
			        select: function(event, ui) {				        	
			            $("#pal"+numero_palanque+"_moniteur_id").val(ui.item ? ui.item.id : "");
			            $("#pal"+numero_palanque+"_moniteur_nom").val(ui.item ? ui.item.nom : "");
			            $("#pal"+numero_palanque+"_moniteur_prenom").val(ui.item ? ui.item.prenom : "");
			            $("#pal"+numero_palanque+"_moniteur_aptitudes").val(ui.item ? ui.item.aptitudes : "");
			            $("#pal"+numero_palanque+"_moniteur_email").val(ui.item ? ui.item.email : "");
			            $("#pal"+numero_palanque+"_moniteur_telephone").val(ui.item ? ui.item.telephone : "");
			        }
		    	};
		    }
		</script>
	<?php
}
/**
 * Affiche le formulaire de modification d'une palanquée si une palanquée est passé en parametre ou le formulaire modele
 * de création de palanquée (caché)
 * @param  array 	$aptitudes Le tableau de toute les aptitudes précédement récupéré en base
 * @param  Palanque $pal la palanque à modifié, null dans le cas d'une création
 */
function printFormFicheSecuritePalanque($aptitudes, Palanque $pal = null){
	if($pal != null)
		$m = true;
	else
		$m = false;
	?>
		<div id="pal<?php echo ($m ? $pal->getNumero() : "_js_clonable");?>"
			 class="palanque" data-numpalanque="<?php if($m) echo $pal->getNumero();?>">	
			<div class="palanque_title">Palanquée numéro <?php if($m) echo $pal->getNumero();?></div>
			<ul id="pal<?php if($m) echo $pal->getNumero();?>_erreur" class="erreur pas_erreur">
			</ul>
			
			<input 	type="hidden" 
					id="pal<?php if($m) echo $pal->getNumero();?>_palanque_id" 
					name="pal<?php if($m) echo $pal->getNumero();?>_palanque_id" 
					value="<?php if($m) echo $pal->getId();?>"/>
			<input 	type="hidden" 
					id="pal<?php if($m) echo $pal->getNumero();?>_palanque_version" 
					name="pal<?php if($m) echo $pal->getNumero();?>_palanque_version" 
					value="<?php if($m) echo $pal->getVersion();?>"/>
			<div class="types">
				<!-- Début information commune à la palanquée -->
					<table class="inside-panel">
						<tr> <!-- Ligne pour les titres de colonnes -->
							<td class="gaz">
								Type de gaz utilisé
							</td>
							<td>
								<label for="pal<?php if($m) echo $pal->getNumero();?>_type_plonge">Type de plongé</label>
							</td>
							<td class="profondeur">
								<label for="pal<?php if($m) echo $pal->getNumero();?>_profondeur_prevue">Profondeur prévue (mètre)</label>
							</td>	
							<td>
								<label for="pal<?php if($m) echo $pal->getNumero();?>_duree_prevue">Durée prévue (minute)</label>
							</td>
						</tr>
						<tr>
							<td class="gaz">
								<select name="pal<?php if($m) echo $pal->getNumero();?>_type_gaz"
										id="pal<?php if($m) echo $pal->getNumero();?>_type_gaz"
										>
										<option value="<?php echo Palanque::gazAir;?>"
												data-sumo-class="airColor"
												<?php if($m && $pal->getTypeGaz() == Palanque::gazAir) echo "selected"?>
												>Air</option>
										<option value="<?php echo Palanque::gazAir;?>"
												data-sumo-class="nitroxColor"
												<?php if($m && $pal->getTypeGaz() == Palanque::gazNitrox) echo "selected"?>
												>Nitrox</option>
								</select>
							</td>
							<td class="plonge">									
								<select name="pal<?php if($m) echo $pal->getNumero();?>_type_plonge"
										id="pal<?php if($m) echo $pal->getNumero();?>_type_plonge"
										>										
										<option value="<?php echo Palanque::plongeAutonome;?>"
												<?php if($m && $pal->getTypePlonge() == Palanque::plongeAutonome) echo "selected"?>
												>Plongée autonome</option>
										<option value="<?php echo Palanque::plongeTechnique;?>"
												<?php if($m && $pal->getTypePlonge() == Palanque::plongeTechnique) echo "selected"?>
												>Plongée technique</option>
										<option value="<?php echo Palanque::plongeEncadre;?>"
												<?php if($m && $pal->getTypePlonge() == Palanque::plongeEncadre) echo "selected"?>
												>Plongée encadrée</option>
										<option value="<?php echo Palanque::plongeBapteme;?>"
												<?php if($m && $pal->getTypePlonge() == Palanque::plongeBapteme) echo "selected"?>
												>Baptême</option>
								</select>									
							</td>
							<td>
								<input 	type="text" 
										name="pal<?php if($m) echo $pal->getNumero();?>_profondeur_prevue" 
										id="pal<?php if($m) echo $pal->getNumero();?>_profondeur_prevue" 
										value="<?php if($m) echo $pal->getProfondeurPrevue(); else echo Palanque::plongeDefaultProf; ?>"/>
							</td>
							<td class="duree">
								
								<input 	type="text" 
										name="pal<?php if($m) echo $pal->getNumero();?>_duree_prevue" 
										id="pal<?php if($m) echo $pal->getNumero();?>_duree_prevue" 
										value="<?php if($m) echo $pal->getDureePrevue(); else echo Palanque::plongeDefaultDuree;?>"/>
							</td>
						</tr>
					</table>
				<!-- Fin information commune à la palanquée -->
			</div>
			<div id="pal<?php if($m) echo $pal->getNumero();?>_moniteur" 
				 class="moniteur" <?php if($m && $pal->getTypePlonge() == Palanque::plongeAutonome) echo "style=\"display:none\"";?>>
					<?php 
					if($m)
						$palm = $pal->getMoniteur() != null;
					else
						$palm = null;
				?>
				<div class="plongeur_title">Moniteur</div>
				<ul id="pal<?php if($m) echo $pal->getNumero();?>_moniteur_erreur" class="erreur pas_erreur">
				</ul>
				<table class="inside-panel">
					<tr>
						<td>Nom</td>
						<td>Prénom</td>
						<td>Aptitudes</td>
						<td>Téléphone</td>
						<td>Email</td>
					</tr>
					<tr> 
						<td>
							<input 	type="text" 
									id="pal<?php if($m) echo $pal->getNumero();?>_moniteur_nom" 
									name="pal<?php if($m) echo $pal->getNumero();?>_moniteur_nom" 
									value="<?php if($palm) echo $pal->getMoniteur()->getNom();?>"/>
						</td>
						<td>
							<input 	type="text" 
									id="pal<?php if($m) echo $pal->getNumero();?>_moniteur_prenom" 
									name="pal<?php if($m) echo $pal->getNumero();?>_moniteur_prenom" 
									value="<?php if($palm) echo $pal->getMoniteur()->getPrenom();?>"/>
						</td>
						<td>
							<input 	type="text"
									id="pal<?php if($m) echo $pal->getNumero();?>_moniteur_aptitudes"
									disabled="true"
									value="<?php if($palm) echo Aptitude::toLibelleString($pal->getMoniteur()->getAptitudes()); ?>">
							<input 	type="hidden" 
									id="pal<?php if($m) echo $pal->getNumero();?>_moniteur_id" 
									name="pal<?php if($m) echo $pal->getNumero();?>_moniteur_id" 
									value="<?php if($palm) echo $pal->getMoniteur()->getId();?>"/>
						</td>
						<td>
							<input 	type="text"
									id="pal<?php if($m) echo $pal->getNumero();?>_moniteur_telephone"
									disabled="true"
									value="<?php if($palm) echo $pal->getMoniteur()->getTelephone();?>">										
						</td>
						<td>
							<input 	type="text"
									id="pal<?php if($m) echo $pal->getNumero();?>_moniteur_email"
									disabled="true"
									value="<?php if($palm) echo $pal->getMoniteur()->getEmail();?>">
							
						</td>
					</tr>
				</table>
				
			</div>
			<div class="plongeurs">
				<div class="plongeur_title">Plongeurs</div>
				<table id="pal<?php if($m) echo $pal->getNumero();?>_plongeurs" class="inside-panel">
					<tr>
						<td>Nom</td>
						<td>Prénom</td>
						<td>Aptitudes</td>	
						<td>Téléphone</td>
						<td>Téléphone Urgence</td>
						<td>Date de Naissance</td>
					</tr>
				<?php
					if($m && $pal->getPlongeurs()){
						for($i = 0; $i < count($pal->getPlongeurs()); $i++) {
							printFormFicheSecuritePalanquePlongeur($aptitudes, $pal->getNumero(), $i+1, $pal->getPlongeurs()[$i]);
						}
					}
				?>
				</table>
			</div>
			<table class="palanque_bouttons">
				<tr>
					<td><span onclick="fs_ajouter_plongeur(<?php echo ($m ? $pal->getNumero() : "numero_palanque");?>)" class="button blue">Ajouter Plongeur</span></td>
					<td><span onclick="fs_supprimer_palanque(<?php echo ($m ? $pal->getNumero() : "numero_palanque");?>)" class="button red">Supprimer cette palanquée</span></td>
				</tr>
			</table>
		</div>
	<?php
}

/**
 * Affiche le formulaire de modification d'un plongeur si est passé en parametre ou le formulaire modele
 * de création de plongeur (caché)
 * @param  array 	$aptitudes Le tableau de toute les aptitudes précédement récupéré en base
 * @param  int $npal l'éventuelle numéro de palanquée avec lequel initialiser le formulaire
 * @param  int $nplon l'éventuelle numéro de plongeur avec lequel initialiser le formulaire
 * @param  Plongeur $plon l'éventuelle plongeur avec lequel initialiser le formulaire
 */
function printFormFicheSecuritePalanquePlongeur($aptitudes, $npal, $nplon, Plongeur $plon = null){
	if($plon && $npal && $nplon){
		$m = true;
		$pal_plon = "pal".$npal."_plon".$nplon;
	}
	else{
		$m = false;
		$pal_plon = "pal_plon";
	}
	?>
		<tr>
			<td colspan="5">
				<ul id="<?php echo $pal_plon;?>_erreur" class="erreur pas_erreur">
				</ul>
			</td>
		</tr>
		<tr id="<?php echo ($m ? $pal_plon : "plon_js_clonable");?>" class="plongeur" data-numplongeur="<?php if($m) echo $nplon;?>">
			
			
			<td>
				<input 	type="text" 
						id="<?php echo $pal_plon;?>_plongeur_nom" 
						name="<?php echo $pal_plon;?>_plongeur_nom" 
						value="<?php if($m) echo $plon->getNom();?>"/>
			</td>
			<td>
				<input 	type="text" 
						id="<?php echo $pal_plon;?>_plongeur_prenom" 
						name="<?php echo $pal_plon;?>_plongeur_prenom" 
						value="<?php if($m) echo $plon->getPrenom();?>"/>
			</td>
			<td>
				<select id="<?php echo $pal_plon;?>_plongeur_aptitudes" 
						multiple="multiple" 
						name="<?php echo $pal_plon;?>_plongeur_aptitudes">
					<?php printListeOptionsAptitudes($aptitudes, ($m ? $plon->getAptitudes() : null)); ?>
				</select>
			</td>
			<td>
				<input 	type="text" 
						id="<?php echo $pal_plon;?>_plongeur_telephone" 
						name="<?php echo $pal_plon;?>_plongeur_telephone" 
						value="<?php if($m) echo $plon->getTelephone();?>"/>
			</td>
			<td>
				<input 	type="text" 
						id="<?php echo $pal_plon;?>_plongeur_telephone_urgence" 
						name="<?php echo $pal_plon;?>_plongeur_telephone_urgence" 
						value="<?php if($m) echo $plon->getTelephoneUrgence();?>"/>
			</td>
			<td>
				<input 	type="text" 
						id="<?php echo $pal_plon;?>_plongeur_date_naissance" 
						name="<?php echo $pal_plon;?>_plongeur_date_naissance" 
						placeholder="JJ/MM/AAAA"
						value="<?php if($m) echo $plon->getDateNaissance();?>"/>
				<input 	type="hidden" 
						id="<?php echo $pal_plon;?>_plongeur_id" 
						name="<?php echo $pal_plon;?>_plongeur_id" 
						value="<?php if($m) echo $plon->getId();?>"/>
				<input 	type="hidden" 
						id="<?php echo $pal_plon;?>_plongeur_version" 
						name="<?php echo $pal_plon;?>_plongeur_version" 
						value="<?php if($m) echo $plon->getVersion();?>"/>
			</td>
			<td>
				<span 	onclick="fs_supprimer_plongeur(<?php echo ($m ? $npal.', '.$nplon : 'numero_palanque, numero_plongeur');?>)" 
						class="button red">Retirer</button>
			</td>
		</tr>
	<?php
}
/**
 * Permet d'avoir la rawSource de moniteur pour l'autocomplete
 * @return string
 */
function printRawSourceAutocompleteMoniteur(){
	$arrayMoniteurs = MoniteurDao::getAllActif();
	$result = "";
	if($arrayMoniteurs != null){
		foreach($arrayMoniteurs as $moniteur){
			if(strlen($result) > 0)
				$result .= ",";
			$result .= "{id: ".$moniteur->getId()
						.", nom: '".$moniteur->getNom()
						."', prenom: '".$moniteur->getPrenom()
						."', email: '".$moniteur->getEmail()
						."', telephone: '".$moniteur->getTelephone()
						."', label: '".$moniteur->getNom()." ".$moniteur->getPrenom()." ".Aptitude::toLibelleString($moniteur->getAptitudes())
						."',  aptitudes: ".Aptitude::toJsLibelle($moniteur->getAptitudes())
						."}";
		}
	}
	echo $result;
}
/**
 * Permet d'avoir la rawSource des plongeurs pour l'autocomplete
 * @return string
 */
function printRawSourceAutocompletePlongeur(){
	$arrayPlongeur = PlongeurDao::getLastX($GLOBALS['nombre_plongeur_suggerer']);
	$result = "";
	foreach($arrayPlongeur as $plongeur){
		if(strlen($result) > 0)
			$result .= ",";
		$result .= "{id: ".$plongeur->getId()
					.", nom: '".$plongeur->getNom()
					."', label: '".$plongeur->getNom()." ".$plongeur->getPrenom()." ".$plongeur->getDateNaissance()
					."', prenom: '".$plongeur->getPrenom()
					."', date_naissance: '".$plongeur->getDateNaissance()
					."', telephone: '".$plongeur->getTelephone()
					."', telephone_urgence: '".$plongeur->getTelephoneUrgence()
					."',  aptitudes: ".Aptitude::toJsIdArray($plongeur->getAptitudes())."}";
	}
	echo $result;
}
////////////
// UTILZ //
////////////
/**
 * Affiche les balise HTML option pour les aptitudes de la base. Si un tableau est fourni, les aptitudes dans le tableau sont séléctionné
 * @param  array $aptitudes 		Tout les aptitudes en base 
 * @param  array $aptitudesPossede 	Aptitudes possédé par le plongeur ou le moniteur
 */
function printListeOptionsAptitudes($aptitudes, $aptitudesPossede){
	$result = "<option value=\"\"></option>";
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
 * Prend un paramètre une fiche de sécurité ou null. Si une fiche est passé, affiche l'heure de sortie prévu par la fiche.
 * Si null, n'affiche rien
 * @param  FicheSecurite $f
 */
function printHeure($f){
	if($f != null && $f->getTime() != null){
		$arr = explode(":", $f->getTime());
		if(count($arr) == 2)
			echo $arr[0];
	}
}

/**
 * Prend un paramètre une fiche de sécurité ou null. Si une fiche est passé, affiche les minutes de sortie prévu par la fiche.
 * Si null, n'affiche rien
 * @param  FicheSecurite $f
 */
function printMinute($f){
	if($f != null && $f->getTime() != null){
		$arr = explode(":", $f->getTime());
		if(count($arr) == 2)
			echo $arr[1];
	}
}

/**
 * Affiche les sources pour l'autocomplete du Directeur de plongé, au format 'Prénom Nom'
 */
function printDirecteurPlongeSource(){
	$arrayDirecteurPlonge = MoniteurDao::getAllActifDirecteurPlonge();
	$result = "";
	if($arrayDirecteurPlonge != null){
		foreach($arrayDirecteurPlonge as $directeurPlonge){
			if(strlen($result) > 0)
				$result .= ",";
			$result .= "{value: '".$directeurPlonge->getPrenom()." ".$directeurPlonge->getNom()."', id: ".$directeurPlonge->getId()." }";
		}
	}
	echo $result;
}

/**
 * Affiche les sources pour l'autocomplete du site de plongé
 */
function printSiteSource(){
	$arraySite = SiteDao::getAll();
	$result = "";

	if($arraySite != null){
		foreach($arraySite as $site){

			if(strlen($result) > 0)
				$result .= ",";
			$commentaire = strlen($site->getCommentaire()) > 0 ? " (".$site->getCommentaire().")" : "";
			$result .= "{value: '".$site->getNom()."', id: ".$site->getId().", label: '".$site->getNom().$commentaire."' }";
		}
	}

	echo $result;
}

/**
 * Affiche les options d'embarcation pour le select, avec éventuellement l'embarcation de la fiche de sécurité courante de séléctionnée
 *
 * @param FicheSecurite $ficheSecurite
 */
function printEmbarcationOptions($ficheSecurite){
	$arrayEmbarcation = EmbarcationDao::getAllDisponible();
	$result = "<option value=\"\">&nbsp;</option>";
	$idEmbarcation = $ficheSecurite != null && $ficheSecurite->getEmbarcation() != null ? $ficheSecurite->getEmbarcation()->getId() : null;
	if($arrayEmbarcation != null){
		foreach($arrayEmbarcation as $embarcation){
			$result .= "<option value = \"".$embarcation->getId()."\"";
			if($idEmbarcation == $embarcation->getId())
				$result .= "selected=\"selected\"";
			$result .= ">".$embarcation->getLibelle()."</option>";
		}
	}
	echo $result;
}
?>