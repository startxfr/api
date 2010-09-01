 
--
-- Début du patch.sql généré par CompareSQL V1
-- Wed Sep 01 15:34:36 +0200 2010
--
 
--
-- Gestion des différences de structures de la BDD et des tables
--
 
 
--
-- Ajout des données supplémentaires
--
 
DELETE FROM `ref_page` WHERE `id_pg` = 'actualite' ;
INSERT INTO `ref_page` VALUES ('actualite','nm','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:21','nm','2009-06-24 08:59:10','nm','actualite',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'actualite.php','normal',NULL,1,'1','0',NULL,NULL,NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'AdminManual' ;
INSERT INTO `ref_page` VALUES ('AdminManual','cl',',0,1,,','2010-09-01 10:53:47','cl','2010-09-01 10:53:47','cl','Manuel de l\'admin','Admin manual','Handbuch des Verwalters','ManuelAdmin.png','ManuelAdmin.png','C\'est le manuel de l\'administrateur','sumary for english person','Handbuch des Verwalters','Manuel de l\'administrateur','Manual for technical staff','Handbuch des Verwalters',NULL,'admin','Help',1,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'AffaireCreate' ;
INSERT INTO `ref_page` VALUES ('AffaireCreate','cl','0,1,2,3,4,10,15,,','2010-09-01 10:50:19','cl',NULL,'cl','Affaire',NULL,NULL,'affaire.png','affaire.create.png','Création d\'un affaire',NULL,NULL,'Création d\'un affaire',NULL,NULL,'AffaireCreate.php','draco','Create',2,'1','0',NULL,'title',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'AffaireFiche' ;
INSERT INTO `ref_page` VALUES ('AffaireFiche','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:19','cl',NULL,'cl','Fiche affaire',NULL,NULL,'affaire.png','affaire.png','Fiche affaire',NULL,NULL,'Fiche affaire',NULL,NULL,'Affaire.php','draco','ListeAffaire',1,'0','0',NULL,'title',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'Application' ;
INSERT INTO `ref_page` VALUES ('Application','cl',',0,1,,','2010-09-01 10:53:47','cl','2010-09-01 10:53:47','cl','Application',NULL,'Anwendung','appli.png','Application.png','Informations sur l\'application',NULL,'Anwendung','Information sur l\'application',NULL,'Anwendung','Application.php','admin',NULL,5,'1','0',NULL,'fullContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'ApplicationConfig' ;
INSERT INTO `ref_page` VALUES ('ApplicationConfig','cl',',0,1,,','2010-09-01 10:53:47','cl','2010-09-01 10:53:47','cl','Configuration',NULL,NULL,'AppliConfig.png','ApplicationConfig.png','Configuration de l\'application',NULL,NULL,'Configuration',NULL,NULL,'ApplicationConfig.php','admin','Application',4,'1','0',NULL,'titleContent','Cette rubrique vous permet de configurer les différents paramètres de votre application. Le bouton de netyage du cache vous permet de réactualiser les informations de votre site. Cette opération est automatiquement réalisée par le système. <br />Si vous constatez que certaines informations ne sont pas à jour, n\\\'hésitez pas à lancer une opération de nettoyage du cache.<br /><br /><br />',NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'Archive' ;
INSERT INTO `ref_page` VALUES ('Archive','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:21','cl','2004-12-31 21:00:00','cl','Archives',NULL,NULL,'archive.png','archives.png','Répertoires d\'archives',NULL,NULL,'Répertoires d\'archives',NULL,NULL,'BrowseArchive.php','gnose',NULL,10,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'AvoirCreate' ;
INSERT INTO `ref_page` VALUES ('AvoirCreate','nm','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:19','nm',NULL,'nm','Avoir',NULL,NULL,'facture.png','facture.png','Créer un avoir',NULL,NULL,'Créer un avoir',NULL,NULL,'FactureCreate.php?type=avoir','facturier','CreateF',2,'1','0',NULL,NULL,NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'CommandeFiche' ;
INSERT INTO `ref_page` VALUES ('CommandeFiche','cl','0,1,2,3,4,5,10,15,20,,','2010-09-01 10:50:21','cl',NULL,'cl','Fiche commande',NULL,NULL,'commande.png','affaire.png','Fiche commande',NULL,NULL,'Fiche commande',NULL,NULL,'Commande.php','pegase','ListeCommande',1,'0','0',NULL,'title',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'Contact' ;
INSERT INTO `ref_page` VALUES ('Contact','nm','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:21','nm',NULL,'nm','Contact',NULL,NULL,'contact.png','contact.png',NULL,NULL,NULL,'Nouveau Contact',NULL,NULL,'Contact.php','prospec','CreateC',2,'1','0',NULL,NULL,NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'Create' ;
INSERT INTO `ref_page` VALUES ('Create','cl','0,1,2,3,4,10,15,,','2010-09-01 10:50:19','cl','2010-05-30 10:01:40','cl','Ajouter',NULL,NULL,'affaire.png','affaire.create.png','Création d\'un affaire',NULL,NULL,'Création d\'un affaire',NULL,NULL,'AffaireCreate.php','draco',NULL,1,'1','0',NULL,'title',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'CreateC' ;
INSERT INTO `ref_page` VALUES ('CreateC','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:21','cl',NULL,'cl','Ajouter',NULL,NULL,'recherche.prospec.png','recherche.prospec.png','Recherche des contacts',NULL,NULL,'Recherche des contacts',NULL,NULL,'fiche.php','prospec',NULL,3,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'CreateF' ;
INSERT INTO `ref_page` VALUES ('CreateF','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:19','cl','2010-05-30 09:40:46','cl','Ajouter',NULL,NULL,'facture.png','facture.png','Créer une facture',NULL,NULL,'Créer une facture',NULL,NULL,'FactureCreate.php','facturier',NULL,1,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'CreateP' ;
INSERT INTO `ref_page` VALUES ('CreateP','nm','0,1,2,3,4,5,10,15,20,,','2010-09-01 10:50:21','nm','2010-05-30 10:03:33','nm','Ajouter',NULL,NULL,'produit.png','produit.png','Ajouter un produit',NULL,NULL,'Ajouter un produit',NULL,NULL,'Produit.php','produit',NULL,1,'1','0',NULL,NULL,NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'csv2db' ;
INSERT INTO `ref_page` VALUES ('csv2db','cl',',0,1,,','2010-09-01 10:50:18','cl',NULL,'cl','Import de données',NULL,NULL,'csvExport.png','csvExport.png',NULL,NULL,NULL,'Import CSV',NULL,NULL,'csv2db.php','admin','Application',1,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'Devis' ;
INSERT INTO `ref_page` VALUES ('Devis','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:19','cl',NULL,'cl','Devis',NULL,NULL,'devis.png','devis.png',' devis',NULL,NULL,'Devis',NULL,NULL,'Devis.php','draco','DevisListe',1,'0','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'DevisCreate' ;
INSERT INTO `ref_page` VALUES ('DevisCreate','cl','0,1,2,3,4,10,15,,','2010-09-01 10:50:19','cl',NULL,'cl','Devis',NULL,NULL,'devis.png','devis.create.png','Créer un devis',NULL,NULL,'Créer un devis',NULL,NULL,'DevisCreate.php','draco','Create',4,'1','0',NULL,'title',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'DevisListe' ;
INSERT INTO `ref_page` VALUES ('DevisListe','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:19','cl',NULL,'cl','Recherche de devis',NULL,NULL,'devis.liste.png','devis.png','Recherche de devis',NULL,NULL,'Recherche de devis',NULL,NULL,'DevisListe.php','draco',NULL,3,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'DevisListe1' ;
INSERT INTO `ref_page` VALUES ('DevisListe1','cl','0,1,2,3,4,10,15,,','2010-09-01 10:50:19','cl','2010-05-30 10:24:35','cl','Devis crée',NULL,NULL,'devis.liste.png','devis.perdu.png','Liste devis crées',NULL,NULL,'Liste devis crées',NULL,NULL,'DevisListe.php?status_dev=1','draco','DevisListe',1,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'DevisListe4' ;
INSERT INTO `ref_page` VALUES ('DevisListe4','cl','0,1,2,3,4,10,15,,','2010-09-01 10:50:19','cl','2010-05-30 10:24:35','cl','Devis envoyés',NULL,NULL,'devis.liste.png','devis.perdu.png','Liste devis envoyés',NULL,NULL,'Liste devis envoyés',NULL,NULL,'DevisListe.php?status_dev=4','draco','DevisListe',2,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'DevisListe5' ;
INSERT INTO `ref_page` VALUES ('DevisListe5','cl','0,1,2,3,4,10,15,,','2010-09-01 10:50:19','cl',NULL,'cl','Devis perdus',NULL,NULL,'devis.liste.png','devis.perdu.png','Liste devis perdus ou annulés',NULL,NULL,'Liste devis perdus ou annulés',NULL,NULL,'DevisListeAnnule.php','draco','DevisListe',3,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'DevisListe6' ;
INSERT INTO `ref_page` VALUES ('DevisListe6','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:19','cl',NULL,'cl','Devis gagnés',NULL,NULL,'devis.liste.png','devis.gagne.png',' Liste des devis gagnés',NULL,NULL,'Liste des devis gagnés',NULL,NULL,'DevisListeGagne.php','draco','DevisListe',4,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'DevisListe7' ;
INSERT INTO `ref_page` VALUES ('DevisListe7','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:19','cl','2010-05-30 10:24:35','cl','Devis archivés',NULL,NULL,'devis.liste.png','devis.gagne.png',' Liste des devis archivés',NULL,NULL,'Liste des devis archivés',NULL,NULL,'DevisListe.php?status_dev=7','draco','DevisListe',5,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'DevisListeRenew' ;
INSERT INTO `ref_page` VALUES ('DevisListeRenew','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:19','cl',NULL,'cl','Devis à renouveler',NULL,NULL,'devis.listerenew.png','devis.renew.png',' Liste des devis à renouveler',NULL,NULL,'Liste des devis a renouveler',NULL,NULL,'DevisListeRenew.php','draco','DevisListe',3,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'FactureCreate' ;
INSERT INTO `ref_page` VALUES ('FactureCreate','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:19','cl',NULL,'cl','Facture',NULL,NULL,'facture.png','facture.png','Créer une facture',NULL,NULL,'Créer une facture',NULL,NULL,'FactureCreate.php','facturier','CreateF',1,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'FactureFiche' ;
INSERT INTO `ref_page` VALUES ('FactureFiche','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:20','cl',NULL,'cl','Facture',NULL,NULL,'facture.png','facture.png','Facture',NULL,NULL,'Facture',NULL,NULL,'Facture.php','facturier','ListeFacture',1,'0','0',NULL,'title',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'FactureFournisseurCreate' ;
INSERT INTO `ref_page` VALUES ('FactureFournisseurCreate','nm','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:20','nm',NULL,'nm','Facture fournisseur',NULL,NULL,'factureFourn.png','factureFourn.png','Créer une facture fournisseur',NULL,NULL,'Ajouter une facture fournisseur',NULL,NULL,'FactureFournisseurCreate.php','facturier','CreateF',3,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'FactureFournisseurFiche' ;
INSERT INTO `ref_page` VALUES ('FactureFournisseurFiche','nm','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:20','nm',NULL,'nm','Facture fournisseur',NULL,NULL,'factureFourn.png','factureFourn.png','Fiche facture fournisseur',NULL,NULL,'Fiche Facture fournisseur',NULL,NULL,'FactureFournisseur.php','facturier','ListeFacture',1,'0','0',NULL,'title',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'FacturePopup' ;
INSERT INTO `ref_page` VALUES ('FacturePopup','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:20','cl',NULL,'cl','Facture',NULL,NULL,'facture.png','facture.png','Facture',NULL,NULL,'Facture',NULL,NULL,'PopupFacture.php','facturier','ListeFacture',1,'0','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'FooterAdmin' ;
INSERT INTO `ref_page` VALUES ('FooterAdmin','cl',',0,1,,','2010-09-01 10:50:18','cl','2004-12-31 21:00:00','cl','Outils','Toolbox',NULL,NULL,NULL,'Lies outils du site','Website toolbox',NULL,'les outils','Toolbox',NULL,NULL,'admin',NULL,10,'0','1',NULL,NULL,NULL,'<br />\r\n',NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'Fournisseur' ;
INSERT INTO `ref_page` VALUES ('Fournisseur','nm','0,1,2,3,4,5,10,15,20,,','2010-09-01 10:50:21','nm',NULL,'nm','Fournisseur',NULL,NULL,'fournisseur.png','fournisseur.png','Gestion des fournisseurs',NULL,NULL,'Gestion des fournisseurs',NULL,NULL,'Fournisseur.php','produit','CreateP',3,'1','0',NULL,NULL,NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'FournisseurListe' ;
INSERT INTO `ref_page` VALUES ('FournisseurListe','nm','0,1,2,3,4,5,10,15,20,,','2010-09-01 10:50:21','nm',NULL,'nm','Fournisseurs',NULL,NULL,'fournisseur.png','fournisseur.png','Liste des fournisseurs',NULL,NULL,'Liste des fournisseurs',NULL,NULL,'FournisseurListe.php','produit','PListe',2,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'History' ;
INSERT INTO `ref_page` VALUES ('History','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:21','cl','2004-12-31 21:00:00','cl','Historique',NULL,NULL,'History.png','History.png','Historique d\'un enregistrement',NULL,NULL,'Historique d\'un enregistrement',NULL,NULL,'History.php','gnose',NULL,4,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'ImageManage' ;
INSERT INTO `ref_page` VALUES ('ImageManage','cl',',0,1,,','2010-09-01 10:50:18','cl','2004-12-31 21:00:00','cl','Bibliothèque d\'image','Picture gallery','Bildbibliothek','image.manage.png','ImageManage.png','Permet de gérer les images','Allow you to manage images','Bildbibliothek','Gestion des images du site','Website image management','Bildbibliothek','ImageManage.php','admin','PageManage',3,'1','0',NULL,'title',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'index' ;
INSERT INTO `ref_page` VALUES ('index','cl',NULL,'2010-09-01 10:53:47','cl','2010-09-01 10:53:47','cl','Accueil','Home Page','Empfangg',NULL,NULL,'Accueil','Homepage','Empfang','Accueil','Homepage','Empfang','index.php','normal',NULL,1,'0','0',NULL,'Accueil','<h1>Bienvenue dans votre espace de travail ZUNO</h1><br/><br/><br/>\r\n<p>Cet extranet vous est d&eacute;di&eacute;. Il vous permet d\'acc&eacute;der &agrave; l\'espace de travail de votre entreprise. Vous pouvez vous connecter en cliquant sur le bouton connexion (en haut &agrave; droite) et en saisisant vos informations de connexion dans la boite de dialogue ad hoc.</p>\r\n<p>L\'acc&egrave;s &agrave; ce site internet n\'est accessible qu\'aux membres autoris&eacute;s par le titulaire du compte de ce service. La connexions &eacute;tant personnelle, vous devez avoir re&ccedil;u vos informations de connexion avant de pouvoir ac&eacute;der &agrave; ce service. Si ne disposez pas de ces &eacute;l&eacute;ments, merci de quitter cet espace de travail. <br />\r\nSi vous avez perdu vos informations de connexion, merci de nous adresser votre demande en vous <a title=\"Formulaire de demande d\'information de connexion\" href=\"zuno.fr/loginPerdu.php\">rendant ici</a>.</p>\r\n<p>Il est rappel&eacute; que la consultation de cet outil est soumise &agrave; la             r&eacute;glementation fran&ccedil;aise en vigueur et que toute information quelle qu\'elle             soit et quel qu\'en soit le support n\'emporte aucune exhaustivit&eacute;.</p>\r\n<p>&nbsp;</p>\r\n<h1 style=\"margin-top: 0pt;\">Un mode de travail intelligent...</h1><br/><br/><br/>\r\n<p>Zuno est d&eacute;di&eacute;e &agrave; la gestion commerciale des PME/TPE. Articul&eacute; autour de plusieurs modules inter-d&eacute;pendant, elle vous permet de g&eacute;rer de bout en bout votre processus commercial. En partant de vos prospec ou client, vous pouvez g&eacute;rer l\'ensemble de votre m&eacute;tier par le biais d\'interface simple et rapide. Vous trouverez ainsi les modules:</p>\r\n<p>&nbsp;<img width=\"200\" height=\"38\" alt=\"logo-modules-client.png\" src=\"./img/imgbank/zuno/logo-modules-client.png\" /></p>\r\n<p><img width=\"200\" height=\"38\" alt=\"logo-modules-client.png\" src=\"./img/imgbank/zuno/logo-modules-manager.png\" /></p>\r\n<p><img width=\"199\" height=\"38\" alt=\"logo-modules-client.png\" src=\"./img/imgbank/zuno/logo-modules-commercial.png\" /> </p>\r\n<p>&nbsp;<img width=\"200\" height=\"38\" alt=\"logo-modules-client.png\" src=\"./img/imgbank/zuno/logo-modules-facture.png\" /></p>\r\n<p><img width=\"200\" height=\"38\" alt=\"logo-modules-client.png\" src=\"./img/imgbank/zuno/logo-modules-ventes.png\" /><br />\r\n<br />',NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'JournalBanque' ;
INSERT INTO `ref_page` VALUES ('JournalBanque','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:20','cl','2010-06-07 20:47:53','cl','Journal de banque',NULL,NULL,'JournalBanque.png','JournalBanque.png','Journal de banque',NULL,NULL,'Journal de banque',NULL,NULL,'JournalBanque.php','facturier',NULL,8,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'Journaux' ;
INSERT INTO `ref_page` VALUES ('Journaux','cl',',0,1,,','2010-09-01 10:50:18','cl',NULL,'cl','Journaux',NULL,NULL,'journaux.png','journaux.png',' Journaux',NULL,NULL,'Journaux',NULL,NULL,NULL,'admin','Application',1,'1','0',NULL,'fullContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'ListeAffaire' ;
INSERT INTO `ref_page` VALUES ('ListeAffaire','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:19','cl',NULL,'cl','Recherche d\'affaire',NULL,NULL,'affaire.png','affaire.png','Recherche d\'affaire',NULL,NULL,'Recherche d\'affaire',NULL,NULL,'ListeAffaires.php','draco',NULL,2,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'ListeCommande' ;
INSERT INTO `ref_page` VALUES ('ListeCommande','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:21','cl',NULL,'cl','Rechercher',NULL,NULL,'commande.png','commande.png',' Recherche des commandes',NULL,NULL,'Recherche des commandes',NULL,NULL,'CommandeListe.php','pegase',NULL,4,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'ListeCommande1' ;
INSERT INTO `ref_page` VALUES ('ListeCommande1','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:21','cl','2010-05-30 09:03:24','cl','Commandes enregistrées',NULL,NULL,'commande.png','commande.png',' Liste des commandes enregistrées',NULL,NULL,'Liste des commandes enregistrées',NULL,NULL,'CommandeListe.php?status_cmd=1','pegase','ListeCommande',1,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'ListeCommande10' ;
INSERT INTO `ref_page` VALUES ('ListeCommande10','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:21','cl','2010-05-30 09:03:24','cl','Commandes archivées',NULL,NULL,'commande.png','commande.png',' Liste des commandes archivées',NULL,NULL,'Liste des commandes archivées',NULL,NULL,'CommandeListe.php?status_cmd=10','pegase','ListeCommande',5,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'ListeCommande4' ;
INSERT INTO `ref_page` VALUES ('ListeCommande4','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:21','cl','2010-05-30 09:03:24','cl','Commandes envoyées',NULL,NULL,'commande.png','commande.png',' Liste des commandes envoyées',NULL,NULL,'Liste des commandes envoyées',NULL,NULL,'CommandeListe.php?status_cmd=4','pegase','ListeCommande',2,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'ListeCommande7' ;
INSERT INTO `ref_page` VALUES ('ListeCommande7','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:21','cl','2010-05-30 09:03:24','cl','Commandes expédiées',NULL,NULL,'commande.png','commande.png',' Liste des commandes expédiées',NULL,NULL,'Liste des commandes expédiées',NULL,NULL,'CommandeListe.php?status_cmd=7','pegase','ListeCommande',3,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'ListeCommande9' ;
INSERT INTO `ref_page` VALUES ('ListeCommande9','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:21','cl','2010-05-30 09:03:24','cl','Commandes terminées',NULL,NULL,'commande.png','commande.png',' Liste des commandes terminées',NULL,NULL,'Liste des commandes terminées',NULL,NULL,'CommandeListe.php?status_cmd=9','pegase','ListeCommande',4,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'ListeFacture' ;
INSERT INTO `ref_page` VALUES ('ListeFacture','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:20','cl',NULL,'cl','Recherche de factures',NULL,NULL,'facture.png','facture.png','Recherche de factures',NULL,NULL,'Recherche de factures',NULL,NULL,'FactureListe.php','facturier',NULL,1,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'ListeFacture1' ;
INSERT INTO `ref_page` VALUES ('ListeFacture1','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:20','cl',NULL,'cl','Crées',NULL,NULL,'facture.cloture.png','facture.cloture.png',' Liste des factures crées',NULL,NULL,'Liste des factures crées',NULL,NULL,'FactureListe.php?status_fact=1','facturier','ListeFacture',1,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'ListeFacture4' ;
INSERT INTO `ref_page` VALUES ('ListeFacture4','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:20','cl',NULL,'cl','Envoyées',NULL,NULL,'facture.cloture.png','facture.cloture.png',' Liste des factures envoyées',NULL,NULL,'Liste des factures envoyées',NULL,NULL,'FactureListe.php?status_fact=4','facturier','ListeFacture',2,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'ListeFacture5' ;
INSERT INTO `ref_page` VALUES ('ListeFacture5','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:20','cl',NULL,'cl','En attente',NULL,NULL,'facture.cloture.png','facture.cloture.png',' Liste des factures en attente',NULL,NULL,'Liste des factures en attente',NULL,NULL,'FactureListe.php?status_fact=5','facturier','ListeFacture',3,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'ListeFacture7' ;
INSERT INTO `ref_page` VALUES ('ListeFacture7','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:20','cl',NULL,'cl','Archivées',NULL,NULL,'facture.cloture.png','facture.cloture.png',' Liste des factures archivées',NULL,NULL,'Liste des factures archivées',NULL,NULL,'FactureListe.php?status_fact=7','facturier','ListeFacture',4,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'ListeFactureCloture' ;
INSERT INTO `ref_page` VALUES ('ListeFactureCloture','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:20','cl',NULL,'cl','Cloturées',NULL,NULL,'facture.cloture.png','facture.cloture.png',' Liste des factures terminées',NULL,NULL,'Liste des factures cloturées',NULL,NULL,'FactureListeCloture.php','facturier','ListeFacture',2,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'ListeFactureF1' ;
INSERT INTO `ref_page` VALUES ('ListeFactureF1','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:20','cl','2010-05-30 09:59:39','cl','Crées',NULL,NULL,'factureFourn.png','factureFourn.png',' Liste des factures fournisseurs créées',NULL,NULL,'Liste des factures fournisseurs créées',NULL,NULL,'FactureFournisseurListe.php?status_factfourn=1','facturier','ListeFactureFournisseur',1,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'ListeFactureF3' ;
INSERT INTO `ref_page` VALUES ('ListeFactureF3','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:20','cl','2010-05-30 09:59:39','cl','A payer',NULL,NULL,'factureFourn.png','factureFourn.png',' Liste des factures fournisseurs à payer',NULL,NULL,'Liste des factures fournisseurs à payer',NULL,NULL,'FactureFournisseurListe.php?status_factfourn=3','facturier','ListeFactureFournisseur',2,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'ListeFactureF4' ;
INSERT INTO `ref_page` VALUES ('ListeFactureF4','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:20','cl','2010-05-30 10:32:37','cl','Payées',NULL,NULL,'factureFourn.png','factureFourn.png',' Liste des factures fournisseurs payées',NULL,NULL,'Liste des factures fournisseurs payées',NULL,NULL,'FactureFournisseurListe.php?status_factfourn=4','facturier','ListeFactureFournisseur',3,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'ListeFactureF5' ;
INSERT INTO `ref_page` VALUES ('ListeFactureF5','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:20','cl','2010-05-30 09:59:39','cl','Archivées',NULL,NULL,'factureFourn.png','factureFourn.png',' Liste des factures fournisseurs archivées',NULL,NULL,'Liste des factures fournisseurs archivées',NULL,NULL,'FactureFournisseurListe.php?status_factfourn=5','facturier','ListeFactureFournisseur',4,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'ListeFactureFournisseur' ;
INSERT INTO `ref_page` VALUES ('ListeFactureFournisseur','nm','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:20','nm',NULL,'nm','Facture fournisseur',NULL,NULL,'factureFourn.png','factureFourn.png','Liste des factures fournisseurs',NULL,NULL,'Liste des factures fournisseur',NULL,NULL,'FactureFournisseurListe.php','facturier',NULL,3,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'ListeLeads' ;
INSERT INTO `ref_page` VALUES ('ListeLeads','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:21','cl',NULL,'cl','Projets',NULL,NULL,'projet.liste.png','projet.liste.png',' Liste des projets',NULL,NULL,'Liste des projets',NULL,NULL,'ListeLeads.php','prospec','RechercheContact',3,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'Login' ;
INSERT INTO `ref_page` VALUES ('Login','cl',NULL,'2010-06-10 15:32:17','cl','2010-02-19 00:36:29','cl','Authentification','Authentication',NULL,NULL,NULL,'Authentification des utilisateurs','Connect to authorized area',NULL,'Authentification des utilisateurs','Connect to authorized area',NULL,'Login.php','normal',NULL,1,'0','0','','Accueil',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'LogView' ;
INSERT INTO `ref_page` VALUES ('LogView','cl',',0,1,,','2010-09-01 10:50:18','cl','2004-12-31 21:00:00','cl','activité',NULL,NULL,NULL,NULL,'voir le journal d\'activité',NULL,NULL,'Journal d\'activité',NULL,NULL,'LogView.php','admin','Journaux',NULL,'1','0',NULL,'title',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'monBureau' ;
INSERT INTO `ref_page` VALUES ('monBureau','cl',NULL,'2010-06-10 21:34:15','cl','2009-09-28 11:24:45','cl','Mon bureau',NULL,NULL,NULL,NULL,'Mon bureau',NULL,NULL,'Mon bureau',NULL,NULL,'Bureau.php','normal',NULL,1,'0','0',NULL,NULL,NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'NewEcriture' ;
INSERT INTO `ref_page` VALUES ('NewEcriture','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:20','cl','2010-06-07 22:18:24','cl','Journal de banque',NULL,NULL,'JournalEcriture.png','JournalEcriture.png','Nouvelle écriture bancaire',NULL,NULL,'Nouvelle écriture bancaire',NULL,NULL,'JournalEcriture.php','facturier','CreateF',9,'1','0',NULL,'title',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'PageManage' ;
INSERT INTO `ref_page` VALUES ('PageManage','cl',',0,1,,','2010-09-01 10:50:18','cl','2004-12-31 21:00:00','cl','Gestion des pages','Pages management','Seiten','page.manage.png','PageManage.png','Permet de gérer de nouvelles pages','Allow you to manage new pages','Seiten','Gestion des pages du site','Website page management','Seiten','PageManage.php','admin',NULL,1,'1','0',NULL,'title',NULL,'<span class=\\\"important\\\"><br /></span>',NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'PageModif' ;
INSERT INTO `ref_page` VALUES ('PageModif','cl',',0,1,,','2010-09-01 10:52:11','cl','2010-09-01 10:52:11','cl','Modifier','Modify','zu ändern','page.modif.png',NULL,'Modification d\'une page','Modify content of a page','zu ändern','Modifier les informations d\'une page du site','Modify content of a page','zu ändern','PageModif.php','admin','PageManage',2,'1','0',NULL,NULL,NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'PageModif4Lang' ;
INSERT INTO `ref_page` VALUES ('PageModif4Lang','cl',',0,1,,','2010-09-01 10:50:18','cl','2004-12-31 21:00:00','cl','Modifier le contenu','modify content','zu ändern','page.modif.png',NULL,'Modification du contenu d\'une page','modify content for english','zu ändern','Modifier les informations d\'une page du site','modify content for english','zu ändern','PageModif4Lang.php','admin','PageModif',2,'0','0',NULL,'title',NULL,'<span class=\\\"important\\\"><br /></span>',NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'PageModifLot' ;
INSERT INTO `ref_page` VALUES ('PageModifLot','cl',',0,1,,','2010-09-01 10:50:18','cl','2004-12-31 21:00:00','cl','Modifier un lot','Modify','zu ändern','page.modif.png',NULL,'Modification d\'un lot de pages','Modify content of a bunch of page','zu ändern','Modification d\'un lot de pages','Modify content of pages','zu ändern','PageModifLot.php','admin','PageManage',2,'0','0',NULL,NULL,NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'PagePopupImportOdt' ;
INSERT INTO `ref_page` VALUES ('PagePopupImportOdt','cl',',0,1,,','2010-09-01 10:53:47','cl','2010-09-01 10:53:47','cl','Importation de contenu',NULL,NULL,NULL,NULL,'Importation de contenu',NULL,NULL,'Importation de contenu',NULL,NULL,'PagePopup.ImportOdt.php','admin','PageManage',3,'0','0',NULL,'titleContent','L\\\'importation d\\\'un contenu a partir d\\\'un document ODT vous permet de simplifier en une opération l\\\'ajout de contenu dans votre site. <br />Si votre document contien des images, \r\nun répertoire (du nom de l\\\'ID de votre page) sera ajouté dans votre photothèque. Il contiendra toutes les images de votre document. <br /><br />La mise en page de votre document sera adaptée au web et certain styles seront automatiquement supprimé. Si vous constatez une trop grande différence avec votre document original, \r\nvous devez en simplifier sa structure (styles trop complexes ou imbriqués)<br /><br /><br />',NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'PListe' ;
INSERT INTO `ref_page` VALUES ('PListe','nm','0,1,2,3,4,5,10,15,20,,','2010-09-01 10:50:21','nm','2010-05-30 10:06:30','nm','Recherche',NULL,NULL,'produit.png','produit.png','Liste des produits',NULL,NULL,'Liste des produits',NULL,NULL,'ProduitListe.php','produit',NULL,2,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'PontComptable' ;
INSERT INTO `ref_page` VALUES ('PontComptable','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:20','cl','2010-05-30 09:51:55','cl','Pont Comptable',NULL,NULL,'PontComptable.png','PontComptable.png','Facture',NULL,NULL,'Pont Comptable',NULL,NULL,'PontComptable.php','facturier','PontComptableHisto',4,'0','0',NULL,'title',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'PontComptableCreate' ;
INSERT INTO `ref_page` VALUES ('PontComptableCreate','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:20','cl','2010-05-22 07:14:53','cl','Nouvel export',NULL,NULL,'PontComptableCreate.png','PontComptableCreate.png','Nouveau fichier d\'export',NULL,NULL,'Nouveau fichier d\'export',NULL,NULL,'PontComptable.php?action=new','facturier','PontComptableHisto',1,'1','0',NULL,'title',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'PontComptableHisto' ;
INSERT INTO `ref_page` VALUES ('PontComptableHisto','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:20','cl','2010-05-22 22:04:57','cl','Pont Comptable',NULL,NULL,'PontComptable.png','PontComptable.png','Historiques des exports comptable',NULL,NULL,'Historiques des exports comptable',NULL,NULL,'PontComptableHisto.php','facturier',NULL,6,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'PopupAffaire' ;
INSERT INTO `ref_page` VALUES ('PopupAffaire','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:19','cl',NULL,'cl','Popup affaires',NULL,NULL,'affaire.png','affaire.png','Popup affaires',NULL,NULL,'Popup affaires',NULL,NULL,'PopupAffaire.php','draco','ListeAffaire',1,'0','0',NULL,'title',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'PopupAppel' ;
INSERT INTO `ref_page` VALUES ('PopupAppel','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:21','cl',NULL,'cl','Popup Appel',NULL,NULL,'appel.png',NULL,'Popup Appel',NULL,NULL,'Popup Appel',NULL,NULL,'PopupAppel.php','prospec','ProspecFiche',1,'0','0',NULL,'title',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'PopupContact' ;
INSERT INTO `ref_page` VALUES ('PopupContact','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:21','cl',NULL,'cl','Popup Contact',NULL,NULL,'contact.png',NULL,'Popup Contact',NULL,NULL,'Popup Contact',NULL,NULL,'PopupContact.php','prospec','ProspecFiche',1,'0','0',NULL,'title',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'PopupEntreprise' ;
INSERT INTO `ref_page` VALUES ('PopupEntreprise','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:21','cl',NULL,'cl','Popup Entreprise',NULL,NULL,'entreprise.png',NULL,'Popup Entreprise',NULL,NULL,'Popup Entreprise',NULL,NULL,'PopupEntreprise.php','prospec','ProspecFiche',1,'0','0',NULL,'title',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'PopupProjet' ;
INSERT INTO `ref_page` VALUES ('PopupProjet','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:21','cl',NULL,'cl','Popup Projet',NULL,NULL,'projet.png',NULL,'Popup Projet',NULL,NULL,'Popup Projet',NULL,NULL,'PopupProjet.php','prospec','ProspecFiche',1,'0','0',NULL,'title',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'Produit' ;
INSERT INTO `ref_page` VALUES ('Produit','nm','0,1,2,3,4,5,10,15,20,,','2010-09-01 10:50:21','nm',NULL,'nm','Produit',NULL,NULL,'produit.png','produit.png','Gestion des produits',NULL,NULL,'Gestion des produits',NULL,NULL,'Produit.php','produit','CreateP',1,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'ProduitListe' ;
INSERT INTO `ref_page` VALUES ('ProduitListe','nm','0,1,2,3,4,5,10,15,20,,','2010-09-01 10:50:21','nm',NULL,'nm','Produits',NULL,NULL,'produit.png','produit.png','Liste des produits',NULL,NULL,'Liste des produits',NULL,NULL,'ProduitListe.php','produit','PListe',1,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'Projet' ;
INSERT INTO `ref_page` VALUES ('Projet','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:22','cl','2010-01-29 13:33:56','cl','Projet',NULL,NULL,'projet.png','projet.png','Projet',NULL,NULL,'Projet',NULL,NULL,'Projet.php','prospec','ListeLeads',1,'0','0',NULL,'title',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'ProspecFiche' ;
INSERT INTO `ref_page` VALUES ('ProspecFiche','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:22','cl',NULL,'cl','Entreprise',NULL,NULL,'entreprise.png','entreprise.png','Entreprise',NULL,NULL,'Nouvelle entreprise',NULL,NULL,'fiche.php','prospec','CreateC',1,'1','0',NULL,'title',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'ProspecListe' ;
INSERT INTO `ref_page` VALUES ('ProspecListe','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:22','cl',NULL,'cl','Relances',NULL,NULL,'relance.png','relance.png','listes de relance',NULL,NULL,'Liste de prospection',NULL,NULL,'ListeProspec.php','prospec','RechercheContact',2,'1','0',NULL,'title',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'RechercheCEnt' ;
INSERT INTO `ref_page` VALUES ('RechercheCEnt','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:22','cl','2010-05-30 10:17:14','cl','Entreprise / Contact',NULL,NULL,'recherche.prospec.png','recherche.prospec.png','Recherche des contacts',NULL,NULL,'Recherche des contacts',NULL,NULL,'Recherche.php','prospec','RechercheContact',1,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'RechercheContact' ;
INSERT INTO `ref_page` VALUES ('RechercheContact','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:22','cl','2010-05-30 10:11:48','cl','Rechercher',NULL,NULL,'recherche.prospec.png','recherche.prospec.png','Recherche des contacts',NULL,NULL,'Recherche des contacts',NULL,NULL,'Recherche.php','prospec',NULL,3,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'RedactorManual' ;
INSERT INTO `ref_page` VALUES ('RedactorManual','cl',',0,1,,','2010-09-01 10:53:47','cl','2010-09-01 10:53:47','cl','Manuel du redacteur','Writer manual','Handbuch des Verfassers','ManuelRedact.png','ManuelRedact.png','Guide d\'utilisation de l\'administration','Writer manual','Handbuch des Verfassers','Le manuel du redacteur','Writer manual','Handbuch des Verfassers',NULL,'admin','Help',1,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'SendMail' ;
INSERT INTO `ref_page` VALUES ('SendMail','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:22','cl',NULL,'cl','Envoi d\\\'un mail',NULL,NULL,'fileSend.png','fileSend.png',' Envoi d\\\'un mail',NULL,NULL,'Envoi d\\\'un mail',NULL,NULL,'PopupSendMail.php','prospec','ProspecFiche',2,'0','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'SessionDetail' ;
INSERT INTO `ref_page` VALUES ('SessionDetail','cl',',0,1,,','2010-09-01 10:50:18','cl','2004-12-31 21:00:00','cl','Détail de la session','Session detail',NULL,NULL,NULL,'détail de la session','Session detail',NULL,'Détail d\'une session','Session detail',NULL,'SessionDetail.php','admin','Application',3,'0','0',NULL,'title',NULL,'<span class=\\\"important\\\"><br /></span>',NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'SessionView' ;
INSERT INTO `ref_page` VALUES ('SessionView','cl',',0,1,,','2010-09-01 10:50:18','cl','2004-12-31 21:00:00','cl','Session','Session log',NULL,NULL,NULL,'Journal de session','Session log',NULL,'Journal de session','Session log',NULL,'SessionView.php','admin','Journaux',1,'1','0',NULL,'title',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'StatFacture' ;
INSERT INTO `ref_page` VALUES ('StatFacture','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:50:20','cl',NULL,'cl','Statistiques',NULL,NULL,'statG.png','stat.png','Statistiques de facturation',NULL,NULL,'Statistiques de facturation',NULL,NULL,'FactureStats.php','facturier',NULL,3,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'UserCreate' ;
INSERT INTO `ref_page` VALUES ('UserCreate','cl',',0,1,,','2010-09-01 10:52:11','cl','2010-09-01 10:52:11','cl','Créer','Create admin account','zu schaffen','user.admin.create.png',NULL,'Création d\'un compte utilisateur','Create admin account','zu schaffen','Création d\'un compte utilisateur','Create admin account','zu schaffen','UserCreate.php','admin','UserManage',1,'1','0',NULL,NULL,NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'UserManage' ;
INSERT INTO `ref_page` VALUES ('UserManage','cl',',0,1,,','2010-09-01 10:50:18','cl','2004-12-31 21:00:00','cl','Utilisateurs','Users','Benutzer','user.manage.png','UserManage.png','Gestions des utilisateurs','Users accounts management','Benutzer','Gestions des utilisateurs','Users accounts management','Benutzer','UserManage.php','admin',NULL,2,'1','0',NULL,'title',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'UserModif' ;
INSERT INTO `ref_page` VALUES ('UserModif','cl',',0,1,,','2010-09-01 10:52:12','cl','2010-09-01 10:52:12','cl','Modifier','Admin account','zu ändern','user.admin.modif.png',NULL,'Modification d\'un compte utilisateur','Modify a manager account','zu ändern','Modification d\'un compte utilisateur','Modify a manager account','zu ändern','UserModif.php','admin','UserManage',2,'1','0',NULL,'title',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'UserView' ;
INSERT INTO `ref_page` VALUES ('UserView','cl',NULL,'2010-09-01 10:52:12','cl','2010-09-01 10:52:12','cl','Informations utilisateur','User account information','zu ändern','UserView.png',NULL,'Visualisation d\'un compte utilisateur','View a user account','zu ändern','Visualisation d\'un compte utilisateur','View a user account','zu ändern','User.php','normal',NULL,2,'0','0',NULL,'title',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'Work' ;
INSERT INTO `ref_page` VALUES ('Work','cl','0,1,2,3,4,5,10,15,,','2010-09-01 10:53:47','cl','2010-09-01 10:53:47','cl','Répertoire de travail',NULL,NULL,'work.png','work.png','Répertoire partagé',NULL,NULL,'Répertoire partagé',NULL,NULL,'BrowseWork.php','gnose',NULL,1,'1','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'ZunoManage' ;
INSERT INTO `ref_page` VALUES ('ZunoManage','cl',',0,1,,','2010-09-01 10:50:19','cl','2010-01-29 12:32:01','cl','Gestion de Zuno',NULL,NULL,'zuno.manage.png','zuno.manage.png','Gestion de Zuno',NULL,NULL,'Gestion de Zuno',NULL,NULL,'ZunoManage.php','admin',NULL,1,'0','0',NULL,'titleContent',NULL,NULL,NULL,'1');
DELETE FROM `ref_page` WHERE `id_pg` = 'ZunoRefConfigure' ;
INSERT INTO `ref_page` VALUES ('ZunoRefConfigure','cl',',0,1,,','2010-09-01 10:50:19','cl',NULL,'cl','Table de référence',NULL,NULL,'ZunoRefTable.png',NULL,' Table de référence',NULL,NULL,'Table de référence',NULL,NULL,'ZunoRefConfigure.php','admin','ZunoManage',1,'0','0',NULL,'titleContent',NULL,NULL,NULL,'1');
 
--
-- Suppression des données qui n'existent plus
--
 
