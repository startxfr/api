<?php
/*#########################################################################
#
#   name :       SendFax.inc
#   desc :       Prospection list for sxprospec
#   categorie :  plugin module
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/
loadPlugin(array('Send/Send','Send/Send'));


function SendFax($coordonnee, $file) {
    return SendFaxMaileva($coordonnee, $file);
}

//$coordonnee = array(
//		'nom1' => 'STARTX',
//		'nom2' => 'Mr Christophe LARUE',
//		'tel' => '33-(0)1 48 25 03 64',
//		'fax' => '33-(0)1 46 99 89 97',
//	);

function SendFaxMaileva($datas, $file) {
    $datas['partie'] = 'send';
    $datas['path'] = '';
    $datas['typeE'] = 'fax';
    $datas['message'] = ' ';
    $datas['sujet'] = ' ';
    $datas['expediteur'] = $_SESSION['user']['fullnom'];
    $datas['destinataire'] = $datas['nom1'];
    $datas['tel'] = $datas['tel'];
    $datas['fax'] = $datas['fax'];
    $datas['fichier'] = $file;
    $send = new Sender($datas);
    return $send->send();
}

?>
