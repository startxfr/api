 
--
-- Début du patch.sql généré par CompareSQL V1
-- Thu Jun 17 03:39:23 +0200 2010
--
 
--
-- Gestion des différences de structures de la BDD et des tables
--
 
ALTER TABLE `facture` ADD `maildelivery_fact` varchar(255) DEFAULT NULL AFTER `pays_fact`;
ALTER TABLE `facture` ADD `complementdelivery_fact` varchar(128) DEFAULT NULL AFTER `maildelivery_fact`;
 
--
-- Ajout des données supplémentaires
--
 
 
--
-- Suppression des données qui n'existent plus
--
 
