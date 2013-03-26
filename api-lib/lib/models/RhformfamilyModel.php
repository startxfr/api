<?php

/**
 * Class for recording session using filesystem
 *
 * @author dev@startx.fr
 */
class RhformfamilyModel extends DefaultModel implements IModel {

    protected $table = 'rhform_family';
    protected $idkey = 'family_id';
    protected $keys = array('family_id', 'family_name', 'family_actif', 'family_color');


    public function bindFilter($filters) {
	if (is_array($filters))
	    foreach ($filters as $k => $v) {
		if (is_array($v)) {
                    if ($v['property'] == "family_name")
                        $filters[$k]['operator'] = "like";

                }

            }
	return $filters;
    }




}

?>