-- phpMyAdmin SQL Dump
-- version 4.2.13
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 27, 2015 at 08:53 AM
-- Server version: 5.5.39-MariaDB
-- PHP Version: 5.5.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `PROD_sxa`
--

-- --------------------------------------------------------

--
-- Table structure for table `etudiants`
--

CREATE TABLE IF NOT EXISTS `etudiants` (
`id_etu` int(10) unsigned NOT NULL,
  `id_cont_etu` int(10) unsigned NOT NULL,
  `id_manager_etu` int(10) unsigned NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `etudiants`
--

INSERT INTO `etudiants` (`id_etu`, `id_cont_etu`, `id_manager_etu`) VALUES
(1, 109605, 109605),
(2, 109618, 109618),
(3, 109639, 109639);

-- --------------------------------------------------------

--
-- Table structure for table `formateur`
--

CREATE TABLE IF NOT EXISTS `formateur` (
`id_formateur` int(10) unsigned NOT NULL,
  `id_cont_formateur` int(10) unsigned NOT NULL,
  `initial_formateur` varchar(32) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `formateur`
--

INSERT INTO `formateur` (`id_formateur`, `id_cont_formateur`, `initial_formateur`) VALUES
(1, 135586, 'MG'),
(2, 135587, 'HQ'),
(3, 135588, 'FM'),
(4, 135589, 'JR');

-- --------------------------------------------------------

--
-- Table structure for table `formation_centre`
--

CREATE TABLE IF NOT EXISTS `formation_centre` (
`id_centre` int(10) unsigned NOT NULL,
  `id_cont_centre` int(10) unsigned NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `formation_centre`
--

INSERT INTO `formation_centre` (`id_centre`, `id_cont_centre`) VALUES
(1, 135585);

-- --------------------------------------------------------

--
-- Table structure for table `formation_cours`
--

CREATE TABLE IF NOT EXISTS `formation_cours` (
`id_cours` int(4) unsigned NOT NULL,
  `name_cours` varchar(32) DEFAULT NULL,
  `cursus_id_cours` varchar(32) NOT NULL,
  `partner_id_cours` varchar(32) NOT NULL,
  `prix_cours` varchar(32) DEFAULT NULL,
  `url_token_cours` varchar(32) DEFAULT NULL,
  `title_cours` varchar(32) DEFAULT NULL,
  `title_s_cours` varchar(32) DEFAULT NULL,
  `title_l_cours` varchar(32) DEFAULT NULL,
  `img_cours` varchar(256) DEFAULT NULL,
  `img_s_cours` varchar(256) DEFAULT NULL,
  `img_l_cours` varchar(256) DEFAULT NULL,
  `desc_cours` text,
  `desc_s_cours` text,
  `desc_l_cours` text
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `formation_cours`
--

INSERT INTO `formation_cours` (`id_cours`, `name_cours`, `cursus_id_cours`, `partner_id_cours`, `prix_cours`, `url_token_cours`, `title_cours`, `title_s_cours`, `title_l_cours`, `img_cours`, `img_s_cours`, `img_l_cours`, `desc_cours`, `desc_s_cours`, `desc_l_cours`) VALUES
(1, 'EX200', 'redhat_infra_rhcsa', 'redhat', '2000', 'ex200', 'EX200 Rapid Track', 'EX200 Rapid Track', 'EX200 Rapid Track', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'Le cours RHCSA Rapid Track (RH199) se rapporte à Red Hat Enterprise Linux 7 est conçu pour les étudiants qui ont déjà une expérience significative de l''administration Linux. Le cours passe en revue les tâches etudiées dans le cours d''administration système de Red Hat I (RH124) et II (RH134) avec un rythme accéléré.', 'Le cours RHCSA Rapid Track (RH199) se rapporte à Red Hat Enterprise Linux 7 est conçu pour les étudiants qui ont déjà une expérience significative de l''administration Linux. Le cours passe en revue les tâches etudiées dans le cours d''administration système de Red Hat I (RH124) et II (RH134) avec un rythme accéléré.', 'Le cours RHCSA Rapid Track (RH199) se rapporte à Red Hat Enterprise Linux 7 est conçu pour les étudiants qui ont déjà une expérience significative de l''administration Linux. Le cours passe en revue les tâches etudiées dans le cours d''administration système de Red Hat I (RH124) et II (RH134) avec un rythme accéléré.'),
(2, 'RH200', 'redhat_infra_rhcsa', 'redhat', '2000', 'rh200', 'RHCSA Rapid Track', 'RHCSA Rapid Track', 'RHCSA Rapid Track', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'Le cours RHCSA Rapid Track (RH199) se rapporte à Red Hat Enterprise Linux 7 est conçu pour les étudiants qui ont déjà une expérience significative de l''administration Linux. Le cours passe en revue les tâches etudiées dans le cours d''administration système de Red Hat I (RH124) et II (RH134) avec un rythme accéléré.', 'Le cours RHCSA Rapid Track (RH199) se rapporte à Red Hat Enterprise Linux 7 est conçu pour les étudiants qui ont déjà une expérience significative de l''administration Linux. Le cours passe en revue les tâches etudiées dans le cours d''administration système de Red Hat I (RH124) et II (RH134) avec un rythme accéléré.', 'Le cours RHCSA Rapid Track (RH199) se rapporte à Red Hat Enterprise Linux 7 est conçu pour les étudiants qui ont déjà une expérience significative de l''administration Linux. Le cours passe en revue les tâches etudiées dans le cours d''administration système de Red Hat I (RH124) et II (RH134) avec un rythme accéléré.'),
(3, 'RH199', 'redhat_infra_rhcsa', 'redhat', '2000', 'rh199', 'RHCSA Rapid Track', 'RHCSA Rapid Track', 'RHCSA Rapid Track', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'Le cours RHCSA Rapid Track (RH199) se rapporte à Red Hat Enterprise Linux 7 est conçu pour les étudiants qui ont déjà une expérience significative de l''administration Linux. Le cours passe en revue les tâches etudiées dans le cours d''administration système de Red Hat I (RH124) et II (RH134) avec un rythme accéléré.', 'Le cours RHCSA Rapid Track (RH199) se rapporte à Red Hat Enterprise Linux 7 est conçu pour les étudiants qui ont déjà une expérience significative de l''administration Linux. Le cours passe en revue les tâches etudiées dans le cours d''administration système de Red Hat I (RH124) et II (RH134) avec un rythme accéléré.', 'Le cours RHCSA Rapid Track (RH199) se rapporte à Red Hat Enterprise Linux 7 est conçu pour les étudiants qui ont déjà une expérience significative de l''administration Linux. Le cours passe en revue les tâches etudiées dans le cours d''administration système de Red Hat I (RH124) et II (RH134) avec un rythme accéléré.'),
(4, 'RH134', 'redhat_infra_rhcsa', 'redhat', '2000', 'ex134', 'RH134 Exemple', 'RH134 Exemple', 'RH134 Exemple', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'Description RH134 Exemple', 'Description RH134 Exemple', 'Description RH134 Exemple'),
(5, 'RH124', 'redhat_infra_rhcsa', 'redhat', '2000', 'rh124', 'RH124 Exemple', 'RH124 Exemple', 'RH124 Exemple', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'Description RH124 Exemple', 'Description RH124 Exemple', 'Description RH124 Exemple'),
(6, 'EX300', 'redhat_infra_rhce', 'redhat', '2000', 'ex300', 'EX300 Exemple', 'EX300 Exemple', 'EX300 Exemple', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'Description EX300 Exemple', 'Description EX300 Exemple', 'Description EX300 Exemple'),
(7, 'RH300', 'redhat_infra_rhce', 'redhat', '2000', 'rh300', 'RH300 Exemple', 'RH300 Exemple', 'RH300 Exemple', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'Description RH300 Exemple', 'Description RH300 Exemple', 'Description RH300 Exemple'),
(8, 'RH255', 'redhat_infra_rhce', 'redhat', '2000', 'rh255', 'RH255 Exemple', 'RH255 Exemple', 'RH255 Exemple', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'Description RH255 Exemple', 'Description RH255 Exemple', 'Description RH255 Exemple'),
(9, 'EX403', 'redhat_infra_rhca', 'redhat', '2000', 'ex403', 'Redhat Examen 403 Exemple', 'Redhat Examen 403 Exemple', 'Redhat Examen 403 Exemple', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'Description Redhat Examen 403 Exemple', 'Description Redhat Examen 403 Exemple', 'Description Redhat Examen 403 Exemple'),
(10, 'RH403', 'redhat_infra_rhca', 'redhat', '2000', 'rh403', 'Redhat 403 Exemple', 'Redhat 403 Exemple', 'Redhat 403 Exemple', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'Description Redhat 403 Exemple', 'Description Redhat 403 Exemple', 'Description Redhat 403 Exemple'),
(11, 'JB248', 'jboss_admin', 'jboss', '2000', 'jb248', 'JBoss Exemple', 'JBoss Exemple', 'JBoss Exemple', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'Description JBoss Exemple', 'Description JBoss Exemple', 'Description JBoss Exemple'),
(12, 'AW001', 'aws_paas', 'aws', '2000', 'aw001', 'AWS Exemple', 'AWS Exemple', 'AWS Exemple', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'Description AWS Exemple', 'Description AWS Exemple', 'Description AWS Exemple');

-- --------------------------------------------------------

--
-- Table structure for table `formation_cursus`
--

CREATE TABLE IF NOT EXISTS `formation_cursus` (
`id_cursus` int(4) unsigned NOT NULL,
  `name_cursus` varchar(32) DEFAULT NULL,
  `part_id_cursus` varchar(32) DEFAULT NULL,
  `color_class_cursus` varchar(32) DEFAULT NULL,
  `url_token_cursus` varchar(32) DEFAULT NULL,
  `title_cursus` varchar(32) DEFAULT NULL,
  `title_s_cursus` varchar(32) DEFAULT NULL,
  `title_l_cursus` varchar(32) DEFAULT NULL,
  `img_cursus` varchar(256) DEFAULT NULL,
  `img_s_cursus` varchar(256) DEFAULT NULL,
  `img_l_cursus` varchar(256) DEFAULT NULL,
  `desc_cursus` text,
  `desc_s_cursus` text,
  `desc_l_cursus` text
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `formation_cursus`
--

INSERT INTO `formation_cursus` (`id_cursus`, `name_cursus`, `part_id_cursus`, `color_class_cursus`, `url_token_cursus`, `title_cursus`, `title_s_cursus`, `title_l_cursus`, `img_cursus`, `img_s_cursus`, `img_l_cursus`, `desc_cursus`, `desc_s_cursus`, `desc_l_cursus`) VALUES
(1, 'jboss_admin', 'jboss', 'event-inverse', 'administrateur', 'JBoss Certified Administrator', 'JBoss JBCAA', 'JBCAA - JBoss Certified Applicat', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'Un professionnel de l''informatique qui a passé avec succès la certification administrateur système Red Hat (RHCSA) dispose des compétences de base en administration système pour effectuer les tâches requises dans les environnements Red Hat Enterprise Linux. Le titre est obtenu après avoir passé avec succès l''examen Certified System Administrator Red Hat (RHCSA)', 'Une certification qui atteste des compétences requise pour administrer un parc de serveurs Red Hat Enterprise Linux', 'Un professionnel de l''informatique qui a passé avec succès la certification administrateur système Red Hat (RHCSA) dispose des compétences de base en administration système pour effectuer les tâches requises dans les environnements Red Hat Enterprise Linux. Le titre est obtenu après avoir passé avec succès l''examen Certified System Administrator Red Hat (RHCSA)'),
(2, 'jboss_dev', 'jboss', 'event-important', 'developpeur', 'JBoss Certified Developpeur', 'JBoss JBCD', 'JBCD - JBoss Certified Applicati', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'Un professionnel de l''informatique qui a passé avec succès la certification administrateur système Red Hat (RHCSA) dispose des compétences de base en administration système pour effectuer les tâches requises dans les environnements Red Hat Enterprise Linux. Le titre est obtenu après avoir passé avec succès l''examen Certified System Administrator Red Hat (RHCSA)', 'Une certification qui atteste des compétences requise pour administrer un parc de serveurs Red Hat Enterprise Linux', 'Un professionnel de l''informatique qui a passé avec succès la certification administrateur système Red Hat (RHCSA) dispose des compétences de base en administration système pour effectuer les tâches requises dans les environnements Red Hat Enterprise Linux. Le titre est obtenu après avoir passé avec succès l''examen Certified System Administrator Red Hat (RHCSA)'),
(3, 'redhat_infra_rhcsa', 'redhat', 'event-info', 'infrastructure_rhcsa', 'Administrateur Linux Certifié', 'RHCSA', 'RHCSA : Administrateur Linux Cer', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'Un professionnel de l''informatique qui a passé avec succès la certification administrateur système Red Hat (RHCSA) dispose des compétences de base en administration système pour effectuer les tâches requises dans les environnements Red Hat Enterprise Linux. Le titre est obtenu après avoir passé avec succès l''examen Certified System Administrator Red Hat (RHCSA)', 'Une certification qui atteste des compétences requise pour administrer un parc de serveurs Red Hat Enterprise Linux', 'Un professionnel de l''informatique qui a passé avec succès la certification administrateur système Red Hat (RHCSA) dispose des compétences de base en administration système pour effectuer les tâches requises dans les environnements Red Hat Enterprise Linux. Le titre est obtenu après avoir passé avec succès l''examen Certified System Administrator Red Hat (RHCSA)'),
(4, 'redhat_infra_rhce', 'redhat', 'event-warning', 'infrastructure_rhce', 'Ingénieur Linux Certifié', 'RHCE', 'RHCE : Ingénieur Linux Certifié ', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'Un professionnel de l''informatique qui a passé avec succès la certification administrateur système Red Hat (RHCSA) dispose des compétences de base en administration système pour effectuer les tâches requises dans les environnements Red Hat Enterprise Linux. Le titre est obtenu après avoir passé avec succès l''examen Certified System Administrator Red Hat (RHCSA)', 'Une certification qui atteste des compétences requise pour administrer un parc de serveurs Red Hat Enterprise Linux', 'Un professionnel de l''informatique qui a passé avec succès la certification administrateur système Red Hat (RHCSA) dispose des compétences de base en administration système pour effectuer les tâches requises dans les environnements Red Hat Enterprise Linux. Le titre est obtenu après avoir passé avec succès l''examen Certified System Administrator Red Hat (RHCSA)'),
(5, 'redhat_infra_rhca', 'redhat', 'event-success', 'infrastructure_rhca', 'Architecte Linux Certifié', 'RHCA', 'RHCA : Architecte Linux Certifié', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'Un professionnel de l''informatique qui a passé avec succès la certification administrateur système Red Hat (RHCSA) dispose des compétences de base en administration système pour effectuer les tâches requises dans les environnements Red Hat Enterprise Linux. Le titre est obtenu après avoir passé avec succès l''examen Certified System Administrator Red Hat (RHCSA)', 'Une certification qui atteste des compétences requise pour administrer un parc de serveurs Red Hat Enterprise Linux', 'Un professionnel de l''informatique qui a passé avec succès la certification administrateur système Red Hat (RHCSA) dispose des compétences de base en administration système pour effectuer les tâches requises dans les environnements Red Hat Enterprise Linux. Le titre est obtenu après avoir passé avec succès l''examen Certified System Administrator Red Hat (RHCSA)'),
(6, 'redhat_cloud', 'redhat', 'event-special', 'cloud', 'Cursus Cloud', 'Cloud', 'Cursus Cloud Openstack', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'Un professionnel de l''informatique qui a passé avec succès la certification administrateur système Red Hat (RHCSA) dispose des compétences de base en administration système pour effectuer les tâches requises dans les environnements Red Hat Enterprise Linux. Le titre est obtenu après avoir passé avec succès l''examen Certified System Administrator Red Hat (RHCSA)', 'Une certification qui atteste des compétences requise pour administrer un parc de serveurs Red Hat Enterprise Linux', 'Un professionnel de l''informatique qui a passé avec succès la certification administrateur système Red Hat (RHCSA) dispose des compétences de base en administration système pour effectuer les tâches requises dans les environnements Red Hat Enterprise Linux. Le titre est obtenu après avoir passé avec succès l''examen Certified System Administrator Red Hat (RHCSA)'),
(7, 'aws_paas', 'aws', 'event-important', 'paas', 'AWS - PAAS', 'PAAS', 'Amazon Web Services - Plateform ', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'Un professionnel de l''informatique qui a passé avec succès la certification administrateur système Red Hat (RHCSA) dispose des compétences de base en administration système pour effectuer les tâches requises dans les environnements Red Hat Enterprise Linux. Le titre est obtenu après avoir passé avec succès l''examen Certified System Administrator Red Hat (RHCSA)', 'Une certification qui atteste des compétences requise pour administrer un parc de serveurs Red Hat Enterprise Linux', 'Un professionnel de l''informatique qui a passé avec succès la certification administrateur système Red Hat (RHCSA) dispose des compétences de base en administration système pour effectuer les tâches requises dans les environnements Red Hat Enterprise Linux. Le titre est obtenu après avoir passé avec succès l''examen Certified System Administrator Red Hat (RHCSA)');

-- --------------------------------------------------------

--
-- Table structure for table `formation_partenaire`
--

CREATE TABLE IF NOT EXISTS `formation_partenaire` (
`id_part` int(4) unsigned NOT NULL,
  `name_part` varchar(32) DEFAULT NULL,
  `url_token_part` varchar(32) DEFAULT NULL,
  `title_part` varchar(32) DEFAULT NULL,
  `title_s_part` varchar(32) DEFAULT NULL,
  `title_l_part` varchar(32) DEFAULT NULL,
  `img_part` varchar(256) DEFAULT NULL,
  `img_s_part` varchar(256) DEFAULT NULL,
  `img_l_part` varchar(256) DEFAULT NULL,
  `desc_part` text,
  `desc_s_part` text,
  `desc_l_part` text
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `formation_partenaire`
--

INSERT INTO `formation_partenaire` (`id_part`, `name_part`, `url_token_part`, `title_part`, `title_s_part`, `title_l_part`, `img_part`, `img_s_part`, `img_l_part`, `desc_part`, `desc_s_part`, `desc_l_part`) VALUES
(1, 'redhat', 'redhat', 'Red Hat', 'Red Hat', 'Red Hat Inc', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'Red Hat est une société américaine éditant des logiciels GNU/Linux. Elle est l''une des rares entreprises exclusivement dédiées aux logiciels Open Source, industrie dont elle est la plus importante et la plus reconnue. L''entreprise est principalement connue pour son produit Red Hat Enterprise Linux, un système d''exploitation destiné aux entreprises.', 'Red Hat est une multinationale américaine principalement connue pour son produit Red Hat Enterprise Linux, un système d''exploitation libre destiné aux entreprises.', 'Red Hat est une société multinationale d''origine américaine éditant des distributions GNU/Linux. Elle est l''une des entreprises dédiées aux logiciels Open Source les plus importantes et les plus reconnues. Elle constitue également le premier distributeur du système d''exploitation GNU/Linux. Red Hat a été fondée en 1993 et son siège social se trouve à Raleigh en Caroline du Nord. Elle possède en plus de ce dernier un nombre important de bureaux dans le monde entier. L''entreprise est principalement connue pour son produit Red Hat Enterprise Linux, un système d''exploitation destiné aux entreprises. Red Hat fournit des plateformes logicielles (système d''exploitation, intergiciel comme JBoss).'),
(2, 'jboss', 'jboss', 'JBoss', 'JBoss', 'Red Hat - JBoss', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'Red Hat est une société américaine éditant des logiciels GNU/Linux. Elle est l''une des rares entreprises exclusivement dédiées aux logiciels Open Source, industrie dont elle est la plus importante et la plus reconnue. L''entreprise est principalement connue pour son produit Red Hat Enterprise Linux, un système d''exploitation destiné aux entreprises.', 'Red Hat est une multinationale américaine principalement connue pour son produit Red Hat Enterprise Linux, un système d''exploitation libre destiné aux entreprises.', 'Red Hat est une société multinationale d''origine américaine éditant des distributions GNU/Linux. Elle est l''une des entreprises dédiées aux logiciels Open Source les plus importantes et les plus reconnues. Elle constitue également le premier distributeur du système d''exploitation GNU/Linux. Red Hat a été fondée en 1993 et son siège social se trouve à Raleigh en Caroline du Nord. Elle possède en plus de ce dernier un nombre important de bureaux dans le monde entier. L''entreprise est principalement connue pour son produit Red Hat Enterprise Linux, un système d''exploitation destiné aux entreprises. Red Hat fournit des plateformes logicielles (système d''exploitation, intergiciel comme JBoss).'),
(3, 'aws', 'aws', 'Amazon AWS', 'AWS', 'AWS - Amazon Web Services', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'Red Hat est une société américaine éditant des logiciels GNU/Linux. Elle est l''une des rares entreprises exclusivement dédiées aux logiciels Open Source, industrie dont elle est la plus importante et la plus reconnue. L''entreprise est principalement connue pour son produit Red Hat Enterprise Linux, un système d''exploitation destiné aux entreprises.', 'Red Hat est une multinationale américaine principalement connue pour son produit Red Hat Enterprise Linux, un système d''exploitation libre destiné aux entreprises.', 'Red Hat est une société multinationale d''origine américaine éditant des distributions GNU/Linux. Elle est l''une des entreprises dédiées aux logiciels Open Source les plus importantes et les plus reconnues. Elle constitue également le premier distributeur du système d''exploitation GNU/Linux. Red Hat a été fondée en 1993 et son siège social se trouve à Raleigh en Caroline du Nord. Elle possède en plus de ce dernier un nombre important de bureaux dans le monde entier. L''entreprise est principalement connue pour son produit Red Hat Enterprise Linux, un système d''exploitation destiné aux entreprises. Red Hat fournit des plateformes logicielles (système d''exploitation, intergiciel comme JBoss).');

-- --------------------------------------------------------

--
-- Table structure for table `formation_session`
--

CREATE TABLE IF NOT EXISTS `formation_session` (
`id_session` int(4) unsigned NOT NULL,
  `cours_id_session` varchar(32) DEFAULT NULL,
  `trainer_id_session` int(4) DEFAULT NULL,
  `location_id_session` int(4) DEFAULT NULL,
  `event_id_session` varchar(512) DEFAULT NULL,
  `prix_session` varchar(32) DEFAULT NULL,
  `status_session` varchar(32) DEFAULT NULL,
  `allday_session` tinyint(1) DEFAULT '0',
  `start_session` int(4) DEFAULT NULL,
  `end_session` int(4) DEFAULT NULL,
  `classroom_session` varchar(128) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `formation_session`
--

INSERT INTO `formation_session` (`id_session`, `cours_id_session`, `trainer_id_session`, `location_id_session`, `event_id_session`, `prix_session`, `status_session`, `allday_session`, `start_session`, `end_session`, `classroom_session`) VALUES
(1, 'RH200', 2, 1, 'startx.fr_4eo69kq9vpt75a813vtn3mfv3s@group.calendar.google.com#cjq29lnmv1hpieceu0j7tlv5r0', '2000', 'none', 1, 1391385600, 1391817600, 'salle 1'),
(2, 'EX300', 2, 1, 'startx.fr_4eo69kq9vpt75a813vtn3mfv3s@group.calendar.google.com#dis4u0tmgqbdjeeg53jhe1tbh4', '2000', 'none', NULL, 1394784000, 1394798400, 'salle 1'),
(3, 'RH300', 2, 1, 'startx.fr_4eo69kq9vpt75a813vtn3mfv3s@group.calendar.google.com#9hcj1v2s45i3j05nk9q1ssb7hs', '2000', 'none', 1, 1394409600, 1394841600, 'salle 1'),
(4, 'RH414', 2, 1, 'startx.fr_4eo69kq9vpt75a813vtn3mfv3s@group.calendar.google.com#a4ki67rev95bk0tlcemn5k977s', '2000', 'none', 1, 1396224000, 1396656000, 'salle 1'),
(5, 'EX413', 2, 1, 'startx.fr_4eo69kq9vpt75a813vtn3mfv3s@group.calendar.google.com#8gke2scte33fj2lfakqjoqqa3o', '2000', 'none', NULL, 1396594800, 1396612800, 'salle 1'),
(6, 'EX200', 1, 1, 'startx.fr_4eo69kq9vpt75a813vtn3mfv3s@group.calendar.google.com#h2o9aud6brqd9s81hj3rolpi0k', '2000', 'none', NULL, 1400223600, 1400234400, 'salle 1'),
(7, 'EX300', 4, 1, 'startx.fr_4eo69kq9vpt75a813vtn3mfv3s@group.calendar.google.com#iaqnf6dv10qjgoajfdlj6vnq6g', '2000', 'none', NULL, 1400830200, 1400837400, 'salle 1'),
(8, 'RH199', 3, 1, 'startx.fr_4eo69kq9vpt75a813vtn3mfv3s@group.calendar.google.com#shg234is39bijpttqem7il34c0', '2000', 'none', 1, 1399852800, 1400198400, 'salle 1'),
(9, 'RH242', 3, 1, 'startx.fr_4eo69kq9vpt75a813vtn3mfv3s@group.calendar.google.com#m5d4918iqh8dmmt2alord8fk14', '2000', 'none', 1, 1402272000, 1402617600, 'salle 1'),
(10, 'EX436', 1, 1, 'startx.fr_4eo69kq9vpt75a813vtn3mfv3s@group.calendar.google.com#vjq1cuag0ge67cdpc5490m1ofc', '2000', 'none', NULL, 1403251200, 1403265600, 'salle 1'),
(11, 'RH436', 1, 1, 'startx.fr_4eo69kq9vpt75a813vtn3mfv3s@group.calendar.google.com#o5tut4u4ae3vpe06op740bi0b0', '2000', 'none', 1, 1402876800, 1403222400, 'salle 1'),
(12, 'EX200', 2, 1, 'startx.fr_4eo69kq9vpt75a813vtn3mfv3s@group.calendar.google.com#3nlf97g1uu8l6tebs3vbbdkc74', '2000', 'none', NULL, 1403856000, 1403863200, 'salle 1'),
(13, 'RH200', 4, 1, 'startx.fr_4eo69kq9vpt75a813vtn3mfv3s@group.calendar.google.com#gg05aci5kuco5cusq274ljm1vs', '2000', 'none', 1, 1403481600, 1403827200, 'salle 1'),
(14, 'RH134', 3, 1, 'startx.fr_4eo69kq9vpt75a813vtn3mfv3s@group.calendar.google.com#1epb1nl2o8tu4e9ola020vava0', '2000', 'none', 1, 1404691200, 1405036800, 'salle 1'),
(15, 'EX200', 3, 1, 'startx.fr_4eo69kq9vpt75a813vtn3mfv3s@group.calendar.google.com#hf9gbunvi46arc487orlagkpf8', '2000', 'none', NULL, 1405063800, 1405072800, 'salle 1'),
(16, 'RH237', 3, 1, 'startx.fr_4eo69kq9vpt75a813vtn3mfv3s@group.calendar.google.com#c1brt6ekihehtjeido92781sag', '2000', 'none', 1, 1405382400, 1405641600, 'salle 1'),
(17, 'RH254', 3, 1, 'startx.fr_4eo69kq9vpt75a813vtn3mfv3s@group.calendar.google.com#2rf34fflu7m6eb2dvvni0oe1rs', '2000', 'none', 1, 1405900800, 1406246400, 'salle 1'),
(18, 'EX200', 3, 1, 'startx.fr_4eo69kq9vpt75a813vtn3mfv3s@group.calendar.google.com#chn763n8qv6ffbqfec8vupmbr4', '2000', 'none', NULL, 1406273400, 1406280600, 'salle 1'),
(19, 'EX442', 3, 1, 'startx.fr_4eo69kq9vpt75a813vtn3mfv3s@group.calendar.google.com#5iq0gtecoequc9liqo8u93a6k4', '2000', 'none', NULL, 1406878200, 1406892600, 'salle 1'),
(20, 'RH300', 3, 1, 'startx.fr_4eo69kq9vpt75a813vtn3mfv3s@group.calendar.google.com#v13q779cbd6jo6s2d7sgpkk1n0', '2000', 'none', 1, 1406505600, 1406937600, 'salle 1'),
(21, 'RH200', 3, 1, 'startx.fr_4eo69kq9vpt75a813vtn3mfv3s@group.calendar.google.com#v5m0qdd64g7dhpu0t96npfiv18', '2000', 'none', 1, 1410134400, 1410566400, 'salle 1'),
(22, 'EX200', 3, 1, 'startx.fr_4eo69kq9vpt75a813vtn3mfv3s@group.calendar.google.com#hre38lh9us3pppf06ln19fao5c', '2000', 'none', NULL, 1410507000, 1410516000, 'salle 1'),
(23, 'RH318', 3, 1, 'startx.fr_4eo69kq9vpt75a813vtn3mfv3s@group.calendar.google.com#2oqrdvvkuekr6thm3e91ocvkoc', '2000', 'none', 1, 1410739200, 1411084800, 'salle 1'),
(24, 'EX318', 3, 1, 'startx.fr_4eo69kq9vpt75a813vtn3mfv3s@group.calendar.google.com#l24btuoicdp5avom13b4qsp0nc', '2000', 'none', NULL, 1411111800, 1411122600, 'salle 1'),
(25, 'RH401', 1, 1, 'startx.fr_4eo69kq9vpt75a813vtn3mfv3s@group.calendar.google.com#g9tkcvll22b5gg7fbmao2madec', '2000', 'none', 1, 1411344000, 1411689600, 'salle 1'),
(26, 'CL210', 2, 1, 'startx.fr_4eo69kq9vpt75a813vtn3mfv3s@group.calendar.google.com#t1v6nfu93431t9c4i94q3ih528', '2000', 'none', 1, 1411948800, 1412294400, 'salle 1'),
(27, 'EX200', 2, 1, 'startx.fr_4eo69kq9vpt75a813vtn3mfv3s@group.calendar.google.com#pulbq8161jdj23gj7qikdq4158', '2000', 'none', NULL, 1412926200, 1412935200, 'salle 1'),
(28, 'RH200', 2, 1, 'startx.fr_4eo69kq9vpt75a813vtn3mfv3s@group.calendar.google.com#ta2geomo7pl50b9uq46445p1ck', '2000', 'none', 1, 1412553600, 1412985600, 'salle 1'),
(29, 'EX401', 1, 1, 'startx.fr_4eo69kq9vpt75a813vtn3mfv3s@group.calendar.google.com#jukj06rg7k1j4q3me215572ojo', '2000', 'none', NULL, 1411715700, 1411730100, 'salle 1'),
(30, 'RH134', 3, 1, 'startx.fr_4eo69kq9vpt75a813vtn3mfv3s@group.calendar.google.com#gb1353vhnsr57spkckh0qftn2g', '2000', 'none', 1, 1413158400, 1413504000, 'salle 1'),
(31, 'EX200', 3, 1, 'startx.fr_4eo69kq9vpt75a813vtn3mfv3s@group.calendar.google.com#088m3h90j0rdpib33ojsih9d80', '2000', 'none', NULL, 1413531000, 1413540000, 'salle 1'),
(32, 'EX210', 1, 1, 'startx.fr_4eo69kq9vpt75a813vtn3mfv3s@group.calendar.google.com#0p271428l765i0l33fktasld6s', '2000', 'none', 1, 1412294400, 1412380800, 'salle 1'),
(33, 'EX200', 3, 1, 'startx.fr_4eo69kq9vpt75a813vtn3mfv3s@group.calendar.google.com#v1pnfd3ngkm2kpdeoohcbtusnk', '2000', 'none', NULL, 1415349000, 1415358000, 'salle 1'),
(34, 'RH200', 3, 1, 'startx.fr_4eo69kq9vpt75a813vtn3mfv3s@group.calendar.google.com#is756sum3i9tct6kdld7st3k38', '2000', 'none', 1, 1414972800, 1415318400, 'salle 1'),
(35, 'RH124', 3, 1, 'startx.fr_4eo69kq9vpt75a813vtn3mfv3s@group.calendar.google.com#r67or4r0m2mofpacb6hnqojv4s', '2000', 'none', 1, 1416182400, 1416614400, 'salle 1'),
(36, 'RH442', 2, 1, 'startx.fr_4eo69kq9vpt75a813vtn3mfv3s@group.calendar.google.com#5s3odur642gb919mpscmhmhhq0', '2000', 'none', 1, 1417392000, 1417737600, 'salle 1'),
(37, 'EX200', 3, 1, 'startx.fr_4eo69kq9vpt75a813vtn3mfv3s@group.calendar.google.com#q4f74mmpus720bm4j14jap688o', '2000', 'none', NULL, 1417768200, 1417777200, 'salle 1'),
(38, 'EX210', 3, 1, 'startx.fr_4eo69kq9vpt75a813vtn3mfv3s@group.calendar.google.com#mmnpjr1bf8m8crl0gtk1aqdt7g', '2000', 'none', NULL, 1417163400, 1417174200, 'salle 1'),
(40, 'EX200', 2, 1, 'startx.fr_4eo69kq9vpt75a813vtn3mfv3s@group.calendar.google.com#msrhidqklp04b9mij0qchktago', '2000', 'none', NULL, 1418371200, 1418380200, 'salle 1'),
(41, 'RH200', 2, 1, 'startx.fr_4eo69kq9vpt75a813vtn3mfv3s@group.calendar.google.com#9au7rmdm9euavn460dasohb1jc', '2000', 'none', 1, 1417996800, 1418342400, 'salle 1');

-- --------------------------------------------------------

--
-- Table structure for table `formation_techno`
--

CREATE TABLE IF NOT EXISTS `formation_techno` (
`id_techno` int(4) unsigned NOT NULL,
  `name_techno` varchar(32) DEFAULT NULL,
  `url_token_techno` varchar(32) DEFAULT NULL,
  `title_techno` varchar(32) DEFAULT NULL,
  `title_s_techno` varchar(32) DEFAULT NULL,
  `title_l_techno` varchar(32) DEFAULT NULL,
  `img_techno` varchar(256) DEFAULT NULL,
  `img_s_techno` varchar(256) DEFAULT NULL,
  `img_l_techno` varchar(256) DEFAULT NULL,
  `desc_techno` text,
  `desc_s_techno` text,
  `desc_l_techno` text
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `formation_techno`
--

INSERT INTO `formation_techno` (`id_techno`, `name_techno`, `url_token_techno`, `title_techno`, `title_s_techno`, `title_l_techno`, `img_techno`, `img_s_techno`, `img_l_techno`, `desc_techno`, `desc_s_techno`, `desc_l_techno`) VALUES
(1, 'kickstart', 'kickstart', 'Kickstart', 'Kickstart', 'Kickstart', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'desc Kickstart', 'desc Kickstart', 'desc Kickstart'),
(2, 'kvm', 'kvm', 'KVM', 'KVM', 'KVM', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'desc KVM', 'desc KVM', 'desc KVM'),
(3, 'smb', 'smb', 'Samba', 'Samba', 'Samba', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'desc Samba', 'desc Samba', 'desc Samba'),
(4, 'nfs', 'nfs', 'NFS', 'NFS', 'NFS', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'http://lorempixel.com/400/200', 'desc NFS', 'desc NFS', 'desc NFS');

-- --------------------------------------------------------

--
-- Table structure for table `join_cours_techno`
--

CREATE TABLE IF NOT EXISTS `join_cours_techno` (
  `id_cote` varchar(32) NOT NULL,
  `id_cours_cote` varchar(32) NOT NULL,
  `id_techno_cote` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `join_cours_techno`
--

INSERT INTO `join_cours_techno` (`id_cote`, `id_cours_cote`, `id_techno_cote`) VALUES
('AW001-kickstart', 'AW001', 'kickstart'),
('AW001-kvm', 'AW001', 'kvm'),
('AW001-nfs', 'AW001', 'nfs'),
('AW001-smb', 'AW001', 'smb'),
('EX200-kickstart', 'EX200', 'kickstart'),
('EX200-kvm', 'EX200', 'kvm'),
('EX200-nfs', 'EX200', 'nfs'),
('EX200-smb', 'EX200', 'smb'),
('EX300-kickstart', 'EX300', 'kickstart'),
('EX300-kvm', 'EX300', 'kvm'),
('EX300-nfs', 'EX300', 'nfs'),
('EX300-smb', 'EX300', 'smb'),
('EX403-kickstart', 'EX403', 'kickstart'),
('EX403-kvm', 'EX403', 'kvm'),
('EX403-nfs', 'EX403', 'nfs'),
('EX403-smb', 'EX403', 'smb'),
('JB248-kickstart', 'JB248', 'kickstart'),
('JB248-kvm', 'JB248', 'kvm'),
('JB248-nfs', 'JB248', 'nfs'),
('JB248-smb', 'JB248', 'smb'),
('RH124-kickstart', 'RH124', 'kickstart'),
('RH124-kvm', 'RH124', 'kvm'),
('RH124-nfs', 'RH124', 'nfs'),
('RH124-smb', 'RH124', 'smb'),
('RH134-kickstart', 'RH134', 'kickstart'),
('RH134-kvm', 'RH134', 'kvm'),
('RH134-nfs', 'RH134', 'nfs'),
('RH134-smb', 'RH134', 'smb'),
('RH199-kickstart', 'RH199', 'kickstart'),
('RH199-kvm', 'RH199', 'kvm'),
('RH199-nfs', 'RH199', 'nfs'),
('RH199-smb', 'RH199', 'smb'),
('RH200-kickstart', 'RH200', 'kickstart'),
('RH200-kvm', 'RH200', 'kvm'),
('RH200-nfs', 'RH200', 'nfs'),
('RH200-smb', 'RH200', 'smb'),
('RH255-kickstart', 'RH255', 'kickstart'),
('RH255-kvm', 'RH255', 'kvm'),
('RH255-nfs', 'RH255', 'nfs'),
('RH255-smb', 'RH255', 'smb'),
('RH300-kickstart', 'RH300', 'kickstart'),
('RH300-kvm', 'RH300', 'kvm'),
('RH300-nfs', 'RH300', 'nfs'),
('RH300-smb', 'RH300', 'smb'),
('RH403-kickstart', 'RH403', 'kickstart'),
('RH403-kvm', 'RH403', 'kvm'),
('RH403-nfs', 'RH403', 'nfs'),
('RH403-smb', 'RH403', 'smb');

-- --------------------------------------------------------

--
-- Table structure for table `join_session_etudiant`
--

CREATE TABLE IF NOT EXISTS `join_session_etudiant` (
  `id_setu` varchar(32) NOT NULL,
  `id_session_setu` int(4) NOT NULL,
  `id_etudiant_setu` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `join_session_etudiant`
--

INSERT INTO `join_session_etudiant` (`id_setu`, `id_session_setu`, `id_etudiant_setu`) VALUES
('1-1', 1, 1),
('1-2', 1, 2),
('1-3', 1, 3),
('10-1', 10, 1),
('10-2', 10, 2),
('10-3', 10, 3),
('11-1', 11, 1),
('11-2', 11, 2),
('11-3', 11, 3),
('12-1', 12, 1),
('12-2', 12, 2),
('12-3', 12, 3),
('13-1', 13, 1),
('13-2', 13, 2),
('13-3', 13, 3),
('14-1', 14, 1),
('14-2', 14, 2),
('14-3', 14, 3),
('15-1', 15, 1),
('15-2', 15, 2),
('15-3', 15, 3),
('16-1', 16, 1),
('16-2', 16, 2),
('16-3', 16, 3),
('17-1', 17, 1),
('17-2', 17, 2),
('17-3', 17, 3),
('18-1', 18, 1),
('18-2', 18, 2),
('18-3', 18, 3),
('19-1', 19, 1),
('19-2', 19, 2),
('19-3', 19, 3),
('2-1', 2, 1),
('2-2', 2, 2),
('2-3', 2, 3),
('20-1', 20, 1),
('20-2', 20, 2),
('20-3', 20, 3),
('21-1', 21, 1),
('21-2', 21, 2),
('21-3', 21, 3),
('22-1', 22, 1),
('22-2', 22, 2),
('22-3', 22, 3),
('23-1', 23, 1),
('23-2', 23, 2),
('23-3', 23, 3),
('24-1', 24, 1),
('24-2', 24, 2),
('24-3', 24, 3),
('25-1', 25, 1),
('25-2', 25, 2),
('25-3', 25, 3),
('26-1', 26, 1),
('26-2', 26, 2),
('26-3', 26, 3),
('27-1', 27, 1),
('27-2', 27, 2),
('27-3', 27, 3),
('28-1', 28, 1),
('28-2', 28, 2),
('28-3', 28, 3),
('29-1', 29, 1),
('29-2', 29, 2),
('29-3', 29, 3),
('3-1', 3, 1),
('3-2', 3, 2),
('3-3', 3, 3),
('30-1', 30, 1),
('30-2', 30, 2),
('30-3', 30, 3),
('31-1', 31, 1),
('31-2', 31, 2),
('31-3', 31, 3),
('32-1', 32, 1),
('32-2', 32, 2),
('32-3', 32, 3),
('33-1', 33, 1),
('33-2', 33, 2),
('33-3', 33, 3),
('34-1', 34, 1),
('34-2', 34, 2),
('34-3', 34, 3),
('35-1', 35, 1),
('35-2', 35, 2),
('35-3', 35, 3),
('36-1', 36, 1),
('36-2', 36, 2),
('36-3', 36, 3),
('37-1', 37, 1),
('37-2', 37, 2),
('37-3', 37, 3),
('38-1', 38, 1),
('38-2', 38, 2),
('38-3', 38, 3),
('4-1', 4, 1),
('4-2', 4, 2),
('4-3', 4, 3),
('40-1', 40, 1),
('40-2', 40, 2),
('40-3', 40, 3),
('41-1', 41, 1),
('41-2', 41, 2),
('41-3', 41, 3),
('5-1', 5, 1),
('5-2', 5, 2),
('5-3', 5, 3),
('6-1', 6, 1),
('6-2', 6, 2),
('6-3', 6, 3),
('7-1', 7, 1),
('7-2', 7, 2),
('7-3', 7, 3),
('8-1', 8, 1),
('8-2', 8, 2),
('8-3', 8, 3),
('9-1', 9, 1),
('9-2', 9, 2),
('9-3', 9, 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `etudiants`
--
ALTER TABLE `etudiants`
 ADD PRIMARY KEY (`id_etu`);

--
-- Indexes for table `formateur`
--
ALTER TABLE `formateur`
 ADD PRIMARY KEY (`id_formateur`);

--
-- Indexes for table `formation_centre`
--
ALTER TABLE `formation_centre`
 ADD PRIMARY KEY (`id_centre`);

--
-- Indexes for table `formation_cours`
--
ALTER TABLE `formation_cours`
 ADD PRIMARY KEY (`id_cours`);

--
-- Indexes for table `formation_cursus`
--
ALTER TABLE `formation_cursus`
 ADD PRIMARY KEY (`id_cursus`);

--
-- Indexes for table `formation_partenaire`
--
ALTER TABLE `formation_partenaire`
 ADD PRIMARY KEY (`id_part`);

--
-- Indexes for table `formation_session`
--
ALTER TABLE `formation_session`
 ADD PRIMARY KEY (`id_session`);

--
-- Indexes for table `formation_techno`
--
ALTER TABLE `formation_techno`
 ADD PRIMARY KEY (`id_techno`);

--
-- Indexes for table `join_cours_techno`
--
ALTER TABLE `join_cours_techno`
 ADD PRIMARY KEY (`id_cote`), ADD UNIQUE KEY `id_cote` (`id_cote`);

--
-- Indexes for table `join_session_etudiant`
--
ALTER TABLE `join_session_etudiant`
 ADD PRIMARY KEY (`id_setu`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `etudiants`
--
ALTER TABLE `etudiants`
MODIFY `id_etu` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `formateur`
--
ALTER TABLE `formateur`
MODIFY `id_formateur` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `formation_centre`
--
ALTER TABLE `formation_centre`
MODIFY `id_centre` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `formation_cours`
--
ALTER TABLE `formation_cours`
MODIFY `id_cours` int(4) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT for table `formation_cursus`
--
ALTER TABLE `formation_cursus`
MODIFY `id_cursus` int(4) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `formation_partenaire`
--
ALTER TABLE `formation_partenaire`
MODIFY `id_part` int(4) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `formation_session`
--
ALTER TABLE `formation_session`
MODIFY `id_session` int(4) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=42;
--
-- AUTO_INCREMENT for table `formation_techno`
--
ALTER TABLE `formation_techno`
MODIFY `id_techno` int(4) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
