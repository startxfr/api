<?php

/**
 * Class for logging detail into a file
 *
 * @author dev@startx.fr
 */
class WsdlStore extends DefaultStore implements IStorage {

    protected $soapHandler = null;
    protected $config = array();

    public function __construct($configXml) {
        parent::__construct($configXml);
        $this->init();
    }

    function __destruct() {
        $this->disconnect();
    }

    public function init() {
             foreach ($this->getConfigs() as $key => $value) {
                $explode = explode('-', $key, 2);
                if ($explode[0] == 'param')
                    $this->setConfig('param',$explode[1],(string) $value);
            }
            if ($this->getConfig('dateformat') == '')
                $this->setConfig('dateformat','Y-m-d');
            if ($this->getConfig('wsdl') == '')
                throw new StoreException("your storage[type=wsdl] tag should contain the 'wsdl' attribute");
            if ($this->getConfig('method') == '')
                throw new StoreException("your storage[type=wsdl] tag should contain the 'method' attribute");
            if ($this->getConfig('result') == '')
                throw new StoreException("your storage[type=wsdl] tag should contain the 'result' attribute");
        return $this;
    }

    public function connect() {
        try {
            $this->soapHandler = new SoapClient($this->getConfig('wsdl'));
        } catch (Exception $e) {
            throw new StoreException('we could not open soap connexion with ' . $this->getConfig('wsdl') . ' because of ' . $e->getMessage());
        }
        return $this;
    }

    public function reconnect() {
        $this->disconnect()->connect();
        return $this;
    }

    public function disconnect() {
        unset($this->soapHandler);
        return $this;
    }

    public function get() {
        $result = $this->callMethod($this->getConfig('method'), $this->getConfig('param'));
        return $result;
    }

    public function set($datas) {
        $result = array();
        foreach ($datas as $key => $row) {
            $params = array_merge($this->getConfig('param'), (array) $row);
            $result[] = $this->callMethod($this->getConfig('method'), $params);
        }
        return $result;
    }

    protected function callMethod($method, $params) {
        try {
            $root = $this->getConfig('result');
            $result = $this->soapHandler->__soapCall($method, array($params));
            $out = StorageWsdl::convertObj2Array($result->$root);
            return $out;
        } catch (Exception $e) {
            throw new StoreException('we could not invoke method ' . $method . ' soap on ' . $this->getConfig('wsdl') . ' because of ' . $e->getMessage());
        }
    }

    static function convertObj2Array($data) {
        if (is_object($data))
            $data = get_object_vars($data);
        return is_array($data) ? array_map('StorageWsdl::convertObj2Array', $data) : $data;
    }

}

?>