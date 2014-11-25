<?php
	/**
	 * Ficher contenant la classe HistoriqueDao
	 * @author Raphaël Bideau - 3iL
	 * @package Dao
	 */

	/**
	 * Classe permettant d'interagir avec la base de données concernant les Historique
	 */
	class HistoriqueDao extends Dao {

		/* Public */

		/**
		 * Renvoi un tableau contenant tout les historique dans la base trié par date décroissant
		 * @return array
		 */
		public static function getAll(){
			return self::getByQuery("SELECT * FROM db_historique ORDER BY timestamp DESC");
		}

		/**
		 * Renvoi un tableau contenant tout les historique dans la base trié par date décroissant effectué par le 
		 * l'utilisateur dont le login est passé en parametre
		 * @param  string $login_utilisateur
		 * @return array
		 */
		public static function getByUtilisateur($login_utilisateur){
			return self::getByQuery("SELECT * FROM db_historique WHERE login_utilisateur = ? ORDER BY timestamp DESC", [$login_utilisateur]);
		}

		/**
		 * Renvoi un tableau contenant tout les historique dans la base trié par date décroissant concernant la fiche
		 * de sécurité dont l'id est passé en parametre
		 * @param  int $id_fiche_securite
		 * @return array
		 */
		public static function getByFicheSecurite($id_fiche_securite){
			return self::getByQuery("SELECT * FROM db_historique WHERE id_fiche_securite = ? ORDER BY timestamp DESC", [$id_fiche_securite]);
		}

		/**
		 * Renvoi l'historique dans la base effectué par l'utilisateur dont le login est passé en parametre 
		 * à la date référencé par le timestamp si il existe, null sinon.
		 * @param  string $login_utilisateur
		 * @param  int $timestamp
		 * @return array
		 */
		public static function getByUtilisateurAndTimestamp($login_utilisateur, $timestamp){
			$result = self::getByQuery("SELECT * FROM db_historique WHERE login_utilisateur = ? AND timestamp = ? ORDER BY timestamp DESC", [$login_utilisateur, $timestamp]);

			if($result != null && count($result) == 1)
				return $result[0];
			else
				return null;
		}

		/**
		 * Enregistre un Historique dans la base et le renvoi. Renvoi null en cas d'erreur.
		 * @param  Historique $historique
		 * @return Historique
		 */
		public static function insert(Historique $historique){
			if($historique == null ||
				$historique->getLoginUtilisateur() == null || 
				$historique->getTimestamp() == null)
				return null;

			$pdo = parent::getConnexion();

			$stmt = $pdo->prepare("INSERT INTO db_historique (login_utilisateur, timestamp, id_fiche_securite, source, commentaire) VALUES (?, ?, ?, ?, ?)");
			$result = $stmt->execute([$historique->getLoginUtilisateur(),
							$historique->getTimestamp(),
							$historique->getIdFicheSecurite(),
							$historique->getSource(),
							$historique->getCommentaire()
							]);
			if($result)
				return $historique;
			else
				return null;
		}

		/* Private */
		/**
		 * Execute la requere $query avec les parametres optionnels contenus dans le tableau $param.
		 * Renvoi un tableau d'Historique.
		 * @param  string $query
		 * @param  array $param
		 * @return array
		 */
		private static function getByQuery($query, $param = null){

			$stmt = parent::getConnexion()->prepare($query);

			if($stmt->execute($param) && $stmt->rowCount() > 0){
				$arrayResultat = array();
				while($row = $stmt->fetch(PDO::FETCH_ASSOC)){

					$historique = new Historique($row['login_utilisateur'], $row['timestamp'], $row['id_fiche_securite']);

					$historique->setSource($row['source']);
					$historique->setCommentaire($row['commentaire']);

					$arrayResultat[] = $historique;
				}

				return $arrayResultat;				
			}
			else{
				return null;
			}
		}
	}
?>