<?php
loadPlugin(array('ZControl/GeneralControl'));

/**
 * Classe Sender qui gère tous les envois quelque soit le type d'envoi
 *
 * @author Nicolas Mannocci
 * @version 1.1
 */
class Sender {

    private $type; //Précise le type d'envoi concerné
    private $token; //clef unique générée de manière aléatoire
    private $email; //L'email de destination
    private $cc; //Les destinataires en copie
    private $from; //L'expéditeur
    private $tel; //Le numéro de téléphonne de l'expéditeur
    private $nomExpediteur; // Le nom de l'expéditeur

    private $typeEmail; //Permet de préciser si l'email est en text ou en html
    private $fichier; //le(s) éventuel(s) fichier(s) à envoyer.
    private $fax; //Le numéro de fax de destinataire
    private $destinataire; //Le nom du destinataire
    private $adresse; //L'adresse du destinataire
    private $adresse1; //Le complément d'adresse du destinataire
    private $cp; // Le code postal du destinataire
    private $ville; //La ville de destination
    private $pays; //Le pays de destination
    private $repertoire; //Le répertoire ou trouver les fichiers à envoyer
    private $dossierPrincipal; //Le dossier principal où sont les fichiers à envoyer
    private $id; //Un id du devis/commande/facture que l'on envoi
    private $partie;//La partie devis, commande, facture

    private $sujet;//Le sujet du message envoyé
    private $message;//Le message envoyé par lui même
    private $signature;//La signature du message

    private $bdd;//Le lien vers la Base de donnée
    private $bug;//pour préciser s'il s'agit d'un envoi d'un bug et agir en conséquence

    /**
     * Le constructeur de la classe.
     * Il faut lui passer tous les paramètres c'est lui qui va tout controler et accepter ou non de faire un envoi
     * @param array $data
     */
    function __construct($data) {
	$fullToken = md5(serialize($data).rand(0,10000));
	$this->token = substr($fullToken,0,8).'-'.substr($fullToken,8,8).'-'.substr($fullToken,16,8).'-'.substr($fullToken,24,8);
	$this->typeEmail = $data['typeEmail'];
	$this->id = $data['id'];
	$this->partie = $data['partie'];
	$this->type = ($data['typeE'] == 'email') ? 'mail' : $data['typeE'];
	$this->message = stripslashes($data['message']);
	$this->sujet = stripslashes($data['sujet']);
	$this->nomExpediteur = $data['expediteur'];
	$this->from = (generalControl::mailControl($data['from'])) ? $data['from'] : $GLOBALS['zunoClientCoordonnee']['mail'];
	if(is_array($data['cc'])) {
	    $aumoins1 = false;
	    foreach($data['cc'] as $v) {
		if(generalControl::mailControl($v['mail'])) {
		    $this->cc[] = $v['mail'];
		    $aumoins1 = true;
		}
	    }
	    if(!$aumoins1)
		$this->cc = false;
	}
	else $this->cc = (generalControl::mailControl($data['cc'])) ? $data['cc'] : false;
	$this->email = (generalControl::mailControl($data['mail'])) ? $data['mail'] : false;
	$this->fax = (generalControl::telephoneControl($data['fax'])) ? $data['fax'] : false;
	$this->tel = (generalControl::telephoneControl($data['tel'])) ? $data['tel'] : false;
	$this->destinataire = ($data['destinataire'] != '') ? $data['destinataire'] : false;
	$this->adresse = ($data['add1'] != '') ? $data['add1'] : false;
	$this->adresse1 = ($data['add2'] != '') ? $data['add2'] : false;
	$this->cp = ($data['cp'] != '') ? $data['cp'] : false;
	$this->ville = ($data['ville'] != '') ? $data['ville'] : false;
	$this->pays = ($data['pays'] != '') ? $data['pays'] : false;
	$this->channel = ($data['channel'] != '') ? $data['channel'] : 'web';
	if($this->partie == 'contactParticulier') {
	    $this->repertoire = $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'];
	    $this->dossierPrincipal = $GLOBALS['ZunoSendMail']['dir.pj'];
	}
	elseif($this->partie == 'facture') {
	    $this->repertoire = "";
	    $this->dossierPrincipal = $GLOBALS['ZunoFacture']['dir.facture'];
	}
	elseif($this->partie == 'factureFournisseur') {
	    $this->repertoire = "";
	    $this->dossierPrincipal = $GLOBALS['ZunoFacture']['dir.factureFournisseur'];
	}
	elseif($this->partie == 'send') {
	    $this->dossierPrincipal = $data['path'];
	    $this->repertoire = "";
	}
	else {
	    $this->repertoire = (is_dir($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoAffaire']['dir.affaire'].$data['dir_aff'])) ? $data['dir_aff'] : false;
	    $this->dossierPrincipal = $GLOBALS['ZunoAffaire']['dir.affaire'].$this->repertoire;
	}
	if($this->repertoire !== false) {
	    if(is_array($data['fichier']) and $this->type == 'mail') {
		foreach($data['fichier'] as $v)
		    if(is_file($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$this->dossierPrincipal.$v))
			$this->fichier[] = $v;
	    }
	    elseif(is_array($data['fichier']) and $this->type != 'mail') {
		foreach($data['fichier'] as $v) {
		    $extension=strrchr($v,'.');
		    if($extension != '.pdf') {
			$this->fichier = false;
			break;
		    }
		    if(is_file($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$this->dossierPrincipal.$v))
			$this->fichier[] = $v;
		}
	    }
	    elseif(!is_array($data['fichier']))
		if(is_file($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$this->dossierPrincipal.$data['fichier']))
		    $this->fichier = $data['fichier'];
		else $this->fichier = false;
	}
	else $this->fichier = false;

	$this->bdd = new Bdd_mysql();
	$this->bug = ($data['bug'] == true) ? true : false;
	$this->signature = "\n\n-------------------------------\nCe message vous est transmis grace au service www.zuno.fr";
    }

    /**
     * La méthode send est appelée pour réaliser l'envoi.
     * @param string $from L'expéditeur que l'on peut repréciser (non obligatoire)
     * @return array Le résultat et ce qu'il faut afficher
     */
    public function send($from = '') {
	if($this->type == 'mail') {
	    if($this->bug === false)
		aiJeLeDroit($this->partie, 50, $this->channel);
	    if($this->email === false) {
		if($this->partie != 'send')
		    $out = viewFiche($this->id, $this->partie, 'Traitement', 'non', $this->channel, true, 'E-mail invalide');
		else $out = "E-mail invalide";
		return array(0,$out);
	    }
	    elseif($this->bug == false and $this->controlTotalEnvoi() === false) {
		if($this->partie != 'send')
		    $out = viewFiche($this->id, $this->partie, 'Traitement', 'non', $this->channel, true, 'Quota d\'envoi par mail dépassé');
		else $out = "Quota d\'envoi par mail dépassé";
		return array(0,$out);
	    }
	    else {
		$from = (generalControl::mailControl($from)) ? $from : '';
		if($this->fichier === false) {
		    $r = $this->simple_mail($from, $this->typeEmail);
		    $this->addHistoriqueEnvoiInManager();
		}
		else {
		    $r = $this->MailAttach($from, $this->typeEmail);
		    $this->addHistoriqueEnvoiInManager();
		}
		if($this->channel != 'iphone' and $this->bug === false and $this->partie != 'send')
		    $out = viewFiche($this->id, $this->partie, 'Traitement', 'non', $this->channel, true, 'E-mail envoyé');
		else
		    $out = "E-mail envoyé";
		return array(1, $out);
	    }
	}
	elseif($this->type == 'fax') {
	    aiJeLeDroit($this->partie, 52, $this->channel);
	    if($this->fax === false) {
		$out =  viewFiche($this->id, $this->partie, 'Traitement', 'non', $this->channel, true, 'Numéro de fax invalide');
		return array(0,$out);
	    }
	    elseif($this->destinataire === false) {
		$out = viewFiche($this->id, $this->partie, 'Traitement', 'non', $this->channel, true, 'Destinataire invalide');
		return array(0,$out);
	    }
	    elseif($this->controlTotalEnvoi() === false) {
		$out = viewFiche($this->id, $this->partie, 'Traitement', 'non', $this->channel, true, 'Quota d\'envoi par fax dépassé');
		return array(0,$out);
	    }
	    else {
		$result = $this->SendFaxMaileva();
		$out = $result;
		if($result[0]) {
		    if($this->channel != 'iphone' and $this->partie != "send")
			$out = viewFiche($this->id, $this->partie, 'Traitement', 'non', $this->channel, true, 'Fax envoyé');
		}
		else {
		    if($this->channel != 'iphone' and $this->partie != "send")
			$out = viewFiche($this->id, $this->partie, 'Traitement', 'non', $this->channel, true, $result[1]);
		}
		return array(1,$out);
	    }
	}
	elseif($this->type == 'courrier') {
	    aiJeLeDroit($this->partie, 54, $this->channel);
	    if($this->controlTotalEnvoi() === false) {
		$out = viewFiche($this->id, $this->partie, 'Traitement', 'non', $this->channel, true, 'Quota d\'envoi par courrier dépassé');
		return array(0,$out);
	    }
	    else {
		$result = $this->SendLetterMaileva();
		if($result[0]) {
		    $out = viewFiche($this->id, $this->partie, 'Traitement', 'non', $this->channel, true, 'Courrier envoyé');
		}
		else $out = $result[1];
		return array(1,$out);
	    }
	}
	else {
	    if($this->channel != 'iphone' and $this->partie != "send")
		$out = viewFiche($this->id, $this->partie, 'Traitement', 'non', $this->channel, true, 'Aucun type d\'envoi sélectionné');
	    return array(0,$out);
	}
    }

    /**
     * La méthode SendFaxMaileva est utilisée pour transférer le mail de fax vers maileva
     * @return array Indique si ça s'est bien passé ou pas
     */
    private function SendFaxMaileva() {
	if($this->destinataire !== false and $this->fax !== false and $this->fichier !== false) {
	    $coordonnee['nom1'] = $this->destinataire;
	    if(substr(trim($this->fax),0,1) == '+') {
		$f = str_replace('.',' ',$this->fax);
		$f = str_replace('-',' ',$f);
		$fx = explode(' ',$f, 2);
		$coordonnee['fax'] = $fx[0].'-'.$fx[1];
	    }
	    elseif(substr(trim($this->fax),0,1) === '0')
		$coordonnee['fax'] = '33-(0)'.substr($this->fax,1);

	    if ($this->tel !== false and (substr(trim($this->tel),0,1) == '+')) {
		$f = str_replace('.',' ',$this->tel);
		$f = str_replace('-',' ',$f);
		$fx = explode(' ',$f, 2);
		$coordonnee['tel'] = $fx[0].'-'.$fx[1];
	    }
	    elseif($this->tel !== false and (substr(trim($this->tel),0,1) === '0'))
		$coordonnee['tel'] = '33-(0)'.substr($this->tel,1);

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
	    $coordonnee['clientRfr'] = $this->token;
	    $coordonnee['instance_code'] = $GLOBALS['zunoWebService']['instance_code'];
	    $pjs = templating('maileva.fax',$coordonnee);
	    $TmpPath = $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'];
	    $TmpDir = substr(md5(rand(1000,9999)+rand(1000,9999)),0,8).'/';
	    @rm($TmpPath.$TmpDir);
	    mkdir($TmpPath.$TmpDir);
	    $TmpFile = $GLOBALS['MailevaConf']['pjsFileName'];
	    touch($TmpPath.$TmpDir.$TmpFile);
	    File_Add2File($TmpPath.$TmpDir.$TmpFile,$pjs,TRUE);

	    $cover = $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'].$this->GenerateCover();
	    if(is_array($this->fichier))
		foreach($this->fichier as $v)
		    $liste .= $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$this->dossierPrincipal.$v. " ";
	    else $liste = $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$this->dossierPrincipal.$this->fichier. " ";
	    $liste = $cover." ".$liste;
	    $commande = 'pdftk '.$liste.' cat output '.$GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'].'fax.pdf';
	    shell_exec($commande);
	    $files[] = $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'].'fax.pdf';
	    $files[] = $TmpPath.$TmpDir.$TmpFile;

	    $this->email = $GLOBALS['MailevaConf']['mailToFax'];
	    $this->fichier = $files;
	    $o = $this->MailAttach('', '', 'fax');
	    if($o) {
		$this->addHistoriqueEnvoiInManager();
		return array(true,$addSynt);
	    }
	    else return array(false,'Erreur lors de l\'envoi du mail vers le service de fax.');
	}
	else return array(false,'Il manque des informations pour l\'envoi par fax. ');
    }

    /**
     * La méthode SendLetterMaileva transfert le mail pour maileva en vue d'être transformé en un courrier classique
     * @return array Indique si le traitement s'est bien passé
     */
    private function SendLetterMaileva() {
	$address['nom1'] = $this->destinataire;

	if($this->adresse !== false and $this->cp !== false and $this->ville !== false
		and $this->fichier !== false) {
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
		elseif(strlen(trim($this->adresse1)) <= 1) {
		    $fill[] = substr($address['nom2'],0,37).'-';
		    if(strlen($address['nom2']) > 76) $adds = '...';
		    $fill[] = substr($address['nom2'],37,35).$adds;
		}
		else $fill[] = substr($address['nom2'],0,35).'...';
	    }
	    elseif($address['nom2'] != '')  $fill[] = $address['nom2'];

	    if(strlen($this->adresse) > 38)
		$fill[] = substr($this->adresse,0,37).'.';
	    else  $fill[] = $this->adresse;
	    if(strlen($this->adresse1) > 38)
		$fill[] = substr($this->adresse1,0,37).'.';
	    else  $fill[] = $this->adresse1;

	    if($this->pays !== false) {
		$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
		$sqlConn->makeRequeteFree("select code_pays, nom_pays from ref_pays WHERE code_pays = '".$this->pays."';");
		$temp = $sqlConn->process2();
		$temp=$temp[1];
		$address['nom_pays']  = strtoupper($temp[0]['nom_pays']);
		$address['code_pays'] = strtoupper($temp[0]['code_pays']);
	    }
	    else {
		$address['nom_pays'] = "France";
		$address['code_pays'] = "1";
	    }
	    $addSynt = $address['nom1'].'<br/>';
	    $addSynt.= $address['nom2'].'<br/>';
	    $addSynt.= $this->adresse.'<br/>';
	    $addSynt.= ($this->adresse1 !== false) ? $this->adresse1.'<br/>' : '';
	    $addSynt.= $this->cp.' '.strtoupper($this->ville).'<br/>';
	    $addSynt.= strtoupper($address['nom_pays']).'<br/>';


	    $address['PID1'] = $fill[0];
	    $address['PID2'] = $fill[1];
	    $address['PID3'] = $fill[2];
	    $address['PID4'] = $fill[3];
	    $address['PID5'] = $fill[4];
	    $address['PID6'] = $this->cp.' '.strtolower($this->ville);
	    $address['PID7'] = $address['nom_pays'];
	    $address['CT'] = $address['code_pays'];

	    $address = array_merge($address,$GLOBALS['MailevaConf']);
	    $address['clientRfr'] = $this->token;
	    $address['instance_code'] = $GLOBALS['zunoWebService']['instance_code'];
	    $pjs = templating('maileva',$address);
	    $TmpPath = $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'];
	    $TmpDir = substr(md5(rand(1000,9999)+rand(1000,9999)),0,8).'/';
	    @rm($TmpPath.$TmpDir);
	    mkdir($TmpPath.$TmpDir);
	    $TmpFile = $GLOBALS['MailevaConf']['pjsFileName'];
	    touch($TmpPath.$TmpDir.$TmpFile);
	    File_Add2File($TmpPath.$TmpDir.$TmpFile,$pjs,TRUE);
	    if(is_array($this->fichier))
		foreach($this->fichier as $v)
		    $liste .= $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$this->dossierPrincipal.$v. " ";
	    else $liste = $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$this->dossierPrincipal.$this->fichier. " ";
	    $commande = 'pdftk '.$liste.' cat output '.$GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'].'courrier.pdf';
	    shell_exec($commande);
	    $files[] = $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'].'courrier.pdf';
	    $files[] = $TmpPath.$TmpDir.$TmpFile;

	    $this->email = $GLOBALS['MailevaConf']['mailToCourrier'];
	    $this->fichier = $files;
	    $o = $this->MailAttach('', '', 'courrier');
	    if($o) {
		$this->addHistoriqueEnvoiInManager();
		return array(true,$addSynt);
	    }
	    else	 return array(false,'Erreur lors de l\'envoi du mail vers le service de courrier.');
	}
	else {
	    return array(false,'Il manque des informations pour l\'envoi par courrier. '.$this->adresse.' '.$this->cp.' '.$this->ville);
	}
    }

    /**
     * Méthode d'envoi d'un mail sans pièce jointe
     * @param string $from Permet de péciser l'expéditeur (optionnel)
     * @param string $type Permet de préciser si le type est html ou text (optionnel)
     * @return array Indqiue si ça s'est bien passé
     */
    private function simple_mail($from = '', $type = '') {
	if ($from == '')
	    $from = $this->from;
	if ($this->sujet == '')
	    $this->sujet = $GLOBALS['zunoClientCoordonnee']['nom'];
	if ($type == 'html')
	    $headers .= "Content-type: text/html; charset=UTF-8\n";
	else	$headers .= "Content-type: text/plain; charset=UTF-8\n";

	// en-têtes
	$headers .= "From: ".$from."\n";
	$headers .= "Reply-To: ".$from."\n";
	$headers .= "Return-Path: ".$from."\n";
	$headers .= "ZunoMessageUID: ".$this->token."\n";
	$headers .= "X-Sender: ".$from."\n";
	if ($this->cc !== false and !is_array($this->cc))
	    $headers .= "Cc: ".$this->cc."\n";
	elseif($this->cc !== false and is_array($this->cc)) {
	    $headers .= "Cc: ";
	    foreach($this->cc as $v) {
		$headers .= $v.",";
	    }
	    $headers = rtrim($headers, ",");
	    $headers .= "\n";
	}
	if ($GLOBALS['ZunoSendMail']['allMailCopy'] == true)
	    $heads .= "Bcc: ".$GLOBALS['ZunoSendMail']['allMailCopyAdress']."\n";

	$o	= mail($this->email, $this->sujet, stripslashes($this->message.$this->signature), $headers);
	return array($o);
    }

    /**
     * Méthode qui permet l'envoi d'un mail avec pièce(s) jointe(s)
     * @param string $from Permet de préciser l'expéditeur (optionnel)
     * @param string $messagetype Permet de préciser le type html ou text (optionnel)
     * @param string $format Permet d'indiquer s'il y a plusieurs fichiers ou un seul (optionnel)
     * @return array Indique si ça s'est bien passé
     */
    private function MailAttach($from = '', $messagetype = '', $format = 'classic') {
	if ($from == '')
	    $from = $this->from;
	if ($this->sujet == '')
	    $this->sujet = $GLOBALS['zunoClientCoordonnee']['nom'];
	if ($messagetype == '')
	    $messagetype = 'text/plain; charset=UTF-8';
	elseif ($messagetype == 'html')
	    $messagetype = 'text/html; charset=UTF-8';

	$heads .= "Reply-To: ".$from."\n";
	$heads .= "From: ".$from."\n";
	$heads .= "Return-Path: ".$from."\n";
	$heads .= "ZunoMessageUID: ".$this->token."\n";
	$heads .= "X-Sender: ".$from."\n";
	if ($this->cc !== false and !is_array($this->cc))
	    $heads .= "Cc: ".$this->cc."\n";
	elseif($this->cc !== false and is_array($this->cc)) {
	    $heads .= "Cc: ";
	    foreach($this->cc as $v)
		$heads .= $v.",";
	    $heads = rtrim($heads, ",");
	    $heads .= "\n";
	}
	if ($GLOBALS['ZunoSendMail']['allMailCopy'] == true)
	    $heads .= "Bcc: ".$GLOBALS['ZunoSendMail']['allMailCopyAdress']."\n";

	$message[1]['content_type'] = $messagetype;
	$message[1]['filename'] = '';
	$message[1]['no_base64'] = TRUE;
	$message[1]['data'] = stripslashes($this->message.$this->signature);
	if($format == 'classic') {
	    if (!is_array($this->fichier))
		$fileIn[] = $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$this->dossierPrincipal.$this->fichier;
	    else
		foreach($this->fichier as $v)
		    $fileIn[] = $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$this->dossierPrincipal.$v;
	}
	else $fileIn = $this->fichier;
	$id_mess = 2;
	foreach($fileIn as $fichier) {
	    $filetype = trim(shell_exec("file -bi ".$fichier));
	    $uril = explode("/", $fichier);
	    $nblast = count($uril)-1;
	    $filename = $uril[$nblast];
	    $message[$id_mess]['content_type']	= $filetype;
	    $message[$id_mess]['filename']	= $filename;
	    $message[$id_mess]['data']		= $this->mp_FileReadFile($fichier);
	    $id_mess++;
	}
	$out	= $this->mp_new_message($message);
	$o	= mail($this->email, $this->sujet, $out[0],$heads.$out[1]);
	Logg::loggerInfo('Sender::MailAttach() ~ Envoi d\'un mail à '.$this->email,serialize($this),__FILE__.'@'.__LINE__);
	return array($o, $this->email);
    }

    /**
     * Méthode qui met le contenu d'un fichier dans un buffer
     * @param string $filename Nom du fichier à lire
     * @return buffer Le buffer contenant le fichier
     */
    private function mp_FileReadFile($filename) {
	return mp_FileReadFile($filename);
    }

    /**
     * Méthode qui génère le header des mails.
     * @param array $message_array Les messages à traiter
     * @return array Les headers générés
     */
    private function mp_new_message($message_array) {
	$headers = $data = array();
	$boundary = $this->mp_new_boundary();
	while(list(, $chunk) = each($message_array)) {
	    $mess = TRUE;
	    unset($headers);
	    unset($data);
	    if (!$chunk['no_base64']) {
		$headers['Content-ID'] = $this->mp_new_message_id();
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
			    'Content-Type: MULTIPART/MIXED;'."\n".
			    '  BOUNDARY="'.$boundary.'"'."\n".
			    'X-Generated-By: '.$GLOBALS['PROJET']['nom'].' Version '.$GLOBALS['PROJET']['version'].' '.$GLOBALS['PROJET']['copyright'].";\n".'  http://www.zuno.fr/',
		    2 => array('MIME-Version: 1.0',
			    'Content-Type: MULTIPART/MIXED;'."\n".
				    '  BOUNDARY="'.$boundary.'"\n',
			    'X-Generated-By: '.$GLOBALS['PROJET']['nom'].' Version '.$GLOBALS['PROJET']['version'].';'."\n".'  http://www.zuno.fr/')
	    );

	}
    }

    /**
     * Génère une chaine unique.
     * @return string La chaine générée
     */
    private function mp_new_boundary() {
	return mp_new_boundary();
    }

    /**
     * Méthode qui génère un id pour le message
     * @return string l'id généré
     */
    private function mp_new_message_id() {
	return mp_new_message_id();
    }

    /**
     * Méthode qui génère la page de couverture d'un fax
     * @param string $outputExt Le type de doc à généré (optionne, défaut à pdf)
     * @return string Le nom du document généré
     */
    private function GenerateCover($outputExt = 'pdf') {
	loadPlugin(array('OOConverter','docGenerator'));
	$cannevasFile = $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoSendMail']['coverFax'];

	$datas['nom_contact'] = $this->nomExpediteur;
	$datas['mail_contact'] = $this->from;
	$datas['tel_contact'] = ($this->tel === false) ? "" : $this->tel;
	$datas['nom_cont'] = $this->destinataire;
	$datas['fax_cont'] = $this->fax;
	$datas['message'] = $this->message;
	$datas['sujet'] = $this->sujet;
	$nb_ligne = 0;
	$lignes = explode("\n", $datas['message']);
	foreach($lignes as $k => $v) {
	    $nb_ligne ++;
	    if(strlen($v) > 54) {
		$nb_ligne += floor(strlen($v)/55);
	    }
	    if($nb_ligne > 17) {
		$datas['message'] = '';
		for  ($i = 0; $i < $k; $i++)
		    $datas['message'] .= $lignes[$i]."\n";
		$datas['message'] .= $lignes[$k];
		break;
	    }
	}

	//1 ligne = maxi 54 caractères
	//17 lignes maxi par le calcul précédent
	if(is_file($cannevasFile) and
		count($datas) > 0 and
		FileGetExtention($cannevasFile) == "odt") {
	    $prefixDoc = 'CF';
	    $fileSuffix = 'CouvertureFax.';

	    $TmpPath = $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'].$prefixDoc.substr(md5(rand(1000,9999)+rand(1000,9999)),0,8).'/';
	    $extract = docGeneratorOpenWorkingDoc($cannevasFile,$prefixDoc);
	    if ($extract[0]) {
		$TmpPath = $extract[1];
		$xml = file_get_contents($TmpPath.'content.xml');
		$datas   = docGeneratorCleanArrayBeforeTemplating($datas);
		docGeneratorAddZunoConfInfoBeforeTemplating($datas);
		$NewXMLcontent = docGeneratorFragmentTemplating($datas,$xml);

		$xmls = file_get_contents($TmpPath.'styles.xml');
		$xmls = docGeneratorFragmentTemplating($datas,$xmls,'StyleGenerator');
		File_Add2File($TmpPath.'styles.xml',$xmls,true);
		$ArchivePath = $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'];
		$ODTName = $fileSuffix.'odt';
		$PDFName = $fileSuffix.$outputExt;
		$fileClose = docGeneratorCloseWorkingDoc($TmpPath,$NewXMLcontent,$ArchivePath.$ODTName);
		if($outputExt != 'odt' and $fileClose[0]) {
		    @rm($ArchivePath.$PDFName);
		    OOConverter($ArchivePath.$ODTName,$ArchivePath.$PDFName,$outputExt);
		}
		return $PDFName;
	    }
	}
    }

    /**
     * Méthode qui envoie au Manager une entrée pour historiser l'envoi
     */

    private function  addHistoriqueEnvoiInManager() {
	$param = array(
		'token'		=> $this->token,
		'type'		=> $this->type,
		'statut'	=> '1',
		'dateRecord'	=> strftime('%Y-%m-%d %H:%M:%S'),
		'dateEnvoi'	=> strftime('%Y-%m-%d %H:%M:%S',time()+rand(0,20)),
		'fromUser'	=> $_SESSION['user']['id'],
		'fromName'	=> $_SESSION['user']['fullnom'],
		'toName'	=> $this->destinataire,
		'toAdresse'	=> $this->adresse,
		'toCp'		=> $this->cp,
		'toVille'	=> $this->ville,
		'toPays'	=> $this->pays,
		'toFax'		=> $this->fax,
		'toMail'	=> $this->email,
		'docPageSend'	=> $this->countPageNumberForDocument()
	);
	if(!$this->bug) {
	    $this->newEnvoi();
	    Sender::setHistoriqueEnvoiInManager($param);
	}
    }


    /**
     * Méthode qui envoi au Manager une entrée dans son historique d'envoi
     * @param array $paramIn Les paramètres à envoyer
     * @param string $cmd Préfixe du service appelé
     * @return string Indique si l'envoi s'est bien déroulé
     */
    static function  setHistoriqueEnvoiInManager($paramIn, $cmd = 'set') {
	loadPlugin(array('ZA.Wsdl'));
	$client = new zunoWsdlClient('instance');
	$client->setParam('raw',serialize($paramIn));
	$client->setParam('propager',true);
	if($client->call('setInstanceEnvoi'))
	    return $client->reponse;
	else return $client->message;
    }

    /**
     * Méthode qui compte le nombre de page du document envoyé
     * @return int Le nombre de page du document
     */
    private function  countPageNumberForDocument() {
	$file = $this->fichier[0];
	if(file_exists($file))
	    return trim(shell_exec('identify -format %n '.$file));
	return 0;
    }

    /**
     * Méthode qui vérifie le quota
     * @return bool Indique si le quota de documents envoyés est dépassé ou non.
     */
    private function controlTotalEnvoi() {
	$this->bdd->makeRequeteFree("SELECT COUNT(*) AS counter FROM send WHERE type_send = '".$this->type."' ");
	$result = $this->bdd->process2();
	if($result[1][0]['counter'] >= $GLOBALS['ZunoSendMail']['maxEnvoi'.ucfirst($this->type)] and $GLOBALS['ZunoSendMail']['maxEnvoi'.ucfirst($this->type)] != -1) {
	    return false;
	}
	else
	    return true;
    }

    /**
     * Méthode qui historise l'envoi sur l'instance.
     * @return array Indique si le traitement s'est bien déroulé
     */
    private function newEnvoi() {
	$data['user_send'] = $_SESSION['user']['id'];
	$data['type_send'] = $this->type;
	$data['date_send'] = date('Y-m-d');
	$this->bdd->makeRequeteInsert('send', $data);
	return $this->bdd->process2();
    }

    /**
     * Méthode qui permet de (re)définir le message
     * @param string $mess
     */
    public function setMessage($mess) {
	$this->message = $mess;
    }

    /**
     * Méthode qui permet de (re)définir le sujet
     * @param string $sujet
     */
    public function setSujet($sujet) {
	$this->sujet = $sujet;
    }

    /**
     * Méthode qui permet de (re)définir le(s) fichier(s) à envoyer en pièce(s) jointe(s)
     * @param string $fichier Le nom du fichier
     */
    public function setFichier($fichier) {
	if($fichier == "")
	    $this->fichier = false;
	if($this->repertoire !== false) {
	    if(is_array($fichier) and $this->type == 'mail') {
		foreach($fichier as $v)
		    if(is_file($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$this->dossierPrincipal.$v))
			$this->fichier[] = $v;
	    }
	    elseif(is_array($fichier) and $this->type != 'mail') {
		foreach($fichier as $v) {
		    $extension=strrchr($v,'.');
		    if($extension != '.pdf') {
			$this->fichier = false;
			break;
		    }
		    if(is_file($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$this->dossierPrincipal.$v))
			$this->fichier[] = $v;
		}
	    }
	    elseif(!is_array($fichier))
		if(is_file($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$this->dossierPrincipal.$fichier))
		    $this->fichier = $fichier;
		else $this->fichier = false;
	}
	else $this->fichier = false;
    }

    /**
     * Méthode qui permet de redéfinir l'email de destination
     * @param string $mail Le nouvel e-mail de destination
     */
    public function setMail($mail) {
	if(generalControl::mailControl($mail))
	    $this->email = $mail;
    }


}

?>
