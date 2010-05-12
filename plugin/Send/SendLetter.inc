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

function SendLetterMaileva($address, $file) {
    if(array_key_exists('nom',$address) and
	    !array_key_exists('nom1',$address))
	$address['nom1'] = $address['nom'];

    if(is_array($address) and
	    array_key_exists('nom1',$address) and
	    array_key_exists('add1',$address) and
	    array_key_exists('cp',$address) and
	    array_key_exists('ville',$address) and
	    (array_key_exists('nom_pays',$address) or
		    array_key_exists('code_pays',$address)) and
	    file_exists($file)) {
	$fill = array();
	if(strlen($address['nom1']) > 38) {
	    $fill[] = substr($address['nom1'],0,37).'-';
	    $fill[] = substr($address['nom1'],37,38);
	}
	else  $fill[] = $address['nom1'];
	if(strlen($address['nom2']) > 38) {
	    if(count($fill) <= 1) {
		$fill[] = substr($address['nom2'],0,37).'-';
		$fill[] = substr($address['nom2'],37,38);
	    }
	    elseif(strlen(trim($address['add2'])) <= 1) {
		$fill[] = substr($address['nom2'],0,37).'-';
		if(strlen($address['nom2']) > 76) $adds = '...';
		$fill[] = substr($address['nom2'],37,35).$adds;
	    }
	    else $fill[] = substr($address['nom2'],0,35).'...';
	}
	elseif($address['nom2'] != '')  $fill[] = $address['nom2'];

	if(strlen($address['add1']) > 38)
	    $fill[] = substr($address['add1'],0,37).'.';
	else  $fill[] = $address['add1'];
	if(strlen($address['add2']) > 38)
	    $fill[] = substr($address['add2'],0,37).'.';
	else  $fill[] = $address['add2'];

	if($address['nom_pays']  == '' or
		$address['code_pays'] == '') {
	    $sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	    if($address['code_pays']  != '') {
		$sqlConn->makeRequeteFree("select code_pays, nom_pays from ref_pays WHERE code_pays = '".$address['code_pays']."';");
		$temp = $sqlConn->process2();
		$temp=$temp[1];
		$address['nom_pays']  = strtoupper($temp[0]['nom_pays']);
		$address['code_pays'] = strtoupper($temp[0]['code_pays']);
	    }
	    elseif($address['nom_pays']  != '') {
		$sqlConn->makeRequeteFree("select code_pays, nom_pays from ref_pays WHERE nom_pays = '".$address['nom_pays']."';");
		$temp = $sqlConn->process2();
		$temp=$temp[1];
		$address['nom_pays']  = strtoupper($temp[0]['nom_pays']);
		$address['code_pays'] = strtoupper($temp[0]['code_pays']);
	    }
	}
	else {
	    $address['nom_pays'] = strtoupper($address['nom_pays']);
	    $address['code_pays'] = strtoupper($address['code_pays']);
	}
	$addSynt = $address['nom1'].'<br/>';
	$addSynt.= $address['nom2'].'<br/>';
	$addSynt.= $address['add1'].'<br/>';
	$addSynt.= ($address['add2'] != '') ? $address['add2'].'<br/>' : '';
	$addSynt.= $address['cp'].' '.strtoupper($address['ville']).'<br/>';
	$addSynt.= strtoupper($address['nom_pays']).'<br/>';


	$address['PID1'] = $fill[0];
	$address['PID2'] = $fill[1];
	$address['PID3'] = $fill[2];
	$address['PID4'] = $fill[3];
	$address['PID5'] = $fill[4];
	$address['PID6'] = $address['cp'].' '.strtolower($address['ville']);
	$address['PID7'] = $address['nom_pays'];
	$address['CT'] = $address['code_pays'];

	$address = array_merge($address,$GLOBALS['MailevaConf']);
	$pjs = templating('maileva',$address);
	$TmpPath = $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'];
	$TmpDir = substr(md5(rand(1000,9999)+rand(1000,9999)),0,8).'/';
	@rm($TmpPath.$TmpDir);
	mkdir($TmpPath.$TmpDir);
	$TmpFile = $GLOBALS['MailevaConf']['pjsFileName'];
	touch($TmpPath.$TmpDir.$TmpFile);
	File_Add2File($TmpPath.$TmpDir.$TmpFile,$pjs,TRUE);

	$files[] = $file;
	$files[] = $TmpPath.$TmpDir.$TmpFile;

	$o = MailAttach($GLOBALS['MailevaConf']['mailToCourrier'],' ',$files,'','',' ');
	if($o) return array(true,$addSynt);
	else	 return array(false,'Erreur lors de l\'envoi du mail vers le service de courrier.');
    }
    else {
	if(!is_array($address))
	    $err = 'Vous devez fournir un tableau à la fonction SendLetter().';
	elseif(!file_exists($file))
	    $err = 'Vous devez fournir un fichier existant à la fonction SendLetter().';
	else {
	    $err = 'Vous devez fournir ';
	    if(!array_key_exists('nom1',$address))
		$err .= ' le nom du déstinataire (nom1), ';
	    if(!array_key_exists('add1',$address))
		$err .= ' l\'adresse du déstinataire (add1), ';
	    if(!array_key_exists('cp',$address))
		$err .= ' le code postal du déstinataire (cp), ';
	    if(!array_key_exists('ville',$address))
		$err .= ' la ville déstinataire (ville), ';
	    if(!array_key_exists('nom_pays',$address) or !array_key_exists('code_pays',$address))
		$err .= ' le nom,  ou le code, du pays du déstinataire (nom_pays, code_pays), ';
	    $err = substr($err,0,-2);
	}
	return array(false,'Il manque des informations pour l\'envoi par courrier. '.$err);
    }
}

?>
