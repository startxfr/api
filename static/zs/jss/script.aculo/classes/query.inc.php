<?php

class query {
	var $result;
	var $num_rows;
	
	function query($query) {
		// Exécution de la requête
		$this->result = @mysql_query($query);
		// Vérification du résultat
		if (!($this->result)) {
			echo "echec de la requête $query :<br />\n". mysql_error() ."<br /><br />\n";
			}
		}
		
	function toObject() {
		// Transformation du résultat en objet
		$this->num_rows = @mysql_num_rows($this->result);
		
		while($res = @mysql_fetch_object($this->result)) {
			$tab[] = $res;
			}
		return $tab;
		}	
		
	function toLine($line) {
		// Transformation du résultat en objet : on récupère uniquement la ligne $line
		@mysql_data_seek($this->result,$line);
		return mysql_fetch_object($this->result);
		}	
		
	function toLineArray($line) {
		// Transformation du résultat en tableau : on récupère uniquement la ligne $line
		@mysql_data_seek($this->result,$line);
		return mysql_fetch_assoc($this->result);
		}
	}

?>