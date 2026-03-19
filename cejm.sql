-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 19 mars 2026 à 16:25
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `cejm`
--

-- --------------------------------------------------------

--
-- Structure de la table `notion`
--

DROP TABLE IF EXISTS `notion`;
CREATE TABLE IF NOT EXISTS `notion` (
  `id_notion` int NOT NULL AUTO_INCREMENT,
  `mot` varchar(100) NOT NULL,
  `definition` varchar(600) NOT NULL,
  PRIMARY KEY (`id_notion`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `notion`
--

INSERT INTO `notion` (`id_notion`, `mot`, `definition`) VALUES
(11, 'Parties prenantes', 'Acteurs ou groupe d\'acteurs ayant une influence sur l\'activité de l\'entreprise'),
(12, 'Finalité', 'Raison d\'être d\'une entreprise'),
(13, 'Finalité économique', 'Production de biens et de services'),
(14, 'Finalité financière', 'Réaliser un Chiffre d\'Affaires'),
(15, 'Finalité sociale', 'Engagement de l\'organisation pour les collaborateurs afin de permettre un bien-être au sein de l\'entreprise'),
(16, 'Finalité environnementale', 'Engagement de l\'organisation à respecter les normes environnementales'),
(17, 'RSE (Responsabilité Sociétale des Entreprises', 'Démarche mise en place par une entreprise respectant des normes sociales, économiques et environnementales.'),
(18, 'Performance', 'La capacité d\'une entreprise à atteindre ses objectifs en étant la plus efficace possible'),
(19, 'Performance économique', 'Elle est mesurée avec la rentabilité de l\'entreprise'),
(20, 'Performance sociale', 'Elle est mesurée grâce au climat social de l\'entreprise'),
(21, 'Performance environnementale', 'Elle est mesurée grâce au respect de l\'entreprise des mesures environnementales.'),
(22, 'Entrepreneur', 'C\'est celui qui dirige l\'entreprise. Il prend des risques à travers la mise en place de nouvelles stratégies évaluées grâce à l\'analyse de l\'environnement micro/macro'),
(23, 'Manageur', 'Il gère des équipes en faisant figure d\'autorité, il fait le lien entre l\'entrepreneur et le salarié et il exécute les stratégies mises en place par l\'entrepreneur'),
(24, 'Période précontractuelle', 'C\'est la phase qui précède la conclusion d\'un contrat. Elle est une étape de négociation et de réflexion, où les parties échangent des informations.'),
(25, 'Obligation d\'information', 'Durant la période précontractuelle, il est obligatoire de fournir les éléments nécessaires pour une décision éclairée.'),
(26, 'Obligation de confidentialité', 'Durant la période précontractuelle, il est obligatoire de ne pas divulguer les informations échangées (NDA)'),
(27, 'Obligation de loyauté', 'Durant la période précontractuelle, il est obligatoire de négocier de bonne foi, sans tromperie.'),
(28, 'Contrat', 'Accord juridique de volonté entre le contractant et le co-contractant'),
(29, 'Politique conjoncturelle', 'C\'est une politique de court/moyen terme qui peut être monétaire ou budgétaire'),
(30, 'Politique structurelle', 'C\'est une politique de long terme.'),
(31, 'Carré de Kaldor', 'C\'est un principe qui est respecté si il y a à la fois :\r\n- une stabilité de la croissance économique\r\n- une stabilité de l\'emploi\r\n- une stabilité des prix\r\n- un équilibre exterieur');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
