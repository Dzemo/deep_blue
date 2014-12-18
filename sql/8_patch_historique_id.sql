/* Ce script modifie la table db_historique pour y ajouter une colonne id_historique en AUTO_INCREMENT et PRIMARY KEY */

/* Création d'une table temporaire pour stocké le contenu de la table db_historique */
CREATE TABLE db_historique_tmp(
    login_utilisateur varchar(20) NOT NULL,
    timestamp BIGINT NOT NULL,
    id_fiche_securite MEDIUMINT,
    source varchar(20) NOT NULL DEFAULT 'WEB',
    commentaire text NOT NULL DEFAULT ''
);

/* Copie des données vers la table temporaire */
INSERT INTO db_historique_tmp(login_utilisateur, timestamp, id_fiche_securite, source, commentaire) SELECT login_utilisateur, timestamp, id_fiche_securite, source, commentaire FROM db_historique;

/* Suppression des données dans la table db_historique pour modifier la structure */
DELETE FROM db_historique;

/* Ajout de la colonne id_historique */
ALTER TABLE db_historique ADD COLUMN id_historique MEDIUMINT;

/* Changement de la PRIMARY KEY sur la colonne id_historique */
ALTER TABLE db_historique DROP PRIMARY KEY;
ALTER TABLE db_historique ADD CONSTRAINT pk_db_historique PRIMARY KEY (id_historique);

/* Modification de la colonne id_historique en AUTO_INCREMENT */
ALTER TABLE db_historique MODIFY id_historique MEDIUMINT NOT NULL AUTO_INCREMENT;

/* Copie des données de la table temporaire vers la table db_historique */
INSERT INTO db_historique(login_utilisateur, timestamp, id_fiche_securite, source, commentaire) SELECT login_utilisateur, timestamp, id_fiche_securite, source, commentaire FROM db_historique_tmp;

/* Suppression de la table temporaire */
DROP TABLE db_historique_tmp;