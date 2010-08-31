 
--
-- Début du patch.sql généré par CompareSQL V1
-- Tue Aug 31 11:47:14 +0200 2010
--
 
--
-- Gestion des différences de structures de la BDD et des tables
--
 
ALTER TABLE `actualite` CHANGE `user` `user` varchar(128) DEFAULT NULL;
 
--
-- Ajout des données supplémentaires
--
 
 
--
-- Suppression des données qui n'existent plus
--
 
