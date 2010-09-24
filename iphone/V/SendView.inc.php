<?php
class sendView {

    /**
     * Génération de la liste des résultats de la recherche
     */
    static function formSendEmail($value = array(), $onError = array(),$errorMess = '') {
	$error   = ($errorMess != '') ? '<div class="err">'.$errorMess.'</div>' : '';
	$out 	 = '<a href="#"  onclick="return WA.Submit(\'formZSendEmail\',null,event)" rel="action" class="iButton iBAction"><img src="'.getStaticUrl('imgPhone').'send.png" alt="Envoyer" /></a>
				<form id="formZSendEmail" action="Send.php?type=mail&action=confirmSend" onsubmit="return WA.Submit(this,null,event)">
				'.$error.'
					<div class="iPanel">
						'.self::innerFormSendEmail($value,$onError,$errorMess).'
					</div>
				</form>';
	return $out;
    }


    /**
     * Génération de la liste des résultats de la recherche
     */
    static function innerFormSendEmail($value = array(), $onError = array(),$errorMess = '') {
	$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$sqlConn->makeRequeteFree("select login, nom, prenom, civ from user order by nom;");
	$temp = $sqlConn->process2();
	$temp=$temp[1];
	foreach($temp as $k => $v)
	    $userList[$v['login']] = $v['civ'].' '.$v['prenom'].' '.$v['nom'];

	$sender = HtmlFormIphone::Select('sender',$userList,$_SESSION['user']['id'], false);
	$senderErr	= (in_array('sender',$onError)) ? '<span class="iFormErr"/>' : '';
	$destinataire = HtmlFormIphone::Input('email',$value['email'],'Pour : ');
	$destinataireERR	= (in_array('email',$onError)) ? '<span class="iFormErr"/>' : '';
	$destinataireCC = HtmlFormIphone::Input('emailcc',urldecode($value['emailcc']),'CC : ');
	$titre = HtmlFormIphone::Input('titre',$value['titre'],'Titre : ');
	$titreERR	= (in_array('titre',$onError)) ? '<span class="iFormErr"/>' : '';
	$descriptif = HtmlFormIphone::Textarea('message',$value['message'],' cols="60" rows="3" onfocus="TextAreaAutoResize(\'formZSendEmail\');"','Message : ');
	$error	= ($errorMess != '') ? '<div class="err">'.$errorMess.'</div>' : '';

	if($value['file'] != '' and file_exists($value['file'])) {
	    $fileSize = FileConvertSize2Human(filesize($value['file']));
	    $fileNameA = explode('/',$value['file']);
	    $fileName = ' '.$fileNameA[count($fileNameA)-1];
	    $fileName.= HtmlFormIphone::Input('file',$value['file'],'','','hidden');
	    $fileIcon = FileOutputType($value['file'],'image');
	    $fileAdd = '<fieldset>
						<legend>Fichier Joint</legend>
						<ul><li>'.$fileIcon.$fileName.' ('.$fileSize.')</li></ul>
					</fieldset>';

	}

	$out 	 = $error.'<fieldset>
					<legend>Expéditeur</legend>
					<ul><li>'.$sender.$senderErr.'</li></ul>
				</fieldset>
				<fieldset>
					<legend>Destinataire(s)</legend>
					<ul><li>'.$destinataire.$destinataireERR.'</li>
						<li>'.$destinataireCC.'</li></ul>
				</fieldset>
				<fieldset>
					<legend>Message</legend>
					<ul><li>'.$titre.$titreERR.'</li>
						<li>'.$descriptif.'</li></ul>
				</fieldset>
				'.$fileAdd;
	return $out;
    }

    /**
     * Génération de la liste des résultats de la recherche
     */
    static function formSendEmailConfirm($value = array()) {
	$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$sqlConn->makeRequeteFree("select mail, nom, prenom, civ from user order by nom;");
	$temp = $sqlConn->process2();
	$temp=$temp[1];
	foreach($temp as $k => $v)
	    $userList[$v['mail']] = $v['civ'].' '.$v['prenom'].' '.$v['nom'];

	$sender = HtmlFormIphone::Select('sender',$userList,$_SESSION['user']['id'], false);
	$destinataire = HtmlFormIphone::Input('email',urldecode($value['email']),'Pour : ');
	$destinataireCC = HtmlFormIphone::Input('emailcc',urldecode($value['emailcc']),'CC : ');
	$titre = HtmlFormIphone::Input('titre',$value['titre'],'Titre : ');
	$descriptif = HtmlFormIphone::Textarea('message',$value['message'],' cols="60" rows="3" onfocus="TextAreaAutoResize(\'formZSendEmail\');"','Message : ');

	if($value['file'] != '' and file_exists($value['file'])) {
	    $fileSize = FileConvertSize2Human(filesize($value['file']));
	    $fileNameA = explode('/',$value['file']);
	    $fileName = ' '.$fileNameA[count($fileNameA)-1];
	    $fileName.= HtmlFormIphone::Input('file',$value['file'],'','','hidden');
	    $fileIcon = FileOutputType($value['file'],'image');
	    $fileAdd = '<fieldset>
						<legend>Fichier Joint</legend>
						<ul><li>'.$fileIcon.$fileName.' ('.$fileSize.')</li></ul>
					</fieldset>';

	}

	$out 	 = '<a href="#"  onclick="return WA.Submit(\'formZSendEmailConfirm\',null,event)" rel="action" class="iButton iBAction"><img src="'.getStaticUrl('imgPhone').'valid.png" alt="Envoyer" /></a>
				<form id="formZSendEmailConfirm" action="Send.php?type=mail&action=doConfirmSend" onsubmit="return WA.Submit(this,null,event)">
				<div class="iPanel">
					<fieldset>
						<legend>Expéditeur</legend>
						<ul><li>'.$sender.'</li></ul>
					</fieldset>
					<fieldset>
						<legend>Déstinataire</legend>
						<ul><li>'.$destinataire.'</li>
							<li>'.$destinataireCC.'</li></ul>
					</fieldset>
					<fieldset>
						<legend>Message</legend>
						<ul><li>'.$titre.'</li>
							<li>'.$descriptif.'</li></ul>
					</fieldset>
					'.$fileAdd.'
					<fieldset>
							<a href="#" class="BigButtonValidLeft"><img src="'.getStaticUrl('imgPhone').'big.annuler.png" alt="Annuler" /></a>
							<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formZSendEmailConfirm\', null, event)"><img src="'.getStaticUrl('imgPhone').'big.confirmer.png" alt="confirmer" /></a>
					</fieldset>
				</div>
				</form>';
	return $out;
    }

    /* Génération de la liste des résultats de la recherche
    */
    static function formSendCourrier($value = array(), $onError = array(),$errorMess = '') {
	$error   = ($errorMess != '') ? '<div class="err">'.$errorMess.'</div>' : '';
	$out 	 = '<a href="#"  onclick="return WA.Submit(\'formZSendCourrier\',null,event)" rel="action" class="iButton iBAction"><img src="'.getStaticUrl('imgPhone').'send.png" alt="Envoyer" /></a>
				<form id="formZSendCourrier" action="Send.php?type=courrier&action=confirmSend" onsubmit="return WA.Submit(this,null,event)">
				'.$error.'
					<div class="iPanel">
						'.self::innerFormSendCourrier($value,$onError,$errorMess).'
					</div>
				</form>
				';
	return $out;
    }


    /* Génération de la liste des résultats de la recherche
    */
    static function innerFormSendCourrier($value = array(), $onError = array(),$errorMess = '') {
	$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$sqlConn->makeRequeteFree("select code_pays, nom_pays from ref_pays;");
	$temp = $sqlConn->process2();
	$temp=$temp[1];
	foreach($temp as $k => $v)
	    $countryList[$v['code_pays']] = $v['nom_pays'];

	$nom 		= HtmlFormIphone::Input('nom',$value['nom'],'Nom : ');
	$nomERR	= (in_array('nom',$onError)) ? '<span class="iFormErr"/>' : '';
	$add1 	= HtmlFormIphone::Input('add1',$value['add1'],'Adresse : ');
	$add1ERR	= (in_array('add1',$onError)) ? '<span class="iFormErr"/>' : '';
	$add2 	= HtmlFormIphone::Input('add2',$value['$add2'],'Adresse : ');
	$cp 		= HtmlFormIphone::Input('cp',$value['cp'],'CP : ');
	$ville 	= HtmlFormIphone::Input('ville',$value['ville'],'Ville : ');
	$pays 	= HtmlFormIphone::Select('cpays',$countryList,$value['cpays'],false);
	$paysERR	= (in_array('cpays',$onError)) ? '<span class="iFormErr"/>' : '';
	$error	= ($errorMess != '') ? '<div class="err">'.$errorMess.'</div>' : '';

	if($value['file'] != '' and file_exists($value['file'])) {
	    $fileSize = FileConvertSize2Human(filesize($value['file']));
	    $fileNameA = explode('/',$value['file']);
	    $fileName = ' '.$fileNameA[count($fileNameA)-1];
	    $fileName.= HtmlFormIphone::Input('file',$value['file'],'','','hidden');
	    $fileIcon = FileOutputType($value['file'],'image');
	    $fileAdd = '<li>'.$fileIcon.$fileName.' ('.$fileSize.')</li>';
	}
	else {
	    foreach ($GLOBALS['ZunoSendMail'] as $key => $val) {
		$k = explode('.',$key,2);
		if($k[0] == 'pj')
		    $toto[$k[1]] = $val;
	    }
	    $fileName = HtmlFormIphone::Select('file',$toto,$value['file'],false);
	    $fileAdd = '<li>'.$fileName.'</li>';
	}

	$out 	 = $error.'<fieldset>
					<legend>Déstinataire</legend>
					<ul><li>'.$nom.$nomERR.'</li></ul>
				</fieldset>
				<fieldset>
					<legend>Adresse</legend>
					<ul><li>'.$add1.$add1ERR.'</li>
					<li>'.$add2.'</li>
					<li>'.$cp.'</li>
					<li>'.$ville.'</li>
					<li>'.$pays.$paysERR.'</li></ul>
				</fieldset>
				<fieldset>
					<legend>Fichier à envoyer</legend>
					<ul>'.$fileAdd.'</ul>
				</fieldset>';
	return $out;
    }



    /* Génération de la liste des résultats de la recherche
    */
    static function formSendCourrierConfirm($value = array()) {
	$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$sqlConn->makeRequeteFree("select code_pays, nom_pays from ref_pays;");
	$temp = $sqlConn->process2();
	$temp=$temp[1];
	foreach($temp as $k => $v)
	    $countryList[$v['code_pays']] = $v['nom_pays'];

	$nom 		= HtmlFormIphone::Input('nom',$value['nom'],'Nom : ');
	$add1 	= HtmlFormIphone::Input('add1',$value['add1'],'Adresse : ');
	$add2 	= HtmlFormIphone::Input('add2',$value['$add2'],'Adresse : ');
	$cp 		= HtmlFormIphone::Input('cp',$value['cp'],'CP : ');
	$ville 	= HtmlFormIphone::Input('ville',$value['ville'],'Ville : ');
	$pays 	= HtmlFormIphone::Select('cpays',$countryList,$value['cpays'],false);


	if($value['file'] != '' and file_exists($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoSendMail']['dir.pj'].$value['file'])) {
	    $fileSize = FileConvertSize2Human(filesize($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoSendMail']['dir.pj'].$value['file']));
	    $fileNameA = explode('/',$value['file']);
	    $fileName = ' '.$fileNameA[count($fileNameA)-1];
	    $fileName.= HtmlFormIphone::Input('file',$value['file'],'','','hidden');
	    $fileIcon = FileOutputType($value['file'],'image');
	    $fileAdd = '<li>'.$fileIcon.$fileName.' ('.$fileSize.')</li>';
	}
	else  $fileAdd = '<li><i>Erreur dans l\'envoi de ce fichier</i></li>';

	$out 	 = '<a href="#"  onclick="return WA.Submit(\'formZSendCourrierConfirm\',null,event)" rel="action" class="iButton iBAction"><img src="'.getStaticUrl('imgPhone').'send.png" alt="Envoyer" /></a>
				<form id="formZSendCourrierConfirm" action="Send.php?type=courrier&action=doConfirmSend" onsubmit="return WA.Submit(this,null,event)">
					<div class="iPanel">
						<fieldset>
							<legend>Déstinataire</legend>
							<ul><li>'.$nom.'</li></ul>
						</fieldset>
						<fieldset>
							<legend>Adresse</legend>
							<li>'.$add1.'</li>
							<li>'.$add2.'</li>
							<li>'.$cp.'</li>
							<li>'.$ville.'</li>
							<li>'.$pays.'</li></ul>
						</fieldset>
						<fieldset>
							<legend>Fichier à envoyer</legend>
							<ul>'.$fileAdd.'</ul>
						</fieldset>
						<fieldset>
								<a href="#" class="BigButtonValidLeft"><img src="'.getStaticUrl('imgPhone').'big.annuler.png" alt="Annuler" /></a>
								<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formZSendCourrierConfirm\', null, event)"><img src="'.getStaticUrl('imgPhone').'big.confirmer.png" alt="confirmer" /></a>
						</fieldset>
					</div>
				</form>
				';
	return $out;
    }




    /* Génération de la liste des résultats de la recherche
    */
    static function formSendFax($value = array(), $onError = array(),$errorMess = '') {
	$error   = ($errorMess != '') ? '<div class="err">'.$errorMess.'</div>' : '';
	$out 	 = '<a href="#"  onclick="return WA.Submit(\'formZSendFax\',null,event)" rel="action" class="iButton iBAction"><img src="'.getStaticUrl('imgPhone').'send.png" alt="Envoyer" /></a>
				<form id="formZSendFax" action="Send.php?type=fax&action=confirmSend" onsubmit="return WA.Submit(this,null,event)">
				'.$error.'
					<div class="iPanel">
						'.self::innerFormSendFax($value,$onError,$errorMess).'
					</div>
				</form>
				';
	return $out;
    }


    /* Génération de la liste des résultats de la recherche
    */
    static function innerFormSendFax($value = array(), $onError = array(),$errorMess = '') {
	$nom 		= HtmlFormIphone::Input('nom',$value['nom'],'Nom : ');
	$nomERR	= (in_array('nom',$onError)) ? '<span class="iFormErr"/>' : '';
	$fax	 	= HtmlFormIphone::Input('fax',$value['fax'],'Numéro : ');
	$faxERR	= (in_array('fax',$onError)) ? '<span class="iFormErr"/>' : '';
	$error	= ($errorMess != '') ? '<div class="err">'.$errorMess.'</div>' : '';

	if($value['file'] != '' and file_exists($value['file'])) {
	    $fileSize = FileConvertSize2Human(filesize($value['file']));
	    $fileNameA = explode('/',$value['file']);
	    $fileName = ' '.$fileNameA[count($fileNameA)-1];
	    $fileName.= HtmlFormIphone::Input('file',$value['file'],'','','hidden');
	    $fileIcon = FileOutputType($value['file'],'image');
	    $fileAdd = '<li>'.$fileIcon.$fileName.' ('.$fileSize.')</li>';
	}
	else {
	    foreach ($GLOBALS['ZunoSendMail'] as $key => $val) {
		$k = explode('.',$key,2);
		if($k[0] == 'pj')
		    $toto[$k[1]] = $val;
	    }
	    $fileName = HtmlFormIphone::Select('file',$toto,$value['file'],false);
	    $fileAdd = '<li>'.$fileName.'</li>';
	}
	$out 	 = $error.'<fieldset>
					<legend>Déstinataire</legend>
					<ul><li>'.$nom.$nomERR.'</li></ul>
				</fieldset>
				<fieldset>
					<legend>Fax</legend>
					<ul><li>'.$fax.$faxERR.'</li></ul>
				</fieldset>
				<fieldset>
					<legend>Fichier à envoyer</legend>
					<ul>'.$fileAdd.'</ul>
				</fieldset>';
	return $out;
    }




    /* Génération de la liste des résultats de la recherche
    */
    static function formSendFaxConfirm($value = array()) {
	$nom 		= HtmlFormIphone::Input('nom',$value['nom'],'Nom : ');
	$fax	 	= HtmlFormIphone::Input('fax',$value['fax'],'Numéro : ');

	if($value['file'] != '' and file_exists($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoSendMail']['dir.pj'].$value['file'])) {
	    $fileSize = FileConvertSize2Human(filesize($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoSendMail']['dir.pj'].$value['file']));
	    $fileNameA = explode('/',$value['file']);
	    $fileName = ' '.$fileNameA[count($fileNameA)-1];
	    $fileName.= HtmlFormIphone::Input('file',$value['file'],'','','hidden');
	    $fileIcon = FileOutputType($value['file'],'image');
	    $fileAdd = '<li>'.$fileIcon.$fileName.' ('.$fileSize.')</li>';
	}
	else  $fileAdd = '<li><i>Erreur dans l\'envoi de ce fichier</i></li>';

	$out 	 = '<a href="#"  onclick="return WA.Submit(\'formZSendFaxConfirm\',null,event)" rel="action" class="iButton iBAction"><img src="'.getStaticUrl('imgPhone').'send.png" alt="Envoyer" /></a>
				<form id="formZSendFaxConfirm" action="Send.php?type=fax&action=doConfirmSend" onsubmit="return WA.Submit(this,null,event)">
					<div class="iPanel">
						<fieldset>
							<legend>Déstinataire</legend>
							<ul><li>'.$nom.'</li></ul>
						</fieldset>
						<fieldset>
							<legend>Fax</legend>
							<ul><li>'.$fax.'</li></ul>
						</fieldset>
						<fieldset>
							<legend>Fichier à envoyer</legend>
							<ul>'.$fileAdd.'</ul>
						</fieldset>
						<fieldset>
								<a href="#" class="BigButtonValidLeft"><img src="'.getStaticUrl('imgPhone').'big.annuler.png" alt="Annuler" /></a>
								<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formZSendFaxConfirm\', null, event)"><img src="'.getStaticUrl('imgPhone').'big.confirmer.png" alt="confirmer" /></a>
						</fieldset>
					</div>
				</form>
				';
	return $out;
    }
}

?>