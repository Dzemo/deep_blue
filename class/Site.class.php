<?php
	/**
	 * Ficher contenant la classe Site, représentent un site de plongé
	 * @author Raphaël Bideau - 3iL
	 * @package Modele
	 */
	
	/**
	 * Classe Site
	 * Représentent un site de plongé disponible ou un site utilisé dans une fiche
	 */
	class Site{

		///////////////////////
		// Variables Debut //
		///////////////////////
		/**
		 * Id de l'aide, utile pour faire des liens
		 * @var int
		 */
		private $id;

		/**
		 * Nom du site
		 * @var string
		 */
		private $nom;

		/**
		 * Commentaire sur le site
		 * @var string
		 */
		private $commentaire;

		//////////////////////
		// Varibables Fin //
		// Constructeur   //
		//////////////////////
		
		/**
		 * Initialise le nom et le commentaire avec une chaine vide
		 * @param int $id optionnel
		 */
		public function Site($id=null){
			$this->id = $id;
			$this->nom = "";
			$this->commentaire = "";
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
			$this->id = $id ;
		}

		/**
		 * @return string
		 */
		public function getNom(){
			return $this->nom ;
		}
		/**
		 * @param string $nom
		 */
		public function setNom($nom){
			$this->nom = $nom ;
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
		 * Renvoie une string représentant ce site
		 * Exemple
		 * @return string
		 */
		public function __toString(){
			return "Id: ".$this->id." Nom: ".$this->nom." Commentaire: ".$this->commentaire;
		}
		/////////////////
		// Utils Fin //
		/////////////////
	}
?>