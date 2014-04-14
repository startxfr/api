<?

class connect {
	var $link;
	var $error;
	
	function connect($server_param,$db) {
		// Connexion au serveur mysql
		if (!(@$this->link=mysql_connect($server_param["name"],$server_param["login"],$server_param["pass"]))) {
				$this->error = "je n'arrive pas à me connecter au serveur de bdd<br />\n";
			}
		// Sélection de la base
		if (!(@mysql_select_db($db))) {
				$this->error .= "je n'arrive pas à utiliser la base $db<br />\n";
			}
		echo $this->error;
		}
	
	function close() {		
		// Fermeture de la connexion
		if (!(@mysql_close($this->link))) {
				echo "je n'arrive pas à me déconnecter du serveur de bdd\n";
			}
		}
	}

?>