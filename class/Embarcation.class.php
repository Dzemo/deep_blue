<?php
	/**
	 * Ficher contenant la classe Embarcation
	 * @author Raphaël Bideau - 3iL
	 * @package Modele
	 */
	
	/**
	 * Classe représentant une embarcation, utiliser pour faire des sortis de plongé
	 */
	
	class Embarcation{
		///////////////////////
		// Variables Debut //
		///////////////////////
		
		/**
		 * Id de l'embarcation.
		 * @var int
		 */
		private $id;
		
		/**
		 * Libelle court de l'embarcation
		 * @var string
		 */
		private $libelle;
		
		/**
		 * Maximum de personnes que l'on peut faire embarquer sur cette embarcation
		 * @var int
		 */
		private $maxpersonne;
		
		/**
		 * Commentaire sur l'embarcation, peut être plus long
		 * @var string
		 */
		private $commentaire;
		
		/**
		 * Indique si l'embarcation est disponible ou si elle est supprimé/desactivé/vendu/perdu/cassé/en reparation
		 * @var boolean
		 */
		private $disponible;
		
		/**
		 * Timestamp de dernièr modification cette embarcation, utilisé pour la synchronisation entre l'interface web
		 * et l'application de plongé.
		 * @var int
		 */
		private $version;

		//////////////////////
		// Varibables Fin //
		// Constructeur  //
		//////////////////////
		
		/**
		 * Initialise l'Embarcation avec un $id et éventuellement une $version, le reste à null
		 * @param int $id
		 * @param int $version Optionnal
		 */
		public function Embarcation($id, $version = 0){
			$this->id = $id;
			$this->libelle = null;
			$this->maxpersonne = null;
			$this->commentaire = null;
			$this->disponible = null;
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
		 * @return string
		 */
		public function getLibelle(){
			return $this->libelle ;
		}
		/**
		 * @param string $libelle
		 */
		public function setLibelle($libelle){
			$this->libelle = $libelle ;
		}
		/**
		 * @return int
		 */
		public function getMaxpersonne(){
			return $this->maxpersonne ;
		}
		/**
		 * @param int $maxpersonne
		 */
		public function setMaxpersonne($maxpersonne){
			$this->maxpersonne = $maxpersonne ;
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
		/**
		 * @return boolean
		 */
		public function getDisponible(){
			return $this->disponible ;
		}
		/**
		 * @param boolean $disponible
		 */
		public function setDisponible($disponible){
			$this->disponible = $disponible ;
		}
		/**
		 * @return int
		 */
		public function getVersion(){
			return $this->version;
		}
		/**
		 * Met la version au timestamp courant
		 */
		public function updateVersion(){
			$this->version = time();
		}
		/////////////////////////////
		// Getter and Setter Fin //
		// Utils Debut
		/////////////////////////////
		/**
		 * Renvoie une string représentant l'embarcation
		 * Example: Id: 7 Libelle: 'EMB-3' Commentaire: 'Embarcation-3, creer lors d'un test, indisponible' Disponible: non Version: 0
		 * @return string
		 */
		public function __toString(){
			return "Id: ".$this->id." Libelle: '".$this->libelle."' Contenance Maximum: ".$this->maxpersonne."' Commentaire: '".$this->commentaire."' Disponible: ".
					($this->disponible ? "oui" : "non")." Version: ".$this->version;
		}
		/////////////////
		// Utils Fin //
		/////////////////
	}
?>