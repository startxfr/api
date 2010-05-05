<?php

/**
 *
 */
class ZunoLayerActualite
{

	/**
	 * HTML Select without label before
	 */
	static function actualiteForm($tri = '')
	{
		if($tri == 'general') $sc['g'] = ' class="select"';
		elseif($tri == 'affaire') $sc['a'] = ' class="select"';
		elseif($tri == 'devis') $sc['d'] = ' class="select"';
		elseif($tri == 'commande') $sc['c'] = ' class="select"';
		elseif($tri == 'facture') $sc['f'] = ' class="select"';
		else  $sc['all'] = ' class="select"';
	return '<div  class="iFormI">
			<a href="Actualite.php?action=add" rel="action" rev="async" class="iButton iBAction"><img src="Img/add.png" alt="Ajouter" /></a>
			<ul>
				<li'.$sc['g'].' id="AFTgeneral"><a href="Actualite.php?tri=general" rev="async" onclick="switchActualiteTri(\'AFTgeneral\')">Général</a></li>
				<li'.$sc['a'].' id="AFTaffaire"><a href="Actualite.php?tri=affaire" rev="async" onclick="switchActualiteTri(\'AFTaffaire\')">Affaire</a></li>
				<li'.$sc['d'].' id="AFTdevis"><a href="Actualite.php?tri=devis" rev="async" onclick="switchActualiteTri(\'AFTdevis\')">Devis</a></li>
				<li'.$sc['c'].' id="AFTcommande"><a href="Actualite.php?tri=commande" rev="async" onclick="switchActualiteTri(\'AFTcommande\')">Commande</a></li>
				<li'.$sc['f'].' id="AFTfacture"><a href="Actualite.php?tri=facture" rev="async" onclick="switchActualiteTri(\'AFTfacture\')">Facture</a></li>
				<li'.$sc['all'].' id="AFTall"><a href="Actualite.php?tri=" rev="async" onclick="switchActualiteTri(\'AFTall\')">Tout</a>
			</ul>
	</div><div id="ActualiteResultAsync" class="iList iListFormI"></div>';
	}
}
?>
