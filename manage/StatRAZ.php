<?php
/*#########################################################################
#
#   name :       ActualiteManage.php
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
| INITIALIZE PAGE CONTEXT
+------------------------------------------------------------------------*/
// Whe get the page context
$PC = new PageContext('admin');
$PC->GetChannelContext();
$PC->GetVarContext();

/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/
if ($PC->rcvP['act'] != '')
{
	if ($PC->rcvP['act'] == 'page')
	{
		$in['stat_pg']		= 0;
		$in['stat_date_pg']	= DateTimestamp2Univ('');
		$tmpreq 		= new Bdd();
		$tmpreq->MakeRequeteUpdate('page','id_pg',$id,$in);
		$tmpreq->process();
	}
	elseif ($PC->rcvP['act'] == 'reset')
	{
		$tmpreq 		= new Bdd();
		$tmpreq->MakeRequeteFree("UPDATE `page` SET `stat_pg` = NULL, `stat_date_pg` = '".DateTimestamp2Univ('')."' WHERE channel_pg = '".$PC->rcvP['channel']."'");
		$tmpreq->process();
	}
}
header("Location: StatView.php");
?>