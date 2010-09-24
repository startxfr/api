<?php
/**
 * Fichier contenant les Layers de la partie affaires
 */

 class ZunoLayerAffaire
 {
 	static function loadDefaultLayer()
 	{
 		$footer = HtmlElementIphone::footer();
 		if($_SESSION['affaireSearch'] != '') $iTag = ' value="'.$_SESSION['affaireSearch'].'"';
 		return '<!------------------------------------ -->
				<!--   Affaires : Menu général         -->
				<!------------------------------------ -->
				<div class="iLayer" id="waMenuAffaire" title="Affaires">
				<a href="Affaire.php?action=addAffaire" rev="async" rel="action" class="iButton iBClassic"><img src="'.getStaticUrl('imgPhone').'add.png" alt="Créer une affaire" /></a>
				<a href="#" rel="back" class="iButton iBBack" onclick="return WA.Back()">Retour</a>
				<div class="iMenu">

 				<ul class="iArrow">
 					<li><a href="#_SearchAffaire" class="Affaire"><img src="'.getStaticUrl('imgPhone').'iconMenu/affaire.search.png" alt="Recherche" /> Rechercher</a></li>
					<li><a href="Affaire.php?action=addAffaire" rev="async" rel="action" class="Affaire"><img src="'.getStaticUrl('imgPhone').'iconMenu/affaire.add.png" alt="Ajouter" /> Créer</a></li>
 				</ul><ul class="iArrow">
 					<li><a href="Affaire.php?action=rechercheavancee" rev="async" rel="action" class="Affaire"><img src="'.getStaticUrl('imgPhone').'iconMenu/affaire.search.png" alt="Recherche avancée" /> Recherche avancée</a></li>
 					<li><a href="Affaire.php?action=statistiques" rev="async" rel="action" class="Affaire"><img src="'.getStaticUrl('imgPhone').'iconMenu/affaire.stat.png" alt="Statistiques" /> Statistiques</a></li>
 				</ul>
				</div>
				'.self::affaireForm().'<br class="clear"/>'.$footer.'
				</div>
				<!------------------------------------ -->
				<!--   Affaires : Menu recherche         -->
				<!------------------------------------ -->
				<div class="iLayer" id="waSearchAffaire" title="Recherche">
				<a href="#"  onclick="return WA.Submit(\'formSearchAffaire\',null,event)" rel="action" class="iButton iBAction"><img src="'.getStaticUrl('imgPhone').'search.png" alt="recherche" /></a>
				<form id="formSearchAffaire" action="Affaire.php?action=searchAffaire" onsubmit="return WA.Submit(this,null,event)">
					<div class="iPanel">
						<div id="form-searchAffaire"></div>
							<fieldset>
								<ul>
									<li><input type="text" name="query" placeholder="recherche"'.$iTag.'/></li>

								</ul>
								<br/>
								<a href="#" onclick="return WA.Submit(\'formSearchAffaire\',null,event)" class="whiteButton"><img src="'.getStaticUrl('imgPhone').'search.png" style="float: left"/><span>Lancer la recherche</span></a>
							</fieldset>
						</div>
				</form>'.$footer.'
				</div>';
 	}

 	static function affaireForm($tri = 'creation')
	{
		if($tri == 'echeance') $sc['ech'] = ' class="select"';
		elseif($tri == 'creation') $sc['cre'] = ' class="select"';
		elseif($tri == 'entreprise') $sc['ent'] = ' class="select"';
		elseif($tri == 'contact') $sc['cont'] = ' class="select"';
	return '<div  class="iFormI">
			<ul>
				<li style="color:#EAF853;font-weight:bold">Tri : </li>
				<li'.$sc['ech'].' id="AFTecheance"><a href="Affaire.php?action=tri_echeance" rev="async" onclick="switchAffaireTri(\'AFTecheance\')">Echéance</a></li>
				<li'.$sc['cre'].' id="AFTcreation"><a href="Affaire.php?action=tri_creation" rev="async" onclick="switchAffaireTri(\'AFTcreation\')">Création</a></li>
				<li'.$sc['ent'].' id="AFTentreprise"><a href="Affaire.php?action=tri_entreprise" rev="async" onclick="switchAffaireTri(\'AFTentreprise\')">Entreprise</a></li>
				<li'.$sc['cont'].' id="AFTcontact"><a href="Affaire.php?action=tri_contact" rev="async" onclick="switchAffaireTri(\'AFTcontact\')">Contact</a></li>
			</ul>
	</div><div id="AffaireTriResultatAsync" class="iList iListFormI"></div>'.
	'<form id="formAutoChargeAffaireTri" action="Affaire.php?action=tri_'.$tri.'" onsubmit="return WA.Submit(this,null,event)"><a id="lienAutoChargeAffaireTri" onclick="return WA.Submit(\'formAutoChargeAffaireTri\',null,event)"></a></form>';
	}

	static function headerFormSearchAff()
	{
		if($_SESSION['searchAffaireQuery'] != '') $iTag = ' value="'.$_SESSION['searchAffaireQuery'].'"';
	return '<form action="Affaire.php?action=inputAffaireResult" id="formSearchAffaireajax" onsubmit="return WA.Submit(this,null,event)"><div  class="iFormI">
			<a href="#_SearchAffaireResult" rel="action" onclick="return WA.Submit(\'formSearchAffaireajax\',null,event)" class="iButton iBAction"><img src="'.getStaticUrl('imgPhone').'search.png" alt="Recherche" /></a>

			<fieldset class="attach">
				<legend>Recherche</legend>
				<input type="search" name="search" placeholder="Votre recherche" id="formSearchAffaireInput"'.$iTag.'/>
			</fieldset>
	</div></form><div id="SearchAffaireResultAsync" style="display:block"></div>';
	}
 }
?>
