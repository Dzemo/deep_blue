<?php
	/**
	 * Ficher contenant la classe SiteDao
	 * @author Raphaël Bideau - 3iL
	 * @package Dao
	 */

	/**
	 * SiteDao permet d'interagir avec la base de donnée en ce qui concerne les sites
	 */
	class SiteDao extends Dao {

		/* Public */

		/**
		 * Renvoi tout les sites disponible dans un tableau indexé par leurs ids ordonnée par leurs id desc
		 * @return array 
		 */
		public static function getAll(){
			return self::getByQuery("SELECT * FROM db_site WHERE DESACTIVE = FALSE ORDER BY id_site DESC");
		}

		/**
		 * Renvoi le site d'id séléctionné ou null si il n'existe pas, peut renvoyé un site désactivé
		 * @param  int $site_id 
		 * @return Site          
		 */
		public static function getById($site_id){
			$result = self::getByQuery("SELECT * FROM db_site WHERE id_site = ?",array($site_id));

			if(count($result) == 1)
				return $result[$site_id];
			else
				return null;
		}

		/**
		 * Renvoi tout les sites dont la version est superieur à celle spécifié (strictement superieur pour version > 0 ou superieur égal pour version = 0)
		 * @param  int $versionMax 
		 * @return array             
		 */
		public static function getFromVersion($versionMax){
			//Quand versionMax vaut zero on veut inclure les version local à 0 car il s'agit de la première synchronisation pour une application
			return self::getByQuery("SELECT * FROM db_site WHERE version ".($versionMax == 0 ? ">=" : ">")." ?",[$versionMax]);
		}

		/**
		 * Ajoute un site en base de données et le retourne ou renvoi null en cas d'erreur
		 * @param  Site   $site 
		 * @return Site 
		 */
		public static function insert(Site $site){
			if($site == null || 
				$site->getNom() == null || strlen($site->getNom()) == 0 )
				return null;
			
			$stmt = parent::getConnexion()->prepare("INSERT INTO db_site (nom, commentaire, version) VALUES (?, ?, ?)");
			$result = $stmt->execute([
				$site->getNom(), 
				$site->getCommentaire(),
				$site->getVersion()
				]);
			
			if($result){
				$site->setId(parent::getConnexion()->lastInsertId());
				return $site;
			}
			else
				return null;
		}

		/**
		 * Met à jour un site, en desactivant l'ancien site et en créant un nouveau avec les données à jours
		 * @param  Site   $site
		 * @return Site
		 */
		public static function update(Site $site){
			if($site == null || $site->getId() == null)
				return null;

			$site->updateVersion();

			self::delete($site->getId());

			return self::insert($site);
		}

		/**
		 * Desactive un site en base de donnée
		 * @param  Site   $site
		 */
		public static function delete($siteId){
			if($siteId == null)
				return ;

			$stmt = parent::getConnexion()->prepare("UPDATE db_site SET DESACTIVE = TRUE WHERE id_site = ?");
			$stmt->execute(array($siteId));
		}

		/* Private */
		
		/**
		 * Execute la requere $query avec les parametres optionnels contenus dans le tableau $param.
		 * Renvoi un tableau de Site indexé par leurs id
		 * @param  string $query
		 * @param  array $param
		 * @return array
		 */
		private static function getByQuery($query, $param = null){

			$stmt = parent::getConnexion()->prepare($query);

			if($stmt->execute($param) && $stmt->rowCount() > 0){
				$arrayResultat = array();
				while($row = $stmt->fetch(PDO::FETCH_ASSOC)){

					//initialisation des objets
					$site = new Site(intval($row['id_site'],$row['version']));
					$site->setNom($row['nom']);
					$site->setCommentaire($row['commentaire']);

					$arrayResultat[$site->getId()] = $site;
				}

				return $arrayResultat;
			}
			else{
				return array();
			}
		}
	}
?>