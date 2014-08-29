<?php
/*#########################################################################
#
#   name :       OOConverter
#   desc :       Converter for OO,MSOffice -> OO,MSOffice,PDF documents
#   categorie :  plugin module
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/


/**
 * Convert a document acording to the zuno-ooserv configuration of the system
 *
 * require unoconv script and zuno-ooserv rpm package available from startx.fr
 *
 * @param $srcFile String: source file
 * @param $destFile String: destination file
 * @param $OOohost String: host of the ooconverter server
 * @param $OOoport String: port of the ooconverter daemon
 * @return bool: true if converting is OK, false otherwise
 */

function OOConverter($srcFile,$destFile,$format = '',$OOohost="localhost",$OOoport="2002") {
    $availableFormat = OOConverterAvailable();
    $toFormat = ($format != '') ? $format : FileGetExtention($destFile);
    $tmpDir   = $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'];
    if(!array_key_exists($toFormat,$availableFormat))
	return 'Impossible de convertir '.$srcFile.' vers le format '.$toFormat.'<br/> Le document ne peut être généré ou converti.';

    $fromFormat  = FileGetExtention($srcFile);
    $toFormatExt = ($availableFormat[$format] != '') ? substr($availableFormat[$format],strpos($availableFormat[$format], '[')+2,-1) : $format;

    $fileFromName= 'OOConv.'.str_replace(dirname($srcFile).'/','',$srcFile);
    $fileToName  = substr($fileFromName,0, (-1*(strlen($fromFormat)+1))).'.'.$toFormatExt;
    $tmpFromFile = $tmpDir.$fileFromName;
    $tmpToFile   = $tmpDir.$fileToName;
    if (!copy($srcFile, $tmpFromFile))
	return 'La copie du fichier '.$fileFromName.' depuis '.dirname($srcFile).' à echouée.<br/> Le document ne peut être généré ou converti.';

    $cmdArg = '';
    if($OOohost != '' and $OOohost != 'localhost')
	$cmdArg .= ' -s '.$OOohost;
    if($OOoport != '' and $OOoport != '2002')
	$cmdArg .= ' -p '.$OOoport;

    $cmd = 'export HOME=/var/www; unoconv '.$cmdArg.' -f '.$toFormat.' '.$tmpFromFile.' &> '.$tmpDir.'dd; cat '.$tmpDir.'dd; rm -f '.$tmpDir.'dd;';
    $retour=`$cmd`;
    if (preg_match("/unoconv:/",$retour)) {
	return $retour;
    }
    elseif (preg_match("/X11 error:/",$retour)) {
	return 'Erreur dans l\'affichage graphique du serveur de conversion unoconv.<br/> Le document ne peut être généré ou converti.';
    }
    elseif (preg_match("/mmap/",$retour)) {
	return 'Erreur dans les permissions du serveur de conversion unoconv.<br/> Le document ne peut être généré ou converti.';
    }
    else {
	if (!@copy($tmpToFile, $destFile)) {
	    $m = @error_get_last();
	    return 'La copie du fichier '.$tmpToFile.' vers '.$destFile.' à echouée pour la raison suivante:<br/>'.$m['message'].'<br/> Le document ne peut être généré ou converti.';
	}
	else  return true;
    }
}

/**
 * Return an array of the conversions available
 *
 * require unoconv script and zuno-ooserv rpm package available from startx.fr
 *
 * @param $kind String: kind of document to convert (should be document, graphics, presentation or spreadsheet)
 * @return array: containing ext and file description
 */

function OOConverterAvailable($type = '') {
    $list = array();
    // Check if directory already exists
    if ($type == 'document' or $type == '') {
//		$list['bib']	= 'BibTeX [.bib]';
	$list['doc']	= 'Microsoft Word 97/2000/XP [.doc]';
//		$list['doc6']	= 'Microsoft Word 6.0 [.doc]';
//		$list['doc95']	= 'Microsoft Word 95 [.doc]';
//		$list['docbook']	= 'DocBook [.xml]';
//		$list['html']	= 'HTML Document [.html]';
	$list['odt']	= 'Open Document Text [.odt]';
//		$list['ott']	= 'Open Document Text [.ott]';
//		$list['ooxml']	= 'Microsoft Office Open XML [.xml]';
//		$list['pdb']	= 'AportisDoc (Palm) [.pdb]';
	$list['pdf']	= 'Portable Document Format [.pdf]';
//		$list['psw']	= 'Pocket Word [.psw]';
//		$list['rtf']	= 'Rich Text Format [.rtf]';
//		$list['latex']	= 'LaTeX 2e [.ltx]';
//		$list['sdw']	= 'StarWriter 5.0 [.sdw]';
//		$list['sdw4']	= 'StarWriter 4.0 [.sdw]';
//		$list['sdw3']	= 'StarWriter 3.0 [.sdw]';
//		$list['stw']	= 'Open Office.org 1.0 Text Document Template [.stw]';
//		$list['sxw']	= 'Open Office.org 1.0 Text Document [.sxw]';
//		$list['text']	= 'Text Encoded [.txt]';
//		$list['txt']	= 'Plain Text [.txt]';
//		$list['vor']	= 'Writer 5.0 Template [.vor]';
//		$list['vor4']	= 'StarWriter 4.0 Template [.vor]';
//		$list['vor3']	= 'StarWriter 3.0 Template [.vor]';
	$list['xhtml']	= 'XHTML Document [.html]';
    }
    if ($type == 'graphics' or $type == '') {
	$list['bmp']	= 'Windows Bitmap [.bmp]';
	$list['emf']	= 'Enhanced Metafile [.emf]';
	$list['eps']	= 'Encapsulated PostScript [.eps]';
	$list['gif']	= 'Graphics Interchange Format [.gif]';
	$list['html']	= 'HTML Document (OpenOffice.org Draw) [.html]';
	$list['jpg']	= 'Joint Photographic Experts Group [.jpg]';
	$list['met']	= 'OS/2 Metafile [.met]';
	$list['odd']	= 'OpenDocument Drawing [.odd]';
	$list['otg']	= 'OpenDocument Drawing Template [.otg]';
	$list['pbm']	= 'Portable Bitmap [.pbm]';
	$list['pct']	= 'Mac Pict [.pct]';
	$list['pdf']	= 'Portable Document Format [.pdf]';
	$list['pgm']	= 'Portable Graymap [.pgm]';
	$list['png']	= 'Portable Network Graphic [.png]';
	$list['ppm']	= 'Portable Pixelmap [.ppm]';
	$list['ras']	= 'Sun Raster Image [.ras]';
	$list['std']	= 'OpenOffice.org 1.0 Drawing Template [.std]';
	$list['svg']	= 'Scalable Vector Graphics [.svg]';
	$list['svm']	= 'StarView Metafile [.svm]';
	$list['swf']	= 'Macromedia Flash (SWF) [.swf]';
	$list['sxd']	= 'OpenOffice.org 1.0 Drawing [.sxd]';
	$list['sxd3']	= 'StarDraw 3.0 [.sxd]';
	$list['sxd5']	= 'StarDraw 5.0 [.sxd]';
	$list['tiff']	= 'Tagged Image File Format [.tiff]';
	$list['vor']	= 'StarDraw 5.0 Template [.vor]';
	$list['vor3']	= 'StarDraw 3.0 Template [.vor]';
	$list['wmf']	= 'Windows Metafile [.wmf]';
	$list['xhtml']	= 'XHTML [.xhtml]';
	$list['xpm']	= 'X PixMap [.xpm]';
    }
    if ($type == 'presentation' or $type == '') {
//		$list['bmp']	= 'Windows Bitmap [.bmp]';
//		$list['emf']	= 'Enhanced Metafile [.emf]';
//		$list['eps']	= 'Encapsulated PostScript [.eps]';
//		$list['gif']	= 'Graphics Interchange Format [.gif]';
//		$list['html']	= 'HTML Document (OpenOffice.org Draw) [.html]';
	$list['jpg']	= 'Joint Photographic Experts Group [.jpg]';
//		$list['met']	= 'OS/2 Metafile [.met]';
	$list['odd']	= 'OpenDocument Drawing [.odd]';
//		$list['odg']	= 'OpenOffice.org 1.0 Drawing (OpenOffice.org Impress) [.odg]';
//		$list['odp']	= 'OpenDocument Presentation [.odp]';
//		$list['pbm']	= 'Portable Bitmap [.pbm]';
//		$list['pct']	= 'Mac Pict [.pct]';
	$list['pdf']	= 'Portable Document Format [.pdf]';
//		$list['pgm']	= 'Portable Graymap [.pgm]';
//		$list['png']	= 'Portable Network Graphic [.png]';
//		$list['pot']	= 'Microsoft PowerPoint 97/2000/XP Template [.pot]';
//		$list['ppm']	= 'Portable Pixelmap [.ppm]';
	$list['ppt']	= 'Microsoft PowerPoint 97/2000/XP [.ppt]';
//		$list['pwp']	= 'PlaceWare [.pwp]';
//		$list['ras']	= 'Sun Raster Image [.ras]';
//		$list['sda']	= 'StarDraw 5.0 (OpenOffice.org Impress) [.sda]';
//		$list['sdd']	= 'StarImpress 5.0 [.sdd]';
//		$list['sdd3']	= 'StarDraw 3.0 (OpenOffice.org Impress) [.sdd]';
//		$list['sdd4']	= 'StarImpress 4.0 [.sdd]';
//		$list['sti']	= 'OpenOffice.org 1.0 Presentation Template [.sti]';
//		$list['stp']	= 'OpenDocument Presentation Template [.stp]';
	$list['svg']	= 'Scalable Vector Graphics [.svg]';
//		$list['svm']	= 'StarView Metafile [.svm]';
	$list['swf']	= 'Macromedia Flash (SWF) [.swf]';
//		$list['sxi']	= 'OpenOffice.org 1.0 Presentation [.sxi]';
//		$list['tiff']	= 'Tagged Image File Format [.tiff]';
//		$list['vor']	= 'StarImpress 5.0 Template [.vor]';
//		$list['vor3']	= 'StarDraw 3.0 Template (OpenOffice.org Impress) [.vor]';
//		$list['vor4']	= 'StarImpress 4.0 Template [.vor]';
//		$list['vor5']	= 'StarDraw 5.0 Template (OpenOffice.org Impress) [.vor]';
//		$list['wmf']	= 'Windows Metafile [.wmf]';
	$list['xhtml']	= 'XHTML [.xml]';
//		$list['xpm']	= 'X PixMap [.xpm]';
    }
    if ($type == 'spreadsheet' or $type == '') {
	$list['csv']	= 'Text CSV [.csv]';
//		$list['dbf']	= 'dBase [.dbf]';
//		$list['dif']	= 'Data Interchange Format [.dif]';
//		$list['html']	= 'HTML Document (OpenOffice.org Calc) [.html]';
	$list['ods']	= 'Open Document Spreadsheet [.ods]';
//		$list['ooxml']	= 'Microsoft Excel 2003 XML [.xml]';
	$list['pdf']	= 'Portable Document Format [.pdf]';
//		$list['pts']	= 'OpenDocument Spreadsheet Template [.pts]';
//		$list['pxl']	= 'Pocket Excel [.pxl]';
//		$list['sdc']	= 'StarCalc 5.0 [.sdc]';
//		$list['sdc4']	= 'StarCalc 4.0 [.sdc]';
//		$list['sdc3']	= 'StarCalc 3.0 [.sdc]';
//		$list['slk']	= 'SYLK [.slk]';
//		$list['stc']	= 'OpenOffice.org 1.0 Spreadsheet Template [.stc]';
//		$list['sxc']	= 'OpenOffice.org 1.0 Spreadsheet [.sxc]';
//		$list['vor3']	= 'StarCalc 3.0 Template [.vor]';
//		$list['vor4']	= 'StarCalc 4.0 Template [.vor]';
//		$list['vor']	= 'StarCalc 5.0 Template [.vor]';
	$list['xhtml']	= 'XHTML [.xhtml]';
	$list['xls']	= 'Microsoft Excel 97/2000/XP [.xls]';
//		$list['xls5']	= 'Microsoft Excel 5.0 [.xls]';
	$list['xls95']	= 'Microsoft Excel 95 [.xls]';
//		$list['xlt']	= 'Microsoft Excel 97/2000/XP Template [.xlt]';
//		$list['xlt5']	= 'Microsoft Excel 5.0 Template [.xlt]';
//		$list['xlt95']	= 'Microsoft Excel 95 Template [.xlt]';
    }
    return $list;
}

?>
