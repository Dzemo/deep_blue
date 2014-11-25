<?php
	/**
	 * Ficher contenant la classe Utilisateur
	 * @author Raphaël Bideau - 3iL
	 * @package Modele
	 */

	/**
	 * Représente un utilisateur qui peut se connecter sur l'application via un login et mot de passe.
	 * Le plus souvent (mais selon ses aptitudes), il pourra aussi éventuellement encadrer des sorties, il sera donc
	 * référencé sur la fiche de sécurité correspondente
	 */
	class Utilisateur{

		///////////////////////
		// Variables Debut //
		///////////////////////

		/**
		 * Login pour ce connecter à l'application sur mobile ou sur l'interface web.
		 * Sauf homonymie, de la forme p.nom pour Mr Prenom Nom ou avec un chiffre en cas d'homonymie.
		 * @var string
		 */
		private $login;

		/**
		 * Nom du utilisateur.
		 * @var string
		 */
		private $nom;

		/**
		 * Prénom du utilisateur.
		 * @var string
		 */
		private $prenom;

		/**
		 * Mot de passe utilisé pour se connecter sur mobile ou l'interface web, hashé en md5.
		 * @var string
		 */
		private $mot_de_passe;

		/**
		 * True si l'utilisateur est administrateur
		 * @var boolean
		 */
		private $administrateur;

		/**
		 * Addresse email.
		 * @var string
		 */
		private $email;

		/**
		 * Indique si ce utilisateur est actif.
		 * @var boolean
		 */
		private $actif;

		/**
		 * Version de ce utilisateur, utilisé pour la synchronisation entre l'interface web
		 * et l'application de plongé.
		 * @var int
		 */
		private $version;
		
		//////////////////////
		// Varibables Fin //
		// Constructeur  //
		//////////////////////
		
		/**
		 * Initialise un Utilisateur avec un login et éventuellement une version.
		 * isAdministrateur est au false, les autres valeurs sont à null
		 *
		 * @param string $login
		 * @param int $version Optionnal
		 */
		public function Utilisateur($login, $version = 0){
			$this->login = $login;
			$this->nom = null;
			$this->prenom = null;
			$this->mot_de_passe = null;
			$this->administrateur = false;
			$this->email = null;
			$this->actif = null;
			$this->version = $version;
		}

		///////////////////////////////
		// Getter and Setter Debut //
		///////////////////////////////

		/**
		 * @param string $login
		 */
		public function setLogin($login){
			$this->login = $login;
		}

		/**
		 * @return string
		 */
		public function getLogin(){
			return $this->login;
		}
		
		/**
		 * @param string $nom
		 */
		public function setNom($nom){
			$this->nom = $nom;
		}
		/**
		 * @return string
		 */
		public function getNom(){
			return $this->nom;
		}

		/**
		 * @param string $prenom
		 */
		public function setPrenom($prenom){
			$this->prenom = $prenom;
		}

		/**
		 * @return string
		 */
		public function getPrenom(){
			return $this->prenom;
		}

		/**
		 * Attend un mot de passe haché.
		 * @param string $mot_de_passe
		 */
		public function setMotDePasse($mot_de_passe){
			$this->mot_de_passe = $mot_de_passe;
		}
		/**
		 * Renvoi le mot de passe haché.
		 * @return string
		 */
		public function getMotDePasse(){
			return $this->mot_de_passe;
		}

		/**
		 * @param boolean $administrateur
		 */
		public function setAdministrateur($administrateur){
			$this->administrateur = $administrateur;
		}
		/**
		 * @return boolean
		 */
		public function isAdministrateur(){
			return $this->administrateur;
		}

		/**
		 * @param string $email
		 */
		public function setEmail($email){
			$this->email = $email;
		}
		/**
		 * @return string
		 */
		public function getEmail(){
			return $this->email;
		}

		/**
		 * @return boolean
		 */
		public function getActif(){
			return $this->actif ;
		}
		/**
		 * @param boolean $actif
		 */
		public function setActif($actif){
			$this->actif = $actif ;
		}

		/**
		 * @return int
		 */
		public function getVersion(){
			return $this->version;
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
		 * Affiche le utilisateur sous forme de chaine de caractere
		 * Exemple: Login: 'test', Nom prenom: 'test ' Mot de passe (hache): '098f6bcd4621d373cade4e832627b4f6' Role: 'UTILISATEUR' Email: 'raphael.bideau@gmail.com' Actif: oui Version: 0
		 * @return string
		 */
		public function __toString(){

			return "Login: '".$this->login."', Nom prenom: '".$this->nom." ".$this->prenom."' Mot de passe (hache): '".
					$this->mot_de_passe."' Administrateur: '".($this->administrateur ? "oui" : "non")."' Email: '".$this->email."' Actif: ".
					($this->actif? "oui" : "non")." Version: ".$this->getVersion() ;
		}

		/////////////////
		// Utils Fin //
		/////////////////
	}
?>