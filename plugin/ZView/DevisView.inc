<?php
loadPlugin(array('ZModels/AffaireModel','ZModels/DevisModel','ZDoc/DevisDoc', 'ZView/GeneralView', 'ZunoRenduHTML'));

/**
 * Classe devisView, elle gère l'affichage des devis dans la partie web
 *
 * @todo Passer en code propre
 * @author Nicolas Mannocci
 * @version 1
 */
class devisView extends generalView {
    public $model;
    public $modelAffaire;

    function __construct() {
	$this->model = new devisModel();
	$this->modelAffaire = new affaireModel();
    }

    public function view($datas, $light, $mess) {
	if(array_key_exists('out',$GLOBALS))
	    $GLOBALS['out']->setTitle('Fiche devis "'.$datas['data']['id_dev'].'" - '.$datas['data']['nom_ent'],'',true);
	$script = '<script type="text/javascript">new Ajax.Autocompleter("ACcontact_dev", "ACcontact_dev_choix", "../ajaxRef.php?action=listeContact&id_ent='.$datas['data']['id_ent'].'", { paramName: "value", minChars: 2, indicator: "loadingCont",  afterUpdateElement : autoCompleteHidden } ); ';
	$script .= "\n".'new Ajax.Autocompleter("ACproduit_ajout_dev", "ACproduit_ajout_dev_choix", "../ajaxRef.php?action=listeProduit", { paramName: "value", minChars: 2, afterUpdateElement : calculetteAjoutProd } ); ';
	$script .= "\n".'new Ajax.Autocompleter("ACcontact_achat_dev", "ACcontact_achat_dev_choix", "../ajaxRef.php?action=listeContact&id_ent='.$datas['data']['id_ent'].'", { paramName: "value", minChars: 2, indicator: "loadingCont2", afterUpdateElement : autoCompleteHidden } ); ';

	$script .= "\n".'function calculetteAjoutProd(text, li) {id = li.title.split(\'-_-\'); $(text.id+\'hidden\').value = id[0]; $(\'prix_Ajout_prod_dev\').value = id[2]; $(\'desc_Ajout_prod_dev\').innerHTML = id[3]; $(\'maxPrix_Ajout_prod_dev\').value = id[4]; calculetteTotal();} ';
	$script .= "\n".'function calculetteTotal(id) {  if(id == undefined) { var qtt = $(\'quantite_Ajout_prod_dev\').value.replace(/,/, ".").replace(/ /,""); var prix = $(\'prix_Ajout_prod_dev\').value.replace(/,/, ".").replace(/ /,""); var rem = $(\'remise_Ajout_prod_dev\').value.replace(/,/, ".").replace(/ /,""); var pxM = $(\'maxPrix_Ajout_prod_dev\').value; $(\'total_Ajout_prod_dev\').value = qtt*prix*(1-rem/100); if(prix*(1-rem/100) < pxM) {$(\'img_Ajout_prod_dev\').style.display = \'block\';} else {$(\'img_Ajout_prod_dev\').style.display = \'none\'; } } else { var qtt = $(\'quantite_Ajout_prod_dev\'+id).value.replace(/,/, ".").replace(/ /,""); var prix = $(\'prix_Ajout_prod_dev\'+id).value.replace(/,/, ".").replace(/ /,""); var rem = $(\'remise_Ajout_prod_dev\'+id).value.replace(/,/, ".").replace(/ /,""); var pxM = $(\'maxPrix_Ajout_prod_dev\'+id).value; $(\'total_Ajout_prod_dev\'+id).value = qtt*prix*(1-rem/100); if(prix*(1-rem/100) < pxM) {$(\'img_Ajout_prod_dev\'+id).style.display = \'block\';} else {$(\'img_Ajout_prod_dev\'+id).style.display = \'none\'; }} } ';
	$script .= "\n".'function autoCopieChamps(id) { $(\'quantite_Ajout_prod_dev\').value = $(\'quantite_Ajout_prod_dev\'+id).value; $(\'prix_Ajout_prod_dev\').value = $(\'prix_Ajout_prod_dev\'+id).value; $(\'remise_Ajout_prod_dev\').value = $(\'remise_Ajout_prod_dev\'+id).value; $(\'total_Ajout_prod_dev\').value = $(\'total_Ajout_prod_dev\'+id).value; $(\'desc_Ajout_prod_dev\').value = $(\'desc_Ajout_prod_dev\'+id).value;} ';
	$script .= "\n".'function afficherFormModif(id) { if($(\'suisJeEnModif\').value != 0) { $($(\'suisJeEnModif\').value+\'td1\').style.display = \'none\'; $($(\'suisJeEnModif\').value+\'td0\').style.display = \'table-row\'; } $(\'suisJeEnModif\').value = id; $(id+\'td0\').style.display = \'none\'; $(id+\'td1\').style.display = \'table-row\'; $(\'tdAjoutProdDevis\').style.display = \'none\'; $(\'ACproduit_ajout_devhidden\').value = $(\'ID\'+id).innerHTML;} ';
	$script .= "\n".'function chargerAction(value) { $(\'actionDevisProd\').value = value;} ';
	$script .= "\n".'function chargerProduit(value) { $(\'produitDevisProd\').value = value;} ';
	$script .= "\n".'function annulerModif() {$($(\'suisJeEnModif\').value+\'td1\').style.display=\'none\'; $($(\'suisJeEnModif\').value+\'td0\').style.display=\'table-row\';  $(\'ACproduit_ajout_devhidden\').value =""; $(\'tdAjoutProdDevis\').style.display = \'table-row\'; $(\'suisJeEnModif\').value = 0;} ';
	$script .= "\n".'function ChangeCannevas() { var choix = document.getElementById(\'CannevasListe\').value; if(choix.length > 0) { document.getElementById(\'MessageTitle\').value =  CannevasTitre[choix]; document.getElementById(\'MessageToSend\').value = Cannevas[choix]; }  } ';
	$script .= "\n".'function ChangeDevisAction(value) { $(\'MakeDevisAction\').value = value; } ';
	$script .= "\n".'function verifAutocompleteur(champ) {if(champ.value == "" || champ.value == null) {$(champ.id+\'hidden\').value = "";} else if(!$(champ.id+\'_choix\').hasChildNodes()) {alert(\'Cette valeur n\\\'existe pas.\'); champ.value=$(champ.id+\'old\').value; setTimeout(function() { champ.focus(); }, 100) } } ';
	$script .= "\n".'function changeDevisFormat(value) { $(\'formatDevisPDF\').value = value;} ';
	$script .= "\n".'function champAdd(display) {$(\'champDestinataire\').style.display = $(\'champAdd1\').style.display = $(\'champAdd2\').style.display = $(\'champAdd3\').style.display = $(\'champAdd4\').style.display = display;} ';
	$script .= "\n".'function affRen(cbox) { if(cbox.checked) { $(\'mailF\').parentNode.parentNode.style.display = \'table-row\'; $(\'statusF\').parentNode.parentNode.style.display = \'table-row\'; $(\'fin_renID\').parentNode.parentNode.style.display = \'table-row\'; $(\'selectF\').parentNode.parentNode.style.display = \'table-row\'; } else { $(\'mailF\').parentNode.parentNode.style.display = \'none\'; $(\'fin_renID\').parentNode.parentNode.style.display = \'none\'; $(\'selectF\').parentNode.parentNode.style.display = \'none\'; $(\'statusF\').parentNode.parentNode.style.display = \'none\'; } } ';
	$script .= "\n".'function montreForm(value) {if(value == \'mail\') {  $(\'champDestinataire\').style.display = $(\'champFax\').style.display = \'none\'; $(\'champCC\').style.display = \'table-row\'; champAdd(\'none\'); $(\'champEmail\').style.display = \'table-row\';} else if(value == \'fax\') { champAdd(\'none\'); $(\'champDestinataire\').style.display = $(\'champFax\').style.display = \'table-row\'; $(\'champEmail\').style.display =\'none\'; $(\'champCC\').style.display = \'none\';} else if(value == \'courrier\') {  $(\'champFax\').style.display = \'none\'; $(\'champCC\').style.display = $(\'champEmail\').style.display = \'none\'; champAdd(\'table-row\')} else {$(\'champEmail\').style.display = $(\'champFax\').style.display = \'none\'; $(\'champCC\').style.display = \'none\'; champAdd(\'none\'); } } ';

	$script .= "\n".'function saveProd() {' .
		'zuno.ajax.post.json(\'../produit/Produit.php\', ' .
		'zuno.business.formTools.getParamFromForm(\'formProduit\'), ' .
		'function(xhr) { ' .
		'var json = xhr.responseText.evalJSON(); ' .
		'if(json.error != undefined){$(\'error\').innerHTML = json.error;}'.
		'else { $(\'ACproduit_ajout_dev\').value = \'[\'+json.datas.id_prod+\']\'; ' .
		'$(\'desc_Ajout_prod_dev\').value = json.datas.description_prod; ' .
		'$(\'prix_Ajout_prod_dev\').value = json.datas.prix_prod; ' .
		'$(\'total_Ajout_prod_dev\').value = json.datas.prix_prod; ' .
		'$(\'ACproduit_ajout_devhidden\').value = json.datas.id_prod; ' .
		'zuno.popup.close(); }' .
		'} );} ';
	$script .= "\n".'function searchProdReturn(id_prod,description_prod,prix_prod) {' .
		'$(\'ACproduit_ajout_dev\').value = \'[\'+id_prod+\']\'; ' .
		'$(\'desc_Ajout_prod_dev\').value = description_prod; ' .
		'$(\'prix_Ajout_prod_dev\').value = prix_prod; ' .
		'$(\'total_Ajout_prod_dev\').value = prix_prod; ' .
		'$(\'ACproduit_ajout_devhidden\').value = id_prod; ' .
		'zuno.popup.close(); ' .
		'} ';

	$script .= "function redirectCommande(id)
{
	document.location.href=\"../pegase/Commande.php?id_cmd=\"+id;
}

function aCVF(prod, fourn)
{
	if(fourn != '')
	{
		$(prod+'prixF').value = prix[prod][fourn];
		$(prod+'prixF').readOnly = false;
		$(prod+'remiseF').value = remise[prod][fourn];
		$(prod+'remiseF').readOnly = false;
		var qtt = $(prod+'qtt_cmd').value.replace(/,/g, '.').replace(/ /g, '');
		$(prod+'qtt_cmd').readOnly = false;
		var total = qtt*prix[prod][fourn]*(1-remise[prod][fourn]/100);
		$('Tot'+prod).innerHTML = Math.round(total*100)/100+' &euro;';
		$('Marge'+prod).innerHTML = Math.round(($(prod+'prixC').value.replace(/,/g, '.').replace(/ /g, '')*$(prod+'qtt').value.replace(/,/g, '.').replace(/ /g, '')*(1-$(prod+'remiseC').value.replace(/,/g, '.').replace(/ /g, '')/100)-total)*100)/100+' &euro;';
	}
	else
	{
		$(prod+'prixF').value = 0;
		$(prod+'prixF').readOnly = true;
		$(prod+'remiseF').value = 0;
		$(prod+'remiseF').readOnly = true;
		$(prod+'qtt_cmd').readOnly = true;
		$('Tot'+prod).innerHTML = '0.00 &euro;';
		$('Marge'+prod).innerHTML = Math.round($(prod+'prixC').value.replace(/,/g, '.').replace(/ /g, '')*$(prod+'qtt').value.replace(/,/g, '.').replace(/ /g, '')*(1-$(prod+'remiseC').value.replace(/,/g, '.').replace(/ /g, '')/100)*100)/100+' &euro;';
	}
	calculTotaux();
}
function changeData(prod)
{
	if(!IsNumeric($(prod+'prixF').value.replace(/,/g, '.').replace(/ /g, '')))
		$(prod+'prixF').value = 0;
	if(!IsNumeric($(prod+'remiseF').value.replace(/,/g, '.').replace(/ /g, '')))
		$(prod+'remiseF').value = 0;
	if(!IsNumeric($(prod+'qtt_cmd').value.replace(/,/g, '.').replace(/ /g, '')))
		$(prod+'qtt_cmd').value = 0;
	var total = $(prod+'qtt_cmd').value.replace(/,/g, '.').replace(/ /g, '')*$(prod+'prixF').value.replace(/,/g, '.').replace(/ /g, '')*(1-$(prod+'remiseF').value.replace(/,/g, '.').replace(/ /g, '')/100);
	$('Tot'+prod).innerHTML = Math.round(total*100)/100+' &euro;';
	$('Marge'+prod).innerHTML = Math.round(($(prod+'prixC').value.replace(/,/g, '.').replace(/ /g, '')*$(prod+'qtt').value.replace(/,/g, '.').replace(/ /g, '')*(1-$(prod+'remiseC').value.replace(/,/g, '.').replace(/ /g, '')/100)-total)*100)/100+' &euro;';
	calculTotaux();
}

function calculTotaux()
{
	var HTF = 0;
	for(i=0; i<produit.length; i++)
	{
		var tot = $('Tot'+produit[i]).innerHTML.substring(0, $('Tot'+produit[i]).innerHTML.length-2);
		HTF += parseFloat(tot);
	}
	$('TotalHTF').innerHTML = HTF+' &euro;';
	$('TotalMarge').innerHTML = Math.round((parseFloat(($('TotalHT').innerHTML.substring(0, $('TotalHT').innerHTML.length-2)).replace(/,/g, '.').replace(/ /g, ''))-HTF)*100)/100+' &euro;';
}
function showCmd()
{
	$('commandePopupForm').style.display = 'block';
	$('facturePopupForm').style.display = 'none';
}
function showFact()
{
	$('commandePopupForm').style.display = 'none';
	$('facturePopupForm').style.display = 'block';
}
function validFormPopup()
{
	if($('commandePopupForm').style.display == 'block')
	{
		return zuno.business.formTools.sendFormAjah('addCmd', 'pegase/Commande.php','formAddCmdFromDevis', 'popup');
	}
	else if($('facturePopupForm').style.display == 'block')
	{
		return zuno.business.formTools.sendFormAjah('addFact', 'facturier/FactureCreate.php','formAddCmdFromDevis', 'popup');
	}
}
function IsNumeric(input)
{
	return (input - 0) == input && input.length > 0;
}";
	$script .= "\n".'function autoRemplir(dest, id, val){$(dest).value=val; $(dest+\'hidden\').value=id; zuno.popup.close();}';
	$script .= '</script>';

	if($light == 'interneInfos')
	    return '<span class="important" style="text-align:center;">'.$mess.'</span>'.$this->interneInfos($datas);
	elseif($light == 'interneProduit')
	    return '<span class="important" style="text-align:center;">'.$mess.'</span>'.$this->interneProduits($datas);
	elseif($light == 'interneTraitement' or $light == 'Traitement')
	    return $this->traitement($datas, '<span class="important" style="text-align:center;">'.$mess.'</span>');
	else
	    return '<div id="error"></div>'.$this->infos($datas).$this->produits($datas).'<div id="boxTraitementDevis">'.$this->traitement($datas).'</div>'.$script;
    }

    private function infos($datas) {
	$data = $datas['data'];

	$titre = '<img src="../img/prospec/devis.png" alt="devis" title="Devis" /> Informations sur le devis '.$data['id_dev'];

	$corps = '<form name="ModifInfosDevis" action="#" method="post">';
	$corps .= '<input type="hidden" name="action" value="modifDevis" />';
	$corps .= '<input type="hidden" name="id_dev" value="'.$data['id_dev'].'" />';
	if($data['id_dev'] == $datas['ren']['idChamp_ren'] or $data['ren_dev'] == "")
	    $corps .= '<input type="hidden" name="ren_dev" value="'.$data['ren_dev'].'" />';
	$corps .= '<div id="ContenuFormInfosDevis">';
	$corps .= $this->interneInfos($datas);
	$corps .= '</div></form>';
	$pied = '<a title="Reset" href="javascript:document.ModifInfosDevis.reset();" ><img align="middle" title="Effacer" alt="Effacer" name="img" src="../img/prospec/cancel.png"/> Recommencer</a>';
	if(verifDroitsAuto('devis', 15, $data['commercial_dev']))
	    $pied .= '<a name="submit" onclick="zuno.business.formTools.sendFormAjah(\'ModifInfosDevis\', \'draco/Devis.php\',\'ContenuFormInfosDevis\');"><img align="middle" title="Enregistrer" alt="Enregistrer" name="img" src="../img/prospec/record.png"/> Modifier ce devis</a>';
	$pied .= '<a name="actuDevis" title="Actualités" onclick="return zuno.popup.open(\'Devis.php\', \'action=actuDevis&id_devis='.$data['id_dev'].'\', \'800\', \'450\', \'\', \'\', \'resize\', \'Actualités\');" ><img align="middle" title="Actualités" alt="Actualités" src="../img/prospec/actu.png"/> Historique de ce devis</a>';

	if ($data['status_dev'] == '2' or $data['status_dev'] == '5') {
	    $pied 	= "";
	}
	return generateZBox($titre,$titre,$corps,$pied,'infosDevis','');
    }
    private function interneInfos($datas) {
	$data = $datas['data'];
	if ($data['nom_ent'] == '')
	    $data['nom_ent'] = '<i>Particulier</i>';
	if ($data['type_ent'] != '')	$data['nom_entH']	= imageTag('../img/'.$GLOBALS['PropsecConf']['dir.img'].'TypeEntreprise/'.$data['type_ent'].'.png',$data['nom_tyent']).' '.$data['nom_ent'];
	else							$data['nom_entH']	= imageTag('../img/'.$GLOBALS['PropsecConf']['dir.img'].'TypeEntreprise/particulier.png','particulier').' <i>Particulier</i>';
	$out = '<div class="block width50">
		<fieldset class="form" style="height: 150px">
		<legend>Informations sur le devis</legend>

		<div class="row">
		    <div class="label" onmouseover="zuno.tooltip(this,ZTips.devis.cree);">Créé le </div>
		    <div class="field">'.DateUniv2Human($data['daterecord_dev'], 'simpleLong').'</div>
		</div>
		<div class="row">
		    <div class="label" onmouseover="zuno.tooltip(this,ZTips.devis.modifie);">Modifié le </div>
		    <div class="field">'.DateUniv2Human($data['datemodif_dev'], 'simpleLong').'</div>
		</div>
		<div class="row">
		    <div class="label" onmouseover="zuno.tooltip(this,ZTips.devis.titre);">Titre</div>
		    <div class="field">'.inputTag('text', 'titre_dev', '', '', '35', $data['titre_dev']).'</div>
		</div>
		<div class="row">
		    <div class="label" onmouseover="zuno.tooltip(this,ZTips.devis.bdcclient);">BDC Client</div>
		    <div class="field">'.inputTag('text', "BDCclient_dev", '', '', '16', $data['BDCclient_dev']).'</div>
		</div>
		<div class="row">
			<div class="label" onmouseover="zuno.tooltip(this,ZTips.devis.statut);">Statut</div>
			<div class="field" style="color: #'.$data['color_stdev'].'"><div class="square" style="background-color:#'.$data['color_stdev'].';">&nbsp;</div> '.$data['nom_stdev'].'</div>
		</div>
		</fieldset>
		<fieldset class="form">
		<legend>Commentaire sur le devis</legend>
		<div class="row">
		    <div class="label" onmouseover="zuno.tooltip(this,ZTips.devis.commentaire);">Commentaire</div>
		    <div class="field">
			    <textarea name="complementdelivery_dev" cols="55" rows="2" >'.$data['complementdelivery_dev'].'</textarea></div>
		</div>
		<div class="row oblig">
		    <div class="label" onmouseover="zuno.tooltip(this,ZTips.devis.tauxTva);">Taux de TVA</div>
		    <div class="field">'.selectTVATag("tva_dev", array(), $data['tva_dev'], '', '', false).'</div>
		</div>
		</fieldset>
		</div>
		<div class="block width50">
		<fieldset class="form" style="height: 150px">
		<legend>Contacts du devis</legend>
		<div class="row">
		    <div class="label" onmouseover="zuno.tooltip(this,ZTips.devis.entreprise);">Entreprise</div>
		    <div class="field"><a href="#" onclick="return zuno.popup.open(\'../prospec/PopupEntreprise.php\',\'id_ent='.$data['id_ent'].'\',950,200,\'\',\'\',\'resize\',\'Entreprise\');" id="entrepriseFiche" title="voir les informations de l\'entreprise '.$data['nom_ent'].'">'.$data['nom_entH'].'</a></div>
		</div>
		<div class="row oblig">
		    <div class="label" onmouseover="zuno.tooltip(this,ZTips.devis.contactTech);">Contact Tech</div>
		    <div class="field">'.inputTag('text', 'contact_dev2','icon2', '', '16',$data['civ_cont'].' '.$data['prenom_cont'].' '.$data['nom_cont'], ' id="ACcontact_dev" onchange="verifAutocompleteur(this);" ').'<a name="addCont" title="Ajouter un contact" onclick="return zuno.popup.open(\'Devis.php\', \'action=addContDev&entreprise='.$data['id_ent'].'&idChamp=ACcontact_dev\',850,300);"><img alt="cont" title="Ajouter un contact" src="../img/prospec/add.png" /></a><a name="search" onclick="return zuno.popup.open(\'../prospec/PopupEntreprise.php\', \'action=searchContDev&entreprise='.$data['id_ent'].'&idChamp=ACcontact_dev\',850,300);"><img alt="recherche" src="../img/ajaxField.png" /></a><img style="display:none; margin-left:5px;" id="loadingCont" src="../img/ajax-loader.gif" alt="loader"/></div>
		    <input type="hidden" name="contact_dev" id="ACcontact_devhidden" value="'.$data['contact_dev'].'" /><input type="hidden" name="contold" id="ACcontact_devold" value="'.$data['civ_cont'].' '.$data['prenom_cont'].' '.$data['nom_cont'].'" />
		    <div id="ACcontact_dev_choix" class="autocomplete"></div>
		</div>
		<div class="row oblig">
		    <div class="label" onmouseover="zuno.tooltip(this,ZTips.devis.contactAchat);">Contact Achat</div>
		    <div class="field">'.inputTag('text', 'contact_achat_dev2','icon2', '', '16',$data['civ_achat'].' '.$data['prenom_achat'].' '.$data['nom_achat'], ' id="ACcontact_achat_dev" onchange="verifAutocompleteur(this);"').'<a name="addCont" title="Ajouter un contact" onclick="return zuno.popup.open(\'Devis.php\', \'action=addContDev&entreprise='.$data['id_ent'].'&idChamp=ACcontact_achat_dev\',850,300);"><img alt="cont" title="Ajouter un contact" src="../img/prospec/add.png" /></a><a name="search" onclick="return zuno.popup.open(\'../prospec/PopupEntreprise.php\', \'action=searchContDev&entreprise='.$data['id_ent'].'&idChamp=ACcontact_achat_dev\',850,300);"><img alt="recherche" src="../img/ajaxField.png" /></a><img style="display:none; margin-left:5px;" id="loadingCont2" src="../img/ajax-loader.gif" alt="loader"/></div>
		    <input type="hidden" name="contact_achat_dev" id="ACcontact_achat_devhidden" value="'.$data['contact_achat_dev'].'" /><input type="hidden" name="oldCont" id="ACcontact_achat_devold" value="'.$data['civ_achat'].' '.$data['prenom_achat'].' '.$data['nom_achat'].'" />
		    <div id="ACcontact_achat_dev_choix" class="autocomplete"></div>
		</div>
		<div class="row oblig">
		    <div class="label" onmouseover="zuno.tooltip(this,ZTips.devis.commercial);">Commercial</div>
		    <div class="field">'.selectTag('commercial_dev',$datas['user'],$data['commercial_dev'],'','',false).'</div>
		</div>
		</fieldset>';
	if($data['id_fact'] != '' or $data['id_cmd'] != '' or $data['id_dev'] != '' or $data['id_aff'] != '') {
	    $out.= '<fieldset class="form">
				<legend>Documents liées </legend>';
	    if($data['id_aff'] != '') {
		$out .= '<div class="row"><div class="label">Affaire : </div>';
		$out .= '<div class="field"><a href="../draco/Affaire.php?id_aff='.$data['id_aff'].'"><img src="../img/actualite/affaire.png" title="Affaire", alt="affaire" /> '.$data['id_aff'].' - '.$data['titre_aff'].' </a></div></div>';
	    }
	    if($data['id_dev'] != '') {
		$out .= '<div class="row"><div class="label">Devis : </div>';
		$out .= '<div class="field"><a href="../draco/Devis.php?id_dev='.$data['id_dev'].'"><img src="../img/actualite/devis.png" title="Devis", alt="devis" /> '.$data['id_dev'].' - '.$data['titre_dev'].'</a></div></div>';
	    }
	    if($data['id_cmd'] != '') {
		$out .= '<div class="row"><div class="label">Commande : </div>';
		$out .= '<div class="field"><a href="../pegase/Commande.php?id_cmd='.$data['id_cmd'].'"><img src="../img/actualite/commande.png" title="Commande", alt="commande"> '.$data['id_cmd'].' - '.$data['titre_cmd'].'</a></div></div>';
	    }
	    if($data['id_fact'] != '') {
		$out .= '<div class="row"><div class="label">Facture : </div>';
		$out .= '<div class="field"><a href="../facturier/Facture.php?id_fact='.$data['id_fact'].'"><img src="../img/actualite/facture.png" title="Facture", alt="facture"> '.$data['id_fact'].' - '.$data['titre_fact'].'</a></div></div>';
	    }
	    $out.= '</fieldset>';
	}
	$out.='</div>
		<div class="block width50">
		<fieldset class="form" style="height: 150px">
		    <legend>Informations sur la livraison</legend>
		    <div class="row">
			    <div class="label" onmouseover="zuno.tooltip(this,ZTips.devis.deliveryNom);">Nom</div>
			    <div class="field">'.inputTag('text', "nomdelivery_dev", '', '', '30', $data['nomdelivery_dev']).'</div>
		    </div>
		    <div class="row">
			    <div class="label" onmouseover="zuno.tooltip(this,ZTips.devis.deliveryAdd);">Adresse</div>
			    <div class="field">'.inputTag('text', "adressedelivery_dev", '', '', '40', $data['adressedelivery_dev']).'</div>
		    </div>
		    <div class="row">
			    <div class="label" onmouseover="zuno.tooltip(this,ZTips.devis.deliveryComplement);">Complément</div>
			    <div class="field">'.inputTag('text', "adresse1delivery_dev", '', '', '40', $data['adresse1delivery_dev']).'</div>
		    </div>
		    <div class="row">
			    <div class="label" onmouseover="zuno.tooltip(this,ZTips.devis.deliveryCpVille);">CP - Ville</div>
			    <div class="field">'.inputTag('text', "cpdelivery_dev", 'civ', '', '6', $data['cpdelivery_dev'], 'onchange="zuno.ajax.get.html(\'../ajaxRef.php\',\'action=villeForCp&cp=\'+this.value,\'devisVille\');"').'<select name=\'villedelivery_dev\'  id="devisVille"  onblur="finishEditing();" onclick="beginEditing(this);" class="nextciv"><option value=""> </option><option value="'.$data['villedelivery_dev'].'" selected="selected">'.$data['villedelivery_dev'].'</option></select></div>
		    </div>
		    <div class="row">
			    <div class="label" onmouseover="zuno.tooltip(this,ZTips.devis.deliveryPays);">Pays</div>
			    <div class="field">'.selectTag('paysdelivery_dev',$datas['pays'],$data['paysdelivery_dev']).'</div>
		    </div>
		    <div class="row">
			    <div class="label" onmouseover="zuno.tooltip(this,ZTips.devis.deliveryMail);">Mail</div>
			    <div class="field">'.inputTag('text', "maildelivery_dev", '', '', '30', $data['maildelivery_dev']).'</div>
		    </div>
		</fieldset>
		</div>';
	if($data['id_dev'] == $datas['ren']['idChamp_ren'] or $data['ren_dev'] == "") {
	    if($datas['ren']['fin_ren'] != '0000-00-00 00:00:00' and $datas['ren']['fin_ren'] != "") {
		$date2 = $datas['ren']['fin_ren'];
		$date2 = substr($date2, 8,2)."/".substr($date2,5,2)."/".substr($date2,0,4);
	    }
	    else $date2 = date('d/m/').(date('Y')+1);
	    if($datas['ren']['actif_ren'] == 1 ) {
		$checked = ' checked="checked" ';
		$styleDisplay="";
	    }
	    else {
		$checked = "";
		$styleDisplay = ' style="display:none" ';
	    }
	    if($datas['ren']['mail_ren'] == "")
		$datas['ren']['mail_ren'] = $_SESSION['user']['mail'];
	    $out .= '<div class="block width50">
	    <fieldset class="form">
		    <legend>Récurrence</legend>
		    <div class="row">
			    <div class="label" style="width:100px">Activer</div>
			    <div class="field"><input type="checkbox" name="actif_ren" value="1" onchange="affRen(this)" '.$checked.' /></div>
		    </div>
		    <div class="row oblig"'.$styleDisplay.'>
			    <div class="label">Fréquence</div>
			    <div class="field">'.selectTag("periode_ren", $datas['periode'], $datas['ren']['periode_ren'], "", " id='selectF' ", false).'</div>
		    </div>
		    <div class="row oblig"'.$styleDisplay.'>
			    <div class="label">Date de fin </div>
			    <div class="field">'.inputDateTag("text", "fin_ren", $date2).'</div>
		     </div>
		     <div class="row oblig"'.$styleDisplay.'>
			    <div class="label">Statut</div>
			    <div class="field">'.selectTag("statusChamp_ren", $datas['status'], $datas['ren']['statusChamp_ren'], "", " id='statusF' ", false).'</div>
		     </div>
		     <div class="row oblig"'.$styleDisplay.'>
			    <div class="label">E-Mail</div>
			    <div class="field">'.inputTag("text", "mail_ren", "", "", "16", $datas['ren']['mail_ren'], " id='mailF' ").'</div>
		     </div>
	    </fieldset>
	</div>';
	}
	else {
	    $out .= '<div class="block width50">
                        <fieldset class="form">
                            <legend>Récurrence</legend>
                            <div class="row">
                                <div class="label">Devis originale</div>
                                <div class="field"><a href="Devis.php?id_dev='.$datas['ren']['idChamp_ren'].'" >Ce devis a été créée en renouvellement du devis '.$datas['ren']['idChamp_ren'].'</a></div>
                            </div>
                         </fieldset>
                        </div>';
	}
	return $out;
    }



    private function produits($datas) {
	$titre = imageTag('../img/prospec/devis.png','devis').' Produits du devis '.$datas['data']['id_dev'];
	$corps = '<form name="productDevis" action="#" method="post">';
	$corps .= '<input type="hidden" name="id_devis" value="'.$datas['data']['id_dev'].'" />';
	$corps .= '<input type="hidden" name="produitAModifier" value="" id="produitDevisProd"/>';
	$corps .= '<input type="hidden" name="enmodif" value="0" id="suisJeEnModif" />';
	$corps .= '<div class="blockTable" id="contenuProdDevis">';
	$corps .= $this->interneProduits($datas);
	$corps .= '</div></form>';
	return generateZBox($titre,$titre,$corps,'','produitsDevis','');
    }
    private function interneProduits($datas) {
	$out = '<table width="100%" cellspacing="0" cellpadding="2" border="0">
		<tbody><tr class="titre">
			<th style="width: 135px">Référence</th>
			<th>Description</th>
			<th style="width: 30px">Quantité</th>
			<th style="width: 90px">Prix</th>
			<th style="width: 65px">Remise</th>
			<th style="width: 110px">Total</th>
			<th width="1%"> </th>
		</tr>';
	$alternance = 0;
	$bigtotal = 0;
	foreach($datas['produits'] as $v) {
	    $total = $v['prix']*$v['quantite']*(1-$v['remise']/100);
	    $bigtotal += $total;
	    if($v['id_prod'] == '') {
		$v['id_prod'] = $v['id_produit'];
		$v['nom_prod'] = $v['id_produit'];
	    }
	    $action = (verifDroitsAuto('devis', 15, $datas['data']['commercial_dev'])) ? '<a name="modif" onclick="afficherFormModif(\''.$v['id'].'\');"><img title="Modifier" alt="Modifier" name="img" src="../img/prospec/devis.modif.png"/></a>
					    <a name="supp" onclick="chargerAction(\'suppProduit\');chargerProduit(\''.$v['id'].'\'); zuno.business.formTools.sendFormAjah(\'productDevis\', \'draco/Devis.php\', \'contenuProdDevis\');"><img title="Supprimer" alt="Supprimer" name="img" src="../img/prospec/devis.delete.png"/></a>' : '';
	    $out .= '<tr class="altern'.$alternance.'" id="'.$v['id'].'td0">
			<td class="barre" title="'.$v['nom_prod'].'" id="ID'.$v['id'].'">'.$v['id_prod'].'</td>
			<td class="barre">'.$v['desc'].'</td>
			<td class="center barre">'.prepareNombreAffichage($v['quantite']).'</td>
			<td class="barre">'.prepareNombreAffichage($v['prix']).' €</td>
			<td class="barre">'.prepareNombreAffichage($v['remise']).' %</td>
			<td class="barre">'.prepareNombreAffichage($total).' €</td>
			<td nowrap="nowrap" class="center">'.$action.'</td>
			</tr>';
	    if(verifDroitsAuto('devis', 15, $datas['data']['commercial_dev']))
		$out .= '<tr class="altern'.$alternance.'" id="'.$v['id'].'td1" style="display:none;">
			<td class="barre" title="'.$v['nom_prod'].'">'.$v['id_prod'].'</td>
			<td class="barre">'.textareaTag('desc2', $v['desc'], '', '', '', ' id="desc_Ajout_prod_dev'.$v['id'].'" style="width:98%; height:30px;" ').'</td>
			<td class="center barre">'.inputTag('text', 'quantite2', '', '', '16', prepareNombreAffichage($v['quantite']), ' id="quantite_Ajout_prod_dev'.$v['id'].'" onchange="calculetteTotal(\''.$v['id'].'\');" style="width:28px" ').'</td>
			<td class="barre">'.inputTag('text', 'prix2', '', '', '16', prepareNombreAffichage($v['prix']), ' id="prix_Ajout_prod_dev'.$v['id'].'" onchange="calculetteTotal(\''.$v['id'].'\');" style="width:60px" ').' €</td>
			<td class="barre">'.inputTag('text', 'remise2', '', '', '16', prepareNombreAffichage($v['remise']), ' id="remise_Ajout_prod_dev'.$v['id'].'" onchange="calculetteTotal(\''.$v['id'].'\');" style="width:30px" ').' %</td>
			<td class="barre">'.inputTag('text', 'prix_total2', '', '', '16', prepareNombreAffichage($total), ' id="total_Ajout_prod_dev'.$v['id'].'" readonly="readonly" style="width:80px" ').' € <img alt="alert" title="Vous risquez de vendre à perte !" src="../img/exclam.png" id="img_Ajout_prod_dev'.$v['id'].'" style="display:none;"/></td>
			'.inputTag('hidden', 'mP', '', '', '', $v['PF'], ' id="maxPrix_Ajout_prod_dev'.$v['id'].'" ').'
			<td nowrap="nowrap" class="center"><a name="modif" onclick="autoCopieChamps(\''.$v['id'].'\');chargerAction(\'modifProduit\'); zuno.business.formTools.sendFormAjah(\'productDevis\', \'draco/Devis.php\',\'contenuProdDevis\');"><img title="Modifier" alt="Modifier" name="img" src="../img/prospec/devis.add.png"/></a>
							<a name="anule" onclick="annulerModif();"><img title="annuler" alt="annuler" src="../img/prospec/cancel.png" /></a></td>
			</tr>';
	    $alternance = ($alternance == '0') ? '1':'0';
	}

	$action2 = (verifDroitsAuto('devis', 15, $datas['data']['commercial_dev'])) ? '<a name="add" onclick="chargerAction(\'addProduit\'); zuno.business.formTools.sendFormAjah(\'productDevis\', \'draco/Devis.php\',\'contenuProdDevis\');"><img title="Ajouter" alt="Ajouter" name="img" src="../img/prospec/devis.addProduct.png"/></a>' : '';
	$out .= '<tr class="altern'.$alternance.'" id="tdAjoutProdDevis">
			<td class="barre">'.inputTag('text', 'produit','', '', '16','', ' id="ACproduit_ajout_dev" style="width:80px" onblur="verifAutocompleteur(this);"').'
				<img src="../img/ajaxField.png" alt="rechercher" onclick="zuno.popup.open(\'../produit/Produit.php\', \'action=popupSearchProd\',950,500);" style="cursor:pointer; margin-right:0px"/><img src="../img/prospec/add.png" alt="ajouter" onclick="zuno.popup.open(\'../produit/Produit.php\', \'action=popupAddProd\', \'800\', \'330\');" style="cursor:pointer; margin-right:0px"/>
				<img style="display:none; margin-left:5px;" id="loadingProd" src="../img/ajax-loader.gif" alt="loader"/>
				<input type="hidden" name="id_produit" id="ACproduit_ajout_devhidden" value="" /><input type="hidden" name="idold" id="ACproduit_ajout_devold" value="" />
				<div id="ACproduit_ajout_dev_choix" class="autocomplete"></div></td>
			<td class="barre">'.textareaTag('desc', '', '', '', '', ' id="desc_Ajout_prod_dev" style="width:98%; height:30px;" ').'</td>
			<td class="center barre">'.inputTag('text', 'quantite', '', '', '16', '1', ' id="quantite_Ajout_prod_dev" onchange="calculetteTotal();" style="width:28px" ').'</td>
			<td class="barre">'.inputTag('text', 'prix', '', '', '16', '', ' id="prix_Ajout_prod_dev" onchange="calculetteTotal();" style="width:60px" ').' €</td>
			<td class="barre">'.inputTag('text', 'remise', '', '', '16', '0', ' id="remise_Ajout_prod_dev" onchange="calculetteTotal();" style="width:30px" ').' %</td>
			<td class="barre">'.inputTag('text', 'prix_total', '', '', '16', '', ' id="total_Ajout_prod_dev"  readonly="readonly" style="width:80px" ').' € <img alt="alert" title="Vous risquez de vendre à perte !" src="../img/exclam.png" id="img_Ajout_prod_dev" style="display:none;"/></td>
			'.inputTag('hidden', 'mP', '', '', '', '', ' id="maxPrix_Ajout_prod_dev" ').'
			<td nowrap="nowrap" class="center">'.$action2.'
			</td>
			</tr>';
	$out .= '<input type="hidden" name="action" value="" id="actionDevisProd" />';
	$tva = $datas['data']['tva_dev']*$bigtotal/100;
	$ttc = $tva+$bigtotal;
	$tva = prepareNombreAffichage($tva);
	$ttc = prepareNombreAffichage($ttc);
	$bigtotal = prepareNombreAffichage($bigtotal);
	$out .= '<tr>
			<th rowspan="4" colspan="3"> </th>
			<th colspan="8"> </th>
		</tr>
		<tr class="titre noround">
			<th colspan="2">Total HT</th>
			<th id="TotalHT" colspan="2">'.$bigtotal.' €</th>
		</tr>
		<tr class="titre noround">
			<th colspan="2">TVA <span style="color: white;" id="TauxDeTVA">'.$datas['data']['tva_dev'].'%</span></th>
			<th id="TotalTVA" colspan="2">'.$tva.' €</th>
		</tr>
		<tr class="titre noround roundbottom">
			<th colspan="2" class="bordG">Total TTC</th>
			<th id="TotalTTC" colspan="2" class="bordD">'.$ttc.' €</th>
		</tr>
		</tbody></table>';
	return $out;
    }

    private function traitement($datas, $mess='') {
	$i=0;
	$input = array();
	$output= array();
	$cleanFrom = array("\n","\t","\r");
	$cleanTo  = array("\\n","\\t","\\r");
	foreach ($datas['data'] as $in => $out) {
	    $cleanFrom[] = "{".$in."}";
	    $cleanTo[] = $out;
	}
	loadPlugin(array('docGenerator'));
	foreach ($a = docGeneratorGetZunoConfInfo() as $in => $out) {
	    $cleanFrom[] = "{".$in."}";
	    $cleanTo[] = $out;
	}
	$outJS = '<script type="text/javascript">';
	$outJS .='Cannevas = new Array(); ';
	$outJS .='CannevasTitre = new Array(); ';
	foreach ($GLOBALS['ZunoSendMail'] as $key => $val) {
	    $k = explode('.',$key,2);
	    if($k[0] == 'texte') {
		$key = $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoSendMail']['dir.texte'].$k[1];
		$outJS .="\tCannevas[\"".$i."\"] = \"".str_replace($cleanFrom,$cleanTo,addslashes(trim(ProcessTemplating($key,$input,$output))))."\";\n";
		$outJS .="\tCannevasTitre[\"".$i."\"] = \"".str_replace($cleanFrom,$cleanTo,addslashes(trim($val)))."\";\n";
		$i++;
	    }
	}
	$outJS .= '</script>';

	$corps = '<div id="ContenuTraitementDevis" >'.$mess.$this->interneTraitement($datas).'</div>';
	if(verifDroitsAuto('devis', 62, $datas['data']['commercial_dev']))
	    $bouton = linkTag('javascript:document.ActionDevisPDF.submit()',imageTag('../img/prospec/devis.pdf.png','créer','middle').' Générer','','','onclick="ChangeDevisAction(\'Voir\')"');

	if (verifDroitsAuto('devis', 60, $datas['data']['commercial_dev'])) {
	    $bouton .= '<a onclick="ChangeDevisAction(\'Record\');zuno.business.formTools.sendFormAjah(\'ActionDevisPDF\', \'draco/Devis.php\',\'boxTraitementDevis\');" title="Enregistrer" name="enreg"><img align="middle" title="Enregistrer" alt="enreg" name="img" src="../img/prospec/devis.record.png"/> Enregistrer</a>';
	    if(verifDroitsAuto('devis', 50, $datas['data']['commercial_dev'])) {
		$bouton .= '<a onclick="ChangeDevisAction(\'RecordSend\');zuno.business.formTools.sendFormAjah(\'ActionDevisPDF\', \'draco/Devis.php\',\'boxTraitementDevis\');" title="Enregistrer puis envoyer" name="enregistrerEnvoyer"><img align="middle" title="Enregistrer et envoyer" alt="enregEnvoy" name="img" src="../img/prospec/devis.recsend.png"/> Enregistrer & envoyer</a>';
		$bouton .= '<a onclick="ChangeDevisAction(\'Send\');zuno.business.formTools.sendFormAjah(\'ActionDevisPDF\', \'draco/Devis.php\',\'boxTraitementDevis\');" title="Envoyer" name="envoyer"><img align="middle" title="Envoyer" alt="envoyer" name="img" src="../img/prospec/devis.send.png"/> Envoyer</a>';

	    }
	}
	// Quand status Envoyé
	if (($datas['data']['status_dev'] == "4")and(verifDroitsAuto('devis', 13, $datas['data']['commercial_dev']))) {
	    $bouton .= '<a title="Perdu" name="perdu" onclick="return zuno.popup.open(\'Devis.php\', \'devis='.$datas['data']['id_dev'].'&action=Perdu\',500,280);"><img align="middle" title="Perdu" alt="perdu" name="img" src="../img/prospec/devis.perdu.png"/> Devis perdu</a>';
	    $bouton   .= "<a name=\"valider\" onclick=\"return zuno.popup.open('Devis.php','id_devis=".$datas['data']['id_dev']."&action=valid',950,380,'','','resize','commandeCreate');\">".imageTag('../img/prospec/devis.valid.png','Validé')." Valider</a> ";
	}
	// Clonage
	if(verifDroitsAuto('devis', 25, $datas['data']['commercial_dev']))
	    $bouton .= '<a title="Cloner" href="Devis.php?action=cloner&dev='.$datas['data']['id_dev'].'"><img align="middle" title="Cloner" alt="cloner" name="img" src="../img/prospec/devis.clone.png"/> Cloner</a>';


	$titre = imageTag('../img/prospec/devis.record.png','créer')." Traitement du devis ".$datas['data']['id_dev'];

	return generateZBox($titre,$titre,$corps.$outJS,$bouton,'traitementDevis','');
    }
    private function interneTraitement($datas) {
	foreach ($GLOBALS['ZunoDevis'] as $key => $val) {
	    $k = explode('.',$key,2);
	    if ($k[0] == 'cannevas' and $k[1] != 'exportTableur')
		$toto[$key] = substr($key,strpos($key,".")+1,strlen($key));
	}
	$input = array();
	$output= array();
	$cleanFrom = array("\n","\t","\r");
	$cleanTo  = array("\\n","\\t","\\r");
	foreach ($GLOBALS['ZunoSendMail'] as $key => $val) {
	    $k = explode('.',$key,2);
	    if($k[0] == 'texte') {
		$toto2[] = $val;
	    }
	}

	loadPlugin(array('OOConverter'));
	$availableConvFormat = OOConverterAvailable('document');
	$value['OutputExt'] = ($value['OutputExt'] != '') ? $value['OutputExt'] : 'pdf';
	$listeDoc = array();
	$selected = "";
	if(verifDroitsAuto('devis', 50, $datas['data']['commercial_dev']))
	    $TypeSend['mail'] = "par e-mail";
	if(verifDroitsAuto('devis', 52, $datas['data']['commercial_dev']))
	    $TypeSend['fax'] = "par fax";
	if(verifDroitsAuto('devis', 54, $datas['data']['commercial_dev']))
	    $TypeSend['courrier'] = "par courrier";

	$fileName= $GLOBALS['ZunoDevis']['file.suffixe'] . $datas['data']['id_dev'];
	if($this->modelAffaire->isAffaireDirectoryFileExist($datas['data'],$fileName.'.pdf') and verifDroitsAuto('devis', 62, $datas['data']['commercial_dev'])) {
	    $listeDoc[$fileName.'.pdf'] = $fileName.'.pdf';
	    $docs .= '<div class="row"><div class="label"><a href="javascript:document.ActionDevisPDF.submit();" onclick="ChangeDevisAction(\'Zieuter\');changeDevisFormat(\'pdf\');"><img src="../img/files/pdf.png" alt="pdf" /></a></div><div class="field"><a href="javascript:document.ActionDevisPDF.submit();" onclick="ChangeDevisAction(\'Zieuter\');changeDevisFormat(\'pdf\');">'.$fileName.'.pdf</a></div></div>';
	    $selected = $fileName.'.pdf';
	}
	if($this->modelAffaire->isAffaireDirectoryFileExist($datas['data'],$fileName.'.odt') and verifDroitsAuto('devis', 62, $datas['data']['commercial_dev'])) {
	    $listeDoc[$fileName.'.odt'] = $fileName.'.odt';
	    $docs .= '<div class="row"><div class="label"><a href="javascript:document.ActionDevisPDF.submit();" onclick="ChangeDevisAction(\'Zieuter\');changeDevisFormat(\'odt\');"><img src="../img/files/document.png" alt="odt" /></a></div><div class="field"><a href="javascript:document.ActionDevisPDF.submit();" onclick="ChangeDevisAction(\'Zieuter\');changeDevisFormat(\'odt\');">'.$fileName.'.odt</a></div></div>';
	    $selected = ($selected == "") ? $fileName.'.odt' : $selected;
	}
	if($this->modelAffaire->isAffaireDirectoryFileExist($datas['data'],$fileName.'.doc') and verifDroitsAuto('devis', 62, $datas['data']['commercial_dev'])) {
	    $listeDoc[$fileName.'.doc'] = $fileName.'.doc';
	    $docs .= '<div class="row"><div class="label"><a href="javascript:document.ActionDevisPDF.submit();" onclick="ChangeDevisAction(\'Zieuter\');changeDevisFormat(\'doc\');"><img src="../img/files/document.png" alt="doc" /></a></div><div class="field"><a href="javascript:document.ActionDevisPDF.submit();" onclick="ChangeDevisAction(\'Zieuter\');changeDevisFormat(\'doc\');">'.$fileName.'.doc</a></div></div>';
	    $selected = ($selected == "") ? $fileName.'.doc' : $selected;
	}
	$out = '<form name="ActionDevisPDF" action="Devis.php" method="post">
		<input type="hidden" id="MakeDevisAction" value="ActionDevis" name="action"/>
		<input type="hidden" value="'.$datas['data']['id_dev'].'" name="id_dev"/>
		<input type="hidden" value="" name="format" id="formatDevisPDF" />
		'.inputTag('hidden', 'dir_aff', '', '', '', $datas['data']['dir_aff']).'
			<div class="block width50">
				<fieldset class="form">
					<legend>Génération des fichiers</legend>
					<div class="row">
						<div class="label" onmouseover="zuno.tooltip(this,ZTips.devis.generateCannevas);">Cannevas</div>
						<div class="field">'.selectTag("Cannevas", $toto, '', '', '', false).'</div>
					</div>
					<div class="row">
						<div class="label" onmouseover="zuno.tooltip(this,ZTips.devis.generateOutput);">Sortie</div>
						<div class="field">'.selectTag("OutputExt", $availableConvFormat, $value['OutputExt'], '', '', false).'</div>
					</div>
				</fieldset>
				<fieldset class="form"><legend>Visualisation des documents</legend>
					'.$docs.'
				</fieldset>
			</div>
			<div class="block width50">
				<fieldset class="form">
					<legend>Détail de l\'envoi</legend>
					<div class="row oblig">
						<div class="label" onmouseover="zuno.tooltip(this,ZTips.devis.sendType);">Type d\'envoi</div>
						<div class="field">'.selectTag('typeE', $TypeSend, '', '', 'onchange="montreForm(this.value);"',false).'</div>
					</div>
					<div class="row oblig">
						<div class="label" onmouseover="zuno.tooltip(this,ZTips.devis.sendSujet);">Sujet</div>
						<div class="field">'.inputTag('text', 'sujet','','','','','id="MessageTitle"').'</div>
					</div>
					<div class="row">
						<div class="label" onmouseover="zuno.tooltip(this,ZTips.devis.sendCannevas);">Cannevas</div>
						<div class="field">'.selectTag("cannevas", $toto2, '','',' id="CannevasListe" onchange="ChangeCannevas();"').'</div>
					</div>
					<div class="row">
						<div class="label" onmouseover="zuno.tooltip(this,ZTips.devis.sendMessage);">Message</div>
						<div class="field">'.textareaTag('message', '', '', '', '', 'id="MessageToSend"').'</div>
					</div>
					<div class="row">
						<div class="label" onmouseover="zuno.tooltip(this,ZTips.devis.sendPJ);">Fichier joint</div>
						<div class="field">'.selectTag('fichier', $listeDoc,$selected,"","",false).'</div>
					</div>
					
				</fieldset>
			</div>
			<div class="block width50">
				<fieldset class="form">
					<legend>Informations sur le destinataire</legend>
					<div class="row oblig" id="champEmail">
						<div class="label" onmouseover="zuno.tooltip(this,ZTips.devis.sendMail);">Courriel</div>
						<div class="field">'.inputTag('text', 'mail', '', '', '', $datas['data']['mail_cont']).'</div>
					</div>
                                        <div class="row" id="champCC">
						<div class="label">Me mettre en copie</div>
						<div class="field"><input type="checkbox" name="cc" value="'.$_SESSION['user']['mail'].'"  checked="checked"/></div>
					</div>
					<div class="row oblig" style="display:none;" id="champFax">
						<div class="label" onmouseover="zuno.tooltip(this,ZTips.devis.sendFax);">Fax</div>
						<div class="field">'.inputTag('text', 'fax', '', '', '', $datas['data']['fax_ent']).'</div>
					</div>
					<div class="row oblig" id="champDestinataire" style="display:none;">
						<div class="label" onmouseover="zuno.tooltip(this,ZTips.devis.sendDestinataire);">Destinataire</div>
						<div class="field">'.inputTag('text', 'destinataire', '', '', '', $datas['data']['civ_cont'].' '.$datas['data']['prenom_cont'].' '.$datas['data']['nom_cont']).'</div>
					</div>
					<div class="row oblig" style="display:none;" id="champAdd1">
						<div class="label" onmouseover="zuno.tooltip(this,ZTips.devis.sendAdd);">Adresse</div>
						<div class="field">'.inputTag('text', 'add1', '', '', '', $datas['data']['adressedelivery_dev']).'</div>
					</div>
					<div class="row" style="display:none;" id="champAdd2">
						<div class="label" onmouseover="zuno.tooltip(this,ZTips.devis.sendComplement);">Complément</div>
						<div class="field">'.inputTag('text', 'add2', '', '', '', $datas['data']['adresse1delivery_dev']).'</div>
					</div>
					<div class="row oblig" style="display:none;" id="champAdd3">
						<div class="label" onmouseover="zuno.tooltip(this,ZTips.devis.sendCpVille);">CP - Ville</div>
						<div class="field">'.inputTag('text', "cp", 'civ', '', '', $datas['data']['cpdelivery_dev'], 'onchange="zuno.ajax.get.html(\'../ajaxRef.php\',\'action=villeForCp&cp=\'+this.value,\'envoiVille\');"').
		'<select name=\'ville\'  id="envoiVille"  onblur="finishEditing();" onclick="beginEditing(this);" class="nextciv"><option value=""> </option><option value="'.$datas['data']['villedelivery_dev'].'" selected="selected">'.$datas['data']['villedelivery_dev'].'</option></select></div>
					</div>
					<div class="row oblig" style="display:none;" id="champAdd4">
						<div class="label" onmouseover="zuno.tooltip(this,ZTips.devis.sendPays);">Pays</div>
						<div class="field">'.selectTag('pays',$datas['pays'],$datas['data']['paysdelivery_dev']).'</div>
					</div>
				</fieldset>
			</div>
		</form>';
	return $out;
    }

    public function searchResult($datas, $light='') {
	if($light == 'result')
	    return $this->Result($datas);
	else
	    return $this->searchForm($datas).$this->listeResult($datas);
    }

    private function searchForm($datas) {
	$script = '<script type="text/javascript">new Ajax.Autocompleter("cpDevisSearch", "cpDevisSearch_choix", "../ajaxRef.php?action=listeCp", { paramName: "value", minChars: 2, indicator : "loadingCp" , afterUpdateElement : autoCompleteHidden } ); ';
	$script .= 'new Ajax.Autocompleter("affaireDevisSearch", "affaireDevisSearch_choix", "../ajaxRef.php?action=listeAffaire", { paramName: "value", minChars: 2, indicator: "loadingAff" , afterUpdateElement : autoCompleteHidden } ); ';
	$script .= 'new Ajax.Autocompleter("entrepriseDevisSearch", "entrepriseDevisSearch_choix", "../ajaxRef.php?action=listeEntreprise", { paramName: "value", minChars: 2, indicator : "loadingEnt" , afterUpdateElement : autoCompleteHidden } ); ';
	$script .= 'new Ajax.Autocompleter("userDevisSearch", "userDevisSearch_choix", "../ajaxRef.php?action=listeUser", { paramName: "value", minChars: 2, indicator: "loadingUsr", afterUpdateElement : autoCompleteHidden } ); ';
	$script .= 'function ChangeDevisID(value) { $(\'idDevisSearch\').value = value; } ';
	$script .= 'function verifVide(objet) { if(objet.value == "") { $(objet.id+\'hidden\').value = ""; } } ';
	$script.= "function ChangeDevisResultPage(limit,from,order,sens) {
			ChangeDevisAction('searchDevis');
			if(limit != undefined) ChangeLimit(limit);
			if(from  != undefined) ChangeFrom(from);
			if(order != undefined) ChangeOrder(order,sens);
			ChangeSubmit();
		    }
		    function ChangeDevisAction(value) {\$('listeDevisAction').value = value;}
		    function ChangeFrom(value) { \$('fromDevisSearch').value = value; }
		    function ChangeLimit(value) { \$('limitDevisSearch').value = value; }
function ChangeOrder(value,sens) {
if(sens == undefined) sens = 'ASC';
\$('orderDevisSearch').value = value;
\$('orderSensDevisSearch').value = sens;
}
function ChangeSubmit() {
zuno.business.formTools.sendFormAjah('searchDevis', 'draco/DevisListe.php','listeResultDevis');
}
		    Event.observe(document, 'keypress', function(event){ if(event.keyCode == Event.KEY_RETURN) ChangeSubmit(); return false;}, false);
		    ";
	$script .= 'function camenbertDevis(value, max) { $(\'devisBudgetMini\').value = value; $(\'devisBudgetMaxi\').value = max; ChangeDevisAction(\'searchDevis\');zuno.business.formTools.sendFormAjah(\'searchDevis\', \'draco/DevisListe.php\',\'listeResultDevis\'); } ';
	$script .= 'function resetForm() {var form = $(\'searchFormDevis\'); form.reset(); form.select(\'input[type="hidden"]\').each(function(input) {input.clear();}); } ';
	$script .= 'function exportTab() { if ($(\'divExportTableur\').style.display == \'inline\') {$(\'divExportTableur\').style.display = \'none\';} else {$(\'divExportTableur\').style.display = \'inline\';} }';
	$script .= '</script>';

	$titre = '<img title="recherche" alt="Rechercher" name="img" src="../img/prospec/voir.png" /> Recherche de devis';
	$corps = '<form name="searchDevis" action="DevisListe.php" method="post" id="searchFormDevis">';
	$corps .= '<div id="PortletSearchDevis">';
	$corps .= '<input type="hidden" name="action" value="searchDevis" id="listeDevisAction" />';
	$corps .= '<input type="hidden" name="id_dev" value="" id="idDevisSearch" />';
	$corps .= '<input type="hidden" name="from" value="0" id="fromDevisSearch" />';
	$corps .= '<input type="hidden" name="limit" value="30" id="limitDevisSearch" />';
	$corps .= '<input type="hidden" name="order" value="id_dev" id="orderDevisSearch" />';
	$corps .= '<input type="hidden" name="orderSens" value="DESC" id="orderSensDevisSearch" />';
	$corps .= $this->interneSearch($datas);
	$corps .= '</div></form>';
	$bouton = '<a name="reset" onclick="resetForm();"><img title="Reset" alt="reset" src="../img/prospec/cancel.png"/> Annuler</a>';
	$bouton .= '<a name="valider" onclick="ChangeDevisAction(\'searchDevis\');ChangeSubmit();"><img title="Valider" alt="valid" src="../img/prospec/voir.png" />Rechercher</a>';
	return generateZBox($titre,$titre,$corps.$script,$bouton,"searchBox",'').$script;
    }

    private function interneSearch($datas) {
	$out = '<div class="block width50"><fieldset class="form" style="height: 220px"><legend>Critères</legend>';
	$out .= '<div class="row"><div class="label" onmouseover="zuno.tooltip(this,ZTips.devis.searchEnt);">Entreprise</div>';
	$out .= '<div class="field">'.inputTag('text', 'entreprise', '', '', '20', '', ' id="entrepriseDevisSearch" onchange="verifVide(this);"').'<img style="display:none; margin-left:5px;" id="loadingEnt" src="../img/ajax-loader.gif" alt="loader"/></div><div class="autocomplete" id="entrepriseDevisSearch_choix"></div>';
	$out .= '<input type="hidden" name="entreprise_dev" value="" id="entrepriseDevisSearchhidden"/></div>';
	$out .= '<div class="row"><div class="label" onmouseover="zuno.tooltip(this,ZTips.devis.searchTitre);">Titre</div>';
	$out .= '<div class="field">'.inputTag('text', 'titre_dev', '', '', '20').'</div></div>';
	$out .= '<div class="row"><div class="label" onmouseover="zuno.tooltip(this,ZTips.devis.searchAff);">Affaire</div>';
	$out .= '<div class="field">'.inputTag('text', 'affaire', '', '', '20', '', ' id="affaireDevisSearch" onchange="verifVide(this);"').'<img style="display:none; margin-left:5px;" id="loadingAff" src="../img/ajax-loader.gif" alt="loader"/></div><div class="autocomplete" id="affaireDevisSearch_choix"></div>';
	$out .= '<input type="hidden" name="affaire_dev" value="" id="affaireDevisSearch_hidden"/></div>';
	$out .= '<div class="row"><div class="label" onmouseover="zuno.tooltip(this,ZTips.devis.searchStatut);">Statut</div>';
	$out .= '<div class="field">'.selectTag('status_dev',$datas['status'],'').'</div></div>';
	$out .= '<div class="row"><div class="label" onmouseover="zuno.tooltip(this,ZTips.devis.searchComm);">Commercial</div>';
	$out .= '<div class="field">'.inputTag('text', 'commercial', '', '', '20', '', ' id="userDevisSearch" onchange="verifVide(this);"').'<img style="display:none; margin-left:5px;" id="loadingUsr" src="../img/ajax-loader.gif" alt="loader"/></div><div class="autocomplete" id="userDevisSearch_choix"></div>';
	$out .= '<input type="hidden" name="commercial_dev" value="" id="userDevisSearch_hidden"/></div>';
	$out .= '<div class="row"><div class="label" onmouseover="zuno.tooltip(this,ZTips.devis.searchCP);">Département</div>';
	$out .= '<div class="field">'.inputTag('text', 'cp', '', '', '20', '', ' id="cpDevisSearch" onchange="verifVide(this);"').'<img style="display:none; margin-left:5px;" id="loadingCp" src="../img/ajax-loader.gif" alt="loader"/></div><div class="autocomplete" id="cpDevisSearch_choix"></div>';
	$out .= '<input type="hidden" name="cp_ent" value="" id="cpDevisSearch_hidden"/></div>';
	$out .= '<div class="row"><div class="label" onmouseover="zuno.tooltip(this,ZTips.devis.searchBudgMin);">Budget min</div>';
	$out .= '<div class="field">'.inputTag('text', 'sommeHT_dev', '', '', '20', '', ' id="devisBudgetMini" ').'</div></div>';
	$out .= '<div class="row"><div class="label" onmouseover="zuno.tooltip(this,ZTips.devis.searchBudgMax);">Budget maxi</div>';
	$out .= '<div class="field">'.inputTag('text', 'sommeHT_dev2', '', '', '20', '', ' id="devisBudgetMaxi" ').'</div></div>';
	$out .= '</fieldset></div>';
	$out .= '<div class="block width50"><fieldset class="form" style="height: 220px"><legend>Répartition des devis</legend>';
	$out .= '<div class="row">'.shell_exec('php ../Img.DevisChart.php').'</div>';
	$out .= '</fieldset></div>';
	return $out;
    }

    public function listeResult($datas) {
	return '<div id=listeResultDevis>'.$this->Result($datas).'</div>';
    }

    private function Result($datas) {
	$suffixeLink = '../';
	$modHistorique = false;
	$linkOrderFunction = 'tagLinkOrderSwitch';
	if (array_key_exists('isHistoVisit',$datas) and $datas['isHistoVisit']) {
	    $suffixeLink = './';
	    $linkOrderFunction = 'tagNoLinkOrderSwitch';
	    $modHistorique = true;
	}

	$out = "<script type=\"text/javascript\">
			function devisDoExportTableur(value) {
			    $('resultDevisAction').value = 'exportTableur';
			    $('resultDevisExportType').value = value;
			    $('resultFormDevis').submit();
			}
			function showCmd()
			{
				$('commandePopupForm').style.display = 'block';
				$('facturePopupForm').style.display = 'none';
			}
			function showFact()
			{
				$('commandePopupForm').style.display = 'none';
				$('facturePopupForm').style.display = 'block';
			}

			</script>";
	$out .= '<div class="blockTable">
		    <form id="resultFormDevis" method="post" action="DevisListe.php" name="resultDevis">
		    <input type="hidden" name="action" value="" id="resultDevisAction" />
		    <input type="hidden" name="exportType" value="" id="resultDevisExportType" />
		    <table cellspacing="0"><tbody>';
	$out .= '<tr class="titre">';
	if(!$modHistorique)
	    $out .= '<th class="barre"><img src="../img/prospec/multiple-check.png" onclick="toggleCheckbox(\'resultFormDevis\');"/></th>';
	$out .= '<th class="barre">'.$this->$linkOrderFunction('ID','id_dev',$datas['order'],$datas['orderSens'],$datas['limit'],'ChangeDevisResultPage',$suffixeLink).'</th>';
	$out .= '<th class="center barre">'.$this->$linkOrderFunction('Entreprise','nom_ent',$datas['order'],$datas['orderSens'],$datas['limit'],'ChangeDevisResultPage',$suffixeLink).'</th>';
	$out .= '<th class="center barre">'.$this->$linkOrderFunction('Contact','c1.nom_cont',$datas['order'],$datas['orderSens'],$datas['limit'],'ChangeDevisResultPage',$suffixeLink).'</th>';
	$out .= '<th>'.$this->$linkOrderFunction('Titre','titre_dev',$datas['order'],$datas['orderSens'],$datas['limit'],'ChangeDevisResultPage',$suffixeLink).'</th>';
	$out .= '<th>'.$this->$linkOrderFunction('Montant HT','sommeHT_dev',$datas['order'],$datas['orderSens'],$datas['limit'],'ChangeDevisResultPage',$suffixeLink).'</th>';
	$out .= '<th class="barre">'.$this->$linkOrderFunction('Status','status_dev',$datas['order'],$datas['orderSens'],$datas['limit'],'ChangeDevisResultPage',$suffixeLink).'</th>';
	if(!$modHistorique)
	    $out .= '<th class="last center">Actions</th>';
	$out .= '</tr>';
	$alternance =  0;
	if(count($datas['data']) > 0)
	    $totalDisplay =0;
	foreach($datas['data'] as $v) {
	    $action = '';
	    //traitement des boutons de modification et d'action
	    // recherche du fichier PDF
	    if (file_exists( $this->modelAffaire->getAffaireDirectoryPath($v).$GLOBALS['ZunoDevis']['file.suffixe'].$v['id_dev'].'.pdf') and verifDroitsAuto('devis', 62, $v['commercial_dev']))
		$action   .= "<a href=\"${suffixeLink}draco/Devis.php?id_devis=".$v['id_dev']."&action=ZieuterPDF\">".imageTag($suffixeLink.'img/prospec/devis.pdf.png','PDF')."</a> ";
	    // Clonage
	    if(verifDroitsAuto('devis', 25, $v['commercial_dev']))
		$action .= '<a title="Cloner" href="Devis.php?action=cloner&dev='.$v['id_dev'].'"><img align="middle" title="Cloner" alt="cloner" name="img" src="'.$suffixeLink.'img/prospec/devis.clone.png"/></a>';
	    // Quand status créer
	    if (($v['status_dev'] == "1")and(verifDroitsAuto('devis', 60, $v['commercial_dev']))) {
		$action   .= "<a href=\"#\" onclick=\"return zuno.popup.open('${suffixeLink}draco/Devis.php','id_devis=".$v['id_dev']."&action=supp',480,130);\">".imageTag($suffixeLink.'img/prospec/devis.delete.png','Supprimer')."</a> ";
		$action .= '<a href="Devis.php?id_dev='.$v['id_dev'].'" title="Enregistrer" name="enreg"><img align="middle" title="Enregistrer" alt="enreg" name="img" src="'.$suffixeLink.'img/prospec/devis.record.png"/></a>';
	    }
	    // Quand status Enregistré
	    elseif (($v['status_dev'] == "3")and(verifDroitsAuto('devis', 50, $v['commercial_dev'])))
		$action .= '<a href="Devis.php?id_dev='.$v['id_dev'].'" title="Envoyer" name="envoyer"><img align="middle" title="Envoyer" alt="envoyer" name="img" src="'.$suffixeLink.'img/prospec/devis.send.png"/></a>';
	    // Quand status Envoyé
	    elseif (($v['status_dev'] == "4")and(verifDroitsAuto('devis', 13, $v['commercial_dev']))) {
		$action .= '<a title="Perdu" name="perdu" onclick="ChangeDevisAction(\'Perdu\');ChangeDevisID(\''.$v['id_dev'].'\');zuno.business.formTools.sendFormAjah(\'searchDevis\', \'draco/DevisListe.php\',\'listeResultDevis\');"><img align="middle" title="Perdu" alt="perdu" name="img" src="'.$suffixeLink.'img/prospec/devis.perdu.png"/></a>';
		$action .= "<a name=\"valider\" onclick=\"return zuno.popup.open('$suffixeLink/draco/Devis.php','id_devis=".$v['id_dev']."&action=valid',950,380);\">".imageTag($suffixeLink.'img/prospec/devis.valid.png','Validé')."</a> ";
	    }
	    if (array_key_exists('isRenew',$datas) and $datas['isRenew']) {
		if (($v['devisgenere'] != ""))
		    $action = "<a href=\"Devis.php?id_dev=".$v['devisgenere']."\">".imageTag('../img/prospec/devis.png','Voir')."</a> ";
		else $action = "<a href=\"${suffixeLink}draco/Devis.php?id_devis=".$v['id_dev']."&action=clone&renew=yes\">".imageTag('../img/prospec/devis.record.png','Créer')."</a> ";

		$diffDate = DateUniv2Timestamp($v['date_renew'])-DateUniv2Timestamp('');
		if ($v['devisgenere'] != "")
		    $v['color_stdev'] = "2c1";
		elseif ($diffDate > 1209600)
		    $v['color_stdev'] = "44a";
		elseif ($diffDate > 0)
		    $v['color_stdev'] = "56b";
		else $v['color_stdev'] = "d34";
	    }
	    if ($v['nom_ent'] != '') {
		if (strlen($v['nom_ent']) > 25)
		    $v['nom_ent'] = substr($v['nom_ent'],0,25)."...";
		if ($v['type_ent'] != '')
		    $v['nom_ent']	= imageTag($suffixeLink.'img/'.$GLOBALS['PropsecConf']['dir.img'].'TypeEntreprise/'.$v['type_ent'].'.png',$v['nom_tyent']).' '.$v['nom_ent'];
		if (strlen($v['ville_ent']) > 18)
		    $v['ville_ent'] = substr($v['ville_ent'],0,18)."...";
		if ($v['ville_ent'] != '')
		    $v['ville_ent'] = ' ('.$v['ville_ent'].')';
		$v['contact'] = $v['nom_ent'].$v['ville_ent'];
	    }
	    else {
		$v['nom_ent'] = '<i>Particulier</i>';
		$v['nom_ent']	= imageTag($suffixeLink.'img/'.$GLOBALS['PropsecConf']['dir.img'].'TypeEntreprise/particulier.png','particulier').' '.$v['nom_ent'];
		if (strlen($v['ville_cont']) > 18)
		    $v['ville_cont'] = substr($v['ville_cont'],0,18)."...";
		if ($v['ville_cont'] != '')
		    $v['ville_cont'] = ' ('.$v['ville_cont'].')';
		$v['contact'] = $v['nom_ent'].$v['ville_cont'];
	    }

	    $out .= '<tr class="altern'.$alternance.'">';
	    if(!$modHistorique)
		$out .= '<td class="barre" style="width:10px"><input type="checkbox" name="select[]" value="'.$v['id_dev'].'" checked="checked"/></td>';
	    $out .= '<td class="barre" style="cursor : pointer; color : #'.$v['color_stdev'].' !important;" onclick="window.location=\''.$suffixeLink.'draco/Devis.php?id_dev='.$v['id_dev'].'\'"><b>'.$v['id_dev'].'</b></td>';
	    $out .= '<td class="barre" style="cursor : pointer; color : #'.$v['color_stdev'].' !important;" onclick="window.location=\''.$suffixeLink.'draco/Devis.php?id_dev='.$v['id_dev'].'\'">'.$v['contact'].'</td>';
	    $out .= '<td class="barre" style="cursor : pointer; color : #'.$v['color_stdev'].' !important;" onclick="window.location=\''.$suffixeLink.'draco/Devis.php?id_dev='.$v['id_dev'].'\'">'.$v['civ_cont'].' '.$v['prenom_cont'].' '.$v['nom_cont'].'</td>';
	    $out .= '<td class="barre" style="cursor : pointer; color : #'.$v['color_stdev'].' !important;" onclick="window.location=\''.$suffixeLink.'draco/Devis.php?id_dev='.$v['id_dev'].'\'">'.$v['titre_dev'].'</td>';
	    $out .= '<td class="right barre" style="cursor : pointer; color : #'.$v['color_stdev'].' !important;" onclick="window.location=\''.$suffixeLink.'draco/Devis.php?id_dev='.$v['id_dev'].'\'">'.formatCurencyDisplay($v['sommeHT_dev']).'</td>';
	    $out .= '<td class="barre" style="cursor : pointer; color : #'.$v['color_stdev'].' !important;" onclick="window.location=\''.$suffixeLink.'draco/Devis.php?id_dev='.$v['id_dev'].'\'"><div class="square" style="background-color:#'.$v['color_stdev'].';">&nbsp;</div>'.$v['nom_stdev'].'</td>';
	    if(!$modHistorique)
		$out .= '<td class="right">'.$action.'</td>';
	    $out .= '</tr>';
	    $alternance = (($alternance+1) % 2);
	    $totalDisplay += $v['sommeHT_dev'];
	}
	if(!$modHistorique) {
	    $out .= '<tr class="titre noround roundbottom">';
	    $out .= '<td colspan="5"></td>';
	    $out .= '<th class="bordG bordD right">'.prepareNombreAffichage($totalDisplay).' &euro;</th>';
	    $out .= '<td colspan="2"></td>';
	    $out .= '</tr>';
	}
	$out .= '</tbody></table>';
	if(!$modHistorique)
	    $out .= $this->ResultGroupActionForm();
	$out .= '</form></div>';
	if($modHistorique)
	    $titre = $this->resultNavigationTitle($datas['from'],$datas['limit'],$datas['total'],'devisHisto');
	else $titre = $this->resultNavigationTitle($datas['from'],$datas['limit'],$datas['total'],'devis');

	$bouton .= $this->resultNavigation($datas['from'],$datas['limit'],$datas['total'],"ChangeDevisResultPage",$suffixeLink);
	loadPlugin(array('OOConverter'));
	$availableConvFormat = OOConverterAvailable('spreadsheet');
	$bouton .= '<div style="display:none;" id="divExportTableur">'.selectTag('format_export', $availableConvFormat, '', '', ' onchange="devisDoExportTableur(this.value);" ').'</div>';
	$bouton .= '<a name="export" style="cursor:pointer;" onclick="exportTab();"><img title="exportation tableur" alt="exportation tableur" src="'.$suffixeLink.'img/prospec/export-csv.png"/> Export Tableur</a>';
	$bouton .= $this->ResultGroupActionBouton($suffixeLink);

	if($modHistorique)
	    $bouton = '';
	return generateZBox($titre,$titre,$out,$bouton,"searchListeDevis",'');
    }



    private function ResultGroupActionForm() {
	$out = '<div id="divGroupAction" style="display: none"><div class="block width50">';
	$fieldset = new ZunoFieldset('Type d\'action','','height: 150px');
	$fieldset->ligneFree(inputTag('radio', 'groupedAction', '', '', '', 'reinit', ' onclick="toggleSubGroupedAction(\'groupedSubActionReinit\')"'),'Re-initialiser ces devis','ZTips.devis.groupActionReinit');
	$fieldset->ligneFree(inputTag('radio', 'groupedAction', '', '', '', 'perdu', ' onclick="toggleSubGroupedAction(\'groupedSubActionPerdu\')"'),'Marquer ces devis comme perdus','ZTips.devis.groupActionPerdu');
	$fieldset->ligneFree(inputTag('radio', 'groupedAction', '', '', '', 'envoye', ' onclick="toggleSubGroupedAction(\'groupedSubActionEnvoye\')"'),'Marquer ces devis comme envoyés','ZTips.devis.groupActionEnvoye');
	$fieldset->ligneFree(inputTag('radio', 'groupedAction', '', '', '', 'delete', ' onclick="toggleSubGroupedAction(\'groupedSubActionDelete\')"'),'Marquer ces devis comme supprimés','ZTips.devis.groupActionDelete');
	$fieldset->ligneFree(inputTag('radio', 'groupedAction', '', '', '', 'archivate', ' onclick="toggleSubGroupedAction(\'groupedSubActionArchive\')"'),'Archivage des devis','ZTips.devis.groupActionArchive');
	$fieldset->ligneFree(inputTag('radio', 'groupedAction', '', '', '', 'changeAttribute', ' onclick="toggleSubGroupedAction(\'groupedSubActionChangeAttribute\')"'),'Changer un attribut par lot','ZTips.devis.groupActionChangeAttribute');
	$out.= $fieldset->generateFieldset();
	$out.= '</div><div class="block width50"><div id="groupedSubAction">';
	$fieldset = new ZunoFieldset('Re-initialiser ces devis','groupedSubActionReinit','height: 90px');
	$fieldset->addOtherLigne('<div class="row"><i>Aucune action sécifique n\'est requise</i></div>');
	$out.= $fieldset->generateFieldset();
	$fieldset = new ZunoFieldset('Marquer ces devis comme perdus','groupedSubActionPerdu','height: 90px');
	$fieldset->ligneInput('Commentaire', 'commentairePerdu');
	$out.= $fieldset->generateFieldset();
	$fieldset = new ZunoFieldset('Marquer ces devis comme envoyés','groupedSubActionEnvoye','height: 90px');
	$fieldset->ligneInput('Complément d\'envoi', 'complementdeliveryEnvoye');
	$fieldset->ligneInput('Commentaire', 'commentaireEnvoye');
	$out.= $fieldset->generateFieldset();
	$fieldset = new ZunoFieldset('Marquer ces devis comme supprimés','groupedSubActionDelete','height: 90px');
	$fieldset->ligneInput('Commentaire', 'commentaireDelete');
	$out.= $fieldset->generateFieldset();
	$fieldset = new ZunoFieldset('Archivage des devis','groupedSubActionArchive','height: 90px');
	$fieldset->addOtherLigne('<div class="row"><i>Seul les devis n\'ayant plus de commande ou facture en cours de traitement seront archivées.</i></div>');
	$out.= $fieldset->generateFieldset();
	$fieldset = new ZunoFieldset('Changer un attribut par lot','groupedSubActionChangeAttribute','height: 90px');
	$fieldset->ligneInput('Titre du devis', 'titre_dev');
	$fieldset->ligneFree('TVA',selectTVATag("tva_dev", array(), '', '', '', true));
	$fieldset->ligneSelect('Commercial', 'commercial_dev','',$this->model->getUser());
	$out.= $fieldset->generateFieldset();
	$out.= '</div>';
	$fieldset = new ZunoFieldset('Executer le changement');
	$fieldset->ligneLinkTo('','javascript:submitGroupAction(\'resultFormDevis\',\'resultDevisAction\');" class="bouton','Effectuer les changements','../img/prospec/voir.png');
	$out.= $fieldset->generateFieldset();
	$out.= '</div></div></div>';
	return $out;
    }

    private function ResultGroupActionBouton($suffixeLink) {
	$bouton = '<a name="groupAction" style="cursor:pointer;" onclick="toggleGroupedAction(\'divGroupAction\');"><img title="actions groupées" alt="actions groupées" src="'.$suffixeLink.'img/prospec/work-multiple.png"/> Actions groupées</a>';
	return $bouton;
    }



    public function creerDevis($mess = '', $data = array()) {

	$script = '<script type="text/javascript">new Ajax.Autocompleter("ACentreprise_dev", "ACentreprise_dev_choix", "../ajaxRef.php?action=listeEntAffDevis", { paramName: "value", minChars: 2, indicator : "loadingEnt", afterUpdateElement : autoCompleteChamps } );  ';
	$script .= "\n".'contact_completer = new Ajax.Autocompleter("ACcontact_dev", "ACcontact_dev_choix", "../ajaxRef.php?action=listeContact", { paramName: "value", minChars: 2, indicator : "loadingcontact_dev", afterUpdateElement : autoCompleteHiddenBis } ); ';
	$script .= "\n".'contact2_completer = new Ajax.Autocompleter("ACcontact_achat_dev", "ACcontact_achat_dev_choix", "../ajaxRef.php?action=listeContact", { paramName: "value", minChars: 2, indicator : "loadingcontact_achat_dev", afterUpdateElement : autoCompleteHiddenBis } ); ';
	if($mess != '' or $data != array())
	    $script .= "\n".'contact_completer.options.defaultParams=\'id_ent=\'+$F(\'ACentreprise_dev_hidden\'); contact2_completer.options.defaultParams=\'id_ent=\'+$F(\'ACentreprise_dev_hidden\'); ';
	else
	    $script .= "\n";
	$script .= "\n".'function autoCompleteChamps(text, li) { id = li.title.split(\'-_-\'); $(\'ACaffaire_dev_hidden\').value = id[0]; $(\'ACentreprise_dev_hidden\').value = id[1]; $(\'devisadd\').value = id[2]; $(\'devisadd1\').value = id[3]; $(\'IDcpdelivery_dev\').value = id[4]; $(\'IDvilledelivery_dev\').options[0]= new Option(id[5], id[5]); $(\'ACpaysdelivery_devhidden\').value = id[6];  $(\'ACpaysdelivery_dev\').value = id[7];$(\'ACcontact_devhidden\').value = $(\'ACcontact_achat_devhidden\').value = id[8]; $(\'ACcontact_dev\').value = $(\'ACcontact_achat_dev\').value = id[9]+\' \'+id[10]+\' \'+id[11]; contact_completer.options.defaultParams=\'id_ent=\'+$F(\'ACentreprise_dev_hidden\'); contact2_completer.options.defaultParams=\'id_ent=\'+$F(\'ACentreprise_dev_hidden\'); $(\'ACcontact_devAddImg\').style.display="inline"; $(\'ACcontact_achat_devAddImg\').style.display="inline"; } ';
	$script .= "\n".'function autoCompleteHiddenBis(text, li) {$(text.id+\'hidden\').value = li.title; if(li.getAttribute(\'value\')) {var id = li.getAttribute(\'value\').split(\'-_-\'); $(\'devisadd\').value = id[0]; $(\'devisadd1\').value = id[1]; $(\'IDcpdelivery_dev\').value = id[2]; $(\'IDvilledelivery_dev\').options[0]= new Option(id[3], id[3]); $(\'ACpaysdelivery_devhidden\').value = id[4]; $(\'ACpaysdelivery_dev\').value = id[5];} }';
	$script .= "\n".'function verifVide(objet) { if(objet.value == "") { if(objet.id == \'ACentreprise_dev\') {$(\'boutonSubmitForm\').style.display = \'none\';} { $(objet.id+\'hidden\').value = ""; } } } ';
	$script .= "\n".'function resetForm() {var form = $(\'formAddDevis\'); form.reset(); form.select(\'input[type="hidden"]\').each(function(input) {input.clear();}); } ';
	$script .= "\n".'function verifAutocompleteur(champ) {if(champ.value == "" || champ.value == null) {$(champ.id+\'hidden\').value = "";} else if(!$(champ.id+\'_choix\').hasChildNodes()) {alert(\'Cette valeur n\\\'existe pas.\'); champ.value=$(champ.id+\'old\').value; setTimeout(function() { champ.focus(); }, 100) } } ';
	$script .= "\n".'function verifAutocompleteurBis(champ) {if(champ.value == "" || champ.value == null) {$(\'ACentreprise_dev_hidden\').value = ""; $(\'ACaffaire_dev_hidden\').value = "";} }';
	$script .= "\n".'function addContact(idRetour) { var ent=\'action=addContDev&entreprise=\'+$(\'ACentreprise_dev_hidden\').value+\'&idRetour=\'+idRetour; zuno.popup.open(\'Devis.php\', ent, 800, 300);} ';
	$script .= "\n".'function addEntreprise() { zuno.popup.open(\'Devis.php\', \'action=addEnt\',800,320);} ';
	$script .= "\n".'</script>';
	$rendu = new ZunoRenduHtml('addNewDevis');
	$form = new ZunoForm('addDevis', 'DevisCreate.php', 'POST');
	$fieldset = new ZunoFieldset('Client et titre du devis','','height: 100px');
	$ligne = '<div class="row">';
	$ligne .= '<div class="label" onmouseover="zuno.tooltip(this,ZTips.devis.addEntAff);">Entreprise ou affaire</div>';
	$ligne .= '<div class="field">'.inputTag('text', 'entreprise', 'icon', '', '16', $data['entreprise'], ' id="ACentreprise_dev" onchange="verifAutocompleteurBis(this);"');
	$ligne .= '<a id="addEnt" name="addEnt" title="Ajouter une entreprise" onclick="addEntreprise();"><img alt="ent" title="Ajouter une entreprise" src="../img/prospec/entreprise.png" /></a>';
	$ligne .= '<img style="display:none; margin-left:5px;" id="loadingEnt" src="../img/ajax-loader.gif" alt="loader"/></div>';
	$ligne .= '<input type="hidden" name="entreprise_dev" id="ACentreprise_dev_hidden" value="'.$data['entreprise_dev'].'" /><input type="hidden" name="affaire_dev" id="ACaffaire_dev_hidden" value="'.$data['affaire_dev'].'" />
		   <div id="ACentreprise_dev_choix" class="autocomplete"></div>';
	$ligne .= '</div>';
	$fieldset->addOtherLigne($ligne);
	$fieldset->ligneInput('Titre', 'titre_dev', $data['titre_dev']);
	$form->newBlock($fieldset->generateFieldset(), '33');
	$fieldset = new ZunoFieldset('Contacts du devis','','height: 100px');
	$fieldset->ligneAutoComplete('Contact commercial', 'contact_dev', $data['contact_devaff'], $data['contact_dev'], 'listeContact', '', true, 'addContact', false);
	$fieldset->ligneAutoComplete('Contact facturation', 'contact_achat_dev', $data['contact_achat_devaff'], $data['contact_achat_dev'], 'listeContact', '', true, 'addContact', false);
	$form->newBlock($fieldset->generateFieldset(), '33');
	$fieldset = new ZunoFieldset('Adresse de livraison','','height: 100px');
	$fieldset->ligneInput('Adresse', 'adressedelivery_dev', $data['adressedelivery_dev'], ' id="devisadd" ');
	$fieldset->ligneInput('Complément', 'adresse1delivery_dev', $data['adresse1delivery_dev'], ' id="devisadd1" ');
	$fieldset->ligneInputCPVille('cpdelivery_dev', $data['cpdelivery_dev'], 'villedelivery_dev', $data['villedelivery_dev']);
	$fieldset->ligneAutoComplete('Pays', 'paysdelivery_dev', $data['paysdelivery_devaff'], $data['paysdelivery_dev'], 'listePays');
	$form->newBlock($fieldset->generateFieldset(), '33');
	$form->newInputHidden('action', 'addDevis');
	$rendu->replaceJS($script);
	$rendu->generateZBox(imageTag('../img/prospec/devis.png','Devis').' Nouveau Devis','',$form->generateForm(), $form->generateButtonsNoAjax('Annuler', 'Enregistrer'), 'idCreationDevis', 'open');
	if($mess != '')
	    $messAff = '<span class="important" style="text-align:center;" id="spanErrorDevis">'.$mess.'</span> <script>setTimeout(function(){$(\'spanErrorDevis\').style.display = \'none\';}, 2000)</script>';
	else $messAff = '';
	return $messAff.$rendu->generateRendu();

    }

    public function popupActu($datas) {
	$alternance = 0;

	$titre = 'Actualités';

	$corps = '<div class="blockTable">';
	$corps .= '<table width="100%" cellspacing="0" cellpadding="2" border="0">';
	$corps .= '<tbody><tr class="titre">';
	$corps .= '<th></th><th>Date</th><th>Titre</th><th>Utilisateur</th>';
	$corps .= '</tr>';

	foreach($datas as $v) {
	    $corps .= '<tr class="altern'.($alternance % 2).'">
			    <td class="nowrap"><img src=\'../img/actualite/'.$v['type'].'.png\' alt=\''.$v['type'].'\'/>'.$v['type'].'</td>
			    <td class="barre nowrap">'.DateUniv2Human($v['date'],'shortdetail').'</td>
			    <td class="barre"><b>'.$v['titre'].'</b><br/><span style="font-size:.8em;font-style:italic;">'.$v['desc'].'</span></td>
			    <td class="barre nowrap">'.$v['user'].'</td></tr>';
	    $alternance++;
	}
	$corps .= '</tbody></table></div>';

	return generateZBox($titre,$titre,$corps,'',"popupAffaireActu",'');
    }

    public function popupPerdu($id) {
	$fieldset = new ZunoFieldset("Options");
	$fieldset->ligneCheckBox('Cloturer l\'affaire liée', 'close_aff');
	$fieldset->ligneCheckBox('Archiver l\'affaire liée', 'archive_aff');
	$fieldset->ligneTArea("Motif", "motif");

	$form = new ZunoForm('perdreDevis', "draco/Devis.php", "POST");
	$form->newInputHidden("id_dev", $id);
	$form->newInputHidden("action", "Perdu");
	$form->newBlock($fieldset->generateFieldset(), "50");

	$rendu = new ZunoRenduHtml();
	$rendu->generateZBox('Options devis Perdu', 'Options devis perdu', $form->generateForm(), $form->generateButtons('boxTraitementDevis', 'Annuler', 'Valider', 'popup2'), 'idDevisPerdu');
	return $rendu->generateInterneRendu();
    }

}











function BureauMyDevis() {
    $limit = 10;
    $req = new devisModel();
    $total = $req->getDataForHistoriqueVisit($limit,$type = 'COUNT');
    $result= $req->getDataForHistoriqueVisit($limit);
    $datas['total'] = $total[1][0]['counter'];
    $datas['data'] = $result[1];
    $datas['from'] = 0;
    $datas['limit'] = $limit;
    $datas['isHistoVisit'] = true;
    $view = new devisView();
    if($datas['total'] > 0)
	return $view->listeResult($datas);
    else return "";
}
?>
