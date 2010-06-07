 
--
-- Début du patch.sql généré par CompareSQL V1
-- Mon Jun 07 15:28:08 +0200 2010
--
 
--
-- Gestion des différences de structures de la BDD et des tables
--
 
ALTER TABLE `commande_produit` CHANGE `id_produit` `id_produit` varchar(32) NOT NULL DEFAULT '0';
ALTER TABLE `devis_produit` CHANGE `id_produit` `id_produit` varchar(32) NOT NULL DEFAULT '0';
ALTER TABLE `facture_produit` CHANGE `id_produit` `id_produit` varchar(32) NOT NULL DEFAULT '0';
ALTER TABLE `produit` CHANGE `id_prod` `id_prod` varchar(32) NOT NULL DEFAULT '0';
ALTER TABLE `produit_fournisseur` CHANGE `produit_id` `produit_id` varchar(32) NOT NULL DEFAULT '0';
ALTER TABLE `projet` DROP `SSII_proj`;
ALTER TABLE `projet` DROP `SSLL_proj`;
 
--
-- Ajout des données supplémentaires
--
 
 
--
-- Suppression des données qui n'existent plus
--
