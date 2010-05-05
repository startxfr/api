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
$sqlCleanOld = "UPDATE commande SET contact_cmd =  '134814' WHERE id_cmd = '090608-01BC';";
$bddOld->makeRequeteFree($sqlCleanOld);
$bddOld->process();
$bddNew->makeRequeteFree("CREATE TABLE IF NOT EXISTS `redhat_archi` (
  `id_arch` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `nom_arch` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_arch`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Type d''architecture materiel pour les produits REDHAT';");
$bddNew->process();
$bddNew->makeRequeteFree("CREATE TABLE IF NOT EXISTS `redhat_contrat` (
  `id_cont` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `nom_cont` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_cont`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Type de contrat proposé par REDHAT';");
$bddNew->process();
$bddNew->makeRequeteFree("INSERT IGNORE INTO ref_fonction (`id_fct`, `nom_fct`, `idsuperieur_fct`) VALUES ('99', 'Plus dans l''entreprise', NULL);");
$bddNew->process();


$end = "<br/>";
echo "================== DEBUT DE LA MIGRATION ==================$end";
$tablesAImporter = array('actualite','affaire','appel','commande','commande_produit','contact','devis','devis_produit','devis_renew',
			 'entreprise','facture','facture_produit','fournisseur','produit','produit_fournisseur','projet','redhat_archi',
			 'redhat_contrat','ref_prodfamille','session');
foreach($tablesAImporter as $table) {
    sleep(3);
    echo "================== table $table $end";
    $bddOld->makeRequeteFree('SELECT * FROM '.$table);
    $list = $bddOld->process();
    if(count($list) > 0) {
	$count = 0;
	$bddNew = new Bdd(1);
	$bddNew->makeRequeteFree("TRUNCATE TABLE $table;");
	$bddNew->process();
	foreach($list as $k => $v) {
	    if($table == 'actualite') {
		$v['isPublic'] = 0;
		$v['isPublieForClient'] = 0;
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
	    elseif($table == 'projet') {
		if(in_array($v['typeproj_proj'],array('16','17','18'))) $v['typeproj_proj'] = '2';
		elseif(in_array($v['typeproj_proj'],array('4','5','9','15','21'))) $v['typeproj_proj'] = '4';
		elseif(in_array($v['typeproj_proj'],array('7','10','11'))) $v['typeproj_proj'] = '5';
		elseif(in_array($v['typeproj_proj'],array('2','3','6','6','12','13','14','19','20'))) $v['typeproj_proj'] = '3';
		else $v['typeproj_proj'] = '1';
	    }
	    elseif($table == 'entreprise') {
		if($v['type_ent'] == '5') $v['type_ent'] = '4';
		elseif($v['type_ent'] == '4') $v['type_ent'] = '3';
		elseif($v['type_ent'] == '3') $v['type_ent'] = '2';


		if($v['visite_ent'] == '1') $v['commsociete_ent'] = 'Client déjà rencontré. '.$v['commsociete_ent'];
		unset($v['visite_ent'],$v['FAI_ent'],$v['hebergeur_ent'],$v['SSLL_ent'],$v['SSII_ent'],$v['infogereur_ent'],$v['commprestataire_ent'],$v['firewall_ent'],$v['IDS_ent'],$v['proxy_ent'],$v['DNS_ent'],$v['FTP_ent'],$v['VPN_ent'],$v['supervisionreseau_ent'],$v['postes_ent'],$v['sites_ent'],$v['windows_ent'],$v['mac_ent'],$v['station_ent'],$v['cluster_ent'],$v['messagerie_ent'],$v['web_ent'],$v['fichier_ent'],$v['routeur_ent'],$v['antivirus_ent'],$v['authentification_ent'],$v['audit_ent']);
	    }
	    elseif($table == 'facture_produit') {
		if($v['id_produit'] == '') $v['id_produit'] = 'SANSREF';
	    }
	    elseif($table == 'fournisseur') {
		$v['actif'] = '1';
	    }
	    elseif($table == 'produit') {
		$v['stock_prod'] = '0';
		$v['dureeRenouvellement_prod'] = $v['duree_prod'];
		if($v['description_prod'] == '') $v['description_prod'] = $v['nom_prod'];
		unset($v['keyredhat_prod'],$v['public_prod'],$v['media_prod'],$v['duree_prod']);
	    }
	    elseif($table == 'produit_fournisseur') {
		$v['actif'] = '1';
	    }
	    elseif($table == 'projet') {
		if($v['titre_proj'] == '') $v['titre_proj'] = '???';
	    }
	    elseif($table == 'ref_fonction') {
	    }
	    $q = $bddNew->makeRequeteInsert($table,$v);
	    $r = $bddNew->process2();
	    if($r[0]) $count++;
	    else { echo "ERREUR sur la requête: $q $end";
		   echo '===> '.mysql_error().$end; }
	}
	echo "================== table $table = $count enregistrements $end";

    }
}












echo "================== FIN DE LA MIGRATION ====================$end";
$generate_time_end = microtime(true);
$time =$generate_time_end-$GLOBALS['generate_time_start'];
echo "+========================================================================+$end";
echo "| GENERATED IN $time seconds                                   |$end";
echo "+========================================================================+$end";
?>
