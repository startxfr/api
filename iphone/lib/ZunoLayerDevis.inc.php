<?php
/**
 * Fichier contenant les layers de la partie Devis
 */

 class ZunoLayerDevis
 {
 	static function loadDefaultLayer()
 	{
 		$footer = HtmlElementIphone::footer();
 		return '<!------------------------------------ -->
				<!--   Devis : Menu général            -->
				<!------------------------------------ -->
				<div class="iLayer" id="waMenuDevis" title="Devis">
				<a href="Devis.php?action=addDevis" rev="async" rel="action" class="iButton iBClassic"><img src="Img/add.png" alt="Créer un devis" /></a>
				<a href="#" rel="back" class="iButton iBBack" onclick="return WA.Back()">Retour</a>
				'.self::iMenuGeneral().$footer.'
				</div>
				<!------------------------------------ -->
				<!--   Devis : Menu Ajout d\'un devis  -->
				<!------------------------------------ -->
				<div class="iLayer" id="waNewDevis" title="Nouv.devis">
				<a href="#" rel="action" class="iButton iBClassic"></a>
				<a href="#" rel="back" class="iButton iBBack" onclick="return WA.Back()">Retour</a>
				'.self::iMenuNew().$footer.'
				</div>
				<!------------------------------------ -->
				<!--   Devis : Menu Recherche          -->
				<!------------------------------------ -->
				<div class="iLayer" id="waSearchDevis" title="Recherche">
				<a href="#"  onclick="return WA.Submit(\'formSearchDevis\',null,event)" rel="action" class="iButton iBAction"><img src="Img/search.png" alt="recherche" /></a>
				'.self::iLayerSearch().$footer.'
				</div>
				<!------------------------------------ -->
				<!-- Devis : Menu statistique          -->
				<!------------------------------------ -->
				<div class="iLayer" id="waStatsDevis" title="Statistiques">
				<a href="#"  onclick="return WA.Submit(\'formStatsDevis\',null,event)" rel="action" class="iButton iBAction"><img src="Img/stats.png" alt="Statistiques" /></a>
				'.$footer.'
				</div>
		';
 	}

 	static function iMenuGeneral()
 	{
 		return '<div class="iMenu">
				<ul class="iArrow">
 					<li><a href="#_SearchDevis" class="Devis"><img src="Img/iconMenu/devis.search.png" alt="Recherche" /> Rechercher</a></li>
 					<li><a href="Devis.php?action=addDevis" rev="async" rel="action" class="Devis"><img src="Img/iconMenu/devis.add.png" alt="Ajouter" /> Créer</a></li>
 					<li><a href="Devis.php?action=voirStats" rev="async" class="Devis"><img src="Img/iconMenu/devis.stat.png" alt="Statistiques" />Statistiques</a></li>
 				</ul>
 				<ul class="iArrow">
 					<li><a href="Devis.php?action=addDevisExpress" rev="async" rel="action" class="Devis"><img src="Img/iconMenu/devis.addExp.png" alt="Ajouter Express" /> Devis Express</a></li>
 				</ul></div>'.self::devisForm().'<br class="clear"/>';
 	}
 	static function devisForm($tri = 'creation')
	{
		if($tri == 'montant') $sc['mont'] = ' class="select"';
		elseif($tri == 'creation') $sc['cre'] = ' class="select"';
		elseif($tri == 'entreprise') $sc['ent'] = ' class="select"';
		elseif($tri == 'contact') $sc['cont'] = ' class="select"';
	return '<div  class="iFormI">
			<ul>
				<li style="color:#547C30;font-weight:bold">Tri : </li>
				<li'.$sc['mont'].' id="DTmontant"><a href="Devis.php?action=tri_montant" rev="async" onclick="switchDevisTri(\'DTmontant\')">Montant</a></li>
				<li'.$sc['cre'].' id="DTcreation"><a href="Devis.php?action=tri_creation" rev="async" onclick="switchDevisTri(\'DTcreation\')">Création</a></li>
				<li'.$sc['ent'].' id="DTentreprise"><a href="Devis.php?action=tri_entreprise" rev="async" onclick="switchDevisTri(\'DTentreprise\')">Entreprise</a></li>
				<li'.$sc['cont'].' id="DTcontact"><a href="Devis.php?action=tri_contact" rev="async" onclick="switchDevisTri(\'DTcontact\')">Contact</a></li>
			</ul>
	</div><div id="DevisTriResultatAsync" class="iList iListFormI"></div>'.
	'<form id="formAutoChargeDevisTri" action="Devis.php?action=tri_'.$tri.'" onsubmit="return WA.Submit(this,null,event)"><a id="lienAutoChargeDevisTri" onclick="return WA.Submit(\'formAutoChargeDevisTri\',null,event)"></a></form>';
	}
 	static function iMenuNew()
 	{
 		return '<div class="iMenu">
		<ul class="iArrow">
			<li><a href="Devis.php?action=addDevis" rev="async" rel="action"><img src="Img/iconMenu/devis.add.png" alt="Ajouter" />Ajouter un nouveau devis</a></li>
		</ul>
		</div>';
 	}

 	static function iLayerSearch()
 	{

 		if($_SESSION['devisSearch'] != '') $iTag = ' value="'.$_SESSION['devisSearch'].'"';
 		return '<form id="formSearchDevis" action="Devis.php?action=searchDevis" onsubmit="return WA.Submit(this,null,event)">
					<div class="iPanel">
						<div id="form-searchDevis"></div>
							<fieldset>
								<ul>
									<li><input type="text" name="query" placeholder="recherche"'.$iTag.'/></li>

								</ul>
								<br/>
								<a href="#" onclick="return WA.Submit(\'formSearchDevis\',null,event)" class="whiteButton"><img src="Img/search.png" style="float: left"/><span>Lancer la recherche</span></a>
							</fieldset>
						</div>
				</form>';
 	}


 	static function headerFormSearchProdDE()
	{
		if($_SESSION['searchProduitQuery'] != '') $iTag = ' value="'.$_SESSION['searchProduitQuery'].'"';
	return '<form action="Devis.php?action=inputProduitResult" id="formSearchProduitajax" onsubmit="return WA.Submit(this,null,event)"><div  class="iFormI">
			<a href="#_SearchProduitResult" rel="action" onclick="return WA.Submit(\'formSearchProduitajax\',null,event)" class="iButton iBAction"><img src="Img/search.png" alt="Recherche" /></a>

			<fieldset class="attach">
				<legend>Recherche</legend>
				<input type="search" name="search" placeholder="Votre recherche" id="formSearchProduitInput"'.$iTag.'/>
			</fieldset>
	</div></form><div id="SearchProduitResultAsync" style="display:block"></div>'.
	'<div style="display:none;"><form action="Devis.php?action=inputProduitDESuite" id="formcacheproduitDE" onsubmit="return WA.Submit(this,null,event)">'.
	'<a id="lienajoutformDE" onclick="return WA.Submit(\'formcacheproduitDE\',null,event)">Lien test</a></form></div>';
	}

	static function headerFormSearchProd()
	{
		if($_SESSION['searchProduitQuery'] != '') $iTag = ' value="'.$_SESSION['searchProduitQuery'].'"';
	return '<form action="Devis.php?action=inputProduitResult" id="formSearchProduitajax" onsubmit="return WA.Submit(this,null,event)"><div  class="iFormI">
			<a href="#_SearchProduitResult" rel="action" onclick="return WA.Submit(\'formSearchProduitajax\',null,event)" class="iButton iBAction"><img src="Img/search.png" alt="Recherche" /></a>

			<fieldset class="attach">
				<legend>Recherche</legend>
				<input type="search" name="search" placeholder="Votre recherche" id="formSearchProduitInput"'.$iTag.'/>
			</fieldset>
	</div></form><div id="SearchProduitResultAsync" style="display:block"></div>';
	}

static function headerFormSearchDev()
	{
		if($_SESSION['searchDevisQuery'] != '') $iTag = ' value="'.$_SESSION['searchDevisQuery'].'"';
	return '<form action="Devis.php?action=inputDevisResult" id="formSearchDevisajax" onsubmit="return WA.Submit(this,null,event)"><div  class="iFormI">
			<a href="#_SearchDevisResult" rel="action" onclick="return WA.Submit(\'formSearchDevisajax\',null,event)" class="iButton iBAction"><img src="Img/search.png" alt="Recherche" /></a>

			<fieldset class="attach">
				<legend>Recherche</legend>
				<input type="search" name="search" placeholder="Votre recherche" id="formSearchDevisInput"'.$iTag.'/>
			</fieldset>
	</div></form><div id="SearchDevisResultAsync" style="display:block"></div>';
	}
 }
?>
