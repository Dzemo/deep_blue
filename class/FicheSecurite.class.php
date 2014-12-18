<?php
	/**
	 * Ficher contenant la classe FicheSecurite
	 * @author Raphaël Bideau - 3iL
	 * @package Modele
	 */
	
	require_once($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'deep_blue'.DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR."DateStringUtils.php");
	
	/**
	 * Classe représentent une fiche de sécurité.
	 * Dans le cas d'une fiche de sécurité, le directeur de plongé est un utilisateur
	 */
	class FicheSecurite{

		////////////////////////
		// Constantes Debut //
		////////////////////////

		const etatCreer = "CREER";
		const etatModifie = "MODIFIE";
		const etatArchive = "ARCHIVE";
		const etatSynchronise = "SYNCHRONISE";

		///////////////////////
		// Constantes Fin  //
		// Variables Debut //
		///////////////////////
		
		/**
		 * id de la fiche de sécurité.
		 * @var [type]
		 */
    	private $id;
    	
    	/**
    	 * L'embarcation de cette fiche de sécurité.
    	 * @var Embarcation
    	 */
    	private $embarcation;
    	
    	/**
    	 * Le directeur de plongé responsable de cette fiche de sécurité.
    	 * @var Moniteur
    	 */
    	private $directeurPlonge;
    	
    	/**
    	 * Tableau des palanqués de la fiche de sécurité
    	 * @var array
    	 */
    	private $palanques;
    	
    	/**
    	 * Timestamp de la date de sortie.
    	 * @var int
    	 */
    	private $timestamp;
    	
    	/**
    	 * Dénomination du lieu de la sortie.
    	 * @var Site
    	 */
    	private $site;
    	
    	/**
    	 * État de la fiche de sécurité. Valeur possible: CREER, SYNCRONISE, ARCHIVE
    	 * @var string
    	 */
    	private $etat;
    	
    	/**
    	 * Version de la fiche. Utilisé pour la synchronisation. Timestamp de dernière modification
    	 * Initialisé via le constructeur.
    	 * @var int
    	 */
    	private $version;

		//////////////////////
		// Varibables Fin //
		// Constructeur  //
		//////////////////////
    	
    	/**
    	 * Initialise une fiche de sécurité avec un $id et éventuellement une version, le reste à null
    	 * @param int $id
    	 * @param int $version Optionnal
    	 */
		public function FicheSecurite($id, $version = 0){
			$this->id = $id;
			$this->embarcation = null;
			$this->directeurPlonge = null;
			$this->palanques = array();
			$this->timestamp = null;
			$this->site = null;
			$this->etat = null;
			$this->version = $version != 0 ? $version : time();
		}

		///////////////////////////////
		// Getter and Setter Debut //
		///////////////////////////////
		
		/**
		 * @return int
		 */
		public function getId(){
			return $this->id ;
		}		
		/**
		 * @param int $id
		 */
		public function setId($id){
			$this->id = $id;
		}

		/**
		 * @return Embarcation
		 */
		public function getEmbarcation(){
			return $this->embarcation ;
		}		
		/**
		 * @param Embarcation $embarcation
		 */
		public function setEmbarcation(Embarcation $embarcation){
			$this->embarcation = $embarcation ;
		}
		
		/**
		 * @return Moniteur
		 */
		public function getDirecteurPlonge(){
			return $this->directeurPlonge ;
		}		
		/**
		 * @param Moniteur $directeurPlonge
		 */
		public function setDirecteurPlonge(Moniteur $directeurPlonge){
			$this->directeurPlonge = $directeurPlonge ;
		}
		
		/**
		 * @return array
		 */
		public function getPalanques(){
			return $this->palanques;
		}
		/**
		 * @param array $palanques
		 */
		public function setPalanques($palanques){
			$this->palanques = $palanques != null ? $palanques : array();
		}

		/**
		 * @return int
		 */
		public function getTimestamp(){
			return $this->timestamp ;
		}
		/**
		 * @param int $timestamp
		 */
		public function setTimestamp($timestamp){
			$this->timestamp = $timestamp ;
		}
		
		/**
		 * @return Site
		 */
		public function getSite(){
			return $this->site ;
		}
		/**
		 * @param Site $site
		 */
		public function setSite(Site $site=null){
			$this->site = $site ;
		}
		
		/**
		 * @return string
		 */
		public function getEtat(){
			return $this->etat ;
		}
		/**
		 * @param string $etat
		 */
		public function setEtat($etat){
			$this->etat = $etat ;
		}		
		
		/**
		 * @return int
		 */
		public function getVersion(){
			return $this->version ;
		}
		/**
		 * Met à jours la version
		 */
		public function updateVersion(){
			$this->version = time();
		}
		
		/////////////////////////////
		// Getter and Setter Fin //
		// Utils Debut           //
		/////////////////////////////
		/**
		 * @see tmspToDate()
		 * @param  string $format
		 * @param  string $timezone
		 * @return string
		 */
		public function getDate($format = "d/m/Y", $timezone="Europe/Paris"){
			return tmspToDate($this->timestamp, $format, $timezone);
		}
		/**
		 * @see tmspToDateLong()
		 * @param  string $timezone
		 * @return string
		 */
		public function getDateLong($timezone="Europe/Paris"){
			return tmspToDateLong($this->timestamp, $timezone);
		}
		
		/**
		 * @see tmspToTime()
		 * @param  string $format
		 * @param  string $timezone
		 * @return string
		 */
		public function getTime($format = "H:i", $timezone="Europe/Paris"){
			return tmspToTime($this->timestamp, $format, $timezone);
		}
		/**
		 * Renvoi un tableau contenant tout les plongeurs triés par palanqué
		 * @return array 
		 */
		public function getPlongeurs(){
			$arrayResult = array();
			if($this->palanques != null){
				foreach ($this->palanques as $palanque) {
					if($palanque->getPlongeurs() != null  && count($palanque->getPlongeurs()) > 0)
						$arrayResult = array_merge($arrayResult, $palanque->getPlongeurs());
				}
			}
			return $arrayResult;
		}
		/**
		 * Renvoi un nom de fiche qui est la concaténation du Site+Embarcation+Date de la sortie
		 * @return string
		 */
		public function getNom(){
			return trim($this->getDirecteurPlonge()->getId()).$this->getEmbarcation()->getLibelle().$this->getDate();
		}
		/**
		 * Renvoi une string représentant la fiche de sécurité
		 * Exemple: Id: 2 Embarcation: (Id: 2) DirecteurPlonge: (Login: test) Date: 12:00 10/10/2014 Site: Le grand bleu Etat: CREER Version: 0
		 * @return string
		 */
		public function __toString(){
			$string = "Fiche de sécurité: Id: ".$this->id."<br/>";
			$string.= ($this->embarcation != null ? $this->embarcation : "pas d'embarcation");
			$string.= "&nbsp;&nbsp;DirecteurPlongee: ". ($this->directeurPlonge != null ? $this->directeurPlonge : "pas de directeur de plongée" );
			$string.= "&nbsp;&nbsp;NbrPalanques: ".count($this->palanques)."<br/>";
			$string.= "&nbsp;&nbsp;NbrPlongeur: ".count($this->getPlongeurs())."<br>";
			$string.= "&nbsp;&nbsp;Date: ".$this->getDate()."<br/>";
			$string.= "&nbsp;&nbsp;".($this->site != null ? $this->site : "pas de site");
			$string.= "&nbsp;&nbsp;Etat: ".$this->etat." Version: ".$this->version;
			

			return $string;
		}
		/////////////////
		// Utils Fin //
		/////////////////
	}
?>