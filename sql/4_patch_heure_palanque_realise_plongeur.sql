/* Ajout de l'heure sur la table palanquee */
ALTER TABLE db_palanquee ADD COLUMN heure varchar(5);

/* Ajout des profondeurs et durées réalisées sur la table db_plongeur */
ALTER TABLE db_plongeur ADD COLUMN profondeur_realisee DECIMAL;
ALTER TABLE db_plongeur ADD COLUMN duree_realisee INT;

/* Copie des durées et profondeurs réalisées depuis db_palanquee vers db_plongeur */
UPDATE db_plongeur JOIN db_palanquee ON db_plongeur.id_palanquee = db_palanquee.id_palanquee
   SET db_plongeur.profondeur_realisee = db_palanquee.profondeur_realisee,
   	   db_plongeur.duree_realisee = db_palanquee.duree_realisee;

/* Suppression des profondeurs et durées réalisées de la table db_palanquee */
ALTER TABLE db_palanquee DROP COLUMN profondeur_realisee;
ALTER TABLE db_palanquee DROP COLUMN duree_realisee;

/* Ajout des profondeurs et durées réalisées sur la table db_palanquee pour le moniteur */
ALTER TABLE db_palanquee ADD COLUMN profondeur_realisee_moniteur DECIMAL;
ALTER TABLE db_palanquee ADD COLUMN duree_realisee_moniteur INT;