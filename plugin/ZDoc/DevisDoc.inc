<?php

/**
 * Classe qui gère tous les liens entre les devis et les fichiers
 *
 * @author STARTX
 * @version 1.0
 */
class devisGnose {
    private $bddtmp;

    /**
     * Constructeur il charge la classe de connexion à la BDD
     */
    function __construct() {
	$this->bddtmp = new devisModel();
    }

    /**
     * Méthode qui génère les code barres.
     * @param string $id_dev L'id du devis
     * @param string $fileTo Le path fichier à générer
     * @param string $size La taille du code barre
     * @return string Le path du fichier généré
     */
    static function generateBarreCode($id_dev,$fileTo = "",$size = "150x40") {
	if($id_dev != "") {
	    if($fileTo == "")
		$fileTo = $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'].'barrecode.'.$id_dev.'.png';
	    loadPlugin(array('barcode/barcode','barcode/c128bobject'));

	    $obj = new C128BObject(150,40,68,$id_dev);
	    $obj->DrawObject(1);
	    $obj->FlushObject($fileTo);
	    //$obj->DestroyObject();
	    unset($obj);
	    return $fileTo;
	}
    }

    /**
     * Méthode qui génère un fichier devis
     * @param string $id_dev L'id du devis à générer
     * @param string $outputExt Le format de sortie
     * @param string $cannevas Le cannevas à utiliser
     * @return string Le nom du fichier généré
     */
    public function DevisGenerateDocument($id_dev,$outputExt = "",$cannevas = "") {
	loadPlugin(array('OOConverter','docGenerator'));
	if (array_key_exists($cannevas,$GLOBALS['ZunoDevis']))
	    $cannevasFile = $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['ZunoDevis']['dir.cannevas'].$GLOBALS['ZunoDevis'][$cannevas];
	else  $cannevasFile = $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['ZunoDevis']['dir.cannevas'].$GLOBALS['ZunoDevis']['cannevas.defaut'];

	$entreprise = $this->bddtmp->getDataFromID($id_dev);
	$entreprise = $entreprise[1][0];
	$entreprise['date_dev'] = DateUniv2Human($entreprise['datemodif_dev'], 'fulldate');
	$entreprise['tauxTVA_ent'] = $entreprise['tva_dev'];

	if(count($entreprise) > 0) {
	    if($entreprise['id_pays'] == "1") $entreprise['nom_pays'] = "";


	    $entreprise['nom_commercial'] = $entreprise['civ']." ".$entreprise['prenom']." ".$entreprise['nom'];
	    $entreprise['mail_commercial'] = $entreprise['mail'];
	    $entreprise['id_dev'] = $entreprise['id_dev'];

	    $produits = $this->bddtmp->getProduitsFromID($id_dev);
	    $produits = $produits[1];
	    $total = 0;
	    if (count($produits) > 0) {
		foreach ($produits as $key => $produit) {

		    $quantite 	= $produit['quantite'];
		    $prix		= $produit['prix'];
		    $remise	= $produit['remise'];
		    $totalRow	= ($quantite*$prix)-((($quantite*$prix)*$remise)/100);
		    $produit['RefProd'] 	= $produit['id_produit'];
		    $produit['Description'] = $produit['desc'];
		    if(substr($produit['quantite'],-3) == '.00')
			$produit['quantite'] = substr($produit['quantite'],0,-3);
		    else  $produit['quantite'] = number_format($produit['quantite'],2,',',' ');
		    if($produit['remise'] > 0) {
			if(substr($produit['remise'],-3) == '.00')
			    $produit['remise'] = substr($produit['remise'],0,-3).'%';
			else  $produit['remise'] = number_format($produit['remise'],2,',',' ').'%';
		    }
		    else  $produit['remise'] = '';
		    $produit['prix'] = number_format($produit["prix"],2,',',' ');
		    $produit['total']= number_format(round($totalRow,2),2,',',' ');
		    $produits[$key] = $produit;
		    $entreprise = array_merge($produit,$entreprise);
		    $total = $total+$totalRow;
		}
	    }
	    else Logg::loggerNotice('devisGnose::DevisGenerateDocument() ~ Impossible de trouver les produits du devis '.$id_dev,$id_dev,__FILE__.'@'.__LINE__);

	    $entreprise['totalHT'] = number_format(round($total,2),2,',',' ');
	    $entreprise['totalTVA'] = number_format(round(($total*$entreprise['tauxTVA_ent']/100),2),2,',',' ');
	    $entreprise['totalTTC'] = number_format(round($total+($total*$entreprise['tauxTVA_ent']/100),2),2,',',' ');
	}
	else Logg::loggerError('devisGnose::DevisGenerateDocument() ~ Impossible de trouver les informations sur le devis '.$id_dev,$id_dev,__FILE__.'@'.__LINE__);

	if(is_file($cannevasFile) and
		count($entreprise) > 0 and
		FileGetExtention($cannevasFile) == "odt") {
	    $prefixDoc  = 'Devis';
	    $fileSuffix = $prefixDoc.'.'.$id_dev.'.';
	    $TmpPath = $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'].$prefixDoc.substr(md5(rand(1000,9999)+rand(1000,9999)),0,8).'/';
	    $extract = docGeneratorOpenWorkingDoc($cannevasFile,$prefixDoc);
	    if ($extract[0]) {
		$TmpPath = $extract[1];
		$xml = file_get_contents($TmpPath.'content.xml');
		if (strpos($xml,"{LIGNE_PROD}")) {
		    $cutBarreCode = docGeneratorCutXml($xml,'BarreCode','draw:frame');

		    if($cutBarreCode[0]) {
			$finInPic = substr($cutBarreCode[1]['middle'],strpos($cutBarreCode[1]['middle'],'xlink:href="')+12);
			$BarreCodePict = substr($finInPic,0,strpos($finInPic,'"'));
			$this->generateBarreCode($id_dev,$TmpPath.$BarreCodePict);
		    }

		    $cutLigneProd = docGeneratorCutXml($xml,'{LIGNE_PROD}','table:table-row');
		    if($cutLigneProd[0]) {
			$DevisDebutProd = $cutLigneProd[1]['top'];
			$DevisLigneProd = $cutLigneProd[1]['middle'];
			$DevisFinProd   = $cutLigneProd[1]['bottom'];
		    }
		    else  $DevisDebutProd = $cutLigneProd[1];

		    if (count($produits) > 0)
			foreach ($produits as $key => $produit) {
			    $produit['LIGNE_PROD']	= '';
			    $produit   = docGeneratorCleanArrayBeforeTemplating($produit);
			    if($cutLigneProd[0]) $OutLigne .= docGeneratorFragmentTemplating($produit,$DevisLigneProd,'DevisLigneProd');
			}

		    $entreprise   = docGeneratorCleanArrayBeforeTemplating($entreprise);
		    docGeneratorAddZunoConfInfoBeforeTemplating($entreprise);
		    $DevisDebutProd = docGeneratorFragmentTemplating($entreprise,$DevisDebutProd,'DevisDebutProd');
		    $DevisFinProd = docGeneratorFragmentTemplating($entreprise,$DevisFinProd,'DevisFinProd');
		    $NewXMLcontent = $DevisDebutProd.$OutLigne.$DevisFinProd;
		    $xmls = file_get_contents($TmpPath.'styles.xml');
		    $xmls = docGeneratorFragmentTemplating($entreprise,$xmls,'StyleGenerator');
		    File_Add2File($TmpPath.'styles.xml',$xmls,true);

		    $ArchivePath = $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'];
		    $ODTName = $fileSuffix.'odt';
		    $PDFName = $fileSuffix.$outputExt;
		    $fileClose = docGeneratorCloseWorkingDoc($TmpPath,$NewXMLcontent,$ArchivePath.$ODTName);

		    if($outputExt != 'odt' and $fileClose[0]) {
			@rm($ArchivePath.$PDFName);
			$retour = OOConverter($ArchivePath.$ODTName,$ArchivePath.$PDFName,$outputExt);
			if(is_string($retour))
			    return false;
		    }
		    else Logg::loggerInfo('devisGnose::DevisGenerateDocument() ~ Génération du document pour le devis '.$id_dev.' (fichier '.$PDFName.')',$cannevasFile,__FILE__.'@'.__LINE__);
		    return $PDFName;
		}
		else Logg::loggerError('devisGnose::DevisGenerateDocument() ~ Impossible de trouver la sequence {LIGNE_PROD} dans le cannevas '.$cannevasFile,$cannevasFile,__FILE__.'@'.__LINE__);
	    }
	    else Logg::loggerError('devisGnose::DevisGenerateDocument() ~ Impossible d\'ouvrir le cannevas '.$cannevasFile,$extract,__FILE__.'@'.__LINE__);
	}
	else Logg::loggerError('devisGnose::DevisGenerateDocument() ~ Impossible de trouver le cannevas '.$cannevasFile.' ou les informations sur le devis '.$id_dev,$entreprise,__FILE__.'@'.__LINE__);
    }

    /**
     * Méthode qui se charge d'effectuer la sauvegarde d'un fichié généré dans la GNOSE
     * @param string $fileFrom Le nom du fichier à sauver
     * @param string $fileName Le nom du fichié une fois sauvé
     * @param string $affaire L'id de l'affaire lié
     * @param string $message Le message pour subversion
     * @return bool Indique si tout s'est bien déroulé
     */
    public function DevisSaveDocInGnose($fileFrom,$fileName,$affaire,$message = "") {
	$bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$bddtmp->makeRequeteSelect('affaire','id_aff',$affaire);
	$aff = $bddtmp->process();
	$aff = $aff[0];

	if ($message == "") $message = "Changement du devis pour l'affaire ".$affaire." par ".$_SESSION['user']['id'];
	$message = escapeshellcmd($message);

	$PathTo = $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoAffaire']['dir.affaire'].$aff['dir_aff'];
	$FileExist = file_exists ($PathTo.$fileName);

	if(copy($fileFrom,$PathTo.$fileName)) {
	    if(!$FileExist) {
		$CmdBase = "svn add --config-dir ".$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir']." ";
		if ($GLOBALS['LOG']['DisplayDebug'])
		    $GLOBALS['LogSVNProcess'][] = $CmdBase.$PathTo.$fileName;
		shell_exec($CmdBase.$PathTo.$fileName);
		chmod($PathTo.$fileName,0770);
	    }
	    $uri	= " -m \"".$message."\" ".$PathTo.$fileName;
	    $CmdBase	= "export EDITOR=\"vi\"; export LC_CTYPE=\"fr_FR.UTF-8\"; export LANG=\"fr_FR.UTF-8\"; svn ci --config-dir ".$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir']." --username ".$_SESSION["user"]["id"]." ";
	    $CmdBase1	= "export LANG=\"en_US.UTF-8\"; export LC_CTYPE=\"en_US.UTF-8\"; export EDITOR=\"vi\"; svn update --non-interactive --no-auth-cache --config-dir ".$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir']." --username ".$_SESSION["user"]["id"]." ";
	    if ($GLOBALS['LOG']['DisplayDebug']) {
		$GLOBALS['LogSVNProcess'][] = $CmdBase.$uri;
		$GLOBALS['LogSVNProcess'][] = $CmdBase1.$GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir']." &";
	    }
	    shell_exec($CmdBase.$uri);
	    shell_exec($CmdBase1.$GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir']." &");
	    Logg::loggerInfo('devisGnose::DevisSaveDocInGnose() ~ Sauvegarde du devis '.$fileName.' dans l\'affaire '.$affaire,array($fileFrom,$fileName,$affaire,$message),__FILE__.'@'.__LINE__);
	    return true;
	}
	else {
	    Logg::loggerError('devisGnose::DevisSaveDocInGnose() ~ Erreur lors de la sauvegarde du devis '.$fileName.' dans l\'affaire '.$affaire,array($fileFrom,$fileName,$affaire,$message),__FILE__.'@'.__LINE__);
	    return "ERREUR LORS DE LA COPIE";
	}
    }

    /**
     * Méthode qui génère un export tableur des devis
     * @param array $devis Toutes les informations sur les devis
     * @param string $format Indique le format de sortie
     * @return string
     */
    public function  DevisExportTableurConverter($devis,$format = '') {
	loadPlugin(array('OOConverter'));
	$availableFormat = OOConverterAvailable('spreadsheet');
	if(!array_key_exists($format,$availableFormat)) $format = 'pdf';
	$fileFrom = $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'].$this->DevisExportTableur($devis);
	$formatExt = ($availableFormat[$format] != '') ? substr($availableFormat[$format],strpos($availableFormat[$format], '[')+2,-1) : $format;
	$fromFormat  = FileGetExtention($fileFrom);
	$fileTo = substr($fileFrom,0, (strlen($fromFormat)+1)*-1).'.'.$formatExt;
	if($format == 'ods') return $fileTo;
	$return = OOConverter($fileFrom,$fileTo,$format);
	if($return === true) return $fileTo;
	else			   return $return;
    }

    /**
     * Méthode qui génère le tableur d'export des devis
     * @param array $devis Toutes les informations sur les devis à exporter
     * @return string Le nom du fichier généré.
     */
    private function DevisExportTableur($devis) {
	loadPlugin(array('OOConverter'));
	$cannevasPath = $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['PropsecConf']['dir.cannevas.exportTableur'];
	$cannevasFile = $cannevasPath.$GLOBALS['ZunoDevis']['cannevas.exportTableur'];
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


	    $pos1 = strpos($xmlContent,'{id_dev}');
	    $debut = substr($xmlContent,0,$pos1);
	    $fin = substr($xmlContent,$pos1);
	    $from = strrpos($debut,'<table:table-row');
	    $to = (strlen($fin)-strpos($fin,'</table:table-row>')-18)*-1;
	    $exportDebutDocument = substr($xmlContent,0,$from);
	    $exportFinDocument = substr($xmlContent,$to);
	    $GLOBALS['TmpTemplate']['exportMiddleDocument'] = substr($xmlContent,$from,$to);
	    if (is_array($devis)) {
		foreach ($devis as $kk => $dev) {
		    $dev = str_replace($TrimFrom,$TrimTo,$dev);
		    $dev['modif_aff'] 	= ($dev['modif_aff'] == '') 		? '' : strftime("%d/%m/%y",strtotime($dev['modif_aff']));
		    $dev['detect_aff'] 	= ($dev['detect_aff'] == '') 		? '' : strftime("%d/%m/%y",strtotime($dev['detect_aff']));
		    $dev['echeance_aff'] 	= ($dev['echeance_aff'] == '') 	? '' : strftime("%d/%m/%y",strtotime($dev['echeance_aff']));
		    $dev['daterecord_dev']	= ($dev['daterecord_dev'] == '') 	? '' : strftime("%d/%m/%y",strtotime($dev['daterecord_dev']));
		    $dev['datemodif_dev']	= ($dev['datemodif_dev'] == '') 	? '' : strftime("%d/%m/%y",strtotime($dev['datemodif_dev']));
		    $dev['daterecord_cmd']	= ($dev['daterecord_cmd'] == '') 	? '' : strftime("%d/%m/%y",strtotime($dev['daterecord_cmd']));
		    $dev['datemodif_cmd']	= ($dev['datemodif_cmd'] == '') 	? '' : strftime("%d/%m/%y",strtotime($dev['datemodif_cmd']));
		    $dev['daterecord_fact'] = ($dev['daterecord_fact'] == '') 	? '' : strftime('%d/%m/%y',strtotime($dev['daterecord_fact']));
		    $dev['dateenvoi_fact']	= ($dev['dateenvoi_fact'] == '') 	? '' : strftime("%d/%m/%y",strtotime($dev['dateenvoi_fact']));
		    $dev['datereglement_fact'] = ($dev['datereglement_fact'] == '') ? '' : strftime("%d/%m/%y",strtotime($dev['datereglement_fact']));
		    $dev['budget_aff'] 	= ($dev['budget_aff'] == '') 		? '' : number_format($dev['budget_aff'],0,',',' ');
		    $dev['sommeHT_dev'] 	= ($dev['sommeHT_dev'] == '') 	? '' : number_format($dev['sommeHT_dev'],0,',',' ');
		    $dev['sommeHT_cmd'] 	= ($dev['sommeHT_cmd'] == '') 	? '' : number_format($dev['sommeHT_cmd'],0,',',' ');
		    $dev['sommeHT_fact'] 	= ($dev['sommeHT_fact'] == '') 	? '' : number_format($dev['sommeHT_fact'],0,',',' ');
		    foreach ($dev as $in => $out) {
			$input[] = "/\{".$in."\}/";
			$output[] = $out;
		    }
		    $outDevis .= ProcessTemplating('exportMiddleDocument',$input,$output);
		    unset($input);
		    unset($output);
		}
	    }
	    else Logg::loggerError('devisGnose::DevisExportTableur() ~ Impossible de trouver des devis à afficher ',count($devis),__FILE__.'@'.__LINE__);
	    $xmlContent = $exportDebutDocument.$outDevis.$exportFinDocument;
	    unset($outDevis);

	    File_Add2File($ExportTmpPath.'content.xml',$xmlContent,TRUE);
	    $ArchivePath = $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'];
	    $ODTName = 'DevisExport.ods';

	    @rm($ArchivePath.$ODTName);
	    $archive = new PclZip($ArchivePath.$ODTName);
	    $archive->add($ExportTmpPath,PCLZIP_OPT_REMOVE_PATH,$ExportTmpPath.'/');
	    $outputFile = $ODTName;
	    Logg::loggerInfo('devisGnose::DevisExportTableur() ~ Export de la liste des devis '.$ODTName,count($devis),__FILE__.'@'.__LINE__);
	    return $outputFile;
	}
	else Logg::loggerError('devisGnose::DevisExportTableur() ~ Impossible de trouver le cannevas '.$cannevasFile,count($devis),__FILE__.'@'.__LINE__);
    }
}
?>
