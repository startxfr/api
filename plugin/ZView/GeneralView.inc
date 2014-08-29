<?php


class generalView {

    public function resultNavigation($from,$limit,$total,$jsFunction = '') {

	$bouton = '';
	if($total == 0)
	    return '';
	elseif($limit != $total) {
	    $nbpage = ceil($total/$limit);

	    $pagecourrante = ($from/$limit)+1;
	    if($nbpage != 1 and $pagecourrante != 1) {
		$bouton .= '<a name="firstPage" onclick="'.$jsFunction.'(\''.$limit.'\',\'0\');" ><<</a>';
		$bouton .= '<a name="pageinf" onclick="'.$jsFunction.'(\''.$limit.'\',\''.(($pagecourrante-2)*$limit).'\');"><</a>';
	    }

	    for ($k=1; $k<=$nbpage; $k++) {
		$fromI = ($k-1)*$limit;
		if($k != $pagecourrante and ($nbpage <11 or (($k >($pagecourrante-3) and $k <($pagecourrante+3))  ) ))
		    $bouton .= '<a name="pageup" onclick="'.$jsFunction.'(\''.$limit.'\',\''.$fromI.'\');">'.$k.'</a>';

		elseif($k == $pagecourrante)
		    $bouton .= '<span class="link selected">'.$k.'</span>';
	    }
	    $fromI = $pagecourrante*$limit;
	    if($nbpage != 1 and $nbpage != $pagecourrante) {
		$bouton .= '<a name="pagesupp" onclick="'.$jsFunction.'(\''.$limit.'\',\''.$fromI.'\');">&gt;</a>';
		$bouton .= '<a name="lastPage" onclick="'.$jsFunction.'(\''.$limit.'\',\''.(($nbpage-1)*$limit).'\');">>></a>';
	    }

	    $bouton .= selectTag('nomselect', array('5' => '5', '15' => '15', '30' => '30', '50' => '50', '75' => '75', '100' => '100'), $limit, '', ' onChange="'.$jsFunction.'(this.value,\'0\');;" ');
	    $bouton .= '<a name="allpage" onclick="'.$jsFunction.'(\'ALL\');"><img title="Vue complète" alt="vuetotale" src="'.getStaticUrl('img').'prospec/viewmode-gofull.png"/> Passer en vue complète</a>';
	}
	else {
	    $bouton .= '<a name="parpage" onclick="'.$jsFunction.'(\'30\',\'0\');"><img title="Vue complète" alt="vuetotale" src="'.getStaticUrl('img').'prospec/viewmode-gofull.png"/> Passer en vue par page</a>';
	}
	return $bouton;
    }



    public function tagLinkOrderSwitch($title,$fieldName,$selectedOrderField,$selectedOrderSens,$limit,$jsFunction) {
	if($selectedOrderField == $fieldName)
	    if(strtoupper($selectedOrderSens) == 'ASC')
		$orderTag = '<a href="javascript:'.$jsFunction.'(\''.$limit.'\',0,\''.$fieldName.'\',\'DESC\');">'.$title.' <img style="vertical-align: middle" src="'.getStaticUrl('img').'orderASC.png" title="trier selon ce critère" alt="trier selon ce critère"/></a>';
	    else $orderTag = '<a href="javascript:'.$jsFunction.'(\''.$limit.'\',0,\''.$fieldName.'\');">'.$title.' <img style="vertical-align: middle" src="'.getStaticUrl('img').'orderDESC.png" title="trier selon ce critère" alt="trier selon ce critère"/></a>';
	else $orderTag = '<a href="javascript:'.$jsFunction.'(\''.$limit.'\',0,\''.$fieldName.'\');">'.$title.' <img style="vertical-align: middle" src="'.getStaticUrl('img').'orderNo.png" title="trier selon ce critère" alt="trier selon ce critère"/></a>';

	return $orderTag;
    }
    public function tagNoLinkOrderSwitch($title,$fieldName,$selectedOrderField,$selectedOrderSens,$limit,$jsFunction) {
	return $title;
    }

    public function resultNavigationTitle($from,$limit,$total,$titre) {
	$to = $from + $limit;
	$from++;
	if($to > $total) $to = $total;
	$tt = '';
	if($total == 0) {
	    if($titre == 'commande')  $tt = 'Aucune commande trouvée';
	    elseif($titre == 'commandeHisto')  $tt = 'Aucune commande récemment consultée';
	    elseif($titre == 'devis') $tt = 'Aucun devis trouvé';
	    elseif($titre == 'devisHisto') $tt = 'Aucun devis récemment consulté';
	    elseif($titre == 'affaire') $tt = 'Aucune affaire trouvée';
	    elseif($titre == 'affaireHisto') $tt = 'Aucune affaire récemment consultée';
	    elseif($titre == 'contact') $tt = 'Aucun contact trouvé';
	    elseif($titre == 'contactHisto') $tt = 'Aucun contact récemment consulté';
	    elseif($titre == 'projet') $tt = 'Aucun projet trouvé';
	    elseif($titre == 'projetHisto') $tt = 'Aucun projet récemment consulté';
	    elseif($titre == 'produit') $tt = 'Aucun produit trouvé';
	    elseif($titre == 'produitHisto') $tt = 'Aucun produit récemment consulté';
	    elseif($titre == 'facture') $tt = 'Aucune facture trouvée';
	    elseif($titre == 'factureHisto') $tt = 'Aucune facture récemment consultée';
	    elseif($titre == 'fournisseur') $tt = 'Aucun fournisseur trouvé';
	    elseif($titre == 'fournisseurHisto') $tt = 'Aucun fournisseur récemment consulté';
	    elseif($titre == 'factureFourn') $tt = 'Aucune facture fournisseur trouvée';
	    elseif($titre == 'factureFournHisto') $tt = 'Aucune facture fournisseur récemment consultée';
	    elseif($titre == 'pontComptable') $tt = 'Aucun fichier d\'export comptable trouvé';
	    elseif($titre == 'pontComptableHisto') $tt = 'Aucun fichier d\'export comptable récemment consulté';
	    $titre = imageTag(getStaticUrl('img').'prospec/result.png','recherche')." $tt";
	}
	elseif($total == 1) {
	    if($titre == 'commande') $tt = 'Affichage de la commande trouvée';
	    elseif($titre == 'commandeHisto') $tt = 'Affichage de la commande consultée';
	    elseif($titre == 'devis') $tt = 'Affichage du devis trouvé';
	    elseif($titre == 'affaire') $tt = 'Affichage de l\'affaire trouvé';
	    elseif($titre == 'contact') $tt = 'Affichage du contact trouvé';
	    elseif($titre == 'projet') $tt = 'Affichage du projet trouvé';
	    elseif($titre == 'produit') $tt = 'Affichage du produit trouvé';
	    elseif($titre == 'facture') $tt = 'Affichage de la facture trouvée';
	    elseif($titre == 'fournisseur') $tt = 'Affichage du fournisseur trouvé';
	    elseif($titre == 'factureFourn') $tt = 'Affichage de la facture fournisseur trouvée';
	    elseif($titre == 'pontComptable') $tt = 'Affichage du fichier d\'export comptable trouvé';
	    elseif($titre == 'devisHisto') $tt = 'Affichage du dernier devis consulté';
	    elseif($titre == 'affaireHisto') $tt = ' Affichage de la dernière affaire consultée';
	    elseif($titre == 'contactHisto') $tt = 'Affichage du dernier contact consulté';
	    elseif($titre == 'projetHisto') $tt = 'Affichage du dernier projet consulté';
	    elseif($titre == 'produitHisto') $tt = 'Affichage du dernier produit consulté';
	    elseif($titre == 'factureHisto') $tt = 'Affichage de la dernière facture consultée';
	    elseif($titre == 'fournisseurHisto') $tt = 'Affichage du dernier fournisseur consulté';
	    elseif($titre == 'factureFournHisto') $tt = 'Affichage de la dernière facture fournisseur consultée';
	    elseif($titre == 'pontComptableHisto') $tt = 'Affichage du dernier fichier d\'export comptable consulté';
	    $titre = imageTag(getStaticUrl('img').'prospec/result.png','recherche')." $tt";
	}
	else {
	    if($titre == 'commande') $tt = ' Affichage des commandes';
	    elseif($titre == 'devis') $tt = ' Affichage des devis';
	    elseif($titre == 'affaire') $tt = ' Affichage des affaires';
	    elseif($titre == 'contact') $tt = ' Affichage des contacts';
	    elseif($titre == 'projet') $tt = ' Affichage des projets';
	    elseif($titre == 'produit') $tt = ' Affichage des produits';
	    elseif($titre == 'facture') $tt = ' Affichage des factures';
	    elseif($titre == 'fournisseur') $tt = ' Affichage des fournisseurs';
	    elseif($titre == 'factureFourn') $tt = ' Affichage des factures fournisseurs';
	    elseif($titre == 'pontComptable') $tt = ' Affichage des fichiers d\'export comptable';
	    elseif($titre == 'devisHisto') $tt = 'Affichage des derniers devis consultés';
	    elseif($titre == 'affaireHisto') $tt = ' Affichage des dernières affaires consultées';
	    elseif($titre == 'commandeHisto') $tt = 'Affichage des dernières commandes consultées';
	    elseif($titre == 'contactHisto') $tt = 'Affichage des derniers contacts consultés';
	    elseif($titre == 'projetHisto') $tt = 'Affichage des derniers projets consultés';
	    elseif($titre == 'produitHisto') $tt = 'Affichage des derniers produits consultés';
	    elseif($titre == 'factureHisto') $tt = 'Affichage des dernières factures consultées';
	    elseif($titre == 'fournisseurHisto') $tt = 'Affichage des derniers fournisseurs consultés';
	    elseif($titre == 'factureFournHisto') $tt = 'Affichage des dernières factures fournisseur consultées';
	    elseif($titre == 'pontComptableHisto') $tt = 'Affichage des derniers  fichier d\'export comptable consultés';
	    if(preg_match('/Histo/i', $titre)) {
		$titre = $tt;
	    }
	    else {
		if($limit >= $total)
		    $titre = imageTag(getStaticUrl('img').'prospec/result.png','recherche')." $tt ($total au total)";
		else $titre = imageTag(getStaticUrl('img').'prospec/result.png','recherche')." $tt $from - $to ($total au total)";
	    }
	}
	return $titre;
    }

    /**
     * Méthode qui génère une popup d'envoi de Mail
     * @param string $type Précise quel type d'envoi est à effectuer
     * @param array $data Les données pré-insérable
     * @return string Le code HTML
     */
    public function popupSendMail($type = 'email', $data = array()) {
	if($type == "email") {
	    $fieldset = new ZunoFieldset("Envoi par e-mail");
	    $fieldset->ligneInput('Expéditeur', 'expediteur', $_SESSION['user']['fullnom'], ' readonly="readonly"');
	    $fieldset->ligneInputHidden('from', $_SESSION['user']['mail']);
	    $fieldset->ligneAutoComplete('Destinataire', 'mail', $data['mail'], $data['mail'], "listeMail");
	    $fieldset->ligneCheckBox('Me mettre en copie', 'cc');
	}
	if($type == "fax") {
	    $fieldset = new ZunoFieldset("Envoi par faximile");
	    $fieldset->ligneInput("Expéditeur", 'expediteur', $_SESSION['user']['fullnom'], ' readonly="readonly"');
	    $fieldset->ligneInput('Destinataire', 'fax', $data['fax']);
	}
	if($type == "courrier") {
	    $fieldset = new ZunoFieldset("Envoi par courrier");
	    $fieldset->ligneInput('Expdétieur', 'expediteur', $_SESSION['user']['fullnom'], ' readonly="readonly"');
	    $fieldset->ligneInput('Destinataire', 'destinataire', $data['destinataire']);
	    $fieldset->ligneInput('Adresse', 'add1', $data['add1']);
	    $fieldset->ligneInput("Complément", "add2", $data['add2']);
	    $fieldset->ligneInputCPVille('cp', $data['cp'], 'ville', $data['ville']);
	    $fieldset->ligneInput('Pays', 'pays', (array_key_exists('pays', $data)) ? $data['pays'] : 'France');
	}
	if($type == "all") {
	    $fieldset = new ZunoFieldset("Envoi");
	    $fieldset->ligneInput('Expéditeur', 'expediteur', $_SESSION['user']['fullnom'], ' readonly="readonly"');
	    $fieldset->ligneSelect('Type d\'envoi', 'typeE', 'mail', array('mail' => 'Par e-mail', 'fax'=> 'Par faximile', 'courrier' => 'Par courrier'), false);
	    //@todo Faire cette partie all
	}

	$fieldset2 = new ZunoFieldset("Message");
	$fieldset2->ligneInput("Sujet", 'sujet', $data['sujet']);
	$fieldset2->ligneTArea("Message", 'message', $data['message']);
	$fieldset2->ligneInput('Fichier joint', 'fichier', $data['fichier'], ' readonly="readonly"');

        $form = new ZunoForm('formSender', 'gnose/Send.php', "POST");

        $form->newInputHidden('action', 'send');
        if($type != "all")
            $form->newInputHidden('typeE', $type);
        if($data['partie'] != '')
            $form->newInputHidden('partie', $data['partie']);
        else
            $form->newInputHidden('path', $data['path']);
        if($data['dir_aff'] != '')
            $form->newInputHidden('dir_aff', $data['dir_aff']);
        $form->newBlock($fieldset->generateFieldset(), '50');
        $form->newBlock($fieldset2->generateFieldset(), '50');

        $rendu = new ZunoRenduHtml();
        $rendu->generateZBox('Envoi de document', 'Envoi de document', $form->generateForm(), $form->generateButtons('messageGnose', 'Annuler', 'Envoyer', 'popup2',''), 'IdPopupSendGnose');

        return $rendu->generateInterneRendu();
    }

}
?>
