-- phpMyAdmin SQL Dump
-- version 4.0.6
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Mar 25 Février 2014 à 18:45
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
  `latitude` float(10,6) NOT NULL,
  `longitude` float(10,6) NOT NULL,
  `address` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `town` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `website` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `Place`
--

INSERT INTO `Place` (`id`, `latitude`, `longitude`, `address`, `country`, `town`, `name`, `website`, `description`) VALUES
('0', 48.858860, 2.347060, '29 Rue du Faubourg Saint-Antoine', 'France', 'Paris', 'Starbucks', 'www.starbucks.fr', ''),
('1', 45.763653, 3.134995, 'Rue de l''Oradou', 'France', 'Clermont-Ferrand', 'McDonald''s', 'www.macdonalds.fr', '');

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
  `username` varchar(255) NOT NULL,
  `website` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `salt` varchar(255) DEFAULT NULL,
  `roles` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `User`
--

INSERT INTO `User` (`id`, `firstname`, `lastname`, `email`, `username`, `website`, `password`, `salt`, `roles`) VALUES
('530ca80b43457', 'admin_firstname', 'admin_lastname', 'admin@hotspotmap.fr', 'admin', 'http://www.hotspotmap.fr', '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg==', NULL, 'ROLE_ADMIN,ROLE_USER,ROLE_AUTH');
