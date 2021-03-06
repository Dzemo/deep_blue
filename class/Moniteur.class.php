<?php
	/**
	 * Ficher contenant la classe Moniteur
	 * @author Raphaël Bideau - 3iL
	 * @package Modele
	 */

	/**
	 * Classe Moniteur
	 */
	class Moniteur implements JsonSerializable {
		///////////////////////
		// Variables Debut //
		///////////////////////

		/**
		 * Id du moniteur
		 * @var int
		 */
		private $id;

		/**
		 * Nom du moniteur
		 * @var string
		 */
		private $nom;

		/**
		 * Prénom du moniteur
		 * @var string
		 */
		private $prenom;

		/**
		 * Tableau des aptitudes de ce moniteur
		 * @var array
		 */
		private $aptitudes;

		/**
		 * Indique si ce moniteur peut être directeur de plongé
		 * @var boolean
		 */
		private $directeurPlonge;

		/**
		 * Indique si le moniteur est disponible
		 * @var boolean
		 */
		private $actif;

		/**
		 * Addresse email du moniteur
		 * @var string
		 */
		private $email;

		/**
		 * Numéro de téléphone du moniteur, sous forme de chaine de caractere de longeur maximum indéfini 
		 * (possibilité de mettre plusieurs numéro)
		 * @var string
		 */
		private $telephone;

		/**
		 * Version du moniteur, pour la synchronisation. Timestamp de dernière modification
		 * @var int
		 */
		private $version;

		//////////////////////
		// Varibables Fin //
		// Constructeur  //
		//////////////////////

		/**
		 * Initialise les valeurs à null, avec éventuellement la version spécifié
		 * @param int $id
		 * @param int $version Optionnel
		 */
		public function Moniteur($id, $version = null){
			$this->id = $id ;
			$this->nom = null ;
			$this->prenom = null ;
			$this->aptitudes = array() ;
			$this->directeurPlonge = null;
			$this->actif = null;
			$this->version = $version != null ? $version : time();
			$this->email = null;
			$this->telephone = null;
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
		public function getPrenom(){
			return $this->prenom ;
		}
		/**
		 * @param string $prenom
		 */
		public function setPrenom($prenom){
			$this->prenom = $prenom ;
		}		

		/**
		 * @return array
		 */
		public function getAptitudes(){
			return $this->aptitudes ;
		}
		/**
		 * @param array $aptitudes
		 */
		public function setAptitudes($aptitudes){
			$this->aptitudes = $aptitudes ;
		}		

		/**
		 * @return boolean
		 */
		public function estDirecteurPlonge(){
			return $this->directeurPlonge ;
		}
		/**
		 * @param boolean $directeurPlonge
		 */
		public function setDirecteurPlonge($directeurPlonge){
			$this->directeurPlonge = $directeurPlonge ;
		}	

		/**
		 * @return boolean
		 */
		public function estActif(){
			return $this->actif ;
		}
		/**
		 * @param boolean $actif
		 */
		public function setActif($actif){
			$this->actif = $actif ;
		}

		/**
		 * @return string
		 */
		public function getEmail(){
			return $this->email ;
		}
		/**
		 * @param string $email
		 */
		public function setEmail($email){
			$this->email = $email ;
		}

		/**
		 * @return string
		 */
		public function getTelephone(){
			return $this->telephone ;
		}
		/**
		 * @param string $telephone
		 */
		public function setTelephone($telephone){
			$this->telephone = $telephone ;
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
		// Utils Debut
		/////////////////////////////

		/**
		 * Permet d'ajouter une aptitude à un moniteur
		 * @param  Aptitude $aptitude 
		 */
		public function ajouterAptitude(Aptitude $aptitude){
			$this->aptitudes[] = $aptitude;
		}

		/**
		 * Renvoie une string représentant Template
		 * Exemple Id: '8', Nom prenom: 'Dunam Olivia' Aptitudes: [ PA-40] Version: 0
		 * @return string
		 */
		public function __toString(){
			$stringAptitudes = "aucune";
			if($this->aptitudes != null){
				$stringAptitudes = "[";
				foreach ($this->aptitudes as $aptitude) {
					if(strlen($stringAptitudes) > 1)
						$stringAptitudes = $stringAptitudes." ";

					$stringAptitudes = $stringAptitudes . $aptitude->getLibelleCourt();
				}
				$stringAptitudes = $stringAptitudes."]";
			}

			$string = "&nbsp;&nbsp;Moniteur ".$this->id.": ".$this->nom." ".$this->prenom."<br>";
			$string.= "&nbsp;&nbsp;&nbsp;&nbsp;Aptitudes: ".$stringAptitudes." Actif: ".($this->actif ? 'oui' : 'non')." DirecteurPlonge: ".($this->directeurPlonge ? 'oui' : 'non')."'<br>";
			$string.= "&nbsp;&nbsp;&nbsp;&nbsp;Email: '".$this->email."' Telephone: '".$this->telephone."' Version: ".$this->version ."<br>";

			return $string;
		}

		/**
		 * Serialize cette aptitude en un array acceptable par json_encode
		 * @return array 
		 */
		public function jsonSerialize(){
			$arrayAptitudes = array();
			foreach ($this->aptitudes as $aptitude) {
				$arrayAptitudes[] = $aptitude;
			}

			return [
				'idWeb' => $this->id,
				'nom' => $this->nom,
				'prenom' => $this->prenom,
				'aptitudes' => $arrayAptitudes,
				'actif' => $this->actif ? 'true' : 'false',
				'directeurPlongee' => $this->directeurPlonge ? 'true' : 'false',
				'email' => $this->email,
				'telephone' => $this->telephone,
				'version' => $this->version,
			];
		}

		/////////////////
		// Utils Fin //
		/////////////////
	}
?>