<?php

/**
 * Class for recording session using filesystem
 *
 * @author dev@startx.fr
 */
class RhformproductModel extends DefaultModel implements IModel {

    protected $table = 'rhform_product';
    protected $idkey = 'product_id';
    protected $keys = array('product_id', 'product_signature', 'product_reference', 'product_price', 'product_pricecurrency', 'product_desc', 'product_content', 'product_duree', 'product_family', 'product_actif');

}

?>