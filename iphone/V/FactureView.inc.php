<?php
/**
 * Classe qui va générer tous les affichage en rapport avec les factures.
 * @author Nicolas Mannocci
 * @version 1
 */
class factureView {
    /**
     * Génération de la liste des résultats de la recherche
     */
    static function searchResult($result, $from = 0, $limit = 5, $total = 0, $qTag = '') {
	if(is_array($result) and count($result) > 0) {
	    foreach($result as $k => $v) {
		//On se balade dans le tableau de résultat de la recherche pour générer la liste.
		$brc = ($v['nom_cont'] != '') ? '<br/>': '';
		$list .= '<li><a href="Facture.php?action=view&id_fact='.$v['id_fact'].'&type='.$v['type_fact'].'" rev="async"><em>'.$v['id_fact'].' - '.$v['titre_fact'].' </em><small><b>'.$v['nom_ent'].'</b>'.$brc.$v['civ_cont'].' '.$v['nom_cont'].' '.$v['prenom_cont'].'</small></a></li>';
	    }
	    if ($from == 0) {//on affiche le haut de la page juste la première fois.
		$out 	 = '<a href="#_MainMenu"  rel="action" class="iButton iBAction"><img src="Img/home.png" alt="Accueil" /></a>' ;
	    }
	    //l'affichage de la liste générée
	    $out	.='<div class="iList"><ul class="iArrow">
						'.$list.'
					</ul></div>';
	    if($total > ($limit+$from))
	    //on affiche le bouton : PLUS DE RESULTAT si besoin est.
		$out .= '<div class="iMore" id="searchResultFactureMore'.$from.'"><a href="Facture.php?action=searchFactureContinue&from='.($limit+$from).'&total='.$total.'" rev="async">Plus de résultats</a></div>';
	    return $out;
	}
    }


    
    /**
     * FICHE DE VISUALISATION
     */

    /**
     * Formulaire complet de visualisation d'une facture.
     * @param array value les valeurs à afficher
     * @param string mode Le mode de vue à afficher
     * @return le code à retourner au navigateur
     */
    static function view($value = array(),$mode = '') {
	$type = ($value['type_fact'] == '' || $value['type_fact'] == null ) ? 'Facture' : $value['type_fact'];
	$txTva = ($value['tauxTVA_fact']/100 +1);
	$creation = '';
	if($value['daterecord_fact'] != NULL)
	    $creation = strftime("%A %d %B %G", strtotime($value['daterecord_fact']));
	$somme	 = ($value['sommeHT_fact'] != NULL) ? '<li>Total HT : <small>'.formatCurencyDisplay(abs($value['sommeHT_fact'])).'</small></li>' : '';
	$sommeTTC	 = ($value['sommeHT_fact'] != NULL) ? '<li>Total TTC : <small>'.formatCurencyDisplay(abs($value['sommeHT_fact']*$txTva)).'</small></li>' : '';
	$entreprise	 = ($value['entreprise_fact'] != NULL) ? '<li>Entreprise : '.contactEntrepriseView::contactLinkSimple($value).'</li>' : '';
	$contact	 = ($value['contact_fact'] != NULL) ? '<li>Contact : '.contactParticulierView::contactLinkSimple($value).'</li>' : '';
	$contactachat	 = ($value['contact_achat_fact'] != NULL && $value['contact_fact'] != $value['contact_achat_fact']) ? '<li>Acheteur : '.contactParticulierView::contactLinkSimple($value, 'achat').'</li>' : '';
	$commercial = ($value['commercial_fact'] != NULL) ? '<li>Commercial : '.$value['nom'].' '.$value['prenom'].'</li>' : '';
	$devis = ($value['devis_cmd'] != NULL) ? '<li>'.devisView::devisLinkSimple($value).'</li>' : '';
	$affaire = ($value['affaire_dev'] != NULL) ? '<li>'.affaireView::affaireLinkSimple($value).'</li>' : '';
	$commande = ($value['commande_fact'] != NULL) ? '<li>'.commandeView::commandeLinkSimple($value).'</li>' : '';
	$condireglement = $value['nom_condreg'];
	if($value['condireglement_fact'] > 3) {
	    if($value['datereglement_fact'] != NULL)
		$datereglement = strftime("%A %d %B %G", strtotime($value['datereglement_fact']));
	    $dreg = '<li>Date de règlement : <small>'.$datereglement.'</small></li>';
	}
	else {
	    $dreg = '';
	}
	$tva = '<li>TVA : <small>'.$value['tauxTVA_fact'].'% ('.formatCurencyDisplay($value['tauxTVA_fact']*$value['sommeHT_fact']/100).')</small></li>';
	//On vient d'effectuer tout un tas de tests pour s'assurer  que l'on ne vas afficher que des blocks avec des choses dedans.

	if($mode == 'afterModif' || $value['status_fact'] > 3) {
	    $linkHead = '<a href="Facture.php?action=view&id_fact='.$value['id_fact'].'"  rel="action" class="iButton iBAction"><img src="Img/config.png" alt="Recharger" /></a>';
	}
	else {
	    $linkHead = '<a href="Facture.php?action=modifFacture&id_fact='.$value["id_fact"].'&type='.$type.'"  rev="async" rel="action" class="iButton iBAction"><img src="Img/edit.png" alt="Modifier" /></a>';
	}

	//On génère maintenant le rendu visuel.
	$out = $linkHead.'<div class="iPanel">
				<fieldset>
					<legend></legend>
					<ul>
						<li><strong>'.$value["titre_fact"].
		'</strong></li>'.$commande.'

					</ul>
				</fieldset>
				<fieldset>
					<legend>Contacts</legend>
					<ul>
							'.$entreprise.'
							'.$contact.'
							'.$contactachat.'
					</ul>
				</fieldset>
				<fieldset>
					<legend>Ressources Liées</legend>
					<ul class="iArrow">
						'.self::subBlockRessourcesLiees($value).$devis.$affaire.'
					</ul>
				</fieldset>
				<fieldset>
					<legend>Autres informations</legend>
					<ul>
						<li>Facture créé le : <small>'.$creation.'</small></li>
						<li>Conditions de règlement : <small>'.$condireglement.'</small></li>'.$dreg.'
						<li>Statut : <small>'.$value['nom_stfact'].'</small></li>
						'.$commercial.'
					</ul>
				</fieldset>
				<fieldset><legend>Offre commerciale</legend>
						<ul class="iArrow">
							'.$somme.$tva.'
							'.$sommeTTC.'
							<li><a href="Facture.php?action=produits&id_fact='.$value["id_fact"].'&tva='.$value["tauxTVA_fact"].'&type='.$type.'" rev="async">Détail de l\'offre</a></li>
						</ul>
				</fieldset>'.self::subBlockAction($value, $type).'
			</div>';
	return $out;
    }

    /**
     * Fonction qui va générer l'affichage de la liste des produits.
     * @param array value Les valeurs à afficher
     * @param string id_fact L'identifiant de la facture
     * @param float tva Le montant de la TVA
     * @param string valide Indique le statut validé
     * @param string type Le type facture ou avoir de ce que l'on affiche
     * @return Le code HTML
     */
    static function produits($value = array(), $id_fact, $tva, $valide = '', $type = 'Facture') {

	if($valide != 'valide') {
	    $produits = '<a href="Facture.php?action=addProduit&id_fact='.$id_fact.'"  rev="async" rel="action" class="iButton iBAction"><img src="Img/add.png" alt="Ajouter" /></a><div class="iPanel">';
	}
	else {
	    $produits = '<a href="#_MainMenu"  rel="action" class="iButton iBBack"><img src="Img/home.png" alt="Accueil" /></a><div class="iPanel">';
	    $modif = '';
	}
	if($value == NULL) {
	    $produits .= '';
	    //Aucun produit à afficher, donc on ne génère pas le tableau.
	}
	else {
	    $TVA=($tva != NULL) ? $tva : 0;
	    $totalHT = 0;
	    foreach($value as $v) {
		$total = abs($v['prix'])*(1-$v['remise']/100)*$v['quantite'];
		$totalHT += $total;
		if(round($v['quantite'],0) != $v['quantite'])
		    $v['quantite'] = formatCurencyDisplay($v['quantite'],2,'');
		else  $v['quantite'] = formatCurencyDisplay($v['quantite'],0,'');
		if(round($v['prix'],0) != $v['prix'])
		    $v['prix'] = formatCurencyDisplay(abs($v['prix']));
		else  $v['prix'] = formatCurencyDisplay(abs($v['prix']),0);
		if(round($v['remise'],0) != $v['remise'])
		    $v['remise'] = formatCurencyDisplay($v['remise'],2,'%');
		else  $v['remise'] = formatCurencyDisplay($v['remise'],0,'%');
		if(round($total,0) != $total)
		    $total = formatCurencyDisplay($total);
		else  $total = formatCurencyDisplay($total,0);
		if($valide != 'valide') {
		    $modif = '<span style="float: right"><a style="margin: 0px; margin-top: -8px" href="Facture.php?action=modifProduit&id_fact='.$id_fact.'&id_prod='.urlencode($v['id_produit']).'&type='.$type.'" rev="async" ><img src="Img/edit.png" title="Modifier"/></a></span>';
		}
		$out.='<fieldset>'.$modif.'<legend>Produit '.$v['id_produit'].'</legend><ul>';
		$out .='<li><label>Référence : </label>'.$v['id_produit'].'</li>';
		$out .='<li><label>Libellé : </label>'.$v['desc'].'</li>';
		if($v['nom_prodfam'] != '')
		    $out .='<li><label>Famille : </label>'.$v['treePathKey'].' '.$v['nom_prodfam'].'</li>';
		$out .='<li><label>Qté x P.U. : </label>'.$v['quantite'].' x '.$v['prix'].'</li>';
		$out .='<li><label>Remise Client : </label>'.$v['remise'].'</li>';
		$out .='<li><label>Total Client : </label>'.$total.'</li>';
		$out .='</ul></fieldset>';
	    }
	    $TTC = (1+$TVA/100)*$totalHT;
	    $out.='<fieldset><legend>Total de l\'offre '.$v['id_fact'].'</legend><ul>';
	    $out .='<li><label>Total HT : </label>'.formatCurencyDisplay($totalHT).'</li>';
	    $out .='<li><label>Taux TVA : </label>'.formatCurencyDisplay($TVA,1,'%').'</li>';
	    $out .='<li><label>Total TTC : </label>'.formatCurencyDisplay($TTC).'</li>';
	    $out .='</ul></fieldset>';
	    $produits .= $out;
	}
	return $produits;//Sortie du résultat avec les liens pour ajout ou modification d'un produit.
    }

    /**
     * Fonction générant le rendu visuel du formulaire de modification d'un produit.
     * @param array value Les valeurs à afficher
     * @param string id_fact L'id de la facture
     * @param float tva Le montant de la TVA
     * @param string type Le type facture ou avoir
     * @return le code HTML
     */
    static function modifProduits($value = array(), $id_fact, $tva = 0, $type = 'Facture') {
	$out = '<a href="#"  onclick="return WA.Submit(\'formModifProduitFacture\',null,event)" rel="action" class="iButton iBAction"><img src="Img/save.png" alt="Enregistrer" /></a>
				<form id="formModifProduitFacture" action="Facture.php?action=doModifProduit&tva='.$tva.'&id_fact='.$id_fact.'&type='.$type.'" onsubmit="return WA.Submit(this,null,event)">
				<div class="iPanel">
					'.self::blockProduitsModif($value, $id_fact, 'rien', $type).'
					<fieldset>
						<a class="BigButtonValidLeft" href="#" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler"></a>
						<a class="BigButtonValidRight" href="#" onclick="return WA.Submit(\'formModifProduitFacture\',null,event)"><img src="Img/big.valider.png" alt="Valider"></a>
					</fieldset>
				</div>
				</form>';
	$out .= '<form id="formModifProduitFactureCache" action="Facture.php?action=suppProduit&tva='.$tva.'&id_fact='.$id_fact.'&type='.$type.'" onsubmit="return WA.Submit(this,null,event)">' .
		'<div style="display:none"><input id="id_produit_hidden_facture" type="hidden" name="id_produit" value=0 />' .
		'<a id="valid_suppProduitfacture" href="#" onclick="return WA.Submit(\'formModifProduitFactureCache\',null,event)">Lien suppression caché</a>' .
		'</div></form>';
	return $out;
    }

    /**
     * Fonction générant le rendu visuel du formulaire d'ajout d'un produit.
     * @param array value Les valeurs à afficher
     * @param string id_fact L'id de la facture
     * @param float tva Le montant de la TVA
     * @param string type Le type facture ou avoir
     * @return Le code HTML
     */
    static function addProduits($value = array(), $id_fact, $tva = 0, $type = 'Facture') {
	$out = '<a href="#"  onclick="return WA.Submit(\'formAddProduitFacture\',null,event)" rel="action" class="iButton iBAction"><img src="Img/save.png" alt="Enregistrer" /></a>
				<form id="formAddProduitFacture" action="Facture.php?action=doAddProduit&tva='.$tva.'&id_fact='.$id_fact.'&type='.$type.'" onsubmit="return WA.Submit(this,null,event)">
				<div class="iPanel">
					'.self::blockProduitsModif(array(), $id_fact, "on_ajoute", $type).'
					<fieldset>
						<a class="BigButtonValidLeft" href="#" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler"></a>
						<a class="BigButtonValidRight" href="#" onclick="return WA.Submit(\'formAddProduitFacture\',null,event)"><img src="Img/big.valider.png" alt="Valider"></a>
					</fieldset>
				</div>
				</form>';
	return $out;
    }

    /**
     * Fonction BLOCK qui inclu tout ce qu'il faut pour la modification des produits.
     * @param array value Les valeurs à afficher
     * @param string id_fact L'id de la facture
     * @param string onfekoi Indique si on est en modif ou pas
     * @param string type Le type facture ou avoir
     * @return string le Code HTML
     */
    static function blockProduitsModif($value = array(), $id_fact = NULL, $onfekoi = 'rien', $type = 'Facture') {
	$_SESSION['idfacture']=$id_fact;
	$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);

	$id = devisView::inputAjaxProduit('id_produitF',$value[0]['id_produit'],'Référence : ',false, 'facture');
	$desc = HtmlFormIphone::TextareaLabel('desc', $value[0]['desc'],' id="id_produitFdesc" ', 'Libellé : ');
	$prixV = HtmlFormIphone::InputLabel('prix', abs($value[0]['prix']), 'Px V : '.$signe, 'id="id_produitFprix" onchange="prixvonfacture(\'id_produitF\', this.value, \''.$signe.'\')"');
	$qtte = HtmlFormIphone::InputLabel('quantite', $value[0]['quantite'], 'Quantité : ', 'id="id_produitFquantite" onchange="qttonfacture(\'id_produitF\', this.value, \''.$signe.'\')"');
	$remiseV = HtmlFormIphone::InputLabel('remise', $value[0]['remise'], 'Remise V : ', 'id="id_produitFremiseV" onchange="remisevonfacture(\'id_produitF\', this.value, \''.$signe.'\')"');
	$prixtotal = abs($value[0]['prix'])*(1-$value[0]['remise']/100)*$value[0]['quantite'];
	$totalV = '<li id="id_produitFtotalV">TT V : '.$prixtotal.' &euro;</li>';
	if($onfekoi == 'rien') {
	    $out.='<fieldset><span class="smallActionButton"><a id="supprimer_produitfacture" onclick="confirmBeforeClick(\'valid_suppProduit\', \''.$value[0]['id_produit'].'\', \'facture\')"><img src="Img/delete.png" title="Supprimer"/></a></span><legend  class="smallActionLegend"> Produit : '.$value[0]['id_produit'].'</legend>';
	}
	else {
	    $out .='<fieldset><legend>Produit : </legend>';
	}
	$out .='<ul><li>'.$id.'</li><li>'.$desc.'</li><li>'.$qtte.'</li></ul>';
	$out .='<ul><li>'.$prixV.'</li><li>'.$remiseV.'</li></ul>';
	$out .='<ul>'.$totalV.'</ul>';
	$out .='</fieldset>';
	return $out;
    }

    /**
     * Fonction qui génère un "Lien simple" vers une facture.
     * @param array value Les valeurs à inclure dans le lien
     * @return string Le code html
     */
    static function factureLinkSimple($value = array()) {
	return '<a href="Facture.php?action=view&id_fact='.$value['id_fact'].'" class="Facture" rev="async"><img src="../img/actualite/facture.png"/> Facture '.$value['id_fact'].' '.$value['titre_fact'].'</a>';
    }

    /**
     * Fonction assurant l'affichage du formulaire de modification d'une facture.
     * @param array value Les valeurs à afficher
     * @param array onError Indique s'il y a une erreur le cas échéant
     * @param string id_fact L'id de la facture
     * @param string type Le type facture ou avoir
     * @return string le code HTML
     */
    static function modif($value = array(),$onError = array(),$errorMess = '',$id_fact = '', $type = 'Facture') {
	$error   = ($errorMess != '') ? '<div class="err">'.$errorMess.'</div>' : '';
	$out 	 = '<a href="#"  onclick="return WA.Submit(\'formModifFacture\',null,event)" rel="action" class="iButton iBAction"><img src="Img/save.png" alt="Enregistrer" /></a>
				<form id="formModifFacture" action="Facture.php?action=doModifFacture&id_fact='.$id_fact.'&type='.$type.'" onsubmit="return WA.Submit(this,null,event)">
				'.$error.'
				<div class="iPanel">
					'.self::blockModif($value,$onError, $type).'
				</div>
				</form>';
	return $out;
    }

    /**
     * Fonction BLOCK qui va générer le rendu visuel du formulaire de modification d'une facture.
     * @param array value Les valeurs initiales
     * @param array onError Indique le cas échant les erreurs à rectifier
     * @param string type Le type facture ou avoir
     * @return string Le code HTML
     */
    static function blockModif($value = array(), $onError = array(), $type = 'Facture') {
	$out = self::subBlockNomFacture($value, $onError, $type);
	$out .= self::subBlockContacts($value, $onError, $type);
	$out .= self::subBlockAdresse($value, $onError, $type);
	$out .= self::subBlockCommercial($value, $onError, $type);
	$out .= self::subBlockTVA($value, $onError, $type);
	$out .= self::subBlockReglement($value, $onError, $type);
	if($value['supprimable'] == '0')
	    $out .='<a href="Facture.php?action=suppFacture&id_fact='.$value["id_fact"].'&type='.$type.'" rev="async" class="redButton"><span>Supprimer cette Facture</span></a>';
	$out .= '<fieldset>
					<a href="#" class="BigButtonValidLeft" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler" /></a>
					<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formModifFacture\', null, event)"><img src="Img/big.valider.png" alt="Valider" /></a>
				</fieldset>';
	return $out;
    }

    /**
     *Fonction qui affiche le block de TVA
     * @param array $value Les valeurs du block
     * @param array $onError Les erreurs le cas échéant
     * @param string $type le type facture ou avoir
     * @return string Le code HTML
     */
    static function subBlockTVA($value = array(), $onError = array(), $type = 'Facture') {
	$list = array('0' => '0 %', '5.5' => '5,5 %', '19.6' => '19,6 %');
	$tva = HtmlFormIphone::Select('tauxTVA_fact', $list, $value['tauxTVA_fact'], false);
	$out = '<fieldset><legend>Taux T.V.A</legend>' .
		'<ul><li>'.$tva.
		'</li></ul></fieldset>';
	return $out;
    }

    /**
     * Fonction qui va géré l'affichage des actualités s'il y en a.
     * @param array $value Les valeurs à afficher
     * @return string Le code HTML
     */
    static function subBlockRessourcesLiees($value = array()) {
	$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$sqlConn->makeRequeteFree("select count(id) as C FROM actualite WHERE id_fact = '".$value['id_fact']."'");
	$temp = $sqlConn->process2();
	$totalActu = $temp[1][0]['C'];
	$info = new factureModel();
	$fileName = $info->getFactureFileName($value,'pdf');
	if (file_exists($info->getFactureDirectory().$fileName) )
	    $outLi .= "<li><a href=\"inc/explorer.php?download=" .$info->getFactureDirectory(false) . $fileName . "\" target=\"_blank\">" . imageTag('../img/files/pdf.png', 'version PDF') . ' ' . $fileName . "</a></li>";
	$fileName = $info->getFactureFileName($value,'odt');
	if (file_exists($info->getFactureDirectory().$fileName) )
	    $outLi .= "<li><a href=\"inc/explorer.php?download=" .$info->getFactureDirectory(false) . $fileName . "\" target=\"_blank\">" . imageTag('../img/files/document.png', 'version ODT') . ' ' . $fileName . "</a></li>";
	$fileName = $info->getFactureFileName($value,'doc');
	if (file_exists($info->getFactureDirectory().$fileName) )
	    $outLi .= "<li><a href=\"inc/explorer.php?download=" .$info->getFactureDirectory(false) . $fileName . "\" target=\"_blank\">" . imageTag('../img/files/document.png', 'version DOC') . ' ' . $fileName . "</a></li>";

	//Récupération des données
	$out = '<li><a rev="async" href="Actualite.php?action=viewFacture&amp;id_fact='.$value['id_fact'].'"><img src="Img/actualite.png"/> '.$totalActu.' Actualités</a></li>'.$outLi;
	return $out;//Génération de l'affichage.
    }

    /**
     * Fonction sous BLOCK qui gère l'affichage du formulaire pour les contacts.
     * @param array $value Les valeurs à afficher
     * @param array $onError Les erreurs le cas échéant
     * @param string $type Le type facture ou avoir
     * @return string le Code HTML
     */
    static function subBlockContacts($value = array(), $onError = array(), $type = 'Facture') {
	$particulier = contactParticulierView::inputAjaxContact('contact_fact',$value['contact_fact'],'Contact : ',false);
	$particulierERR	= (in_array('contact_fact',$onError)) ? '<span class="iFormErr"/>' : '';
	$acheteur = contactParticulierView::inputAjaxContact('contact_achat_fact',$value['contact_achat_fact'],'Acheteur : ',true);
	$acheteurERR	= (in_array('contact_achat_fact',$onError)) ? '<span class="iFormErr"/>' : '';

	$out = '<fieldset>
					<legend>Contacts</legend>
					<ul>
						<li>'.$particulier.$particulierERR.'</li>
						<li>'.$acheteur.$acheteurERR.'</li>
					</ul>
				</fieldset>';
	return $out;
    }

    /**
     * Fonction sous BLOCK qui gère l'affichage du formulaire pour le commercial.
     * @param array $value Les valeurs à afficher
     * @param array $onError Les erreurs le cas échéant
     * @param string $type Le type facture ou avoir
     * @return string le Code HTML
     */
    static function subBlockCommercial($value = array(), $onError = array(), $type = 'Facture') {
	$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$sqlConn->makeRequeteFree("select login, nom, prenom, civ from user order by nom;");
	$temp = $sqlConn->process2();
	$temp=$temp[1];
	foreach($temp as $k => $v) {
	    $userList[$v['login']] = $v['civ'].' '.$v['prenom'].' '.$v['nom'];
	}

	$valuecommercial = ($value['commercial_fact'] != NULL) ? $value['commercial_fact'] : $_SESSION['user']['id'];
	$commercial = HtmlFormIphone::Select('commercial_fact',$userList,$valuecommercial, false);
	$commercialERR	= (in_array('commercial_fact',$onError)) ? '<span class="iFormErr"/>' : '';

	$out = '<fieldset>
					<legend>Commercial</legend>
					<ul>
						<li>'.$commercial.$commercialERR.'</li>
					</ul>
				</fieldset>';
	return $out;
    }

    /**
     * Fonction sous BLOCK qui gère l'affichage du formulaire pour le devis lié.
     * @param array $value Les valeurs à afficher
     * @param array $onError Les erreurs le cas échéant
     * @param string $type Le type facture ou avoir
     * @return string Le code HTML
     */
    static function subBlockCommande($value = array(), $onError = array(), $type = 'Facture') {
	$devis = commandeView::inputAjaxCommande('commande_fact',$value['commande_fact'],'Commande : ',false);
	$devisERR	= (in_array('commande_fact',$onError)) ? '<span class="iFormErr"/>' : '';
	$out = '<fieldset>
					<legend>Commande</legend>
					<ul>
						<li>'.$devis.$devisERR.'</li>

					</ul>
				</fieldset>';
	return $out;
    }

    /**
     * Fonction sous BLOCK qui gère l'affichage pour les entrées d'une adresse.
     * @param array $value Les valeurs à afficher
     * @param array $onError Les erreurs le cas échéant
     * @param string $type Le type facture ou avoir
     * @return string Le code HTML
     */
    static function subBlockAdresse($value = array(), $onError = array(), $type = 'Facture') {
	$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$sqlConn->makeRequeteFree("select id_pays, nom_pays from ref_pays;");
	$temp = $sqlConn->process2();
	$temp=$temp[1];
	foreach($temp as $k => $v) {
	    $countryList[$v['id_pays']] = $v['nom_pays'];
	}
	$nom = HtmlFormIphone::InputLabel('nomentreprise_fact',$value['nomentreprise_fact'],'Nom : ');
	$add1 = HtmlFormIphone::InputLabel('add1_fact',$value['add1_fact'],'Adresse : ');
	$add2 = HtmlFormIphone::InputLabel('add2_fact',$value['add2_fact'],'Complément : ');
	$cp = HtmlFormIphone::InputLabel('cp_fact',$value['cp_fact'],'CP : ');
	$ville = HtmlFormIphone::InputLabel('ville_fact',$value['ville_fact'],'Ville : ');
	$pays 	= HtmlFormIphone::Select('pays_fact',$countryList,$value['pays_fact'],false);

	$out = '<fieldset>
					<legend>Adresse</legend>
					<ul>
						<li>'.$nom.'</li>
						<li>'.$add1.'</li>
						<li>'.$add2.'</li>
						<li>'.$cp.'</li>
						<li>'.$ville.'</li>
						<li>'.$pays.'</li>
					</ul>
				</fieldset>';
	return $out;
    }

    /**
     * Fonction sous BLOCK qui gère l'affichage du formulaire pour le nom du commande.
     * @param array $value Les valeurs à afficher
     * @param array $onError Les erreurs le cas échéant
     * @param string $type Le type facture ou avoir
     * @return string le code HTML
     */
    static function subBlockNomFacture($value = array(), $onError = array(), $type = 'Facture') {
	$nom = HtmlFormIphone::InputLabel('titre_fact',$value['titre_fact'],'Nom : ');
	$out = '<fieldset>
					<legend>'.$type.'</legend>
					<ul>
						<li>'.$nom.'</li>

					</ul>
				</fieldset>';
	return $out;
    }

    /**
     * Fonction qui affiche le block concernant le règlement
     * @param array $value Les valeurs à afficher
     * @param array $onError Les erreurs le cas échéant
     * @param string $type Le type facture ou avoir
     * @return string Le code HTML
     */
    static function subBlockReglement($value = array(), $onError = array(), $type = 'Facture') {
	$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$sqlConn->makeRequeteFree("select id_modereg, nom_modereg from ref_modereglement;");
	$temp = $sqlConn->process2();
	$temp=$temp[1];
	foreach($temp as $k => $v)
	    $list[$v['id_modereg']] = $v['nom_modereg'];
	$mr 	= HtmlFormIphone::SelectLabel('modereglement_fact',$list,$value['modereglement_fact'],'Mode : ',true);
	$mrERR= (in_array('modereglement_cmd',$onError)) ? '<span class="iFormErr"/>' : '';

	$sqlConn->makeRequeteFree("select id_condreg, nom_condreg from ref_condireglement;");
	$temp = $sqlConn->process2();
	$temp=$temp[1];
	foreach($temp as $k => $v)
	    $list[$v['id_condreg']] = $v['nom_condreg'];
	$cr 	= HtmlFormIphone::SelectLabel('condireglement_fact',$list,$value['condireglement_fact'],'Conditions : ',true);
	$crERR= (in_array('condireglement_cmd',$onError)) ? '<span class="iFormErr"/>' : '';

	$out = '<fieldset><legend>Règlement</legend>
				<ul><li>'.$mr.$mrERR.'</li><li>'
		.$cr.$crERR.'</li></ul></fieldset>';
	return $out;
    }

    /**
     *Fonction qui affiche le block d'action d'une facture
     * @param array $value Les valeurs à afficher
     * @param string $type Le type facture ou avoir
     * @return string Le code HTML
     */
    static function subBlockAction($value = array(), $type = 'Facture') {
	$out = '<fieldset>
				<legend>Actions</legend>
				<ul class="iArrow">';
	if ($value['status_fact'] <= 3)
	    $out.= '<li><a rev="async" href="Facture.php?action=addProduit&amp;id_fact='.$value['id_fact'].'&type='.$type.'"><img src="../img/prospec/devis.addProduct.png"/> Ajouter un Produit</a></li>';
	if ($value['sommeHT_fact'] > 0) {
	    $out.= '<li><a rev="async" href="Facture.php?action=voir&amp;id_fact='.$value['id_fact'].'&type='.$type.'"><img src="../img/prospec/facture.pdf.png"/> Voir le PDF</a></li>';
	    if ($value['status_fact'] < 4) {
		$preFixRec = ($value['status_fact'] >= 3) ? 'Re-e' : 'E';
		$preFixSend = ($value['status_fact'] >= 4) ? 'Re-e' : 'E';
		$out.= '<li><a rev="async" href="Facture.php?action=rec&amp;id_fact='.$value['id_fact'].'&type='.$type.'"><img src="../img/prospec/devis.record.png"/> '.$preFixRec.'nregistrer</a></li>';
		$out.= '<li><a rev="async" href="Facture.php?action=recsend&amp;id_fact='.$value['id_fact'].'&type='.$type.'"><img src="../img/prospec/devis.recsend.png"/> '.$preFixRec.'nregistrer & '.$preFixSend.'nvoyer</a></li>';
		if ($value['status_fact'] >= 3)
		    $out.= '<li><a rev="async" href="Facture.php?action=send&amp;id_fact='.$value['id_fact'].'&type='.$type.'"><img src="../img/prospec/devis.send.png"/> '.$preFixSend.'nvoyer</a></li>';
	    }
	    if ( ($value['status_fact'] == 4)and $_SESSION['user']['id'] == $value['commercial_fact'] ) {
		$out.= '<li><a rev="async" href="Facture.php?action=nonregle&amp;id_fact='.$value['id_fact'].'&type='.$type.'"><img src="../img/prospec/devis.perdu.png"/> Règlement attendu</a></li>';
		$out.= '<li><a rev="async" href="Facture.php?action=regle&amp;id_fact='.$value['id_fact'].'&type='.$type.'"><img src="../img/prospec/facture.valid.png"/> Règlement effectué</a></li>';
	    }
	    if ( ($value['status_fact'] == 5)and $_SESSION['user']['id'] == $value['commercial_fact'] ) {
		$out.= '<li><a rev="async" href="Facture.php?action=regle&amp;id_fact='.$value['id_fact'].'&type='.$type.'"><img src="../img/prospec/facture.valid.png"/> Règlement effectué</a></li>';
	    }
	}
	$out.= '<li><a rev="async" href="Facture.php?action=cloner&amp;id_fact='.$value['id_fact'].'&type='.$type.'"><img src="../img/prospec/devis.clone.png"/> Cloner cette facture</a></li>';
	if($type == 'Facture')
	    $out.='<li><a rev="async" href="Facture.php?action=avoir&amp;id_fact='.$value['id_fact'].'&type='.$type.'"><img src="../img/prospec/devis.clone.png"/> Créer un avoir</a></li>';
	$out.= '	</ul>
			</fieldset>';
	return $out;
    }


    /**
     * Fonction qui gère l'affichage lors de la suppression d'une facture.
     * @param array $value Les valeurs à afficher
     * @param string $type Le type facture ou avoir
     * @return string Le code HTML
     */
    static function delete($value = array(), $type = 'Facture') {
	if ($value["id_fact"] == 0) {
	    $out='<a href="#_MainMenu"  rel="action" class="iButton iBBack"><img src="Img/home.png" alt="Accueil" /></a>
			  <div class="iPanel">
			  <div class="err">
			  		<strong> Facture supprimé ! </strong>
				</div>
						';
	    return $out;
	}
	$creation = '';
	if($value['daterecord_fact'] != NULL)
	    $creation = strftime("%A %d %B %G", strtotime($value['daterecord_fact']));
	$somme	 = ($value['sommeHT_fact'] != NULL) ? '<li>Somme total HT : <small>'.formatCurencyDisplay($value['sommeHT_fact']).'</small></li>' : '';
	$entreprise	 = ($value['entreprise_fact'] != NULL) ? '<li>Entreprise : '.contactEntrepriseView::contactLinkSimple($value).'</li>' : '';
	$contact	 = ($value['contact_fact'] != NULL) ? '<li>Contact : '.contactParticulierView::contactLinkSimple($value).'</li>' : '';
	$contactachat	 = ($value['contact_achat_fact'] != NULL) ? '<li>Acheteur : '.contactParticulierView::contactLinkSimple($value, 'achat').'</li>' : '';
	$commercial = ($value['commercial_fact'] != NULL) ? '<li>Commercial : '.$value['nom'].' '.$value['prenom'].'</li>' : '';
	$commande = ($value['commande_fact'] != NULL) ? '<li>Commande liée : '.commandeView::commandeLinkSimple($value).'</li>' : '';
	//On vient d'effectuer tout un tas de tests pour s'assurer  que l'on ne vas afficher que des blocks avec des choses dedans.

	$linkHead = '<a href="Facture.php?action=doDeleteFacture&id_fact='.$value["id_fact"].'&type='.$type.'"  rev="async" rel="action" class="iButton iBAction"><img src="Img/remove.png" alt="Supprimer" /></a>';
	//On génère maintenant le rendu visuel.
	$out = $linkHead.'<div class="iPanel">' .
		'<div class="err">
			  		<strong> Êtes vous sur de vouloir supprimer cette facture ? </strong>
				</div>
				<fieldset>
					<legend></legend>
					<ul>
						<li><strong>'.$value["titre_fact"].
		'</strong></li>'.$commande.'

					</ul>
				</fieldset>
				<fieldset>
					<legend>Contacts</legend>
					<ul>
							'.$entreprise.'
							'.$contact.'
							'.$contactachat.'
					</ul>
				</fieldset>'.self::subBlockRessourcesLiees($value).'
				<fieldset>
					<legend>Autres informations</legend>
					<ul>
						<li>Commande créé le : <small>'.$creation.'</small></li>
						'.$somme.'
						<li>Statut : <small>'.$value['nom_stdev'].'</small></li>
						'.$commercial.'
					</ul>
				</fieldset>
				<fieldset><legend>Produits liés</legend>
						<ul class="iArrow"><li><a href="Facture.php?action=produits&id_fact='.$value["id_fact"].'&tva='.$value["tauxTVA_ent"].'" rev="async">Voir les produits</a></li></ul>
				</fieldset>
			</div>';
	return $out;
    }

    /**
     * Fonction qui gère l'affichage lors de l'ajout d'une facture.
     * @param array $value Les valeurs à afficher
     * @param array $onError Les erreurs le cas échéant
     * @param string $errorMess Le mesage d'erreur le cas échéant
     * @param string $type Le type facture ou avoir
     * @return string le code HTML
     */
    static function addPre($value = array(),$onError = array(),$errorMess = '', $type = 'Facture') {
	$error   = ($errorMess != '') ? '<div class="err">'.$errorMess.'</div>' : '';
	$out 	 = '<a href="#"  onclick="return WA.Submit(\'formAddPreFacture\',null,event)" rel="action" class="iButton iBAction"><img src="Img/save.png" alt="Enregistrer" /></a>
				<form id="formAddPreFacture" action="Facture.php?action=addFacture&type='.$type.'" onsubmit="return WA.Submit(this,null,event)">
				'.$error.'
				<div class="iPanel">
					'.self::blockAddPre($value,$onError, $type).'
				</div>
				</form>';
	return $out;
    }

    /**
     *Fonction qui affiche le formulaire d'ajout
     * @param array $value Les valeurs à afficher
     * @param array $onError Les erreurs le cas échéant
     * @param string $errorMess Le message d'erreur le cas échéant
     * @param string $type Le type facture ou avoir
     * @return string Le code HTML
     */
    static function add($value = array(), $onError = array(), $errorMess = '', $type = 'Facture') {
	$error   = ($errorMess != '') ? '<div class="err">'.$errorMess.'</div>' : '';
	$out 	 = '<a href="#"  onclick="return WA.Submit(\'formAddFacture\',null,event)" rel="action" class="iButton iBAction"><img src="Img/save.png" alt="Enregistrer" /></a>
				<form id="formAddFacture" action="Facture.php?action=doAddFacture&type='.$type.'" onsubmit="return WA.Submit(this,null,event)">
				'.$error.'
				<div class="iPanel">
					'.self::blockAdd($value,$onError, $type).'
				</div>
				</form>';
	return $out;

    }

    /**
     * Fonction BLOCK pour l'ajout d'une facture.
     * @param array $value Les valeurs à afficher
     * @param array $onError Les erreurs le cas échéant
     * @param string $type Le type facture ou avoir
     * @return string le code HTML
     */
    static function blockAddPre($value = array(), $onError = array(), $type = 'Facture') {

	$out = self::subBlockCommande($value, $onError, $type);
	$out .= '<fieldset>
					<a href="#" class="BigButtonValidLeft" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler" /></a>
					<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formAddPreFacture\', null, event)"><img src="Img/big.valider.png" alt="Valider" /></a>
				</fieldset>';
	return $out;
    }

    /**
     *FOnction qui affiche le block d'ajout
     * @param array $value Les valeurs à afficher
     * @param array $onError Les erreurs le cas échéant
     * @param string $type Le type facture ou avoir
     * @return string Le code HTML
     */
    static function blockAdd($value = array(), $onError = array(), $type = 'Facture') {
	$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$numero = 1;
	$default['modereglement_fact']= '3';
	$default['condireglement_fact']= '4';
	$commande = $value[0]['id_commande'];
	$outJS .= 'totalBrut = new Array();'."\n";
	$out = '<input type="hidden" name="commande_fact" id="commande_fact" value="'.$value[0]['id_commande'].'"/>';
	foreach ($value as $k => $v) {
	    $list = array();
	    $sqlConn->makeRequeteFree("select * from commande_produit dp left join produit_fournisseur pf on pf.produit_id = dp.id_produit left join fournisseur ON fournisseur.id_fourn = pf.fournisseur_id left join entreprise e ON e.id_ent = fournisseur.entreprise_fourn where dp.id_produit = '".trim($v['id_produit'])."' and dp.id_commande = '".trim($commande)."';");
	    $temp = $sqlConn->process2();
	    $temp=$temp[1];
	    $prix = ($v['prix_prod'] == NULL ) ? $v['prix'] : $v['prix_prod'];
	    $totalBrut = abs($prix)*$v['quantite']*(1-$v['remise']/100);
	    $total = $totalBrut;
	    $remise = HtmlFormIphone::InputLabel('remise',$v['remise'],'Remise : ', 'id="RemiseFacture'.$v['id_prod'].'" onchange="factureChangeRemise(\''.$v['id_prod'].'\', this.value)"');
	    $outJS .= 'totalBrut["'.$v['id_prod'].'"]='.$totalBrut.";\n";
	    $out .= '<fieldset><legend>Produit N° : '.$numero.'</legend><ul><li>Produit : '.$v['id_produit'].'</li>
				<li>Desc : '.$v['desc'].'</li>
				<li>Px * Qtté : '.abs($prix).' * '.$v['quantite'].'</li>
				<li>'.$remise.'</li>
				<li id="TotalFacture'.$v['id_prod'].'">Total : '.$signe.$total.' &euro;</li></ul></fieldset>';
	    $numero ++;
	}
	$out .= '<script>'.$outJS.'</script>';
	$out .= self::subBlockReglement($default, array(), $type);
	$out .= '<fieldset><ul><li>'.HtmlFormIphone::InputLabel('BDCclient', '', 'BDC : ').'</li></ul></fieldset>';
	$out .= '<fieldset>
					<a href="#" class="BigButtonValidLeft" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler" /></a>
					<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formAddFacture\', null, event)"><img src="Img/big.valider.png" alt="Valider" /></a>
				</fieldset>';
	return $out;
    }

    /**
     * Fonction de gestion de l'affichage de la fiche pour clonage
     * @param array $value Les valeurs à afficher
     * @param string $type Le type facture ou avoir
     * @param string $creationavoir Précise si on créé un avoir ou pas
     * @return string Le code HTML
     */
    static function cloner($value = array(), $type = 'Facture', $creationavoir = 'non') {
	$ckoi = ($type == 'Avoir') ? 'cet avoir' : 'cette facture';
	if($creationavoir == 'non')
	    return '<div class="iPanel"><br/><br/>
				<div class="msg"><br/>Merci de confirmer le clonage de '.$ckoi.'<br/></div>
				<br/>
				<fieldset>
					<a href="#" style="float: left; margin-left: 8px;"onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler"></a>
					<a href="Facture.php?action=doCloner&id_fact='.$value["id_fact"].'&type='.$type.'" rev="async" style="float: right; margin-right: 8px;"><img src="Img/big.valider.png" alt="Valider"></a>
				</fieldset>
			</div>';
	elseif($creationavoir == 'oui')
	    return '<div class="iPanel"><br/><br/>
				<div class="msg"><br/>Merci de confirmer la création d\'un avoir<br/></div>
				<br/>
				<fieldset>
					<a href="#" style="float: left; margin-left: 8px;"onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler"></a>
					<a href="Facture.php?action=doAvoir&id_fact='.$value["id_fact"].'&type='.$type.'" rev="async" style="float: right; margin-right: 8px;"><img src="Img/big.valider.png" alt="Valider"></a>
				</fieldset>
			</div>';
    }

    /**
     *Fonction qui permet de créer une facture en partant de rien
     * @param array $val Les valeurs à afficher
     * @param array $onError Les erreurs le cas échéant
     * @param string $errorMess Le message d'erreur le cas échéant
     * @param string $type Le type facture ou avoir
     * @return string Le code HTML
     */
    static function addExpress($val = array(), $onError = array(),$errorMess = '', $type = 'Facture') {
	$error   = ($errorMess != '') ? '<div class="err">'.$errorMess.'</div>' : '';

	$out 	 .= '<a href="#"  onclick="return WA.Submit(\'formAddFactureExpress\',null,event)" rel="action" class="iButton iBAction"><img src="Img/save.png" alt="Enregistrer" /></a>
				<form name="formAddFactureExpress" id="formAddFactureExpress" action="Facture.php?action=addFactureExpressSuite" onsubmit="return WA.Submit(this,null,event)">
				' .$error.'<div class="iPanel">
					'.self::blockAddExpress1($val, $onError, $type).'
				</div>
				</form>';
	$out .= '<form id="formEntrepriseFactureExpress" action="Facture.php?action=entrepriseFactureExpress" onsubmit="return WA.Submit(this,null,event)">' .
		'<div style="display:none"><input id="entreprise_hidden_facture_express" type="hidden" name="entreprise" value=0 />' .
		'<a id="valid_entrepriseFactureExpress" onclick="return WA.Submit(\'formEntrepriseFactureExpress\',null,event)">Lien caché</a>' .
		'</div></form>';
	return $out;
    }

    /**
     *Fonction qui affiche le block d'ajout express
     * @param array $val Les valeurs à afficher
     * @param array $onError Les erreurs le cas échéant
     * @param string $type Le type facture ou avoir
     * @return string Le code HTML
     */
    static function blockAddExpress1($val = array(), $onError = array(), $type = 'Facture') {
	$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$sqlConn->makeRequeteFree("select id_pays, nom_pays from ref_pays;");
	$temp = $sqlConn->process2();
	$temp=$temp[1];
	foreach($temp as $k => $v)
	    $countryList[$v['id_pays']] = $v['nom_pays'];
	$entERR	= (in_array('nomdelivery_fact',$onError)) ? '<span class="iFormErr"/>' : '';
	$add1ERR	= (in_array('adressedelivery_fact',$onError)) ? '<span class="iFormErr"/>' : '';
	$cpERR	= (in_array('cpdelivery_fact',$onError)) ? '<span class="iFormErr"/>' : '';
	$villeERR	= (in_array('villedelivery_fact',$onError)) ? '<span class="iFormErr"/>' : '';
	$mailERR	= (in_array('maildelivery_fact',$onError)) ? '<span class="iFormErr"/>' : '';
	$nb_prodERR	= (in_array('nb_prod',$onError)) ? '<span class="iFormErr"/>' : '';
	$ent = HtmlFormIphone::InputLabelWnoku('nomdelivery_fact', $val['nomdelivery_fact'], 'Entreprise : ', 'id="entrepriseFactureExpress" onkeyup="modifEntrepriseFactureExpress(this.value);"');
	$cont = HtmlFormIphone::InputLabelWnoku('contact_fact', $val['contact_fact'], 'Contact : ', 'id="contactFactureExpress" onkeyup="doModifContactDevisExpress(\'Facture\');"');
	$add1 = HtmlFormIphone::InputLabel('adressedelivery_fact', $val['adressedelivery_fact'], 'Adresse : ', 'id="add1FactureExpress" ');
	$add2 = HtmlFormIphone::InputLabel('adresse1delivery_fact', $val['adresse1delivery_fact'], 'Complément : ', 'id="add2FactureExpress"');
	$cp = HtmlFormIphone::InputLabel('cpdelivery_fact', $val['cpdelivery_fact'], 'CP : ', 'id="cpFactureExpress"');
	$ville = HtmlFormIphone::InputLabel('villedelivery_fact', $val['villedelivery_fact'], 'Ville : ', 'id="villeFactureExpress"');
	$pays = HtmlFormIphone::Select('paysdelivery_fact',$countryList,$val['paysdelivery_fact'],false, 'id="paysFactureExpress"');
	$mail = HtmlFormIphone::InputLabel('maildelivery_fact', $val['maildelivery_fact'], 'Mail : ', 'id="mailFactureExpress"');
	$prenom = HtmlFormIphone::Input('prenom_cont', $val['prenom_cont'], 'Prénom : ');
	$telcont = HtmlFormIphone::Input('tel_cont', $val['tel_cont'], 'Tél : ');
	$telent = HtmlFormIphone::Input('tel_ent', $val['tel_ent'], 'Tél : ');
	$list = array('0' => '0 %', '5.5' => '5,5 %', '19.6' => '19,6 %');
	$civList 	= $GLOBALS['CIV_'.$_SESSION["language"]];
	$civ 	= HtmlFormIphone::Select('civ_cont',$civList,$val['civ_cont'],false);
	$tvadefault = ($val['tva_fact'] != NULL) ? $val['tva_fact'] : '19.6';
	$tva = HtmlFormIphone::SelectLabel('tva_fact', $list, $tvadefault,'Tx TVA :', false);
	$out = '<fieldset>';
	$out .= '<ul><li>'.$ent.$entERR.'</li>';
	$out .= '<li id="telEntrepriseFJS" style="display:none">'.$telent.'</li>';
	$out .='<li class="proposition_entreprise" id="propositionEntrepriseFJS" style="display:none" onclick="addEntrepriseAuto(\'propositionEntrepriseFJS\', \'Facture\');"></li>';
	$out .='<li class="proposition_entreprise" id="propositionEntrepriseF2JS" style="display:none" onclick="addEntrepriseAuto(\'propositionEntrepriseF2JS\', \'Facture\');"></li>';
	$out .='<li class="proposition_entreprise" id="propositionEntrepriseF3JS" style="display:none" onclick="addEntrepriseAuto(\'propositionEntrepriseF3JS\', \'Facture\');"></li>';
	$out .='<li class="proposition_entreprise" id="propositionEntrepriseF4JS" style="display:none" onclick="addEntrepriseAuto(\'propositionEntrepriseF4JS\', \'Facture\');"></li>';
	$out .='<li class="proposition_entreprise" id="propositionEntrepriseF5JS" style="display:none" onclick="addEntrepriseAuto(\'propositionEntrepriseF5JS\', \'Facture\');"></li>';
	$out .= '<li>'.$cont.'</li>';
	$out .='<li class="proposition_contact" id="propositionContactFJS" style="display:none" onclick="addContactAuto(\'\', \'Facture\');"></li>';
	$out .='<li class="proposition_contact" id="propositionContactF2JS" style="display:none" onclick="addContactAuto(\'2\', \'Facture\');"></li>';
	$out .='<li class="proposition_contact" id="propositionContactF3JS" style="display:none" onclick="addContactAuto(\'3\', \'Facture\');"></li>';
	$out .='<li class="proposition_contact" id="propositionContactF4JS" style="display:none" onclick="addContactAuto(\'4\', \'Facture\');"></li>';
	$out .='<li class="proposition_contact" id="propositionContactF5JS" style="display:none" onclick="addContactAuto(\'5\', \'Facture\');"></li>';
	$out .='<li id="idcontExpressF" style="display:none">&nbsp;</li>';
	$out .='<li id="idcontExpressF2" style="display:none">&nbsp;</li>';
	$out .='<li id="idcontExpressF3" style="display:none">&nbsp;</li>';
	$out .='<li id="idcontExpressF4" style="display:none">&nbsp;</li>';
	$out .='<li id="idcontExpressF5" style="display:none">&nbsp;</li>';
	$out .= '<li id="prenomContactFJS" style="display:none">'.$prenom.'</li>';
	$out .= '<li id="civContactFJS" style="display:none">'.$civ.'</li>';
	$out .= '<li id="telContactFJS" style="display:none">'.$telcont.'</li>';
	$out .= '<li>'.$mail.$mailERR.'</li></ul>';
	$out .= '<ul><li>'.$add1.$add1ERR.'</li>';
	$out .= '<li>'.$add2.'</li>';
	$out .= '<li>'.$cp.$cpERR.'</li>';
	$out .= '<li>'.$ville.$villeERR.'</li>';
	$out .= '<li>'.$pays.'</li></ul>';
	$out .= '<input type="hidden" name="listeContact" id="id_contFactureExpress" value="" />';
	$out .= '<input type="hidden" name="entreprise_fact" id="id_entFactureExpress" value="'.$val['entreprise_fact'].'" />';
	$out .= '<ul><li>'.$tva.'</li></ul>';
	$out .= '</fieldset>';
	$out .= '<fieldset>
					<a href="#" class="BigButtonValidLeft" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler" /></a>
					<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formAddFactureExpress\', null, event)"><img src="Img/big.valider.png" alt="Valider" /></a>
				</fieldset>';
	return $out;
    }

    /**
     *Fonction qui affiche la seconde aprtie de l'ajout express
     * @param array $val Les valeurs à afficher
     * @param array $onError Les erreurs le cas échéant
     * @param string $errorMess Le message d'erreur le cas échéant
     * @param string $type Le type facture ou avoir
     * @return string Le code HTML
     */
    static function addExpressSuite($val = array(), $onError = array(),$errorMess = '', $type = 'Facture') {
	$nombre = 1;
	$error   = ($errorMess != '') ? '<div class="err">'.$errorMess.'</div>' : '';
	$out = '<a href="#"  onclick="return WA.Submit(\'formFactureExpressProduit\',null,event)" rel="action" class="iButton iBAction"><img src="Img/save.png" alt="Enregistrer" /></a>
				<form id="formFactureExpressProduit" action="Facture.php?action=doAddFactureExpress" onsubmit="return WA.Submit(this,null,event)">
				'.$error.'<div class="iPanel" id="FactureExpressForm">';
	$out .= '<div id="DEProdFacture">';
	while($nombre <= $_SESSION['factureExpress']['nb_prod']) {
	    $out.='<fieldset><legend>Produit '.$nombre.'</legend><ul>';
	    $id = devisView::inputAjaxProduit('id_produitFactureExpress'.$nombre, $val['id_produitFactureExpress'.$nombre],'Référence : ',true, 'express', 'DE');
	    $qtte = HtmlFormIphone::InputLabel('quantite'.$nombre, $val['quantite'.$nombre], 'Quantité : ', 'id="id_produitFactureExpress'.$nombre.'quantite", onchange="quantiteOnFactureExpress(this.value,'.$_SESSION['devisExpress']['nb_prod'].','.$nombre.',\'19.6\');"');
	    $remise = HtmlFormIphone::InputLabel('remise'.$nombre, $val['remise'.$nombre], 'Remise : ', 'id="id_produitFactureExpress'.$nombre.'remise", onchange="remiseOnFactureExpress(this.value,'.$_SESSION['devisExpress']['nb_prod'].','.$nombre.',\'19.6\');"');
	    $prix = HtmlFormIphone::InputLabel('prix'.$nombre,$val['prix'.$nombre], 'Px unit. : ', 'id="id_produitFactureExpress'.$nombre.'prix", onchange="prixOnFactureExpress(this.value,'.$_SESSION['devisExpress']['nb_prod'].','.$nombre.',\'19.6\');"');
	    $desc = HtmlFormIphone::TextareaLabel('desc'.$nombre, $val['desc'.$nombre],' id="id_produitFactureExpress'.$nombre.'desc" ', 'Libellé : ');
	    $qttERR	= (in_array('quantite'.$nombre,$onError)) ? '<span class="iFormErr"/>' : '';
	    $remiseERR	= (in_array('remise'.$nombre,$onError)) ? '<span class="iFormErr"/>' : '';
	    $prixERR	= (in_array('prix'.$nombre,$onError)) ? '<span class="iFormErr"/>' : '';
	    $out .='<li>'.$id.'</li>';
	    $out .='<li>'.$desc.'</li>';
	    $out .='<li>'.$qtte.$qttERR.'</li>';
	    $out .='<li>'.$remise.$remiseERR.'</li>';
	    $out .='<li>'.$prix.$prixERR.'</li>';
	    $out .='<li>Ss total : <div style="display:inline" id="sstotalid_produitFactureExpress'.$nombre.'">0</div> €</li>';
	    $out .='</ul></fieldset>';
	    $nombre++;
	}
	$out .= '</div>';
	$out .='<fieldset><legend>Total</legend><ul>';
	$out .='<li id="htFactureExpress">Total HT : </li>';
	$out .='<li id="tvaFactureExpress">TVA : </li>';
	$out .='<li id="ttcFactureExpress">Total TTC : </li>';
	$out .='</ul></fieldset>';
	$out .='<fieldset>
						<a class="BigButtonValidLeft" href="#" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler"></a>
						<a class="BigButtonValidRight" href="#" onclick="return WA.Submit(\'formFactureExpressProduit\',null,event)"><img src="Img/big.valider.png" alt="Valider"></a>
					</fieldset>
				</div>
				</form>';
	return $out;
    }

    /**
     *Fonction qui permet de retourner le formulaire de visualisation du document
     * @param array $value Les valeurs à afficher
     * @param string $type Le type facture ou avoir
     * @return string Le code HTML
     */
    static function actionVoir($value = array(), $type = 'Facture') {
	foreach ($GLOBALS['ZunoFacture'] as $key => $val) {
	    $k = explode('.',$key,2);
	    if ($k[0] == 'cannevas')
		$toto[$key] = $key;
	}

	$availableConvFormat = OOConverterAvailable('document');
	$value['OutputExt'] = ($value['OutputExt'] != '') ? $value['OutputExt'] : 'pdf';

	$cannevas  = HtmlFormIphone::SelectLabel('Cannevas',$toto,$value['Cannevas'],'Cannevas : ',false);
	$extention = HtmlFormIphone::SelectLabel('OutputExt',$availableConvFormat,$value['OutputExt'],'Format : ',false);

	return '<a href="#_MainMenu" rel="action" class="iButton iBAction"><img src="Img/home.png" alt="Accueil" /></a>
				<form id="formFactureDoVoir" action="Facture.php?action=doVoir&id_fact='.$value["id_fact"].'" onsubmit="return WA.Submit(this,null,event)">
				<div class="iPanel">
				<fieldset>
					<legend>Options du document</legend>
					<ul>
						<li>'.$cannevas.'</li>
						<li>'.$extention.'</li>
					<input type="hidden" name="type" value="'.$type.'" />
					</ul>
				</fieldset>
				<div id="formFactureDoVoirResponse"></div>
				<fieldset>
					<a href="#" class="BigButtonValidLeft" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler"></a>
					<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formFactureDoVoir\',null,event)"><img src="Img/big.valider.png" alt="Valider"></a>
				</fieldset>
			</div>';
    }

    /**
     *Fonction qui affiche le formulaire d'enregistreement des documents pdf
     * @param array $value Les valeurs à afficher
     * @param string $type Le type fatcure ou avoir
     * @return string Le code HTML
     */
    static function actionRecord($value = array(), $type = 'Facture') {
	foreach ($GLOBALS['ZunoFacture'] as $key => $val) {
	    $k = explode('.',$key,2);
	    if ($k[0] == 'cannevas')
		$toto[$key] = $key;
	}
	$availableConvFormat = OOConverterAvailable('document');
	$value['OutputExt'] = ($value['OutputExt'] != '') ? $value['OutputExt'] : 'pdf';

	$cannevas  = HtmlFormIphone::SelectLabel('Cannevas',$toto,$value['Cannevas'],'Cannevas :',false);
	$extention = HtmlFormIphone::SelectLabel('OutputExt',$availableConvFormat,$value['OutputExt'],'Format :',false);

	$value['message'] = ($value['message'] != '') ? $value['message'] : 'Ajout du document '.$value["doc_fact"];
	$mess = HtmlFormIphone::TextareaLabel('message',$value['message'],'','Message :');

	return '<a href="#_MainMenu" rel="action" class="iButton iBAction"><img src="Img/home.png" alt="Accueil" /></a>
				<form id="formFactureDoRec" action="Facture.php?action=doRec&id_fact='.$value["id_fact"].'" onsubmit="return WA.Submit(this,null,event)">
				<div class="iPanel">
				<fieldset>
					<legend>Options du document</legend>
					<p><i>Le message sera utilisé comme message d\'enregistrement lors de l\'ajout du document dans l\'entrepôt ZunoGed.</i></p>
					<ul>
						<li>'.$cannevas.'</li>
						<li>'.$extention.'</li>
						<li>'.$mess.'</li>
					</ul><input type="hidden" name="type" value="'.$type.'" />
				</fieldset>
				<fieldset>
					<a href="#" class="BigButtonValidLeft" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler"></a>
					<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formFactureDoRec\',null,event)"><img src="Img/big.valider.png" alt="Valider"></a>
				</fieldset>
			</div>';
    }

    /**
     *Fonction qui affiche le formulaire d'envoi de document
     * @param array $value Les valeurs à afficher
     * @param string $type Le type facture ou avoir
     * @return string Le code html
     */
    static function actionSend($value = array(), $type = 'Facture') {
	foreach ($GLOBALS['ZunoFacture'] as $key => $val) {
	    $k = explode('.',$key,2);
	    if ($k[0] == 'cannevas' and $k[1] != 'exportTableur')
		$toto[$key] = $key;
	}
	$availableConvFormat = OOConverterAvailable('document');
	$value['OutputExt'] = ($value['OutputExt'] != '') ? $value['OutputExt'] : 'pdf';

	$cannevas  = HtmlFormIphone::SelectLabel('Cannevas',$toto,$value['Cannevas'],'Cannevas :',false);
	$extention = HtmlFormIphone::SelectLabel('OutputExt',$availableConvFormat,$value['OutputExt'],'Format :',false);
	$typeEnvoi		= HtmlFormIphone::SelectLabel('typeEnvoi',array('email'=>'E-mail','courrier'=>'Courrier','fax'=>'Fax'),$value['type'],'Type :',false);

	return '<a href="#_MainMenu" rel="action" class="iButton iBAction"><img src="Img/home.png" alt="Accueil" /></a>
				<form id="formFactureDoSend" action="Facture.php?action=send1&id_fact='.$value["id_fact"].'" onsubmit="return WA.Submit(this,null,event)">
				<div class="iPanel">
				<fieldset>
					<legend>Options du document</legend>
					<ul>
						<li>'.$cannevas.'</li>
						<li>'.$extention.'</li>
					</ul><input type="hidden" name="type" value="'.$type.'" />
				</fieldset>
				<fieldset>
					<legend>Options d\'envoi</legend>
					<ul>
						<li>'.$typeEnvoi.'</li>
					</ul>
				</fieldset>
				<fieldset>
					<a href="#" class="BigButtonValidLeft" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler"></a>
					<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formFactureDoSend\',null,event)"><img src="Img/big.valider.png" alt="Valider"></a>
				</fieldset>
			</div>';
    }

    /**
     *Fonction qui donne le form final de l'envoi de doc
     * @param array $value Les valeurs à afficher
     * @param array $onError Les erreurs le cas échéant
     * @param string $errorMess Le mesage d'erreur le cas échéant
     * @param string $type Le type facture ou avoir
     * @return string Le code HTML
     */
    static function actionSend1($value = array(), $onError = array(),$errorMess = '', $type = 'Facture') {
	if($value['type'] == 'courrier')
	    $form = sendView::innerFormSendCourrier($value,$onError,$errorMess);
	elseif($value['type'] == 'fax')
	    $form = sendView::innerFormSendFax($value,$onError,$errorMess);
	else  $form = sendView::innerFormSendEmail($value,$onError,$errorMess);
	$form.= HtmlFormIphone::Input('type',$value['type'],'','','hidden');

	return '<a href="#_MainMenu" rel="action" class="iButton iBAction"><img src="Img/home.png" alt="Accueil" /></a>
				<form id="formFactureDoSend1" action="Facture.php?action=doSend&id_fact='.$value["id_fact"].'" onsubmit="return WA.Submit(this,null,event)">
				<div class="iPanel">
				'.$form.'<input type="hidden" name="type" value="'.$type.'" />
				<fieldset>
					<a href="#" class="BigButtonValidLeft" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler"></a>
					<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formFactureDoSend1\',null,event)"><img src="Img/big.valider.png" alt="Valider"></a>
				</fieldset>
			</div>';
    }

    /**
     *Fonction qui affiche le formulaire d'enregistrement et d'envoi
     * @param array $value Les valeurs à afficher
     * @param string $type Le tyupe facture ou avoir
     * @return string Le code HTML
     */
    static function actionRecordSend($value = array(), $type = 'Facture') {
	foreach ($GLOBALS['ZunoFacture'] as $key => $val) {
	    $k = explode('.',$key,2);
	    if ($k[0] == 'cannevas' and $k[1] != 'exportTableur')
		$toto[$key] = $key;
	}
	$availableConvFormat = OOConverterAvailable('document');
	$value['OutputExt'] = ($value['OutputExt'] != '') ? $value['OutputExt'] : 'pdf';

	$cannevas  = HtmlFormIphone::SelectLabel('Cannevas',$toto,$value['Cannevas'],'Cannevas :',false);
	$extention = HtmlFormIphone::SelectLabel('OutputExt',$availableConvFormat,$value['OutputExt'],'Format :',false);
	$typeEnvoi		= HtmlFormIphone::SelectLabel('typeEnvoi',array('email'=>'E-mail','courrier'=>'Courrier','fax'=>'Fax'),$value['type'],'Type :',false);

	$value['message'] = ($value['message'] != '') ? $value['message'] : 'Ajout du document '.$value["doc_fact"];
	$mess = HtmlFormIphone::TextareaLabel('message',$value['message'],'','Message :');

	return '<a href="#_MainMenu" rel="action" class="iButton iBAction"><img src="Img/home.png" alt="Accueil" /></a>
				<form id="formFactureDoRec" action="Facture.php?action=recsend1&id_fact='.$value["id_fact"].'" onsubmit="return WA.Submit(this,null,event)">
				<div class="iPanel">
				<fieldset>
					<legend>Options du document</legend>
					<p><i>Le message sera utilisé comme message d\'enregistrement lors de l\'ajout du document dans l\'entrepôt ZunoGed.</i></p>
					<ul>
						<li>'.$cannevas.'</li>
						<li>'.$extention.'</li>
						<li>'.$mess.'</li>
					</ul><input type="hidden" name="type" value="'.$type.'" />
				</fieldset>
				<fieldset>
					<legend>Options d\'envoi</legend>
					<ul>
						<li>'.$typeEnvoi.'</li>
					</ul>
				</fieldset>
				<fieldset>
					<a href="#" class="BigButtonValidLeft" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler"></a>
					<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formFactureDoRec\',null,event)"><img src="Img/big.valider.png" alt="Valider"></a>
				</fieldset>
			</div>';
    }

    /**
     *Fonction qui insert le formulaire d'envoi après enregistrement
     * @param array $value Les valeurs à afficher
     * @param array $onError Les erreurs le cas échéant
     * @param string $errorMess Le message d'erreur le cas échéant
     * @param string $type Le type facture ou avoir
     * @return string Le code HTML
     */
    static function actionRecordSend1($value = array(), $onError = array(),$errorMess = '', $type = 'Facture') {
	if($value['type'] == 'courrier')
	    $form = sendView::innerFormSendCourrier($value,$onError,$errorMess);
	elseif($value['type'] == 'fax')
	    $form = sendView::innerFormSendFax($value,$onError,$errorMess);
	else  $form = sendView::innerFormSendEmail($value,$onError,$errorMess);
	$form.= HtmlFormIphone::Input('type',$value['type'],'','','hidden');

	return '<a href="#_MainMenu" rel="action" class="iButton iBAction"><img src="Img/home.png" alt="Accueil" /></a>
				<form id="formFactureDoRec1" action="Facture.php?action=doRecsend&id_fact='.$value["id_fact"].'" onsubmit="return WA.Submit(this,null,event)">
				<div class="iPanel">
				'.$form.'<input type="hidden" name="type" value="'.$type.'" />
				<fieldset>
					<a href="#" class="BigButtonValidLeft" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler"></a>
					<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formFactureDoRec1\',null,event)"><img src="Img/big.valider.png" alt="Valider"></a>
				</fieldset>
			</div>';
    }

    /**
     *Fonction qui affiche un tri par montant des factures
     * @param array $value le svaleurs à afficher
     * @param int $limit L'index final
     * @param int $from L'index initial
     * @param int $total Le nombre total de résultat
     * @return string Le code HTML
     */
    static function tri_montant($value = array(), $limit = 20, $from = 0, $total = 0) {
	$out = '<ul>';
	$prix = $_SESSION['user']['LastLetterSearch'];
	$valeurs = getStats('facture');
	foreach($value[1] as $k => $v) {
	    $sortie = triMontant($v['sommeHT_fact'], $prix, $valeurs);
	    $prix = $sortie[1];
	    $out .= $sortie[0];
	    $out .= '<li><a href="Facture.php?action=view&id_fact='.$v['id_fact'].'"rev="async">'.$v['id_fact'].' - '.$v['titre_fact'].' <small>Montant : <b>'.formatCurencyDisplay($v['sommeHT_fact']).'</b></small></a></li>';
	}
	if($total > ($limit+$from))
	    $out .= '<li class="iMore" id="triMontantFactureMore'.$from.'"><a href="Facture.php?action=triMontantMore&from='.($limit+$from).'&total='.$total.'" rev="async">Plus de résultats</a></li>';
	$_SESSION['user']['LastLetterSearch'] = $prix;
	$out .= '</ul>';
	return $out;
    }

    /**
     *Fonction qui affiche un tri par date de création des factures
     * @param array $value le svaleurs à afficher
     * @param int $limit L'index final
     * @param int $from L'index initial
     * @param int $total Le nombre total de résultat
     * @return string Le code HTML
     */
    static function tri_creation($value = array(), $limit = 20, $from = 0, $total = 0) {
	$mois = $_SESSION['user']['LastLetterSearch'];
	$out = '<ul>';
	foreach($value[1] as $k => $v) {
	    if($v['daterecord_fact'] != NULL) {
		if($mois != ucfirst(strftime("%B %G", strtotime($v['daterecord_fact'])))) {
		    $mois = ucfirst(strftime("%B %G", strtotime($v['daterecord_fact'])));
		    $out .= '</ul><h2>'.$mois.'</h2><ul class="iArrow">';
		}
		$echeance=strftime("%d/%m/%G", strtotime($v['daterecord_fact']));
		$out .= '<li><a href="Facture.php?action=view&id_fact='.$v['id_fact'].'" rev="async">'.$v['id_fact'].' - '.$v['titre_fact'].' <small>Création le : <b>'.$echeance.'</b></small></a></li>';
	    }
	}
	if($total > ($limit+$from))
	    $out .= '<li class="iMore" id="triCreationFactureMore'.$from.'"><a href="Facture.php?action=triCreationMore&from='.($limit+$from).'&total='.$total.'" rev="async">Plus de résultats</a></li>';
	$out .= '</ul>';
	$_SESSION['user']['LastLetterSearch'] = $mois;
	return $out;
    }

    /**
     *Fonction qui affiche un tri par entreprise cliente des factures
     * @param array $value le svaleurs à afficher
     * @param int $limit L'index final
     * @param int $from L'index initial
     * @param int $total Le nombre total de résultat
     * @return string Le code HTML
     */
    static function tri_entreprise($value = array(), $limit = 20, $from = 0, $total = 0) {
	$ent = $_SESSION['user']['LastLetterSearch'];
	$out = '<ul>';
	foreach($value[1] as $k => $v) {
	    if($v['nom_ent'] != NULL) {
		if($ent != strtoupper($v['nom_ent']{0})) {
		    $ent=strtoupper($v['nom_ent']{0});
		    $out .= '</ul><h2>'.$ent.'</h2><ul class="iArrow">';
		}
		$entreprise=$v['nom_ent'];
		$out .= '<li><a href="Facture.php?action=view&id_fact='.$v['id_fact'].'" rev="async">'.$v['id_fact'].' - '.$v['titre_fact'].' <small><b>'.$entreprise.'</b></small></a></li>';
	    }
	    elseif($v['nom_ent'] == NULL) {
		if($ent != 'Sans entreprise') {
		    $ent='Sans entreprise';
		    $out .= '</ul><h2>'.$ent.'</h2><ul class="iArrow">';
		}
		$out .= '<li><a href="Facture.php?action=view&id_fact='.$v['id_fact'].'" rev="async">'.$v['id_fact'].' - '.$v['titre_fact'].' <small><b>Aucune entreprise liée</b></small></a></li>';
	    }
	}

	if($total > ($limit+$from))
	    $out .= '<li class="iMore" id="triEntrepriseFactureMore'.$from.'"><a href="Facture.php?action=triEntrepriseMore&from='.($limit+$from).'&total='.$total.'" rev="async">Plus de résultats</a></li>';
	$out .= '</ul>';
	$_SESSION['user']['LastLetterSearch'] = $ent;
	return $out;
    }

    /**
     *Fonction qui affiche un tri par contact des factures
     * @param array $value le svaleurs à afficher
     * @param int $limit L'index final
     * @param int $from L'index initial
     * @param int $total Le nombre total de résultat
     * @return string Le code HTML
     */
    static function tri_contact($value = array(), $limit = 20, $from = 0, $total = 0) {
	$ent = $_SESSION['user']['LastLetterSearch'];
	$out = '<ul>';
	foreach($value[1] as $k => $v) {
	    if($v['nom_cont'] != NULL) {
		if($ent != strtoupper($v['nom_cont']{0})) {
		    $ent=strtoupper($v['nom_cont']{0});
		    $out .= '</ul><h2>'.$ent.'</h2><ul class="iArrow">';
		}
		$entreprise=$v['nom_cont'];
		$out .= '<li><a href="Facture.php?action=view&id_fact='.$v['id_fact'].'" rev="async">'.$v['id_fact'].' - '.$v['titre_fact'].' <small><b>'.$entreprise.'</b></small></a></li>';
	    }
	    elseif($v['nom_cont'] == NULL) {
		if($ent != 'Sans contact') {
		    $ent='Sans contact';
		    $out .= '</ul><h2>'.$ent.'</h2><ul class="iArrow">';
		}
		$out .= '<li><a href="Facture.php?action=view&id_fact='.$v['id_fact'].'" rev="async">'.$v['id_fact'].' - '.$v['titre_fact'].' <small><b>Aucun contact lié</b></small></a></li>';
	    }
	}
	if($total > ($limit+$from))
	    $out .= '<li class="iMore" id="triContactFactureMore'.$from.'"><a href="Facture.php?action=triContactMore&from='.($limit+$from).'&total='.$total.'" rev="async">Plus de résultats</a></li>';
	$out .= '</ul>';
	$_SESSION['user']['LastLetterSearch'] = $ent;
	return $out;
    }

    /**
     *Fonction d'affichage des stats
     * @param array $facture Les factures
     * @param array $avoir Les avoirs
     * @return string Le code HTML
     */
    static function afficherStats($facture = array(), $avoir = array()) {
	$out = '<div class="iPanel">';
	$out .= '<fieldset><legend>Factures</legend><ul><li>Nombre : '.$facture[0].'</li>';
	$out .= '<li>Valeur totale : '.$facture[5].' &euro;</li>';
	$out .= '<li>Prix moyen : '.$facture[1].' €</li>';
	$out .= '<li>Prix médian : '.$facture[2].' €</li>';
	$out .= '<li>Variance : '.$facture[3].'</li>';
	$out .= '<li>Écart type : '.$facture[4].' €</li>';
	$out .= '<li>Coefficient de variation : '.$facture[6].'</li></ul></fieldset>';
	$out .= '<fieldset><legend>Avoirs</legend><ul><li>Nombre : '.$avoir[0].'</li>';
	$out .= '<li>Valeur totale : '.$avoir[5].' &euro;</li>';
	$out .= '<li>Prix moyen : '.$avoir[1].' €</li>';
	$out .= '<li>Prix médian : '.$avoir[2].' €</li>';
	$out .= '<li>Variance : '.$avoir[3].'</li>';
	$out .= '<li>Écart type : '.$avoir[4].' €</li>';
	$out .= '<li>Coefficient de variation : '.$avoir[6].'</li></ul></fieldset></div>';
	return $out;
    }
}
?>
