ALTER TABLE db_site ADD COLUMN version INTEGER UNSIGNED;
UPDATE db_site SET version = 0;