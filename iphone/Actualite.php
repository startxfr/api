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
include_once ('lib/HtmlForm.inc.php');
include_once ('lib/ZunoLayerActualite.inc.php');
include_once ('V/GeneralView.inc.php');
loadPlugin(array('ZModels/ActualiteModel'));
include_once ('V/ActualiteView.inc.php');
include_once ('V/ContactView.inc.php');
include_once ('V/AffaireView.inc.php');
loadPlugin(array('ZControl/ActualiteControl'));

// On lance la bufferisation de sortie et les entetes qui vont bien
ob_start();
header("Content-type: text/xml");
echo '<?xml version="1.0" encoding="utf-8" ?>';

// On recupère les informations sur le contexte de la page
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
aiJeLeDroit('actualite',5);
if(verifDroits('actualite',10)) {
    $plus = '';
}
else {
    $plus = " AND user = '".$_SESSION['user']['id']."' ";
}


$limitList = $_SESSION['user']['config']['LenghtSearchActualite'];


if(trim($PC->rcvG['action']) == 'view' and $PC->rcvG['id'] != '') {
    viewFiche($PC->rcvG['id'], 'actualite');
}
elseif(trim($PC->rcvG['action']) == 'continue' and $PC->rcvG['from'] != '') {
    $info = new ActualiteModel();
    $zoneTo = $outJs = $out = '';
    $from = ($PC->rcvG['from'] != '') ? $PC->rcvG['from'] : 0;
    $act = $info->getData($_SESSION['actualite'],$from,$limitList, $plus);
    if($act[0]) {
	$out .= actualiteView::actualiteResultRow($act[1]);
	if(count($act[1]) >= $limitList)
	    $out .= '<li class="iMore" id="actualiteResultMore'.$from.'"><a href="Actualite.php?action=continue&from='.($from+$limitList).'" rev="async">Plus de résultats</a></li>';
	$outJs = 'removeElementFromDom(\'actualiteResultMore'.($from-$limitList).'\')';
	$zoneTo = 'actualiteResultUl';
    }

    if($zoneTo != '') {
	if(aiJeLeDroit('actualite', 5)) {
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
}
elseif(trim($PC->rcvG['tri']) != '') {
    $_SESSION['actualite'] = $PC->rcvG['tri'];
    $info = new ActualiteModel();
    $act = $info->getData($_SESSION['actualite'],0,$limitList, $plus);
    if($act[0]) {
	$out .= '<ul class="iArrow" id="actualiteResultUl">';
	$out .= actualiteView::actualiteResultRow($act[1]);
	if(count($act[1]) >= $limitList)
	    $out .= '<li class="iMore" id="actualiteResultMore0"><a href="Actualite.php?action=continue&from='.$limitList.'" rev="async">Plus de résultats</a></li>';
	$out .= '</ul>';
    }
    else	$out .= '<h2 class="Actualite">Aucune actualités</h2>';
    ?>
<root>
    <part>
	<title set="waActualiteResult">Actualités <?php echo $_SESSION['actualite']; ?></title>
	<destination mode="replace" zone="ActualiteResultAsync"/>
	<data><![CDATA[
	    <div class="iList">
    <?php echo $out; ?>
	    </div>
			]]></data>
    </part>
</root>
		    <?php
}
	    elseif(trim($PC->rcvG['action']) == 'viewAffaire' and $PC->rcvG['id_aff'] != '') {
    viewRessourcesLies($PC->rcvG['id_aff'], 'actualite', 'affaire', 'iphone', true);
}
elseif(trim($PC->rcvG['action']) == 'viewDevis' and $PC->rcvG['id_dev'] != '') {
    viewRessourcesLies($PC->rcvG['id_dev'], 'actualite', 'devis', 'iphone', true);
}
elseif(trim($PC->rcvG['action']) == 'viewCommande' and $PC->rcvG['id_cmd'] != '') {
    viewRessourcesLies($PC->rcvG['id_cmd'], 'actualite', 'commande', 'iphone', true);
}
elseif(trim($PC->rcvG['action']) == 'viewFacture' and $PC->rcvG['id_fact'] != '') {
    viewRessourcesLies($PC->rcvG['id_fact'], 'actualite', 'facture', 'iphone', true);
}
elseif($PC->rcvG['action'] == 'add') {
    viewFormulaire('', 'actualite', 'add', 'iphone', true, '');
}
elseif($PC->rcvG['action'] == 'doAdd') {
    aiJeLeDroit('actualite', 20);
    $data = stripslashs($PC->rcvP);
    $control = new actualiteControl();
    $res = $control->generalControl($data);
    if($res[0]) {
	$req = new actualiteModel();
	$result = $req->insert($data);
	if($result[0]) {
	    $id = $req->getLastID();
	    viewFiche($id, 'actualite');
	}
    }
    else {
	?><root><go to="waActualiteAdd"/>
    <title set="waActualiteAdd"><?php echo 'Nouvelle actualité'; ?></title>
    <part><destination mode="replace" zone="waActualiteAdd" create="true"/>
	<data><![CDATA[ <?php echo actualiteView::add($PC->rcvP, $res[2], $res[1]); ?> ]]></data>
    </part>
</root><?php
    }
}
elseif($PC->rcvG['action'] == 'modif') {
    viewFormulaire($PC->rcvG['id'], 'actualite', 'modif', 'iphone', true, '');
}
elseif($PC->rcvG['action'] == 'doModif') {
    $control = new actualiteControl();
    $data = stripslashs($PC->rcvP);
    $res = $control->generalControl($data);
    if($res[0]) {
	$req = new actualiteModel();
	$result = $req->update($PC->rcvP['id'], $data);
	if($result[0]) {
	    viewFiche($PC->rcvP['id'], 'actualite', 'afterModif');
	}
    }
    else {
	?><root><go to="waActualiteModif"/>
    <title set="waActualiteModif"><?php echo ucfirst($result[1][0]['type']); ?></title>
    <part><destination mode="replace" zone="waActualiteModif" create="true"/>
	<data><![CDATA[ <?php echo actualiteView::modif($PC->rcvP, $res[2], $res[1]); ?> ]]></data>
    </part>
</root><?php
    }
}
else {
    $_SESSION['actualite'] = '';
    $info = new ActualiteModel();
    $act = $info->getData('',0,$limitList, $plus);

    if($act[0]) {
	$out .= '<ul class="iArrow" id="actualiteResultUl">';
	$out .= actualiteView::actualiteResultRow($act[1]);
	if(count($act[1]) >= $limitList)
	    $out .= '<li class="iMore" id="actualiteResultMore0"><a href="Actualite.php?action=continue&from='.$limitList.'" rev="async">Plus de résultats</a></li>';
	$out .= '</ul>';
    }
    else	$out .= '<h2 class="Actualite">Aucune actualités</h2>';
    ?>
<root>
    <go to="waActualiteResult"/>
    <title set="waActualiteResult">Actualités</title>
    <part>
	<destination mode="replace" zone="waActualiteResult" create="true"/>
	<data><![CDATA[
    <?php echo ZunoLayerActualite::actualiteForm(); ?>
			]]></data>
    </part>
    <part>
	<destination mode="replace" zone="ActualiteResultAsync"/>
	<data><![CDATA[
	    <div class="iList">
    <?php echo $out; ?>
	    </div>
			]]></data>
    </part>
</root>
    <?php
}

ob_end_flush();
?>
