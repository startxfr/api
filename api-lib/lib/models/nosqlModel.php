<?php

/**
 * This model is used to manipulate data stored in a nosql Store.
 *
 * @class    nosqlModel
 * @author   Dev Team <dev@startx.fr>
 * @see      defaultModel
 * @see      nosqlModelResource
 * @link     https://github.com/startxfr/sxapi/wiki/Model
 */
class nosqlModel extends defaultModel implements IModel {

    public function __construct($config = array(), $storageID = 'default') {
        parent::__construct($config, $storageID);
        $api = Api::getInstance();
        if (!is_object($this->getStore()) or get_class($this->getStore()) != 'nosqlStore')
            throw new ModelException("Could not " . __FUNCTION__ . " " . get_class($this) . " because '" . $storageID . "' store is not of type nosqlStore", 508);
        if ($this->getConfig('collection', '') == '') {
            $api->logError(506, get_class($this) . " resource config should contain the 'collection' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ModelException(get_class($this) . " resource config should contain the 'collection' attribute");
        }
    }

    public function readOne($id) {
        $search = array($this->getConfig('id_key', '_id') => new MongoId($id));
        $data = $this->getStore()->readOne($this->getConfig('collection'), $search);
        if (is_array($data) and count($data) > 0)
            return $this->filterResult($data, false);
        else
            return array();
    }

    public function readDetail($criteria = array(), $order = array(), $from = 0, $max = 30) {
        $result = $this->getStore()->read($this->getConfig('collection'), $criteria, $order, $from, $max);
        if (is_null($result))
            return array();
        else
            return $this->filterResults(iterator_to_array($result,false), false);
    }

    public function readCount($criteria = array()) {
        return $this->getStore()->readCount($this->getConfig('collection'), $criteria);
    }

    public function read($criteria = array(), $order = array(), $from = 0, $max = 30) {
        $result = $this->getStore()->read($this->getConfig('collection'), $criteria, $order, $from, $max);
        if (is_null($result))
            return array();
        else
            return $this->filterResults(iterator_to_array($result,false));
    }

    public function create($data) {
        $data[$this->getConfig('id_key', '_id')] = new MongoId();
        $result = $this->getStore()->create($this->getConfig('collection'), $this->bindVars($data));
        return $result;
    }

    public function update($id, $data) {
        unset($data[$this->getConfig('id_key', '_id')]);
        $result = $this->getStore()->update($this->getConfig('collection'), $this->getConfig('id_key', '_id'), new MongoId($id), $this->bindVars($data));
        return $result;
    }

    public function delete($id) {
        $result = $this->getStore()->delete($this->getConfig('collection'), $this->getConfig('id_key', '_id'), new MongoId($id));
        return $result;
    }

    protected function filterResults($results, $outputFilter = true) {
        if (is_array($results))
            foreach ($results as $k => $v) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $this->getConfig('id_key', '_id') and is_object($v2) and get_class($v2) == 'MongoId')
                        $results[$k][$k2] = (string) $v2;
                    elseif (is_object($v2) and get_class($v2) == 'MongoDate')
                        $results[$k][$k2] = date('Y-m-d H:i:s', (string) $v2->sec);
                }
            }
        return parent::filterResults($results, $outputFilter);
    }

    protected function filterResult($result, $outputFilter = true) {
        foreach ($result as $k => $v) {
            if ($k == $this->getConfig('id_key', '_id') and is_object($v) and get_class($v) == 'MongoId')
                $result[$k] = (string) $v;
            elseif (is_object($v) and get_class($v) == 'MongoDate')
                $result[$k] = date('Y-m-d H:i:s', (string) $v->sec);
        }
        return parent::filterResult($result, $outputFilter);
    }

}

?>