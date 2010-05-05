<?php

/**
 *
 */
class ZunoLayerGeneral
{
	/**
	 * HTML Select without label before
	 */
	static function loadDefaultLayer($isAuthentified = false)
	{
		$footer = HtmlElementIphone::footer();
		if($isAuthentified)
		{
$out = '<!-- Layer du menu principal puis de l\'accueil -->
	<div class="iLayer" id="waMainMenu" title="Menu Principal">
		'.self::iMenuGeneral().$footer.'
	</div>
	<div class="iLayer" id="waHome" title="Bienvenue">
		'.self::iFormLogin().$footer.'
	</div>';
$out .= '<div class="iLayer" id="waNavigator" title="Navigateur"></div>';

		}
		else
		{
			if(!array_key_exists('login', $_COOKIE))
$out = '<!-- Layer de l\'accueil puis du menu principal -->
	<div class="iLayer" id="waHome" title="Bienvenue">
		'.self::iFormLogin().$footer.'
	</div>
	<div class="iLayer" id="waMainMenu" title="Menu Principal">
		'.self::iMenuGeneral().$footer.'
	</div>';
	else	{
				$out = '<!-- Layer de l\'accueil puis du menu principal -->
	<div class="iLayer" id="waHomePin" title="Code PIN">
		'.self::iFormPin().'</div>
	<div class="iLayer" id="waHome" title="Bienvenue">
		'.self::iFormLogin().$footer.'
	</div>
	<div class="iLayer" id="waMainMenu" title="Menu Principal">
		'.self::iMenuGeneral().$footer.'
	</div>';
			}
		}

	return $out;
	}

	/**
	 * HTML Select without label before
	 */
	static function iFormLogin()
	{
return '<form id="formConnect" id="formConnectName" action="Connect.php" onsubmit="return WA.Submit(this,null,event)">
		<div class="iPanel">
			<h1><img src="Img/zunoHomeP.png" alt="ZUNO" id="homePageHeaderImg"/></h1>
			<div id="form-connect"></div>
			<fieldset>
				<ul>
					<li><input type="text" name="login" placeholder="Identifiant" autocorrect = "off" autocapitalize="off"/></li>
					<li><input type="password" name="pwd" placeholder="Mot de passe" /></li>
				</ul>
			</fieldset>
			<fieldset>
					<a href="#" class="BigButtonValidLeft"><img src="Img/big.annuler.png" alt="Annuler" /></a>
					<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formConnect\', null, event)"><img src="Img/big.valider.png" alt="Valider" /></a>
			</fieldset>
		</div>
		</form>';
	}

		/**
	 * HTML Select without label before
	 */
	static function iFormPin($erreur = 'non')
	{
		$message = ($erreur == 'oui') ? '<div class="err"><strong>Mauvais code pin</strong></div>' : '';
		$chiffres = '<div class="chiffreslogin">
					<div class="chiffres" id="chiffre0">&nbsp;</div>
					<div class="chiffres" id="chiffre1">&nbsp;</div>
					<div class="chiffres" id="chiffre2">&nbsp;</div>
					<div class="chiffres" id="chiffre3">&nbsp;</div>
				</div>';
		$clavier = '<div class="clavier">
					<div class="lignepin">
						<div class="touche" ontouchstart="ajoutepin(\'1\');"><img src="Img/pin/1.png"/></div>
						<div class="touche" ontouchstart="ajoutepin(\'2\');"><img src="Img/pin/2.png"/></div>
						<div class="touche" ontouchstart="ajoutepin(\'3\');"><img src="Img/pin/3.png"/></div>
					</div>
					<div class="lignepin">
						<div class="touche" ontouchstart="ajoutepin(\'4\');"><img src="Img/pin/4.png"/></div>
						<div class="touche" ontouchstart="ajoutepin(\'5\');"><img src="Img/pin/5.png"/></div>
						<div class="touche" ontouchstart="ajoutepin(\'6\');"><img src="Img/pin/6.png"/></div>
					</div>
					<div class="lignepin">
						<div class="touche" ontouchstart="ajoutepin(\'7\');"><img src="Img/pin/7.png"/></div>
						<div class="touche" ontouchstart="ajoutepin(\'8\');"><img src="Img/pin/8.png"/></div>
						<div class="touche" ontouchstart="ajoutepin(\'9\');"><img src="Img/pin/9.png"/></div>
					</div>
					<div class="lignepin">
						<div class="touche" ontouchstart="ajoutepin(\'*\');"><img src="Img/pin/etoile.png"/></div>
						<div class="touche" ontouchstart="ajoutepin(\'0\');"><img src="Img/pin/0.png"/></div>
						<div class="touche" ontouchstart="ajoutepin(\'#\');"><img src="Img/pin/dieze.png"/></div>
					</div>
					<div class="lignepin">
						<div class="touche" ontouchstart="videpin();"><img src="Img/pin/cancel.png"/></div>
						<div class="touche"><a href="#_Home"><img src="Img/pin/user.png"/></a></div>
						<div class="touche" ontouchstart="soumettrepin();"><img src="Img/pin/valider.png"/></div>
					</div>
				</div>';

		$form = '<div style="display:none">
					<form id="formPin" action="Connect.php?action=verifPin" onsubmit="return WA.Submit(this,null,event)">
						<input type="hidden" value="" name="pinCode" id="pinCode" />
					</form>
					<a href="#" id="lienactivationpin" onclick="return WA.Submit(\'formPin\',null,event)">Lien caché</a>
				</div>';
		return $message.$chiffres.$clavier.$form;
	}


	/**
	 * HTML Select without label before
	 */
	static function iMenuGeneral()
	{
		return
		'<a href="#_MainMenu" rel="action"></a><a href="#_MainMenu" rel="back"></a>
		<div class="iMenu">
		<ul class="iArrow">
			<li><a href="#_MenuContact" class="Contact"><img src="Img/iconMenu/contact.png" />Contacts</a></li>
			<li onClick="chargeAuto(this,\'lienAutoChargeAffaireTri\')"><a href="#_MenuAffaire" class="Affaire"><img src="Img/iconMenu/affaire.png" />Affaires</a></li>
			<li onClick="chargeAuto(this,\'lienAutoChargeDevisTri\')"><a href="#_MenuDevis" class="Devis"><img src="Img/iconMenu/devis.png" />Devis</a></li>
			<li onClick="chargeAuto(this,\'lienAutoChargeCommandeTri\')"><a href="#_MenuCommande" class="Commande"><img src="Img/iconMenu/commande.png" />Commandes</a></li>
			<li onClick="chargeAuto(this,\'lienAutoChargeFactureTri\')"><a href="#_MenuFacture" class="Facture"><img src="Img/iconMenu/facture.png" />Factures</a></li>
			<li><a href="#_MenuProduit" class="Produit"><img src="Img/iconMenu/produit.png" />Produits</a></li>
		</ul>
		<ul class="iArrow">
			<li><a href="Actualite.php" rev="async" class="Actualite"><img src="Img/iconMenu/actualite.png" />Actualités</a></li>
			<li><a href="Navigator.php?action=racine" rev="async" class="Navigator"><img src="Img/iconMenu/Dossiers.png" />Navigateur</a></li>
			<li><a href="Search.php" rev="async" class="Search"><img src="Img/iconMenu/search.png" />Recherche</a></li>
		</ul>
		<ul class="iArrow">
			<li><a href="#_MainMenuPref" class="Preferences"><img src="Img/iconMenu/pref.png" />Préférences</a></li>
			<li class="iLess"><a href="Preference.php?action=disconnect#_Home" rev="async" class="Deconnexion"><img src="Img/iconMenu/disconnect.png" />Déconnexion</a></li>
		</ul>
	</div>';
	}

}

?>
