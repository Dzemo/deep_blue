/*
Ajout d'une table enregistrant les sites 
Modification de la table db_fiche_securite pour retirer la colonne 'site' et ajouter un lien vers les sites utilisés
*/

/*Ajout de la table db_site*/
DROP TABLE IF EXISTS db_site;
CREATE TABLE db_site(
	id_site MEDIUMINT NOT NULL AUTO_INCREMENT,
	nom varchar(40) NOT NULL,
	commentaire TEXT DEFAULT '',
	DESACTIVE BOOLEAN DEFAULT FALSE, /*suppression logique*/
	CONSTRAINT PRIMARY KEY (id_site)
);

/*Ajout des sites existants dans db_site et db_site_disponible*/
INSERT INTO db_site(nom) SELECT site FROM db_fiche_securite GROUP BY site;

/*Ajout de la clé étrangère dans db_fiche_securite;*/
ALTER TABLE db_fiche_securite ADD COLUMN id_site MEDIUMINT;

/*Ajout des liens*/
UPDATE db_fiche_securite JOIN db_site ON db_fiche_securite.site = db_site.nom 
   SET db_fiche_securite.id_site = db_site.id_site ;

/*Ajout de la contrainte*/
ALTER TABLE db_fiche_securite ADD CONSTRAINT fk_db_site FOREIGN KEY (id_site) REFERENCES db_site(id_site);

/*Drop de l'ancienne colonne*/
ALTER TABLE db_fiche_securite DROP COLUMN site;
