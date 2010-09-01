<?php
// On inclus les librairies et les fichiers de configurations
include ('../inc/conf.inc');
include ('../inc/core.inc');		// Load core library
$GLOBALS['LOG']['DisplayDebug'] =
$GLOBALS['LOG']['DisplayError'] = false;
loadPlugin(array('Send/Send', 'ZModels/ContactModel', 'ZControl/SendControl'));

// On inclus les librairies speciales iPhone
include_once ('lib/Debug.inc.php');
include_once ('lib/HtmlElement.inc.php');
include_once ('lib/HtmlForm.inc.php');
include_once ('lib/ZunoLayerSend.inc.php');

include_once ('V/SendView.inc.php');
include_once ('V/ContactView.inc.php');

include_once ('V/GeneralView.inc.php');


// On lance la bufferisation de sortie et les entetes qui vont bien
ob_start();
header("Content-type: text/xml");
echo '<?xml version="1.0" encoding="utf-8" ?>';

// On recupère les informations sur le contexte de la page
// (channel, variables d'entrée, session)
$PC = new PageContext('iPhone');
$PC->GetVarContext();
$PC->GetChannelContext();
// Contrôle de la session et de sa validité
if($PC->GetSessionContext('',false) === false) {
    echo HtmlElementIphone::redirectOnSessionEnd();
    ob_end_flush();
    exit;
}
if($_SESSION['user']['config']['send'] != 'oui') {
    ?>
<root><go to="waDroits"/>
    <title set="waDroits"><?php echo 'Droits insuffisants'; ?></title>
    <part><destination mode="replace" zone="waDroits" create="true"/>
	<data><![CDATA[ <?php echo generalView::droits();?> ]]></data>
    </part>
</root>
    <?php
    ob_end_flush();
    exit;
}
// Envoi d'un mail
// ---------------------------
// Send.php?type=mail
//	* email=xxx@yyyyyyyy.fr
//	  message=essai+de+message
//	  titre=titre+de+mon+message
//	  emailcc=toto@startx.fr
//	  file=../tmp/todo.ods
// ex: <a href="Send.php?type=mail&email=clarue@startx.fr&message=essai+de+message&titre=titre+de+mon+message&emailcc=toto@startx.fr&file=../tmp/todo.ods" rev="async"><img src="Img/iconMenu/zsendMail.png" />Envoi de Mail </a>
//
// Envoi d'un courrier
// ---------------------------
// Send.php?type=courrier
//	* nom=xxx
//	* add1=yy yyyyyyy
//	  add2=
//	* cp=75000
//	* ville=PARIS
//	  cpays=fr
//	  emailcc=toto@startx.fr
//	* file=../tmp/todo.ods
// ex: <a href="Send.php?type=courrier&nom=Mr+Christophe+LARUE&add1=16+rue+camille+Desmoulins&cp=75011&ville=PARIS&pays=FRANCE&cpays=fr&file=../tmp/todo.ods" rev="async"><img src="Img/iconMenu/zsendCourrier.png" />Envoi de Courrier </a>
if($PC->rcvG['type'] == 'mail') {
    if($PC->rcvG['action'] == 'doConfirmSend') {
	$_SESSION['ZSend']['data']['typeE'] = 'email';
	$_SESSION['ZSend']['data']['sujet'] = $_SESSION['ZSend']['data']['titre'];
	$_SESSION['ZSend']['data']['from'] = $_SESSION['ZSend']['data']['sender'];
	$_SESSION['ZSend']['data']['cc'] = $_SESSION['ZSend']['data']['emailcc'];
	$_SESSION['ZSend']['data']['mail'] = $_SESSION['ZSend']['data']['email'];
	$_SESSION['ZSend']['data']['partie'] = 'contactParticulier';
	$_SESSION['ZSend']['data']['fichier'] =$_SESSION['ZSend']['data']['file'];
	$sender = new Sender($_SESSION['ZSend']['data']);
	$sender->send();

	?>
<root><go to="<?php echo $_SESSION['ZSend']['returnTo']; ?>" /></root>
	<?php
	unset($_SESSION['ZSend']);
    }
    elseif($PC->rcvG['action'] == 'confirmSend') {
	// On verifie alors les données fournies
	$control = sendControl::sendMail($PC->rcvP);

	if($control[0]) {
	    $_SESSION['ZSend']['data'] = $PC->rcvP;
	    ?>
<root><go to="waZSendConfirm" />
    <part>
	<title set="waZSendConfirm">Envoi e-mail</title>
	<destination mode="replace" zone="waZSendConfirm" create="true" />
	<data><![CDATA[<?php echo sendView::formSendEmailConfirm($_SESSION['ZSend']['data']); ?>]]></data>
    </part>
</root>
	    <?php
	}
	else {	?>
<root><go to="waZSend"/>
    <title set="waZSend">Envoi e-mail</title>
    <part><destination mode="replace" zone="waZSend" create="true"/>
	<data><![CDATA[ <?php echo sendView::formSendEmail($PC->rcvP,$control[2],$control[1]); ?> ]]></data>
    </part>
</root>
	    <?php
	}
    }
    else {
	$_SESSION['ZSend']['returnTo'] = $PC->rcvG['__source'];
	$in['email'] = (array_key_exists('email',$PC->rcvG)) ? urldecode($PC->rcvG['email']) : '';
	$in['emailcc'] = (array_key_exists('emailcc',$PC->rcvG)) ? urldecode($PC->rcvG['emailcc']) : '';
	$in['titre'] = (array_key_exists('titre',$PC->rcvG)) ? urldecode($PC->rcvG['titre']) : '';
	$in['message'] = (array_key_exists('message',$PC->rcvG)) ? urldecode($PC->rcvG['message']) : '';
	$in['file'] = (array_key_exists('file',$PC->rcvG)) ? urldecode($PC->rcvG['file']) : '';
	?>
<root><go to="waZSend" />
    <part>
	<title set="waZSend">Envoi e-mail</title>
	<destination mode="replace" zone="waZSend" create="true" />
	<data><![CDATA[<?php echo sendView::formSendEmail($in); ?>]]></data>
    </part>
</root>
	<?php
    }
}
elseif($PC->rcvG['type'] == 'courrier') {
    if($PC->rcvG['action'] == 'doConfirmSend') {

	$_SESSION['ZSend']['data']['typeE'] = 'courrier';
	$_SESSION['ZSend']['data']['sujet'] = $_SESSION['ZSend']['data']['titre'];
	$_SESSION['ZSend']['data']['from'] = $_SESSION['ZSend']['data']['sender'];
	$_SESSION['ZSend']['data']['cc'] = $_SESSION['ZSend']['data']['emailcc'];
	$_SESSION['ZSend']['data']['mail'] = $_SESSION['ZSend']['data']['email'];
	$_SESSION['ZSend']['data']['partie'] = 'contactParticulier';
	$_SESSION['ZSend']['data']['fichier'] = $_SESSION['ZSend']['data']['file'];
	$_SESSION['ZSend']['data']['destinataire'] =$_SESSION['ZSend']['data']['nom'];
	$_SESSION['ZSend']['data']['pays'] = $_SESSION['ZSend']['data']['cpays'];
	$sender = new Sender($_SESSION['ZSend']['data']);
	$result = $sender->send();


	if($result[0]) {	?>
<root><go to="<?php echo $_SESSION['ZSend']['returnTo']; ?>" /></root>
	    <?php
	    unset($_SESSION['ZSend']);
	}
	else { ?>
<root><part><destination mode="before" zone="waZSend"/>
	<data><![CDATA[ <div class="err"><?php echo $result[1]; ?></div> ]]></data>
    </part>
</root>
	    <?php
	}
    }
    elseif($PC->rcvG['action'] == 'confirmSend') {
	// On verifie alors les données fournies
	$control = sendControl::sendCourrier($PC->rcvP);

	if($control[0]) {
	    $_SESSION['ZSend']['data'] = $PC->rcvP;
	    ?>
<root><go to="waZSendConfirm" />
    <part>
	<title set="waZSendConfirm">Envoi de courrier</title>
	<destination mode="replace" zone="waZSendConfirm" create="true" />
	<data><![CDATA[<?php echo sendView::formSendCourrierConfirm($_SESSION['ZSend']['data']); ?>]]></data>
    </part>
</root>
	    <?php
	}
	else {	?>
<root><go to="waZSend"/>
    <title set="waZSend">Envoi de courrier</title>
    <part><destination mode="replace" zone="waZSend" create="true"/>
	<data><![CDATA[ <?php echo sendView::formSendCourrier($PC->rcvP,$control[2],$control[1]); ?> ]]></data>
    </part>
</root>
	    <?php
	}
    }
    else {
	$_SESSION['ZSend']['returnTo'] = $PC->rcvG['__source'];
	$in['nom'] = (array_key_exists('nom',$PC->rcvG)) ? urldecode($PC->rcvG['nom']) : '';
	$in['add1'] = (array_key_exists('add1',$PC->rcvG)) ? urldecode($PC->rcvG['add1']) : '';
	$in['add2'] = (array_key_exists('add2',$PC->rcvG)) ? urldecode($PC->rcvG['add2']) : '';
	$in['cp'] = (array_key_exists('cp',$PC->rcvG)) ? urldecode($PC->rcvG['cp']) : '';
	$in['ville'] = (array_key_exists('ville',$PC->rcvG)) ? urldecode($PC->rcvG['ville']) : '';
	$in['cpays'] = (array_key_exists('cpays',$PC->rcvG)) ? urldecode($PC->rcvG['cpays']) : 'fr';
	$in['file'] = (array_key_exists('file',$PC->rcvG)) ? urldecode($PC->rcvG['file']) : '';
	?>
<root><go to="waZSend" />
    <part>
	<title set="waZSend">Envoi de courrier</title>
	<destination mode="replace" zone="waZSend" create="true" />
	<data><![CDATA[<?php echo sendView::formSendCourrier($in); ?>]]></data>
    </part>
</root>
	<?php
    }
}
elseif($PC->rcvG['type'] == 'fax') {
    if($PC->rcvG['action'] == 'doConfirmSend') {
	$d = $_SESSION['ZSend']['data'];
	$_SESSION['ZSend']['data']['typeE'] = 'fax';
	$_SESSION['ZSend']['data']['sujet'] = $_SESSION['ZSend']['data']['titre'];
	$_SESSION['ZSend']['data']['from'] = $_SESSION['ZSend']['data']['sender'];
	$_SESSION['ZSend']['data']['cc'] = $_SESSION['ZSend']['data']['emailcc'];
	$_SESSION['ZSend']['data']['mail'] = $_SESSION['ZSend']['data']['email'];
	$_SESSION['ZSend']['data']['partie'] = 'contactParticulier';
	$_SESSION['ZSend']['data']['fichier'] = $_SESSION['ZSend']['data']['file'];
	$_SESSION['ZSend']['data']['destinataire'] =$_SESSION['ZSend']['data']['nom'];

	$sender = new Sender($_SESSION['ZSend']['data']);
	$result = $sender->send();

	if($result[0]) {	?>
<root><go to="<?php echo $_SESSION['ZSend']['returnTo']; ?>" /></root>
	    <?php
	    unset($_SESSION['ZSend']);
	}
	else { ?>
<root><part><destination mode="before" zone="waZSend"/>
	<data><![CDATA[ <div class="err"><?php echo $result[1]; ?></div> ]]></data>
    </part>
</root>
	    <?php
	}
    }
    elseif($PC->rcvG['action'] == 'confirmSend') {
	// On verifie alors les données fournies
	$control = sendControl::sendFax($PC->rcvP);

	if($control[0]) {
	    $_SESSION['ZSend']['data'] = $PC->rcvP;
	    ?>
<root><go to="waZSendConfirm" />
    <part>
	<title set="waZSendConfirm">Envoi de fax</title>
	<destination mode="replace" zone="waZSendConfirm" create="true" />
	<data><![CDATA[<?php echo sendView::formSendFaxConfirm($_SESSION['ZSend']['data']); ?>]]></data>
    </part>
</root>
	    <?php
	}
	else {	?>
<root><go to="waZSend"/>
    <title set="waZSend">Envoi de fax</title>
    <part><destination mode="replace" zone="waZSend" create="true"/>
	<data><![CDATA[ <?php echo sendView::formSendFax($PC->rcvP,$control[2],$control[1]); ?> ]]></data>
    </part>
</root>
	    <?php
	}
    }
    else {
	$_SESSION['ZSend']['returnTo'] = $PC->rcvG['__source'];
	$in['nom']  = (array_key_exists('nom',$PC->rcvG)) ? urldecode($PC->rcvG['nom']) : '';
	$in['fax']  = (array_key_exists('fax',$PC->rcvG)) ? urldecode($PC->rcvG['fax']) : '';
	$in['file'] = (array_key_exists('file',$PC->rcvG)) ? urldecode($PC->rcvG['file']) : '';
	?>
<root><go to="waZSend" />
    <part>
	<title set="waZSend">Envoi de fax</title>
	<destination mode="replace" zone="waZSend" create="true" />
	<data><![CDATA[<?php echo sendView::formSendFax($in); ?>]]></data>
    </part>
</root>
	<?php
    }
}
ob_end_flush();
?>
