<?php
class generalView
{
	static function droits($partie = '')
	{
		return '<div class="iPanel"><br/><br/>
				<div class="err"><br/>Vous n\'avez pas acheté le module pour accéder à cette page.<br/>
				Pour y accéder vous avez besoin du module '.$partie.'</div>
				<br/></div>';
	}
	static function droitsAdmin()
	{
		return '<div class="iPanel"><br/><br/>
				<div class="err"><br/>Vous n\'avez pas les droits suffisants pour accéder à cette page<br/></div>
				<br/></div>';
	}
	static function CPToolbox()
	{
		$out 	 = '<a href="#"  onclick="return WA.Submit(\'formCPToolbox\',null,event)" rel="action" class="iButton iBAction"><img src="Img/search.png" alt="Rechercher" /></a>
				<form id="formCPToolbox" action="Preference.php?action=validFormCP" onsubmit="return WA.Submit(this,null,event)">
				<div class="iPanel">
					<fieldset><ul>
							<li>'.HtmlFormIphone::InputLabel('valeur','','CP / ville : ').'</li>
					</ul></fieldset>
				</div>
				</form>';
		$out 	.= '<div id="resultToolboxCP"></div>';
		return $out;
	}
	static function ResultCPToolbox($data = array())
	{
		$out = '<div class="iList"><ul>';
		foreach($data as $v)
		{
			$out .= '<li><a href="http://maps.google.com/maps?q='.urlencode($v['CP'].' - '.$v['Ville']).'" >'.$v['Ville'].' '.$v['CP'].' <small>('.$v['Pays'].')</small></a></li>';
		}
		$out .= '</ul></div>';
		return $out;
	}
	static function TVAToolbox()
	{
		$list[0] = '0%';
		$list['0.055'] = '5.5%';
		$list['0.196'] = '19.6%';
		$out 	 = '<a href="#"  onclick="return WA.Submit(\'formTVAToolbox\',null,event)" rel="action" class="iButton iBAction"><img src="Img/search.png" alt="Rechercher" /></a>
				<form id="formTVAToolbox" action="Preference.php?action=validFormTVA" onsubmit="return WA.Submit(this,null,event)">
				<div class="iPanel">
					<fieldset><ul>
							<li>'.HtmlFormIphone::InputLabel('ht','','Prix HT : ').'</li>
							<li>'.HtmlFormIphone::InputLabel('ttc','','Prix TTC : ').'</li>
							<li>'.HtmlFormIphone::SelectLabel('tva',$list,'', 'TVA : ').'</li>
					</ul></fieldset>
				</div>
				</form>';
		$out 	.= '<div id="resultToolboxTVA"></div>';
		return $out;
	}
	static function ResultTVAToolbox($texte, $valeur, $unite)
	{
		return '<div class="iPanel"><div class="iList"><ul><li>'.$texte.$valeur.$unite.'</li></ul></div></div>';
	}
}
?>
