-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Mar 06 Janvier 2015 à 17:27
-- Version du serveur :  5.6.21
-- Version de PHP :  5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `deep_blue`
--

-- --------------------------------------------------------

--
-- Structure de la table `db_aide`
--

CREATE TABLE IF NOT EXISTS `db_aide` (
`id_question` mediumint(9) NOT NULL,
  `question` text NOT NULL,
  `reponse` text NOT NULL,
  `tag` text NOT NULL,
  `voir_aussi` text,
  `disponible` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=latin1;

--
-- Contenu de la table `db_aide`
--

INSERT INTO `db_aide` (`id_question`, `question`, `reponse`, `tag`, `voir_aussi`, `disponible`) VALUES
(1, 'Comment ajouter de nouveaux moniteurs ?', 'L&#39;ajout de moniteur se fait dans la partie administration. l&#39;accès à l&#39;administration est réservé aux utilisateurs ayant les droits d&#39;administration correspondants.', 'administration, moniteurs', 'Array', 1),
(2, 'Lorsque je saisis des plongeurs, certains plongeurs sont suggérés, comment cela fonctionne-t-il ?', 'Les 15 derniers plongeurs ajoutés sont proposés en fonction de la saisie en cours (Exemple : Si Jacques Dupond a été ajouté à une plongée hier, si je saisis la lettre &#34;J&#34; dans une fiche aujourd&#39;hui, ce plongeur sera suggéré)', 'fiche, création, plongeurs, suggestions', 'Array', 1),
(3, 'Comment puis-je fais pour changer mon mot de passe ?', 'Il faut contacter l&#39;administrateur en charge d&#39;Oxygen et lui demander une réinitialisation de son mot de passe', 'mot de passe, connexion, compte', 'Array', 1),
(4, 'Que faire si j&#39;oublie mon mot de passe ?', 'Un bouton de mot de passe oublié est disponible sur la page de connexion. Celui-ci permettra de réinitialiser le mot de passe grâce à un lien envoyé sur l&#39;email utilisé pendant l&#39;enregistrement du compte', 'mot de passe, oublie, compte', '3', 1),
(5, 'Que signifie responsable dans la liste des fiches ?', 'Le responsable de la fiche est le directeur de plongée affecté à la fiche de sécurité', 'fiche, responsable, directeur de plongée', '19', 1),
(6, 'Que signifie les nombres dans les colonnes &#34;Palanquées&#34; et &#34;Plongeurs&#34; lorsque je consulte la liste des fiches ?', 'Il s&#39;agit du nombre de personnes dans la palanquées, et du nombre de plongeurs au total sur cette fiche de sécurité', 'fiche, palanquées, plongeurs', '19', 1),
(7, 'Une date est associée à une fiche dans la liste, de quelle date s&#39;agit-il ?', 'Il s&#39;agit de la date pour laquelle est programmé la plongée sous-marine', 'fiche, date', 'Array', 1),
(8, 'Comment puis-je fais pour consulter une fiche dans la liste ?', 'Pour consulter une fiche, il faut cliquer sur la loupe présente devant la fiche concernée', 'fiche, consultation', '9', 1),
(9, 'Comment puis-je modifier une fiche dans la liste ?', 'Pour modifier une fiche il faut cliquer sur le crayon présent devant la fiche conernée', 'fiche, modification', '12', 1),
(10, 'Comment puis-je supprimer une fiche dans la liste ?', 'Vous pouvez supprimer une fiche en cliquant sur la croix présente devant la fiche concernée', 'fiche, suppression', '13', 1),
(11, 'Comment puis-je rechercher une fiche particulière parmis la liste ?', 'Une case &#34;Rechercher&#34; située en haut à gauche de la liste des fiche permet de rechercher une liste particulière', 'fiche, recherche', 'Array', 1),
(12, 'Comment puis-je modifier une fiche que je suis en train de consulter ?', 'Un bouton violet &#34;Modifier la fiche&#34; en bas de page permet de modifier la fiche en cours de consultation', 'fiche, modification', '9', 1),
(13, 'Comment puis-je supprimer une fiche que je suis en train de consulter ?', 'Un bouton rouge &#34;Supprimer la fiche&#34; en bas de page permet de supprimer la fiche en cours de consultation', 'fiche, suppression', '10', 1),
(14, 'Certains plongeurs sont de couleurs différentes, qu&#39;est-ce que cela signifie ?', 'Les plongeurs sont inscrit en violet, et le moniteur en rouge', 'fiche, plongeurs, couleurs', 'Array', 1),
(15, 'Que signifie le champs type de plongée ?', 'Le type de plongée définie les conditions dans lesquelles la palanquée va plonger : Autonome (Exploration entre plongeurs), Encadré (Exploration avec un moniteur), Technique (Dans le but de valider un niveau de plongée), ou Baptême (pour initier de nouveax plongeurs à la plongée sous-marine)', 'fiche, type plongées', 'Array', 1),
(16, 'Comment se lit le temps d&#39;immersion prévue ?', 'Le temps d&#39;immersion est inscrit en minutes (Exemple : 20 veut dire 20 minutes)', 'fiche, plongées, immersion, durée', 'Array', 1),
(17, 'Que signifie le champs Gaz ?', 'Il s&#39;agit du gaz qui sera utilisé pour que les plongeurs respirent sous l&#39;eau (souvent de l&#39;Air, mais parfois du Nitrox)', 'fiche, plongées, gaz', 'Array', 1),
(18, 'Qu&#39;est-ce que l&#39;historique bas de page de la consultation d&#39;une fiche', 'Il s&#39;agit d&#39;un récapitulatif des modifications apportées à cette fiche depuis sa création', 'fiche, historique', 'Array', 1),
(19, 'Comment créer une fiche ?', 'Il suffit dans la barre horizontale bleue de navigation de cliquer sur &#34;Ajouter une fiche&#34;', 'fiche, création', 'Array', 1),
(20, 'Comment sont organisée les informations de la fiche pendant la création ?', 'Les informations générales apparaissent directement à l&#39;écran, puis pour chaque détails de palanquée il faut aller dans les onglets de la palanquée souhaitée', 'fiche, création', '19', 1),
(21, 'Comment créer une palanquée ?', 'Il faut cliquer sur le bouton bleu &#34;Ajouter une palanquée&#34; en bas de la création / modification de la fiche', 'fiche, création, palanquées', '19', 1),
(22, 'Comment supprimer une palanquée ?', 'Il faut cliquer sur le bouton rouge  &#34;Supprimer cette palanquée&#34; dans l&#39;onglet de la palanquée en question', 'fiche, palanquées, suppression', 'Array', 1),
(23, 'Je n&#39;arrive pas à enregistrer ma fiche de sécurité ?', 'Assurer vous d&#39;avoir bien remplie les informations de la fiche de sécurité (Le directeur de plongée, et l&#39;embarcation sont obligatoire pour enregistrer la fiche) et que les palanquées soit correctement constituées (pas trop de plongeurs, que les plongeurs aient les aptitudes requises, vérifier les profondeurs).', 'fiche, enregistrement, règles de gestion', '24', 1),
(24, 'Un bandeau rouge m&#39;informe qu&#39;il y a une erreur, comment puis-je la corriger ?', 'Les bandeaux rouges informent souvent de la cause de l&#39;erreur, il s&#39;agit le plus souvent d&#39;incohérence dans la saisie d&#39;une fiche par rapport aux règles de gestion (exemple : trop de plongeurs dans la palanquée, ou quand le moniteur n&#39;a pas les aptitudes nécéssaires pour encadrer cette palanquée). Corriger ces erreurs et vous pourrez enregistrer la fiche.', 'fiche, enregistrement, règles de gestion', '23', 1),
(25, 'A quoi correspond une fiche archivée ?', 'Il s&#39;agit d&#39;une fiche correspondant à une plongée déjà effectuée et validé', 'fiche, archives', 'Array', 1);

--
-- Index pour les tables exportées
--

--
-- Index pour la table `db_aide`
--
ALTER TABLE `db_aide`
 ADD PRIMARY KEY (`id_question`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `db_aide`
--
ALTER TABLE `db_aide`
MODIFY `id_question` mediumint(9) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=26;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
