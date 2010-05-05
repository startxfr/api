<?php
include ('../inc/conf.inc');
include ('../inc/core.inc');
ob_start();
include_once ('lib/Debug.inc.php');
include_once ('lib/ZunoLayerGeneral.inc.php');
include_once ('lib/HtmlElement.inc.php');

header("Content-type: text/xml");
echo '<?xml version="1.0" encoding="utf-8" ?>';

$PC = new PageContext('iPhone');
$PC->GetVarContext();
$PC->GetChannelContext();
$isAutorized = false;
$jeviensdepin = 'non';
if($PC->rcvG['action'] == 'verifPin') {
    $code = $PC->rcvP['pinCode'];
    $sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
    $sqlConn->makeRequeteFree('select pin, pwd from user where login = "'.$_COOKIE['login'].'";');
    $temp = $sqlConn->process2();
    if(md5($code) == $temp[1][0]['pin']) {
        $AuthTest = new SessionUser($PC->channel);
        $AuthTest->TestUser($_COOKIE['login'],$temp[1][0]['pwd'], 'yes');
        $AuthTest->CreateSession($_COOKIE['login'],false);
        $isAutorized = true;
    }
    else {
        ?>
<root><go to="waHomePin"/>
    <title set="waHomePin"><?php echo 'Code PIN'; ?></title>
    <part><destination mode="replace" zone="waHomePin" create="true"/>
        <data><![CDATA[ <?php echo ZunoLayerGeneral::iFormPin('oui'); ?> ]]></data>
    </part>
</root>
        <?php
        $jeviensdepin = 'oui';
    }
}

else {
    if($PC->rcvG["mess"] == 'delsess') {
        $message = $GLOBALS['Tx4Lg']['LoginErrorExpire'];
    }
    elseif($PC->rcvG["mess"] == 'badsess') {
        $message = $GLOBALS['Tx4Lg']['LoginErrorSession'];
    }
    elseif(count($PC->rcvP) == 0) {

    }
    else {
        if($PC->rcvP["login"] == '') {
            $authentification = 'NO_LOGIN';
        }
        elseif($PC->rcvP["pwd"] == '') {
            $authentification = 'NO_PWD';
        }
        elseif(($PC->rcvP["login"] != '')
                and($PC->rcvP["pwd"] != '')) {
            $AuthTest = new SessionUser($PC->channel);
            $authentification = $AuthTest->TestUser($PC->rcvP["login"],$PC->rcvP["pwd"]);
        }

        if($authentification == 'OK') {
            $message.=$AuthTest->CreateSession($PC->rcvP["login"],false);
            $isAutorized = true;
        }
        elseif($authentification == 'BAD_LOGIN') {
            $message = $GLOBALS['Tx4Lg']['LoginErrorBadLog'];
        }
        elseif($authentification == 'BAD_RIGHT') {
            $message = $GLOBALS['Tx4Lg']['LoginErrorNoRight'];
        }
        elseif($authentification == 'INACTIVE_USER') {
            $message = $GLOBALS['Tx4Lg']['LoginErrorInactive'];
        }
        elseif($authentification == 'BAD_PWD') {
            $message = $GLOBALS['Tx4Lg']['LoginErrorBadPw'];
        }
        elseif($authentification == 'NO_PWD') {
            $message = $GLOBALS['Tx4Lg']['LoginErrorBadMDP'];
        }
        elseif($authentification == 'NO_LOGIN') {
            $message = $GLOBALS['Tx4Lg']['LoginErrorBadID'];
        }
        elseif($authentification == 'NO_SESSION') {
            $message = $GLOBALS['Tx4Lg']['LoginErrorBadCookie'];
        }
    }
}


if($isAutorized) {
    $sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
    $sqlConn->makeRequeteFree("SELECT * FROM user_iphoneConfig WHERE user = '".$_SESSION['user']['id']."'");
    $r = $sqlConn->process2();
    if($r[0])
        foreach($r[1] as $k => $d)
            $_SESSION['user']['config'][$d['key']] = $d['val'];

    if(!is_array($_SESSION['user']['config'])) {
        $_SESSION['user']['config'] = array();
    }
    if(!array_key_exists('LenghtSearchActualite', $_SESSION['user']['config'])) {
        $_SESSION['user']['config']['LenghtSearchActualite'] = '10';
    }
    if(!array_key_exists('LenghtSearchAffaire', $_SESSION['user']['config'])) {
        $_SESSION['user']['config']['LenghtSearchAffaire'] = '10';
    }
    if(!array_key_exists('LenghtSearchCommande', $_SESSION['user']['config'])) {
        $_SESSION['user']['config']['LenghtSearchCommande'] = '10';
    }
    if(!array_key_exists('LenghtSearchContactEnt', $_SESSION['user']['config'])) {
        $_SESSION['user']['config']['LenghtSearchContactEnt'] = '10';
    }
    if(!array_key_exists('LenghtSearchContactPart', $_SESSION['user']['config'])) {
        $_SESSION['user']['config']['LenghtSearchContactPart'] = '10';
    }
    if(!array_key_exists('LenghtSearchDevis', $_SESSION['user']['config'])) {
        $_SESSION['user']['config']['LenghtSearchDevis'] = '10';
    }
    if(!array_key_exists('LenghtSearchFacture', $_SESSION['user']['config'])) {
        $_SESSION['user']['config']['LenghtSearchFacture'] = '10';
    }
    if(!array_key_exists('LenghtSearchGeneral', $_SESSION['user']['config'])) {
        $_SESSION['user']['config']['LenghtSearchGeneral'] = '10';
    }
    if(!array_key_exists('LenghtSearchProduit', $_SESSION['user']['config'])) {
        $_SESSION['user']['config']['LenghtSearchProduit'] = '10';
    }
    /*
	$sqlConn->makeRequeteFree("SELECT * FROM user_droits WHERE login = '".$_SESSION['user']['id']."'");
	$r = $sqlConn->process2();
	if($r[0])
		foreach($r[1] as $k => $d)
			$_SESSION['user']['permissions'][$d['droit']] = $d['droit'];
    */
    if($_SESSION['user']['module']['mobile'] == 'oui') {
        ?>
<root>
    <go to="waMainMenu"/>
    <title set="waMainMenu">Menu Principal</title>
</root>
        <?php
    }
    else {
        session_destroy();
        setcookie('login','',$_SERVER['REQUEST_TIME'] - 86400*60);
        ?>
<root>
    <destination mode="replace" zone="form-connect"/>
    <data><![CDATA[<div class="err"><strong>Module non accessible</strong><br/>Vous n'avez pas acheté le module mobile. Vous ne pouvez pas accéder à ZUNO de cette manière.</div> ]]></data>
</root>
        <?php
    }
}
elseif($jeviensdepin == 'non') {
    ?>
<root>
    <destination mode="replace" zone="form-connect"/>
    <data><![CDATA[ <div class="err"><strong>Impossible de vous identifier</strong><br/><?php echo $message; ?></div> ]]></data>
</root>
    <?php
}
ob_end_flush();
?>