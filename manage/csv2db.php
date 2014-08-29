<?php
/*#########################################################################
#
#   name :       Application.php
#   desc :       Authentification interface
#   categorie :  management page
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| FRAMEWORK LOADING	& LOG ACTIVITY
+------------------------------------------------------------------------*/
include ('../inc/conf.inc');		// Declare global variables from config files
include ('../inc/core.inc');		// Load core library
loadPlugin(array('Aide'));
/*------------------------------------------------------------------------+
| INITIALIZE PAGE CONTEXT
+------------------------------------------------------------------------*/

// Whe get the page context
$PC = new PageContext('admin');
$PC->GetFullContext();
// Whe initialize page display
$out = new PageDisplay($PC->channel);
$out->ConfigureWithPageData($PC->Data,$PC->cacheXML);
ini_set("memory_limit","20M");
/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/
$separatorList = array(";" => ";","," => ",","|" => "|");
$AssociationList = array("simple" => "Association Simple",
	"md5" => "Cryptage MD5",
	"convert1" => "Convertion Civ",
	"convert2" => "Convertion Bool (x, )",
	"convert3" => "Convertion 3",
	"convert4" => "Convertion 4",
	"fixed1" => "Fixé 1",
	"fixed2" => "Fixé 2");
$ConvertionList = array( "convert1" => array("Monsieur" => "M.","monsieur" => "M.","Mr" => "M.","M" => "M.","M." => "M.","Madame" => "Mme","madame" => "Mme","MME" => "Mme","Mme" => "Mme","Madmoiselle" => "Mlle","madmoiselle" => "Mlle","Mlle" => "Mlle"),
	"convert2" => array("x" => "1"," " => "0"),
	"convert3" => array("x" => "4"," " => "5"),
	"convert4" => array("x" => "1"," " => "0"));
$ConvertionListDefaut = array( "convert1" => "M.",
	"convert2" => "1",
	"convert3" => "5",
	"convert4" => "1");
$FixedList	 = array( "fixed1" => "2",
	"fixed2" => "fr");

if ($PC->rcvP['bouton'] == 'Recommencer') {
    unset($_SESSION['tmpCSVImport']);
    $PC->rcvP['action'] = '';
}



if ($PC->rcvP['action'] == 'etape1') {
    if(!is_array($PC->rcvF['fichier'])) {
	$mess_err1 .= "Merci de choisir un fichier";
    }
    else {
	$_SESSION['tmpCSVImport']['file']['sep'] = $PC->rcvP['sep'];
	$_SESSION['tmpCSVImport']['file']['path'] = $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'].$PC->rcvF['fichier']['name'];
	$row = 0;
	$handle = fopen($_SESSION['tmpCSVImport']['file']['path'], "r");
	$data = fgetcsv($handle, 1000,$_SESSION['tmpCSVImport']['file']['sep']);
	$_SESSION['tmpCSVImport']['file']['row'] = $data;
	fclose($handle);
    }

    if($mess_err1 == "") {
	$_SESSION['tmpCSVImport']['etape'] = 1;
	for($i = 1; $i < 10; $i++) {
	    if($GLOBALS['DBPool_'.$i]['type'] == 'mysql') {
		$base[$i] = $GLOBALS['DBPool_'.$i]['base'];
	    }
	}
	$tmp['message'] = "";
	$tmp['base'] = selectTag("base",$base,$PC->rcvP['base'],'','',FALSE);
	$content = templating('manage/CSVImport.etape2',$tmp);
    }
    else {
	$tmp['message'] = $mess_err1;
	$tmp['separator'] = selectTag("sep",$separatorList,$PC->rcvP['sep'],'','',FALSE);
	$content = templating('manage/CSVImport.etape1',$tmp);
    }
}
elseif ($PC->rcvP['action'] == 'etape2') {
    $_SESSION['tmpCSVImport']['base']['pool'] = $PC->rcvP['base'];
    $_SESSION['tmpCSVImport']['etape'] = 2;


    $bddtmp = new Bdd($_SESSION['tmpCSVImport']['base']['pool']);
    $ListeTable = $bddtmp->AnalyseDatabaseStructure();
    foreach($ListeTable as $value) {
	$table[$value] = $value;
    }

    $tmp['message'] = "";
    $tmp['table'] = selectTag("table",$table,$PC->rcvP['table'],'','',FALSE);
    $content = templating('manage/CSVImport.etape3',$tmp);
}
elseif ($PC->rcvP['action'] == 'etape3') {
    $_SESSION['tmpCSVImport']['etape'] = 3;
    $_SESSION['tmpCSVImport']['base']['table'] = $PC->rcvP['table'];

    $bddtmp = new Bdd($_SESSION['tmpCSVImport']['base']['pool']);
    $ListeRow = $bddtmp->AnalyseTableStructure($_SESSION['tmpCSVImport']['base']['table']);

    $_SESSION['tmpCSVImport']['base']['row'] = $ListeRow[1];

    $nbrCol = count($_SESSION['tmpCSVImport']['base']['row'])+2;
    $nbrRow = count($_SESSION['tmpCSVImport']['file']['row'])+2;
    $outP = "<table width='100%'>\n<tr><th colspan='".$nbrCol."'>Champs de la table</th></tr>
		<tr><th rowspan='".$nbrRow."'>Champs<br /><br />du<br /><br />fichier</th><th></th>\n";
    foreach($_SESSION['tmpCSVImport']['base']['row'] as $Bkey => $Bvalue) {
	$outP .= "<th>".$Bkey."</th>\n";
    }
    $outP .= "</tr>\n";
    foreach($_SESSION['tmpCSVImport']['file']['row'] as $Rkey => $Rvalue) {
	$outP .= "<tr>\n";
	$outP .= "<th>".$Rvalue."</th>";
	foreach($_SESSION['tmpCSVImport']['base']['row'] as $Bkey => $Bvalue) {
	    if($Rvalue == $Bvalue['nom']) {
		$PC->rcvP["Assoc-".$Rkey.'-'.$Bkey] = 'simple';
	    }
	    $outP .= "<td>".selectTag("Assoc-".$Rkey.'-'.$Bkey,$AssociationList,$PC->rcvP["Assoc-".$Rkey.'-'.$Bkey],""," style='width:auto;'")."</td>\n";
	}
	$outP .= "</tr>\n";
    }
    $outP .= "</table>";
    $tmp['message'] = "";
    $tmp['table'] = $outP;
    $content = templating('manage/CSVImport.etape4',$tmp);
}
elseif ($PC->rcvP['action'] == 'etape4') {
    foreach($PC->rcvP as $key => $value) {
	$tmpConv = explode("-",$key);
	if($value != "" and $tmpConv[0] == "Assoc") {
	    $convertTypeMatrix[$tmpConv[1]][] = $value;
	    $convertFieldMatrix[$tmpConv[1]][] = $tmpConv[2];
	}
    }
    $rowNbr = 0;
    $handle = fopen($_SESSION['tmpCSVImport']['file']['path'], "r");
    while (($ligne = fgetcsv($handle, 1000,$_SESSION['tmpCSVImport']['file']['sep'])) !== FALSE) {
	if($rowNbr != 0) {
	    foreach($ligne as $Rowkey => $RowValue) {
		if(array_key_exists($Rowkey, $convertTypeMatrix)) {
		    foreach($convertTypeMatrix[$Rowkey] as $subConvertKey => $convertType) {
			$fieldTo = $convertFieldMatrix[$Rowkey][$subConvertKey];
			if($RowValue == 'NULL' or $RowValue == '') {
			    $Export[$rowNbr][$fieldTo] = '';
			}
			elseif($convertType == 'simple') {
			    $Export[$rowNbr][$fieldTo] = stripslashs(mysql_escape_string(stripslashs($RowValue)));
			}
			elseif($convertType == 'md5') {
			    $Export[$rowNbr][$fieldTo] = md5($RowValue);
			}
			elseif(is_array($ConvertionList[$convertType])) {
			    if(array_key_exists($RowValue, $ConvertionList[$convertType])) {
				$Export[$rowNbr][$fieldTo] = stripslashs(mysql_escape_string(stripslashs($ConvertionList[$convertType][$RowValue])));
			    }
			    else {
				$Export[$rowNbr][$fieldTo] = stripslashs(mysql_escape_string(stripslashs($ConvertionListDefaut[$convertType])));
			    }
			}
			else {
			    $Export[$rowNbr][$fieldTo] = stripslashs(mysql_escape_string(stripslashs($FixedList[$convertType])));
			}
		    }
		}
	    }
	}
	$rowNbr++;
    }
    fclose($handle);
    $dbConnexion = new Bdd($_SESSION['tmpCSVImport']['base']['pool']);
    $nbrInsert = 0;
    foreach($Export as $newRow) {
	$dbConnexion->makeRequeteInsert($_SESSION['tmpCSVImport']['base']['table'],$newRow)."<br/>";
	$dbConnexion->process();
	$nbrInsert++;
    }
    $tmp['message'] = "Vous venez d'ajouter ".$nbrInsert." lignes dans la table ".$_SESSION['tmpCSVImport']['base']['table'];
    $content = templating('manage/CSVImport.etape5',$tmp);
}
else {
    unset($_SESSION['tmpCSVImport']);
    $tmp['message'] = "";
    $tmp['separator'] = selectTag("sep",$separatorList,$PC->rcvP['sep'],'','',FALSE);
    $content = templating('manage/CSVImport.etape1',$tmp);
}

/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($content);
$out->Process();
?>
