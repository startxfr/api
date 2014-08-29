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
    $data['from'] = 'bugtrack@zuno.fr <instance '.$GLOBALS['zunoWebService']['instance_code'].'>';
    $data['sujet'] = 'Rapport de bug de : '.$GLOBALS['zunoClientCoordonnee']['nom'];
    $data['message'] = "Rapport de bug : "."\n"."Rapporteur : ".$_SESSION['user']['fullnom']."\n";
    $data['message'] .= "Instance : ".$GLOBALS['zunoWebService']['instance_code']."\n";
    $data['message'] .= "Titre (selon le client) : ".$_POST['loca_bug']."\n";
    $data['message'] .= "URL d'envoi : ".$_SERVER['HTTP_REFERER'];
    $data['message'] .= "\nDescription : ".$_POST['desc_bug']."\n";
    $data['bug'] = true;

    $send = new Sender($data);
    $rs = $send->send();
    if($rs[0])
	echo '<div id="BodyContent"><span class="importantgreen" id="resultBugMsg">Votre rapport de bug à bien été enregistré.<br/>Nous vous remercions de votre participation à l\'amélioration du service Zuno.</span>
	      <script type="text/javascript">$(\'idreportBugformFull\').style.display = \'none\';$(\'fieldLocaBug\').value = \'\';$(\'fieldDescBug\').value = \'\'; setTimeout(function() { zuno.contextBox.close(); },1500); setTimeout(function() { $(\'idreportBugformFull\').style.display = \'block\';$(\'resultBugMsg\').style.display = \'none\'; },2500);</script></div>';
    else
	echo '<erreur>returnMessageRapportBug</erreur>Erreur lors de l\'envoi de votre rapport.<br/>Vous pouvez renouveller votre envoi dans quelques minutes ou contacter notre hotline.';

    exit;
}

?>