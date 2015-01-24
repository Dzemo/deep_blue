--
-- Retrait des contraintes NOT NULL pour l'id_embarcation, id_directeur_plonge et timestamps
-- car les fiches récupérés d'une application mobile lors de la synchronisation peuvent ne
-- pas contenir ces informations
--
-- Ajout de la suppression logique avec une colonne desactivé pour les palanquées et les plongeurs

ALTER TABLE db_fiche_securite MODIFY id_embarcation MEDIUMINT;
ALTER TABLE db_fiche_securite MODIFY id_directeur_plonge MEDIUMINT;
ALTER TABLE db_fiche_securite MODIFY timestamp INT UNSIGNED;

--Ajout des colonnes desactivé
ALTER TABLE db_fiche_securite ADD COLUMN desactive BOOLEAN DEFAULT FALSE;
UPDATE db_fiche_securite SET desactive = TRUE WHERE disponible = FALSE;
ALTER TABLE db_fiche_securite DROP COLUMN disponible;

ALTER TABLE db_palanquee ADD COLUMN desactive BOOLEAN DEFAULT FALSE;
ALTER TABLE db_plongeur ADD COLUMN desactive BOOLEAN DEFAULT FALSE;
