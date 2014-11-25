<?php
	/**
	 * Ficher contenant la classe Palanque
	 * @author Raphaël Bideau - 3iL
	 * @package Modele
	 */
	/**
	 * Classe palanquee
	 */
	class Palanque{
		////////////////////////
		// Constantes Debut //
		////////////////////////
		/**
		 * Constante d'une palanqué utilisant de l'air comme gaz
		 */
		const gazAir = "AIR";
		/**
		 * Constante d'une palanqué utilisant du nitrox comme gaz
		 */
		const gazNitrox = "NITROX";
		/**
		 * Constante d'une palanque effectuant une plongé technique / d'enseignement
		 */
		const plongeTechnique = "TECHNIQUE";
		/**
		 * Constante d'une palanque effectuant une plongé encadré
		 */
		const plongeEncadre = "ENCADRE";
		/**
		 * Constante d'une palanque effectuant une plongé autonome
		 */
		const plongeAutonome = "AUTONOME";
		/**
		 * Constante d'une plongé effectuant un baptême
		 */
		const plongeBapteme = "BAPTEME";
		///////////////////////
		// Constantes Fun  //
		// Variables Debut //
		///////////////////////
		/**
		 * Id de la palanqué
		 * @var int
		 */
		private $id;
		/**
		 * Id de la fiche de sécurité à laquel se réfère cette palanqué
		 * @var int
		 */
		private $id_fiche_securite;
		/**
		 * Moniteur de la palanqué si il y en a un. Dans le cadre d'une sortie en autonomie, sera null	
		 * @var Moniteur
		 */
		private $moniteur;
		/**
		 * Numéro de la palanqué
		 * @var int
		 */
		private $numero;
		/**
		 * Type de gaz utilisé par la palanqué
		 * @see Palanque::gazNitrox
		 * @see Palanque::gazAir
		 * @var string
		 */
		private $type_gaz;
		/**
		 * Type de plongé effectué par la palanqué
		 * @see Palanque::plongeTechnique
		 * @see Palanque::plongeEncadre
		 * @see Palanque::plongeAutonome
		 * @var string
		 */
		private $type_plonge;
		/**
		 * Profondeur que prévoit de réalisé la palanqué (en mètre)
		 * @var float
		 */
		private $profondeur_prevue;
		/**
		 * Profondeur qu'a réalisé la palanqué (en mètre)
		 * @var float
		 */
		private $profondeur_realisee;
		/**
		 * Durée de plongé prévu par la palanqué (en seconde)
		 * @var int
		 */
		private $duree_prevue;
		/**
		 * Durée de plongé réalisé par la palanqué (en seconde)
		 * @var int
		 */
		private $duree_realisee;
		/**
		 * Tableaux contenant les plongeurs appartenenant à cette palanqué
		 * @var array
		 */
		private $plongeurs;
		/**
		 * Version de la palanqué, pour la synchronisation
		 * @var int
		 */
		private $version;
		//////////////////////
		// Varibables Fin //
		// Constructeur   //
		//////////////////////
		/**
		 * Initialise les valeurs à null, avec éventuellement la version spécifié
		 * @param int $id
		 * @param int $version Optionnel
		 */
		public function Palanque($id, $version = 0){
			$this->id = $id;
			$this->id_fiche_securite = null;
			$this->moniteur = null;
			$this->numero = null;
			$this->type_plonge = null;
			$this->type_gaz = null;
			$this->duree_prevue = null;
			$this->duree_realisee = null;
			$this->profondeur_prevue = null;
			$this->profondeur_realisee = null;
			$this->plongeurs = array();
			$this->version = $version;
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
		 * @return int
		 */
		public function getIdFicheSecurite(){
			return $this->id_fiche_securite ;
		}
		/**
		 * @param int $id_fiche_securite
		 */
		public function setIdFicheSecurite($id_fiche_securite){
			$this->id_fiche_securite = $id_fiche_securite ;
		}
		/**
		 * @return Moniteur
		 */
		public function getMoniteur(){
			return $this->moniteur ;
		}
		/**
		 * @param Moniteur $moniteur
		 */
		public function setMoniteur($moniteur){
			$this->moniteur = $moniteur ;
		}
		/**
		 * @return int
		 */
		public function getNumero(){
			return $this->numero ;
		}
		/**
		 * @param int $numero
		 */
		public function setNumero($numero){
			$this->numero = $numero ;
		}
		/**
		 * @return string
		 */
		public function getTypeGaz(){
			return $this->type_gaz ;
		}
		/**
		 * @param string $type_gaz
		 */
		public function setTypeGaz($type_gaz){
			$this->type_gaz = $type_gaz ;
		}
		/**
		 * @return string
		 */
		public function getTypePlonge(){
			return $this->type_plonge ;
		}
		/**
		 * @param string $type_plonge
		 */
		public function setTypePlonge($type_plonge){
			$this->type_plonge = $type_plonge ;
		}
		/**
		 * @return float
		 */
		public function getProfondeurPrevue(){
			return $this->profondeur_prevue ;
		}
		/**
		 * @param float $profondeur_prevue
		 */
		public function setProfondeurPrevue($profondeur_prevue){
			$this->profondeur_prevue = $profondeur_prevue ;
		}
		/**
		 * @return float
		 */
		public function getProfondeurRealisee(){
			return $this->profondeur_realisee ;
		}
		/**
		 * @param float $profondeur_realisee
		 */
		public function setProfondeurRealisee($profondeur_realisee){
			$this->profondeur_realisee = $profondeur_realisee ;
		}
		/**
		 * @return int
		 */
		public function getDureePrevue(){
			return $this->duree_prevue ;
		}
		/**
		 * @param int $duree_prevue
		 */
		public function setDureePrevue($duree_prevue){
			$this->duree_prevue = $duree_prevue ;
		}
		/**
		 * @return int
		 */
		public function getDureeRealisee(){
			return $this->duree_realisee ;
		}
		/**
		 * @param int $duree_realisee
		 */
		public function setDureeRealisee($duree_realisee){
			$this->duree_realisee = $duree_realisee ;
		}
		/**
		 * @return array
		 */
		public function getPlongeurs(){
			return $this->plongeurs ;
		}
		/**
		 * @param array $plongeurs
		 */
		public function setPlongeurs($plongeurs){
			$this->plongeurs = $plongeurs ;
		}
		/**
		 * @return int
		 */
		public function getVersion(){
			return $this->version ;
		}
		/**
		 * Augmente la version de 1
		 */
		public function incrementeVersion(){
			$this->version++;
		}
		
		/////////////////////////////
		// Getter and Setter Fin //
		// Utils Debut
		/////////////////////////////
		/**
		 * Renvoie une string représentant Palanque
		 * Exemple TODO
		 * @return string
		 */
		public function __toString(){
			return "Id: ".$this->id." IdFicheSecurite: ".$this->id_fiche_securite.($this->moniteur != null ? " Moniteur: ".$this->moniteur->getNom() : "" )."Numero: ".$this->numero." NbrPlongeur: ".count($this->plongeurs)." TypePlonge: ".$this->type_plonge." TypeGaz: ".$this->type_gaz." ProfondeurPrevu: ".$this->profondeur_prevue." ProfondeurRealise: ".$this->profondeur_realisee." DureePrevue: ".$this->duree_prevue." DureeRealise: ".$this->duree_realisee." Version: ".$this->version;
		}
		/////////////////
		// Utils Fin //
		/////////////////
	}
?>