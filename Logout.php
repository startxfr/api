<?php
/*#########################################################################
#
#   name :       Aide.delete.php
#   desc :       Delete help tip
#   categorie :  management page
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| FRAMEWORK LOADING 
+------------------------------------------------------------------------*/
	include ('inc/conf.inc');		// Declare global variables from config files
	include ('inc/core.inc');		// Load core library

$Session = new Session();
$Session->CatchSession();
$Session->Deconnect();
$Session->RedirectSession('USER_ENDED');
?>
