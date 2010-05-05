<?php
/**
 * Fichier contenant les layers de la partie Facture
 */

 class ZunoLayerFacture
 {
 	static function loadDefaultLayer()
 	{
 		$footer = HtmlElementIphone::footer();
 		return 		'<!------------------------------------ -->
				<!--    Facture : Menu général         -->
				<!------------------------------------ -->
				<div class="iLayer" id="waMenuFacture" title="Facture">
				<a href="Facture.php?action=addFacturePre" rev="async" rel="action" class="iButton iBClassic"><img src="Img/add.png" alt="Créer une facture" /></a>
				<a href="#" rel="back" class="iButton iBBack" onclick="return WA.Back()">Retour</a>
				'.self::iMenuGeneral().$footer.'
				</div>
				<!----------------------------------------->
				<!--Facture : Menu Ajout                 -->
				<!----------------------------------------->
				<div class="iLayer" id="waNewFacture" title="Nouv.facture">
				<a href="#" rel="action" class="iButton iBClassic"></a>
				<a href="#" rel="back" class="iButton iBBack" onclick="return WA.Back()">Retour</a>
				'.self::iMenuNew().$footer.'
				</div>
				<!------------------------------------ -->
				<!--   Facture : Menu Recherche       -->
				<!------------------------------------ -->
				<div class="iLayer" id="waSearchFacture" title="Recherche">
				<a href="#"  onclick="return WA.Submit(\'formSearchFacture\',null,event)" rel="action" class="iButton iBAction"><img src="Img/search.png" alt="recherche" /></a>
				'.self::iLayerSearch().$footer.'
				</div>
				<!------------------------------------ -->
				<!-- Facture : Menu statistique        -->
				<!------------------------------------ -->
				<div class="iLayer" id="waStatsFacture" title="Statistiques">
				<a href="#"  onclick="return WA.Submit(\'formStatsFacture\',null,event)" rel="action" class="iButton iBAction"><img src="Img/stats.png" alt="Statistiques" /></a>
				'.$footer.'
				</div>
		';
 	}

 	static function iMenuGeneral()
 	{
 		return '<div class="iMenu">
				<ul class="iArrow">
 					<li><a href="#_SearchFacture" class="Facture"><img src="Img/iconMenu/facture.search.png" alt="Recherche" /> Rechercher</a></li>
 					<li><a href="Facture.php?action=addFacturePre&type=Facture" rev="async" rel="action" class="Facture"><img src="Img/iconMenu/facture.add.png" alt="Ajouter" /> Créer une Facture</a></li>
 					<li><a href="Facture.php?action=addFacturePre&type=Avoir" rev="async" rel="action" class="Facture"><img src="Img/iconMenu/facture.avoirAdd.png" alt="Ajouter" /> Créer un Avoir</a></li>
 					<li><a href="Facture.php?action=voirStats" rev="async" class="Facture"><img src="Img/iconMenu/facture.stat.png" alt="Statistiques" />Statistiques</a></li>
 				</ul>
 				<ul class="iArrow">
 					<li><a href="Facture.php?action=addFactureExpress" rev="async" rel="action" class="Facture"><img src="Img/iconMenu/facture.addExp.png" alt="Ajouter Express" /> Facture Express</a></li>
 				</ul></div>'.self::factureForm().'<br class="clear"/>';
 	}
	static function factureForm($tri = 'creation')
	{
		if($tri == 'montant') $sc['mont'] = ' class="select"';
		elseif($tri == 'creation') $sc['cre'] = ' class="select"';
		elseif($tri == 'entreprise') $sc['ent'] = ' class="select"';
		elseif($tri == 'contact') $sc['cont'] = ' class="select"';
	return '<div  class="iFormI">
			<ul>
				<li style="color:#3C508C;font-weight:bold">Tri : </li>
				<li'.$sc['mont'].' id="FTmontant"><a href="Facture.php?action=tri_montant" rev="async" onclick="switchFactureTri(\'FTmontant\')">Montant</a></li>
				<li'.$sc['cre'].' id="FTcreation"><a href="Facture.php?action=tri_creation" rev="async" onclick="switchFactureTri(\'FTcreation\')">Création</a></li>
				<li'.$sc['ent'].' id="FTentreprise"><a href="Facture.php?action=tri_entreprise" rev="async" onclick="switchFactureTri(\'FTentreprise\')">Entreprise</a></li>
				<li'.$sc['cont'].' id="FTcontact"><a href="Facture.php?action=tri_contact" rev="async" onclick="switchFactureTri(\'FTcontact\')">Contact</a></li>
			</ul>
	</div><div id="FactureTriResultatAsync" class="iList iListFormI"></div>'.
	'<form id="formAutoChargeFactureTri" action="Facture.php?action=tri_'.$tri.'" onsubmit="return WA.Submit(this,null,event)"><a id="lienAutoChargeFactureTri" onclick="return WA.Submit(\'formAutoChargeFactureTri\',null,event)"></a></form>';
	}
 	static function iMenuNew()
 	{
 		return '<div class="iMenu">
		<ul class="iArrow">
			<li><a href="Facture.php?action=addFacture" rev="async" rel="action"><img src="Img/iconMenu/facture.add.png" alt="Ajouter" />Ajouter une nouvelle Facture</a></li>
		</ul>
		</div>';
 	}

 	static function iLayerSearch()
 	{

 		if($_SESSION['factureSearch'] != '') $iTag = ' value="'.$_SESSION['factureSearch'].'"';
 		return '<form id="formSearchFacture" action="Facture.php?action=searchFacture" onsubmit="return WA.Submit(this,null,event)">
					<div class="iPanel">
						<div id="form-searchFacture"></div>
							<fieldset>
								<ul>
									<li><input type="text" name="query" placeholder="recherche"'.$iTag.'/></li>
									<li><select name="type"><option value="Facture" checked="checked">Recherche sur les factures</option>
										<option value="Avoir">Recherche sur les avoirs</option>
										<option value="Tout">Recherche sur les factures et les avoirs</option></select><br class="clear"/>
									</li>
								</ul>
								<br/>
								<a href="#" onclick="return WA.Submit(\'formSearchFacture\',null,event)" class="whiteButton"><img src="Img/search.png" style="float: left"/><span>Lancer la recherche</span></a>
							</fieldset>
						</div>
				</form>';
 	}



 }
?>
