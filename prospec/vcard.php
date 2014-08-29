<?php

include ('../inc/conf.inc');	// Declare global variables from config files
include ('../inc/core.inc');	// Load core library

loadPlugin('ZModels/ContactModel');
// Whe get the page context
$PC = new PageContext('prospec');
$PC->GetVarContext();

$model = new contactParticulierModel();

$vCard = $model->exportToVcard($PC->rcvG['id_cont']);




header('Content-Type: text/x-vcard');
header('Content-Disposition: inline; filename=vCard_' . date('Y-m-d_H-m-s') . '.vcf');
echo $vCard;

?>
