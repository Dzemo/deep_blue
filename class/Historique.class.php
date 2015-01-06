<?php
	/**
	 * Ficher contenant la classe Historique
	 * @author Raphaël Bideau - 3iL
	 * @package Modele
	 */

	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."utils".DIRECTORY_SEPARATOR."DateStringUtils.php");

	/**
	 * Classe Historique représentent une action d'un utilisateur
	 */
	class Historique{
		///////////////////////
		//Constantes Debut //
		///////////////////////
		/**
		 * Valeur pour un historique qui à été effectué sur l'interface web
		 * @var string
		 */
		const sourceWeb = "WEB";
		/**
		 * Valeur pour un historique qui à été effectué sur l'application mobile
		 * @var string
		 */
		const sourceMobile = "MOBILE";
		/**
		 * Valeur pour un historique qui à été éféctué lors d'une opération de synchronisation
		 * @var string
		 */
		const sourceSynchronize = "SYNCHRONIZE";
		
		///////////////////////
		// Constantes Fin  //
		// Variables Debut //
		///////////////////////
		/**
		 * Le login de l'utilisateur concerné.
		 * Initialisé dans le constructeur, pas de setter.
		 * @var string
		 */
		private $login_utilisateur;
		/**
		 * Le timestamps représentant le moment ou l'action est réalisé.
		 * Initialisé dans le constructeur, pas de setter.
		 * @var int
		 */
		private $timestamp;
		/**
		 * L'id de la fiche de sécurité concerné par cette action si il y en a une. Si l'action ne
		 * concerne pas de fiche de sécurité, null alors.
		 * Initialisé dans le constructeur, pas de setter.
		 * @var int
		 */
		private $id_fiche_securite;
		/**
		 * Source de l'action. Valeur possible : WEB ou MOBILE
		 * @var string
		 */
		private $source;
		/**
		 * Commentaire décrivant l'action et les éventuelles changement apporté
		 * @var string
		 */
		private $commentaire;
		//////////////////////
		// Varibables Fin //
		// Constructeur  //
		//////////////////////
		/**
		 * Initialise le login de l'utilisateur et le timestamps, le reste à null
		 */
		/**
		 * Initialise le login de l'utilisateur, le timestamps et éventuellement l'id de la fiche de sécurité 
		 * référencé. Le reste à null.
		 * @param string $login_utilisateur
		 * @param int $timestamp
		 * @param int $id_fiche_securite Optionnal
		 */
		public function Historique($login_utilisateur, $timestamp, $id_fiche_securite = null){
			$this->login_utilisateur = $login_utilisateur;
			$this->timestamp = $timestamp;
			$this->id_fiche_securite = $id_fiche_securite;
			$this->source = null;
			$this->commentaire = null;
		}
		///////////////////////////////
		// Getter and Setter Debut //
		///////////////////////////////
		/**
		 * @return int
		 */
		public function getLoginUtilisateur(){
			return $this->login_utilisateur ;
		}
		/**
		 * @return int
		 */
		public function getTimestamp(){
			return $this->timestamp ;
		}
		/**
		 * @return int
		 */
		public function getIdFicheSecurite(){
			return $this->id_fiche_securite ;
		}
		/**
		 * @return string
		 */
		public function getSource(){
			return $this->source ;
		}
		/**
		 * @param string $source
		 */
		public function setSource($source){
			$this->source = $source ;
		}
		/**
		 * @return string
		 */
		public function getCommentaire(){
			return $this->commentaire ;
		}
		/**
		 * @param string $commentaire
		 */
		public function setCommentaire($commentaire){
			$this->commentaire = $commentaire ;
		}
		
		/////////////////////////////
		// Getter and Setter Fin //
		// Utils Debut
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
		 * Renvoie une string représentant Template
		 * @example TODO
		 * @return string
		 */
		public function __toString(){
			return "Le ".$this->getDateLong()." par ".$this->login_utilisateur." sur ".$this->source." ".
					($this->id_fiche_securite != null ? "concernant la fiche ".$this->id_fiche_securite." " : "").
					": ".$this->commentaire;
		}
		/////////////////
		// Utils Fin //
		/////////////////
	}
?>