<?php
/**
 *Fonction qui regarde si tu es proprio et appelle correctement verfiDroits
 * @param string $partie
 * @param int $souspartie
 * @param string $proprio
 * @return bool
 */
function verifDroitsAuto($partie, $souspartie, $proprio) {
    if($proprio == $_SESSION['user']['id']) {
	return verifDroits($partie, $souspartie);
    }
    else {
	switch($souspartie) {
	    case 13:
		return verifDroits($partie, 14);
		break;
	    case 15:
		return verifDroits($partie, 17);
		break;
	    case 25:
		return verifDroits($partie, 35);
		break;
	    case 27:
		return verifDroits($partie, 37);
		break;
	    case 30:
		return verifDroits($partie, 40);
		break;
	    case 50:
		return verifDroits($partie, 51);
		break;
	    case 52:
		return verifDroits($partie, 53);
		break;
	    case 54:
		return verifDroits($partie, 55);
		break;
	    case 60:
		return verifDroits($partie, 61);
		break;
	    case 62:
		return verifDroits($partie, 63);
		break;
	    default:
		return verifDroits($partie, $souspartie);
		break;
	}
    }
}

/**
 * Fonction qui gère les accès aux différentes parties de l'appli
 * @param string $partie Le nom de la partie / du module dans le quel on se trouve
 * @param int $souspartie Le code de l'action désirée
 * @return bool Indique si on a le droit ou pas
 */
function verifDroits($partie, $souspartie = 99 ) {
    switch ($partie) {
	case 'actualite':
	    $nbpartie = 1000;
	    break;
	case 'affaire':
	    $nbpartie = 1100;
	    break;
	case 'commande':
	    $nbpartie = 1200;
	    break;
	case 'contact':
	case 'contactParticulier':
	case 'contactEntreprise' :
	    $partie = 'contact';
	    $nbpartie = 1300;
	    break;
	case 'devis':
	    $nbpartie = 1400;
	    break;
	case 'facture':
	    $nbpartie = 1500;
	    break;
	case 'navigator':
	    $nbpartie = 2000;
	    break;
	case 'preference':
	    $nbpartie = 3000;
	    break;
	case 'search':
	    $nbpartie = 2100;
	    break;
	case 'send':
	    $nbpartie = 2200;
	    break;
	case 'avoir':
	    $nbpartie = 1600;
	    break;
	case 'produit':
	    $nbpartie = 1700;
	    break;
	case 'statistiques':
	    $nbpartie = 5000;
	    break;
//	case 'mobile':
//	    $nbpartie = 7000;
//	    break;
	case 'pontComptable':
	    $nbpartie = 6000;
	    break;
	default:
	    $nbpartie = 9900;
	    break;
    }
    $ousuisje=$nbpartie+$souspartie;


    //insérer les cas particuliers ici, au début de la fonction
    if($_SESSION['user']['right'] == 0) {
	return 1;
    } // Utilisateur STARTX

    if($_SESSION['user']['right'] == 1) {
	return 1;
    } // Utilisateur STARTX bis

    //Là on va gérer le fait que le client a acheté les modules ou pas
    elseif($_SESSION['user']['module'][$partie] != 'oui') {
	return array('0', $partie);
    }
    elseif((($souspartie>=50 and $souspartie<=55) or $souspartie == 59)and $_SESSION['user']['module']['send'] != 'oui') {
	return array('0', 'Send');
    }
    elseif($souspartie == 45 and $_SESSION['user']['module']['statistiques'] != 'oui') {
	return array('0', 'statistiques');
    }

    // Ici on sait que le client a acheté le module, on vérifie s'il a le droit de faire l'action désirée
    if($_SESSION['user']['right'] == 2) {
	return 1;
    } // C'est le compte principal du client, il a donc le droit
    elseif(array_key_exists('permissions', $_SESSION['user'])) {
	if(array_key_exists($nbpartie, $_SESSION['user']['permissions'])) {
	    return 1;
	} // L'utilisateur a le droit de tout faire dans cette section
	elseif(array_key_exists($nbpartie+99, $_SESSION['user']['permissions'])) {
	    return 0;
	} // L'utilisateur est interdit d'action dans cette section

	elseif((array_key_exists($nbpartie+59, $_SESSION['user']['permissions'])) && (($souspartie < 59) && ($souspartie > 49))) {
	    return 1;
	}
	elseif((array_key_exists($nbpartie+69, $_SESSION['user']['permissions'])) && (($souspartie < 69) && ($souspartie > 59))) {
	    return 1;
	}
	elseif(array_key_exists($ousuisje, $_SESSION['user']['permissions'])) {
	    return 1;
	} // L'utilisateur a le droit
	elseif($souspartie == 15 && array_key_exists($ousuisje+2, $_SESSION['user']['permissions'])) {
	    return 1;
	}
	elseif(($souspartie == 25 xor $souspartie == 27 xor $souspartie == 30) && array_key_exists($ousuisje+10, $_SESSION['user']['permissions'])) {
	    return 1;
	}
	elseif(($souspartie == 50 xor $souspartie == 52 xor $souspartie == 54 xor $souspartie == 60 xor $souspartie == 62 xor $souspartie == 13) && array_key_exists($ousuisje+1, $_SESSION['user']['permissions'])) {
	    return 1;
	}

	else {
	    return 0;
	} // Autre cas, donc il n'a pas le droit
    }
    else {
	return 0;
    } // Un problème avec la session car les droits ne sont pas dans la variable $_SESSION
}

/**
 * Fonction qui vérifie si on a le droit de faire quelque chose et retourne un message d'erreur le cas échéant
 * @param string $partie Le module / La partie de l'appli où nous sommes
 * @param int $souspartie Le code de l'action à effectuer
 * @param string $channel Précise si on est sur l'iphone ou sur le web
 * @return bool Retourne vrai si on a le droit. Dans le cas contraire stoppe l'appli avec un message d'erreur
 */
function aiJeLeDroit ($partie, $souspartie = 99 ,$channel = 'iphone') {
    $result = verifDroits($partie, $souspartie);
    if(is_array($result)) {
	if($channel == 'iphone') { ?>
<root><go to="waDroits"/>
    <title set="waDroits"><?php echo 'Droits insuffisants'; ?></title>
    <part><destination mode="replace" zone="waDroits" create="true"/>
        <data><![CDATA[ <?php echo generalView::droits($result[1]);?> ]]></data>
    </part>
</root>
	    <?php }
	else {
	    if(!headers_sent()) {
		header("Location:../Bureau.php?module=".$result[1]);
		exit;
	    }
	    echo '<div class="important">Vous n\'avez pas acheté le module pour effectuer cette opération.<br/>
				Pour y accéder vous avez besoin du module <b>'.$result[1].'</b></div>';
	}
	exit;
    }
    elseif(!$result) {
	if($channel == 'iphone') { ?>
<root><go to="waDroits"/>
    <title set="waDroits"><?php echo 'Droits insuffisants'; ?></title>
    <part><destination mode="replace" zone="waDroits" create="true"/>
        <data><![CDATA[ <?php echo generalView::droitsAdmin();?> ]]></data>
    </part>
</root>
	    <?php }
	else {
	    if(!headers_sent()) {
		header("Location:../Bureau.php?droits=absent");
		exit;
	    }?>
<span class="important" style="text-align: center;">Vous n'avez pas les droits suffisants pour effectuer cette opération</span>
	    <?php }
	exit;
    }
    else {
	return 1;
    }
}

?>
