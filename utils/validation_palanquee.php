<?php
/**
 * Fichier contenant des fonctions nécessaire à la validation des fiches de sécurité par rapport au code du sport
 * @package Utils
 */

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."classloader.php");
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."utils".DIRECTORY_SEPARATOR."DateStringUtils.php");


/**
 * Vérifie si une palanqué est valide par rapport aux règles du code du sport :
 * - vérifie que le type de plongé est bien possible pour la profondeur et le gaz
 * - vérifie la présence du moniteur et le nombre de plongeur en fonction du type de plongé
 * - vérifie que les plongeurs ont bien les aptitudes requises en fonction du type de plongé, de la profondeur et du gaz
 * - vérifie que le moniteur à bien les aptitudes pour enseigner/encadrer la palanquee en fonction du type de plongé, de la profondeur et du gaz
 * 
 * @param  Palanque $palanque La palanquee à vérifier
 * @return array              Tableau contenant les éventuelle erreurs, tableau vide pour une palanquee valide
 */
function validePalanquee(Palanque $palanque){
	$erreurs = array();

	////////////////////////////////////////////////////////////////////////////////
	//Vérification que la palanqué est valide par rapport aux règle de gestion //
	////////////////////////////////////////////////////////////////////////////////
	
	//Plan de vérification :
	//Vérification du type de plongé :
	//	ce type de plongé est bien possible à cette profondeur et pour ce gaz (0-6 pas d'encadré au nitrox ni d'autonome, baptême que 0-6)
	//	présence d'un moniteur
	//	nombre de plongeur (avec l'eventuelle ajout en fonction de la profondeur)
	//	
	//Vérification du type de plongé et du gazen fonction de la profondeur
	//	les plongeurs peuvent bien plongé à cette profondeur avec ce type de plongé
	//	si il y a un moniteur, il peut bien encadré ce type de plongé à cette profondeur avec ce gaz
	//	les plongeurs peuvent bien plongé à cette profondeur avec ce gaz
	
	
	//Vérification du type de plongé :
	//	ce type de plongé est bien possible à cette profondeur et pour ce gaz (0-6 pas d'encadré au nitrox ni d'autonome, baptême que 0-6)
	if($palanque->getProfondeurPrevue() <= 6){
		//0-6 pas d'encadré au nitrox ni d'autonome
		if($palanque->getTypePlonge() == Palanque::plongeAutonome){
			$erreurs[] = ['numero' => $palanque->getNumero(), 'type' => 'gestion', 'msg' => 'Impossible de plonger en <strong>autonomie</strong> entre 0 et 6 mètres de profondeur'];
		}
		else if($palanque->getTypePlonge() == Palanque::plongeEncadre && $palanque->getTypeGaz() == Palanque::gazNitrox){
			$erreurs[] = ['numero' => $palanque->getNumero(), 'type' => 'gestion', 'msg' => 'Impossible de plonger en <strong>exploration encadrée</strong> avec du <strong>nitrox</strong> entre 0 et 6 mètres de profondeur'];
		}
	}
	else if($palanque->getTypePlonge() == Palanque::plongeBapteme){
		$erreurs[] = ['numero' => $palanque->getNumero(), 'type' => 'gestion', 'msg' => 'Impossible de faire un <strong>baptême</strong> à <strong>'.$palanque->getProfondeurPrevue().' mètres</strong> de profondeur, maximum 6 mètres'];
	}
	

	//	présence d'un moniteur
	if($palanque->getTypePlonge() != Palanque::plongeAutonome){
		if($palanque->getMoniteur() == null){
			$erreurs[] = ['numero' => $palanque->getNumero(), 'type' => 'gestion', 'msg' => 'Il est necessaire d\'avoir un <strong>moniteur</strong> pour une plongée '.typePlongeToString($palanque->getTypePlonge())];
		}
	}
	

	//	nombre de plongeurs (avec l'eventuelle ajout en fonction de la profondeur)
	if($palanque->getTypePlonge() == Palanque::plongeBapteme){
		$max_plongeur_non_bonus = 1;
		$max_plongeur_bonus = 2;
		$plongeur_bonus = 0;
		if(count($palanque->getPlongeurs()) > $max_plongeur_non_bonus){
			//Si il y a plus de 1 plongeurs, il faut vérifié qu'il n'y a qu'un plongeur de plus
			if(count($palanque->getPlongeurs()) <= $max_plongeur_bonus){
				// Nous savons qu'il y a donc 2 plongeur, le deuxième plongeur doit avoir les bonnes aptitudes
				foreach($palanque->getPlongeurs() as $plongeur){
					if(peutEtrePlongeurBonus($plongeur, $palanque->getTypeGaz(),$palanque->getProfondeurPrevue()))
						$plongeur_bonus++;
				}
				if($plongeur_bonus==0)
					$erreurs[] = ['numero' => $palanque->getNumero(), 'type' => 'gestion', 'msg' => 'Le plongeur supplémentaire n\'a pas les bonnes aptitudes pour une plongée '.typePlongeToString($palanque->getTypePlonge()).' et à une profondeur prévue de '. $palanque->getProfondeurPrevue().' mètres'];
			}
			else {
					$erreurs[] = ['numero' => $palanque->getNumero(), 'type' => 'gestion', 'msg' => 'Il y a trop de plongeur pour une plongée '.typePlongeToString($palanque->getTypePlonge()).' et à une profondeur prévue de '. $palanque->getProfondeurPrevue().' mètres'];
			}
		}
	}
	else if($palanque->getTypePlonge() == Palanque::plongeTechnique || $palanque->getTypePlonge() == Palanque::plongeEncadre){
		$max_plongeur_non_bonus = 4;
		$max_plongeur_bonus = 5;
		$plongeur_bonus = 0;
		if(count($palanque->getPlongeurs()) > $max_plongeur_non_bonus){
			//Si il y a plus de 4 plongeurs, il faut vérifié qu'il n'y a qu'un plongeur de plus
			if(count($palanque->getPlongeurs()) <= $max_plongeur_bonus){
				// Nous savons qu'il y a donc 5 plongeur, le 5eme plongeur doit avoir les bonnes aptitudes
				foreach($palanque->getPlongeurs() as $plongeur){
					if(peutEtrePlongeurBonus($plongeur, $palanque->getTypeGaz(),$palanque->getProfondeurPrevue()))
						$plongeur_bonus++;
				}
				if($plongeur_bonus==0)
					$erreurs[] = ['numero' => $palanque->getNumero(), 'type' => 'gestion', 'msg' => 'Le plongeur supplémentaire n\'a pas les bonnes aptitudes pour une plongée '.typePlongeToString($palanque->getTypePlonge()).' et à une profondeur prévue de '. $palanque->getProfondeurPrevue().' mètres'];
			}
			else {
					$erreurs[] = ['numero' => $palanque->getNumero(), 'type' => 'gestion', 'msg' => 'Il y a trop de plongeur pour une plongée '.typePlongeToString($palanque->getTypePlonge()).' et à une profondeur prévue de '. $palanque->getProfondeurPrevue().' mètres'];
			}
		}
	}
	else{
		//Plongé autonome, minimum 3 plongeurs
		if(count($palanque->getPlongeurs()) > 3){
			$erreurs[] = ['numero' => $palanque->getNumero(), 'type' => 'gestion', 'msg' => 'Il faut au maximum <stong>3</strong> plongeurs pour une plongée '.typePlongeToString($palanque->getTypePlonge())];
		}
	}
	
	
	//Vérification du type de plongé en fonction de la profondeur
	//les plongeurs peuvent bien plongé à cette profondeur avec ce type de plongé
	$plongeur_index = 0;
	foreach ($palanque->getPlongeurs() as $plongeur) {
		if(!peutPlongerPlongeurProfondeurPlonge($plongeur, $palanque->getProfondeurPrevue(), $palanque->getTypePlonge())){
			$msg = $plongeur->getPrenom()." ".$plongeur->getNom()." ne peut pas faire une plongée <strong>".typePlongeToString($palanque->getTypePlonge())."</strong> à une profondeur de <strong>".$palanque->getProfondeurPrevue()." mètres</strong> avec ces <strong>aptitudes</strong>";
			$erreurs[] = ['numero' => $palanque->getNumero(), 'subnumero' => $plongeur_index, 'type' => 'gestion', 'msg' => $msg];
		}
		$plongeur_index++;
	}

	//si il y a un moniteur, il peut bien encadré ce type de plongé à cette profondeur avec ce gaz
	if($palanque->getMoniteur() != null){
		if($palanque->getMoniteur()->getAptitudes() == null){
			$erreurs[] = ['numero' => $palanque->getNumero(), 'subnumero' => 0, 'type' => 'gestion', 'msg' => $palanque->getMoniteur()->getPrenom().' '.$palanque->getMoniteur()->getNom().' n\'a pas les aptitudes nécéssaires pour encadrer cette palanquée'];
		}
		else{
			//Si le gaz n'est pas nitrox, alors true donc pas besoin de vérifier
			$enseignement_nitrox_ok = $palanque->getTypeGaz() != Palanque::gazNitrox || $palanque->getTypePlonge() != Palanque::plongeTechnique;
			//Meme chose pour air
			$enseignement_air_ok = $palanque->getTypeGaz() != Palanque::gazAir || $palanque->getTypePlonge() != Palanque::plongeTechnique;
			
			//Si l'encadrement n'est pas plongeEncadre, alor à true (donc pas besoin de vérifié)
			$encadrement_ok = $palanque->getTypePlonge() != Palanque::plongeEncadre;
			
			//true si le type de gaz est pas nitrox
			$gaz_ok = $palanque->getTypeGaz() != Palanque::gazNitrox;
			
			//On admet que le moniteur peut plongé au profondeur ou il peut enseigner
			$profondeur = ceil($palanque->getProfondeurPrevue());
			
			foreach ($palanque->getMoniteur()->getAptitudes() as $aptitude) {

				if(!$enseignement_nitrox_ok && $aptitude->getEnseignementNitroxMax() >= $profondeur)
					$enseignement_nitrox_ok = true;

				if(!$enseignement_air_ok && $aptitude->getEnseignementAirMax() >= $profondeur)
					$enseignement_air_ok = true;

				if(!$encadrement_ok && $aptitude->getEncadrementMax() >= $profondeur)
					$encadrement_ok = true;

				if(!$gaz_ok && $aptitude->getNitroxMax() >= $profondeur)
					$gaz_ok = true;						
			}					

			//Si tout n'est pas ok on construit le message
			if(!$enseignement_nitrox_ok || !$enseignement_air_ok || !$encadrement_ok || !$gaz_ok){
				if(!$enseignement_nitrox_ok)
					$raison = "ne peut pas enseigner à une palanquée utilisant du nitrox à cette profondeur";
				else if(!$enseignement_air_ok)
					$raison = "ne peut pas enseigner à une palanquée à cette profondeur";
				else if(!$encadrement_ok){
					if(!$gaz_ok)
						$raison = "ne peut pas encadrer une palanquée utilisant du nitrox à cette profondeur";
					else
						$raison = "ne peut pas encadrer une palanquée à cette profondeur";
				}
				else
					$raison = "ne peut pas encadrer une palanquée utilisant du nitrox";
				$erreurs[] = ['numero' => $palanque->getNumero(), 'subnumero' =>0, 'type' => 'gestion', 'msg' => $palanque->getMoniteur()->getPrenom().' '.$palanque->getMoniteur()->getNom().' '.$raison];
			}
		}
	}
	
	//	les plongeurs peuvent bien plongé à cette profondeur avec ce gaz
	if($palanque->getTypeGaz() == Palanque::gazNitrox){
		$plongeur_index = 0;
		foreach ($palanque->getPlongeurs() as $plongeur) {
			if(!peutPlongerPlongeurProfondeurNitrox($plongeur, $palanque->getProfondeurPrevue())){
				$erreurs[] = ['numero' => $palanque->getNumero(), 'subnumero' => $plongeur_index, 'type' => 'gestion', 'msg' => $plongeur->getPrenom()." ".$plongeur->getNom()." ne peut pas plonger avec du <strong>nitrox</strong> à une profondeur de <strong>".$palanque->getProfondeurPrevue()." mètres</strong>"];
			}
			$plongeur_index++;
		}
	}

	return $erreurs;
}





/**
 * Vérifie si ce plongeur peut s'ajouter à cette palanqué en temps que plongeur supplémentaire
 * pour cette profondeur et ce type de gaz
 * @param  Plongeur $plongeur    
 * @param  float $profondeur  
 * @param  string $gaz
 * @return boolean true si le plongeur peut s'ajouter, false sinon
 */
function peutAjouterPlongeurProfondeurGaz($plongeur, $profondeur, $gaz){
	$profondeur_ok = false;
	$gaz_ok = $gaz != Palanque::gazNitrox;
	$profondeur = ceil($profondeur);
	if($plongeur->getAptitudes() == null)
		return false;
	else{
		foreach($plongeur->getAptitudes() as $aptitude){
			if($aptitude->getAjoutMax() >= $profondeur){
				$profondeur_ok = true;
				if($gaz_ok)
					return true;
			}
			if($aptitude->getNitroxMax() >= 20){
				//On considère qu'il s'agit d'un PN-C
				$gaz_ok = true;
				if($profondeur_ok)
					return true;
			}
		}
		return false;
	}
}
/**
 * Vérifie si ce plongeur peut plongé à cette profondeur pour ce type de plongé
 * @param  Plongeur $plongeur   
 * @param  float $profondeur 
 * @param  string $plonge     
 * @return boolean             true si le plongeur peut plonger, false sinon
 */
function peutPlongerPlongeurProfondeurPlonge($plongeur, $profondeur, $plonge){
	$profondeur = ceil($profondeur);

	if($profondeur <= 6)
		return true;
	if($plongeur->getAptitudes() == null && $profondeur > 6)
		return false;
	else{
		foreach($plongeur->getAptitudes() as $aptitude){
			switch($plonge){
				case Palanque::plongeAutonome:
					if($aptitude->getAutonomeMax() >= $profondeur) return true;
					break;
				case Palanque::plongeEncadre:
					if($aptitude->getEncadreeMax() >= $profondeur) return true;
					break;
				case Palanque::plongeTechnique:
					if($aptitude->getTechniqueMax() >= $profondeur) return true;
					break;
				case Palanque::plongeBapteme:
					return true;
					break;
				default:
					break;
			}
		}
		return false;
	}
}
/**
 * Vérifie si ce plongeur peut plongé à cette profondeur avec du nitrox
 * @param  Plongeur $plongeur   
 * @param  float $profondeur 
 * @return boolean             true si le plongeur peut plonger, false sinon
 */
function peutPlongerPlongeurProfondeurNitrox($plongeur, $profondeur){
	$profondeur = ceil($profondeur);
	if($profondeur <= 6)
		return true;
	if($plongeur->getAptitudes() == null && $profondeur > 6)
		return false;
	else{
		foreach($plongeur->getAptitudes() as $aptitude){
			if($aptitude->getNitroxMax() >= $profondeur)
				return true;
		}
		return false;
	}
}

/**
 * Vérifie si ce plongeur peut s'ajouter à cette palanqué en temps que plongeur supplémentaire
 * pour cette profondeur et ce type de gaz
 * @param  Plongeur $plongeur    
 * @param  float $profondeur  
 * @param  string $gaz
 * @return boolean true si le plongeur peut s'ajouter, false sinon
 */
function peutEtrePlongeurBonus($plongeur,$gaz,$profondeur){
	$aptitude_ok = false;
	$gaz_ok = false;

	// Vérifie que la plongée est bien à moins de 40m
	if($profondeur > 40)
		return false;
	else{
		// Vérifier qu'il est GP ou E4
		if($plongeur->getAptitudes() == null)
			return false;
		else{
			foreach($plongeur->getAptitudes() as $aptitude){
				if($aptitude->getLibelleCourt() == "GP" || $aptitude->getLibelleCourt() == "P-4"){
					$aptitude_ok = true;
				}
				if($gaz == Palanque::gazNitrox && $aptitude->getLibelleCourt() == "PN-C"){
					$gaz_ok = true;
				}
				else {
					$gaz_ok = true;
				}
			}
			if($gaz_ok && $aptitude_ok)
				return true;
			else
				return false;
		}
	}
}
?>