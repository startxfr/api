<?php
/*------------------------------------------------------------------------+
| FRAMEWORK LOADING	& LOG ACTIVITY
+------------------------------------------------------------------------*/
include ('../inc/conf.inc');		// Declare global variables from config files
include ('../inc/core.inc');		// Load core library
loadPlugin(array('ZA.Wsdl','ZModels/ProduitModel', 'ZView/ProduitView', 'ZunoSxa'));
setlocale(LC_ALL,'fr_FR.UTF8');
$GLOBALS['currentChannel'] = 'webservice';
$GLOBALS['LOG']['DisplayDebug'] =
$GLOBALS['LOG']['DisplayError'] = false;
if($_SERVER['QUERY_STRING'] == 'wsdl')
    Logg::loggerInfo('webservice sxa.tools ~ Appel au fichier WSDL CLIENT',"IP : ".$_SERVER['REMOTE_ADDR'],__FILE__.'@'.__LINE__);
else Logg::loggerInfo('webservice sxa.tools ~ Appel au webservice CLIENT',"IP : ".$_SERVER['REMOTE_ADDR'],__FILE__.'@'.__LINE__);

$server = new zunoWsdlServer('tools','Ensemble de petits outils très pratique sur Sxa');
//Initialisation du serveur

$server->registerAuthenticatedAction('getProduits',
	array('all' => 'xsd:Boolean'),
	array('return' => 'xsd:string'),
	'Donne les produits en rapport avec ZUNO ou tous les produits'
);
//Web service pour récupérer les données des produits

$server->service();
//Lancement des Webservices

function getProduits($token, $all){
    Logg::loggerInfo('webservice sxa.client.getProduits() ~ Début du traitement',"Tous : ".$all,__FILE__.'@'.__LINE__);
    global $zunoLastError;
    if(zunoWsdlServer::checkServerCredentials($token)) {
        $model = new produitModel();
        if($all)
             return serialize($model->getAllZunoProduits());
        else return serialize($model->getAllZunoProduits("Z"));
    }
    else return zunoWsdlServer::raiseFault('accesNonAutorisé','webservice sxa.tools.getProduits',$zunoLastError,array($token),__FILE__,__LINE__);
}
?>