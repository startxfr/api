<?php
/*------------------------------------------------------------------------+
| GetClientBrowserInfo
|
| Destroy session an log action
+-------------------------------------------------------------------------+
| return 2 chars label for client browser: IE, FF, NN, OP, GK, MZ
+------------------------------------------------------------------------*/
function GetClientBrowserInfo() {
    $curos=strtolower($_SERVER['HTTP_USER_AGENT']);
    $uip=$_SERVER['REMOTE_ADDR'];
    $uht=gethostbyaddr($_SERVER['REMOTE_ADDR']);

    if (strstr($curos,"linux")) {
	$uos="Linux";
    } else if (strstr($curos,"win")) {
	$uos="Windows";
    } else if (strstr($curos,"bsd")) {
	$uos="BSD";
    } else if (strstr($curos,"qnx")) {
	$uos="QNX";
    } else if (strstr($curos,"sun")) {
	$uos="SunOS";
    } else if (strstr($curos,"solaris")) {
	$uos="Solaris";
    } else if (strstr($curos,"irix")) {
	$uos="IRIX";
    } else if (strstr($curos,"aix")) {
	$uos="AIX";
    } else if (strstr($curos,"unix")) {
	$uos="Unix";
    } else if (strstr($curos,"amiga")) {
	$uos="Amiga";
    } else if (strstr($curos,"os/2")) {
	$uos="OS/2";
    } else if (strstr($curos,"beos")) {
	$uos="BeOS";
    } else if (strstr($curos,"ipod")) {
	$uos="iPod";
    } else if (strstr($curos,"iphone")) {
	$uos="iPhone";
    } else if (strstr($curos,"ipad")) {
	$uos="iPad";
    } else if (strstr($curos,"mac")) {
	$uos="MacOS";
    } else {
	$uos="other";
    }

    if (strstr($curos,"khtml")) {
	if (strstr($curos,"safari") and strstr($curos,"mobile")) {
	    $bos="safari Mobile";
	} else if (strstr($curos,"safari")) {
	    $bos="safari";
	} else {
	    $bos="khtml";
	}
    }
    elseif (strstr($curos,"gecko")) {
	if (strstr($curos,"safari")) {
	    $bos="Safari";
	} else if (strstr($curos,"camino")) {
	    $bos="Camino";
	} else if (strstr($curos,"firefox")) {
	    $bos="Firefox";
	} else if (strstr($curos,"netscape")) {
	    $bos="Netscape";
	} else {
	    $bos="Mozilla";
	}
    } else if (strstr($curos,"opera")) {
	$bos="Opera";
    } else if (strstr($curos,"msie")) {
	$bos="IE";
    } else if (strstr($curos,"voyager")) {
	$bos="Voyager";
    } else if (strstr($curos,"lynx")) {
	$bos="Lynx";
    } else {
	$bos="Other";
    }

    return array ($uos,$bos,$uip,$uht);
}

function getStaticUrl($type) {
    if($type == 'img')
    return $GLOBALS['StaticContent']['UrlImg'];
    elseif($type == 'Jss')
    return $GLOBALS['StaticContent']['urlJss'];
    elseif($type == 'imgPhone')
    return $GLOBALS['StaticContent']['UrlImgPhone'];
    elseif($type == 'Webapp')
    return $GLOBALS['StaticContent']['urlWebapp'];
    else
    return '';
}

function getMutoolsPath($type) {
    if($type == 'jpgraph')
    return $GLOBALS['MutTools']['pathJpgraph'];
    else
    return '';
}
?>
