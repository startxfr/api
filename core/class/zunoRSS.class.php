<?php
/*#########################################################################
#
#   name :       pdf.inc
#   desc :       library for PDF creation
#   categorie :  core module
#   ID :  	 $Id: pdf.inc 1915 2008-12-13 01:46:04Z cl $
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| Html2PDF
|
| generate pdf from html doc and save it into tmp folder
+-------------------------------------------------------------------------+
| $html  	*   Template input
| $name		*   name for the pdf file
| $option	    option you want to put in
+-------------------------------------------------------------------------+
| return template document with filled data
+------------------------------------------------------------------------*/

class zunoRSS {
    var $feed;
    var $bdd;

    function __construct() {
	$this->feed = new RSS();
	$this->bdd  = new Bdd($GLOBALS['PropsecConf']['DBPool']);

    }

    function generateMyRss($uid) {
	$this->feed->title       = "Votre actualité (".$_SESSION['user']['fullnom'].")";
	$this->feed->link        = $GLOBALS['zunoClientCoordonnee']['url'];
	$this->feed->description = "Vous trouverez dans ce fils les 20 dernières actualités que vous avez générés";
	$this->bdd->makeRequeteFree("SELECT * FROM actualite WHERE user = '$uid' LIMIT 0, 20");
	$this->generateRssRows($this->bdd->process());
    }

    function generatePublicRss() {
	$this->feed->title       = "Actualités publique de ".$GLOBALS['zunoClientCoordonnee']['nom'];
	$this->feed->link        = $GLOBALS['zunoClientCoordonnee']['url'];
	$this->feed->description = "Vous trouverez dans ce fils les 20 dernières actualités publique de l'entreprise ".$GLOBALS['zunoClientCoordonnee']['nom'];
	$this->bdd->makeRequeteFree("SELECT * FROM actualite WHERE isPublic = '1' LIMIT 0, 20");
	$this->generateRssRows($this->bdd->process());
    }

    function generateClientRss($token) {
	$this->bdd->makeRequeteFree("SELECT * FROM entreprise WHERE MD5(CONCAT(id_ent,'-',cp_ent)) = '$token'");
	$ent = $this->bdd->process();
	$ent = $ent[0];

	$this->feed->title       = "Actualité commerciale entre ".$GLOBALS['zunoClientCoordonnee']['nom']." et ".$ent['nom_ent'];
	$this->feed->link        = $GLOBALS['zunoClientCoordonnee']['url'];
	$this->feed->description = "Bonjour entreprise ".$ent['nom_ent'].".<br/>Vous trouverez dans ce fils les 20 dernières actions que nous avons réalisés pour vous ou avec vous.";
	$this->bdd->makeRequeteFree("SELECT * FROM actualite WHERE isPublieForClient = '1' AND id_ent = '".$ent['id_ent']."' LIMIT 0, 20");
	$this->generateRssRows($this->bdd->process());
    }

    function generateUnauthorizedRss() {
	$this->feed->title       = "Fils RSS de l'entreprise ".$GLOBALS['zunoClientCoordonnee']['nom'];
	$this->feed->link        = $GLOBALS['zunoClientCoordonnee']['url'];
	$this->feed->description = "Vous ne remplissez pas les conditions nécessaires à l'obtention de ce fil d'information.
										Veuillez entrer en contact avec l'entreprise ".$GLOBALS['zunoClientCoordonnee']['nom']." 
										pour vérifier ou obtenir les identifiants nécéssaires";
    }

    function checkClientToken($token) {
	$this->bdd->makeRequeteFree("SELECT * FROM entreprise WHERE MD5(CONCAT(id_ent,'-',cp_ent)) = '$token'");
	$ent = $this->bdd->process();
	if($ent[0]['id_ent'] != '')
	    return true;
	else return false;
    }

    static function generateHeaderLinkCommon($channel) {
	$suffix = ($channel != 'normal') ? '../' : '';
	$out = '<link title="Actualité publique de '.$GLOBALS['zunoClientCoordonnee']['nom'].'" type="application/rss+xml" rel="alternate" href="'.$suffix.'rss.php"/>';
	if(isset($_SESSION) and array_key_exists('user',$_SESSION) and $_SESSION['user']['id'] != '')
	    $out.= '<link title="Mon actualité Business" type="application/rss+xml" rel="alternate" href="'.$suffix.'rss.php?type=my"/>';
	return $out;
    }



    function display() {
	echo $this->feed->serve();
    }




    private function generateRssRows($res) {
	$url = 'http://'.
		$_SERVER['SERVER_NAME'].
		substr($_SERVER['REQUEST_URI'], 0, strrpos($_SERVER['REQUEST_URI'], "/")+1).
		'actualite.php?id=';
	if (count($res) > 0) {
	    foreach ($res as $key => $a) {
		$item = new RSSItem();
		$item->title = $a['titre'];
		$item->link  = $url.$a['id'];
		$item->setPubDate(strtotime($a['date']));
		$item->description = "<![CDATA[ ".$a['desc']." ]]>";
		$this->feed->addItem($item);
	    }
	}
    }
}




















class RSS {
    var $title;
    var $link;
    var $description;
    var $language = "fr-FR";
    var $pubDate;
    var $items;
    var $tags;

    function RSS() {
	$this->items = array();
	$this->tags  = array();
    }

    function addItem($item) {
	$this->items[] = $item;
    }

    function setPubDate($when) {
	if(strtotime($when) == false)
	    $this->pubDate = date("D, d M Y H:i:s ", $when) . "GMT";
	else
	    $this->pubDate = date("D, d M Y H:i:s ", strtotime($when)) . "GMT";
    }

    function getPubDate() {
	if(empty($this->pubDate))
	    return date("D, d M Y H:i:s ") . "GMT";
	else
	    return $this->pubDate;
    }

    function addTag($tag, $value) {
	$this->tags[$tag] = $value;
    }

    function out() {
	$out  = $this->header();
	$out .= "<channel>\n";
	$out .= "<title>" . $this->title . "</title>\n";
	$out .= "<link>" . $this->link . "</link>\n";
	$out .= "<description>" . $this->description . "</description>\n";
	$out .= "<language>" . $this->language . "</language>\n";
	$out .= "<pubDate>" . $this->getPubDate() . "</pubDate>\n";

	foreach($this->tags as $key => $val) $out .= "<$key>$val</$key>\n";
	foreach($this->items as $item) $out .= $item->out();

	$out .= "</channel>\n";

	$out .= $this->footer();

	$out = str_replace("&", "&amp;", $out);

	return $out;
    }

    function serve($contentType = "application/xml") {
	$xml = $this->out();
	header("Content-type: $contentType");
	echo $xml;
    }

    function header() {
	$out  = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
	$out .= '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">' . "\n";
	return $out;
    }

    function footer() {
	return '</rss>';
    }
}

class RSSItem {
    var $title;
    var $link;
    var $description;
    var $pubDate;
    var $guid;
    var $tags;
    var $attachment;
    var $length;
    var $mimetype;

    function RSSItem() {
	$this->tags = array();
    }

    function setPubDate($when) {
	if(strtotime($when) == false)
	    $this->pubDate = date("D, d M Y H:i:s ", $when) . "GMT";
	else
	    $this->pubDate = date("D, d M Y H:i:s ", strtotime($when)) . "GMT";
    }

    function getPubDate() {
	if(empty($this->pubDate))
	    return date("D, d M Y H:i:s ") . "GMT";
	else
	    return $this->pubDate;
    }

    function addTag($tag, $value) {
	$this->tags[$tag] = $value;
    }

    function out() {
	$out .= "<item>\n";
	$out .= "<title>" . $this->title . "</title>\n";
	$out .= "<link>" . $this->link . "</link>\n";
	$out .= "<description>" . $this->description . "</description>\n";
	$out .= "<pubDate>" . $this->getPubDate() . "</pubDate>\n";

	if($this->attachment != "")
	    $out .= "<enclosure url='{$this->attachment}' length='{$this->length}' type='{$this->mimetype}' />";

	if(empty($this->guid)) $this->guid = $this->link;
	$out .= "<guid>" . $this->guid . "</guid>\n";

	foreach($this->tags as $key => $val) $out .= "<$key>$val</$key\n>";
	$out .= "</item>\n";
	return $out;
    }

    function enclosure($url, $mimetype, $length) {
	$this->attachment = $url;
	$this->mimetype   = $mimetype;
	$this->length     = $length;
    }
}
?>
