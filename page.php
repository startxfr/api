<?php
/*#########################################################################
#
#   name :       page.php
#   desc :       Display page content
#   categorie :  page
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| FRAMEWORK LOADING
+------------------------------------------------------------------------*/
	include ('inc/conf.inc');		// Declare global variables from config files
	include ('inc/core.inc');		// Load core library

// Whe get the page context
$PC = new PageContext();
$PC->GetFullContext();
// Whe initialize page display
$out = new PageDisplay($PC->channel);
$out->ConfigureWithPageData($PC->Data,$PC->cacheXML);

/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent("");
$out->Process();
?>
