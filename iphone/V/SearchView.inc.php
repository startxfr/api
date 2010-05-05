<?php


/**
 *
 */
class searchView
{
	/**
	 * Affichage d'une liste d'entreprise
	 */
	static function searchResultRowEntreprise($result)
	{
		$out = '';
		if(is_array($result) and count($result) > 0)
		foreach($result as $k => $v)
		$out .= '<li><a href="Contact.php?action=viewEnt&id_ent='.$v['id_ent'].'" rev="async">' .
			  '<em>'.$v['nom_ent'].'</em>' .
			  '<small>'.$v['cp_ent'].' - '.substr(strtoupper($v['ville_ent']),0,35).'</small>' .
			  '</a></li>';
		
		return $out;
	}

	/**
	 * Affichage d'une liste de contact
	 */
	static function searchResultRowContact($result)
	{
		$out = '';
		if(is_array($result) and count($result) > 0)
		foreach($result as $k => $v)
		$out .= '<li><a href="Contact.php?action=viewPart&id_cont='.$v['id_cont'].'" rev="async">' .
			  '<em>'.ucfirst($v['prenom_cont']).' '.strtoupper($v['nom_cont']).'</em>' .
			  '<small>'.$v['mail_cont'].'</small>' .
			  '</a></li>';
		return $out;
	}
	
	/**
	 * Affichage d'une liste d'affaires
	 */
	 
	 static function searchResultRowAffaire($result)
	 {
	 	$out = '';
		if(is_array($result) and count($result) > 0)
		foreach($result as $k => $v)
		$out .= '<li><a href="Affaire.php?action=view&id_aff='.$v['id_aff'].'" rev="async">' .
			  '<em>'.$v['id_aff'].' - '.$v['titre_aff'].' </em><small><b>'.$v['nom_ent'].'</b> - '.$v['civ_cont'].' '.$v['nom_cont'].' '.$v['prenom_cont'].'</small> 
			  </a></li>';
		return $out;
	 }
	 
	 static function searchResultRowDevis($result)
	 {
	 	$out = '';
		if(is_array($result) and count($result) > 0)
		foreach($result as $k => $v)
		$out .= '<li><a href="Devis.php?action=view&id_dev='.$v['id_dev'].'" rev="async">' .
			  '<em>'.$v['id_dev'].' - '.$v['titre_dev'].' </em><small><b>'.$v['nom_ent'].'</b> - '.$v['civ_cont'].' '.$v['nom_cont'].' '.$v['prenom_cont'].'</small> 
			  </a></li>';
		return $out;
	 }
	 
	 static function searchResultRowCommande($result)
	 {
	 	$out = '';
		if(is_array($result) and count($result) > 0)
		foreach($result as $k => $v)
		$out .= '<li><a href="Commande.php?action=view&id_cmd='.$v['id_cmd'].'" rev="async">' .
			  '<em>'.$v['id_cmd'].' - '.$v['titre_cmd'].' </em><small><b>'.$v['nom_ent'].'</b> - '.$v['civ_cont'].' '.$v['nom_cont'].' '.$v['prenom_cont'].'</small> 
			  </a></li>';
		return $out;
	 }
	 
	 static function searchResultRowFacture($result)
	 {
	 	$out = '';
		if(is_array($result) and count($result) > 0)
		foreach($result as $k => $v)
		$out .= '<li><a href="Facture.php?action=view&id_fact='.$v['id_fact'].'" rev="async">' .
			  '<em>Facture : '.$v['id_fact'].' - '.$v['titre_fact'].' </em><small><b>'.$v['nom_ent'].'</b> - '.$v['civ_cont'].' '.$v['nom_cont'].' '.$v['prenom_cont'].'</small> 
			  </a></li>';
		return $out;
	 }
	 
	 static function searchResultRowProduit($result)
	 {
	 	$out = '';
		if(is_array($result) and count($result) > 0)
		foreach($result as $k => $v)
		$out .= '<li><a href="Produit.php?action=viewProd&id_prod='.$v['id_prod'].'" rev="async">' .
			  '<em>Produit : '.$v['id_prod'].' </em><small><b>'.$v['nom_prod'].'</b> Prix : '.$v['prix_prod'].' €</small> 
			  </a></li>';
		return $out;
	 }
}
?>