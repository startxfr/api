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


function SendFax($coordonnee, $file) {
    return SendFaxMaileva($coordonnee, $file);
}




//$coordonnee = array(
//		'nom1' => 'STARTX',
//		'nom2' => 'Mr Christophe LARUE',
//		'tel' => '33-(0)1 48 25 03 64',
//		'fax' => '33-(0)1 46 99 89 97',
//	);

function SendFaxMaileva($coordonnee, $file) {
    if(array_key_exists('nom',$coordonnee) and
	    !array_key_exists('nom1',$coordonnee))
	$coordonnee['nom1'] = $coordonnee['nom'];

    if(is_array($coordonnee) and
	    array_key_exists('nom1',$coordonnee) and
	    array_key_exists('fax',$coordonnee) and
	    file_exists($file)) {
	if(substr(trim($coordonnee['fax']),0,1) == '+') {
	    $f = str_replace('.',' ',$coordonnee['fax']);
	    $f = str_replace('-',' ',$f);
	    $fx = explode(' ',$f, 2);
	    $coordonnee['fax'] = $fx[0].'-'.$fx[1];
	}
	elseif(substr(trim($coordonnee['fax']),0,1) === '0')
	    $coordonnee['fax'] = '33-(0)'.substr($coordonnee['fax'],1);
	if(substr(trim($coordonnee['tel']),0,1) == '+') {
	    $f = str_replace('.',' ',$coordonnee['tel']);
	    $f = str_replace('-',' ',$f);
	    $fx = explode(' ',$f, 2);
	    $coordonnee['tel'] = $fx[0].'-'.$fx[1];
	}
	elseif(substr(trim($coordonnee['tel']),0,1) === '0')
	    $coordonnee['tel'] = '33-(0)'.substr($coordonnee['tel'],1);

	$addSynt = $coordonnee['nom1'].' - ';
	$addSynt.= $coordonnee['nom2'].'. Fax: ';
	$addSynt.= $coordonnee['fax'];

	$coordonnee['info'] = '';
	if(trim($coordonnee['nom1']) != '')
	    $coordonnee['info'].= 'MSG_FFN='.substr(trim($coordonnee['nom1']),0,64)."\n";
	if(trim($coordonnee['nom2']) != '')
	    $coordonnee['info'].= 'MSG_CORP='.substr(trim($coordonnee['nom2']),0,64)."\n";
	if(trim($coordonnee['tel']) != '')
	    $coordonnee['info'].= 'TN='.$coordonnee['tel']."\n";
	$coordonnee['info'].= 'FN='.$coordonnee['fax']."\n";

	$coordonnee = array_merge($coordonnee,$GLOBALS['MailevaConf']);
	$pjs = templating('maileva.fax',$coordonnee);
	$TmpPath = $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'];
	$TmpDir = substr(md5(rand(1000,9999)+rand(1000,9999)),0,8).'/';
	@rm($TmpPath.$TmpDir);
	mkdir($TmpPath.$TmpDir);
	$TmpFile = $GLOBALS['MailevaConf']['pjsFileName'];
	touch($TmpPath.$TmpDir.$TmpFile);
	File_Add2File($TmpPath.$TmpDir.$TmpFile,$pjs,TRUE);

	$files[] = $file;
	$files[] = $TmpPath.$TmpDir.$TmpFile;

	$o = MailAttach($GLOBALS['MailevaConf']['mailToFax'],' ',$files,'','',' ');
	if($o) return array(true,$addSynt);
	else	 return array(false,'Erreur lors de l\'envoi du mail vers le service de fax.');
    }
    else {
	if(!is_array($coordonnee))
	    $err = 'Vous devez fournir un tableau à la fonction SendFax().';
	elseif(!file_exists($file))
	    $err = 'Vous devez fournir un fichier existant à la fonction SendFax().';
	else {
	    $err = 'Vous devez fournir ';
	    if(!array_key_exists('nom1',$coordonnee))
		$err .= ' le nom du destinataire (nom1), ';
	    if(!array_key_exists('tel',$coordonnee))
		$err .= ' le numéro de téléphone du destinataire (tel), ';
	    if(!array_key_exists('fax',$coordonnee))
		$err .= ' le numéro de fax destinataire (fax), ';
	    $err = substr($err,0,-2);
	}
	return array(false,'Il manque des informations pour l\'envoi par fax. '.$err);
    }
}

?>
