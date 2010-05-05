<?php

/**
 * Classe qui gère le module de recherche global de l'iphone
 * La classe s'occupe de la liaison avec la Base de données
 *
 * @author Nicolas Mannocci
 * @version 1.0
 */
class searchModel extends GeneralModel {
    /**
     * Constructeur de la classe qui instancie les variables
     */
    public function __construct() {
	$this->sqlTableName = '';
	$this->sqlIdField = '';
	$this->availableFields = array();
	parent::__construct();
    }
    /**
     * Méthode qui recherche des entreprises
     * @param <type> $search Ce que l'on cherche
     * @param <type> $from L'index du premier résultat
     * @param <type> $limit Le nombre maximum de résultat(s) souhaité
     * @param <type> $count Précise si on veut les résultat ou le nombre de résultat
     * @return array Les résultats.
     */
    public function getDataFromEntreprise($search,$from = 0, $limit = 5, $count = 'no') {
	$queryBody = "FROM entreprise
				  LEFT JOIN ref_pays ON ref_pays.id_pays = entreprise.pays_ent
				  WHERE nom_ent LIKE '%$search%' OR ville_ent LIKE '%$search%'";
	if($count != 'no')
	    $this->makeRequeteFree("SELECT COUNT(*) $queryBody");
	else  $this->makeRequeteFree("SELECT * $queryBody ORDER BY nom_ent ASC LIMIT $from , $limit");
	return $this->process2();
    }

    /**
     * Méthode qui recherche des contacts
     * @param <type> $search Ce que l'on cherche
     * @param <type> $from L'index du premier résultat
     * @param <type> $limit Le nombre maximum de résultat(s) souhaité
     * @param <type> $count Précise si on veut les résultat ou le nombre de résultat
     * @return array Les résultats.
     */
    public function getDataFromContact($search,$from = 0, $limit = 5, $count = 'no') {
	$queryBody = "FROM contact
				  LEFT JOIN ref_fonction ON ref_fonction.id_fct = contact.fonction_cont
				  WHERE nom_cont LIKE '%$search%' OR prenom_cont LIKE '%$search%'  OR nom_fct LIKE '%$search%'";
	if($count != 'no')
	    $this->makeRequeteFree("SELECT COUNT(*) $queryBody");
	else  $this->makeRequeteFree("SELECT * $queryBody ORDER BY nom_cont ASC LIMIT $from , $limit");
	return $this->process2();
    }

    /**
     * Méthode qui recherche des affaires
     * @param <type> $search Ce que l'on cherche
     * @param <type> $from L'index du premier résultat
     * @param <type> $limit Le nombre maximum de résultat(s) souhaité
     * @param <type> $count Précise si on veut les résultat ou le nombre de résultat
     * @return array Les résultats.
     */
    public function getDataFromAffaire($search, $from = 0, $limit = 5, $count = 'no', $plus = '') {
	$qTag = " LIKE '%".$search."%' ";
	$queryBody = "FROM affaire
				  LEFT JOIN entreprise ON entreprise.id_ent = affaire.entreprise_aff
				  LEFT JOIN contact ON contact.id_cont = affaire.contact_aff
				  WHERE (id_aff $qTag OR titre_aff $qTag) ".$plus;
	if($count != 'no')
	    $this->makeRequeteFree("SELECT COUNT(*) $queryBody $plus ORDER BY id_aff ASC ");
	else  $this->makeRequeteFree("SELECT * $queryBody $plus ORDER BY id_aff ASC LIMIT $from , $limit");
	return $this->process2();
    }

    /**
     * Méthode qui recherche des devis
     * @param <type> $search Ce que l'on cherche
     * @param <type> $from L'index du premier résultat
     * @param <type> $limit Le nombre maximum de résultat(s) souhaité
     * @param <type> $count Précise si on veut les résultat ou le nombre de résultat
     * @return array Les résultats.
     */
    public function getDataFromDevis($search, $from = 0, $limit = 5, $count = 'no', $plus = '') {
	$qTag = " LIKE '%".$search."%' ";
	$queryBody = "FROM devis
				  LEFT JOIN affaire ON affaire.id_aff = devis.affaire_dev
				  LEFT JOIN entreprise ON entreprise.id_ent = affaire.entreprise_aff
				  LEFT JOIN contact ON contact.id_cont = affaire.contact_aff
				  WHERE (id_dev $qTag OR titre_dev $qTag) $plus ";
	if($count != 'no')
	    $this->makeRequeteFree("SELECT COUNT(*) $queryBody $plus ORDER BY id_dev ASC ");
	else  $this->makeRequeteFree("SELECT * $queryBody $plus ORDER BY id_dev ASC LIMIT $from , $limit");
	return $this->process2();
    }

    /**
     * Méthode qui recherche des commandes
     * @param <type> $search Ce que l'on cherche
     * @param <type> $from L'index du premier résultat
     * @param <type> $limit Le nombre maximum de résultat(s) souhaité
     * @param <type> $count Précise si on veut les résultat ou le nombre de résultat
     * @return array Les résultats.
     */
    public function getDataFromCommande($search, $from = 0, $limit = 5, $count = 'no', $plus = '') {
	$qTag = " LIKE '%".$search."%' ";
	$queryBody = "FROM commande
				  LEFT JOIN entreprise ON entreprise.id_ent = commande.entreprise_cmd
				  LEFT JOIN contact ON contact.id_cont = commande.contact_cmd
				  WHERE (id_cmd $qTag OR titre_cmd $qTag) $plus ";
	if($count != 'no')
	    $this->makeRequeteFree("SELECT COUNT(*) $queryBody $plus ORDER BY id_cmd ASC ");
	else  $this->makeRequeteFree("SELECT * $queryBody $plus ORDER BY id_cmd ASC LIMIT $from , $limit");
	return $this->process2();
    }

    /**
     * Méthode qui recherche des factures
     * @param <type> $search Ce que l'on cherche
     * @param <type> $from L'index du premier résultat
     * @param <type> $limit Le nombre maximum de résultat(s) souhaité
     * @param <type> $count Précise si on veut les résultat ou le nombre de résultat
     * @return array Les résultats.
     */
    public function getDataFromFacture($search, $from = 0, $limit = 5, $count = 'no', $plus = '') {
	$qTag = " LIKE '%".$search."%' ";
	$queryBody = "FROM facture
				  LEFT JOIN entreprise ON entreprise.id_ent = facture.entreprise_fact
				  LEFT JOIN contact ON contact.id_cont = facture.contact_fact
				  WHERE (id_fact = '$search' OR titre_fact $qTag OR commande_fact $qTag) $plus ";
	if($count != 'no')
	    $this->makeRequeteFree("SELECT COUNT(*) $queryBody $plus ORDER BY id_fact ASC ");
	else  $this->makeRequeteFree("SELECT * $queryBody $plus ORDER BY id_fact ASC LIMIT $from , $limit");
	return $this->process2();
    }

    /**
     * Méthode qui recherche des produits
     * @param <type> $search Ce que l'on cherche
     * @param <type> $from L'index du premier résultat
     * @param <type> $limit Le nombre maximum de résultat(s) souhaité
     * @param <type> $count Précise si on veut les résultat ou le nombre de résultat
     * @return array Les résultats.
     */
    public function getDataFromProduit($search, $from = 0, $limit = 5, $count = 'no') {
	$qTag = " LIKE '%".$search."%' ";
	$queryBody = "FROM produit
				 WHERE id_prod = '$search' OR nom_prod $qTag ";
	if($count != 'no')
	    $this->makeRequeteFree("SELECT COUNT(*) $queryBody ORDER BY id_prod ASC ");
	else  $this->makeRequeteFree("SELECT * $queryBody ORDER BY id_prod ASC LIMIT $from , $limit");
	return $this->process2();
    }
}

?>