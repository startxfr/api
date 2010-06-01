<?php
loadPlugin(array('ZModels/CommandeModel','ZDoc/CommandeDoc','ZView/GeneralView', 'ZunoRenduHTML'));

/**
 * Classe qui gère les vues de la partie commande pour un rendu web classique
 *
 * @author Nicolas Mannocci
 * @version 1.0
 * @todo Remplacer l'HTML par l'utilisation de la classe ZunoRenduHTML
 */
class commandeView extends generalView {

    /**
     * Méthode qui affiche un popup de création d'une nouvelle commande
     * @param array $data Les données à afficher
     * @param string $light Précise si on doit affihcer tout ou partie du contenu du popup
     * @param string $mess Message à afficher à l'utilisateur (le cas échéant)
     * @return string Le code HTML du popup
     */
    public function popupCommande($data, $light='', $mess='') {
	if($light == 'erreurCmd') {
	    return '<span class="important">'.$mess.'</span>'.$this->formPopupCmd($data);
	}
	elseif($light == 'ok') {
	    return '<span class="importantgreen">'.$mess.'</span>';
	}
	else {
	    $titre = 'Nouvelle Commande';
	    $corps .= '<div id="formAddCmdFromDevis">'.$this->formPopupCmd($data).'</div>';
	    $pied = '<a name="close" onclick="zuno.popup.close();" ><img align="middle" title="Annuler" alt="cancel.png" name="img" src="../img/prospec/cancel.png"/>Annuler</a>';
	    $pied .= '<a name="submit" onclick="validFormPopup();zuno.popup.close();"><img align="middle" title="Enregistrer" alt="record.png" name="img" src="../img/prospec/record.png"/>Enregistrer</a>';
	    return generateZBox($titre, $titre, $corps, $pied, 'popupAddCmd');
	}
    }

    /**
     * Méthode qui affiche un popup de validation d'une commande / création d'une facture
     * @param string $id L'id de la commande à cloturer
     * @return string le Code HTML
     */
    public function popupValidFacturation($id) {
	$titre = 'Création Facture';
	$corps = '<div id="formAddFact">Etes vous sûr de vouloir cloturer la commande '.$id.' et de créer la facture correspondante ?</div>';
	$corps .= '<form name="addFact" action="../facturier/Facture.php" method="POST"><input type="hidden" name="id_cmd" value="'.$id.'" /><input type="hidden" name="action" value="addFactFromCmd" /></form>';
	$pied = '<a onclick="javascript:zuno.popup.close();" ><img align="middle" title="Non" alt="cancel.png" name="img" src="../img/prospec/cancel.png"/>Non</a>';
	$pied .= '<a name="submit" href="javascript:document.forms.addFact.submit();"><img align="middle" title="Enregistrer" alt="record.png" name="img" src="../img/prospec/confirm.png"/>Oui</a>';
	return generateZBox($titre, $titre, $corps, $pied, 'popupFact');
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

    /**
     * Méthode qui génère le contenu du popup de création d'une commande
     * @param array $data Les données à afficher
     * @return string Du code HTML
     */
    private function formPopupCmd($data) {
	$increment = 0;
	$totalHT = 0;
	$tag['script'] = '';
	if(is_array($data['produits']))
	    foreach($data['produits'] as $v) {
		if($v['quantite_cmd'] == '')
		    $v['quantite_cmd'] = $v['quantite'];
		if($v['stock_prod'] == '')
		    $v['stock_prod'] = '0';
		$tag['script'] .= 'produit['.$increment.']=\''.$v['id_produit'].'\'; ';
		$tag['LinkBox'] = '<input type="hidden" name="id_produit['.$increment.']" value="'.$v['id_produit'].'" />'.$v['id_produit'];
		$tag['stock'] = prepareNombreAffichage($v['stock_prod']);
		$tag['quantite'] = inputTag('text', 'quantite['.$increment.']', '', '', '', prepareNombreAffichage($v['quantite']), 'READONLY id="'.$v['id_produit'].'qtt"');
		$tag['prix'] = inputTag('text', 'prix['.$increment.']', '', '', '', prepareNombreAffichage($v['prix']), 'READONLY id="'.$v['id_produit'].'prixC"');
		$tag['remise'] = inputTag('text', 'remise['.$increment.']', '', '', '', prepareNombreAffichage($v['remise']), 'READONLY id="'.$v['id_produit'].'remiseC"');
		$tag['total'] = $v['quantite']*$v['prix']*(1-$v['remise']/100);
		$totalHT += $tag['total'];
		if($v['fournisseurs'] != null) {
		    $tag['quantiteF'] = inputTag('text', 'quantite_cmd['.$increment.']', '', '', '', prepareNombreAffichage($v['quantite_cmd']), 'READONLY id="'.$v['id_produit'].'qtt_cmd" onchange="changeData(\''.$v['id_produit'].'\');" ');
		    $tag['fournisseur'] = selectTag('fournisseur['.$increment.']', $v['fournisseurs'], prepareNombreAffichage($v['fournisseur']), '', 'id="'.$v['id_produit'].'fournisseur" onchange="aCVF(\''.$v['id_produit'].'\', this.value);" ', true);
		    $tag['remiseF'] = inputTag('text', 'remiseF['.$increment.']', '', '', '', 0, 'READONLY id="'.$v['id_produit'].'remiseF" onchange="changeData(\''.$v['id_produit'].'\');"');
		    $tag['prixF'] = inputTag('text', 'prixF['.$increment.']', '', '', '', 0, 'READONLY id="'.$v['id_produit'].'prixF" onchange="changeData(\''.$v['id_produit'].'\');"');

		    $tag['script'] .= "prix['".$v['id_produit']."']= new Array(); remise['".$v['id_produit']."']= new Array(); ";
		    foreach($v['fournisseurs'] as $kk => $vv) {
			$tag['script'] .= "prix['".$v['id_produit']."']['".$kk."'] = '".prepareNombreTraitement($v['PF'][$kk])."'; ";
			$tag['script'] .= "remise['".$v['id_produit']."']['".$kk."'] = '".prepareNombreTraitement($v['RF'][$kk])."'; ";
		    }
		}
		else {
		    $tag['quantiteF'] = '-';
		    $tag['prixF'] = '-';
		    $tag['remiseF'] = '-';
		    $tag['fournisseur'] = '-';
		}
		$tag['id_produit'] = $v['id_produit'];
		$tag['altern'] = ($increment%2);
		$increment++;
		$tmptab .= templating('draco/Devis2BDC.ListProduct.row',$tag);
	    }
	$tag['renew'] = inputTag('checkbox',"renew",'','','','1', 'onclick="if(this.checked) {$(\'renewPeriode\').style.display = \'table-row\';} else {$(\'renewPeriode\').style.display = \'none\';}"');
	$tag['renewperiode']= HtmlForm::addAutoSelect('renewperiode','ref_renewperiode','','','','',$GLOBALS['PropsecConf']['DBPool']);
	$tag['nom_cmd'] = inputTag('text', 'titre_cmd', '', '', '', $data['titre_dev']);
	$tag['BDC'] = inputTag('text', 'BDCclient_cmd', '', '', '', $data['BDCclient_dev']);
	$tag['totalHT'] = prepareNombreAffichage($totalHT);
	$tag['id_dev'] = $data['id_dev'];
	$tag['liste'] = $tmptab;
	$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$sqlConn->makeRequeteFree("select id_modereg, nom_modereg from ref_modereglement;");
	$temp = $sqlConn->process2();
	$temp=$temp[1];
	foreach($temp as $k => $v)
	    $list[$v['id_modereg']] = $v['nom_modereg'];
	$tag['ModeReglement'] = selectTag('modereglement_cmd', $list, $data['modereglement_cmd']);
	$sqlConn->makeRequeteFree("select id_condreg, nom_condreg from ref_condireglement;");
	$temp = $sqlConn->process2();
	$temp=$temp[1];
	foreach($temp as $k => $v)
	    $list[$v['id_condreg']] = $v['nom_condreg'];
	$tag['CondiReglement'] = selectTag('condireglement_cmd', $list, $data['condireglement_cmd']);
	return templating('draco/Devis2BDC.ListProduct',$tag);
    }

    /**
     * Méthode qui affiche une fiche commande
     * @param array $datas Les données à afficher
     * @param string $light Précise si on veut tout ou partie de la fiche
     * @param string $mess Le message à afficher à l'utilisateur (le cas échéant)
     * @return string Le code HTML
     */
    public function view($datas, $light, $mess) {
	if(array_key_exists('out',$GLOBALS))
	    $GLOBALS['out']->setTitle('Fiche commande "'.$datas['data']['id_cmd'].'" - '.$datas['data']['nom_ent'],'',true);
	$script = '<script type="text/javascript">function autoCompletage() { new Ajax.Autocompleter("ACcontact_cmd", "ACcontact_cmd_choix", "../ajaxRef.php?action=listeContact&id_ent='.$datas['data']['id_ent'].'", { paramName: "value", minChars: 2, indicator: "loadingCont",  afterUpdateElement : autoCompleteHidden } ); ';
	$script .= "\n".'new Ajax.Autocompleter("ACcontact_achat_cmd", "ACcontact_achat_cmd_choix", "../ajaxRef.php?action=listeContact&id_ent='.$datas['data']['id_ent'].'", { paramName: "value", minChars: 2, indicator: "loadingCont2", afterUpdateElement : autoCompleteHidden } );} ';
	$script .= "\n".'function afficherFormModif(id) { if($(\'suisJeEnModif\').value != 0) { $($(\'suisJeEnModif\').value+\'M\').style.display = \'none\'; $($(\'suisJeEnModif\').value).style.display = \'table-row\'; } $(\'suisJeEnModif\').value = id; $(id).style.display = \'none\'; $(id+\'M\').style.display = \'table-row\';} ';
	$script .= "\n".'function annulerModif() {$($(\'suisJeEnModif\').value+\'M\').style.display=\'none\'; $($(\'suisJeEnModif\').value).style.display=\'table-row\'; $(\'suisJeEnModif\').value = 0;} ';
	$script .= "\n".'function calculette() { var id=$(\'suisJeEnModif\').value; var total = $(id+\'QTT\').value*$(id+\'PF\').value*(1-$(id+\'RF\').value/100); $(id+\'FHT\').value = Math.round(total*100)/100; $(id+\'HT\').value = Math.round(($(id+\'QTTC\').value*$(id+\'PC\').value*(1-($(id+\'RC\').value/100)))*100)/100; $(id+\'MARGE\').innerHTML = Math.round(($(id+\'HT\').value-total)*100)/100+\' &euro;\';} ';
	foreach($datas['produits'] as $v) {
	    if(is_array($v['fournisseurs'])) {
		$script .= "\n prix = new Array(); remise = new Array(); ";
		$script .= "\n"."prix['".$v['id']."']= new Array(); remise['".$v['id']."']= new Array(); ";
		foreach($v['fournisseurs'] as $kk => $vv) {
		    $script .= "prix['".$v['id']."']['".$kk."'] = '".prepareNombreTraitement($v['PF'][$kk])."'; ";
		    $script .= "remise['".$v['id']."']['".$kk."'] = '".prepareNombreTraitement($v['RF'][$kk])."'; ";
		}
	    }
	}
	$script .= "\n".'function ChangeCannevas() { var choix = document.getElementById(\'CannevasListe\').value; if(choix.length > 0) { output = Cannevas[choix]; } document.getElementById(\'MessageToSend\').value = output; } ';
	$script .= "\n".'function changeCommandeFormat(value) { $(\'formatCommandePDF\').value = value;} ';
	$script .= "\n".'function montreForm(value) {if(value == \'mail\') { $(\'champFax\').style.display = \'none\'; $(\'champCC\').style.display = \'table-row\'; champAdd(\'none\'); $(\'champEmail\').style.display = \'table-row\';} else if(value == \'fax\') {$(\'champFax\').style.display = \'table-row\'; $(\'champCC\').style.display = \'none\'; champAdd(\'none\'); $(\'champEmail\').style.display =\'none\';} else if(value == \'courrier\') { $(\'champFax\').style.display = \'none\'; $(\'champCC\').style.display = \'none\'; $(\'champEmail\').style.display = \'none\'; champAdd(\'table-row\')} else {$(\'champEmail\').style.display = $(\'champFax\').style.display = \'none\'; champAdd(\'none\'); } } ';
	$script .= "\n".'function champAdd(display) {$(\'champAdd1\').style.display = $(\'champAdd2\').style.display = $(\'champAdd3\').style.display = $(\'champAdd4\').style.display = display;} ';
	$script .= "\n".'function changeFourn(produit, fourn) {if(fourn != "") {$(produit+\'PF\').value = prix[produit][fourn];$(produit+\'RF\').value = remise[produit][fourn]; calculette();} else {$(produit+\'PF\').value = 0; $(produit+\'RF\').value = 0; calculette();} } '."\n";
	$script .= 'function ChangeCommandeAction(value) {	$(\'MakeCommandeAction\').value = value;}'."\n";
	$script .= "\n".'function verifAutocompleteur(champ) {if(champ.value == "" || champ.value == null) {$(champ.id+\'hidden\').value = "";} else if(!$(champ.id+\'_choix\').hasChildNodes()) {alert(\'Cette valeur n\\\'existe pas.\'); champ.value=$(champ.id+\'old\').value; setTimeout(function() { champ.focus(); }, 100) } } ';
	$script .= 'function autoCompleteHidden(text, li) { $(text.id+\'hidden\').value = li.title;}</script>';

	if($light == 'interneInfos')
	    return '<span class="important">'.$mess.'</span>'.$this->blockInfosCommande($datas);
	elseif($light == 'interneProduit')
	    return '<span class="important">'.$mess.'</span>'.$this->interneProduits($datas);
	elseif($light == 'interneTraitement')
	    return '<span class="important">'.$mess.'</span>'.$this->interneTraitement($datas);
	elseif($light == 'Traitement')
	    return $this->traitement($datas, '<span class="important" style="text-align:center;">'.$mess.'</span>');
	else
	    return $script.$this->infosCommande($datas).$this->produits($datas).'<div id="traitementCommande">'.$this->traitement($datas).'</div>';
    }

    /**
     * Méthode qui génère le block contenant les informations relatives à la commande
     * @param array $datas Les données à afficher
     * @return string Le code HTML généré
     */
    private function infosCommande($datas) {
	$titre = '<img src="../img/prospec/commande.png" alt="commande" title="Commande" onload="autoCompletage();" /> Informations sur la commande '.$datas['data']['id_cmd'];

	$corps = '<form name="ModifInfosCmd" action="#" method="post">';
	$corps .= '<input type="hidden" name="action" value="modifCmd" />';
	$corps .= '<input type="hidden" name="id_cmd" value="'.$datas['data']['id_cmd'].'" />';
	$corps .= '<div id="ContenuFormInfosCmd">';
	$corps .= $this->blockInfosCommande($datas);
	$corps .= '</div></form>';
	$pied = '<a title="Reset" href="javascript:document.ModifInfosCmd.reset();" ><img align="middle" title="Effacer" alt="Effacer" name="img" src="../img/prospec/cancel.png"/> Recommencer</a>';
	$pied .= (verifDroitsAuto('commande', 15, $datas['data']['commercial_cmd'])) ? '<a name="submit" onclick="zuno.business.formTools.sendFormAjah(\'ModifInfosCmd\', \'../pegase/Commande.php\',\'ContenuFormInfosCmd\');"><img align="middle" title="Enregistrer" alt="Enregistrer" name="img" src="../img/prospec/record.png"/> Modifier cette commande</a>' : '';
	if ($datas['data']['status_cmd'] >= '7') {
	    $pied 	= "";
	}
	$pied .= '<a name="submit" onclick="return zuno.popup.open(\'../pegase/Commande.php\', \'action=actuCommande&id_cmd='.$datas['data']['id_cmd'].'\', \'800\', \'450\', \'\', \'\', \'resize\', \'Actualités\');"><img align="middle" title="Historique" alt="Historique" name="img" src="../img/prospec/actu.png"/> Historique de cette commande</a>';
	return generateZBox($titre,$titre,$corps,$pied,'infosCmd','');
    }

    /**
     * Méthode qui génère le contenu du block des commandes
     * @param array $datas Les données à afficher
     * @return string Le code HTML
     */
    private function blockInfosCommande($datas) {
	$Ncont = ($datas['data']['nom_cont'] != '') ? $datas['data']['civ_cont'].' '.$datas['data']['prenom_cont'].' '.$datas['data']['nom_cont'] : '';
	$Nachat = ($datas['data']['nom_achat'] != '') ? $datas['data']['civ_achat'].' '.$datas['data']['prenom_achat'].' '.$datas['data']['nom_achat'] : '';

	$out = '<div class="block width50">';
	$out .= '<fieldset class="form"><legend>Informations sur la commande</legend>';
	if($datas['data']['id_fact'] != '' or $datas['data']['id_dev'] != '' or $datas['data']['id_aff'] != '') {
	    if($datas['data']['id_aff'] != '') {
		$out .= '<div class="row"><div class="label">Affaire : </div>';
		$out .= '<div class="field"><a href="../draco/Affaire.php?id_aff='.$datas['data']['id_aff'].'"><img src="../img/actualite/affaire.png" title="Affaire", alt="affaire" /> '.$datas['data']['id_aff'].' - '.$datas['data']['titre_aff'].' </a></div></div>';
	    }
	    if($datas['data']['id_dev'] != '') {
		$out .= '<div class="row"><div class="label">Devis : </div>';
		$out .= '<div class="field"><a href="../draco/Devis.php?id_dev='.$datas['data']['id_dev'].'"><img src="../img/actualite/devis.png" title="Devis", alt="devis" /> '.$datas['data']['id_dev'].' - '.$datas['data']['titre_dev'].'</a></div></div>';
	    }
	    if($datas['data']['id_fact'] != '') {
		$out .= '<div class="row"><div class="label">Facture : </div>';
		$out .= '<div class="field"><a href="../facturier/Facture.php?id_fact='.$datas['data']['id_fact'].'"><img src="../img/actualite/facture.png" title="Facture", alt="facture"> '.$datas['data']['id_fact'].' - '.$datas['data']['titre_fact'].'</a></div></div>';
	    }
	}
	$out .= '<div class="row">
                    <div class="label" onmouseover="zuno.tooltip(this,ZTips.commande.infoDateCree);">Créé le : </div>
                    <div class="field">'.DateUniv2Human($datas['data']['daterecord_cmd'], 'simpleLong').'</div>
		</div>
		<div class="row"> 
                    <div class="label" onmouseover="zuno.tooltip(this,ZTips.commande.infoDateModif);">Modifié le : </div>
                    <div class="field">'.DateUniv2Human($datas['data']['datemodif_cmd'], 'simpleLong').'</div>
		</div>';
	$out .= '<div class="row">';
	$out .= '<div class="label" onmouseover="zuno.tooltip(this,ZTips.commande.infoTitre);">Titre</div>';
	$out .= '<div class="field">'.inputTag('text', 'titre_cmd', '', '', '', $datas['data']['titre_cmd']).'</div>';
	$out .= '</div>';
	$out .= '<div class="row">';
	$out .= '<div class="label" onmouseover="zuno.tooltip(this,ZTips.commande.infoBDCC);">BDC client</div>';
	$out .= '<div class="field">'.inputTag('text', 'BDCclient_cmd', '', '', '', $datas['data']['BDCclient_cmd']).'</div>';
	$out .= '</div>';
	$out .= '</fieldset></div>';

	$out .= '<div class="block width50">';
	$out .= '<fieldset class="form"><legend>Adresse de livraison</legend>';
	$out .= '<div class="row">';
	$out .= '<div class="label" onmouseover="zuno.tooltip(this,ZTips.commande.deliveryNom);">Entreprise</div>';
	if ($datas['data']['type_ent'] != '')
	    $datas['data']['nom_ent']	= imageTag('../img/'.$GLOBALS['PropsecConf']['dir.img'].'TypeEntreprise/'.$datas['data']['type_ent'].'.png',$datas['data']['nom_tyent']).' '.$datas['data']['nom_ent'];
	else $datas['data']['nom_ent']	= imageTag('../img/'.$GLOBALS['PropsecConf']['dir.img'].'TypeEntreprise/particulier.png','particulier').'<i>Particulier</i>';
	$out .= '<div class="field"><a href="#" id="entrepriseFacture" onclick="return zuno.popup.open(\'../prospec/PopupEntreprise.php\',\'id_ent='.$datas['data']['id_ent'].'\',900,200,\'\',\'\',\'resize\',\'Entreprise\');" title="#" >'.$datas['data']['nom_ent'].' ('.$datas['data']['cp_ent'].')</a></div>';
	$out .= '</div>';
	$out .= '<div class="row">';
	$out .= '<div class="label" onmouseover="zuno.tooltip(this,ZTips.commande.deliveryAdd);">Adresse</div>';
	$out .= '<div class="field">'.inputTag('text', 'adressedelivery_cmd', '', '', '', $datas['data']['adressedelivery_cmd']).'</div>';
	$out .= '</div>';
	$out .= '<div class="row">';
	$out .= '<div class="label" onmouseover="zuno.tooltip(this,ZTips.commande.deliveryAdd2);">Complément</div>';
	$out .= '<div class="field">'.inputTag('text', 'adresse1delivery_cmd', '', '', '', $datas['data']['adresse1delivery_cmd']).'</div>';
	$out .= '</div>';
	$out .= '<div class="row">
                    <div class="label" onmouseover="zuno.tooltip(this,ZTips.commande.deliveryCpVille);">CP - Ville</div>
                    <div class="field">'.inputTag('text', "cpdelivery_cmd", 'civ', '', '6', $datas['data']['cpdelivery_cmd'], 'onchange="zuno.ajax.get.html(\'../ajaxRef.php\',\'action=villeForCp&cp=\'+this.value,\'cmdVille\');"').'<select name=\'villedelivery_cmd\'  id="cmdVille"  onblur="finishEditing();" onclick="beginEditing(this);" class="nextciv"><option value=""> </option><option value="'.$datas['data']['villedelivery_cmd'].'" selected="selected">'.$datas['data']['villedelivery_cmd'].'</option></select></div>
		</div>';
	$out .= '<div class="row">
                    <div class="label" onmouseover="zuno.tooltip(this,ZTips.commande.deliveryPays);">Pays</div>
                    <div class="field">'.selectTag('paysdelivery_cmd',$datas['pays'],$datas['data']['paysdelivery_cmd']).'</div>
		</div>
		<div class="row"> 
                    <div class="label" onmouseover="zuno.tooltip(this,ZTips.commande.deliveryMail);">Mail</div>
                    <div class="field">'.inputTag('text', "maildelivery_cmd", '', '', '30', $datas['data']['maildelivery_cmd']).'</div>
		</div></fieldset></div>';
	$out .= '<div class="block width50">';
	$out .= '<fieldset class="form"><legend>Informations sur les contacts</legend>';
	$out .= '<div class="row">';
	$out .= '<div class="label" onmouseover="zuno.tooltip(this,ZTips.commande.entreprise);">Entreprise</div>';
	$out .= '<div class="field"><a href="#" id="entrepriseFacture" onclick="return zuno.popup.open(\'../prospec/PopupEntreprise.php\',\'id_ent='.$datas['data']['id_ent'].'\',900,200,\'\',\'\',\'resize\',\'Entreprise\');" title="#" >'.$datas['data']['nom_ent'].' ('.$datas['data']['cp_ent'].')</a></div>';
	$out .= '</div>';
	$out .= '<div class="row">
                    <div class="label" onmouseover="zuno.tooltip(this,ZTips.commande.contactTech);">Contact Tech</div>
                    <div class="field">'.inputTag('text', 'contact_cmd2','icon', '', '16',$Ncont, ' id="ACcontact_cmd" onchange="verifAutocompleteur(this);" ').'<a name="addCont" title="Ajouter un contact" onclick="return zuno.popup.open(\'Commande.php\', \'action=addContCmd&entreprise='.$datas['data']['id_ent'].'&idChamp=ACcontact_cmd\');"><img alt="cont" title="Ajouter un contact" src="../img/prospec/add.png" /></a><img style="display:none; margin-left:5px;" id="loadingCont" src="../img/ajax-loader.gif" alt="loader"/></div>
                    <input type="hidden" name="contact_cmd" id="ACcontact_cmdhidden" value="'.$datas['data']['contact_cmd'].'" /><input type="hidden" name="contold" id="ACcontact_cmdold" value="'.$Ncont.'" />
                    <div id="ACcontact_cmd_choix" class="autocomplete" style="display:none;"></div>
		</div>';
	$out .= '<div class="row">
                    <div class="label" onmouseover="zuno.tooltip(this,ZTips.commande.contactAchat);">Contact Achat</div>
                    <div class="field">'.inputTag('text', 'contact_achat_cmd2','icon', '', '16',$Nachat, ' id="ACcontact_achat_cmd" onchange="verifAutocompleteur(this);"').'<a name="addCont" title="Ajouter un contact" onclick="return zuno.popup.open(\'Commande.php\', \'action=addContCmd&entreprise='.$datas['data']['id_ent'].'&idChamp=ACcontact_achat_cmd\');"><img alt="cont" title="Ajouter un contact" src="../img/prospec/add.png" /></a><img style="display:none; margin-left:5px;" id="loadingCont2" src="../img/ajax-loader.gif" alt="loader"/></div>
                    <input type="hidden" name="contact_achat_cmd" id="ACcontact_achat_cmdhidden" value="'.$datas['data']['contact_achat_cmd'].'" /><input type="hidden" name="oldCont" id="ACcontact_achat_cmdold" value="'.$Nachat.'" />
                    <div id="ACcontact_achat_cmd_choix" class="autocomplete" style="display:none;"></div>
		</div>';
	$out .= '<div class="row">';
	$out .= '<div class="label" onmouseover="zuno.tooltip(this,ZTips.commande.commercial);">Commercial</div>';
	$out .= '<div class="field">'.selectTag('commercial_cmd',$datas['user'],$datas['data']['commercial_cmd']).'</div>';
	$out .= '</div>';
	$out .= '</fieldset></div>';

	$out .= '<div class="block width50">';
	$out .= '<fieldset class="form"><legend>Commentaire de la commande</legend>';
	$out .= '<div class="row">
                    <div class="label" onmouseover="zuno.tooltip(this,ZTips.commande.deliveryComplement);">Commentaire</div>
                    <div class="field">
			<textarea name="complementdelivery_cmd" cols="55" rows="2" value="'.$datas['data']['complementdelivery_cmd'].'"></textarea></div>
		</div>';
	$out .= '</fieldset></div>';
	return $out;
    }

    /**
     * Méthode qui génère le block d'infos produits
     * @param array $datas Les données à afficher
     * @return string Le code HTML
     */
    private function produits($datas) {
	$titre = imageTag('../img/prospec/commande.png','commande').' Produits de la commande '.$datas['data']['id_cmd'];
	$corps = '<form name="productCmd" action="#" method="post">';
	$corps .= '<input type="hidden" name="id_cmd" value="'.$datas['data']['id_cmd'].'" />';
	$corps .= '<input type="hidden" name="enmodif" value="0" id="suisJeEnModif" />';
	$corps .= '<input type="hidden" name="action" value="modifProd" />';
	$corps .= '<div class="blockTable" id="contenuProdCmd">';
	$corps .= $this->interneProduits($datas);
	$corps .= '</div></form>';

	return generateZBox($titre,$titre,$corps,'','produitsCommande','');
    }

    /**
     * Méthode qui génère le contenu du block produit
     * @param array $datas Les données à afficher
     * @return string Le code HTML
     */
    private function interneProduits($datas) {
	$out = '<table width="100%" cellspacing="0" cellpadding="2" border="0">
		<tbody><tr class="titre"> 
			<th rowspan="2">ID</th>
			<th rowspan="2">Fournisseur</th>
			<th rowspan="2">Stock</th>
			<th colspan="4">Client</th>
			<th colspan="4">Fournisseur</th>
			<th rowspan="2">Marge</th>
			<th rowspan="2">Action</th>
		</tr>
		<tr class="subtitre">
			<th>Quantité</th> 
			<th>Prix</th>
			<th>Remise</th>
			<th>Total</th>
			<th>Quantité</th>
			<th>Prix</th>
			<th>Remise</th>
			<th>Total</th>
		</tr>';
	$alternance = $numero = $HT = $FHT = 0;
	foreach($datas['produits'] as $v) {
	    $totalC = $v['prix']*$v['quantite']*(1-$v['remise']/100);
	    $HT += $totalC;
	    $totalF = $v['prixF2']*$v['quantite_cmd']*(1-$v['remiseF2']/100);
	    $FHT += $totalF;
	    $out .= '<tr class="altern'.$alternance.'" id="'.$v['id'].'">';
	    $out2 = '<tr class="altern'.$alternance.'" id="'.$v['id'].'M" style="display:none;">';
	    $out .= '<td id="ID'.$v['id'].'">'.$v['id_produit'].'</td>';
	    $out2 .= '<td>'.$v['id_produit'].'</td>';
	    if($v['fournisseur'] != '') {
		$out .= '<td class="barre"><a name="fourn'.$v['fournisseur'].'" onclick="return zuno.popup.open(\'../prospec/PopupEntreprise.php\', \'id_fourn='.$v['fournisseur'].'\', \'900\', \'200\',\'\',\'\', \'resize\', \'Fournisseur\')"><img alt="fourn" title="Fournisseur" src="../img/prospec/TypeEntreprise/5.png" /> '.$v['fournisseurs'][$v['fournisseur']].'</a></td>';
		$out2 .= '<td class="barre">'.selectTag('fournisseur['.$v['id'].']', $v['fournisseurs'], $v['fournisseur'], '', 'onchange="changeFourn(\''.$v['id'].'\', this.value);"').'</td>';
	    }
	    else {
		$out .= '<td class="barre"> - </td>';
	    }
	    $out .= '<td class="barre">'.$v['stock_prod'].'</td>';
	    $out2 .= '<td class="barre">'.$v['stock_prod'].'</td>';
	    $out .= '<td>'.$v['quantite'].'</td>';
	    $out2 .= '<td>'.inputTag('text', 'quantite['.$v['id'].']', '', '', '', $v['quantite'], 'id="'.$v['id'].'QTTC" style="max-width:55px;" onchange="calculette();"').'</td>';
	    $out .= '<td>'.prepareNombreAffichage($v['prix']).' &euro;</td>';
	    $out2 .= '<td>'.inputTag('text', 'prix['.$v['id'].']', '', '', '', $v['prix'], 'id="'.$v['id'].'PC" style="max-width:55px;" onchange="calculette();"').' &euro;</td>';
	    $out .= '<td>'.prepareNombreAffichage($v['remise']).' %</td>';
	    $out2 .= '<td>'.inputTag('text', 'remise['.$v['id'].']', '', '', '', $v['remise'], 'id="'.$v['id'].'RC" style="max-width:55px;" onchange="calculette();"').' %</td>';
	    $out .= '<td class="barre">'.prepareNombreAffichage($totalC).' &euro;</td>';
	    $out2 .= '<td class="barre" nowrap="nowrap">'.inputTag('text', 'total', '', '', '', prepareNombreTraitement($totalC), 'id="'.$v['id'].'HT" READONLY style="max-width:55px;"').' &euro;</td>';
	    if($v['fournisseur'] != '') {
		$out .= '<td>'.$v['quantite_cmd'].'</td>';
		$out2 .= '<td nowrap="nowrap">'.inputTag('text', 'quantite_cmd['.$v['id'].']', '', '', '', $v['quantite_cmd'], 'id="'.$v['id'].'QTT" style="max-width:55px;" onchange="calculette();"').'</td>';
		$out .= '<td>'.prepareNombreAffichage($v['prixF2']).' &euro;</td>';
		$out2 .= '<td nowrap="nowrap">'.inputTag('text', 'prixF['.$v['id'].']', '', '', '', $v['prixF2'], 'id="'.$v['id'].'PF" style="max-width:55px;" onchange="calculette();"').' &euro;</td>';
		$out .= '<td>'.prepareNombreAffichage($v['remiseF2']).' %</td>';
		$out2 .= '<td nowrap="nowrap">'.inputTag('text', 'remiseF['.$v['id'].']', '', '', '', $v['remiseF2'], 'id="'.$v['id'].'RF" style="max-width:55px;" onchange="calculette();"').' %</td>';
		$out .= '<td class="barre">'.prepareNombreAffichage($totalF).' &euro;</td>';
		$out2 .= '<td class="barre" nowrap="nowrap">'.inputTag('text', 'totalF', '', '', '', $totalF, 'id="'.$v['id'].'FHT" READONLY style="max-width:55px;"').' &euro;</td>';
	    }
	    else {
		$out .= '<td> - </td><td> - </td><td> - </td><td class="barre"> - </td>';
	    }
	    $out .= '<td class="barre">'.prepareNombreAffichage(($totalC-$totalF)).' &euro;</td>';
	    $out2 .= '<td class="barre" nowrap="nowrap" id="'.$v['id'].'MARGE">'.prepareNombreAffichage(($totalC-$totalF)).' &euro;</td>';
	    if($datas['data']['status_cmd'] <= 2 and $v['fournisseur'] != '')
		$out .= '<td class="center"><a name="modif" onclick="afficherFormModif(\''.$v['id'].'\');"><img title="Modifier" alt="Modifier" name="img" src="../img/prospec/commande.modif.png"/></a></td>';
	    else
		$out .= '<td class="center"> - </td>';
	    $modif = (verifDroitsAuto('commande', 15, $datas['data']['commercial_cmd'])) ? '<a name="modif" onclick="zuno.business.formTools.sendFormAjah(\'productCmd\', \'pegase/Commande.php\',\'contenuProdCmd\');"><img title="Modifier" alt="Modifier" name="img" src="../img/prospec/commande.add.png"/></a>' : '';
	    $out2 .= '<td class="center">'.$modif.'<a name="anule" onclick="annulerModif();"><img title="annuler" alt="annuler" src="../img/prospec/cancel.png" /></a></td>';
	    $out .= '</tr>';
	    $out2 .= '</tr>';
	    $out .= $out2;
	    $numero++;
	    $alternance = $numero%2;
	}
	$out .= '<tr>
				<th colspan="5" rowspan="2"> </th>
				<th colspan="7"> </th>
			</tr>
			<tr class="titre noround roundbottom"> 
				<th nowrap="nowrap" class="bordG">HT Client</th>
				<th id="TotalHT" nowrap="nowrap">'.prepareNombreAffichage($HT).' &euro;</th>
				<th colspan="3" class="right">
				HT Fournisseur</th>
				<th id="TotalHTF" nowrap="nowrap">'.prepareNombreAffichage($FHT).' &euro;</th>
				<th>Marge</th>
				<th id="TotalMarge" nowrap="nowrap" colspan="2" class="bordD">'.prepareNombreAffichage(($HT-$FHT)).' &euro;</th>
			</tr>';
	$out .= '</tbody></table>';
	return $out;
    }

    /**
     * Méthode qui génère le block traitement
     * @param array $datas Les données à afficher
     * @param string $mess Le message à afficher (le cas échéant)
     * @return string Le code HTML
     */
    private function traitement($datas, $mess = '') {
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
	foreach ($GLOBALS['ZunoSendMail'] as $key => $val) {
	    $k = explode('.',$key,2);
	    if($k[0] == 'texte') {
		$key = $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoSendMail']['dir.texte'].$k[1];
		$outJS .="\tCannevas[\"".$i."\"] = \"".str_replace($cleanFrom,$cleanTo,addslashes(trim(ProcessTemplating($key,$input,$output))))."\";\n";
		$i++;
	    }
	}
	$outJS .= '</script>';

	$corps = '<div id="ContenuTraitementCommande" >'.$mess.$this->interneTraitement($datas).'</div>';

	if($datas['data']['status_cmd'] == "1" and verifDroitsAuto('commande', 13, $datas['data']['commercial_cmd'])) {
	    $bouton = '<a name="valid" onclick="ChangeCommandeAction(\'Valid\');zuno.business.formTools.sendFormAjah(\'ActionCommande\', \'pegase/Commande.php\',\'traitementCommande\');" title="Valider"><img src="../img/prospec/commande.valid.png" alt="valid" title="Valider la commande" /> Valider le bon de commande client</a>';
	}
	elseif (verifDroitsAuto('commande', 60, $datas['data']['commercial_cmd'])) {
	    $bouton .= '<a onclick="ChangeCommandeAction(\'GenererDocs\');zuno.business.formTools.sendFormAjah(\'ActionCommandePDF\', \'pegase/Commande.php\',\'traitementCommande\');" title="Enregistrer" name="enreg"><img align="middle" title="Enregistrer" alt="enreg" name="img" src="../img/prospec/commande.record.png"/> Enregistrer</a>';
	    if(verifDroitsAuto('commande', 50, $datas['data']['commercial_cmd'])) {
		$bouton .= '<a onclick="ChangeCommandeAction(\'RecordSend\');zuno.business.formTools.sendFormAjah(\'ActionCommandePDF\', \'pegase/Commande.php\',\'traitementCommande\');" title="Enregistrer puis envoyer" name="enregistrerEnvoyer"><img align="middle" title="Enregistrer et envoyer" alt="enregEnvoy" name="img" src="../img/prospec/commande.recsend.png"/> Enregistrer & envoyer</a>';
		if($datas['data']['status_cmd'] >= "3")
		    $bouton .= '<a onclick="ChangeCommandeAction(\'Send\');zuno.business.formTools.sendFormAjah(\'ActionCommandePDF\', \'pegase/Commande.php\',\'traitementCommande\');" title="Envoyer" name="envoyer"><img align="middle" title="Envoyer" alt="envoyer" name="img" src="../img/prospec/commande.send.png"/> Envoyer</a>';

	    }
	}
	// Quand status Envoyé
	if (($datas['data']['status_cmd'] >= "4")and(verifDroitsAuto('commande', 13, $datas['data']['commercial_cmd']))) {
	    if($datas['data']['status_cmd'] == "4")
		$bouton .= '<a title="Traitement" name="traitement" onclick="ChangeCommandeAction(\'traitement\');zuno.business.formTools.sendFormAjah(\'ActionCommandePDF\', \'pegase/Commande.php\',\'traitementCommande\');"><img align="middle" title="Traitement" alt="Traitement" name="img" src="../img/prospec/commande.AR.png"/> Commande en traitement</a>';
	    if($datas['data']['status_cmd'] == "6")
		$bouton .= '<a title="Expédiée" name="expedie" onclick="ChangeCommandeAction(\'expedie\');zuno.business.formTools.sendFormAjah(\'ActionCommandePDF\', \'pegase/Commande.php\',\'traitementCommande\');"><img align="middle" title="Expédiée" alt="expedie" name="img" src="../img/prospec/commande.exped.png"/> Commande expédiée</a>';
	    if($datas['data']['status_cmd'] == "7")
		$bouton .= '<a title="Reception" name="reception" onclick="ChangeCommandeAction(\'reception\');zuno.business.formTools.sendFormAjah(\'ActionCommandePDF\', \'pegase/Commande.php\',\'traitementCommande\');"><img align="middle" title="Reception" alt="Reception" name="img" src="../img/prospec/commande.valid.png"/> Commande réceptionnée</a>';
	    if(verifDroitsAuto('facture', 20, $datas['data']['commercial_cmd']))
		$bouton   .= "<a name=\"facture\" onclick=\"return zuno.popup.open('Commande.php','id_commande=".$datas['data']['id_cmd']."&action=fact',700,450,'','','resize','FactureCreate');\">".imageTag('../img/prospec/facture.create.png','Facture')." Créer la facture</a> ";
	}

	$titre = imageTag('../img/prospec/commande.record.png','créer')." Traitement de la Commande ".$datas['data']['id_cmd'];

	return generateZBox($titre,$titre,$corps.$outJS,$bouton,'iTraitementCommande','');
    }

    /**
     * Méthode qui génère le contenu du block
     * @param array $datas Les données à afficher
     * @return string Le code HTML
     */
    private function internetraitement($datas) {
	$id_cmd = $datas['data']['id_cmd'];
	$opt = "";
	$gnose = new commandeGnose();
	$tag['FormName'] = 'ActionCommande';
	$tag['action'] = inputTag('hidden','action','','','',$tag['FormName'],' id="MakeCommandeAction"');
	$tag['action'] .= inputTag('hidden','id_cmd','','','',$id_cmd,'');
	$tag['action'] .= inputTag('hidden', 'dir_aff', '', '', '', $datas['data']['dir_aff']);
	$AutoMessage = "Changement de la commande ".$id_cmd." par ".$_SESSION['user']['id'];

	if ($datas['data']['status_cmd'] == "1" and verifDroitsAuto('commande', 13, $datas['data']['commercial_cmd'])) {
	    $tag['BDCclient_cmd'] = inputTag('text','BDCclient_cmd','','','',$datas['data']['BDCclient_cmd'],'');
	    $tag['modereglement_cmd']= HtmlForm::addAutoSelect('modereglement_cmd','ref_modereglement',$datas['data']['modereglement_cmd'],'','',' ORDER BY id_modereg ASC',$GLOBALS['PropsecConf']['DBPool']);
	    $tag['condireglement_cmd']= HtmlForm::addAutoSelect('condireglement_cmd','ref_condireglement',$datas['data']['condireglement_cmd'],'','',' ORDER BY id_condreg ASC',$GLOBALS['PropsecConf']['DBPool']);
	    $BarreCodeName = "BarreCode.".$id_cmd.".png";
	    $gnose->generateCommandeBarreCode($id_cmd,$GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'].$BarreCodeName);
	    $tag['CodeBarre'] = imageTag('../tmp/'.$BarreCodeName,'voir','center');

	    return templating('pegase/CommandeFiche.Action1',$tag);
	}
	else {
	    foreach ($GLOBALS['ZunoCommande'] as $key => $val) {
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
	    if(verifDroitsAuto('commande', 50, $datas['data']['commercial_cmd']))
		$TypeSend['mail'] = "par e-mail";
	    if(verifDroitsAuto('commande', 52, $datas['data']['commercial_cmd']))
		$TypeSend['fax'] = "par fax";
	    if(verifDroitsAuto('commande', 54, $datas['data']['commercial_cmd']))
		$TypeSend['courrier'] = "par courrier";


	    $ListeFournisseur = $datas['LF'];

	    if(count($ListeFournisseur) > 0) {
		$j = 0;
		foreach ($ListeFournisseur as $idfourn => $val) {
		    if(verifDroitsAuto('commande', 62, $datas['data']['commande_cmd'])) {
			if(is_file($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoAffaire']['dir.affaire'].$datas['data']['dir_aff'].'BCF.'.$datas['data']['id_dev'].'-'.$val['id_fourn'].'.pdf')) {
			    $listeDoc['BCF.'.$datas['data']['id_dev'].'-'.$val['id_fourn'].'.pdf'] = 'BCF.'.$datas['data']['id_dev'].'-'.$val['id_fourn'].'.pdf';
			    $generes .= '<div class="row"><div class="label"><a href="javascript:document.ActionCommandePDF.submit();" onclick="ChangeCommandeAction(\'VoirBCF'.$val['id_fourn'].'\');changeCommandeFormat(\'pdf\');"><img src="../img/files/pdf.png" alt="pdf" /></a></div><div class="field">BCF.'.$datas['data']['id_dev'].'-'.$val['id_fourn'].'.pdf</div></div>';
			}
			if(is_file($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoAffaire']['dir.affaire'].$datas['data']['dir_aff'].'BCF.'.$datas['data']['id_dev'].'-'.$val['id_fourn'].'.odt')) {
			    $listeDoc['BCF.'.$datas['data']['id_dev'].'-'.$val['id_fourn'].'.odt'] = 'BCF.'.$datas['data']['id_dev'].'-'.$val['id_fourn'].'.odt';
			    $generes .= '<div class="row"><div class="label"><a href="javascript:document.ActionCommandePDF.submit();" onclick="ChangeCommandeAction(\'VoirBCF'.$val['id_fourn'].'\');changeCommandeFormat(\'odt\');"><img src="../img/files/document.png" alt="odt" /></a></div><div class="field">BCF.'.$datas['data']['id_dev'].'-'.$val['id_fourn'].'.odt</div></div>';
			}
			if(is_file($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoAffaire']['dir.affaire'].$datas['data']['dir_aff'].'BCF.'.$datas['data']['id_dev'].'-'.$val['id_fourn'].'.doc')) {
			    $listeDoc['BCF.'.$datas['data']['id_dev'].'-'.$val['id_fourn'].'.doc'] = 'BCF.'.$datas['data']['id_dev'].'-'.$val['id_fourn'].'.doc';
			    $generes .= '<div class="row"><div class="label"><a href="javascript:document.ActionCommandePDF.submit();" onclick="ChangeCommandeAction(\'VoirBCF'.$val['id_fourn'].'\');changeCommandeFormat(\'doc\');"><img src="../img/files/document.png" alt="doc" /></a></div><div class="field">BCF.'.$datas['data']['id_dev'].'-'.$val['id_fourn'].'.doc</div></div>';
			}
		    }
		    $docs .= '<div class="row"><div class="label"><input type="checkbox" name="BCF'.$val['id_fourn'].'" value="1"/></div><div class="field">Bon de commande fournisseur '.$val['id_fourn'].'</div></div>';
		}
	    }
	    if(verifDroitsAuto('commande', 62, $datas['data']['commercial_cmd'])) {
		if(is_file($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoAffaire']['dir.affaire'].$datas['data']['dir_aff'].'BDL.'.$datas['data']['id_dev'].'.pdf')) {
		    $listeDoc['BDL.'.$datas['data']['id_dev'].'.pdf'] = 'BDL.'.$datas['data']['id_dev'].'.pdf';
		    $generes .= '<div class="row"><div class="label"><a href="javascript:document.ActionCommandePDF.submit();" onclick="ChangeCommandeAction(\'VoirBDL\');changeCommandeFormat(\'pdf\');"><img src="../img/files/pdf.png" alt="pdf" /></a></div><div class="field">BDL.'.$datas['data']['id_dev'].'.pdf</div></div>';
		}
		if(is_file($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoAffaire']['dir.affaire'].$datas['data']['dir_aff'].'BDL.'.$datas['data']['id_dev'].'.odt')) {
		    $listeDoc['BDL.'.$datas['data']['id_dev'].'.odt'] = 'BDL.'.$datas['data']['id_dev'].'.odt';
		    $generes .= '<div class="row"><div class="label"><a href="javascript:document.ActionCommandePDF.submit();" onclick="ChangeCommandeAction(\'VoirBDL\');changeCommandeFormat(\'odt\');"><img src="../img/files/document.png" alt="odt" /></a></div><div class="field">BDL.'.$datas['data']['id_dev'].'.odt</div></div>';
		}
		if(is_file($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoAffaire']['dir.affaire'].$datas['data']['dir_aff'].'BDL.'.$datas['data']['id_dev'].'.doc')) {
		    $listeDoc['BDL.'.$datas['data']['id_dev'].'.doc'] = 'BDL.'.$datas['data']['id_dev'].'.doc';
		    $generes .= '<div class="row"><div class="label"><a href="javascript:document.ActionCommandePDF.submit();" onclick="ChangeCommandeAction(\'VoirBDL\');changeCommandeFormat(\'doc\');"><img src="../img/files/document.png" alt="doc" /></a></div><div class="field">BDL.'.$datas['data']['id_dev'].'.doc</div></div>';
		}

		if(is_file($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoAffaire']['dir.affaire'].$datas['data']['dir_aff'].'BC.'.$datas['data']['id_dev'].'.pdf')) {
		    $listeDoc['BC.'.$datas['data']['id_dev'].'.pdf'] = 'BC.'.$datas['data']['id_dev'].'.pdf';
		    $generes .= '<div class="row"><div class="label"><a href="javascript:document.ActionCommandePDF.submit();" onclick="ChangeCommandeAction(\'VoirBC\');changeCommandeFormat(\'pdf\');"><img src="../img/files/pdf.png" alt="pdf" /></a></div><div class="field">BC.'.$datas['data']['id_dev'].'.pdf</div></div>';
		}
		if(is_file($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoAffaire']['dir.affaire'].$datas['data']['dir_aff'].'BC.'.$datas['data']['id_dev'].'.odt')) {
		    $listeDoc['BC.'.$datas['data']['id_dev'].'.odt'] = 'BC.'.$datas['data']['id_dev'].'.odt';
		    $generes .= '<div class="row"><div class="label"><a href="javascript:document.ActionCommandePDF.submit();" onclick="ChangeCommandeAction(\'VoirBC\');changeCommandeFormat(\'odt\');"><img src="../img/files/document.png" alt="odt" /></a></div><div class="field">BC.'.$datas['data']['id_dev'].'.odt</div></div>';
		}
		if(is_file($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoAffaire']['dir.affaire'].$datas['data']['dir_aff'].'BC.'.$datas['data']['id_dev'].'.doc')) {
		    $listeDoc['BC.'.$datas['data']['id_dev'].'.doc'] = 'BC.'.$datas['data']['id_dev'].'.doc';
		    $generes .= '<div class="row"><div class="label"><a href="javascript:document.ActionCommandePDF.submit();" onclick="ChangeCommandeAction(\'VoirBC\');changeCommandeFormat(\'doc\');"><img src="../img/files/document.png" alt="doc" /></a></div><div class="field">BC.'.$datas['data']['id_dev'].'.doc</div></div>';
		}

		if(is_file($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoAffaire']['dir.affaire'].$datas['data']['dir_aff'].'RapportIntervention.'.$datas['data']['id_dev'].'.pdf')) {
		    $listeDoc['RapportIntervention.'.$datas['data']['id_dev'].'.pdf'] = 'RapportIntervention.'.$datas['data']['id_dev'].'.pdf';
		    $generes .= '<div class="row"><div class="label"><a href="javascript:document.ActionCommandePDF.submit();" onclick="ChangeCommandeAction(\'VoirRI\');changeCommandeFormat(\'pdf\');"><img src="../img/files/pdf.png" alt="pdf" /></a></div><div class="field">RapportIntervention.'.$datas['data']['id_dev'].'.pdf</div></div>';
		}
		if(is_file($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoAffaire']['dir.affaire'].$datas['data']['dir_aff'].'RapportIntervention.'.$datas['data']['id_dev'].'.odt')) {
		    $listeDoc['RapportIntervention.'.$datas['data']['id_dev'].'.odt'] = 'RapportIntervention.'.$datas['data']['id_dev'].'.odt';
		    $generes .= '<div class="row"><div class="label"><a href="javascript:document.ActionCommandePDF.submit();" onclick="ChangeCommandeAction(\'VoirRI\');changeCommandeFormat(\'odt\');"><img src="../img/files/document.png" alt="odt" /></a></div><div class="field">RapportIntervention.'.$datas['data']['id_dev'].'.odt</div></div>';
		}
		if(is_file($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoAffaire']['dir.affaire'].$datas['data']['dir_aff'].'RapportIntervention.'.$datas['data']['id_dev'].'.doc')) {
		    $listeDoc['RapportIntervention.'.$datas['data']['id_dev'].'.doc'] = 'RapportIntervention.'.$datas['data']['id_dev'].'.doc';
		    $generes .= '<div class="row"><div class="label"><a href="javascript:document.ActionCommandePDF.submit();" onclick="ChangeCommandeAction(\'VoirRI\');changeCommandeFormat(\'doc\');"><img src="../img/files/document.png" alt="doc" /></a></div><div class="field">RapportIntervention.'.$datas['data']['id_dev'].'.doc</div></div>';
		}
	    }


	    $docs .= '<div class="row"><div class="label"><input type="checkbox" name="GetDocBDC" value="1" /></div><div class="field">Bon de commande client</div></div>';
	    $docs .= '<div class="row"><div class="label"><input type="checkbox" name="GetDocBDL" value="1" /></div><div class="field">Bon de livraison</div></div>';
	    $docs .= '<div class="row"><div class="label"><input type="checkbox" name="GetDocRI" value="1" /></div><div class="field">Rapport d\'intervention</div></div>';

	    return '<form name="ActionCommandePDF" action="Commande.php" method="post">
		<input type="hidden" id="MakeCommandeAction" value="ActionCommande" name="action"/>
		<input type="hidden" value="'.$datas['data']['id_cmd'].'" name="id_cmd"/>
		<input type="hidden" value="" name="format" id="formatCommandePDF"/>
		'.inputTag('hidden', 'dir_aff', '', '', '', $datas['data']['dir_aff']).'
			<div class="block width50">
				<fieldset class="form"><legend>Génération des fichiers</legend>
					<div class="row">
						<div class="label" onmouseover="zuno.tooltip(this,ZTips.commande.generateOutput);">Format</div>
						<div class="field">'.selectTag("OutputExt", $availableConvFormat, $value['OutputExt'], '', '', false).'</div>
					</div>'.$docs.'
				</fieldset>
				<fieldset class="form"><legend>Visualisation des documents</legend>
					'.$generes.'
				</fieldset>
			</div>
			<div class="block width50">
				<fieldset class="form"><legend>Envoyer</legend>
					<div class="row">
						<div class="label" onmouseover="zuno.tooltip(this,ZTips.commande.sendType);">Type d\'envoi</div>
						<div class="field">'.selectTag('typeE', $TypeSend, '', '', 'onchange="montreForm(this.value);"').'</div>
					</div>
					<div class="row">
						<div class="label" onmouseover="zuno.tooltip(this,ZTips.commande.sendDestinataire);">Destinataire</div>
						<div class="field">'.inputTag('text', 'destinataire', '', '', '', $datas['data']['civ_cont'].' '.$datas['data']['prenom_cont'].' '.$datas['data']['nom_cont']).'</div>
					</div>
					<div class="row">
						<div class="label" onmouseover="zuno.tooltip(this,ZTips.commande.sendSujet);">Sujet</div>
						<div class="field">'.inputTag('text', 'sujet').'</div>
					</div>
					<div class="row"> 
						<div class="label" onmouseover="zuno.tooltip(this,ZTips.commande.sendCannevas);">Cannevas</div>
						<div class="field">'.selectTag("cannevas", $toto2, '','',' id="CannevasListe" onchange="ChangeCannevas();"').'</div>
					</div>
					<div class="row">
						<div class="label" onmouseover="zuno.tooltip(this,ZTips.commande.sendMessage);">Message</div>
						<div class="field">'.textareaTag('message', '', '', '', '', 'id="MessageToSend"').'</div>
					</div>
					<div class="row">
						<div class="label" onmouseover="zuno.tooltip(this,ZTips.commande.sendPJ);">Fichier joint</div>
						<div class="field">'.selectTag('fichier', $listeDoc,"","","",false).'</div>
					</div>
						
				</fieldset>
			</div>
			<div class="block width50">
				<fieldset class="form"><legend>Informations sur le destinataire</legend>
					<div class="row" style="display:none;" id="champEmail">
						<div class="label" onmouseover="zuno.tooltip(this,ZTips.commande.sendMail);">Courriel</div>
						<div class="field">'.inputTag('text', 'mail', '', '', '', $datas['data']['mail_cont']).'</div>
					</div>
                                        <div class="row" style="display:none;" id="champCC">
						<div class="label" onmouseover="zuno.tooltip(this,ZTips.commande.sendCopyToMe);">Me mettre en copie</div>
						<div class="field"><input type="checkbox" name="cc" value="'.$_SESSION['user']['mail'].'" /></div>
					</div>
					<div class="row" style="display:none;" id="champFax">
						<div class="label" onmouseover="zuno.tooltip(this,ZTips.commande.sendFax);">Fax</div>
						<div class="field">'.inputTag('text', 'fax', '', '', '', $datas['data']['fax_ent']).'</div>
					</div>
					<div class="row" style="display:none;" id="champAdd1">
						<div class="label" onmouseover="zuno.tooltip(this,ZTips.commande.sendAdd);">Adresse</div>
						<div class="field">'.inputTag('text', 'add1', '', '', '', $datas['data']['adressedelivery_dev']).'</div>
					</div>
					<div class="row" style="display:none;" id="champAdd2">
						<div class="label" onmouseover="zuno.tooltip(this,ZTips.commande.sendComplement);">Complément</div>
						<div class="field">'.inputTag('text', 'add2', '', '', '', $datas['data']['adresse1delivery_cmd']).'</div>
					</div>
					<div class="row" style="display:none;" id="champAdd3"> 
						<div class="label" onmouseover="zuno.tooltip(this,ZTips.commande.sendCpVille);">CP - Ville</div>
						<div class="field">'.inputTag('text', "cp", '', '', '', $datas['data']['cpdelivery_dev'], 'onchange="zuno.ajax.get.html(\'../ajaxRef.php\',\'action=villeForCp&cp=\'+this.value,\'envoiVille\');"').
		    '<select name=\'ville\'  id="envoiVille"  onblur="finishEditing();" onclick="beginEditing(this);" ><option value=""> </option><option value="'.$datas['data']['villedelivery_dev'].'" selected="selected">'.$datas['data']['villedelivery_dev'].'</option></select></div>
					</div>
					<div class="row" style="display:none;" id="champAdd4"> 
						<div class="label" onmouseover="zuno.tooltip(this,ZTips.commande.sendPays);">Pays</div>
						<div class="field">'.selectTag('pays',$datas['pays'],$datas['data']['paysdelivery_dev']).'</div>
					</div>
				</fieldset>
			</div>
		</form>';			
	}
    }

    /**
     * Méthode qui génère le tableau de recherche et les résultats
     * @param array $datas Les données à afficher
     * @param string $light Précise si on ne veut que les résultats
     * @return string Le code HTML
     */
    public function searchResult($datas, $light='') {
	if($light == 'result')
	    return $this->Result($datas);
	else
	    return $this->searchForm($datas).$this->listeResult($datas);
    }

    /**
     * Méthode qui génère le formulaire de recherche
     * @param array $datas Les données nécessaires pour le formulaire
     * @return string Le code HTML
     */
    private function searchForm($datas) {
	$script = '<script type="text/javascript">function autoCompletage() { new Ajax.Autocompleter("cpCmdSearch", "cpCmdSearch_choix", "../ajaxRef.php?action=listeCp", { paramName: "value", minChars: 2, indicator : "loadingCp" , afterUpdateElement : autoCompleteHidden } ); '."\n";
	$script .= 'new Ajax.Autocompleter("affaireCmdSearch", "affaireCmdSearch_choix", "../ajaxRef.php?action=listeAffaire", { paramName: "value", minChars: 2, indicator: "loadingAff" , afterUpdateElement : autoCompleteHidden } ); '."\n";
	$script .= 'new Ajax.Autocompleter("entrepriseCmdSearch", "entrepriseCmdSearch_choix", "../ajaxRef.php?action=listeEntreprise", { paramName: "value", minChars: 2, indicator : "loadingEnt" , afterUpdateElement : autoCompleteHidden } ); '."\n";
	$script .= 'new Ajax.Autocompleter("userCmdSearch", "userCmdSearch_choix", "../ajaxRef.php?action=listeUser", { paramName: "value", minChars: 2, indicator: "loadingUsr", afterUpdateElement : autoCompleteHidden } );} '."\n";
	$script .= 'function autoCompleteHidden(text, li) { $(text.id+\'_hidden\').value = li.title; } '."\n";
	$script .= 'function ChangeCmdID(value) { $(\'idCmdSearch\').value = value; } '."\n";
	$script .= 'function verifVide(objet) { if(objet.value == "") { $(objet.id+\'_hidden\').value = ""; } } '."\n";
	$script.= "function ChangeCmdResultPage(limit,from,order,sens) {
			ChangeCmdAction('searchCmd');
			if(limit != undefined) ChangeLimit(limit);
			if(from  != undefined) ChangeFrom(from);
			if(order != undefined) ChangeOrder(order,sens);
			ChangeSubmit();
		    }
		    function ChangeCmdAction(value) {\$('listeCmdAction').value = value;}
		    function ChangeFrom(value) { \$('fromCmdSearch').value = value; }
		    function ChangeLimit(value) { \$('limitCmdSearch').value = value; }
		    function ChangeOrder(value,sens) {
			if(sens == undefined) sens = 'ASC';
			\$('orderCmdSearch').value = value;
			\$('orderSensCmdSearch').value = sens;
		    }
		    function ChangeSubmit() {
			zuno.business.formTools.sendFormAjah('searchCmd', 'pegase/CommandeListe.php','listeResultCmd');
		    }";
	$script .= 'function camenbertCmd(value, max) { $(\'cmdBudgetMini\').value = value; $(\'cmdBudgetMaxi\').value = max; ChangeCmdAction(\'searchCmd\');zuno.business.formTools.sendFormAjah(\'searchCmd\', \'pegase/CommandeListe.php\',\'listeResultCmd\'); } '."\n";
	$script .= 'function resetForm() {var form = $(\'searchFormCmd\'); form.reset(); form.select(\'input[type="hidden"]\').each(function(input) {input.clear();}); } '."\n";
	$script .= 'function exportTab() { if ($(\'divExportTableur\').style.display == \'inline\') {$(\'divExportTableur\').style.display = \'none\';} else {$(\'divExportTableur\').style.display = \'inline\';} }'."\n";
	$script .= '</script>';

	$titre = '<img title="recherche" alt="Rechercher" onload="autoCompletage();" name="img" src="../img/prospec/voir.png" /> Recherche de commandes';
	$corps = '<form name="searchCmd" action="CommandeListe.php" method="post" id="searchFormCmd">';
	$corps .= '<div id="PortletSearchCmd">';
	$corps .= '<input type="hidden" name="action" value="searchCmd" id="listeCmdAction" />';
	$corps .= '<input type="hidden" name="id_cmd" value="" id="idCmdSearch" />';
	$corps .= '<input type="hidden" name="from" value="" id="fromCmdSearch" />';
	$corps .= '<input type="hidden" name="limit" value="" id="limitCmdSearch" />';
	$corps .= '<input type="hidden" name="order" value="id_cmd" id="orderCmdSearch" />';
	$corps .= '<input type="hidden" name="orderSens" value="DESC" id="orderSensCmdSearch" />';
	$corps .= $this->interneSearch($datas);
	$corps .= '</div></form>';
	$bouton = '<a name="reset" onclick="resetForm();" style="cursor:pointer;"><img title="Reset" alt="reset" src="../img/prospec/cancel.png"/> Annuler</a>';
	$bouton .= '<a name="valider" onclick="ChangeCmdAction(\'searchCmd\');ChangeSubmit();"><img title="Valider" alt="valid" src="../img/prospec/voir.png" />Rechercher</a>';
	return $script.generateZBox($titre,$titre,$corps,$bouton,"searchBox",'');
    }

    /**
     * Méthode qui génère le contenu du block de recherche
     * @param array $datas Les données pour la recherche
     * @return string Le code HTML
     */
    private function interneSearch($datas) {
	$out = '<div class="block width50"><fieldset class="form"><legend>Critères de recherche</legend>';
	$out .= '<div class="row"><div class="label" onmouseover="zuno.tooltip(this,ZTips.commande.searchEnt);">Entreprise</div>';
	$out .= '<div class="field">'.inputTag('text', 'entreprise', '', '', '20', '', ' id="entrepriseCmdSearch" onchange="verifVide(this);"').'<img style="display:none; margin-left:5px;" id="loadingEnt" src="../img/ajax-loader.gif" alt="loader"/></div><div class="autocomplete" id="entrepriseCmdSearch_choix"></div>';
	$out .= '<input type="hidden" name="entreprise_cmd" value="" id="entrepriseCmdSearch_hidden"/></div>';
	$out .= '<div class="row"><div class="label" onmouseover="zuno.tooltip(this,ZTips.commande.searchTitre);">Titre</div>';
	$out .= '<div class="field">'.inputTag('text', 'titre_cmd', '', '', '20').'</div></div>';
	$out .= '<div class="row"><div class="label" onmouseover="zuno.tooltip(this,ZTips.commande.searchAff);">Affaire</div>';
	$out .= '<div class="field">'.inputTag('text', 'affaire', '', '', '20', '', ' id="affaireCmdSearch" onchange="verifVide(this);"').'<img style="display:none; margin-left:5px;" id="loadingAff" src="../img/ajax-loader.gif" alt="loader"/></div><div class="autocomplete" id="affaireCmdSearch_choix"></div>';
	$out .= '<input type="hidden" name="id_aff" value="" id="affaireCmdSearch_hidden"/></div>';
	$out .= '<div class="row"><div class="label" onmouseover="zuno.tooltip(this,ZTips.commande.searchStatut);">Status</div>';
	$out .= '<div class="field">'.selectTag('status_cmd',$datas['status'],'').'</div></div>';
	$out .= '<div class="row"><div class="label" onmouseover="zuno.tooltip(this,ZTips.commande.searchComm);">Commercial</div>';
	$out .= '<div class="field">'.inputTag('text', 'commercial', '', '', '20', '', ' id="userCmdSearch" onchange="verifVide(this);"').'<img style="display:none; margin-left:5px;" id="loadingUsr" src="../img/ajax-loader.gif" alt="loader"/></div><div class="autocomplete" id="userCmdSearch_choix"></div>';
	$out .= '<input type="hidden" name="commercial_cmd" value="" id="userCmdSearch_hidden"/></div>';
	$out .= '<div class="row"><div class="label" onmouseover="zuno.tooltip(this,ZTips.commande.searchCP);">Département</div>';
	$out .= '<div class="field">'.inputTag('text', 'cp', '', '', '20', '', ' id="cpCmdSearch" onchange="verifVide(this);"').'<img style="display:none; margin-left:5px;" id="loadingCp" src="../img/ajax-loader.gif" alt="loader"/></div><div class="autocomplete" id="cpCmdSearch_choix"></div>';
	$out .= '<input type="hidden" name="cp_ent" value="" id="cpCmdSearch_hidden"/></div>';
	$out .= '<div class="row"><div class="label" onmouseover="zuno.tooltip(this,ZTips.commande.searchBudgMin);">Budget min</div>';
	$out .= '<div class="field">'.inputTag('text', 'sommeFHT_cmd', '', '', '20', '', ' id="cmdBudgetMini" ').'</div></div>';
	$out .= '<div class="row"><div class="label" onmouseover="zuno.tooltip(this,ZTips.commande.searchBudgMax);">Budget maxi</div>';
	$out .= '<div class="field">'.inputTag('text', 'sommeFHT_cmd2', '', '', '20', '', ' id="cmdBudgetMaxi" ').'</div></div>';
	$out .= '</fieldset></div>';
	$out .= '<div class="block width50"><fieldset class="form"><legend>Répartition des ventes</legend>';
	$out .= '<div class="row">'.shell_exec('php ../Img.CommandeChart.php').'</div>';
	$out .= '</fieldset></div>';
	return $out;
    }

    /**
     * Méthode qui affiche la liste des résultats d'une recherche
     * @param array $datas Les résultats à afficher
     * @return string Le code HTML
     */
    public function listeResult($datas) {
	return '<div id=listeResultCmd>'.$this->Result($datas).'</div>';
    }

    /**
     * Méthode qui affiche les résultats d'une recherche
     * @param array $datas Les résultats à afficher
     * @return string Le code HTML
     */
    private function Result($datas) {
	$bureau = false;
	$suffixeLink = '../';
	$linkOrderFunction = 'tagLinkOrderSwitch';
	if (array_key_exists('isHistoVisit',$datas) and $datas['isHistoVisit']) {
	    $suffixeLink = './';
	    $linkOrderFunction = 'tagNoLinkOrderSwitch';
	    $bureau = true;
	}

	$out = "<script type=\"text/javascript\">
		function cmdDoExportTableur(value) {
		    $('resultCmdAction').value = 'exportTableur';
		    $('resultCmdExportType').value = value;
		    $('resultFormCmd').submit();
		}
		</script>";
	$out .= '<div class="blockTable">
		    <form id="resultFormCmd" method="post" action="CommandeListe.php" name="resultCmd">
		    <input type="hidden" name="action" value="" id="resultCmdAction" />
		    <input type="hidden" name="exportType" value="" id="resultCmdExportType" />
		    <table cellspacing="0"><tbody>';
	$out .= '<tr class="titre">';
	$out .= '<th class="barre"><img src="../img/prospec/multiple-check.png" onclick="toggleCheckbox(\'resultFormCmd\');"/></th>';
	$out .= '<th class="barre">'.$this->$linkOrderFunction('ID','id_cmd',$datas['order'],$datas['orderSens'],$datas['limit'],'ChangeCmdResultPage',$suffixeLink).'</th>';
	$out .= '<th class="center barre">Entreprise '.$this->$linkOrderFunction('Entreprise','nom_ent',$datas['order'],$datas['orderSens'],$datas['limit'],'ChangeCmdResultPage',$suffixeLink).'</th>';
	$out .= '<th class="center barre">'.$this->$linkOrderFunction('Contact','c1.nom_cont',$datas['order'],$datas['orderSens'],$datas['limit'],'ChangeCmdResultPage',$suffixeLink).'</th>';
	$out .= '<th>'.$this->$linkOrderFunction('Titre','titre_cmd',$datas['order'],$datas['orderSens'],$datas['limit'],'ChangeCmdResultPage',$suffixeLink).'</th>';
	$out .= '<th>'.$this->$linkOrderFunction('Montant Vente','sommeHT_cmd',$datas['order'],$datas['orderSens'],$datas['limit'],'ChangeCmdResultPage',$suffixeLink).'</th>';
	$out .= '<th>'.$this->$linkOrderFunction('Montant Achat','sommeFHT_cmd',$datas['order'],$datas['orderSens'],$datas['limit'],'ChangeCmdResultPage',$suffixeLink).'</th>';
	$out .= '<th>Marge </th>';
	$out .= '<th class="barre">'.$this->$linkOrderFunction('Status','status_cmd',$datas['order'],$datas['orderSens'],$datas['limit'],'ChangeCmdResultPage',$suffixeLink).'</th>';
	$out .= '<th class="last center">Actions</th>';
	$out .= '</tr>';
	$alternance =  0;
	foreach($datas['data'] as $v) {
	    $action = '';
	    //traitement des boutons de modification et d'action
	    // recherche du fichier PDF
	    if (file_exists($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoAffaire']['dir.affaire'].$v['dir_aff'].$GLOBALS['ZunoCommande']['file.suffixe'].$v['id_cmd'].'.pdf') and verifDroitsAuto('commande', 62, $v['commercial_cmd']))
		$action   .= "<a href=\"${suffixeLink}pegase/Commande.php?id_commande=".$v['id_cmd']."&action=VoirBDCC\">".imageTag($suffixeLink.'img/prospec/commande.pdf.png','PDF')."</a> ";
	    // Quand status créer
	    if (($v['status_cmd'] == "1")and(verifDroitsAuto('commande', 13, $datas['data']['commercial_cmd']))) {
		$action   .= "<a href=\"#\" onclick=\"return zuno.popup.open('${suffixeLink}pegase/Commande.php','id_commande=".$v['id_cmd']."&action=supp',600,290,'','','resize','devisDelete');\">".imageTag($suffixeLink.'img/prospec/commande.delete.png','Supprimer')."</a> ";
		$action .= '<a href="'.$suffixeLink.'.pegase/Commande.php?id_cmd='.$v['id_cmd'].'" title="Enregistrer" name="enreg"><img align="middle" title="Valider" alt="enreg" name="img" src="'.$suffixeLink.'img/prospec/commande.valid.png"/></a>';
	    }
	    // Quand status Validé
	    elseif (($v['status_cmd'] == "2")and(verifDroitsAuto('commande', 60, $datas['data']['commercial_cmd'])))
		$action .= '<a href="Commande.php?id_cmd='.$v['id_cmd'].'" title="Générer" name="générer"><img align="middle" title="Générer" alt="générer" name="img" src="'.$suffixeLink.'img/prospec/commande.generate.png"/></a>';
	    // Quand status Généré
	    elseif (($v['status_cmd'] == "3")and(verifDroitsAuto('commande', 50, $datas['data']['commercial_cmd'])))
		$action .= '<a href="Commande.php?id_cmd='.$v['id_cmd'].'" title="Envoyer" name="envoyer"><img align="middle" title="Envoyer" alt="envoyer" name="img" src="'.$suffixeLink.'img/prospec/commande.send.png"/></a>';

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

	    $marge = $v['sommeHT_cmd']-$v['sommeFHT_cmd'];
	    $score = commandeModel::ScoreCommande($v['id_cmd'], $bureau);
	    $out .= '<tr class="altern'.$alternance.'">';
	    $out .= '<td class="barre" style="width:10px"><input type="checkbox" name="select[]" value="'.$v['id_cmd'].'" checked="checked"/></td>';
	    $out .= '<td class="barre" style="cursor : pointer; color : #'.$score['color'].' !important;" onclick="window.location=\''.$suffixeLink.'pegase/Commande.php?id_cmd='.$v['id_cmd'].'\'"><b>'.$score['img'].$v['id_cmd'].'</b></td>';
	    $out .= '<td class="barre" style="cursor : pointer; color : #'.$score['color'].' !important;" onclick="window.location=\''.$suffixeLink.'pegase/Commande.php?id_cmd='.$v['id_cmd'].'\'">'.$v['contact'].'</td>';
	    $out .= '<td class="barre" style="cursor : pointer; color : #'.$score['color'].' !important;" onclick="window.location=\''.$suffixeLink.'pegase/Commande.php?id_cmd='.$v['id_cmd'].'\'">'.$v['civ_cont'].' '.$v['prenom_cont'].' '.$v['nom_cont'].'</td>';
	    $out .= '<td class="barre" style="cursor : pointer; color : #'.$score['color'].' !important;" onclick="window.location=\''.$suffixeLink.'pegase/Commande.php?id_cmd='.$v['id_cmd'].'\'">'.$v['titre_cmd'].'</td>';
	    $out .= '<td class="barre" style="cursor : pointer; color : #'.$score['color'].' !important;" onclick="window.location=\''.$suffixeLink.'pegase/Commande.php?id_cmd='.$v['id_cmd'].'\'">'.$v['sommeHT_cmd'].' &euro;</td>';
	    $out .= '<td class="barre" style="cursor : pointer; color : #'.$score['color'].' !important;" onclick="window.location=\''.$suffixeLink.'pegase/Commande.php?id_cmd='.$v['id_cmd'].'\'">'.$v['sommeFHT_cmd'].' &euro;</td>';
	    $out .= '<td class="barre" style="cursor : pointer; color : #'.$score['color'].' !important;" onclick="window.location=\''.$suffixeLink.'pegase/Commande.php?id_cmd='.$v['id_cmd'].'\'">'.$marge.' &euro;</td>';
	    $out .= '<td class="barre" style="cursor : pointer; color : #'.$score['color'].' !important;" onclick="window.location=\''.$suffixeLink.'pegase/Commande.php?id_cmd='.$v['id_cmd'].'\'">'.$v['nom_stcmd'].'</td>';
	    $out .= '<td class="right">'.$action.'</td>';
	    $out .= '</tr>';
	    $alternance = (($alternance+1) % 2);
	}

	$out .= '</tbody></table>';
	$out .= $this->ResultGroupActionForm();
	$out .= '</form></div>';
	if (array_key_exists('isHistoVisit',$datas) and $datas['isHistoVisit'])
	    $titre = $this->resultNavigationTitle($datas['from'],$datas['limit'],$datas['total'],'commandeHisto');
	else $titre = $this->resultNavigationTitle($datas['from'],$datas['limit'],$datas['total'],'commande');
	$bouton .= $this->resultNavigation($datas['from'],$datas['limit'],$datas['total'],"ChangeCmdResultPage",$suffixeLink);
	loadPlugin(array('OOConverter'));
	$availableConvFormat = OOConverterAvailable('spreadsheet');
	$bouton .= '<div style="display:none;" id="divExportTableur">'.selectTag('format_export', $availableConvFormat, '', '', ' onchange="cmdDoExportTableur(this.value);" ').'</div>';
	$bouton .= '<a name="export" style="cursor:pointer;" onclick="exportTab();"><img title="exportation tableur" alt="exportation tableur" src="'.$suffixeLink.'img/prospec/export-csv.png"/> Export Tableur</a>';
	$bouton .= $this->ResultGroupActionBouton($suffixeLink);

	if (array_key_exists('isHistoVisit',$datas) and $datas['isHistoVisit'])
	    $bouton = '';
	return generateZBox($titre,$titre,$out,$bouton,"searchListeCommande",'');
    }

    private function ResultGroupActionForm() {
	$sql = new commandeModel();
	$out = '<div id="divGroupAction" style="display: none"><div class="block width50">';
	$fieldset = new ZunoFieldset('Type d\'action');
	$fieldset->ligneFree(inputTag('radio', 'groupedAction', '', '', '', 'reinit', ' onclick="toggleSubGroupedAction(\'groupedSubActionReinit\')"'),'Re-initialiser ces commandes','ZTips.commande.groupActionReinit');
	$fieldset->ligneFree(inputTag('radio', 'groupedAction', '', '', '', 'recu', ' onclick="toggleSubGroupedAction(\'groupedSubActionRecu\')"'),'Commandes client reçues','ZTips.commande.groupActionRecu');
	$fieldset->ligneFree(inputTag('radio', 'groupedAction', '', '', '', 'bdcfenvoye', ' onclick="toggleSubGroupedAction(\'groupedSubActionBDCFEnvoye\')"'),'Commandes fournisseurs envoyées','ZTips.commande.groupActionBDCFEnvoye');
	$fieldset->ligneFree(inputTag('radio', 'groupedAction', '', '', '', 'bdcfrecu', ' onclick="toggleSubGroupedAction(\'groupedSubActionBDCFRecu\')"'),'Commandes fournisseurs reçu par vos partenaires','ZTips.commande.groupActionBDCFRecu');
	$fieldset->ligneFree(inputTag('radio', 'groupedAction', '', '', '', 'bdcfvalid', ' onclick="toggleSubGroupedAction(\'groupedSubActionBDCFValid\')"'),'Commandes fournisseurs validés par vos partenaires','ZTips.commande.groupActionBDCFValid');
	$fieldset->ligneFree(inputTag('radio', 'groupedAction', '', '', '', 'expedie', ' onclick="toggleSubGroupedAction(\'groupedSubActionExpedie\')"'),'Commandes expediées','ZTips.commande.groupActionExpedie');
	$fieldset->ligneFree(inputTag('radio', 'groupedAction', '', '', '', 'receptionne', ' onclick="toggleSubGroupedAction(\'groupedSubActionReceptionne\')"'),'Commandes réceptionnées par le client','ZTips.commande.groupActionReceptionne');
	$fieldset->ligneFree(inputTag('radio', 'groupedAction', '', '', '', 'termine', ' onclick="toggleSubGroupedAction(\'groupedSubActionTermine\')"'),'Commandes terminées','ZTips.commande.groupActionTermine');
	$fieldset->ligneFree(inputTag('radio', 'groupedAction', '', '', '', 'archivate', ' onclick="toggleSubGroupedAction(\'groupedSubActionArchive\')"'),'Archivage des commandes','ZTips.commande.groupActionArchive');
	$fieldset->ligneFree(inputTag('radio', 'groupedAction', '', '', '', 'changeAttribute', ' onclick="toggleSubGroupedAction(\'groupedSubActionChangeAttribute\')"'),'Changer un attribut par lot','ZTips.commande.groupActionChangeAttribute');
	$out.= $fieldset->generateFieldset();
	$out.= '</div><div class="block width50" id="groupedSubAction">';
	$fieldset = new ZunoFieldset('Re-initialiser ces commandes','groupedSubActionReinit');
	$fieldset->addOtherLigne('<div class="row"><i>Aucune action sécifique n\'est requise</i></div>');
	$out.= $fieldset->generateFieldset();
	$fieldset = new ZunoFieldset('Commandes client reçues','groupedSubActionRecu');
	$fieldset->ligneInput('Commentaire', 'commentaireRecu');
	$out.= $fieldset->generateFieldset();
	$fieldset = new ZunoFieldset('Commandes fournisseurs envoyées','groupedSubActionBDCFEnvoye');
	$fieldset->ligneInput('Complément d\'envoi', 'complementdeliveryBDCFEnvoye');
	$fieldset->ligneInput('Commentaire', 'commentaireBDCFEnvoye');
	$out.= $fieldset->generateFieldset();
	$fieldset = new ZunoFieldset('Commandes fournisseurs reçu par vos partenaires','groupedSubActionBDCFRecu');
	$fieldset->ligneInput('Commentaire', 'commentaireBDCFRecu');
	$out.= $fieldset->generateFieldset();
	$fieldset = new ZunoFieldset('Commandes fournisseurs validés par vos partenaires','groupedSubActionBDCFValid');
	$fieldset->ligneInput('Commentaire', 'commentaireBDCFValid');
	$out.= $fieldset->generateFieldset();
	$fieldset = new ZunoFieldset('Commandes expediées','groupedSubActionExpedie');
	$fieldset->ligneInput('Commentaire', 'commentaireExpedie');
	$out.= $fieldset->generateFieldset();
	$fieldset = new ZunoFieldset('Commandes réceptionnées par le client','groupedSubActionReceptionne');
	$fieldset->ligneInput('Commentaire', 'commentaireReceptionne');
	$out.= $fieldset->generateFieldset();
	$fieldset = new ZunoFieldset('Commandes terminées','groupedSubActionTermine');
	$fieldset->ligneInput('Commentaire', 'commentaireTermine');
	$out.= $fieldset->generateFieldset();
	$fieldset = new ZunoFieldset('Archivage des commandes','groupedSubActionArchive');
	$fieldset->addOtherLigne('<div class="row"><i>Seul les commandes n\'ayant plus de facture en cours de traitement seront archivées.</i></div>');
	$out.= $fieldset->generateFieldset();
	$fieldset = new ZunoFieldset('Changer un attribut par lot','groupedSubActionChangeAttribute');
	$fieldset->ligneInput('Titre de la commande', 'titre_cmd');
	$fieldset->ligneFree('TVA',selectTVATag("tva_cmd", array(), '', '', '', true));
	$fieldset->ligneSelect('Mode de règlement', 'modereglement_cmd','',$sql->getModeReglement());
	$fieldset->ligneSelect('Cond. de règlement', 'condireglement_cmd','',$sql->getCondReglement());
	$fieldset->ligneSelect('Commercial', 'commercial_cmd','',$sql->getUser());
	$out.= $fieldset->generateFieldset();
	$out.= '</div>';
	$out.= '<div class="block width50">';
	$fieldset = new ZunoFieldset('Executer le changement');
	$fieldset->ligneLinkTo('','javascript:submitGroupAction(\'resultFormCmd\',\'resultCmdAction\');" class="bouton','Effectuer les changements','../img/prospec/voir.png');
	$out.= $fieldset->generateFieldset();
	$out.= '</div></div>';
	return $out;
    }

    private function ResultGroupActionBouton($suffixeLink) {
	$bouton = '<a name="groupAction" style="cursor:pointer;" onclick="toggleGroupedAction(\'divGroupAction\');"><img title="actions groupées" alt="actions groupées" src="'.$suffixeLink.'img/prospec/work-multiple.png"/> Actions groupées</a>';
	return $bouton;
    }

}
function BureauMyCommande() {
    $limit = 10;
    $req = new commandeModel();
    $total = $req->getDataForHistoriqueVisit($limit,$type = 'COUNT');
    $result= $req->getDataForHistoriqueVisit($limit);
    $datas['total'] = $total[1][0]['counter'];
    $datas['data'] = $result[1];
    $datas['from'] = 0;
    $datas['limit'] = $limit;
    $datas['isHistoVisit'] = true;
    $view = new commandeView();
    if($datas['total'] > 0)
	return $view->listeResult($datas);
    else return "";
}
?>
