<?php

/**
 * This resource is used to interact (read - write) with nosql data, recorded in a store.
 * Data are returned to the client using the output method.
 *
 * @package  SXAPI.Resource.Model
 * @author   Dev Team <dev@startx.fr>
 * @see      devisModelResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class devisSxaStartxResource extends defaultSxaStartxResource implements IResource {

    static public $ConfDesc = '{
        "class_name":"devisSxaStartxResource",
        "desc":"get list of deals available for the SXA project",
        "properties": [
        
        ]
      }';

    public function getDataFromID($id, $filter = "*") {
        $r = $this->getStorage()->execQuery('SELECT  FROM devis
		SELECT ' . $filter . ', 
                    c2.id_cont AS id_achat, 
                    c2.civ_cont AS civ_achat, 
                    c2.prenom_cont AS prenom_achat, 
                    c2.nom_cont AS nom_achat, 
                    c2.mail_cont AS mail_achat, 
                    c2.fax_cont AS fax_achat
		FROM devis
		LEFT JOIN entreprise ON entreprise.id_ent = devis.entreprise_dev
		LEFT JOIN ref_typeentreprise te ON te.id_tyent = entreprise.type_ent
		LEFT JOIN contact c2 ON c2.id_cont = devis.contact_achat_dev
		LEFT JOIN contact c1 ON c1.id_cont = devis.contact_dev
		LEFT JOIN affaire ON affaire.id_aff = devis.affaire_dev
		LEFT JOIN ref_statusdevis rsd ON rsd.id_stdev = devis.status_dev
		LEFT JOIN ref_pays rfp ON rfp.id_pays = devis.paysdelivery_dev
		LEFT JOIN user ON user.login = devis.commercial_dev
		LEFT JOIN commande ON commande.devis_cmd = devis.id_dev
		LEFT JOIN facture f ON f.commande_fact = commande.id_cmd
		WHERE id_dev = \'' . $id . '\'');
        if(!is_array($r)) {
            return array();
        }
        return $r[0];
    }

    public function getProduitsFromID($id) {
        $r = $this->getStorage()->execQuery("SELECT *,
			(select prixF*(1-remiseF/100) as PFourn from produit_fournisseur where produit_id = id_prod order by PFourn ASC limit 0,1) as PF 
			FROM devis_produit
			LEFT JOIN produit ON produit.id_prod = devis_produit.id_produit
			LEFT JOIN ref_prodfamille ON ref_prodfamille.id_prodfam = produit.famille_prod
			WHERE id_devis = '" . trim($id) . "' ;");
        if(!is_array($r)) {
            return array();
        }
        return $r;
    }

    public function createId($id) {
        $res = $this->getStorage()->execQuery("SELECT COUNT(id_dev) AS nb FROM `devis` WHERE id_dev LIKE '%" . $id . "%' ");
        if(!is_array($res)) {
            $res[0]['nb'] = 0;
        }
        $lastid = $res[0]['nb'];
        if($lastid > 0) {
            $lastid++;
            if($lastid < 10)
                $lastid = "-0" . $lastid;
            else
                $lastid = "-" . $lastid;
        }
        else
            $lastid = "-01";
        $id .= $lastid;
        return $id;
    }

    public function insert($data) {
        if(!array_key_exists('datemodif_dev', $data)) {
            $data['datemodif_dev'] = date('Y-m-d');
        }
        if(!array_key_exists('daterecord_dev', $data)) {
            $data['daterecord_dev'] = date('Y-m-d');
        }
        if(!array_key_exists('status_dev', $data)) {
            $data['status_dev'] = '1';
        }
        if(!array_key_exists('affaire_dev', $data)) {
            throw new ResourceException(" could not insert devis because property 'affaire_dev' is missing", 88);
        }
        if(!array_key_exists('contact_dev', $data)) {
            throw new ResourceException(" could not insert devis because property 'contact_dev' is missing", 88);
        }
        if(!array_key_exists('id_dev', $data)) {
            throw new ResourceException(" could not insert devis because property 'id_dev' is missing", 88);
        }
        return $this->getStorage()->create($this->getConfig('dataset'), $data);
    }
    
    
    public function insertProduit($data) {
        if(!array_key_exists('quantite', $data)) {
            $data['quantite'] = 1;
        }
        if(!array_key_exists('remise', $data)) {
            $data['remise'] = 0;
        }
        if(!array_key_exists('prix', $data)) {
            $data['prix'] = 0;
        }
        return $this->getStorage()->create('devis_produit', $data);
    }

}
