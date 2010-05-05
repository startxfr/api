-- cleanning up database
-- ------------------------------------------------------
TRUNCATE `actualite`;
TRUNCATE `affaire`;
TRUNCATE `appel`;
TRUNCATE `commande`;
TRUNCATE `commande_produit`;
TRUNCATE `contact`;
TRUNCATE `devis`;
TRUNCATE `devis_produit`;
TRUNCATE `devis_renew`;
TRUNCATE `entreprise`;
TRUNCATE `facture`;
TRUNCATE `facture_produit`;
TRUNCATE `fournisseur`;
TRUNCATE `produit`;
TRUNCATE `produit_fournisseur`;
TRUNCATE `projet`;
TRUNCATE `session`;
TRUNCATE `user`;
TRUNCATE `user_droits`;
TRUNCATE `user_iphoneConfig`;
TRUNCATE `facture_fournisseur`;
TRUNCATE `message`;
TRUNCATE `renouvellement`;
TRUNCATE `token`;
TRUNCATE `send`;



INSERT INTO `actualite` VALUES (1,'2010-03-10 23:28:32','cl','General','Ouverture de ZUNO-SXA','sxa.startx.fr est maintenant disponible',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'0','0','1');
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