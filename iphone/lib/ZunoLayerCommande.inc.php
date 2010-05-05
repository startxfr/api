<?php
/**
 * Fichier contenant les layers de la partie Commande
 */

 class ZunoLayerCommande
 {
 	static function loadDefaultLayer()
 	{
 		$footer = HtmlElementIphone::footer();
 		return 		'<!------------------------------------ -->
				<!--   Commande : Menu général         -->
				<!------------------------------------ -->
				<div class="iLayer" id="waMenuCommande" title="Commande">
				<a href="Commande.php?action=addCommandePre" rev="async" rel="action" class="iButton iBClassic"><img src="Img/add.png" alt="Créer une commande" /></a>
				<a href="#" rel="back" class="iButton iBBack" onclick="return WA.Back()">Retour</a>
				'.self::iMenuGeneral().$footer.'
				</div>
				<!----------------------------------------->
				<!--Commande : Menu Ajout d\'une commande-->
				<!----------------------------------------->
				<div class="iLayer" id="waNewCommande" title="Nouv.commande">
				<a href="#" rel="action" class="iButton iBClassic"></a>
				<a href="#" rel="back" class="iButton iBBack" onclick="return WA.Back()">Retour</a>
				'.self::iMenuNew().$footer.'
				</div>
				<!------------------------------------ -->
				<!--   Commande : Menu Recherche       -->
				<!------------------------------------ -->
				<div class="iLayer" id="waSearchCommande" title="Recherche">
				<a href="#"  onclick="return WA.Submit(\'formSearchCommande\',null,event)" rel="action" class="iButton iBAction"><img src="Img/search.png" alt="recherche" /></a>
				'.self::iLayerSearch().$footer.'
				</div>
				<!------------------------------------ -->
				<!-- Commande : Menu statistique       -->
				<!------------------------------------ -->
				<div class="iLayer" id="waStatsCommande" title="Statistiques">
				<a href="#"  onclick="return WA.Submit(\'formStatsCommande\',null,event)" rel="action" class="iButton iBAction"><img src="Img/stats.png" alt="Statistiques" /></a>
				'.$footer.'
				</div>
		';
 	}

 	static function iMenuGeneral()
 	{
 		return '<div class="iMenu">
				<ul class="iArrow">
 					<li><a href="#_SearchCommande" class="Commande"><img src="Img/iconMenu/commande.search.png" alt="Recherche" /> Rechercher</a></li>
 					<li><a href="Commande.php?action=addCommandePre" rev="async" rel="action" class="Commande"><img src="Img/iconMenu/commande.add.png" alt="Ajouter" /> Créer</a></li>
 					<li><a href="Commande.php?action=voirStats" rev="async" class="Commande"><img src="Img/iconMenu/commande.stat.png" alt="Statistiques" />Statistiques</a></li>
 				</ul>
 				</div>'.self::commandeForm().'<br class="clear"/>';
 	}
 	static function commandeForm($tri = 'creation')
	{
		if($tri == 'montant') $sc['mont'] = ' class="select"';
		elseif($tri == 'creation') $sc['cre'] = ' class="select"';
		elseif($tri == 'entreprise') $sc['ent'] = ' class="select"';
		elseif($tri == 'contact') $sc['cont'] = ' class="select"';
	return '<div  class="iFormI">
			<ul>
				<li style="color:#348C6E;font-weight:bold">Tri : </li>
				<li'.$sc['mont'].' id="CTmontant"><a href="Commande.php?action=tri_montant" rev="async" onclick="switchCommandeTri(\'CTmontant\')">Montant</a></li>
				<li'.$sc['cre'].' id="CTcreation"><a href="Commande.php?action=tri_creation" rev="async" onclick="switchCommandeTri(\'CTcreation\')">Création</a></li>
				<li'.$sc['ent'].' id="CTentreprise"><a href="Commande.php?action=tri_entreprise" rev="async" onclick="switchCommandeTri(\'CTentreprise\')">Entreprise</a></li>
				<li'.$sc['cont'].' id="CTcontact"><a href="Commande.php?action=tri_contact" rev="async" onclick="switchCommandeTri(\'CTcontact\')">Contact</a></li>
			</ul>
	</div><div id="CommandeTriResultatAsync" class="iList iListFormI"></div>'.
	'<form id="formAutoChargeCommandeTri" action="Commande.php?action=tri_'.$tri.'" onsubmit="return WA.Submit(this,null,event)"><a id="lienAutoChargeCommandeTri" onclick="return WA.Submit(\'formAutoChargeCommandeTri\',null,event)"></a></form>';
	}

 	static function iMenuNew()
 	{
 		return '<div class="iMenu">
		<ul class="iArrow">
			<li><a href="Commande.php?action=addCommande" rev="async" rel="action"><img src="Img/iconMenu/commande.add.png" alt="Ajouter" />Ajouter une nouvelle commande</a></li>
		</ul>
		</div>';
 	}

 	static function iLayerSearch()
 	{

 		if($_SESSION['commandeSearch'] != '') $iTag = ' value="'.$_SESSION['commandeSearch'].'"';
 		return '<form id="formSearchCommande" action="Commande.php?action=searchCommande" onsubmit="return WA.Submit(this,null,event)">
					<div class="iPanel">
						<div id="form-searchCommande"></div>
							<fieldset>
								<ul>
									<li><input type="text" name="query" placeholder="recherche"'.$iTag.'/></li>

								</ul>
								<br/>
								<a href="#" onclick="return WA.Submit(\'formSearchCommande\',null,event)" class="whiteButton"><img src="Img/search.png" style="float: left"/><span>Lancer la recherche</span></a>
							</fieldset>
						</div>
				</form>';
 	}


 	static function headerFormSearchProd()
	{
		if($_SESSION['searchProduitQuery'] != '') $iTag = ' value="'.$_SESSION['searchProduitQuery'].'"';
	return '<form action="Commande.php?action=inputProduitResult" id="formSearchProduitajax" onsubmit="return WA.Submit(this,null,event)"><div  class="iFormI">
			<a href="#_SearchProduitResult" rel="action" onclick="return WA.Submit(\'formSearchProduitajax\',null,event)" class="iButton iBAction"><img src="Img/search.png" alt="Recherche" /></a>

			<fieldset class="attach">
				<legend>Recherche</legend>
				<input type="search" name="search" placeholder="Votre recherche" id="formSearchProduitInput"'.$iTag.'/>
			</fieldset>
	</div></form><div id="SearchProduitResultAsync" style="display:block"></div>';
	}

	static function headerFormSearchCmd()
	{
		if($_SESSION['searchCommandeQuery'] != '') $iTag = ' value="'.$_SESSION['searchCommandeQuery'].'"';
	return '<form action="Commande.php?action=inputCommandeResult" id="formSearchCommandeajax" onsubmit="return WA.Submit(this,null,event)"><div  class="iFormI">
			<a href="#_SearchCommandeResult" rel="action" onclick="return WA.Submit(\'formSearchCommandeajax\',null,event)" class="iButton iBAction"><img src="Img/search.png" alt="Recherche" /></a>

			<fieldset class="attach">
				<legend>Recherche</legend>
				<input type="search" name="search" placeholder="Votre recherche" id="formSearchCommandeInput"'.$iTag.'/>
			</fieldset>
	</div></form><div id="SearchCommandeResultAsync" style="display:block"></div>';
	}
 }
?>
