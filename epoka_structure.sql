-- phpMyAdmin SQL Dump
-- Base de données : epoka
-- Serveur : localhost

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Création de la base de données
--
CREATE DATABASE IF NOT EXISTS `epoka` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `epoka`;

-- --------------------------------------------------------

--
-- Structure de la table `ville`
--
CREATE TABLE `ville` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `code_postal` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `agence`
--
CREATE TABLE `agence` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse` varchar(255) NOT NULL,
  `id_ville` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_ville` (`id_ville`),
  CONSTRAINT `fk_agence_ville` FOREIGN KEY (`id_ville`) REFERENCES `ville` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `salarie`
--
CREATE TABLE `salarie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fonction` varchar(100) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `peut_valider` tinyint(1) NOT NULL DEFAULT 0,
  `peut_payer` tinyint(1) NOT NULL DEFAULT 0,
  `id_agence` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_agence` (`id_agence`),
  CONSTRAINT `fk_salarie_agence` FOREIGN KEY (`id_agence`) REFERENCES `agence` (`id`) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `mission`
--
CREATE TABLE `mission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `intitule` varchar(255) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `id_ville_depart` int(11) NOT NULL,
  `id_ville_arrivee` int(11) NOT NULL,
  `id_salarie` int(11) NOT NULL,
  `statut` enum('brouillon','validee','payee') NOT NULL DEFAULT 'brouillon',
  PRIMARY KEY (`id`),
  KEY `id_ville_depart` (`id_ville_depart`),
  KEY `id_ville_arrivee` (`id_ville_arrivee`),
  KEY `id_salarie` (`id_salarie`),
  CONSTRAINT `fk_mission_ville_arrivee` FOREIGN KEY (`id_ville_arrivee`) REFERENCES `ville` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_mission_ville_depart` FOREIGN KEY (`id_ville_depart`) REFERENCES `ville` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_mission_salarie` FOREIGN KEY (`id_salarie`) REFERENCES `salarie` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `distance`
--
CREATE TABLE `distance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_ville_depart` int(11) NOT NULL,
  `id_ville_arrivee` int(11) NOT NULL,
  `km` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_ville_depart` (`id_ville_depart`),
  KEY `id_ville_arrivee` (`id_ville_arrivee`),
  CONSTRAINT `fk_distance_ville_arrivee` FOREIGN KEY (`id_ville_arrivee`) REFERENCES `ville` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_distance_ville_depart` FOREIGN KEY (`id_ville_depart`) REFERENCES `ville` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `parametre`
--
CREATE TABLE `parametre` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prix_km` decimal(10,2) NOT NULL DEFAULT 0.50,
  `prix_hebergement` decimal(10,2) NOT NULL DEFAULT 80.00,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Données par défaut (Paramètres, Ville Paris, Agence de base, Admin)
--
INSERT INTO `parametre` (`id`, `prix_km`, `prix_hebergement`) VALUES (1, '0.50', '80.00');

INSERT INTO `ville` (`id`, `nom`, `code_postal`) VALUES (1, 'Paris', '75000');
INSERT INTO `agence` (`id`, `adresse`, `id_ville`) VALUES (1, 'Siège social', 1);

-- Note: Le mot de passe ici est le hash de 'admin'
INSERT INTO `salarie` (`id`, `fonction`, `nom`, `prenom`, `mot_de_passe`, `peut_valider`, `peut_payer`, `id_agence`) VALUES
(1, 'Administrateur', 'Admin', 'Admin', '$2y$10$wT0/eK/E6J8P2.K7f91hLu4.L0256tE//7o.FqZ8DkVU/.u8tV.Iq', 1, 1, 1);

COMMIT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
