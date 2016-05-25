<?php

/**
 * This resource is used to interact (read - write) with nosql data, recorded in a store.
 * Data are returned to the client using the output method.
 *
 * @package  SXAPI.Resource.Model
 * @author   Dev Team <dev@startx.fr>
 * @see      affaireModelResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class affaireSxaStartxResource extends defaultSxaStartxResource implements IResource {

    static public $ConfDesc = '{
        "class_name":"affaireSxaStartxResource",
        "desc":"get list of deals available for the SXA project",
        "properties": [
        
        ]
      }';
    
    
    public function getById($id, $filter = "*") {
        $r = $this->getStorage()->execQuery('SELECT ' . $filter . ' FROM affaire
			LEFT JOIN contact ON contact.id_cont = affaire.contact_aff
			LEFT JOIN ref_statusaffaire ON affaire.status_aff = ref_statusaffaire.id_staff
			LEFT JOIN ref_typeproj ON affaire.typeproj_aff = ref_typeproj.id_typro
			, entreprise
			LEFT JOIN ref_activite ON entreprise.activite_ent = ref_activite.id_act
			LEFT JOIN ref_typeentreprise ON ref_typeentreprise.id_tyent = entreprise.type_ent
			LEFT JOIN ref_pays ON entreprise.pays_ent = ref_pays.id_pays
			WHERE entreprise_aff = id_ent
			AND id_aff = \'' . $id . '\'
			GROUP BY affaire.id_aff
			ORDER BY detect_aff ASC');
        if(!is_array($r)) {
            return array();
        }
        return $r[0];
    }

}
