<?php
/*#########################################################################
#
#   name :       docGenerator
#   desc :       generate OO document from a template document
#   categorie :  plugin module
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/



/**
 * Create portlet for Company detail
 * @param $id_ent String: company ID
 * @return HTML portlet ready to insert
 */
function docGeneratorOpenWorkingDoc($cannevasFile,$prefixDoc = '') {
    $pathTo = $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'].$prefixDoc.substr(md5(rand(1000,9999)+rand(1000,9999)),0,8).'/';
    @rm($pathTo);
    mkdir($pathTo);
    loadPlugin(array('pclzip'));
    $zip = new PclZip($cannevasFile);
    $list = $zip->extract(PCLZIP_OPT_PATH,$pathTo);
    if ($list > 0)
	return array(true,$pathTo);
    else  return array(false);
}

/**
 * Create portlet for Company detail
 * @param $id_ent String: company ID
 * @return HTML portlet ready to insert
 */
function docGeneratorCloseWorkingDoc($workingPath,$NewXMLcontent,$fileName) {
    File_Add2File($workingPath.'content.xml',$NewXMLcontent,true);
    @rm($fileName);
    $archive = new PclZip($fileName);
    $archive->add($workingPath,PCLZIP_OPT_REMOVE_PATH,$workingPath."/");
    return array(true);
}


/**
 * Create portlet for Company detail
 * @param $id_ent String: company ID
 * @return HTML portlet ready to insert
 */
function docGeneratorCutXml($xml,$findString,$parentTagName) {
    if (strpos($xml,$findString)) {
	$pos = strpos($xml,$findString);
	$debut = substr($xml,0,$pos);
	$fin = substr($xml,$pos);
	$from = strrpos($debut,'<'.$parentTagName);
	$to = (strlen($fin)-strpos($fin,'</'.$parentTagName.'>')-(strlen($parentTagName)+3))*-1;
	$out['top'] = substr($xml,0,$from);
	$out['bottom'] = substr($xml,$to);
	$out['middle'] = substr($xml,$from,$to);
	return array(true,$out);
    }
    else  return array(false,$xml);
}


/**
 * Create portlet for Company detail
 * @param $id_ent String: company ID
 * @return HTML portlet ready to insert
 */
function docGeneratorCleanArrayBeforeTemplating($array) {
    $TrimFrom = array("\n", "\t", "&", "\\", "\'");
    $TrimTo   = array("<text:line-break/>", " ", " et ", "", "'");
    $array    = str_replace($TrimFrom,$TrimTo,$array);
    array_walk($array,'trim_array');
    return $array;
}



/**
 * Create portlet for Company detail
 * @param $id_ent String: company ID
 * @return HTML portlet ready to insert
 */
function docGeneratorFragmentTemplating($array,$fragment,$name = 'docGenerator') {
    if(is_array($array)) {
	$GLOBALS['TmpTemplate'][$name] = $fragment;
	foreach ($array as $in => $out) {
	    $input[] = "/\{".$in."\}/";
	    $output[] = $out;
	}
	$result = ProcessTemplating($name,$input,$output);
	unset($input);
	unset($output);
	return $result;
    }
    else  return $fragment;
}




/**
 * Create portlet for Company detail
 * @param $id_ent String: company ID
 * @return HTML portlet ready to insert
 */
function docGeneratorGetZunoConfInfo() {
    foreach ($GLOBALS['zunoClientCoordonnee'] as $k => $v)
	$out['zncc_'.$k] = $v;
    foreach ($GLOBALS['zunoClientStatut'] as $k => $v)
	$out['zncs_'.$k] = $v;
    if($out['zncs_capital'] !='')
	$out['zncs_capital'] = 'au capital de '.$out['zncs_capital'].' &#8364;';
    foreach ($GLOBALS['zunoClientBanque'] as $k => $v)
	$out['zncb_'.$k] = $v;
    $out['zuno_version'] = $GLOBALS['PROJET']['version'];
    return $out;
}


/**
 * Create portlet for Company detail
 * @param $id_ent String: company ID
 * @return HTML portlet ready to insert
 */
function docGeneratorAddZunoConfInfoBeforeTemplating(&$info) {
    $info = array_merge(docGeneratorGetZunoConfInfo(),$info);
}


?>