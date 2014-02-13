-- phpMyAdmin SQL Dump
-- version 4.0.6
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Jeu 13 Février 2014 à 21:38
-- Version du serveur: 5.5.33
-- Version de PHP: 5.5.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Base de données: `HotspotMap`
--

-- --------------------------------------------------------

--
-- Structure de la table `Place`
--

CREATE TABLE IF NOT EXISTS `Place` (
  `id` varchar(255) NOT NULL,
  `latitude` varchar(255) NOT NULL,
  `longitude` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `town` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `website` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `Place`
--

INSERT INTO `Place` (`id`, `latitude`, `longitude`, `address`, `country`, `town`, `name`, `website`) VALUES
('0', '2.34706', '48.858859', '29 Rue du Faubourg Saint-Antoine', 'France', 'Paris', 'Stabucks', 'www.starbucks.fr'),
('1', '3.134995', '45.763653', 'Rue de l''Oradou', 'France', 'Clermont-Ferrand', 'McDonald''s', 'www.macdonalds.fr');

-- --------------------------------------------------------

--
-- Structure de la table `PlacesUsers`
--

CREATE TABLE IF NOT EXISTS `PlacesUsers` (
  `idPlace` varchar(255) NOT NULL,
  `idUser` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `User`
--

CREATE TABLE IF NOT EXISTS `User` (
  `id` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `pseudo` varchar(255) NOT NULL,
  `website` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `User`
--

INSERT INTO `User` (`id`, `firstname`, `lastname`, `email`, `pseudo`, `website`) VALUES
('0', 'Kevin', 'Renella', 'kevin.renella@gmail.com', 'Drusy', 'www.filesdnd.com'),
('1', 'Florian', 'Rotagnon', 'florian.rotagnon@gmail.com', 'Loof42', '');
