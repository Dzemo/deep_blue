ALTER TABLE db_aide ADD COLUMN disponible BOOLEAN;
UPDATE db_aide SET disponible = TRUE;