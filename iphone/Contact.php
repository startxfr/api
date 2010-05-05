<?php
// On inclus les librairies et les fichiers de configurations
include ('../inc/conf.inc');
include ('../inc/core.inc');

// On inclus les librairies speciales iPhone
include_once ('lib/Debug.inc.php');
include_once ('lib/HtmlElement.inc.php');
include_once ('lib/HtmlForm.inc.php');
include_once ('lib/ZunoLayerContact.inc.php');
include_once ('V/GeneralView.inc.php');
loadPlugin(array('ZControl/GeneralControl'));
loadPlugin(array('ZModels/ContactModel'));
include_once ('V/ContactView.inc.php');
include_once ('V/DevisView.inc.php');
include_once ('V/AffaireView.inc.php');
include_once ('V/CommandeView.inc.php');
include_once ('V/FactureView.inc.php');
loadPlugin(array('ZControl/ContactControl'));

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
if($PC->GetSessionContext('',false) === false)
{
	echo HtmlElementIphone::redirectOnSessionEnd();
	ob_end_flush(); exit;
}

aiJeLeDroit('contact', '05');
if($PC->rcvG['action'] == 'viewEnt')
{
	viewFiche($PC->rcvG['id_ent'], 'contactEntreprise');
}
elseif($PC->rcvG['action'] == 'searchEnt')
{
	viewResults($PC->rcvP['query'], 'contactEntreprise', 'reset', 'iphone', true);
}
elseif($PC->rcvG['action'] == 'searchEntContinue')
{
	viewResults('', 'contactEntreprise', 'suite', 'iphone', true);
}
elseif($PC->rcvG['action'] == 'modifEnt')
{
	viewFormulaire($PC->rcvG['id_ent'], 'contactEntreprise', 'modif', 'iphone', true, '');
}
elseif($PC->rcvG['action'] == 'addEnt')
{ 
	viewFormulaire('', 'contactEntreprise', 'add', 'iphone', true, '');
}
elseif($PC->rcvG['action'] == 'doAddEnt')
{
	insertBDD('contactEntreprise', $PC->rcvP, $PC->rcvG['__source'], 'iphone', true);
}
elseif($PC->rcvG['action'] == 'addContactLie')
{
	viewFormulaire($PC->rcvG['id_ent'], 'contactEntreprise', 'add', 'iphone', true, 'contactlie');
}
elseif($PC->rcvG['action'] == 'doModifEnt')
{
	updateBDD($PC->rcvG['id_ent'], 'contactEntreprise', $PC->rcvP, $PC->rcvG['__source'], 'iphone', true);
}
elseif($PC->rcvG['action'] == 'suppEnt')
{
	viewFormulaire($PC->rcvG['id_ent'], 'contactEntreprise', 'supp', 'iphone', true, '');
}
elseif($PC->rcvG['action'] == 'doSuppEnt')
{
	suppBDD($PC->rcvG['id_ent'], 'contactEntreprise', 'iphone', true);
}
elseif(trim($PC->rcvG['action']) == 'viewAffaireEnt' and $PC->rcvG['id'] != '')
{
	viewRessourcesLies($PC->rcvG['id'], 'contactEntreprise', 'affaire', 'iphone', true);
}
elseif(trim($PC->rcvG['action']) == 'viewDevisEnt' and $PC->rcvG['id'] != '')
{
	viewRessourcesLies($PC->rcvG['id'], 'contactEntreprise', 'devis', 'iphone', true);
}
elseif(trim($PC->rcvG['action']) == 'viewCommandeEnt' and $PC->rcvG['id'] != '')
{
	viewRessourcesLies($PC->rcvG['id'], 'contactEntreprise', 'commande', 'iphone', true);
}
elseif(trim($PC->rcvG['action']) == 'viewFactureEnt' and $PC->rcvG['id'] != '')
{
	viewRessourcesLies($PC->rcvG['id'], 'contactEntreprise', 'facture', 'iphone', true);
}
elseif(trim($PC->rcvG['action']) == 'viewProduitEnt' and $PC->rcvG['id'] != '')
{
	viewRessourcesLies($PC->rcvG['id'], 'contactEntreprise', 'produit', 'iphone', true);
}


elseif($PC->rcvG['action'] == 'viewPart')
{
	viewFiche($PC->rcvG['id_cont'], 'contactParticulier');
}
elseif($PC->rcvG['action'] == 'searchPart')
{
	viewResults($PC->rcvP['query'], 'contactParticulier', 'reset', 'iphone', true);
}
elseif($PC->rcvG['action'] == 'searchPartContinue')
{
	viewResults('', 'contactParticulier', 'suite', 'iphone', true);
}
elseif($PC->rcvG['action'] == 'modifPart')
{
	viewFormulaire($PC->rcvG['id_cont'], 'contactParticulier', 'modif', 'iphone', true, '');
}
elseif($PC->rcvG['action'] == 'addPart')
{
	viewFormulaire('', 'contactParticulier', 'add', 'iphone', true, '');
}
elseif($PC->rcvG['action'] == 'doAddPart')
{
	insertBDD('contactParticulier', $PC->rcvP, $PC->rcvG['__source'], 'iphone', true);
}
elseif($PC->rcvG['action'] == 'doAddPartBis')
{
	$PC->rcvP['entreprise_cont'] = $PC->rcvG['id_ent'];
	insertBDD('contactParticulier', $PC->rcvP, $PC->rcvG['__source'], 'iphone', true);
}
elseif($PC->rcvG['action'] == 'doModifPart')
{
	updateBDD($PC->rcvG['id_cont'], 'contactParticulier', $PC->rcvP, $PC->rcvG['__source'], 'iphone', true);
}
elseif($PC->rcvG['action'] == 'suppPart')
{
	viewFormulaire($PC->rcvG['id_cont'], 'contactParticulier', 'supp', 'iphone', true, '');
}
elseif($PC->rcvG['action'] == 'doSuppPart')
{
	suppBDD($PC->rcvG['id_cont'], 'contactParticulier', 'iphone', false);
}
elseif(trim($PC->rcvG['action']) == 'viewAffaireCont' and $PC->rcvG['id'] != '')
{
	viewRessourcesLies($PC->rcvG['id'], 'contactParticulier', 'affaire', 'iphone', true);
}
elseif(trim($PC->rcvG['action']) == 'viewDevisCont' and $PC->rcvG['id'] != '')
{
	viewRessourcesLies($PC->rcvG['id'], 'contactParticulier', 'devis', 'iphone', true);
}
elseif(trim($PC->rcvG['action']) == 'viewCommandeCont' and $PC->rcvG['id'] != '')
{
	viewRessourcesLies($PC->rcvG['id'], 'contactParticulier', 'commande', 'iphone', true);
}
elseif(trim($PC->rcvG['action']) == 'viewFactureCont' and $PC->rcvG['id'] != '')
{
	viewRessourcesLies($PC->rcvG['id'], 'contactParticulier', 'facture', 'iphone', true);
}

elseif($PC->rcvG['action'] == 'inputContact')
{
	$_SESSION['searchContactLayerBackTo'] = $PC->rcvG['__source'];
	$_SESSION['searchContactTagsBackTo'] = $PC->rcvG['tag'];
?>
	<root><go to="waContactInputAjax"/>
		<title set="waContactInputAjax">Choix d'un contact</title>
		<part><destination mode="replace" zone="waContactInputAjax" create="true"/>
		<data><![CDATA[ <?php echo ZunoLayerContact::headerFormSearchPers(); ?> ]]></data>
		</part>
		<script><![CDATA[ new dynAjax('formSearchContactInput',3,'formSearchContact'); ]]></script>
	</root>
<?php
}
elseif($PC->rcvG['action'] == 'inputContactResult')
{
	$_SESSION['searchContactQuery'] = $PC->rcvP['search'];
	$info = new contactParticulierModel();
	$from = 0;
	$limit = $_SESSION['user']['config']['LenghtSearchContactPart'];
	$contact = $info->getDataForSearch($PC->rcvP['search'],$limit,$from);
	if($contact[0])
	{
		$out .= '<ul id="searchResultInputContactUl">';
		$out .= contactParticulierView::searchInputResultRow($contact[1],$_SESSION['searchContactLayerBackTo'],$_SESSION['searchContactTagsBackTo']);
		if(count($contact[1]) >= $limit)
		$out .= '<li class="iMore" id="searchResultInputContactMore'.$from.'"><a href="Contact.php?action=inputContactContinue&from='.$limit.'" rev="async">Plus de résultats</a></li>';
		$out .= '</ul>';
	}
	else	$out .= '<h2 class="Contact">Contact (0)</h2>';

	?>
	<root>
		<part>
			<destination mode="replace" zone="SearchContactResultAsync"/>
			<data><![CDATA[
				<div class="iList">
					<?php echo $out; ?>
				</div>
			]]></data>
		</part>
	</root>
	<?php
}
elseif($PC->rcvG['action'] == 'inputContactContinue')
{
	$info = new contactParticulierModel();
	$zoneTo = $outJs = $out = '';
	$limit = $_SESSION['user']['config']['LenghtSearchContactPart'];
	$from = ($PC->rcvG['from'] != '') ? $PC->rcvG['from'] : 0;
	$result = $info->getDataForSearch($_SESSION['searchContactQuery'],$from,$limit);
	if($result[0])
	{
		$out .= contactParticulierView::searchInputResultRow($result[1],$_SESSION['searchContactLayerBackTo'],$_SESSION['searchContactTagsBackTo']);
		if(count($result[1]) >= $limit)
		$out .= '<li class="iMore" id="searchResultInputContactMore'.$from.'"><a href="Contact.php?action=inputContactContinue&from='.($from+$limit).'" rev="async">Plus de résultats</a></li>';
		$outJs = 'removeElementFromDom(\'searchResultInputContactMore'.($from-$limit).'\')';
		$zoneTo = 'searchResultInputContactUl';
	}

	if($zoneTo != '')
	{	?>
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
elseif($PC->rcvG['action'] == 'listeVille')
{
	$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$sqlConn->makeRequeteFree("select distinct CP, Ville, Pays from code_postal where CP ='".$PC->rcvP['cp']."' order by Ville ;");
	$temp = $sqlConn->process2();
	$temp=$temp[1];
	if(!array_key_exists('0', $temp))
	{
		exit;
	}
	elseif(!array_key_exists('1', $temp))
	{
		?>
	<root>
		<part>
			<destination mode="replace" zone="<?php echo $PC->rcvG['id']; ?>"/>
			<data><![CDATA[  ]]></data>
		</part><script>$("<?php echo $PC->rcvG['id']; ?>").value = '<?php echo $temp[0]['Ville']; ?>';</script>
	</root>
	<?php
	exit;
	}
	else
	{
		$out ='<div class="iPanel"><fieldset><legend>Villes</legend><ul>';
		$lien = (preg_match("/Contact/", $PC->rcvG['id'])) ? 'ContactPartAdd' : 'ContactEntAdd';
		foreach($temp as $v)
		{
			$out .= '<li><a href="#_'.$lien.'" onclick="placerVille(\''.$v['Ville'].'\', \''.$PC->rcvG['id'].'\')">'.$v['Ville'].'</a></li>';
		}
		$out .= '<li><a href="#_'.$lien.'">Autre ville</a></li>';
		$out .= '</ul></fieldset></div>';
		?>
		<root><go to="waListeVille"/>
			<title set="waListeVille">Choix d'une ville</title>
			<part><destination mode="replace" zone="waListeVille" create="true"/>
			<data><![CDATA[ <?php echo $out; ?> ]]></data>
			</part>
		</root>
		<?php
	}
	
}

ob_end_flush();
?>
