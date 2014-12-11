<?php
	/**
	 * Ficher contenant la classe AideDao
	 * @author Raphaël Bideau - 3iL
	 * @package Dao
	 */

	/**
	 * Classe permettant d'interagir avec la base de données concernant les Aides
	 */
	class AideDao extends Dao {

		/* Public */

		/**
		 * Renvoi toute les aides de la base dans un tableau indéxé par leur id
		 * @return array 
		 */
		public static function getAll(){
			return self::getByQuery("SELECT * FROM db_aide");
		}

		/* Private */
		/**
		 * Execute la requere $query avec les parametres optionnels contenus dans le tableau $param.
		 * Renvoi un tableau de Aide indéxé par leur id.
		 * @param  string $query
		 * @param  array $param
		 * @return array
		 */
		private static function getByQuery($query, $param = null){

			$stmt = parent::getConnexion()->prepare($query);

			if($stmt->execute($param) && $stmt->rowCount() > 0){
				$arrayResultat = array();
				$arrayVoirAussi = array();
				while($row = $stmt->fetch(PDO::FETCH_ASSOC)){

					//initialisation des objets
					$aide = new Aide($row['id']);
					$aide->setQuestion($row['question']);
					$aide->setReponse($row['reponse']);
					$aide->setTag($row['tag']);

					$arrayResultat[$aide->getId()] = $aide;
					$arrayVoirAussi[$aide->getId()] = $row['voir_aussi'];
				}

				//Récupération des voir aussi
				if(count($arrayResultat) > 0){
					foreach ($arrayResultat as $aide) {
						$voirs_aussi = explode(";",$arrayVoirAussi[$aide->getId()]);
						if(count($voirs_aussi) > 0){
							foreach ($voirs_aussi as $lien) {
								if(intval($lien)>0 && isset($arrayResultat[intval($lien)]))
								$aide->ajouterVoirAussi($arrayResultat[intval($lien)]);
							}
						}
					}
				}

				return $arrayResultat;
			}
			else{
				return null;
			}
		}
	}
?>