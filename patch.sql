 
--
-- Début du patch.sql généré par CompareSQL V1
-- Tue Jul 27 18:55:03 +0200 2010
--
 
--
-- Gestion des différences de structures de la BDD et des tables
--
 
ALTER TABLE `commande_produit` CHANGE `remise` `remise` decimal(5,2) NOT NULL DEFAULT '0.00';
ALTER TABLE `commande_produit` CHANGE `remiseF` `remiseF` decimal(5,2) NOT NULL DEFAULT '0.00';
ALTER TABLE `devis_produit` CHANGE `remise` `remise` decimal(5,2) NOT NULL DEFAULT '0.00';
ALTER TABLE `entreprise` CHANGE `remise_ent` `remise_ent` decimal(5,2) DEFAULT '0.00';
ALTER TABLE `facture_produit` CHANGE `remise` `remise` decimal(5,2) NOT NULL DEFAULT '0.00';
 
--
-- Ajout des données supplémentaires
--
 
 
--
-- Suppression des données qui n'existent plus
--
 
