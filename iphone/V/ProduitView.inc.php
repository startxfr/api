<?php
class produitView
{
	static function searchResult($result, $from = 0, $limit = 5, $total = 0, $qTag ='')
	{
		if(is_array($result) and count($result) > 0)
		{
			$letter = $_SESSION['user']['LastLetterSearch'];
			foreach($result as $k => $v)
			{
				if($letter != strtoupper($v['nom_prod']{0})) { $letter = strtoupper($v['nom_prod']{0}) ; $list .= '</ul><h2>'.$letter.'</h2><ul class="iArrow">'; }
				elseif($from != 0) {$list .= '</ul><ul class="iArrow">';}
				$list .= '<li><a href="Produit.php?action=viewProd&id_prod='.$v['id_prod'].'" rev="async"><em>'.$v['nom_prod'].'</em><small>'.$v['treePathKey'].' '.$v['nom_prodfam'].'</small></a></li>';
			}
			$list = substr($list,5).'</ul>';
			if ($from == 0)
			{
			$out 	 = '<a href="#_MainMenu"  rel="action" class="iButton iBAction"><img src="Img/home.png" alt="Accueil" /></a>
						';
			}
			$out     .='<div class="iList">
						'.$list.'
					</div>';
			if($total > ($limit+$from))
			$out .= '<div class="iMore" id="searchResultProduitMore'.$from.'"><a href="Produit.php?action=searchProdContinue&total='.$total.'&from='.($limit+$from).'" rev="async">Plus de résultats</a></div>';
			$_SESSION['user']['LastLetterSearch'] = $letter;
			return $out;
		}
	}
	
		static function formAdd($value = array(),$onError = array(),$errorMess = '')
	{
		
		$error   = ($errorMess != '') ? '<div class="err">'.$errorMess.'</div>' : '';
		$out 	 = '<a href="#"  onclick="return WA.Submit(\'formAddProduit\',null,event)" rel="action" class="iButton iBAction"><img src="Img/save.png" alt="Enregistrer" /></a>
				<form id="formAddProduit" action="Produit.php?action=doAddProd" onsubmit="return WA.Submit(this,null,event)">
				'.$error.'
				<div class="iPanel">
					'.self::blockAdd($value,$onError);
		$out .= '<fieldset>
					<a href="#" class="BigButtonValidLeft" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler" /></a>
					<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formAddProduit\', null, event)"><img src="Img/big.valider.png" alt="Valider" /></a>
				</fieldset>
				</div>
				</form>';
		$out .= '<form id="formFamilleProd" action="Produit.php?action=familleProduitAdd" onsubmit="return WA.Submit(this,null,event)">' .
				'<div style="display:none"><input id="famille_hidden_produit_add" type="hidden" name="famille" value=0 />' .
				'<a id="valid_familleProduitAdd" onclick="return WA.Submit(\'formFamilleProd\',null,event)">Lien caché</a>' .
				'</div></form>';
		return $out;
	}
		static function add($value = array(),$onError = array(),$errorMess = '')
	{
				return self::formAdd($value,$onError,$errorMess);
	}
	
		static function blockAdd($value = array(), $onError = array())
	{
		$famille = HtmlFormIphone::InputLabelWnoku('nom_prodfam', $value['nom_prodfam'], 'Famille : ', 'id="familleProduitAdd" onkeyup="modifFamille(this.value);"');

		$out = '<fieldset><ul><li>'.HtmlFormIphone::InputLabel('nom_prod', $value['nom_prod'], 'Nom : ').'</li>';
		$out .= '<li>'.HtmlFormIphone::InputLabel('id_prod', $value['id_prod'], 'Référence : ', 'autocapitalize="off"').'</li>';
		$out .= '<li>'.HtmlFormIphone::TextareaLabel('description_prod', $value['description_prod'], '', 'Desc : ').'</li>';
		$out .= '<li>'.HtmlFormIphone::InputLabel('prix_prod', $value['prix_prod'], 'Prix (€) : ').'</li>';
		$out .= '<li>'.$famille.'</li>';
		$out .='<li class="proposition_entreprise" id="propositionFamilleJS" style="display:none" onclick="addFamilleAuto(\'propositionFamilleJS\');"></li>';
		$out .='<li class="proposition_entreprise" id="propositionFamille2JS" style="display:none" onclick="addFamilleAuto(\'propositionFamille2JS\');"></li>';
		$out .='<li class="proposition_entreprise" id="propositionFamille3JS" style="display:none" onclick="addFamilleAuto(\'propositionFamille3JS\');"></li>';
		$out .='<li class="proposition_entreprise" id="propositionFamille4JS" style="display:none" onclick="addFamilleAuto(\'propositionFamille4JS\');"></li>';
		$out .='<li class="proposition_entreprise" id="propositionFamille5JS" style="display:none" onclick="addFamilleAuto(\'propositionFamille5JS\');"></li>';
		$out .= '<input type="hidden" name="famille_prod" id="id_famille" value="'.$value['famille_prod'].'" />';
		$out .= '</ul></fieldset>';
		
		return $out;
	}
		static function formModif($value = array(),$onError = array(),$errorMess = '',$id = '', $fourn)
	{
		$error   = ($errorMess != '') ? '<div class="err">'.$errorMess.'</div>' : '';
		$out 	 = '<a href="#"  onclick="return WA.Submit(\'formModifProduit\',null,event)" rel="action" class="iButton iBAction"><img src="Img/save.png" alt="Enregistrer" /></a>
				<form id="formModifProduit" action="Produit.php?action=doModifProd&id_prod='.$id.'" onsubmit="return WA.Submit(this,null,event)">
				'.$error.'
				<div class="iPanel">
					'.self::blockModif($value,$onError, $fourn).'
				</div>
				</form>';
		$out .= '<form id="formFamilleProd" action="Produit.php?action=familleProduitAdd" onsubmit="return WA.Submit(this,null,event)">' .
				'<div style="display:none"><input id="famille_hidden_produit_add" type="hidden" name="famille" value=0 />' .
				'<a id="valid_familleProduitAdd" onclick="return WA.Submit(\'formFamilleProd\',null,event)">Lien caché</a>' .
				'</div></form>';
		return $out;
	}
	
		static function modif($value = array(),$onError = array(),$errorMess = '',$id = '', $fourn)
	{
				return self::formModif($value,$onError,$errorMess,$id, $fourn);
	}
		static function blockModif($value = array(), $onError = array(), $fourn = array())
	{
		$out = self::blockAdd($value, $onError);
		$numero = 1;
		$out .= '<fieldset>';
		foreach($fourn as $v)
			{
				$out .= '<ul><li>'.contactEntrepriseView::contactLinkSimple($v).'</li>';
				$out .= '<li>Code : '.$v['fournisseur_id'].'</li>';
				if($v['prixF'] == NULL)
				{$v['prixF'] = $value['prix_prod'];}
				$out .='<li>'.HtmlFormIphone::InputLabel('prixF'.$numero, $v['prixF'], 'Prix (€) : ', 'id="prixProdModif'.$numero.'" onChange="modifPrixProduit(this.value, \''.$numero.'\')"').'</li>';
				$out .='<li>'.HtmlFormIphone::InputLabel('remiseF'.$numero, $v['remiseF'], 'Remise % : ', 'id="remiseProdModif'.$numero.'" onChange="modifRemiseProduit(this.value, \''.$numero.'\')"').'</li>';
				$total = $v['prixF']*(100-$v['remiseF'])/100;
				$out .='<li id="prixcalcule'.$numero.'">Prix avec la remise : '.$total.' €</li></ul>';
				$out .= '<input type="hidden" name="fournisseur_id'.$numero.'" value="'.$v['fournisseur_id'].'" />';
				$numero++;
			}
		$out .='<input type="hidden" name="nombrefourn" value="'.$numero--.'" />';
		$out .= '</fieldset>';
		$out .= '<fieldset>
					<a href="#" class="BigButtonValidLeft" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler" /></a>
					<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formModifProduit\', null, event)"><img src="Img/big.valider.png" alt="Valider" /></a>
				</fieldset>';
		return $out;
	}
		static function view($value = array(), $fourn = array())
	{
		$out = '<a href="Produit.php?action=modifProd&id_prod='.$value["id_prod"].'"  rev="async" rel="action" class="iButton iBAction"><img src="Img/edit.png" alt="Modifier" /></a>';
		$out .= '<div class="iPanel"><fieldset>';
		$out .= '<ul><li>Nom : '.$value['nom_prod'].'</li>';
		$out .= '<li>Description : '.$value['description_prod'].'</li>';
		$out .= '<li>Famille : '.$value['treePathKey'].' '.$value['nom_prodfam'].'</li>';
		$out .= '<li>Prix de vente : '.$value['prix_prod'].' €</li>';
		$out .= '<li>Stock : '.$value['stock_prod'].'</li>';
		$out .= '<li>Devisé '.$value['nbreDevis'].' fois</li>';
		$out .= '<li>Commandé '.$value['nbreCommande'].' fois</li>';
		$out .= '<li>Facturé '.$value['nbreFacture'].' fois</li></ul>';
		if($value['stillAvailable_prod'] == 1)
		{
			$out .='<ul><li>Ce produit est actif</li></ul>';
		}
		else
		{
			$out .= '<ul><li>Ce produit n\'est plus vendu</li></ul>';
		}
	
		if($fourn != array() && $value['stillAvailable_prod'] == 1)
		{
			$out .= '</fieldset><fieldset><legend>Fournisseurs</legend>';
			foreach($fourn as $v)
			{
				$out .= '<ul><li>Code : '.$v['fournisseur_id'].'</li>';
				if($v['prixF'] == NULL)
				{$v['prixF'] = $value['prix_prod'];}
				$out .='<li>Prix : '.$v['prixF'].' €</li>';
				$out .='<li>Remise fournisseur : '.$v['remiseF'].' %</li>';
				$total = $v['prixF']*(100-$v['remiseF'])/100;
				$out .='<li>Prix avec la remise : '.$total.' €</li>';
				$out .= '<li>'.contactEntrepriseView::contactLinkWithLinks($v, 'fourn').'</li></ul>';
			}
		}
		$out .= '</fieldset>';
		$out .= self::subBlockAction($value, $fourn).'</div>';
		return $out;
	}
	static function subBlockAction($value = array(), $fourn = array())
	{
		if($value['stillAvailable_prod'] == 1)
		{
			$del = ($fourn != array()) ? '<li><a rev="async" href="Produit.php?action=dellFournProd&amp;id_prod='.$value['id_prod'].'"><img src="../img/prospec/contact.delete.png"/> Supprimer un fournisseur</a></li>' : '';
			$zero = ($del == '') ? 'zero' : 'non';
			$out = '<fieldset>
					<legend>Actions</legend>
					<ul class="iArrow">
						<li><a rev="async" href="Produit.php?action=stock&amp;id_prod='.$value['id_prod'].'"><img src="../img/page.menu/page.modif.png"/> Modifier le stock</a></li>
						<li><a rev="async" href="Produit.php?action=addFournProd&amp;id_prod='.$value['id_prod'].'&fourn='.$zero.'"><img src="../img/prospec/contact.png"/> Ajouter un fournisseur</a></li>
						'.$del.'
					</ul>
				</fieldset>';
			$out .= '<a href="#_FicheProduit" class="redButton" onClick="confirmBeforeClickSimple(\'confirmSuppProduitFiche\', \'la désactivation\')"><span>Désactiver cette référence</span></a>';
			$out .= '<form id="formSuppProduitFiche" action="Produit.php?action=suppProduit&id_prod='.$value['id_prod'].'" onsubmit="return WA.Submit(this,null,event)">' .
					'<div style="display:none;"><a id="confirmSuppProduitFiche" onclick="return WA.Submit(\'formSuppProduitFiche\',null,event)">Lien caché</a>' .
					'</div></form>';		
			return $out;
		}
		else
		{
			$out .= '<a href="#_FicheProduit" class="redButton" onClick="confirmBeforeClickSimple(\'confirmActivProduitFiche\', \'l\\\'activation\')"><span>Activer cette référence</span></a>';
			$out .= '<form id="formActivProduitFiche" action="Produit.php?action=activProduit&id_prod='.$value['id_prod'].'" onsubmit="return WA.Submit(this,null,event)">' .
					'<div style="display:none;"><a id="confirmActivProduitFiche" onclick="return WA.Submit(\'formActivProduitFiche\',null,event)">Lien caché</a>' .
					'</div></form>';		
			return $out;
		}
		
	}
	static function deleteFournProd($fourn = array(), $id = '')
	{
		$out = '<div class="iPanel"><fieldset>';
		foreach($fourn as $v)
			{
				$out .= '<ul><li>Code : '.$v['fournisseur_id'].'</li>';
				if($v['prixF'] == NULL)
				{$v['prixF'] = 'non renseigné';}
				$out .='<li>Prix : '.$v['prixF'].' €</li>';
				$out .='<li>Remise fournisseur : '.$v['remiseF'].' %</li>';
				$total = $v['prixF']*(100-$v['remiseF'])/100;
				$out .='<li>Prix avec la remise : '.$total.' €</li>';
				$out .= '<li>'.contactEntrepriseView::contactLinkSimple($v).'</li>';
				$out .= '</ul><a class="redButton" href="#_DellFourn" onClick="confirmBeforeClickBis(\'confirmSuppFournProd\', \''.$v['fournisseur_id'].'\')"><span>Enlever ce fournisseur</span></a><br />';
			}
		$out .= '</fieldset>';
		$out .= '<form id="formSuppFournProd" action="Produit.php?action=doDellFournProd&id_prod='.$id.'" onsubmit="return WA.Submit(this,null,event)">' .
				'<input id="confirmSuppFournProd_hidden" type="hidden" name="fourntosupp" value="" />'.
				'<div style="display:none;"><a id="confirmSuppFournProd" onclick="return WA.Submit(\'formSuppFournProd\',null,event)">Lien caché</a>' .
				'</div></form>';
		$out .= '</div>';
		return $out;
	}
	static function addFournProd($fourn = array(), $id_prod = '', $errorMess = '', $onError = array())
	{
		if(!is_array($fourn))
		{
			return '<div class="err">Aucun fournisseur disponible</div>';
		}
		$script = '<script> fourn = new Array(); remise = new Array();';
		foreach($fourn as $v)
		{
			$list[$v['id_fourn']] = $v['id_fourn'];
			$script .= 'fourn[\''.$v['id_fourn'].'\'] = \''.$v['nom_ent'].' ('.$v['cp_ent'].')\'; ';
			$script .= 'remise[\''.$v['id_fourn'].'\'] = \''.$v['remise_ent'].'\'; ';
		}
		$script .= '</script>';
		$form = '<fieldset><legend>Fournisseur</legend><ul>';
		$form .='<li>'.HtmlFormIphone::SelectLabel('fournisseur_id',$list, '', 'Id : ', true, 'onChange="affichernomfourn(this.value, \'\')"').'</li>';
		$form .= '<li id="nomfournisseurajout" style="display:none"></li></ul></fieldset>';
		$form .='<fieldset><ul><li>'.HtmlFormIphone::InputLabel('prixF', '', 'Prix (€) : ', 'id="prixfournisseurproduit" onChange="affichertotal(\'prix\', this.value)"').'</li>';
		$form .='<li>'.HtmlFormIphone::InputLabel('remiseF', '', '% Remise : ', 'id="remisefournisseurproduit" onChange="affichertotal(\'remise\', this.value)"').'</li>';
		$form .='<li id="totalfournisseurajout">Total unitaire : </li></ul></fieldset>';
		$error   = ($errorMess != '') ? '<div class="err">'.$errorMess.'</div>' : '';
		$out 	 = '<a href="#"  onclick="return WA.Submit(\'formAddFournProd\',null,event)" rel="action" class="iButton iBAction"><img src="Img/save.png" alt="Enregistrer" /></a>
				<form id="formAddFournProd" action="Produit.php?action=doAddFournProd&id_prod='.$id_prod.'" onsubmit="return WA.Submit(this,null,event)">
				'.$error.'
				<div class="iPanel">
					'.$form.'
				</div>
				</form>'.$script;
		return $out;
	}
	static function addNewFourn($data = array())
	{
		$script = '<script> fourn = new Array();';
		foreach($data as $v)
		{
			$list[$v['id_ent']] = $v['nom_ent'];
			$script .= 'fourn[\''.$v['id_ent'].'\'] = \''.$v['ville_ent'].' ('.$v['cp_ent'].')\';';
		}
		$script .= '</script>';
		$out = '<a href = "#" onclick="return WA.Submit(\'formAddNewFourn\', null, event)" rel="action" class="iButton iBAction"><img src="Img/save.png" alt="Enregistrer" /></a>';
		$out .='<form id="formAddNewFourn" action="Produit.php?action=doAddNewFourn" onsubmit="return WA.Submit(this,null,event)">';
		$out .='<div class="iPanel">';
		$out .='<fieldset><legend>Fournisseur</legend><ul>';
		$out .='<li>'.HtmlFormIphone::SelectLabel('entreprise_fourn',$list, '', 'Nom : ', true, 'onChange="affichernomfourn(this.value, \'New\')"').'</li>';
		$out .='<li id="nomfournisseurajoutNew" style="display:none"></li></ul></fieldset>';
		$out .= '<fieldset id="fieldsetContactAddFourn" style="display:none;"><legend>Contacts</legend>';
		$out .= '<div id="ContactNewFourn"></div>';
		$out .= '</fieldset>';
		$out .= '<fieldset>
					<a href="#" class="BigButtonValidLeft" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler" /></a>
					<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formAddNewFourn\', null, event)"><img src="Img/big.valider.png" alt="Valider" /></a>
				</fieldset>';
		$out .='</div></form>'.$script;
		$out .= '<form id="formaddNewFournCont" action="Produit.php?action=listeContFourn" onsubmit="return WA.Submit(this,null,event)">' .
				'<input id="id_ent_hidden" type="hidden" name="id_ent" value="" />'.
				'<div style="display:none;"><a id="addNewFournCont" onclick="return WA.Submit(\'formaddNewFournCont\',null,event)">Lien caché</a>' .
				'</div></form>';
		return $out;
	}
	static function addNewFournCont($liste, $value = array())
	{
		if(!is_array($liste))
		{
			$out = '<ul><li>Aucun contact associé, impossible d\'activer ce fournisseur.</li></ul>';
			return $out;
		}
		else
		{
			foreach($liste as $v)
			{
				$list[$v['id_cont']] = $v['prenom_cont'].' '.$v['nom_cont'];
			}
			$out = '<ul>';
			$out .='<li>'.HtmlFormIphone::SelectLabel('contactComm_fourn',$list, $value['contactComm_fourn'], 'Commerce : ', false).'</li>';
			$out .='<li>'.HtmlFormIphone::SelectLabel('ContactADV_fourn',$list, $value['ContactADV_fourn'], 'ADV : ', true).'</li>';
			$out .='<li>'.HtmlFormIphone::SelectLabel('contactFact_fourn',$list, $value['contactFact_fourn'], 'Facture : ', false).'</li>';
			$out .= '</ul>';
			return $out;
		}
	}
	static function modifFourn($liste, $value)
	{
		$out = '<a href = "#" onclick="return WA.Submit(\'formModifFourn\', null, event)" rel="action" class="iButton iBAction"><img src="Img/save.png" alt="Enregistrer" /></a>';
		$out .='<form id="formModifFourn" action="Produit.php?action=doModifFourn&id_fourn='.$value['id_fourn'].'" onsubmit="return WA.Submit(this,null,event)">';
		$out .='<div class="iPanel">';
		$out .='<fieldset><legend>Fournisseur</legend><ul>';
		$out .='<li>'.$value['nom_ent'].' ('.$value['cp_ent'].')</li></ul></fieldset>';
		$out .= '<fieldset><legend>Contacts</legend>';
		$out .= self::addNewFournCont($liste, $value);
		$out .= '</fieldset>';
		$out .= '<fieldset>
					<a href="#" class="BigButtonValidLeft" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler" /></a>
					<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formModifFourn\', null, event)"><img src="Img/big.valider.png" alt="Valider" /></a>
				</fieldset>';
		$out .='</div></form>';
		return $out;
	}
		static function stockModif($value = array(),$onError = array(),$errorMess = '')
	{
		$error   = ($errorMess != '') ? '<div class="err">'.$errorMess.'</div>' : '';
		$out 	 = '<a href="#"  onclick="return WA.Submit(\'formStockProduit\',null,event)" rel="action" class="iButton iBAction"><img src="Img/save.png" alt="Enregistrer" /></a>
				<form id="formStockProduit" action="Produit.php?action=doStock&id_prod='.$value['id_prod'].'" onsubmit="return WA.Submit(this,null,event)">
				'.$error.'
				<div class="iPanel">
					<fieldset><legend>Produit '.$value['id_prod'].'</legend>
						<ul><li>Stock courrant : '.$value['stock_prod'].'</li>
						<li>'.HtmlFormIphone::InputLabel('stock_prod', $value['stock_prod'], 'Nouveau : ').'</li></ul></fieldset>
				</div>
				</form>';
		return $out;
	}
}
?>
