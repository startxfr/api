<?php
// On inclus les librairies et les fichiers de configurations
include ('../inc/conf.inc');
include ('../inc/core.inc');

// On inclus les librairies speciales iPhone
include_once ('lib/Debug.inc.php');
include_once ('lib/HtmlElement.inc.php');
include_once ('lib/HtmlForm.inc.php');
include_once ('lib/ZunoLayerAffaire.inc.php');
loadPlugin(array('ZModels/AffaireModel'));
loadPlugin(array('ZModels/DevisModel'));
loadPlugin(array('ZModels/ActualiteModel'));
loadPlugin(array('ZModels/ContactModel'));
include_once ('V/AffaireView.inc.php');
include_once ('V/ContactView.inc.php');
include_once ('V/DevisView.inc.php');
include_once ('V/GeneralView.inc.php');
loadPlugin(array('ZControl/AffaireControl'));
loadPlugin(array('ZControl/GeneralControl'));



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
aiJeLeDroit('affaire',5);
if($PC->rcvG['action'] == 'searchAffaire' ) {
    viewResults(stripslashs($PC->rcvP['query']), 'affaire', 'reset', 'iphone', true);
}
elseif($PC->rcvG['action'] == 'searchAffaireContinue') {
    viewResults('', 'affaire', 'suite', 'iphone', true);
}
elseif($PC->rcvG['action'] == 'view') {
    viewFiche($PC->rcvG['id_aff'], 'affaire');
}
elseif($PC->rcvG['action'] == 'modifAffaire') {
    viewFormulaire($PC->rcvG['id_aff'], 'affaire', 'modif', 'iphone', true, '');
}
elseif($PC->rcvG['action'] == 'doModifAffaire') {

    // On verifie alors les données fournies
    $control = affaireControl::affaire($PC->rcvP);

    if($control[0]) // si elles sont bonnes, on lance le modèle pour modification
    {
	$jour = substr($PC->rcvP['echeance_aff'],0,2);
	$mois = substr($PC->rcvP['echeance_aff'], 3, 2);
	$année = substr($PC->rcvP['echeance_aff'], 6, 4);
	$heure = substr($PC->rcvP['echeance_aff'], 14, 2);
	$minute = substr($PC->rcvP['echeance_aff'], 17, 2);
	$PC->rcvP['echeance_aff'] = $année.'-'.$mois.'-'.$jour.' '.$heure.':'.$minute;
	$PC->rcvP['actif_aff'] = (array_key_exists('actif_aff', $PC->rcvP)) ? 1 : 0;

	$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$sqlConn->makeRequeteFree('select entreprise_cont from contact where id_cont = '.$PC->rcvP['contact_aff'].';');
	$temp = $sqlConn->process2();
	$entreprise_aff=$temp[1][0]['entreprise_cont'];
	$model  = new affaireModel();
	$result = $model->update(stripslashs($PC->rcvP),$PC->rcvG['id_aff'], $entreprise_aff);
	if($result[0]) {
	    viewFiche($PC->rcvG['id_aff'], 'affaire', 'afterModif');
	}
    }
    else {
	?>
<root><go to="waAffaireModif"/>
    <title set="waAffaireModif"><?php echo $result[1][0]['id_aff'];?></title>
    <part><destination mode="replace" zone="waAffaireModif" create="true"/>
        <data><![CDATA[ <?php echo affaireView::Modif($PC->rcvP,$control[2],$control[1],$PC->rcvG['id_aff']); ?> ]]></data>
    </part><script><![CDATA[ _KK(); ]]></script>
</root>
	<?php
    }
}

elseif($PC->rcvG['action'] == 'archiver') {
    $info = new affaireModel();
    $result = $info->getDataFromID($PC->rcvG['id_aff']);
    if($result[1][0]['commercial_aff'] != $_SESSION['user']['id']) {
	aiJeLeDroit('affaire', 37);
    }
    else {
	aiJeLeDroit('affaire', 27);
    }
    if($result[0]) { ?>
<root><go to="waAffaireAction"/>
    <title set="waAffaireAction">Archivage</title>
    <part><destination mode="replace" zone="waAffaireAction" create="true"/>
        <data><![CDATA[ <?php echo affaireView::archiver($result[1][0]); ?> ]]></data>
    </part>
</root>
	<?php }
    else { ?>
<root><go to="waAffaireModif"/>
    <part><destination mode="replace" zone="waAffaireModif" create="true"/>
        <data><![CDATA[ <div class="iBlock"><div class="err">Cette affaire n'<strong>existe plus</strong><br/></div></div> ]]></data>
    </part>
</root>
	<?php
    }
}
elseif($PC->rcvG['action'] == 'doArchivage') {
    loadPlugin(array('ZView/AffaireView'));
    affaireModel::archivateAffaireInDB($PC->rcvG['id_aff']);
    $devis = new devisModel();
    $info = $devis->getDataFromAffaire($PC->rcvG['id_aff']);
    foreach($info[1] as $v) {
	$v['status_dev'] = 7;
	$result = $devis->update($v, $v['id_dev']);
    }

    ?>
<root><go to="waAffaireSearchResult"/><part></part></root>
    <?php
}
elseif($PC->rcvG['action'] == 'suppAffaire') {
    viewFormulaire($PC->rcvG['id_aff'], 'affaire', 'supp', 'iphone', true, '');
}
elseif($PC->rcvG['action'] == 'marqueSuppAffaire') {
    viewFormulaire($PC->rcvG['id_aff'], 'affaire', 'supp', 'iphone', true, 'marqueSupp');
}
elseif($PC->rcvG['action'] == 'doMarqueSuppAffaire') {
    $data['status_aff'] = '17';
    $model = new affaireModel();
    $result = $model->update($data,$PC->rcvG['id_aff'], $PC->rcvG['entreprise']);
    if($result[0]) {
	$result = $model->getDataFromID($PC->rcvG['id_aff']);
	if($result[0]) { ?>
<root><go to="waAffaireFiche"/>
    <title set="waAffaireFiche"><?php echo $result[1][0]['id_aff']." Marquée supprimée."; ?></title>
    <part><destination mode="replace" zone="waAffaireFiche" create="true"/>
        <data><![CDATA[ <?php echo affaireView::view($result[1][0]); ?> ]]></data>
    </part>
</root>
	    <?php }
	else { ?>
<root><go to="waAffaireFiche"/>
    <part><destination mode="replace" zone="waAffaireFiche" create="true"/>
        <data><![CDATA[ <div class="iBlock"><div class="err">Cette affaire n'<strong>existe plus</strong><br/></div></div> ]]></data>
    </part>
</root>
	    <?php }
    }
}
elseif($PC->rcvG['action'] == 'doDeleteAffaire') {
    $info = new affaireModel();
    $affaire = $info->getDataFromID($PC->rcvG['id_aff']);
    $result = $info->delete($PC->rcvG['id_aff']);
    if($result[0]) {
	?>
<root><go to="waAffaireDelete"/>
    <title set="waAffaireDelete"><?php echo $result[1][0]['id_aff']; ?></title>
    <part><destination mode="replace" zone="waAffaireDelete" create="true"/>
        <data><![CDATA[ <?php echo affaireView::delete($result[1][0]); ?> ]]></data>
    </part>
</root>
	<?php }
    else { ?>
<root><go to="waAffaireFiche"/>
    <part><destination mode="replace" zone="waAffaireFiche" create="true"/>
        <data><![CDATA[ <div class="iBlock"><div class="err">Cette affaire n'<strong>existe plus</strong><br/></div></div> ]]></data>
    </part>
</root>
	<?php }
}
elseif($PC->rcvG['action'] == 'cloner') {
    viewFormulaire($PC->rcvG['id_aff'], 'affaire', 'cloner', 'iphone', true, '');
}
elseif($PC->rcvG['action'] == 'doCloner') {
    $info = new affaireModel();
    $result = $info->getDataFromID($PC->rcvG['id_aff']);
    loadPlugin(array('ZView/AffaireView'));
    $id = affaireModel::affaireGenerateID();
    $result=$result[1][0];
    $result['id_aff']=$id;
    $result['actif_aff']=1;
    $result['archived_aff']=0;
    $result['projet_aff']=NULL;
    $result['modif_aff']=date('Y-m-d');
    $result['detect_aff']=date('Y-m-d');
    $result['status_aff']=1;
    $result['echeance_aff']=NULL;
    $result['budget_aff']=NULL;
    $result['decid_aff']=NULL;
    $result['commercial_aff']=$_SESSION['user']['id'];
    $resultat = $info->insert($result, 'cloner', $PC->rcvG['id_aff']);

    viewFiche($PC->rcvG['id_aff'], 'affaire');
}

elseif($PC->rcvG['action'] == 'addAffaire') {
    viewFormulaire('', 'affaire', 'add', 'iphone', true);
}
elseif($PC->rcvG['action'] == 'doAddAffaire') {
    // On verifie alors les données fournies
    $control = affaireControl::affaire($PC->rcvP);
    if($control[0]) // si elles sont bonnes, on lance le model pour insertion
    {
	$jour = substr($PC->rcvP['echeance_aff'],0,2);
	$mois = substr($PC->rcvP['echeance_aff'], 3, 2);
	$année = substr($PC->rcvP['echeance_aff'], 6, 4);
	$heure = substr($PC->rcvP['echeance_aff'], 14, 2);
	$minute = substr($PC->rcvP['echeance_aff'], 17, 2);
	$PC->rcvP['actif_aff'] = 1;
	$PC->rcvP['archived_aff'] = 0;
	$PC->rcvP['status_aff'] = 1;
	$PC->rcvP['echeance_aff'] = $année.'-'.$mois.'-'.$jour.' '.$heure.':'.$minute;
	$PC->rcvP['actif_aff'] = (array_key_exists('actif_aff', $PC->rcvP)) ? 1 : 0;

	$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$sqlConn->makeRequeteFree('select entreprise_cont from contact where id_cont = '.$PC->rcvP['contact_aff'].';');
	$temp = $sqlConn->process2();
	$PC->rcvP['entreprise_aff']=$temp[1][0]['entreprise_cont'];
	$model  = new affaireModel();
	$resultinsert = $model->insert($PC->rcvP);
	viewFiche($resultinsert['id_aff'], 'affaire');

    }
    else {
	?>
<root><go to="waAffaireNew"/>
    <title set="waAffaireNew"><?php echo Affaire; ?></title>
    <part><destination mode="replace" zone="waAffaireNew" create="true"/>
        <data><![CDATA[ <?php echo affaireView::add($PC->rcvP, $control[2], $control[1]); ?> ]]></data>
    </part><script><![CDATA[ _KK(); ]]></script>
</root>
	<?php
    }

}
elseif($PC->rcvG['action'] == 'tri_echeance') {
    viewTri('affaire', 'echeance', 'reset', 0, 0, 'iphone', true);
}
elseif($PC->rcvG['action'] == 'triEcheanceMore') {
    viewTri('affaire', 'echeance', 'suite', $PC->rcvG['from'], $PC->rcvG['total'], 'iphone', true);
}
elseif($PC->rcvG['action'] == 'tri_creation') {
    viewTri('affaire', 'creation', 'reset', 0, 0, 'iphone', true);
}
elseif($PC->rcvG['action'] == 'triCreationMore') {
    viewTri('affaire', 'creation', 'suite', $PC->rcvG['from'], $PC->rcvG['total'], 'iphone', true);
}
elseif($PC->rcvG['action'] == 'tri_entreprise') {
    viewTri('affaire', 'entreprise', 'reset', 0, 0, 'iphone', true);
}
elseif($PC->rcvG['action'] == 'triEntrepriseMore') {
    viewTri('affaire', 'entreprise', 'suite', $PC->rcvG['from'], $PC->rcvG['total'], 'iphone', true);
}

elseif($PC->rcvG['action'] == 'tri_contact') {
    viewTri('affaire', 'contact', 'reset', 0, 0, 'iphone', true);
}

elseif($PC->rcvG['action'] == 'triContactMore') {
    viewTri('affaire', 'contact', 'suite', $PC->rcvG['from'], $PC->rcvG['total'], 'iphone', true);
}
elseif($PC->rcvG['action'] == 'rechercheavancee') {
    aiJeLeDroit('affaire', 5);
    ?>
<root><go to="waAffaireFormAvance" />
    <part>
        <title set="waAffaireFormAvance">Recherche avancée affaires</title>
        <destination mode="replace" zone="waAffaireFormAvance" create="true" />
        <data><![CDATA[<?php echo affaireView::form_avance(); ?>]]></data>
    </part>
</root>
    <?php
}
elseif($PC->rcvG['action'] == 'doRechercheAvancee') {
    if(verifDroits('affaire',10)) {
	$plus = '';
    }
    else {
	$plus = "AND commercial_aff = '".$_SESSION['user']['id']."' ";
    }
    $info = new affaireModel();
    $result = $info->getDataForRA(stripslashs($PC->rcvP), $plus);
    $_SESSION['user']['LastLetterSearch'] = '~#~|#';
    $_SESSION['user']['ordre'] = $PC->rcvP['affichage'];

    ?>
<root><go to="waAffaireResultAvance" />
    <part>
        <title set="waAffaireResultAvance">Recherche avancée affaires</title>
        <destination mode="replace" zone="waAffaireResultAvance" create="true" />
        <data><![CDATA[<?php echo affaireView::result_avance($result); ?>]]></data>
    </part>
</root>
    <?php
}
elseif($PC->rcvG['action'] == 'inputAffaire') {
    $_SESSION['searchAffaireLayerBackTo'] = $PC->rcvG['__source'];
    $_SESSION['searchAffaireTagsBackTo'] = $PC->rcvG['tag'];

    ?>
<root><go to="waAffaireInputAjax"/>
    <title set="waAffaireInputAjax">Choix d'une affaire</title>
    <part><destination mode="replace" zone="waAffaireInputAjax" create="true"/>
        <data><![CDATA[ <?php echo ZunoLayerAffaire::headerFormSearchAff(); ?> ]]></data>
    </part>
    <script><![CDATA[ new dynAjax('formSearchAffaireInput',3,'formSearchAffaireajax'); ]]></script>
</root>
    <?php
}

elseif($PC->rcvG['action'] == 'inputAffaireResult') {
    $_SESSION['searchAffaireQuery'] = $PC->rcvP['search'];
    aiJeLeDroit('affaire',5);
    if(verifDroits('affaire',10)) {
	$plus = '';
    }
    else {
	$plus = "AND commercial_aff = '".$_SESSION['user']['id']."' ";
    }
    $info = new affaireModel();
    $from = 0;
    $limit = $_SESSION['user']['config']['LenghtSearchAffaire'];
    $affaire = $info->getDataForSearch(stripslashs($PC->rcvP['search']),$limit,$from, $plus);
    if($affaire[0]) {
	$out .= '<ul id="searchResultInputAffaireUl">';
	$out .= affaireView::searchInputResultRow($affaire[1],$_SESSION['searchAffaireLayerBackTo'],$_SESSION['searchAffaireTagsBackTo']);
	if(count($affaire[1]) >= $limit)
	    $out .= '<li class="iMore" id="searchResultInputAffaireMore'.$from.'"><a href="Affaire.php?action=inputAffaireContinue&from='.$limit.'" rev="async">Plus de résultats</a></li>';
	$out .= '</ul>';
    }
    else	$out .= '<h2 class="Contact">Affaire (0)</h2>';

    ?>
<root>
    <part>
        <destination mode="replace" zone="SearchAffaireResultAsync"/>
        <data><![CDATA[
            <div class="iList">
		    <?php echo $out; ?>
            </div>
			]]></data>
    </part>
</root>
    <?php
}
elseif($PC->rcvG['action'] == 'inputAffaireContinue') {
    if(verifDroits('affaire',10)) {
	$plus = '';
    }
    else {
	$plus = "AND commercial_aff = '".$_SESSION['user']['id']."' ";
    }
    $info = new affaireModel();
    $zoneTo = $outJs = $out = '';
    $limit = $_SESSION['user']['config']['LenghtSearchAffaire'];
    $from = ($PC->rcvG['from'] != '') ? $PC->rcvG['from'] : 0;
    $result = $info->getDataForSearch($_SESSION['searchAffaireQuery'],$limit,$from, $plus);
    if($result[0]) {
	$out .= affaireView::searchInputResultRow($result[1],$_SESSION['searchAffaireLayerBackTo'],$_SESSION['searchAffaireTagsBackTo']);
	if(count($result[1]) >= $limit)
	    $out .= '<li class="iMore" id="searchResultInputAffaireMore'.$from.'"><a href="Affaire.php?action=inputAffaireContinue&from='.($from+$limit).'" rev="async">Plus de résultats</a></li>';
	$outJs = 'removeElementFromDom(\'searchResultInputAffaireMore'.($from-$limit).'\')';
	$zoneTo = 'searchResultInputAffaireUl';
    }

    if($zoneTo != '') {	?>
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
elseif($PC->rcvG['action'] == 'statistiques') {
    aiJeLeDroit('affaire', 45);
    $result = getStats('affaire');
    placementAffichage('Statistiques', "waAffaireStats", 'affaireView::afficherStats', array($result));
}
ob_end_flush();
?>
