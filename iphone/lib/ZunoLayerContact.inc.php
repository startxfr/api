<?php

/**
 *
 */
class ZunoLayerContact
{

	/**
	 * HTML Select without label before
	 */
	static function loadDefaultLayer()
	{
		$footer = HtmlElementIphone::footer();
return '<!------------------------------------ -->
		<!-- CONTACT : Menu général            -->
		<!------------------------------------ -->
		<div class="iLayer" id="waMenuContact" title="Contacts">
			<a href="#_NewContact" rel="action" class="iButton iBClassic"><img src="Img/add.png" alt="Ajouter un contact" /></a>
			<a href="#" rel="back" class="iButton iBBack" onclick="return WA.Back()">Retour</a>
			'.self::iMenuGeneral().$footer.'
		</div>
		<!------------------------------------ -->
		<!-- CONTACT : Menu Ajout d\'un contact -->
		<!------------------------------------ -->
		<div class="iLayer" id="waNewContact" title="Nouv.contact">
			<a href="#" rel="action" class="iButton iBClassic"></a>
			<a href="#" rel="back" class="iButton iBBack" onclick="return WA.Back()">Retour</a>
			'.self::iMenuNew().$footer.'
		</div>
		<!------------------------------------ -->
		<!-- CONTACT : Menu Recherche          -->
		<!------------------------------------ -->
		<div class="iLayer" id="waSearchContactEnt" title="Recherche">
			<a href="#"  onclick="return WA.Submit(\'formSearchContactEnt\',null,event)" rel="action" class="iButton iBAction"><img src="Img/search.png" alt="recherche" /></a>
			'.self::iLayerSearchEnt().$footer.'
		</div>
		<div class="iLayer" id="waSearchContactPers" title="Recherche">
			<a href="#"  onclick="return WA.Submit(\'formSearchContactPers\',null,event)" rel="action" class="iButton iBAction"><img src="Img/search.png" alt="recherche" /></a>
			'.self::iLayerSearchPers().$footer.'
		</div>
		<!------------------------------------ -->
		<!-- CONTACT : Container des forms     -->
		<!------------------------------------ -->
		<div class="iLayer" id="waContactSearchResult" title="Résultat"></div>
		<div class="iLayer" id="waContactFichePart" title="Fiche"></div>
		<div class="iLayer" id="waContactFicheEnt" title="Fiche"></div>
		<div class="iLayer" id="waContactEnt" title="Entreprise"></div>
		<div class="iLayer" id="waContactPers" title="Contact"></div>
		';
	}

	/**
	 * HTML Select without label before
	 */
	static function iMenuNew()
	{
return '<div class="iMenu">
		<ul class="iArrow">
			<li><a href="Contact.php?action=addEnt" rev="async" rel="action" class="Contact"><img src="Img/iconMenu/contact.Entadd.png" alt="Ajouter" />Entreprise</a></li>
			<li><a href="Contact.php?action=addPart" rev="async" rel="action" class="Contact"><img src="Img/iconMenu/contact.Partadd.png" alt="Ajouter" />Particulier</a></li>
		</ul>
	</div>';
	}

	/**
	 * HTML Select without label before
	 */
	static function iMenuGeneral()
	{
return '<div class="iMenu">
		<h3 class="Contact">Entreprise</h3>
		<ul class="iArrow">
			<li><a href="#_SearchContactEnt" class="Contact"><img src="Img/iconMenu/contact.Entsearch.png" alt="Recherche" /> Rechercher </a></li>
			<li><a href="Contact.php?action=addEnt" rev="async" rel="action" class="Contact"><img src="Img/iconMenu/contact.Entadd.png" alt="Ajouter" /> Ajouter</a></li>
		</ul>
		<h3 class="Contact">Particulier</h3>
		<ul class="iArrow">
			<li><a href="#_SearchContactPers" class="Contact"><img src="Img/iconMenu/contact.Partsearch.png" alt="Recherche" />Rechercher</a></li>
			<li><a href="Contact.php?action=addPart" rev="async" rel="action" class="Contact"><img src="Img/iconMenu/contact.Partadd.png" alt="Ajouter" />Ajouter</a></li>
		</ul>
		</div>';
	}

	/**
	 * HTML Select without label before
	 */
	static function iLayerSearchEnt()
	{
return '<form id="formSearchContactEnt" action="Contact.php?action=searchEnt" onsubmit="return WA.Submit(this,null,event)">
		<div class="iPanel">
			<div id="form-contactSearchEnt"></div>
			<fieldset>
				<ul>
					<li><input type="text" name="query" placeholder="recherche" /></li>
				</ul>
				<br/>
				<a href="#" onclick="return WA.Submit(\'formSearchContactEnt\',null,event)" class="whiteButton"><img src="Img/search.png" style="float: left"/><span>Lancer la recherche</span></a>
			</fieldset>
		</div>
	</form>';
	}

	/**
	 * HTML Select without label before
	 */
	static function iLayerSearchPers()
	{
return '<form id="formSearchContactPers" action="Contact.php?action=searchPart" onsubmit="return WA.Submit(this,null,event)">
		<div class="iPanel">
			<div id="form-contactSearchPers"></div>
			<fieldset>
				<ul>
					<li><input type="text" name="query" placeholder="recherche" /></li>
				</ul>
				<br/>
				<a href="#" onclick="return WA.Submit(\'formSearchContactPers\',null,event)" class="whiteButton"><img src="Img/search.png" style="float: left"/><span>Lancer la recherche</span></a>
			</fieldset>
		</div>
	</form>';
	}

	/**
	 * HTML Select without label before
	 */
	static function headerFormSearchPers()
	{
		if($_SESSION['searchContactQuery'] != '') $iTag = ' value="'.$_SESSION['searchContactQuery'].'"';
return '<form action="Contact.php?action=inputContactResult" id="formSearchContact" onsubmit="return WA.Submit(this,null,event)"><div  class="iFormI">
			<a href="#_SearchContactResult" rel="action" onclick="return WA.Submit(\'formSearchContact\',null,event)" class="iButton iBAction"><img src="Img/search.png" alt="Recherche" /></a>

			<fieldset class="attach">
				<legend>Recherche</legend>
				<input type="search" name="search" placeholder="Votre recherche" id="formSearchContactInput"'.$iTag.'/>
			</fieldset>
	</div></form><div id="SearchContactResultAsync" style="display:block"></div>';
	}
}

?>
