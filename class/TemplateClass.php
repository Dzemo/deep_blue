<?php
	/**
	 * Ficher contenant la classe Template
	 * @author Raphaël Bideau - 3iL
	 * @package Modele
	 */

	/**
	 * Classe template
	 */
	class Template{

		///////////////////////
		// Variables Debut //
		///////////////////////

		//////////////////////
		// Varibables Fin //
		// Constructeur  //
		//////////////////////

		/**
		 * Initialise les valeurs à null, avec éventuellement la version spécifié
		 * @param int $version Optionnel
		 */
		public function Template($version = 0){
			//numéro de version ?
		}

		///////////////////////////////
		// Getter and Setter Debut //
		///////////////////////////////

		/**
		 * @return [type]
		 */
		public function get(){
			return $this-> ;
		}
		/**
		 * @param [type] $
		 */
		public function set($){
			$this-> = $ ;
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
		 * Renvoie une string représentant Template
		 * @example TODO
		 * @return string
		 */
		public function __toString(){
			echo "<br/>var_dump : <br/>";
			var_dump($this);
			echo "<br/>";
		}

		/////////////////
		// Utils Fin //
		/////////////////
	}
?>