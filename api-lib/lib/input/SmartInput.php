<?php

/**
 * Class used to present to HTTP context. It work with the .htaccess file and extract virtual url fragments.
 * Smart side reside in the fact that this input will analyse the full HTTP context and extract the following elements:
 * <ul>
 * <li>protocol : The used protocol (could be http:// or https://) </li>
 * <li>host : The hostname</li>
 * <li>root : The root path fragment to the php script file</li>
 * <li>path : The API tree path to the resource (described as a path string)</li>
 * <li>elements : Exploded version of the path. Describe requested resources</li>
 * <li>method : the requested method used (could be GET, POST, PUT, PATCH, DELETE or TRACE)</li>
 * <li>format : The output format guessed according to the HTTP request content type. If a format param is received, it force output param. (ex: ?format=json force json output)</li>
 * <li>data : Incomings data. According to the HTTP request, could use the request body or the GET method</li>
 * </ul>
 *
 * @package  SXAPI.Input
 * @author   Dev Team <dev@startx.fr>
 * @see      DefaultInput
 * @link     https://github.com/startxfr/sxapi/wiki/Inputs
 */
class SmartInput extends DefaultInput implements IInput {

    /**
     * protocol used to call this webservice
     * @var string
     */
    private $protocol;

    /**
     * the host of this request
     * @var string
     */
    private $host;

    /**
     * the root path of this request
     * @var string
     */
    private $root;

    /**
     * the path represented by this request
     * @var string
     */
    private $path;

    /**
     * list of elements contained in the request path
     * @var array
     */
    private $elements;

    /**
     * method used to call this webservice
     * @var string
     */
    private $method;

    /**
     * method used to call this webservice
     * @var string
     */
    protected $data;

    /**
     * method used to call this webservice
     * @var string
     */
    public $format;

    /**
     * construct the smart input object
     *
     * @param array configuration of this object
     * @see Configurable
     * @return void
     */
    public function __construct($config) {
        parent::__construct($config);
        $this->method = strtolower($_SERVER['REQUEST_METHOD']);
        $this->loadPathInfo();
        $this->loadIncomingParams();
    }

    /**
     * load informations about the request path
     */
    private function loadPathInfo() {
        $path = "/";
        // load host
        $this->host = $_SERVER['HTTP_HOST'];
        // load protocol
        if (array_key_exists('HTTPS', $_SERVER) and $_SERVER['HTTPS'] == 'on')
            $this->protocol = "https://";
        else
            $this->protocol = "http://";
        // use of mode_rewrite (no index.php in url)
        if (array_key_exists('REDIRECT_URL', $_SERVER)) {
            $root = dirname($_SERVER['PHP_SELF']) . DS;
            $root = str_replace(DS . DS, DS, $root);
            $path = str_replace($root, '', $_SERVER['REDIRECT_URL']);
        }
        // or url contain script filenames
        else {
            $root = dirname($_SERVER['SCRIPT_NAME']) . DS;
            $root = str_replace(DS . DS, DS, $root);
            $path = str_replace($root, '', $_SERVER['PHP_SELF']);
        }
        $elements = explode(DS, $path);
        if ($elements[0] == 'index.php')
            $elements[0] = '/';
        if ($elements[count($elements) - 1] == '')
            array_pop($elements);
        if ($elements[0] != '/')
            array_unshift($elements, '/');
        $this->root = $root;
        $this->path = $path;
        $this->elements = $elements;
        Api::logDebug(201, "Loaded path '" . $this->path . "' with method " . $this->method . " method in '" . __CLASS__ . "'", array('root' => $this->root, 'path' => $this->getPath(), 'elements' => $this->elements));
    }

    /**
     * load informations about the request parameters
     */
    private function loadIncomingParams() {
        $parameters = array();
        // first of all, pull the GET vars
        if (isset($_SERVER['QUERY_STRING']))
            parse_str($_SERVER['QUERY_STRING'], $parameters);
        // now how about PUT/POST bodies? These override what we got from GET
        $body = file_get_contents("php://input");
        $content_type = false;
        if (isset($_SERVER['CONTENT_TYPE']))
            $content_type = $_SERVER['CONTENT_TYPE'];
        switch ($content_type) {
            case "application/json":
                $this->format = "json";
                $body_params = json_decode($body);
                if ($body_params)
                    foreach ($body_params as $param_name => $param_value)
                        $parameters[$param_name] = $param_value;
                break;
            case "application/x-www-form-urlencoded":
                $this->format = "html";
                $postvars = array();
                parse_str($body, $postvars);
                foreach ($postvars as $field => $value)
                    $parameters[$field] = $value;
                break;
            default:
                $list = explode(",", $_SERVER['HTTP_ACCEPT']);
                if (isset($_SERVER['HTTP_ACCEPT']) and count($list) > 0) {
                    if ($list[0] == "application/json")
                        $this->format = "json";
                    else
                        $this->format = "html";
                }
                $parameters = array_merge($_GET,$_POST);
                break;
        }
        $this->data = $parameters;
        if (array_key_exists('format', $this->data))
            $this->format = $this->data['format'];
        Api::logDebug(202, "Loaded " . count($this->data) . " params in '" . __CLASS__ . "'", $this->data);
    }

    /**
     * return the request method
     * @return string
     */
    public function getOutputFormat() {
        return $this->format;
    }

    /**
     * return the request method
     * @return string
     */
    public function getProtocol() {
        return $this->protocol;
    }

    /**
     * return the host of this request
     * @return string
     */
    public function getHost() {
        return $this->host;
    }

    /**
     * return the root path of this request
     * @return string
     */
    public function getRoot() {
        return $this->root;
    }

    /**
     * return the root path of this request
     * @return string
     */
    public function getRootUrl() {
        return $this->getProtocol() . $this->getHost() . $this->getRoot();
    }

    /**
     * return the request method
     * @return string
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * return the request path
     * @return string
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * return all the request elements
     * @return array
     */
    public function getElements() {
        return $this->elements;
    }

    /**
     * return the value of the given request element
     * @param type $position the position of an element
     * @return string
     */
    public function getElement($position = null) {
        if (!is_null($position) and array_key_exists($position, $this->elements))
            return $this->elements[$position];
        else
            return null;
    }

    /**
     * return the value of the given request element
     * @param type $position the position of an element
     * @return string
     */
    public function getElementPosition($element = null) {
        $list = array_reverse($this->elements, true);
        foreach ($list as $key => $value)
            if ($element == $value)
                return $key;
        return false;
    }

    /**
     * return the request parameters
     * @return array
     */
    public function getParams() {
        return $this->data;
    }

    /**
     * return the value of the given request parameters
     * @param type $key the key of the parameter
     * @return string
     */
    public function isParam($key = null) {
        if (!is_null($key) and array_key_exists($key, $this->data))
            return true;
        else
            return false;
    }
    /**
     * return the value of the given request parameters
     * @param type $key the key of the parameter
     * @return string
     */
    public function getParam($key = null, $default = null) {
        if (!is_null($key) and array_key_exists($key, $this->data))
            return $this->data[$key];
        else
            return $default;
    }

    /**
     * set the value of the given key into request parameters
     * @param string $key the key of the parameter
     * @param string $value the value of the parameter
     * @return this
     */
    public function setParam($key, $value = null) {
        $this->data[$key] = $value;
        return this;
    }

    /**
     * return the value of the given request parameters
     * @param type $key the key of the parameter
     * @return string
     */
    public function getJsonParam($key = null, $default = null) {
        $data = $this->getParam($key, $default);
        if (is_string($data) and (substr($data, 0, 1) == '[' or substr($data, 0, 1) == '{'))
            return Toolkit::object2Array(@json_decode($data));
        else
            return $data;
    }

    /**
     * return the request parameters
     * @return array
     */
    public function getParamsQuery() {
        $params = http_build_query($this->data);
        return ($params == '') ? '' : '?' . $params;
    }

    public function get($key, $default = null) {
        return $this->getParam($key, $default);
    }

    public function set($key, $data) {
        $this->setParam($key, $data);
        return $this;
    }

    public function getAll() {
        return $this->getParams();
    }

    public function setAll($data) {
        $this->data = $data;
        return $this;
    }

    public function getContext() {
        return array(
            'method' => $this->getMethod(),
            'root' => $this->getRootUrl(),
            'path' => $this->getPath(),
            'elements' => $this->getElements(),
            'params' => $this->getParams()
        );
    }

}

?>