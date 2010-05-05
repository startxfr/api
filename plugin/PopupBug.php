<?php
/*#########################################################################
#
#   name :       PopupBug.php
#   desc :       Display page content
#   categorie :  devis
#   ID :  	 $Id: Devis.php 2814 2009-06-29 14:54:25Z nm $
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| FRAMEWORK LOADING
+------------------------------------------------------------------------*/
include ('../inc/conf.inc');	// Declare global variables from config files
include ('../inc/core.inc');	// Load core library
loadPlugin(array('ZunoCore','Send/Send', 'ZunoRenduHTML'));

// Whe get the page context
$PC = new PageContext();
$PC->GetVarContext();
$PC->GetChannelContext();
$PC->GetSessionContext();

/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/

if($_POST['action'] == 'sendBug') {
    $data['typeE'] = 'mail';
    $data['mail'] = $GLOBALS['PROJET']['mail'];
    $data['from'] = 'zuno@startx.fr';
    $data['sujet'] = 'Rapport de bug de : '.$GLOBALS['zunoClientCoordonnee']['nom'];
    $data['message'] = "Rapport de bug : "."\n"."Rapporteur : ".$_SESSION['user']['fullnom']."\n";
    $data['message'] .= "Titre (selon le client) : ".$_POST['loca_bug']."\n";
    $data['message'] .= "URL d'envoi : ".$_SERVER['HTTP_REFERER'];
    $data['message'] .= "Description : ".$_POST['desc_bug']."\n";
    $data['bug'] = true;

    $send = new Sender($data);
    $rs = $send->send();
    if($rs[0])
	echo '<div id="BodyContent"><span class="importantgreen">Votre rapport de bug à bien été enregistré. Nous vous remercions de votre participation à l\'amélioration du service Zuno qui devient chaque jour un peu plus le votre.</span>
	      <script type="text/javascript">$(\'idreportBugformFull\').style.display = \'none\'; setTimeout(function() { zuno.contextBox.close();$(\'idreportBugformFull\').style.display = \'block\'; },2000);</script></div>';
    else
	echo '<erreur>returnMessageRapportBug</erreur>Erreur lors de l\'envoi de votre rapport.<br/>Vous pouvez renouveller votre envoi dans quelques minutes ou contacter notre hotline.';

    exit;
}

?>
