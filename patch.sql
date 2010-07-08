 
--
-- Début du patch.sql généré par CompareSQL V1
-- Thu Jul 08 01:56:09 +0200 2010
--
 
--
-- Gestion des différences de structures de la BDD et des tables
--
 
ALTER TABLE `affaire` CHANGE `contact_aff` `contact_aff` int(4) unsigned DEFAULT NULL;
DROP TABLE IF EXISTS `banque`;
CREATE TABLE `banque` (`id_bq` int(8) unsigned NOT NULL AUTO_INCREMENT,`nom_bq` varchar(255) NOT NULL,`isActif_bq` enum('0','1') NOT NULL DEFAULT '1',`isExportablePontComptable_bq` enum('0','1') NOT NULL DEFAULT '1',`commentaire_bq` text,PRIMARY KEY (`id_bq`)) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='table des banques';
DROP TABLE IF EXISTS `cloud`;
CREATE TABLE `cloud` (`recherche_cloud` varchar(20) NOT NULL,`total_cloud` int(10) unsigned NOT NULL DEFAULT '1',`module_cloud` enum('entreprise','contact','affaire','devis','commande','facture','factureFourn','produit','fournisseur') NOT NULL DEFAULT 'entreprise',`user_cloud` varchar(127) NOT NULL,PRIMARY KEY (`recherche_cloud`,`module_cloud`,`user_cloud`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `commande` CHANGE `complementdelivery_cmd` `complementdelivery_cmd` varchar(255) DEFAULT NULL;
ALTER TABLE `contact` ADD INDEX LD_cont` (`LD_cont`);
DROP TABLE IF EXISTS `journal_banque`;
CREATE TABLE `journal_banque` (`id_jb` int(8) unsigned NOT NULL AUTO_INCREMENT,`date_record_jb` datetime NOT NULL,`date_effet_jb` datetime NOT NULL,`banque_jb` int(8) unsigned NOT NULL,`modereglement_jb` tinyint(1) NOT NULL,`libelle_jb` varchar(255) NOT NULL,`montant_jb` decimal(12,2) NOT NULL,`commentaire_jb` text,`file_jb` varchar(255) DEFAULT NULL,`facture_jb` mediumint(3) unsigned DEFAULT NULL,`entreprise_jb` mediumint(3) unsigned DEFAULT NULL,PRIMARY KEY (`id_jb`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='table de journal de banque pour les mouvements de compte';
 
--
-- Ajout des données supplémentaires
--
 
 
--
-- Suppression des données qui n'existent plus
--
 
