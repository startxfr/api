<?php
/*#########################################################################
#
#   name :       ZunoSxa
#   desc :       Plugin avec toutes les évolutions spécifiques à l'instance Sxa de ZUNO
#   categorie :  client module
#   ID :  	 $Id: ZunoSxa.inc 3567 2010-01-10 19:36:00Z cl $
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

// Remplacement par surcharge de la classe ProduitView
class ProduitView extends ProduitViewOriginal {




    /**
     * Fonction searchForm qui génère le formulaire de recherche d'un produit
     * @param $datas	D'éventuels champs pré-remplis
     * @return $form	Le formulaire
     */
    public function searchForm($datas,$option = 'result') {
        $sql = new ProduitModel();
	$form = parent::searchForm($datas,$option);
	$fieldset = new ZunoFieldset('Informations Redhat');
        $fieldset->ligneSelect('Archi', 'archi_prod', $datas['data']['archi_prod'], $sql->getRedhatArchi(), true);
        $fieldset->ligneSelect('Contrat', 'contrat_prod', $datas['data']['contrat_prod'], $sql->getRedhatContrat(), true);
        $fieldset->ligneSelect('Famille Redhat', 'familleredhat_prod', $datas['data']['familleredhat_prod'], $sql->getRedhatFamille(), true);
        $form->newBlock($fieldset->generateFieldset(), '50');
        return $form;
    }

}


// Remplacement par surcharge de la classe produitModel
class produitModel extends produitModelOriginal {



    /**
     * Méthode qui récupère les périodes de renouvellement de la base
     * @return array Les périodes de renouvellement
     */
    public function getRedhatArchi() {
        $this->makeRequeteFree("SELECT * from redhat_archi order by id_arch ASC ");
        $rs = $this->process2();
        if($rs[0]) {
            $rs=$rs[1];
            foreach($rs as $k => $v)
                $list[$v['id_arch']] = $v['nom_arch'];
            return $list;
        }
    }

    /**
     * Méthode qui récupère les périodes de renouvellement de la base
     * @return array Les périodes de renouvellement
     */
    public function getRedhatContrat() {
        $this->makeRequeteFree("SELECT * from redhat_contrat order by id_cont ASC ");
        $rs = $this->process2();
        if($rs[0]) {
            $rs=$rs[1];
            foreach($rs as $k => $v)
                $list[$v['id_cont']] = $v['nom_cont'];
            return $list;
        }
    }

    /**
     * Méthode qui récupère les périodes de renouvellement de la base
     * @return array Les périodes de renouvellement
     */
    public function getRedhatFamille() {
        $this->makeRequeteFree("SELECT DISTINCT familleredhat_prod FROM produit order by familleredhat_prod ASC ");
        $rs = $this->process2();
        if($rs[0]) {
            $rs=$rs[1];
            foreach($rs as $k => $v)
                $list[$v['familleredhat_prod']] = $v['familleredhat_prod'];
            return $list;
        }
    }



}



function sxaFilterVar4SearchProduit($formIn,$dataOut) {
    if($formIn['archi_prod'] != '')
        $dataOut['archi_prod'] = $formIn['archi_prod'];
    if($formIn['contrat_prod'] != '')
        $dataOut['contrat_prod'] = $formIn['contrat_prod'];
    if($formIn['familleredhat_prod'] != '')
        $dataOut['familleredhat_prod'] = $formIn['familleredhat_prod'];

}

?>