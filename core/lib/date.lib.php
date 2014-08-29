<?php
/*#########################################################################
#
#   name :       date.inc
#   desc :       library for date management
#   categorie :  core module
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| Universal date to human
|
| Take a universal date (20040530133545) an transform to human output form
+-------------------------------------------------------------------------+
| $date		*   must be a valid universal date
| $output	    type of output
| $DBCorection	    enable/disable use of MySQL4 Correction (2005-01-01 or 20050101)
+-------------------------------------------------------------------------+
| return date
+------------------------------------------------------------------------*/

function DateUniv2Human($date,$output,$forceDate = false) {
    $time = DateUniv2Timestamp($date,$forceDate);
    if($time == '' and $forceDate == false)
	return '';

    switch ($output) {
	case 'univ':		$mask = '%Y-%m-%d %H:%M:%S';
	    break;
	case 'univSimple':	$mask = '%Y-%m-%d';
	    break;
	case 'today':		$mask = '%A %d %B %Y';
	    break;
	case 'fulldate':		$mask = '%A %d %B %Y';
	    break;
	case 'simple':		$mask = '%d/%m/%y';
	    break;
	case 'simpleLong':	$mask = '%d/%m/%Y';
	    break;
	case 'year':		$mask = '%Y';
	    break;
	case 'short':		$mask = '%y%m';
	    break;
	case 'shortdetail':	$mask = '%d/%m/%Y %H:%M';
	    break;
	case 'simpleDH':		$mask = '%d/%m/%Y à %Hh%M';
	    break;
	case 'short_explicit':	$mask = '%d/%m/%Y (%A)';
	    break;
	case 'MY':			$mask = '%B %Y';
	    break;
	case 'heure_detail':	$mask = '%Hh%M %Ss';
	    break;
	case 'heure':		$mask = '%Hh%M';
	    break;
	case 'fulldateheure':	$mask = '%A %d %m %Y à %Hh%M';
	    break;
	default:			$mask = '%A %d %m %Y à %Hh%M et %Ss';
	    break;
    }
    return $out = strftime($mask,$time);
}

/*------------------------------------------------------------------------+
| Timestamp to universal
|
| Transform timestamp to universal
+-------------------------------------------------------------------------+
| $timestamp	   timestamp to transform. default = now
+-------------------------------------------------------------------------+
| return universal date
+------------------------------------------------------------------------*/
function DateTimestamp2Univ ($timestamp = '',$forceDate = false) {
    if($timestamp == '' and $forceDate)
	$timestamp = time();
    elseif($timestamp == '')
	return '';
    else  return strftime('%Y-%m-%d %H:%M:%S',$timestamp);
}

/*------------------------------------------------------------------------+
| Universal to timestamp
|
| Transform universal to timestamp
+-------------------------------------------------------------------------+
| $univ	   univesal to transform. default = now
+-------------------------------------------------------------------------+
| return universal date
+------------------------------------------------------------------------*/
function DateUniv2Timestamp ($date = '',$forceDate = true) {
    $time = strtotime($date);
    if($time === false and $forceDate)
	return strtotime('now');
    elseif($time === false)
	return '';
    else  return $time;
}

/*------------------------------------------------------------------------+
| DateHuman2Univ
|
| Transform universal to timestamp
+-------------------------------------------------------------------------+
| $type	   format de la date ins�r�e
| $univ	   univesal to transform. default = now
+-------------------------------------------------------------------------+
| return universal date
+------------------------------------------------------------------------*/
function DateHuman2Timestamp($date,$forceDate = true) {
    if($date == '' and !$forceDate)
	return '';
    elseif($date == '' and $forceDate)
	return DateTimestamp2Univ('now');

    $test1 = explode(' ',$date);
    if(count($test1) > 1) {
	$d = explode('/',$test1[0]);
	$h = explode(':',$test1[1]);
    }
    else {
	$d = explode('/',$date);
	$h = array(0,0,0);
    }
    $dd = ($d[0] != '') ? $d[0] : 1;
    $dm = ($d[1] != '') ? $d[1] : 1;
    $dy = ($d[2] != '') ? $d[2] : date('y');
    $hh = ($h[0] != '') ? $h[0] : 0;
    $hm = ($h[1] != '') ? $h[1] : 0;
    $hs = ($h[2] != '') ? $h[2] : 0;

    return mktime($hh,$hm,$hs,$dm,$dd,$dy);
}



function DateHuman2Univ ($date,$forceDate = true) {
    if($date == '' and !$forceDate)
	return '';
    $timestamp = DateHuman2Timestamp($date,$forceDate);
    return strftime('%Y%m%d%H%M%S',$timestamp);
}




function finDeMois($time) {
    $date = date('n',$time);
    switch($date) {
	case 1:
	case 3:
	case 5:
	case 7:
	case 8:
	case 10:
	case 12:
	    $jour = '31';
	    $out = date('Y-m-',$time).$jour;
	    break;
	case 4:
	case 6:
	case 9:
	case 11:
	    $jour = '30';
	    $out = date('Y-m-',$time).$jour;
	    break;
	case 2:
	    if(date('L') == '1') {
		$jour = '29';
	    }
	    else {
		$jour = '28';
	    }
	    $out = date('Y-m-',$time).$jour;
	    break;
    }
    return $out;
}
function le10DuMois($time) {
    $date=date('j',$time);
    if($date<=10) {
	return date('Y-m-',$time).'10';
    }
    else {
	$date=date('n',$time);
	if($date < 12) {
	    return date('Y-',$time).$date++.'-10';
	}
	else {
	    $annee=date('Y',$time)+1;
	    return $annee.'-01-10';
	}
    }
}
?>
