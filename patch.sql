 
--
-- Début du patch.sql généré par CompareSQL V1
-- Sun Jun 13 11:27:10 +0200 2010
--
 
--
-- Gestion des différences de structures de la BDD et des tables
--
 
ALTER TABLE `ref_statusaffaire` CHANGE `color_staff` `color_staff` varchar(64) DEFAULT NULL;
ALTER TABLE `ref_statuscommande` CHANGE `color_stcmd` `color_stcmd` varchar(32) DEFAULT NULL;
ALTER TABLE `ref_statusdevis` CHANGE `color_stdev` `color_stdev` varchar(32) NOT NULL;
ALTER TABLE `ref_statusfacture` CHANGE `color_stfact` `color_stfact` varchar(32) DEFAULT NULL;
 
--
-- Ajout des données supplémentaires
--
 
DELETE FROM `ref_activite` WHERE `id_act` = '55' ;
INSERT INTO `ref_activite` VALUES (55,'Hôtels et restaurants');
DELETE FROM `ref_statusaffaire` WHERE `id_staff` = '1' ;
INSERT INTO `ref_statusaffaire` VALUES (1,'Ouverte',10,'d48319');
DELETE FROM `ref_statusaffaire` WHERE `id_staff` = '2' ;
INSERT INTO `ref_statusaffaire` VALUES (2,'Attente CDC',15,'c46f00');
DELETE FROM `ref_statusaffaire` WHERE `id_staff` = '3' ;
INSERT INTO `ref_statusaffaire` VALUES (3,'Rédaction de réponse',20,'59c800');
DELETE FROM `ref_statusaffaire` WHERE `id_staff` = '4' ;
INSERT INTO `ref_statusaffaire` VALUES (4,'Réponse envoyée',25,'429300;font-weight:bold');
DELETE FROM `ref_statusaffaire` WHERE `id_staff` = '5' ;
INSERT INTO `ref_statusaffaire` VALUES (5,'Accord de principe',45,'00b537');
DELETE FROM `ref_statusaffaire` WHERE `id_staff` = '6' ;
INSERT INTO `ref_statusaffaire` VALUES (6,'Perdu',0,'cd4833;text-decoration:line-through');
DELETE FROM `ref_statusaffaire` WHERE `id_staff` = '7' ;
INSERT INTO `ref_statusaffaire` VALUES (7,'BDC Client recu',50,'188da6;font-weight:bold');
DELETE FROM `ref_statusaffaire` WHERE `id_staff` = '8' ;
INSERT INTO `ref_statusaffaire` VALUES (8,'BDC Fournisseur Crée',52,'2d9db5');
DELETE FROM `ref_statusaffaire` WHERE `id_staff` = '9' ;
INSERT INTO `ref_statusaffaire` VALUES (9,'BDC Fournisseur envoyé',70,'38a7bf');
DELETE FROM `ref_statusaffaire` WHERE `id_staff` = '10' ;
INSERT INTO `ref_statusaffaire` VALUES (10,'Commande Client traitée',75,'40b8d2');
DELETE FROM `ref_statusaffaire` WHERE `id_staff` = '11' ;
INSERT INTO `ref_statusaffaire` VALUES (11,'Commande terminée',80,'3894bf');
DELETE FROM `ref_statusaffaire` WHERE `id_staff` = '12' ;
INSERT INTO `ref_statusaffaire` VALUES (12,'Facture crée',80,'316fc4');
DELETE FROM `ref_statusaffaire` WHERE `id_staff` = '13' ;
INSERT INTO `ref_statusaffaire` VALUES (13,'Facture validée',85,'3a76cf');
DELETE FROM `ref_statusaffaire` WHERE `id_staff` = '14' ;
INSERT INTO `ref_statusaffaire` VALUES (14,'Facture éditée',90,'2363ba');
DELETE FROM `ref_statusaffaire` WHERE `id_staff` = '15' ;
INSERT INTO `ref_statusaffaire` VALUES (15,'Facture envoyée',90,'185ebc;font-weight:bold');
DELETE FROM `ref_statusaffaire` WHERE `id_staff` = '16' ;
INSERT INTO `ref_statusaffaire` VALUES (16,'Facture réglée',100,'1843bc;font-weight:bold');
DELETE FROM `ref_statusaffaire` WHERE `id_staff` = '17' ;
INSERT INTO `ref_statusaffaire` VALUES (17,'Affaire supprimée',0,'bcbcbc;text-decoration:line-through');
DELETE FROM `ref_statusaffaire` WHERE `id_staff` = '18' ;
INSERT INTO `ref_statusaffaire` VALUES (18,'Affaire archivée',0,'b0b0b0;font-style:italic');
DELETE FROM `ref_statusaffaire` WHERE `id_staff` = '19' ;
INSERT INTO `ref_statusaffaire` VALUES (19,'Affaire désactivée',0,'9a9a9a;font-style:italic');
DELETE FROM `ref_statusaffaire` WHERE `id_staff` = '20' ;
INSERT INTO `ref_statusaffaire` VALUES (20,'Affaire re-activée',0,'70c45d');
DELETE FROM `ref_statuscommande` WHERE `id_stcmd` = '4' ;
INSERT INTO `ref_statuscommande` VALUES (4,3,25,'BDCF Envoyé','Le bon de commande fournisseur est parti vers votre fournisseur',4,'67c;font-weight:bold');
DELETE FROM `ref_statuscommande` WHERE `id_stcmd` = '5' ;
INSERT INTO `ref_statuscommande` VALUES (5,4,50,'BDCF reçu par le fournisseur','Le fournisseur à bien reçu le bon de commande',2,'38d;font-weight:bold');
DELETE FROM `ref_statuscommande` WHERE `id_stcmd` = '6' ;
INSERT INTO `ref_statuscommande` VALUES (6,5,60,'BDCF en cours de traitement','Le fournisseur valide la vente et lance son traitement;font-weight:bold',3,'49e');
DELETE FROM `ref_statuscommande` WHERE `id_stcmd` = '9' ;
INSERT INTO `ref_statuscommande` VALUES (9,8,100,'Commande terminée','Clôture de la commande',5,'2c1;font-weight:bold');
DELETE FROM `ref_statuscommande` WHERE `id_stcmd` = '10' ;
INSERT INTO `ref_statuscommande` VALUES (10,9,100,'Commande archivée','La commande est archivée',5,'b0b0b0;font-style:italic');
DELETE FROM `ref_statusdevis` WHERE `id_stdev` = '4' ;
INSERT INTO `ref_statusdevis` VALUES (4,'Envoyé','38d;font-weight:bold','50');
DELETE FROM `ref_statusdevis` WHERE `id_stdev` = '5' ;
INSERT INTO `ref_statusdevis` VALUES (5,'Perdu','d34;text-decoration:line-through','100');
DELETE FROM `ref_statusdevis` WHERE `id_stdev` = '6' ;
INSERT INTO `ref_statusdevis` VALUES (6,'Validé','2c1;font-weight:bold','100');
DELETE FROM `ref_statusdevis` WHERE `id_stdev` = '7' ;
INSERT INTO `ref_statusdevis` VALUES (7,'Archivé','b0b0b0;font-style:italic','0');
DELETE FROM `ref_statusfacture` WHERE `id_stfact` = '1' ;
INSERT INTO `ref_statusfacture` VALUES (1,10,'Facture créée','La facture est enregistrée dans nos bases','6879de','0');
DELETE FROM `ref_statusfacture` WHERE `id_stfact` = '2' ;
INSERT INTO `ref_statusfacture` VALUES (2,25,'Facture validée','Facture controlée et validée','5062d2','0');
DELETE FROM `ref_statusfacture` WHERE `id_stfact` = '3' ;
INSERT INTO `ref_statusfacture` VALUES (3,45,'Facture enregistrée','La facture est éditée et enregistrée dans gnose','384bc3','0');
DELETE FROM `ref_statusfacture` WHERE `id_stfact` = '4' ;
INSERT INTO `ref_statusfacture` VALUES (4,50,'Facture Envoyée','La facture est envoyée','529631','1');
DELETE FROM `ref_statusfacture` WHERE `id_stfact` = '5' ;
INSERT INTO `ref_statusfacture` VALUES (5,75,'Facture En attente de règlement','La facture est en attente de règlement','4d8b31;font-weight:bold','1');
DELETE FROM `ref_statusfacture` WHERE `id_stfact` = '6' ;
INSERT INTO `ref_statusfacture` VALUES (6,100,'Facture Cloturée','Le règlement vient d\'etre enregistré dans nos bases','93b087','1');
DELETE FROM `ref_statusfacture` WHERE `id_stfact` = '7' ;
INSERT INTO `ref_statusfacture` VALUES (7,100,'Facture archivée','La facture est maintenant archivée','b0b0b0;font-style:italic','1');
 
--
-- Suppression des données qui n'existent plus
--
 
