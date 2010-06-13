<?php
/*#########################################################################
#
#   name :       SendLetter.inc
#   desc :       Prospection list for sxprospec
#   categorie :  plugin module
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/
loadPlugin('Send/Send');


function SendLetter($address, $file) {
    return SendLetterMaileva($address, $file);
}


//$address = array(
//		'nom1' => 'STARTX',
//		'nom2' => 'Mr Christophe LARUE',
//		'add1' => '83 rue de reuilly',
//		'add2' => 'batiment B, 4eme Etage',
//		'add3' => 'encore une idication',
//		'cp'   => 'cp',
//		'ville'=> 'ville',
//		'nom_pays' => 'France',
//		'code_pays' => 'FR',
//	);
function SendLetterMaileva($datas, $file) {
	$datas['partie'] = 'send';
	$datas['path'] = '';
	$datas['typeE'] = 'courrier';
	$datas['message'] = ' ';
	$datas['sujet'] = ' ';
	$datas['expediteur'] = $_SESSION['user']['fullnom'];
	$datas['destinataire'] = $datas['nom1'];
	$datas['add1'] = $datas['add1'];
	$datas['add2'] = $datas['add2'];
	$datas['cp'] = $datas['cp'];
	$datas['ville'] = $datas['ville'];
	$datas['pays'] = $datas['cpays'];
	$datas['fichier'] = $file;
	$send = new Sender($datas);
	return $send->send();
}

?>
