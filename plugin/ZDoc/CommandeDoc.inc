<?php

/**
 * Classe qui gère tous les liens entre les fichiers et les commandes
 *
 * @version 1.0
 */
class commandeGnose {
    static function generateCommandeBarreCode($id_cmd,$fileTo = "",$size = "190x40", $id_fourn ='', $type="BCC") {
	if($id_cmd != "") {
	    switch ($type) {
		case "BCC" :
		    if($fileTo == "")
			$fileTo = $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'].'barrecode.'.$id_cmd.'C.png';
		    loadPlugin(array('barcode/barcode','barcode/c128bobject'));
		    $obj = new C128BObject(190,40,68,$id_cmd."C");
		    break;
		case "BL" :
		    if($fileTo == "")
			$fileTo = $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'].'barrecode.'.substr($id_cmd,0,-1).'L.png';
		    loadPlugin(array('barcode/barcode','barcode/c128bobject'));
		    $obj = new C128BObject(170,40,68,substr($id_cmd,0,-1)."L");
		    break;
		case "BCF" :
		    if($fileTo == "")
			$fileTo = $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'].'barrecode.'.$id_cmd."F-".$id_fourn.'.png';
		    loadPlugin(array('barcode/barcode','barcode/c128bobject'));
		    $obj = new C128BObject(240,40,68,$id_cmd."F-".$id_fourn);
		    break;
	    }

	    $obj->DrawObject(1);
	    $obj->FlushObject($fileTo);
	    unset($obj);
	    return $fileTo;
	}
    }
    static function CommandeGenerateBDC($datas,$outputExt = 'pdf', $type = 'BC') {
	loadPlugin(array('OOConverter','docGenerator'));
	if($type == 'BC')
	    $cannevasFile =  $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoCommande']['dir.cannevas'].$GLOBALS['ZunoCommande']['cannevas.BDCC'];
	elseif($type == 'BDL')
	    $cannevasFile = $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoCommande']['dir.cannevas'].$GLOBALS['ZunoCommande']['cannevas.BDL'];
	elseif($type == 'RI')
	    $cannevasFile = $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoCommande']['dir.cannevas'].$GLOBALS['ZunoCommande']['cannevas.RI'];
	$id_cmd = $datas['data']['id_cmd'];

	if($datas['data']['entreprise_cmd'] != '') {
	    $entreprise = $datas['data'];
	    if($datas['data']['paysdelivery_cmd'] == "1") $entreprise['nom_pays'] = "";
	    else $entreprise['nom_pays'] = $datas['pays'][$datas['data']['paydelivery_cmd']];

	    $entreprise['nom_commercial'] = $datas['user']['nom'];
	    $entreprise['mail_commercial'] = $datas['user']['mail'];
	    $entreprise['id_aff'] = substr($datas['data']['id_cmd'],0,6);
	    $entreprise['id_dev'] = substr($datas['data']['id_cmd'],0,9);
	    $entreprise['date_cmd'] = DateUniv2Human($entreprise['datemodif_cmd'], 'fulldate');
	    $produits = array();
	    if (array_key_exists('produit', $datas) and count($datas['produit']) > 0) {
		foreach ($datas['produit'] as $key => $produit) {
		    $quantite 	= $produit['quantite'];
		    $prix       = $produit["prix"];
		    $remise	= $produit['remise'];
		    $totalRow	= ($produit['quantite']*$produit['prix'])-((($produit['quantite']*$produit['prix'])*$produit['remise'])/100);
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
		    $produit['prix'] = number_format($prix,2,',',' ');
		    $produit['total']= number_format(round($totalRow,2),2,',',' ');
		    $produits[$key] = $produit;
		    $entreprise = array_merge($produit,$entreprise);
		    $total += $totalRow;
		}
	    }
	    else Logg::loggerNotice('commandeGnose::CommandeGenerateBDC() ~ Impossible de trouver les produits pour la commande '.$id_cmd,$id_cmd,__FILE__.'@'.__LINE__);
	    if($type == 'BC' or $type == 'BDL') {
		$entreprise['totalHT'] = number_format(round($total,2),2,',',' ');
		$entreprise['totalTVA'] = number_format(round(($total*$entreprise['tauxTVA_ent']/100),2),2,',',' ');
		$entreprise['totalTTC'] = number_format(round($total+($total*$entreprise['tauxTVA_ent']/100),2),2,',',' ');
	    }
	}
	else Logg::loggerError('commandeGnose::CommandeGenerateBDC() ~ Impossible de trouver les informations sur la commande '.$id_cmd,$id_cmd,__FILE__.'@'.__LINE__);
	if(is_file($cannevasFile) and
		count($entreprise) > 0 and
		FileGetExtention($cannevasFile) == "odt") {
	    $prefixDoc  = $type;
	    if($type == 'BC')
		$fileSuffix = $prefixDoc.'.'.substr($id_cmd,0,9).'.';
	    elseif($type == 'BDL')
		$fileSuffix = $prefixDoc.'.'.substr($id_cmd,0,9).'.';
	    elseif($type == 'RI') {
		$prefixDoc  = 'RapportIntervention';
		$fileSuffix = $prefixDoc.'.'.substr($id_cmd,0,9).'.';
	    }
	    $TmpPath = $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'].$prefixDoc.substr(md5(rand(1000,9999)+rand(1000,9999)),0,8).'/';
	    $extract = docGeneratorOpenWorkingDoc($cannevasFile,$prefixDoc);
	    if ($extract[0]) {
		$TmpPath = $extract[1];
		$xml = file_get_contents($TmpPath.'content.xml');
		if (strpos($xml,"{LIGNE_PROD}") or $type == 'RI') {
		    $cutBarreCode = docGeneratorCutXml($xml,'BarreCode','draw:frame');
		    if($type == 'RI') {
			if($cutBarreCode[0]) {
			    $finInPic = substr($cutBarreCode[1]['middle'],strpos($cutBarreCode[1]['middle'],'xlink:href="')+12);
			    $BarreCodePict = substr($finInPic,0,strpos($finInPic,'"'));
			    commandeGnose::generateCommandeBarreCode($id_cmd,$TmpPath.$BarreCodePict, '170x40', '', 'BL');
			}
		    }
		    else {
			if($cutBarreCode[0]) {
			    $finInPic = substr($cutBarreCode[1]['middle'],strpos($cutBarreCode[1]['middle'],'xlink:href="')+12);
			    $BarreCodePict = substr($finInPic,0,strpos($finInPic,'"'));
			    commandeGnose::generateCommandeBarreCode($id_cmd,$TmpPath.$BarreCodePict);
			}
			$cutLigneProd = docGeneratorCutXml($xml,'{LIGNE_PROD}','table:table-row');
			if($cutLigneProd[0]) {
			    $CommandeDebutProd = $cutLigneProd[1]['top'];
			    $CommandeLigneProd = $cutLigneProd[1]['middle'];
			    $CommandeFinProd   = $cutLigneProd[1]['bottom'];
			}
			else  $CommandeDebutProd = $cutLigneProd[1];

			if (count($produits) > 0)
			    foreach ($produits as $key => $produit) {
				$produit['LIGNE_PROD']	= '';
				$produit   = docGeneratorCleanArrayBeforeTemplating($produit);
				if($cutLigneProd[0]) $OutLigne .= docGeneratorFragmentTemplating($produit,$CommandeLigneProd,'BDLLigneProd');
			    }
		    }
		    $entreprise   = docGeneratorCleanArrayBeforeTemplating($entreprise);
		    docGeneratorAddZunoConfInfoBeforeTemplating($entreprise);
		    if($type != 'RI') {
			$CommandeDebutProd = docGeneratorFragmentTemplating($entreprise,$CommandeDebutProd,'BDLDebutProd');
			$CommandeFinProd = docGeneratorFragmentTemplating($entreprise,$CommandeFinProd,'BDLFinProd');
			$NewXMLcontent = $CommandeDebutProd.$OutLigne.$CommandeFinProd;
		    }
		    else {
			$NewXMLcontent = docGeneratorFragmentTemplating($entreprise,$xml,'RIFull');
		    }
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
		    else Logg::loggerInfo('commandeGnose::CommandeGenerateBDC() ~ Génération du document bon de commande client '.$id_cmd.' (fichier '.$PDFName.')',$cannevasFile,__FILE__.'@'.__LINE__);
		    return $PDFName;
		}
		else Logg::loggerError('commandeGnose::CommandeGenerateBDC() ~ Impossible de trouver la sequence {LIGNE_PROD} dans le cannevas '.$cannevasFile,$cannevasFile,__FILE__.'@'.__LINE__);
	    }
	    else Logg::loggerError('commandeGnose::CommandeGenerateBDC() ~ Impossible d\'ouvrir le cannevas '.$cannevasFile,$extract,__FILE__.'@'.__LINE__);
	}
	else Logg::loggerError('commandeGnose::CommandeGenerateBDC() ~ Impossible de trouver le cannevas '.$cannevasFile.' ou les informations sur la commande '.$id_cmd,$entreprise,__FILE__.'@'.__LINE__);
    }


    static function CommandeGenerateBDCF($datas,$outputExt = 'pdf') {
	loadPlugin(array('OOConverter','docGenerator'));

	$id_cmd = $datas['data']['id_cmd'];
	$entreprise = $datas['data'];

	$fournisseur = $datas['fournisseur'];
	// on ajoute un F a la fin des data concernant le fournisseur
	foreach ($fournisseur as $key => $fourn)
	    $FTemp[$key."F"] = $fourn;
	$fournisseur = $FTemp;
	if ($fournisseur['BDCCannevas_fournF'] != '')
	    $cannevasFile = $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoCommande']['dir.cannevas'].$fournisseur['BDCCannevas_fournF'];
	if (!is_file($cannevasFile))
	    $cannevasFile = $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoCommande']['dir.cannevas'].$GLOBALS['ZunoCommande']['cannevas.BDCF'];

	if(count($entreprise) > 0 and count($fournisseur) > 0) {
	    if($entreprise['paysdelivery_cmd'] == "1") $entreprise['nom_paysF'] = "";
	    else $entreprise['nom_paysF'] = $datas['pays'][$datas['data']['paydelivery_cmd']];

	    $entreprise['nom_commercial'] = $datas['user']['nom'];
	    $entreprise['mail_commercial'] = $datas['user']['mail'];
	    $entreprise['id_aff'] = substr($id_cmd,0,6);
	    $entreprise['id_dev'] = substr($id_cmd,0,9);
	    $entreprise['date_cmd'] = DateUniv2Human($entreprise['datemodif_cmd'], 'fulldate');

	    $produits = $datas['produit'];
	    $total = 0;
	    if (count($produits) > 0) {
		foreach ($produits as $key => $produit) {
		    $quantite 	= $produit['quantite_cmd'];
		    $prixfourn  = ($produit["prixF"] == 0 || $produit["prixF"] == null) ? $produit["prix"] : $produit["prixF"];
		    $remise	= $produit['remiseF'];
		    $totalRow	= $quantite*$prixfourn*(1-$remise/100);
		    $produit['RefProd'] 	= $produit['id_produit'];
		    $produit['Description'] = $produit['desc'];
		    $produit['quantite'] = $quantite;
		    if(substr($produit['quantite'],-3) == '.00')
			$produit['quantite'] = substr($produit['quantite'],0,-3);
		    else  $produit['quantite'] = number_format($produit['quantite'],2,',',' ');
		    if($produit['remiseF'] > 0) {
			if(substr($produit['remiseF'],-3) == '.00')
			    $produit['remiseF'] = substr($produit['remiseF'],0,-3).'%';
			else  $produit['remiseF'] = number_format($produit['remiseF'],2,',',' ').'%';
		    }
		    else  $produit['remiseF'] = '';
		    $produit['prix'] = number_format($prixfourn,2,',',' ');
		    $produit['total']= number_format(round($totalRow,2),2,',',' ');
		    $produits[$key] = $produit;
		    $entreprise = array_merge($produit,$entreprise);
		    $total += $totalRow;
		}
	    }
	    else Logg::loggerNotice('commandeGnose::CommandeGenerateBDCF() ~ Impossible de trouver les produits pour la commande '.$id_cmd,$id_cmd,__FILE__.'@'.__LINE__);
	    $entreprise['totalHT'] = number_format(round($total,2),2,',',' ');
	    $entreprise['totalTVA'] = number_format(round(($total*$fournisseur['tauxTVA_entF']/100),2),2,',',' ');
	    $entreprise['totalTTC'] = number_format(round($total+($total*$fournisseur['tauxTVA_entF']/100),2),2,',',' ');
	}
	else Logg::loggerError('commandeGnose::CommandeGenerateBDCF() ~ Impossible de trouver les informations sur la commande '.$id_cmd,$id_cmd,__FILE__.'@'.__LINE__);

	if(is_file($cannevasFile) and
		count($entreprise) > 0 and
		FileGetExtention($cannevasFile) == "odt") {
	    $prefixDoc  = 'BCF';
	    $fileSuffix = $prefixDoc.'.'.substr($id_cmd,0,9).'-'.$datas['fournisseur']['id_fourn'].'.';
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
			commandeGnose::generateCommandeBarreCode($id_cmd,$TmpPath.$BarreCodePict);
		    }

		    $cutLigneProd = docGeneratorCutXml($xml,'{LIGNE_PROD}','table:table-row');
		    if($cutLigneProd[0]) {
			$CommandeDebutProd = $cutLigneProd[1]['top'];
			$CommandeLigneProd = $cutLigneProd[1]['middle'];
			$CommandeFinProd   = $cutLigneProd[1]['bottom'];
		    }
		    else  $CommandeDebutProd = $cutLigneProd[1];

		    if (count($produits) > 0)
			foreach ($produits as $key => $produit) {
			    $produit['LIGNE_PROD']	= '';
			    $produit   = docGeneratorCleanArrayBeforeTemplating($produit);
			    if($cutLigneProd[0]) $OutLigne .= docGeneratorFragmentTemplating($produit,$CommandeLigneProd,'CommandeLigneProd');
			}

		    $entreprise   = docGeneratorCleanArrayBeforeTemplating($entreprise);
		    $entreprise   = array_merge($entreprise,$fournisseur);
		    docGeneratorAddZunoConfInfoBeforeTemplating($entreprise);
		    $CommandeDebutProd = docGeneratorFragmentTemplating($entreprise,$CommandeDebutProd,'CommandeDebutProd');
		    $CommandeFinProd = docGeneratorFragmentTemplating($entreprise,$CommandeFinProd,'CommandeFinProd');
		    $NewXMLcontent = $CommandeDebutProd.$OutLigne.$CommandeFinProd;
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
		    else Logg::loggerInfo('commandeGnose::CommandeGenerateBDCF() ~ Génération du document bon de commande fournisseur '.$id_cmd.' (fichier '.$PDFName.')',$cannevasFile,__FILE__.'@'.__LINE__);
		    return $PDFName;
		}
		else Logg::loggerError('commandeGnose::CommandeGenerateBDCF() ~ Impossible de trouver la sequence {LIGNE_PROD} dans le cannevas '.$cannevasFile,$cannevasFile,__FILE__.'@'.__LINE__);
	    }
	    else Logg::loggerError('commandeGnose::CommandeGenerateBDCF() ~ Impossible d\'ouvrir le cannevas '.$cannevasFile,$extract,__FILE__.'@'.__LINE__);
	}
	else Logg::loggerError('commandeGnose::CommandeGenerateBDCF() ~ Impossible de trouver le cannevas '.$cannevasFile.' ou les informations sur la commande '.$id_cmd,$entreprise,__FILE__.'@'.__LINE__);
    }

    static function CommandeSaveDocInGnose($fileFrom,$commande,$message = "") {
	if ($message == "") $message = "Changement de la commande pour l'affaire ".$commande." par ".$_SESSION['user']['id'];
	$message = escapeshellcmd($message);
	$PathTo  = $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoAffaire']['dir.affaire'].$commande['dir_aff'];
	$PathFrom= $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'];
	foreach ($fileFrom as $in => $file) {
	    $FileExist = file_exists($PathTo.$file);
	    if(copy($PathFrom.$file,$PathTo.$file)) {
		Logg::loggerInfo('commandeGnose::CommandeSaveDocInGnose() ~ Sauvegarde du fichier '.$file.' de la commande '.$commande,array($fileFrom,$commande,$message),__FILE__.'@'.__LINE__);
		if(!$FileExist) {
		    $fileAdd[] = $PathTo.$file;
		    chmod($PathTo.$file,0770);
		}
		$fileCI[] = $PathTo.$file;
	    }
	}
	if(count($fileAdd) > 0) {
	    foreach ($fileAdd as $in => $file) {
		$uriAdd .= $file." ";
	    }
	    $CmdBase = "svn add --config-dir ".$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir']." ";
	    if ($GLOBALS['LOG']['DisplayDebug']) {
		$GLOBALS['LogSVNProcess'][] = $CmdBase.$uriAdd;
	    }
	    shell_exec($CmdBase.$uriAdd);
	}
	if(count($fileCI) > 0) {
	    foreach ($fileCI as $in => $file) {
		$uriCI .= $file." ";
	    }
	    $uri		= " -m \"".$message."\" ".$uriCI;
	    $CmdBase	= "export EDITOR=\"vi\"; export LC_CTYPE=\"fr_FR.UTF-8\"; export LANG=\"fr_FR.UTF-8\"; svn ci --config-dir ".$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir']." --username ".$_SESSION["user"]["id"]." ";
	    $CmdBase1	= "export LANG=\"en_US.UTF-8\"; export LC_CTYPE=\"en_US.UTF-8\"; export EDITOR=\"vi\"; svn update --non-interactive --no-auth-cache --config-dir ".$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir']." --username ".$_SESSION["user"]["id"]." ";
	    if ($GLOBALS['LOG']['DisplayDebug']) {
		$GLOBALS['LogSVNProcess'][] = $CmdBase.$uri;
		$GLOBALS['LogSVNProcess'][] = $CmdBase1.$GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir']." &";
	    }
	    shell_exec($CmdBase.$uri);
	    shell_exec($CmdBase1.$GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir']." &");
	    return true;
	}
	else {
	    Logg::loggerError('commandeGnose::CommandeSaveDocInGnose() ~ Sauvegarde des fichiers de la commande '.$commande.' impossible. Pas de fichiers',array($fileFrom,$commande,$message),__FILE__.'@'.__LINE__);
	    return "ERREUR PAS DE FICHIER EN";
	}
    }

    /**
     * Méthode qui génère un export tableur des commande
     * @param array $commande Toutes les informations sur les commande
     * @param string $format Indique le format de sortie
     * @return string
     */
    public function  CommandeExportTableurConverter($commande,$format = '') {
	loadPlugin(array('OOConverter'));
	$availableFormat = OOConverterAvailable('spreadsheet');
	if(!array_key_exists($format,$availableFormat)) $format = 'pdf';
	$fileFrom = $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'].$this->CommandeExportTableur($commande);
	$formatExt = ($availableFormat[$format] != '') ? substr($availableFormat[$format],strpos($availableFormat[$format], '[')+2,-1) : $format;
	$fromFormat  = FileGetExtention($fileFrom);
	$fileTo = substr($fileFrom,0, (strlen($fromFormat)+1)*-1).'.'.$formatExt;
	if($format == 'ods') return $fileTo;
	$return = OOConverter($fileFrom,$fileTo,$format);
	if($return === true) return $fileTo;
	else		     return $return;
    }

    /**
     * Méthode qui génère le tableur d'export des commande
     * @param array $commande Toutes les informations sur les commande à exporter
     * @return string Le nom du fichier généré.
     */
    private function CommandeExportTableur($commande) {
	loadPlugin(array('OOConverter'));
	$cannevasPath = $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['PropsecConf']['dir.cannevas.exportTableur'];
	$cannevasFile = $cannevasPath.$GLOBALS['ZunoCommande']['cannevas.exportTableur'];
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


	    $pos1 = strpos($xmlContent,'{id_cmd}');
	    $debut = substr($xmlContent,0,$pos1);
	    $fin = substr($xmlContent,$pos1);
	    $from = strrpos($debut,'<table:table-row');
	    $to = (strlen($fin)-strpos($fin,'</table:table-row>')-18)*-1;
	    $exportDebutDocument = substr($xmlContent,0,$from);
	    $exportFinDocument = substr($xmlContent,$to);
	    $GLOBALS['TmpTemplate']['exportMiddleDocument'] = substr($xmlContent,$from,$to);
	    if (is_array($commande)) {
		foreach ($commande as $kk => $dev) {
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
		    $dev['sommeFHT_cmd'] 	= ($dev['sommeHT_cmd'] == '') 	? '' : number_format($dev['sommeHT_cmd'],0,',',' ');
		    $dev['sommeHT_fact'] 	= ($dev['sommeHT_fact'] == '') 	? '' : number_format($dev['sommeHT_fact'],0,',',' ');
		    foreach ($dev as $in => $out) {
			$input[] = "/\{".$in."\}/";
			$output[] = $out;
		    }
		    $outCommande .= ProcessTemplating('exportMiddleDocument',$input,$output);
		    unset($input);
		    unset($output);
		}
	    }
	    else Logg::loggerError('commandeGnose::CommandeExportTableur() ~ Impossible de trouver des commandes à afficher ',count($contacts),__FILE__.'@'.__LINE__);
	    $xmlContent = $exportDebutDocument.$outCommande.$exportFinDocument;
	    unset($outCommande);

	    File_Add2File($ExportTmpPath.'content.xml',$xmlContent,TRUE);
	    $ArchivePath = $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'];
	    $ODTName = 'CommandeExport.ods';

	    @rm($ArchivePath.$ODTName);
	    $archive = new PclZip($ArchivePath.$ODTName);
	    $archive->add($ExportTmpPath,PCLZIP_OPT_REMOVE_PATH,$ExportTmpPath.'/');
	    $outputFile = $ODTName;
	    Logg::loggerInfo('commandeGnose::CommandeExportTableur() ~ Export de la liste des commandes '.$ODTName,count($commande),__FILE__.'@'.__LINE__);
	    return $outputFile;
	}
	else Logg::loggerError('commandeGnose::CommandeExportTableur() ~ Impossible de trouver le cannevas '.$cannevasFile,count($commande),__FILE__.'@'.__LINE__);
    }





}
?>
