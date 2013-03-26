<?php

/**
 * Class for recording session using filesystem
 *
 * @author dev@startx.fr
 */
class RhformdatesModel extends DefaultModel implements IModel {

    protected $table = 'rhform_dates';
    protected $idkey = 'date_id';
    protected $keys = array('date_id', 'product_reference', 'date_city', 'date_begin', 'date_end', 'date_courselang', 'date_materiallang', 'date_actif');

}

?>