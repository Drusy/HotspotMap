-- phpMyAdmin SQL Dump
-- version 4.0.6deb1
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Dim 09 Mars 2014 à 23:15
-- Version du serveur: 5.5.35-0ubuntu0.13.10.2
-- Version de PHP: 5.5.3-1ubuntu2.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `HotspotMap`
--

-- --------------------------------------------------------

--
-- Structure de la table `Comment`
--

CREATE TABLE IF NOT EXISTS `Comment` (
  `id` varchar(255) NOT NULL,
  `content` varchar(1024) NOT NULL,
  `author` varchar(255) NOT NULL,
  `place` varchar(255) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `creation_date` datetime NOT NULL,
  `validated` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `Comment`
--

INSERT INTO `Comment` (`id`, `content`, `author`, `place`, `avatar`, `creation_date`, `validated`) VALUES
('1', 'commentaire de test', 'Florian', '1', NULL, '2014-03-07 00:00:00', 1),
('531cad6fabb6a', 'hello', 'flo', '531b158ae3c18', '', '2014-03-09 06:05:35', 1),
('531caed5b811d', 'Le mcDo c''est trop cool', 'flo', '1', '', '2014-03-09 06:11:33', 1),
('531cb6623077c', 'message c''est cool les lapinou', 'flo', '1', 'http://cdn.pratique.fr/sites/default/files/articles/lapin-blanc.jpg', '2014-03-09 06:43:46', 1);

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
  `validated` tinyint(1) NOT NULL DEFAULT '0',
  `copy_of` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `Place`
--

INSERT INTO `Place` (`id`, `latitude`, `longitude`, `address`, `country`, `town`, `name`, `website`, `description`, `validated`, `copy_of`) VALUES
('1', 45.763653, 3.134995, 'Rue de l''Oradou', 'France', 'Clermont-Ferrand', 'McDonald''s', 'www.macdonalds.fr', 'C''est pas top Ã§a pue la frite !', 1, NULL),
('531b158ae3c18', 45.600918, 4.087811, '9 impasse de l''agriculture 42600', 'France', 'Montbrison', 'Maison Rotagnon', '', 'wifi gratuit', 1, NULL);

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

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
