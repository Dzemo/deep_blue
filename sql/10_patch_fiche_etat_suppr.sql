ALTER TABLE db_fiche_securite ADD COLUMN disponible BOOLEAN;
UPDATE db_fiche_securite SET disponible = TRUE;