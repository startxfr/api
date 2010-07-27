<?php
loadPlugin(array('ZModels/FactureModel','ZDoc/FactureDoc', 'ZunoRenduHTML'));
loadPlugin('ZView/GeneralView');


class factureView extends generalView {
    public function view($datas, $light, $mess) {
        $script = '<script type="text/javascript">function autoCompletage() { new Ajax.Autocompleter("ACcontact_fact", "ACcontact_fact_choix", "../ajaxRef.php?action=listeContact&id_ent='.$datas['data']['id_ent'].'", { paramName: "value", minChars: 2, indicator: "loadingCont",  afterUpdateElement : autoCompleteHidden } ); ';
        $script .= "\n".'new Ajax.Autocompleter("ACproduit_ajout_fact", "ACproduit_ajout_fact_choix", "../ajaxRef.php?action=listeProduit", { paramName: "value", minChars: 2, afterUpdateElement : calculetteAjoutProd } ); ';
        $script .= "\n".'new Ajax.Autocompleter("ACcontact_achat_fact", "ACcontact_achat_fact_choix", "../ajaxRef.php?action=listeContact&id_ent='.$datas['data']['id_ent'].'", { paramName: "value", minChars: 2, indicator: "loadingCont2", afterUpdateElement : autoCompleteHidden } );} ';

        $script .= "\n".'function calculetteAjoutProd(text, li) {id = li.title.split(\'-_-\'); $(text.id+\'hidden\').value = id[0]; $(\'prix_Ajout_prod_fact\').value = id[2]; $(\'desc_Ajout_prod_fact\').innerHTML = id[3]; calculetteTotal();} ';
        $script .= "\n".'function calculetteTotal(id) {  if(id == undefined) { var qtt = $(\'quantite_Ajout_prod_fact\').value; var prix = $(\'prix_Ajout_prod_fact\').value; var rem = $(\'remise_Ajout_prod_fact\').value; $(\'total_Ajout_prod_fact\').value = qtt*prix*(1-rem/100);} else { var qtt = $(\'quantite_Ajout_prod_fact\'+id).value; var prix = $(\'prix_Ajout_prod_fact\'+id).value; var rem = $(\'remise_Ajout_prod_fact\'+id).value; $(\'total_Ajout_prod_fact\'+id).value = qtt*prix*(1-rem/100);} } ';
        $script .= "\n".'function autoCopieChamps(id) { $(\'quantite_Ajout_prod_fact\').value = $(\'quantite_Ajout_prod_fact\'+id).value; $(\'prix_Ajout_prod_fact\').value = $(\'prix_Ajout_prod_fact\'+id).value; $(\'remise_Ajout_prod_fact\').value = $(\'remise_Ajout_prod_fact\'+id).value; $(\'total_Ajout_prod_fact\').value = $(\'total_Ajout_prod_fact\'+id).value; $(\'desc_Ajout_prod_fact\').value = $(\'desc_Ajout_prod_fact\'+id).value;} ';
        $script .= "\n".'function afficherFormModif(id) { if($(\'suisJeEnModif\').value != 0) { $($(\'suisJeEnModif\').value+\'td1\').style.display = \'none\'; $($(\'suisJeEnModif\').value+\'td0\').style.display = \'table-row\'; } $(\'suisJeEnModif\').value = id; $(id+\'td0\').style.display = \'none\'; $(id+\'td1\').style.display = \'table-row\'; $(\'tdAjoutProdFacture\').style.display = \'none\'; $(\'ACproduit_ajout_facthidden\').value = $(\'ID\'+id).innerHTML;} ';
        $script .= "\n".'function chargerAction(value) { $(\'actionFactureProd\').value = value;} ';
        $script .= "\n".'function chargerProduit(value) { $(\'produitFactureProd\').value = value;} ';
        $script .= "\n".'function annulerModif() {$($(\'suisJeEnModif\').value+\'td1\').style.display=\'none\'; $($(\'suisJeEnModif\').value+\'td0\').style.display=\'table-row\';  $(\'ACproduit_ajout_facthidden\').value =""; $(\'tdAjoutProdFacture\').style.display = \'table-row\'; $(\'suisJeEnModif\').value = 0;} ';
        $script .= "\n".'function ChangeCannevas() { var choix = document.getElementById(\'CannevasListe\').value; if(choix.length > 0) { document.getElementById(\'MessageTitle\').value =  CannevasTitre[choix]; document.getElementById(\'MessageToSend\').value = Cannevas[choix]; }  } ';
        $script .= "\n".'function ChangeFactureAction(value) { $(\'MakeFactureAction\').value = value; } ';
        $script .= "\n".'function verifAutocompleteur(champ) {if(champ.value == "" || champ.value == null) {$(champ.id+\'hidden\').value = "";} else if(!$(champ.id+\'_choix\').hasChildNodes()) {alert(\'Cette valeur n\\\'existe pas.\'); champ.value=$(champ.id+\'old\').value; setTimeout(function() { champ.focus(); }, 100) } } ';
        $script .= "\n".'function champAdd(display) {$(\'champDestinataire\').style.display = $(\'champAdd1\').style.display = $(\'champAdd2\').style.display = $(\'champAdd3\').style.display = $(\'champAdd4\').style.display = display;} ';
        $script .= "\n".'function changeFactureFormat(value) { $(\'formatFacturePDF\').value = value;} ';
        $script .= "\n".'function montreForm(value) {if(value == \'mail\') {  $(\'champDestinataire\').style.display = $(\'champFax\').style.display = \'none\'; $(\'champCC\').style.display = \'table-row\'; champAdd(\'none\'); $(\'champEmail\').style.display = \'table-row\';} else if(value == \'fax\') { champAdd(\'none\'); $(\'champDestinataire\').style.display = $(\'champFax\').style.display = \'table-row\'; $(\'champEmail\').style.display =\'none\'; $(\'champCC\').style.display = \'none\';} else if(value == \'courrier\') {  $(\'champFax\').style.display = \'none\'; $(\'champCC\').style.display = $(\'champEmail\').style.display = \'none\'; champAdd(\'table-row\')} else {$(\'champEmail\').style.display = $(\'champFax\').style.display = \'none\'; $(\'champCC\').style.display = \'none\'; champAdd(\'none\'); } } ';
        $script .= "\n".'function affRen(cbox) { if(cbox.checked) { $(\'mailF\').parentNode.parentNode.style.display = \'table-row\'; $(\'statusF\').parentNode.parentNode.style.display = \'table-row\'; $(\'fin_renID\').parentNode.parentNode.style.display = \'table-row\'; $(\'selectF\').parentNode.parentNode.style.display = \'table-row\'; } else { $(\'mailF\').parentNode.parentNode.style.display = \'none\'; $(\'fin_renID\').parentNode.parentNode.style.display = \'none\'; $(\'selectF\').parentNode.parentNode.style.display = \'none\'; $(\'statusF\').parentNode.parentNode.style.display = \'none\'; } } ';
        $script .= "\n".'function saveProd() {' .
                'zuno.ajax.post.json(\'../produit/Produit.php\', ' .
                'zuno.business.formTools.getParamFromForm(\'formProduit\'), ' .
                'function(xhr) { ' .
                'var json = xhr.responseText.evalJSON(); ' .
                'if(json.error != undefined){$(\'error\').innerHTML = json.error;}'.
                'else { $(\'ACproduit_ajout_fact\').value = \'[\'+json.datas.id_prod+\']\'; ' .
                '$(\'desc_Ajout_prod_fact\').value = json.datas.description_prod; ' .
                '$(\'prix_Ajout_prod_fact\').value = json.datas.prix_prod; ' .
                '$(\'total_Ajout_prod_fact\').value = json.datas.prix_prod; ' .
                '$(\'ACproduit_ajout_facthidden\').value = json.datas.id_prod; ' .
                'zuno.popup.close(); }' .
                '} );} ';
        $script .= "\n".'function searchProdReturn(id_prod,description_prod,prix_prod) {' .
                '$(\'ACproduit_ajout_fact\').value = \'[\'+id_prod+\']\'; ' .
                '$(\'desc_Ajout_prod_fact\').value = description_prod; ' .
                '$(\'prix_Ajout_prod_fact\').value = prix_prod; ' .
                '$(\'total_Ajout_prod_fact\').value = prix_prod; ' .
                '$(\'ACproduit_ajout_facthidden\').value = id_prod; ' .
                'zuno.popup.close(); ' .
                '} ';
        $script .= 'var cont = new Array();'."\n".'function autoLoadDatas(val) { if (val != \'\') {$(\'nomCB\').value = cont[val][\'nom\'];
		$(\'prenomCB\').value = cont[val][\'prenom\'];
		$(\'mailCB\').value = cont[val][\'mail\'];
		$(\'civCB\').value = cont[val][\'civ\'];
		if(cont[val][\'carte\'] != \'\')
		$(\'carteCB\').value = \'XXXX \'+cont[val][\'carte\'];
		$(\'dateCB\').value = cont[val][\'date\'];
		if(cont[val][\'carte\'] != \'\')
		$(\'cvvCB\').value = \'XXX\';
		$(\'walletCB\').value = cont[val][\'wallet\'];}
		else
		{$(\'nomCB\').value = \'\';
		$(\'prenomCB\').value = \'\';
		$(\'civCB\').value = \'\';
		$(\'mailCB\').value = \'\';
		$(\'carteCB\').value = \'\';
		$(\'dateCB\').value = \'\';
		$(\'cvvCB\').value = \'\';
		$(\'walletCB\').value = \'\';}
		$(\'modifiedCB\').value = \'false\';
		}'."\n";
        $script .= "\n".'function change() {$(\'modifiedCB\').value = \'true\';}';
        $script .= "\n".'function autoRemplir(dest, id, val){$(dest).value=val; $(dest+\'hidden\').value=id; zuno.popup.close();}';
        $script .= '</script>';

        $datas['nom'] = ($datas['data']['type_fact'] == 'Facture')?'la facture' : 'l\'avoir';

        if($light == 'interneInfos')
            return '<span class="important" style="text-align:center;">'.$mess.'</span>'.$this->interneInfos($datas);
        elseif($light == 'interneProduit')
            return '<span class="important" style="text-align:center;">'.$mess.'</span>'.$this->interneProduits($datas);
        elseif($light == 'interneTraitement')
            return '<span class="important" style="text-align:center;">'.$mess.'</span>'.$this->interneTraitement($datas);
        elseif($light == 'Traitement')
            return $this->traitement($datas, '<span class="important" style="text-align:center;">'.$mess.'</span>');
        elseif($light == 'ficheComplete')
            return '<span class="important" style="text-align:center;">'.$mess.'</span>'.$this->infos($datas).$this->produits($datas).$this->traitement($datas);
        else
            return $script.'<div id="ficheComplete">'.$this->infos($datas).$this->produits($datas).$this->traitement($datas).'</div>';

    }

    private function infos($datas) {
        $data = $datas['data'];
        if(array_key_exists('out',$GLOBALS))
            $GLOBALS['out']->setTitle('Fiche facture "'.$this->prepareAffichageID($data).'" - '.$data['nom_ent'],'',true);
        $titre = '<img src="../img/prospec/facture.png" alt="facture" title="Facture" /> Informations sur '.$datas['nom'].' '.$this->prepareAffichageID($data);

        $corps = '<form name="ModifInfosFacture" action="#" method="post">';
        $corps .= '<input type="hidden" name="action" value="modifFacture" />';
        $corps .= '<input type="hidden" name="id_fact" value="'.$data['id_fact'].'" />';
        if($data['id_fact'] == $datas['ren']['idChamp_ren'] or $data['ren_fact'] == "")
            $corps .= '<input type="hidden" name="ren_fact" value="'.$data['ren_fact'].'" />';
        $corps .= '<div id="ContenuFormInfosFacture">';
        $corps .= $this->interneInfos($datas);
        $corps .= '</div></form>';
        $pied = '<a title="Reset" href="javascript:document.ModifInfosFacture.reset();" ><img onload="autoCompletage();" align="middle" title="Effacer" alt="Effacer" src="../img/prospec/cancel.png"/> Recommencer</a>';
        $pied .= '<a name="submit" onclick="zuno.business.formTools.sendFormAjah(\'ModifInfosFacture\', \'facturier/Facture.php\',\'ContenuFormInfosFacture\');" style="cursor: pointer;"><img align="middle" title="Enregistrer" alt="Enregistrer" src="../img/prospec/record.png"/> Modifier cette facture</a>';
        if($datas['last'] == $data['id_fact'] and $data['status_fact'] < 3)
            $pied .= '<a name="supprimer" onclick="zuno.popup.open(\'Facture.php\',\'action=popupSupp&facture='.$data['id_fact'].'\',200,150);"><img alt="Supp" title="Supprimer cette facture" src="../img/prospec/facture.delete.png"  /> Supprimer cette facture</a>';
        $pied .= '<a title="Actualités" onclick="return zuno.popup.open(\'Facture.php\', \'action=actuFacture&id_facture='.$data['id_fact'].'\', \'800\', \'450\', \'\', \'\', \'resize\', \'Actualités\');" ><img align="middle" title="Actualités" alt="Actualités" src="../img/prospec/actu.png"/> Historique de cette facture</a>';
        if ($data['status_fact'] == '6') {
            $pied = '<a title="Actualités" onclick="return zuno.popup.open(\'Facture.php\', \'action=actuFacture&id_facture='.$data['id_fact'].'\', \'800\', \'450\', \'\', \'\', \'resize\', \'Actualités\');" ><img onload="autoCompletage();" align="middle" title="Actualités" alt="Actualités" src="../img/prospec/actu.png"/> Historique de cette facture</a>';
        }
        return generateZBox($titre,$titre,$corps,$pied,'infosFacture','');
    }
    private function interneInfos($datas) {
        $data = $datas['data'];
        if ($data['nom_ent'] == '')
            $data['nom_ent'] = '<i>Particulier</i>';
        if ($data['type_ent'] != '')
            $data['nom_ent'] = imageTag('../img/'.$GLOBALS['PropsecConf']['dir.img'].'TypeEntreprise/'.$data['type_ent'].'.png',$data['nom_tyent']).' '.$data['nom_ent'];
        else $data['nom_ent'] = imageTag('../img/'.$GLOBALS['PropsecConf']['dir.img'].'TypeEntreprise/particulier.png','particulier').' <i>Particulier</i>';
        $tva = ($data['tauxTVA_fact'] != "") ? $data['tauxTVA_fact'] : $GLOBALS['zunoClientStatut']['tauxTVA'];

        $out = '';
        $out .= '<div class="block width50">
		<fieldset class="form" style="height: 180px">
		<legend>Informations sur '.$datas['nom'].'</legend>
		<div class="row">
			<div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.infoDateCree);">Créé le : </div>
			<div class="field">'.DateUniv2Human($data['daterecord_fact'], 'simpleLong').'</div>
		</div>
		<div class="row">
			<div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.infoDateModif);">Modifié le : </div>
			<div class="field">'.DateUniv2Human($data['datemodif_fact'], 'simpleLong').'</div>
		</div>
		<div class="row">
			<div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.statut+\'<br/>Statut de cette facture : '.addslashes($data['description_stfact']).'\');">Statut</div>
			<div class="field" title="'.$data['description_stfact'].'" style="color: #'.$data['color_stfact'].'"><div class="square" style="background-color:#'.$data['color_stfact'].';">&nbsp;</div> '.$data['nom_stfact'].'</div>
		</div>
		<div class="row">
			<div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.infoBDCC);">BDC Client</div>
			<div class="field">'.inputTag('text', "BDCclient_fact", '', '', '16', $data['BDCclient_fact']).'</div>
		</div>
		<div class="row">
			<div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.infoTitre);">Titre</div>
			<div class="field">'.inputTag('text', 'titre_fact', '', '', '35', $data['titre_fact']).'</div>
		</div>
		<div class="row oblig">
			<div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.infotauxTva);">Taux TVA</div>
			<div class="field">'.selectTVATag('tauxTVA_fact', array(), $tva, '', '', false).'</div>
		</div>
		</fieldset>
		</div>
		<div class="block width50">
		<fieldset class="form" style="height: 180px">
		<legend>Contacts de '.$datas['nom'].'</legend>
		<div class="row">
			<div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.entreprise);">Entreprise</div>
			<div class="field"><a href="#" id="entrepriseFiche" onclick="return zuno.popup.open(\'../prospec/PopupEntreprise.php\',\'id_ent='.$data['id_ent'].'\',950,220,\'\',\'\',\'resize\',\'Entreprise\');" title="#" >'.$data['nom_ent'].'</a></div>
		</div>
		<div class="row oblig">
			<div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.contactTech);">Contact Tech</div>
			<div class="field">'.inputTag('text', 'contact_fact2','icon2', '', '16',$data['civ_cont'].' '.$data['prenom_cont'].' '.$data['nom_cont'], ' id="ACcontact_fact" onchange="verifAutocompleteur(this);" ').'<a name="addCont" title="Ajouter un contact" onclick="return zuno.popup.open(\'Facture.php\', \'action=addContFact&entreprise='.$data['id_ent'].'&idChamp=ACcontact_fact\',800,300);"><img alt="cont" title="Ajouter un contact" src="../img/prospec/add.png" /></a><a name="search" onclick="return zuno.popup.open(\'../prospec/PopupEntreprise.php\', \'action=searchContDev&entreprise='.$data['id_ent'].'&idChamp=ACcontact_fact\',850,300);"><img alt="recherche" src="../img/ajaxField.png" /></a><img style="display:none; margin-left:5px;" id="loadingCont" src="../img/ajax-loader.gif" alt="loader"/></div>
			<input type="hidden" name="contact_fact" id="ACcontact_facthidden" value="'.$data['contact_fact'].'" /><input type="hidden" name="contold" id="ACcontact_factold" value="'.$data['civ_cont'].' '.$data['prenom_cont'].' '.$data['nom_cont'].'" />
			<div id="ACcontact_fact_choix" class="autocomplete"></div>
		</div>
		<div class="row oblig">
			<div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.contactAchat);">Contact Achat</div>
			<div class="field">'.inputTag('text', 'contact_achat_fact2','icon2', '', '16',$data['civ_achat'].' '.$data['prenom_achat'].' '.$data['nom_achat'], ' id="ACcontact_achat_fact" onchange="verifAutocompleteur(this);"').'<a name="addCont" title="Ajouter un contact" onclick="return zuno.popup.open(\'Facture.php\', \'action=addContFact&entreprise='.$data['id_ent'].'&idChamp=ACcontact_achat_fact\',800,300);"><img alt="cont" title="Ajouter un contact" src="../img/prospec/add.png" /></a><a name="search" onclick="return zuno.popup.open(\'../prospec/PopupEntreprise.php\', \'action=searchContDev&entreprise='.$data['id_ent'].'&idChamp=ACcontact_achat_fact\',850,300);"><img alt="recherche" src="../img/ajaxField.png" /></a><img style="display:none; margin-left:5px;" id="loadingCont2" src="../img/ajax-loader.gif" alt="loader"/></div>
			<input type="hidden" name="contact_achat_fact" id="ACcontact_achat_facthidden" value="'.$data['contact_achat_fact'].'" /><input type="hidden" name="oldCont" id="ACcontact_achat_factold" value="'.$data['civ_achat'].' '.$data['prenom_achat'].' '.$data['nom_achat'].'" />
			<div id="ACcontact_achat_fact_choix" class="autocomplete"></div>
		</div>
		<div class="row oblig">
			<div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.commercial);">Commercial</div>
			<div class="field">'.selectTag('commercial_fact',$datas['user'],$data['commercial_fact'],'','',false).'</div>
		</div>
		</fieldset>';
        if($data['id_cmd'] != '' or $data['id_dev'] != '' or $data['id_aff'] != '') {
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
            $out.= '</fieldset>';
        }
        $out.='</div>
		<div class="block width50">
		<fieldset class="form" style="height: 180px">
			<legend>Informations sur le client</legend>
			<div class="row oblig">
				<div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.deliveryNom);">Nom</div>
				<div class="field">'.inputTag('text', "nomentreprise_fact", '', '', '30', $data['nomentreprise_fact']).'</div>
			</div>
			<div class="row">
				<div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.deliveryAdd);">Adresse</div>
				<div class="field">'.inputTag('text', "add1_fact", '', '', '40', $data['add1_fact']).'</div>
			</div>
			<div class="row">
				<div class="label">Complément</div>
				<div class="field">'.inputTag('text', "add2_fact", '', '', '40', $data['add2_fact']).'</div>
			</div>
			<div class="row">
				<div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.deliveryCpVille);">CP - Ville</div>
				<div class="field">'.inputTag('text', "cp_fact", 'civ', '', '6', $data['cp_fact'], 'onchange="zuno.ajax.get.html(\'../ajaxRef.php\',\'action=villeForCp&cp=\'+this.value,\'devisVille\');"').'<select name=\'ville_fact\'  id="devisVille" class="nextciv" onblur="finishEditing();" onclick="beginEditing(this);" ><option value=""> </option><option value="'.$data['ville_fact'].'" selected="selected">'.$data['ville_fact'].'</option></select></div>
			</div>
			<div class="row">
				<div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.deliveryPays);">Pays</div>
				<div class="field">'.selectTag('pays_fact',$datas['pays'],$data['pays_fact']).'</div>
			</div>
			<div class="row">
				<div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.deliveryMail);">Mail</div>
				<div class="field">'.inputTag('text', "maildelivery_fact", '', '', '30', $data['maildelivery_fact']).'</div>
			</div>
			<div class="row">
			    <div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.complementDelivery);">Commentaire</div>
			    <div class="field">
				    <textarea name="complementdelivery_fact" cols="55" rows="1" >'.$data['complementdelivery_fact'].'</textarea></div>
			</div>
		</fieldset>
		</div>';
        $out.='<div class="block width50">
		<fieldset class="form" style="height: 180px">
			<legend>Règlement de cette facture</legend>
			<div class="row">
				<div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.infoDateReglement);">Réglé le : </div>
				<div class="field">'.DateUniv2Human($data['datereglement_fact'], 'simpleLong').'</div>
			</div>
			<div class="row">
				<div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.reglementMode);">Mode</div>
				<div class="field">'.selectTag('modereglement_fact',$datas['modereglement'],$data['modereglement_fact'],'','',false).'</div>
			</div>
			<div class="row">
				<div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.reglementCondi);">Condition</div>
				<div class="field">'.selectTag('condireglement_fact',$datas['condireglement'],$data['condireglement_fact'],'','',false).'</div>
			</div>
			<div class="row">
				<div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.commentreglement);">Commentaire</div>
				<div class="field">'.textareaTag('commentreglement_fact', $data['commentreglement_fact'], '', '', 3).'</div>
			</div>
		</fieldset>
		</div>';
        if($datas['data']['id_fact'] == $datas['ren']['idChamp_ren'] or $datas['data']['ren_fact'] == "") {
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
                                            <div class="label" style="width:100px" onmouseover="zuno.tooltip(this,ZTips.facture.renewActiver);">Activer</div>
                                            <div class="field"><input type="checkbox" name="actif_ren" value="1" onchange="affRen(this)" '.$checked.' /></div>
                                    </div>
                                    <div class="row"'.$styleDisplay.'>
                                            <div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.renewFrequence);">Fréquence</div>
                                            <div class="field">'.selectTag("periode_ren", $datas['periode'], $datas['ren']['periode_ren'], "", " id='selectF' ", false).'</div>
                                    </div>
                                    <div class="row"'.$styleDisplay.'>
                                            <div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.renewDateFin);">Date de fin </div>
                                            <div class="field">'.inputDateTag("text", "fin_ren", $date2).'</div>
                                     </div>
                                     <div class="row"'.$styleDisplay.'>
                                            <div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.renewStatut);">Statut</div>
                                            <div class="field">'.selectTag("statusChamp_ren", $datas['status'], $datas['ren']['statusChamp_ren'], "", " id='statusF' ", false).'</div>
                                     </div>
                                     <div class="row"'.$styleDisplay.'>
                                            <div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.renewMail);">E-Mail</div>
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
                                <div class="label">Facture originale</div>
                                <div class="field"><a href="Facture.php?id_fact='.$datas['ren']['idChamp_ren'].'" >Cette facture a été créée en renouvellement de la facture '.$datas['ren']['idChamp_ren'].'</a></div>
                            </div>
                         </fieldset>
                        </div>';
        }
        return $out;
    }



    private function produits($datas) {
        $titre = imageTag('../img/prospec/facture.png','facture').' Produits de '.$datas['nom'].' '.$this->prepareAffichageID($datas['data']);
        $corps = '<form name="productFacture" action="#" method="post">';
        $corps .= '<input type="hidden" name="id_facture" value="'.$datas['data']['id_fact'].'" />';
        $corps .= '<input type="hidden" name="produitAModifier" value="" id="produitFactureProd"/>';
        $corps .= '<input type="hidden" name="enmodif" value="0" id="suisJeEnModif" />';
        $corps .= '<div class="blockTable" id="contenuProdFacture">';
        $corps .= $this->interneProduits($datas);
        $corps .= '</div></form>';
        return generateZBox($titre,$titre,$corps,'','produitsFacture','');
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
            $total = abs($v['prix'])*$v['quantite']*(1-$v['remise']/100);
            $bigtotal += $total;
            if($v['id_prod'] == '') {
                $v['id_prod'] = $v['id_produit'];
                $v['nom_prod'] = $v['id_produit'];
            }
            $out .= '<tr class="altern'.$alternance.'" id="'.$v['id'].'td0">
			<td class="barre" title="'.$v['nom_prod'].'" id="ID'.$v['id'].'">'.$v['id_prod'].'</td>
			<td class="barre">'.$v['desc'].'</td>
			<td class="center barre">'.$v['quantite'].'</td>
			<td class="barre">'.prepareNombreAffichage(abs($v['prix'])).' €</td>
			<td class="barre">'.prepareNombreAffichage($v['remise']).' %</td>
			<td class="barre">'.prepareNombreAffichage($total).' €</td>
			<td nowrap="nowrap" class="center"><a name="modif" onclick="afficherFormModif(\''.$v['id'].'\');"><img title="Modifier" alt="Modifier" name="img" src="../img/prospec/facture.modif.png"/></a>
					    <a name="supp" onclick="chargerAction(\'suppProduit\');chargerProduit(\''.$v['id'].'\'); zuno.business.formTools.sendFormAjah(\'productFacture\', \'facturier/Facture.php\', \'contenuProdFacture\');"><img title="Supprimer" alt="Supprimer" name="img" src="../img/prospec/facture.delete.png"/></a></td>
			</tr>';
            $out .= '<tr class="altern'.$alternance.'" id="'.$v['id'].'td1" style="display:none;">
			<td class="barre" title="'.$v['nom_prod'].'">'.$v['id_prod'].'</td>
			<td class="barre">'.textareaTag('desc2', $v['desc'], '', '', '', ' id="desc_Ajout_prod_fact'.$v['id'].'" style="width:98%; height:30px;" ').'</td>
			<td class="center barre">'.inputTag('text', 'quantite2', '', '', '16', $v['quantite'], ' id="quantite_Ajout_prod_fact'.$v['id'].'" onchange="calculetteTotal(\''.$v['id'].'\');" style="width:28px" ').'</td>
			<td class="barre">'.inputTag('text', 'prix2', '', '', '16', abs($v['prix']), ' id="prix_Ajout_prod_fact'.$v['id'].'" onchange="calculetteTotal(\''.$v['id'].'\');" style="width:60px" ').' €</td>
			<td class="barre">'.inputTag('text', 'remise2', '', '', '16', $v['remise'], ' id="remise_Ajout_prod_fact'.$v['id'].'" onchange="calculetteTotal(\''.$v['id'].'\');" style="width:30px" ').' %</td>
			<td class="barre">'.inputTag('text', 'prix_total2', '', '', '16', $total, ' id="total_Ajout_prod_fact'.$v['id'].'"  readonly="readonly" style="width:80px" ').' €</td>
			<td nowrap="nowrap" class="center"><a name="modif" onclick="autoCopieChamps(\''.$v['id'].'\');chargerAction(\'modifProduit\'); zuno.business.formTools.sendFormAjah(\'productFacture\', \'facturier/Facture.php\',\'contenuProdFacture\');"><img title="Modifier" alt="Modifier" name="img" src="../img/prospec/facture.add.png"/></a>
			<a name="anule" onclick="annulerModif();"><img title="annuler" alt="annuler" src="../img/prospec/cancel.png" /></a></td>
			</tr>';
            $alternance = ($alternance == '0') ? '1':'0';
        }
        $out .= '<tr class="altern'.$alternance.'" id="tdAjoutProdFacture">
			<td class="barre">'.inputTag('text', 'produit','', '', '16','', ' id="ACproduit_ajout_fact" style="width:80px" onchange="verifAutocompleteur(this);"').'
				<img src="../img/ajaxField.png" alt="rechercher" onclick="zuno.popup.open(\'../produit/Produit.php\', \'action=popupSearchProd\',950,500);" style="cursor:pointer; margin-right:0px"/><img src="../img/prospec/add.png" alt="ajouter" onclick="zuno.popup.open(\'../produit/Produit.php\', \'action=popupAddProd\',800,330);" style="cursor:pointer; margin-right:0px"/>
				<img style="display:none; margin-left:5px;" id="loadingProd" src="../img/ajax-loader.gif" alt="loader"/>
				<input type="hidden" name="id_produit" id="ACproduit_ajout_facthidden" value="" /><input type="hidden" name="idold" id="ACproduit_ajout_factold" value="" />
				<div id="ACproduit_ajout_fact_choix" class="autocomplete"></div></td>
			<td class="barre">'.textareaTag('desc', '', '', '', '', ' id="desc_Ajout_prod_fact" style="width:98%; height:30px;" ').'</td>
			<td class="center barre">'.inputTag('text', 'quantite', '', '', '16', '1', ' id="quantite_Ajout_prod_fact" onchange="calculetteTotal();" style="width:28px" ').'</td>
			<td class="barre">'.inputTag('text', 'prix', '', '', '16', '', ' id="prix_Ajout_prod_fact" onchange="calculetteTotal();" style="width:60px" ').' €</td>
			<td class="barre">'.inputTag('text', 'remise', '', '', '16', '0', ' id="remise_Ajout_prod_fact" onchange="calculetteTotal();" style="width:30px" ').' %</td>
			<td class="barre">'.inputTag('text', 'prix_total', '', '', '16', '', ' id="total_Ajout_prod_fact"  readonly="readonly" style="width:80px" ').' €</td>
			<td nowrap="nowrap" class="center"><a name="add" onclick="chargerAction(\'addProduit\'); zuno.business.formTools.sendFormAjah(\'productFacture\', \'facturier/Facture.php\',\'contenuProdFacture\');"><img title="Ajouter" alt="Ajouter" name="img" src="../img/prospec/facture.addProduct.png"/></a>
			</td>
			</tr>';
        $out .= '<input type="hidden" name="action" value="" id="actionFactureProd" />';
        $tva = $datas['data']['tauxTVA_fact']*$bigtotal/100;
        $ttc = $tva+$bigtotal;
        $tva = formatCurencyDisplay($tva,2,'');
        $ttc = formatCurencyDisplay($ttc,2,'');
        $bigtotal = formatCurencyDisplay($bigtotal,2,'');
        $out .= '<tr>
			<th rowspan="4" colspan="3"> </th>
			<th colspan="8"> </th>
		</tr>
		<tr class="titre noround">
			<th colspan="2">Total HT</th>
			<th id="TotalHT" colspan="2">'.$bigtotal.' €</th>
		</tr>
		<tr class="titre noround">
			<th colspan="2">TVA <span style="color: white;" id="TauxDeTVA">'.$datas['data']['tauxTVA_fact'].'%</span></th>
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

        $corps = '<div id="ContenuTraitementFacture" >'.$mess.$this->interneTraitement($datas).'</div>';

        $bouton = linkTag('javascript:document.ActionFacturePDF.submit()',imageTag('../img/prospec/facture.pdf.png','créer','middle').' Générer','','','onclick="ChangeFactureAction(\'Voir\')"');

        if (verifDroitsAuto('facture', 60, $datas['data']['commercial_fact'])) {
            $bouton .= '<a onclick="ChangeFactureAction(\'Record\');zuno.business.formTools.sendFormAjah(\'ActionFacturePDF\', \'facturier/Facture.php\',\'ContenuTraitementFacture\');" title="Enregistrer" name="enreg"><img align="middle" title="Enregistrer" alt="enreg" name="img" src="../img/prospec/facture.record.png"/> Enregistrer</a>';
            if(verifDroitsAuto('facture', 50, $datas['data']['commercial_fact'])) {
                $bouton .= '<a onclick="ChangeFactureAction(\'RecordSend\');zuno.business.formTools.sendFormAjah(\'ActionFacturePDF\', \'facturier/Facture.php\',\'traitementFacture\');" title="Enregistrer puis envoyer" name="enregistrerEnvoyer"><img align="middle" title="Enregistrer et envoyer" alt="enregEnvoy" name="img" src="../img/prospec/facture.recsend.png"/> Enregistrer & envoyer</a>';
            }
        }
        if(verifDroitsAuto('facture', 50, $datas['data']['commercial_fact']))
            $bouton .= '<a onclick="ChangeFactureAction(\'Send\');zuno.business.formTools.sendFormAjah(\'ActionFacturePDF\', \'facturier/Facture.php\',\'traitementFacture\');" title="Envoyer" name="envoyer"><img align="middle" title="Envoyer" alt="envoyer" name="img" src="../img/prospec/facture.send.png"/> Envoyer</a>';

        if (($datas['data']['status_fact'] < 5) and (verifDroitsAuto('facture', 13, $datas['data']['commercial_fact'])))
            $bouton .= '<a title="En attente de règlement" name="attenteRglmt" onclick="ChangeFactureAction(\'AttenteRglmt\');zuno.business.formTools.sendFormAjah(\'ActionFacturePDF\', \'facturier/Facture.php\',\'traitementFacture\');"><img align="middle" title="En attente de règlement" alt="En attente de règlement" name="img" src="../img/prospec/facture.attente.png"/> En attente du règlement</a>';

        // Quand status Envoyé
        if (($datas['data']['status_fact'] == "5")and(verifDroitsAuto('facture', 13, $datas['data']['commercial_fact']))) {
            $bouton .= '<a title="Réglée" name="regle" onclick="return zuno.popup.open(\'Facture.php\', \'facture='.$datas['data']['id_fact'].'&action=confirmClore\', 500, 150, \'\', \'\', \'resize\', \'PopupConfirmCloreFact\');"><img align="middle" title="Réglée" alt="reglee" name="img" src="../img/prospec/facture.paye.png"/> Facture réglée</a>';
            if(array_key_exists('zunoPayline', $GLOBALS) and $GLOBALS['zunoPayline']['MERCHANT_ID'] != "")
                $bouton .= '<a title="Payer par carte bancaire" name="CB" onclick="zuno.popup.open(\'Facture.php\', \'action=popupCB&facture='.$datas['data']['id_fact'].'\',800,230);"><img align="middle" title="Payer par carte bancaire" alt="payer par carte" name="img" src="../img/prospec/facture.paye.png"/> Payer par carte bancaire</a>';

            if(verifDroitsAuto('avoir', 20, '') and $datas['data']['type_fact'] != 'Avoir') {
                $bouton .= '<a title="Faire un avoir" name="avoir" onclick="ChangeFactureAction(\'avoirFromFact\');zuno.business.formTools.sendFormAjah(\'ActionFacturePDF\', \'facturier/Facture.php\', \'ficheComplete\');"><img align="middle" title="Faire un avoir" name="avoir" src="../img/prospec/facture.png" /> Faire un avoir</a>';
            }
        }
        // Clonage
        if(verifDroitsAuto('facture', 25, $datas['data']['commercial_fact']))
            $bouton .= '<a title="Cloner" href="Facture.php?action=cloner&fact='.$datas['data']['id_fact'].'"><img align="middle" title="Cloner" alt="cloner" name="img" src="../img/prospec/facture.clone.png" /> Cloner</a>';


        $titre = imageTag('../img/prospec/facture.record.png','créer')." Traitement de ".$datas['nom']." ".$this->prepareAffichageID($datas['data']);

        return generateZBox($titre,$titre,$corps.$outJS,$bouton,'traitementFacture','');
    }
    private function interneTraitement($datas) {
        foreach ($GLOBALS['ZunoFacture'] as $key => $val) {
            $k = explode('.',$key,2);
            if ($k[0] == 'cannevas' and $k[1] != 'exportTableur')
                $toto[$key] = substr($key,strpos($key,".")+1,strlen($key));
        }
        $input = array();
        $output= array();
        $selected = "";
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
        if(verifDroitsAuto('facture', 50, $datas['data']['commercial_fact']))
            $TypeSend['mail'] = "par e-mail";
        if(verifDroitsAuto('facture', 52, $datas['data']['commercial_fact']))
            $TypeSend['fax'] = "par fax";
        if(verifDroitsAuto('facture', 54, $datas['data']['commercial_fact']))
            $TypeSend['courrier'] = "par courrier";

        if(verifDroitsAuto('facture', 62, $datas['data']['commercial_fact'])) {

            if(is_file($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoFacture']['dir.facture'].'Facture.'.$datas['data']['id_fact'].'.pdf')) {
                $listeDoc['Facture.'.$datas['data']['id_fact'].'.pdf'] = 'Facture.'.$datas['data']['id_fact'].'.pdf';
                $docs .= '<div class="row"><div class="label"><a href="javascript:document.ActionFacturePDF.submit();" onclick="ChangeFactureAction(\'Zieuter\');changeFactureFormat(\'pdf\');"><img src="../img/files/pdf.png" alt="pdf" /></a></div><div class="field">Facture.'.$datas['data']['id_fact'].'.pdf</div></div>';
                $selected = 'Facture.'.$datas['data']['id_fact'].'.pdf';
            }
            if(is_file($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoFacture']['dir.facture'].'Facture.'.$datas['data']['id_fact'].'.odt')) {
                $listeDoc['Facture.'.$datas['data']['id_fact'].'.odt'] = 'Facture.'.$datas['data']['id_fact'].'.odt';
                $docs .= '<div class="row"><div class="label"><a href="javascript:document.ActionFacturePDF.submit();" onclick="ChangeFactureAction(\'Zieuter\');changeFactureFormat(\'odt\');"><img src="../img/files/document.png" alt="odt" /></a></div><div class="field">Facture.'.$datas['data']['id_fact'].'.odt</div></div>';
                $selected = ($selected == "") ? 'Facture.'.$datas['data']['id_fact'].'.odt' : $selected;
            }
            if(is_file($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoFacture']['dir.facture'].'Facture.'.$datas['data']['id_fact'].'.doc')) {
                $listeDoc['Facture.'.$datas['data']['id_fact'].'.doc'] = 'Facture.'.$datas['data']['id_fact'].'.doc';
                $docs .= '<div class="row"><div class="label"><a href="javascript:document.ActionFacturePDF.submit();" onclick="ChangeFactureAction(\'Zieuter\');changeFactureFormat(\'doc\');"><img src="../img/files/document.png" alt="doc" /></a></div><div class="field">Facture.'.$datas['data']['id_fact'].'.doc</div></div>';
                $selected = ($selected == "") ? 'Facture.'.$datas['data']['id_fact'].'.doc' : $selected;
            }
        }

        $out = '<form name="ActionFacturePDF" action="Facture.php" method="post">
		<input type="hidden" id="MakeFactureAction" value="ActionFacture" name="action"/>
		<input type="hidden" value="'.$datas['data']['id_fact'].'" name="id_fact"/>
		<input type="hidden" value="" name="format" id="formatFacturePDF" />
		'.inputTag('hidden', 'dir_aff', '', '', '', $datas['data']['dir_aff']).'
			<div class="block width50">
				<fieldset class="form">
				<legend>Génération des fichiers</legend>
					<div class="row">
						<div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.generateCannevas);">Cannevas</div>
						<div class="field">'.selectTag("Cannevas", $toto, ($datas['data']['type_fact'] == 'Facture') ? '' : 'cannevas.avoir', '', '', false).'</div>
					</div>
					<div class="row">
						<div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.generateOutput);">Sortie</div>
						<div class="field">'.selectTag("OutputExt", $availableConvFormat, $value['OutputExt'], '', '', false).'</div>
					</div>
				</fieldset>
				<fieldset class="form"><legend>Visualisation des documents</legend>
					'.$docs.'
				</fieldset>
			</div>
			<div class="block width50">
				<fieldset class="form">
				<legend>Envoyer</legend>
					<div class="row oblig">
						<div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.sendType);">Type d\'envoi</div>
						<div class="field">'.selectTag('typeE', $TypeSend, '', '', 'onchange="montreForm(this.value);"',false).'</div>
					</div>
					<div class="row oblig">
						<div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.sendSujet);">Sujet</div>
						<div class="field">'.inputTag('text', 'sujet','','','','','id="MessageTitle"').'</div>
					</div>
					<div class="row">
						<div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.sendCannevas);">Cannevas</div>
						<div class="field">'.selectTag("cannevas", $toto2, '','',' id="CannevasListe" onchange="ChangeCannevas();"').'</div>
					</div>
					<div class="row">
						<div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.sendMessage);">Message</div>
						<div class="field">'.textareaTag('message', '', '', '', '', 'id="MessageToSend"').'</div>
					</div>
					<div class="row">
						<div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.sendPJ);">Fichier joint</div>
						<div class="field">'.selectTag('fichier', $listeDoc, $selected, "", "", false).'</div>
					</div>
					
				</fieldset>
			</div>
			<div class="block width50">
				<fieldset class="form">
				<legend>Informations sur le destinataire</legend>
					<div class="row oblig" id="champEmail">
						<div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.sendMail);">Courriel</div>
						<div class="field">'.inputTag('text', 'mail', '', '', '', $datas['data']['mail_cont']).'</div>
					</div>
                                        <div class="row" id="champCC">
						<div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.sendCopyToMe);">Me mettre en copie</div>
						<div class="field"><input type="checkbox" name="cc" value="'.$_SESSION['user']['mail'].'" checked="checked"/></div>
					</div>
					<div class="row oblig" style="display:none;" id="champFax">
						<div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.sendFax);">Fax</div>
						<div class="field">'.inputTag('text', 'fax', '', '', '', $datas['data']['fax_ent']).'</div>
					</div>
					<div class="row oblig" style="display:none;" id="champDestinataire">
						<div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.sendDestinataire);">Destinataire</div>
						<div class="field">'.inputTag('text', 'destinataire', '', '', '', $datas['data']['civ_cont'].' '.$datas['data']['prenom_cont'].' '.$datas['data']['nom_cont']).'</div>
					</div>
					<div class="row oblig" style="display:none;" id="champAdd1">
						<div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.sendAdd);">Adresse</div>
						<div class="field">'.inputTag('text', 'add1', '', '', '', $datas['data']['add1_fact']).'</div>
					</div>
					<div class="row" style="display:none;" id="champAdd2">
						<div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.sendComplement);">Complément</div>
						<div class="field">'.inputTag('text', 'add2', '', '', '', $datas['data']['add2_fact']).'</div>
					</div>
					<div class="row oblig" style="display:none;" id="champAdd3">
						<div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.sendCpVille);">CP - Ville</div>
						<div class="field">'.inputTag('text', "cp", '', '', '', $datas['data']['cp_fact'], 'onchange="zuno.ajax.get.html(\'../ajaxRef.php\',\'action=villeForCp&cp=\'+this.value,\'envoiVille\');"').
                '<select name=\'ville\'  id="envoiVille"  onblur="finishEditing();" onclick="beginEditing(this);" ><option value=""> </option><option value="'.$datas['data']['ville_fact'].'" selected="selected">'.$datas['data']['ville_fact'].'</option></select></div>
					</div>
					<div class="row oblig" style="display:none;" id="champAdd4">
						<div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.sendPays);">Pays</div>
						<div class="field">'.selectTag('pays',$datas['pays'],$datas['data']['pays_fact']).'</div>
					</div>
				</fieldset>
			</div>
		</form>';
        return $out;
    }

    public function searchResult($datas, $light='') {
        if($light == 'result')
            return $this->Result($datas);
        elseif($light == 'supp')
            return '<span class="important" style="text-align:center;">Votre facture a été supprimée</span>'.$this->searchForm($datas).$this->listeResult($datas);
        else
            return $this->searchForm($datas).$this->listeResult($datas);
    }

    private function searchForm($datas) {
        $script = '<script type="text/javascript">function autoCompletage() { new Ajax.Autocompleter("cpFactureSearch", "cpFactureSearch_choix", "../ajaxRef.php?action=listeCp", { paramName: "value", minChars: 2, indicator : "loadingCp" , afterUpdateElement : autoCompleteHidden } ); ';
        $script .= 'new Ajax.Autocompleter("affaireFactureSearch", "affaireFactureSearch_choix", "../ajaxRef.php?action=listeAffaire", { paramName: "value", minChars: 2, indicator: "loadingAff" , afterUpdateElement : autoCompleteHidden } ); ';
        $script .= 'new Ajax.Autocompleter("entrepriseFactureSearch", "entrepriseFactureSearch_choix", "../ajaxRef.php?action=listeEntreprise", { paramName: "value", minChars: 2, indicator : "loadingEnt" , afterUpdateElement : autoCompleteHidden } ); ';
        $script .= 'new Ajax.Autocompleter("userFactureSearch", "userFactureSearch_choix", "../ajaxRef.php?action=listeUser", { paramName: "value", minChars: 2, indicator: "loadingUsr", afterUpdateElement : autoCompleteHidden } );} ';
        $script .= 'function ChangeFactureID(value) { $(\'idFactureSearch\').value = value; } ';
        $script .= 'function verifVide(objet) { if(objet.value == "") { $(objet.id+\'hidden\').value = ""; } } ';
        $script.= "function ChangeFactureResultPage(limit,from,order,sens) {
		    ChangeFactureAction('searchFacture');
		    if(limit != undefined) ChangeLimit(limit);
		    if(from  != undefined) ChangeFrom(from);
		    if(order != undefined) ChangeOrder(order,sens);
		    ChangeSubmit();
		}
		function ChangeFactureAction(value) {\$('listeFactureAction').value = value;}
		function ChangeFrom(value) { \$('fromFactureSearch').value = value; }
		function ChangeLimit(value) { \$('limitFactureSearch').value = value; }
		function ChangeOrder(value,sens) {
		    if(sens == undefined) sens = 'ASC';
		    \$('orderFactureSearch').value = value;
		    \$('orderSensFactureSearch').value = sens;
		}
		function ChangeSubmit() {
			zuno.business.formTools.sendFormAjah('searchFacture', 'facturier/FactureListe.php','listeResultFacture');
		}
		Event.observe(document, 'keypress', function(event){ if(event.keyCode == Event.KEY_RETURN) ChangeSubmit(); return false;}, false);
		";
        $script .= 'function camenbertFacture(value, max) { $(\'devisBudgetMini\').value = value; $(\'devisBudgetMaxi\').value = max; ChangeFactureAction(\'searchFacture\');zuno.business.formTools.sendFormAjah(\'searchFacture\', \'draco/FactureListe.php\',\'listeResultFacture\'); } ';
        $script .= 'function resetForm() {var form = $(\'searchFormFacture\'); form.reset(); form.select(\'input[type="hidden"]\').each(function(input) {input.clear();}); } ';
        $script .= 'function exportTab() { if ($(\'divExportTableur\').style.display == \'inline\') {$(\'divExportTableur\').style.display = \'none\';} else {$(\'divExportTableur\').style.display = \'inline\';} }';
        $script .= '</script>';

        $titre = '<img title="recherche" alt="Rechercher" onload="autoCompletage();" name="img" src="../img/prospec/voir.png" /> Recherche de factures';
        $corps = '<form name="searchFacture" action="FactureListe.php" method="post" id="searchFormFacture">';
        $corps .= '<div id="PortletSearchFacture">';
        $corps .= '<input type="hidden" name="action" value="searchFacture" id="listeFactureAction" />';
        $corps .= '<input type="hidden" name="id_fact" value="" id="idFactureSearch" />';
        $corps .= '<input type="hidden" name="from" value="" id="fromFactureSearch" />';
        $corps .= '<input type="hidden" name="limit" value="" id="limitFactureSearch" />';
        $corps .= '<input type="hidden" name="order" value="id_cmd" id="orderFactureSearch" />';
        $corps .= '<input type="hidden" name="orderSens" value="DESC" id="orderSensFactureSearch" />';
        $corps .= $this->interneSearch($datas);
        $corps .= '</div></form>';
        $bouton = '<a name="reset" onclick="resetForm();" style="cursor:pointer;"><img title="Reset" alt="reset" src="../img/prospec/cancel.png"/> Annuler</a>';
        $bouton .= '<a name="valider" onclick="ChangeFactureAction(\'searchFacture\');ChangeSubmit();"><img title="Valider" alt="valid" src="../img/prospec/voir.png" />Rechercher</a>';
        return generateZBox($titre,$titre,$corps.$script,$bouton,"searchBox",'');
    }

    private function interneSearch($datas) {
        $listeType['Facture'] = 'facture';
        $listeType['Avoir'] = 'avoir';

        $out = '<div class="block width50"><fieldset class="form" style="height: 250px">';
        $out .= '<div class="row"><div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.searchEnt);">Entreprise</div>';
        $out .= '<div class="field">'.inputTag('text', 'entreprise', '', '', '20', '', ' id="entrepriseFactureSearch" onchange="verifVide(this);"').'<img style="display:none; margin-left:5px;" id="loadingEnt" src="../img/ajax-loader.gif" alt="loader"/></div><div class="autocomplete" id="entrepriseFactureSearch_choix"></div>';
        $out .= '<input type="hidden" name="entreprise_fact" value="" id="entrepriseFactureSearchhidden"/></div>';
        $out .= '<div class="row"><div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.searchTitre);">Titre</div>';
        $out .= '<div class="field">'.inputTag('text', 'titre_fact', '', '', '20').'</div></div>';
        $out .= '<div class="row"><div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.searchAff);">Affaire</div>';
        $out .= '<div class="field">'.inputTag('text', 'affaire', '', '', '20', '', ' id="affaireFactureSearch" onchange="verifVide(this);"').'<img style="display:none; margin-left:5px;" id="loadingAff" src="../img/ajax-loader.gif" alt="loader"/></div><div class="autocomplete" id="affaireFactureSearch_choix"></div>';
        $out .= '<input type="hidden" name="affaire_fact" value="" id="affaireFactureSearchhidden"/></div>';
        $out .= '<div class="row"><div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.searchStatut);">Status</div>';
        $out .= '<div class="field">'.selectTag('status_fact',$datas['status'],'').'</div></div>';
        $out .= '<div class="row"><div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.searchComm);">Commercial</div>';
        $out .= '<div class="field">'.inputTag('text', 'commercial', '', '', '20', '', ' id="userFactureSearch" onchange="verifVide(this);"').'<img style="display:none; margin-left:5px;" id="loadingUsr" src="../img/ajax-loader.gif" alt="loader"/></div><div class="autocomplete" id="userFactureSearch_choix"></div>';
        $out .= '<input type="hidden" name="commercial_fact" value="" id="userFactureSearchhidden"/></div>';
        $out .= '<div class="row"><div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.searchCP);">Département</div>';
        $out .= '<div class="field">'.inputTag('text', 'cp', '', '', '20', '', ' id="cpFactureSearch" onchange="verifVide(this);"').'<img style="display:none; margin-left:5px;" id="loadingCp" src="../img/ajax-loader.gif" alt="loader"/></div><div class="autocomplete" id="cpFactureSearch_choix"></div>';
        $out .= '<input type="hidden" name="cp_ent" value="" id="cpFactureSearchhidden"/></div>';
        $out .= '<div class="row"><div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.searchBudgMin);">Montant min</div>';
        $out .= '<div class="field">'.inputTag('text', 'sommeHT_fact', '', '', '20', '', ' id="devisBudgetMini" ').'</div></div>';
        $out .= '<div class="row"><div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.searchBudgMax);">Montant maxi</div>';
        $out .= '<div class="field">'.inputTag('text', 'sommeHT_fact2', '', '', '20', '', ' id="devisBudgetMaxi" ').'</div></div>';
        $out .= '<div class="row"><div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.searchType);">Type</div>';
        $out .= '<div class="field">'.selectTag('type_fact',$listeType,'').'</div></div>';
        $out .= '</fieldset></div>';
        $out .= '<div class="block width50"><fieldset class="form" style="height: 250px">';
        $out .= '<div class="row"><img border="0" alt="Graph de trésorerie" src="../Img.TresoGraph.php?format=small"/></div>';
        $out .= '</fieldset></div>';
        return $out;
    }

    public function listeResult($datas) {
        return '<div id=listeResultFacture>'.$this->Result($datas).'</div>';
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
		function factureDoExportTableur(value) {
		    $('resultFactureAction').value = 'exportTableur';
		    $('resultFactureExportType').value = value;
		    $('resultFormFacture').submit();
		}
		</script>";
        $out .= '<div class="blockTable">
		    <form id="resultFormFacture" method="post" action="FactureListe.php" name="resultFacture">
		    <input type="hidden" name="action" value="" id="resultFactureAction" />
		    <input type="hidden" name="exportType" value="" id="resultFactureExportType" />
		    <table cellspacing="0"><tbody>';
        $out .= '<tr class="titre">';
        if(!$modHistorique)
            $out .= '<th class="barre"><img src="../img/prospec/multiple-check.png" onclick="toggleCheckbox(\'resultFormFacture\');"/></th>';
        $out .= '<th class="barre">'.$this->$linkOrderFunction('ID','id_fact',$datas['order'],$datas['orderSens'],$datas['limit'],'ChangeFactureResultPage',$suffixeLink).'</th>';
        $out .= '<th class="barre">'.$this->$linkOrderFunction('Entreprise','nom_ent',$datas['order'],$datas['orderSens'],$datas['limit'],'ChangeFactureResultPage',$suffixeLink).'</th>';
        $out .= '<th class="barre">'.$this->$linkOrderFunction('Contact','c1.nom_cont',$datas['order'],$datas['orderSens'],$datas['limit'],'ChangeFactureResultPage',$suffixeLink).'</th>';
        $out .= '<th>'.$this->$linkOrderFunction('Titre','titre_fact',$datas['order'],$datas['orderSens'],$datas['limit'],'ChangeFactureResultPage',$suffixeLink).'</th>';
        $out .= '<th class="barre">'.$this->$linkOrderFunction('Création','daterecord_fact',$datas['order'],$datas['orderSens'],$datas['limit'],'ChangeFactureResultPage',$suffixeLink).'</th>';
        $out .= '<th class="barre">'.$this->$linkOrderFunction('Règlement','datereglement_fact',$datas['order'],$datas['orderSens'],$datas['limit'],'ChangeFactureResultPage',$suffixeLink).'</th>';
        //$out .= '<th>'.$this->$linkOrderFunction('Montant HT','sommeHT_fact',$datas['order'],$datas['orderSens'],$datas['limit'],'ChangeFactureResultPage',$suffixeLink).'</th>';
        $out .= '<th>Montant TTC</th>';
        $out .= '<th class="barre">'.$this->$linkOrderFunction('Status','status_fact',$datas['order'],$datas['orderSens'],$datas['limit'],'ChangeFactureResultPage',$suffixeLink).'</th>';
        if(!$modHistorique)
            $out .= '<th class="last center">Actions</th>';
        $out .= '</tr>';
        $alternance =  0;
        $totalTTCDisplay = $totalDisplay = 0;
        foreach($datas['data'] as $v) {
            $action = '';
            $datereglement_fact = ($v['datereglement_fact'] != '') ? DateUniv2Human($v['datereglement_fact'], 'simpleLong') : '';
            $daterecord_fact = ($v['daterecord_fact'] != '') ? DateUniv2Human($v['daterecord_fact'], 'simpleLong') : '';
            $sommeTTC_fact = round(($v['sommeHT_fact']*(100+$v['tauxTVA_fact']))/100,2);
            //traitement des boutons de modification et d'action
            // recherche du fichier PDF
            if (file_exists($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoFacture']['dir.facture'].$GLOBALS['ZunoFacture']['file.suffixe'].$v['id_fact'].'.pdf')and verifDroitsAuto('facture', 62, $datas['data']['commercial_fact']))
                $action   .= "<a href=\"$suffixeLink/facturier/Facture.php?id_facture=".$v['id_fact']."&action=pdf\">".imageTag($suffixeLink.'img/prospec/facture.pdf.png','PDF')."</a> ";
            // Clonage
            if(verifDroitsAuto('facture', 25, $datas['data']['commercial_fact']))
                $action .= '<a title="Cloner" href="'.$suffixeLink.'facturier/Facture.php?action=cloner&fact='.$v['id_fact'].'"><img align="middle" title="Cloner" alt="cloner" name="img" src="'.$suffixeLink.'img/prospec/facture.clone.png" style="margin-right: 12px"/></a>';
            // Quand status créer
            if (($v['status_fact'] == "1")and(verifDroitsAuto('facture', 60, $datas['data']['commercial_fact']))) {
                $action .= '<a href="'.$suffixeLink.'facturier/Facture.php?id_fact='.$v['id_fact'].'" title="Enregistrer" name="enreg"><img align="middle" title="Enregistrer" alt="enreg" name="img" src="'.$suffixeLink.'img/prospec/facture.record.png"/></a>';
            }
            // Quand status Enregistré
            elseif (($v['status_fact'] == "3")and(verifDroitsAuto('facture', 50, $datas['commercial_fact'])))
                $action .= '<a href="'.$suffixeLink.'facturier/Facture.php?id_fact='.$v['id_fact'].'" title="Envoyer" name="envoyer"><img align="middle" title="Envoyer" alt="envoyer" name="img" src="'.$suffixeLink.'img/prospec/facture.send.png"/></a>';
            // Quand status Envoyé
            elseif (($v['status_fact'] == "4")and(verifDroitsAuto('facture', 13, $datas['data']['commercial_fact'])))
                $action .= '<a title="Réglée" name="reglee" onclick="ChangeFactureAction(\'Clore\');ChangeFactureID(\''.$v['id_fact'].'\');zuno.business.formTools.sendFormAjah(\'searchFacture\', \''.$suffixeLink.'facturier/FactureListe.php\',\'listeResultFacture\');"><img align="middle" title="Réglée" alt="reglee" name="img" src="'.$suffixeLink.'img/prospec/facture.paye.png"/></a>';

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
                $out .= '<td class="barre" style="width:10px"><input type="checkbox" name="select[]" value="'.$v['id_fact'].'" checked="checked"/></td>';
            $out .= '<td class="barre" style="cursor : pointer; color : #'.$v['color_stfact'].' !important;" onclick="window.location=\''.$suffixeLink.'facturier/Facture.php?id_fact='.$v['id_fact'].'\'"><b>'.$this->prepareAffichageID($v).'</b></td>';
            $out .= '<td class="barre" style="cursor : pointer; color : #'.$v['color_stfact'].' !important;" onclick="window.location=\''.$suffixeLink.'facturier/Facture.php?id_fact='.$v['id_fact'].'\'">'.$v['contact'].'</td>';
            $out .= '<td class="barre" style="cursor : pointer; color : #'.$v['color_stfact'].' !important;" onclick="window.location=\''.$suffixeLink.'facturier/Facture.php?id_fact='.$v['id_fact'].'\'">'.$v['civ_cont'].' '.$v['prenom_cont'].' '.$v['nom_cont'].'</td>';
            $out .= '<td class="barre" style="cursor : pointer; color : #'.$v['color_stfact'].' !important;" onclick="window.location=\''.$suffixeLink.'facturier/Facture.php?id_fact='.$v['id_fact'].'\'">'.$v['titre_fact'].'</td>';
            $out .= '<td class="barre right" style="cursor : pointer; color : #'.$v['color_stfact'].' !important;" onclick="window.location=\''.$suffixeLink.'facturier/Facture.php?id_fact='.$v['id_fact'].'\'">'.$daterecord_fact.'</td>';
            $out .= '<td class="barre right" style="cursor : pointer; color : #'.$v['color_stfact'].' !important;" onclick="window.location=\''.$suffixeLink.'facturier/Facture.php?id_fact='.$v['id_fact'].'\'">'.$datereglement_fact.'</td>';
            //$out .= '<td class="barre right" style="cursor : pointer; color : #'.$v['color_stfact'].' !important;" onclick="window.location=\''.$suffixeLink.'facturier/Facture.php?id_fact='.$v['id_fact'].'\'">'.prepareNombreAffichage($v['sommeHT_fact']).' &euro;</td>';
            $out .= '<td class="barre right" style="cursor : pointer; color : #'.$v['color_stfact'].' !important;" onclick="window.location=\''.$suffixeLink.'facturier/Facture.php?id_fact='.$v['id_fact'].'\'">'.prepareNombreAffichage($sommeTTC_fact).' &euro;</td>';
            $out .= '<td class="barre" style="cursor : pointer; color : #'.$v['color_stfact'].' !important;" onclick="window.location=\''.$suffixeLink.'facturier/Facture.php?id_fact='.$v['id_fact'].'\'"><div class="square" style="background-color:#'.$v['color_stfact'].';">&nbsp;</div>'.$v['nom_stfact'].'</td>';
            if(!$modHistorique)
                $out .= '<td class="right">'.$action.'</td>';
            $out .= '</tr>';
            $alternance = (($alternance+1) % 2);
            $totalDisplay += $v['sommeHT_fact'];
            $totalTTCDisplay += $sommeTTC_fact;
        }
        $out .= '<tr class="titre noround roundbottom">';
        if(!$modHistorique)
            $out .= '<td colspan="5"></td>';
        else $out .= '<td colspan="5"></td>';
        //$out .= '<th class="bordG right">'.prepareNombreAffichage($totalDisplay).' &euro;</th>';
        $out .= '<th class="bordD bordG right">'.prepareNombreAffichage($totalTTCDisplay).' &euro;</th>';
        $out .= '<td colspan="2"></td>';
        $out .= '</tr>';

        $out .= '</tbody></table>';
        if(!$modHistorique)
            $out .= $this->ResultGroupActionForm();
        $out .= '</form></div>';
        if($modHistorique)
            $titre = $this->resultNavigationTitle($datas['from'],$datas['limit'],$datas['total'],'factureHisto');
        else $titre = $this->resultNavigationTitle($datas['from'],$datas['limit'],$datas['total'],'facture');
        $bouton .= $this->resultNavigation($datas['from'],$datas['limit'],$datas['total'],"ChangeFactureResultPage",$suffixeLink);
        loadPlugin(array('OOConverter'));
        $availableConvFormat = OOConverterAvailable('spreadsheet');
        $bouton .= '<div style="display:none;" id="divExportTableur">'.selectTag('format_export', $availableConvFormat, '', '', ' onchange="factureDoExportTableur(this.value);" ').'</div>';
        $bouton .= '<a name="export" style="cursor:pointer;" onclick="exportTab();"><img title="exportation tableur" alt="exportation tableur" src="'.$suffixeLink.'img/prospec/export-csv.png"/> Export Tableur</a>';
        $bouton .= $this->ResultGroupActionBouton($suffixeLink);

        if($modHistorique)
            $bouton = '';
        return generateZBox($titre,$titre,$out,$bouton,"searchListeFacture",'');
    }


    private function ResultGroupActionForm() {
        $sql = new factureModel();
        $out = '<div id="divGroupAction" style="display: none"><div class="block width50">';
        $fieldset = new ZunoFieldset('Type d\'action','','height: 210px');
        $fieldset->ligneFree(inputTag('radio', 'groupedAction', '', '', '', 'reinit', ' onclick="toggleSubGroupedAction(\'groupedSubActionReinit\')"'),'Re-initialiser ces factures','ZTips.facture.groupActionReinit');
        $fieldset->ligneFree(inputTag('radio', 'groupedAction', '', '', '', 'valide', ' onclick="toggleSubGroupedAction(\'groupedSubActionValid\')"'),'Marquer ces factures comme validées','ZTips.facture.groupActionValid');
        $fieldset->ligneFree(inputTag('radio', 'groupedAction', '', '', '', 'attente', ' onclick="toggleSubGroupedAction(\'groupedSubActionEnvoye\')"'),'Marquer ces factures en attente de règlement','ZTips.facture.groupActionEnvoye');
        $fieldset->ligneFree(inputTag('radio', 'groupedAction', '', '', '', 'regle', ' onclick="toggleSubGroupedAction(\'groupedSubActionRegle\')"'),'Marquer ces factures comme réglées','ZTips.facture.groupActionRegle');
        $fieldset->ligneFree(inputTag('radio', 'groupedAction', '', '', '', 'archivate', ' onclick="toggleSubGroupedAction(\'groupedSubActionArchive\')"'),'Archivage des facture','ZTips.facture.groupActionArchive');
        $fieldset->ligneFree(inputTag('radio', 'groupedAction', '', '', '', 'changeAttribute', ' onclick="toggleSubGroupedAction(\'groupedSubActionChangeAttribute\')"'),'Changer un attribut par lot','ZTips.facture.groupActionChangeAttribute');
        $out.= $fieldset->generateFieldset();
        $out.= '</div><div class="block width50"><div id="groupedSubAction">';
        $fieldset = new ZunoFieldset('Re-initialiser ces factures','groupedSubActionReinit','height: 150px');
        $fieldset->addOtherLigne('<div class="row"><i>Aucune action sécifique n\'est requise</i></div>');
        $out.= $fieldset->generateFieldset();
        $fieldset = new ZunoFieldset('Marquer ces factures comme validées','groupedSubActionValid','height: 150px');
        $fieldset->ligneInput('Commentaire', 'commentaireValid');
        $out.= $fieldset->generateFieldset();
        $fieldset = new ZunoFieldset('Marquer ces factures en attente de règlement','groupedSubActionEnvoye','height: 150px');
        $fieldset->ligneInput('Commentaire', 'commentaireEnvoye');
        $out.= $fieldset->generateFieldset();
        $fieldset = new ZunoFieldset('Marquer ces factures comme Réglées','groupedSubActionRegle','height: 150px');
        $fieldset->ligneInput('Commentaire Règlement', 'commentreglement_fact');
        $fieldset->ligneInput('Commentaire', 'commentaireRegle');
        $out.= $fieldset->generateFieldset();
        $fieldset = new ZunoFieldset('Archivage des factures','groupedSubActionArchive','height: 150px');
        $fieldset->addOtherLigne('<div class="row"><i>Seul les factures réglées seront archivées.</i></div>');
        $out.= $fieldset->generateFieldset();
        $fieldset = new ZunoFieldset('Changer un attribut par lot','groupedSubActionChangeAttribute','height: 150px');
        $fieldset->ligneInput('Titre', 'titre_fact');
        $fieldset->ligneInput('Commentaire', 'commentaire_fact');
        $fieldset->ligneFree('TVA',selectTVATag("tauxTVA_fact", array(), '', '', '', true));
        $fieldset->ligneSelect('Mode de règlement', 'modereglement_fact','',$sql->getModeReglement());
        $fieldset->ligneSelect('Cond. de règlement', 'condireglement_fact','',$sql->getCondReglement());
        $fieldset->ligneSelect('Commercial', 'commercial_fact','',$sql->getUser());
        $out.= $fieldset->generateFieldset();
        $out.= '</div>';
        $fieldset = new ZunoFieldset('Executer le changement');
        $fieldset->ligneLinkTo('','javascript:submitGroupAction(\'resultFormFacture\',\'resultFactureAction\');" class="bouton','Effectuer les changements','../img/prospec/voir.png');
        $out.= $fieldset->generateFieldset();
        $out.= '</div></div></div>';
        return $out;
    }

    private function ResultGroupActionBouton($suffixeLink) {
        $bouton = '<a name="groupAction" style="cursor:pointer;" onclick="toggleGroupedAction(\'divGroupAction\');"><img title="actions groupées" alt="actions groupées" src="'.$suffixeLink.'img/prospec/work-multiple.png"/> Actions groupées</a>';
        return $bouton;
    }



    public function creerFacture($light = 'non', $mess = '', $data = array()) {
        if($light == 'light') {
            return $this->contactsFacture($data);
        }
        else {
            $script = '<script type="text/javascript">function autoCompletage() {new Ajax.Autocompleter("ACentreprise_fact", "ACentreprise_fact_choix", "../ajaxRef.php?action=listeEntCmdFact", { paramName: "value", minChars: 2, indicator : "loadingEnt", afterUpdateElement : autoCompleteChamps } ); ';
            $script .= 'contact_completer = new Ajax.Autocompleter("ACcontact_fact", "ACcontact_fact_choix", "../ajaxRef.php?action=listeContact", { paramName: "value", minChars: 2, indicator : "loadingCont", afterUpdateElement : autoCompleteHiddenBis } ); ';
            $script .= 'contact2_completer = new Ajax.Autocompleter("ACcontact_achat_fact", "ACcontact_achat_fact_choix", "../ajaxRef.php?action=listeContact", { paramName: "value", minChars: 2, indicator : "loadingCont2", afterUpdateElement : autoCompleteHiddenBis } ); ';
            $script .= 'new Ajax.Autocompleter("ACpays_fact", "ACpays_fact_choix", "../ajaxRef.php?action=listePays", { paramName: "value", minChars: 2, afterUpdateElement : autoCompleteHidden, indicator : "loadingPays" } ); } ';
            $script .= "\n".'function autoCompleteHiddenBis(text, li) {$(text.id+\'hidden\').value = li.title; if(li.getAttribute(\'value\')) {var id = li.getAttribute(\'value\').split(\'-_-\'); $(\'devisadd\').value = id[0]; $(\'devisadd1\').value = id[1]; $(\'deviscp\').value = id[2]; $(\'devisVille\').options[0]= new Option(id[3], id[3]); $(\'ACpays_facthidden\').value = id[4]; $(\'ACpays_fact\').value = id[5];} }';
            $script .= 'function autoCompleteChamps(text, li) { id = li.title.split(\'-_-\'); $(\'ACaffaire_facthidden\').value = id[0]; $(\'ACentreprise_facthidden\').value = id[1]; $(\'devisadd\').value = id[2]; $(\'devisadd1\').value = id[3]; $(\'deviscp\').value = id[4]; $(\'devisVille\').options[0]= new Option(id[5], id[5]); $(\'ACpays_facthidden\').value = id[6];  $(\'ACpays_fact\').value = id[7]; $(\'ACcontact_facthidden\').value = $(\'ACcontact_achat_facthidden\').value = id[8]; $(\'ACcontact_fact\').value = $(\'ACcontact_achat_fact\').value = id[9]+\' \'+id[10]+\' \'+id[11]; contact_completer.options.defaultParams=\'id_ent=\'+$F(\'ACentreprise_facthidden\'); contact2_completer.options.defaultParams=\'id_ent=\'+$F(\'ACentreprise_facthidden\'); $(\'addContTech\').style.display="inline"; $(\'addContFact\').style.display="inline"; } ';
            $script .= 'function ChangeFactureAction(value) { $(\'actionFactureAdd\').value = value; } ';
            $script .= 'function verifVide(objet) { if(objet.value == "") { if(objet.id == \'ACentreprise_fact\') {$(\'boutonSubmitForm\').style.display = \'none\';} { $(objet.id+\'hidden\').value = ""; } } } ';
            $script .= 'function resetForm() {var form = $(\'formAddFacture\'); form.reset(); form.select(\'input[type="hidden"]\').each(function(input) {input.clear();}); } ';
            $script .= "\n".'function verifAutocompleteur(champ) {if(champ.value == "" || champ.value == null) {$(champ.id+\'hidden\').value = "";} else if(!$(champ.id+\'_choix\').hasChildNodes()) {alert(\'Cette valeur n\\\'existe pas.\'); champ.value=$(champ.id+\'old\').value; setTimeout(function() { champ.focus(); }, 100) } } ';
            $script .= "\n".'function verifAutocompleteurBis(champ) {if(champ.value == "" || champ.value == null) {$(\'ACentreprise_facthidden\').value = ""; $(\'ACaffaire_facthidden\').value = "";} }';
            $script .= 'function addContact(idRetour) { var ent=\'action=addContFact&entreprise=\'+$(\'ACentreprise_facthidden\').value+\'&idRetour=\'+idRetour; zuno.popup.open(\'Facture.php\', ent,800,300);} ';
            $script .= 'function addEntreprise() { zuno.popup.open(\'Facture.php\', \'action=addEnt\',800,320);} ';
            $script .= '</script>';

            if($light == 'avoir')
                $titre = 'Nouvel avoir';
            else
                $titre = 'Nouvelle facture';
            $corps = '<form name="addFacture" action="FactureCreate.php" method="post" id="formAddFacture" >';
            $corps .= '<input type="hidden" name="action" value="addFacture" id="actionFactureAdd" />';
            $corps .= '<input type="hidden" name="type" value="'.ucfirst($light).'" />';
            if($mess != '')
                $corps .= '<span class="important" style="text-align : center;">'.$mess.'</span>';
            $corps .= '<div id="contactsFacture">';
            $corps .= $this->contactsFacture();
            $corps .= '</div>';
            $bouton = '<a name="reset" style="cursor:pointer;" title="Reset" onclick="resetForm();"><img src="../img/prospec/cancel.png" alt="reset" onload="autoCompletage();"/> Recommencer</a>';
            $bouton .= '<a id="boutonSubmitForm" href="javascript:document.addFacture.submit();" onclick="ChangeFactureAction(\'addFacture\');"><img alt="confirmer" title="Enregistrer" src="../img/prospec/confirm.png" /> Enregistrer</a>';
            return $script.generateZBox($titre,$titre,$corps,$bouton,"createFactureForm",'');
        }
    }

    private function contactsFacture($data = array()) {
        $out = '<div class="block width50">';
        $out .= '<fieldset class="form">';
        $out .= '<legend>Informations sur la facture</legend>';
        $out .= '<div class="row">';
        $out .= '<div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.cmdOuEnt);">Commande / Entreprise</div>';
        $out .= '<div class="field">'.inputTag('text', 'entreprise', 'icon', '', '16', $data['entreprise'], ' id="ACentreprise_fact" onchange="verifAutocompleteurBis(this);"');
        $out .= '<a id="addEnt" name="addEnt" title="Ajouter une entreprise" onclick="addEntreprise();"><img alt="ent" title="Ajouter une entreprise" src="../img/prospec/entreprise.png" /></a>';
        $out .= '<img style="display:none; margin-left:5px;" id="loadingEnt" src="../img/ajax-loader.gif" alt="loader"/></div>';
        $out .= '<input type="hidden" name="entreprise_fact" id="ACentreprise_facthidden" value="'.$data['entreprise_fact'].'" /><input type="hidden" name="commande_fact" id="ACaffaire_facthidden" value="'.$data['commande_fact'].'" />
					<div id="ACentreprise_fact_choix" class="autocomplete"></div>';
        $out .= '</div>';
        $out .= '<div class="row oblig">';
        $out .= '<div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.contactTech);">Contact technique</div>';
        $out .= '<div class="field">'.inputTag('text', 'contact', '', '', '16', $data['contact'], ' id="ACcontact_fact" onchange="verifAutocompleteur(this);"');
        $out .= '<a id="addContTech" style="display:none;" name="addCont" title="Ajouter un contact" onclick="addContact(\'ACcontact_fact\');"><img alt="cont" title="Ajouter un contact" src="../img/prospec/add.png" /></a>';
        $out .= '<img style="display:none; margin-left:5px;" id="loadingCont" src="../img/ajax-loader.gif" alt="loader"/></div>';
        $out .= '<input type="hidden" name="contact_fact" id="ACcontact_facthidden" value="'.$data['contact_fact'].'" />
					<div id="ACcontact_fact_choix" class="autocomplete"></div>';
        $out .= '</div>';
        $out .= '<div class="row oblig">';
        $out .= '<div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.contactAchat);">Contact facturation</div>';
        $out .= '<div class="field">'.inputTag('text', 'contact2', '', '', '16', $data['contact2'], ' id="ACcontact_achat_fact" onchange="verifAutocompleteur(this);"');
        $out .= '<a id="addContFact" style="display:none;" name="addCont" title="Ajouter un contact" onclick="addContact(\'ACcontact_achat_fact\');"><img alt="cont" title="Ajouter un contact" src="../img/prospec/add.png" /></a>';
        $out .= '<img style="display:none; margin-left:5px;" id="loadingCont2" src="../img/ajax-loader.gif" alt="loader"/></div>';
        $out .= '<input type="hidden" name="contact_achat_fact" id="ACcontact_achat_facthidden" value="'.$data['contact_achat_fact'].'" />
					<div id="ACcontact_achat_fact_choix" class="autocomplete"></div>';
        $out .= '</div>';
        $out .= '<div class="row">';
        $out .= '<div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.nom);">Titre</div>';
        $out .= '<div class="field">'.inputTag('text', 'titre_fact', '', '', '16', $data['titre_fact']).'</div>';
        $out .= '</div>';
        $out .= '</fieldset></div>';

        $out .= '<div class="block width50">';
        $out .= '<fieldset class="form">';
        $out .= '<legend>Coordonnées de la facture</legend>';
        $out .= '<div class="row">';
        $out .= '<div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.deliveryAdd);">Adresse</div>';
        $out .= '<div class="field">'.inputTag('text', 'add1_fact', '', '', '16', $data['add1_fact'], ' id="devisadd" ').'</div>';
        $out .= '</div>';
        $out .= '<div class="row">';
        $out .= '<div class="label">Complément</div>';
        $out .= '<div class="field">'.inputTag('text', 'add2_fact', '', '', '16', $data['add2_fact'], ' id="devisadd1" ').'</div>';
        $out .= '</div>';
        $out .= '<div class="row">';
        $out .= '<div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.deliveryCpVille);">CP - ville</div>';
        $out .= '<div class="field">'.inputTag('text','cp_fact','civ','',4,$data['cp_fact'],' id="deviscp" onchange="zuno.ajax.get.html(\'../ajaxRef.php\',\'action=villeForCp&cp=\'+this.value,\'devisVille\');"').selectTag('ville_fact',array(),$data['ville_fact'], 'nextciv',' id="devisVille"  onblur="finishEditing();" onclick="beginEditing(this);"').'</div>';
        $out .= '</div>';
        $out .= '<div class="row">';
        $out .= '<div class="label" onmouseover="zuno.tooltip(this,ZTips.facture.deliveryPays);">Pays</div>';
        $out .= '<div class="field">'.inputTag('text', 'pays', '', '', '16', $data['pays'], ' id="ACpays_fact" onchange="verifVide(this);" ');
        $out .= '<img style="display:none; margin-left:5px;" id="loadingPays" src="../img/ajax-loader.gif" alt="loader"/></div>';
        $out .= '<input type="hidden" name="pays_fact" id="ACpays_facthidden" value="'.$data['pays_fact'].'" />
					<div id="ACpays_fact_choix" class="autocomplete"></div>';
        $out .= '</div>';
        $out .= '</fieldset></div>';
        return $out;
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

        return generateZBox($titre,$titre,$corps,'',"popupFactureActu",'');
    }

    public function popupCB($data, $id) {
        $js = '<script>'."\n";
        foreach($data as $v) {

            if($v['prenom_cp'] != "")
                $v['prenom_cont'] = $v['prenom_cp'];
            if($v['nom_cp'] != "")
                $v['nom_cont'] = $v['nom_cp'];

            $liste[$v['id_cont']] = $v['civ_cont']." ".$v['nom_cont'];
            $js .= "cont['".$v['id_cont']."'] = new Array();"."\n";
            $js .= "cont['".$v['id_cont']."']['nom'] = '".$v['nom_cont']."';"."\n";
            $js .= "cont['".$v['id_cont']."']['prenom'] = '".$v['prenom_cont']."';"."\n";
            $js .= "cont['".$v['id_cont']."']['civ'] = '".$v['civ_cont']."';"."\n";
            $js .= "cont['".$v['id_cont']."']['mail'] = '".$v['mail_cont']."';"."\n";
            $js .= "cont['".$v['id_cont']."']['carte'] = '".$v['finCarte_cont']."';"."\n";
            $js .= "cont['".$v['id_cont']."']['date'] = '".$v['dateCarte_cont']."';"."\n";
            $js .= "cont['".$v['id_cont']."']['cvv'] = '".$v['cvvCarte_cont']."';"."\n";
            $js .= "cont['".$v['id_cont']."']['wallet'] = '".$v['wallet_cont']."';"."\n";
        }

        $js .= '</script>';
        $fieldset = new ZunoFieldset("Contact",'','height:120px');
        $fieldset->ligneSelect("Contact", "id_cont", "", $liste, true, 'onchange="autoLoadDatas(this.value);"', "");
        $fieldset->ligneInput("Nom", "nom_cont", "", 'id="nomCB" onchange="change();"', "");
        $fieldset->ligneInput("Prenom", "prenom_cont", "", 'id="prenomCB" onchange="change();"', "");
        $fieldset->ligneInput("E-mail", "mail_cont", "", 'id="mailCB" ');
        $fieldset->ligneCheckBox("Prévenir le client", "email_client", true);

        $fieldset2 = new ZunoFieldset("Informations de payement",'','height:120px');
        $fieldset2->ligneInput("Numéro de carte", "finCarte_cont", "", 'id="carteCB" onchange="change();"', "");
        $fieldset2->ligneInput("Expire le", "dateCarte_cont", "", 'id="dateCB" onchange="change();"', "");
        $fieldset2->ligneInput("Cryptogramme", "cvvCarte_cont", "", 'id="cvvCB" onchange="change();"', "");
        $fieldset2->ligneCheckBox("Sauvegarder", "save_cont", false, 'id="saveCB"', "");
        $fieldset2->ligneCheckBox("Écrire dans le journal de banque", "journalise", true);

        $form = new ZunoForm("formCB", "facturier/Facture.php", "POST");
        $form->newInputHidden("id_fact", $id);
        $form->newInputHidden("action", "payerCB");
        $form->newInputHidden("wallet", "", 'walletCB');
        $form->newInputHidden("modified", 'false', 'modifiedCB');
        $form->newInputHidden("civ_cont", "M", 'civCB');
        $form->newBlock($fieldset->generateFieldset());
        $form->newBlock($fieldset2->generateFieldset());

        $html = new ZunoRenduHtml('popupCB');
        $html->insertJS($js);
        $html->generateZBox("Payer par carte bancaire", "Payer par carte bancaire", $form->generateForm(), $form->generateButtons('ContenuTraitementFacture', "Annuler", "Effectuer", "popup2"), 'idPopupCB');
        return $html->generateRendu();
    }



    /**
     * CPortlet de visualisation d'une affaire
     * @param $id_ent String: company ID
     * @return HTML portlet ready to insert
     */
    static function PortletStatistiquesFactures() {
        $tag['FormName'] = "StatsFact";
        $tag['FactureParDepartement'] = shell_exec('php Img.FactureParDepartement.php');
        $tag['FactureAnneeParDepartement'] = shell_exec('php Img.FactureAnneeParDepartement.php');
        $tag['FactureChart'] = shell_exec('php ../Img.FactureChart.php');
        $tag['FactureDelaiPaiement'] = '<img border="0" alt="Delai" src="Img.FactureDelaiPaiement.php"/>';
        $tag['FactureAnneeDelaiPaiement'] = '<img border="0" alt="Delai" src="Img.FactureAnneeDelaiPaiement.php"/>';
        $tag['FactureCondiReglement'] = shell_exec('php Img.FactureCondiReglement.php');
        $tag['FactureModeReglement'] = shell_exec('php Img.FactureModeReglement.php');
        $tag['FactureTopTen'] = '<img border="0" alt="TopTen" src="Img.FactureTopTen.php"/>';
        $tag['FactureAnneeTopTen'] = '<img border="0" alt="TopTen" src="Img.FactureAnneeTopTen.php"/>';
        $tag['FactureTresoGraph'] = '<img border="0" alt="Graph de trésorerie" src="../Img.TresoGraph.php"/>';
        $tag['FactureParProduit'] = '<img border="0" alt="TopTen" src="Img.FactureParProduit.php"/>';
        $tag['FactureAnneeParProduit'] = '<img border="0" alt="TopTen" src="Img.FactureAnneeParProduit.php"/>';
        $tag['FactureParFamilleProd'] = '<img border="0" alt="TopTen" src="Img.FactureParFamilleProd.php"/>';
        $tag['FactureAnneeParFamilleProd'] = '<img border="0" alt="TopTen" src="Img.FactureAnneeParFamilleProd.php"/>';
        $output = templating('facturier/PortletStatistiques',$tag);
        $titre = imageTag('../img/prospec/result.png','recherche').' Statistiques des factures';
        $pied 	= '';
        return generateZBox($titre,$titre,$output,'','StatFactures','');
    }

    /**
     * Méthode qui formate l'affichage de l'id d'une facture / d'un avoir
     * @param int $id L'id de la facture
     * @param string $date La date de création de la facture
     * @param string $type Le type facture ou avoir
     * @return string
     */
    private function prepareAffichageID($data) {
        $req = new factureModel();
        return $req->getFormatedIdFromData($data);
    }

    /**
     * Méthode qui génère une popup de suppression de facture(s)
     * @param array $id Le / Les ID(s) de facture(s) à archiver
     * @return string Le code HTML
     */
    public function popupConfirmSupp($id) {
        $titre = "Suppression";
        $form = new ZunoForm('FactureToSupp', 'Facture.php', 'POST');
        $form->newInputHidden('action', 'doSupp');
        $form->newInputHidden('facture', $id);
        $form->newInputHidden('ID['.$id.']',$id);
        $text = '<span class="important">Confirmez vous la supression de la facture "'.$id.'"</span>';
        $block = new ZunoRenduHtml();
        $block->generateZBox($titre, $titre, $text.$form->generateForm(), $form->generateButtonsNoAjax(), 'idPopupSuppFact');
        return $block->generateRendu();

    }

    public function popupConfirmClore($id, $type = 'facture') {
        if($type == 'facture') {
            $titre = "Clore la facture";
            $form = new ZunoForm('facturetoClore', 'facturier/Facture.php', 'POST');
            $form->newInputHidden('facture', $id);
            $fieldset = new ZunoFieldset('Journal de banque');
            $liste[0]['value'] = 'JournaliseAndClore';
            $liste[0]['label'] = 'Oui';
            $liste[1]['value'] = 'Clore';
            $liste[1]['label'] = 'Non';
            $fieldset->ligneRadio('Inscrire dans le journal de banque ?', 'action', 'JournaliseAndClore', $liste, '', '', true);
            $form->newBlock($fieldset->generateFieldset());
            $block = new ZunoRenduHtml('popupfactclore');
            $block->generateZBox($titre, $titre, $form->generateForm(), $form->generateButtons('traitementFacture', 'Annuler', 'Valier', 'popup2'), 'idPopupCloreFact');
            return $block->generateRendu();
        }
        else {
            $titre = 'Clore l\'avoir';
            $form = new ZunoForm('facturetoClore', 'facturier/Facture.php', 'POST');
            $form->newInputHidden('facture', $id);
            $form->newInputHidden('action', 'Clore');
            $text = '<span class="important">Confirmez vous la supression de l\'avoir "'.$id.'"</span>';
            $block = new ZunoRenduHtml();
            $block->generateZBox($titre, $titre, $text.$form->generateForm(), $form->generateButtons('traitementFacture', 'Annuler', 'Confirmer', 'popup2'), 'idPopupCloreFact');
            return $block->generateRendu();

        }

    }

    static function MyBureau() {
        $limit = 10;
        $req = new factureModel();
        $total = $req->getDataForHistoriqueVisit($limit,$type = 'COUNT');
        $result= $req->getDataForHistoriqueVisit($limit);
        $datas['total'] = $total[1][0]['counter'];
        $datas['data'] = $result[1];
        $datas['from'] = 0;
        $datas['limit'] = $limit;
        $datas['isHistoVisit'] = true;
        $view = new factureView();
        if($datas['total'] > 0)
            return $view->listeResult($datas);
        else return "";
    }


}




?>
