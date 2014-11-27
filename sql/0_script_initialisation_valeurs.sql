/*
Ajout des valeurs par défaut :
 deux utilisateur (test et admin)
 deux embarcation (une activé une desactivé)
 deux fiches de sécurité avec 3 et 1 palanquées
 les deux historique de création des palanquées
 */


/*Ajout des utilisateurs*/
INSERT INTO db_utilisateur (login,nom, prenom, mot_de_passe, administrateur, email, actif) VALUES ('admin','admin', 'admin', '21232f297a57a5a743894a0e4a801fc3', TRUE, 'raphael.bideau@gmail.com', true);
INSERT INTO db_utilisateur (login,nom, prenom, mot_de_passe, administrateur, email, actif) VALUES ('test','test', 'test', '098f6bcd4621d373cade4e832627b4f6', FALSE, 'raphael.bideau@gmail.com', true);
DROP TABLE IF EXISTS db_embarcation;

/*Ajout des embarcations*/
INSERT INTO db_embarcation (libelle, maxpersonne, commentaire, disponible) VALUES ('EMB-1', 25, 'Embarcation-1, disponible', true);
INSERT INTO db_embarcation (libelle, maxpersonne, commentaire, disponible) VALUES ('EMB-2', 14, 'Embarcation-2, indisponible', false);

/*Ajout des moniteurs*/
INSERT INTO db_moniteur (nom, prenom, aptitudes, actif, directeur_plonge, email, telephone) VALUES ('Tomas', 'Bessiere', '5;15', TRUE, FALSE, 'tomas.bessiere@email.com', '01 23 45 67 89');
INSERT INTO db_moniteur (nom, prenom, aptitudes, actif, directeur_plonge, email, telephone) VALUES ('François ', 'Simonet', '5;15;11', TRUE, FALSE, 'francois.simonet@email.com', '01 23 45 67 89');
INSERT INTO db_moniteur (nom, prenom, aptitudes, actif, directeur_plonge, email, telephone) VALUES ('Pierre', 'Saunier', '16;5', TRUE, TRUE, 'pierre.saunier@email.com', '01 23 45 67 89');
INSERT INTO db_moniteur (nom, prenom, aptitudes, actif, directeur_plonge, email, telephone) VALUES ('Eric', 'Delaunay', '21;11', TRUE, TRUE, 'eric.delaunay@email.com', '01 23 45 67 89');

/* Ajout des fiches de sécurité */
INSERT INTO db_fiche_securite (id_embarcation, id_directeur_plonge, timestamp, site, etat) 
    VALUES (1, 3, 1412931600, 'La plage sur mer', 'CREER');
INSERT INTO db_fiche_securite (id_embarcation, id_directeur_plonge, timestamp, site, etat) 
    VALUES (1, 4, 1412935200, 'Le grand bleu', 'ARCHIVE');

/* Ajout des historiques */
INSERT INTO db_historique (login_utilisateur, timestamp, id_fiche_securite, source, commentaire) VALUES ('admin', UNIX_TIMESTAMP(), 1, 'WEB', 'Création de la fiche de sécurité');
INSERT INTO db_historique (login_utilisateur, timestamp, id_fiche_securite, source, commentaire) VALUES ('test', UNIX_TIMESTAMP(), 2, 'WEB', 'Création de la fiche de sécurité');

/*Ajout palanquees*/
INSERT INTO db_palanque (id_fiche_securite, id_moniteur, numero, type_plonge, type_gaz, profondeur_prevue, profondeur_realisee, duree_prevue, duree_realisee)
    VALUES (1, 1, 1, 'TECHNIQUE', 'AIR', 12, NULL, 900, NULL);
INSERT INTO db_palanque (id_fiche_securite, id_moniteur, numero, type_plonge, type_gaz, profondeur_prevue, profondeur_realisee, duree_prevue, duree_realisee)
    VALUES (1, 2, 2, 'ENCADRE', 'NITROX', 60, NULL, 2700, NULL);
INSERT INTO db_palanque (id_fiche_securite, id_moniteur, numero, type_plonge, type_gaz, profondeur_prevue, profondeur_realisee, duree_prevue, duree_realisee)
    VALUES (1, NULL, 3, 'AUTONOME', 'AIR', 25, NULL, 1800, NULL);
INSERT INTO db_palanque (id_fiche_securite, id_moniteur, numero, type_plonge, type_gaz, profondeur_prevue, profondeur_realisee, duree_prevue, duree_realisee)
    VALUES (2, NULL, 1, 'AUTONOME', 'AIR', 25, NULL, 1800, NULL);

/*Ajout des plongeurs*/
INSERT INTO db_plongeur (id_palanque, id_fiche_securite, nom, prenom, aptitudes, telephone, telephone_urgence, date_naissance) VALUES (1, 1, 'Bessiere', 'Cyril', '6', '01 23 45 67 89', '01 98 76 54 32', '01/12/1984');
INSERT INTO db_plongeur (id_palanque, id_fiche_securite, nom, prenom, aptitudes, telephone, telephone_urgence, date_naissance) VALUES (1, 1, 'Guillon', 'Amelie', '6', '01 23 45 67 89', '01 98 76 54 32', '31/01/1987');

INSERT INTO db_plongeur (id_palanque, id_fiche_securite, nom, prenom, aptitudes, telephone, telephone_urgence, date_naissance) VALUES (2, 1, 'Verhaeghe', 'Marie', '9;11', '01 23 45 67 89', '01 98 76 54 32', '13/05/1984');
INSERT INTO db_plongeur (id_palanque, id_fiche_securite, nom, prenom, aptitudes, telephone, telephone_urgence, date_naissance) VALUES (2, 1, 'Verhaeghe', 'Hervé', '9;11', '01 23 45 67 89', '01 98 76 54 32', '02/06/1974');
INSERT INTO db_plongeur (id_palanque, id_fiche_securite, nom, prenom, aptitudes, telephone, telephone_urgence, date_naissance) VALUES (2, 1, 'Gimenez', 'Sara', '9;11', '01 23 45 67 89', '01 98 76 54 32', '08/04/1991');
INSERT INTO db_plongeur (id_palanque, id_fiche_securite, nom, prenom, aptitudes, telephone, telephone_urgence, date_naissance) VALUES (2, 1, 'Saunier', 'Jean-Luc', '9;11', '01 23 45 67 89', '01 98 76 54 32', '11/10/1985');

INSERT INTO db_plongeur (id_palanque, id_fiche_securite, nom, prenom, aptitudes, telephone, telephone_urgence, date_naissance) VALUES (3, 1, 'Lacour', 'Myriam', '4;10', '01 23 45 67 89', '01 98 76 54 32', '30/07/1974');
INSERT INTO db_plongeur (id_palanque, id_fiche_securite, nom, prenom, aptitudes, telephone, telephone_urgence, date_naissance) VALUES (3, 1, 'Girault', 'Stéphane', '4;10', '01 23 45 67 89', '01 98 76 54 32', '20/11/1967');
INSERT INTO db_plongeur (id_palanque, id_fiche_securite, nom, prenom, aptitudes, telephone, telephone_urgence, date_naissance) VALUES (3, 1, 'Leriche', 'Marc', '4;10', '01 23 45 67 89', '01 98 76 54 32', '15/06/1985');

INSERT INTO db_plongeur (id_palanque, id_fiche_securite, nom, prenom, aptitudes, telephone, telephone_urgence, date_naissance) VALUES (4, 2, 'Lacour', 'Myriam', '4;10', '01 23 45 67 89', '01 98 76 54 32', '30/07/1974');
INSERT INTO db_plongeur (id_palanque, id_fiche_securite, nom, prenom, aptitudes, telephone, telephone_urgence, date_naissance) VALUES (4, 2, 'Girault', 'Stéphane', '4;10', '01 23 45 67 89', '01 98 76 54 32', '20/11/1967');
INSERT INTO db_plongeur (id_palanque, id_fiche_securite, nom, prenom, aptitudes, telephone, telephone_urgence, date_naissance) VALUES (4, 2, 'Leriche', 'Marc', '4;10', '01 23 45 67 89', '01 98 76 54 32', '15/06/1985');
