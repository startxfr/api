<?php

/**
 *
 */
class HtmlElementIphone
{
	/**
	 * HTML Select without label before
	 */
	static function footer()
	{
		$rev = substr(substr($GLOBALS['PROJET']['revision'],6),0,-2);
		return '<div class="iFooter">
					'.$GLOBALS['PROJET']['auteur'].' v '.$GLOBALS['PROJET']['version'].'.'.$rev.'<br/>
					<small>&copy;'.$GLOBALS['PROJET']['copyright'].', tous droits réservés.</small>
				</div>';
	}

	/**
	 * HTML Select without label before
	 */
	static function redirectOnSessionEnd()
	{
		if(!array_key_exists('login',$_COOKIE))
		return '<root>
			<go to="waHome"/>
				<destination mode="replace" zone="form-connect"/>
				<data><![CDATA[ <div class="err"><strong>Votre session est expirée</strong>. Merci de vous identifier à nouveau.</div> ]]></data>
			</root>';
		else
		{
			return'<root><go to="waHomePin"/>
				<title set="waHomePin">Code PIN</title>
				<part><destination mode="replace" zone="waHomePin" create="true"/>
				<data><![CDATA[ '.ZunoLayerGeneral::iFormPin().' ]]></data>
				</part>
			</root>';
		}
	}



	/**
	 * HTML <a> link, filled with the mail icon, and pointing to the given email adress
	 */
	static function linkIconMail($value,$displayText = true)
	{
		$txt = ($displayText) ? $value : '';
		if($value != '')
		return '<a href="mailto:'.$value.'" title="envoyer un email (via IPhone Mail)"><span><img src="'.getStaticUrl('imgPhone').'iconMenu/mail.png" /></span>'.$txt.'</a>';
	}


	/**
	 * HTML <a> link, filled with the mail icon, and pointing to the given email adress
	 */
	static function linkIconMailWithZSend($value,$displayText = true, $fiche = false)
	{
		$txt = ($displayText) ? $value : '';
		if($value != '')
		if($fiche)
		return '<a class="linkCont" href="Send.php?type=mail&email='.urlencode($value).'" rev="async" title="Envoyer un e-mail"><span><img src="'.getStaticUrl('imgPhone').'iconMenu/zsendMail.png" /></span>'.$txt.'</a>';
		else
		return '<a href="Send.php?type=mail&email='.urlencode($value).'" rev="async" title="Envoyer un e-mail"><span><img src="'.getStaticUrl('imgPhone').'iconMenu/zsendMail.png" /></span>'.$txt.'</a>';
	}


	/**
	 * HTML <a> link, filled with the tel icon, and launching a call to the given tel
	 */
	static function linkIconTel($value,$displayText = true)
	{
		$txt = ($displayText) ? $value : '';
		if($value != '')
		return '<a href="tel:'.$value.'" title="Appeler le numéro de téléphone"><span><img src="'.getStaticUrl('imgPhone').'iconMenu/tel.png" /></span>'.$txt.'</a>';
	}


	/**
	 * HTML <a> link, filled with the tel icon, and launching a call to the given tel
	 */
	static function linkIconFax($value,$displayText = true)
	{
		$txt = ($displayText) ? $value : '';
		if($value != '')
		return '<a href="tel:'.$value.'" title="Appler le numéro de fax"><span><img src="'.getStaticUrl('imgPhone').'iconMenu/fax.png" /></span>'.$txt.'</a>';
	}

	/**
	 * HTML <a> link, filled with the tel icon, and launching a call to the given tel
	 */
	static function linkIconFaxWithZSend($value,$displayText = true,$contactName='', $fiche = false)
	{
		$txt = ($displayText) ? $value : '';
		$q = ($contactName != '') ? '&nom='.$contactName : '';
		if($value != '')
		if($fiche)
		return '<a class="linkCont" href="Send.php?type=fax&fax='.urlencode($value).$q.'" rev="async" title="Envoyer un fax à ce contact"><span><img src="'.getStaticUrl('imgPhone').'iconMenu/zsendFax.png" /></span>'.$txt.'</a>';
		else
		return '<a href="Send.php?type=fax&fax='.urlencode($value).$q.'" rev="async" title="Envoyer un fax à ce contact"><span><img src="'.getStaticUrl('imgPhone').'iconMenu/zsendFax.png" /></span>'.$txt.'</a>';
	}


	/**
	 * HTML <a> link, filled with the web icon, and pointing to the given URL
	 */
	static function linkIconWeb($value,$displayText = true)
	{
		$txt = ($displayText) ? $value : '';
		if($value != '')
		return '<a href="http://'.$value.'" target="_blank" title="Consulter le site internet"><span><img src="'.getStaticUrl('imgPhone').'iconMenu/web.png" /></span>'.$txt.'</a>';
	}


	/**
	 * HTML <a> link, filled with the web icon, and pointing to the given URL
	 */
	static function linkIconAddress($ad1,$ad2,$cp,$ville,$pays,$title = '',$displayText = true)
	{
		$q = $t = '';
		if( $pays == '1') $pays = '';
		if( $ad1 != '' and ($cp != '' or $ville != ''))
		{
			$q .= ( $ad1 != '')  ? $ad1.','	: '';
			$t .= ( $ad1 != '')  ? $ad1.'<br/> ': '';
			$q .= ( $ad2 != '')  ? $ad2.','	: '';
			$t .= ( $ad2 != '')  ? $ad2.'<br/> ': '';
			$q .= ( $cp != '')   ? $cp.','	: '';
			$q .= ( $ville != '')? $ville.','	: '';
			if( $cp != '' and $ville != '')	$t .= $cp.' - '.$ville.'<br/> ';
			elseif( $ville != '') 			$t .= $ville.'<br/> ';
			elseif( $cp != '') 			$t .= $cp.'<br/> ';
			$q .= ( $pays != '')  ?  $pays.','	: 'FRANCE,';
			$t .= ( $pays != '')  ? $pays		: '';
			$q  = substr($q,0,-1);
			$q .= ( $title != '')  ?  ' ('.$title.')'	: '';
			$q  = urlencode($q);
			$txt= ($displayText) ? $t : '';
			return '<a href="http://maps.google.com/maps?q='.$q.'" title="localiser cet emplacement sur GoogleMap"><span><img src="'.getStaticUrl('imgPhone').'iconMenu/gmap.png" /></span>'.$txt.'</a>';
		}
	}

	/**
	 * HTML <a> link, filled with the web icon, and pointing to the given URL
	 */
	static function linkIconAddressWithZSend($ad1,$ad2,$cp,$ville,$pays,$title = '',$displayText = true, $fiche = false)
	{
		$q = $t = '';
		if( $pays == '1' or $pays == 'fr') $pays = '';
		if( $ad1 != '' and ($cp != '' or $ville != ''))
		{
			$q .= ( $title != '')  ? '&nom='.urlencode($title): '';
			$q .= ( $ad1 != '')  ? '&add1='.urlencode($ad1): '';
			$q .= ( $ad2 != '')  ? '&add2='.urlencode($ad2): '';
			$q .= ( $cp != '')  ? '&cp='.urlencode($cp): '';
			$q .= ( $ville != '')  ? '&ville='.urlencode($ville): '';
			$q .= ( $pays != '')  ? '&cpays='.urlencode($pays) : '&cpays=fr';
			$txt= ($displayText) ? $t : '';
			if($fiche)
			return '<a class="linkCont2" href="Send.php?type=courrier'.$q.'" rev="async" title="envoyer un courrier postal"><span><img src="'.getStaticUrl('imgPhone').'iconMenu/zsendCourrier.png" /></span>'.$txt.'</a>';
			else
			return '<a href="Send.php?type=courrier'.$q.'" rev="async" title="envoyer un courrier postal"><span><img src="'.getStaticUrl('imgPhone').'iconMenu/zsendCourrier.png" /></span>'.$txt.'</a>';
		}
	}

}

?>
