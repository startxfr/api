<?php

/**
 *
 */
class ZunoLayerSearch
{

	/**
	 * HTML Select without label before
	 */
	static function searchForm()
	{
		if($_SESSION['searchQuery'] != '') $iTag = ' value="'.$_SESSION['searchQuery'].'"';
return '<form action="Search.php" id="formSearch" onsubmit="return WA.Submit(this,null,event)"><div  class="iFormI">
			<a href="#_SearchResult" rel="action" onclick="return WA.Submit(\'formSearch\',null,event)" class="iButton iBAction"><img src="Img/search.png" alt="Recherche" /></a>

			<fieldset class="attach">
				<legend>Recherche</legend>
				<input type="search" name="search" placeholder="Votre recherche" id="formSearchInput"'.$iTag.'/>
			</fieldset>
	</div></form><div id="SearchResultAsync" style="display:block"></div>';
	}
}
?>
