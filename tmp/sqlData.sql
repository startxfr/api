-- MySQL dump 10.13  Distrib 5.1.45, for redhat-linux-gnu (x86_64)
--
-- Host: 127.0.0.1    Database: ZunoDev_sxa
-- ------------------------------------------------------
-- Server version	5.1.45
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `actualite`
--


--
-- Dumping data for table `affaire`
--


--
-- Dumping data for table `appel`
--


--
-- Dumping data for table `commande`
--


--
-- Dumping data for table `commande_produit`
--


--
-- Dumping data for table `contact`
--


--
-- Dumping data for table `contact_payline`
--


--
-- Dumping data for table `devis`
--


--
-- Dumping data for table `devis_produit`
--


--
-- Dumping data for table `devis_renew`
--


--
-- Dumping data for table `entreprise`
--


--
-- Dumping data for table `facture`
--


--
-- Dumping data for table `facture_fournisseur`
--


--
-- Dumping data for table `facture_produit`
--


--
-- Dumping data for table `fournisseur`
--


--
-- Dumping data for table `historique_payline`
--


--
-- Dumping data for table `log`
--


--
-- Dumping data for table `message`
--


--
-- Dumping data for table `module`
--

INSERT INTO `module` VALUES ('actualite','oui');
INSERT INTO `module` VALUES ('affaire','oui');
INSERT INTO `module` VALUES ('commande','oui');
INSERT INTO `module` VALUES ('contact','oui');
INSERT INTO `module` VALUES ('devis','oui');
INSERT INTO `module` VALUES ('facture','oui');
INSERT INTO `module` VALUES ('navigator','oui');
INSERT INTO `module` VALUES ('preference','oui');
INSERT INTO `module` VALUES ('search','oui');
INSERT INTO `module` VALUES ('send','oui');
INSERT INTO `module` VALUES ('avoir','oui');
INSERT INTO `module` VALUES ('produit','oui');
INSERT INTO `module` VALUES ('statistiques','oui');
INSERT INTO `module` VALUES ('actualite','oui');
INSERT INTO `module` VALUES ('affaire','oui');
INSERT INTO `module` VALUES ('commande','oui');
INSERT INTO `module` VALUES ('contact','oui');
INSERT INTO `module` VALUES ('devis','oui');
INSERT INTO `module` VALUES ('facture','oui');
INSERT INTO `module` VALUES ('navigator','oui');
INSERT INTO `module` VALUES ('preference','oui');
INSERT INTO `module` VALUES ('search','oui');
INSERT INTO `module` VALUES ('send','oui');
INSERT INTO `module` VALUES ('avoir','oui');
INSERT INTO `module` VALUES ('produit','oui');
INSERT INTO `module` VALUES ('statistiques','oui');

--
-- Dumping data for table `produit`
--


--
-- Dumping data for table `produit_fournisseur`
--


--
-- Dumping data for table `projet`
--


--
-- Dumping data for table `ref_activite`
--

INSERT INTO `ref_activite` VALUES (1,'Agriculture, chasse, services annexes');
INSERT INTO `ref_activite` VALUES (2,'Sylviculture, exploitation forestière');
INSERT INTO `ref_activite` VALUES (5,'PÃªche, aquaculture');
INSERT INTO `ref_activite` VALUES (10,'Extraction de houille, de lignite et de tourbe');
INSERT INTO `ref_activite` VALUES (11,'Extraction d\'hydrocarbures ; services annexes');
INSERT INTO `ref_activite` VALUES (12,'Extraction de minerais d\'uranium');
INSERT INTO `ref_activite` VALUES (13,'Extraction de minerais métalliques');
INSERT INTO `ref_activite` VALUES (14,'Autres industries extractives');
INSERT INTO `ref_activite` VALUES (15,'Industries alimentaires');
INSERT INTO `ref_activite` VALUES (16,'Industries du tabac');
INSERT INTO `ref_activite` VALUES (17,'Industrie textile');
INSERT INTO `ref_activite` VALUES (18,'Industrie de l\'habillement et des fourrures');
INSERT INTO `ref_activite` VALUES (19,'Industrie du cuir et de la chaussure');
INSERT INTO `ref_activite` VALUES (20,'Travail du bois et fabrication d\'articles en bois');
INSERT INTO `ref_activite` VALUES (21,'Industrie du papier et du carton');
INSERT INTO `ref_activite` VALUES (22,'Edition, imprimerie, reproduction');
INSERT INTO `ref_activite` VALUES (23,'Cokéfaction, raffinage, industries nucléaires');
INSERT INTO `ref_activite` VALUES (24,'Industrie chimique');
INSERT INTO `ref_activite` VALUES (25,'Industrie du caoutchouc et des plastiques');
INSERT INTO `ref_activite` VALUES (26,'Fabrication d\'autres produits minéraux non métalliques');
INSERT INTO `ref_activite` VALUES (27,'Métallurgie');
INSERT INTO `ref_activite` VALUES (28,'Travail des métaux');
INSERT INTO `ref_activite` VALUES (29,'Fabrication de machines et équipements');
INSERT INTO `ref_activite` VALUES (30,'Fabrication de machines de bureau et de matériel informatique');
INSERT INTO `ref_activite` VALUES (31,'Fabrication de machines et appareils électriques');
INSERT INTO `ref_activite` VALUES (32,'Fabrication d\'équipements de radio, télévision et communicati');
INSERT INTO `ref_activite` VALUES (33,'Fabrication d\'instruments médicaux, de précision, d\'optique et');
INSERT INTO `ref_activite` VALUES (34,'Industrie automobile');
INSERT INTO `ref_activite` VALUES (35,'Fabrication d\'autres matériels de transport');
INSERT INTO `ref_activite` VALUES (36,'Fabrication de meubles ; industries diverses');
INSERT INTO `ref_activite` VALUES (37,'Récupération');
INSERT INTO `ref_activite` VALUES (40,'Production et distribution d\'électricité, de gaz et de chaleur');
INSERT INTO `ref_activite` VALUES (41,'Captage, traitement et distribution d\'eau');
INSERT INTO `ref_activite` VALUES (45,'Construction');
INSERT INTO `ref_activite` VALUES (50,'Commerce et réparation automobile');
INSERT INTO `ref_activite` VALUES (51,'Commerce de gros et intermédiaires du commerce');
INSERT INTO `ref_activite` VALUES (52,'Commerce de détail et réparation d\'articles domestiques');
INSERT INTO `ref_activite` VALUES (55,'HÃ´tels et restaurants');
INSERT INTO `ref_activite` VALUES (60,'Transport terrestre');
INSERT INTO `ref_activite` VALUES (61,'Transport par eau');
INSERT INTO `ref_activite` VALUES (62,'Transport aérien');
INSERT INTO `ref_activite` VALUES (63,'Services auxiliaires des transports');
INSERT INTO `ref_activite` VALUES (64,'Postes et télécommunications');
INSERT INTO `ref_activite` VALUES (65,'Intermédiation financière');
INSERT INTO `ref_activite` VALUES (66,'Assurance');
INSERT INTO `ref_activite` VALUES (67,'Auxiliaires financiers et d\'assurance');
INSERT INTO `ref_activite` VALUES (70,'Activités immobilières');
INSERT INTO `ref_activite` VALUES (71,'Location sans opérateur');
INSERT INTO `ref_activite` VALUES (72,'Activités informatiques');
INSERT INTO `ref_activite` VALUES (73,'Recherche et développement');
INSERT INTO `ref_activite` VALUES (74,'Services fournis principalement aux entreprises');
INSERT INTO `ref_activite` VALUES (75,'Administration publique');
INSERT INTO `ref_activite` VALUES (80,'Education');
INSERT INTO `ref_activite` VALUES (85,'Santé et action sociale');
INSERT INTO `ref_activite` VALUES (90,'Assainissement, voirie et gestion des déchets');
INSERT INTO `ref_activite` VALUES (91,'Activités associatives');
INSERT INTO `ref_activite` VALUES (92,'Activités récréatives, culturelles et sportives');
INSERT INTO `ref_activite` VALUES (93,'Services personnels');
INSERT INTO `ref_activite` VALUES (95,'Services domestiques');
INSERT INTO `ref_activite` VALUES (99,'Activités extra-territoriales');

--
-- Dumping data for table `ref_condireglement`
--

INSERT INTO `ref_condireglement` VALUES (1,'A la commande','');
INSERT INTO `ref_condireglement` VALUES (2,'A la livraison','');
INSERT INTO `ref_condireglement` VALUES (3,'Date de facturation','');
INSERT INTO `ref_condireglement` VALUES (4,'30 jours net','$jour = $jour+30;');
INSERT INTO `ref_condireglement` VALUES (5,'30 jours fin de mois','$mois = $mois+2;$jour = 0;');
INSERT INTO `ref_condireglement` VALUES (6,'30 jours fin de mois, le 10','$mois = $mois+2;$jour = 10;');
INSERT INTO `ref_condireglement` VALUES (7,'60 jours net','$jour = $jour+60;');
INSERT INTO `ref_condireglement` VALUES (8,'60 jours fin de mois','$mois = $mois+3;$jour = 0;');
INSERT INTO `ref_condireglement` VALUES (9,'60 jours fin de mois, le 10','$mois = $mois+3;$jour = 10;');
INSERT INTO `ref_condireglement` VALUES (10,'90 jours net','$jour = $jour+90;');
INSERT INTO `ref_condireglement` VALUES (11,'90 jours fin de mois','$mois = $mois+4;$jour = 0;');
INSERT INTO `ref_condireglement` VALUES (12,'90 jours fin de mois, le 10','$mois = $mois+4;$jour = 10;');
INSERT INTO `ref_condireglement` VALUES (13,'120 jours','$jour = $jour+120;');
INSERT INTO `ref_condireglement` VALUES (14,'45 jours net','$jour = $jour+45;');

--
-- Dumping data for table `ref_departement`
--

INSERT INTO `ref_departement` VALUES ('01','Ain ','Bourg-en-Bresse ','RhÃ´ne-Alpes');
INSERT INTO `ref_departement` VALUES ('02','Aisne','Laon','Picardie');
INSERT INTO `ref_departement` VALUES ('03','Allier','Moulins','Auvergne');
INSERT INTO `ref_departement` VALUES ('04','Alpes de Hautes-Provence','Digne','PACA');
INSERT INTO `ref_departement` VALUES ('05','Hautes-Alpes','Gap','PACA');
INSERT INTO `ref_departement` VALUES ('06','Alpes-Maritimes','Nice','PACA');
INSERT INTO `ref_departement` VALUES ('07','Ardèche','Privas','RhÃ´ne-Alpes');
INSERT INTO `ref_departement` VALUES ('08','Ardennes','Charleville-Mézières','Champagne-Ardenne');
INSERT INTO `ref_departement` VALUES ('09','Ariège','Foix','Midi-Pyrénées');
INSERT INTO `ref_departement` VALUES ('10','Aube','Troyes','Champagne-Ardenne');
INSERT INTO `ref_departement` VALUES ('11','Aude','Carcassonne','Languedoc-Roussillon');
INSERT INTO `ref_departement` VALUES ('12','Aveyron','Rodez','Midi-Pyrénées');
INSERT INTO `ref_departement` VALUES ('13','Bouches-du-RhÃ´ne','Marseille','PACA');
INSERT INTO `ref_departement` VALUES ('14','Calvados','Caen','Basse-Normandie');
INSERT INTO `ref_departement` VALUES ('15','Cantal','Aurillac','Auvergne');
INSERT INTO `ref_departement` VALUES ('16','Charente','AngoulÃªme','Poitou-Charentes');
INSERT INTO `ref_departement` VALUES ('17','Charente-Maritime','La Rochelle','Poitou-Charentes');
INSERT INTO `ref_departement` VALUES ('18','Cher','Bourges','Centre');
INSERT INTO `ref_departement` VALUES ('19','Corrèze','Tulle','Limousin');
INSERT INTO `ref_departement` VALUES ('20','Corse','Ajaccio','Corse');
INSERT INTO `ref_departement` VALUES ('21','CÃ´te-d\'Or','Dijon','Bourgogne');
INSERT INTO `ref_departement` VALUES ('22','CÃ´tes d\'Armor','Saint-Brieuc','Bretagne');
INSERT INTO `ref_departement` VALUES ('23','Creuse','Guéret','Limousin');
INSERT INTO `ref_departement` VALUES ('24','Dordogne','Périgueux','Aquitaine');
INSERT INTO `ref_departement` VALUES ('25','Doubs','Besançon','Franche-Comté');
INSERT INTO `ref_departement` VALUES ('26','DrÃ´me','Valence','RhÃ´ne-Alpes');
INSERT INTO `ref_departement` VALUES ('27','Eure','Ã‰vreux','Haute-Normandie');
INSERT INTO `ref_departement` VALUES ('28','Eure-et-Loir','Chartres','Centre');
INSERT INTO `ref_departement` VALUES ('29','Finistère','Quimper','Bretagne');
INSERT INTO `ref_departement` VALUES ('30','Gard','NÃ®mes','Languedoc-Roussillon');
INSERT INTO `ref_departement` VALUES ('31','Haute-Garonne','Toulouse','Midi-Pyrénées');
INSERT INTO `ref_departement` VALUES ('32','Gers','Auch','Midi-Pyrénées');
INSERT INTO `ref_departement` VALUES ('33','Gironde','Bordeaux','Aquitaine');
INSERT INTO `ref_departement` VALUES ('34','Hérault','Montpellier','Languedoc-Roussillon');
INSERT INTO `ref_departement` VALUES ('35','Ille-et-Vilaine','Rennes','Bretagne');
INSERT INTO `ref_departement` VALUES ('36','Indre','ChÃ¢teauroux','Centre');
INSERT INTO `ref_departement` VALUES ('37','Indre-et-Loire','Tours','Centre');
INSERT INTO `ref_departement` VALUES ('38','Isère','Grenoble','RhÃ´ne-Alpes');
INSERT INTO `ref_departement` VALUES ('39','Jura','Lons-le-Saunier','Franche-Comté');
INSERT INTO `ref_departement` VALUES ('40','Landes','Mont-de-Marsan','Aquitaine');
INSERT INTO `ref_departement` VALUES ('41','Loir-et-Cher','Blois','Centre');
INSERT INTO `ref_departement` VALUES ('42','Loire','Saint-Ã‰tienne','RhÃ´ne-Alpes');
INSERT INTO `ref_departement` VALUES ('43','Haute-Loire','Le Puy-en-Velay','Auvergne');
INSERT INTO `ref_departement` VALUES ('44','Loire-Atlantique','Nantes','Pays de la Loire');
INSERT INTO `ref_departement` VALUES ('45','Loiret','Orléans','Centre');
INSERT INTO `ref_departement` VALUES ('46','Lot','Cahors','Midi-Pyrénées');
INSERT INTO `ref_departement` VALUES ('47','Lot-et-Garonne','Agen','Aquitaine');
INSERT INTO `ref_departement` VALUES ('48','Lozère','Mende','Languedoc-Roussillon');
INSERT INTO `ref_departement` VALUES ('49','Maine-et-Loire','Angers','Pays de la Loire');
INSERT INTO `ref_departement` VALUES ('50','Manche','Saint-LÃ´','Basse-Normandie');
INSERT INTO `ref_departement` VALUES ('51','Marne','ChÃ¢lons-en-Champagne','Champagne-Ardenne');
INSERT INTO `ref_departement` VALUES ('52','Haute-Marne','Chaumont','Champagne-Ardenne');
INSERT INTO `ref_departement` VALUES ('53','Mayenne','Laval','Pays de la Loire');
INSERT INTO `ref_departement` VALUES ('54','Meurthe-et-Moselle','Nancy','Lorraine');
INSERT INTO `ref_departement` VALUES ('55','Meuse','Bar-le-Duc','Lorraine');
INSERT INTO `ref_departement` VALUES ('56','Morbihan','Vannes','Bretagne');
INSERT INTO `ref_departement` VALUES ('57','Moselle','Metz','Lorraine');
INSERT INTO `ref_departement` VALUES ('58','Nièvre','Nevers','Bourgogne');
INSERT INTO `ref_departement` VALUES ('59','Nord','Lille','Nord-Pas-de-Calais');
INSERT INTO `ref_departement` VALUES ('60','Oise','Beauvais','Picardie');
INSERT INTO `ref_departement` VALUES ('61','Orne','AlenÃ§on','Basse-Normandie');
INSERT INTO `ref_departement` VALUES ('62','Pas-de-Calais','Arras','Nord-Pas-de-Calais');
INSERT INTO `ref_departement` VALUES ('63','Puy-de-DÃ´me','Clermont-Ferrand','Auvergne');
INSERT INTO `ref_departement` VALUES ('64','Pyrénées-Atlantiques','Pau','Aquitaine');
INSERT INTO `ref_departement` VALUES ('65','Hautes-Pyrénées','Tarbes','Midi-Pyrénées');
INSERT INTO `ref_departement` VALUES ('66','Pyrénées-Orientales','Perpignan','Languedoc-Roussillon');
INSERT INTO `ref_departement` VALUES ('67','Bas-Rhin','Strasbourg','Alsace');
INSERT INTO `ref_departement` VALUES ('68','Haut-Rhin','Colmar','Alsace');
INSERT INTO `ref_departement` VALUES ('69','RhÃ´ne','Lyon','RhÃ´ne-Alpes');
INSERT INTO `ref_departement` VALUES ('70','Haute-Saöne','Vesoul','Franche-Comté');
INSERT INTO `ref_departement` VALUES ('71','SaÃ´ne-et-Loire','MÃ¢con','Bourgogne');
INSERT INTO `ref_departement` VALUES ('72','Sarthe','Le Mans','Pays de la Loire');
INSERT INTO `ref_departement` VALUES ('73','Savoie','Chambéry','Rhône-Alpes');
INSERT INTO `ref_departement` VALUES ('74','Haute-Savoie','Annecy','RhÃ´ne-Alpes');
INSERT INTO `ref_departement` VALUES ('75','Paris','Paris','Ile-de-France');
INSERT INTO `ref_departement` VALUES ('76','Seine-Maritime','Rouen','Haute-Normandie');
INSERT INTO `ref_departement` VALUES ('77','Seine-et-Marne','Melun','Ile-de-France');
INSERT INTO `ref_departement` VALUES ('78','Yvelines','Versailles','Ile-de-France');
INSERT INTO `ref_departement` VALUES ('79','Deux-Sèvres','Niort','Poitou-Charentes');
INSERT INTO `ref_departement` VALUES ('80','Somme','Amiens','Picardie');
INSERT INTO `ref_departement` VALUES ('81','Tarn','Albi','Midi-Pyrénées');
INSERT INTO `ref_departement` VALUES ('82','Tarn-et-Garonne','Montauban','Midi-Pyrénées');
INSERT INTO `ref_departement` VALUES ('83','Var','Toulon','PACA');
INSERT INTO `ref_departement` VALUES ('84','Vaucluse','Avignon','PACA');
INSERT INTO `ref_departement` VALUES ('85','Vendée','La Roche-sur-Yon','Pays de la Loire');
INSERT INTO `ref_departement` VALUES ('86','Vienne','Poitiers','Poitou-Charentes');
INSERT INTO `ref_departement` VALUES ('87','Haute-Vienne','Limoges','Limousin');
INSERT INTO `ref_departement` VALUES ('88','Vosges','Ã‰pinal','Lorraine');
INSERT INTO `ref_departement` VALUES ('89','Yonne','Auxerre','Bourgogne');
INSERT INTO `ref_departement` VALUES ('90','Territoire-de-Belfort','Belfort','Franche-Comté');
INSERT INTO `ref_departement` VALUES ('91','Essonne','Ã‰vry','Ile-de-France');
INSERT INTO `ref_departement` VALUES ('92','Hauts-de-Seine','Nanterre','Ile-de-France');
INSERT INTO `ref_departement` VALUES ('93','Seine-Saint-Denis','Bobigny','Ile-de-France');
INSERT INTO `ref_departement` VALUES ('94','Val-de-Marne','Créteil','Ile-de-France');
INSERT INTO `ref_departement` VALUES ('95','Val-d\'Oise','Pontoise','Ile-de-France');
INSERT INTO `ref_departement` VALUES ('971','La Guadeloupe','Basse-Terre','La Guadeloupe');
INSERT INTO `ref_departement` VALUES ('972','La Martinique','Fort-de-France','La Martinique');
INSERT INTO `ref_departement` VALUES ('973','La Guyane','Cayenne','La Guyane');
INSERT INTO `ref_departement` VALUES ('974','La Réunion','Saint-Denis','La Réunion');
INSERT INTO `ref_departement` VALUES ('975','Saint-Pierre-et-Miquelon','Saint-Pierre','Saint-Pierre-et-Miquelon');
INSERT INTO `ref_departement` VALUES ('985','Mayotte','Mamoudzou','Mayotte');

--
-- Dumping data for table `ref_droit`
--

INSERT INTO `ref_droit` VALUES (0,'SuperAdministrateur','Utilisateur ayant le plus de pouvoir dans l\'application');
INSERT INTO `ref_droit` VALUES (1,'Administrateur technique','Utilisateur pouvant administrer techniquement le site');
INSERT INTO `ref_droit` VALUES (2,'Administrateur','Utilisateur pouvant administrer le site');
INSERT INTO `ref_droit` VALUES (3,'Dirigeant','Dirigeant de l\'entreprise');
INSERT INTO `ref_droit` VALUES (5,'Comptable','Comptable de l\'entreprise');
INSERT INTO `ref_droit` VALUES (10,'Production','Expéditions, production ou magasin');
INSERT INTO `ref_droit` VALUES (15,'Commercial','Commercial de l\'entreprise');
INSERT INTO `ref_droit` VALUES (20,'Fournisseur','fournisseur de l\'entreprise');
INSERT INTO `ref_droit` VALUES (21,'Client','client de l\'entreprise');

--
-- Dumping data for table `ref_fonction`
--

INSERT INTO `ref_fonction` VALUES (1,'PDG - DG - Gerant',NULL);
INSERT INTO `ref_fonction` VALUES (2,'Directeur Commercial',NULL);
INSERT INTO `ref_fonction` VALUES (3,'Directeur Financier',NULL);
INSERT INTO `ref_fonction` VALUES (4,'Directeur Achat',NULL);
INSERT INTO `ref_fonction` VALUES (5,'Directeur informatique',NULL);
INSERT INTO `ref_fonction` VALUES (6,'Commercial',NULL);
INSERT INTO `ref_fonction` VALUES (7,'Comptable',NULL);
INSERT INTO `ref_fonction` VALUES (8,'Acheteur',NULL);
INSERT INTO `ref_fonction` VALUES (9,'Consultant informatique','');
INSERT INTO `ref_fonction` VALUES (10,'Assistant',NULL);
INSERT INTO `ref_fonction` VALUES (99,'Plus dans l\'entreprise',NULL);

--
-- Dumping data for table `ref_modereglement`
--

INSERT INTO `ref_modereglement` VALUES (1,'Espèces');
INSERT INTO `ref_modereglement` VALUES (2,'Chèque');
INSERT INTO `ref_modereglement` VALUES (3,'Virement');
INSERT INTO `ref_modereglement` VALUES (4,'TPE');

--
-- Dumping data for table `ref_page`
--

INSERT INTO `ref_page` VALUES ('actualite','nm',NULL,'2010-05-07 20:15:33','nm','2009-06-24 10:59:10','nm','actualite',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'actualite.php','normal',NULL,1,'1','0',NULL,NULL,238955,'0000-00-00 00:00:00',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('AdminManual','cl',',0,,','2008-08-12 21:32:36','cl','2004-12-31 22:00:00','cl','Manuel de l\'admin','Admin manual','Handbuch des Verwalters','ManuelAdmin.png','ManuelAdmin.png','C\'est le manuel de l\'administrateur','sumary for english person','Handbuch des Verwalters','Manuel de l\'administrateur','Manual for technical staff','Handbuch des Verwalters',NULL,'admin','Help',1,'1','0',NULL,'titleContent',1,'2005-10-28 13:14:50','mettre ici toutes les informations disponibles pour les webmasters et les superadministrateurs.',NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('AffaireCreate','cl',NULL,'2010-05-11 22:41:23','cl',NULL,'cl','Créer une affaire',NULL,NULL,'affaire.png','affaire.create.png','Création d\'un affaire',NULL,NULL,'Création d\'un affaire',NULL,NULL,'AffaireCreate.php','draco','ListeAffaire',1,'1','0',NULL,'title',396,'0000-00-00 00:00:00',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('AffaireFiche','cl',',0,1,2,,','2010-03-15 16:59:53','cl',NULL,'cl','Fiche affaire',NULL,NULL,'affaire.png','affaire.png','Fiche affaire',NULL,NULL,'Fiche affaire',NULL,NULL,'Affaire.php','draco','ListeAffaire',1,'0','0',NULL,'title',1673,'2006-03-13 22:03:41',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('Application','cl',',0,1,,','2009-12-27 14:15:49','cl','2004-12-31 22:00:00','cl','Application',NULL,'Anwendung','appli.png','Application.png','Informations sur l\'application',NULL,'Anwendung','Information sur l\'application',NULL,'Anwendung','Application.php','admin',NULL,5,'1','0',NULL,'fullContent',80,'2005-10-28 13:14:50','<p align=\\\"center\\\" class=\\\"MsoNormal\\\" style=\\\"text-align: center;\\\">\r\n											<b><font size=\\\"5\\\" color=\\\"#990000\\\">GNOSE<br />\r\n											</font></b>\r\n											<font size=\\\"4\\\" color=\\\"#990000\\\">\r\n											 <br />\r\n											Etymologiquement : la connaissance \r\n											(grec gnosis). <br />\r\n											</font><font size=\\\"4\\\" color=\\\"#000080\\\"><br /></font></p><p class=\\\"MsoNormal\\\" style=\\\"text-align: justify;\\\"><font color=\\\"#000000\\\">Gnose signifie \r\n											connaissance. Il s\\\'agit de la \r\n											connaissance intÃ©rieure, par \r\n											laquelle l\\\'homme apprÃ©hende le \r\n											divin, indÃ©pendamment de tout dogme, \r\n											de tout enseignement; la gnose \r\n											s\\\'apparente ainsi au mysticisme. <br /></font></p><p class=\\\"MsoNormal\\\" style=\\\"text-align: justify;\\\"><span style=\\\"color: black;\\\">La Gnose \r\n											est une connaissance universelle. \r\n											Lorsque nous Ã©tudions les \r\n											civilisations antiques (Ã‰gyptienne, \r\n											Maya, Celte, Grecque, Hindoue), nous \r\n											dÃ©couvrons Ã  la base les mÃªmes \r\n											enseignements. Câ€™est cette \r\n											connaissance unique que les \r\n											vÃ©ritables sages de tous les temps \r\n											(Confucius, Socrate, Bouddha, JÃ©sus, \r\n											Krishna, Blavatsky, Steinerâ€¦) sont \r\n											venus livrer Ã  lâ€™humanitÃ©.</span></p>\r\n											<p class=\\\"MsoNormal\\\" style=\\\"text-align: justify;\\\">\r\n											<span style=\\\"color: black;\\\">La Gnose \r\n											dÃ©voile les clÃ©s thÃ©oriques et \r\n											pratiques indispensables Ã  lâ€™homme \r\n											et Ã  la femme modernes qui dÃ©sirent accÃ©der Ã  une plus grande connaissance.<br /></span></p><p class=\\\"MsoNormal\\\" style=\\\"text-align: justify;\\\"><font size=\\\"4\\\" color=\\\"#990000\\\"><br /></font></p><div style=\\\"text-align: center;\\\"><font size=\\\"4\\\" color=\\\"#990000\\\">Pratiquement : partage de connaissance<br /><br /></font></div><p class=\\\"MsoNormal\\\" style=\\\"text-align: justify;\\\"><font size=\\\"4\\\" color=\\\"#990000\\\">\r\n											</font><font color=\\\"#000000\\\">L\\\'objectif du projet GNOSE est de fournir aux entreprises une solution flexible pour hÃ©berger l\\\'ensemble de leurs connaissances mÃ©tiers.<br />Le serveur de fichier GNOSE repose sur une architecture flexible et entiÃ¨rement ouverte, vous permettant d\\\'adapter son fonctionnement Ã  votre language. <br />La connaissance (et son partage) Ã©tant au coeur de nos entreprise moderne, cette solution Ã  Ã©tÃ© conÃ§ue pour :<br /></font></p><ul><li><font color=\\\"#000000\\\">SÃ©curiser l\\\'accÃ¨s et la diffusion des informations</font></li><li><font color=\\\"#000000\\\">AmÃ©liorer la recherche d\\\'information</font></li><li><font color=\\\"#000000\\\">DÃ©materialiser l\\\'accÃ¨s aux donnÃ©es</font></li><li><font color=\\\"#000000\\\">Simplifier les interfaces</font></li></ul><i><span style=\\\"color: black; font-family: Times New Roman;\\\"></span></i>',NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('ApplicationConfig','cl',NULL,'2007-03-28 10:54:31','cl','2004-12-31 22:00:00','cl','Configuration',NULL,NULL,'AppliConfig.png','ApplicationConfig.png','Configuration de l\'application',NULL,NULL,'Configuration',NULL,NULL,'ApplicationConfig.php','admin','Application',4,'1','0',NULL,'titleContent',459,'2005-10-28 13:14:50','Cette rubrique vous permet de configurer les diffÃ©rents paramÃ¨tres de votre application. Le bouton de netyage du cache vous permet de rÃ©actualiser les informations de votre site. Cette opÃ©ration est automatiquement rÃ©alisÃ©e par le systÃ¨me. <br />Si vous constatez que certaines informations ne sont pas Ã  jour, n\\\'hÃ©sitez pas Ã  lancer une opÃ©ration de nettoyage du cache.<br /><br /><br />',NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('Archive','cl',',0,1,2,,','2010-05-11 22:44:53','cl','2010-05-11 22:44:53','cl','Archives',NULL,NULL,'archive.png','archives.png','Répertoires d\'archives',NULL,NULL,'Répertoires d\'archives',NULL,NULL,'BrowseArchive.php','gnose',NULL,10,'1','0',NULL,'titleContent',159,'2005-10-28 13:14:50',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('AvoirCreate','nm',',0,1,2,,','2010-05-11 22:44:53','nm',NULL,'nm','Ajouter avoir',NULL,NULL,'facture.png','facture.png','Créer un avoir',NULL,NULL,'Créer un avoir',NULL,NULL,'FactureCreate.php?type=avoir','facturier','ListeFacture',2,'1','0',NULL,NULL,3,'0000-00-00 00:00:00',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('BackHomePageFacturier','cl',NULL,'2006-03-07 15:46:02','cl','2004-12-31 22:00:00','cl','Accueil','Homepage',NULL,NULL,NULL,'Retour accueil','Homepage',NULL,'Retour Accueil','Homepage',NULL,'../gnose','facturier','FooterFacturier',1,'1','0',NULL,'titleContent',NULL,'2006-03-07 15:46:02',NULL,'<span class=\\\"important\\\"><br /></span>',NULL,'1');
INSERT INTO `ref_page` VALUES ('BackHomePagePegase','cl',NULL,'2006-03-07 15:45:53','cl','2004-12-31 22:00:00','cl','Accueil','Homepage',NULL,NULL,NULL,'Retour accueil','Homepage',NULL,'Retour Accueil','Homepage',NULL,'../gnose','pegase','FooterPegase',1,'1','0',NULL,'titleContent',NULL,'2006-03-07 15:45:53',NULL,'<span class=\\\"important\\\"><br /></span>',NULL,'1');
INSERT INTO `ref_page` VALUES ('BackHomePageProspec','cl',NULL,'2006-03-07 15:45:36','cl','2004-12-31 22:00:00','cl','Accueil','Homepage',NULL,NULL,NULL,'Retour accueil','Homepage',NULL,'Retour Accueil','Homepage',NULL,'../gnose','prospec','FooterProspec',1,'1','0',NULL,'titleContent',NULL,'2006-03-07 15:45:36',NULL,'<span class=\\\"important\\\"><br /></span>',NULL,'1');
INSERT INTO `ref_page` VALUES ('CommandeFiche','cl',',0,1,2,,','2010-03-15 15:14:38','cl',NULL,'cl','Fiche commande',NULL,NULL,'commande.png','affaire.png','Fiche commande',NULL,NULL,'Fiche commande',NULL,NULL,'Commande.php','pegase','ListeCommande',1,'0','0',NULL,'title',2187,'2006-04-03 13:50:32',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('Contact','nm',NULL,'2010-03-15 14:41:42','nm',NULL,'nm','Nouveau Contact',NULL,NULL,'contact.png','contact.png',NULL,NULL,NULL,'Nouveau Contact',NULL,NULL,'Contact.php','prospec','ProspecSearch',2,'1','0',NULL,NULL,251,'0000-00-00 00:00:00',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('Credit','cl',NULL,'2010-05-11 22:44:53','cl','2010-05-11 22:44:53','cl','Crédits','Credits',NULL,'Credits.png',NULL,'Crédits du site','Credits',NULL,'Crédits du site','Credits',NULL,NULL,'normal','FooterNormal',6,'0','0',NULL,'titleContent',3,'2005-10-28 13:14:50','Les photographies présentées sur le site appartiennent à STARTX SARL.\r\n      <br />\r\n      <br />\r\n      <b>Responsable de la publication</b> : Christophe LARUE\r\n      <br />\r\n      <br />\r\n      <b>Conception &amp; réalisation  : STARTX</b><br />STARTX\r\nà conçu et réalisé la charte graphique et l\'habillage de ce site. Son\r\néquipe à également développé les fonctionalités et l\'ergonomie de ce\r\nsite. Plus d\'information sur ce partenaire, vous pouvez vous rendre à\r\nl\'adresse suivante: <a href=\"http://startx.fr/\">www.startx.fr</a>.<br /><br /><br /><b>Recomandations techniques</b><br />\r\nCe site est optimisé pour un résolution d\'affichage supérieure ou Ã©gale\r\nÃ  1024x768. Bien que compatible Internet Explorer 4 ou supÃ©rieur, Opera\r\net Konqueror (Safari) l\'affichage sera optimal pour les utilisateurs du\r\nnavigateur Mozilla Firefox. <br />\r\n      <br />Certaines fonctions du site nécessitent d\'ouvrir une popup. Si vous utilisez un bloqueur de popup,\r\nmerci d\'accepter l\'ouverture de popup depuis notre site. <br /><br /><br /><span style=\"font-weight: bold;\">\r\n      Version logicielle : 1.7b\r\n      </span><br />\r\n      <br /><span style=\"font-weight: bold;\">Plateforme :</span><br /><br />Serveur web :  <a href=\"http://www.apache.org/\">Apache</a><br />OS : <a href=\"http://fedora.redhat.com/\">Linux Fedora</a><br />Script : <a href=\"http://www.php.net/\">PHP</a><br />Framework : <a href=\"http://www.startx.fr/\">SXFramework</a><br />SGBD : <a href=\"http://www.mysql.com/\">Mysql</a>\r\n\r\n	<div style=\"opacity: 0.9; position: absolute; left: -500px; top: -500px; z-index: 1000; visibility: hidden;\"><table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"border: 0pt none ; margin: 0pt; padding: 0pt; width: 120px; height: 115px;\" padding=\"0px\" margin=\"0px\" id=\"linkpreviewtable\"><tbody><tr style=\"background: transparent none repeat scroll 0% 50%; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial;\"><td height=\"20\" background=\"chrome://linkpreview/content/lpc.png\" style=\"border: 0pt none ; padding: 0pt;\"><br /></td><td width=\"20\" background=\"chrome://linkpreview/content/lsh.png\" style=\"border: 0pt none ; padding: 0pt;\" rowspan=\"2\"><br /></td></tr><tr><td style=\"border: 0pt none ; padding: 0pt;\"><img border=\"0\" align=\"middle\" style=\"border: 1px solid black; visibility: hidden;\" src=\"about:blank\" /></td></tr><tr style=\"background: transparent none repeat scroll 0% 50%; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial;\"><td height=\"20\" background=\"chrome://linkpreview/content/bsh.png\" style=\"border: 0pt none ; padding: 0pt;\"><br /></td><td width=\"20\" height=\"20\" background=\"chrome://linkpreview/content/csh.png\" style=\"border: 0pt none ; padding: 0pt;\"><br /></td></tr></tbody></table></div>',NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('csv2db','cl',NULL,'2010-05-11 22:49:10','cl',NULL,'cl','Import de données',NULL,NULL,'csvExport.png','csvExport.png',NULL,NULL,NULL,'Import CSV',NULL,NULL,'csv2db.php','admin','Application',1,'1','0',NULL,'titleContent',160,'0000-00-00 00:00:00',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('Devis','cl',NULL,'2010-05-04 15:56:19','cl',NULL,'cl','Devis',NULL,NULL,'devis.png','devis.png',' devis',NULL,NULL,'Devis',NULL,NULL,'Devis.php','draco','DevisListe',1,'0','0',NULL,'titleContent',4107,'0000-00-00 00:00:00',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('DevisCreate','cl',NULL,'2010-05-11 22:44:53','cl',NULL,'cl','Créer un devis',NULL,NULL,'devis.png','devis.create.png','Créer un devis',NULL,NULL,'Créer un devis',NULL,NULL,'DevisCreate.php','draco','DevisListe',1,'1','0',NULL,'title',1686,'0000-00-00 00:00:00',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('DevisIPhone','cl',NULL,'2006-10-03 10:58:40','cl','2004-12-31 22:00:00','cl','Devis','Quotation','Empfang',NULL,NULL,'Devis','Quotation','Empfang','Devis','Quotation','Empfang','Devis.php','iPhone',NULL,1,'1','0',NULL,'titleContent',0,'2006-03-07 15:46:02','Liste des devis en cours','Quotations','das wilcomen page','1');
INSERT INTO `ref_page` VALUES ('DevisListe','cl',NULL,'2010-05-04 15:54:59','cl',NULL,'cl','Liste devis',NULL,NULL,'devis.liste.png','devis.png',' Liste des devis',NULL,NULL,'Liste des devis',NULL,NULL,'DevisListe.php','draco',NULL,3,'1','0',NULL,'titleContent',2070,'0000-00-00 00:00:00',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('DevisListeAnnule','cl',NULL,'2010-05-11 22:44:53','cl',NULL,'cl','Devis perdus',NULL,NULL,'devis.liste.png','devis.perdu.png','Liste devis perdus ou annulés',NULL,NULL,'Liste devis perdus ou annulés',NULL,NULL,'DevisListeAnnule.php','draco','DevisListe',4,'1','0',NULL,'titleContent',12,'2006-04-19 11:07:42',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('DevisListeGagne','cl',NULL,'2010-05-11 22:44:53','cl',NULL,'cl','Devis gagnés',NULL,NULL,'devis.liste.png','devis.gagne.png',' Liste des devis gagnés',NULL,NULL,'Liste des devis gagnés',NULL,NULL,'DevisListeGagne.php','draco','DevisListe',2,'1','0',NULL,'titleContent',35,'2006-04-19 11:08:43',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('DevisListeRenew','cl',NULL,'2009-09-24 02:14:06','cl',NULL,'cl','Devis Ã  renouveler',NULL,NULL,'devis.listerenew.png','devis.renew.png',' Liste des devis Ã  renouveler',NULL,NULL,'Liste des devis a renouveler',NULL,NULL,'DevisListeRenew.php','draco','DevisListe',3,'1','0',NULL,'titleContent',113,'2006-04-19 11:08:43',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('DevisPopup','cl',NULL,'2010-03-15 11:21:02','cl',NULL,'cl','Info devis',NULL,NULL,'devis.png','devis.create.png','Info devis',NULL,NULL,'Info devis',NULL,NULL,'PopupDevis.php','draco','DevisListe',4,'0','0',NULL,'title',713,'2006-03-20 08:38:40',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('FactureCreate','cl',',0,1,2,,','2010-05-11 22:44:53','cl',NULL,'cl','Ajouter facture',NULL,NULL,'facture.png','facture.png','Créer une facture',NULL,NULL,'Créer une facture',NULL,NULL,'FactureCreate.php','facturier','ListeFacture',1,'1','0',NULL,'titleContent',308,'2006-04-15 21:42:52',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('FactureFiche','cl',',0,1,2,,','2010-03-15 16:22:24','cl',NULL,'cl','Facture',NULL,NULL,'facture.png','facture.png','Facture',NULL,NULL,'Facture',NULL,NULL,'Facture.php','facturier','ListeFacture',1,'0','0',NULL,'title',2576,'2006-04-12 12:50:45',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('FactureFournisseurCreate','nm',',0,1,2,,','2010-05-11 22:44:53','nm',NULL,'nm','Ajouter facture fournisseur',NULL,NULL,'factureFourn.png','factureFourn.png','Créer une facture fournisseur',NULL,NULL,'Ajouter une facture fournisseur',NULL,NULL,'FactureFournisseurCreate.php','facturier','ListeFactureFournisseur',1,'1','0',NULL,'titleContent',77,'0000-00-00 00:00:00',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('FactureFournisseurFiche','nm',',0,1,2,,','2010-02-22 23:33:43','nm',NULL,'nm','Facture fournisseur',NULL,NULL,'factureFourn.png','factureFourn.png','Fiche facture fournisseur',NULL,NULL,'Fiche Facture fournisseur',NULL,NULL,'FactureFournisseur.php','facturier','ListeFacture',1,'0','0',NULL,'title',212,'0000-00-00 00:00:00',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('FacturePopup','cl',',0,1,2,,','2008-08-12 21:32:36','cl',NULL,'cl','Facture',NULL,NULL,'facture.png','facture.png','Facture',NULL,NULL,'Facture',NULL,NULL,'PopupFacture.php','facturier','ListeFacture',1,'0','0',NULL,'titleContent',30,'2006-04-17 15:10:58',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('FooterAdmin','cl',NULL,'2005-10-28 13:14:50','cl','2004-12-31 22:00:00','cl','Outils','Toolbox',NULL,NULL,NULL,'Lies outils du site','Website toolbox',NULL,'les outils','Toolbox',NULL,NULL,'admin',NULL,10,'0','1',NULL,NULL,NULL,'2005-10-28 13:14:50',NULL,'<br />\r\n',NULL,'1');
INSERT INTO `ref_page` VALUES ('FooterDraco','cl',NULL,'2006-03-07 15:46:02','cl','2004-12-31 22:00:00','cl','Outils','Toolbox',NULL,NULL,NULL,'Lies outils du site','Website toolbox',NULL,'les outils','Toolbox',NULL,NULL,'draco',NULL,10,'0','1',NULL,NULL,NULL,'2006-03-07 15:46:02',NULL,'<br />\r\n',NULL,'1');
INSERT INTO `ref_page` VALUES ('FooterFacturier','cl',NULL,'2006-03-07 15:46:02','cl','2004-12-31 22:00:00','cl','Outils','Toolbox',NULL,NULL,NULL,'Lies outils du site','Website toolbox',NULL,'les outils','Toolbox',NULL,NULL,'facturier',NULL,10,'0','1',NULL,NULL,NULL,'2006-03-07 15:46:02',NULL,'<br />\r\n',NULL,'1');
INSERT INTO `ref_page` VALUES ('FooterNormal','cl',NULL,'2008-07-23 15:14:46','cl','2004-12-31 22:00:00','cl','Outils','Toolbox',NULL,NULL,NULL,'Lies outils du site','Website toolbox',NULL,'les outils','Toolbox',NULL,NULL,'normal',NULL,9,'0','1',NULL,'fullContent',2,'2005-10-28 13:14:50',NULL,'<br />\r\n',NULL,'1');
INSERT INTO `ref_page` VALUES ('FooterPegase','cl',NULL,'2006-03-08 11:35:16','cl','2004-12-31 22:00:00','cl','Outils','Toolbox',NULL,NULL,NULL,'Lies outils du site','Website toolbox',NULL,'les outils','Toolbox',NULL,NULL,'pegase',NULL,15,'0','1',NULL,'titleContent',NULL,'2006-03-07 15:45:53',NULL,'<br />\r\n',NULL,'1');
INSERT INTO `ref_page` VALUES ('FooterProspec','cl',NULL,'2006-03-09 23:26:16','cl','2004-12-31 22:00:00','cl','Outils','Toolbox',NULL,NULL,NULL,'Lies outils du site','Website toolbox',NULL,'les outils','Toolbox',NULL,NULL,'prospec',NULL,9,'0','1',NULL,'titleContent',NULL,'2006-03-07 15:45:36',NULL,'<br />\r\n',NULL,'1');
INSERT INTO `ref_page` VALUES ('Fournisseur','nm',NULL,'2010-03-09 23:00:01','nm',NULL,'nm','Nouveau fournisseur',NULL,NULL,'fournisseur.png','fournisseur.png','Gestion des fournisseurs',NULL,NULL,'Gestion des fournisseurs',NULL,NULL,'Fournisseur.php','produit','FournisseurListe',3,'1','0',NULL,NULL,85,'0000-00-00 00:00:00',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('FournisseurListe','nm',NULL,'2010-03-10 22:48:10','nm',NULL,'nm','Liste des fournisseurs',NULL,NULL,'fournisseur.png','fournisseur.png','Liste des fournisseurs',NULL,NULL,'Liste des fournisseurs',NULL,NULL,'FournisseurListe.php','produit',NULL,4,'1','0',NULL,'titleContent',91,'0000-00-00 00:00:00',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('History','cl',',0,1,2,3,,','2010-02-19 12:08:23','cl','2004-12-31 22:00:00','cl','Historique',NULL,NULL,'History.png','History.png','Historique d\'un enregistrement',NULL,NULL,'Historique d\'un enregistrement',NULL,NULL,'History.php','gnose',NULL,4,'1','0',NULL,'titleContent',308,'2005-10-28 13:14:50',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('ImageManage','cl',',0,1,2,3,,','2010-05-11 22:49:36','cl','2010-05-11 22:49:36','cl','Bibliothèque d\'image','Picture gallery','Bildbibliothek','image.manage.png','ImageManage.png','Permet de gérer les images','Allow you to manage images','Bildbibliothek','Gestion des images du site','Website image management','Bildbibliothek','ImageManage.php','admin','PageManage',3,'1','0',NULL,'title',104,'2005-10-28 13:14:50',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('index','cl',NULL,'2010-05-07 20:15:24','cl','2009-09-24 02:20:00','cl','Accueil','Home Page','Empfangg',NULL,NULL,'Accueil','Homepage','Empfang','Accueil','Homepage','Empfang','index.php','normal',NULL,1,'0','0',NULL,'Accueil',2484,'2005-10-28 15:14:50','<h1>Bienvenue dans votre espace de travail ZUNO</h1><br/><br/><br/>\r\n<p>Cet extranet vous est d&eacute;di&eacute;. Il vous permet d\'acc&eacute;der &agrave; l\'espace de travail de votre entreprise. Vous pouvez vous connecter en cliquant sur le bouton connexion (en haut &agrave; droite) et en saisisant vos informations de connexion dans la boite de dialogue ad hoc.</p>\r\n<p>L\'acc&egrave;s &agrave; ce site internet n\'est accessible qu\'aux membres autoris&eacute;s par le titulaire du compte de ce service. La connexions &eacute;tant personnelle, vous devez avoir re&ccedil;u vos informations de connexion avant de pouvoir ac&eacute;der &agrave; ce service. Si ne disposez pas de ces &eacute;l&eacute;ments, merci de quitter cet espace de travail. <br />\r\nSi vous avez perdu vos informations de connexion, merci de nous adresser votre demande en vous <a title=\"Formulaire de demande d\'information de connexion\" href=\"zuno.fr/loginPerdu.php\">rendant ici</a>.</p>\r\n<p>Il est rappel&eacute; que la consultation de cet outil est soumise &agrave; la             r&eacute;glementation fran&ccedil;aise en vigueur et que toute information quelle qu\'elle             soit et quel qu\'en soit le support n\'emporte aucune exhaustivit&eacute;.</p>\r\n<p>&nbsp;</p>\r\n<h1 style=\"margin-top: 0pt;\">Un mode de travail intelligent...</h1><br/><br/><br/>\r\n<p>Zuno est d&eacute;di&eacute;e &agrave; la gestion commerciale des PME/TPE. Articul&eacute; autour de plusieurs modules inter-d&eacute;pendant, elle vous permet de g&eacute;rer de bout en bout votre processus commercial. En partant de vos prospec ou client, vous pouvez g&eacute;rer l\'ensemble de votre m&eacute;tier par le biais d\'interface simple et rapide. Vous trouverez ainsi les modules:</p>\r\n<p>&nbsp;<img width=\"200\" height=\"38\" alt=\"logo-modules-client.png\" src=\"./img/imgbank/zuno/logo-modules-client.png\" /></p>\r\n<p><img width=\"200\" height=\"38\" alt=\"logo-modules-client.png\" src=\"./img/imgbank/zuno/logo-modules-manager.png\" /></p>\r\n<p><img width=\"199\" height=\"38\" alt=\"logo-modules-client.png\" src=\"./img/imgbank/zuno/logo-modules-commercial.png\" /> </p>\r\n<p>&nbsp;<img width=\"200\" height=\"38\" alt=\"logo-modules-client.png\" src=\"./img/imgbank/zuno/logo-modules-facture.png\" /></p>\r\n<p><img width=\"200\" height=\"38\" alt=\"logo-modules-client.png\" src=\"./img/imgbank/zuno/logo-modules-ventes.png\" /><br />\r\n<br />',NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('IndexAdmin','cl',NULL,'2010-05-02 21:43:19','cl','2004-12-31 22:00:00','cl','Accueil','Home Page','Empfang',NULL,NULL,'Accueil','Homepage','Empfang','Accueil','Homepage','Empfang','index.php','admin',NULL,1,'0','0',NULL,'Accueil',447,'2005-10-28 13:14:50','L\\\'objectif du projet GNOSE est de fournir aux entreprises une solution flexible pour hÃ©berger l\\\'ensemble de leurs connaissances mÃ©tiers.\r\n<div class=\\\"P2\\\">Le serveur de fichier GNOSE repose sur une architecture flexible et entiÃ¨rement ouverte, vous permettant d\\\'adapter son fonctionnement Ã  votre mÃ©tier. <br />La connaissance (et son partage) Ã©tant au coeur de nos entreprise moderne, cette solution Ã  Ã©tÃ© conÃ§ue pour :</div>\r\n<ul class=\\\"L2_1\\\" style=\\\"margin-left: 120px;\\\"><li><div class=\\\"P4\\\"><span class=\\\"T1\\\">SÃ©curiser</span> l\\\'accÃ¨s et la diffusion des informations</div></li><li><div class=\\\"P4\\\">AmÃ©liorer la <span class=\\\"T1\\\">recherche</span> d\\\'information</div></li><li><div class=\\\"P4\\\"><span class=\\\"T1\\\">DÃ©materialiser</span> l\\\'accÃ¨s aux donnÃ©es</div></li><li><div class=\\\"P5\\\"><span class=\\\"T1\\\">Simplifier</span> les interfaces</div></li></ul>','it is welcome page','das wilcomen page','1');
INSERT INTO `ref_page` VALUES ('IndexDraco','cl',NULL,'2008-12-08 11:45:51','cl','2004-12-31 22:00:00','cl','Accueil','Home Page','Empfang',NULL,NULL,'Accueil','Homepage','Empfang','Accueil','Homepage','Empfang','index.php','draco',NULL,1,'0','0',NULL,'titleContent',865,'2006-03-07 15:46:02','&nbsp;<br />','it is welcome page','das wilcomen page','1');
INSERT INTO `ref_page` VALUES ('IndexFacturier','cl',NULL,'2008-08-13 19:42:42','cl','2004-12-31 22:00:00','cl','Accueil','Home Page','Empfang',NULL,NULL,'Accueil','Homepage','Empfang','Accueil','Homepage','Empfang','index.php','facturier',NULL,1,'0','0',NULL,'titleContent',425,'2006-03-07 15:46:02','&nbsp;<br />','it is welcome page','das wilcomen page','1');
INSERT INTO `ref_page` VALUES ('IndexGnose','cl',NULL,'2010-02-04 14:17:46','cl','2004-12-31 22:00:00','cl','Accueil','Home Page','Empfang',NULL,NULL,'Accueil','Homepage','Empfang','Accueil','Homepage','Empfang','index.php','gnose',NULL,1,'0','0',NULL,'Accueil',519,'2005-10-28 13:14:50','L\\\'objectif du projet GNOSE est de fournir aux entreprises une solution flexible pour hÃ©berger l\\\'ensemble de leurs connaissances mÃ©tiers.\r\n<div class=\\\"P2\\\">Le serveur de fichier GNOSE repose sur une architecture flexible et entiÃ¨rement ouverte, \r\nvous permettant d\\\'adapter son fonctionnement Ã  votre mÃ©tier. <br />La connaissance (et son partage) Ã©tant au coeur de nos entreprise moderne, \r\ncette solution Ã  Ã©tÃ© conÃ§ue pour :</div>\r\n<ul class=\\\"L2_1\\\" style=\\\"margin-left: 120px;\\\"><li><div class=\\\"P4\\\"><span class=\\\"T1\\\">SÃ©curiser</span> l\\\'accÃ¨s et la diffusion des informations</div></li><li><div class=\\\"P4\\\">AmÃ©liorer la <span class=\\\"T1\\\">recherche</span> d\\\'information</div></li><li><div class=\\\"P4\\\"><span class=\\\"T1\\\">DÃ©materialiser</span> l\\\'accÃ¨s aux donnÃ©es</div></li><li><div class=\\\"P5\\\"><span class=\\\"T1\\\">Simplifier</span> les interfaces</div></li></ul>','it is welcome page','das wilcomen page','1');
INSERT INTO `ref_page` VALUES ('IndexIPhone','cl',NULL,'2008-08-13 19:42:42','cl','2004-12-31 22:00:00','cl','Accueil','Home Page','Empfang',NULL,NULL,'Accueil','Homepage','Empfang','Accueil','Homepage','Empfang','index.php','iPhone',NULL,1,'0','0',NULL,'titleContent',440,'2006-03-07 15:46:02','&nbsp;<br />','it is welcome page','das wilcomen page','1');
INSERT INTO `ref_page` VALUES ('IndexPegase','cl',NULL,'2008-08-14 15:14:04','cl','2004-12-31 22:00:00','cl','Accueil','Home Page','Empfang',NULL,NULL,'Accueil','Homepage','Empfang','Accueil','Homepage','Empfang','index.php','pegase',NULL,1,'0','0',NULL,'titleContent',479,'2006-03-07 15:45:53','&nbsp;<br />','it is welcome page','das wilcomen page','1');
INSERT INTO `ref_page` VALUES ('IndexProspec','cl',NULL,'2008-11-12 09:37:05','cl','2004-12-31 22:00:00','cl','Accueil','Home Page','Empfang',NULL,NULL,'Accueil','Homepage','Empfang','Accueil','Homepage','Empfang','index.php','prospec',NULL,1,'0','0',NULL,'Accueil',833,'2006-03-07 15:45:36','L\\\'objectif du projet GNOSE est de fournir aux entreprises une solution flexible pour hÃ©berger l\\\'ensemble de leurs connaissances mÃ©tiers.\r\n<div class=\\\"P2\\\">Le serveur de fichier GNOSE repose sur une architecture flexible et entiÃ¨rement ouverte, \r\nvous permettant d\\\'adapter son fonctionnement Ã  votre mÃ©tier. <br />La connaissance (et son partage) Ã©tant au coeur de nos entreprise moderne, \r\ncette solution Ã  Ã©tÃ© conÃ§ue pour :</div>\r\n<ul class=\\\"L2_1\\\" style=\\\"margin-left: 120px;\\\"><li><div class=\\\"P4\\\"><span class=\\\"T1\\\">SÃ©curiser</span> l\\\'accÃ¨s et la diffusion des informations</div></li><li><div class=\\\"P4\\\">AmÃ©liorer la <span class=\\\"T1\\\">recherche</span> d\\\'information</div></li><li><div class=\\\"P4\\\"><span class=\\\"T1\\\">DÃ©materialiser</span> l\\\'accÃ¨s aux donnÃ©es</div></li><li><div class=\\\"P5\\\"><span class=\\\"T1\\\">Simplifier</span> les interfaces</div></li></ul>','it is welcome page','das wilcomen page','1');
INSERT INTO `ref_page` VALUES ('Journaux','cl',NULL,'2010-05-04 15:57:18','cl',NULL,'cl','Journaux',NULL,NULL,'journaux.png','journaux.png',' Journaux',NULL,NULL,'Journaux',NULL,NULL,NULL,'admin','Application',1,'1','0',NULL,'fullContent',11,'0000-00-00 00:00:00',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('Legal','cl',NULL,'2010-05-11 22:45:43','cl','2010-05-11 22:45:43','cl','Info légales','Legals informations',NULL,'Legales.png',NULL,'Informations légales du site','Legals informations desc',NULL,'Informations légales','legals informations title',NULL,NULL,'normal','FooterNormal',5,'0','0',NULL,'titleContent',6,'2005-10-28 13:14:50','<h1>PropriÃ©tÃ© intellectuelle</h1>\r\n<p>Tous les textes, commentaires, documents, illustrations et images reproduits sur le site Internet gnose.startx.fr sont rÃ©servÃ©s au titre du droit d\'auteur ainsi qu\'au titre de la propriÃ©tÃ© intellectuelle et pour le monde entier (article L. 122-4 du Code de la propriÃ©tÃ© intellectuelle franÃ§aise).</p><p>A ce titre et conformÃ©ment aux dispositions du Code de la PropriÃ©tÃ© Intellectuelle, seule l\'utilisation pour un usage privÃ©, et sous rÃ©serve de dispositions diffÃ©rentes est autorisÃ©e (article L.122-5, alinÃ©a premier, du Code de la propriÃ©tÃ© intellectuelle).</p><p>Toute autre utilisation est constitutive de contrefaÃ§on et sanctionnÃ©e au titre de la PropriÃ©tÃ© Intellectuelle sauf autorisation prÃ©alable de STARTX SARL. Toute reproduction totale ou partielle du contenu du site et/ou de ses documents PDF sans accord prÃ©alable de la sociÃ©tÃ© STARTX est strictement interdite.</p><p>Toute reproduction, reprÃ©sentation ou utilisation autorisÃ©e d\'un Ã©lÃ©ment constitutif du site Web doit mentionner la phrase suivante :<br />Â© 2005 STARTX, Tous droits rÃ©servÃ©s.</p><h1>ResponsabilitÃ©</h1><p>Des liens hypertextes peuvent renvoyer vers d\'autres sites que le site gnose.startx.fr et STARTX dÃ©gage toutes responsabilitÃ©s dans le cas oÃ¹ le contenu de ces sites contreviendrait aux dispositions lÃ©gales et rÃ©glementaires en vigueur.</p><h1>Droit applicable - litiges</h1><p>Les prÃ©sentes conditions sont soumises Ã  la loi franÃ§aise. La langue du prÃ©sent contrat est la langue franÃ§aise. En cas de litige les tribunaux FranÃ§ais seront seuls compÃ©tents.</p><h1>Respect de la vie privÃ©e</h1><p>STARTX, ses filiales et ses partenaies sont seuls destinataires des informations nominatives que vous lui communiquez via son site internet. STARTX s\'engage Ã  ne pas divulguer Ã  d\'autres tiers les informations que vous lui communiquez. Celles-ci sont confidentielles. Elles ne seront utilisÃ©es par nos services que pour le traitement de votre demande ou l\'envoi d\'information sur notre activitÃ© (lettre d\'information).</p><p>En consÃ©quence, conformÃ©ment Ã  la loi informatique et libertÃ©s du 6 janvier 1978, vous disposez d\'un droit d\'accÃ¨s, de rectification, et d\'opposition aux donnÃ©es personnelles vous concernant. Pour cela il suffit de nous en faire la demande par courrier en nous indiquant vos nom, prenom, adresse Ã  l\'adresse suivante :<br />STARTX<br /><a href=\"mailto:cnil@startx.fr\">cnil@startx.fr</a></p>',NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('ListeAffaire','cl',',0,1,2,,','2010-04-09 15:39:35','cl',NULL,'cl','Liste des affaires',NULL,NULL,'affaire.png','affaire.png',' Liste des affaires',NULL,NULL,'Liste des affaires',NULL,NULL,'ListeAffaires.php','draco',NULL,4,'1','0',NULL,'titleContent',1596,'2006-03-13 22:03:41',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('ListeCommande','cl',',0,1,2,,','2010-05-04 15:57:10','cl',NULL,'cl','Commandes',NULL,NULL,'commande.png','commande.png',' Liste des commandes',NULL,NULL,'Liste des commandes',NULL,NULL,'CommandeListe.php','pegase',NULL,4,'1','0',NULL,'titleContent',786,'2006-04-03 13:50:32',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('ListeDevisIPhone','cl',NULL,'2006-10-03 10:58:40','cl','2004-12-31 22:00:00','cl','Devis','Quotation','Empfang',NULL,NULL,'Devis','Quotation','Empfang','Devis','Quotation','Empfang','ListeDevis.php','iPhone',NULL,1,'1','0',NULL,'titleContent',0,'2006-03-07 15:46:02','Liste des devis en cours','Quotations','das wilcomen page','1');
INSERT INTO `ref_page` VALUES ('ListeFacture','cl',',0,1,2,,','2010-05-04 15:53:25','cl',NULL,'cl','En cours',NULL,NULL,'facture.png','facture.png',' Liste des factures en cours',NULL,NULL,'Liste des factures en cours',NULL,NULL,'FactureListe.php','facturier',NULL,1,'1','0',NULL,'titleContent',1806,'2006-04-03 13:50:32',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('ListeFactureCloture','cl',',0,1,2,,','2010-05-11 22:45:43','cl',NULL,'cl','Cloturées',NULL,NULL,'facture.cloture.png','facture.cloture.png',' Liste des factures terminées',NULL,NULL,'Liste des factures cloturées',NULL,NULL,'FactureListeCloture.php','facturier',NULL,2,'1','0',NULL,'titleContent',169,'2006-04-17 15:33:20',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('ListeFactureFournisseur','nm',',0,1,2,,','2010-03-13 15:31:24','nm',NULL,'nm','Facture fournisseur',NULL,NULL,'factureFourn.png','factureFourn.png','Liste des factures fournisseurs',NULL,NULL,'Liste des factures fournisseur',NULL,NULL,'FactureFournisseurListe.php','facturier',NULL,4,'1','0',NULL,'titleContent',90,'0000-00-00 00:00:00',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('ListeLeads','cl',',0,1,2,,','2010-05-04 15:54:46','cl',NULL,'cl','Liste des projets',NULL,NULL,'projet.liste.png','projet.liste.png',' Liste des projets',NULL,NULL,'Liste des projets',NULL,NULL,'ListeLeads.php','prospec',NULL,4,'1','0',NULL,'titleContent',659,'0000-00-00 00:00:00',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('Login','cl',NULL,'2010-05-07 20:15:24','cl','2010-02-19 01:36:29','cl','Authentification','Authentication',NULL,NULL,NULL,'Authentification des utilisateurs','Connect to authorized area',NULL,'Authentification des utilisateurs','Connect to authorized area',NULL,'Login.php','normal',NULL,1,'0','0','','Accueil',5021,'2005-10-28 13:14:50',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('LoginAdmin','cl',NULL,'2010-05-02 21:43:18','cl','2010-02-19 01:32:28','cl','Authentification','Authentication',NULL,NULL,NULL,'Authentification des utilisateurs','Connect to authorized area',NULL,'Authentification des utilisateurs','Connect to authorized area',NULL,'Login.php','admin',NULL,1,'0','0',NULL,NULL,321,'2005-10-28 13:14:50',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('LoginDraco','cl',NULL,'2010-03-18 13:11:53','cl','2010-02-19 01:32:28','cl','Authentification','Authentication',NULL,NULL,NULL,'Authentification des utilisateurs','Connect to authorized area',NULL,'Authentification des utilisateurs','Connect to authorized area',NULL,'Login.php','draco',NULL,1,'0','0',NULL,NULL,342,'2006-03-07 15:46:02',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('LoginFacturier','cl',NULL,'2010-05-04 15:52:57','cl','2010-02-19 01:32:28','cl','Authentification','Authentication',NULL,NULL,NULL,'Authentification des utilisateurs','Connect to authorized area',NULL,'Authentification des utilisateurs','Connect to authorized area',NULL,'Login.php','facturier',NULL,1,'0','0',NULL,NULL,150,'2006-03-07 15:46:02',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('LoginGnose','cl',NULL,'2010-02-19 01:32:28','cl','2010-02-19 01:32:28','cl','Authentification','Authentication',NULL,NULL,NULL,'Authentification des utilisateurs','Connect to authorized area',NULL,'Authentification des utilisateurs','Connect to authorized area',NULL,'Login.php','gnose',NULL,1,'0','0',NULL,NULL,235,'2005-10-28 13:14:50',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('LoginIPhone','cl',NULL,'2010-02-19 01:32:28','cl','2010-02-19 01:32:28','cl','Authentification','Authentication',NULL,NULL,NULL,'Authentification des utilisateurs','Connect to authorized area',NULL,'Authentification des utilisateurs','Connect to authorized area',NULL,'Login.php','iPhone',NULL,1,'0','0',NULL,NULL,151,'2006-03-07 15:46:02',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('LoginPegase','cl',NULL,'2010-03-09 09:09:41','cl','2010-02-19 01:32:28','cl','Authentification','Authentication',NULL,NULL,NULL,'Authentification des utilisateurs','Connect to authorized area',NULL,'Authentification des utilisateurs','Connect to authorized area',NULL,'Login.php','pegase',NULL,1,'0','0',NULL,NULL,74,'2006-03-07 15:45:53',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('LoginProduit','nm',NULL,'2010-03-13 15:23:22','nm',NULL,'nm','Authentification','Authentification',NULL,NULL,NULL,'Authentification des utilisateurs','Connect to authorized area',NULL,'Authentification des utilisateurs','Connect to authorized area',NULL,'Login.php','produit',NULL,1,'0','0',NULL,NULL,18,'0000-00-00 00:00:00',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('LoginProspec','cl',NULL,'2010-04-12 13:34:58','cl','2010-02-19 01:32:28','cl','Authentification','Authentication',NULL,NULL,NULL,'Authentification des utilisateurs','Connect to authorized area',NULL,'Authentification des utilisateurs','Connect to authorized area',NULL,'Login.php','prospec',NULL,1,'0','0',NULL,NULL,276,'2006-03-07 15:45:36',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('LogView','cl',NULL,'2010-05-11 22:46:54','cl','2010-05-11 22:46:54','cl','activité',NULL,NULL,NULL,NULL,'voir le journal d\'activité',NULL,NULL,'Journal d\'activité',NULL,NULL,'LogView.php','admin','Journaux',NULL,'1','0',NULL,'title',145,'2005-10-28 13:14:50',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('monBureau','cl',NULL,'2010-05-06 14:13:05','cl','2009-09-28 13:24:45','cl','Mon bureau',NULL,NULL,NULL,NULL,'Mon bureau',NULL,NULL,'Mon bureau',NULL,NULL,'Bureau.php','normal',NULL,1,'0','0',NULL,NULL,1042,'0000-00-00 00:00:00',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('PageCreate','cl',',0,1,,','2010-05-11 22:46:54','cl','2010-05-11 22:46:54','cl','Créer','Create','zu schaffen','page.modif.png',NULL,'Création d\'une nouvelle page','Create a new page','zu schaffen','Création d\'une nouvelle page du site','Create a new page','zu schaffen','PageCreate.php','admin','PageManage',1,'1','0',NULL,NULL,146,'2005-10-28 13:14:50',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('PageDelete','cl',',0,1,,','2008-08-12 21:32:36','cl','2004-12-31 22:00:00','cl','Supprimer','Delete','abzuschaffen','page.modif.png',NULL,'Suppression d\'une page','Delete a page','abzuschaffen','Supprimer les informations d\'une page du site','Delete content of a page','abzuschaffen','PageDelete.php','admin','PageManage',2,'0','0',NULL,NULL,50,'2005-10-28 13:14:50',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('PageDeleteFile','cl',',0,1,,','2010-05-11 22:46:54','cl',NULL,'cl','Supprimer un document','Delete a document','Delete a document',NULL,NULL,' suppression d\'un document a télécharger','Delete a document','Delete a document','Supprimer un document','Delete a document','Delete a document','PageDeleteFile.php','admin','PageModif',1,'0','0',NULL,'titleContent',NULL,'2005-10-28 13:14:50',NULL,'contenu en francais',NULL,'1');
INSERT INTO `ref_page` VALUES ('PageManage','cl',',0,1,2,,','2010-05-11 22:46:54','cl','2010-05-11 22:46:54','cl','Gestion des pages','Pages management','Seiten','page.manage.png','PageManage.png','Permet de gérer de nouvelles pages','Allow you to manage new pages','Seiten','Gestion des pages du site','Website page management','Seiten','PageManage.php','admin',NULL,1,'1','0',NULL,'title',758,'2005-10-28 13:14:50',NULL,'<span class=\\\"important\\\"><br /></span>',NULL,'1');
INSERT INTO `ref_page` VALUES ('PageModif','cl',',0,1,2,3,,','2010-01-29 22:39:24','cl','2009-08-27 18:03:36','cl','Modifier','Modify','zu Ã¤ndern','page.modif.png',NULL,'Modification d\'une page','Modify content of a page','zu Ã¤ndern','Modifier les informations d\'une page du site','Modify content of a page','zu Ã¤ndern','PageModif.php','admin','PageManage',2,'1','0',NULL,NULL,1083,'2005-10-28 13:14:50',' ',NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('PageModif4Lang','cl',',0,1,2,3,,','2008-08-12 21:32:36','cl','2004-12-31 22:00:00','cl','Modifier le contenu','modify content','zu Ã¤ndern','page.modif.png',NULL,'Modification du contenu d\'une page','modify content for english','zu Ã¤ndern','Modifier les informations d\'une page du site','modify content for english','zu Ã¤ndern','PageModif4Lang.php','admin','PageModif',2,'0','0',NULL,'title',59,'2005-11-07 10:01:18',NULL,'<span class=\\\"important\\\"><br /></span>',NULL,'1');
INSERT INTO `ref_page` VALUES ('PageModifFile','cl',',0,1,,','2008-08-13 19:46:26','cl',NULL,'cl','Modification d\'un document','Modify a document',NULL,NULL,NULL,' modfification d\'un document','Modify a document',NULL,'Modification d\'un document','Modify a document',NULL,'PageModifFile.php','admin','PageModif',2,'0','0',NULL,'title',1,'2005-10-28 13:14:50',NULL,'contenu de la page en francais',NULL,'1');
INSERT INTO `ref_page` VALUES ('PageModifLot','cl',',0,1,2,3,,','2008-08-12 21:32:36','cl','2004-12-31 22:00:00','cl','Modifier un lot','Modify','zu Ã¤ndern','page.modif.png',NULL,'Modification d\'un lot de pages','Modify content of a bunch of page','zu Ã¤ndern','Modification d\'un lot de pages','Modify content of pages','zu Ã¤ndern','PageModifLot.php','admin','PageManage',2,'0','0',NULL,NULL,1045,'2005-10-28 13:14:50',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('PagePopupImportOdt','cl',',0,1,,','2010-01-29 22:38:48','cl','2004-12-31 22:00:00','cl','Importation de contenu',NULL,NULL,NULL,NULL,'Importation de contenu',NULL,NULL,'Importation de contenu',NULL,NULL,'PagePopup.ImportOdt.php','admin','PageManage',3,'0','0',NULL,'titleContent',72,'2006-01-18 15:18:21','L\\\'importation d\\\'un contenu a partir d\\\'un document ODT vous permet de simplifier en une opÃ©ration l\\\'ajout de contenu dans votre site. <br />Si votre document contien des images, \r\nun rÃ©pertoire (du nom de l\\\'ID de votre page) sera ajoutÃ© dans votre photothÃ¨que. Il contiendra toutes les images de votre document. <br /><br />La mise en page de votre document sera adaptÃ©e au web et certain styles seront automatiquement supprimÃ©. Si vous constatez une trop grande diffÃ©rence avec votre document original, \r\nvous devez en simplifier sa structure (styles trop complexes ou imbriquÃ©s)<br /><br /><br />',NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('Plan','cl',NULL,'2010-02-16 13:00:48','cl','2004-12-31 22:00:00','cl','Plan du site','Website map',NULL,'Plan.png','Plan.png','Plan du site','Site map',NULL,'Plan du site','Site map',NULL,'Plan.php','normal','FooterNormal',4,'1','0',NULL,'titleContent',48,'2005-10-28 13:14:50','\r\n		\r\n		\r\n		\r\n		le plan\r\n		\r\n	\r\n	\r\n	\r\n	\r\n	',NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('PopupAffaire','cl',',0,1,2,,','2008-08-14 19:55:20','cl',NULL,'cl','Popup affaires',NULL,NULL,'affaire.png','affaire.png','Popup affaires',NULL,NULL,'Popup affaires',NULL,NULL,'PopupAffaire.php','draco','ListeAffaire',1,'0','0',NULL,'title',573,'2006-03-13 22:03:41',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('PopupAppel','cl',',0,1,2,,','2009-06-24 14:37:12','cl',NULL,'cl','Popup Appel',NULL,NULL,'appel.png',NULL,'Popup Appel',NULL,NULL,'Popup Appel',NULL,NULL,'PopupAppel.php','prospec','ProspecFiche',1,'0','0',NULL,'title',446,'2006-03-10 15:34:41',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('PopupCommande','cl',',0,1,2,,','2010-01-29 22:26:57','cl',NULL,'cl','Popup commande',NULL,NULL,'commande.png','commande.png','Popup commande',NULL,NULL,'Popup commande',NULL,NULL,'PopupCommande.php','pegase','ListeCommande',1,'0','0',NULL,'title',76,'2006-04-03 13:50:32',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('PopupContact','cl',',0,1,2,,','2009-06-09 12:22:49','cl',NULL,'cl','Popup Contact',NULL,NULL,'contact.png',NULL,'Popup Contact',NULL,NULL,'Popup Contact',NULL,NULL,'PopupContact.php','prospec','ProspecFiche',1,'0','0',NULL,'title',193,'2006-03-10 15:34:41',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('PopupEntreprise','cl',',0,1,2,,','2010-03-15 17:00:10','cl',NULL,'cl','Popup Entreprise',NULL,NULL,'entreprise.png',NULL,'Popup Entreprise',NULL,NULL,'Popup Entreprise',NULL,NULL,'PopupEntreprise.php','prospec','ProspecFiche',1,'0','0',NULL,'title',251,'2006-03-10 15:34:41',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('PopupProjet','cl',',0,1,2,,','2009-10-01 23:30:43','cl',NULL,'cl','Popup Projet',NULL,NULL,'projet.png',NULL,'Popup Projet',NULL,NULL,'Popup Projet',NULL,NULL,'PopupProjet.php','prospec','ProspecFiche',1,'0','0',NULL,'title',495,'2006-03-10 15:33:15',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('Produit','nm',NULL,'2010-05-04 15:57:01','nm',NULL,'nm','Nouveau Produit',NULL,NULL,'produit.png','produit.png','Gestion des produits',NULL,NULL,'Gestion des produits',NULL,NULL,'Produit.php','produit','ProduitListe',1,'1','0',NULL,'titleContent',428,'0000-00-00 00:00:00',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('ProduitListe','nm',NULL,'2010-05-04 15:56:57','nm',NULL,'nm','Liste des Produits',NULL,NULL,'produit.png','produit.png','Liste des produits',NULL,NULL,'Liste des produits',NULL,NULL,'ProduitListe.php','produit',NULL,2,'1','0',NULL,'titleContent',231,'0000-00-00 00:00:00',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('Projet','cl',',0,1,2,,','2010-05-04 15:54:54','cl','2010-01-29 14:33:56','cl','Projet',NULL,NULL,'projet.png','projet.png','Projet',NULL,NULL,'Projet',NULL,NULL,'Projet.php','prospec','ListeLeads',1,'0','0',NULL,'title',513,'2006-03-10 15:33:15',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('ProspecFiche','cl',',0,1,2,,','2010-04-23 15:58:22','cl',NULL,'cl','Nouvelle entreprise',NULL,NULL,'entreprise.png','entreprise.png','Entreprise',NULL,NULL,'Nouvelle entreprise',NULL,NULL,'fiche.php','prospec','ProspecSearch',1,'1','0',NULL,'title',7159,'2006-03-07 15:45:36',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('ProspecListe','cl',',0,1,2,,','2010-02-27 17:15:25','cl',NULL,'cl','Liste de relance',NULL,NULL,'relance.png','relance.png','listes de relance',NULL,NULL,'Liste de prospection',NULL,NULL,'ListeProspec.php','prospec',NULL,5,'1','0',NULL,'title',1270,'2006-03-07 15:45:36',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('ProspecSearch','cl',',0,1,2,,','2010-04-23 15:58:18','cl',NULL,'cl','Liste des contacts',NULL,NULL,'recherche.prospec.png','recherche.prospec.png','Liste des contacts',NULL,NULL,'Liste des contacts',NULL,NULL,'Recherche.php','prospec',NULL,3,'1','0',NULL,'titleContent',3171,'0000-00-00 00:00:00',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('RedactorManual','cl',',0,,','2008-08-12 21:32:36','cl','2004-12-31 22:00:00','cl','Manuel du redacteur','Writer manual','Handbuch des Verfassers','ManuelRedact.png','ManuelRedact.png','Guide d\'utilisation de l\'administration','Writer manual','Handbuch des Verfassers','Le manuel du redacteur','Writer manual','Handbuch des Verfassers',NULL,'admin','Help',1,'1','0',NULL,'titleContent',1,'2005-10-28 13:14:50','<h3>1. Les formulaires d\\\'administrations</h3><h4 style=\\\"font-style: italic; text-decoration: underline;\\\">1.1 Les langues</h4>L\\\'ensemble du site est disponible dans plusieurs langues selon votre configuration (voir dans la partie<a href=\\\"https://dev.startx.fr/sxframework/manage/application.php\\\"> Application</a> ). Les formulaires d\\\'administration du site (pages, newsletter, autres plugins...) sont alors traduits dans la langue demandÃ©e. Si aucune traduction n\\\'est disponible pour tout ou partie de la page demandÃ©e, le texte de la langue par defaut est alors affichÃ©.<br /><img vspace=\\\"6\\\" hspace=\\\"6\\\" border=\\\"0\\\" align=\\\"right\\\" alt=\\\"diffÃ©rentes langues\\\" src=\\\"https://dev.startx.fr/sxframework/img/imgbank/manul_pour_les_langues.png\\\" /><br />Lorsqu\\\'un administrateur utilise l\\\'administration dans une langue diffÃ©rente de la langue par defaut, il est en mesure de modifier le contenu des pages pour cette langue. Ainsi certaines rubriques ou champs de formulaires seront affichÃ© en vert, signalant une modification valable uniquement pour la langue en cours.<br />Les autres champs conservent leurs portÃ©e globales.<br /><br /><h4 style=\\\"font-style: italic; text-decoration: underline;\\\">1.2 Les symboles</h4><img vspace=\\\"5\\\" hspace=\\\"5\\\" border=\\\"0\\\" align=\\\"left\\\" src=\\\"https://dev.startx.fr/sxframework/img/imgbank/manuel_champs_oblig.png\\\" />Les formulaires peuvent contenir plusieurs types de champs, boites et autres listes de sÃ©lÃ©ction. Lorsqu\\\'un de ces Ã©lÃ©ments est suivi d\\\'un simple &quot;!&quot; transparent, cela signifie que cette information est obligatoire. Le champ associÃ© doit impÃ©rativement Ãªtre rempli.<br />Le panneaux &quot;attention&quot; indique que des contraites particuliÃ¨res doivent Ãªtre rÃ©spÃ©ctÃ©e pour complÃ©tÃ© ce champs. Vous devez alos survoler l\\\'image pour obtenir plus de dÃ©tail.<br /><br />Il vous est toujours possible de survoler les titres devant chaque champs afin d\\\'avoir un dÃ©tail du contenu a fournir. Cela peut s\\\'averer utile lorsque vous ne savez pas Ã  quoi correspond cette case.<br /><br /><br /><br />',NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('SendMail','cl',',0,1,2,21,22,3,4,41,42,43,44,5,5','2010-02-15 15:26:16','cl',NULL,'cl','Envoi d\\\'un mail',NULL,NULL,'fileSend.png','fileSend.png',' Envoi d\\\'un mail',NULL,NULL,'Envoi d\\\'un mail',NULL,NULL,'PopupSendMail.php','prospec','ProspecFiche',2,'0','0',NULL,'titleContent',107,'0000-00-00 00:00:00',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('SessionDetail','cl',NULL,'2010-05-11 22:48:07','cl','2010-05-11 22:48:07','cl','Détail de la session','Session detail',NULL,NULL,NULL,'détail de la session','Session detail',NULL,'Détail d\'une session','Session detail',NULL,'SessionDetail.php','admin','Application',3,'0','0',NULL,'title',1,'2005-10-28 13:14:50',NULL,'<span class=\\\"important\\\"><br /></span>',NULL,'1');
INSERT INTO `ref_page` VALUES ('SessionView','cl',NULL,'2009-12-16 12:01:06','cl','2004-12-31 22:00:00','cl','Session','Session log',NULL,NULL,NULL,'Journal de session','Session log',NULL,'Journal de session','Session log',NULL,'SessionView.php','admin','Journaux',1,'1','0',NULL,'title',13,'2005-10-28 13:14:50',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('StatFacture','cl',',0,1,2,,','2010-03-15 16:54:58','cl',NULL,'cl','Statistiques',NULL,NULL,'statG.png','stat.png','Statistiques de facturation',NULL,NULL,'Statistiques de facturation',NULL,NULL,'FactureStats.php','facturier',NULL,10,'1','0',NULL,'titleContent',117,'2006-04-03 13:50:32',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('StatView','cl',',0,1,2,,','2008-08-14 11:28:07','cl','2004-12-31 22:00:00','cl','Statistiques','Statistics','Statistiken','statistique.png','StatView.png','Gestions des statistiques','Statistics management','Statistiken','Gestions des statistiques','Statistics management','Statistiken','StatView.php','admin','PageManage',8,'1','0',NULL,'title',104,'2005-10-28 13:14:50','<br />\r\n',NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('UserCreate','cl',',0,1,2,,','2010-05-11 22:48:07','cl','2010-05-11 22:48:07','cl','Créer','Create admin account','zu schaffen','user.admin.create.png',NULL,'Création d\'un compte utilisateur','Create admin account','zu schaffen','Création d\'un compte utilisateur','Create admin account','zu schaffen','UserCreate.php','admin','UserManage',1,'1','0',NULL,NULL,15,'2005-10-28 13:14:50','<br />\r\n',NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('UserDelete','cl',',0,1,,','2008-08-12 21:32:36','cl','2004-12-31 22:00:00','cl','Supprimer','Delete admin account','Abschaffung eines Verwalters','user.admin.del.png',NULL,'Suppression d\'un utilisateur','Delete admin account','Abschaffung eines Verwalters','Suppression d\'un utilisateur','Delete admin account','Abschaffung eines Verwalters','UserDelete.php','admin','UserManage',2,'0','0',NULL,'title',6,'2005-10-28 13:14:50',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('UserManage','cl',',0,1,2,,','2009-12-27 03:15:57','cl','2004-12-31 22:00:00','cl','Utilisateurs','Users','Benutzer','user.manage.png','UserManage.png','Gestions des utilisateurs','Users accounts management','Benutzer','Gestions des utilisateurs','Users accounts management','Benutzer','UserManage.php','admin',NULL,2,'1','0',NULL,'title',157,'2005-10-28 13:14:50',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('UserModif','cl',',0,1,2,,','2009-12-16 12:00:05','cl','2004-12-31 22:00:00','cl','Modifier','Admin account','zu Ã¤ndern','user.admin.modif.png',NULL,'Modification d\'un compte utilisateur','Modify a manager account','zu Ã¤ndern','Modification d\'un compte utilisateur','Modify a manager account','zu Ã¤ndern','UserModif.php','admin','UserManage',2,'1','0',NULL,'title',24,'2005-10-28 13:14:50','<br />\r\n',NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('UserViewAdmin','cl',',0,1,2,3,4,5,,','2008-08-12 21:32:36','cl','2004-12-31 22:00:00','cl','Informations utilisateur','User account information','zu Ã¤ndern','UserView.png',NULL,'Visualisation d\'un compte utilisateur','View a user account','zu Ã¤ndern','Visualisation d\'un compte utilisateur','View a user account','zu Ã¤ndern','User.php','admin','UserManage',2,'0','0',NULL,'title',43,'2005-11-02 08:47:50','',NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('UserViewDraco','cl',',0,1,2,3,4,5,,','2008-08-12 21:32:36','cl','2004-12-31 22:00:00','cl','Informations utilisateur','User account information','zu Ã¤ndern','UserView.png',NULL,'Visualisation d\'un compte utilisateur','View a user account','zu Ã¤ndern','Visualisation d\'un compte utilisateur','View a user account','zu Ã¤ndern','User.php','draco','FooterDraco',2,'0','0',NULL,'title',8,'2006-03-07 15:46:02','',NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('UserViewFacturier','cl',',0,1,2,3,4,5,,','2008-08-12 21:32:36','cl','2004-12-31 22:00:00','cl','Informations utilisateur','User account information','zu Ã¤ndern','UserView.png',NULL,'Visualisation d\'un compte utilisateur','View a user account','zu Ã¤ndern','Visualisation d\'un compte utilisateur','View a user account','zu Ã¤ndern','User.php','facturier','FooterFacturier',2,'0','0',NULL,'title',6,'2006-03-07 15:46:02','',NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('UserViewPegase','cl',',0,1,2,3,4,5,,','2009-06-22 12:17:03','cl','2004-12-31 22:00:00','cl','Informations utilisateur','User account information','zu Ã¤ndern','UserView.png',NULL,'Visualisation d\'un compte utilisateur','View a user account','zu Ã¤ndern','Visualisation d\'un compte utilisateur','View a user account','zu Ã¤ndern','User.php','pegase','FooterPegase',2,'0','0',NULL,'title',5,'2006-03-07 15:45:53','',NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('UserViewProspec','cl',',0,1,2,3,4,5,,','2009-06-23 07:29:16','cl','2004-12-31 22:00:00','cl','Informations utilisateur','User account information','zu Ã¤ndern','UserView.png',NULL,'Visualisation d\'un compte utilisateur','View a user account','zu Ã¤ndern','Visualisation d\'un compte utilisateur','View a user account','zu Ã¤ndern','User.php','prospec','FooterProspec',2,'0','0',NULL,'title',13,'2006-03-07 15:45:36','',NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('Work','cl',',0,1,2,3,,','2010-05-11 22:48:07','cl','2010-05-11 22:48:07','cl','Répertoire de travail',NULL,NULL,'work.png','work.png','Répertoire partagé',NULL,NULL,'Répertoire partagé',NULL,NULL,'BrowseWork.php','gnose',NULL,1,'1','0',NULL,'titleContent',3088,'2005-10-28 13:14:50','<span style=\\\"font-weight: bold; color: rgb(51, 102, 0);\\\"></span>',NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('ZunoManage','cl',NULL,'2010-01-29 13:31:21','cl','2010-01-29 13:32:01','cl','Gestion de Zuno',NULL,NULL,'zuno.manage.png','zuno.manage.png','Gestion de Zuno',NULL,NULL,'Gestion de Zuno',NULL,NULL,'ZunoManage.php','admin',NULL,1,'0','0',NULL,'titleContent',136,'0000-00-00 00:00:00',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('ZunoRefConfigure','cl',NULL,'2010-05-11 22:48:07','cl',NULL,'cl','Table de référence',NULL,NULL,'ZunoRefTable.png',NULL,' Table de référence',NULL,NULL,'Table de référence',NULL,NULL,'ZunoRefConfigure.php','admin','ZunoManage',1,'0','0',NULL,'titleContent',136,'0000-00-00 00:00:00',NULL,NULL,NULL,'1');

--
-- Dumping data for table `ref_pays`
--

INSERT INTO `ref_pays` VALUES (1,'France','fr');
INSERT INTO `ref_pays` VALUES (2,'Afghanistan','af');
INSERT INTO `ref_pays` VALUES (3,'Afrique du sud','za');
INSERT INTO `ref_pays` VALUES (4,'Albanie','al');
INSERT INTO `ref_pays` VALUES (5,'Algérie','dz');
INSERT INTO `ref_pays` VALUES (6,'Allemagne','de');
INSERT INTO `ref_pays` VALUES (7,'Arabie saoudite','sa');
INSERT INTO `ref_pays` VALUES (8,'Argentine','ar');
INSERT INTO `ref_pays` VALUES (9,'Australie','au');
INSERT INTO `ref_pays` VALUES (10,'Autriche','at');
INSERT INTO `ref_pays` VALUES (11,'Belgique','be');
INSERT INTO `ref_pays` VALUES (12,'Brésil','br');
INSERT INTO `ref_pays` VALUES (13,'Bulgarie','bg');
INSERT INTO `ref_pays` VALUES (14,'Canada','ca');
INSERT INTO `ref_pays` VALUES (15,'Chili','cl');
INSERT INTO `ref_pays` VALUES (16,'Chine (Rép. pop.)','cn');
INSERT INTO `ref_pays` VALUES (17,'Colombie','co');
INSERT INTO `ref_pays` VALUES (18,'Corée, Sud','kr');
INSERT INTO `ref_pays` VALUES (19,'Costa Rica','cr');
INSERT INTO `ref_pays` VALUES (20,'Croatie','hr');
INSERT INTO `ref_pays` VALUES (21,'Danemark','dk');
INSERT INTO `ref_pays` VALUES (22,'Ã‰gypte','eg');
INSERT INTO `ref_pays` VALUES (23,'Ã‰mirats arabes unis','ae');
INSERT INTO `ref_pays` VALUES (24,'Ã‰quateur','ec');
INSERT INTO `ref_pays` VALUES (25,'Ã‰tats-Unis','us');
INSERT INTO `ref_pays` VALUES (26,'El Salvador','sv');
INSERT INTO `ref_pays` VALUES (27,'Espagne','es');
INSERT INTO `ref_pays` VALUES (28,'Finlande','fi');
INSERT INTO `ref_pays` VALUES (29,'Grèce','gr');
INSERT INTO `ref_pays` VALUES (30,'Hong Kong','hk');
INSERT INTO `ref_pays` VALUES (31,'Hongrie','hu');
INSERT INTO `ref_pays` VALUES (32,'Inde','in');
INSERT INTO `ref_pays` VALUES (33,'Indonésie','id');
INSERT INTO `ref_pays` VALUES (34,'Irlande','ie');
INSERT INTO `ref_pays` VALUES (35,'IsraÃ«l','il');
INSERT INTO `ref_pays` VALUES (36,'Italie','it');
INSERT INTO `ref_pays` VALUES (37,'Japon','jp');
INSERT INTO `ref_pays` VALUES (38,'Jordanie','jo');
INSERT INTO `ref_pays` VALUES (39,'Liban','lb');
INSERT INTO `ref_pays` VALUES (40,'Malaisie','my');
INSERT INTO `ref_pays` VALUES (41,'Maroc','ma');
INSERT INTO `ref_pays` VALUES (42,'Mexique','mx');
INSERT INTO `ref_pays` VALUES (43,'Norvège','no');
INSERT INTO `ref_pays` VALUES (44,'Nouvelle-Zélande','nz');
INSERT INTO `ref_pays` VALUES (45,'Pérou','pe');
INSERT INTO `ref_pays` VALUES (46,'Pakistan','pk');
INSERT INTO `ref_pays` VALUES (47,'Pays-Bas','nl');
INSERT INTO `ref_pays` VALUES (48,'Philippines','ph');
INSERT INTO `ref_pays` VALUES (49,'Pologne','pl');
INSERT INTO `ref_pays` VALUES (50,'Porto Rico','pr');
INSERT INTO `ref_pays` VALUES (51,'Portugal','pt');
INSERT INTO `ref_pays` VALUES (52,'République tchèque','cz');
INSERT INTO `ref_pays` VALUES (53,'Roumanie','ro');
INSERT INTO `ref_pays` VALUES (54,'Royaume-Uni','uk');
INSERT INTO `ref_pays` VALUES (55,'Russie','ru');
INSERT INTO `ref_pays` VALUES (56,'Singapour','sg');
INSERT INTO `ref_pays` VALUES (57,'Suède','se');
INSERT INTO `ref_pays` VALUES (58,'Suisse','ch');
INSERT INTO `ref_pays` VALUES (59,'Taiwan','tw');
INSERT INTO `ref_pays` VALUES (60,'Thailande','th');
INSERT INTO `ref_pays` VALUES (61,'Turquie','tr');
INSERT INTO `ref_pays` VALUES (62,'Ukraine','ua');
INSERT INTO `ref_pays` VALUES (63,'Venezuela','ve');
INSERT INTO `ref_pays` VALUES (64,'Yougoslavie','yu');
INSERT INTO `ref_pays` VALUES (65,'Samoa','as');
INSERT INTO `ref_pays` VALUES (66,'Andorre','ad');
INSERT INTO `ref_pays` VALUES (67,'Angola','ao');
INSERT INTO `ref_pays` VALUES (68,'Anguilla','ai');
INSERT INTO `ref_pays` VALUES (69,'Antarctique','aq');
INSERT INTO `ref_pays` VALUES (70,'Antigua et Barbuda','ag');
INSERT INTO `ref_pays` VALUES (71,'Arménie','am');
INSERT INTO `ref_pays` VALUES (72,'Aruba','aw');
INSERT INTO `ref_pays` VALUES (73,'AzerbaÃ¯djan','az');
INSERT INTO `ref_pays` VALUES (74,'Bahamas','bs');
INSERT INTO `ref_pays` VALUES (75,'Bahrain','bh');
INSERT INTO `ref_pays` VALUES (76,'Bangladesh','bd');
INSERT INTO `ref_pays` VALUES (77,'Biélorussie','by');
INSERT INTO `ref_pays` VALUES (78,'Belize','bz');
INSERT INTO `ref_pays` VALUES (79,'Benin','bj');
INSERT INTO `ref_pays` VALUES (80,'Bermudes (Les)','bm');
INSERT INTO `ref_pays` VALUES (81,'Bhoutan','bt');
INSERT INTO `ref_pays` VALUES (82,'Bolivie','bo');
INSERT INTO `ref_pays` VALUES (83,'Bosnie-Herzégovine','ba');
INSERT INTO `ref_pays` VALUES (84,'Botswana','bw');
INSERT INTO `ref_pays` VALUES (85,'Bouvet (ÃŽles)','bv');
INSERT INTO `ref_pays` VALUES (86,'Territoire britannique de l\'océan Indien','io');
INSERT INTO `ref_pays` VALUES (87,'Vierges britanniques (ÃŽles)','vg');
INSERT INTO `ref_pays` VALUES (88,'Brunei','bn');
INSERT INTO `ref_pays` VALUES (89,'Burkina Faso','bf');
INSERT INTO `ref_pays` VALUES (90,'Burundi','bi');
INSERT INTO `ref_pays` VALUES (91,'Cambodge','kh');
INSERT INTO `ref_pays` VALUES (92,'Cameroun','cm');
INSERT INTO `ref_pays` VALUES (93,'Cap Vert','cv');
INSERT INTO `ref_pays` VALUES (94,'Cayman (ÃŽles)','ky');
INSERT INTO `ref_pays` VALUES (95,'République centrafricaine','cf');
INSERT INTO `ref_pays` VALUES (96,'Tchad','td');
INSERT INTO `ref_pays` VALUES (97,'Christmas (ÃŽle)','cx');
INSERT INTO `ref_pays` VALUES (98,'Cocos (ÃŽles)','cc');
INSERT INTO `ref_pays` VALUES (99,'Comores','km');
INSERT INTO `ref_pays` VALUES (100,'Rép. Dém. du Congo','cg');
INSERT INTO `ref_pays` VALUES (101,'Cook (ÃŽles)','ck');
INSERT INTO `ref_pays` VALUES (102,'Cuba','cu');
INSERT INTO `ref_pays` VALUES (103,'Chypre','cy');
INSERT INTO `ref_pays` VALUES (104,'Djibouti','dj');
INSERT INTO `ref_pays` VALUES (105,'Dominique','dm');
INSERT INTO `ref_pays` VALUES (106,'République Dominicaine','do');
INSERT INTO `ref_pays` VALUES (107,'Timor','tp');
INSERT INTO `ref_pays` VALUES (108,'Guinée Equatoriale','gq');
INSERT INTO `ref_pays` VALUES (109,'Erythrée','er');
INSERT INTO `ref_pays` VALUES (110,'Estonie','ee');
INSERT INTO `ref_pays` VALUES (111,'Ethiopie','et');
INSERT INTO `ref_pays` VALUES (112,'Falkland (ÃŽle)','fk');
INSERT INTO `ref_pays` VALUES (113,'Féroé (Îles)','fo');
INSERT INTO `ref_pays` VALUES (114,'Fidji (République des)','fj');
INSERT INTO `ref_pays` VALUES (115,'Guyane franÃ§aise','gf');
INSERT INTO `ref_pays` VALUES (116,'Polynésie française','pf');
INSERT INTO `ref_pays` VALUES (117,'Territoires franÃ§ais du sud','tf');
INSERT INTO `ref_pays` VALUES (118,'Gabon','ga');
INSERT INTO `ref_pays` VALUES (119,'Gambie','gm');
INSERT INTO `ref_pays` VALUES (120,'Géorgie','ge');
INSERT INTO `ref_pays` VALUES (121,'Ghana','gh');
INSERT INTO `ref_pays` VALUES (122,'Gibraltar','gi');
INSERT INTO `ref_pays` VALUES (123,'Groenland','gl');
INSERT INTO `ref_pays` VALUES (124,'Grenade','gd');
INSERT INTO `ref_pays` VALUES (125,'Guadeloupe','gp');
INSERT INTO `ref_pays` VALUES (126,'Guam','gu');
INSERT INTO `ref_pays` VALUES (127,'Guatemala','gt');
INSERT INTO `ref_pays` VALUES (128,'Guinée','gn');
INSERT INTO `ref_pays` VALUES (129,'Guinée-Bissau','gw');
INSERT INTO `ref_pays` VALUES (130,'Guyane','gy');
INSERT INTO `ref_pays` VALUES (131,'HaÃ¯ti','ht');
INSERT INTO `ref_pays` VALUES (132,'Heard et McDonald (ÃŽles)','hm');
INSERT INTO `ref_pays` VALUES (133,'Honduras','hn');
INSERT INTO `ref_pays` VALUES (134,'Islande','is');
INSERT INTO `ref_pays` VALUES (135,'Iran','ir');
INSERT INTO `ref_pays` VALUES (136,'Irak','iq');
INSERT INTO `ref_pays` VALUES (137,'CÃ´te d\'Ivoire','ci');
INSERT INTO `ref_pays` VALUES (138,'JamaÃ¯que','jm');
INSERT INTO `ref_pays` VALUES (139,'Kazakhstan','kz');
INSERT INTO `ref_pays` VALUES (140,'Kenya','ke');
INSERT INTO `ref_pays` VALUES (141,'Kiribati','ki');
INSERT INTO `ref_pays` VALUES (142,'Corée du Nord','kp');
INSERT INTO `ref_pays` VALUES (143,'Koweit','kw');
INSERT INTO `ref_pays` VALUES (144,'Kirghizistan','kg');
INSERT INTO `ref_pays` VALUES (145,'Laos','la');
INSERT INTO `ref_pays` VALUES (146,'Lettonie','lv');
INSERT INTO `ref_pays` VALUES (147,'Lesotho','ls');
INSERT INTO `ref_pays` VALUES (148,'Libéria','lr');
INSERT INTO `ref_pays` VALUES (149,'Libye','ly');
INSERT INTO `ref_pays` VALUES (150,'Liechtenstein','li');
INSERT INTO `ref_pays` VALUES (151,'Lithuanie','lt');
INSERT INTO `ref_pays` VALUES (152,'Luxembourg','lu');
INSERT INTO `ref_pays` VALUES (153,'Macau','mo');
INSERT INTO `ref_pays` VALUES (154,'Macédoine','mk');
INSERT INTO `ref_pays` VALUES (155,'Madagascar','mg');
INSERT INTO `ref_pays` VALUES (156,'Malawi','mw');
INSERT INTO `ref_pays` VALUES (157,'Maldives (ÃŽles)','mv');
INSERT INTO `ref_pays` VALUES (158,'Mali','ml');
INSERT INTO `ref_pays` VALUES (159,'Malte','mt');
INSERT INTO `ref_pays` VALUES (160,'Marshall (ÃŽles)','mh');
INSERT INTO `ref_pays` VALUES (161,'Martinique','mq');
INSERT INTO `ref_pays` VALUES (162,'Mauritanie','mr');
INSERT INTO `ref_pays` VALUES (163,'Maurice','mu');
INSERT INTO `ref_pays` VALUES (164,'Mayotte','yt');
INSERT INTO `ref_pays` VALUES (165,'Micronésie (Etats fédérés de)','fm');
INSERT INTO `ref_pays` VALUES (166,'Moldavie','md');
INSERT INTO `ref_pays` VALUES (167,'Monaco','mc');
INSERT INTO `ref_pays` VALUES (168,'Mongolie','mn');
INSERT INTO `ref_pays` VALUES (169,'Montserrat','ms');
INSERT INTO `ref_pays` VALUES (170,'Mozambique','mz');
INSERT INTO `ref_pays` VALUES (171,'Myanmar','mm');
INSERT INTO `ref_pays` VALUES (172,'Namibie','na');
INSERT INTO `ref_pays` VALUES (173,'Nauru','nr');
INSERT INTO `ref_pays` VALUES (174,'Nepal','np');
INSERT INTO `ref_pays` VALUES (175,'Antilles néerlandaises','an');
INSERT INTO `ref_pays` VALUES (176,'Nouvelle Calédonie','nc');
INSERT INTO `ref_pays` VALUES (177,'Nicaragua','ni');
INSERT INTO `ref_pays` VALUES (178,'Niger','ne');
INSERT INTO `ref_pays` VALUES (179,'Nigeria','ng');
INSERT INTO `ref_pays` VALUES (180,'Niue','nu');
INSERT INTO `ref_pays` VALUES (181,'Norfolk (ÃŽles)','nf');
INSERT INTO `ref_pays` VALUES (182,'Mariannes du Nord (ÃŽles)','mp');
INSERT INTO `ref_pays` VALUES (183,'Oman','om');
INSERT INTO `ref_pays` VALUES (184,'Palau','pw');
INSERT INTO `ref_pays` VALUES (185,'Panama','pa');
INSERT INTO `ref_pays` VALUES (186,'Papouasie-Nouvelle-Guinée','pg');
INSERT INTO `ref_pays` VALUES (187,'Paraguay','py');
INSERT INTO `ref_pays` VALUES (188,'Pitcairn (ÃŽles)','pn');
INSERT INTO `ref_pays` VALUES (189,'Qatar','qa');
INSERT INTO `ref_pays` VALUES (190,'Réunion (La)','re');
INSERT INTO `ref_pays` VALUES (191,'Rwanda','rw');
INSERT INTO `ref_pays` VALUES (192,'Géorgie du Sud et Sandwich du Sud (Îles)','gs');
INSERT INTO `ref_pays` VALUES (193,'Saint-Kitts et Nevis','kn');
INSERT INTO `ref_pays` VALUES (194,'Sainte Lucie','lc');
INSERT INTO `ref_pays` VALUES (195,'Saint Vincent et les Grenadines','vc');
INSERT INTO `ref_pays` VALUES (196,'Samoa','ws');
INSERT INTO `ref_pays` VALUES (197,'Saint-Marin (Rép. de)','sm');
INSERT INTO `ref_pays` VALUES (198,'Sao Tomé et Principe (Rép.)','st');
INSERT INTO `ref_pays` VALUES (199,'Sénégal','sn');
INSERT INTO `ref_pays` VALUES (200,'Seychelles','sc');
INSERT INTO `ref_pays` VALUES (201,'Sierra Leone','sl');
INSERT INTO `ref_pays` VALUES (202,'Slovaquie','sk');
INSERT INTO `ref_pays` VALUES (203,'Slovénie','si');
INSERT INTO `ref_pays` VALUES (204,'Somalie','so');
INSERT INTO `ref_pays` VALUES (205,'Sri Lanka','lk');
INSERT INTO `ref_pays` VALUES (206,'Sainte Hélène','sh');
INSERT INTO `ref_pays` VALUES (207,'Saint Pierre et Miquelon','pm');
INSERT INTO `ref_pays` VALUES (208,'Soudan','sd');
INSERT INTO `ref_pays` VALUES (209,'Suriname','sr');
INSERT INTO `ref_pays` VALUES (210,'Svalbard et Jan Mayen (ÃŽles)','sj');
INSERT INTO `ref_pays` VALUES (211,'Swaziland','sz');
INSERT INTO `ref_pays` VALUES (212,'Syrie','sy');
INSERT INTO `ref_pays` VALUES (213,'Tadjikistan','tj');
INSERT INTO `ref_pays` VALUES (214,'Tanzanie','tz');
INSERT INTO `ref_pays` VALUES (215,'Togo','tg');
INSERT INTO `ref_pays` VALUES (216,'Tokelau','tk');
INSERT INTO `ref_pays` VALUES (217,'Tonga','to');
INSERT INTO `ref_pays` VALUES (218,'Trinité et Tobago','tt');
INSERT INTO `ref_pays` VALUES (219,'Tunisie','tn');
INSERT INTO `ref_pays` VALUES (220,'Turkménistan','tm');
INSERT INTO `ref_pays` VALUES (221,'Turks et CaÃ¯ques (ÃŽles)','tc');
INSERT INTO `ref_pays` VALUES (222,'Tuvalu','tv');
INSERT INTO `ref_pays` VALUES (223,'Îles Mineures éloignées des Etats-Unis','um');
INSERT INTO `ref_pays` VALUES (224,'Ouganda','ug');
INSERT INTO `ref_pays` VALUES (225,'Uruguay','uy');
INSERT INTO `ref_pays` VALUES (226,'Ouzbékistan','uz');
INSERT INTO `ref_pays` VALUES (227,'Vanuatu','vu');
INSERT INTO `ref_pays` VALUES (228,'Vatican (Etat du)','va');
INSERT INTO `ref_pays` VALUES (229,'Vietnam','vn');
INSERT INTO `ref_pays` VALUES (230,'Vierges (ÃŽles)','vi');
INSERT INTO `ref_pays` VALUES (231,'Wallis et Futuna (ÃŽles)','wf');
INSERT INTO `ref_pays` VALUES (232,'Sahara Occidental','eh');
INSERT INTO `ref_pays` VALUES (233,'Yemen','ye');
INSERT INTO `ref_pays` VALUES (234,'ZaÃ¯re','zr');
INSERT INTO `ref_pays` VALUES (235,'Zambie','zm');
INSERT INTO `ref_pays` VALUES (236,'Zimbabwe','zw');
INSERT INTO `ref_pays` VALUES (237,'La Barbad','bb');

--
-- Dumping data for table `ref_prodfamille`
--

INSERT INTO `ref_prodfamille` VALUES (1,'SERVICES STARTX','0','0');
INSERT INTO `ref_prodfamille` VALUES (2,'REDHAT CONTRAT + MEDI','1','1');
INSERT INTO `ref_prodfamille` VALUES (3,'CONTRAT RHEL','1','1');
INSERT INTO `ref_prodfamille` VALUES (4,'MEDIA-KIT REDHAT','1','1');
INSERT INTO `ref_prodfamille` VALUES (5,'PROGRAMME ACADEMIQUE','1','1');
INSERT INTO `ref_prodfamille` VALUES (6,'RED HAT DESKTOP','1','1');
INSERT INTO `ref_prodfamille` VALUES (7,'REDHAT NETWORK','1','1');
INSERT INTO `ref_prodfamille` VALUES (8,'REDHAT ENTERPRISE APPLICATIONS','1','1');
INSERT INTO `ref_prodfamille` VALUES (9,'FORMATIONS REDHAT','0','1');
INSERT INTO `ref_prodfamille` VALUES (10,'SUSE ENTERPRISE LINUX','1','1');
INSERT INTO `ref_prodfamille` VALUES (11,'JBoss Product','1','1');
INSERT INTO `ref_prodfamille` VALUES (12,'AJOUTE MANUELLEMENT','0','0');

--
-- Dumping data for table `ref_redhat_archi`
--

INSERT INTO `ref_redhat_archi` VALUES (1,'x86');
INSERT INTO `ref_redhat_archi` VALUES (2,'Itanium (IPF)');
INSERT INTO `ref_redhat_archi` VALUES (3,'AMD64');
INSERT INTO `ref_redhat_archi` VALUES (4,'iSeries/pSeries (PPC)');
INSERT INTO `ref_redhat_archi` VALUES (6,'x86 et x86_64');
INSERT INTO `ref_redhat_archi` VALUES (5,'zSeries/S390');
INSERT INTO `ref_redhat_archi` VALUES (7,'VMWare');

--
-- Dumping data for table `ref_redhat_contrat`
--

INSERT INTO `ref_redhat_contrat` VALUES (1,'Basic');
INSERT INTO `ref_redhat_contrat` VALUES (2,'Standard');
INSERT INTO `ref_redhat_contrat` VALUES (3,'Premium');
INSERT INTO `ref_redhat_contrat` VALUES (4,'Académique');

--
-- Dumping data for table `ref_renewperiode`
--

INSERT INTO `ref_renewperiode` VALUES (1,'Mensuel');
INSERT INTO `ref_renewperiode` VALUES (3,'Chaque Trimestre');
INSERT INTO `ref_renewperiode` VALUES (6,'Bi-annuel');
INSERT INTO `ref_renewperiode` VALUES (12,'Annuel');
INSERT INTO `ref_renewperiode` VALUES (24,'Tous les 2 ans');
INSERT INTO `ref_renewperiode` VALUES (36,'Tous les 3 ans');

--
-- Dumping data for table `ref_statusaffaire`
--

INSERT INTO `ref_statusaffaire` VALUES (1,'Ouverte',10,'d9dd69');
INSERT INTO `ref_statusaffaire` VALUES (2,'Attente CDC',15,'b0ce55');
INSERT INTO `ref_statusaffaire` VALUES (3,'Rédaction de réponse',20,'68bb36');
INSERT INTO `ref_statusaffaire` VALUES (4,'Réponse envoyée',25,'35b420');
INSERT INTO `ref_statusaffaire` VALUES (5,'Accord de principe',45,'23bc76');
INSERT INTO `ref_statusaffaire` VALUES (6,'Perdu',0,'bc3523');
INSERT INTO `ref_statusaffaire` VALUES (7,'BDC Client recu',50,'10a19c');
INSERT INTO `ref_statusaffaire` VALUES (8,'BDC Fournisseur Crée',52,'2393bc');
INSERT INTO `ref_statusaffaire` VALUES (9,'BDC Fournisseur envoyé',70,'1d77bd');
INSERT INTO `ref_statusaffaire` VALUES (10,'Commande Client traitée',75,'1f4fc8');
INSERT INTO `ref_statusaffaire` VALUES (11,'Commande validé',80,'391ac1');
INSERT INTO `ref_statusaffaire` VALUES (12,'Facture crée',80,'832ddb');
INSERT INTO `ref_statusaffaire` VALUES (13,'Facture validée',85,'932ddb');
INSERT INTO `ref_statusaffaire` VALUES (14,'Facture éditée',90,'891ec6');
INSERT INTO `ref_statusaffaire` VALUES (15,'Facture envoyée',90,'8015bd');
INSERT INTO `ref_statusaffaire` VALUES (16,'Facture réglée',100,'870aa6');
INSERT INTO `ref_statusaffaire` VALUES (17,'Affaire supprimée',0,NULL);
INSERT INTO `ref_statusaffaire` VALUES (18,'Affaire archivée',0,NULL);
INSERT INTO `ref_statusaffaire` VALUES (19,'Affaire désactivée',0,NULL);
INSERT INTO `ref_statusaffaire` VALUES (20,'Affaire re-activée',0,NULL);

--
-- Dumping data for table `ref_statuscommande`
--

INSERT INTO `ref_statuscommande` VALUES (1,0,5,'BDCC enregistré','La commande client est enregistrée. En attente du bon de commande client.',5,'44a');
INSERT INTO `ref_statuscommande` VALUES (2,1,10,'BDCC reçu','Nous avons reçu le bon de commande client dument validé',5,'55b');
INSERT INTO `ref_statuscommande` VALUES (3,2,15,'BDCF Généré','Le bon de commande fournisseur est maintenant généré',5,'56b');
INSERT INTO `ref_statuscommande` VALUES (4,3,25,'BDCF Envoyé','Le bon de commande fournisseur est parti vers votre fournisseur',4,'67c');
INSERT INTO `ref_statuscommande` VALUES (5,4,50,'BDCF reÃ§u par le fournisseur','Le fournisseur Ã  bien reÃ§u le bon de commande',2,'38d');
INSERT INTO `ref_statuscommande` VALUES (6,5,60,'BDCF en cours de traitement','Le fournisseur valide la vente et lance son traitement',3,'49e');
INSERT INTO `ref_statuscommande` VALUES (7,6,90,'Commande expédié','Commande expédié par ',5,'5af');
INSERT INTO `ref_statuscommande` VALUES (8,7,100,'Commande réceptionnée','Réception de la commande par le client ',5,'6bf');
INSERT INTO `ref_statuscommande` VALUES (9,8,100,'Commande terminée','Clôture de la commande',5,'2c1');
INSERT INTO `ref_statuscommande` VALUES (10,9,100,'Commande archivée','La commande est archivée',5,'2c1');

--
-- Dumping data for table `ref_statusdevis`
--

INSERT INTO `ref_statusdevis` VALUES (1,'Crée','44a','15');
INSERT INTO `ref_statusdevis` VALUES (2,'Supprimé','c55','0');
INSERT INTO `ref_statusdevis` VALUES (3,'Enregistré','56b','30');
INSERT INTO `ref_statusdevis` VALUES (4,'Envoyé','38d','50');
INSERT INTO `ref_statusdevis` VALUES (5,'Perdu','d34','100');
INSERT INTO `ref_statusdevis` VALUES (6,'Validé','2c1','100');
INSERT INTO `ref_statusdevis` VALUES (7,'Archivé','c55','0');

--
-- Dumping data for table `ref_statusfacture`
--

INSERT INTO `ref_statusfacture` VALUES (1,10,'Facture créée','La facture est enregistrée dans nos bases','44a');
INSERT INTO `ref_statusfacture` VALUES (2,25,'Facture validée','Facture controlée et validée','55b');
INSERT INTO `ref_statusfacture` VALUES (3,45,'Facture enregistrée','La facture est éditée et enregistrée dans gnose','56b');
INSERT INTO `ref_statusfacture` VALUES (4,50,'Facture Envoyée','La facture est envoyée','56b');
INSERT INTO `ref_statusfacture` VALUES (5,75,'Facture En attente de règlement','La facture est en attente de règlement','56b');
INSERT INTO `ref_statusfacture` VALUES (6,100,'Facture Cloturée','Le règlement vient d\'etre enregistré dans nos bases','56b');
INSERT INTO `ref_statusfacture` VALUES (7,100,'Facture archivée','La facture est maintenant archivée','56b');

--
-- Dumping data for table `ref_statusfacturefournisseur`
--

INSERT INTO `ref_statusfacturefournisseur` VALUES (1,15,'Enregistrée','La facture fournisseur a bien été reçue et a été enregistrée',NULL);
INSERT INTO `ref_statusfacturefournisseur` VALUES (2,50,'Enregistrée en comptabilité','La facture est enregistrée par le service de comptabilitée',NULL);
INSERT INTO `ref_statusfacturefournisseur` VALUES (3,75,'A payer','La facture doit être payée.',NULL);
INSERT INTO `ref_statusfacturefournisseur` VALUES (4,100,'Payée','La facture a été payée',NULL);
INSERT INTO `ref_statusfacturefournisseur` VALUES (5,0,'Archivée','La facture fournisseur est archivée',NULL);

--
-- Dumping data for table `ref_typeentreprise`
--

INSERT INTO `ref_typeentreprise` VALUES (1,'indéfini');
INSERT INTO `ref_typeentreprise` VALUES (2,'prospec');
INSERT INTO `ref_typeentreprise` VALUES (3,'client');
INSERT INTO `ref_typeentreprise` VALUES (4,'grand compte');
INSERT INTO `ref_typeentreprise` VALUES (5,'fournisseur');
INSERT INTO `ref_typeentreprise` VALUES (6,'partenaire');
INSERT INTO `ref_typeentreprise` VALUES (7,'zuno - prospect');
INSERT INTO `ref_typeentreprise` VALUES (8,'zuno - client');

--
-- Dumping data for table `ref_typepayline`
--

INSERT INTO `ref_typepayline` VALUES (1,'Autorisation (100)');
INSERT INTO `ref_typepayline` VALUES (2,'Autorisation + validation (101)');
INSERT INTO `ref_typepayline` VALUES (3,'Autorisation avec cvv (110)');
INSERT INTO `ref_typepayline` VALUES (4,'Autorisation + validation avec cvv (111)');
INSERT INTO `ref_typepayline` VALUES (5,'Autorisation sans cvv (120)');
INSERT INTO `ref_typepayline` VALUES (6,'Autorisation + validation sans cvv (121)');
INSERT INTO `ref_typepayline` VALUES (7,'Autorisation d\'une transaction rejouée (130)');
INSERT INTO `ref_typepayline` VALUES (8,'Autorisation + validation transaction rejouée (131)');
INSERT INTO `ref_typepayline` VALUES (9,'Validation (201)');
INSERT INTO `ref_typepayline` VALUES (10,'Débit (204)');
INSERT INTO `ref_typepayline` VALUES (11,'Remboursement (421)');
INSERT INTO `ref_typepayline` VALUES (12,'Recrédit (422)');
INSERT INTO `ref_typepayline` VALUES (13,'Ré-autorisation (202)');
INSERT INTO `ref_typepayline` VALUES (14,'Création Wallet');
INSERT INTO `ref_typepayline` VALUES (15,'Mise Ã  jour Wallet');
INSERT INTO `ref_typepayline` VALUES (16,'Désactivation Wallet');
INSERT INTO `ref_typepayline` VALUES (17,'Réactivation Wallet');

--
-- Dumping data for table `ref_typeproj`
--

INSERT INTO `ref_typeproj` VALUES (1,'Indéfini','0');
INSERT INTO `ref_typeproj` VALUES (2,'Produits, Services < 2000&euro;','40');
INSERT INTO `ref_typeproj` VALUES (3,'Produits','65');
INSERT INTO `ref_typeproj` VALUES (4,'Services','70');
INSERT INTO `ref_typeproj` VALUES (5,'Projets clefs','10');
INSERT INTO `ref_typeproj` VALUES (6,'Projet ZUNO','70');

--
-- Dumping data for table `renouvellement`
--


--
-- Dumping data for table `send`
--


--
-- Dumping data for table `session`
--


--
-- Dumping data for table `token`
--


--
-- Dumping data for table `transaction`
--


--
-- Dumping data for table `user`
--

INSERT INTO `user` VALUES ('cl','39cc6b9db3689b98c985258c09962f8d','LARUE','Christophe','M.','startx@startx.fr',NULL,0,NULL,'1','0','fr','6e1d1dd19e589f4bf3918ea55fc90192','1920x940');
INSERT INTO `user` VALUES ('mg','b351bb9b0af6e4fc678749675c53ad67','GAILLARD','Maxime','M.','startx@startx.fr',NULL,0,NULL,'1','0','fr','0f98df87c7440c045496f705c7295344',NULL);
INSERT INTO `user` VALUES ('nm','93122a9e4abcba124d5a7d4beaba3f89','MANOCCI','Nicolas','M.','nm@startx.fr',NULL,1,NULL,'1','0','fr','0000','1920x674');

--
-- Dumping data for table `user_droits`
--

INSERT INTO `user_droits` VALUES ('cl',1005);
INSERT INTO `user_droits` VALUES ('cl',1010);
INSERT INTO `user_droits` VALUES ('cl',1015);
INSERT INTO `user_droits` VALUES ('cl',1020);
INSERT INTO `user_droits` VALUES ('cl',1105);
INSERT INTO `user_droits` VALUES ('cl',1110);
INSERT INTO `user_droits` VALUES ('cl',1112);
INSERT INTO `user_droits` VALUES ('cl',1115);
INSERT INTO `user_droits` VALUES ('cl',1120);
INSERT INTO `user_droits` VALUES ('cl',1205);
INSERT INTO `user_droits` VALUES ('cl',1210);
INSERT INTO `user_droits` VALUES ('cl',1215);
INSERT INTO `user_droits` VALUES ('cl',1220);
INSERT INTO `user_droits` VALUES ('cl',1305);
INSERT INTO `user_droits` VALUES ('cl',1310);
INSERT INTO `user_droits` VALUES ('cl',1315);
INSERT INTO `user_droits` VALUES ('cl',1320);
INSERT INTO `user_droits` VALUES ('cl',1405);
INSERT INTO `user_droits` VALUES ('cl',1410);
INSERT INTO `user_droits` VALUES ('cl',1415);
INSERT INTO `user_droits` VALUES ('cl',1420);
INSERT INTO `user_droits` VALUES ('cl',1505);
INSERT INTO `user_droits` VALUES ('cl',1510);
INSERT INTO `user_droits` VALUES ('cl',1515);
INSERT INTO `user_droits` VALUES ('cl',1520);
INSERT INTO `user_droits` VALUES ('cl',1605);
INSERT INTO `user_droits` VALUES ('cl',1610);
INSERT INTO `user_droits` VALUES ('cl',1615);
INSERT INTO `user_droits` VALUES ('cl',1620);
INSERT INTO `user_droits` VALUES ('cl',2005);
INSERT INTO `user_droits` VALUES ('cl',2010);
INSERT INTO `user_droits` VALUES ('cl',2015);
INSERT INTO `user_droits` VALUES ('cl',2020);
INSERT INTO `user_droits` VALUES ('cl',2105);
INSERT INTO `user_droits` VALUES ('cl',2110);
INSERT INTO `user_droits` VALUES ('cl',2115);
INSERT INTO `user_droits` VALUES ('cl',2120);
INSERT INTO `user_droits` VALUES ('cl',2205);
INSERT INTO `user_droits` VALUES ('cl',2210);
INSERT INTO `user_droits` VALUES ('cl',2215);
INSERT INTO `user_droits` VALUES ('cl',2220);
INSERT INTO `user_droits` VALUES ('mg',1005);
INSERT INTO `user_droits` VALUES ('mg',1010);
INSERT INTO `user_droits` VALUES ('mg',1015);
INSERT INTO `user_droits` VALUES ('mg',1020);
INSERT INTO `user_droits` VALUES ('mg',1105);
INSERT INTO `user_droits` VALUES ('mg',1110);
INSERT INTO `user_droits` VALUES ('mg',1112);
INSERT INTO `user_droits` VALUES ('mg',1115);
INSERT INTO `user_droits` VALUES ('mg',1120);
INSERT INTO `user_droits` VALUES ('mg',1205);
INSERT INTO `user_droits` VALUES ('mg',1210);
INSERT INTO `user_droits` VALUES ('mg',1215);
INSERT INTO `user_droits` VALUES ('mg',1220);
INSERT INTO `user_droits` VALUES ('mg',1305);
INSERT INTO `user_droits` VALUES ('mg',1310);
INSERT INTO `user_droits` VALUES ('mg',1315);
INSERT INTO `user_droits` VALUES ('mg',1320);
INSERT INTO `user_droits` VALUES ('mg',1405);
INSERT INTO `user_droits` VALUES ('mg',1410);
INSERT INTO `user_droits` VALUES ('mg',1415);
INSERT INTO `user_droits` VALUES ('mg',1420);
INSERT INTO `user_droits` VALUES ('mg',1505);
INSERT INTO `user_droits` VALUES ('mg',1510);
INSERT INTO `user_droits` VALUES ('mg',1515);
INSERT INTO `user_droits` VALUES ('mg',1520);
INSERT INTO `user_droits` VALUES ('mg',1605);
INSERT INTO `user_droits` VALUES ('mg',1610);
INSERT INTO `user_droits` VALUES ('mg',1615);
INSERT INTO `user_droits` VALUES ('mg',1620);
INSERT INTO `user_droits` VALUES ('mg',2005);
INSERT INTO `user_droits` VALUES ('mg',2010);
INSERT INTO `user_droits` VALUES ('mg',2015);
INSERT INTO `user_droits` VALUES ('mg',2020);
INSERT INTO `user_droits` VALUES ('mg',2105);
INSERT INTO `user_droits` VALUES ('mg',2110);
INSERT INTO `user_droits` VALUES ('mg',2115);
INSERT INTO `user_droits` VALUES ('mg',2120);
INSERT INTO `user_droits` VALUES ('mg',2205);
INSERT INTO `user_droits` VALUES ('mg',2210);
INSERT INTO `user_droits` VALUES ('mg',2215);
INSERT INTO `user_droits` VALUES ('mg',2220);
INSERT INTO `user_droits` VALUES ('nm',1005);
INSERT INTO `user_droits` VALUES ('nm',1010);
INSERT INTO `user_droits` VALUES ('nm',1015);
INSERT INTO `user_droits` VALUES ('nm',1020);
INSERT INTO `user_droits` VALUES ('nm',1105);
INSERT INTO `user_droits` VALUES ('nm',1110);
INSERT INTO `user_droits` VALUES ('nm',1112);
INSERT INTO `user_droits` VALUES ('nm',1115);
INSERT INTO `user_droits` VALUES ('nm',1120);
INSERT INTO `user_droits` VALUES ('nm',1205);
INSERT INTO `user_droits` VALUES ('nm',1210);
INSERT INTO `user_droits` VALUES ('nm',1215);
INSERT INTO `user_droits` VALUES ('nm',1220);
INSERT INTO `user_droits` VALUES ('nm',1305);
INSERT INTO `user_droits` VALUES ('nm',1310);
INSERT INTO `user_droits` VALUES ('nm',1315);
INSERT INTO `user_droits` VALUES ('nm',1320);
INSERT INTO `user_droits` VALUES ('nm',1405);
INSERT INTO `user_droits` VALUES ('nm',1410);
INSERT INTO `user_droits` VALUES ('nm',1415);
INSERT INTO `user_droits` VALUES ('nm',1420);
INSERT INTO `user_droits` VALUES ('nm',1505);
INSERT INTO `user_droits` VALUES ('nm',1510);
INSERT INTO `user_droits` VALUES ('nm',1515);
INSERT INTO `user_droits` VALUES ('nm',1520);
INSERT INTO `user_droits` VALUES ('nm',1605);
INSERT INTO `user_droits` VALUES ('nm',1610);
INSERT INTO `user_droits` VALUES ('nm',1615);
INSERT INTO `user_droits` VALUES ('nm',1620);
INSERT INTO `user_droits` VALUES ('nm',2005);
INSERT INTO `user_droits` VALUES ('nm',2010);
INSERT INTO `user_droits` VALUES ('nm',2015);
INSERT INTO `user_droits` VALUES ('nm',2020);
INSERT INTO `user_droits` VALUES ('nm',2105);
INSERT INTO `user_droits` VALUES ('nm',2110);
INSERT INTO `user_droits` VALUES ('nm',2115);
INSERT INTO `user_droits` VALUES ('nm',2120);
INSERT INTO `user_droits` VALUES ('nm',2205);
INSERT INTO `user_droits` VALUES ('nm',2210);
INSERT INTO `user_droits` VALUES ('nm',2215);
INSERT INTO `user_droits` VALUES ('nm',2220);

--
-- Dumping data for table `user_iphoneConfig`
--

INSERT INTO `user_iphoneConfig` VALUES ('cl','actualite','oui');
INSERT INTO `user_iphoneConfig` VALUES ('cl','affaire','oui');
INSERT INTO `user_iphoneConfig` VALUES ('cl','autocapitalize','non');
INSERT INTO `user_iphoneConfig` VALUES ('cl','autocorrect','non');
INSERT INTO `user_iphoneConfig` VALUES ('cl','commande','oui');
INSERT INTO `user_iphoneConfig` VALUES ('cl','contact','oui');
INSERT INTO `user_iphoneConfig` VALUES ('cl','devis','oui');
INSERT INTO `user_iphoneConfig` VALUES ('cl','facture','oui');
INSERT INTO `user_iphoneConfig` VALUES ('cl','LenghtSearchActualite','10');
INSERT INTO `user_iphoneConfig` VALUES ('cl','LenghtSearchAffaire','5');
INSERT INTO `user_iphoneConfig` VALUES ('cl','LenghtSearchCommande','5');
INSERT INTO `user_iphoneConfig` VALUES ('cl','LenghtSearchContactEnt','5');
INSERT INTO `user_iphoneConfig` VALUES ('cl','LenghtSearchContactPart','5');
INSERT INTO `user_iphoneConfig` VALUES ('cl','LenghtSearchDevis','5');
INSERT INTO `user_iphoneConfig` VALUES ('cl','LenghtSearchFacture','5');
INSERT INTO `user_iphoneConfig` VALUES ('cl','LenghtSearchGeneral','10');
INSERT INTO `user_iphoneConfig` VALUES ('cl','LenghtSearchProduit','5');
INSERT INTO `user_iphoneConfig` VALUES ('cl','navigator','oui');
INSERT INTO `user_iphoneConfig` VALUES ('cl','preference','oui');
INSERT INTO `user_iphoneConfig` VALUES ('cl','search','oui');
INSERT INTO `user_iphoneConfig` VALUES ('cl','send','oui');
INSERT INTO `user_iphoneConfig` VALUES ('mg','actualite','oui');
INSERT INTO `user_iphoneConfig` VALUES ('mg','affaire','oui');
INSERT INTO `user_iphoneConfig` VALUES ('mg','autocapitalize','non');
INSERT INTO `user_iphoneConfig` VALUES ('mg','autocorrect','non');
INSERT INTO `user_iphoneConfig` VALUES ('mg','commande','oui');
INSERT INTO `user_iphoneConfig` VALUES ('mg','contact','oui');
INSERT INTO `user_iphoneConfig` VALUES ('mg','devis','oui');
INSERT INTO `user_iphoneConfig` VALUES ('mg','facture','oui');
INSERT INTO `user_iphoneConfig` VALUES ('mg','LenghtSearchActualite','10');
INSERT INTO `user_iphoneConfig` VALUES ('mg','LenghtSearchAffaire','5');
INSERT INTO `user_iphoneConfig` VALUES ('mg','LenghtSearchCommande','5');
INSERT INTO `user_iphoneConfig` VALUES ('mg','LenghtSearchContactEnt','5');
INSERT INTO `user_iphoneConfig` VALUES ('mg','LenghtSearchContactPart','5');
INSERT INTO `user_iphoneConfig` VALUES ('mg','LenghtSearchDevis','5');
INSERT INTO `user_iphoneConfig` VALUES ('mg','LenghtSearchFacture','5');
INSERT INTO `user_iphoneConfig` VALUES ('mg','LenghtSearchGeneral','10');
INSERT INTO `user_iphoneConfig` VALUES ('mg','LenghtSearchProduit','5');
INSERT INTO `user_iphoneConfig` VALUES ('mg','navigator','oui');
INSERT INTO `user_iphoneConfig` VALUES ('mg','preference','oui');
INSERT INTO `user_iphoneConfig` VALUES ('mg','search','oui');
INSERT INTO `user_iphoneConfig` VALUES ('mg','send','oui');
INSERT INTO `user_iphoneConfig` VALUES ('nm','actualite','oui');
INSERT INTO `user_iphoneConfig` VALUES ('nm','affaire','oui');
INSERT INTO `user_iphoneConfig` VALUES ('nm','autocapitalize','non');
INSERT INTO `user_iphoneConfig` VALUES ('nm','autocorrect','non');
INSERT INTO `user_iphoneConfig` VALUES ('nm','commande','oui');
INSERT INTO `user_iphoneConfig` VALUES ('nm','contact','oui');
INSERT INTO `user_iphoneConfig` VALUES ('nm','devis','oui');
INSERT INTO `user_iphoneConfig` VALUES ('nm','facture','oui');
INSERT INTO `user_iphoneConfig` VALUES ('nm','LenghtSearchActualite','10');
INSERT INTO `user_iphoneConfig` VALUES ('nm','LenghtSearchAffaire','5');
INSERT INTO `user_iphoneConfig` VALUES ('nm','LenghtSearchCommande','5');
INSERT INTO `user_iphoneConfig` VALUES ('nm','LenghtSearchContactEnt','5');
INSERT INTO `user_iphoneConfig` VALUES ('nm','LenghtSearchContactPart','5');
INSERT INTO `user_iphoneConfig` VALUES ('nm','LenghtSearchDevis','5');
INSERT INTO `user_iphoneConfig` VALUES ('nm','LenghtSearchFacture','5');
INSERT INTO `user_iphoneConfig` VALUES ('nm','LenghtSearchGeneral','10');
INSERT INTO `user_iphoneConfig` VALUES ('nm','LenghtSearchProduit','5');
INSERT INTO `user_iphoneConfig` VALUES ('nm','navigator','oui');
INSERT INTO `user_iphoneConfig` VALUES ('nm','preference','oui');
INSERT INTO `user_iphoneConfig` VALUES ('nm','search','oui');
INSERT INTO `user_iphoneConfig` VALUES ('nm','send','oui');
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-05-12  1:44:17
