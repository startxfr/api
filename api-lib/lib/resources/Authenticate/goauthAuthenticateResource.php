<?php

/**
 * This resource is used to authenticate using google, and obtaining access to google's services.
 *
 * @package  SXAPI.Resource.Authenticate
 * @author   Dev Team <dev@startx.fr>
 * @see      defaultAuthenticateResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class goauthAuthenticateResource extends defaultAuthenticateResource implements IResource {

        static public $ConfDesc = '{"class_name":"goauthAuthenticateResource",
                                "desc":"Google Authentication mechanism",
                                "properties":
	[
                {
			"name":"store",
			"type":"string",
			"mandatory":"true",
			"desc":"store to query"
		},                
                {
			"name":"store_dataset",
			"type":"string",
			"mandatory":"true",
			"desc":"dataset to query"
		},
                {
			"name":"store_id_key",
			"type":"string",
			"mandatory":"true",
			"desc":"name of the id key use in store"
		},
                {
			"name":"application_name",
			"type":"string",
			"mandatory":"false",
			"desc":"name of the application"
		},
                {
			"name":"client_id",
			"type":"string",
			"mandatory":"true",
			"desc":"your google client_id"
		},
                {
			"name":"client_secret",
			"type":"string",
			"mandatory":"true",
			"desc":"your google client_secret"
		},
                {
			"name":"google_service",
			"type":"string",
			"mandatory":"false",
			"desc":"name of the google service you want to use, will be Oauth2 by default"
		},
                {
			"name":"uri_local_default",
			"type":"string",
			"mandatory":"true",
			"desc":"where to redirect when logged. Will be override by $_GET[\'uri_local\']"
		},
                {
			"name":"uri_reg_default",
			"type":"string",
			"mandatory":"true",
			"desc":"where to redirect when Oauth Authentication succeed but user doesn\'t exist. Will be override by $_GET[\'uri_reg\']"
		}
	]
}'
;
    
    protected $client = null;
    protected $services = array();

    public function __construct($config) {
        parent::__construct($config);
        require_once LIBPATHEXT . 'google-api-php-client' . DS . 'src' . DS . 'Google_Client.php';
        $this->client = new Google_Client();
        if ($this->getConfig('uri_local_default', '') == '') {
            $api->logError(906, get_class($this) . " resource config should contain the 'uri_local_default' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource config should contain the 'uri_local_default' attribute");
        }
        if ($this->getConfig('uri_reg_default', '') == '') {
            $api->logError(906, get_class($this) . " resource config should contain the 'uri_reg_default' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource config should contain the 'uri_reg_default' attribute");
        }
        if ($this->getConfig('store', '') == '') {
            $api->logError(906, get_class($this) . " resource config should contain the 'store' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource config should contain the 'store' attribute");
        }
        if ($this->getConfig('store_dataset', '') == '') {
            $api->logError(906, get_class($this) . " resource config should contain the 'store_dataset' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource config should contain the 'store_dataset' attribute");
        }
        if ($this->getConfig('store_id_key', '') == '') {
            $api->logError(906, get_class($this) . " resource config should contain the 'store_id_key' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource config should contain the 'store_id_key' attribute");
        }
    }

    public function init() {
        parent::init();
        $api = Api::getInstance();
        $input = $api->getInput();
        if ($this->getConfig('application_name') != '')
            $this->client->setApplicationName($this->getConfig('application_name'));
        if ($this->getConfig('client_id') == '') {
            $api->logError(906, get_class($this) . " resource config should contain the 'client_id' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource config should contain the 'client_id' attribute");
        }
        $this->client->setClientId($this->getConfig('client_id'));
        if ($this->getConfig('client_secret') == '') {
            $api->logError(906, get_class($this) . " resource config should contain the 'client_secret' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource config should contain the 'client_secret' attribute");
        }
        $this->client->setClientSecret($this->getConfig('client_secret'));
        $this->client->setRedirectUri("http://localhost/startx/api/formation/auth/oauth");
        return $this;
    }

    public function readAction() {
        $api = Api::getInstance();
        $api->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        try {
            if (isset($_GET['code'])) {
                $state = $this->retrieveStatetokenData($_GET['state']);                
                if ($api->getInput('session')->get('af_token') !== $state->state)
                    return array(false, 910, "No user access from google because : anti-forgery token is not valid", array(), 401);                 
                $this->loadServices();
                $this->client->authenticate($_GET['code']);
                $accessInfo = json_decode($this->client->getAccessToken());
                $api->getInput("session")->set('user_goauth_token', json_encode($accessInfo));
                $user = $this->services['Oauth2']->userinfo->get();
                $user['email'] = filter_var($user['email'], FILTER_SANITIZE_EMAIL);
                $user['picture'] = filter_var($user['picture'], FILTER_VALIDATE_URL);
                $user['google_token'] = $accessInfo;

                $store = $api->getStore($this->getConfig('store'));
                $data = $store->readOne($this->getConfig('store_dataset'), array($this->getConfig('store_id_key') => $user['email']) );                
                
                if (is_array($data) and $data["_id"] == $user['email']) {
                    $api->getInput("session")->set('user_goauth_token', json_encode($accessInfo));
                    $api->getInput('session')->set('user', $user['email']);              
                    $message = sprintf($this->getConfig('message_service_read', 'user %s is now associated to session %s'), $user['email'], session_id());
                    $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return user info for " . $user['email'], $this->getResourceTrace(__FUNCTION__, false, array('user' => $user, 'answer' => $accessInfo)), 1);
                //    return array(true, $message, $user, count($user));
                    $app_uri = $state->local_uri;
                    header('Location: ' . $app_uri . '?authmsg=Successfully logged in');
                    exit();
                }
                else {
                    $user_data = json_encode($this->prepUserData($user));       
                    $app_uri = $state->reg_uri;
                    header('Location: ' . $app_uri . '?user_data='.$user_data);
                    exit();
                }                              
            } 
            else if (isset($_GET['error'])) {
                $state = $this->retrieveStatetokenData($_GET['state']);
                switch ($_GET['error']) {
                    case "access_denied":
                        $message = "No user access from google because : " . $_GET['error'];
                        break;
                    default:
                        $message = "No user access from google because : " . $_GET['error'];
                        break;
                }
                $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $message, $exc);
                //return array(false, 910, $message, array(), 401);
                $app_uri = $state->local_uri;
                header('Location: ' . $app_uri . '?authmsg=' . $message);
                exit();
            }
            else {
                $af_token = md5(rand());
                $reg_uri = (array_key_exists('reg_uri', $_GET) ? $_GET['reg_uri'] : $this->getConfig('uri_reg_default'));
                $local_uri = (array_key_exists('local_uri', $_GET) ? $_GET['local_uri'] : $this->getConfig('uri_local_default'));
                //$reg_uri = $_GET['reg_uri'];
                //$local_uri = $_GET['local_uri'];
                $api->getInput('session')->set('af_token', $af_token);
                $this->client->setState($this->prepStateTokenData($af_token, $reg_uri, $local_uri));
                $this->loadServices();
                $authUrl = $this->client->createAuthUrl();
                $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' start oauth request with google ", $this->getResourceTrace(__FUNCTION__, false, array('oauth_url' => $authUrl)), 1);
                return array(true, "googleOauth", $authUrl);
            }
        } catch (Exception $exc) {
            $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            return array(false, $exc->getCode(), $exc->getMessage(), array(), 401);
        }
        return true;
    }

    public function loadServices() {
        $api = Api::getInstance();
        $serviceName = $this->getConfig('google_service', "Oauth2");
        try {
            $serviceClass = 'Google_' . ucfirst($serviceName) . 'Service';
            require_once LIBPATHEXT . 'google-api-php-client' . DS . 'src' . DS . 'contrib' . DS . $serviceClass . '.php';
            $this->services[$serviceName] = new $serviceClass($this->client);
        } catch (Exception $exc) {
            $api->logWarn(910, "Warning on '" . __FUNCTION__ . "' for '" . get_class($this) . "' : " . $exc->getMessage(), $exc);
        }
        return true;
    }

    public function prepUserData( $user ) {
        $data = [];
        $data['forname'] = $user['given_name'];
        $data['lastname'] = $user['family_name'];
        $data['email'] = $user['email'];
        $data['refresh_token'] = $user['google_token']->refresh_token;
        return $data;
    }
    
    public function prepStateTokenData( $state, $reg_uri, $local_uri ) {
        $data = [];
        $data['state'] = $state;
        $data['reg_uri'] = $reg_uri;
        $data['local_uri'] = $local_uri;
        $str = base64_encode(json_encode($data));
        return $str;
    }
    
    public function retrieveStatetokenData( $state_data ) {
        $state = json_decode(base64_decode($state_data));
        return $state;
    }
    
    public function createAction() {
        Api::getInstance()->logDebug(930, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getConfigs(), 3);
        return $this->readAction();
    }

    public function updateAction() {
        Api::getInstance()->logDebug(950, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getConfigs(), 3);
        return $this->readAction();
    }

    public function deleteAction() {
        Api::getInstance()->logDebug(970, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getConfigs(), 3);
        return $this->readAction();
    }

}

?>
