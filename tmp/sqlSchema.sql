-- MySQL dump 10.13  Distrib 5.1.46, for redhat-linux-gnu (x86_64)
--
-- Host: 127.0.0.1    Database: ZunoDev_sxa
-- ------------------------------------------------------
-- Server version	5.1.46

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `actualite`
--

DROP TABLE IF EXISTS `actualite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `actualite` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `user` varchar(128) NOT NULL,
  `type` varchar(32) NOT NULL,
  `titre` varchar(128) NOT NULL,
  `desc` mediumtext,
  `id_ent` int(4) unsigned DEFAULT NULL,
  `id_cont` int(4) unsigned DEFAULT NULL,
  `id_aff` varchar(12) DEFAULT NULL,
  `status_aff` int(2) unsigned DEFAULT NULL,
  `id_dev` varchar(12) DEFAULT NULL,
  `status_dev` int(2) unsigned DEFAULT NULL,
  `id_cmd` varchar(12) DEFAULT NULL,
  `status_cmd` int(2) unsigned DEFAULT NULL,
  `id_fact` int(4) DEFAULT NULL,
  `status_fact` int(2) unsigned DEFAULT NULL,
  `id_factfourn` varchar(8) DEFAULT NULL,
  `status_factfourn` tinyint(2) DEFAULT NULL,
  `isPublic` enum('0','1') NOT NULL DEFAULT '0',
  `isPublieForClient` enum('0','1') NOT NULL DEFAULT '0',
  `isVisibleFilActu` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `id_ent` (`id_ent`),
  KEY `id_cont` (`id_cont`),
  KEY `id_dev` (`id_dev`),
  KEY `id_aff` (`id_aff`),
  KEY `id_cmd` (`id_cmd`),
  KEY `id_fact` (`id_fact`),
  KEY `date` (`date`),
  KEY `user` (`user`),
  KEY `type` (`type`),
  KEY `date_2` (`date`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `affaire`
--

DROP TABLE IF EXISTS `affaire`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `affaire` (
  `id_aff` varchar(6) NOT NULL,
  `entreprise_aff` int(6) unsigned DEFAULT NULL,
  `contact_aff` int(4) unsigned NOT NULL DEFAULT '0',
  `actif_aff` enum('0','1') NOT NULL DEFAULT '1',
  `archived_aff` enum('0','1') NOT NULL DEFAULT '0',
  `projet_aff` int(4) unsigned DEFAULT NULL,
  `titre_aff` varchar(254) DEFAULT '-- sujet non defini --',
  `modif_aff` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `detect_aff` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status_aff` int(1) NOT NULL DEFAULT '1',
  `desc_aff` text,
  `echeance_aff` timestamp NULL DEFAULT NULL,
  `budget_aff` int(8) DEFAULT NULL,
  `decid_aff` int(4) DEFAULT NULL,
  `comm_aff` text,
  `typeproj_aff` int(2) NOT NULL DEFAULT '0',
  `commercial_aff` varchar(64) NOT NULL DEFAULT 'cl',
  `technique_aff` varchar(64) NOT NULL DEFAULT 'cl',
  `dir_aff` varchar(128) DEFAULT NULL,
  `ren_aff` int(8) DEFAULT NULL,
  PRIMARY KEY (`id_aff`),
  KEY `entreprise_aff` (`entreprise_aff`),
  KEY `contact_aff` (`contact_aff`),
  KEY `actif_aff` (`actif_aff`),
  KEY `archived_aff` (`archived_aff`),
  KEY `detect_aff` (`detect_aff`),
  KEY `status_aff` (`status_aff`),
  KEY `typeproj_aff` (`typeproj_aff`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `appel`
--

DROP TABLE IF EXISTS `appel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appel` (
  `id_app` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `contact_app` int(8) NOT NULL DEFAULT '0',
  `appel_app` date NOT NULL DEFAULT '0000-00-00',
  `rappel_app` date DEFAULT NULL,
  `comm_app` text,
  `heure_app` int(2) DEFAULT NULL,
  `premiercont_app` int(1) NOT NULL DEFAULT '0',
  `relactive_app` int(1) NOT NULL DEFAULT '1',
  `utilisateur_app` varchar(64) NOT NULL DEFAULT 'cl',
  `affaire_app` varchar(32) DEFAULT NULL,
  `projet_app` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id_app`),
  KEY `contact_app` (`contact_app`),
  KEY `rappel_app` (`rappel_app`),
  KEY `premiercont_app` (`premiercont_app`),
  KEY `utilisateur_app` (`utilisateur_app`),
  KEY `appel_app` (`appel_app`),
  KEY `affaire_app` (`affaire_app`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `commande`
--

DROP TABLE IF EXISTS `commande`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commande` (
  `id_cmd` varchar(12) NOT NULL,
  `devis_cmd` varchar(12) DEFAULT NULL,
  `status_cmd` tinyint(2) unsigned NOT NULL DEFAULT '1',
  `titre_cmd` varchar(255) DEFAULT NULL,
  `commercial_cmd` varchar(64) NOT NULL DEFAULT 'cl',
  `sommeHT_cmd` decimal(10,2) NOT NULL DEFAULT '0.00',
  `sommeFHT_cmd` decimal(10,2) NOT NULL DEFAULT '0.00',
  `modereglement_cmd` tinyint(1) DEFAULT '3',
  `condireglement_cmd` tinyint(1) DEFAULT '4',
  `BDCclient_cmd` varchar(64) DEFAULT NULL,
  `entreprise_cmd` int(16) DEFAULT NULL,
  `contact_cmd` varchar(32) NOT NULL DEFAULT '0',
  `contact_achat_cmd` varchar(32) DEFAULT NULL,
  `datemodif_cmd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `daterecord_cmd` timestamp NULL DEFAULT NULL,
  `nomdelivery_cmd` varchar(128) DEFAULT NULL,
  `adressedelivery_cmd` varchar(128) DEFAULT NULL,
  `adresse1delivery_cmd` varchar(128) DEFAULT NULL,
  `villedelivery_cmd` varchar(64) DEFAULT NULL,
  `cpdelivery_cmd` varchar(8) DEFAULT NULL,
  `paysdelivery_cmd` tinyint(3) NOT NULL DEFAULT '1',
  `maildelivery_cmd` varchar(255) DEFAULT NULL,
  `complementdelivery_cmd` varchar(128) DEFAULT NULL,
  `tva_cmd` decimal(3,1) NOT NULL DEFAULT '19.6',
  `ren_cmd` int(8) DEFAULT NULL,
  `commentaire_cmd` text,
  PRIMARY KEY (`id_cmd`),
  KEY `devis_cmd` (`devis_cmd`),
  KEY `status_cmd` (`status_cmd`),
  KEY `commercial_cmd` (`commercial_cmd`),
  KEY `sommeHT_cmd` (`sommeHT_cmd`),
  KEY `sommeFHT_cmd` (`sommeFHT_cmd`),
  KEY `modereglement_cmd` (`modereglement_cmd`),
  KEY `condireglement_cmd` (`condireglement_cmd`),
  KEY `entreprise_cmd` (`entreprise_cmd`),
  KEY `contact_cmd` (`contact_cmd`),
  KEY `contact_achat_cmd` (`contact_achat_cmd`),
  KEY `daterecord_cmd` (`daterecord_cmd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Liste des commandes enregistrées';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `commande_produit`
--

DROP TABLE IF EXISTS `commande_produit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commande_produit` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_commande` varchar(12) NOT NULL,
  `id_produit` varchar(32) NOT NULL DEFAULT '0',
  `desc` varchar(255) DEFAULT NULL,
  `quantite` decimal(8,2) DEFAULT '1.00',
  `quantite_cmd` decimal(8,2) DEFAULT NULL,
  `remise` decimal(4,2) NOT NULL DEFAULT '0.00',
  `prix` decimal(10,2) NOT NULL DEFAULT '0.00',
  `fournisseur` varchar(4) DEFAULT NULL,
  `remiseF` decimal(4,2) NOT NULL DEFAULT '0.00',
  `prixF` decimal(10,2) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fournisseur` (`fournisseur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Detail des produits d''un bon de commande ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contact`
--

DROP TABLE IF EXISTS `contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact` (
  `id_cont` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `entreprise_cont` mediumint(3) unsigned DEFAULT NULL,
  `nom_cont` varchar(32) NOT NULL DEFAULT '',
  `prenom_cont` varchar(32) DEFAULT NULL,
  `civ_cont` varchar(4) NOT NULL DEFAULT 'M.',
  `fonction_cont` int(2) unsigned DEFAULT NULL,
  `LD_cont` varchar(16) DEFAULT NULL,
  `mail_cont` varchar(128) DEFAULT NULL,
  `comm_cont` text,
  `relactive_cont` int(1) NOT NULL DEFAULT '1',
  `add1_cont` varchar(128) DEFAULT NULL,
  `add2_cont` varchar(128) DEFAULT NULL,
  `cp_cont` varchar(8) DEFAULT NULL,
  `ville_cont` varchar(64) DEFAULT NULL,
  `pays_cont` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `tel_cont` varchar(16) DEFAULT NULL,
  `fax_cont` varchar(16) DEFAULT NULL,
  `www_cont` varchar(128) DEFAULT NULL,
  `mob_cont` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`id_cont`),
  KEY `nom_cont` (`nom_cont`),
  KEY `prenom_cont` (`prenom_cont`),
  KEY `fonction_cont` (`fonction_cont`),
  KEY `relactive_cont` (`relactive_cont`),
  KEY `mail_cont` (`mail_cont`),
  KEY `LD_cont` (`LD_cont`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contact_payline`
--

DROP TABLE IF EXISTS `contact_payline`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_payline` (
  `contact_cp` int(11) NOT NULL,
  `nom_cp` varchar(64) NOT NULL,
  `prenom_cp` varchar(32) DEFAULT NULL,
  `wallet_cp` varbinary(50) NOT NULL,
  `cvv_cp` varbinary(4) NOT NULL,
  `date_cp` date DEFAULT NULL,
  `fin_cp` varchar(6) NOT NULL,
  `type_cp` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`contact_cp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `devis`
--

DROP TABLE IF EXISTS `devis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `devis` (
  `id_dev` varchar(12) NOT NULL,
  `affaire_dev` varchar(12) DEFAULT NULL,
  `status_dev` tinyint(2) unsigned NOT NULL DEFAULT '1',
  `titre_dev` varchar(255) DEFAULT NULL,
  `commercial_dev` varchar(64) NOT NULL DEFAULT 'cl',
  `sommeHT_dev` decimal(10,2) NOT NULL DEFAULT '0.00',
  `BDCclient_dev` varchar(64) DEFAULT NULL,
  `entreprise_dev` int(16) DEFAULT NULL,
  `contact_dev` varchar(32) NOT NULL DEFAULT '0',
  `contact_achat_dev` varchar(32) DEFAULT NULL,
  `datemodif_dev` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `daterecord_dev` timestamp NULL DEFAULT NULL,
  `nomdelivery_dev` varchar(128) DEFAULT NULL,
  `adressedelivery_dev` varchar(128) DEFAULT NULL,
  `adresse1delivery_dev` varchar(128) DEFAULT NULL,
  `villedelivery_dev` varchar(64) DEFAULT NULL,
  `cpdelivery_dev` varchar(8) DEFAULT NULL,
  `paysdelivery_dev` int(4) NOT NULL DEFAULT '1',
  `maildelivery_dev` varchar(255) DEFAULT NULL,
  `complementdelivery_dev` varchar(128) DEFAULT NULL,
  `tva_dev` decimal(3,1) DEFAULT NULL,
  `ren_dev` int(8) DEFAULT NULL,
  `commentaire_dev` text,
  PRIMARY KEY (`id_dev`),
  KEY `affaire_dev` (`affaire_dev`),
  KEY `status_dev` (`status_dev`),
  KEY `commercial_dev` (`commercial_dev`),
  KEY `sommeHT_dev` (`sommeHT_dev`),
  KEY `entreprise_dev` (`entreprise_dev`),
  KEY `contact_dev` (`contact_dev`),
  KEY `contact_achat_dev` (`contact_achat_dev`),
  KEY `daterecord_dev` (`daterecord_dev`),
  KEY `datemodif_dev` (`datemodif_dev`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Liste des devis enregistrées';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `devis_produit`
--

DROP TABLE IF EXISTS `devis_produit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `devis_produit` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_devis` varchar(12) NOT NULL,
  `id_produit` varchar(32) NOT NULL DEFAULT '0',
  `desc` varchar(500) DEFAULT NULL,
  `quantite` decimal(8,2) DEFAULT '1.00',
  `remise` decimal(4,2) NOT NULL DEFAULT '0.00',
  `prix` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Detail des produits devisés ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `devis_renew`
--

DROP TABLE IF EXISTS `devis_renew`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `devis_renew` (
  `id_devis` varchar(12) NOT NULL,
  `date_modif` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_record` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_renew` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `cyclerenew` tinyint(2) NOT NULL,
  `devisgenere` varchar(12) DEFAULT NULL,
  PRIMARY KEY (`id_devis`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `entreprise`
--

DROP TABLE IF EXISTS `entreprise`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entreprise` (
  `id_ent` mediumint(3) unsigned NOT NULL AUTO_INCREMENT,
  `type_ent` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `nom_ent` varchar(64) NOT NULL DEFAULT '???',
  `add1_ent` varchar(128) DEFAULT NULL,
  `add2_ent` varchar(128) DEFAULT NULL,
  `cp_ent` varchar(5) DEFAULT NULL,
  `ville_ent` varchar(64) DEFAULT NULL,
  `pays_ent` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `tel_ent` varchar(16) DEFAULT NULL,
  `telsi_ent` varchar(16) DEFAULT NULL,
  `fax_ent` varchar(16) DEFAULT NULL,
  `www_ent` varchar(128) DEFAULT NULL,
  `activite_ent` tinyint(1) unsigned DEFAULT NULL,
  `effectif_ent` varchar(4) DEFAULT NULL,
  `codefourn_ent` varchar(32) DEFAULT NULL,
  `SIRET_ent` varchar(32) DEFAULT NULL,
  `numeroTVA_ent` varchar(32) DEFAULT NULL,
  `tauxTVA_ent` decimal(3,1) NOT NULL DEFAULT '19.6',
  `remise_ent` float DEFAULT '0',
  `groupe_ent` int(4) unsigned DEFAULT NULL,
  `siege_ent` int(1) unsigned DEFAULT NULL,
  `commsociete_ent` text,
  `loginRHN_ent` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id_ent`),
  KEY `nom_ent` (`nom_ent`),
  KEY `add1_ent` (`add1_ent`),
  KEY `cp_ent` (`cp_ent`),
  KEY `ville_ent` (`ville_ent`),
  KEY `pays_ent` (`pays_ent`),
  KEY `activite_ent` (`activite_ent`),
  KEY `groupe_ent` (`groupe_ent`),
  KEY `siege_ent` (`siege_ent`),
  KEY `type_ent` (`type_ent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `facture`
--

DROP TABLE IF EXISTS `facture`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `facture` (
  `id_fact` mediumint(3) unsigned NOT NULL,
  `commande_fact` varchar(12) DEFAULT NULL,
  `status_fact` tinyint(2) unsigned NOT NULL DEFAULT '1',
  `titre_fact` varchar(255) DEFAULT NULL,
  `commercial_fact` varchar(64) NOT NULL DEFAULT 'cl',
  `sommeHT_fact` decimal(10,2) NOT NULL DEFAULT '0.00',
  `modereglement_fact` tinyint(1) DEFAULT '3',
  `condireglement_fact` tinyint(1) DEFAULT '4',
  `commentreglement_fact` varchar(128) DEFAULT NULL,
  `tauxTVA_fact` decimal(3,1) NOT NULL DEFAULT '19.6',
  `numeroTVA_fact` varchar(32) DEFAULT NULL,
  `BDCclient_fact` varchar(64) DEFAULT NULL,
  `entreprise_fact` int(16) DEFAULT NULL,
  `contact_fact` varchar(32) NOT NULL DEFAULT '0',
  `contact_achat_fact` varchar(32) DEFAULT NULL,
  `datemodif_fact` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `daterecord_fact` timestamp NULL DEFAULT NULL,
  `dateenvoi_fact` timestamp NULL DEFAULT NULL,
  `datereglement_fact` timestamp NULL DEFAULT NULL,
  `envoi_fact` enum('0','1') NOT NULL DEFAULT '0',
  `nomentreprise_fact` varchar(128) NOT NULL,
  `add1_fact` varchar(128) DEFAULT NULL,
  `add2_fact` varchar(128) DEFAULT NULL,
  `cp_fact` varchar(8) DEFAULT NULL,
  `ville_fact` varchar(64) DEFAULT NULL,
  `pays_fact` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `file_fact` varchar(128) DEFAULT NULL,
  `type_fact` varchar(32) NOT NULL DEFAULT 'Facture',
  `ren_fact` int(8) DEFAULT NULL,
  `commentaire_fact` text,
  PRIMARY KEY (`id_fact`),
  KEY `commande_fact` (`commande_fact`),
  KEY `status_fact` (`status_fact`),
  KEY `commercial_fact` (`commercial_fact`),
  KEY `sommeHT_fact` (`sommeHT_fact`),
  KEY `modereglement_fact` (`modereglement_fact`),
  KEY `condireglement_fact` (`condireglement_fact`),
  KEY `entreprise_fact` (`entreprise_fact`),
  KEY `contact_fact` (`contact_fact`),
  KEY `contact_achat_fact` (`contact_achat_fact`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Liste des factures enregistrées';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `facture_fournisseur`
--

DROP TABLE IF EXISTS `facture_fournisseur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `facture_fournisseur` (
  `id_factfourn` varchar(10) NOT NULL,
  `titre_factfourn` varchar(128) NOT NULL DEFAULT 'Facture',
  `desc_factfourn` text,
  `montantTTC_factfourn` float(10,2) NOT NULL DEFAULT '0.00',
  `tauxTVA_factfourn` float(4,2) NOT NULL DEFAULT '0.00',
  `dateFact_factfourn` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `datePaye_factfourn` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `dateReglement_factfourn` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modeReglement_factfourn` tinyint(1) NOT NULL DEFAULT '3',
  `status_factfourn` tinyint(1) NOT NULL DEFAULT '1',
  `user_factfourn` varchar(127) NOT NULL,
  `entreprise_factfourn` mediumint(3) DEFAULT NULL,
  `contact_factfourn` int(8) DEFAULT NULL,
  `fichier_factfourn` varchar(255) DEFAULT NULL,
  `ren_factfourn` int(8) DEFAULT NULL,
  PRIMARY KEY (`id_factfourn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `facture_produit`
--

DROP TABLE IF EXISTS `facture_produit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `facture_produit` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_facture` mediumint(3) unsigned NOT NULL,
  `id_produit` varchar(32) NOT NULL DEFAULT '0',
  `desc` varchar(255) DEFAULT NULL,
  `quantite` decimal(8,2) DEFAULT '1.00',
  `remise` decimal(4,2) NOT NULL DEFAULT '0.00',
  `prix` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Detail des produits d''une facture ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fournisseur`
--

DROP TABLE IF EXISTS `fournisseur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fournisseur` (
  `id_fourn` varchar(4) NOT NULL,
  `entreprise_fourn` int(8) unsigned NOT NULL,
  `contactComm_fourn` int(8) unsigned NOT NULL,
  `ContactADV_fourn` int(8) unsigned DEFAULT NULL,
  `contactFact_fourn` int(8) unsigned DEFAULT NULL,
  `BDCCannevas_fourn` varchar(128) DEFAULT NULL,
  `remise_fourn` float(5,2) DEFAULT '0.00',
  `actif` binary(1) DEFAULT '1',
  PRIMARY KEY (`id_fourn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `historique_payline`
--

DROP TABLE IF EXISTS `historique_payline`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `historique_payline` (
  `id_hp` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_hp` varchar(50) DEFAULT NULL,
  `date_hp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type_hp` int(2) NOT NULL,
  `montant_hp` int(10) DEFAULT NULL,
  `devise_hp` int(3) DEFAULT NULL,
  `typeCb_hp` varchar(10) DEFAULT NULL,
  `differe_hp` date DEFAULT NULL,
  `contact_hp` int(11) DEFAULT NULL,
  `codeRetour_hp` int(5) NOT NULL,
  `shortRetour_hp` varchar(50) NOT NULL,
  `longRetour_hp` varchar(255) NOT NULL,
  `fraude_hp` tinyint(1) DEFAULT NULL,
  `doublon_hp` tinyint(1) DEFAULT NULL,
  `autorisation_hp` varchar(6) DEFAULT NULL,
  `dateAutorisation_hp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_hp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log` (
  `id_log` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `date_log` double(13,3) NOT NULL,
  `session_log` varchar(255) DEFAULT NULL,
  `level_log` varchar(16) DEFAULT NULL,
  `component_log` varchar(255) DEFAULT '',
  `nom_log` text,
  `fichier_log` varchar(255) DEFAULT NULL,
  `channel_log` varchar(16) DEFAULT NULL,
  `trace_log` text,
  PRIMARY KEY (`id_log`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `message`
--

DROP TABLE IF EXISTS `message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `message` (
  `id_mess` int(11) NOT NULL AUTO_INCREMENT,
  `titre_mess` varchar(128) NOT NULL DEFAULT 'Message',
  `contenu_mess` text,
  `debut_mess` date NOT NULL,
  `fin_mess` date DEFAULT NULL,
  `lu_mess` tinyint(1) NOT NULL DEFAULT '0',
  `user_mess` varchar(20) NOT NULL,
  PRIMARY KEY (`id_mess`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `module`
--

DROP TABLE IF EXISTS `module`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `module` (
  `nom_mod` varchar(16) NOT NULL,
  `acces_mod` enum('oui','non') NOT NULL DEFAULT 'non'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pontcomptable_histo`
--

DROP TABLE IF EXISTS `pontcomptable_histo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pontcomptable_histo` (
  `id_pcth` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `date_pcth` datetime NOT NULL,
  `nom_pcth` varchar(254) DEFAULT NULL,
  `fichier_pcth` varchar(64) DEFAULT NULL,
  `config_statutFact_pcth` varchar(12) DEFAULT NULL,
  `config_statutFactFourn_pcth` varchar(12) DEFAULT NULL,
  `config_dateDebut_pcth` datetime DEFAULT NULL,
  `config_dateFin_pcth` datetime DEFAULT NULL,
  `config_hasFactureClient_pcth` enum('0','1') NOT NULL DEFAULT '1',
  `config_hasFactureFourn_pcth` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_pcth`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `produit`
--

DROP TABLE IF EXISTS `produit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `produit` (
  `id_prod` varchar(32) NOT NULL DEFAULT '0',
  `nom_prod` varchar(254) NOT NULL DEFAULT '',
  `famille_prod` int(2) unsigned NOT NULL DEFAULT '1',
  `description_prod` text,
  `dureeRenouvellement_prod` tinyint(1) DEFAULT NULL,
  `prix_prod` decimal(10,2) DEFAULT NULL,
  `stock_prod` int(16) NOT NULL DEFAULT '0',
  `remisefournisseur_prod` tinyint(3) NOT NULL DEFAULT '24',
  `bestsell_prod` enum('0','1') NOT NULL DEFAULT '0',
  `stillAvailable_prod` int(1) NOT NULL DEFAULT '1',
  `archi_prod` tinyint(1) DEFAULT NULL,
  `contrat_prod` tinyint(1) DEFAULT NULL,
  `familleredhat_prod` varchar(6) DEFAULT NULL,
  `prodredhat_prod` enum('0','1') DEFAULT NULL,
  `compteComptable_prod` varchar(32) DEFAULT NULL,
  `refExterne` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id_prod`),
  KEY `nom_prod` (`nom_prod`),
  KEY `famille_prod` (`famille_prod`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='liste des produits disponibles dans PEGASE';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `produit_fournisseur`
--

DROP TABLE IF EXISTS `produit_fournisseur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `produit_fournisseur` (
  `produit_id` varchar(32) NOT NULL DEFAULT '0',
  `fournisseur_id` varchar(6) NOT NULL,
  `remiseF` decimal(4,2) NOT NULL DEFAULT '0.00',
  `prixF` varchar(8) DEFAULT NULL,
  `actif` binary(1) DEFAULT '1',
  PRIMARY KEY (`produit_id`,`fournisseur_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `projet`
--

DROP TABLE IF EXISTS `projet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projet` (
  `id_proj` int(4) NOT NULL AUTO_INCREMENT,
  `contact_proj` int(8) NOT NULL DEFAULT '0',
  `appel_proj` int(8) DEFAULT NULL,
  `affaire_proj` varchar(6) DEFAULT NULL,
  `titre_proj` varchar(254) NOT NULL DEFAULT '-- sujet non defini --',
  `detect_proj` date NOT NULL DEFAULT '0000-00-00',
  `rdv_proj` date DEFAULT NULL,
  `heure_proj` int(2) DEFAULT NULL,
  `actif_proj` enum('0','1') NOT NULL DEFAULT '1',
  `desc_proj` text,
  `echeance_proj` date DEFAULT NULL,
  `budget_proj` int(8) DEFAULT NULL,
  `decid_proj` int(4) DEFAULT NULL,
  `rdvavec_proj` int(8) DEFAULT NULL,
  `SSII_proj` int(4) DEFAULT NULL,
  `SSLL_proj` int(4) DEFAULT NULL,
  `comm_proj` text,
  `typeproj_proj` int(2) NOT NULL DEFAULT '0',
  `utilisateur_proj` varchar(64) NOT NULL DEFAULT 'cl',
  `ren_proj` int(8) DEFAULT NULL,
  PRIMARY KEY (`id_proj`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ref_activite`
--

DROP TABLE IF EXISTS `ref_activite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ref_activite` (
  `id_act` int(8) NOT NULL AUTO_INCREMENT,
  `nom_act` varchar(64) NOT NULL DEFAULT '???',
  PRIMARY KEY (`id_act`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ref_condireglement`
--

DROP TABLE IF EXISTS `ref_condireglement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ref_condireglement` (
  `id_condreg` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  `nom_condreg` varchar(64) NOT NULL,
  `calcul_condreg` varchar(32) NOT NULL,
  PRIMARY KEY (`id_condreg`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ref_departement`
--

DROP TABLE IF EXISTS `ref_departement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ref_departement` (
  `id_dep` varchar(4) NOT NULL,
  `nom_dep` varchar(128) NOT NULL,
  `prefecture_dep` varchar(128) NOT NULL,
  `region_dep` varchar(128) NOT NULL,
  PRIMARY KEY (`id_dep`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ref_droit`
--

DROP TABLE IF EXISTS `ref_droit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ref_droit` (
  `id_dt` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `nom_dt` varchar(64) NOT NULL DEFAULT '',
  `desc_dt` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_dt`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ref_droit_user`
--

DROP TABLE IF EXISTS `ref_droit_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ref_droit_user` (
  `id_du` int(11) NOT NULL,
  `desc_du` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id_du`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ref_fonction`
--

DROP TABLE IF EXISTS `ref_fonction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ref_fonction` (
  `id_fct` int(2) unsigned NOT NULL AUTO_INCREMENT,
  `nom_fct` varchar(32) NOT NULL DEFAULT '',
  `idsuperieur_fct` char(2) DEFAULT NULL,
  PRIMARY KEY (`id_fct`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ref_modereglement`
--

DROP TABLE IF EXISTS `ref_modereglement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ref_modereglement` (
  `id_modereg` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  `nom_modereg` varchar(32) NOT NULL,
  PRIMARY KEY (`id_modereg`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ref_page`
--

DROP TABLE IF EXISTS `ref_page`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ref_page` (
  `id_pg` varchar(64) NOT NULL DEFAULT '',
  `owner_pg` varchar(64) NOT NULL DEFAULT 'cl',
  `droit_pg` varchar(32) DEFAULT NULL,
  `modif_date_pg` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modif_user_pg` varchar(32) DEFAULT 'cl',
  `create_date_pg` timestamp NULL DEFAULT NULL,
  `create_user_pg` varchar(64) NOT NULL DEFAULT 'cl',
  `nom_pg` varchar(128) NOT NULL DEFAULT 'new page',
  `nom_pg_en` varchar(128) DEFAULT NULL,
  `nom_pg_de` varchar(128) DEFAULT NULL,
  `img_pg` varchar(64) DEFAULT NULL,
  `img_menu_pg` varchar(64) DEFAULT NULL,
  `desc_pg` varchar(255) DEFAULT NULL,
  `desc_pg_en` varchar(255) DEFAULT NULL,
  `desc_pg_de` varchar(255) DEFAULT NULL,
  `header_pg` varchar(128) DEFAULT NULL,
  `header_pg_en` varchar(128) DEFAULT NULL,
  `header_pg_de` varchar(128) DEFAULT NULL,
  `page_pg` varchar(64) DEFAULT NULL,
  `channel_pg` enum('normal','admin','gnose','prospec','pegase','draco','facturier','iPhone','produit') NOT NULL DEFAULT 'normal',
  `parent_pg` varchar(64) DEFAULT NULL,
  `order_pg` tinyint(2) unsigned DEFAULT '1',
  `menuon_pg` enum('0','1') NOT NULL DEFAULT '1',
  `sousmenu_pg` enum('0','1') NOT NULL DEFAULT '0',
  `frameset_pg` varchar(32) DEFAULT NULL,
  `style_pg` varchar(32) DEFAULT NULL,
  `content_pg` longtext,
  `content_pg_en` longtext,
  `content_pg_de` longtext,
  `actif_pg` enum('-1','0','1','2') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_pg`),
  KEY `owner_pg` (`owner_pg`,`nom_pg`,`page_pg`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='liste des pages disponibles pour l''application';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ref_pays`
--

DROP TABLE IF EXISTS `ref_pays`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ref_pays` (
  `id_pays` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `nom_pays` varchar(80) NOT NULL,
  `code_pays` char(3) NOT NULL,
  PRIMARY KEY (`id_pays`)
) ENGINE=InnoDB AUTO_INCREMENT=238 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ref_prodfamille`
--

DROP TABLE IF EXISTS `ref_prodfamille`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ref_prodfamille` (
  `id_prodfam` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `nom_prodfam` varchar(32) NOT NULL DEFAULT '',
  `livrable` enum('0','1') NOT NULL DEFAULT '1',
  `revente` enum('0','1') NOT NULL DEFAULT '1',
  `treePathKey` varchar(6) DEFAULT NULL,
  PRIMARY KEY (`id_prodfam`),
  KEY `nom_prodfam` (`nom_prodfam`),
  KEY `treePathKey` (`treePathKey`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='Type de produit';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ref_redhat_archi`
--

DROP TABLE IF EXISTS `ref_redhat_archi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ref_redhat_archi` (
  `id_arch` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `nom_arch` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_arch`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Type d''architecture materiel pour les produits REDHAT';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ref_redhat_contrat`
--

DROP TABLE IF EXISTS `ref_redhat_contrat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ref_redhat_contrat` (
  `id_cont` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `nom_cont` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_cont`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Type de contrat proposé par REDHAT';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ref_renewperiode`
--

DROP TABLE IF EXISTS `ref_renewperiode`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ref_renewperiode` (
  `id_refrnw` tinyint(2) unsigned NOT NULL,
  `nom_refrnw` varchar(32) NOT NULL,
  PRIMARY KEY (`id_refrnw`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ref_statusaffaire`
--

DROP TABLE IF EXISTS `ref_statusaffaire`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ref_statusaffaire` (
  `id_staff` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  `nom_staff` varchar(254) NOT NULL DEFAULT '',
  `score_staff` int(2) unsigned NOT NULL DEFAULT '50',
  `color_staff` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id_staff`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ref_statuscommande`
--

DROP TABLE IF EXISTS `ref_statuscommande`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ref_statuscommande` (
  `id_stcmd` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `parent_stcmd` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `pourcent_stcmd` int(2) unsigned NOT NULL DEFAULT '0',
  `nom_stcmd` varchar(128) NOT NULL DEFAULT '',
  `description_stcmd` text,
  `right_stcmd` tinyint(1) NOT NULL DEFAULT '0',
  `color_stcmd` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id_stcmd`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='etat possible d''une commande';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ref_statusdevis`
--

DROP TABLE IF EXISTS `ref_statusdevis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ref_statusdevis` (
  `id_stdev` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  `nom_stdev` varchar(254) NOT NULL DEFAULT '',
  `color_stdev` varchar(32) NOT NULL,
  `score_stdev` varchar(3) NOT NULL,
  PRIMARY KEY (`id_stdev`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ref_statusfacture`
--

DROP TABLE IF EXISTS `ref_statusfacture`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ref_statusfacture` (
  `id_stfact` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `pourcent_stfact` int(2) unsigned NOT NULL DEFAULT '0',
  `nom_stfact` varchar(128) NOT NULL DEFAULT '',
  `description_stfact` text,
  `color_stfact` varchar(32) DEFAULT NULL,
  `pontComptableExportable_stfact` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_stfact`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='etat possible d''une facture';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ref_statusfacturefournisseur`
--

DROP TABLE IF EXISTS `ref_statusfacturefournisseur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ref_statusfacturefournisseur` (
  `id_stfactfourn` tinyint(4) NOT NULL AUTO_INCREMENT,
  `pourcent_stfactfourn` int(2) NOT NULL DEFAULT '0',
  `nom_stfactfourn` varchar(128) NOT NULL,
  `desc_stfactfourn` text,
  `color_stfactfourn` varchar(16) DEFAULT NULL,
  `pontComptableExportable_stfactfourn` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_stfactfourn`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ref_typeentreprise`
--

DROP TABLE IF EXISTS `ref_typeentreprise`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ref_typeentreprise` (
  `id_tyent` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  `nom_tyent` varchar(254) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_tyent`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ref_typepayline`
--

DROP TABLE IF EXISTS `ref_typepayline`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ref_typepayline` (
  `id_typl` int(11) NOT NULL AUTO_INCREMENT,
  `nom_typl` varchar(255) NOT NULL,
  PRIMARY KEY (`id_typl`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ref_typeproj`
--

DROP TABLE IF EXISTS `ref_typeproj`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ref_typeproj` (
  `id_typro` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  `nom_typro` varchar(254) NOT NULL DEFAULT '',
  `score_typro` char(2) NOT NULL DEFAULT '4',
  PRIMARY KEY (`id_typro`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `renouvellement`
--

DROP TABLE IF EXISTS `renouvellement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `renouvellement` (
  `id_ren` int(11) NOT NULL AUTO_INCREMENT,
  `type_ren` varchar(32) NOT NULL,
  `idChamp_ren` varchar(16) NOT NULL,
  `statusChamp_ren` int(8) NOT NULL DEFAULT '1',
  `periode_ren` tinyint(2) NOT NULL,
  `fin_ren` timestamp NULL DEFAULT NULL,
  `actif_ren` tinyint(1) NOT NULL DEFAULT '0',
  `mail_ren` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id_ren`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `send`
--

DROP TABLE IF EXISTS `send`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `send` (
  `id_send` int(11) NOT NULL AUTO_INCREMENT,
  `user_send` varchar(16) DEFAULT NULL,
  `type_send` enum('mail','fax','courrier') DEFAULT NULL,
  `date_send` date DEFAULT NULL,
  PRIMARY KEY (`id_send`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `session`
--

DROP TABLE IF EXISTS `session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `session` (
  `id_sess` varchar(255) NOT NULL DEFAULT '',
  `user_sess` varchar(64) NOT NULL DEFAULT 'ERROR_USER',
  `date_sess` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `datefin_sess` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `secure_sess` varchar(16) DEFAULT NULL,
  `OS_sess` varchar(32) DEFAULT NULL,
  `browser_sess` varchar(32) DEFAULT NULL,
  `ip_sess` varchar(16) DEFAULT NULL,
  `host_sess` varchar(64) DEFAULT NULL,
  `channel_sess` varchar(32) DEFAULT NULL,
  `backup_sess` mediumtext,
  PRIMARY KEY (`id_sess`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `token`
--

DROP TABLE IF EXISTS `token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `token` (
  `id_token` varchar(64) NOT NULL,
  `user_token` varchar(16) NOT NULL,
  `action_token` varchar(256) NOT NULL,
  `used_token` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transaction`
--

DROP TABLE IF EXISTS `transaction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transaction` (
  `id_trans` int(11) NOT NULL AUTO_INCREMENT,
  `payline_trans` int(11) NOT NULL,
  `contact_trans` int(11) DEFAULT NULL,
  `nom_trans` varchar(255) NOT NULL,
  `prenom_trans` varchar(255) NOT NULL,
  `facture_trans` int(11) NOT NULL,
  `devise_trans` int(3) NOT NULL,
  `montant_trans` int(10) NOT NULL,
  PRIMARY KEY (`id_trans`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `login` varchar(127) NOT NULL DEFAULT '',
  `pwd` varchar(255) DEFAULT NULL,
  `nom` varchar(128) NOT NULL DEFAULT '',
  `prenom` varchar(128) NOT NULL DEFAULT '-',
  `civ` varchar(4) NOT NULL DEFAULT 'M.',
  `mail` varchar(128) NOT NULL DEFAULT 'dev@zuno.fr',
  `image` varchar(64) DEFAULT NULL,
  `droit` tinyint(2) unsigned NOT NULL DEFAULT '5',
  `profil` tinyint(2) unsigned DEFAULT NULL,
  `actif` enum('0','1') NOT NULL DEFAULT '0',
  `isDelete` enum('0','1') NOT NULL DEFAULT '0',
  `lang` char(2) NOT NULL DEFAULT 'fr',
  `pin` varchar(64) DEFAULT NULL,
  `viewportSize` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`login`),
  KEY `actif` (`actif`),
  KEY `login` (`login`),
  KEY `isDelete` (`isDelete`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_droits`
--

DROP TABLE IF EXISTS `user_droits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_droits` (
  `login` varchar(32) NOT NULL,
  `droit` int(11) NOT NULL,
  PRIMARY KEY (`login`,`droit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_iphoneConfig`
--

DROP TABLE IF EXISTS `user_iphoneConfig`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_iphoneConfig` (
  `user` varchar(128) NOT NULL DEFAULT '',
  `key` varchar(128) NOT NULL DEFAULT '',
  `val` varchar(255) NOT NULL,
  PRIMARY KEY (`user`,`key`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-06-13  3:42:09
