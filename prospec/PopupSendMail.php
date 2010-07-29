<?php
/*#########################################################################
#
#   name :       PopupSendMail.php
#   desc :       Display page content
#   categorie :  page
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| FRAMEWORK LOADING
+------------------------------------------------------------------------*/
include ('../inc/conf.inc');	// Declare global variables from config files
include ('../inc/core.inc');	// Load core library
loadPlugin(array('ZunoCore', 'ZModels/ContactModel', 'ZView/ContactView', 'Send/Send'));
// Whe get the page context
$PC = new PageContext('prospec');
$PC->GetFullContext();
/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/


if($PC->rcvP['action'] == 'new') {
    $sql = new contactParticulierModel();
    $result = $sql->getDataFromID($PC->rcvP['id_cont']);
    $datas['id'] = $PC->rcvP['id_cont'];
    $datas['partie'] = 'contactParticulier';
    $datas['typeE'] = $PC->rcvP['type'];
    $datas['message'] = $PC->rcvP['mess'];
    $datas['sujet'] = $PC->rcvP['titre'];
    $datas['expediteur'] = $_SESSION['user']['fullnom'];
    $datas['from'] = $PC->rcvP['sender'];
    $datas['cc'] = $PC->rcvP['cc'];
    $datas['mail'] = $PC->rcvP['to'];
    $datas['fax'] = $result[1][0]['fax_cont'];
    $datas['tel'] = $result[1][0]['tel_cont'];
    $datas['destinataire'] = $result[1][0]['civ_cont'].' '.$result[1][0]['prenom_cont'].' '.$result[1][0]['nom_cont'];
    $datas['add1'] = $result[1][0]['add1_cont'];
    $datas['add2'] = $result[1][0]['add2_cont'];
    $datas['cp'] = $result[1][0]['cp_cont'];
    $datas['ville'] = $result[1][0]['ville_cont'];
    $datas['pays'] = $result[1][0]['pays_cont'];
    $datas['fichier'] = $PC->rcvP['file'];
    $send = new Sender($datas);
    $result = $send->send();
    if($result[0])
	echo $result[1];
    else	echo '<erreur>errorPopupMail</erreur>'.$result[1];
    exit;
}
elseif($PC->rcvG['id_cont'] != '') {
    $bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
    $id_cont = $PC->rcvG['id_cont'];
    $bddtmp->makeRequeteFree("SELECT * FROM entreprise,contact WHERE entreprise_cont = id_ent AND id_cont = '".$id_cont."'");
    $cont = $bddtmp->process();
    $cont = $cont[0];
    if ($cont['mail_cont'] != '')
	echo BlockSendmail($id_cont,"");
    else  echo "<html><body><script language=\"javascript\">zuno.popup.close();</script></body></html>";
}
else echo BlockSendmail('',"");
?>
