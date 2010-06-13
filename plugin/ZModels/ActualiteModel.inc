<?php
loadPlugin('ZModels/GeneralModel');
/**
 * Class d'accès aux information d'un particulier
 * @author Nicolas Mannocci
 * @version 1.0
 */
class actualiteModel extends GeneralModel {


    /**
     * Constructeur de la classe qui instancie les variables
     */
    public function __construct() {
	$this->sqlTableName = 'actualite';
	$this->sqlIdField = 'id';
	$this->availableFields = array(
		'id','date','user','type',
		'titre','desc','id_ent',
		'id_cont','id_aff','status_dev',
		'id_dev','id_cmd','id_fact', 'id_factfourn',
		'status_aff','status_cmd','status_fact', 'status_factfourn');
	parent::__construct();
    }

    /**
     * Fonction de recherche des données à partir d'un type de document
     * @param string $type Le type d'actualités désiré
     * @param int $from Le premier résultat (optionnel, défaut 0)
     * @param int $limit Le dernier résultat (optionnel, defaut 5)
     * @param string $plus Permet d'ajouter d'autres restrictions à la recherche. (optionnel)
     * @return array Le résultat
     */
    public function getData($type,$from = 0, $limit = 5, $plus = '') {
	$sqlAdd = ($type != '') ? 'WHERE type LIKE \'%'.$type.'%\' '.$plus : 'WHERE id is not null '.$plus;
	$this->makeRequeteFree( "SELECT * FROM actualite
		".$sqlAdd."ORDER BY date DESC, id DESC LIMIT $from , $limit ");
	return $this->process2();
    }

    /**
     * Fonction de recherche des actualités liées à une affaire
     * @param string $affaire L'id de l'affaire dont on cherche les actualités
     * @param string $plus D'éventuelles restrictions à la recherche
     * @return array Le résultat
     */
    public function getData4Affaire($affaire, $plus = '') {
	$this->makeRequeteFree('SELECT * FROM actualite WHERE id_aff = \''.$affaire.' '.$plus."' ORDER BY date DESC, id DESC");
	return $this->process2();
    }

    /**
     * Fonction de recherche des actualités liées à un devis
     * @param string $devis L'id du devis
     * @param string $plus D'éventuelles restrictions à la recherche
     * @return array Le résultat
     */
    public function getData4Devis($devis, $plus = '') {
	$this->makeRequeteFree('SELECT * FROM actualite WHERE id_dev = \''.$devis.' '.$plus."' ORDER BY date DESC, id DESC");
	return $this->process2();
    }
    /**
     * Fonction de recherche des actualités liées à une commande
     * @param string $commande L'ide de la commande
     * @param string $plus D'éventuelles restrictions à la recherche
     * @return array Le résultat
     */
    public function getData4Commande($commande, $plus = '') {
	$this->makeRequeteFree('SELECT * FROM actualite WHERE id_cmd = \''.$commande.' '.$plus."' ORDER BY date DESC, id DESC");
	return $this->process2();
    }
    /**
     * Fonction de recherche des actualités liées à une facture
     * @param string $facture L'id de la facture
     * @param string $plus D'éventuelles restrictions à la recherche
     * @return array Le résultat
     */
    public function getData4Facture($facture, $plus = '') {
	$this->makeRequeteFree('SELECT * FROM actualite WHERE id_fact = \''.$facture.' '.$plus."' ORDER BY date DESC, id DESC");
	return $this->process2();
    }

    /**
     * Fonction de recherche de tout à propos d'une actualité
     * @param int $id L'id d'une actualité
     * @return array Le résultat
     */
    public function getDataFromID($id) {
	$this->makeRequeteFree("SELECT * FROM actualite
		LEFT JOIN entreprise ON entreprise.id_ent = actualite.id_ent
		LEFT JOIN contact ON contact.id_cont = actualite.id_cont
		LEFT JOIN affaire ON affaire.id_aff = actualite.id_aff
		LEFT JOIN devis ON devis.id_dev = actualite.id_dev
		LEFT JOIN commande ON commande.id_cmd = actualite.id_cmd
		LEFT JOIN facture ON facture.id_fact = actualite.id_fact
		LEFT JOIN user ON user.login = actualite.user
		WHERE actualite.id = '".$id."'");
	return $this->process2();
    }

    /**
     * Méthode d'insertion d'une actualité dans la BDD
     * @param array $data Les données à insérer
     * @return array Précise si la requète s'est bien passée
     */
    public function insert($data) {
	$id = $this->getLastID();
	$id++;
	$data['id'] = $id;
	$data['date'] = date('Y-m-d H:i:s');
	$data['user'] = $_SESSION['user']['id'];
	$data['type'] = (!array_key_exists('type', $data)) ? 'general' : $data['type'];
	$this->makeRequeteInsert('actualite', $this->bindVarActualite($data));
	$result = $this->process2();

	return $result;
    }

    /**
     * Méthode qui met à jour une actualité dans la base
     * @param int $id L'id de l'actualité à mettre à jour
     * @param array $data Les données à mettre à jour
     * @return array Indique si la requète s'est bien passée.
     */
    public function update($id, $data='') {
	$this->makeRequeteUpdate('actualite','id',$id, $this->bindVarActualite($data));
	$result = $this->process2();

	return $result;
    }

    /**
     * Fonction qui filtre les clefs d'un tableau et ne retourne que les clefs corespondant à la table actualite
     * @param <array> $data tableau avec les données brut
     * @return <array> données filtrées selon la propriété $this->availableFields
     */
    public function bindVarActualite($data) {
	return $this->bindVar($data,$this->availableFields);
    }
}
?>
