<?php


/**
 *
 */
class actualiteView
{
	/**
	 * Affichage d'une liste d'actualite
	 */
	static function actualiteResultRow($result)
	{
		$out = '';
		$date = '';
		if(is_array($result) and count($result) > 0)
		{
			foreach($result as $k => $v)
			{
				$dateInt = strtotime($v['date']);
				$class = (ucfirst($v['type']) != 'General') ? ucfirst($v['type']) : 'Actualite';
				if($date != substr($v['date'],0,10)) { $date = substr($v['date'],0,10); $out .= '</ul><h2>'.strftime("%A %d %B %Y",$dateInt).'</h2><ul class="iArrow">'; }
				$out .= '<li><a href="Actualite.php?action=view&id='.$v['id'].'" rev="async" class="'.$class.'">' .
					  '<span><img src="../img/actualite/'.$v['type'].'.png"/></span>' .
					  '<em><span class="actualiteHeure">'.strftime("%H:%M",$dateInt).'</span> '.$v['titre'].'</em>' .
					  '</a></li>';
			}
			$out = substr($out,5).'</ul>';
		}
		return '<div class="iList"><ul class="iArrow">'.$out.'</ul></div>';
	}



	/**
	 * Formulaire complet de visualisation d'une fiche actualite.
	 */
	static function view($v = array(), $from = '')
	{
		$class = (ucfirst($v['type']) != 'General') ? ucfirst($v['type']) : 'Actualite';
		$utilisateur = $v['civ'].' '.$v['prenom'].' '.$v['nom'];
		$out  = '<fieldset><ul>';
		$out .= '<li>'.ucfirst($v['titre']).'</li>';
		$out .= '<li><small style="color: #888">'.$v['desc'].'</small></li>';
		$out .= '<li> effectué le '.strftime("%d/%m/%Y %H:%M",strtotime($v['date'])).' par '.$utilisateur.'</li>';
		$out .= '<li><em><i class="'.$class.'"><img src="../img/actualite/'.$v['type'].'.png"/> '.ucfirst($v['type']).'</i></em></li>';
		$out .= '</ul></fieldset>';
		if($v['id_ent'] != '' or
		   $v['id_cont'] != '' or
		   $v['id_aff'] != '' or
		   $v['id_dev'] != '' or
		   $v['id_cmd'] != '' or
		   $v['id_fact'] != '')
		  {
		  	$out  .= '<fieldset><legend>Liens de l\'actualité</legend>';
			$out  .= '<ul class="iArrow">';
			if($v['id_ent'] != '')  $out .= '<li>'.contactEntrepriseView::contactLinkSimple($v).'</li>';
			if($v['id_cont'] != '') $out .= '<li>'.contactParticulierView::contactLinkSimple($v).'</li>';
			if($v['id_aff'] != '')  $out .= '<li>'.affaireView::affaireLinkSimple($v).'</li>';
			if($v['id_dev'] != '')
			$out .= '<li><a href="Devis.php?action=view&id_dev='.$v['id_dev'].'" class="Devis" rev="async"><img src="../img/actualite/devis.png"/> Devis : '.$v['id_dev'].' '.$v['titre_dev'].'</a></li>';
			if($v['id_cmd'] != '')
			$out .= '<li><a href="Commande.php?action=view&id_cmd='.$v['id_cmd'].'" class="Commande" rev="async"><img src="../img/actualite/commande.png"/> Commande : '.$v['id_cmd'].' '.$v['titre_cmd'].'</a></li>';
			if($v['id_fact'] != '')
			$out .= '<li><a href="Facture.php?action=view&id_fact='.$v['id_fact'].'&type='.$v['type_fact'].'" class="Facture" rev="async"><img src="../img/actualite/facture.png"/> '.$v['type_fact'].' : '.$v['id_fact'].' '.$v['titre_fact'].'</a></li>';
			$out  .= '</ul>';
			$out  .= '</fieldset>';
		  }
		  if(($v['type'] == "general") && ($from != 'afterModif'))
		  $output = '<a href="Actualite.php?action=modif&id='.$v["id"].'"  rev="async" rel="action" class="iButton iBAction"><img src="Img/edit.png" alt="Modifier" /></a>
				<div class="iPanel">
				'.$out.'
			</div>';
			else
			{
				$output = '<div class="iPanel">'.$out.'</div>';
			}
		return $output;
	}
	
	static function add($value = '', $onError = array(), $errorMess = '')
	{
		$error   = ($errorMess != '') ? '<div class="err">'.$errorMess.'</div>' : '';
		$form = '<fieldset><legend>Titre de l\'actualité : </legend>';
		$form .= '<ul><li>';
		$form .= HtmlFormIphone::Input('titre', $value['titre'], 'Titre');
		$form .= (in_array('titre',$onError)) ? '<span class="iFormErr"/>' : '';
		$form .= '</ul></li></fieldset>';
		$form .= '<fieldset><legend>Contenu de l\'actualité : </legend>';
		$form .= '<ul><li>';
		$form .= HtmlFormIphone::Textarea('desc', $value['desc']);
		$form .= (in_array('desc',$onError)) ? '<span class="iFormErr"/>' : '';
		$form .= '</ul></li></fieldset>';
		$form .= '<fieldset>
					<a href="#" class="BigButtonValidLeft" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler" /></a>
					<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formAddActualite\', null, event)"><img src="Img/big.valider.png" alt="Valider" /></a>
				</fieldset>';
		$out = '<a href="#"  onclick="return WA.Submit(\'formAddActualite\',null,event)" rel="action" class="iButton iBAction"><img src="Img/save.png" alt="Enregistrer" /></a>
				<form id="formAddActualite" action="Actualite.php?action=doAdd" onsubmit="return WA.Submit(this,null,event)">
				'.$error.'<div class="iPanel">
					'.$form.'
				</div>
				</form>';
		return $out;
	}
	
	static function modif($value = '', $onError = array(), $errorMess = '')
	{
		$error   = ($errorMess != '') ? '<div class="err">'.$errorMess.'</div>' : '';
		$form = '<fieldset><legend>Titre de l\'actualité : </legend>';
		$form .= '<ul><li>';
		$form .= HtmlFormIphone::Input('titre', $value['titre'], 'Titre');
		$form .= (in_array('titre',$onError)) ? '<span class="iFormErr"/>' : '';
		$form .= '</ul></li></fieldset>';
		$form .= '<fieldset><legend>Contenu de l\'actualité : </legend>';
		$form .= '<ul><li>';
		$form .= HtmlFormIphone::Textarea('desc', $value['desc']);
		$form .= (in_array('desc',$onError)) ? '<span class="iFormErr"/>' : '';
		$form .= '<input type="hidden" name="id" value="'.$value['id'].'" />';
		$form .= '</ul></li></fieldset>';
		$form .= '<fieldset>
					<a href="#" class="BigButtonValidLeft" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler" /></a>
					<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formModifActualite\', null, event)"><img src="Img/big.valider.png" alt="Valider" /></a>
				</fieldset>';
		$out = '<a href="#"  onclick="return WA.Submit(\'formModifActualite\',null,event)" rel="action" class="iButton iBAction"><img src="Img/save.png" alt="Enregistrer" /></a>
				<form id="formModifActualite" action="Actualite.php?action=doModif" onsubmit="return WA.Submit(this,null,event)">
				'.$error.'<div class="iPanel">
					'.$form.'
				</div>
				</form>';
		return $out;
	}

}
?>
