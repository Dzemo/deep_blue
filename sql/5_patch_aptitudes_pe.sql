/* Correctif des profondeurs PE-X */
UPDATE db_aptitude 
SET technique_max = 20
WHERE id_aptitude = 6; 

UPDATE db_aptitude 
SET technique_max = 40
WHERE id_aptitude = 7; 

UPDATE db_aptitude
SET technique_max = 60
WHERE id_aptitude = 8;

/* Correctif des profondeurs PA-X */
UPDATE db_aptitude 
SET technique_max = 20
WHERE id_aptitude = 2; 

UPDATE db_aptitude 
SET technique_max = 40
WHERE id_aptitude = 3; 

UPDATE db_aptitude
SET technique_max = 60
WHERE id_aptitude = 4;