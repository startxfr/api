<?php
class zunoLayerProduit
{
	static function loadDefaultLayer()
	{
		$footer = HtmlElementIphone::footer();
		if($_SESSION['prodSearch'] != '') $iTag = ' value="'.$_SESSION['prodSearch'].'"';
		return '<!------------------------------------ -->
				<!--   Produits : Menu général         -->
				<!------------------------------------ -->
				<div class="iLayer" id="waMenuProduit" title="Produits">
				<a href="Produit.php?action=addProduit" rev="async" rel="action" class="iButton iBClassic"><img src="Img/add.png" alt="Ajouter une référence" /></a>
				<a href="#" rel="back" class="iButton iBBack" onclick="return WA.Back()">Retour</a>
				<div class="iMenu">

 				<ul class="iArrow">
 					<li><a href="#_SearchProduit" class="Produit"><img src="Img/iconMenu/produit.search.png" alt="Recherche" /> Rechercher un produit</a></li>
					<li><a href="Produit.php?action=addProduit" rev="async" rel="action" class="Produit"><img src="Img/iconMenu/produit.add.png" alt="Ajouter" /> Ajouter une référence</a></li>
 				</ul>
 				<ul class="iArrow">
 					<li><a href="#_SearchFournisseur" class="Produit"><img src="Img/iconMenu/fournisseur.search.png" alt="Recherche" /> Rechercher un fournisseur</a></li>
					<li><a href="Produit.php?action=addFournisseur" rev="async" rel="action" class="Produit"><img src="Img/iconMenu/fournisseur.add.png" alt="Ajouter" /> Ajouter un fournisseur</a></li>
 				</ul>
				</div>
				'./*self::produitForm().'<br class="clear"/>'.$footer.*/'
				</div>
				<!------------------------------------ -->
				<!--   Produits : Menu recherche       -->
				<!------------------------------------ -->
				<div class="iLayer" id="waSearchProduit" title="Recherche">
				<a href="#"  onclick="return WA.Submit(\'formSearchProduit\',null,event)" rel="action" class="iButton iBAction"><img src="Img/search.png" alt="recherche" /></a>
				<form id="formSearchProduit" action="Produit.php?action=searchProduit" onsubmit="return WA.Submit(this,null,event)">
					<div class="iPanel">
						<div id="form-searchProduit"></div>
							<fieldset>
								<ul>
									<li><input type="text" name="query" placeholder="recherche"'.$iTag.'/></li>

								</ul>
								<br/>
								<a href="#" onclick="return WA.Submit(\'formSearchProduit\',null,event)" class="whiteButton"><img src="Img/search.png" style="float: left"/><span>Lancer la recherche</span></a>
							</fieldset>
						</div>
				</form>'.$footer.'
				</div>
				<div class="iLayer" id="waSearchFournisseur" title="Recherche">
				<a href="#"  onclick="return WA.Submit(\'formSearchFournisseur\',null,event)" rel="action" class="iButton iBAction"><img src="Img/search.png" alt="recherche" /></a>
				<form id="formSearchFournisseur" action="Produit.php?action=searchFournisseur" onsubmit="return WA.Submit(this,null,event)">
					<div class="iPanel">
						<div id="form-searchFournisseur"></div>
							<fieldset>
								<ul>
									<li><input type="text" name="query" placeholder="recherche"'.$iTag.'/></li>

								</ul>
								<br/>
								<a href="#" onclick="return WA.Submit(\'formSearchFournisseur\',null,event)" class="whiteButton"><img src="Img/search.png" style="float: left"/><span>Lancer la recherche</span></a>
							</fieldset>
						</div>
				</form>'.$footer.'
				</div>';
	}
}
?>
