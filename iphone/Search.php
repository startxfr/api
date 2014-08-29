<?php
// On inclus les librairies et les fichiers de configurations
include ('../inc/conf.inc');
include ('../inc/core.inc');
$GLOBALS['LOG']['DisplayDebug'] =
$GLOBALS['LOG']['DisplayError'] = false;

// On inclus les librairies speciales iPhone
include_once ('lib/Debug.inc.php');
include_once ('lib/ZunoLayerGeneral.inc.php');
include_once ('lib/HtmlElement.inc.php');
include_once ('lib/ZunoLayerSearch.inc.php');
loadPlugin(array('ZModels/SearchModel'));
include_once ('V/SearchView.inc.php');
include_once ('V/GeneralView.inc.php');

// On lance la bufferisation de sortie et les entetes qui vont bien
ob_start();
header("Content-type: text/xml");
echo '<?xml version="1.0" encoding="utf-8" ?>';

// On récupère les informations sur le contexte de la page
// (channel, variables d'entrée, session)
$PC = new PageContext('iPhone');
$PC->GetVarContext();
$PC->GetChannelContext();
// Contrôle de la session et de sa validité
if($PC->GetSessionContext('',false) === false) {
    echo HtmlElementIphone::redirectOnSessionEnd();
    ob_end_flush();
    exit;
}
aiJeLeDroit('search', '05');
if(trim($PC->rcvG['action']) == 'continue' and $_SESSION['searchQuery'] != '') {
    $info = new SearchModel();
    $zoneTo = $outJs = $out = '';
    $limit = $_SESSION['user']['config']['LenghtSearchGeneral'];
    $from = ($PC->rcvG['from'] != '') ? $PC->rcvG['from'] : 0;
    if(trim($PC->rcvG['part']) == 'ent') {
	$result = $info->getDataFromEntreprise($_SESSION['searchQuery'],$from,$limit);
	if($result[0]) {
	    $out .= searchView::searchResultRowEntreprise($result[1]);
	    if(count($result[1]) >= $limit)
		$out .= '<li class="iMore" id="searchResultEntrepriseMore'.$from.'"><a href="Search.php?action='.$PC->rcvG['action'].'&part='.$PC->rcvG['part'].'&from='.($from+$limit).'" rev="async">Plus de résultats</a></li>';
	    $outJs = 'removeElementFromDom(\'searchResultEntrepriseMore'.($from-$limit).'\')';
	    $zoneTo = 'searchResultEntrepriseUl';
	}
    }
    elseif(trim($PC->rcvG['part']) == 'cont') {
	$result = $info->getDataFromContact($_SESSION['searchQuery'],$from,$limit);
	if($result[0]) {
	    $out .= searchView::searchResultRowContact($result[1]);
	    if(count($result[1]) >= $limit)
		$out .= '<li class="iMore" id="searchResultContactMore'.$from.'"><a href="Search.php?action='.$PC->rcvG['action'].'&part='.$PC->rcvG['part'].'&from='.($from+$limit).'" rev="async">Plus de résultats</a></li>';
	    $outJs = 'removeElementFromDom(\'searchResultContactMore'.($from-$limit).'\')';
	    $zoneTo = 'searchResultContactUl';
	}
    }
    elseif(trim($PC->rcvG['part']) == 'aff') {
	if(verifDroits('affaire',10)) {
	    $plus = '';
	}
	else {
	    $plus = " AND commercial_aff = '".$_SESSION['user']['id']."' ";
	}
	$result = $info->getDataFromAffaire($_SESSION['searchQuery'],$from,$limit, 'no', $plus);
	if($result[0]) {
	    $out .= searchView::searchResultRowAffaire($result[1]);
	    if(count($result[1]) >= $limit)
		$out .= '<li class="iMore" id="searchResultAffaireMore'.$from.'"><a href="Search.php?action='.$PC->rcvG['action'].'&part='.$PC->rcvG['part'].'&from='.($from+$limit).'" rev="async">Plus de résultats</a></li>';
	    $outJs = 'removeElementFromDom(\'searchResultAffaireMore'.($from-$limit).'\')';
	    $zoneTo = 'searchResultAffaireUl';
	}
    }
    elseif(trim($PC->rcvG['part']) == 'dev') {
	if(verifDroits('devis',10)) {
	    $plus = '';
	}
	else {
	    $plus = " AND commercial_dev = '".$_SESSION['user']['id']."' ";
	}
	$result = $info->getDataFromDevis($_SESSION['searchQuery'],$from,$limit, 'no', $plus);
	if($result[0]) {
	    $out .= searchView::searchResultRowDevis($result[1]);
	    if(count($result[1]) >= $limit)
		$out .= '<li class="iMore" id="searchResultDevisMore'.$from.'"><a href="Search.php?action='.$PC->rcvG['action'].'&part='.$PC->rcvG['part'].'&from='.($from+$limit).'" rev="async">Plus de résultats</a></li>';
	    $outJs = 'removeElementFromDom(\'searchResultDevisMore'.($from-$limit).'\')';
	    $zoneTo = 'searchResultDevisUl';
	}
    }
    elseif(trim($PC->rcvG['part']) == 'cmd') {
	if(verifDroits('commande',10)) {
	    $plus = '';
	}
	else {
	    $plus = " AND commercial_cmd = '".$_SESSION['user']['id']."' ";
	}
	$result = $info->getDataFromCommande($_SESSION['searchQuery'],$from,$limit, 'no', $plus);
	if($result[0]) {
	    $out .= searchView::searchResultRowCommande($result[1]);
	    if(count($result[1]) >= $limit)
		$out .= '<li class="iMore" id="searchResultCommandeMore'.$from.'"><a href="Search.php?action='.$PC->rcvG['action'].'&part='.$PC->rcvG['part'].'&from='.($from+$limit).'" rev="async">Plus de résultats</a></li>';
	    $outJs = 'removeElementFromDom(\'searchResultCommandeMore'.($from-$limit).'\')';
	    $zoneTo = 'searchResultCommandeUl';
	}
    }
    elseif(trim($PC->rcvG['part']) == 'fact') {
	if(verifDroits('facture',10)) {
	    $plus = '';
	}
	else {
	    $plus = " AND commercial_fact = '".$_SESSION['user']['id']."' ";
	}
	$result = $info->getDataFromFacture($_SESSION['searchQuery'],$from,$limit, 'no', $plus);
	if($result[0]) {
	    $out .= searchView::searchResultRowFacture($result[1]);
	    if(count($result[1]) >= $limit)
		$out .= '<li class="iMore" id="searchResultFactureMore'.$from.'"><a href="Search.php?action='.$PC->rcvG['action'].'&part='.$PC->rcvG['part'].'&from='.($from+$limit).'" rev="async">Plus de résultats</a></li>';
	    $outJs = 'removeElementFromDom(\'searchResultFactureMore'.($from-$limit).'\')';
	    $zoneTo = 'searchResultFactureUl';
	}
    }
    elseif(trim($PC->rcvG['part']) == 'prod') {
	$result = $info->getDataFromProduit($_SESSION['searchQuery'],$from,$limit);
	if($result[0]) {
	    $out .= searchView::searchResultRowProduit($result[1]);
	    if(count($result[1]) >= $limit)
		$out .= '<li class="iMore" id="searchResultProduitMore'.$from.'"><a href="Search.php?action='.$PC->rcvG['action'].'&part='.$PC->rcvG['part'].'&from='.($from+$limit).'" rev="async">Plus de résultats</a></li>';
	    $outJs = 'removeElementFromDom(\'searchResultProduitMore'.($from-$limit).'\')';
	    $zoneTo = 'searchResultProduitUl';
	}
    }

    if($zoneTo != '') {
	?>
<root>
    <part>
	<destination mode="append" zone="<?php echo $zoneTo; ?>"/>
	<data><![CDATA[ <?php echo $out; ?> ]]></data>
	<script><![CDATA[ <?php echo $outJs; ?> ]]></script>
    </part>
</root>
	<?php
    }
}
elseif(trim($PC->rcvP['search']) != '') {
    $_SESSION['searchQuery'] = $PC->rcvP['search'];
    $info = new SearchModel();
    $from = 0;
    $limit = $_SESSION['user']['config']['LenghtSearchGeneral'];
    $ent = $info->getDataFromEntreprise($PC->rcvP['search'],$from,$limit);
    $totalE = $info->getDataFromEntreprise($PC->rcvP['search'],$from,$limit,'yes');
    $totalE = $totalE[1][0]["counter"];
    if($ent[0]) {
	$out .= '<h2 class="Entreprise">Entreprise ('.$totalE.')</h2>';
	$out .= '<ul class="iArrow" id="searchResultEntrepriseUl">';
	$out .= searchView::searchResultRowEntreprise($ent[1]);
	if(count($ent[1]) >= $limit)
	    $out .= '<li class="iMore" id="searchResultEntrepriseMore'.$from.'"><a href="Search.php?action=continue&part=ent&from='.$limit.'" rev="async">Plus de résultats</a></li>';
	$out .= '</ul>';
    }
    else	$out .= '<h2 class="Entreprise">Entreprise (0)</h2>';
    $cont = $info->getDataFromContact($PC->rcvP['search'],$from,$limit);
    $totalC = $info->getDataFromContact($PC->rcvP['search'],$from,$limit, 'yes');
    $totalC = $totalC[1][0]["counter"];
    if($cont[0]) {
	$out .= '<h2 class="Contact">Contact ('.$totalC.')</h2>';
	$out .= '<ul class="iArrow" id="searchResultContactUl">';
	$out .= searchView::searchResultRowContact($cont[1]);
	if(count($cont[1]) >= $limit)
	    $out .= '<li class="iMore" id="searchResultContactMore'.$from.'"><a href="Search.php?action=continue&part=cont&from='.$limit.'" rev="async">Plus de résultats</a></li>';
	$out .= '</ul>';
    }
    else	$out .= '<h2 class="Contact">Contact (0)</h2>';

    if(verifDroits('affaire',10)) {
	$plus = '';
    }
    else {
	$plus = " AND commercial_aff = '".$_SESSION['user']['id']."' ";
    }
    $aff = $info->getDataFromAffaire($PC->rcvP['search'], $from, $limit, 'no', $plus);
    $totalA = $info->getDataFromAffaire($PC->rcvP['search'],$from,$limit, 'yes', $plus);
    $totalA = $totalA[1][0]["counter"];
    if($aff[0]) {
	$out .='<h2 class="Affaire">Affaire ('.$totalA.')</h2>';
	$out .= '<ul class="iArrow" id="searchResultAffaireUl">';
	$out .= searchView::searchResultRowAffaire($aff[1]);
	if(count($aff[1]) >= $limit)
	    $out .= '<li class="iMore" id="searchResultAffaireMore'.$from.'"><a href="Search.php?action=continue&part=aff&from='.$limit.'" rev="async">Plus de résultats</a></li>';
	$out .= '</ul>';

    }
    else $out .= '<h2 class="Affaire">Affaire (0)</h2>';

    if(verifDroits('devis',10)) {
	$plus = '';
    }
    else {
	$plus = " AND commercial_dev = '".$_SESSION['user']['id']."' ";
    }
    $dev = $info->getDataFromDevis($PC->rcvP['search'], $from, $limit, 'no', $plus);
    $totalD = $info->getDataFromDevis($PC->rcvP['search'],$from,$limit, 'yes', $plus);
    $totalD = $totalD[1][0]["counter"];
    if($dev[0]) {
	$out .='<h2 class="Devis">Devis ('.$totalD.')</h2>';
	$out .= '<ul class="iArrow" id="searchResultDevisUl">';
	$out .= searchView::searchResultRowDevis($dev[1]);
	if(count($dev[1]) >= $limit)
	    $out .= '<li class="iMore" id="searchResultDevisMore'.$from.'"><a href="Search.php?action=continue&part=dev&from='.$limit.'" rev="async">Plus de résultats</a></li>';
	$out .= '</ul>';

    }else $out .= '<h2 class="Devis">Devis (0)</h2>';

    if(verifDroits('commande',10)) {
	$plus = '';
    }
    else {
	$plus = " AND commercial_cmd = '".$_SESSION['user']['id']."' ";
    }
    $cmd = $info->getDataFromCommande($PC->rcvP['search'], $from, $limit, 'no', $plus);
    $totalC = $info->getDataFromCommande($PC->rcvP['search'],$from,$limit, 'yes', $plus);
    $totalC = $totalC[1][0]["counter"];
    if($cmd[0]) {
	$out .='<h2 class="Commande">Commande ('.$totalC.')</h2>';
	$out .= '<ul class="iArrow" id="searchResultCommandeUl">';
	$out .= searchView::searchResultRowCommande($cmd[1]);
	if(count($cmd[1]) >= $limit)
	    $out .= '<li class="iMore" id="searchResultCommandeMore'.$from.'"><a href="Search.php?action=continue&part=cmd&from='.$limit.'" rev="async">Plus de résultats</a></li>';
	$out .= '</ul>';

    }else $out .= '<h2 class="Commande">Commande (0)</h2>';

    if(verifDroits('facture',10)) {
	$plus = '';
    }
    else {
	$plus = " AND commercial_fact = '".$_SESSION['user']['id']."' ";
    }
    $fact = $info->getDataFromFacture($PC->rcvP['search'], $from, $limit, 'no', $plus);
    $totalF = $info->getDataFromFacture($PC->rcvP['search'],$from,$limit, 'yes', $plus);
    $totalF = $totalF[1][0]["counter"];
    if($fact[0]) {
	$out .='<h2 class="Facture">Facture ('.$totalF.')</h2>';
	$out .= '<ul class="iArrow" id="searchResultFactureUl">';
	$out .= searchView::searchResultRowFacture($fact[1]);
	if(count($fact[1]) >= $limit)
	    $out .= '<li class="iMore" id="searchResultFactureMore'.$from.'"><a href="Search.php?action=continue&part=fact&from='.$limit.'" rev="async">Plus de résultats</a></li>';
	$out .= '</ul>';

    }else $out .= '<h2 class="Facture">Facture (0)</h2>';

    $prod = $info->getDataFromProduit($PC->rcvP['search'], $from, $limit);
    $totalP = $info->getDataFromProduit($PC->rcvP['search'],$from,$limit, 'yes');
    $totalP = $totalP[1][0]["counter"];
		    if($prod[0]) {
	$out .='<h2 class="Produit">Produit ('.$totalP.')</h2>';
		    $out .= '<ul class="iArrow" id="searchResultProduitUl">';
	$out .= searchView::searchResultRowProduit($prod[1]);
	if(count($prod[1]) >= $limit)
	    $out .= '<li class="iMore" id="searchResultProduitMore'.$from.'"><a href="Search.php?action=continue&part=prod&from='.$limit.'" rev="async">Plus de résultats</a></li>';
	$out .= '</ul>';

    }else $out .= '<h2 class="Produit">Produit (0)</h2>';
    ?>
<root>
    <part>
	<destination mode="replace" zone="SearchResultAsync"/>
	<data><![CDATA[
	    <div class="iList">
		<?php echo $out; ?>
	    </div>
			]]></data>
    </part>
</root>
    <?php
}
else {
    ?>
<root>
    <go to="waSearchResult"/>
    <title set="waSearchResult">Recherche</title>
    <part>
	<destination mode="replace" zone="waSearchResult" create="true"/>
	<data><![CDATA[
    <?php echo ZunoLayerSearch::searchForm(); ?>
			]]></data>
	<script><![CDATA[
	    new dynAjax('formSearchInput',3,'formSearch');
	]]></script>
    </part>
</root>
    <?php
}

ob_end_flush();
?>