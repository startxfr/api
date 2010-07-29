<?php
/*#########################################################################
#
#   name :       mail.inc
#   desc :       library for mail management
#   categorie :  core module
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| simple_mail
|
| send mail filled with given text
+-------------------------------------------------------------------------+
| $to		*   recever
| $message	    message to send
| $subject	    subject of the mail
| $from	    default = webmaster
| $cc	    	    Copy to send
| $type	    html or txt
+-------------------------------------------------------------------------+
| send mail according to given options
+------------------------------------------------------------------------*/
function simple_mail($to,$message,$subject = '',$from = '',$cc = '',$type ='html',$token ='',$bcc=null) {
//$from = $GLOBALS['PROJET']['mail'];
//$GLOBALS['PROJET']['nom'].' '.$GLOBALS['zunoWebService']['instance_code'];
    if ($from == '')
	$from = $GLOBALS['zunoClientCoordonnee']['nom'].'<'.$GLOBALS['zunoClientCoordonnee']['mail'].'>';
    if ($subject == '')
	$subject = 'Message de '.$GLOBALS['zunoClientCoordonnee']['nom'];
    if ($type == 'html')
	$headers .= "Content-type: text/html; charset=UTF-8\r\n";
    else	$headers .= "Content-type: text/plain; charset=UTF-8\r\n";
    if ($token != '')
	$headers .= "ZunoMessageUID: ".$token."\r\n";
    $headers .= "From: ".$from."\r\n";
    $headers .= "Reply-To: ".$from."\r\n";
    $headers .= "Return-Path: ".$from."\r\n";
    $headers .= "X-Sender: ".$from."\r\n";
    if ($cc !== false and !is_array($cc))
	$headers .= "Cc: ".$cc."\r\n";
    elseif($cc !== false and is_array($cc)) {
	$hd = "Cc: ";
	foreach($cc as $v)
	    $hd .= $v.",";
	$headers .= rtrim($hd, ",");
	$headers .= "\r\n";
    }
    if ($bcc != '')
	$headers .= "Bcc: ".$bcc."\r\n";
    $o	= mail($to, $subject, $message, $headers);
    return array($o);
}
/*------------------------------------------------------------------------+
| MailAttach
|
| send mail with single or multiple files attached
+-------------------------------------------------------------------------+
+------------------------------------------------------------------------*/
function MailAttach($to,$messager,$file,$filetype = '',$messagetype = '',$subject = '',$from = '',$cc = '',$token ='') {
    if ($from == '')
	$from = $GLOBALS['zunoClientCoordonnee']['nom'].'<'.$GLOBALS['zunoClientCoordonnee']['mail'].'>';
    if ($subject == '')
	$subject = 'Message de '.$GLOBALS['zunoClientCoordonnee']['nom'];
    if ($messagetype == '')
	$messagetype = 'text/plain; charset=UTF-8';
    elseif ($messagetype == 'html')
	$messagetype = 'text/html; charset=UTF-8';
    if ($token != '')
	$heads .= "ZunoMessageUID: ".$token."\r\n";
    $heads .= "Reply-To: ".$from."\r\n";
    $heads .= "From: ".$from."\r\n";
    $heads .= "Return-Path: ".$from."\r\n";
    $heads .= "X-Sender: ".$from."\n";
    if ($cc !== false and !is_array($cc))
	$heads .= "Cc: ".$cc."\r\n";
    elseif($cc !== false and is_array($cc)) {
	$hd = "Cc: ";
	foreach($cc as $v)
	    $hd .= $v.",";
	$heads .= rtrim($hd, ",");
	$heads .= "\r\n";
    }

    $message[1]['content_type'] = $messagetype;
    $message[1]['filename'] = '';
    $message[1]['no_base64'] = TRUE;
    $message[1]['data'] = stripslashs($messager);

    if (!is_array($file))
	$fileIn[] = $file;
    else	$fileIn = $file;

    $id_mess = 2;
    foreach($fileIn as $fichier) {
	if ($filetype == 'pdf')
	    $filetype = 'application/pdf';
	elseif ($filetype != '')
	    $filetype = $filetype;
	elseif(substr($fichier,-3) == 'pjs')
	    $filetype = 'binary';
	else	$filetype = trim(shell_exec("file -bi ".$fichier));
	$uril = explode("/", $fichier);
	$nblast = count($uril)-1;
	$filename = $uril[$nblast];
	$message[$id_mess]['content_type']	= $filetype;
	$message[$id_mess]['filename']	= $filename;
	$message[$id_mess]['data']		= mp_FileReadFile($fichier);
	$id_mess++;
    }
    $out	= mp_new_message($message);

    $o	= mail($to, $subject, $out[0],$heads.$out[1]);
    Logg::loggerInfo('function MailAttach() ~ Envoi d\'un mail à '.$to,array($to, $subject, $out[0],$heads.$out[1]),__FILE__.'@'.__LINE__);
    return array($o, $to);
}

#     Multipart mime email generator library for PHP.
#     Copyright 2002 Jeremy Brand, B.S. <jeremy@nirvani.net>
#     http://www.jeremybrand.com/Jeremy/Brand/Jeremy_Brand.html


function mp_FileReadFile($filename) {
    $buf = '';
    $fd = fopen($filename, 'r');
    if ($fd) {
	while(!feof($fd))
	    $buf .= fread($fd, 256);
	fclose($fd);
    }
    if (strlen($buf))
	return $buf;
}

function mp_new_message($message_array) {
    $headers = $data = array();
    $boundary = mp_new_boundary();
    while(list(, $chunk) = each($message_array)) {
	$mess = TRUE;
	unset($headers);
	unset($data);
	if (!$chunk['no_base64']) {
	    $headers['Content-ID'] = mp_new_message_id();
	    $headers['Content-Transfer-Encoding'] = 'BASE64';
	    if (strlen($chunk['filename'])) {
		$headers['Content-Type'] = $chunk['content_type'].'; name="'.$chunk['filename'].'"';
		$headers['Content-Disposition'] = 'attachment; filename="'.$chunk['filename'].'"';
	    }
	    else $headers['Content-Type'] = $chunk['content_type'];
	    $data = chunk_split(base64_encode($chunk['data']),60,"\n");
	}
	else {
	    $headers['Content-Type'] = $chunk['content_type'];
	    $data = $chunk['data'] . "\n";
	}

	if (is_array($chunk['headers']) && count($chunk['headers']))
	    while(list($key, $val) = each($chunk['headers']))
		$headers[$key] = $val;

	$buf .= '--' . $boundary. "\n";
	while(list($key, $val) = each($headers))
	    $buf .= $key.': '.$val."\n";
	$buf .= "\n";
	$buf .= $data;
    }
    if ($mess) {
	$buf .= '--' . $boundary. '--' ;
	return array(
		0 => $buf,
		1 => 'MIME-Version: 1.0'."\n".
			'Content-Type: MULTIPART/MIXED;'."\r\n".
			'  BOUNDARY="'.$boundary.'"'."\r\n",
		2 => array('MIME-Version: 1.0',
			'Content-Type: MULTIPART/MIXED;'."\r\n".
				'  BOUNDARY="'.$boundary.'"\r\n','')
	);

    }
}


function mp_new_message_id() {
    return '<'.$GLOBALS['PROJET']['nom'].'-'.str_replace(' ','.',microtime()).'@'.'>';
}

function mp_new_boundary() {
    return '-'.$GLOBALS['PROJET']['nom'].'-'.str_replace(' ','.',microtime());
}



?>
