<?php


/**
 * Description of MyCurl
 * Classe qui permet d'envoyer un formulaire en POST via curl
 *
 * @author Nicolas Mannocci
 * @version 1
 */
class MyCurl {

    private $curlSession;
    private $curlRetour;
    private $url;
    private $post;
    private $done;

    /**
     * Constructeur de la classe, il initialise la session Curl
     */
    function __construct() {

	$this->curlSession = curl_init();
	$this->done = false;
    }

    /**
     * Méthode pour fixer l'URL d'envoi
     * @param string $url L'url à laquelle sera transmise la requête
     */
    public function setUrl($url) {
	$this->url = $url;
    }

    /**
     * Méthode qui initialise les valeurs à mettre en POST dans la requête
     * @param array $post Les données à envoyer
     */
    public function setPost($post) {
	if(is_array($post)) {
	    $this->post = "";
	    foreach($post as $k => $v) {
		if(is_string($v) or is_numeric($v)) {
		    $this->post.= $k."=".urlencode($v)."&";
		}
	    }
	    $this->post = substr($this->post, 0, strlen($this->post)-1);
	}
    }

    /**
     * Méthode qui ajoute un élément à la variable POST
     * @param string $key La clef de la variable
     * @param string $value La valeur de la variable
     */
    public function addToPost($key, $value) {
	if(is_string($value) and is_string($this->post) and is_string($key))
	    $this->post .= "&".$key."=".$value;
    }

    /**
     * Méthode qui génère la requête curl, l'envoi et libère les ressources.
     */
    private function curl() {
	curl_setopt( $this->curlSession, CURLOPT_URL, $this->url );
	curl_setopt( $this->curlSession, CURLOPT_HEADER, 0 );
	curl_setopt( $this->curlSession, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt( $this->curlSession, CURLOPT_POST, 1 );
	curl_setopt( $this->curlSession, CURLOPT_POSTFIELDS, $this->post );
	$this->curlRetour = curl_exec( $this->curlSession );
	curl_close( $this->curlSession );
	$this->done = true;
    }

    /**
     * Méthode appelée pour envoyer la requête
     * @return string Le retour
     */
    public function send() {
	print_r("Envoyé : ".$this->post);
	echo "<br /><br />";
	if(is_string($this->url) and is_string($this->post)) {
	    if($this->done)
		$this->curlSession = curl_init();
	    $this->curl();
	    return $this->curlRetour;
	}
	else {
	    return "Pas envoyé !";
	}

    }


}
?>
