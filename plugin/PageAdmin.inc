<?php
/*#########################################################################
#
#   name :       ACTUALITE
#   desc :       page central module
#   categorie :  plugin module
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/**
 * PageAdmin.
 * Class used for generating news portlets
 */
class PageAdminPortlet {
    /** Sort orderField field */
    var $orderField;
    /** Sort order direction */
    var $orderDirection;
    /** Selected page ID */
    var $selectID;
    /** Message used for administration process. */
    var $message;
    /** XML Content. */
    var $xml;
    /** Set to TRUE dynamic XML file. */
    var $dynXml;
    /** XSL file to use for transformation. */
    var $xsl;
    /** Parameters to use for XSL. */
    var $XslParam;
    /** Parameters to pass after XSLT process. */
    var $XslPostParam;
    /** Lang used for this portlet. */
    var $Lang;
    /** Cache interface. */
    var $cache;
    /** Channel to use for menu and manage. */
    var $channel;

    /**
     * Constructor.
     * @param channel define channel used for this object
     */
    function __construct($lang = '') {
	$this->orderField	= 'create';
	$this->orderDirection	= 'ascending';
	$this->channel 		= "normal";
	$this->dynXml 		= FALSE;
	if($lang != '') {
	    $this->Lang = $lang;
	}
	elseif(($_SESSION["language"] != $GLOBALS['LANGUE']['default'])and($_SESSION["language"] != '')) {
	    $this->Lang = $_SESSION["language"];
	}
	else {
	    $this->Lang = $GLOBALS['LANGUE']['default'];
	}
	$this->cache = new PageXMLCache($this->Lang);
    }

    /**
     * Set News to select.
     * @id News ID to select in this instance
     */
    function SetSelected($id) {
	$this->selectID = $id;
    }

    /**
     * Set News channel to use
     * @channel News channel to use for this instance
     */
    function SetChannel($channel = "normal") {
	$this->channel = $channel;
    }

    /**
     * Set Message to display in admin portlet.
     * @message Content of the message
     */
    function SetMessage($message) {
	$this->message = "<span class=\"important\">".$message."</span>";
    }

    /**
     * Set News order for a list.
     * @field Filed to use for ordering
     * @direction Direction of this ordering
     */
    function SetOrder($field, $direction = 'ascending') {
	$this->orderField = $field;
	$this->orderDirection = $direction;
    }

    /**
     * Select XSL file to use for transformation.
     * @xsl Set URI of the XSL stylesheet to apply for this instance
     */
    function Xsl($xsl) {
	$this->xsl = $xsl;
    }

    /**
     * Type is a shortcut to configure various kind of transformation.
     * @type Type to use for configuring this instance
     * @data array with data given from the form
     */
    function Type($type = '', $data = '') {
	$varin['order'] = $this->orderField;
	$varin['orderDir'] = $this->orderDirection;
	$varin['selectedID'] = $this->selectID;

	if($type == 'CREATE') {
	    //On genére un XML dynamic avec les variables du client
	    $varin	= $data;
	    $varin['nom_pg'] = '';
	    $varin['desc_pg'] = '';
	    $tmpXML = new Xml();
	    $tmpXML->getXmlFile($GLOBALS['REP']['template']."xml/page.detail.xml");
	    $tmpXML->preProcess($varin);
	    $tmpXML->Process();
	    $this->xml = $tmpXML->recordXmlFile();
	    $this->dynXml = TRUE;
	    //On genére un XSL dynamique
	    //on prépare les combo de page
	    //on initilialise le cache de menu et la transfo
	    $cacheMenu = new XMLCache_Menu('normal',$this->Lang);
	    $combobj = new Xml2Xsl();
	    //on prend la feuille de style pour faire un combo
	    $combobj->xslFile("admin/MenuCombo.xsl");
	    $varin['toselect'] = $data['parent_pg'];
	    $combobj->xslParameter($varin);
	    //on prepare le combo des channels
	    foreach($GLOBALS['CHANNEL_list'] as $key => $val) {
		$cacheMenu->setFile($key);
		//on enregistre le nouveau xml pour la transfo
		$combobj->xmlFile($cacheMenu->Process());
		//on transforme et on enregistre pour le channel admin
		$cmbList = stripslashs($combobj->Process());
		if($key != 'normal') {
		    $disp = "display: none;";
		}
		$outList .= "<DIV ID='Lparent4".$key."' style='".$disp."POSITION: relative;'>
							<select name='".$key."_parent_pg'>".$cmbList."</select>
						     </DIV>";

		foreach($GLOBALS['CHANNEL_list'] as $key1 => $val1) {
		    if($key1 == $key) {
			$disp = "";
		    }
		    else {
			$disp = "none";
		    }
		    $cmbFocus .= "MM_changeProp('Lparent4".$key1."','','style.display','".$disp."','DIV');";
		}
		if($key == 'normal') {
		    $chk = "checked='checked'";
		}
		else {
		    $chk = "";
		}
		$cmb4Channel .= $val."<input type=\"radio\" name=\"channel_pg\" value=\"".$key."\"  onFocus=\"".$cmbFocus."\"".$chk."/> -";
		unset($cmbFocus);
	    }
	    //on prepare le combo de ref_droit
	    $dbConnexion = new Bdd();
	    $dbConnexion->makeRequeteAuto('ref_droit');
	    $testDroit = $dbConnexion->process();
	    foreach($testDroit as $key => $val) {
		$cmb4droit .= "<option id=\"droit_".$val['id_dt']."\" value=\"".$val['id_dt']."\">".htmlspecialchars($val['nom_dt'],ENT_QUOTES)."</option>";
	    }
	    //on prepare le combo de style
	    $listall = FileDirectoryDetail($GLOBALS['REP']['appli'].$GLOBALS['REP']['xsl']."defaut/",array('xsl'),1);
	    foreach($listall as $key => $val) {
		if(substr($val['nom'], 0, 17) == 'BodyContent.Page.') {
		    $tmpname = substr($val['nom'], 17, -4);
		    $cmb4style .= "<option id=\"style_".$tmpname."\" value=\"".$tmpname."\">".$tmpname."</option>";
		}
	    }
	    $varin2['droit'] 		= $_SESSION['user']['right'];
	    $varin2['message'] 		= $this->message;
	    $varin2['droit_list'] 		= $cmb4droit;
	    $varin2['style_list'] 		= $cmb4style;
	    $varin3['outList']		= $outList;
	    $varin3['cmb4Channel'] 		= $cmb4Channel;
	    $varin3['HelpID'] 		= '';
	    $varin3['PageSpecial'] 		= '';
	    $varin3['PageChannel'] 		= '';
	    $varin3['PageBlocDesc'] 	= '';
	    $varin3['PageBlocDisplay'] 	= '';
	    $this->XslParam = $varin2;
	    $this->XslPostParam = $varin3;
	    $this->xsl = "admin/PageAdminCreate.xsl";
	}
	elseif($type == 'MODIF') {
	    //On recupere le fichier de cache complet
	    $this->cache->setLang("fr");
	    $this->cache->setCacheFile($this->selectID);
	    $this->xml = $this->cache->Process();
	    //On genére un XSL dynamique
	    $combobj = new Xml2Xsl();
	    //on recupere la page parent depuis le fichier XML
	    $combobj->xslFile("admin/PageAdmin.GetSelected.xsl");
	    $combobj->xmlFile($this->xml);
	    $selected = trim($combobj->Process());
	    $selected = trim(str_replace("<?xml version='".$GLOBALS['CACHEXML']['version']."' encoding='".$GLOBALS['CACHEXML']['encoding']."'?>", "",$selected));
	    $combobj->xslFile("admin/PageAdmin.StyleSelected.xsl");
	    $styleSelect = trim($combobj->Process());
	    $styleSelect = trim(str_replace("<?xml version='".$GLOBALS['CACHEXML']['version']."' encoding='".$GLOBALS['CACHEXML']['encoding']."'?>", "",$styleSelect));
	    $combobj->xslFile("admin/PageAdmin.DroitSelected.xsl");
	    $droitSelect = trim($combobj->Process());
	    $droitSelect = trim(str_replace("<?xml version='".$GLOBALS['CACHEXML']['version']."' encoding='".$GLOBALS['CACHEXML']['encoding']."'?>", "",$droitSelect));
	    $combobj->xslFile("admin/PageAdmin.MenuSelected.xsl");
	    $menuSelect = trim($combobj->Process());
	    $menuSelect = trim(str_replace("<?xml version='".$GLOBALS['CACHEXML']['version']."' encoding='".$GLOBALS['CACHEXML']['encoding']."'?>", "",$menuSelect));
	    $combobj->xslFile("admin/PageAdmin.ChannelSelected.xsl");
	    $channelSelect = trim($combobj->Process());
	    $channelSelect = trim(str_replace("<?xml version='".$GLOBALS['CACHEXML']['version']."' encoding='".$GLOBALS['CACHEXML']['encoding']."'?>", "",$channelSelect));
	    //On genére un XSL dynamique
	    //on prépare les combo de page
	    //on initilialise le cache de menu et la transfo
	    $cacheMenu = new XMLCache_Menu('normal',$this->Lang);
	    $combobj = new Xml2Xsl();
	    //on prend la feuille de style pour faire un combo
	    $combobj->xslFile("admin/MenuCombo.xsl");
	    $varin['toselect'] = $data['parent_pg'];
	    $combobj->xslParameter($varin);
	    //on prepare le combo des channels
	    foreach($GLOBALS['CHANNEL_list'] as $key => $val) {
		$cacheMenu->setFile($key);
		//on enregistre le nouveau xml pour la transfo
		$combobj->xmlFile($cacheMenu->Process());
		//on transforme et on enregistre pour le channel admin
		$cmbList = stripslashs($combobj->Process());
		if($key != $channelSelect)
		    $disp = "display: none;";
		$outList .= "<DIV ID='Lparent4".$key."' style='".$disp."POSITION: relative;'>
							<select name='".$key."_parent_pg'>".$cmbList."</select>
						     </DIV>";

		foreach($GLOBALS['CHANNEL_list'] as $key1 => $val1) {
		    if($key1 == $key)
			$disp = "";
		    else $disp = "none";
		    $cmbFocus .= "MM_changeProp('Lparent4".$key1."','','style.display','".$disp."','DIV');";
		}
		if($key == $channelSelect)
		    $chk = "checked='checked'";
		else $chk = "";
		$cmb4Channel .= $val."<input type=\"radio\" name=\"channel_pg\" value=\"".$key."\"  onFocus=\"".$cmbFocus."\"".$chk."/> -";
		unset($cmbFocus);
	    }
	    //on prepare le combo de ref_droit
	    $dbConnexion = new Bdd();
	    $dbConnexion->makeRequeteAuto('ref_droit');
	    $testDroit = $dbConnexion->process();

	    $menuSelect = trim(str_replace("<?xml version='".$GLOBALS['CACHEXML']['version']."' encoding='".$GLOBALS['CACHEXML']['encoding']."'?>", "",$menuSelect));
	    if ($menuSelect == "0") {
		$selectMenu0 = "checked";
	    }
	    else {
		$selectMenu1 = "checked";
	    }

	    foreach($testDroit as $key => $val) {
		if ((strpos($droitSelect,',') !== false and in_array($val['id_dt'],explode(',',$droitSelect)))or (strpos($droitSelect,',') === false and $val['id_dt'] == $droitSelect))
		    $cmb4droit .= "<option id=\"droit_".$val['id_dt']."\" value=\"".$val['id_dt']."\" selected=\"selected\">".htmlspecialchars($val['nom_dt'],ENT_QUOTES)."</option>";
		else $cmb4droit .= "<option id=\"droit_".$val['id_dt']."\" value=\"".$val['id_dt']."\">".htmlspecialchars($val['nom_dt'],ENT_QUOTES)."</option>";
	    }
	    //on prepare le combo de style
	    $listall = FileDirectoryDetail($GLOBALS['REP']['appli'].$GLOBALS['REP']['xsl']."defaut/",array('xsl'),1);
	    foreach($listall as $key => $val) {
		if(substr($val['nom'], 0, 17) == 'BodyContent.Page.') {
		    $tmpname = substr($val['nom'], 17, -4);
		    $titre = $tmpname;
		    if ($tmpname == $styleSelect)
			$cmb4style .= "<option id=\"style_".$tmpname."\" value=\"".$tmpname."\" selected=\"selected\">".$titre."</option>";
		    else $cmb4style .= "<option id=\"style_".$tmpname."\" value=\"".$tmpname."\">".$titre."</option>";
		}
	    }

	    // Gestion des bouton pour la popup de modification des langues
	    $listesupported = explode(",",$GLOBALS['LANGUE']['supported']);
	    foreach ($listesupported as $val) {
		if ($val != $GLOBALS['LANGUE']["default"]) {
		    $out2Lang.= linkTag("#\" onclick=\"return zuno.popup.open('../manage/PageModif4Lang.php','language=".$val."&amp;id=".$this->selectID."','750','600','','','','PageModif');",
			    imageTag(getStaticUrl('img')."lang/".$val.".png",$val))." - ";
		}
	    }
	    $out2Lang   = substr($out2Lang, 0, -2);

	    //On fourre tout dans les parametres XSL
	    $varin2['droit'] 		= $_SESSION['user']['right'];
	    $varin2['message'] 		= $this->message;
	    $varin2['contentLangDef']	= $GLOBALS['LANGUE_'.$_SESSION["language"]][$GLOBALS['LANGUE']["default"]];
	    $varin2['droit_list'] 		= $cmb4droit;
	    $varin2['style_list'] 		= $cmb4style;
	    $varin2['root_path'] 		= ($this->channel != 'normal' ? '../' : '');
	    $varin3['outList']		= $outList;
	    $varin3['cmb4Channel'] 		= $cmb4Channel;
	    $varin3['PageSpecial'] 		= '';
	    $varin3['PageChannel'] 		= '';
	    $varin3['PageBlocDesc'] 	= '';
	    $varin3['PageBlocDisplay'] 	= '';
	    $varin3['contentLangLink']	= $out2Lang;
	    $this->XslParam = $varin2;
	    $this->XslPostParam = $varin3;
	    $this->xsl = "admin/PageAdminModif.xsl";
	}
	elseif($type == 'MODIFCONTENT') {
	    //On recupere le fichier de cache complet
	    $this->cache->setCacheFile($this->selectID);
	    $this->xml = $this->cache->Process();
	    //On genére un XSL dynamique
	    $varin2['droit'] 		= $_SESSION['user']['right'];
	    $varin2['message'] 		= $this->message;
	    $varin2['lang'] 		= $this->Lang;
	    $varin3['PageSpecial'] 		= '';
	    $this->XslParam = $varin2;
	    $this->XslPostParam = $varin3;
	    $this->xsl = "admin/PageAdminModifContent.xsl";
	}
	elseif($type == 'IMPORTODT') {
	    $varin['help'] 	  = '';
	    $varin['file']    =inputTag('file', 'odt', '', '', '20','','');
	    $varin['bouton']  =buttonTag('','','bouton','Importer').
		    buttonTag('','','bouton','Annuler');
	    $varin['message'] = $this->message;
	    return templating('page/ImportOdt',$varin);
	}
	elseif($type == 'IMPORTODTConfirm') {
	    $data['message'] = $this->message;
	    $data['bouton']  =buttonTag('','','bouton','Confirmer').
		    buttonTag('reset','','bouton','Annuler');
	    return templating('page/ImportOdtConfirm',$data);
	}
	elseif($type == 'MANAGE') {
	    $cache = new XMLCache_Menu($this->channel, $this->Lang);
	    $this->xml = $cache->Process();
	    //On genére un XSL dynamic avec les variables de tri
	    $varin['droit'] = $_SESSION['user']['right'];
	    $varin['channel'] = $this->channel;
	    $this->XslParam = $varin;
	    $this->xsl = "admin/PageAdminManage.xsl";
	}
	elseif($type == 'DELETE') {
	    //On recupere le fichier de cache complet
	    $this->cache->setCacheFile($this->selectID);
	    $this->xml = $this->cache->Process();
	    //On genére un XSL dynamique
	    $combobj = new Xml2Xsl();
	    //on recupere la page parent depuis le fichier XML
	    $combobj->xslFile("admin/PageAdmin.GetSelected.xsl");
	    $combobj->xmlFile($this->xml);
	    $selected = trim($combobj->Process());
	    //on prépare les combo de page
	    //on initilialise le cache de menu et la transfo
	    $cacheMenu = new XMLCache_Menu('normal',$this->Lang);
	    //on prepare les variable pour ce fichier XSL
	    $varin['toselect'] = $selected;
	    $combobj->xslParameter($varin);
	    //on prend la feuille de style pour faire un combo
	    $combobj->xslFile("admin/MenuCombo.xsl");
	    //on enregistre le xml de cache pour le channel normal
	    $combobj->xmlFile($cacheMenu->Process());
	    //on transforme et on enregistre pour le channel normal
	    $cmb4normal = stripslashs($combobj->Process());
	    //on change le cache
	    $cacheMenu->setFile('admin');
	    //on enregistre le nouveau xml pour la transfo
	    $combobj->xmlFile($cacheMenu->Process());
	    //on transforme et on enregistre pour le channel admin
	    $cmb4admin = stripslashs($combobj->Process());
	    //on prepare le combo de ref_droit
	    $dbConnexion = new Bdd();
	    $dbConnexion->makeRequeteAuto('ref_droit');
	    $testDroit = $dbConnexion->process();
	    foreach($testDroit as $key => $val) {
		$cmb4droit .= "<option id=\"droit_".$val['id_dt']."\" value=\"".$val['id_dt']."\">".$val['nom_dt']."</option>";
	    }
	    //on prepare le combo de style
	    $listall = FileDirectoryDetail($GLOBALS['REP']['appli'].$GLOBALS['REP']['xsl']."defaut/",array('xsl'),1);
	    foreach($listall as $key => $val) {
		if(substr($val['nom'], 0, 17) == 'BodyContent.Page.') {
		    $tmpname = substr($val['nom'], 17, -4);
		    $cmb4style .= "<option id=\"style_".$tmpname."\" value=\"".$tmpname."\">".$tmpname."</option>";
		}
	    }
	    $varin2['droit'] 		= $_SESSION['user']['right'];
	    $varin2['message'] 		= $this->message;
	    $varin2['parent_normal_list']	= $cmb4normal;
	    $varin2['parent_admin_list']	= $cmb4admin;
	    $varin2['droit_list'] 		= $cmb4droit;
	    $varin2['style_list'] 		= $cmb4style;
	    $varin2['root_path'] 		= ($this->channel != 'normal' ? '../' : '');
	    $this->XslParam = $varin2;
	    $this->xsl = "admin/PageAdminDelete.xsl";
	}
    }

    /**
     * Process CreateXML() if not already done. Then check for XSL stylesheet
     * and process transformation
     * @OutType Kind of output type. 'XML' for complete XML file
     * @return Portlet content to place in everywhere or XML for direct client output
     */
    function Process($OutType = '') {
	//echo $this->xml;
	if($OutType == 'XML') {
	    // take care to set header('Content-type: application/xml'); before output document
	    $XML = "<?xml version='".$GLOBALS['CACHEXML']['version']."' encoding='".$GLOBALS['CACHEXML']['encoding']."'?>\n";
	    $XML.= "<?xml-stylesheet type=\"text/xsl\" href=\"".$this->xsl."\"?>\n";
	    $XML.= $this->xml;
	    return $XML;
	}
	else {
	    $output = new Xml2Xsl();
	    $output->xslFile($this->xsl);
	    $output->xmlFile($this->xml);
	    if(is_array($this->XslParam)) {
		$output->xslParameter($this->XslParam);
	    }
	    if(is_array($this->XslPostParam)) {
		$output->postProcess($this->XslPostParam);
	    }
	    if($this->dynXml) {
		$output->setDynXml();
	    }
	    return stripslashs($output->Process());
	}
    }

}





/**
 * PageAdmin.
 * News toolkit for news management and processing
 */
class PageAdminToolkit {
    /**
     * DBRecord.
     * @data array with data to update or insert news info
     */
    function DBRecord($data, $create=FALSE, $uselang=FALSE) {
	$lang = '';
	$dbConnexion = new Bdd();
	//on prepare les vars
	if ($data['style_pg'] != "") {
	    $in['style_pg'] 	= $data['style_pg'];
	}
	if ($data['img_pg'] != "") {
	    $in['img_pg'] 	= $data['img_pg'];
	}
	if ($data['img_menu_pg'] != "") {
	    $in['img_menu_pg'] 	= $data['img_menu_pg'];
	}
	if ($data['menuon_pg'] != "") {
	    $in['menuon_pg'] 	= $data['menuon_pg'];
	}
	if ($data['file_pg'] != "") {
	    $in['file_pg'] 	= $data['file_pg'];
	}
	if ($data['img_del'] == "1") {
	    $in['img_pg'] 	= "";
	}
	if ($data['icone_del'] == "1") {
	    $in['img_menu_pg'] 	= "";
	}
	if ($data['channel_pg'] != "") {
	    $in['channel_pg'] 	= $data['channel_pg'];
	}
	if ($data['actif_pg'] != "") {
	    $in['actif_pg'] 	= $data['actif_pg'];
	}
	if ($data['page_pg'] != "") {
	    $in['page_pg'] 	= $data['page_pg'];
	}
	if ($data['order_pg'] != "") {
	    $in['order_pg']	= $data['order_pg'];
	}
	if (isset($data['droit_pg'])) {
	    $in['droit_pg'] 	= $data['droit_pg'];
	}
	if (isset($data['parent_pg'])) {
	    $in['parent_pg'] 	= $data['parent_pg'];
	}
	//Changement de variables si utilisé avec une autre langue
	if ($uselang and ($_SESSION["language"] != $GLOBALS['LANGUE']['default'])) {
	    $extlang = "_".$_SESSION["language"];
	    $lang = $_SESSION["language"];
	}

	// la variable de nom editor qui viens de htmlarea doit passer se nommer content_lang
	$imgSuffix = ($in['channel_pg'] != "" and $in['channel_pg'] != "normal") ? '../' : './';
	if(array_key_exists('editor', $data))
	    $data['editor'] = str_replace('../../../',$imgSuffix,$data['editor']);
	if (array_key_exists('editor', $data))
	    if (trim($data['editor']) != "<br />" )
		$in['content_pg'.$extlang] = trim(stripslashes($data['editor']));

	if ($data['header_pg'] != "") {
	    $in['header_pg'.$extlang] = $data['header_pg'];
	}
	if ($data['desc_pg'] != "") {
	    $in['desc_pg'.$extlang] = $data['desc_pg'];
	}
	if ($data['nom_pg'] != "") {
	    $in['nom_pg'.$extlang] = $data['nom_pg'];
	}

	if ($create) {
	    $in['id_pg'] = $data['id_pg'];
	    $in['owner_pg'] = $_SESSION['user']['id'];
	    $in['modif_user_pg'] = $_SESSION['user']['id'];
	    if ($in['channel_pg'] == "") {
		$in['channel_pg'] 	= "normal";
	    }
	    $dbConnexion->makeRequeteInsert('ref_page',$in);
	}
	else $dbConnexion->makeRequeteUpdate('ref_page','id_pg',$data['id_pg'],$in);
	$dbConnexion->process();
	if ($in['channel_pg'] == "")
	    $in['channel_pg'] 	= "normal";
	// on flush le cache
	$foo = new XMLCache_Menu($in['channel_pg'],$_SESSION["language"]);
	$foo->XmlCache->flushMe();
	//on flush le cache de ce fichier
	$cache = new PageXMLCache($lang);
	$cache->setCacheFile($data['id_pg']);
	$cache->XmlCache->flushMe();
	$cache->setCacheFile('');
	$cache->XmlCache->flushMe();
    }

    /**
     * DBRecordDocument.
     * @data array with data to update or insert news info
     */
    function DBRecordDocument($data, $create=FALSE) {
	$dbConnexion = new Bdd();
	//on prepare les vars
	if ($data['nom_doc'] != "") {
	    $in['nom_doc'] = $data['nom_doc'];
	}
	if ($data['order_doc'] != "") {
	    $in['order_doc'] = $data['order_doc'];
	}
	else {
	    $in['order_doc'] = '1';
	}
	if ($data['file_doc'] != "") {
	    $in['file_doc'] = $data['file_doc'];
	}
	if ($data['page_doc'] != "") {
	    $in['page_doc'] = $data['page_doc'];
	}
	$in['desc_doc'] 		= $data['desc_doc'];
	$in['modif_user_doc'] 		= $_SESSION['user']['id'];

	if ($create) {
	    $in['owner_doc'] = $_SESSION['user']['id'];
	    $dbConnexion->makeRequeteInsert('document',$in);
	}
	else {
	    echo $dbConnexion->makeRequeteUpdate('document','id_doc',$data['id_doc'],$in);
	}
	$dbConnexion->process();
	// on flush le cache
	$foo = new XMLCache_Menu($data['channel_pg']);
	$foo->XmlCache->flushMe();
	//on flush le cache de ce fichier
	$cache = new PageXMLCache('');
	$cache->setCacheFile($data['page_doc']);
	$cache->XmlCache->flushMe();
	$cache->setCacheFile('');
	$cache->XmlCache->flushMe();
	if (!class_exists('Search_XMLCache'))
	    loadPlugin(array('Search.XMLCache'));
	$cache = new Search_XMLCache();
	$cache->XmlCache->flushMe();
    }


    /**
     * DBDelete.
     * @id string ID of the newsletter to remove from database
     */
    function DBDelete($id = '', $channel = 'normal') {
	if($id != '') {
	    $in['id_pg']= $id;
	    $dbConnexion = new Bdd();
	    $dbConnexion->makeRequeteDelete('ref_page',$in);
	    $dbConnexion->process();
	    //on flush le cache de ce fichier
	    $cache = new PageXMLCache();
	    $cache->setCacheFile($id);
	    $cache->XmlCache->flushMe();
	    if (!class_exists('Search_XMLCache'))
		loadPlugin(array('Search.XMLCache'));
	    $cache = new Search_XMLCache();
	    $cache->XmlCache->flushMe();

	    foreach ( explode (",", $GLOBALS['LANGUE']['supported']) as $key => $val ) {
		$menu = new XMLCache_Menu();
		$menu->setFile($channel, $val);
		$menu->XmlCache->flushMe();
	    }
	}
    }

    /*
	 * PageDBRemove
	 * @param $pageid	    page ID *
    */
    function PageDBRemove($pageid) {
	//on prepare les vars
	$in['id_pg']	= $pageid;
	$tmpreq = new Bdd($GLOBALS['CHANNEL_'.$this->Channel]['SessDBPool']);
	$tmpreq->MakeRequeteDelete('ref_page',$in);
	$tmpreq->process();

	$docliste = PageDBImportDocument($pageid);
	if (count($docliste) > 0) {
	    foreach ($docliste as $iddoc => $detail_doc) {
		PageDBRemoveDocument($detail_doc['id_pg'],$pageid);
	    }
	}
    }

    /*
	 * PageDBRemoveDocument
	 * @param $docid	    document ID *
	 * @param $pageid	    Page ID *
    */
    function PageDBRemoveDocument($docid,$pageid) {
	//on prepare les vars
	$in['id_doc']	= $docid;
	$tmpreq = new Bdd($GLOBALS['CHANNEL_1']['SessDBPool']);
	$tmpreq->MakeRequeteAuto('document',$in);
	$docinfo = $tmpreq->process();
	$docinfo = $docinfo[0];

	$tmpreq = new Bdd($GLOBALS['CHANNEL_1']['SessDBPool']);
	$tmpreq->MakeRequeteDelete('document',$in);
	$tmpreq->process();

	$cache = new PageXMLCache();
	$cache->setCacheFile($pageid);
	$cache->XmlCache->flushMe();

	if (!class_exists('Search_XMLCache'))
	    loadPlugin(array('Search.XMLCache'));
	$cache = new Search_XMLCache();
	$cache->XmlCache->flushMe();
	@unlink($GLOBALS['REP']['appli'].$GLOBALS['PAGE']['REP_doc'].$docinfo['file_doc']);
    }
    /*
	 * PageRemoveImage
	 * @param $image	image filename from photothéque directory *
    */
    function PageRemoveImage($image) {
	if (FileIsFileExist($GLOBALS['REP']['appli'].$GLOBALS['PAGE']['REP_phototeque'].$image)) {
	    unlink($GLOBALS['REP']['appli'].$GLOBALS['PAGE']['REP_phototeque'].$image);
	    return TRUE;
	}
	else {
	    return FALSE;
	}
    }
}

?>