/**
 * Tente d'enregister par ajax la fiche de sécurité courante
 * En cas de succès d'enregistrement renvoi vers la visualisation de cette fiche.
 * En cas d'erreur de validation de la fiche, affiche les erreurs
 */
function fs_enregistrer(){
	//Suppression des erreurs qui aurai pu être causé lors d'une précédente tentative d'enregistrement
	$('.erreur').addClass('pas_erreur').html('');
	
	//Reset et affichage du modal d'envoi
	$('#modal_enregistrer #resultat_succes').hide();
	$('#modal_enregistrer #resultat_erreur').hide();
	$('#modal_enregistrer #lancement').show();
	$('#modal_enregistrer').bPopup({transitionClose: 'fadeIn'});
	
	//Récupération des valeurs de la fiche de sécurité
	var fiche_securite_id_val = $('#fiche_securite_id').val();
	var embarcation_id_val = $('#id_embarcation').val();
	var date_val = $('#date').val();
	var heure_val = $('#heure').val();
	var minute_val = $('#minute').val();
	var id_site_val = $('#id_site').val();
	var nom_site_val = $('#nom_site').val();
	var directeur_plonge_id_val = $('#id_directeur_plonge').val();
	var fiche_securite_version_val = $('#fiche_securite_version').val();

	//Récupération des palanqués
	var tableauNumeroPalanque = [];
	$('#form_fiche_palanque').children().each(function(){
			tableauNumeroPalanque.push($(this).data('numpalanque'));
	});

	var palanques = [];
	for(var i = 0; i < tableauNumeroPalanque.length ; i++){
		var numero_palanque = tableauNumeroPalanque[i];
		
		var plongeurs_array = [];
		//Récupération des plongeurs de la palanqué 
		var tableauNumeroPlongeurs = [];
		$('#pal'+numero_palanque+'_plongeurs .plongeur').each(function(){
				tableauNumeroPlongeurs.push($(this).data('numplongeur'));
		});
		for(var j = 0; j < tableauNumeroPlongeurs.length ; j++){
			var numero_plongeur = tableauNumeroPlongeurs[j];
			// Récupère toutes les variables du formulaires et les ajoute dans une variable globale AJAX
			var plongeur = {
				id: $('#pal'+numero_palanque+'_plon'+numero_plongeur+'_plongeur_id').val(),
				numero: numero_plongeur,
				version: $('#pal'+numero_palanque+'_plon'+numero_plongeur+'_plongeur_version').val(),
				nom: $('#pal'+numero_palanque+'_plon'+numero_plongeur+'_plongeur_nom').val(),
				prenom: $('#pal'+numero_palanque+'_plon'+numero_plongeur+'_plongeur_prenom').val(),
				date_naissance: $('#pal'+numero_palanque+'_plon'+numero_plongeur+'_plongeur_date_naissance').val(),
				aptitudes: $('#pal'+numero_palanque+'_plon'+numero_plongeur+'_plongeur_aptitudes').val(),
				telephone: $('#pal'+numero_palanque+'_plon'+numero_plongeur+'_plongeur_telephone').val(),
				telephone_urgence: $('#pal'+numero_palanque+'_plon'+numero_plongeur+'_plongeur_telephone_urgence').val()
			}
			plongeurs_array.push(plongeur);
		}
		var palanque = {
			id: $('#pal'+numero_palanque+'_palanque_id').val(),
			numero: numero_palanque,
			version: $('#pal'+numero_palanque+'_palanque_version').val(),
			gaz: $("#pal"+numero_palanque+"_type_gaz").val(),
			plonge: $("#pal"+numero_palanque+"_type_plonge").val(),
			profondeur_prevue: $("#pal"+numero_palanque+"_profondeur_prevue").val(),
			duree_prevue: $("#pal"+numero_palanque+"_duree_prevue").val(),
			moniteur: {
				id: $('#pal'+numero_palanque+'_moniteur_id').val(),
				//Pas besoin des autres informations du moniteurs car elle seront récupérés en base à partir de l'id
			},
			plongeurs: plongeurs_array,
		}
		palanques.push(palanque);
	}
	//Fin de la récupération des palanquées	

	//Rassemblement des données à envoyées
	var data_vals = {
		fiche_securite_id:fiche_securite_id_val, 
		fiche_securite_version: fiche_securite_version_val, 
		date:date_val, 
		heure:heure_val, 
		minute:minute_val, 
		id_site:id_site_val, 
		nom_site:nom_site_val, 
		directeur_plonge_id:directeur_plonge_id_val, 
		embarcation_id:embarcation_id_val, 
		liste_palanques: palanques
	};

	//Envoi de la requete ajax
	$.ajax({type:"POST",
			url:"traitement/enregister_fiche_traitement.php", 
			data: data_vals
	}).done(function(result){
		try{
			var json = jQuery.parseJSON(result);
		}catch(err){
			$('#modal_enregistrer #lancement').hide();
			$('#modal_enregistrer #resultat_erreur').html('Une erreur c\'est produite lors de la récupération des données du serveur, <a href="index.php">Retour à l\'acceuil</a>').show();
		}
		
		//Pour la gestion des erreurs:
		//type: val_abs|gestion|non_connecte
		//numero: 0:fiche_securite|1-X:palanque numero X
		//subnumero: 1-X:plongeur numeor X (null pour fiche securite)
		//msg: message de l'erreur
		if(json.hasOwnProperty('erreurs')){
			console.log(json.erreurs);

			for(i = 0; i < json.erreurs.length; i++){
				erreur = json.erreurs[i];
				selecteurErreur = "";
				if(erreur.numero == 0){
					selecteurErreur = '#fiche_erreur';
					console.log('error fihe');
				}
				else{
					if(erreur.hasOwnProperty('subnumero')){
						if(erreur.subnumero == 0)
							selecteurErreur = '#pal'+erreur.numero+'_moniteur_erreur';
						else
							selecteurErreur = '#pal'+erreur.numero+'_plon'+erreur.subnumero+'_erreur';
					}
					else
						selecteurErreur = '#pal'+erreur.numero+'_erreur';
				}

				console.log('selecteurErreur='+selecteurErreur);
				$(selecteurErreur).removeClass('pas_erreur').append('<li>'+erreur.msg+'</li>');
			}
			$('#modal_enregistrer #lancement').hide();
			$('#modal_enregistrer #resultat_erreur').html('Des erreurs sont présentes').show();
			setTimeout(function(){
				$('#modal_enregistrer').bPopup().close();
			},750);
		}
		else if(json.hasOwnProperty('succes')){
			$('#modal_enregistrer #lancement').hide();
			$('#modal_enregistrer #resultat_succes').html(json.succes.msg).show();
			var interval = setInterval(function(){
				$('#modal_enregistrer #resultat_succes').append('.');
			},500);
			document.location.href=json.succes.redirect;
		}
	}).error(function(){		
		$('#modal_enregistrer #lancement').hide();
		$('#modal_enregistrer #resultat_erreur').html(
			'Une erreur c\'est produite lors de l\'envoi des données au serveur, <a href="index.php">Retour à l\'acceuil</a>').show();
	});
}
/**
 * Annule l'édition ou la création d'une feuille de sécurité. Si il y a eu des 
 * m, affiche une popup d'alerte
 */
function fs_annule(adresse){
	if(!formulaire_modifie ||confirm("Des modifications ont été apporté, voulez vous quitter sans sauvegarder ?")){
		document.location.href=adresse;
	}
}
/**
 * Supprime la palanqué
 * @param  {int} numero_palanque palanqué à supprimer
 */
function fs_supprimer_palanque(numero_palanque){
	if(confirm("Supprimer la palanqué "+numero_palanque+" ?")){
		$('#pal'+numero_palanque).remove();
		$('#li_pal'+numero_palanque).remove();


		$( "#form_fiche_palanque" ).tabs( "refresh" ); 

	}
}
/**
 * Supprime le plongeur de la palanqué
 * @param  {int} numero_palanque palanqué à laquel supprimer un plongeur
 * @param  {int} numero_plongeur plongeur à supprimer
 */
function fs_supprimer_plongeur(numero_palanque, numero_plongeur){
	$('#pal'+numero_palanque+'_plon'+numero_plongeur).remove();
}
/**
 * Permet de ne pas afficher le moniteur pour les plongé autonome
 * @param  {event} event 
 */
function fs_toggle_form_moniteur(event){
	var numero_palanque = getNumeroPalanqueFromString($(event.target).attr('id'));
	if($(event.target).val() == "AUTONOME"){
		$('#pal'+numero_palanque+'_moniteur').hide();
	}
	else{
		$('#pal'+numero_palanque+'_moniteur').show();
	}
}
/**
 * Permet de savoir si le formulaire a été modifier, pour affiché 
 * éventuellemnt une alerte
 * @type {Boolean}
 */
var formulaire_modifie = false;
function fs_modification_form(){
	formulaire_modifie = true;
}
/**
 * Permet de mettre à jours les valeurs dans un select utilisant SumoSelect
 * @param {string} id     Id du select à modifier
 * @param {array} values tableau des valeurs du select (value des options)
 */
function setSelectValue(id, values) {
    //Reset du select
    var selectOptions = $("#"+id+" option");
   	selectOptions.prop('selected', false);    
    //Selection dans le select
    selectOptions.filter(function () {
        return values.indexOf($(this).val()) != -1;
    }).prop('selected', true);
    
    //Reset et selection dans le sumo
    var selectSumo =  $('#'+id)[0].sumo;
    for(var j = 0; j < selectOptions.length; j++){
			if(values.indexOf(""+(j+1)) != -1){
	        selectSumo.selectItem(j);
		}
		else{
			selectSumo.unSelectItem(j);
		}
    }
}	
/**
 * Parse le numéro de palanque dans un id de la forme palX_blablabla
 * @param  {string} string 
 * @return {int}        le numéro de la palanqué
 */
function getNumeroPalanqueFromString(string){
	var tableau = string.split('_');
	if(tableau.length > 0){
		return parseInt(tableau[0].substr(3));
	}
	else{
		return "-";
	}
}