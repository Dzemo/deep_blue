<?php
	/**
	 * Ficher contenant la classe UtilisateurDao
	 * @author Raphaël Bideau - 3iL
	 * @package Dao
	 */
	
	/**
	 * Classe permettant d'interagir avec la base de données concernant les Utilisateur
	 */
	class UtilisateurDao extends Dao{
		
		/* Public */
		
		/**
		 * Renvoi tout les utilisateurs dans la base.
		 * @return array
		 */
		public static function getAll(){
			return self::getByQuery("SELECT * FROM db_utilisateur");
		}

		/**
		 * Renvoi tout les utilisateurs actif.
		 * @return array
		 */
		public static function getAllActif(){
			return self::getByQuery("SELECT * FROM db_utilisateur WHERE actif = TRUE");
		}

		/**
		 * Recherche un utilisateur par login. Renvoi null si aucun utilisateur pour ce login.
		 * @param  string $login
		 * @return Utilisateur
		 */
		public static function getByLogin($login){
			$result = self::getByQuery("SELECT * FROM db_utilisateur WHERE login = ?", [$login]);
			if($result != null && count($result) == 1)
				return $result[0];
			else
				return null;
		}

		/**
		 * Retourne l'utilisateur associé au moniteur dont l'id est en paramêtre. Null si ce moniteur n'a pas d'utilisateur associé
		 * @param  int $id_moniteur 
		 * @return Utilisateur              
		 */
		public static function getByMoniteurAssocie($id_moniteur){
			$result = self::getByQuery("SELECT * FROM db_utilisateur WHERE id_moniteur = ?", [$id_moniteur]);
			if($result != null && count($result) == 1)
				return $result[0];
			else
				return null;
		}		

		/**
		 * Enregistre de utilisateur passé en parametre et le renvoi ou renvoi null en cas d'erreur
		 * @param  Utilisateur $utilisateur
		 * @return Utilisateur
		 */
		public static function insert(Utilisateur $utilisateur){
			if($utilisateur == null || 
				$utilisateur->getLogin() == null || strlen($utilisateur->getLogin()) == 0 ||
				$utilisateur->getNom() == null || strlen($utilisateur->getNom()) == 0 ||
				$utilisateur->getPrenom()== null || strlen($utilisateur->getPrenom()) == 0 ||
				$utilisateur->getMotDePasse()== null || strlen($utilisateur->getMotDePasse()) == 0 ||
				$utilisateur->getEmail() == null || strlen($utilisateur->getEmail()) == 0)
				return null;

			$utilisateur->updateVersion();

			$stmt = parent::getConnexion()->prepare("INSERT INTO db_utilisateur (login,nom, prenom, mot_de_passe, administrateur, email, actif, id_moniteur, version) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
			$result = $stmt->execute([
				$utilisateur->getLogin(), 
				$utilisateur->getNom(), 
				$utilisateur->getPrenom(), 
				$utilisateur->getMotDePasse(), 
				$utilisateur->isAdministrateur(), 
				$utilisateur->getEmail(), 
				$utilisateur->getActif(),
				$utilisateur->getMoniteurAssocie() != null ? $utilisateur->getMoniteurAssocie()->getId() : null,
				$utilisateur->getVersion()
				]);
			if($result)
				return $utilisateur;
			else
				return null;
		}

		/**
		 * Met à jours en base de donnée l'utilisateur passé en parametre SAUF SON MOT DE PASSE et le renvoi ou renvoi null en cas d'erreur.
		 * Augmente automatiquement sa version de 1.
		 * @param  Utilisateur $utilisateur
		 * @return Utilisateur
		 */
		public static function update(Utilisateur $utilisateur){			
			if($utilisateur == null || 
				$utilisateur->getLogin() == null || strlen($utilisateur->getLogin()) == 0 ||
				$utilisateur->getNom() == null || strlen($utilisateur->getNom()) == 0 ||
				$utilisateur->getPrenom()== null || strlen($utilisateur->getPrenom()) == 0 ||
				$utilisateur->getEmail() == null || strlen($utilisateur->getEmail()) == 0 ||
				$utilisateur->getActif() === null || 
				$utilisateur->getVersion() === null){
				return null;
			}
			
			$utilisateur->updateVersion();
			$stmt = parent::getConnexion()->prepare("UPDATE db_utilisateur SET nom = ?, prenom = ?, administrateur = ?, email = ?, actif = ?, id_moniteur = ?, version = ? WHERE login = ?");
			$result = $stmt->execute([
				$utilisateur->getNom(), 
				$utilisateur->getPrenom(), 
				$utilisateur->isAdministrateur(), 
				$utilisateur->getEmail(), 
				$utilisateur->getActif(),
				$utilisateur->getMoniteurAssocie() != null ? $utilisateur->getMoniteurAssocie()->getId() : null,
				$utilisateur->getVersion(),
				$utilisateur->getLogin()
				]);
			if($result)
				return $utilisateur;
			else
				return null;
		}

		/**
		 * Met à jours en base de donnée le mot de passe de l'utilisateur et le renvoi ou renvoi null en cas d'erreur.
		 * Augmente automatiquement sa version de 1.
		 * @param  Utilisateur $utilisateur
		 * @return Utilisateur
		 */
		public static function updateMotDePasse(Utilisateur $utilisateur){			
			if($utilisateur == null || 
				$utilisateur->getLogin() == null || strlen($utilisateur->getLogin()) == 0 ||
				$utilisateur->getMotDePasse()== null || strlen($utilisateur->getMotDePasse()) == 0 ||
				$utilisateur->getVersion() === null)
				return null;
			
			$utilisateur->updateVersion();
			$stmt = parent::getConnexion()->prepare("UPDATE db_utilisateur SET mot_de_passe = ?, version = ? WHERE login = ?");
			$result = $stmt->execute([
				$utilisateur->getMotDePasse(),
				$utilisateur->getVersion(),
				$utilisateur->getLogin()
				]);
			if($result)
				return $utilisateur;
			else
				return null;
		}

		/**
		 * Tente de rechercher en base un utilisateur actif correspondent au identifiant passé en parametre.
		 * Renvoi l'Utilisateur si les identifiants correspondent, ou null sinon.
		 * @param  string $login
		 * @param  string $mot_de_passe hashé en md5
		 * @return Utilisateur
		 */
		public static function authenticate($login, $mot_de_passe){
			//le mot de passe est étendu déjà hashé en md5
			$result = self::getByQuery("SELECT * FROM db_utilisateur WHERE login = ? AND mot_de_passe = ? AND actif = TRUE", [$login, $mot_de_passe]);
			if($result != null && count($result) == 1)
				return $result[0];
			else
				return null;
		}

		/* Private */

		/**
		 * Execute la requere $query avec les parametres optionnels contenus dans le tableau $param.
		 * Renvoi un tableau de Utilisateur.
		 * @param  string $query
		 * @param  array $param
		 * @return array
		 */
		private static function getByQuery($query, $param = null){
			$stmt = parent::getConnexion()->prepare($query);
			if($stmt->execute($param) && $stmt->rowCount() > 0){
				$arrayUtilisateur = array();
				while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
					$utilisateur = new Utilisateur($row['login'], $row['version']);
					$utilisateur->setNom($row['nom']);
					$utilisateur->setPrenom($row['prenom']);
					$utilisateur->setMotDePasse($row['mot_de_passe']);
					$utilisateur->setAdministrateur($row['administrateur']);
					$utilisateur->setEmail($row['email']);
					$utilisateur->setActif($row['actif']);
					$utilisateur->setMoniteurAssocie(MoniteurDao::getById($row['id_moniteur']));
					$arrayUtilisateur[] = $utilisateur;
				}
				return $arrayUtilisateur;
			}
			else{
				return null;
			}
		}
	}
?>