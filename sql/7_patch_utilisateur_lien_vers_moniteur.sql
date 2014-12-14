ALTER TABLE db_utilisateur ADD COLUMN id_moniteur MEDIUMINT DEFAULT NULL;
ALTER TABLE db_utilisateur ADD CONSTRAINT fk_db_moniteur FOREIGN KEY (id_moniteur) REFERENCES db_moniteur(id_moniteur);