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

		/**
		 * Renvoi toute les aides de la base dans un tableau indéxé par leur id
		 * @return array 
		 */
		public static function getAllDisponible(){
			return self::getByQuery("SELECT * FROM db_aide WHERE disponible = TRUE");
		}

		/**
		 * Renvoi toute les aides de la base dans un tableau indéxé par leur id
		 * @return array 
		 */
		public static function getById($id){
			$result = self::getByQuery("SELECT * FROM db_aide WHERE id_question = ?", [intval($id)]);
			if($result != null && count($result) == 1)
				return $result[$id];
			else
				return null;
		}

		/**
		 * Ajoute une aide en base de données et la retourne ou renvoi null en cas d'erreur
		 * @param  Aide   $aide 
		 * @return Aide 
		 */
		public static function insert(Aide $aide){
			if($aide == null || 
				$aide->getQuestion() == null || strlen($aide->getQuestion()) == 0 )
				return null;
			
			$stmt = parent::getConnexion()->prepare("INSERT INTO db_aide (question, reponse, tag, voir_aussi, disponible) VALUES (?, ?, ? , ?, ?)");
			$result = $stmt->execute([
				$aide->getQuestion(), 
				$aide->getReponse(),
				$aide->getTag(),
				$aide->getVoirAussi(),
				$aide->getDisponible()
				]);
			
			if($result){
				$aide->setId(parent::getConnexion()->lastInsertId());
				return $aide;
			}
			else
				return null;
		}

		/**
		 * Met à jour l'Aide passé en parametre, renvoi null en cas d'erreur.
		 * @param  Aide $aide
		 * @return Aide
		 */
		public static function update(Aide $aide){
			if($aide == null ||	$aide->getId() == null ||
				$aide->getQuestion() == null || strlen($aide->getQuestion()) == 0 ||
				$aide->getReponse() == null || strlen($aide->getReponse()) == 0 ||
				$aide->getTag() == null)
				return null;
			echo "CA APPELLE AU MOINS NON?";
			$stmt = parent::getConnexion()->prepare("UPDATE db_aide SET question = ?, reponse = ?, tag = ?, voir_aussi = ?, disponible = ? WHERE id_question = ?");
			$result = $stmt->execute([$aide->getQuestion(), 
							$aide->getReponse(),
							$aide->getTag(), 
							$aide->getVoirAussi(), 
							$aide->getDisponible(),
							$aide->getId()]);
			if($result){
				echo "CA MARCHE MOYEN";
				return $aide;
				}
			else{
				echo "CA MARCHE PAS";
				return null;
			}
				
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
					$aide = new Aide($row['id_question']);
					$aide->setQuestion($row['question']);
					$aide->setReponse($row['reponse']);
					$aide->setTag($row['tag']);
					$aide->setDisponible($row['disponible']);

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