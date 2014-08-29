<?php

/**
 *
 */
class ZunoLayerPreference {
    /**
     * HTML Select without label before
     */
    static function loadDefaultLayer() {
        $footer = HtmlElementIphone::footer();
        return '	<!------------------------------------ -->
		<!-- PREFERENCE : Menu général         -->
		<!------------------------------------ -->
		<div class="iLayer" id="waMainMenuPref" title="Préférences">
			'.self::iMenuGeneral().$footer.'
		</div>
		<div class="iLayer" id="waMenuPrefsAssist" title="Assistance">
			'.self::partAssist().$footer.'
		</div>
		<div class="iLayer" id="waMenuToolbox" title="Toolbox">
			<div class="iMenu"><ul class="iArrow">
				<li><a href="Preference.php?action=CP" rev="async">Code Postal</a></li>
				<li><a href="Preference.php?action=TVA" rev="async">Calcul TVA</a></li>
			</ul></div>
		</div>';
    }

    /**
     * HTML Select without label before
     */
    static function iMenuGeneral() {
        if($_SESSION['user']['right']<2)
            $mess = '<ul class="iArrow">
				<li><a href="Preference.php?action=session" rev="async"><img src="'.getStaticUrl('imgPhone').'iconMenu/pref.session.png" />Session</a></li>
				<li><a href="Preference.php?action=debug" rev="async"><img src="'.getStaticUrl('imgPhone').'iconMenu/pref.debug.png" />Debug</a></li>
			</ul>';
        else $mess = '';
        return '<div class="iMenu">
			<ul class="iArrow">
				<li><a href="Preference.php?action=profil" rev="async"><img src="'.getStaticUrl('imgPhone').'iconMenu/pref.profil.png" />Profil</a></li>
                                <li><a href="Preference.php?action=serveur" rev="async"><img src="'.getStaticUrl('imgPhone').'iconMenu/pref.serveur.png" />Serveur</a></li>
			</ul>
			'.$mess.'
			<ul class="iArrow">
				<li><a href="#_MenuToolbox">Toolbox</a></li>
			</ul>
			<br/>
			<ul class="iArrow">
				<li><a href="#_MenuPrefsAssist"><img src="'.getStaticUrl('imgPhone').'iconMenu/pref.assist.png" /><i style="color:#900">Assistance</i></a></li>
			</ul>
		</div>';
    }


    /**
     * HTML Select without label before
     */
    static function partProfil() {
        $listeChoix = array(5=>'5',10=>'10',15=>'15',20=>'20');
        return '<a href="#"  onclick="return WA.Submit(\'formModifProfil\',null,event)" rel="action" class="iButton iBAction"><img src="'.getStaticUrl('imgPhone').'save.png" alt="Enregistrer" /></a>
				<div class="iPanel">
					<div id="formProfilResult"></div>
					<fieldset>
						<legend>Informations</legend>
						<ul class="iList">
							<li><strong>'.$_SESSION['user']['fullnom'].'</strong><br/>
							<small>'.$_SESSION['user']['rightDesc'].'</small></li>
						</ul>
					</fieldset>
					<form id="formModifProfil" action="Preference.php?action=recordProfil" onsubmit="return WA.Submit(this,null,event)">
						<fieldset>
							<legend>Configuration</legend>
							<ul>
								<li>'.HtmlFormIphone::InputLabel('nom',$_SESSION['user']['nom'],'Nom', '" onchange="autosaveProfilBis(0, this);"').'</li>
								<li>'.HtmlFormIphone::InputLabel('prenom',$_SESSION['user']['prenom'],'Prenom', '" onchange="autosaveProfilBis(0, this);"').'</li>
								<li>'.HtmlFormIphone::InputLabel('login',$_SESSION['user']['id'],'Login', '" onchange="autosaveProfilBis(0, this);"').'</li>
								<li>'.HtmlFormIphone::Checkbox('actif','Actif','OUI|NON','ok', 'onclick="autosaveProfil(0);"').'</li>
								<li>'.HtmlFormIphone::Checkbox('autocorrect','Correction auto','OUI|NON',$_SESSION['user']['config']['autocorrect'], 'onclick="autosaveProfil(0);"').'</li>
								<li>'.HtmlFormIphone::Checkbox('majauto','Majuscule auto','OUI|NON',$_SESSION['user']['config']['autocapitalize'], 'onclick="autosaveProfil(0);"').'</li>
								<input type="hidden" value="'.$_SESSION['user']['id'].'" name="login_hidden" id="login_pref_hidden" />
								<input type="hidden" value="'.$_SESSION['user']['nom'].'" name="nom_hidden" id="nom_pref_hidden" />
								<input type="hidden" value="'.$_SESSION['user']['prenom'].'" name="prenom_hidden" id="prenom_pref_hidden" />
							</ul>
						</fieldset>
						<fieldset>
							<legend>Gestions des longueurs de listes</legend>
							<ul>
								'.HtmlFormIphone::Radio('LenghtSearchActualite',$listeChoix,$_SESSION['user']['config']['LenghtSearchActualite'],'Actualités',false, 'onclick="autosaveProfil(2000);"').'
								'.HtmlFormIphone::Radio('LenghtSearchGeneral',$listeChoix,$_SESSION['user']['config']['LenghtSearchGeneral'],'Recherche globale',false, 'onclick="autosaveProfil(2000);"').'
								'.HtmlFormIphone::Radio('LenghtSearchContactEnt',$listeChoix,$_SESSION['user']['config']['LenghtSearchContactEnt'],'Recherche des entreprises',false, 'onclick="autosaveProfil(2000);"').'
								'.HtmlFormIphone::Radio('LenghtSearchContactPart',$listeChoix,$_SESSION['user']['config']['LenghtSearchContactPart'],'Recherche  des particuliers',false, 'onclick="autosaveProfil(2000);"').'
								'.HtmlFormIphone::Radio('LenghtSearchAffaire',$listeChoix,$_SESSION['user']['config']['LenghtSearchAffaire'],'Recherche des affaires',false, 'onclick="autosaveProfil(2000);"').'
								'.HtmlFormIphone::Radio('LenghtSearchDevis',$listeChoix,$_SESSION['user']['config']['LenghtSearchDevis'],'Recherche des devis',false, 'onclick="autosaveProfil(2000);"').'
								'.HtmlFormIphone::Radio('LenghtSearchCommande',$listeChoix,$_SESSION['user']['config']['LenghtSearchCommande'],'Recherche des commandes',false, 'onclick="autosaveProfil(2000);"').'
								'.HtmlFormIphone::Radio('LenghtSearchFacture',$listeChoix,$_SESSION['user']['config']['LenghtSearchFacture'],'Recherche des factures',false, 'onclick="autosaveProfil(2000);"').'
								'.HtmlFormIphone::Radio('LenghtSearchProduit',$listeChoix,$_SESSION['user']['config']['LenghtSearchProduit'],'Recherche des produits',false, 'onclick="autosaveProfil(2000);"').'
							</ul>
						</fieldset>
						<div style="display:none"><a id="liendeautosaveProfil" href="#_MenuPrefsProfil" onclick="return WA.Submit(\'formModifProfil\',null,event)">Lien caché</a></div>
					</form>
				</div>';
    }

    /**
     * HTML Select without label before
     */
    static function partSession() {
        $GLOBALS['DGV'] = '';
        array_walk($_SESSION,'displayGlobVar');
        return '<div class="iPanel">
					<fieldset>
						<legend>Information stockée</legend>
						<ul>'.$GLOBALS['DGV'].'
						</ul>
					</fieldset>
				</div>';
    }

    /**
     * HTML Select without label before
     */
    static function partServeur() {
        return '<div class="iPanel">
				<fieldset>
					<legend>Informations</legend>
					<ul>
						<li><label>Referer : </label><small>'.$_SERVER['HTTP_ACCEPT_LANGUAGE'].'</small></li>
						<li><label>Encoding : </label><small>'.$_SERVER['HTTP_ACCEPT_ENCODING'].'</small></li>
						<li><label>Connexion : </label><small>'.$_SERVER['HTTP_CONNECTION'].'</small></li>
						<li><label>Moteur : </label><small>'.$_SERVER['SERVER_SOFTWARE'].'</small></li>
						<li><label>Nom : </label><small>'.$_SERVER['SERVER_NAME'].'</small></li>
						<li><label>IP : </label><small>'.$_SERVER['SERVER_ADDR'].' on '.$_SERVER['SERVER_PORT'].'</small></li>
						<li><a href="mailto:'.$_SERVER['SERVER_ADMIN'].'"><label>Admin : </label><span><img src="'.getStaticUrl('imgPhone').'iconMenu/mail.png" /></span>'.$_SERVER['SERVER_ADMIN'].'</a></li>
					</ul>
				</fieldset>
				</div>'.$GLOBALS['DGV'].'
						</ul>
					</fieldset>
				</div>';
    }

    /**
     * HTML Select without label before
     */
    static function partDebug() {
        $GLOBALS['DGV'] = '';
        array_walk($_GET,'displayGlobVar');
        $out = '<div class="iPanel">
			  <fieldset>
				<legend>Paramètres GET</legend>
				<ul>'.$GLOBALS['DGV'];
        $GLOBALS['DGV'] = '';
        array_walk($_COOKIE,'displayGlobVar');
        $out.= '</ul>
				<legend>Paramètres COOKIE</legend>
				<ul>
					'.$GLOBALS['DGV'].'
				</ul>
			</fieldset>';
        $out .='<fieldset><legend>$_SESSION</legend><ul>';
        $out .= displayArrayAsList($_SESSION).'</ul></fieldset></div>';
        return $out;
    }

    /**
     * HTML Select without label before
     */
    static function partAssist() {
        return '<div class="iBlock">
					<h1>Niveaux d\'Assistance</h1>
					<p>2 niveaux de support sont disponibles en fonction de votre abonnement:<br/>
					* Support Web (réponse en 24h)<br/>
					* Support téléphonique (prise en compte en 2h)</p>
					<br/>
					<h1>Assistance Web</h1>
					<p>Pour accéder au support par mail, merci d\'envoyer votre demande à <a href="mailto:hl@zuno.fr">Hot Line ZUNO</a>. La prise en compte de votre demande vous sera notifiée par retour avec votre ticket d\'incident. Le temps d\'intervention vous seras facturé sur votre prochaine facture.</p>
					<br/>
					<h1>Assistance téléphonique</h1>
					<p>L\'accès  notre serveur vocal est disponible au numéro suivant: <a href="tel:0148250364">01 48 25 03 64</a><sup>(0.8&euro;/min)</sup>. La prise en compte de votre demande vous sera notifiée par retour avec votre ticket d\'incident. Le temps d\'intervention vous seras facturé sur votre prochaine facture.</p>
				</div>';
    }

}



function displayGlobVar( $value, $key) {
    $GLOBALS['DGV'] .= '<li><label>'.$key.' : </label><small>';
    if(is_array($value)) $GLOBALS['DGV'] .= 'array('.count($value).')';
    elseif(strlen($value) > 38)
        $GLOBALS['DGV'] .= substr($value,0,38).'...';
    else $GLOBALS['DGV'] .=  $value;
    $GLOBALS['DGV'] .= '</small></li>';
}
?>
