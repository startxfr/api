<?php

/**
 * Classe qui gère tous les liens entre les contact et les fichiers
 *
 * @author STARTX
 * @version 1.0
 */
class contactGnose {
    private $bddtmp;

    /**
     * Constructeur il charge la classe de connexion à la BDD
     */
    function __construct() {
	$this->bddtmp = new contactParticulierModel();
    }

    /**
     * Méthode qui génère un export tableur des contact
     * @param array $contact Toutes les informations sur les contact
     * @param string $format Indique le format de sortie
     * @return string
     */
    public function  ContactExportTableurConverter($contact,$format = '') {
	loadPlugin(array('OOConverter'));
	$availableFormat = OOConverterAvailable('spreadsheet');
	if(!array_key_exists($format,$availableFormat)) $format = 'pdf';
	$fileFrom = $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'].$this->ContactExportTableur($contact);
	$formatExt = ($availableFormat[$format] != '') ? substr($availableFormat[$format],strpos($availableFormat[$format], '[')+2,-1) : $format;
	$fromFormat  = FileGetExtention($fileFrom);
	$fileTo = substr($fileFrom,0, (strlen($fromFormat)+1)*-1).'.'.$formatExt;
	if($format == 'ods') return $fileTo;
	$return = OOConverter($fileFrom,$fileTo,$format);
	if($return === true) return $fileTo;
	else			   return $return;
    }

    /**
     * Méthode qui génère le tableur d'export des contact
     * @param array $contact Toutes les informations sur les contact à exporter
     * @return string Le nom du fichier généré.
     */
    private function ContactExportTableur($contacts) {
	loadPlugin(array('OOConverter'));
	$cannevasPath = $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['PropsecConf']['dir.cannevas.exportTableur'];
	$cannevasFile = $cannevasPath.$GLOBALS['PropsecConf']['cannevas.exportContactTableur'];
	if(is_file($cannevasFile)) {
	    $extIn 	= FileGetExtention($cannevasFile);
	    $TmpPath = $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'];
	    $ExportTmpPath = $TmpPath.'ExportTmp/';
	    @rm($ExportTmpPath);
	    mkdir($ExportTmpPath);
	    loadPlugin(array('pclzip'));
	    $zip = new PclZip($cannevasFile);
	    $list = $zip->extract(PCLZIP_OPT_PATH,$ExportTmpPath);

	    $contentFileUri = $ExportTmpPath.'content.xml';
	    $xmlContent = file_get_contents($ExportTmpPath.'content.xml');

	    //config des convertion de champs
	    $TrimFrom = array("\n", "\t", "&", "\\", "\'", "<",">");
	    $TrimTo  = array(" ", " ", " et ", "", "'", "&lt;","&gt;");


	    $pos1 = strpos($xmlContent,'{nom_ent}');
	    $debut = substr($xmlContent,0,$pos1);
	    $fin = substr($xmlContent,$pos1);
	    $from = strrpos($debut,'<table:table-row');
	    $to = (strlen($fin)-strpos($fin,'</table:table-row>')-18)*-1;
	    $exportDebutDocument = substr($xmlContent,0,$from);
	    $exportFinDocument = substr($xmlContent,$to);
	    $GLOBALS['TmpTemplate']['exportMiddleDocument'] = substr($xmlContent,$from,$to);

	    if (is_array($contacts)) {
		foreach ($contacts as $kk => $contact) {
		    $contact = str_replace($TrimFrom,$TrimTo,$contact);
		    foreach ($contact as $in => $out) {
			$input[] = "/\{".$in."\}/";
			$output[] = $out;
		    }
		    $outContact .= ProcessTemplating('exportMiddleDocument',$input,$output);
		    unset($input);
		    unset($output);
		}
	    }
	    else Logg::loggerError('contactGnose::ContactExportTableur() ~ Impossible de trouver des contacts à afficher ',count($contacts),__FILE__.'@'.__LINE__);
	    $xmlContent = $exportDebutDocument.$outContact.$exportFinDocument;
	    unset($outContact);

	    File_Add2File($ExportTmpPath.'content.xml',$xmlContent,TRUE);
	    $ArchivePath = $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'];
	    $ODTName = 'ContactExport.ods';

	    @rm($ArchivePath.$ODTName);
	    $archive = new PclZip($ArchivePath.$ODTName);
	    $archive->add($ExportTmpPath,PCLZIP_OPT_REMOVE_PATH,$ExportTmpPath.'/');
	    $outputFile = $ODTName;
	    Logg::loggerInfo('contactGnose::ContactExportTableur() ~ Export de la liste des contacts '.$ODTName,count($contacts),__FILE__.'@'.__LINE__);
	    return $outputFile;
	}
	else Logg::loggerError('contactGnose::ContactExportTableur() ~ Impossible de trouver le cannevas '.$cannevasFile,count($contacts),__FILE__.'@'.__LINE__);
    }
}
?>
