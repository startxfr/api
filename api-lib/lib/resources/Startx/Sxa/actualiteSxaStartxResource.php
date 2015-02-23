<?php

/**
 * This resource is used to interact (read - write) with nosql data, recorded in a store.
 * Data are returned to the client using the output method.
 *
 * @package  SXAPI.Resource.Model
 * @author   Dev Team <dev@startx.fr>
 * @see      actualiteModelResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class actualiteSxaStartxResource extends defaultSxaStartxResource implements IResource {

    static public $ConfDesc = '{
        "class_name":"actualiteSxaStartxResource",
        "desc":"get list of deals available for the SXA project",
        "properties": [
        
        ]
      }';

    public function getDataFromID($id, $filter = "*") {
        $r = $this->getStorage()->execQuery('SELECT ' . $filter . ' FROM actualite
		LEFT JOIN entreprise ON entreprise.id_ent = actualite.id_ent
		LEFT JOIN contact ON contact.id_cont = actualite.id_cont
		LEFT JOIN affaire ON affaire.id_aff = actualite.id_aff
		LEFT JOIN devis ON devis.id_dev = actualite.id_dev
		LEFT JOIN commande ON commande.id_cmd = actualite.id_cmd
		LEFT JOIN facture ON facture.id_fact = actualite.id_fact
		LEFT JOIN user ON user.login = actualite.user
		WHERE actualite.id = \'' . $id . '\'
                ORDER BY id ASC');
        if(!is_array($r)) {
            return array();
        }
        return $r[0];
    }

    public function insert($data) {
        if(!array_key_exists('date', $data)) {
            $data['date'] = date('Y-m-d');
        }
        if(!array_key_exists('type', $data)) {
            $data['type'] = 'general';
        }
        if(!array_key_exists('titre', $data)) {
            $data['titre'] = 'sans titre';
        }
        if(!array_key_exists('isPublic', $data)) {
            $data['isPublic'] = '0';
        }
        if(!array_key_exists('isPublieForClient', $data)) {
            $data['isPublieForClient'] = '0';
        }
        if(!array_key_exists('isVisibleFilActu', $data)) {
            $data['isVisibleFilActu'] = '1';
        }
        return $this->getStorage()->create($this->getConfig('dataset'), $data);
    }

    public function update($id, $data = array()) {
        return $this->getStorage()->update($this->getConfig('dataset'), $this->getConfig('id_key', "_id"), $id, $data);
    }

    public function delete($id) {
        return $this->getStorage()->delete($this->getConfig('dataset'), $this->getConfig('id_key', "_id"), $id);
    }

}
