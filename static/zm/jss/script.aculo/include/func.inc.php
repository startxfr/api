<?

// Formatage de date à partir d'un Timestamp Unix
function FormatDate ($date) {
	return strftime("%A %d %B %y",$date);
	}

function deconnect() {
	// Détruit toutes les variables de session
	$_SESSION = array();
	
	// Si vous voulez détruire complètement la session, effacez également le cookie de session.
	// Note : cela détruira la session et pas seulement les données de session !
	if (isset($_COOKIE[session_name()])) {
	   setcookie(session_name(), '', time()-42000, '/');
	}
	
	// Finalement, on détruit la session.
	session_destroy();
	
	// Initialisation d'une nouvelle session.
	session_start();
	}

?>