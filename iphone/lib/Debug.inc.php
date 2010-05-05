<?php


function ZunoErrorHandler($errno, $errstr, $errfile, $errline)
{

	$o = $t = '';
	switch ($errno)
	{
		case E_ERROR:		$t = 'Error'; 		break;
		case E_CORE_ERROR:	$t = 'Core Error'; 	break;
		case E_USER_ERROR:	$t = 'User Error'; 	break;
		case E_WARNING:		$t = 'Alert';		break;
		case E_CORE_WARNING:	$t = 'Core Alert';	break;
		case E_USER_WARNING:	$t = 'User Alert';	break;
		case E_NOTICE:		$t = ''; break;
		case E_USER_NOTICE:	$t = ''; break;
		case E_STRICT:		$t = '';break;
		default:			$t = 'Autre'; break;
	}

	if($t != '')
	{
		$o.= "<style>.innerDebug a {display: inline }</style>";
		$o.= "<ul><li>$t</li>";
		$o.= "<li><label>Fichier :</label><small>$errfile</small></li>";
		$o.= "<li><label>Ligne :</label><small>$errline</small></li></ul>";
		$o.= "<ul><li><div class=\"innerDebug\">".nl2br($errstr)."</div></li></ul>";

		ob_end_clean();
		ob_start();
		header("Content-type: text/xml");
		echo "<?xml version=\"".$GLOBALS['CACHEXML']['version']."\" encoding=\"".$GLOBALS['CACHEXML']['encoding']."\"?>";
		echo "<root>
				<go to=\"waError\"/>
				<title set=\"waError\">$t</title>
				<part>
					<destination mode=\"replace\" zone=\"waError\" create=\"true\"/>
					<data><![CDATA[
						<div class=\"iPanel\">
							<fieldset>$o</fieldset>
						</div>
					]]></data>
				</part>
			</root>";
		ob_end_flush();
		exit(1);

	}
}

set_error_handler("ZunoErrorHandler");





function displayArrayAsList($session, $numero = 0)
{
	foreach($session as $v=>$k)
	{
		$out .= '<li style="padding-left:'.$numero.'0px;">['.$v.'] => ['.$k.']</li>';
		if(is_array($k))
		{
			$numero+=5;
			$out .= displayArrayAsList($k, $numero);
			$numero-=5;
		}
	}
	return $out;
}
?>