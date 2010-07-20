-- MySQL dump 10.13  Distrib 5.1.47, for redhat-linux-gnu (x86_64)
--
-- Host: 127.0.0.1    Database: ZunoDev_sxa
-- ------------------------------------------------------
-- Server version	5.1.47

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
-- Dumping data for table `actualite`
--


--
-- Dumping data for table `affaire`
--


--
-- Dumping data for table `appel`
--


--
-- Dumping data for table `banque`
--

INSERT INTO `banque` VALUES (1,'FORTIS','1','1','');

--
-- Dumping data for table `cloud`
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
-- Dumping data for table `journal_banque`
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
INSERT INTO `module` VALUES ('pontComptable','oui');

--
-- Dumping data for table `pontcomptable_histo`
--


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
INSERT INTO `ref_activite` VALUES (5,'Pêche, aquaculture');
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

INSERT INTO `ref_departement` VALUES ('01','Ain ','Bourg-en-Bresse ','Rhône-Alpes');
INSERT INTO `ref_departement` VALUES ('02','Aisne','Laon','Picardie');
INSERT INTO `ref_departement` VALUES ('03','Allier','Moulins','Auvergne');
INSERT INTO `ref_departement` VALUES ('04','Alpes de Hautes-Provence','Digne','PACA');
INSERT INTO `ref_departement` VALUES ('05','Hautes-Alpes','Gap','PACA');
INSERT INTO `ref_departement` VALUES ('06','Alpes-Maritimes','Nice','PACA');
INSERT INTO `ref_departement` VALUES ('07','Ardèche','Privas','Rhône-Alpes');
INSERT INTO `ref_departement` VALUES ('08','Ardennes','Charleville-Mézières','Champagne-Ardenne');
INSERT INTO `ref_departement` VALUES ('09','Ariège','Foix','Midi-Pyrénées');
INSERT INTO `ref_departement` VALUES ('10','Aube','Troyes','Champagne-Ardenne');
INSERT INTO `ref_departement` VALUES ('11','Aude','Carcassonne','Languedoc-Roussillon');
INSERT INTO `ref_departement` VALUES ('12','Aveyron','Rodez','Midi-Pyrénées');
INSERT INTO `ref_departement` VALUES ('13','Bouches-du-Rhône','Marseille','PACA');
INSERT INTO `ref_departement` VALUES ('14','Calvados','Caen','Basse-Normandie');
INSERT INTO `ref_departement` VALUES ('15','Cantal','Aurillac','Auvergne');
INSERT INTO `ref_departement` VALUES ('16','Charente','Angoulême','Poitou-Charentes');
INSERT INTO `ref_departement` VALUES ('17','Charente-Maritime','La Rochelle','Poitou-Charentes');
INSERT INTO `ref_departement` VALUES ('18','Cher','Bourges','Centre');
INSERT INTO `ref_departement` VALUES ('19','Corrèze','Tulle','Limousin');
INSERT INTO `ref_departement` VALUES ('20','Corse','Ajaccio','Corse');
INSERT INTO `ref_departement` VALUES ('21','Côte-d\'Or','Dijon','Bourgogne');
INSERT INTO `ref_departement` VALUES ('22','Côtes d\'Armor','Saint-Brieuc','Bretagne');
INSERT INTO `ref_departement` VALUES ('23','Creuse','Guéret','Limousin');
INSERT INTO `ref_departement` VALUES ('24','Dordogne','Périgueux','Aquitaine');
INSERT INTO `ref_departement` VALUES ('25','Doubs','Besançon','Franche-Comté');
INSERT INTO `ref_departement` VALUES ('26','Drôme','Valence','Rhône-Alpes');
INSERT INTO `ref_departement` VALUES ('27','Eure','Évreux','Haute-Normandie');
INSERT INTO `ref_departement` VALUES ('28','Eure-et-Loir','Chartres','Centre');
INSERT INTO `ref_departement` VALUES ('29','Finistère','Quimper','Bretagne');
INSERT INTO `ref_departement` VALUES ('30','Gard','Nîmes','Languedoc-Roussillon');
INSERT INTO `ref_departement` VALUES ('31','Haute-Garonne','Toulouse','Midi-Pyrénées');
INSERT INTO `ref_departement` VALUES ('32','Gers','Auch','Midi-Pyrénées');
INSERT INTO `ref_departement` VALUES ('33','Gironde','Bordeaux','Aquitaine');
INSERT INTO `ref_departement` VALUES ('34','Hérault','Montpellier','Languedoc-Roussillon');
INSERT INTO `ref_departement` VALUES ('35','Ille-et-Vilaine','Rennes','Bretagne');
INSERT INTO `ref_departement` VALUES ('36','Indre','Châteauroux','Centre');
INSERT INTO `ref_departement` VALUES ('37','Indre-et-Loire','Tours','Centre');
INSERT INTO `ref_departement` VALUES ('38','Isère','Grenoble','Rhône-Alpes');
INSERT INTO `ref_departement` VALUES ('39','Jura','Lons-le-Saunier','Franche-Comté');
INSERT INTO `ref_departement` VALUES ('40','Landes','Mont-de-Marsan','Aquitaine');
INSERT INTO `ref_departement` VALUES ('41','Loir-et-Cher','Blois','Centre');
INSERT INTO `ref_departement` VALUES ('42','Loire','Saint-Étienne','Rhône-Alpes');
INSERT INTO `ref_departement` VALUES ('43','Haute-Loire','Le Puy-en-Velay','Auvergne');
INSERT INTO `ref_departement` VALUES ('44','Loire-Atlantique','Nantes','Pays de la Loire');
INSERT INTO `ref_departement` VALUES ('45','Loiret','Orléans','Centre');
INSERT INTO `ref_departement` VALUES ('46','Lot','Cahors','Midi-Pyrénées');
INSERT INTO `ref_departement` VALUES ('47','Lot-et-Garonne','Agen','Aquitaine');
INSERT INTO `ref_departement` VALUES ('48','Lozère','Mende','Languedoc-Roussillon');
INSERT INTO `ref_departement` VALUES ('49','Maine-et-Loire','Angers','Pays de la Loire');
INSERT INTO `ref_departement` VALUES ('50','Manche','Saint-Lô','Basse-Normandie');
INSERT INTO `ref_departement` VALUES ('51','Marne','Châlons-en-Champagne','Champagne-Ardenne');
INSERT INTO `ref_departement` VALUES ('52','Haute-Marne','Chaumont','Champagne-Ardenne');
INSERT INTO `ref_departement` VALUES ('53','Mayenne','Laval','Pays de la Loire');
INSERT INTO `ref_departement` VALUES ('54','Meurthe-et-Moselle','Nancy','Lorraine');
INSERT INTO `ref_departement` VALUES ('55','Meuse','Bar-le-Duc','Lorraine');
INSERT INTO `ref_departement` VALUES ('56','Morbihan','Vannes','Bretagne');
INSERT INTO `ref_departement` VALUES ('57','Moselle','Metz','Lorraine');
INSERT INTO `ref_departement` VALUES ('58','Nièvre','Nevers','Bourgogne');
INSERT INTO `ref_departement` VALUES ('59','Nord','Lille','Nord-Pas-de-Calais');
INSERT INTO `ref_departement` VALUES ('60','Oise','Beauvais','Picardie');
INSERT INTO `ref_departement` VALUES ('61','Orne','Alençon','Basse-Normandie');
INSERT INTO `ref_departement` VALUES ('62','Pas-de-Calais','Arras','Nord-Pas-de-Calais');
INSERT INTO `ref_departement` VALUES ('63','Puy-de-Dôme','Clermont-Ferrand','Auvergne');
INSERT INTO `ref_departement` VALUES ('64','Pyrénées-Atlantiques','Pau','Aquitaine');
INSERT INTO `ref_departement` VALUES ('65','Hautes-Pyrénées','Tarbes','Midi-Pyrénées');
INSERT INTO `ref_departement` VALUES ('66','Pyrénées-Orientales','Perpignan','Languedoc-Roussillon');
INSERT INTO `ref_departement` VALUES ('67','Bas-Rhin','Strasbourg','Alsace');
INSERT INTO `ref_departement` VALUES ('68','Haut-Rhin','Colmar','Alsace');
INSERT INTO `ref_departement` VALUES ('69','Rhône','Lyon','Rhône-Alpes');
INSERT INTO `ref_departement` VALUES ('70','Haute-Saöne','Vesoul','Franche-Comté');
INSERT INTO `ref_departement` VALUES ('71','Saône-et-Loire','Mâcon','Bourgogne');
INSERT INTO `ref_departement` VALUES ('72','Sarthe','Le Mans','Pays de la Loire');
INSERT INTO `ref_departement` VALUES ('73','Savoie','Chambéry','Rhône-Alpes');
INSERT INTO `ref_departement` VALUES ('74','Haute-Savoie','Annecy','Rhône-Alpes');
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
INSERT INTO `ref_departement` VALUES ('88','Vosges','Épinal','Lorraine');
INSERT INTO `ref_departement` VALUES ('89','Yonne','Auxerre','Bourgogne');
INSERT INTO `ref_departement` VALUES ('90','Territoire-de-Belfort','Belfort','Franche-Comté');
INSERT INTO `ref_departement` VALUES ('91','Essonne','Évry','Ile-de-France');
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
-- Dumping data for table `ref_droit_user`
--

INSERT INTO `ref_droit_user` VALUES (0,'effectuer toutes les actions');
INSERT INTO `ref_droit_user` VALUES (5,'rechercher');
INSERT INTO `ref_droit_user` VALUES (10,'visualiser une fiche');
INSERT INTO `ref_droit_user` VALUES (14,'changer le statut');
INSERT INTO `ref_droit_user` VALUES (17,'modifier une fiche');
INSERT INTO `ref_droit_user` VALUES (20,'ajouter une fiche');
INSERT INTO `ref_droit_user` VALUES (35,'cloner une fiche');
INSERT INTO `ref_droit_user` VALUES (37,'archiver une fiche');
INSERT INTO `ref_droit_user` VALUES (40,'supprimer une fiche');
INSERT INTO `ref_droit_user` VALUES (45,'voir les statistiques');
INSERT INTO `ref_droit_user` VALUES (59,'envoyer un document');
INSERT INTO `ref_droit_user` VALUES (69,'gérer les documents');
INSERT INTO `ref_droit_user` VALUES (99,'interdire toute action');

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

INSERT INTO `ref_page` VALUES ('actualite','nm',NULL,'2010-06-03 20:25:06','nm','2009-06-24 08:59:10','nm','actualite',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'actualite.php','normal',NULL,1,'1','0',NULL,NULL,NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('AdminManual','cl',',0,,','2008-08-12 19:32:36','cl','2004-12-31 21:00:00','cl','Manuel de l\'admin','Admin manual','Handbuch des Verwalters','ManuelAdmin.png','ManuelAdmin.png','C\'est le manuel de l\'administrateur','sumary for english person','Handbuch des Verwalters','Manuel de l\'administrateur','Manual for technical staff','Handbuch des Verwalters',NULL,'admin','Help',1,'1','0',NULL,'titleContent','mettre ici toutes les informations disponibles pour les webmasters et les superadministrateurs.',NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('AffaireCreate','cl',NULL,'2010-05-30 09:37:37','cl',NULL,'cl','Affaire',NULL,NULL,'affaire.png','affaire.create.png','Création d\'un affaire',NULL,NULL,'Création d\'un affaire',NULL,NULL,'AffaireCreate.php','draco','Create',2,'1','0',NULL,'title',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('AffaireFiche','cl',',0,1,2,,','2010-05-31 10:35:00','cl',NULL,'cl','Fiche affaire',NULL,NULL,'affaire.png','affaire.png','Fiche affaire',NULL,NULL,'Fiche affaire',NULL,NULL,'Affaire.php','draco','ListeAffaire',1,'0','0',NULL,'title',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('Application','cl',',0,1,,','2009-12-27 13:15:49','cl','2004-12-31 21:00:00','cl','Application',NULL,'Anwendung','appli.png','Application.png','Informations sur l\'application',NULL,'Anwendung','Information sur l\'application',NULL,'Anwendung','Application.php','admin',NULL,5,'1','0',NULL,'fullContent','<p align=\\\"center\\\" class=\\\"MsoNormal\\\" style=\\\"text-align: center;\\\">\r\n											<b><font size=\\\"5\\\" color=\\\"#990000\\\">GNOSE<br />\r\n											</font></b>\r\n											<font size=\\\"4\\\" color=\\\"#990000\\\">\r\n											 <br />\r\n											Etymologiquement : la connaissance \r\n											(grec gnosis). <br />\r\n											</font><font size=\\\"4\\\" color=\\\"#000080\\\"><br /></font></p><p class=\\\"MsoNormal\\\" style=\\\"text-align: justify;\\\"><font color=\\\"#000000\\\">Gnose signifie \r\n											connaissance. Il s\\\'agit de la \r\n											connaissance intérieure, par \r\n											laquelle l\\\'homme appréhende le \r\n											divin, indépendamment de tout dogme, \r\n											de tout enseignement; la gnose \r\n											s\\\'apparente ainsi au mysticisme. <br /></font></p><p class=\\\"MsoNormal\\\" style=\\\"text-align: justify;\\\"><span style=\\\"color: black;\\\">La Gnose \r\n											est une connaissance universelle. \r\n											Lorsque nous étudions les \r\n											civilisations antiques (Égyptienne, \r\n											Maya, Celte, Grecque, Hindoue), nous \r\n											découvrons à la base les mêmes \r\n											enseignements. C’est cette \r\n											connaissance unique que les \r\n											véritables sages de tous les temps \r\n											(Confucius, Socrate, Bouddha, Jésus, \r\n											Krishna, Blavatsky, Steiner…) sont \r\n											venus livrer à l’humanité.</span></p>\r\n											<p class=\\\"MsoNormal\\\" style=\\\"text-align: justify;\\\">\r\n											<span style=\\\"color: black;\\\">La Gnose \r\n											dévoile les clés théoriques et \r\n											pratiques indispensables à l’homme \r\n											et à la femme modernes qui désirent accéder à une plus grande connaissance.<br /></span></p><p class=\\\"MsoNormal\\\" style=\\\"text-align: justify;\\\"><font size=\\\"4\\\" color=\\\"#990000\\\"><br /></font></p><div style=\\\"text-align: center;\\\"><font size=\\\"4\\\" color=\\\"#990000\\\">Pratiquement : partage de connaissance<br /><br /></font></div><p class=\\\"MsoNormal\\\" style=\\\"text-align: justify;\\\"><font size=\\\"4\\\" color=\\\"#990000\\\">\r\n											</font><font color=\\\"#000000\\\">L\\\'objectif du projet GNOSE est de fournir aux entreprises une solution flexible pour héberger l\\\'ensemble de leurs connaissances métiers.<br />Le serveur de fichier GNOSE repose sur une architecture flexible et entièrement ouverte, vous permettant d\\\'adapter son fonctionnement à votre language. <br />La connaissance (et son partage) étant au coeur de nos entreprise moderne, cette solution à été conçue pour :<br /></font></p><ul><li><font color=\\\"#000000\\\">Sécuriser l\\\'accès et la diffusion des informations</font></li><li><font color=\\\"#000000\\\">Améliorer la recherche d\\\'information</font></li><li><font color=\\\"#000000\\\">Dématerialiser l\\\'accès aux données</font></li><li><font color=\\\"#000000\\\">Simplifier les interfaces</font></li></ul><i><span style=\\\"color: black; font-family: Times New Roman;\\\"></span></i>',NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('ApplicationConfig','cl',NULL,'2007-03-28 08:54:31','cl','2004-12-31 21:00:00','cl','Configuration',NULL,NULL,'AppliConfig.png','ApplicationConfig.png','Configuration de l\'application',NULL,NULL,'Configuration',NULL,NULL,'ApplicationConfig.php','admin','Application',4,'1','0',NULL,'titleContent','Cette rubrique vous permet de configurer les différents paramètres de votre application. Le bouton de netyage du cache vous permet de réactualiser les informations de votre site. Cette opération est automatiquement réalisée par le système. <br />Si vous constatez que certaines informations ne sont pas à jour, n\\\'hésitez pas à lancer une opération de nettoyage du cache.<br /><br /><br />',NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('Archive','cl',',0,1,2,,','2010-05-07 22:43:03','cl','2004-12-31 21:00:00','cl','Archives',NULL,NULL,'archive.png','archives.png','Répertoires d\'archives',NULL,NULL,'Répertoires d\'archives',NULL,NULL,'BrowseArchive.php','gnose',NULL,10,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('AvoirCreate','nm',',0,1,2,,','2010-05-30 09:41:36','nm',NULL,'nm','Avoir',NULL,NULL,'facture.png','facture.png','Créer un avoir',NULL,NULL,'Créer un avoir',NULL,NULL,'FactureCreate.php?type=avoir','facturier','CreateF',2,'1','0',NULL,NULL,NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('CommandeFiche','cl',',0,1,2,,','2010-05-28 01:28:20','cl',NULL,'cl','Fiche commande',NULL,NULL,'commande.png','affaire.png','Fiche commande',NULL,NULL,'Fiche commande',NULL,NULL,'Commande.php','pegase','ListeCommande',1,'0','0',NULL,'title',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('Contact','nm',NULL,'2010-05-30 10:12:35','nm',NULL,'nm','Contact',NULL,NULL,'contact.png','contact.png',NULL,NULL,NULL,'Nouveau Contact',NULL,NULL,'Contact.php','prospec','CreateC',2,'1','0',NULL,NULL,NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('Create','cl',NULL,'2010-05-30 10:01:40','cl','2010-05-30 10:01:40','cl','Ajouter',NULL,NULL,'affaire.png','affaire.create.png','Création d\'un affaire',NULL,NULL,'Création d\'un affaire',NULL,NULL,'AffaireCreate.php','draco',NULL,1,'1','0',NULL,'title',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('CreateC','cl',',0,1,2,,','2010-05-30 20:32:34','cl',NULL,'cl','Ajouter',NULL,NULL,'recherche.prospec.png','recherche.prospec.png','Recherche des contacts',NULL,NULL,'Recherche des contacts',NULL,NULL,'fiche.php','prospec',NULL,3,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('CreateF','cl',',0,1,2,,','2010-05-30 10:18:07','cl','2010-05-30 09:40:46','cl','Ajouter',NULL,NULL,'facture.png','facture.png','Créer une facture',NULL,NULL,'Créer une facture',NULL,NULL,'FactureCreate.php','facturier',NULL,1,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('CreateP','nm',NULL,'2010-05-31 10:36:38','nm','2010-05-30 10:03:33','nm','Ajouter',NULL,NULL,'produit.png','produit.png','Ajouter un produit',NULL,NULL,'Ajouter un produit',NULL,NULL,'Produit.php','produit',NULL,1,'1','0',NULL,NULL,NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('csv2db','cl',NULL,'2009-06-24 13:16:53','cl',NULL,'cl','Import de données',NULL,NULL,'csvExport.png','csvExport.png',NULL,NULL,NULL,'Import CSV',NULL,NULL,'csv2db.php','admin','Application',1,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('Devis','cl',NULL,'2010-06-01 08:35:17','cl',NULL,'cl','Devis',NULL,NULL,'devis.png','devis.png',' devis',NULL,NULL,'Devis',NULL,NULL,'Devis.php','draco','DevisListe',1,'0','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('DevisCreate','cl',NULL,'2010-05-30 09:37:40','cl',NULL,'cl','Devis',NULL,NULL,'devis.png','devis.create.png','Créer un devis',NULL,NULL,'Créer un devis',NULL,NULL,'DevisCreate.php','draco','Create',4,'1','0',NULL,'title',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('DevisListe','cl',NULL,'2010-05-30 10:23:32','cl',NULL,'cl','Recherche de devis',NULL,NULL,'devis.liste.png','devis.png','Recherche de devis',NULL,NULL,'Recherche de devis',NULL,NULL,'DevisListe.php','draco',NULL,3,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('DevisListe1','cl',NULL,'2010-05-31 10:35:46','cl','2010-05-30 10:24:35','cl','Devis crée',NULL,NULL,'devis.liste.png','devis.perdu.png','Liste devis crées',NULL,NULL,'Liste devis crées',NULL,NULL,'DevisListe.php?status_dev=1','draco','DevisListe',1,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('DevisListe4','cl',NULL,'2010-05-30 10:24:35','cl','2010-05-30 10:24:35','cl','Devis envoyés',NULL,NULL,'devis.liste.png','devis.perdu.png','Liste devis envoyés',NULL,NULL,'Liste devis envoyés',NULL,NULL,'DevisListe.php?status_dev=4','draco','DevisListe',2,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('DevisListe5','cl',NULL,'2010-05-30 10:22:52','cl',NULL,'cl','Devis perdus',NULL,NULL,'devis.liste.png','devis.perdu.png','Liste devis perdus ou annulés',NULL,NULL,'Liste devis perdus ou annulés',NULL,NULL,'DevisListeAnnule.php','draco','DevisListe',3,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('DevisListe6','cl',NULL,'2010-05-30 09:31:07','cl',NULL,'cl','Devis gagnés',NULL,NULL,'devis.liste.png','devis.gagne.png',' Liste des devis gagnés',NULL,NULL,'Liste des devis gagnés',NULL,NULL,'DevisListeGagne.php','draco','DevisListe',4,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('DevisListe7','cl',NULL,'2010-05-30 10:24:35','cl','2010-05-30 10:24:35','cl','Devis archivés',NULL,NULL,'devis.liste.png','devis.gagne.png',' Liste des devis archivés',NULL,NULL,'Liste des devis archivés',NULL,NULL,'DevisListe.php?status_dev=7','draco','DevisListe',5,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('DevisListeRenew','cl',NULL,'2010-05-30 09:59:51','cl',NULL,'cl','Devis à renouveler',NULL,NULL,'devis.listerenew.png','devis.renew.png',' Liste des devis à renouveler',NULL,NULL,'Liste des devis a renouveler',NULL,NULL,'DevisListeRenew.php','draco','DevisListe',3,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('FactureCreate','cl',',0,1,2,,','2010-05-30 09:41:36','cl',NULL,'cl','Facture',NULL,NULL,'facture.png','facture.png','Créer une facture',NULL,NULL,'Créer une facture',NULL,NULL,'FactureCreate.php','facturier','CreateF',1,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('FactureFiche','cl',',0,1,2,,','2010-05-30 01:41:29','cl',NULL,'cl','Facture',NULL,NULL,'facture.png','facture.png','Facture',NULL,NULL,'Facture',NULL,NULL,'Facture.php','facturier','ListeFacture',1,'0','0',NULL,'title',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('FactureFournisseurCreate','nm',',0,1,2,,','2010-05-30 09:50:59','nm',NULL,'nm','Facture fournisseur',NULL,NULL,'factureFourn.png','factureFourn.png','Créer une facture fournisseur',NULL,NULL,'Ajouter une facture fournisseur',NULL,NULL,'FactureFournisseurCreate.php','facturier','CreateF',3,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('FactureFournisseurFiche','nm',',0,1,2,,','2010-05-30 10:33:29','nm',NULL,'nm','Facture fournisseur',NULL,NULL,'factureFourn.png','factureFourn.png','Fiche facture fournisseur',NULL,NULL,'Fiche Facture fournisseur',NULL,NULL,'FactureFournisseur.php','facturier','ListeFacture',1,'0','0',NULL,'title',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('FacturePopup','cl',',0,1,2,,','2008-08-12 19:32:36','cl',NULL,'cl','Facture',NULL,NULL,'facture.png','facture.png','Facture',NULL,NULL,'Facture',NULL,NULL,'PopupFacture.php','facturier','ListeFacture',1,'0','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('FooterAdmin','cl',NULL,'2005-10-28 11:14:50','cl','2004-12-31 21:00:00','cl','Outils','Toolbox',NULL,NULL,NULL,'Lies outils du site','Website toolbox',NULL,'les outils','Toolbox',NULL,NULL,'admin',NULL,10,'0','1',NULL,NULL,NULL,'<br />\r\n',NULL,'1');
INSERT INTO `ref_page` VALUES ('Fournisseur','nm',NULL,'2010-05-30 10:04:26','nm',NULL,'nm','Fournisseur',NULL,NULL,'fournisseur.png','fournisseur.png','Gestion des fournisseurs',NULL,NULL,'Gestion des fournisseurs',NULL,NULL,'Fournisseur.php','produit','CreateP',3,'1','0',NULL,NULL,NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('FournisseurListe','nm',NULL,'2010-05-30 10:07:29','nm',NULL,'nm','Fournisseurs',NULL,NULL,'fournisseur.png','fournisseur.png','Liste des fournisseurs',NULL,NULL,'Liste des fournisseurs',NULL,NULL,'FournisseurListe.php','produit','PListe',2,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('History','cl',',0,1,2,3,,','2010-05-30 10:17:42','cl','2004-12-31 21:00:00','cl','Historique',NULL,NULL,'History.png','History.png','Historique d\'un enregistrement',NULL,NULL,'Historique d\'un enregistrement',NULL,NULL,'History.php','gnose',NULL,4,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('ImageManage','cl',',0,1,2,3,,','2008-08-12 19:32:36','cl','2004-12-31 21:00:00','cl','Bibliothèque d\'image','Picture gallery','Bildbibliothek','image.manage.png','ImageManage.png','Permet de gérer les images','Allow you to manage images','Bildbibliothek','Gestion des images du site','Website image management','Bildbibliothek','ImageManage.php','admin','PageManage',3,'1','0',NULL,'title',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('index','cl',NULL,'2010-06-03 01:10:15','cl','2009-09-24 00:20:00','cl','Accueil','Home Page','Empfangg',NULL,NULL,'Accueil','Homepage','Empfang','Accueil','Homepage','Empfang','index.php','normal',NULL,1,'0','0',NULL,'Accueil','<h1>Bienvenue dans votre espace de travail ZUNO</h1><br/><br/><br/>\r\n<p>Cet extranet vous est d&eacute;di&eacute;. Il vous permet d\'acc&eacute;der &agrave; l\'espace de travail de votre entreprise. Vous pouvez vous connecter en cliquant sur le bouton connexion (en haut &agrave; droite) et en saisisant vos informations de connexion dans la boite de dialogue ad hoc.</p>\r\n<p>L\'acc&egrave;s &agrave; ce site internet n\'est accessible qu\'aux membres autoris&eacute;s par le titulaire du compte de ce service. La connexions &eacute;tant personnelle, vous devez avoir re&ccedil;u vos informations de connexion avant de pouvoir ac&eacute;der &agrave; ce service. Si ne disposez pas de ces &eacute;l&eacute;ments, merci de quitter cet espace de travail. <br />\r\nSi vous avez perdu vos informations de connexion, merci de nous adresser votre demande en vous <a title=\"Formulaire de demande d\'information de connexion\" href=\"zuno.fr/loginPerdu.php\">rendant ici</a>.</p>\r\n<p>Il est rappel&eacute; que la consultation de cet outil est soumise &agrave; la             r&eacute;glementation fran&ccedil;aise en vigueur et que toute information quelle qu\'elle             soit et quel qu\'en soit le support n\'emporte aucune exhaustivit&eacute;.</p>\r\n<p>&nbsp;</p>\r\n<h1 style=\"margin-top: 0pt;\">Un mode de travail intelligent...</h1><br/><br/><br/>\r\n<p>Zuno est d&eacute;di&eacute;e &agrave; la gestion commerciale des PME/TPE. Articul&eacute; autour de plusieurs modules inter-d&eacute;pendant, elle vous permet de g&eacute;rer de bout en bout votre processus commercial. En partant de vos prospec ou client, vous pouvez g&eacute;rer l\'ensemble de votre m&eacute;tier par le biais d\'interface simple et rapide. Vous trouverez ainsi les modules:</p>\r\n<p>&nbsp;<img width=\"200\" height=\"38\" alt=\"logo-modules-client.png\" src=\"./img/imgbank/zuno/logo-modules-client.png\" /></p>\r\n<p><img width=\"200\" height=\"38\" alt=\"logo-modules-client.png\" src=\"./img/imgbank/zuno/logo-modules-manager.png\" /></p>\r\n<p><img width=\"199\" height=\"38\" alt=\"logo-modules-client.png\" src=\"./img/imgbank/zuno/logo-modules-commercial.png\" /> </p>\r\n<p>&nbsp;<img width=\"200\" height=\"38\" alt=\"logo-modules-client.png\" src=\"./img/imgbank/zuno/logo-modules-facture.png\" /></p>\r\n<p><img width=\"200\" height=\"38\" alt=\"logo-modules-client.png\" src=\"./img/imgbank/zuno/logo-modules-ventes.png\" /><br />\r\n<br />',NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('Journaux','cl',NULL,'2009-12-16 11:00:37','cl',NULL,'cl','Journaux',NULL,NULL,'journaux.png','journaux.png',' Journaux',NULL,NULL,'Journaux',NULL,NULL,NULL,'admin','Application',1,'1','0',NULL,'fullContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('ListeAffaire','cl',',0,1,2,,','2010-05-31 10:33:46','cl',NULL,'cl','Recherche d\'affaire',NULL,NULL,'affaire.png','affaire.png','Recherche d\'affaire',NULL,NULL,'Recherche d\'affaire',NULL,NULL,'ListeAffaires.php','draco',NULL,2,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('ListeCommande','cl',',0,1,2,,','2010-05-31 10:33:24','cl',NULL,'cl','Rechercher',NULL,NULL,'commande.png','commande.png',' Recherche des commandes',NULL,NULL,'Recherche des commandes',NULL,NULL,'CommandeListe.php','pegase',NULL,4,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('ListeCommande1','cl',',0,1,2,,','2010-05-30 10:34:18','cl','2010-05-30 09:03:24','cl','Commandes enregistrées',NULL,NULL,'commande.png','commande.png',' Liste des commandes enregistrées',NULL,NULL,'Liste des commandes enregistrées',NULL,NULL,'CommandeListe.php?status_cmd=1','pegase','ListeCommande',1,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('ListeCommande10','cl',',0,1,2,,','2010-05-30 09:19:21','cl','2010-05-30 09:03:24','cl','Commandes archivées',NULL,NULL,'commande.png','commande.png',' Liste des commandes archivées',NULL,NULL,'Liste des commandes archivées',NULL,NULL,'CommandeListe.php?status_cmd=10','pegase','ListeCommande',5,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('ListeCommande4','cl',',0,1,2,,','2010-05-31 10:33:06','cl','2010-05-30 09:03:24','cl','Commandes envoyées',NULL,NULL,'commande.png','commande.png',' Liste des commandes envoyées',NULL,NULL,'Liste des commandes envoyées',NULL,NULL,'CommandeListe.php?status_cmd=4','pegase','ListeCommande',2,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('ListeCommande7','cl',',0,1,2,,','2010-05-30 10:18:25','cl','2010-05-30 09:03:24','cl','Commandes expédiées',NULL,NULL,'commande.png','commande.png',' Liste des commandes expédiées',NULL,NULL,'Liste des commandes expédiées',NULL,NULL,'CommandeListe.php?status_cmd=7','pegase','ListeCommande',3,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('ListeCommande9','cl',',0,1,2,,','2010-05-30 09:19:25','cl','2010-05-30 09:03:24','cl','Commandes terminées',NULL,NULL,'commande.png','commande.png',' Liste des commandes terminées',NULL,NULL,'Liste des commandes terminées',NULL,NULL,'CommandeListe.php?status_cmd=9','pegase','ListeCommande',4,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('ListeFacture','cl',',0,1,2,,','2010-05-30 20:23:35','cl',NULL,'cl','Recherche de factures',NULL,NULL,'facture.png','facture.png','Recherche de factures',NULL,NULL,'Recherche de factures',NULL,NULL,'FactureListe.php','facturier',NULL,1,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('ListeFacture1','cl',',0,1,2,,','2010-05-30 10:34:10','cl',NULL,'cl','Crées',NULL,NULL,'facture.cloture.png','facture.cloture.png',' Liste des factures crées',NULL,NULL,'Liste des factures crées',NULL,NULL,'FactureListe.php?status_fact=1','facturier','ListeFacture',1,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('ListeFacture4','cl',',0,1,2,,','2010-05-30 10:33:45','cl',NULL,'cl','Envoyées',NULL,NULL,'facture.cloture.png','facture.cloture.png',' Liste des factures envoyées',NULL,NULL,'Liste des factures envoyées',NULL,NULL,'FactureListe.php?status_fact=4','facturier','ListeFacture',2,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('ListeFacture5','cl',',0,1,2,,','2010-05-30 09:43:25','cl',NULL,'cl','En attente',NULL,NULL,'facture.cloture.png','facture.cloture.png',' Liste des factures en attente',NULL,NULL,'Liste des factures en attente',NULL,NULL,'FactureListe.php?status_fact=5','facturier','ListeFacture',3,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('ListeFacture7','cl',',0,1,2,,','2010-05-30 10:34:03','cl',NULL,'cl','Archivées',NULL,NULL,'facture.cloture.png','facture.cloture.png',' Liste des factures archivées',NULL,NULL,'Liste des factures archivées',NULL,NULL,'FactureListe.php?status_fact=7','facturier','ListeFacture',4,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('ListeFactureCloture','cl',',0,1,2,,','2010-05-30 09:43:25','cl',NULL,'cl','Cloturées',NULL,NULL,'facture.cloture.png','facture.cloture.png',' Liste des factures terminées',NULL,NULL,'Liste des factures cloturées',NULL,NULL,'FactureListeCloture.php','facturier','ListeFacture',2,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('ListeFactureF1','cl',',0,1,2,,','2010-05-30 10:32:58','cl','2010-05-30 09:59:39','cl','Crées',NULL,NULL,'factureFourn.png','factureFourn.png',' Liste des factures fournisseurs créées',NULL,NULL,'Liste des factures fournisseurs créées',NULL,NULL,'FactureFournisseurListe.php?status_factfourn=1','facturier','ListeFactureFournisseur',1,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('ListeFactureF3','cl',',0,1,2,,','2010-05-30 10:32:51','cl','2010-05-30 09:59:39','cl','A payer',NULL,NULL,'factureFourn.png','factureFourn.png',' Liste des factures fournisseurs à payer',NULL,NULL,'Liste des factures fournisseurs à payer',NULL,NULL,'FactureFournisseurListe.php?status_factfourn=3','facturier','ListeFactureFournisseur',2,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('ListeFactureF4','cl',',0,1,2,,','2010-05-30 10:33:36','cl','2010-05-30 10:32:37','cl','Payées',NULL,NULL,'factureFourn.png','factureFourn.png',' Liste des factures fournisseurs payées',NULL,NULL,'Liste des factures fournisseurs payées',NULL,NULL,'FactureFournisseurListe.php?status_factfourn=4','facturier','ListeFactureFournisseur',3,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('ListeFactureF5','cl',',0,1,2,,','2010-05-30 10:33:02','cl','2010-05-30 09:59:39','cl','Archivées',NULL,NULL,'factureFourn.png','factureFourn.png',' Liste des factures fournisseurs archivées',NULL,NULL,'Liste des factures fournisseurs archivées',NULL,NULL,'FactureFournisseurListe.php?status_factfourn=5','facturier','ListeFactureFournisseur',4,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('ListeFactureFournisseur','nm',',0,1,2,,','2010-05-30 10:33:05','nm',NULL,'nm','Facture fournisseur',NULL,NULL,'factureFourn.png','factureFourn.png','Liste des factures fournisseurs',NULL,NULL,'Liste des factures fournisseur',NULL,NULL,'FactureFournisseurListe.php','facturier',NULL,3,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('ListeLeads','cl',',0,1,2,,','2010-05-30 17:19:15','cl',NULL,'cl','Projets',NULL,NULL,'projet.liste.png','projet.liste.png',' Liste des projets',NULL,NULL,'Liste des projets',NULL,NULL,'ListeLeads.php','prospec','RechercheContact',3,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('Login','cl',NULL,'2010-06-03 01:10:14','cl','2010-02-19 00:36:29','cl','Authentification','Authentication',NULL,NULL,NULL,'Authentification des utilisateurs','Connect to authorized area',NULL,'Authentification des utilisateurs','Connect to authorized area',NULL,'Login.php','normal',NULL,1,'0','0','','Accueil',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('LogView','cl',NULL,'2010-05-23 21:45:36','cl','2004-12-31 21:00:00','cl','activité',NULL,NULL,NULL,NULL,'voir le journal d\'activité',NULL,NULL,'Journal d\'activité',NULL,NULL,'LogView.php','admin','Journaux',NULL,'1','0',NULL,'title',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('monBureau','cl',NULL,'2010-06-03 09:41:19','cl','2009-09-28 11:24:45','cl','Mon bureau',NULL,NULL,NULL,NULL,'Mon bureau',NULL,NULL,'Mon bureau',NULL,NULL,'Bureau.php','normal',NULL,1,'0','0',NULL,NULL,NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('PageCreate','cl',',0,1,,','2009-06-30 05:49:43','cl','2004-12-31 21:00:00','cl','Créer','Create','zu schaffen','page.modif.png',NULL,'Création d\'une nouvelle page','Create a new page','zu schaffen','Création d\'une nouvelle page du site','Create a new page','zu schaffen','PageCreate.php','admin','PageManage',1,'1','0',NULL,NULL,NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('PageDelete','cl',',0,1,,','2008-08-12 19:32:36','cl','2004-12-31 21:00:00','cl','Supprimer','Delete','abzuschaffen','page.modif.png',NULL,'Suppression d\'une page','Delete a page','abzuschaffen','Supprimer les informations d\'une page du site','Delete content of a page','abzuschaffen','PageDelete.php','admin','PageManage',2,'0','0',NULL,NULL,NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('PageDeleteFile','cl',',0,1,,','2008-08-13 17:46:26','cl',NULL,'cl','Supprimer un document','Delete a document','Delete a document',NULL,NULL,' suppression d\'un document a télécharger','Delete a document','Delete a document','Supprimer un document','Delete a document','Delete a document','PageDeleteFile.php','admin','PageModif',1,'0','0',NULL,'titleContent',NULL,'contenu en francais',NULL,'1');
INSERT INTO `ref_page` VALUES ('PageManage','cl',',0,1,2,,','2010-02-07 13:30:14','cl','2004-12-31 21:00:00','cl','Gestion des pages','Pages management','Seiten','page.manage.png','PageManage.png','Permet de gérer de nouvelles pages','Allow you to manage new pages','Seiten','Gestion des pages du site','Website page management','Seiten','PageManage.php','admin',NULL,1,'1','0',NULL,'title',NULL,'<span class=\\\"important\\\"><br /></span>',NULL,'1');
INSERT INTO `ref_page` VALUES ('PageModif','cl',',0,1,2,3,,','2010-01-29 21:39:24','cl','2009-08-27 16:03:36','cl','Modifier','Modify','zu ändern','page.modif.png',NULL,'Modification d\'une page','Modify content of a page','zu ändern','Modifier les informations d\'une page du site','Modify content of a page','zu ändern','PageModif.php','admin','PageManage',2,'1','0',NULL,NULL,' ',NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('PageModif4Lang','cl',',0,1,2,3,,','2008-08-12 19:32:36','cl','2004-12-31 21:00:00','cl','Modifier le contenu','modify content','zu ändern','page.modif.png',NULL,'Modification du contenu d\'une page','modify content for english','zu ändern','Modifier les informations d\'une page du site','modify content for english','zu ändern','PageModif4Lang.php','admin','PageModif',2,'0','0',NULL,'title',NULL,'<span class=\\\"important\\\"><br /></span>',NULL,'1');
INSERT INTO `ref_page` VALUES ('PageModifFile','cl',',0,1,,','2008-08-13 17:46:26','cl',NULL,'cl','Modification d\'un document','Modify a document',NULL,NULL,NULL,' modfification d\'un document','Modify a document',NULL,'Modification d\'un document','Modify a document',NULL,'PageModifFile.php','admin','PageModif',2,'0','0',NULL,'title',NULL,'contenu de la page en francais',NULL,'1');
INSERT INTO `ref_page` VALUES ('PageModifLot','cl',',0,1,2,3,,','2008-08-12 19:32:36','cl','2004-12-31 21:00:00','cl','Modifier un lot','Modify','zu ändern','page.modif.png',NULL,'Modification d\'un lot de pages','Modify content of a bunch of page','zu ändern','Modification d\'un lot de pages','Modify content of pages','zu ändern','PageModifLot.php','admin','PageManage',2,'0','0',NULL,NULL,NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('PagePopupImportOdt','cl',',0,1,,','2010-01-29 21:38:48','cl','2004-12-31 21:00:00','cl','Importation de contenu',NULL,NULL,NULL,NULL,'Importation de contenu',NULL,NULL,'Importation de contenu',NULL,NULL,'PagePopup.ImportOdt.php','admin','PageManage',3,'0','0',NULL,'titleContent','L\\\'importation d\\\'un contenu a partir d\\\'un document ODT vous permet de simplifier en une opération l\\\'ajout de contenu dans votre site. <br />Si votre document contien des images, \r\nun répertoire (du nom de l\\\'ID de votre page) sera ajouté dans votre photothèque. Il contiendra toutes les images de votre document. <br /><br />La mise en page de votre document sera adaptée au web et certain styles seront automatiquement supprimé. Si vous constatez une trop grande différence avec votre document original, \r\nvous devez en simplifier sa structure (styles trop complexes ou imbriqués)<br /><br /><br />',NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('PListe','nm',NULL,'2010-05-31 10:37:02','nm','2010-05-30 10:06:30','nm','Recherche',NULL,NULL,'produit.png','produit.png','Liste des produits',NULL,NULL,'Liste des produits',NULL,NULL,'ProduitListe.php','produit',NULL,2,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('PontComptable','cl',',0,1,2,,','2010-05-30 09:51:55','cl','2010-05-30 09:51:55','cl','Pont Comptable',NULL,NULL,'PontComptable.png','PontComptable.png','Facture',NULL,NULL,'Pont Comptable',NULL,NULL,'PontComptable.php','facturier','PontComptableHisto',4,'0','0',NULL,'title',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('PontComptableCreate','cl',',0,1,2,,','2010-05-30 20:32:22','cl','2010-05-22 07:14:53','cl','Nouvel export',NULL,NULL,'PontComptableCreate.png','PontComptableCreate.png','Nouveau fichier d\'export',NULL,NULL,'Nouveau fichier d\'export',NULL,NULL,'PontComptable.php?action=new','facturier','PontComptableHisto',1,'1','0',NULL,'title',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('PontComptableHisto','cl',',0,1,2,,','2010-05-30 08:24:09','cl','2010-05-22 22:04:57','cl','Pont Comptable',NULL,NULL,'PontComptable.png','PontComptable.png','Historiques des exports comptable',NULL,NULL,'Historiques des exports comptable',NULL,NULL,'PontComptableHisto.php','facturier',NULL,6,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('PopupAffaire','cl',',0,1,2,,','2010-05-07 18:57:54','cl',NULL,'cl','Popup affaires',NULL,NULL,'affaire.png','affaire.png','Popup affaires',NULL,NULL,'Popup affaires',NULL,NULL,'PopupAffaire.php','draco','ListeAffaire',1,'0','0',NULL,'title',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('PopupAppel','cl',',0,1,2,,','2009-06-24 12:37:12','cl',NULL,'cl','Popup Appel',NULL,NULL,'appel.png',NULL,'Popup Appel',NULL,NULL,'Popup Appel',NULL,NULL,'PopupAppel.php','prospec','ProspecFiche',1,'0','0',NULL,'title',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('PopupContact','cl',',0,1,2,,','2009-06-09 10:22:49','cl',NULL,'cl','Popup Contact',NULL,NULL,'contact.png',NULL,'Popup Contact',NULL,NULL,'Popup Contact',NULL,NULL,'PopupContact.php','prospec','ProspecFiche',1,'0','0',NULL,'title',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('PopupEntreprise','cl',',0,1,2,,','2010-05-18 12:20:11','cl',NULL,'cl','Popup Entreprise',NULL,NULL,'entreprise.png',NULL,'Popup Entreprise',NULL,NULL,'Popup Entreprise',NULL,NULL,'PopupEntreprise.php','prospec','ProspecFiche',1,'0','0',NULL,'title',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('PopupProjet','cl',',0,1,2,,','2009-10-01 21:30:43','cl',NULL,'cl','Popup Projet',NULL,NULL,'projet.png',NULL,'Popup Projet',NULL,NULL,'Popup Projet',NULL,NULL,'PopupProjet.php','prospec','ProspecFiche',1,'0','0',NULL,'title',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('Produit','nm',NULL,'2010-05-30 10:04:26','nm',NULL,'nm','Produit',NULL,NULL,'produit.png','produit.png','Gestion des produits',NULL,NULL,'Gestion des produits',NULL,NULL,'Produit.php','produit','CreateP',1,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('ProduitListe','nm',NULL,'2010-05-30 10:07:29','nm',NULL,'nm','Produits',NULL,NULL,'produit.png','produit.png','Liste des produits',NULL,NULL,'Liste des produits',NULL,NULL,'ProduitListe.php','produit','PListe',1,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('Projet','cl',',0,1,2,,','2010-05-07 20:34:38','cl','2010-01-29 13:33:56','cl','Projet',NULL,NULL,'projet.png','projet.png','Projet',NULL,NULL,'Projet',NULL,NULL,'Projet.php','prospec','ListeLeads',1,'0','0',NULL,'title',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('ProspecFiche','cl',',0,1,2,,','2010-05-30 10:12:35','cl',NULL,'cl','Entreprise',NULL,NULL,'entreprise.png','entreprise.png','Entreprise',NULL,NULL,'Nouvelle entreprise',NULL,NULL,'fiche.php','prospec','CreateC',1,'1','0',NULL,'title',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('ProspecListe','cl',',0,1,2,,','2010-05-30 10:34:33','cl',NULL,'cl','Relances',NULL,NULL,'relance.png','relance.png','listes de relance',NULL,NULL,'Liste de prospection',NULL,NULL,'ListeProspec.php','prospec','RechercheContact',2,'1','0',NULL,'title',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('RechercheCEnt','cl',',0,1,2,,','2010-05-30 20:32:30','cl','2010-05-30 10:17:14','cl','Entreprise / Contact',NULL,NULL,'recherche.prospec.png','recherche.prospec.png','Recherche des contacts',NULL,NULL,'Recherche des contacts',NULL,NULL,'Recherche.php','prospec','RechercheContact',1,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('RechercheContact','cl',',0,1,2,,','2010-05-30 10:10:56','cl','2010-05-30 10:11:48','cl','Rechercher',NULL,NULL,'recherche.prospec.png','recherche.prospec.png','Recherche des contacts',NULL,NULL,'Recherche des contacts',NULL,NULL,'Recherche.php','prospec',NULL,3,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('RedactorManual','cl',',0,,','2008-08-12 19:32:36','cl','2004-12-31 21:00:00','cl','Manuel du redacteur','Writer manual','Handbuch des Verfassers','ManuelRedact.png','ManuelRedact.png','Guide d\'utilisation de l\'administration','Writer manual','Handbuch des Verfassers','Le manuel du redacteur','Writer manual','Handbuch des Verfassers',NULL,'admin','Help',1,'1','0',NULL,'titleContent','<h3>1. Les formulaires d\\\'administrations</h3><h4 style=\\\"font-style: italic; text-decoration: underline;\\\">1.1 Les langues</h4>L\\\'ensemble du site est disponible dans plusieurs langues selon votre configuration (voir dans la partie<a href=\\\"https://dev.startx.fr/sxframework/manage/application.php\\\"> Application</a> ). Les formulaires d\\\'administration du site (pages, newsletter, autres plugins...) sont alors traduits dans la langue demandée. Si aucune traduction n\\\'est disponible pour tout ou partie de la page demandée, le texte de la langue par defaut est alors affiché.<br /><img vspace=\\\"6\\\" hspace=\\\"6\\\" border=\\\"0\\\" align=\\\"right\\\" alt=\\\"différentes langues\\\" src=\\\"https://dev.startx.fr/sxframework/img/imgbank/manul_pour_les_langues.png\\\" /><br />Lorsqu\\\'un administrateur utilise l\\\'administration dans une langue différente de la langue par defaut, il est en mesure de modifier le contenu des pages pour cette langue. Ainsi certaines rubriques ou champs de formulaires seront affiché en vert, signalant une modification valable uniquement pour la langue en cours.<br />Les autres champs conservent leurs portée globales.<br /><br /><h4 style=\\\"font-style: italic; text-decoration: underline;\\\">1.2 Les symboles</h4><img vspace=\\\"5\\\" hspace=\\\"5\\\" border=\\\"0\\\" align=\\\"left\\\" src=\\\"https://dev.startx.fr/sxframework/img/imgbank/manuel_champs_oblig.png\\\" />Les formulaires peuvent contenir plusieurs types de champs, boites et autres listes de séléction. Lorsqu\\\'un de ces éléments est suivi d\\\'un simple &quot;!&quot; transparent, cela signifie que cette information est obligatoire. Le champ associé doit impérativement être rempli.<br />Le panneaux &quot;attention&quot; indique que des contraites particulières doivent être réspéctée pour complété ce champs. Vous devez alos survoler l\\\'image pour obtenir plus de détail.<br /><br />Il vous est toujours possible de survoler les titres devant chaque champs afin d\\\'avoir un détail du contenu a fournir. Cela peut s\\\'averer utile lorsque vous ne savez pas à quoi correspond cette case.<br /><br /><br /><br />',NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('SendMail','cl',',0,1,2,21,22,3,4,41,42,43,44,5,5','2010-02-15 14:26:16','cl',NULL,'cl','Envoi d\\\'un mail',NULL,NULL,'fileSend.png','fileSend.png',' Envoi d\\\'un mail',NULL,NULL,'Envoi d\\\'un mail',NULL,NULL,'PopupSendMail.php','prospec','ProspecFiche',2,'0','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('SessionDetail','cl',NULL,'2009-12-16 11:01:20','cl','2004-12-31 21:00:00','cl','Détail de la session','Session detail',NULL,NULL,NULL,'détail de la session','Session detail',NULL,'Détail d\'une session','Session detail',NULL,'SessionDetail.php','admin','Application',3,'0','0',NULL,'title',NULL,'<span class=\\\"important\\\"><br /></span>',NULL,'1');
INSERT INTO `ref_page` VALUES ('SessionView','cl',NULL,'2009-12-16 11:01:06','cl','2004-12-31 21:00:00','cl','Session','Session log',NULL,NULL,NULL,'Journal de session','Session log',NULL,'Journal de session','Session log',NULL,'SessionView.php','admin','Journaux',1,'1','0',NULL,'title',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('StatFacture','cl',',0,1,2,,','2010-05-30 09:51:42','cl',NULL,'cl','Statistiques',NULL,NULL,'statG.png','stat.png','Statistiques de facturation',NULL,NULL,'Statistiques de facturation',NULL,NULL,'FactureStats.php','facturier',NULL,3,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('StatView','cl',',0,1,2,,','2008-08-14 09:28:07','cl','2004-12-31 21:00:00','cl','Statistiques','Statistics','Statistiken','statistique.png','StatView.png','Gestions des statistiques','Statistics management','Statistiken','Gestions des statistiques','Statistics management','Statistiken','StatView.php','admin','PageManage',8,'1','0',NULL,'title','<br />\r\n',NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('UserCreate','cl',',0,1,2,,','2009-06-22 10:18:18','cl','2004-12-31 21:00:00','cl','Créer','Create admin account','zu schaffen','user.admin.create.png',NULL,'Création d\'un compte utilisateur','Create admin account','zu schaffen','Création d\'un compte utilisateur','Create admin account','zu schaffen','UserCreate.php','admin','UserManage',1,'1','0',NULL,NULL,'<br />\r\n',NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('UserDelete','cl',',0,1,,','2008-08-12 19:32:36','cl','2004-12-31 21:00:00','cl','Supprimer','Delete admin account','Abschaffung eines Verwalters','user.admin.del.png',NULL,'Suppression d\'un utilisateur','Delete admin account','Abschaffung eines Verwalters','Suppression d\'un utilisateur','Delete admin account','Abschaffung eines Verwalters','UserDelete.php','admin','UserManage',2,'0','0',NULL,'title',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('UserManage','cl',',0,1,2,,','2009-12-27 02:15:57','cl','2004-12-31 21:00:00','cl','Utilisateurs','Users','Benutzer','user.manage.png','UserManage.png','Gestions des utilisateurs','Users accounts management','Benutzer','Gestions des utilisateurs','Users accounts management','Benutzer','UserManage.php','admin',NULL,2,'1','0',NULL,'title',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('UserModif','cl',',0,1,2,,','2009-12-16 11:00:05','cl','2004-12-31 21:00:00','cl','Modifier','Admin account','zu ändern','user.admin.modif.png',NULL,'Modification d\'un compte utilisateur','Modify a manager account','zu ändern','Modification d\'un compte utilisateur','Modify a manager account','zu ändern','UserModif.php','admin','UserManage',2,'1','0',NULL,'title','<br />\r\n',NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('UserView','cl',',0,1,2,3,4,5,,','2010-05-30 08:52:37','cl','2010-05-30 08:33:53','cl','Informations utilisateur','User account information','zu ändern','UserView.png',NULL,'Visualisation d\'un compte utilisateur','View a user account','zu ändern','Visualisation d\'un compte utilisateur','View a user account','zu ändern','User.php','normal',NULL,2,'0','0',NULL,'title','',NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('Work','cl',',0,1,2,3,,','2010-05-24 01:37:22','cl','2004-12-31 21:00:00','cl','Répertoire de travail',NULL,NULL,'work.png','work.png','Répertoire partagé',NULL,NULL,'Répertoire partagé',NULL,NULL,'BrowseWork.php','gnose',NULL,1,'1','0',NULL,'titleContent','<span style=\\\"font-weight: bold; color: rgb(51, 102, 0);\\\"></span>',NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('ZunoManage','cl',NULL,'2010-01-29 12:31:21','cl','2010-01-29 12:32:01','cl','Gestion de Zuno',NULL,NULL,'zuno.manage.png','zuno.manage.png','Gestion de Zuno',NULL,NULL,'Gestion de Zuno',NULL,NULL,'ZunoManage.php','admin',NULL,1,'0','0',NULL,'titleContent',NULL,NULL,NULL,'1');
INSERT INTO `ref_page` VALUES ('ZunoRefConfigure','cl',NULL,'2010-01-29 12:31:21','cl',NULL,'cl','Table de référence',NULL,NULL,'ZunoRefTable.png',NULL,' Table de référence',NULL,NULL,'Table de référence',NULL,NULL,'ZunoRefConfigure.php','admin','ZunoManage',1,'0','0',NULL,'titleContent',NULL,NULL,NULL,'1');

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
INSERT INTO `ref_pays` VALUES (22,'Égypte','eg');
INSERT INTO `ref_pays` VALUES (23,'Émirats arabes unis','ae');
INSERT INTO `ref_pays` VALUES (24,'Équateur','ec');
INSERT INTO `ref_pays` VALUES (25,'États-Unis','us');
INSERT INTO `ref_pays` VALUES (26,'El Salvador','sv');
INSERT INTO `ref_pays` VALUES (27,'Espagne','es');
INSERT INTO `ref_pays` VALUES (28,'Finlande','fi');
INSERT INTO `ref_pays` VALUES (29,'Grèce','gr');
INSERT INTO `ref_pays` VALUES (30,'Hong Kong','hk');
INSERT INTO `ref_pays` VALUES (31,'Hongrie','hu');
INSERT INTO `ref_pays` VALUES (32,'Inde','in');
INSERT INTO `ref_pays` VALUES (33,'Indonésie','id');
INSERT INTO `ref_pays` VALUES (34,'Irlande','ie');
INSERT INTO `ref_pays` VALUES (35,'Israël','il');
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
INSERT INTO `ref_pays` VALUES (73,'Azerbaïdjan','az');
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
INSERT INTO `ref_pays` VALUES (85,'Bouvet (Îles)','bv');
INSERT INTO `ref_pays` VALUES (86,'Territoire britannique de l\'océan Indien','io');
INSERT INTO `ref_pays` VALUES (87,'Vierges britanniques (Îles)','vg');
INSERT INTO `ref_pays` VALUES (88,'Brunei','bn');
INSERT INTO `ref_pays` VALUES (89,'Burkina Faso','bf');
INSERT INTO `ref_pays` VALUES (90,'Burundi','bi');
INSERT INTO `ref_pays` VALUES (91,'Cambodge','kh');
INSERT INTO `ref_pays` VALUES (92,'Cameroun','cm');
INSERT INTO `ref_pays` VALUES (93,'Cap Vert','cv');
INSERT INTO `ref_pays` VALUES (94,'Cayman (Îles)','ky');
INSERT INTO `ref_pays` VALUES (95,'République centrafricaine','cf');
INSERT INTO `ref_pays` VALUES (96,'Tchad','td');
INSERT INTO `ref_pays` VALUES (97,'Christmas (Île)','cx');
INSERT INTO `ref_pays` VALUES (98,'Cocos (Îles)','cc');
INSERT INTO `ref_pays` VALUES (99,'Comores','km');
INSERT INTO `ref_pays` VALUES (100,'Rép. Dém. du Congo','cg');
INSERT INTO `ref_pays` VALUES (101,'Cook (Îles)','ck');
INSERT INTO `ref_pays` VALUES (102,'Cuba','cu');
INSERT INTO `ref_pays` VALUES (103,'Chypre','cy');
INSERT INTO `ref_pays` VALUES (104,'Djibouti','dj');
INSERT INTO `ref_pays` VALUES (105,'Dominique','dm');
INSERT INTO `ref_pays` VALUES (106,'République Dominicaine','do');
INSERT INTO `ref_pays` VALUES (107,'Timor','tp');
INSERT INTO `ref_pays` VALUES (108,'Guinée Equatoriale','gq');
INSERT INTO `ref_pays` VALUES (109,'Érythrée','er');
INSERT INTO `ref_pays` VALUES (110,'Estonie','ee');
INSERT INTO `ref_pays` VALUES (111,'Ethiopie','et');
INSERT INTO `ref_pays` VALUES (112,'Falkland (Île)','fk');
INSERT INTO `ref_pays` VALUES (113,'Féroé (Îles)','fo');
INSERT INTO `ref_pays` VALUES (114,'Fidji (République des)','fj');
INSERT INTO `ref_pays` VALUES (115,'Guyane française','gf');
INSERT INTO `ref_pays` VALUES (116,'Polynésie française','pf');
INSERT INTO `ref_pays` VALUES (117,'Territoires français du sud','tf');
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
INSERT INTO `ref_pays` VALUES (131,'Haïti','ht');
INSERT INTO `ref_pays` VALUES (132,'Heard et McDonald (Îles)','hm');
INSERT INTO `ref_pays` VALUES (133,'Honduras','hn');
INSERT INTO `ref_pays` VALUES (134,'Islande','is');
INSERT INTO `ref_pays` VALUES (135,'Iran','ir');
INSERT INTO `ref_pays` VALUES (136,'Irak','iq');
INSERT INTO `ref_pays` VALUES (137,'Côte d\'Ivoire','ci');
INSERT INTO `ref_pays` VALUES (138,'Jamaïque','jm');
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
INSERT INTO `ref_pays` VALUES (157,'Maldives (Îles)','mv');
INSERT INTO `ref_pays` VALUES (158,'Mali','ml');
INSERT INTO `ref_pays` VALUES (159,'Malte','mt');
INSERT INTO `ref_pays` VALUES (160,'Marshall (Îles)','mh');
INSERT INTO `ref_pays` VALUES (161,'Martinique','mq');
INSERT INTO `ref_pays` VALUES (162,'Mauritanie','mr');
INSERT INTO `ref_pays` VALUES (163,'Maurice','mu');
INSERT INTO `ref_pays` VALUES (164,'Mayotte','yt');
INSERT INTO `ref_pays` VALUES (165,'Micronésie (États fédérés de)','fm');
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
INSERT INTO `ref_pays` VALUES (181,'Norfolk (Îles)','nf');
INSERT INTO `ref_pays` VALUES (182,'Mariannes du Nord (Îles)','mp');
INSERT INTO `ref_pays` VALUES (183,'Oman','om');
INSERT INTO `ref_pays` VALUES (184,'Palau','pw');
INSERT INTO `ref_pays` VALUES (185,'Panama','pa');
INSERT INTO `ref_pays` VALUES (186,'Papouasie-Nouvelle-Guinée','pg');
INSERT INTO `ref_pays` VALUES (187,'Paraguay','py');
INSERT INTO `ref_pays` VALUES (188,'Pitcairn (Îles)','pn');
INSERT INTO `ref_pays` VALUES (189,'Qatar','qa');
INSERT INTO `ref_pays` VALUES (190,'Réunion (La)','re');
INSERT INTO `ref_pays` VALUES (191,'Rwanda','rw');
INSERT INTO `ref_pays` VALUES (192,'Géorgie du Sud et Sandwich du Sud (Îles)','gs');
INSERT INTO `ref_pays` VALUES (193,'Saint-Kitts et Nevis','kn');
INSERT INTO `ref_pays` VALUES (194,'Sainte Lucie','lc');
INSERT INTO `ref_pays` VALUES (195,'Saint Vincent et les Grenadines','vc');
INSERT INTO `ref_pays` VALUES (196,'Samoa','ws');
INSERT INTO `ref_pays` VALUES (197,'Saint-Marin (Rép. de)','sm');
INSERT INTO `ref_pays` VALUES (198,'São Tomé et Príncipe (Rép.)','st');
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
INSERT INTO `ref_pays` VALUES (210,'Svalbard et Jan Mayen (Îles)','sj');
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
INSERT INTO `ref_pays` VALUES (221,'Turks et Caïques (Îles)','tc');
INSERT INTO `ref_pays` VALUES (222,'Tuvalu','tv');
INSERT INTO `ref_pays` VALUES (223,'Îles Mineures Éloignées des États-Unis','um');
INSERT INTO `ref_pays` VALUES (224,'Ouganda','ug');
INSERT INTO `ref_pays` VALUES (225,'Uruguay','uy');
INSERT INTO `ref_pays` VALUES (226,'Ouzbékistan','uz');
INSERT INTO `ref_pays` VALUES (227,'Vanuatu','vu');
INSERT INTO `ref_pays` VALUES (228,'Vatican (Etat du)','va');
INSERT INTO `ref_pays` VALUES (229,'Vietnam','vn');
INSERT INTO `ref_pays` VALUES (230,'Vierges (Îles)','vi');
INSERT INTO `ref_pays` VALUES (231,'Wallis et Futuna (Îles)','wf');
INSERT INTO `ref_pays` VALUES (232,'Sahara Occidental','eh');
INSERT INTO `ref_pays` VALUES (233,'Yemen','ye');
INSERT INTO `ref_pays` VALUES (234,'Zaïre','zr');
INSERT INTO `ref_pays` VALUES (235,'Zambie','zm');
INSERT INTO `ref_pays` VALUES (236,'Zimbabwe','zw');
INSERT INTO `ref_pays` VALUES (237,'La Barbad','bb');

--
-- Dumping data for table `ref_prodfamille`
--

INSERT INTO `ref_prodfamille` VALUES (1,'Services','0','0','S');
INSERT INTO `ref_prodfamille` VALUES (2,'Prestations','0','0','S1');
INSERT INTO `ref_prodfamille` VALUES (3,'Système','0','0','S11');
INSERT INTO `ref_prodfamille` VALUES (4,'Rédaction de doc','0','0','S12');
INSERT INTO `ref_prodfamille` VALUES (5,'Régie','0','0','S13');
INSERT INTO `ref_prodfamille` VALUES (6,'Audits','0','0','S14');
INSERT INTO `ref_prodfamille` VALUES (7,'Support','0','0','S15');
INSERT INTO `ref_prodfamille` VALUES (8,'Développements','0','0','S2');
INSERT INTO `ref_prodfamille` VALUES (9,'Développements PHP','0','0','S21');
INSERT INTO `ref_prodfamille` VALUES (10,'Développements GED','0','0','S22');
INSERT INTO `ref_prodfamille` VALUES (11,'Consulting','0','0','S23');
INSERT INTO `ref_prodfamille` VALUES (12,'Formations','0','1','S3');
INSERT INTO `ref_prodfamille` VALUES (13,'Formations Système','0','1','S31');
INSERT INTO `ref_prodfamille` VALUES (14,'Formations Développement','0','1','S32');
INSERT INTO `ref_prodfamille` VALUES (15,'Hébergement','0','0','S4');
INSERT INTO `ref_prodfamille` VALUES (16,'Hébergement web','0','0','S41');
INSERT INTO `ref_prodfamille` VALUES (17,'Hébergement Système','0','0','S42');
INSERT INTO `ref_prodfamille` VALUES (18,'Formations','0','0','F');
INSERT INTO `ref_prodfamille` VALUES (19,'Cours Système','0','0','F1');
INSERT INTO `ref_prodfamille` VALUES (20,'Clustering','0','0','F11');
INSERT INTO `ref_prodfamille` VALUES (21,'Cours développement','0','0','F2');
INSERT INTO `ref_prodfamille` VALUES (22,'Framework','0','0','F21');
INSERT INTO `ref_prodfamille` VALUES (23,'Revente','0','1','R');
INSERT INTO `ref_prodfamille` VALUES (24,'Redhat','0','1','R1');
INSERT INTO `ref_prodfamille` VALUES (25,'Souscriptions RHEL','0','1','R11');
INSERT INTO `ref_prodfamille` VALUES (26,'RHEL Académique','0','1','R12');
INSERT INTO `ref_prodfamille` VALUES (27,'RHN','0','1','R13');
INSERT INTO `ref_prodfamille` VALUES (28,'Formations','0','1','R14');
INSERT INTO `ref_prodfamille` VALUES (29,'Jboss','0','1','R15');
INSERT INTO `ref_prodfamille` VALUES (30,'Divers','0','1','R19');
INSERT INTO `ref_prodfamille` VALUES (31,'Zarafa','0','1','R2');
INSERT INTO `ref_prodfamille` VALUES (32,'Licences','0','1','R21');
INSERT INTO `ref_prodfamille` VALUES (33,'Formations','0','1','R22');
INSERT INTO `ref_prodfamille` VALUES (34,'Zuno','0','0','Z');
INSERT INTO `ref_prodfamille` VALUES (35,'Abonnements','0','0','Z1');
INSERT INTO `ref_prodfamille` VALUES (36,'Instance','0','0','Z11');
INSERT INTO `ref_prodfamille` VALUES (37,'Modules','0','0','Z12');
INSERT INTO `ref_prodfamille` VALUES (38,'Utilisateurs','0','0','Z13');
INSERT INTO `ref_prodfamille` VALUES (39,'Consommables','0','0','Z2');
INSERT INTO `ref_prodfamille` VALUES (40,'Expéditions','0','0','Z21');
INSERT INTO `ref_prodfamille` VALUES (41,'Services','0','0','Z3');
INSERT INTO `ref_prodfamille` VALUES (42,'Gestion de l\'instance','0','0','Z31');
INSERT INTO `ref_prodfamille` VALUES (43,'Demande ponctuelle','0','0','Z32');
INSERT INTO `ref_prodfamille` VALUES (44,'Exceptionnel','0','0','Z4');
INSERT INTO `ref_prodfamille` VALUES (45,'Opérations marketing','0','0','Z41');
INSERT INTO `ref_prodfamille` VALUES (46,'Autres ','0','0','X');
INSERT INTO `ref_prodfamille` VALUES (47,'Frais','0','0','X1');
INSERT INTO `ref_prodfamille` VALUES (48,'Frais divers','0','0','X11');
INSERT INTO `ref_prodfamille` VALUES (49,'Frais d\'expédition','0','0','X12');
INSERT INTO `ref_prodfamille` VALUES (50,'Marketing','0','0','X2');
INSERT INTO `ref_prodfamille` VALUES (51,'Leads','0','0','X21');
INSERT INTO `ref_prodfamille` VALUES (52,'xxxxxxxxx','1','1','R19');

--
-- Dumping data for table `ref_redhat_archi`
--

INSERT INTO `ref_redhat_archi` VALUES (1,'x86');
INSERT INTO `ref_redhat_archi` VALUES (2,'Itanium (IPF)');
INSERT INTO `ref_redhat_archi` VALUES (3,'AMD64');
INSERT INTO `ref_redhat_archi` VALUES (4,'iSeries/pSeries (PPC)');
INSERT INTO `ref_redhat_archi` VALUES (5,'zSeries/S390');
INSERT INTO `ref_redhat_archi` VALUES (6,'x86 et x86_64');
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

INSERT INTO `ref_statusaffaire` VALUES (1,'Ouverte',10,'d48319');
INSERT INTO `ref_statusaffaire` VALUES (2,'Attente CDC',15,'c46f00');
INSERT INTO `ref_statusaffaire` VALUES (3,'Rédaction de réponse',20,'59c800');
INSERT INTO `ref_statusaffaire` VALUES (4,'Réponse envoyée',25,'429300;font-weight:bold');
INSERT INTO `ref_statusaffaire` VALUES (5,'Accord de principe',45,'00b537');
INSERT INTO `ref_statusaffaire` VALUES (6,'Perdu',0,'cd4833;text-decoration:line-through');
INSERT INTO `ref_statusaffaire` VALUES (7,'BDC Client recu',50,'188da6;font-weight:bold');
INSERT INTO `ref_statusaffaire` VALUES (8,'BDC Fournisseur Crée',52,'2d9db5');
INSERT INTO `ref_statusaffaire` VALUES (9,'BDC Fournisseur envoyé',70,'38a7bf');
INSERT INTO `ref_statusaffaire` VALUES (10,'Commande Client traitée',75,'40b8d2');
INSERT INTO `ref_statusaffaire` VALUES (11,'Commande terminée',80,'3894bf');
INSERT INTO `ref_statusaffaire` VALUES (12,'Facture crée',80,'316fc4');
INSERT INTO `ref_statusaffaire` VALUES (13,'Facture validée',85,'3a76cf');
INSERT INTO `ref_statusaffaire` VALUES (14,'Facture éditée',90,'2363ba');
INSERT INTO `ref_statusaffaire` VALUES (15,'Facture envoyée',90,'185ebc;font-weight:bold');
INSERT INTO `ref_statusaffaire` VALUES (16,'Facture réglée',100,'1843bc;font-weight:bold');
INSERT INTO `ref_statusaffaire` VALUES (17,'Affaire supprimée',0,'bcbcbc;text-decoration:line-through');
INSERT INTO `ref_statusaffaire` VALUES (18,'Affaire archivée',0,'b0b0b0;font-style:italic');
INSERT INTO `ref_statusaffaire` VALUES (19,'Affaire désactivée',0,'9a9a9a;font-style:italic');
INSERT INTO `ref_statusaffaire` VALUES (20,'Affaire re-activée',0,'70c45d');

--
-- Dumping data for table `ref_statuscommande`
--

INSERT INTO `ref_statuscommande` VALUES (1,0,5,'BDCC enregistré','La commande client est enregistrée. En attente du bon de commande client.',5,'44a');
INSERT INTO `ref_statuscommande` VALUES (2,1,10,'BDCC reçu','Nous avons reçu le bon de commande client dument validé',5,'55b');
INSERT INTO `ref_statuscommande` VALUES (3,2,15,'BDCF Généré','Le bon de commande fournisseur est maintenant généré',5,'56b');
INSERT INTO `ref_statuscommande` VALUES (4,3,25,'BDCF Envoyé','Le bon de commande fournisseur est parti vers votre fournisseur',4,'67c;font-weight:bold');
INSERT INTO `ref_statuscommande` VALUES (5,4,50,'BDCF reçu par le fournisseur','Le fournisseur à bien reçu le bon de commande',2,'38d;font-weight:bold');
INSERT INTO `ref_statuscommande` VALUES (6,5,60,'BDCF en cours de traitement','Le fournisseur valide la vente et lance son traitement;font-weight:bold',3,'49e');
INSERT INTO `ref_statuscommande` VALUES (7,6,90,'Commande expédié','Commande expédié par ',5,'5af');
INSERT INTO `ref_statuscommande` VALUES (8,7,100,'Commande réceptionnée','Réception de la commande par le client ',5,'6bf');
INSERT INTO `ref_statuscommande` VALUES (9,8,100,'Commande terminée','Clôture de la commande',5,'2c1;font-weight:bold');
INSERT INTO `ref_statuscommande` VALUES (10,9,100,'Commande archivée','La commande est archivée',5,'b0b0b0;font-style:italic');

--
-- Dumping data for table `ref_statusdevis`
--

INSERT INTO `ref_statusdevis` VALUES (1,'Crée','44a','15');
INSERT INTO `ref_statusdevis` VALUES (2,'Supprimé','c55','0');
INSERT INTO `ref_statusdevis` VALUES (3,'Enregistré','56b','30');
INSERT INTO `ref_statusdevis` VALUES (4,'Envoyé','38d;font-weight:bold','50');
INSERT INTO `ref_statusdevis` VALUES (5,'Perdu','d34;text-decoration:line-through','100');
INSERT INTO `ref_statusdevis` VALUES (6,'Validé','2c1;font-weight:bold','100');
INSERT INTO `ref_statusdevis` VALUES (7,'Archivé','b0b0b0;font-style:italic','0');

--
-- Dumping data for table `ref_statusfacture`
--

INSERT INTO `ref_statusfacture` VALUES (1,10,'Facture créée','La facture est enregistrée dans nos bases','6879de','0');
INSERT INTO `ref_statusfacture` VALUES (2,25,'Facture validée','Facture controlée et validée','5062d2','0');
INSERT INTO `ref_statusfacture` VALUES (3,45,'Facture enregistrée','La facture est éditée et enregistrée dans gnose','384bc3','0');
INSERT INTO `ref_statusfacture` VALUES (4,50,'Facture Envoyée','La facture est envoyée','529631','1');
INSERT INTO `ref_statusfacture` VALUES (5,75,'Facture En attente de règlement','La facture est en attente de règlement','4d8b31;font-weight:bold','1');
INSERT INTO `ref_statusfacture` VALUES (6,100,'Facture Cloturée','Le règlement vient d\'etre enregistré dans nos bases','93b087','1');
INSERT INTO `ref_statusfacture` VALUES (7,100,'Facture archivée','La facture est maintenant archivée','b0b0b0;font-style:italic','1');

--
-- Dumping data for table `ref_statusfacturefournisseur`
--

INSERT INTO `ref_statusfacturefournisseur` VALUES (1,15,'Enregistrée','La facture fournisseur a bien été reçue et a été enregistrée',NULL,'0');
INSERT INTO `ref_statusfacturefournisseur` VALUES (2,50,'Enregistrée en comptabilité','La facture est enregistrée par le service de comptabilitée',NULL,'1');
INSERT INTO `ref_statusfacturefournisseur` VALUES (3,75,'A payer','La facture doit être payée.',NULL,'1');
INSERT INTO `ref_statusfacturefournisseur` VALUES (4,100,'Payée','La facture a été payée',NULL,'1');
INSERT INTO `ref_statusfacturefournisseur` VALUES (5,0,'Archivée','La facture fournisseur est archivée',NULL,'1');

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
INSERT INTO `ref_typepayline` VALUES (15,'Mise à jour Wallet');
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
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-07-20 18:48:49
