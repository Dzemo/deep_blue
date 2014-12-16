<?php
	/**
	 * Ficher contenant la classe Aide
	 * @author Raphaël Bideau - 3iL
	 * @package Modele
	 */

	/**
	 * Classe Aide
	 * Représente un élément de la faq, une question une réponse
	 */
	class Aide{

		///////////////////////
		// Variables Debut //
		///////////////////////

		/**
		 * Id de l'aide, utile pour faire des liens
		 * @var int
		 */
		private $id_question;

		/**
		 * Question de cette élément d'aide
		 * @var string
		 */
		private $question;

		/**
		 * Réponse de cette élément d'aide
		 * @var string
		 */
		private $reponse;

		/**
		 * Tag de cette élément d'aide
		 * @var string
		 */
		private $tag;

		/**
		 * Tableau d'aide contenant les autres aide auqel cette question peut ce rapporter
		 * @var [type]
		 */
		private $voir_aussi;

		/**
		 * Etat de cet élément d'aide (disponible, ou pas)
		 * @var boolean
		 */
		private $disponible;

		//////////////////////
		// Varibables Fin //
		// Constructeur  //
		//////////////////////

		/**
		 * Initialise les valeurs à null, avec éventuellement la version spécifié
		 * @param int $id_question Optionnel
		 */
		public function Aide($id_question){
			$this->id_question = $id_question;
			$this->question = null;
			$this->reponse = null;
			$this->tag = null;
			$this->voir_aussi = array();
			$this->disponible = 1;
		}

		///////////////////////////////
		// Getter and Setter Debut //
		///////////////////////////////

		/**
		 * @return int
		 */
		public function getId(){
			return $this->id_question ;
		}
		/**
		 * @param int $id
		 */
		public function setId($id_question){
			$this->id_question = $id_question ;
		}

		/**
		 * @return string
		 */
		public function getQuestion(){
			return $this->question ;
		}
		/**
		 * @param string $question
		 */
		public function setQuestion($question){
			$this->question = $question ;
		}

		/**
		 * @return string
		 */
		public function getReponse(){
			return $this->reponse ;
		}
		/**
		 * @param string $reponse
		 */
		public function setReponse($reponse){
			$this->reponse = $reponse ;
		}

		/**
		 * @return string
		 */
		public function getTag(){
			return $this->tag ;
		}
		/**
		 * @param string $tag
		 */
		public function setTag($tag){
			$this->tag = $tag ;
		}

		/**
		 * @return array
		 */
		public function getVoirAussi(){
			return $this->voir_aussi ;
		}
		/**
		 * @param array $voir_aussi
		 */
		public function setVoirAussi($voir_aussi){
			$this->voir_aussi = $voir_aussi ;
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
		
		/////////////////////////////
		// Getter and Setter Fin //
		// Utils Debut
		/////////////////////////////

		/**
		 * Ajoute l'aide passé en parametre dans le tableau de cette aide contenant les aides à laquels elle est relié
		 * @param  Aide   $voir 
		 */
		public function ajouterVoirAussi(Aide $voir){
			$this->voir_aussi[] = $voir;
		}

		/**
		 * Renvoie une string représentant cette question
		 * Exemple
		 * @return string
		 */
		public function __toString(){
			return "Id_question: ".$this->id_question." Disponible: ".$this->disponible." Question: ".$this->question." Réponse: ".$this->reponse." Tag: ".$this->tag." Rélié à ".count($this->voir_aussi)." autres aides";
		}

		/////////////////
		// Utils Fin //
		/////////////////
	}
?>