<?php
	/**
	 * Fichier contenant des fonctions pour transformer les dates en chaines de caractere
	 * @package Utils
	 */
	/**
	 * Transforme un timestamps en chaine de caractère suivant un format (par défaut "d/m/Y")
	 * et une timezone (par défaut "Europe/Paris") représentant une date
	 * @example 07/10/2014
	 * @param  int $timestamp
	 * @param  string $format Optionnal
	 * @param  string $timezone Optionnal
	 * @return string
	 */
	function tmspToDate($timestamp, $format = "d/m/Y", $timezone="Europe/Paris"){
		$date = new DateTime();
		$date->setTimestamp($timestamp);
		$date->setTimezone(new DateTimeZone($timezone));
		return englishDayToJourFrancais($date->format($format));
	}
	/**
	 * Transforme un timestamps en chaine de caractère suivant le format "l j M G:i"
	 * et une timezone (par défaut "Europe/Paris") représentant une date et heure
	 * Exemple Mardi 7 Oct 19:25
	 * @param  int $timestamp
	 * @param  string $timezone Optionnal
	 * @return string
	 */
	function tmspToDateLong($timestamp, $timezone="Europe/Paris"){
		$date = new DateTime();
		$date->setTimestamp($timestamp);
		$date->setTimezone(new DateTimeZone($timezone));
		return englishDayToJourFrancais($date->format("l j M G:i"));
	}
	
	/**
	 * Transforme un timestamps en chaine de caractère suivant un format (par défaut "H:i")
	 * et une timezone (par défaut "Europe/Paris") représentant une heure
	 * Exemple 19:25
	 * @param  int $timestamp
	 * @param  string $format Optionnal
	 * @param  string $timezone Optionnal
	 * @return string
	 */
	function tmspToTime($timestamp, $format = "H:i", $timezone="Europe/Paris"){
		$date = new DateTime();
		$date->setTimestamp($timestamp);
		$date->setTimezone(new DateTimeZone($timezone));
		return englishDayToJourFrancais($date->format($format));
	}
	
	/**
	 * Transforme les jours et mois anglais en français
	 * @param  string $string
	 * @return string
	 */
	function englishDayToJourFrancais($string){
		$map = array('Monday' 	=> 'LUNDI',
					 'Tuesday' 	=> 'MARDI',
					'Wednesday' => 'MERCREDI',
					'Thirday' 	=> 'JEUDI',
					'Friday' 	=> 'VENDREDI',
					'Saturday' => 'SAMEDI',
					'Sunday' 	=> 'DIMANCHE',
					'January' 	=> 'Janvier',
					'February' 	=> 'Février',
					'March' 	=> 'Mars',
					'April' 	=> 'April',
					'May' 		=> 'Mai',
					'June' 		=> 'Juin',
					'July' 		=> 'Juillet',
					'August' 	=> 'Août',
					'September' => 'Septembre',
					'October' 	=> 'Octobre',
					'November' 	=> 'Novembre',
					'Decembre' 	=> 'Décembre',
					'Jan' 		=> 'Janvier',
					'Feb' 		=> 'Février',
					'Mar' 		=> 'Mars',
					'Apr' 		=> 'April',
					'May' 		=> 'Mai',
					'Jun' 		=> 'Juin',
					'Jul' 		=> 'Juillet',
					'Aug' 		=> 'Août',
					'Sept' 		=> 'Septembre',
					'Oct' 		=> 'Octobre',
					'Nov' 		=> 'Novembre',
					'Dec' 		=> 'Décembre');
		
		return str_replace(array_keys($map), $map, $string);
	}
	
	/**
	 * Convertie un temps en minute en minute'heure''
	 * @param  int $time   Le temps en minute
	 * @param  string $format format de sorti, par défaut '%d\'%d\'\''
	 * @return strong         le temps formaté
	 */
	function convertToMinSec($time, $format = '%d\'%d\'\'') {
		    settype($time, 'integer');
		    if ($time < 1) {
		        return;
	    }
	    $minutes = floor($time / 60);
	    $seconds = ($time % 60);
	    return sprintf($format, $minutes, $seconds);
	}

	/**
	 * Prend un paramêtre un type de plongé (static dans Palanque) et renvoi une chaine de caractère correspondent à ce type de plongé de façon affichable à l'utilisateur
	 * @param  string  $type
	 * @param  boolean $maj Indique si la première lettre doit être en majuscule, par défaut à false
	 * @return string       
	 */
	function typePlongeToString($type, $maj = false){
		$result = "";
		switch($type){
			case Palanque::plongeAutonome:
				$result = "autonome";
				break;
			case Palanque::plongeEncadre:
				$result = "encadrée";
				break;
			case Palanque::plongeTechnique:
				$result = "technique";
				break;
			case Palanque::plongeBapteme:
				$result = "baptême";
				break;
			default:
				break;
		}

		if($maj)
			$result = ucfirst($result);

		return $result;
	}
?>