/* Ajout de l'heure sur la table palanque */
ALTER TABLE db_palanque ADD COLUMN heure varchar(5);

/* Ajout des profondeurs et durées réalisées sur la table db_plongeur */
ALTER TABLE db_plongeur ADD COLUMN profondeur_realisee DECIMAL;
ALTER TABLE db_plongeur ADD COLUMN duree_realisee INT;

/* Copie des durées et profondeurs réalisées depuis db_palanque vers db_plongeur */
UPDATE db_plongeur JOIN db_palanque ON db_plongeur.id_palanque = db_palanque.id_palanque
   SET db_plongeur.profondeur_realisee = db_palanque.profondeur_realisee,
   	   db_plongeur.duree_realisee = db_palanque.duree_realisee;

/* Suppression des profondeurs et durées réalisées de la table db_palanque */
ALTER TABLE db_palanque DROP COLUMN profondeur_realisee;
ALTER TABLE db_palanque DROP COLUMN duree_realisee;