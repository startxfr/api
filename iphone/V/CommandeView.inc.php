<?php
/**
 * Classe qui va générer tous les affichage en rapport avec les commandes.
 */
class commandeView
{

	/**
	 * Génération de la liste des résultats de la recherche
	 */
	static function searchResult($result, $from = 0, $limit = 5, $total = 0, $qTag = '')
	{
		if(is_array($result) and count($result) > 0)
		{
			$letter = $_SESSION['user']['LastLetterSearch'];
			$annee = $_SESSION['user']['annee'];
			foreach($result as $k => $v)
			{
				//On se balade dans le tableau de résultat de la recherche pour générer la liste.
				$tempmois = (substr($v['id_cmd'],2,2) < 50) ? substr($v['id_cmd'],2,2) : substr($v['id_cmd'],2,2)-50;
				$tempannee = substr($v['id_cmd'],0,2);
				if($letter != $tempmois || $annee != $tempannee) { $letter = $tempmois; $annee = $tempannee ; $list .= '</ul><h2>'.ucfirst(strftime("%B",strtotime('2008-'.$letter.'-27'))).' 20'.substr($v['id_cmd'],0,2).'</h2><ul class="iArrow">'; }
				elseif($from != 0) {$list .= '</ul><ul class="iArrow">';}
				$brc = ($v['nom_cont'] != '') ? '<br/>': '';
				//On génère la liste ici :
				$list .= '<li><a href="Commande.php?action=view&id_cmd='.$v['id_cmd'].'" rev="async"><em>'.$v['id_cmd'].' - '.$v['titre_cmd'].' </em><small><b>'.$v['nom_ent'].'</b>'.$brc.$v['civ_cont'].' '.$v['nom_cont'].' '.$v['prenom_cont'].'</small></a></li>';
			}
			$list = substr($list,5).'</ul>';
			if ($from == 0)
			{//on affiche le haut de la page juste la première fois.
			$out 	 = '<a href="#_MainMenu"  rel="action" class="iButton iBAction"><img src="Img/home.png" alt="Accueil" /></a>' ;
			}
			//l'affichage de la liste générée
			$out	.='<div class="iList">
						'.$list.'
					</div>';
			if($total > ($limit+$from))
			//on affiche le bouton : PLUS DE RESULTAT si besoin est.
			$out .= '<div class="iMore" id="searchResultCommandeMore'.$from.'"><a href="Commande.php?action=searchCommandeContinue&from='.($limit+$from).'&total='.$total.'" rev="async">Plus de résultats</a></div>';

			$_SESSION['user']['LastLetterSearch'] = $letter;
			$_SESSION['user']['annee'] = $annee;
			return $out;
		}
	}



	/**
	 * FICHE DE VISUALISATION
	 */

	/**
	 * Formulaire complet de visualisation d'une devis.
	 */
	static function view($value = array(),$mode = '', $fourn = array())
	{
		$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
		$sqlConn->makeRequeteFree("select id_fact, titre_fact from facture LEFT JOIN commande ON commande.id_cmd = facture.commande_fact where commande_fact LIKE '%".$value['id_cmd']."%' ORDER BY id_fact ASC;");
		$temp = $sqlConn->process2();
		$temp = $temp[1];
		if($temp == array())
		{
			$facture = '';
		}
		else
		{
			foreach($temp as $v)
			{
				$facture .= '<li><a href="Facture.php?action=view&id_fact='.$v['id_fact'].'" class="Facture" rev="async"><img src="../img/actualite/facture.png"/> Facture : '.$v['id_fact'].' ['.$v['titre_fact'].']</a></li>';
			}
		}
		$txTva = ($value['tva_cmd']/100 +1);
		$creation = '';
		if($value['daterecord_cmd'] != NULL)
			$creation = strftime("%A %d %B %G", strtotime($value['daterecord_cmd']));
		$somme	 = ($value['sommeHT_cmd'] != NULL) ? '<li>Total HT : <small>'.number_format($value['sommeHT_cmd'],2,',',' ').' &euro;</small></li>' : '';
		$sommeFHT	 = ($value['sommeFHT_cmd'] != NULL) ? '<li>Total FHT : <small>'.number_format($value['sommeFHT_cmd'],2,',',' ').' &euro;</small></li>' : '';
		$sommeTTC	 = ($value['sommeHT_cmd'] != NULL) ? '<li>Total TTC : <small>'.number_format(($value['sommeHT_cmd']*$txTva),2,',',' ').' &euro;</small></li>' : '';
		$entreprise	 = ($value['entreprise_cmd'] != NULL) ? '<li>Entreprise : '.contactEntrepriseView::contactLinkSimple($value).'</li>' : '';
		$contact	 = ($value['contact_cmd'] != NULL) ? '<li>Contact : '.contactParticulierView::contactLinkSimple($value).'</li>' : '';
		$contactachat	 = ($value['contact_achat_cmd'] != NULL && $value['contact_cmd'] != $value['contact_achat_cmd']) ? '<li>Acheteur : '.contactParticulierView::contactLinkSimple($value, 'achat').'</li>' : '';
		$commercial = ($value['commercial_cmd'] != NULL) ? '<li>Commercial : '.$value['nom'].' '.$value['prenom'].'</li>' : '';
		$devis = ($value['devis_cmd'] != NULL) ? '<li>'.devisView::devisLinkSimple($value).'</li>' : '';
		$marge = '<li>Marge : <small>'.number_format($value['sommeHT_cmd']-$value['sommeFHT_cmd'],2,',',' ').' &euro;</small></li>';

//On vient d'effectuer tout un tas de tests pour s'assurer  que l'on ne vas afficher que des blocks avec des choses dedans.

		if($mode == 'afterModif') {$linkHead = '<a href="Commande.php?action=view&id_cmd='.$value['id_cmd'].'"  rel="action" class="iButton iBAction"><img src="Img/config.png" alt="Recharger" /></a>';}
		else				  {$linkHead = '<a href="Commande.php?action=modifCommande&id_cmd='.$value["id_cmd"].'"  rev="async" rel="action" class="iButton iBAction"><img src="Img/edit.png" alt="Modifier" /></a>';}

		//On génère maintenant le rendu visuel.
		$out = $linkHead.'<div class="iPanel">
				<fieldset>
					<legend></legend>
					<ul>
						<li><strong>'.$value["titre_cmd"].
							'</strong></li>'.$devis.'

					</ul>
				</fieldset>
				<fieldset>
					<legend>Contacts</legend>
					<ul>
							'.$entreprise.'
							'.$contact.'
							'.$contactachat.'
					</ul>
				</fieldset>'.self::subBlockRessourcesLiees($value, $fourn, $facture).'
				<fieldset>
					<legend>Autres informations</legend>
					<ul>
						<li>Commande créé le : <small>'.$creation.'</small></li>
						<li>Statut : <small>'.$value['nom_stcmd'].'</small></li>
						'.$commercial.'
					</ul>
				</fieldset>
				<fieldset><legend>Offre commerciale</legend>
						<ul class="iArrow">
							'.$somme.'
							'.$sommeTTC.'
							'.$sommeFHT.'
							'.$marge.'
							<li><a href="Commande.php?action=produits&id_cmd='.$value["id_cmd"].'&tva='.$value["tva_cmd"].'" rev="async">Détail de l\'offre</a></li>
						</ul>
				</fieldset>'.self::subBlockAction($value).'
			</div>';
		return $out;
	}

	/**
	 * Fonction qui va générer l'affichage de la liste des produits.
	 */
	static function produits($value = array(), $id_cmd, $tva, $valide = '')
	{
		if($valide != 'valide')
		{$produits = '<a href="Commande.php?action=addProduit&id_cmd='.$id_cmd.'"  rev="async" rel="action" class="iButton iBAction"><img src="Img/add.png" alt="Ajouter" /></a><div class="iPanel">';}
		else {
			$produits = '<a href="#_MainMenu"  rel="action" class="iButton iBBack"><img src="Img/home.png" alt="Accueil" /></a><div class="iPanel">';
			$modif = '';
		}
		if($value == NULL)
		{
			$produits .= '';
			//Aucun produit à afficher, donc on ne génère pas le tableau.
		}
		else
		{
			$TVA=($tva != NULL || $tva != '') ? $tva : 19.6;
			$totalHT = 0;


			foreach($value as $v)
			{
				$total = $v['prix']*(1-$v['remise']/100)*$v['quantite'];
				$totalF = $v['prixF']*(1-$v['remiseF']/100)*$v['quantite'];
				$totalHT += $total;
				$totalFHT += $totalF;
				$margeintermediaire = $total - $totalF;
				$marge += $margeintermediaire;
				if(round($v['quantite'],0) != $v['quantite'])
					$v['quantite'] = number_format($v['quantite'],2,',',' ');
				else  $v['quantite'] = number_format($v['quantite'],0,',',' ');
				if(round($v['prix'],0) != $v['prix'])
					$v['prix'] = number_format($v['prix'],2,',',' ');
				else  $v['prix'] = number_format($v['prix'],0,',',' ');
				if(round($v['prixF'],0) != $v['prixF'])
					$v['prixF'] = number_format($v['prixF'],2,',',' ');
				else  $v['prixF'] = number_format($v['prixF'],0,',',' ');
				if(round($v['remise'],0) != $v['remise'])
					$v['remise'] = number_format($v['remise'],2,',',' ');
				else  $v['remise'] = number_format($v['remise'],0,',',' ');
				if(round($total,0) != $total)
					$total = number_format($total,2,',',' ');
				else  $total = number_format($total,0,',',' ');
				if(round($v['remiseF'],0) != $v['remiseF'])
					$v['remiseF'] = number_format($v['remiseF'],2,',',' ');
				else  $v['remiseF'] = number_format($v['remiseF'],0,',',' ');
				if(round($totalF,0) != $totalF)
					$totalF = number_format($totalF,2,',',' ');
				else  $totalF = number_format($totalF,0,',',' ');
				if($v['stock_prod'] != null && $v['id_ent'] != '')
				{
					$stock = '<li><label>En stock : </label>'.$v['stock_prod'].'</li>';
				}
				else
				{
					$stock = '';
				}
				if($valide != 'valide')
				{
					$modif = '<span style="float: right"><a style="margin: 0px; margin-top: -7px" href="Commande.php?action=modifProduit&id_cmd='.$id_cmd.'&id_prod='.urlencode($v['id_produit']).'" rev="async" ><img src="Img/edit.png" title="Modifier"/></a></span>';
				}
				$out.='<fieldset>'.$modif.'<legend>Produit '.$v['id_produit'].'</legend><ul>';
				$out .='<li><label>Référence : </label>'.$v['id_produit'].'</li>';
				$out .='<li><label>Libellé : </label>'.$v['desc'].'</li>';
				if($v['nom_prodfam'] != '')
					$out .='<li><label>Famille : </label>'.$v['nom_prodfam'].'</li>';
				if($v['id_ent'] != '')
					$out .='<li><label>Fournisseur : </label>'.$v['nom_ent'].'</li>';
				$out .='<li><label>Quantité client : </label>'.$v['quantite'].'</li>';
				$out .='<li><label>Quantité commandée : </label>'.$v['quantite_cmd'].'</li>';
				$out .= $stock;
				$out .= '<li><label>Prix fournisseur : </label>'.$v['prixF'].' &euro;</li>';
				$out .='<li><label>Remise Fournisseur : </label>'.$v['remiseF'].'%</li>';
				$out .='<li><label>Total Fournisseur : </label>'.$totalF.' &euro;</li></ul>';
				$out .='<ul><li><label>Remise Client : </label>'.$v['remise'].'%</li>';
				$out .='<li><label>Total Client : </label>'.$total.' &euro;</li>';
				$out .='</ul>';
				$out .='<ul><li><label>Marge : </label>'.$margeintermediaire.' &euro </li></ul></fieldset>';
			}
			$TTC = (1+$TVA/100)*$totalHT;
			$out.='<fieldset><legend>Total de l\'offre '.$v['id_cmd'].'</legend><ul>';
			$out .='<li><label>Total HT : </label>'.$totalHT.' &euro;</li>';
			$out .='<li><label>Taux TVA : </label>'.number_format($TVA,1,',',' ').'%</li>';
			$out .='<li><label>Total TTC : </label>'.$TTC.' &euro;</li>';
			$out .='</ul>';
			$out .='<ul><li><label>Total FHT : </label>'.$totalFHT.' &euro;</li>';
			$out .='<li><label>Marge totale : </label>'.$marge.' &euro;</li></ul></fieldset>';
			$produits .= $out;
		}
		return $produits;//Sortie du résultat avec les liens pour ajout ou modification d'un produit.
	}

	/**
	 * Fonction générant le rendu visuel du formulaire de modification d'un produit.
	 */
	static function modifProduits($value = array(), $id_cmd, $tva = 0)
	{
		$out = '<a href="#"  onclick="return WA.Submit(\'formModifProduitCommande\',null,event)" rel="action" class="iButton iBAction"><img src="Img/save.png" alt="Enregistrer" /></a>
				<form id="formModifProduitCommande" action="Commande.php?action=doModifProduit&tva='.$tva.'&id_cmd='.$id_cmd.'" onsubmit="return WA.Submit(this,null,event)">
				<div class="iPanel">
					'.self::blockProduitsModif($value, $id_cmd).'
					<fieldset>
						<a class="BigButtonValidLeft" href="#" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler"></a>
						<a class="BigButtonValidRight" href="#" onclick="return WA.Submit(\'formModifProduitCommande\',null,event)"><img src="Img/big.valider.png" alt="Valider"></a>
					</fieldset>
				</div>
				</form>';
		$out .= '<form id="formModifProduitCommandeCache" action="Commande.php?action=suppProduit&tva='.$tva.'&id_cmd='.$id_cmd.'" onsubmit="return WA.Submit(this,null,event)">' .
				'<div style="display:none"><input id="id_produit_hidden_commande" type="hidden" name="id_produit" value=0 />' .
				'<a id="valid_suppProduitcommande" href="#" onclick="return WA.Submit(\'formModifProduitCommandeCache\',null,event)">Lien suppression caché</a>' .
				'</div></form>';
		return $out;
	}
	/**
	 * Fonction générant le rendu visuel du formulaire d'ajout d'un produit.
	 */
	static function addProduits($value = array(), $id_cmd, $tva = 0)
	{
		$out = '<a href="#"  onclick="return WA.Submit(\'formAddProduitCommande\',null,event)" rel="action" class="iButton iBAction"><img src="Img/save.png" alt="Enregistrer" /></a>
				<form id="formAddProduitCommande" action="Commande.php?action=doAddProduit&tva='.$tva.'&id_cmd='.$id_cmd.'" onsubmit="return WA.Submit(this,null,event)">
				<div class="iPanel">
					'.self::blockProduitsModif(array(), $id_cmd, "on_ajoute").'
					<fieldset>
						<a class="BigButtonValidLeft" href="#" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler"></a>
						<a class="BigButtonValidRight" href="#" onclick="return WA.Submit(\'formAddProduitCommande\',null,event)"><img src="Img/big.valider.png" alt="Valider"></a>
					</fieldset>
				</div>
				</form>';
		return $out;
	}
	/**
	 * Fonction BLOCK qui inclu tout ce qu'il faut pour la modification des produits.
	 */
	static function blockProduitsModif($value = array(), $id_cmd = NULL, $onfékoi = 'rien')
	{
		$_SESSION['idcommande']=$id_cmd;
		$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
		$outJS = 'fournisseur = new Array();'."\n";
		$outJS .= 'totalBrut = new Array();'."\n";
		$list = array();
		$sqlConn->makeRequeteFree("select * from commande_produit dp left join produit_fournisseur pf on pf.produit_id = dp.id_produit left join fournisseur ON fournisseur.id_fourn = pf.fournisseur_id left join entreprise e ON e.id_ent = fournisseur.entreprise_fourn where dp.id_produit = '".trim($value[0]['id_produit'])."' and dp.id_commande = '".trim($id_cmd)."';");
		$temp = $sqlConn->process2();
		$temp=$temp[1];
		$prix = ($value[0]['prix_prod'] == NULL ) ? $value[0]['prix'] : $value[0]['prix_prod'];
		$totalBrut = $prix*$value[0]['quantite'];
		if ($temp[0]['fournisseur_id'] == NULL )
		{
			$fourn = '';
			$remise = '';
			$total = $totalBrut;
		}
		else
		{
			$outJS .= 'fournisseur["'.$value[0]['id_prod'].'"]=new Array();'."\n";
			foreach($temp as $kk => $vv)
			{	$list[$vv['fournisseur_id']] = $vv['nom_ent'].' ('.$vv['remiseF'].'%)';
				$outJS .= 'fournisseur["'.$value[0]['id_prod'].'"]["'.$vv['fournisseur_id'].'"]='.$vv['remiseF'].";\n";
				$outJS .= 'fournisseur["'.$value[0]['id_prod'].'"]["'.$vv['fournisseur_id'].'P"]='.$vv['prixF'].";\n";
			}
		}
		$outJS .= 'totalBrut["'.$value[0]['id_prod'].'"]='.$prix.";\n";
		$totalFourn = number_format(($value[0]['quantite']*$value[0]['prixF']*(1-$value[0]['remiseF']/100)),2,'.',' ');
		$id = devisView::inputAjaxProduit('id_produitC',$value[0]['id_produit'],'Référence : ',false, 'oui');
		$desc = HtmlFormIphone::TextareaLabel('desc', $value[0]['desc'],' id="id_produitCdesc" ', 'Libellé : ');
		$prixV = HtmlFormIphone::InputLabel('prix', $value[0]['prix'], 'Px V : ', 'id="id_produitCprix" onchange="prixvoncommand(\'id_produitC\', this.value)"');
		$qtte = HtmlFormIphone::InputLabel('quantite', $value[0]['quantite'], 'Qté client : ', 'id="id_produitCquantite" onchange="qttoncommand(\'id_produitC\', this.value)"');
		$qtteCmd = HtmlFormIphone::InputLabel('quantite_cmd', $value[0]['quantite_cmd'], 'Qté commandée : ', 'id="id_produitCquantite" onchange="qttoncommand(\'id_produitC\', this.value)"');
		$remiseV = HtmlFormIphone::InputLabel('remise', $value[0]['remise'], 'Remise V : ', 'id="id_produitCremiseV" onchange="remisevoncommand(\'id_produitC\', this.value)"');
		$fournisseur = HtmlFormIphone::Select('fournisseur',$list ,$value[0]['fournisseur'], false, 'id="id_produitCfourn" onchange="fournoncommand(\'id_produitC\', this.value)"');
		$remiseF = HtmlFormIphone::InputLabel('remiseF', $value[0]['remiseF'], 'Remise F : ', 'id="id_produitCremiseF" onchange="remisefoncommand(\'id_produitC\', this.value)"');
		$prixtotal = $value[0]['prix']*(1-$value[0]['remise']/100)*$value[0]['quantite'];
		$prixF = ($value[0]['prixF'] == NULL) ? '<li id="id_produitCprixF">Px F : 0 €</li>' : '<li id="id_produitCprixF">Px F : '.$value[0]['prixF'].' €</li>';
		$totalF = '<li id="id_produitCtotalF">TT F : '.$totalFourn.' &euro;</li>';
		$totalV = '<li id="id_produitCtotalV">TT V : '.$prixtotal.' &euro;</li>';
		$marge = '<li id="id_produitCmarge">Marge : '.number_format(($prixtotal-$totalFourn),2,',','  ').' &euro;</li>';
		$stock = 'En stock : '.$value[0]['stock_prod'];
		if($onfékoi == 'rien') {$out.='<fieldset><span class="smallActionButton"><a id="supprimer_produitcommmande" onclick="confirmBeforeClick(\'valid_suppProduit\', \''.$value[0]['id_produit'].'\', \'commande\')"><img src="Img/delete.png" title="Supprimer"/></a></span><legend  class="smallActionLegend"> Produit : '.$value[0]['id_produit'].'</legend>';}
		else {$out .='<fieldset><legend>Produit : </legend>';}
		$out .='<ul><li>'.$id.'</li><li>'.$desc.'</li><li>'.$qtte.'</li><li>'.$qtteCmd.'</li><li>'.$stock.'</li></ul></fieldset>';
		$out .='<fieldset><legend>Fournisseur</legend><ul><li>'.$fournisseur.'</li>'.$prixF.'<input type="hidden" value="'.$value[0]['prixF'].'" name="prixF" id="id_produitCprixF_hidden" /><li>'.$remiseF.'</li></ul>';
		$out .='<ul><li>'.$prixV.'</li><li>'.$remiseV.'</li></ul>';
		$out .='<ul>'.$totalF.$totalV.$marge.'</ul>';
		$out .='</fieldset>';
		$out .='<script type="text/javascript">'.$outJS.'</script>';
		return $out;
	}
	/**
	 * Fonction qui génère un "Lien simple" vers un commande.
	 */
	static function commandeLinkSimple($value = array())
	{
		return '<a href="Commande.php?action=view&id_cmd='.$value['id_cmd'].'" class="Commande" rev="async"><img src="../img/actualite/commande.png"/> Commande '.$value['id_cmd'].' '.$value['titre_cmd'].'</a>';
	}
	/**
	 * Fonction assurant l'affichage du formulaire de modification d'un commande.
	 */
	static function modif($value = array(),$onError = array(),$errorMess = '',$id_cmd = '')
	{
		$error   = ($errorMess != '') ? '<div class="err">'.$errorMess.'</div>' : '';
		$out 	 = '<a href="#"  onclick="return WA.Submit(\'formModifCommande\',null,event)" rel="action" class="iButton iBAction"><img src="Img/save.png" alt="Enregistrer" /></a>
				<form id="formModifCommande" action="Commande.php?action=doModifCommande&id_cmd='.$id_cmd.'" onsubmit="return WA.Submit(this,null,event)">
				'.$error.'
				<div class="iPanel">
					'.self::blockModif($value,$onError).'
				</div>
				</form>';
		return $out;
	}
	/**
	 * Fonction BLOCK qui va générer le rendu visuel du formulaire de modification d'un commande.
	 */
	static function blockModif($value = array(), $onError = array())
	{
		$out = self::subBlockNomCommande($value, $onError);
		$out .= self::subBlockContacts($value, $onError);
		$out .= self::subBlockAdresse($value, $onError);
		$out .= self::subBlockCommercial($value, $onError);
		$out .= self::subBlockTVA($value, $onError);
		$out .= self::subBlockReglement($value, $onError);
		if($value['supprimable'] == '0')
		$out .='<a href="Commande.php?action=suppCommande&id_cmd='.$value["id_cmd"].'" rev="async" class="redButton"><span>Supprimer cette Commande</span></a>';
		$out .= '<fieldset>
					<a href="#" class="BigButtonValidLeft" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler" /></a>
					<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formModifCommande\', null, event)"><img src="Img/big.valider.png" alt="Valider" /></a>
				</fieldset>';
		return $out;
	}
	static function subBlockTVA($value = array(), $onError = array())
	{
		$list = array('0' => '0 %', '5.5' => '5,5 %', '19.6' => '19,6 %');
		$tva = HtmlFormIphone::Select('tva_cmd', $list, $value['tva_cmd'], false);
		$out = '<fieldset><legend>Taux T.V.A</legend>' .
				'<ul><li>'.$tva.
				'</li></ul></fieldset>';
		return $out;
	}
	/**
	 * Fonction qui va géré l'affichage des actualités s'il y en a.
	 */
	static function subBlockRessourcesLiees($value = array(), $fourn = array(), $facture = '')
	{
		$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
		$sqlConn->makeRequeteFree("select count(id) as C FROM actualite WHERE id_cmd = '".$value['id_cmd']."'");
		$temp = $sqlConn->process2();
		$totalActu = $temp[1][0]['C'];
		foreach($fourn as $v)
		{
			if (file_exists($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoAffaire']['dir.affaire'].$value['dir_aff'].$value['id_cmd'].'F-'.$v['fournisseur'].'.pdf'))
			$outLi   .= "<li><a href=\"../pegase/Commande.php?id_commande=".$value['id_cmd']."&action=VoirBDCF&fourn=".$v['fournisseur']."\" target=\"_blank\">".imageTag('../img/prospec/commande.pdf.png','PDF').' '.$value['id_cmd']."F-".$v['fournisseur'].".pdf</a></li>";
		}
		if (file_exists($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoAffaire']['dir.affaire'].$value['dir_aff'].$value['id_cmd'].'C.pdf'))
			$outLi   .= "<li><a href=\"../pegase/Commande.php?id_commande=".$value['id_cmd']."&action=VoirBDCC\" target=\"_blank\">".imageTag('../img/prospec/commande.pdf.png','PDF').' '.$value['id_cmd']."C.pdf</a></li>";
		if (file_exists($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoAffaire']['dir.affaire'].$value['dir_aff'].substr($value['id_cmd'],0,-1).'L.pdf'))
			$outLi   .= "<li><a href=\"../pegase/Commande.php?id_commande=".$value['id_cmd']."&action=VoirBDL\" target=\"_blank\">".imageTag('../img/prospec/commande.pdf.png','PDF').' '.substr($value['id_cmd'],0,-1)."L.pdf</a></li>";
		if (file_exists($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoAffaire']['dir.affaire'].$value['dir_aff'].'RapportIntervention.'.$value['id_cmd'].'.pdf'))
			$outLi   .= "<li><a href=\"../pegase/Commande.php?id_commande=".$value['id_cmd']."&action=VoirRI\" target=\"_blank\">".imageTag('../img/prospec/commande.pdf.png','PDF').' '.substr($value['id_cmd'],0,-2)."RI.pdf</a></li>";

		//Récupération des données
		$out = '<fieldset>
					<legend>Ressources Liées</legend>
					<ul class="iArrow">
						<li><a rev="async" href="Actualite.php?action=viewCommande&amp;id_cmd='.$value['id_cmd'].'"><img src="Img/actualite.png"/> '.$totalActu.' Actualités</a></li>
						'.$facture.$outLi.'
					</ul>
				</fieldset>';
		return $out;//Génération de l'affichage.
	}
	/**
	 * Fonction sous BLOCK qui gère l'affichage du formulaire pour les contacts.
	 */
	static function subBlockContacts($value = array(), $onError = array())
	{
		$particulier = contactParticulierView::inputAjaxContact('contact_cmd',$value['contact_cmd'],'Contact : ',false);
		$particulierERR	= (in_array('contact_cmd',$onError)) ? '<span class="iFormErr"/>' : '';
		$acheteur = contactParticulierView::inputAjaxContact('contact_achat_cmd',$value['contact_achat_cmd'],'Acheteur : ',true);
		$acheteurERR	= (in_array('contact_achat_cmd',$onError)) ? '<span class="iFormErr"/>' : '';

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
	 */
	static function subBlockCommercial($value = array(), $onError = array())
	{
		$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
			$sqlConn->makeRequeteFree("select login, nom, prenom, civ from user order by nom;");
			$temp = $sqlConn->process2();
			$temp=$temp[1];
			foreach($temp as $k => $v)
			{
				$userList[$v['login']] = $v['civ'].' '.$v['prenom'].' '.$v['nom'];
			}

		$valuecommercial = ($value['commercial_cmd'] != NULL) ? $value['commercial_cmd'] : $_SESSION['user']['id'];
		$commercial = HtmlFormIphone::Select('commercial_cmd',$userList,$valuecommercial, false);
		$commercialERR	= (in_array('commercial_cmd',$onError)) ? '<span class="iFormErr"/>' : '';

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
	 */
	static function subBlockDevis($value = array(), $onError = array())
	{
		$devis = devisView::inputAjaxDevis('devis_cmd',$value['devis_cmd'],'Devis : ',false);
		$devisERR	= (in_array('devis_cmd',$onError)) ? '<span class="iFormErr"/>' : '';
		$out = '<fieldset>
					<legend>Devis</legend>
					<ul>
						<li>'.$devis.$devisERR.'</li>

					</ul>
				</fieldset>';
		return $out;
	}
	/**
	 * Fonction sous BLOCK qui gère l'affichage pour les entrées d'une adresse.
	 */
	static function subBlockAdresse($value = array(), $onError = array())
	{
		$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
			$sqlConn->makeRequeteFree("select id_pays, nom_pays from ref_pays;");
			$temp = $sqlConn->process2();
			$temp=$temp[1];
			foreach($temp as $k => $v)
			{
				$countryList[$v['id_pays']] = $v['nom_pays'];
			}
		$nom = HtmlFormIphone::InputLabel('nomdelivery_cmd',$value['nomdelivery_cmd'],'Nom : ');
		$add1 = HtmlFormIphone::InputLabel('adressedelivery_cmd',$value['adressedelivery_cmd'],'Adresse : ');
		$add2 = HtmlFormIphone::InputLabel('adresse1delivery_cmd',$value['adresse1delivery_cmd'],'Complément : ');
		$cp = HtmlFormIphone::InputLabel('cpdelivery_cmd',$value['cpdelivery_cmd'],'CP : ');
		$ville = HtmlFormIphone::InputLabel('villedelivery_cmd',$value['villedelivery_cmd'],'Ville : ');
		$pays 	= HtmlFormIphone::Select('paysdelivery_cmd',$countryList,$value['paysdelivery_cmd'],false);

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
	 */
	static function subBlockNomCommande($value = array(), $onError = array())
	{
		$nom = HtmlFormIphone::InputLabel('titre_cmd',$value['titre_cmd'],'Nom : ');
		$out = '<fieldset>
					<legend>Commande</legend>
					<ul>
						<li>'.$nom.'</li>

					</ul>
				</fieldset>';
		return $out;
	}

	static function subBlockReglement($value = array(), $onError = array())
	{
		$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
			$sqlConn->makeRequeteFree("select id_modereg, nom_modereg from ref_modereglement;");
			$temp = $sqlConn->process2();
			$temp=$temp[1];
			foreach($temp as $k => $v)
				$list[$v['id_modereg']] = $v['nom_modereg'];
		$mr 	= HtmlFormIphone::SelectLabel('modereglement_cmd',$list,$value['modereglement_cmd'],'Mode : ',true);
		$mrERR= (in_array('modereglement_cmd',$onError)) ? '<span class="iFormErr"/>' : '';
			$sqlConn->makeRequeteFree("select id_condreg, nom_condreg from ref_condireglement;");
			$temp = $sqlConn->process2();
			$temp=$temp[1];
			foreach($temp as $k => $v)
				$list[$v['id_condreg']] = $v['nom_condreg'];
		$cr 	= HtmlFormIphone::SelectLabel('condireglement_cmd',$list,$value['condireglement_cmd'],'Conditions : ',true);
		$crERR= (in_array('condireglement_cmd',$onError)) ? '<span class="iFormErr"/>' : '';

		$out = '<fieldset><legend>Règlement</legend>
				<ul><li>'.$mr.$mrERR.'</li><li>'
					.$cr.$crERR.'</li></ul></fieldset>';
		return $out;
	}

	static function subBlockAction($value = array())
	{
		$out = '<fieldset>
				<legend>Actions</legend>
				<ul class="iArrow">';
		if ($value['status_cmd'] <= 6)
			$out.= '<li><a rev="async" href="Commande.php?action=addProduit&amp;id_cmd='.$value['id_cmd'].'"><img src="../img/prospec/devis.addProduct.png"/> Ajouter un produit</a></li>';
		if ($value['sommeHT_cmd'] > 0)
		{
			$out.= '<li><a rev="async" href="Commande.php?action=voir&amp;id_cmd='.$value['id_cmd'].'"><img src="../img/prospec/commande.pdf.png"/> Voir le PDF</a></li>';
			if ($value['status_cmd'] <= 4)
			{
				$preFixRec = ($value['status_cmd'] >= 3) ? 'Re-e' : 'E';
				$preFixSend = ($value['status_cmd'] >= 4) ? 'Re-e' : 'E';
				$out.= '<li><a rev="async" href="Commande.php?action=rec&amp;id_cmd='.$value['id_cmd'].'"><img src="../img/prospec/commande.record.png"/> '.$preFixRec.'nregistrer</a></li>';
				$out.= '<li><a rev="async" href="Commande.php?action=recsend&amp;id_cmd='.$value['id_cmd'].'"><img src="../img/prospec/devis.recsend.png"/> '.$preFixRec.'nregistrer & '.$preFixSend.'nvoyer</a></li>';
				if ($value['status_cmd'] >= 3)
					$out.= '<li><a rev="async" href="Commande.php?action=send&amp;id_cmd='.$value['id_cmd'].'"><img src="../img/prospec/devis.send.png"/> '.$preFixSend.'nvoyer</a></li>';
			}
			if ( ($value['status_cmd'] == 4)and
			     $_SESSION['user']['id'] == $value['commercial_cmd'] )
			{
				$out.= '<li><a rev="async" href="Commande.php?action=valide&id_cmd='.$value['id_cmd'].'"><img src="../img/prospec/commande.valid.png"/> Commande validée</a></li>';
				$out.= '<li><a rev="async" href="Commande.php?action=recep&id_cmd='.$value['id_cmd'].'"><img src="../img/prospec/commande.valid.png"/> Commande réceptionnée</a></li>';
			}
		}
		if (($value['sommeHT_cmd'] > 0) && ($value['entreprise_cmd'] != NULL))
		$out .= '<li><a rev="async" href="Facture.php?action=addFacture&commande_fact='.$value['id_cmd'].'"><img src="../img/prospec/facture.create.png" /> Créer une facture liée</a></li>';
		$out.= '	</ul>
			</fieldset>';
		return $out;
	}


	/**
	 * Fonction qui gère l'affichage lors de la suppression d'un commande.
	 */
	static function delete($value = array())
	{
		if ($value["id_cmd"] == 0)
		{
			$out='<a href="#_MainMenu"  rel="action" class="iButton iBBack"><img src="Img/home.png" alt="Accueil" /></a>
			  <div class="iPanel">
			  <div class="err">
			  		<strong> Commande supprimé ! </strong>
				</div>
						';
				return $out;
		}
		$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
		$requete = $sqlConn->makeRequeteFree("select count(*) as total from facture where commande_fact = '".$value['id_cmd']."' ; ");
		$requete = $sqlConn->process2();
		$facture = '';
		if($requete[1][0]['total'] >= 1)
		{$facture = '<strong> Cela supprimera également '.$requete[1][0]['total'].' facture(s) liée(s)</strong>';}
		$creation = '';
		if($value['daterecord_cmd'] != NULL)
			$creation = strftime("%A %d %B %G", strtotime($value['daterecord_cmd']));
		$somme	 = ($value['sommeHT_cmd'] != NULL) ? '<li>Somme total HT : <small>'.number_format($value['sommeHT_cmd'],2,',',' ').' &euro;</small></li>' : '';
		$entreprise	 = ($value['entreprise_cmd'] != NULL) ? '<li>Entreprise : '.contactEntrepriseView::contactLinkSimple($value).'</li>' : '';
		$contact	 = ($value['contact_cmd'] != NULL) ? '<li>Contact : '.contactParticulierView::contactLinkSimple($value).'</li>' : '';
		$contactachat	 = ($value['contact_achat_cmd'] != NULL) ? '<li>Acheteur : '.contactParticulierView::contactLinkSimple($value, 'achat').'</li>' : '';
		$commercial = ($value['commercial_cmd'] != NULL) ? '<li>Commercial : '.$value['nom'].' '.$value['prenom'].'</li>' : '';
		$devis = ($value['devis_cmd'] != NULL) ? '<li>Devis liée : '.devisView::devisLinkSimple($value).'</li>' : '';
		//On vient d'effectuer tout un tas de tests pour s'assurer  que l'on ne vas afficher que des blocks avec des choses dedans.

		$linkHead = '<a href="Commande.php?action=doDeleteCommande&id_cmd='.$value["id_cmd"].'&facture='.$requete[1][0]['total'].'"  rev="async" rel="action" class="iButton iBAction"><img src="Img/remove.png" alt="Supprimer" /></a>';
		//On génère maintenant le rendu visuel.
		$out = $linkHead.'<div class="iPanel">' .
				'<div class="err">
			  		<strong> Êtes vous sur de vouloir supprimer cette commande ? </strong>
			  		'.$facture.'
				</div>
				<fieldset>
					<legend></legend>
					<ul>
						<li><strong>'.$value["titre_cmd"].
							'</strong></li>'.$devis.'

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
						<ul class="iArrow"><li><a href="Commande.php?action=produits&id_cmd='.$value["id_cmd"].'&tva='.$value["tauxTVA_ent"].'" rev="async">Voir les produits</a></li></ul>
				</fieldset>
			</div>';
		return $out;
	}
	/**
	 * Fonction qui gère l'affichage lors de l'ajout d'un commande.
	 */
	static function addPre($value = array(),$onError = array(),$errorMess = '')
	{
		$error   = ($errorMess != '') ? '<div class="err">'.$errorMess.'</div>' : '';
		$out 	 = '<a href="#"  onclick="return WA.Submit(\'formAddPreCommande\',null,event)" rel="action" class="iButton iBAction"><img src="Img/save.png" alt="Enregistrer" /></a>
				<form id="formAddPreCommande" action="Commande.php?action=addCommande" onsubmit="return WA.Submit(this,null,event)">
				'.$error.'
				<div class="iPanel">
					'.self::blockAddPre($value,$onError).'
				</div>
				</form>';
		return $out;
	}

	static function add($value = array(), $onError = array(), $errorMess = '')
	{
		$error   = ($errorMess != '') ? '<div class="err">'.$errorMess.'</div>' : '';
		$out 	 = '<a href="#"  onclick="return WA.Submit(\'formAddCommande\',null,event)" rel="action" class="iButton iBAction"><img src="Img/save.png" alt="Enregistrer" /></a>
				<form id="formAddCommande" action="Commande.php?action=doAddCommande" onsubmit="return WA.Submit(this,null,event)">
				'.$error.'
				<div class="iPanel">
					'.self::blockAdd($value,$onError).'
				</div>
				</form>';
		return $out;

	}
	/**
	 * Fonction BLOCK pour l'ajout d'une commande.
	 */
	static function blockAddPre($value = array(), $onError = array())
	{

		$out = self::subBlockDevis($value, $onError);
		return $out;
	}

	static function blockAdd($value = array(), $onError = array())
	{
		$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
		$numero = 1;
		$default['modereglement_cmd']= '3';
		$default['condireglement_cmd']= '4';
		$devis = $value[0]['id_devis'];
		$out = '<input type="hidden" name="devis_cmd" id="devis_cmd" value="'.$value[0]['id_devis'].'"/>';
		$outJS = 'fournisseur = new Array();'."\n";
		foreach ($value as $k => $v)
		{
			$list = array();
			$sqlConn->makeRequeteFree("select * from devis_produit dp left join produit_fournisseur pf on pf.produit_id = dp.id_produit left join fournisseur ON fournisseur.id_fourn = pf.fournisseur_id left join entreprise e ON e.id_ent = fournisseur.entreprise_fourn where dp.id_produit = '".trim($v['id_produit'])."' and dp.id_devis = '".trim($devis)."';");
			$temp = $sqlConn->process2();
			$temp=$temp[1];
			$prix = ($v['prix_prod'] == NULL ) ? $v['prix'] : $v['prix_prod'];
			
			if ($temp[0]['fournisseur_id'] == NULL )
			{
				$fourn = '';
				$remise = '';
				$champprix = '<li>Prix : '.$prix.' €</li>';
				$total = $prix*$v['quantite'];
				$stock = '';
			}
			else
			{
				$outJS .= 'fournisseur["'.$v['id_prod'].'"]=new Array();'."\n";
				foreach($temp as $kk => $vv)
				{	
					$list[$vv['fournisseur_id']] = $vv['nom_ent'].' ('.$vv['remiseF'].'%)';
					$outJS .= 'fournisseur["'.$v['id_prod'].'"]["'.$vv['fournisseur_id'].'"]='.$vv['remiseF'].";\n";
					$outJS .= 'fournisseur["'.$v['id_prod'].'"]["'.$vv['fournisseur_id'].'P"]='.$vv['prixF'].";\n";				
				}
			$totalBrut = $temp[0]['prixF']*$v['quantite'];
			$fourn = HtmlFormIphone::SelectLabel('fournisseur['.$v['id_prod'].']',$list,'','Fournisseur', false, 'id="FournisseurCommande'.$v['id_prod'].'" onchange="commandChangePrice(\''.$v['id_prod'].'\', this.value)"');
			$fourn = '<li>'.$fourn.'</li>';
			$remise = HtmlFormIphone::InputLabel('remiseF['.$v['id_prod'].']',$temp[0]['remiseF'], 'Remise : ', 'id="RemiseCommande'.$v['id_prod'].'" onchange="commandChangeRemise(\''.$v['id_prod'].'\', this.value)"');
			$remise = '<li>'.$remise.'</li>';
			$remise .= '<input type="hidden" name="prixF['.$v['id_prod'].']" id="PrixCommande'.$v['id_prod'].'_hidden" value="'.$temp[0]['prixF'].'" />';
			$champprix = '<li id="PrixCommande'.$v['id_prod'].'">Prix : '.$temp[0]['prixF'].' €</li>';
			$total = $totalBrut*(1-$temp[0]['remiseF']/100);
			$stock = '<li>Stock : '.$v['stock_prod'].'</li>';
			}
			$out .= '<fieldset><legend>Produit N° '.$numero.'</legend><ul><li>Produit : '.$v['id_produit'].'</li>
				<li>Desc : '.$v['desc'].'</li>'
				.$fourn.$champprix.
				'<li>'.HtmlFormIphone::InputLabel('quantite['.$v['id_prod'].']',$v['quantite'], 'Quantité : ', 'id="QuantiteCommande'.$v['id_prod'].'" onchange="commandChangeQtt(\''.$v['id_prod'].'\', this.value)"').'</li>
				'.$stock.$remise.'
				<li id="TotalCommande'.$v['id_prod'].'">Total : '.$total.' &euro;</li></ul></fieldset>';
			$numero ++;
		}
		$out .= '<script type="text/javascript">'.$outJS.'</script>';
		$out .= self::subBlockReglement($default);
		$out .= '<fieldset><ul><li>'.HtmlFormIphone::InputLabel('BDCclient', '', 'BDC : ').'</li></ul></fieldset>';
		$out .= '<fieldset>
					<a href="#" class="BigButtonValidLeft" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler" /></a>
					<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formAddCommande\', null, event)"><img src="Img/big.valider.png" alt="Valider" /></a>
				</fieldset>';
		return $out;
	}

	/**
	 * Fonction de gestion de l'affichage de la fiche pour clonage
	 */
	static function cloner($value = array())
	{
		return '<div class="iPanel"><br/><br/>
				<div class="msg"><br/>Merci de confirmer le clonage de cette commande<br/></div>
				<br/>
				<fieldset>
					<a href="#" style="float: left; margin-left: 8px;"onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler"></a>
					<a href="Commande.php?action=doCloner&id_cmd='.$value["id_cmd"].'" rev="async" style="float: right; margin-right: 8px;"><img src="Img/big.valider.png" alt="Valider"></a>
				</fieldset>
			</div>';
	}
	static function inputAjaxCommande($nom = '', $selected = '', $titre = '', $withBlank = true)
	{
		$nom = ($nom != '') ? $nom : 'commande_fact';
		$titre = ($titre != '') ? '<label style="float:left;">'.$titre.'</label>' : '';
		if($selected != '')
		{
			$info = new commandeModel();
			$result = $info->getDataFromID($selected);
			if($result[0])
			{
				$nomSelected = $result[1][0]['titre_cmd'].' ['.$result[1][0]['id_cmd'].']';
				$idSelected = $result[1][0]['id_cmd'];
			}
			elseif(!$withBlank) $nomSelected = '<i>Veuillez choisir une commande</i>';
			else			  $nomSelected = '&nbsp;';
		}
		elseif(!$withBlank) $nomSelected = '<i>Veuillez choisir une commande</i>';
		else			  $nomSelected = '&nbsp;';
		$out = $titre.' <a href="Commande.php?action=inputCommande&tag='.$nom.'"  id="'.$nom.'AId" style="float:left;width:70%" rev="async"/> '.$nomSelected.'</a>
			<input type="hidden" name="'.$nom.'" id="'.$nom.'InputId" value="'.$idSelected.'"/><br class="clear"/>';
		return $out;
	}
	static function searchInputResultRow($result,$layerBackTo,$tagsBackTo)
	{
		$out = '';
		if(is_array($result) and count($result) > 0)
		foreach($result as $k => $v)
		{
			$ent = ($v['nom_ent'] != NULL) ? '<small> ('.$v['nom_ent'].') </small>' : '';
			$n = $v['id_cmd'].' '.strtoupper($v['titre_cmd']).$ent;
			$out .= '<li><a href="#_'.substr($layerBackTo,2).'" onclick="returnAjaxInputResult(\''.$tagsBackTo.'\',\''.$v['id_cmd'].'\',\''.$n.'\')">' .
			  '<em>'.$n.'</em>' .
			  '</a></li>';
		}
		return $out;
	}

	static function actionVoir($value = array(), $id_cmd)
	{

		$availableConvFormat = OOConverterAvailable('document');
		foreach($value as $v)
		{
			if($v['fournisseur'] != NULL)
			$listDocs[$v['fournisseur']] = 'BCF : '.$v['fournisseur'];
		}
		$listDocs['bdcc'] = 'BDC Client';
		$listDocs['bdl'] = 'Bon de Livraison';
		$listDocs['ri'] = 'Rapport d\'Intervention';
		$value['OutputExt'] = ($value['OutputExt'] != '') ? $value['OutputExt'] : 'pdf';
		$document = HtmlFormIphone::SelectLabel('document',$listDocs,NULL,'Cannevas : ',false);
		$extention = HtmlFormIphone::SelectLabel('OutputExt',$availableConvFormat,$value['OutputExt'],'Format : ',false);

		return '<a href="#_MainMenu" rel="action" class="iButton iBAction"><img src="Img/home.png" alt="Accueil" /></a>
				<form id="formCommandeDoVoir" action="Commande.php?action=doVoir&id_cmd='.$id_cmd.'" onsubmit="return WA.Submit(this,null,event)">
				<div class="iPanel">
				<fieldset>
					<legend>Choix du document</legend>
					<ul>
						<li>'.$document.'</li>
						<li>'.$extention.'</li>
					</ul>
				</fieldset>
				<div id="formCommandeDoVoirResponse"></div>
				<fieldset>
					<a href="#" class="BigButtonValidLeft" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler"></a>
					<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formCommandeDoVoir\',null,event)"><img src="Img/big.valider.png" alt="Valider"></a>
				</fieldset>
			</div>';
	}
	static function actionRecord($value = array(), $produits = '')
	{
		foreach($produits as $v)
		{
			if($v['fournisseur'] != NULL)
			$document .='<li>'.HtmlFormIphone::CheckBox($v['fournisseur'],'BCF : '.$v['fournisseur'],'','1').'</li>';
		}
		$document .='<li>'.HtmlFormIphone::CheckBox('bdcc','BDC Client','','1').'</li>';
		$document .='<li>'.HtmlFormIphone::CheckBox('bdl','Bon de livraison','','1').'</li>';
		$document .='<li>'.HtmlFormIphone::CheckBox('ri','Rapport d\'intervention','','1').'</li>';
		$availableConvFormat = OOConverterAvailable('document');
		$value['OutputExt'] = ($value['OutputExt'] != '') ? $value['OutputExt'] : 'pdf';

		$extention = HtmlFormIphone::SelectLabel('OutputExt',$availableConvFormat,$value['OutputExt'],'Format :',false);

		$value['message'] = ($value['message'] != '') ? $value['message'] : 'Ajout du document '.$value["doc_cmd"];
		$mess = HtmlFormIphone::TextareaLabel('message',$value['message'],'','Message :');

		return '<a href="#_MainMenu" rel="action" class="iButton iBAction"><img src="Img/home.png" alt="Accueil" /></a>
				<form id="formCommandeDoRec" action="Commande.php?action=doRec&id_cmd='.$value["id_cmd"].'" onsubmit="return WA.Submit(this,null,event)">
				<div class="iPanel">
				<fieldset>
					<legend>Options du document</legend>
					<p><i>Le message sera utilisé comme message d\'enregistrement lors de l\'ajout du document dans l\'entrepôt ZunoGed.</i></p>
					<ul>
						'.$document.'
						<li>'.$extention.'</li>
						<li>'.$mess.'</li>
					</ul>
				</fieldset>
				<fieldset>
					<a href="#" class="BigButtonValidLeft" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler"></a>
					<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formCommandeDoRec\',null,event)"><img src="Img/big.valider.png" alt="Valider"></a>
				</fieldset>
			</div>';
	}


	static function actionSend($value = array(), $produits = '')
	{
		foreach($produits as $v)
		{
			if($v['fournisseur'] != NULL)
			$listDocs[$v['fournisseur']] = 'BCF : '.$v['fournisseur'];
		}
		$listDocs['bdcc'] = 'BDC Client';
		$listDocs['bdl'] = 'Bon de Livraison';
		$listDocs['ri'] = 'Rapport d\'Intervention';
		$availableConvFormat = OOConverterAvailable('document');
		$value['OutputExt'] = ($value['OutputExt'] != '') ? $value['OutputExt'] : 'pdf';
		$document = HtmlFormIphone::SelectLabel('document',$listDocs,NULL,'Cannevas',false);
		$extention = HtmlFormIphone::SelectLabel('OutputExt',$availableConvFormat,$value['OutputExt'],'Format :',false);
		$type		= HtmlFormIphone::SelectLabel('type',array('email'=>'E-mail','courrier'=>'Courrier','fax'=>'Fax'),$value['type'],'Type :',false);

		return '<a href="#_MainMenu" rel="action" class="iButton iBAction"><img src="Img/home.png" alt="Accueil" /></a>
				<form id="formCommandeDoSend" action="Commande.php?action=send1&id_cmd='.$value["id_cmd"].'" onsubmit="return WA.Submit(this,null,event)">
				<div class="iPanel">
				<fieldset>
					<legend>Choix du document</legend>
					<ul>
						<li>'.$document.'</li>
						<li>'.$extention.'</li>
					</ul>
				</fieldset>
				<fieldset>
					<legend>Options d\'envoi</legend>
					<ul>
						<li>'.$type.'</li>
					</ul>
				</fieldset>
				<fieldset>
					<a href="#" class="BigButtonValidLeft" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler"></a>
					<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formCommandeDoSend\',null,event)"><img src="Img/big.valider.png" alt="Valider"></a>
				</fieldset>
			</div>';
	}



	static function actionSend1($value = array(), $onError = array(),$errorMess = '')
	{
		if($value['type'] == 'courrier')
			$form = sendView::innerFormSendCourrier($value,$onError,$errorMess);
		elseif($value['type'] == 'fax')
			$form = sendView::innerFormSendFax($value,$onError,$errorMess);
		else  $form = sendView::innerFormSendEmail($value,$onError,$errorMess);
		$form.= HtmlFormIphone::Input('type',$value['type'],'','','hidden');

		return '<a href="#_MainMenu" rel="action" class="iButton iBAction"><img src="Img/home.png" alt="Accueil" /></a>
				<form id="formCommandeDoSend1" action="Commande.php?action=doSend&id_cmd='.$value["id_cmd"].'" onsubmit="return WA.Submit(this,null,event)">
				<div class="iPanel">
				'.$form.'
				<fieldset>
					<a href="#" class="BigButtonValidLeft" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler"></a>
					<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formCommandeDoSend1\',null,event)"><img src="Img/big.valider.png" alt="Valider"></a>
				</fieldset>
			</div>';
	}

	static function actionRecordSend($value = array(), $produits ='')
	{
		foreach($produits as $v)
		{
			if($v['fournisseur'] != NULL)
			$listDocs[$v['fournisseur']] = 'BCF : '.$v['fournisseur'];
		}
		$listDocs['bdcc'] = 'BDC Client';
		$listDocs['bdl'] = 'Bon de Livraison';
		$listDocs['ri'] = 'Rapport d\'Intervention';
		$availableConvFormat = OOConverterAvailable('document');
		$value['OutputExt'] = ($value['OutputExt'] != '') ? $value['OutputExt'] : 'pdf';
		$document = HtmlFormIphone::SelectLabel('document',$listDocs,NULL,'Cannevas : ',false);
		$extention = HtmlFormIphone::SelectLabel('OutputExt',$availableConvFormat,$value['OutputExt'],'Format :',false);
		$type		= HtmlFormIphone::SelectLabel('type',array('email'=>'E-mail','courrier'=>'Courrier','fax'=>'Fax'),$value['type'],'Type :',false);

		$value['message'] = ($value['message'] != '') ? $value['message'] : 'Ajout du document '.$value["doc_cmd"];
		$mess = HtmlFormIphone::TextareaLabel('message',$value['message'],'Message :');

		return '<a href="#_MainMenu" rel="action" class="iButton iBAction"><img src="Img/home.png" alt="Accueil" /></a>
				<form id="formCommandeDoRec" action="Commande.php?action=recsend1&id_cmd='.$value["id_cmd"].'" onsubmit="return WA.Submit(this,null,event)">
				<div class="iPanel">
				<fieldset>
					<legend>Options du document</legend>
					<p><i>Le message sera utilisé comme message d\'enregistrement lors de l\'ajout du document dans l\'entrepôt ZunoGed.</i></p>
					<ul>
						<li>'.$document.'</li>
						<li>'.$extention.'</li>
						<li>'.$mess.'</li>
					</ul>
				</fieldset>
				<fieldset>
					<legend>Options d\'envoi</legend>
					<ul>
						<li>'.$type.'</li>
					</ul>
				</fieldset>
				<fieldset>
					<a href="#" class="BigButtonValidLeft" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler"></a>
					<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formCommandeDoRec\',null,event)"><img src="Img/big.valider.png" alt="Valider"></a>
				</fieldset>
			</div>';
	}


	static function actionRecordSend1($value = array(), $onError = array(),$errorMess = '')
	{
		if($value['type'] == 'courrier')
			$form = sendView::innerFormSendCourrier($value,$onError,$errorMess);
		elseif($value['type'] == 'fax')
			$form = sendView::innerFormSendFax($value,$onError,$errorMess);
		else  $form = sendView::innerFormSendEmail($value,$onError,$errorMess);
		$form.= HtmlFormIphone::Input('type',$value['type'],'','','hidden');

		return '<a href="#_MainMenu" rel="action" class="iButton iBAction"><img src="Img/home.png" alt="Accueil" /></a>
				<form id="formCommandeDoRec1" action="Commande.php?action=doRecsend&id_cmd='.$value["id_cmd"].'" onsubmit="return WA.Submit(this,null,event)">
				<div class="iPanel">
				'.$form.'
				<fieldset>
					<a href="#" class="BigButtonValidLeft" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler"></a>
					<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formCommandeDoRec1\',null,event)"><img src="Img/big.valider.png" alt="Valider"></a>
				</fieldset>
			</div>';
	}
	static function tri_montant($value = array(), $limit, $from, $total)
	{
		$out = '<ul>';
		$prix = $_SESSION['user']['LastLetterSearch'];
		$valeurs = getStats('commande');
		foreach($value[1] as $k => $v)
		{
			$sortie = triMontant($v['sommeFHT_cmd'], $prix, $valeurs);
			$prix = $sortie[1];
			$out .= $sortie[0];
			$out .= '<li><a href="Commande.php?action=view&id_cmd='.$v['id_cmd'].'"rev="async">'.$v['id_cmd'].' - '.$v['titre_cmd'].' <small>Total Fournisseur : <b>'.number_format($v['sommeFHT_cmd'], 2, ',', ' ').' €</b></small></a></li>';
		}


		if($total > ($limit+$from))
		{
			$out .= '<li class="iMore" id="triMontantCommandeMore'.$from.'"><a href="Commande.php?action=triMontantMore&from='.($limit+$from).'&total='.$total.'" rev="async">Plus de résultats</a></li>';

		}
		$_SESSION['user']['LastLetterSearch'] = $prix;
		$out .= '</ul>';
		return $out;
	}

	static function tri_creation($value = array(), $limit, $from, $total)
	{
		$mois = $_SESSION['user']['LastLetterSearch'];
		$out = '<ul>';

		foreach($value[1] as $k => $v)
		{
			if($v['daterecord_cmd'] != NULL)
			{

					if($mois != ucfirst(strftime("%B %G", strtotime($v['daterecord_cmd'])))) { $mois = ucfirst(strftime("%B %G", strtotime($v['daterecord_cmd']))); $out .= '</ul><h2>'.$mois.'</h2><ul class="iArrow">'; }
					$echeance=strftime("%d/%m/%G", strtotime($v['daterecord_cmd']));
					$out .= '<li><a href="Commande.php?action=view&id_cmd='.$v['id_cmd'].'" rev="async">'.$v['id_cmd'].' - '.$v['titre_cmd'].' <small>Création le : <b>'.$echeance.'</b></small></a></li>';

			}
		}

		if($total > ($limit+$from))
		{
			$out .= '<li class="iMore" id="triCreationCommandeMore'.$from.'"><a href="Commande.php?action=triCreationMore&from='.($limit+$from).'&total='.$total.'" rev="async">Plus de résultats</a></li>';

		}

		$out .= '</ul>';
		$_SESSION['user']['LastLetterSearch'] = $mois;
		return $out;
	}
	static function tri_entreprise($value = array(), $limit, $from, $total)
	{
		$ent = $_SESSION['user']['LastLetterSearch'];
		$out = '<ul>';

		foreach($value[1] as $k => $v)
		{
			if($v['nom_ent'] != NULL)
			{

					if($ent != strtoupper($v['nom_ent']{0})) { $ent=strtoupper($v['nom_ent']{0}); $out .= '</ul><h2>'.$ent.'</h2><ul class="iArrow">'; }
					$entreprise=$v['nom_ent'];
					$out .= '<li><a href="Commande.php?action=view&id_cmd='.$v['id_cmd'].'" rev="async">'.$v['id_cmd'].' - '.$v['titre_cmd'].' <small><b>'.$entreprise.'</b></small></a></li>';

			}

			elseif($v['nom_ent'] == NULL)
			{

					if($ent != 'Sans entreprise') { $ent='Sans entreprise'; $out .= '</ul><h2>'.$ent.'</h2><ul class="iArrow">'; }
					$out .= '<li><a href="Commande.php?action=view&id_cmd='.$v['id_cmd'].'" rev="async">'.$v['id_cmd'].' - '.$v['titre_cmd'].' <small><b>Aucune entreprise liée</b></small></a></li>';

			}
		}

		if($total > ($limit+$from))
		{
			$out .= '<li class="iMore" id="triEntrepriseCommandeMore'.$from.'"><a href="Commande.php?action=triEntrepriseMore&from='.($limit+$from).'&total='.$total.'" rev="async">Plus de résultats</a></li>';

		}

		$out .= '</ul>';
		$_SESSION['user']['LastLetterSearch'] = $ent;
		return $out;
	}
	static function tri_contact($value = array(), $limit, $from, $total)
	{
		$ent = $_SESSION['user']['LastLetterSearch'];
		$out = '<ul>';

		foreach($value[1] as $k => $v)
		{
			if($v['nom_cont'] != NULL)
			{

					if($ent != strtoupper($v['nom_cont']{0})) { $ent=strtoupper($v['nom_cont']{0}); $out .= '</ul><h2>'.$ent.'</h2><ul class="iArrow">'; }
					$entreprise=$v['nom_cont'];
					$out .= '<li><a href="Commande.php?action=view&id_cmd='.$v['id_cmd'].'" rev="async">'.$v['id_cmd'].' - '.$v['titre_cmd'].' <small><b>'.$entreprise.'</b></small></a></li>';

			}

			elseif($v['nom_cont'] == NULL)
			{

					if($ent != 'Sans contact') { $ent='Sans contact'; $out .= '</ul><h2>'.$ent.'</h2><ul class="iArrow">'; }
					$out .= '<li><a href="Commande.php?action=view&id_cmd='.$v['id_cmd'].'" rev="async">'.$v['id_cmd'].' - '.$v['titre_cmd'].' <small><b>Aucun contact lié</b></small></a></li>';

			}
		}

		if($total > ($limit+$from))
		{
			$out .= '<li class="iMore" id="triContactCommandeMore'.$from.'"><a href="Commande.php?action=triContactMore&from='.($limit+$from).'&total='.$total.'" rev="async">Plus de résultats</a></li>';

		}

		$out .= '</ul>';
		$_SESSION['user']['LastLetterSearch'] = $ent;
		return $out;
	}
	
	static function afficherStats($datas = array())
	{
		$out = '<div class="iPanel">';
		$out .= '<fieldset><legend>Commandes</legend><ul><li>Nombre : '.$datas[0].'</li>';
		$out .= '<li>Valeur totale : '.$datas[5].' &euro;</li>';
		$out .= '<li>Prix moyen : '.$datas[1].' €</li>';
		$out .= '<li>Prix médian : '.$datas[2].' €</li>';
		$out .= '<li>Variance : '.$datas[3].'</li>';
		$out .= '<li>Écart type : '.$datas[4].' €</li>';
		$out .= '<li>Coefficient de variation : '.$datas[6].'</li></ul></fieldset></div>';
		return $out;
	}
}
?>
