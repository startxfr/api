<?php

/**
 * Classe qui gère tous les liens entre les factures et les fichiers
 *
 * @author STARTX
 * @version 1.0
 */
class factureGnose {
    private $bddtmp;

    /**
     * Constructeur il charge la classe de connexion à la BDD
     */
    function __construct() {
	$this->bddtmp = new factureModel();
    }

    /**
     * Méthode qui génère les code barres.
     * @param string $id_fact L'id de la facture
     * @param string $fileTo Le path fichier à générer
     * @param string $size La taille du code barre
     * @return string Le path du fichier généré
     */
    public function FactureGenerateBarreCode($id_fact,$fileTo = "",$size = "150x40") {
	if($id_fact != "") {
	    if($fileTo == "")
		$fileTo = $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'].'barrecode.'.$id_fact.'.png';
	    loadPlugin(array('barcode/barcode','barcode/c128bobject'));

	    $obj = new C128BObject(150,40,68,$id_fact);
	    $obj->DrawObject(1);
	    $obj->FlushObject($fileTo);
	    //$obj->DestroyObject();
	    unset($obj);
	    return $fileTo;
	}
    }

    /**
     * Méthode qui génère un fichier facture
     * @param string $id_fact L'id de la facture à générer
     * @param string $outputExt Le format de sortie
     * @param string $cannevas Le cannevas à utiliser
     * @return string Le nom du fichier généré
     */
    public function FactureGenerateDocument($id_fact,$outputExt = "",$cannevas = "") {
	if($outputExt == '')
	    $outputExt = 'pdf';
	loadPlugin(array('OOConverter','docGenerator'));
	if (array_key_exists($cannevas,$GLOBALS['ZunoFacture']))
	    $cannevasFile = $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoFacture']['dir.cannevas'].$GLOBALS['ZunoFacture'][$cannevas];
	else $cannevasFile = $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoFacture']['dir.cannevas'].$GLOBALS['ZunoFacture']['cannevas.defaut'];

	$entreprise = $this->bddtmp->getDataFromID($id_fact);
	$entreprise = $entreprise[1][0];
	$entreprise['tauxTVA_ent'] = $entreprise['tauxTVA_fact'];
	if(strlen($id_fact) == 1)
	    $id = "00".$id_fact;
	elseif(strlen($id_fact) == 2)
	    $id = "0".$id_fact;
	else
	    $id = $id_fact;
	$entreprise['id_affiche'] = substr($entreprise['daterecord_fact'], 2, 2).substr(strtoupper($entreprise['type_fact']),0,1).$id;
	$entreprise['datemodif_fact'] = DateUniv2Human($entreprise['datemodif_fact'], 'fulldate');
	$entreprise['date_fact'] = $entreprise['datemodif_fact'];
	$entreprise['daterecord_fact'] = DateUniv2Human($entreprise['daterecord_fact'], 'simpleLong');
	$entreprise['dateenvoi_fact'] = ($entreprise['dateenvoi_fact'] == null) ? " ":DateUniv2Human($entreprise['dateenvoi_fact'], 'fulldate');
	$entreprise['datereglement_fact'] = ($entreprise['datereglement_fact'] == null) ? " ":DateUniv2Human($entreprise['datereglement_fact'], 'simpleLong');
	if(count($entreprise) > 0) {
	    if($entreprise['id_pays'] == "1") $entreprise['nom_pays'] = "";


	    $entreprise['nom_commercial'] = $entreprise['civ']." ".$entreprise['prenom']." ".$entreprise['nom'];
	    $entreprise['mail_commercial'] = $entreprise['mail'];
	    $entreprise['id_fact'] = $entreprise['id_fact'];
	    $produits = $this->bddtmp->getProduitsFromID($id_fact);
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
			$produit['quantite'] = formatCurencyDisplay($produit['quantite'],0,'');
		    else  $produit['quantite'] = formatCurencyDisplay($produit['quantite'],2,'');
		    if($produit['remise'] > 0) {
			if(substr($produit['remise'],-3) == '.00')
			    $produit['remise'] = formatCurencyDisplay($produit['remise'],0,'%');
			else  $produit['remise'] = formatCurencyDisplay($produit['remise'],2,'%');
		    }
		    else  $produit['remise'] = '';
		    $produit['prix'] = formatCurencyDisplay($produit["prix"],2,'');
		    $produit['total']= formatCurencyDisplay(round($totalRow,2),2,'');
		    $produits[$key] = $produit;
		    $entreprise = array_merge($produit,$entreprise);
		    $total = $total+$totalRow;
		}
	    }
	    else Logg::loggerNotice('factureGnose::FactureGenerateDocument() ~ Erreur lors de l\'accès aux lignes produits de la facture '.$id_fact,serialize($entreprise),__FILE__.'@'.__LINE__);

	    $entreprise['totalHT'] = formatCurencyDisplay(round($total,2),2,'');
	    $entreprise['totalTVA'] = formatCurencyDisplay(round(($total*$entreprise['tauxTVA_ent']/100),2),2,'');
	    $entreprise['totalTTC'] = formatCurencyDisplay(round($total+($total*$entreprise['tauxTVA_ent']/100),2),2,'');
	}
	else Logg::loggerNotice('factureGnose::FactureGenerateDocument() ~ Erreur lors de l\'accès aux données de la facture '.$id_fact,serialize($entreprise),__FILE__.'@'.__LINE__);


	if(is_file($cannevasFile) and
		count($entreprise) > 0 and
		FileGetExtention($cannevasFile) == "odt") {
	    $prefixDoc  = 'Facture';
	    $fileSuffix = $prefixDoc.'.'.$id_fact.'.';
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
			$this->FactureGenerateBarreCode($id_fact,$TmpPath.$BarreCodePict);
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
			    if($cutLigneProd[0]) $OutLigne .= docGeneratorFragmentTemplating($produit,$DevisLigneProd,'FactureLigneProd');
			}

		    $entreprise   = docGeneratorCleanArrayBeforeTemplating($entreprise);
		    docGeneratorAddZunoConfInfoBeforeTemplating($entreprise);
		    $DevisDebutProd = docGeneratorFragmentTemplating($entreprise,$DevisDebutProd,'FactureDebutProd');
		    $DevisFinProd = docGeneratorFragmentTemplating($entreprise,$DevisFinProd,'FactureFinProd');
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
			if(is_string($retour)) {
			    Logg::loggerError('factureGnose::FactureGenerateDocument() ~ Erreur lors de la conversion en '.$outputExt.' du fichier '.$ODTName,array($ArchivePath.$ODTName,$ArchivePath.$PDFName,$outputExt),__FILE__.'@'.__LINE__);
			    return false;
			}
		    }
		    Logg::loggerInfo('factureGnose::FactureGenerateDocument() ~ Génération du document de la facture '.$id_fact.' (fichier '.$PDFName.')',array($ArchivePath.$ODTName,$ArchivePath.$PDFName,$outputExt),__FILE__.'@'.__LINE__);
		    return $PDFName;
		}
		else Logg::loggerError('factureGnose::FactureGenerateDocument() ~ Erreur lors de la génération du fichier '.$ODTName.' aucune Ligne produit disponible dans le cannevas',$cannevasFile,__FILE__.'@'.__LINE__);
	    }
	    else Logg::loggerError('factureGnose::FactureGenerateDocument() ~ Erreur lors de la génération du fichier '.$ODTName.' impossible d\'extraire le cannevas',array($cannevasFile,$extract),__FILE__.'@'.__LINE__);
	}
	else Logg::loggerError('factureGnose::FactureGenerateDocument() ~ Erreur lors de la génération du fichier '.$ODTName.' impossible de trouver le cannevas ou le tableau des informations sur la facture'.$cannevasFile,array($cannevasFile,$entreprise),__FILE__.'@'.__LINE__);
    }

    /**
     * Méthode qui se charge d'effectuer la sauvegarde d'un fichié généré dans la GNOSE
     * @param string $fileFrom Le nom du fichier à sauver
     * @param string $facture L'id de la facture
     * @param string $message Le message pour subversion
     * @return bool Indique si tout s'est bien déroulé
     */
    public function FactureSaveDocInGnose($fileFrom,$facture,$message = "") {
	$bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$bddtmp->makeRequeteFree("SELECT * FROM facture
						  LEFT JOIN affaire ON id_aff = SUBSTRING(commande_fact,1,6)
						  WHERE id_fact = '".$facture."'");
	$res = $bddtmp->process();
	$factData = $res[0];
	$prefixDoc = ($factData['type_fact'] == 'Avoir') ? 'Avoir' : 'Facture';
	$typeDocLa = ($factData['type_fact'] == 'Avoir') ? 'l\'avoir' : 'la facture';

	if ($message == "")
	    $message = "Changement de $typeDocLa ".$facture." par ".$_SESSION['user']['id'];
	$message = escapeshellcmd($message);

	$PathToAff = $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoAffaire']['dir.affaire'].$factData['dir_aff'];
	$PathTo  = $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoFacture']['dir.facture'];
	$PathFrom= $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'];
	$FileExist = file_exists($PathTo.$fileFrom);
	$FileExistAff = file_exists($PathToAff.$fileFrom);

	$return = "modification de $typeDocLa ".$fileFrom;

	// Si le fichier existe, on le remplace
	if($FileExist) {
	    if(!copy($PathFrom.$fileFrom,$PathTo.$fileFrom))
		Logg::loggerError('factureGnose::FactureSaveDocInGnose() ~ Impossible de copier le fichier '.$PathTo,'',__FILE__.'@'.__LINE__);
	    else Logg::loggerInfo('factureGnose::FactureSaveDocInGnose() ~ Sauvegarde du document de la facture '.$facture,$factData,__FILE__.'@'.__LINE__);
	    chmod($PathTo.$fileFrom,0770);
	    $fileCI.= $PathTo.$fileFrom.' ';
	    // S'il y a une affaire
	    if($factData['dir_aff'] != '') {
		// On copie le fichier dans l'affaire
		if(!copy($PathFrom.$fileFrom,$PathToAff.$fileFrom))
		    Logg::loggerError('factureGnose::FactureSaveDocInGnose() ~ impossible de copier le fichier '.$PathFrom.$fileFrom.' vers '.$PathToAff,'',__FILE__.'@'.__LINE__);
		else Logg::loggerInfo('factureGnose::FactureSaveDocInGnose() ~ Sauvegarde du document de la facture '.$facture.' dans l\'affaire '.$factData['dir_aff'],$factData,__FILE__.'@'.__LINE__);
		// Si le fichier n'existait pas avant, on l'ajoute
		if(!$FileExistAff) {
		    $CmdBase = 'svn add ';
		    $CmdBase.= '--config-dir '.$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir'].' ';
		    $CmdBase.= $PathToAff.$fileFrom.' ';
		    if ($GLOBALS['LOG']['DisplayDebug']) $GLOBALS['LogSVNProcess'][] = $CmdBase;
		    shell_exec($CmdBase);
		}
		// et on traite les perm
		chmod($PathToAff.$fileFrom,0770);
		$fileCI .= ' '.$PathToAff.$fileFrom.' ';
	    }
	}
	else {
	    // Si le fichier n'existe pas, mais une version avec un autre nom
	    //  existe, on fait un svn mv avant la copie et on met  jour la
	    // base de données.
	    if($factData['file_fact'] != '' and
		    $factData['file_fact'] != $fileFrom and
		    file_exists($PathTo.$factData['file_fact'])) {
		$CmdBase = 'svn mv ';
		$CmdBase.= '--config-dir '.$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir'].' ';
		$CmdBase.= $PathTo.$factData['file_fact'].' ';
		$CmdBase.= $PathTo.$fileFrom.' ';
		if ($GLOBALS['LOG']['DisplayDebug']) $GLOBALS['LogSVNProcess'][] = $CmdBase;
		shell_exec($CmdBase);
		if(!copy($PathFrom.$fileFrom,$PathTo.$fileFrom))
		    Logg::loggerError('factureGnose::FactureSaveDocInGnose() ~ impossible de copier le fichier '.$PathFrom.$fileFrom.' vers '.$pathTo,'',__FILE__.'@'.__LINE__);
		else Logg::loggerInfo('factureGnose::FactureSaveDocInGnose() ~ Sauvegarde du document de la facture '.$facture.' après un changement de nom de fichier',array($factData,$fileFrom),__FILE__.'@'.__LINE__);
		$bddtmp->makeRequeteUpdate('facture','id_fact',$facture,array('file_fact'=>$fileFrom));
		$bddtmp->process();
		$return = 'Changement de '.$typeDocLa.' '.$facture.' en changement de son nom de '.$factData['file_fact'].' vers '.$fileFrom;
		chmod($PathTo.$fileFrom,0770);
		$fileCI.= $PathTo.$factData['file_fact'].' '.$PathTo.$fileFrom.' ';
		// S'il y a une affaire
		if($factData['dir_aff'] != '') {
		    // On copie le fichier dans l'affaire
		    if(!copy($PathFrom.$fileFrom,$PathToAff.$fileFrom))
			Logg::loggerError('factureGnose::FactureSaveDocInGnose() ~ impossible de copier le fichier '.$PathFrom.$fileFrom.' vers '.$PathToAff,'',__FILE__.'@'.__LINE__);
		    else Logg::loggerInfo('factureGnose::FactureSaveDocInGnose() ~ Sauvegarde du document de la facture '.$facture.' dans l\'affaire '.$factData['dir_aff'],$factData,__FILE__.'@'.__LINE__);
		    // Si le fichier n'existait pas avant, on l'ajoute
		    if(!$FileExistAff) {
			$CmdBase = 'svn add ';
			$CmdBase.= '--config-dir '.$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir'].' ';
			$CmdBase.= $PathToAff.$fileFrom.' ';
			if ($GLOBALS['LOG']['DisplayDebug']) $GLOBALS['LogSVNProcess'][] = $CmdBase;
			shell_exec($CmdBase);
		    }
		    // et on traite les perm
		    chmod($PathToAff.$fileFrom,0770);
		    $fileCI .= ' '.$PathToAff.$fileFrom.' ';
		}
	    }
	    // sinon le fichier n'as jamais existé
	    else {
		if(!copy($PathFrom.$fileFrom,$PathTo.$fileFrom))
		    Logg::loggerError('factureGnose::FactureSaveDocInGnose()::Cant_copy_file_to:'.$PathTo);
		else Logg::loggerInfo('factureGnose::FactureSaveDocInGnose() ~ Sauvegarde du document de la facture '.$facture.' pour la première fois',array($factData,$fileFrom),__FILE__.'@'.__LINE__);
		$CmdBase = 'svn add ';
		$CmdBase.= '--config-dir '.$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir'].' ';
		$CmdBase.= $PathTo.$fileFrom.' ';
		if ($GLOBALS['LOG']['DisplayDebug']) $GLOBALS['LogSVNProcess'][] = $CmdBase;
		shell_exec($CmdBase);
		$return = "ajout de la facture ".$fileFrom;
		chmod($PathTo.$fileFrom,0770);
		$fileCI.= $PathTo.$fileFrom.' ';
		$bddtmp->makeRequeteUpdate('facture','id_fact',$facture,array('file_fact'=>$fileFrom));
		$bddtmp->process();
		// S'il y a une affaire
		if($factData['dir_aff'] != '') {
		    // On copie le fichier dans l'affaire
		    if(!copy($PathFrom.$fileFrom,$PathToAff.$fileFrom))
			Logg::loggerError('factureGnose::FactureSaveDocInGnose() ~ impossible de copier le fichier '.$PathFrom.$fileFrom.' vers '.$PathToAff,'',__FILE__.'@'.__LINE__);
		    else Logg::loggerInfo('factureGnose::FactureSaveDocInGnose() ~ Sauvegarde du document de la facture '.$facture.' dans l\'affaire '.$factData['dir_aff'],$factData,__FILE__.'@'.__LINE__);
		    // on ajoute le fichier a l'entrepot
		    $CmdBase = 'svn add ';
		    $CmdBase.= '--config-dir '.$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir'].' ';
		    $CmdBase.= $PathToAff.$fileFrom.' ';
		    if ($GLOBALS['LOG']['DisplayDebug']) $GLOBALS['LogSVNProcess'][] = $CmdBase;
		    shell_exec($CmdBase);
		    // et on traite les perm
		    chmod($PathToAff.$fileFrom,0770);
		    $fileCI .= ' '.$PathToAff.$fileFrom.' ';
		}
	    }
	}

	if($fileCI != '') {
	    $CmdBase = 'export EDITOR="vi"; ';
	    $CmdBase.= 'export LC_CTYPE="fr_FR.UTF-8"; ';
	    $CmdBase.= 'export LANG="fr_FR.UTF-8"; ';
	    $CmdBase.= 'svn ci ';
	    $CmdBase.= '--config-dir '.$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir'].' ';
	    $CmdBase.= '--username '.$_SESSION["user"]["id"].' ';
	    $CmdBase.= '-m "'.$message.'" ';
	    $CmdBase.= $fileCI.' ';
	    if ($GLOBALS['LOG']['DisplayDebug'])
		$GLOBALS['LogSVNProcess'][] = $CmdBase;
	    shell_exec($CmdBase);
	    return $return;
	}
	else return "ERREUR PAS DE FACTURE A ENREGISTRER";
    }

    /**
     * Méthode qui génère un export tableur des factures
     * @param array $req Toutes les informations sur les factures
     * @param string $format Indique le format de sortie
     * @return string
     */
    function  FactureExportTableurConverter($req,$format = '') {
	loadPlugin(array('OOConverter'));
	$availableFormat = OOConverterAvailable('spreadsheet');
	if(!array_key_exists($format,$availableFormat)) $format = 'pdf';
	$fileFrom = $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'].factureGnose::FactureExportTableur($req);
	$formatExt = ($availableFormat[$format] != '') ? substr($availableFormat[$format],strpos($availableFormat[$format], '[')+2,-1) : $format;
	$fromFormat  = FileGetExtention($fileFrom);
	$fileTo = substr($fileFrom,0, (strlen($fromFormat)+1)*-1).'.'.$formatExt;
	if($format == 'ods') return $fileTo;
	$return = OOConverter($fileFrom,$fileTo,$format);
	if($return === true) return $fileTo;
	else			   return $return;
    }

    /**
     * Méthode qui génère le tableur d'export des factures
     * @param array $facture Toutes les informations sur les factures à exporter
     * @return string Le nom du fichier généré.
     */
    function FactureExportTableur($facture) {
	loadPlugin(array('OOConverter'));
	$cannevasPath = $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['PropsecConf']['dir.cannevas.exportTableur'];
	$cannevasFile = $cannevasPath.$GLOBALS['ZunoFacture']['cannevas.exportTableur'];

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

	    $pos1 = strpos($xmlContent,'{id_fact}');
	    $debut = substr($xmlContent,0,$pos1);
	    $fin = substr($xmlContent,$pos1);
	    $from = strrpos($debut,'<table:table-row');
	    $to = (strlen($fin)-strpos($fin,'</table:table-row>')-18)*-1;
	    $exportDebutDocument = substr($xmlContent,0,$from);
	    $exportFinDocument = substr($xmlContent,$to);
	    $GLOBALS['TmpTemplate']['exportMiddleDocument'] = substr($xmlContent,$from,$to);
	    if (count($facture) > 0) {
		foreach ($facture as $kk => $fact) {
		    $fact = str_replace($TrimFrom,$TrimTo,$fact);
		    $fact['modif_aff'] 	= ($fact['modif_aff'] == '') 		? '' : strftime("%d/%m/%y",strtotime($fact['modif_aff']));
		    $fact['detect_aff'] 	= ($fact['detect_aff'] == '') 		? '' : strftime("%d/%m/%y",strtotime($fact['detect_aff']));
		    $fact['echeance_aff'] 	= ($fact['echeance_aff'] == '') 	? '' : strftime("%d/%m/%y",strtotime($fact['echeance_aff']));
		    $fact['daterecord_dev']	= ($fact['daterecord_dev'] == '') 	? '' : strftime("%d/%m/%y",strtotime($fact['daterecord_dev']));
		    $fact['datemodif_dev']	= ($fact['datemodif_dev'] == '') 	? '' : strftime("%d/%m/%y",strtotime($fact['datemodif_dev']));
		    $fact['daterecord_cmd']	= ($fact['daterecord_cmd'] == '') 	? '' : strftime("%d/%m/%y",strtotime($fact['daterecord_cmd']));
		    $fact['datemodif_cmd']	= ($fact['datemodif_cmd'] == '') 	? '' : strftime("%d/%m/%y",strtotime($fact['datemodif_cmd']));
		    $fact['daterecord_fact'] = ($fact['daterecord_fact'] == '') 	? '' : strftime('%d/%m/%y',strtotime($fact['daterecord_fact']));
		    $fact['dateenvoi_fact']	= ($fact['dateenvoi_fact'] == '') 	? '' : strftime("%d/%m/%y",strtotime($fact['dateenvoi_fact']));
		    $fact['datereglement_fact'] = ($fact['datereglement_fact'] == '') ? '' : strftime("%d/%m/%y",strtotime($fact['datereglement_fact']));
		    $fact['budget_aff'] 	= ($fact['budget_aff'] == '') 		? '' : formatCurencyDisplay($fact['budget_aff'],0,'');
		    $fact['sommeHT_dev'] 	= ($fact['sommeHT_dev'] == '') 	? '' : formatCurencyDisplay($fact['sommeHT_dev'],2,'');
		    $fact['sommeHT_cmd'] 	= ($fact['sommeHT_cmd'] == '') 	? '' : formatCurencyDisplay($fact['sommeHT_cmd'],2,'');
		    $fact['sommeHT_fact'] 	= ($fact['sommeHT_fact'] == '') 	? '' : formatCurencyDisplay($fact['sommeHT_fact'],2,'');
		    foreach ($fact as $in => $out) {
			$input[] = "/\{".$in."\}/";
			$output[] = $out;
		    }
		    $outFacture .= ProcessTemplating('exportMiddleDocument',$input,$output);
		    unset($input);
		    unset($output);
		}
	    }
	    else Logg::loggerError('factureGnose::FactureExportTableur() ~ Impossible de trouver des factures à afficher ',count($facture),__FILE__.'@'.__LINE__);
	    $xmlContent = $exportDebutDocument.$outFacture.$exportFinDocument;
	    unset($outFacture);

	    File_Add2File($ExportTmpPath.'content.xml',$xmlContent,TRUE);
	    $ArchivePath = $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'];
	    $ODTName = 'FactureExport.ods';

	    @rm($ArchivePath.$ODTName);
	    $archive = new PclZip($ArchivePath.$ODTName);
	    $archive->add($ExportTmpPath,PCLZIP_OPT_REMOVE_PATH,$ExportTmpPath.'/');
	    $outputFile = $ODTName;
	    Logg::loggerInfo('factureGnose::FactureExportTableur() ~ Export de la liste des factures '.$ODTName,count($facture),__FILE__.'@'.__LINE__);
	    return $outputFile;
	}
	else Logg::loggerError('factureGnose::FactureExportTableur() ~ Impossible de trouver le cannevas '.$cannevasFile,count($facture),__FILE__.'@'.__LINE__);
    }
}
?>
