 
--
-- Début du patch.sql généré par CompareSQL V1
-- Tue Jun 01 23:40:16 +0200 2010
--
 
--
-- Gestion des différences de structures de la BDD et des tables
--
 
 
--
-- Ajout des données supplémentaires
--
 
DELETE FROM `ref_activite` WHERE `id_act` = '55' ;
INSERT INTO `ref_activite` VALUES (55,'Hôtels et restaurants');
DELETE FROM `ref_page` WHERE `id_pg` = 'actualite' ;
INSERT INTO `ref_page` VALUES ('actualite','nm',NULL,'2010-05-31 13:46:34','nm','2009-06-24 08:59:10','nm','actualite',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'actualite.php','normal',NULL,1,'1','0',NULL,NULL,183844,'0000-00-00 00:00:00',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'AffaireFiche' ;
INSERT INTO `ref_page` VALUES ('AffaireFiche','cl',',0,1,2,,','2010-05-31 10:35:00','cl',NULL,'cl','Fiche affaire',NULL,NULL,'affaire.png','affaire.png','Fiche affaire',NULL,NULL,'Fiche affaire',NULL,NULL,'Affaire.php','draco','ListeAffaire',1,'0','0',NULL,'title',1721,'2006-03-13 21:03:41',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'CreateC' ;
INSERT INTO `ref_page` VALUES ('CreateC','cl',',0,1,2,,','2010-05-30 20:32:34','cl',NULL,'cl','Ajouter',NULL,NULL,'recherche.prospec.png','recherche.prospec.png','Recherche des contacts',NULL,NULL,'Recherche des contacts',NULL,NULL,'fiche.php','prospec',NULL,3,'1','0',NULL,'titleContent',3199,'0000-00-00 00:00:00',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'CreateP' ;
INSERT INTO `ref_page` VALUES ('CreateP','nm',NULL,'2010-05-31 10:36:38','nm','2010-05-30 10:03:33','nm','Ajouter',NULL,NULL,'produit.png','produit.png','Ajouter un produit',NULL,NULL,'Ajouter un produit',NULL,NULL,'Produit.php','produit',NULL,1,'1','0',NULL,NULL,98,'0000-00-00 00:00:00',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'Devis' ;
INSERT INTO `ref_page` VALUES ('Devis','cl',NULL,'2010-06-01 08:35:17','cl',NULL,'cl','Devis',NULL,NULL,'devis.png','devis.png',' devis',NULL,NULL,'Devis',NULL,NULL,'Devis.php','draco','DevisListe',1,'0','0',NULL,'titleContent',4056,'0000-00-00 00:00:00',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'DevisListe1' ;
INSERT INTO `ref_page` VALUES ('DevisListe1','cl',NULL,'2010-05-31 10:35:46','cl','2010-05-30 10:24:35','cl','Devis crée',NULL,NULL,'devis.liste.png','devis.perdu.png','Liste devis crées',NULL,NULL,'Liste devis crées',NULL,NULL,'DevisListe.php?status_dev=1','draco','DevisListe',1,'1','0',NULL,'titleContent',15,'2006-04-19 09:07:42',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'index' ;
INSERT INTO `ref_page` VALUES ('index','cl',NULL,'2010-05-31 10:32:48','cl','2009-09-24 00:20:00','cl','Accueil','Home Page','Empfangg',NULL,NULL,'Accueil','Homepage','Empfang','Accueil','Homepage','Empfang','index.php','normal',NULL,1,'0','0',NULL,'Accueil',2531,'2005-10-28 13:14:50','<h1>Bienvenue dans votre espace de travail ZUNO</h1><br/><br/><br/>\r\n<p>Cet extranet vous est d&eacute;di&eacute;. Il vous permet d\'acc&eacute;der &agrave; l\'espace de travail de votre entreprise. Vous pouvez vous connecter en cliquant sur le bouton connexion (en haut &agrave; droite) et en saisisant vos informations de connexion dans la boite de dialogue ad hoc.</p>\r\n<p>L\'acc&egrave;s &agrave; ce site internet n\'est accessible qu\'aux membres autoris&eacute;s par le titulaire du compte de ce service. La connexions &eacute;tant personnelle, vous devez avoir re&ccedil;u vos informations de connexion avant de pouvoir ac&eacute;der &agrave; ce service. Si ne disposez pas de ces &eacute;l&eacute;ments, merci de quitter cet espace de travail. <br />\r\nSi vous avez perdu vos informations de connexion, merci de nous adresser votre demande en vous <a title=\"Formulaire de demande d\'information de connexion\" href=\"zuno.fr/loginPerdu.php\">rendant ici</a>.</p>\r\n<p>Il est rappel&eacute; que la consultation de cet outil est soumise &agrave; la             r&eacute;glementation fran&ccedil;aise en vigueur et que toute information quelle qu\'elle             soit et quel qu\'en soit le support n\'emporte aucune exhaustivit&eacute;.</p>\r\n<p>&nbsp;</p>\r\n<h1 style=\"margin-top: 0pt;\">Un mode de travail intelligent...</h1><br/><br/><br/>\r\n<p>Zuno est d&eacute;di&eacute;e &agrave; la gestion commerciale des PME/TPE. Articul&eacute; autour de plusieurs modules inter-d&eacute;pendant, elle vous permet de g&eacute;rer de bout en bout votre processus commercial. En partant de vos prospec ou client, vous pouvez g&eacute;rer l\'ensemble de votre m&eacute;tier par le biais d\'interface simple et rapide. Vous trouverez ainsi les modules:</p>\r\n<p>&nbsp;<img width=\"200\" height=\"38\" alt=\"logo-modules-client.png\" src=\"./img/imgbank/zuno/logo-modules-client.png\" /></p>\r\n<p><img width=\"200\" height=\"38\" alt=\"logo-modules-client.png\" src=\"./img/imgbank/zuno/logo-modules-manager.png\" /></p>\r\n<p><img width=\"199\" height=\"38\" alt=\"logo-modules-client.png\" src=\"./img/imgbank/zuno/logo-modules-commercial.png\" /> </p>\r\n<p>&nbsp;<img width=\"200\" height=\"38\" alt=\"logo-modules-client.png\" src=\"./img/imgbank/zuno/logo-modules-facture.png\" /></p>\r\n<p><img width=\"200\" height=\"38\" alt=\"logo-modules-client.png\" src=\"./img/imgbank/zuno/logo-modules-ventes.png\" /><br />\r\n<br />',NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'ListeAffaire' ;
INSERT INTO `ref_page` VALUES ('ListeAffaire','cl',',0,1,2,,','2010-05-31 10:33:46','cl',NULL,'cl','Recherche d\'affaire',NULL,NULL,'affaire.png','affaire.png','Recherche d\'affaire',NULL,NULL,'Recherche d\'affaire',NULL,NULL,'ListeAffaires.php','draco',NULL,2,'1','0',NULL,'titleContent',1640,'2006-03-13 21:03:41',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'ListeCommande' ;
INSERT INTO `ref_page` VALUES ('ListeCommande','cl',',0,1,2,,','2010-05-31 10:33:24','cl',NULL,'cl','Rechercher',NULL,NULL,'commande.png','commande.png',' Recherche des commandes',NULL,NULL,'Recherche des commandes',NULL,NULL,'CommandeListe.php','pegase',NULL,4,'1','0',NULL,'titleContent',827,'2006-04-03 11:50:32',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'ListeCommande4' ;
INSERT INTO `ref_page` VALUES ('ListeCommande4','cl',',0,1,2,,','2010-05-31 10:33:06','cl','2010-05-30 09:03:24','cl','Commandes envoyées',NULL,NULL,'commande.png','commande.png',' Liste des commandes envoyées',NULL,NULL,'Liste des commandes envoyées',NULL,NULL,'CommandeListe.php?status_cmd=4','pegase','ListeCommande',2,'1','0',NULL,'titleContent',818,'2006-04-03 11:50:32',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'ListeFacture' ;
INSERT INTO `ref_page` VALUES ('ListeFacture','cl',',0,1,2,,','2010-05-30 20:23:35','cl',NULL,'cl','Recherche de factures',NULL,NULL,'facture.png','facture.png','Recherche de factures',NULL,NULL,'Recherche de factures',NULL,NULL,'FactureListe.php','facturier',NULL,1,'1','0',NULL,'titleContent',1817,'2006-04-03 11:50:32',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'ListeLeads' ;
INSERT INTO `ref_page` VALUES ('ListeLeads','cl',',0,1,2,,','2010-05-30 17:19:15','cl',NULL,'cl','Projets',NULL,NULL,'projet.liste.png','projet.liste.png',' Liste des projets',NULL,NULL,'Liste des projets',NULL,NULL,'ListeLeads.php','prospec','RechercheContact',3,'1','0',NULL,'titleContent',677,'0000-00-00 00:00:00',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'Login' ;
INSERT INTO `ref_page` VALUES ('Login','cl',NULL,'2010-06-01 08:35:18','cl','2010-02-19 00:36:29','cl','Authentification','Authentication',NULL,NULL,NULL,'Authentification des utilisateurs','Connect to authorized area',NULL,'Authentification des utilisateurs','Connect to authorized area',NULL,'Login.php','normal',NULL,1,'0','0','','Accueil',2739,'2005-10-28 11:14:50',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'monBureau' ;
INSERT INTO `ref_page` VALUES ('monBureau','cl',NULL,'2010-05-31 10:32:49','cl','2009-09-28 11:24:45','cl','Mon bureau',NULL,NULL,NULL,NULL,'Mon bureau',NULL,NULL,'Mon bureau',NULL,NULL,'Bureau.php','normal',NULL,1,'0','0',NULL,NULL,1022,'0000-00-00 00:00:00',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'PListe' ;
INSERT INTO `ref_page` VALUES ('PListe','nm',NULL,'2010-05-31 10:37:02','nm','2010-05-30 10:06:30','nm','Recherche',NULL,NULL,'produit.png','produit.png','Liste des produits',NULL,NULL,'Liste des produits',NULL,NULL,'ProduitListe.php','produit',NULL,2,'1','0',NULL,'titleContent',309,'0000-00-00 00:00:00',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'PontComptableCreate' ;
INSERT INTO `ref_page` VALUES ('PontComptableCreate','cl',',0,1,2,,','2010-05-30 20:32:22','cl','2010-05-22 07:14:53','cl','Nouvel export',NULL,NULL,'PontComptableCreate.png','PontComptableCreate.png','Nouveau fichier d\'export',NULL,NULL,'Nouveau fichier d\'export',NULL,NULL,'PontComptable.php?action=new','facturier','PontComptableHisto',1,'1','0',NULL,'title',2594,'2006-04-12 10:50:45',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'RechercheCEnt' ;
INSERT INTO `ref_page` VALUES ('RechercheCEnt','cl',',0,1,2,,','2010-05-30 20:32:30','cl','2010-05-30 10:17:14','cl','Entreprise / Contact',NULL,NULL,'recherche.prospec.png','recherche.prospec.png','Recherche des contacts',NULL,NULL,'Recherche des contacts',NULL,NULL,'Recherche.php','prospec','RechercheContact',1,'1','0',NULL,'titleContent',3203,'0000-00-00 00:00:00',NULL,NULL,NULL,'1');
DELETE FROM `module`;
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
INSERT INTO `module` VALUES ('mobile','oui');
INSERT INTO `module` VALUES ('pontComptable','oui');
 
--
-- Suppression des données qui n'existent plus
--
 
