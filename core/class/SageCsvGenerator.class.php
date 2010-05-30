<?php
/*#########################################################################
#
#   name :       SVN.inc
#   desc :       Client library for Subversion
#   categorie :  core module
#   ID :  	 $Id: libSvn.class.php 4097 2010-04-26 09:20:45Z cl $
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/


class SageCsvGenerator
{
	private $filePath = '/tmp';
	private $fileName = 'export';
	private $fileExt  = '.sage.csv';
	private $fileUri;
	private $fileContent;
	private $schemaFC = array(
		'DATE_FACT'		=> array('type'=>'D','length'=>10 ,'value'=>''),
		'CODE_JOURN'		=> array('type'=>'N','length'=>4,'value'=>''),
		'NUMERO_FACT'		=> array('type'=>'A','length'=>10 ,'value'=>''),
		'COMPTES'		=> array('type'=>'N','length'=>10,'value'=>''),
		'COMPTE_TIERS'		=> array('type'=>'A','length'=>12,'value'=>''),
		'LIBELLE'		=> array('type'=>'A','length'=>24,'value'=>''),
		'DEBIT'			=> array('type'=>'N','length'=>14,'value'=>''),
		'CREDIT'		=> array('type'=>'N','length'=>14,'value'=>'')
	);
	private $schemaFF = array(
		'DATE_FACT'		=> array('type'=>'D','length'=>10 ,'value'=>''),
		'CODE_JOURN'		=> array('type'=>'N','length'=>4,'value'=>''),
		'NUMERO_FACT'		=> array('type'=>'A','length'=>10 ,'value'=>''),
		'COMPTES'		=> array('type'=>'N','length'=>10,'value'=>''),
		'COMPTE_TIERS'		=> array('type'=>'A','length'=>12,'value'=>''),
		'LIBELLE'		=> array('type'=>'A','length'=>24,'value'=>''),
		'DEBIT'			=> array('type'=>'N','length'=>14,'value'=>''),
		'CREDIT'		=> array('type'=>'N','length'=>14,'value'=>'')
	);

	private $additionalFC = array();
	private $additionalFF = array();

	/**
	 * Constructor.
	 */
	function __construct($fileName ='') {
		$this->filePath = $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'];
		$this->fileExt  = $GLOBALS['zunoPontComptable']['fileExt'];
		$this->setFileUri($fileName);
	}


	/**
	 * defini le nom de fichier à exporter
	 */
	public function getFileUri() {
		return $this->fileUri;
	}
	/**
	 * defini le nom de fichier à exporter
	 */
	public function getFileName() {
		return $this->fileName;
	}
	/**
	 * defini le nom de fichier à exporter
	 */
	public function getFileFullName() {
		return $this->fileName.$this->fileExt;
	}
	/**
	 * defini le nom de fichier à exporter
	 */
	public function getFilePath() {
		return $this->filePath;
	}


	/**
	 * defini le nom de fichier à exporter
	 */
	public function setFileUri($fileName = '') {
		if($fileName != '')
			$this->fileName	= $fileName;
		$this->fileUri	= $this->filePath.$this->fileName.$this->fileExt;
	}
	
	
	/**
	 * Enregistre le contenu dans le fichier
	 */
	public function saveFile($fileName = '') {
		if($fileName != '')
			$this->setFileUri($fileName);
		File_Add2File($this->fileUri,$this->fileContent,true);
		return $this->fileUri;
	}

	/**
	 * Enregistre le contenu dans le fichier et pousse le fichier vers le navigateur
	 */
	public function pushFile($display = false) {
		$this->saveFile();
		if($display) $mime = 'text/plain';
		else		 $mime = 'application/download';
		PushFileToBrowser($this->fileUri,$this->fileName.$this->fileExt,$mime);
	}

	/**
	 * Supprime une clef dans le tableau des informations FC
	 */
	public function removeFCInfo($key) {
		if(array_key_exists($key,$this->additionalFC))
			unset($this->additionalFC[$key]);
		return $this;
	}


	/**
	 * Supprime une clef dans le tableau des informations FF
	 */
	public function removeFFInfo($key) {
		if(array_key_exists($key,$this->additionalFF))
			unset($this->additionalFF[$key]);
		return $this;
	}

	/**
	 * Ajoute les informations dans le tableau des informations FC
	 */
	public function addFCInfo($code,$valeur,$type = '',$longeur = '') {
		$info = array();
		$info['value'] = $valeur;
		if($type != '')		$info['type'] = $type;
		if($longeur != '')	$info['length'] = $longeur;
		$this->additionalFC[$code] = $info;
		return $this;
	}

	/**
	 * Ajoute les informations dans le tableau des informations FF
	 */
	public function addFFInfo($code,$valeur,$type = '',$longeur = '') {
		$info = array();
		$info['value'] = $valeur;
		if($type != '')		$info['type'] = $type;
		if($longeur != '')	$info['length'] = $longeur;
		$this->additionalFF[$code] = $info;
		return $this;
	}

	/**
	 * Remplit tout le tableau des informations FC et ajoute la ligne
	 */
	public function quickAddFCLigne($date,$id,$compte,$montant,$sens= 'D',$id_client='',$nom_client='') {
	    	$this->addFCInfo('CODE_JOURN',$GLOBALS['zunoPontComptable']['CodeJournal']);
		$this->addFCInfo('DATE_FACT',$date);
		$this->addFCInfo('NUMERO_FACT',$id);
		$this->addFCInfo('COMPTES',$compte);
		$this->addFCInfo('COMPTE_TIERS',$id_client);
		$this->addFCInfo('LIBELLE',$nom_client);
		if($sens == 'D')
		     $this->addFCInfo('DEBIT',$montant);
		else $this->addFCInfo('CREDIT',$montant);
		$this->createFCLigne();
		return $this;
	}


	/**
	 * Remplit tout le tableau des informations FF et ajoute la ligne
	 */
	public function quickAddFFLigne($date,$id,$compte,$montant,$sens= 'D',$id_client='',$nom_client='') {
	    	$this->addFCInfo('CODE_JOURN',$GLOBALS['zunoPontComptable']['CodeJournal']);
		$this->addFCInfo('DATE_FACT',$date);
		$this->addFCInfo('NUMERO_FACT',$id);
		$this->addFCInfo('COMPTES',$compte);
		$this->addFCInfo('COMPTE_TIERS',$id_client);
		$this->addFCInfo('LIBELLE',$nom_client);
		if($sens == 'D')
		     $this->addFCInfo('DEBIT',$montant);
		else $this->addFCInfo('CREDIT',$montant);
		$this->createFCLigne();
		return $this;
	}
	
	/**
	 * formate les informations du tableau selon le schema FC
	 */
	public function createFCLigne($resetData = true) {
		$this->addLigne($this->additionalFC,$this->schemaFC);
		if($resetData)
			$this->resetFC();
		return $this;
	}
	
	/**
	 * formate les informations du tableau selon le schema FF
	 */
	public function createFFLigne($resetData = true) {
		$this->addLigne($this->additionalFF,$this->schemaFF);
		if($resetData)
			$this->resetFF();
		return $this;
	}
	
	
	
	/**
	 * formate les informations d'une ligne et l'ajoute dans le contenu du fichier
	 */
	private function addLigne($data,$schema) {
		$out = '';
		foreach($schema as $key => $desc) {
			if(array_key_exists($key,$data)) {
				if(array_key_exists('value',$data[$key]))
					$desc['value'] = $data[$key]['value'];
				if(array_key_exists('length',$data[$key]))
					$desc['length'] = $data[$key]['length'];
				if(array_key_exists('type',$data[$key]))
					$desc['type'] = $data[$key]['type'];
			}
			$out .= $this->formatChaine($desc['value'],$desc['length'],$desc['type']);
		}	
		$this->fileContent .= $out."\r\n";
	
	}
	
	/**
	 * formate les informations du tableau selon le schema FC
	 */
	private function resetFC() {
		$this->additionalFC = array();
	}
	
	/**
	 * formate les informations du tableau selon le schema FF
	 */
	private function resetFF() {
		$this->additionalFF = array();
	}

	/**
	 * formate une chaine de charactère selon les type de données imposés par SAGE
	 */
	private function formatChaine($string,$rowLength,$format='A',$blankSymbol = ' ') {
		// petit hack qui fait passé les dates 21/01/2009 a 21/01/09 quand on demande une date à 8 chiffre
		if($format == 'D' and $rowLength == 8 and substr($string,5,3) == '/20')
			$string = substr($string,0,6).substr($string,8,2);
		$stringLength = strlen($string);
		// Quand la chaine est plus grande
		if($stringLength > $rowLength)
			$string = substr($string,0,$rowLength);
		
		return '"'.$string.'";';
	}
}

?>
