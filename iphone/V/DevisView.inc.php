<?php

/**
 * Classe qui va générer tous les affichage en rapport avec les devis.
 */
class devisView {

    /**
     * Génération de la liste des résultats de la recherche
     */
    static function searchResult($result, $from = 0, $limit = 5, $total = 0, $qTag = '') {
	if (is_array($result) and count($result) > 0) {
	    $letter = $_SESSION['user']['LastLetterSearch'];
	    $annee = $_SESSION['user']['annee'];
	    foreach ($result as $k => $v) {
		//On se balade dans le tableau de résultat de la recherche pour générer la liste.
		$tempmois = (substr($v['id_dev'], 2, 2) < 50) ? substr($v['id_dev'], 2, 2) : substr($v['id_dev'], 2, 2) - 50;
		$tempannee = substr($v['id_dev'], 0, 2);
		if ($letter != $tempmois || $annee != $tempannee) {
		    $annee = $tempannee;
		    $letter = $tempmois;
		    $list .= '</ul><h2>' . ucfirst(strftime("%B", strtotime('2008-' . $letter . '-27'))) . ' 20' . substr($v['id_dev'], 0, 2) . '</h2><ul class="iArrow">';
		} elseif ($from != 0) {
		    $list .= '</ul><ul class="iArrow">';
		}
		$brc = ($v['nom_cont'] != '') ? '<br/>' : '';
		//On génère la liste ici :
		$list .= '<li><a href="Devis.php?action=view&id_dev=' . $v['id_dev'] . '" rev="async"><em>' . $v['id_dev'] . ' - ' . $v['titre_dev'] . ' </em><small><b>' . $v['nom_ent'] . '</b>' . $brc . $v['civ_cont'] . ' ' . $v['nom_cont'] . ' ' . $v['prenom_cont'] . '</small></a></li>';
	    }
	    $list = substr($list, 5) . '</ul>';
	    if ($from == 0)
		$out = '<a href="#_MainMenu"  rel="action" class="iButton iBAction"><img src="Img/home.png" alt="Accueil" /></a>';
	    //l'affichage de la liste générée
	    $out .='<div class="iList">' . $list . '</div>';
	    if ($total > ($limit + $from))
		$out .= '<div class="iMore" id="searchResultDevisMore' . $from . '"><a href="Devis.php?action=searchDevisContinue&from=' . ($limit + $from) . '&total=' . $total . '" rev="async">Plus de résultats</a></div>';

	    $_SESSION['user']['LastLetterSearch'] = $letter;
	    $_SESSION['user']['annee'] = $annee;
	    return $out;
	}
    }

    /**
     * Formulaire complet de visualisation d'un devis.
     */
    static function view($value = array(), $mode = '') {
	$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$sqlConn->makeRequeteFree("select id_cmd, titre_cmd from commande LEFT JOIN devis ON devis.id_dev = commande.devis_cmd where id_dev = '" . $value['id_dev'] . "' ORDER BY id_cmd ASC;");
	$temp = $sqlConn->process2();
	$temp = $temp[1];
	if ($temp == array())
	    $commande = '';
	else
	    foreach ($temp as $v)
		$commande .= '<li><a href="Commande.php?action=view&id_cmd=' . $v['id_cmd'] . '" class="Commande" rev="async"><img src="../img/actualite/commande.png"/> Commande : ' . $v['id_cmd'] . ' [' . $v['titre_cmd'] . ']</a></li>';
	$sqlConn->makeRequeteFree("select id_fact, titre_fact from facture LEFT JOIN commande ON commande.id_cmd = facture.commande_fact where commande_fact LIKE '%" . $value['id_dev'] . "%' ORDER BY id_fact ASC;");
	$temp = $sqlConn->process2();
	$temp = $temp[1];
	if ($temp == array())
	    $facture = '';
	else
	    foreach ($temp as $v)
		$facture .= '<li><a href="Facture.php?action=view&id_fact=' . $v['id_fact'] . '" class="Facture" rev="async"><img src="../img/actualite/facture.png"/> Facture : ' . $v['id_fact'] . ' [' . $v['titre_fact'] . ']</a></li>';

	$txTva = ($value['tva_dev'] / 100 + 1);
	$_SESSION['tva'] = $value['tva_dev'];
	$creation = '';
	$tva = '<li>T.V.A : <small>' . formatCurencyDisplay($_SESSION['tva'], 1, '%') . ' (' . formatCurencyDisplay(($value['sommeHT_dev'] * $_SESSION['tva'] / 100)) . ')</small></li>';
	if ($value['daterecord_dev'] != NULL)
	    $creation = strftime("%A %d %B %G", strtotime($value['daterecord_dev']));
	$somme = ($value['sommeHT_dev'] != NULL) ? '<li>Total HT : <small>' . formatCurencyDisplay($value['sommeHT_dev']) . '</small></li>' : '';
	$sommeTTC = ($value['sommeHT_dev'] != NULL) ? '<li>Total TTC : <small>' . formatCurencyDisplay(($value['sommeHT_dev'] * $txTva)) . '</small></li>' : '';
	$entreprise = ($value['entreprise_dev'] != NULL) ? '<li>' . contactEntrepriseView::contactLinkSimple($value) . '</li>' : '';
	$contact = ($value['contact_dev'] != NULL) ? '<li>' . contactParticulierView::contactLinkSimple($value) . '</li>' : '';
	$contactachat = ($value['contact_achat_dev'] != NULL && $value['contact_dev'] != $value['contact_achat_dev']) ? '<li>' . contactParticulierView::contactLinkSimple($value, 'achat') . '</li>' : '';
	$commercial = ($value['commercial_dev'] != NULL) ? '<li>Commercial : ' . $value['prenom'] . ' ' . $value['nom'] . '</li>' : '';
	$affaire = ($value['affaire_dev'] != NULL) ? '<li>' . affaireView::affaireLinkSimple($value) . '</li>' : '';
	$titre = ($value['titre_dev'] != NULL) ? '<strong>' . $value['titre_dev'] . '</strong>' : '<i>Aucun titre de devis</i>';
	//On vient d'effectuer tout un tas de tests pour s'assurer  que l'on ne vas afficher que des blocks avec des choses dedans.

	if ($mode == 'afterModif')
	    $linkHead = '<a href="Devis.php?action=view&id_dev=' . $value['id_dev'] . '"  rel="action" class="iButton iBAction"><img src="Img/config.png" alt="Recharger" /></a>';
	else
	    $linkHead = '<a href="Devis.php?action=modifDevis&id_dev=' . $value["id_dev"] . '"  rev="async" rel="action" class="iButton iBAction"><img src="Img/edit.png" alt="Modifier" /></a>';

	//On génère maintenant le rendu visuel.
	$out = $linkHead . '<div class="iPanel">
<fieldset>
    <legend></legend>
    <ul>
	<li>' . $titre . '</li>' . $affaire . '
    </ul>
    </fieldset>
    <fieldset>
    <legend>Contacts</legend>
    <ul>
	' . $entreprise . '
	' . $contact . '
	' . $contactachat . '
    </ul>
</fieldset>
<fieldset><legend>Offre commerciale</legend>
    <ul class="iArrow">
	' . $somme . '
	' . $tva . '
	' . $sommeTTC . '
	<li><a href="Devis.php?action=produits&id_dev=' . $value["id_dev"] . '" rev="async">Détail de l\'offre</a></li>
    </ul>
</fieldset>
<fieldset>
    <legend>Ressources Liées</legend>
    <ul class="iArrow">
	    ' . self::subBlockRessourcesLiees($value, $commande, $facture) . '
    </ul>
</fieldset>
<fieldset>
    <legend>Autres informations</legend>
    <ul>
	<li>Devis créé le : <small>' . $creation . '</small></li>
	<li>Statut : <small>' . $value['nom_stdev'] . '</small></li>
	' . $commercial . '
    </ul>
</fieldset>' . self::subBlockAction($value) . '
</div>';
	return $out;
    }

    /**
     * Fonction qui va géré l'affichage des actualités s'il y en a.
     */
    static function subBlockRessourcesLiees($value = array(), $commande = '', $facture = '') {
	$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$sqlConn->makeRequeteFree("select count(id) as C FROM actualite WHERE id_dev = '" . $value['id_dev'] . "'");
	$temp = $sqlConn->process2();
	$totalActu = $temp[1][0]['C'];
	$AffDir= ($value['archived_aff'] == '1') ? $GLOBALS['SVN_Pool1']['ArchivesDir'] : $GLOBALS['SVN_Pool1']['WorkDir'];
	$AffDir.= $GLOBALS['ZunoAffaire']['dir.affaire'].$value['dir_aff']; 
	$fileName= $GLOBALS['ZunoDevis']['file.suffixe'] . $value['id_dev'];
	
	if (file_exists($GLOBALS['SVN_Pool1']['WorkCopy']. $AffDir . $fileName . '.pdf'))
	    $outLi .= "<li><a href=\"inc/explorer.php?download=" .$AffDir . $fileName . ".pdf\" target=\"_blank\">" . imageTag('../img/files/pdf.png', 'version PDF') . ' ' . $fileName . ".pdf</a></li>";
	if (file_exists($GLOBALS['SVN_Pool1']['WorkCopy']. $AffDir . $fileName . '.odt'))
	    $outLi .= "<li><a href=\"inc/explorer.php?download=" .$AffDir . $fileName . ".odt\" target=\"_blank\">" . imageTag('../img/files/document.png', 'version ODT') . ' ' . $fileName . ".odt</a></li>";
	if (file_exists($GLOBALS['SVN_Pool1']['WorkCopy']. $AffDir . $fileName . '.doc'))
	    $outLi .= "<li><a href=\"inc/explorer.php?download=" .$AffDir . $fileName . ".doc\" target=\"_blank\">" . imageTag('../img/files/document.png', 'version DOC') . ' ' . $fileName . ".doc</a></li>";
	//Récupération des données
	$out = '<li><a rev="async" href="Actualite.php?action=viewDevis&amp;id_dev=' . $value['id_dev'] . '"><img src="Img/actualite.png"/> ' . $totalActu . ' Actualités</a></li> ' . $commande . $facture . $outLi;
	return $out; //Génération de l'affichage.
    }

    /**
     * Fonction qui va générer l'affichage de la liste des produits.
     */
    static function produits($value = array(), $id_dev = '', $prod = '', $mode = '') {
	$tva = $_SESSION['tva'];
	if ($mode == 'valide') {
	    $produits = '<a href="#_MainMenu"  rel="action" class="iButton iBBack"><img src="Img/home.png" alt="Accueil" /></a><div class="iPanel">';
	    $modiflignes = '';
	    $modif = '';
	} elseif ($mode == '') {
	    $produits = '<a href="Devis.php?action=addProduit&id_dev=' . $id_dev . '"  rev="async" rel="action" class="iButton iBAction"><img src="Img/add.png" alt="Ajouter" /></a><div class="iPanel">';
	    $modiflignes = '<li><a href="Devis.php?action=modifProduit&id_dev=' . $id_dev . '" rev="async">Tout modifier</a></li></ul></fieldset>';
	} else {
	    $produits = '<a href="Devis.php?action=doSuppProduit&id_prod=' . $prod . '&id_dev=' . $id_dev . '"  rev="async" rel="action" class="iButton iBAction"><img src="Img/delete.png" alt="Supprimer" /></a><div class="iPanel">';
	    $modiflignes = '<li><a href="Devis.php?action=modifProduit&id_dev=' . $id_dev . '" rev="async">Tout modifier</a></li></ul></fieldset>';
	}
	if ($value == NULL)
	    $produits .= '';
	else {
	    $TVA = ($tva != NULL) ? $tva : 0;
	    $totalHT = 0;

	    foreach ($value as $v) {
		$total = $v['prix'] * (1 - $v['remise'] / 100) * $v['quantite'];
		$totalHT += $total;
		if (round($v['quantite'], 0) != $v['quantite'])
		    $v['quantite'] = formatCurencyDisplay($v['quantite'], 2, '');
		else
		    $v['quantite'] = formatCurencyDisplay($v['quantite'], 0, '');
		if (round($v['prix'], 0) != $v['prix'])
		    $v['prix'] = formatCurencyDisplay($v['prix']);
		else
		    $v['prix'] = formatCurencyDisplay($v['prix'], 0);
		if (round($v['remise'], 0) != $v['remise'])
		    $v['remise'] = formatCurencyDisplay($v['remise'], 2, '%');
		else
		    $v['remise'] = formatCurencyDisplay($v['remise'], 0, '%');
		if (round($total, 0) != $total)
		    $total = formatCurencyDisplay($total);
		else
		    $total = formatCurencyDisplay($total, 0);
		if ($mode != 'valide')
		    $modif = '<span style="float: right"><a style="margin: 0px; margin-top: -21px" href="Devis.php?action=modifProduit&nbprod=one&id_prod=' . $v['id_produit'] . '&id_dev=' . $id_dev . '" rev="async"><img src="Img/edit-mini.png" title="Modifier"/></a></span>';
		$out.='<fieldset>' . $modif . '<legend  class="smallActionLegend"> Produit ' . $v['id_produit'] . '</legend><ul>';
		$out .='<li>' . $v['desc'] . '</li>';
		if ($v['nom_prodfam'] != '')
		    $out .='<li><label>Famille : </label>' . $v['treePathKey'] . ' ' . $v['nom_prodfam'] . '</li>';
		$out .='<li><label>Qté x P.U. : </label>' . $v['quantite'] . ' x ' . $v['prix'] . '</li>';
		if ($v['remise'] > 0)
		    $out .='<li><label>Remise : </label>' . $v['remise'] . '</li>';
		$out .='<li><label>Total : </label>' . $total . '</li>';
		$out .='</ul></fieldset>';
	    }
	    $TTC = (1 + $TVA / 100) * $totalHT;
	    $out.='<fieldset><legend>Total de l\'offre ' . $v['id_dev'] . '</legend><ul class="iArrow">';
	    $out .='<li><label>Total HT : </label>' . formatCurencyDisplay($totalHT) . '</li>';
	    $out .='<li><label>Taux TVA : </label>' . formatCurencyDisplay($TVA, 1, '%') . '</li>';
	    $out .='<li><label>Total TTC : </label>' . formatCurencyDisplay($TTC) . '</li>';
	}

	$produits .= $out . $modiflignes;
	return $produits; //Sortie du résultat avec les liens pour ajout ou modification d'un produit.
    }

    /**
     * Fonction générant le rendu visuel du formulaire de modification des produits.
     */
    static function modifProduits($value = array(), $id_dev = '') {
	$out = '<a href="#"  onclick="return WA.Submit(\'formModifProduit\',null,event)" rel="action" class="iButton iBAction"><img src="Img/save.png" alt="Enregistrer" /></a>
				<form id="formModifProduit" action="Devis.php?action=doModifProduit&id_dev=' . $id_dev . '" onsubmit="return WA.Submit(this,null,event)">
				<div class="iPanel">
					' . self::blockProduits($value, 'modif', $id_dev) . '
					<fieldset>
						<a class="BigButtonValidLeft" href="#" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler"></a>
						<a class="BigButtonValidRight" href="#" onclick="return WA.Submit(\'formModifProduit\',null,event)"><img src="Img/big.valider.png" alt="Valider"></a>
					</fieldset>
				</div>
				</form>';
	$out .= '<form id="formModifProduitDevisCache" action="Devis.php?action=suppProduit&id_dev=' . $id_dev . '" onsubmit="return WA.Submit(this,null,event)">' .
		'<div style="display:none"><input id="id_produit_hidden_devis" type="hidden" name="id_produit" value=0 />' .
		'<a id="valid_suppProduitdevis" href="#" onclick="return WA.Submit(\'formModifProduitDevisCache\',null,event)">Lien suppression caché</a>' .
		'</div></form>';
	return $out;
    }

    /**
     * Fonction générant le rendu visuel du formulaire d'ajout d'un produit.
     */
    static function addProduits($value = array(), $id_dev = '', $mess ='') {
	$erreur = ($mess == '') ? '' : '<div class="err" id="ErreurProd" >' . $mess . '</div>';
	$out = '<a href="#"  onclick="return WA.Submit(\'formAddProduit\',null,event)" rel="action" class="iButton iBAction"><img src="Img/save.png" alt="Enregistrer" /></a>
				<form id="formAddProduitDevis" action="Devis.php?action=doAddProduit&id_dev=' . $id_dev . '" onsubmit="return WA.Submit(this,null,event)">
				' . $erreur . '
				<div class="iPanel">
					' . self::blockProduits($value, 'add', $id_dev) . '
					<fieldset>
						<a class="BigButtonValidLeft" href="#" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler"></a>
						<a class="BigButtonValidRight" href="#" onclick="return WA.Submit(\'formAddProduitDevis\',null,event)"><img src="Img/big.valider.png" alt="Valider"></a>
					</fieldset>
				</div>
				</form>';
	return $out;
    }

    /**
     * Fonction BLOCK qui inclu tout ce qu'il faut pour la gestion des produits.
     */
    static function blockProduits($value = array(), $action = '', $id_dev = NULL) {
	$_SESSION['id_dev'] = $id_dev;
	$out = '';
	$nombre = 0;
	if ($action == 'modif') {
	    foreach ($value as $v) {
		$quantite = ($v['quantite'] != NULL) ? $v['quantite'] : '1';
		$rem = ($v['remise'] != NULL) ? $v['remise'] : '0';
		$nombre++;
		$out.='<fieldset><span class="smallActionButton"><a id="supprimer_produitdevis" onclick="confirmBeforeClick(\'valid_suppProduit\', \'' . $v['id_produit'] . '\', \'devis\')"><img src="Img/delete.png" title="Supprimer"/></a></span><legend  class="smallActionLegend"> Produit ' . $v['id_produit'] . '</legend><ul>';
		$id = self::inputAjaxProduit('id_produit' . $nombre, $v['id_produit'], 'Référence : ', false);
		$qtte = HtmlFormIphone::InputLabel('quantite' . $nombre, $quantite, 'Quantité : ', 'id="id_produit' . $nombre . 'quantite"');
		$remise = HtmlFormIphone::InputLabel('remise' . $nombre, $rem, 'Remise : ', 'id="id_produit' . $nombre . 'remise"');
		$desc = HtmlFormIphone::TextareaLabel('desc' . $nombre, $v['desc'], ' id="id_produit' . $nombre . 'desc" ', 'Libellé : ');
		$prix = HtmlFormIphone::InputLabel('prix' . $nombre, $v['prix'], 'Px unit. : ', 'id="id_produit' . $nombre . 'prix"');
		$out .='<li>' . $id . '</li>';
		$out .='<li>' . $desc . '</li>';
		$out .='<li>' . $qtte . '</li>';
		$out .='<li>' . $remise . '</li>';
		$out .='<li>' . $prix . '</li>';
		$out .='<input type="hidden" name="old_id' . $nombre . '" value="' . $v['id_produit'] . '">';
		$out .='</ul></fieldset>';
	    }
	} else {
	    $quantite = ($value['quantite'] != NULL) ? $value['quantite'] : '1';
	    $rem = ($value['remise'] != NULL) ? $value['remise'] : '0';
	    $out.='<fieldset><legend>Ajout d\'un produit</legend><ul>';
	    $id = self::inputAjaxProduit('id_produit', $value['id_produit'], 'Référence : ', true);
	    $qtte = HtmlFormIphone::InputLabel('quantite', $quantite, 'Quantité : ', 'id="id_produitquantite"');
	    $remise = HtmlFormIphone::InputLabel('remise', $rem, 'Remise : ', 'id="id_produitremise"');
	    $prix = HtmlFormIphone::InputLabel('prix', $value['prix'], 'Px unit. : ', 'id="id_produitprix"');
	    $desc = HtmlFormIphone::TextareaLabel('desc' . $nombre, $value['desc'], ' id="id_produitdesc" ', 'Libellé : ');
	    $out .='<li>' . $id . '</li>';
	    $out .='<li>' . $desc . '</li>';
	    $out .='<li>' . $qtte . '</li>';
	    $out .='<li>' . $remise . '</li>';
	    $out .='<li>' . $prix . '</li>';
	    $out .='</ul></fieldset>';
	}
	$_SESSION['produits']['nombre'] = $nombre;
	return $out;
    }

    /**
     * Fonction qui génère un "Lien simple" vers un devis.
     */
    static function devisLinkSimple($value = array()) {
	return '<a href="Devis.php?action=view&id_dev=' . $value['id_dev'] . '" class="Devis" rev="async"><img src="../img/actualite/devis.png"/> Devis ' . $value['id_dev'] . ' ' . $value['titre_dev'] . '</a>';
    }

    /**
     * Fonction assurant l'affichage du formulaire de modification d'un devis.
     */
    static function modif($value = array(), $onError = array(), $errorMess = '', $id_dev = '') {
	$error = ($errorMess != '') ? '<div class="err">' . $errorMess . '</div>' : '';
	$out = '<a href="#"  onclick="return WA.Submit(\'formModifDevis\',null,event)" rel="action" class="iButton iBAction"><img src="Img/save.png" alt="Enregistrer" /></a>
				<form id="formModifDevis" action="Devis.php?action=doModifDevis&id_dev=' . $id_dev . '" onsubmit="return WA.Submit(this,null,event)">
				' . $error . '
				<div class="iPanel">
					' . self::blockModif($value, $onError) . '
				</div>
				</form>';
	return $out;
    }

    /**
     * Fonction BLOCK qui va générer le rendu visuel du formulaire de modification d'un devis.
     */
    static function blockModif($value = array(), $onError = array()) {
	$out = self::subBlockNomDevis($value, $onError);
	$out .= self::subBlockContacts($value, $onError);
	$out .= self::subBlockAutresInfo($value, $onError);
	$out .= self::subBlockAdresse($value, $onError);
	$out .= self::subBlockAction($value, $onError);
	if ($value['supprimable'] == '0')
	    $out .='<a href="Devis.php?action=suppDevis&id_dev=' . $value["id_dev"] . '" rev="async" class="redButton"><img style="float: left;" src="Img/delete.png"/><span>Supprimer ce Devis</span></a>';
	$out .= '<fieldset>
					<a href="#" class="BigButtonValidLeft" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler" /></a>
					<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formModifDevis\', null, event)"><img src="Img/big.valider.png" alt="Valider" /></a>
				</fieldset>';
	return $out;
    }

    /**
     * Fonction sous BLOCK qui gère l'affichage du formulaire pour les contacts.
     */
    static function subBlockContacts($value = array(), $onError = array()) {
	$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$sqlConn->makeRequeteFree("select login, nom, prenom, civ from user order by nom;");
	$temp = $sqlConn->process2();
	$temp = $temp[1];
	foreach ($temp as $k => $v)
	    $userList[$v['login']] = $v['prenom'] . ' ' . $v['nom'];

	$particulier = contactParticulierView::inputAjaxContact('contact_dev', $value['contact_dev'], 'Contact : ', false);
	$particulierERR = (in_array('contact_dev', $onError)) ? '<span class="iFormErr"/>' : '';
	$acheteur = contactParticulierView::inputAjaxContact('contact_achat_dev', $value['contact_achat_dev'], 'Acheteur : ', true);
	$acheteurERR = (in_array('contact_achat_dev', $onError)) ? '<span class="iFormErr"/>' : '';
	$entreprise = ($value['entreprise_dev'] != NULL) ? '<li>' . contactEntrepriseView::contactLinkSimple($value) . '</li>' : '';
	$valuecommercial = ($value['commercial_dev'] != NULL) ? $value['commercial_dev'] : $_SESSION['user']['id'];
	$commercial = HtmlFormIphone::SelectLabel('commercial_dev', $userList, $valuecommercial, 'Commercial :', false);
	$commercialERR = (in_array('commercial_dev', $onError)) ? '<span class="iFormErr"/>' : '';

	$out = '<fieldset>
		<legend>Contacts</legend>
		<ul>
			' . $entreprise . '
			<li>' . $particulier . $particulierERR . '</li>
			<li>' . $acheteur . $acheteurERR . '</li>
			<li>' . $commercial . $commercialERR . '</li>
		</ul>
	</fieldset>';
	return $out;
    }

    /**
     * Fonction sous BLOCK qui gère l'affichage du formulaire pour l'affaire liée.
     */
    static function subBlockAffaire($value = array(), $onError = array()) {
	$affaire = affaireView::inputAjaxAffaire('affaire_dev', $value['affaire_dev'], 'Affaire : ', false);
	$affaireERR = (in_array('affaire_dev', $onError)) ? '<span class="iFormErr"/>' : '';
	$out = '<fieldset>
		<legend>Affaire</legend>
		<ul>
			<li>' . $affaire . $affaireERR . '</li>
		</ul>
	</fieldset>';
	return $out;
    }

    /**
     * Fonction sous BLOCK qui gère l'affichae pour les entrées d'une adresse.
     */
    static function subBlockAdresse($value = array(), $onError = array()) {
	$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$sqlConn->makeRequeteFree("select id_pays, nom_pays from ref_pays;");
	$temp = $sqlConn->process2();
	$temp = $temp[1];
	foreach ($temp as $k => $v)
	    $countryList[$v['id_pays']] = $v['nom_pays'];

	$nom = HtmlFormIphone::InputLabel('nomdelivery_dev', $value['nomdelivery_dev'], 'Nom : ');
	$add1 = HtmlFormIphone::InputLabel('adressedelivery_dev', $value['adressedelivery_dev'], 'Adresse : ');
	$add2 = HtmlFormIphone::InputLabel('adresse1delivery_dev', $value['adresse1delivery_dev'], 'Complément : ');
	$cp = HtmlFormIphone::InputLabel('cpdelivery_dev', $value['cpdelivery_dev'], 'CP : ');
	$ville = HtmlFormIphone::InputLabel('villedelivery_dev', $value['villedelivery_dev'], 'Ville : ');
	$pays = HtmlFormIphone::Select('paysdelivery_dev', $countryList, $value['paysdelivery_dev'], false);

	$out = '<fieldset>
		<legend>Adresse de livraison</legend>
		<ul>
			<li>' . $nom . '</li>
			<li>' . $add1 . '</li>
			<li>' . $add2 . '</li>
			<li>' . $cp . '</li>
			<li>' . $ville . '</li>
			<li>' . $pays . '</li>
		</ul>
	</fieldset>';
	return $out;
    }

    /**
     * Fonction sous BLOCK qui gère l'affichage du formulaire pour le nom du devis.
     */
    static function subBlockNomDevis($value = array(), $onError = array()) {
	$nom = HtmlFormIphone::InputLabel('titre_dev', $value['titre_dev'], 'Nom : ');
	$out = '<fieldset>
		<legend>Devis</legend>
		<ul>
			<li>' . $nom . '</li>
		</ul>
	</fieldset>';
	return $out;
    }

    static function subBlockAutresInfo($value = array(), $onError = array()) {
	$list = array('0' => '0 %', '5.5' => '5,5 %', '19.6' => '19,6 %');
	$txtva = ($value['tva_dev'] == NULL) ? '19.6' : $value['tva_dev'];
	$tva = HtmlFormIphone::SelectLabel('tva_dev', $list, $txtva, 'Tx TVA :', false);
	$desc = HtmlFormIphone::TextareaLabel('complementdelivery_dev', $value['complementdelivery_dev'], '', 'Complément :');
	$mail = HtmlFormIphone::InputLabel('maildelivery_dev', $value['maildelivery_dev'], 'E-mail :');

	$out = '<fieldset><legend>Informations de livraison</legend>' .
		'<ul>
			<li>' . $mail . '</li>
			<li>' . $desc . '</li>
			<li>' . $tva . '</li>
		</ul></fieldset>';
	return $out;
    }

    static function subBlockAction($value = array()) {
	$out = '<fieldset>
				<legend>Actions</legend>
				<ul class="iArrow">';
	if ($value['status_dev'] <= 4)
	    $out.= '<li><a rev="async" href="Devis.php?action=addProduit&amp;id_dev=' . $value['id_dev'] . '"><img src="../img/prospec/devis.addProduct.png"/> Ajouter un produit</a></li>';
	if ($value['sommeHT_dev'] > 0) {
	    $out.= '<li><a rev="async" href="Devis.php?action=voir&amp;id_dev=' . $value['id_dev'] . '"><img src="../img/prospec/devis.pdf.png"/> Voir le PDF</a></li>';
	    if ($value['status_dev'] <= 4) {
		$preFixRec = ($value['status_dev'] >= 3) ? 'Re-e' : 'E';
		$preFixSend = ($value['status_dev'] >= 4) ? 'Re-e' : 'E';
		$out.= '<li><a rev="async" href="Devis.php?action=rec&amp;id_dev=' . $value['id_dev'] . '"><img src="../img/prospec/devis.record.png"/> ' . $preFixRec . 'nregistrer</a></li>';
		$out.= '<li><a rev="async" href="Devis.php?action=recsend&amp;id_dev=' . $value['id_dev'] . '"><img src="../img/prospec/devis.recsend.png"/> ' . $preFixRec . 'nregistrer & ' . $preFixSend . 'nvoyer</a></li>';
		if ($value['status_dev'] >= 3)
		    $out.= '<li><a rev="async" href="Devis.php?action=send&amp;id_dev=' . $value['id_dev'] . '"><img src="../img/prospec/devis.send.png"/> ' . $preFixSend . 'nvoyer</a></li>';
	    }
	    if (($value['status_dev'] == 4) and $_SESSION['user']['id'] == $value['commercial_dev']) {
		$out.= '<li><a rev="async" href="Devis.php?action=perdu&amp;id_dev=' . $value['id_dev'] . '"><img src="../img/prospec/devis.perdu.png"/> Devis perdu</a></li>';
		$out.= '<li><a rev="async" href="Devis.php?action=valid&amp;id_dev=' . $value['id_dev'] . '"><img src="../img/prospec/devis.valid.png"/> Devis gagné</a></li>';
	    }
	}
	$out.= '<li><a rev="async" href="Devis.php?action=cloner&amp;id_dev=' . $value['id_dev'] . '"><img src="../img/prospec/devis.clone.png"/> Cloner ce devis</a></li>';
	if ($value['sommeHT_dev'] > 0) {
	    if ($value['id_cmd'] == null || $value['id_cmd'] == '')
		$out .= '<li><a rev="async" href="Commande.php?action=addCommande&devis_cmd=' . $value['id_dev'] . '"><img src="../img/prospec/commande.add.png" /> Créer une commande liée.</a></li>';
	    $out .= '<li><a rev="async" href="Facture.php?action=addFactureFromDevis&devis_cmd=' . $value['id_dev'] . '"><img src="../img/prospec/facture.create.png" /> Créer une facture liée.</a></li>';
	}
	$out.= '	</ul>
			</fieldset>';
	return $out;
    }

    /**
     * Fonction qui gère l'affichage lors de la suppression d'un devis.
     */
    static function delete($value = array()) {
	if ($value["id_dev"] == 0) {
	    $out = '<a href="#_MainMenu"  rel="action" class="iButton iBBack"><img src="Img/home.png" alt="Accueil" /></a>
		  <div class="iPanel">
		  <div class="err">
				<strong> Devis supprimé ! </strong>
			</div>';
	    return $out;
	}
	$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$facture = '';
	$requeteC = $sqlConn->makeRequeteFree("select count(*) as total from commande where devis_cmd = '" . $value['id_dev'] . "' ; ");
	$requeteC = $sqlConn->process2();
	if ($requeteC[1][0]['total'] >= 1) {
	    $requete = $sqlConn->makeRequeteFree("select count(*) as total from facture where commande_fact = '" . $value['id_dev'] . "BC" . "' ; ");
	    $requete = $sqlConn->process2();
	    if ($requete[1][0]['total'] >= 1)
		$facture = '<strong> Cela supprimera également la commande ' . $value['id_dev'] . "BC" . ' et ' . $requete[1][0]['total'] . ' facture(s) liée(s)</strong>';
	    else
		$facture = '<strong> Cela supprimera également la commande ' . $value['id_dev'] . "BC" . ' liée</strong>';
	}
	$creation = '';
	if ($value['daterecord_dev'] != NULL)
	    $creation = strftime("%A %d %B %G", strtotime($value['daterecord_dev']));
	$somme = ($value['sommeHT_dev'] != NULL) ? '<li>Somme total HT : <small>' . formatCurencyDisplay($value['sommeHT_dev']) . '</small></li>' : '';
	$entreprise = ($value['entreprise_dev'] != NULL) ? '<li>' . contactEntrepriseView::contactLinkSimple($value) . '</li>' : '';
	$contact = ($value['contact_dev'] != NULL) ? '<li>' . contactParticulierView::contactLinkSimple($value) . '</li>' : '';
	$contactachat = ($value['contact_achat_dev'] != NULL) ? '<li>' . contactParticulierView::contactLinkSimple($value, 'achat') . '</li>' : '';
	$commercial = ($value['commercial_dev'] != NULL) ? '<li>Commercial : ' . $value['nom'] . ' ' . $value['prenom'] . '</li>' : '';
	$affaire = ($value['affaire_dev'] != NULL) ? '<li>' . affaireView::affaireLinkSimple($value) . '</li>' : '';
	$titre = ($value['titre_dev'] != NULL) ? '<strong>' . $value['titre_dev'] . '</strong>' : '<i>Aucun titre de devis</i>';
	$_SESSION['tva'] = $value["tauxTVA_ent"];
	//On vient d'effectuer tout un tas de tests pour s'assurer  que l'on ne vas afficher que des blocks avec des choses dedans.

	$linkHead = '<a href="Devis.php?action=doDeleteDevis&id_dev=' . $value["id_dev"] . '"  rev="async" rel="action" class="iButton iBAction"><img src="Img/remove.png" alt="Supprimer" /></a>';
	//On génère maintenant le rendu visuel.
	$out = $linkHead . '<div class="iPanel">' .
		'<div class="err">
			<strong> Êtes vous sur de vouloir supprimer ce devis? </strong>' . $facture . '
		</div>
		<fieldset>
			<legend></legend>
			<ul>
				<li>' . $titre . '</li>' . $affaire . '
			</ul>
		</fieldset>
		<fieldset>
			<legend>Contacts</legend>
			<ul>
				' . $entreprise . '
				' . $contact . '
				' . $contactachat . '
			</ul>
		</fieldset>' . self::subBlockRessourcesLiees($value) . '
		<fieldset>
			<legend>Autres informations</legend>
			<ul>
				<li>Devis créé le : <small>' . $creation . '</small></li>
				' . $somme . '
				<li>Statut : <small>' . $value['nom_stdev'] . '</small></li>
				' . $commercial . '
			</ul>
		</fieldset>
		<fieldset><legend>Produits liés</legend>
				<ul class="iArrow"><li><a href="Devis.php?action=produits&id_dev=' . $value["id_dev"] . '" rev="async">Voir les produits</a></li></ul>
		</fieldset>
		<fieldset>
			<a class="BigButtonValidLeft" href="#" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler"></a>
			<a class="BigButtonValidRight" href="Devis.php?action=doDeleteDevis&id_dev=' . $value["id_dev"] . '"><img src="Img/big.confirmer.png" alt="Valider"></a>
		</fieldset>
	</div>';
	return $out;
    }

    /**
     * Fonction qui gère l'affichage lors de l'ajout d'un devis.
     */
    static function add($value = array(), $onError = array(), $errorMess = '') {
	$error = ($errorMess != '') ? '<div class="err">' . $errorMess . '</div>' : '';
	$out = '<a href="#"  onclick="return WA.Submit(\'formAddDevis\',null,event)" rel="action" class="iButton iBAction"><img src="Img/save.png" alt="Enregistrer" /></a>
	<form id="formAddDevis" action="Devis.php?action=doAddDevis" onsubmit="return WA.Submit(this,null,event)">
	' . $error . '
	<div class="iPanel">
		' . self::blockAdd($value, $onError) . '
	</div>
	</form>';
	return $out;
    }

    /**
     * Fonction BLOCK pour l'ajout d'un devis.
     */
    static function blockAdd($value = array(), $onError = array()) {

	$out = self::subBlockAffaire($value, $onError);
	$out .= self::subBlockNomDevis($value, $onError);
	$out .= self::subBlockContacts($value, $onError);
	$out .= '<fieldset>
		<a href="#" class="BigButtonValidLeft" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler" /></a>
		<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formAddDevis\', null, event)"><img src="Img/big.valider.png" alt="Valider" /></a>
	</fieldset>';
	return $out;
    }

    /**
     * Fonction qui gère l'affichage lorsqu'un devis est en pahse d'être créé et qu'il n'a pas d'adresse.
     */
    static function addPlus($value = array(), $onError = array(), $errorMess = '') {
	$id_aff = $value['affaire_dev'];
	$error = ($errorMess != '') ? '<div class="err">' . $errorMess . '</div>' : '';
	$error .='<div class="err">Aucune entreprise liée à l\'affaire, ou aucune adresse pour l\'entreprise liée.</div>';
	$out = '<a href="#"  onclick="return WA.Submit(\'formAddDevisPlus\',null,event)" rel="action" class="iButton iBAction"><img src="Img/save.png" alt="Enregistrer" /></a>
	<form id="formAddDevisPlus" action="Devis.php?action=doAddDevisPlus&id_aff=' . $id_aff . '" onsubmit="return WA.Submit(this,null,event)">
	' . $error . '
	<div class="iPanel">
		' . self::blockAddPlus($value, $onError) . '
	</div>
	</form>';
	return $out;
    }

    /**
     * Fonction BLOCK pour l'ajout d'un devis sans adresse.
     */
    static function blockAddPlus($value = array(), $onError = array()) {

	$out = self::subBlockAdresse($value, $onError);
	$out .= self::subBlockAffaire($value, $onError);
	$out .= self::subBlockNomDevis($value, $onError);
	$out .= self::subBlockContacts($value, $onError);
	$out .= '<fieldset>
		<a href="#" class="BigButtonValidLeft" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler" /></a>
		<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formAddDevisPlus\', null, event)"><img src="Img/big.valider.png" alt="Valider" /></a>
	</fieldset>';
	return $out;
    }

    static function inputAjaxProduit($nom = '', $selected = '', $titre = '', $withBlank = true, $commande = NULL, $de = '') {
	$_SESSION['commandeAjaxProduit'] = $commande;
	$nom = ($nom != '') ? $nom : 'id_produit';
	$titre = ($titre != '') ? '<label style="float:left;">' . $titre . '</label>' : '';
	if ($selected != '') {
	    if ($commande == 'oui')
		$info = new commandeModel();
	    elseif ($commande == 'facture')
		$info = new factureModel();
	    else
		$info = new devisModel();
	    $result = $info->getInfoProduitsPlus($selected);
	    if ($result[0]) {
		if ($result[1][0]['nom_prod'] == '' || $result[1][0]['nom_prod'] == NULL) {
		    $nomSelected = '[' . $result[1][0]['id_produit'] . ']';
		    $idSelected = $result[1][0]['id_produit'];
		} else {
		    $nomSelected = $result[1][0]['nom_prod'] . ' [' . $result[1][0]['id_prod'] . ']';
		    $idSelected = $result[1][0]['id_prod'];
		}
	    } elseif (!$withBlank)
		$nomSelected = '<i>Veuillez choisir un produit</i>';
	    else
		$nomSelected = '&nbsp;';
	}
	elseif (!$withBlank)
	    $nomSelected = '<i>Veuillez choisir un produit</i>';
	else
	    $nomSelected = '&nbsp;';

	$out = $titre . ' <a href="Devis.php?action=inputProduit' . $de . '&tag=' . $nom . '"  id="' . $nom . 'AId" style="float:left;width:70%" rev="async"/> ' . $nomSelected . '</a>
			<input type="hidden" name="' . $nom . '" id="' . $nom . 'InputId" value="' . $idSelected . '"/>' .
		'<br class="clear" /><div id="boxmemorize' . $nom . '" style="display:none" >' . HtmlFormIphone::Checkbox('memorize' . $nom, 'Mémoriser ce produit : ') . '</div>' .
		'<br class="clear"/>';
	return $out;
    }

    static function searchInputResultRowProd($result, $layerBackTo, $tagsBackTo) {
	$commande = $_SESSION['commandeAjaxProduit'];
	$out = '';
	if (is_array($result) and count($result) > 0)
	    foreach ($result as $k => $v) {
		$n = $v['id_prod'] . ' ';
		$nom = '<small>' . FileCleanFileName(strtoupper($v['nom_prod']), 'APOSTROPHE') . '</small>';
		$n .= $nom;
		$desc = FileCleanFileName($v['nom_prod'], 'APOSTROPHE');
		$prix = $v['prix_prod'];
		if ($commande == NULL) {
		    $out .= '<li><a href="#_' . substr($layerBackTo, 2) . '" onclick="returnAjaxInputResultProduit(\'' . $tagsBackTo . '\',\'' . $v['id_prod'] . '\',\'' . $n . '\',\'' . $desc . '\',\'' . $prix . '\')">' .
			    '<em>' . $n . '</em>' .
			    '</a></li>';
		}
		if ($commande == 'express') {
		    $out .= '<li><a href="#_' . substr($layerBackTo, 2) . '" onclick="returnAjaxInputResultProduitExpress(\'' . $tagsBackTo . '\',\'' . $v['id_prod'] . '\',\'' . $n . '\',\'' . $desc . '\',\'' . $prix . '\',\'' . $_SESSION['devisExpress']['nb_prod'] . '\', \'19.6\')">' .
			    '<em>' . $n . '</em>' .
			    '</a></li>';
		} elseif ($commande == 'oui' || $commande == 'facture') {
		    $sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
		    $list = array();
		    $sqlConn->makeRequeteFree("select distinct * from produit p left join produit_fournisseur pf on pf.produit_id = p.id_prod left join fournisseur ON fournisseur.id_fourn = pf.fournisseur_id left join entreprise e ON e.id_ent = fournisseur.entreprise_fourn where p.id_prod = '" . trim($v['id_prod']) . "' and pf.actif = '1';");
		    $temp = $sqlConn->process2();
		    $temp = $temp[1];
		    $prix = $temp[0]['prix_prod'];
		    $totalBrut = $prix;
		    if ($temp[0]['fournisseur_id'] == NULL) {
			$fourn = '';
			$remise = '';
			$total = $totalBrut;
		    } else {
			$outJS .= 'fournisseur["' . $temp[0]['id_prod'] . '"]=new Array();' . "\n";
			$outJS .= 'afffourn["' . $temp[0]['id_prod'] . '"]=new Array();' . "\n";
			foreach ($temp as $kk => $vv) {
			    $list[$vv['fournisseur_id']] = $vv['nom_ent'] . ' (' . $vv['remiseF'] . '%)';
			    $outJS .= 'fournisseur["' . $temp[0]['id_prod'] . '"]["' . $vv['fournisseur_id'] . '"]=' . $vv['remiseF'] . ";\n";
			    $outJS .= 'fournisseur["' . $temp[0]['id_prod'] . '"]["' . $vv['fournisseur_id'] . 'P"]=' . $vv['prixF'] . ";\n";
			    $outJS .= 'afffourn["' . $temp[0]['id_prod'] . '"]["' . $vv['fournisseur_id'] . '"]="' . $vv['nom_ent'] . ' (' . $vv['remiseF'] . '%)"' . ";\n";
			}
		    }
		    $outJS .= 'totalBrut["' . $temp[0]['id_prod'] . '"]=' . $totalBrut . ";\n";
		    $out .='<script>' . $outJS . '</script>';
		    $out .= '<li><a href="#_' . substr($layerBackTo, 2) . '" onclick="returnAjaxInputResultProduitCommande(\'' . $tagsBackTo . '\',\'' . $v['id_prod'] . '\',\'' . $n . '\',\'' . $desc . '\',\'' . $prix . '\')">' .
			    '<em>' . $n . '</em>' .
			    '</a></li>';
		}
	    }
	return $out;
    }

    /**
     * Fonction de gestion de l'affichage de la fiche pour clonage
     */
    static function cloner($value = array()) {
	return '<a href="#_MainMenu" rel="action" class="iButton iBAction"><img src="Img/home.png" alt="Accueil" /></a>
	<div class="iPanel"><br/><br/>
		<div class="msg"><br/>Merci de confirmer le clonage de ce devis<br/></div>
		<br/>
		<fieldset>
			<a href="#" class="BigButtonValidLeft" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler"></a>
			<a href="Devis.php?action=doCloner&id_dev=' . $value["id_dev"] . '" rev="async" class="BigButtonValidRight"><img src="Img/big.confirmer.png" alt="confirmer"></a>
		</fieldset>
	</div>';
    }

    /**
     * Fonction de gestion de l'affichage de la fiche pour clonage
     */
    static function actionVoir($value = array()) {
	foreach ($GLOBALS['ZunoDevis'] as $key => $val) {
	    $k = explode('.', $key, 2);
	    if ($k[0] == 'cannevas' and $k[1] != 'exportTableur')
		$toto[$key] = $key;
	}
	$availableConvFormat = OOConverterAvailable('document');
	$value['OutputExt'] = ($value['OutputExt'] != '') ? $value['OutputExt'] : 'pdf';

	$cannevas = HtmlFormIphone::SelectLabel('Cannevas', $toto, $value['Cannevas'], 'Cannevas : ', false);
	$extention = HtmlFormIphone::SelectLabel('OutputExt', $availableConvFormat, $value['OutputExt'], 'Format : ', false);

	return '<a href="#_MainMenu" rel="action" class="iButton iBAction"><img src="Img/home.png" alt="Accueil" /></a>
		<form id="formDevisDoVoir" action="Devis.php?action=doVoir&id_dev=' . $value["id_dev"] . '" onsubmit="return WA.Submit(this,null,event)">
		<div class="iPanel">
		<fieldset>
			<legend>Options du document</legend>
			<ul>
				<li>' . $cannevas . '</li>
				<li>' . $extention . '</li>
			</ul>
		</fieldset>
		<div id="formDevisDoVoirResponse"></div>
		<fieldset>
			<a href="#" class="BigButtonValidLeft" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler"></a>
			<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formDevisDoVoir\',null,event)"><img src="Img/big.valider.png" alt="Valider"></a>
		</fieldset>
	</div>';
    }

    /**
     * Fonction de gestion de l'affichage de la fiche pour clonage
     */
    static function actionRecord($value = array()) {
	foreach ($GLOBALS['ZunoDevis'] as $key => $val) {
	    $k = explode('.', $key, 2);
	    if ($k[0] == 'cannevas' and $k[1] != 'exportTableur')
		$toto[$key] = $key;
	}
	$availableConvFormat = OOConverterAvailable('document');
	$value['OutputExt'] = ($value['OutputExt'] != '') ? $value['OutputExt'] : 'pdf';

	$cannevas = HtmlFormIphone::SelectLabel('Cannevas', $toto, $value['Cannevas'], 'Cannevas :', false);
	$extention = HtmlFormIphone::SelectLabel('OutputExt', $availableConvFormat, $value['OutputExt'], 'Format :', false);

	$value['message'] = ($value['message'] != '') ? $value['message'] : 'Ajout du document ' . $value["doc_dev"];
	$mess = HtmlFormIphone::TextareaLabel('message', $value['message'], '', 'Message :');

	return '<a href="#_MainMenu" rel="action" class="iButton iBAction"><img src="Img/home.png" alt="Accueil" /></a>
		<form id="formDevisDoRec" action="Devis.php?action=doRec&id_dev=' . $value["id_dev"] . '" onsubmit="return WA.Submit(this,null,event)">
		<div class="iPanel">
		<fieldset>
			<legend>Options du document</legend>
			<p><i>Le message sera utilisé comme message d\'enregistrement lors de l\'ajout du document dans l\'entrepôt ZunoGed.</i></p>
			<ul>
				<li>' . $cannevas . '</li>
				<li>' . $extention . '</li>
				<li>' . $mess . '</li>
			</ul>
		</fieldset>
		<fieldset>
			<a href="#" class="BigButtonValidLeft" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler"></a>
			<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formDevisDoRec\',null,event)"><img src="Img/big.valider.png" alt="Valider"></a>
		</fieldset>
	</div>';
    }

    /**
     * Fonction de gestion de l'affichage de la fiche pour clonage
     */
    static function actionSend($value = array()) {
	foreach ($GLOBALS['ZunoDevis'] as $key => $val) {
	    $k = explode('.', $key, 2);
	    if ($k[0] == 'cannevas' and $k[1] != 'exportTableur')
		$toto[$key] = $key;
	}
	$availableConvFormat = OOConverterAvailable('document');
	$value['OutputExt'] = ($value['OutputExt'] != '') ? $value['OutputExt'] : 'pdf';

	$cannevas = HtmlFormIphone::SelectLabel('Cannevas', $toto, $value['Cannevas'], 'Cannevas :', false);
	$extention = HtmlFormIphone::SelectLabel('OutputExt', $availableConvFormat, $value['OutputExt'], 'Format :', false);
	$type = HtmlFormIphone::SelectLabel('type', array('email' => 'E-mail', 'courrier' => 'Courrier', 'fax' => 'Fax'), $value['type'], 'Type :', false);

	return '<a href="#_MainMenu" rel="action" class="iButton iBAction"><img src="Img/home.png" alt="Accueil" /></a>
		<form id="formDevisDoSend" action="Devis.php?action=send1&id_dev=' . $value["id_dev"] . '" onsubmit="return WA.Submit(this,null,event)">
		<div class="iPanel">
		<fieldset>
			<legend>Options du document</legend>
			<ul>
				<li>' . $cannevas . '</li>
				<li>' . $extention . '</li>
			</ul>
		</fieldset>
		<fieldset>
			<legend>Options d\'envoi</legend>
			<ul>
				<li>' . $type . '</li>
			</ul>
		</fieldset>
		<fieldset>
			<a href="#" class="BigButtonValidLeft" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler"></a>
			<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formDevisDoSend\',null,event)"><img src="Img/big.valider.png" alt="Valider"></a>
		</fieldset>
	</div>';
    }

    /**
     * Fonction de gestion de l'affichage de la fiche pour clonage
     */
    static function actionSend1($value = array(), $onError = array(), $errorMess = '') {
	if ($value['type'] == 'courrier')
	    $form = sendView::innerFormSendCourrier($value, $onError, $errorMess);
	elseif ($value['type'] == 'fax')
	    $form = sendView::innerFormSendFax($value, $onError, $errorMess);
	else
	    $form = sendView::innerFormSendEmail($value, $onError, $errorMess);
	$form.= HtmlFormIphone::Input('type', $value['type'], '', '', 'hidden');

	return '<a href="#_MainMenu" rel="action" class="iButton iBAction"><img src="Img/home.png" alt="Accueil" /></a>
		<form id="formDevisDoSend1" action="Devis.php?action=doSend&id_dev=' . $value["id_dev"] . '" onsubmit="return WA.Submit(this,null,event)">
		<div class="iPanel">
		' . $form . '
		<fieldset>
			<a href="#" class="BigButtonValidLeft" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler"></a>
			<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formDevisDoSend1\',null,event)"><img src="Img/big.valider.png" alt="Valider"></a>
		</fieldset>
	</div>';
    }

    /**
     * Fonction de gestion de l'affichage de la fiche pour clonage
     */
    static function actionRecordSend($value = array()) {
	foreach ($GLOBALS['ZunoDevis'] as $key => $val) {
	    $k = explode('.', $key, 2);
	    if ($k[0] == 'cannevas' and $k[1] != 'exportTableur')
		$toto[$key] = $key;
	}
	$availableConvFormat = OOConverterAvailable('document');
	$value['OutputExt'] = ($value['OutputExt'] != '') ? $value['OutputExt'] : 'pdf';

	$cannevas = HtmlFormIphone::SelectLabel('Cannevas', $toto, $value['Cannevas'], 'Cannevas :', false);
	$extention = HtmlFormIphone::SelectLabel('OutputExt', $availableConvFormat, $value['OutputExt'], 'Format :', false);
	$type = HtmlFormIphone::SelectLabel('type', array('email' => 'E-mail', 'courrier' => 'Courrier', 'fax' => 'Fax'), $value['type'], 'Type :', false);

	$value['message'] = ($value['message'] != '') ? $value['message'] : 'Ajout du document ' . $value["doc_dev"];
	$mess = HtmlFormIphone::TextareaLabel('message', $value['message'], '', 'Message :');

	return '<a href="#_MainMenu" rel="action" class="iButton iBAction"><img src="Img/home.png" alt="Accueil" /></a>
		<form id="formDevisDoRec" action="Devis.php?action=recsend1&id_dev=' . $value["id_dev"] . '" onsubmit="return WA.Submit(this,null,event)">
		<div class="iPanel">
		<fieldset>
			<legend>Options du document</legend>
			<p><i>Le message sera utilisé comme message d\'enregistrement lors de l\'ajout du document dans l\'entrepôt ZunoGed.</i></p>
			<ul>
				<li>' . $cannevas . '</li>
				<li>' . $extention . '</li>
				<li>' . $mess . '</li>
			</ul>
		</fieldset>
		<fieldset>
			<legend>Options d\'envoi</legend>
			<ul>
				<li>' . $type . '</li>
			</ul>
		</fieldset>
		<fieldset>
			<a href="#" class="BigButtonValidLeft" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler"></a>
			<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formDevisDoRec\',null,event)"><img src="Img/big.valider.png" alt="Valider"></a>
		</fieldset>
	</div>';
    }

    /**
     * Fonction de gestion de l'affichage de la fiche pour clonage
     */
    static function actionRecordSend1($value = array(), $onError = array(), $errorMess = '') {
	if ($value['type'] == 'courrier')
	    $form = sendView::innerFormSendCourrier($value, $onError, $errorMess);
	elseif ($value['type'] == 'fax')
	    $form = sendView::innerFormSendFax($value, $onError, $errorMess);
	else
	    $form = sendView::innerFormSendEmail($value, $onError, $errorMess);
	$form.= HtmlFormIphone::Input('type', $value['type'], '', '', 'hidden');

	return '<a href="#_MainMenu" rel="action" class="iButton iBAction"><img src="Img/home.png" alt="Accueil" /></a>
		<form id="formDevisDoRec1" action="Devis.php?action=doRecsend&id_dev=' . $value["id_dev"] . '" onsubmit="return WA.Submit(this,null,event)">
		<div class="iPanel">
		' . $form . '
		<fieldset>
			<a href="#" class="BigButtonValidLeft" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler"></a>
			<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formDevisDoRec1\',null,event)"><img src="Img/big.valider.png" alt="Valider"></a>
		</fieldset>
	</div>';
    }

    static function inputAjaxDevis($nom = '', $selected = '', $titre = '', $withBlank = true) {
	$nom = ($nom != '') ? $nom : 'devis_cmd';
	$titre = ($titre != '') ? '<label style="float:left;">' . $titre . '</label>' : '';
	if ($selected != '') {
	    $info = new devisModel();
	    $result = $info->getDataFromID($selected);
	    if ($result[0]) {
		$nomSelected = $result[1][0]['titre_dev'] . ' [' . $result[1][0]['id_dev'] . ']';
		$idSelected = $result[1][0]['id_dev'];
	    } elseif (!$withBlank)
		$nomSelected = '<i>Veuillez choisir un devis</i>';
	    else
		$nomSelected = '&nbsp;';
	}
	elseif (!$withBlank)
	    $nomSelected = '<i>Veuillez choisir un devis</i>';
	else
	    $nomSelected = '&nbsp;';
	$out = $titre . ' <a href="Devis.php?action=inputDevis&tag=' . $nom . '"  id="' . $nom . 'AId" style="float:left;width:70%" rev="async"/> ' . $nomSelected . '</a>
			<input type="hidden" name="' . $nom . '" id="' . $nom . 'InputId" value="' . $idSelected . '"/><br class="clear"/>';
	return $out;
    }

    static function searchInputResultRow($result, $layerBackTo, $tagsBackTo) {
	$out = '';
	if (is_array($result) and count($result) > 0)
	    foreach ($result as $k => $v) {
		$ent = ($v['nom_ent'] != NULL) ? '<small> (' . $v['nom_ent'] . ') </small>' : '';
		$n = $v['id_dev'] . ' ' . strtoupper($v['titre_dev']) . $ent;
		$out .= '<li><a href="#_' . substr($layerBackTo, 2) . '" onclick="returnAjaxInputResult(\'' . $tagsBackTo . '\',\'' . $v['id_dev'] . '\',\'' . $n . '\')">' .
			'<em>' . $n . '</em>' .
			'</a></li>';
	    }
	return $out;
    }

    static function addExpress($val = array(), $onError = array(), $errorMess = '') {
	$error = ($errorMess != '') ? '<div class="err">' . $errorMess . '</div>' : '';

	$out .= '<a href="#"  onclick="return WA.Submit(\'formAddDevisExpress\',null,event)" rel="action" class="iButton iBAction"><img src="Img/save.png" alt="Enregistrer" /></a>
	<form name="formAddDevisExpress" id="formAddDevisExpress" action="Devis.php?action=addDevisExpressSuite" onsubmit="return WA.Submit(this,null,event)">
	' . $error . '<div class="iPanel">
		' . self::blockAddExpress1($val, $onError) . '
	</div>
	</form>';
	$out .= '<form id="formEntrepriseDevisExpress" action="Devis.php?action=entrepriseDevisExpress" onsubmit="return WA.Submit(this,null,event)">' .
		'<div style="display:none"><input id="entreprise_hidden_devis_express" type="hidden" name="entreprise" value=0 />' .
		'<a id="valid_entrepriseDevisExpress" onclick="return WA.Submit(\'formEntrepriseDevisExpress\',null,event)">Lien caché</a>' .
		'</div></form>';
	return $out;
    }

    static function blockAddExpress1($val = array(), $onError = array()) {
	$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$sqlConn->makeRequeteFree("select id_pays, nom_pays from ref_pays;");
	$temp = $sqlConn->process2();
	$temp = $temp[1];
	foreach ($temp as $k => $v)
	    $countryList[$v['id_pays']] = $v['nom_pays'];
	$entERR = (in_array('nomdelivery_dev', $onError)) ? '<span class="iFormErr"/>' : '';
	$add1ERR = (in_array('adressedelivery_dev', $onError)) ? '<span class="iFormErr"/>' : '';
	$cpERR = (in_array('cpdelivery_dev', $onError)) ? '<span class="iFormErr"/>' : '';
	$villeERR = (in_array('villedelivery_dev', $onError)) ? '<span class="iFormErr"/>' : '';
	$mailERR = (in_array('maildelivery_dev', $onError)) ? '<span class="iFormErr"/>' : '';
	$nb_prodERR = (in_array('nb_prod', $onError)) ? '<span class="iFormErr"/>' : '';
	$ent = HtmlFormIphone::InputLabelWnoku('nomdelivery_dev', $val['nomdelivery_dev'], 'Entreprise : ', 'id="entrepriseDevisExpress" onkeyup="modifEntrepriseDevisExpress(this.value);"');
	$cont = HtmlFormIphone::InputLabelWnoku('contact_dev', $val['contact_dev'], 'Contact : ', 'id="contactDevisExpress" onkeyup="doModifContactDevisExpress(\'Devis\');"');
	$add1 = HtmlFormIphone::InputLabel('adressedelivery_dev', $val['adressedelivery_dev'], 'Adresse : ', 'id="add1DevisExpress" ');
	$add2 = HtmlFormIphone::InputLabel('adresse1delivery_dev', $val['adresse1delivery_dev'], 'Complément : ', 'id="add2DevisExpress"');
	$cp = HtmlFormIphone::InputLabel('cpdelivery_dev', $val['cpdelivery_dev'], 'CP : ', 'id="cpDevisExpress"');
	$ville = HtmlFormIphone::InputLabel('villedelivery_dev', $val['villedelivery_dev'], 'Ville : ', 'id="villeDevisExpress"');
	$pays = HtmlFormIphone::Select('paysdelivery_dev', $countryList, $val['paysdelivery_dev'], false, 'id="paysDevisExpress"');
	$mail = HtmlFormIphone::InputLabel('maildelivery_dev', $val['maildelivery_dev'], 'Mail : ', 'id="mailDevisExpress"');
	$prenom = HtmlFormIphone::Input('prenom_cont', $val['prenom_cont'], 'Prénom : ');
	$telcont = HtmlFormIphone::Input('tel_cont', $val['tel_cont'], 'Tél : ');
	$telent = HtmlFormIphone::Input('tel_ent', $val['tel_ent'], 'Tél : ');
	$list = array('0' => '0 %', '5.5' => '5,5 %', '19.6' => '19,6 %');
	$civList = $GLOBALS['CIV_' . $_SESSION["language"]];
	$civ = HtmlFormIphone::Select('civ_cont', $civList, $val['civ_cont'], false);
	$tvadefault = ($val['tva_dev'] != NULL) ? $val['tva_dev'] : '19.6';
	$tva = HtmlFormIphone::SelectLabel('tva_dev', $list, $tvadefault, 'Tx TVA :', false);
	$out = '<fieldset>';
	$out .= '<ul><li>' . $ent . $entERR . '</li>';
	$out .= '<li id="telEntrepriseJS" style="display:none">' . $telent . '</li>';
	$out .='<li class="proposition_entreprise" id="propositionEntrepriseJS" style="display:none" onclick="addEntrepriseAuto(\'propositionEntrepriseJS\', \'Devis\');"></li>';
	$out .='<li class="proposition_entreprise" id="propositionEntreprise2JS" style="display:none" onclick="addEntrepriseAuto(\'propositionEntreprise2JS\', \'Devis\');"></li>';
	$out .='<li class="proposition_entreprise" id="propositionEntreprise3JS" style="display:none" onclick="addEntrepriseAuto(\'propositionEntreprise3JS\', \'Devis\');"></li>';
	$out .='<li class="proposition_entreprise" id="propositionEntreprise4JS" style="display:none" onclick="addEntrepriseAuto(\'propositionEntreprise4JS\', \'Devis\');"></li>';
	$out .='<li class="proposition_entreprise" id="propositionEntreprise5JS" style="display:none" onclick="addEntrepriseAuto(\'propositionEntreprise5JS\', \'Devis\');"></li>';
	$out .= '<li>' . $cont . '</li>';
	$out .='<li class="proposition_contact" id="propositionContactJS" style="display:none" onclick="addContactAuto(\'\', \'Devis\');"></li>';
	$out .='<li class="proposition_contact" id="propositionContact2JS" style="display:none" onclick="addContactAuto(\'2\', \'Devis\');"></li>';
	$out .='<li class="proposition_contact" id="propositionContact3JS" style="display:none" onclick="addContactAuto(\'3\', \'Devis\');"></li>';
	$out .='<li class="proposition_contact" id="propositionContact4JS" style="display:none" onclick="addContactAuto(\'4\', \'Devis\');"></li>';
	$out .='<li class="proposition_contact" id="propositionContact5JS" style="display:none" onclick="addContactAuto(\'5\', \'Devis\');"></li>';
	$out .='<li id="idcontExpress" style="display:none">&nbsp;</li>';
	$out .='<li id="idcontExpress2" style="display:none">&nbsp;</li>';
	$out .='<li id="idcontExpress3" style="display:none">&nbsp;</li>';
	$out .='<li id="idcontExpress4" style="display:none">&nbsp;</li>';
	$out .='<li id="idcontExpress5" style="display:none">&nbsp;</li>';
	$out .= '<li id="prenomContactJS" style="display:none">' . $prenom . '</li>';
	$out .= '<li id="civContactJS" style="display:none">' . $civ . '</li>';
	$out .= '<li id="telContactJS" style="display:none">' . $telcont . '</li>';
	$out .= '<li>' . $mail . $mailERR . '</li></ul>';
	$out .= '<ul><li>' . $add1 . $add1ERR . '</li>';
	$out .= '<li>' . $add2 . '</li>';
	$out .= '<li>' . $cp . $cpERR . '</li>';
	$out .= '<li>' . $ville . $villeERR . '</li>';
	$out .= '<li>' . $pays . '</li></ul>';
	$out .= '<input type="hidden" name="listeContact" id="id_contDevisExpress" value="" />';
	$out .= '<input type="hidden" name="entreprise_dev" id="id_entDevisExpress" value="' . $val['entreprise_dev'] . '" />';
	$out .= '<ul><li>' . $tva . '</li></ul>';
	$out .= '</fieldset>';
	$out .= '<fieldset>
					<a href="#" class="BigButtonValidLeft" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler" /></a>
					<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formAddDevisExpress\', null, event)"><img src="Img/big.valider.png" alt="Valider" /></a>
				</fieldset>';
	return $out;
    }

    static function addExpressSuite($val = array(), $onError = array(), $errorMess = '') {
	$nombre = 1;
	$error = ($errorMess != '') ? '<div class="err">' . $errorMess . '</div>' : '';
	$out = '<a href="#"  onclick="return WA.Submit(\'formDevisExpressProduit\',null,event)" rel="action" class="iButton iBAction"><img src="Img/save.png" alt="Enregistrer" /></a>
				<form id="formDevisExpressProduit" action="Devis.php?action=doAddDevisExpress" onsubmit="return WA.Submit(this,null,event)">
				' . $error . '<div class="iPanel" id="DevisExpressForm">';
	$out .= '<div id="DEProdDevis">';
	while ($nombre <= $_SESSION['devisExpress']['nb_prod']) {
	    $out.='<fieldset><legend>Produit ' . $nombre . '</legend><ul>';
	    $id = self::inputAjaxProduit('id_produitDevisExpress' . $nombre, $val['id_produitDevisExpress' . $nombre], 'Référence : ', true, 'express', 'DE');
	    $qtte = HtmlFormIphone::InputLabel('quantite' . $nombre, $val['quantite' . $nombre], 'Quantité : ', 'id="id_produitDevisExpress' . $nombre . 'quantite", onchange="quantiteOnDevisExpress(this.value,' . $_SESSION['devisExpress']['nb_prod'] . ',' . $nombre . ',\'19.6\');"');
	    $remise = HtmlFormIphone::InputLabel('remise' . $nombre, $val['remise' . $nombre], 'Remise : ', 'id="id_produitDevisExpress' . $nombre . 'remise", onchange="remiseOnDevisExpress(this.value,' . $_SESSION['devisExpress']['nb_prod'] . ',' . $nombre . ',\'19.6\');"');
	    $prix = HtmlFormIphone::InputLabel('prix' . $nombre, $val['prix' . $nombre], 'Px unit. : ', 'id="id_produitDevisExpress' . $nombre . 'prix", onchange="prixOnDevisExpress(this.value,' . $_SESSION['devisExpress']['nb_prod'] . ',' . $nombre . ',\'19.6\');"');
	    $desc = HtmlFormIphone::TextareaLabel('desc' . $nombre, $val['desc' . $nombre], ' id="id_produitDevisExpress' . $nombre . 'desc" ', 'Libellé : ');
	    $qttERR = (in_array('quantite' . $nombre, $onError)) ? '<span class="iFormErr"/>' : '';
	    $remiseERR = (in_array('remise' . $nombre, $onError)) ? '<span class="iFormErr"/>' : '';
	    $prixERR = (in_array('prix' . $nombre, $onError)) ? '<span class="iFormErr"/>' : '';
	    $out .='<li>' . $id . '</li>';
	    $out .='<li>' . $desc . '</li>';
	    $out .='<li>' . $qtte . $qttERR . '</li>';
	    $out .='<li>' . $remise . $remiseERR . '</li>';
	    $out .='<li>' . $prix . $prixERR . '</li>';
	    $out .='<li>Ss total : <div style="display:inline" id="sstotalid_produitDevisExpress' . $nombre . '">0</div> €</li>';
	    $out .='</ul></fieldset>';
	    $nombre++;
	}
	$out .= '</div>';
	$out .='<fieldset><legend>Total</legend><ul>';
	$out .='<li id="htDevisExpress">Total HT : </li>';
	$out .='<li id="tvaDevisExpress">TVA : </li>';
	$out .='<li id="ttcDevisExpress">Total TTC : </li>';
	$out .='</ul></fieldset>';
	$out .='<fieldset>
		<a class="BigButtonValidLeft" href="#" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler"></a>
		<a class="BigButtonValidRight" href="#" onclick="return WA.Submit(\'formDevisExpressProduit\',null,event)"><img src="Img/big.valider.png" alt="Valider"></a>
	</fieldset>
	</div>
	</form>';
	$out .= '<div style="display:none;"><a href="Devis.php?action=inputProduitDESuite" rev="async" id="ajoutproduitdevisexpressauto" ></a></div>';
	return $out;
    }

    static function tri_montant($value = array(), $limit, $from, $total) {
	$out = '<ul>';
	$prix = $_SESSION['user']['LastLetterSearch'];
	$valeurs = getStats('devis');
	foreach ($value[1] as $k => $v) {
	    $sortie = triMontant($v['sommeHT_dev'], $prix, $valeurs);
	    $prix = $sortie[1];
	    $out .= $sortie[0];
	    $out .= '<li><a href="Devis.php?action=view&id_dev=' . $v['id_dev'] . '"rev="async">' . $v['id_dev'] . ' - ' . $v['titre_dev'] . ' <small>Montant : <b>' . formatCurencyDisplay($v['sommeHT_dev']) . '</b></small></a></li>';
	}
	if ($total > ($limit + $from))
	    $out .= '<li class="iMore" id="triMontantDevisMore' . $from . '"><a href="Devis.php?action=triMontantMore&from=' . ($limit + $from) . '&total=' . $total . '" rev="async">Plus de résultats</a></li>';
	$_SESSION['user']['LastLetterSearch'] = $prix;
	$out .= '</ul>';
	return $out;
    }

    static function tri_creation($value = array(), $limit = '', $from = '', $total = '') {
	$mois = $_SESSION['user']['LastLetterSearch'];
	$out = '<ul>';
	foreach ($value[1] as $k => $v) {
	    if ($v['daterecord_dev'] != NULL) {
		if ($mois != ucfirst(strftime("%B %G", strtotime($v['daterecord_dev'])))) {
		    $mois = ucfirst(strftime("%B %G", strtotime($v['daterecord_dev'])));
		    $out .= '</ul><h2>' . $mois . '</h2><ul class="iArrow">';
		}
		$echeance = strftime("%d/%m/%G", strtotime($v['daterecord_dev']));
		$out .= '<li><a href="Devis.php?action=view&id_dev=' . $v['id_dev'] . '" rev="async">' . $v['id_dev'] . ' - ' . $v['titre_dev'] . ' <small>Création le : <b>' . $echeance . '</b></small></a></li>';
	    }
	}
	if ($total > ($limit + $from))
	    $out .= '<li class="iMore" id="triCreationDevisMore' . $from . '"><a href="Devis.php?action=triCreationMore&from=' . ($limit + $from) . '&total=' . $total . '" rev="async">Plus de résultats</a></li>';
	$out .= '</ul>';
	$_SESSION['user']['LastLetterSearch'] = $mois;
	return $out;
    }

    static function tri_entreprise($value = array(), $limit = '', $from = '', $total = '') {
	$ent = $_SESSION['user']['LastLetterSearch'];
	$out = '<ul>';
	foreach ($value[1] as $k => $v) {
	    if ($v['nom_ent'] != NULL) {
		if ($ent != strtoupper($v['nom_ent']{0})) {
		    $ent = strtoupper($v['nom_ent']{0});
		    $out .= '</ul><h2>' . $ent . '</h2><ul class="iArrow">';
		}
		$entreprise = $v['nom_ent'];
		$out .= '<li><a href="Devis.php?action=view&id_dev=' . $v['id_dev'] . '" rev="async">' . $v['id_dev'] . ' - ' . $v['titre_dev'] . ' <small><b>' . $entreprise . '</b></small></a></li>';
	    } elseif ($v['nom_ent'] == NULL) {
		if ($ent != 'Sans entreprise') {
		    $ent = 'Sans entreprise';
		    $out .= '</ul><h2>' . $ent . '</h2><ul class="iArrow">';
		}
		$out .= '<li><a href="Devis.php?action=view&id_dev=' . $v['id_dev'] . '" rev="async">' . $v['id_dev'] . ' - ' . $v['titre_dev'] . ' <small><b>Aucune entreprise liée</b></small></a></li>';
	    }
	}
	if ($total > ($limit + $from))
	    $out .= '<li class="iMore" id="triEntrepriseDevisMore' . $from . '"><a href="Devis.php?action=triEntrepriseMore&from=' . ($limit + $from) . '&total=' . $total . '" rev="async">Plus de résultats</a></li>';
	$out .= '</ul>';
	$_SESSION['user']['LastLetterSearch'] = $ent;
	return $out;
    }

    static function tri_contact($value = array(), $limit = '', $from = '', $total = '') {
	$ent = $_SESSION['user']['LastLetterSearch'];
	$out = '<ul>';
	foreach ($value[1] as $k => $v) {
	    if ($v['nom_cont'] != NULL) {
		if ($ent != strtoupper($v['nom_cont']{0})) {
		    $ent = strtoupper($v['nom_cont']{0});
		    $out .= '</ul><h2>' . $ent . '</h2><ul class="iArrow">';
		}
		$entreprise = $v['nom_cont'];
		$out .= '<li><a href="Devis.php?action=view&id_dev=' . $v['id_dev'] . '" rev="async">' . $v['id_dev'] . ' - ' . $v['titre_dev'] . ' <small><b>' . $entreprise . '</b></small></a></li>';
	    } elseif ($v['nom_cont'] == NULL) {
		if ($ent != 'Sans contact') {
		    $ent = 'Sans contact';
		    $out .= '</ul><h2>' . $ent . '</h2><ul class="iArrow">';
		}
		$out .= '<li><a href="Devis.php?action=view&id_dev=' . $v['id_dev'] . '" rev="async">' . $v['id_dev'] . ' - ' . $v['titre_dev'] . ' <small><b>Aucun contact lié</b></small></a></li>';
	    }
	}
	if ($total > ($limit + $from))
	    $out .= '<li class="iMore" id="triContactDevisMore' . $from . '"><a href="Devis.php?action=triContactMore&from=' . ($limit + $from) . '&total=' . $total . '" rev="async">Plus de résultats</a></li>';
	$out .= '</ul>';
	$_SESSION['user']['LastLetterSearch'] = $ent;
	return $out;
    }

    static function afficherStats($datas = array()) {
	$out = '<div class="iPanel">';
	$out .= '<fieldset><legend>Devis</legend><ul><li>Nombre : ' . $datas[0] . '</li>';
	$out .= '<li>Valeur total : ' . $datas[5] . ' &euro;</li>';
	$out .= '<li>Prix moyen : ' . $datas[1] . ' €</li>';
	$out .= '<li>Prix médian : ' . $datas[2] . ' €</li>';
	$out .= '<li>Variance : ' . $datas[3] . '</li>';
	$out .= '<li>Écart type : ' . $datas[4] . ' €</li>';
	$out .= '<li>Coefficient de variation : ' . $datas[6] . '</li></ul></fieldset></div>';
	return $out;
    }

}

?>
