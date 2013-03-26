<?php

/**
 * This class deliver default informations (homepage) and provide default ressource methods
 * availables url are
 * - http://api.startx.fr/v1/       -> informations about the default ressource
 */
abstract class defaultModelRessource extends defaultRessource implements IRessource {

    protected $model;

    public function init() {
        parent::init();
        $api = Api::getInstance();
        if ($this->getConfig('model', '') == '') {
            $api->logError(906, get_class($this) . " ressource config should contain the 'model' attribute", $this->getRessourceTrace(__FUNCTION__, false));
            throw new RessourceException(get_class($this) . " ressource config should contain the 'model' attribute");
        }
        $this->model = $api->getModel($this->getConfig('model'));
        if (is_null($this->getConfig('search_params')))
            $this->setConfig('search_params', array());
        elseif (is_string($this->getConfig('search_params')))
            $this->setConfig('search_params', explode(',', $this->getConfig('search_params')));
        return $this;
    }

    public function getModel() {
        return $this->model;
    }

    public function filterSearchParams($params) {
        $search = array();
        if ($this->getConfig('search_params') == '*' or $this->getConfig('search_params') == 'all' or is_null($this->getConfig('search_params')))
            return $params;
        if (is_string($this->getConfig('search_params')))
            $this->setConfig('search_params', explode(',', $this->getConfig('search_params')));
        foreach ($this->getConfig('search_params', array()) as $key)
            if ($params[$key] != null)
                $search[$key] = $params[$key];
        return $search;
    }

}

?>
