<?php
/*#########################################################################
#
#   name :       index.php
#   desc :       Authentification interface
#   categorie :  management page
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| FRAMEWORK LOADING	& LOG ACTIVITY
+------------------------------------------------------------------------*/
	include ('../inc/conf.inc');		// Declare global variables from config files
	include ('../inc/core.inc');		// Load core library
/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/

header("Location: CommandeListe.php");
?>