<?php
include ('../inc/conf.inc');
include ('../inc/core.inc');
$GLOBALS['LOG']['DisplayDebug'] =
$GLOBALS['LOG']['DisplayError'] = false;

ob_start();

include_once ('lib/Debug.inc.php');
include_once ('lib/ZunoLayerGeneral.inc.php');
include_once ('lib/ZunoLayerContact.inc.php');
include_once ('lib/ZunoLayerSearch.inc.php');
include_once ('lib/ZunoLayerPreference.inc.php');
include_once ('lib/ZunoLayerDevis.inc.php');
include_once ('lib/ZunoLayerAffaire.inc.php');
include_once ('lib/ZunoLayerCommande.inc.php');
include_once ('lib/ZunoLayerFacture.inc.php');
include_once ('lib/ZunoLayerProduit.inc.php');
include_once ('lib/HtmlElement.inc.php');

$PC = new PageContext('iPhone');
$PC->GetVarContext();
$PC->GetChannelContext();
if($PC->GetSessionContext('',false) === false) {
    $isAuthentified = false;
    $_SESSION["language"] = 'fr';
}
else								$isAuthentified = true;


header('Content-type: text/html; charset=UTF-8');
?>
<html>
    <head>
	<title>ZUNO</title>
	<meta http-equiv="Content-type" content="text/html; charset=UTF-8"/>
	<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta names="apple-mobile-web-app-status-bar-style" content="black-translucent" />
	<link rel="apple-touch-icon" href="Img/zuno.png" />
	<link rel="Stylesheet" href="WebApp/Design/Render.css" />
	<?php $cliInf = GetClientBrowserInfo();
	if($cliInf[1] == 'Firefox')
	    echo '<link rel="Stylesheet" href="WebApp/Design/Firefox.css" />';
	?>
	<link rel="Stylesheet" href="../jss/iphone/iphone.css" />
	<link rel="Stylesheet" href="../jss/JSCal2-1.0/css/jscal2.css" />
	<link rel="Stylesheet" href="../jss/JSCal2-1.0/css/border-radius.css" />
	<link rel="Stylesheet" href="../jss/JSCal2-1.0/css/reduce-spacing.css" />
	<script type="text/javascript" src="WebApp/Action/Logic.js"></script>
	<script type="text/javascript" src="../jss/iphone/iphone.js"></script>
	<script language="JavaScript" type="text/javascript" src="../jss/JSCal2-1.0/js/jscal2.js"></script>
	<script language="JavaScript" type="text/javascript" src="../jss/JSCal2-1.0/js/lang/<?php echo $_SESSION["language"]; ?>.js"></script>
    </head>
    <?php
    echo '<body ';
    if ($cfg["windowoptions"]["resizable"])
	echo ' onmouseup="initResizeTable(1)"';
    echo '-dir="rtl">';
    ?>

    <div id="WebApp">
	<div id="loader" class="iItem" style="padding:10px 5px;font-weight:bold;font-size:12px;text-align:center;">
	    <div style="font-size:20px">
		<a href="#" style="display:block;border-width: 0 12px;line-height:45px;-webkit-border-image: url('Img/button-b-black.png') 0 12 0 12;margin:10px;color:white;text-decoration:none;text-align:center;text-shadow:#000 1px -1px 0;font-weight:bold">Test</a>
		<a href="#" style="display:block;border-width: 0 12px;line-height:45px;-webkit-border-image: url('Img/button-b-white.png') 0 12 0 12;margin:10px;color:black;text-decoration:none;text-align:center;text-shadow:#fff 1px 1px 0;font-weight:bold">Test</a>
		<a href="#" style="display:block;border-width: 0 12px;line-height:45px;-webkit-border-image: url('Img/button-b-red.png') 0 12 0 12;margin:10px;color:white;text-decoration:none;text-align:center;text-shadow:rgba(0,0,0,0.2) 1px -1px 0;font-weight:bold">Test</a>
	    </div>
	</div>
	<div id="authentificationToken" style="display:none;">
	    <input type="hidden" name="statut" value="<?php echo ($isAuthentified) ? 'yes' : 'no'; ?>"/>
	</div>


	<!---------------------------------------------- -->
	<!--        Block contenant les en-têtes         -->
	<!---------------------------------------------- -->
	<div id="iHeader">
	    <a href="#" id="waBackButton">Précédent</a>
	    <a href="#" id="waHomeButton"><img src="Img/home.png" alt="accueil"/></a>
	    <a href="#_MainMenu" onclick="WA.HideBar()"><span id="waHeadTitle">ZUNO</span></a>
	</div>

	<!---------------------------------------------- -->
	<!--  Groupe de calques disponibles par defaut   -->
	<!---------------------------------------------- -->
	<div id="iGroup">
	    <!-- Layer de chargement (loader) -->
	    <div id="iLoader">Chargement, veuillez patienter...</div>

	    <?php
	    echo ZunoLayerGeneral::loadDefaultLayer($isAuthentified);
	    echo ZunoLayerContact::loadDefaultLayer();
	    echo ZunoLayerPreference::loadDefaultLayer();
	    echo ZunoLayerDevis::loadDefaultLayer();
	    echo ZunoLayerAffaire::loadDefaultLayer();
	    echo ZunoLayerCommande::loadDefaultLayer();
	    echo ZunoLayerFacture::loadDefaultLayer();
	    echo ZunoLayerProduit::loadDefaultLayer();
	    ?>
	</div>
    </div>
</body>
</html>
<?php
ob_end_flush();
?>
