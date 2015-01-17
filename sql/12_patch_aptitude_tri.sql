--
-- Ce patch modifie la table db_aptitude en ajoutant deux colonne permettant de différencier les aptitudes
-- qui concernent les plongeurs et/ou les moniteurs
--

-- Ajout des colonnes permettant de marquer les aptitudes qui concernent les plongeurs et/ou les moniteurs
ALTER TABLE db_aptitude ADD COLUMN pour_plongeur BOOLEAN DEFAULT FALSE;
ALTER TABLE db_aptitude ADD COLUMN pour_moniteur BOOLEAN DEFAULT FALSE;

-- Mise à jours de la version pour pousser les modifications vers les applications mobiles
UPDATE db_aptitude SET version = UNIX_TIMESTAMP();

-- Aptitude qui concernent les plongeurs
UPDATE db_aptitude SET pour_plongeur = TRUE WHERE 
libelle_court = 'Débutant' OR 
libelle_court = 'PA-12' OR 
libelle_court = 'PA-20' OR 
libelle_court = 'PA-40' OR 
libelle_court = 'PA-60' OR
libelle_court = 'PE-12' OR 
libelle_court = 'PE-20' OR 
libelle_court = 'PE-40' OR 
libelle_court = 'PE-60' OR 
libelle_court = 'PN-20' OR 
libelle_court = 'PN-C' OR 
libelle_court = 'P-1' OR 
libelle_court = 'P-1 a' OR 
libelle_court = 'P-2' OR 
libelle_court = 'P-3' OR 
libelle_court = 'P-4' OR 
libelle_court = 'GP';

-- Aptitudes qui concernent les moniteurs
UPDATE db_aptitude SET pour_moniteur = TRUE WHERE 
libelle_court = 'PN-C' OR 
libelle_court = 'E-1' OR 
libelle_court = 'E-2' OR 
libelle_court = 'E-3' OR 
libelle_court = 'E-4' OR 
libelle_court = 'GP' OR 
libelle_court = 'P-4';
