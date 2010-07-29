<?php
// On inclus les librairies et les fichiers de configurations
include ('../inc/conf.inc');
include ('../inc/core.inc');

// On inclus les librairies speciales iPhone
include_once ('lib/Debug.inc.php');



// On recupère les informations sur le contexte de la page
// (channel, variables d'entrée, session)
$PC = new PageContext('iPhone');
$PC->GetVarContext();
$PC->GetChannelContext();
// Contrôle de la session et de sa validité
if($PC->GetSessionContext('',false) === false) {
    echo HtmlElementIphone::redirectOnSessionEnd();
    exit;
}



if($PC->rcvG['type'] == 'view') {
    if($PC->rcvG['file'] != '' and file_exists($GLOBALS['REP']['appli'].$PC->rcvG['file'])) {
	PushFileToBrowser($GLOBALS['REP']['appli'].$PC->rcvG['file']);
	exit;
    }
    else {
	?>
<root>
    <part><destination mode="before" zone="<?php echo $PC->rcvG['__source']; ?>" create="true" />
	<data><![CDATA[<div class="err">Erreur, impossible de trouver le document.</div>]]></data>
    </part>
</root>
	<?php
    }
}
?>
