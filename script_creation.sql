DROP TABLE IF EXISTS db_historique;
DROP TABLE IF EXISTS db_utilisateur;
DROP TABLE IF EXISTS db_plongeur;
DROP TABLE IF EXISTS db_palanque;
DROP TABLE IF EXISTS db_fiche_securite;
DROP TABLE IF EXISTS db_moniteur;
DROP TABLE IF EXISTS db_aptitude;


CREATE TABLE db_aptitude(
    id_aptitude MEDIUMINT NOT NULL AUTO_INCREMENT,
    libelle_court varchar(20) NOT NULL,
    libelle_long text NOT NULL DEFAULT '',
    technique_max INT NOT NULL DEFAULT 0,
    encadree_max INT NOT NULL DEFAULT 0,
    autonome_max INT NOT NULL DEFAULT 0,
    nitrox_max INT NOT NULL DEFAULT 0,
    ajout_max INT NOT NULL DEFAULT 0,
    enseignement_air_max INT NOT NULL DEFAULT 0,
    enseignement_nitrox_max INT NOT NULL DEFAULT 0,
    encadremement_max INT NOT NULL DEFAULT 0,
    version MEDIUMINT NOT NULL DEFAULT 0,
    CONSTRAINT pk_db_aptitude PRIMARY KEY (id_aptitude)
);
/*Ajout des aptitudes en fin de fichier*/

CREATE TABLE db_utilisateur (
    login varchar(20) NOT NULL,
    nom varchar(20) NOT NULL,
    prenom varchar(20) NOT NULL,
    mot_de_passe varchar(32) NOT NULL,
    administrateur boolean NOT NULL DEFAULT FALSE,
    email varchar(40) NOT NULL,
    actif boolean NOT NULL DEFAULT TRUE,
    version MEDIUMINT NOT NULL DEFAULT 0,
    CONSTRAINT pk_db_utilisateur PRIMARY KEY (login)
);
/*Ajout des utilisateurs*/
INSERT INTO db_utilisateur (login,nom, prenom, mot_de_passe, administrateur, email, actif) VALUES ('admin','admin', 'admin', '21232f297a57a5a743894a0e4a801fc3', TRUE, 'raphael.bideau@gmail.com', true);
INSERT INTO db_utilisateur (login,nom, prenom, mot_de_passe, administrateur, email, actif) VALUES ('test','test', 'test', '098f6bcd4621d373cade4e832627b4f6', FALSE, 'raphael.bideau@gmail.com', true);
DROP TABLE IF EXISTS db_embarcation;

CREATE TABLE db_embarcation (
    id_embarcation MEDIUMINT NOT NULL AUTO_INCREMENT,
    libelle varchar(20) NOT NULL,
    maxpersonne int NOT NULL DEFAULT 0,
    commentaire text,
    disponible boolean NOT NULL DEFAULT TRUE,
    version MEDIUMINT NOT NULL DEFAULT 0,
    CONSTRAINT pk_db_embarcation PRIMARY KEY (id_embarcation)
);
/*Ajout des embarcations*/
INSERT INTO db_embarcation (libelle, maxpersonne, commentaire, disponible) VALUES ('EMB-1', 25, 'Embarcation-1, disponible', true);
INSERT INTO db_embarcation (libelle, maxpersonne, commentaire, disponible) VALUES ('EMB-2', 14, 'Embarcation-2, indisponible', false);

CREATE TABLE db_moniteur (
    id_moniteur MEDIUMINT NOT NULL AUTO_INCREMENT,
    nom varchar(20) NOT NULL,
    prenom varchar(20) NOT NULL,
    aptitudes varchar(20) NOT NULL DEFAULT '',
    actif boolean NOT NULL DEFAULT TRUE,
    directeur_plonge boolean NOT NULL DEFAULT FALSE,
    email varchar(40),
    telephone text,
    version MEDIUMINT NOT NULL DEFAULT 0,
    CONSTRAINT pk_db_moniteur PRIMARY KEY (id_moniteur)
);
INSERT INTO db_moniteur (nom, prenom, aptitudes, actif, directeur_plonge, email, telephone) VALUES ('Tomas', 'Bessiere', '5;15', TRUE, FALSE, 'tomas.bessiere@email.com', '01 23 45 67 89');
INSERT INTO db_moniteur (nom, prenom, aptitudes, actif, directeur_plonge, email, telephone) VALUES ('François ', 'Simonet', '5;15;11', TRUE, FALSE, 'francois.simonet@email.com', '01 23 45 67 89');
INSERT INTO db_moniteur (nom, prenom, aptitudes, actif, directeur_plonge, email, telephone) VALUES ('Pierre', 'Saunier', '16;5', TRUE, TRUE, 'pierre.saunier@email.com', '01 23 45 67 89');
INSERT INTO db_moniteur (nom, prenom, aptitudes, actif, directeur_plonge, email, telephone) VALUES ('Eric', 'Delaunay', '21;11', TRUE, TRUE, 'eric.delaunay@email.com', '01 23 45 67 89');

CREATE TABLE db_fiche_securite (
    id_fiche_securite MEDIUMINT NOT NULL AUTO_INCREMENT,
    id_embarcation MEDIUMINT NOT NULL,
    id_directeur_plonge MEDIUMINT NOT NULL,
    timestamp BIGINT NOT NULL,
    site varchar(20) NOT NULL,
    etat varchar(20) NOT NULL,
    version INT NOT NULL DEFAULT 0,
    CONSTRAINT pk_db_fiche_securite PRIMARY KEY (id_fiche_securite),
    CONSTRAINT fk_db_moniteur FOREIGN KEY (id_directeur_plonge) REFERENCES db_moniteur(id_moniteur),
    CONSTRAINT fk_db_embarcation FOREIGN KEY (id_embarcation) REFERENCES db_embarcation(id_embarcation)
);
/* Ajout des fiches de sécurité */
INSERT INTO db_fiche_securite (id_embarcation, id_directeur_plonge, timestamp, site, etat) 
    VALUES (1, 3, 1412931600, 'La plage sur mer', 'CREER');
INSERT INTO db_fiche_securite (id_embarcation, id_directeur_plonge, timestamp, site, etat) 
    VALUES (1, 4, 1412935200, 'Le grand bleu', 'ARCHIVE');

CREATE TABLE db_historique(
    login_utilisateur varchar(20) NOT NULL,
    timestamp BIGINT NOT NULL,
    id_fiche_securite MEDIUMINT,
    source varchar(20) NOT NULL DEFAULT 'WEB',
    commentaire text NOT NULL DEFAULT '',
    CONSTRAINT pk_db_historique PRIMARY KEY (login_utilisateur, timestamp),
    CONSTRAINT fk_db_utilisateur FOREIGN KEY (login_utilisateur) REFERENCES db_utilisateur(login),
    CONSTRAINT fk_db_fiche_securite_historique FOREIGN KEY (id_fiche_securite) REFERENCES db_fiche_securite(id_fiche_securite)
);
/* Ajout des historiques */
INSERT INTO db_historique (login_utilisateur, timestamp, id_fiche_securite, source, commentaire) VALUES ('admin', UNIX_TIMESTAMP(), 1, 'WEB', 'Création de la fiche de sécurité');
INSERT INTO db_historique (login_utilisateur, timestamp, id_fiche_securite, source, commentaire) VALUES ('test', UNIX_TIMESTAMP(), 2, 'WEB', 'Création de la fiche de sécurité');

CREATE TABLE db_palanque (
    id_palanque MEDIUMINT NOT NULL AUTO_INCREMENT,
    id_fiche_securite MEDIUMINT NOT NULL,
    id_moniteur MEDIUMINT DEFAULT NULL,
    numero MEDIUMINT NOT NULL,
    type_plonge varchar(20) NOT NULL,
    type_gaz varchar(20) NOT NULL,
    profondeur_prevue DECIMAL,
    profondeur_realisee DECIMAL,
    duree_prevue INT,
    duree_realisee INT,
    version MEDIUMINT NOT NULL DEFAULT 0,
    CONSTRAINT pk_db_palanque PRIMARY KEY (id_palanque),
    CONSTRAINT fk_db_fiche_securite_palanque FOREIGN KEY (id_fiche_securite) REFERENCES db_fiche_securite(id_fiche_securite)
);
/*Ajout palanque*/
INSERT INTO db_palanque (id_fiche_securite, id_moniteur, numero, type_plonge, type_gaz, profondeur_prevue, profondeur_realisee, duree_prevue, duree_realisee)
    VALUES (1, 1, 1, 'TECHNIQUE', 'AIR', 12, NULL, 900, NULL);
INSERT INTO db_palanque (id_fiche_securite, id_moniteur, numero, type_plonge, type_gaz, profondeur_prevue, profondeur_realisee, duree_prevue, duree_realisee)
    VALUES (1, 2, 2, 'ENCADRE', 'NITROX', 60, NULL, 2700, NULL);
INSERT INTO db_palanque (id_fiche_securite, id_moniteur, numero, type_plonge, type_gaz, profondeur_prevue, profondeur_realisee, duree_prevue, duree_realisee)
    VALUES (1, NULL, 3, 'AUTONOME', 'AIR', 25, NULL, 1800, NULL);
INSERT INTO db_palanque (id_fiche_securite, id_moniteur, numero, type_plonge, type_gaz, profondeur_prevue, profondeur_realisee, duree_prevue, duree_realisee)
    VALUES (2, NULL, 1, 'AUTONOME', 'AIR', 25, NULL, 1800, NULL);

CREATE TABLE db_plongeur (
    id_plongeur MEDIUMINT NOT NULL AUTO_INCREMENT,
    id_palanque MEDIUMINT NOT NULL,
    id_fiche_securite MEDIUMINT NOT NULL,
    nom varchar(20) NOT NULL,
    prenom varchar(20) NOT NULL,
    aptitudes varchar(20) NOT NULL DEFAULT '',
    telephone text,
    telephone_urgence text,
    date_naissance varchar(20) NOT NULL,
    version MEDIUMINT NOT NULL DEFAULT 0,
    CONSTRAINT pk_db_plongeur PRIMARY KEY (id_plongeur),
    CONSTRAINT fk_db_palanque FOREIGN KEY (id_palanque) REFERENCES db_palanque(id_palanque),
    CONSTRAINT fk_db_fiche_securite FOREIGN KEY (id_fiche_securite) REFERENCES db_fiche_securite(id_fiche_securite)
);
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

/*Ajout des aptitudes*/
INSERT INTO db_aptitude (id_aptitude, libelle_court, libelle_long, technique_max, encadree_max, autonome_max, nitrox_max, ajout_max, enseignement_air_max, enseignement_nitrox_max, encadremement_max) 
                 VALUES (1, 'Débutant', 'Débutant', 6, 0, 0, 0, 0, 0, 0, 0);
/*PA*/
INSERT INTO db_aptitude (id_aptitude, libelle_court, libelle_long, technique_max, encadree_max, autonome_max, nitrox_max, ajout_max, enseignement_air_max, enseignement_nitrox_max, encadremement_max) 
                 VALUES (2, 'PA-12', 'Plongé autonome 12m', 12, 12, 12, 0, 0, 0, 0, 0);
INSERT INTO db_aptitude (id_aptitude, libelle_court, libelle_long, technique_max, encadree_max, autonome_max, nitrox_max, ajout_max, enseignement_air_max, enseignement_nitrox_max, encadremement_max) 
                 VALUES (3, 'PA-20', 'Plongé autonome 20m', 12, 12, 12, 0, 0, 0, 0, 0);
INSERT INTO db_aptitude (id_aptitude, libelle_court, libelle_long, technique_max, encadree_max, autonome_max, nitrox_max, ajout_max, enseignement_air_max, enseignement_nitrox_max, encadremement_max) 
                 VALUES (4, 'PA-40', 'Plongé autonome 40m', 40, 40, 40, 0, 0, 0, 0, 0);
INSERT INTO db_aptitude (id_aptitude, libelle_court, libelle_long, technique_max, encadree_max, autonome_max, nitrox_max, ajout_max, enseignement_air_max, enseignement_nitrox_max, encadremement_max) 
                 VALUES (5, 'PA-60', 'Plongé autonome 60m', 60, 60, 60, 0, 0, 0, 0, 0);
/*PE*/
INSERT INTO db_aptitude (id_aptitude, libelle_court, libelle_long, technique_max, encadree_max, autonome_max, nitrox_max, ajout_max, enseignement_air_max, enseignement_nitrox_max, encadremement_max) 
                 VALUES (6, 'PE-12', 'Plongé encadré', 12, 12, 0, 0, 0, 0, 0, 0);
INSERT INTO db_aptitude (id_aptitude, libelle_court, libelle_long, technique_max, encadree_max, autonome_max, nitrox_max, ajout_max, enseignement_air_max, enseignement_nitrox_max, encadremement_max) 
                 VALUES (7, 'PE-20', 'Plongé encadré', 12, 12, 0, 0, 0, 0, 0, 0);
INSERT INTO db_aptitude (id_aptitude, libelle_court, libelle_long, technique_max, encadree_max, autonome_max, nitrox_max, ajout_max, enseignement_air_max, enseignement_nitrox_max, encadremement_max) 
                 VALUES (8, 'PE-40', 'Plongé encadré', 40, 40, 0, 0, 0, 0, 0, 0);
INSERT INTO db_aptitude (id_aptitude, libelle_court, libelle_long, technique_max, encadree_max, autonome_max, nitrox_max, ajout_max, enseignement_air_max, enseignement_nitrox_max, encadremement_max) 
                 VALUES (9, 'PE-60', 'Plongé encadré', 60, 60, 0, 0, 0, 0, 0, 0);
/*Nitrox*/
INSERT INTO db_aptitude (id_aptitude, libelle_court, libelle_long, technique_max, encadree_max, autonome_max, nitrox_max, ajout_max, enseignement_air_max, enseignement_nitrox_max, encadremement_max) 
                 VALUES (10, 'PN-20', 'Plongé au nitrox 20m', 0, 0, 0, 20, 0, 0, 0, 0);   
INSERT INTO db_aptitude (id_aptitude, libelle_court, libelle_long, technique_max, encadree_max, autonome_max, nitrox_max, ajout_max, enseignement_air_max, enseignement_nitrox_max, encadremement_max) 
                 VALUES (11, 'PN-C', 'Plongé au nitrox confirmée', 0, 0, 0, 60, 0, 0, 0, 0);
/*Enseignement*/
INSERT INTO db_aptitude (id_aptitude, libelle_court, libelle_long, technique_max, encadree_max, autonome_max, nitrox_max, ajout_max, enseignement_air_max, enseignement_nitrox_max, encadremement_max) 
                 VALUES (12, 'E-1', 'Enseignement niveau 1, BPJEPS plongée, Stagiaire BPJEPS plongée', 0, 0, 0, 0, 0, 6, 0, 6);
INSERT INTO db_aptitude (id_aptitude, libelle_court, libelle_long, technique_max, encadree_max, autonome_max, nitrox_max, ajout_max, enseignement_air_max, enseignement_nitrox_max, encadremement_max) 
                 VALUES (13, 'E-2', 'Enseignement niveau 2, Stagiaire BEES 1 plongée', 0, 0, 0, 0, 0, 20, 20, 20);
INSERT INTO db_aptitude (id_aptitude, libelle_court, libelle_long, technique_max, encadree_max, autonome_max, nitrox_max, ajout_max, enseignement_air_max, enseignement_nitrox_max, encadremement_max) 
                 VALUES (14, 'E-3', 'Enseignement niveau 3, BEES 1 plongée Stagiaire, DEJEPS plongée Stagiaire, DESJEPS plongée', 0, 0, 0, 0, 0, 40, 40, 40);
INSERT INTO db_aptitude (id_aptitude, libelle_court, libelle_long, technique_max, encadree_max, autonome_max, nitrox_max, ajout_max, enseignement_air_max, enseignement_nitrox_max, encadremement_max) 
                 VALUES (15, 'E-4', 'Enseignement niveau 4, BEES 2 plongée, DEJEPS plongée, DESJEPS plongée', 0, 0, 0, 0, 0, 60, 60, 60);
/*GP*/
INSERT INTO db_aptitude (id_aptitude, libelle_court, libelle_long, technique_max, encadree_max, autonome_max, nitrox_max, ajout_max, enseignement_air_max, enseignement_nitrox_max, encadremement_max) 
                 VALUES (16, 'GP', 'Guide de palanqué, BPJEPS plongée, Stagiaire BPJEPS plongée', 0, 0, 0, 0, 40, 0, 0, 40);
/*P*/
INSERT INTO db_aptitude (id_aptitude, libelle_court, libelle_long, technique_max, encadree_max, autonome_max, nitrox_max, ajout_max, enseignement_air_max, enseignement_nitrox_max, encadremement_max) 
                 VALUES (17, 'P-1', 'Plongé niveau 1', 20, 20, 0, 0, 0, 0, 0, 0);
INSERT INTO db_aptitude (id_aptitude, libelle_court, libelle_long, technique_max, encadree_max, autonome_max, nitrox_max, ajout_max, enseignement_air_max, enseignement_nitrox_max, encadremement_max) 
                 VALUES (18, 'P-1 a', 'Plongé niveau 1 autonome', 20, 20, 12, 0, 0, 0, 0, 0);
INSERT INTO db_aptitude (id_aptitude, libelle_court, libelle_long, technique_max, encadree_max, autonome_max, nitrox_max, ajout_max, enseignement_air_max, enseignement_nitrox_max, encadremement_max) 
                 VALUES (19, 'P-2', 'Plongé niveau 2', 40, 40, 20, 0, 0, 0, 0, 0);
INSERT INTO db_aptitude (id_aptitude, libelle_court, libelle_long, technique_max, encadree_max, autonome_max, nitrox_max, ajout_max, enseignement_air_max, enseignement_nitrox_max, encadremement_max) 
                 VALUES (20, 'P-3', 'Plongé niveau 3', 60, 60, 60, 60, 60, 60, 60, 60);
INSERT INTO db_aptitude (id_aptitude, libelle_court, libelle_long, technique_max, encadree_max, autonome_max, nitrox_max, ajout_max, enseignement_air_max, enseignement_nitrox_max, encadremement_max) 
                 VALUES (21, 'P-4', 'Plongé niveau 4', 60, 60, 60, 0, 40, 0, 0, 40);

/* TABLE AIDE */
DROP TABLE IF EXISTS db_aide;
CREATE TABLE db_aide(
    id_question MEDIUMINT NOT NULL AUTO_INCREMENT,
    question text NOT NULL,
    reponse text NOT NULL,
    tag text NOT NULL DEFAULT '',
    voir_aussi text,
    CONSTRAINT pk_db_aide PRIMARY KEY (id_question)
);
/*questions-réponses*/
INSERT INTO db_aide (question, reponse, tag, voir_aussi) 
    VALUES ('Comment créer de nouveau moniteur ?', 'Il faut ce rendre dans la partie administration','','');
INSERT INTO db_aide (question, reponse, tag, voir_aussi) 
    VALUES ('Quels sont les plongeurs suggérés ?', 'Ce sont les 15 dernier plongeurs','','');