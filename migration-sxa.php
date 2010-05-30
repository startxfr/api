<?php
/*#########################################################################
#
#   name :       index.php
#   desc :       Homepage
#   categorie :  management page
#   ID :  	 $Id: index.php 3386 2009-11-30 22:48:00Z cl $
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| FRAMEWORK LOADING	& LOG ACTIVITY
+------------------------------------------------------------------------*/
include ('inc/conf.inc');		// Declare global variables from config files
include ('inc/core.inc');		// Load core library

$uinf = GetClientBrowserInfo();
if($uinf[0] == 'iPod' or $uinf[0] == 'iPhone') {
    header("Location: ".$GLOBALS['CHANNEL_iPhone']['path']);
    exit;
}
/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/
$_SERVER["SCRIPT_NAME"] = '/test/index.php';
// Whe get the page context
$PC = new PageContext();
$PC->GetFullContext();
// Whe initialize page display
$out = new PageDisplay($PC->channel);
$out->ConfigureWithPageData($PC->Data,$PC->cacheXML);
$bddNew = new Bdd(1);
$bddOld = new Bdd(2);
set_time_limit(240);
ini_set("memory_limit","200M");

// WARNING Avant l'import, ouvrir le fichier de dump SXA et
//  Changer tout les latin1 en utf8
// Changer la clef de la table document (trop longue)
// Changer la clef de la table page (trop longue)
// Changer la clef de la table prop_rules (trop longue)
//
// puis lancer la commande mysql -h localhost -u dev -pdev --default_character_set utf8 ZunoDev_sxaOld < SXA.sql
$bddOld->makeRequeteFree("UPDATE commande SET contact_cmd =  '134814' WHERE id_cmd = '090608-01BC';");
$bddOld->process();
$bddOld->makeRequeteFree("DELETE FROM affaire WHERE id_aff = '060401';");
$bddOld->process();
$bddNew->makeRequeteFree("CREATE TABLE IF NOT EXISTS `ref_redhat_archi` (
  `id_arch` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `nom_arch` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_arch`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Type d''architecture materiel pour les produits REDHAT';");
$bddNew->process();
$bddNew->makeRequeteFree("CREATE TABLE IF NOT EXISTS `ref_redhat_contrat` (
  `id_cont` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `nom_cont` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_cont`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Type de contrat proposé par REDHAT';");
$bddNew->process();
$bddNew->makeRequeteFree("INSERT IGNORE INTO ref_fonction (`id_fct`, `nom_fct`, `idsuperieur_fct`) VALUES ('99', 'Plus dans l''entreprise', NULL);");
$bddNew->process();


$end = "<br/>";
echo "================== DEBUT DE LA MIGRATION ==================$end";
$tablesAImporter = array('produit','actualite','affaire','appel','commande','commande_produit','contact','devis','devis_produit','devis_renew',
	'entreprise','facture','facture_produit','fournisseur','produit_fournisseur','projet','redhat_archi',
	'redhat_contrat','session');
$matriceDeConversionRefProduit = array("AUDIT"=>"S14-AUDIT",
"FORM/PLB"=>"S32-XML","ZFSTD"=>"R21-STD","ZFSTDCAL"=>"R21-STDCAL","ZFSTDRN"=>"R21-STDRN","ZFSTDRNCAL"=>"R21-STDRNCAL","SXZFA"=>"R21-STD","CONS"=>"S11-CONSULTING","SERVMG"=>"S11-CONSULTING","SXCONS"=>"S11-CONSULTING","SXCONS1"=>"S11-CONSULTING","SXCONS2"=>"S11-CONSULTING","SXCONS3"=>"S11-CONSULTING","SXCONS4"=>"S11-CONSULTING","VPN"=>"S11-CONSULTING","SXPRES"=>"S11-CONSULTING","SXSER"=>"S11-CONSULTING","SXSER1"=>"S11-CONSULTING","SXSERV"=>"S11-CONSULTING","SXDOC"=>"S12-DOCU","REGIE"=>"S13-REGIE","SXAUDT"=>"S14-AUDIT","DEV"=>"S21-DEV",
"DEV-FORF"=>"S21-DEV","DEV1"=>"S21-DEV","SERVDEV2"=>"S21-DEV","SXDEV"=>"S21-DEV","SXDEV0"=>"S21-DEV","SXDEV1"=>"S21-DEV","SXDEV2"=>"S21-DEV","SXDEV3"=>"S21-DEV","SXDEV4"=>"S21-DEV","SXDEVRT"=>"S21-DEV","SXDEVS"=>"S21-DEV","DEVGNOSE"=>"S22-DEVGED","SHL"=>"S31-SHL","FORM"=>"S32-DEV","FROM"=>"S32-PHA","SXFORM-PHA"=>"S32-PHA","WNI"=>"S32-WNI","HEBERG"=>"S41-HEBERG1Y",
"SXHEB"=>"S42-CLOUD","FRAIS"=>"X11-FRAIS","SXENV16"=>"X12-EXPED","SXENV30"=>"X12-EXPED","CFE-SVN"=>"S22-DEVGED","CSLDAP"=>"S21-DEV","DVMD/A"=>"S21-DEV","DVMD/B"=>"S21-DEV","DVMD/C"=>"S21-DEV","DVMD/D"=>"S21-DEV","DVMD/E"=>"S21-DEV","DVMD/F"=>"S21-DEV","DVMD/G"=>"S21-DEV","DVMD/H"=>"S21-DEV","ENV"=>"X12-EXPED","EVENT"=>"X21-EVENT","FMT"=>"F11-FORMATION","FMT-PHH"=>"S32-PHH","FMT-SPV"=>"S32-SPV","INTV"=>"S11-CONSULTING",
"LUT-DV"=>"S21-DEV","OPINSERM"=>"R11-OPINSERM","OPMKGRH"=>"X21-LEADS","P001"=>"S21-DEV","P002"=>"S21-DEV","P003"=>"S21-DEV","P004"=>"S21-DEV","P005"=>"S21-DEV","P006"=>"S21-DEV","P007"=>"S21-DEV","P008"=>"S21-DEV","P010"=>"S21-DEV","P011"=>"S21-DEV","REGUL"=>"S21-DEV","S001"=>"S21-DEV","SANSREF"=>"S11-CONSULTING","SER"=>"S11-CONSULTING","SERVFX"=>"S11-CONSULTING","SERVSPM"=>"S11-CONSULTING","SUPP"=>"S13-REGIE",
"SUPP1"=>"S13-REGIE","SUPP2"=>"S13-REGIE","SVFW"=>"S11-CONSULTING","SXETD"=>"S21-DEV","SXFORM"=>"S31-FORM","SXFORM1"=>"S31-FORM1","SXFORMWB"=>"F11-FORMATION","SXMOD1"=>"S21-DEV","SXMOD2"=>"S21-DEV","SXMOD3"=>"S21-DEV","SXMOD4"=>"S21-DEV","SXPREP"=>"S11-CONSULTING","SXREG"=>"S12-REGIE","SXSUPP"=>"S15-SUPPORT","SXSYS1"=>"S11-CONSULTING","SXTSUP"=>"S15-SUPPORT","WBD01"=>"S23-CONSULTING");
$matriceDeConversionRefProdFamille = array("1"=>"3","2"=>"25","3"=>"25","4"=>"30","5"=>"26","6"=>"25","7"=>"27","8"=>"29","9"=>"28","10"=>"30","11"=>"29","12"=>"32");
$prodPriceList = array();
foreach($tablesAImporter as $table) {
    if($table == 'redhat_archi' or $table == 'redhat_contrat')
	$tableNew = 'ref_'.$table;
    else $tableNew = $table;
    echo "================== table $table $end";
    $bddOld->makeRequeteFree('SELECT * FROM '.$table);
    $list = $bddOld->process();
    if(count($list) > 0) {
	$count = 0;
	$bddNew = new Bdd(1);
	$bddNew->makeRequeteFree("TRUNCATE TABLE $tableNew;");
	$bddNew->process();
	$countOld = count($list);
	$timeLoopBegin = microtime(true);
	foreach($list as $k => $v) {
	    if($table == 'actualite') {
		$v['isPublic'] = 0;
		$v['isPublieForClient'] = 0;
		$v['isVisibleFilActu'] = 1;
	    }
	    elseif($table == 'contact') {
		if($v['civ_cont'] == '') $v['civ_cont'] = 'M.';
		if($v['nom_cont'] == '') $v['nom_cont'] = '???';
		if(in_array($v['fonction_cont'],array('12','14','15'))) $v['fonction_cont'] = '5';
		elseif(in_array($v['fonction_cont'],array('25','24','35','30','31','45','41','29','33','42','28','39','34','36','38','49','42','37','27','43'))) $v['fonction_cont'] = '9';
		elseif($v['fonction_cont'] == '91') $v['fonction_cont'] = '';
		elseif(in_array($v['fonction_cont'],array('92','7','9'))) $v['fonction_cont'] = '2';
		elseif(in_array($v['fonction_cont'],array('4','6'))) $v['fonction_cont'] = '3';
		elseif(in_array($v['fonction_cont'],array('3','2'))) $v['fonction_cont'] = '1';
		elseif($v['fonction_cont'] == '61') $v['fonction_cont'] = '4';
		elseif($v['fonction_cont'] == '90') $v['fonction_cont'] = '99';
		elseif($v['fonction_cont'] == '50') $v['fonction_cont'] = '10';
		elseif(in_array($v['fonction_cont'],array('10','60'))) $v['fonction_cont'] = '8';
		elseif(in_array($v['fonction_cont'],array('93','62'))) $v['fonction_cont'] = '7';
		elseif(in_array($v['fonction_cont'],array('8','72'))) $v['fonction_cont'] = '6';
		else $v['fonction_cont'] = '';
	    }
	    elseif($table == 'entreprise') {
		if($v['type_ent'] == '5') $v['type_ent'] = '4';
		elseif($v['type_ent'] == '4') $v['type_ent'] = '3';
		elseif($v['type_ent'] == '3') $v['type_ent'] = '2';
		if($v['visite_ent'] == '1') $v['commsociete_ent'] = 'Client déjà rencontré. '.$v['commsociete_ent'];
		unset($v['visite_ent'],$v['FAI_ent'],$v['hebergeur_ent'],$v['SSLL_ent'],$v['SSII_ent'],$v['infogereur_ent'],$v['commprestataire_ent'],$v['firewall_ent'],$v['IDS_ent'],$v['proxy_ent'],$v['DNS_ent'],$v['FTP_ent'],$v['VPN_ent'],$v['supervisionreseau_ent'],$v['postes_ent'],$v['sites_ent'],$v['windows_ent'],$v['mac_ent'],$v['station_ent'],$v['cluster_ent'],$v['messagerie_ent'],$v['web_ent'],$v['fichier_ent'],$v['routeur_ent'],$v['antivirus_ent'],$v['authentification_ent'],$v['audit_ent']);
	    }
	    elseif($table == 'devis_produit' or $table == 'commande_produit') {
		if($v['id_produit'] == '') $v['id_produit'] = 'SANSREF';
		if(array_key_exists($v['id_produit'],$matriceDeConversionRefProduit))
		    $v['id_produit'] = $matriceDeConversionRefProduit[$v['id_produit']];
		elseif(substr($v['id_produit'],0,5) == "DVMD/" or
		       substr($v['id_produit'],0,5) == "HTML-" or
		       substr($v['id_produit'],0,5) == "SXDEV" or
		       substr($v['id_produit'],0,3) == "DEV" or
		       substr($v['id_produit'],0,4) == "GH1." or
		       $v['id_produit'] == "CF1" or
		       $v['id_produit'] == "CF2" or
		       $v['id_produit'] == "CF3" or
		       $v['id_produit'] == "GPH001" or
		       $v['id_produit'] == "GRPH" or
		       $v['id_produit'] == "CMS" or
		       $v['id_produit'] == "CMS1" or
		       $v['id_produit'] == "CONS")
		    $v['id_produit'] = 'S21-DEV';
		elseif(substr($v['id_produit'],0,6) == "FPHPLB")
		    $v['id_produit'] = 'S32-PHP';
		elseif(substr($v['id_produit'],0,5) == "SXSER" or
			substr($v['id_produit'],0,6) == "SXPRES" or
			substr($v['id_produit'],0,6) == "SXCONS")
		    $v['id_produit'] = 'S11-CONSULTING';
		elseif(substr($v['id_produit'],0,5) == "SXENV")
		    $v['id_produit'] = 'X12-EXPED';

	    }
	    elseif($table == 'facture_produit') {
		if($v['id_produit'] == '') $v['id_produit'] = 'SANSREF';
		if($v['id_facture'] <= 330) {
		    if(strpos($v['desc'], 'Academic Edition') !== false or
			    strpos($v['desc'], 'Academic edition') !== false or
			    strpos($v['desc'], 'académique') !== false)
			$v['id_produit'] = 'MCT0402';
		    elseif(strpos($v['desc'], 'Contrat de support Red Hat Enterprise Linux AS') !== false or
			    strpos($v['desc'], 'Contrat de support RedHat Enterprise Linux AS') !== false or
			    strpos($v['desc'], 'Contrat de support RedHat Entrprise Linux AS') !== false or
			    strpos($v['desc'], 'Contrat RedHat Entreprise Linux AS') !== false  or
			    strpos($v['desc'], 'Souscription Media kit AS') !== false or
			    strpos($v['desc'], 'RHES AS 3 Standard') !== false or
			    strpos($v['desc'], 'RHEL AS 2.1') !== false or
			    strpos($v['desc'], 'Souscription Red Hat Enterprise Linux AS') !== false or
			    strpos($v['desc'], 'Red Hat Enterprise Linux AS 3') !== false or
			    strpos($v['desc'], 'RedHat Enterprise Linux AS') !== false or
			    strpos($v['desc'], 'Logiciel Red Hat Enterprise Linux AS') !== false or
			    strpos($v['desc'], 'logiciel RedHat Enterprise Linux AS') !== false or
			    strpos($v['desc'], 'Licence Red Hat Enterprise Linux AS') !== false or
			    strpos($v['desc'], 'Contrat RedHat Entreprise Linux AS') !== false or
			    strpos($v['desc'], 'Contrat de maintenance RedHat Entreprise Linux AS') !== false or
			    strpos($v['desc'], 'RedHat Entreprise Linux 3.0 AS') !== false or
			    strpos($v['desc'], 'RedHat Entreprise Linux AS') !== false  or
			    strpos($v['desc'], 'Logiciel RedHat Entreprise Linux AS') !== false )
			$v['id_produit'] = 'MCT0335';
		    elseif(strpos($v['desc'], 'Contrat de support Red Hat Enterprise Linux ES') !== false or
			    strpos($v['desc'], 'Contrat de support RedHat Enterprise Linux ES') !== false or
			    strpos($v['desc'], 'Contrat de support RedHat Entrprise Linux ES') !== false or
			    strpos($v['desc'], 'Contrat RedHat Entreprise Linux ES') !== false  or
			    strpos($v['desc'], 'Souscription Media kit ES') !== false or
			    strpos($v['desc'], 'RHEL ES 3 Standard') !== false or
			    strpos($v['desc'], 'RHEL ES 2.1') !== false or
			    strpos($v['desc'], 'Souscription Red Hat Enterprise Linux ES') !== false or
			    strpos($v['desc'], 'Red Hat Enterprise Linux ES') !== false or
			    strpos($v['desc'], 'RedHat Enterprise Linux ES') !== false or
			    strpos($v['desc'], 'Logiciel Red Hat Enterprise Linux ES') !== false or
			    strpos($v['desc'], 'logiciel RedHat Enterprise Linux ES') !== false or
			    strpos($v['desc'], 'Contrat de souscription RedHat Entreprise Linux ES') !== false or
			    strpos($v['desc'], 'Contrat de support logiciel RedHat Entreprise Linux ES') !== false or
			    strpos($v['desc'], 'RedHat Entreprise Linux v 3.0') !== false or
			    strpos($v['desc'], 'Logiciel RedHat Entreprise Linux ES') !== false or
			    strpos($v['desc'], 'RedHat Entreprise Linux ES') !== false  or
			    strpos($v['desc'], 'Distribution RedHat Entrepirse Linux') !== false or
			    strpos($v['desc'], 'RedHat Enterprise Linux Version 3.0 edition standa') !== false )
			$v['id_produit'] = 'MCT0345';
		    elseif(strpos($v['desc'], 'Contrat de support Red Hat Enterprise Linux WS') !== false or
			    strpos($v['desc'], 'Contrat de support RedHat Enterprise Linux WS') !== false or
			    strpos($v['desc'], 'Contrat de support RedHat Entrprise Linux WS') !== false or
			    strpos($v['desc'], 'Contrat RedHat Entreprise Linux WS') !== false  or
			    strpos($v['desc'], 'Souscription Media kit WS') !== false or
			    strpos($v['desc'], 'RHEL WS 3 Standard') !== false or
			    strpos($v['desc'], 'RHEL WS 2.1') !== false or
			    strpos($v['desc'], 'Souscription Red Hat Enterprise Linux WS') !== false or
			    strpos($v['desc'], 'Red Hat Enterprise Linux WS') !== false or
			    strpos($v['desc'], 'RedHat Enterprise Linux WS') !== false or
			    strpos($v['desc'], 'Logiciel Red Hat Enterprise Linux WS') !== false or
			    strpos($v['desc'], 'logiciel RedHat Enterprise Linux WS') !== false or
			    strpos($v['desc'], 'Contrat de souscription RedHat Entreprise Linux WS') !== false or
			    strpos($v['desc'], 'Contrat de support logiciel RedHat Entreprise Linux WS') !== false or
			    strpos($v['desc'], 'Contrat de support Red Hat Enterprise LinuxWS') !== false or
			    strpos($v['desc'], 'RedHat Entreprise Linux WS') !== false or
			    strpos($v['desc'], 'Logiciel RedHat Entreprise Linux WS') !== false or
			    strpos($v['desc'], 'RedHat Entreprise Linux 2.1 WS') !== false  or
			    strpos($v['desc'], 'Redhat Entreprise Linux WS 3') !== false or
			    strpos($v['desc'], '1 RHEL WS 3') !== false)
			$v['id_produit'] = 'MCT0351';
		    elseif(strpos($v['desc'], 'éalisation de CD') !== false or
			    strpos($v['desc'], 'Media kit') !== false or
			    strpos($v['desc'], 'média Kit') !== false or
			    strpos($v['desc'], 'réalisation d\'un CD') !== false)
			$v['id_produit'] = 'RHF0148US';
		    elseif(strpos($v['desc'], 'Formation') !== false or
			    strpos($v['desc'], 'formation') !== false )
			$v['id_produit'] = 'SXFORM1';
		    elseif(strpos($v['desc'], 'Frai d\'envoi') !== false or
			    strpos($v['desc'], 'Frais d envoi') !== false or
			    strpos($v['desc'], 'Frais d\'envoi') !== false or
			    strpos($v['desc'], 'frais d\'envoi') !== false or
			    strpos($v['desc'], 'Frais de livraison') !== false or
			    strpos($v['desc'], 'frais de livraison') !== false or
			    strpos($v['desc'], 'fris de livraison') !== false or
			    strpos($v['desc'], 'Fris de livraison') !== false or
			    strpos($v['desc'], 'Frais de port') !== false or
			    strpos($v['desc'], 'frais de port') !== false or
			    strpos($v['desc'], 'rais de livraison') !== false or
			    strpos($v['desc'], 'Contrat de support Frais envoi') !== false)
			$v['id_produit'] = 'SXENV16';
		    elseif(strpos($v['desc'], 'Installation et configuration') !== false or
			    strpos($v['desc'], 'Intervention') !== false or
			    strpos($v['desc'], 'Journée') !== false or
			    strpos($v['desc'], 'journée') !== false or
			    strpos($v['desc'], 'Assistance') !== false or
			    strpos($v['desc'], 'assistance') !== false or
			    strpos($v['desc'], 'Prestation d') !== false or
			    strpos($v['desc'], 'prestation') !== false or
			    strpos($v['desc'], 'Transfert de compétences') !== false or
			    strpos($v['desc'], 'Etude et Assistance') !== false)
			$v['id_produit'] = 'SXCONS2';
		    elseif(strpos($v['desc'], 'ecobra.com') !== false or
			    strpos($v['desc'], 'stagiaire.fr') !== false or
			    strpos($v['desc'], 'ihes.fr') !== false or
			    strpos($v['desc'], 'adinas.net') !== false or
			    strpos($v['desc'], 'Hébergment') !== false or
			    strpos($v['desc'], 'congress.fr') !== false or
			    strpos($v['desc'], 'artshaker') !== false or
			    strpos($v['desc'], 'réalisation d\'un intranet') !== false or
			    strpos($v['desc'], 'embauchehandicap.fr') !== false or
			    strpos($v['desc'], 'site internet') !== false)
			$v['id_produit'] = 'SXDEV3';
		}
		if(array_key_exists($v['id_produit'],$matriceDeConversionRefProduit)) {
		    $oldProduitRef = $v['id_produit'];
		    $v['id_produit'] = $matriceDeConversionRefProduit[$v['id_produit']];
		}
		if(in_array($v['id_facture'] ,array(666,696,710,735,786,689)) and array_key_exists($oldProduitRef,$matriceDeConversionRefProduit))
		    $v['id_produit'] = 'S32-FORM';
		if(in_array($v['id_facture'] ,array(356,598,732)) and array_key_exists($oldProduitRef,$matriceDeConversionRefProduit))
		    $v['id_produit'] = 'S31-FORM';
	    }
	    elseif($table == 'fournisseur') {
		$v['actif'] = '1';
	    }
	    elseif($table == 'produit') {
		$v['stock_prod'] = '0';
		if(in_array($v['famille_prod'], array(2,3,4,5,6,7,8,10,11,12)))
		    $v['compteComptable_prod'] = '70616000';
		elseif(in_array($v['famille_prod'], array(1))) {
		    if(in_array($v['id_prod'], array('SXENV16','SXENV30')))
			$v['compteComptable_prod'] = '70618000';
		    elseif(in_array($v['id_prod'], array('SXFORM1')))
			$v['compteComptable_prod'] = '70613000';
		    elseif(in_array($v['id_prod'], array('SXDEP190','SXDEP300')))
			$v['compteComptable_prod'] = '70617000';
		    else $v['compteComptable_prod'] = '70615000';
		}
		elseif(in_array($v['famille_prod'], array(9)))
		     $v['compteComptable_prod'] = '70613000';
		else $v['compteComptable_prod'] = '70616000';
		$v['dureeRenouvellement_prod'] = $v['duree_prod'];
		if($v['description_prod'] == '') $v['description_prod'] = $v['nom_prod'];
		unset($v['keyredhat_prod'],$v['public_prod'],$v['media_prod'],$v['duree_prod']);
		$prodPriceList[$v['id_prod']] = $v['prix_prod'];
		$v['refExterne'] = $v['id_prod'];
		if(substr($v['id_prod'],0,5) == "SXDEP")
		    $v['id_prod'] = 'X11-DEPLACEMENT';
		elseif(substr($v['id_prod'],0,6) == "SXCONS")
		    $v['id_prod'] = 'S11-CONSULTING';
		elseif(substr($v['id_prod'],0,5) == "SXDEV")
		    $v['id_prod'] = 'S21-DEV';
		elseif(substr($v['id_prod'],0,5) == "SXENV")
		    $v['id_prod'] = 'X12-ENV';
		elseif(substr($v['id_prod'],0,6) == "SXFORM")
		    $v['id_prod'] = 'F21-FORM';
		elseif(substr($v['id_prod'],0,2) == "ZF" and $v['famille_prod'] == '12')
		    $v['id_prod'] = 'R21-'.$v['id_prod'];
		if(array_key_exists($v['famille_prod'],$matriceDeConversionRefProdFamille))
		    $v['famille_prod'] = $matriceDeConversionRefProdFamille[$v['famille_prod']];
	    }
	    elseif($table == 'produit_fournisseur') {
		$v['prixF'] = $prodPriceList[$v['produit_id']];
		$v['actif'] = '1';
	    }
	    elseif($table == 'commande_produit') {
		$v['prixF'] = $prodPriceList[$v['id_produit']];
	    }
	    elseif($table == 'projet') {
		if(in_array($v['typeproj_proj'],array('16','17','18'))) $v['typeproj_proj'] = '2';
		elseif(in_array($v['typeproj_proj'],array('4','5','9','15','21'))) $v['typeproj_proj'] = '4';
		elseif(in_array($v['typeproj_proj'],array('7','10','11'))) $v['typeproj_proj'] = '5';
		elseif(in_array($v['typeproj_proj'],array('2','3','6','6','12','13','14','19','20'))) $v['typeproj_proj'] = '3';
		else $v['typeproj_proj'] = '1';
		if($v['titre_proj'] == '')
		    $v['titre_proj'] = '???';
		$v['actif_proj'] = '0';
		unset($v['rdv_idgrpw_proj']);
	    }
	    foreach ($v as $k => $v2) {
		if(!in_array($k, array('backup_sess')))
		$v[$k] = str_replace('\\\'', '\'', $v2);
	    }
	    $q = $bddNew->makeRequeteInsert($tableNew,$v);
	    $r = $bddNew->process2();
	    if($r[0]) $count++;
	    else {
		echo "ERREUR sur la requête: $q $end";
		echo '===> '.mysql_error().$r[1].$end;
	    }

	    if(in_array($k, array(400,800,1200,1600,2000,2400,2800,3200,3600,4000,4400,4800,5200,5600,6000,6400,6800,7200,7600,8000,8400,8800,9200,9600,10000,10400,10800,11200,11600,12000)))
		sleep(2);
	}

	$timeLoop = microtime(true)-$timeLoopBegin;
	if($timeLoop > 60)
	    $timeLoop = round(($timeLoop/60),4).' minutes';
	else $timeLoop = round($timeLoop,4).' secondes';
	$diff = $countOld-$count;
	if(($countOld-$count) > 0)
	    $add = '<span style="color:red; font-weight: bold">(perte de '.($countOld-$count).' ligne(s))</span>';
	else $add = '<span style="color:lightgreen; font-weight: bold">(aucune perte)</span>';
	echo "================== table $tableNew = $count enregistrements en ".$timeLoop." $add $end";
    }
}

// SQL DE NETOYAGE DES INFO INUTILES
$bddNew->makeRequeteFree('DELETE FROM actualite WHERE `id_ent` IS NULL AND  `id_cont` IS NULL AND  `id_aff` IS NULL AND  `id_dev` IS NULL AND  `id_cmd` IS NULL AND  `id_fact` IS NULL;');
$bddNew->process2();
$bddNew->makeRequeteFree("UPDATE `actualite` SET `id_ent` =  '9550', `id_cont` = '134785' WHERE `id` =9212;");
$bddNew->process2();
$bddNew->makeRequeteFree("UPDATE `actualite` SET `id_ent` =  '9457', `id_cont` = '134064' WHERE `id` =9275;");
$bddNew->process2();
$bddNew->makeRequeteFree("DELETE FROM actualite WHERE `id_ent` IS NULL AND `id_dev` IS NOT NULL;");
$bddNew->process2();
$bddNew->makeRequeteFree("UPDATE `actualite` SET `id_ent` =  '9578' WHERE `id_cont` =134281;");
$bddNew->process2();
$bddNew->makeRequeteFree("UPDATE `actualite` SET `id_cont` =  '134814' WHERE `id_ent` =9762;");
$bddNew->process2();
$bddNew->makeRequeteFree("UPDATE `actualite` SET `id_aff` =  SUBSTRING_INDEX(id_dev, '-', 1) WHERE `id_aff` IS NULL AND `id_dev` IS NOT NULL;");
$bddNew->process2();
$bddNew->makeRequeteFree("UPDATE `actualite` SET `id_fact` = SUBSTRING(titre,23,4) WHERE type = 'facture' AND `id_fact` IS NULL;");
$bddNew->process2();
$bddNew->makeRequeteFree("DELETE FROM  appel WHERE comm_app LIKE 'Insert_auto'");
$bddNew->process2();
$bddNew->makeRequeteFree("DELETE FROM produit WHERE id_prod IN ('SER0099US','SER0102US','SER0105US','SER0184US','SER0185US','RHF0095US','RHF0133US','RHF0136US','RHF0139US','RHF0142US','RHF0145US','RHF0151US','RHF0154US','RHF0157US','RHF0160US','RHF0163US','RHF0262US','RHF0267US','RHF0270US','RHF0273US','RHF0319US','RHF0322US','RHF0325US','RHF0328US','RHF0331US','RHF0334US','RHF0374US','RHF0375US','RHF0376US','RHF0377US')");
$bddNew->process2();
$bddNew->makeRequeteFree("TRUNCATE TABLE  ref_prodfamille;");
$bddNew->process2();
$bddNew->makeRequeteFree("INSERT INTO `ref_prodfamille` (`id_prodfam`, `nom_prodfam`, `livrable`, `revente`, `treePathKey`) VALUES
(1, 'Services', '0', '0', 'S'),
(2, 'Prestations', '0', '0', 'S1'),
(3, 'Système', '0', '0', 'S11'),
(4, 'Rédaction de doc', '0', '0', 'S12'),
(5, 'Régie', '0', '0', 'S13'),
(6, 'Audits', '0', '0', 'S14'),
(7, 'Support', '0', '0', 'S15'),
(8, 'Développements', '0', '0', 'S2'),
(9, 'Développements PHP', '0', '0', 'S21'),
(10, 'Développements GED', '0', '0', 'S22'),
(11, 'Consulting', '0', '0', 'S23'),
(12, 'Formations', '0', '1', 'S3'),
(13, 'Formations Système', '0', '1', 'S31'),
(14, 'Formations Développement', '0', '1', 'S32'),
(15, 'Hébergement', '0', '0', 'S4'),
(16, 'Hébergement web', '0', '0', 'S41'),
(17, 'Hébergement Système', '0', '0', 'S42'),
(18, 'Formations', '0', '0', 'F'),
(19, 'Cours Système', '0', '0', 'F1'),
(20, 'Clustering', '0', '0', 'F11'),
(21, 'Cours développement', '0', '0', 'F2'),
(22, 'Framework', '0', '0', 'F21'),
(23, 'Revente', '0', '1', 'R'),
(24, 'Redhat', '0', '1', 'R1'),
(25, 'Souscriptions RHEL', '0', '1', 'R11'),
(26, 'RHEL Académique', '0', '1', 'R12'),
(27, 'RHN', '0', '1', 'R13'),
(28, 'Formations', '0', '1', 'R14'),
(29, 'Jboss', '0', '1', 'R15'),
(30, 'Divers', '0', '1', 'R19'),
(31, 'Zarafa', '0', '1', 'R2'),
(32, 'Licences', '0', '1', 'R21'),
(33, 'Formations', '0', '1', 'R22'),
(34, 'Zuno', '0', '0', 'Z'),
(35, 'Abonnements', '0', '0', 'Z1'),
(36, 'Instance', '0', '0', 'Z11'),
(37, 'Modules', '0', '0', 'Z12'),
(38, 'Utilisateurs', '0', '0', 'Z13'),
(39, 'Consommables', '0', '0', 'Z2'),
(40, 'Expéditions', '0', '0', 'Z21'),
(41, 'Services', '0', '0', 'Z3'),
(42, 'Gestion de l\'instance', '0', '0', 'Z31'),
(43, 'Demande ponctuelle', '0', '0', 'Z32'),
(44, 'Exceptionnel', '0', '0', 'Z4'),
(45, 'Opérations marketing', '0', '0', 'Z41'),
(46, 'Autres ', '0', '0', 'X'),
(47, 'Frais', '0', '0', 'X1'),
(48, 'Frais divers', '0', '0', 'X11'),
(49, 'Frais d''expédition', '0', '0', 'X12'),
(50, 'Marketing', '0', '0', 'X2'),
(51, 'Leads', '0', '0', 'X21'),
(52, 'xxxxxxxxx', '1', '1', 'R19');");
$bddNew->process();
$bddNew->makeRequeteFree("INSERT INTO `produit` (`id_prod`, `nom_prod`, `famille_prod`, `description_prod`, `dureeRenouvellement_prod`, `prix_prod`, `stock_prod`, `remisefournisseur_prod`, `bestsell_prod`, `stillAvailable_prod`, `archi_prod`, `contrat_prod`, `familleredhat_prod`, `prodredhat_prod`, `compteComptable_prod`, `refExterne`) VALUES
('Z11-ABO0', 'Zuno Sans engagement', 36, 'Abonnement ZUNO sans engagement', NULL, '11.90', 0, 0, '0', 1, NULL, NULL, NULL, '0', '70616000', 'XXXXX'),
('Z11-ABO12', 'Zuno 12 mois', 36, 'Abonnement de Zuno sur 12 mois ', NULL, '9.90', 0, 0, '1', 1, NULL, NULL, NULL, '0', '70616000', 'XXXXX'),
('Z11-ABO24', 'Zuno 24 mois', 36, 'Abonnement de ZUNO sur 24 mois', NULL, '8.49', 0, 0, '0', 1, NULL, NULL, NULL, '0', '70616000', 'XXXXX'),
('Z12-ACTU', 'Module Actualité', 37, 'Module Actualité de ZUNO', NULL, '0', 0, 0, '1', 1, NULL, NULL, NULL, '0', '70616000', 'XXXXX'),
('Z12-AFF', 'Module Affaire', 37, 'Module Affaire de ZUNO', NULL, '0', 0, 0, '1', 1, NULL, NULL, NULL, '0', '70616000', 'XXXXX'),
('Z12-AVOIR', 'Module Avoir', 37, 'Module Avoir de ZUNO', NULL, '0', 0, 0, '1', 1, NULL, NULL, NULL, '0', '70616000', 'XXXXX'),
('Z12-CMD', 'Module Commande', 37, 'Module Commande de ZUNO', NULL, '0', 0, 0, '1', 1, NULL, NULL, NULL, '0', '70616000', 'XXXXX'),
('Z12-COMPTA', 'Module Pont Comptable', 37, 'Module des pont comptable de ZUNO', NULL, '1.50', 0, 0, '1', 1, NULL, NULL, NULL, '0', '70616000', 'XXXXX'),
('Z12-CONT', 'Module Contact', 37, 'Module Contact de ZUNO', NULL, '3', 0, 0, '1', 1, NULL, NULL, NULL, '0', '70616000', 'XXXXX'),
('Z21-COU1', 'Courrier ZUNO', 40, 'Page supplémentaire de courrier ZUNO', NULL, '0.49', 0, 0, '0', 1, NULL, NULL, NULL, '0', '70616000', 'XXXXX'),
('Z21-COUR', 'Courrier ZUNO', 40, 'Première page d''un courrier ZUNO', NULL, '1.99', 0, 0, '0', 1, NULL, NULL, NULL, '0', '70616000', 'XXXXX'),
('Z12-DDV', 'Disque Dur Virtuel', 37, 'Accès à la plateforme WebDav de ZUNO', NULL, '1.99', 0, 0, '0', 1, NULL, NULL, NULL, '0', '70616000', 'XXXXX'),
('Z12-DEV', 'Module Devis', 37, 'Module Devis de ZUNO', NULL, '0', 0, 0, '1', 1, NULL, NULL, NULL, '0', '70616000', 'XXXXX'),
('Z12-ENV', 'Module Envoi', 37, 'Module Envoi de ZUNO', NULL, '2.99', 0, 0, '1', 1, NULL, NULL, NULL, '0', '70616000', 'XXXXX'),
('Z31-FACT', 'Facture Détaillée', 42, 'Facture détaillée de l''abonnement ZUNO', NULL, '0.99', 0, 0, '0', 1, NULL, NULL, NULL, '0', '70616000', 'XXXXX'),
('Z12-FACT', 'Module Facture', 37, 'Module Facture de ZUNO', NULL, '0', 0, 0, '1', 1, NULL, NULL, NULL, '0', '70616000', 'XXXXX'),
('Z21-FAX', 'Fax via ZUNO', 40, 'Faximile envoyé depuis l''interface ZUNO', NULL, '0.59', 0, 0, '0', 1, NULL, NULL, NULL, '0', '70616000', 'XXXXX'),
('Z13-ADM', 'Administrateur ZUNO', 38, 'Compte utilisateur ZUNO inclus dans l''abonnement', NULL, '0', 0, 0, '1', 1, NULL, NULL, NULL, '0', '70616000', 'XXXXX'),
('Z12-GED', 'Module GED', 37, 'Module Gestion des Documents de ZUNO', NULL, '0.50', 0, 0, '1', 1, NULL, NULL, NULL, '0', '70616000', 'XXXXX'),
('Z11-MOB', 'Zuno Mobile',36, 'Accès à ZUNO via la plateforme mobile', NULL, '1.99', 0, 0, '0', 1, NULL, NULL, NULL, '0', '70616000', 'XXXXX'),
('Z12-PREF', 'Module Préférences', 37, 'Module Préférences de ZUNO', NULL, '1.5', 0, 0, '1', 1, NULL, NULL, NULL, '0', '70616000', 'XXXXX'),
('Z12-PROD', 'Module Produit', 37, 'Module Produit de ZUNO', NULL, '0', 0, 0, '1', 1, NULL, NULL, NULL, '0', '70616000', 'XXXXX'),
('Z12-SEARCH', 'Module Recherche', 37, 'Module de Recherche de ZUNO', NULL, '0', 0, 0, '1', 1, NULL, NULL, NULL, '0', '70616000', 'XXXXX'),
('Z12-STATS', 'Module Statistiques', 37, 'Module des statistiques de ZUNO', NULL, '1.50', 0, 0, '1', 1, NULL, NULL, NULL, '0', '70616000', 'XXXXX'),
('Z13-USER', 'Utilisateur ZUNO', 38, 'Utilisateur de ZUNO', NULL, '4.90', 0, 0, '1', 1, NULL, NULL, NULL, '0', '70616000', 'XXXXX');");
$bddNew->process();
$bddNew->makeRequeteFree("UPDATE `produit` SET `refExterne` =  id_prod WHERE refExterne = 'XXXXX';");
$bddNew->process();

$bddNew->makeRequeteFree("SELECT id_aff, titre_aff, titre_dev
FROM `affaire` , devis
WHERE `titre_aff` LIKE '-- sujet non defini --'
AND affaire_dev = id_aff
AND titre_dev IS NOT NULL
AND `titre_dev` NOT LIKE '-- sujet non defini --'");
$list = $bddNew->process();
if(count($list) > 0) {
    foreach($list as $k => $v) {
	$bddNew->makeRequeteUpdate('affaire', 'id_aff', $v['id_aff'], array('titre_aff' => $v['titre_dev']));
	$bddNew->process2();
    }
}


echo "================== FIN DE LA MIGRATION ====================$end";
$generate_time_end = microtime(true);
$time =$generate_time_end-$GLOBALS['generate_time_start'];
if($time > 60)
    $time = round(($time/60),4).' minutes';
else $time = round($time,4).' secondes';
echo "+========================================================================+$end";
echo "| GENERATED IN ".$time."                                   |$end";
echo "+========================================================================+$end";
?>
