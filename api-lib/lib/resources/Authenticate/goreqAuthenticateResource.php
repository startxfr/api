<?php

/**
 * This resource is used to authenticate using google, and obtaining access to google's services.
 *
 * @package  SXAPI.Resource.Authenticate
 * @author   Dev Team <dev@startx.fr>
 * @see      defaultAuthenticateResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class goreqAuthenticateResource extends defaultAuthenticateResource implements IResource {

    protected $client = null;
    protected $services = array();

    public function __construct($config) {
        parent::__construct($config);
        require_once LIBPATHEXT . 'google-api-php-client' . DS . 'src' . DS . 'Google_Client.php';
        $this->client = new Google_Client();
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
        return $this;
    }

    public function readAction() {
        $api = Api::getInstance();
        $api->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        try {
            if (isset($_GET['code'])) {                     
                $app_uri = $_GET['app_uri'];
                $this->client->setRedirectUri("http://localhost/startx/api/auth/oauthnext?app_uri=".$app_uri);                  
                $this->loadServices();
                $this->client->authenticate($_GET['code']);
                $accessInfo = json_decode($this->client->getAccessToken());
                $api->getInput("session")->set('user_goauth_token', json_encode($accessInfo));
                $user = $this->services['Oauth2']->userinfo->get();
                $user['email'] = filter_var($user['email'], FILTER_SANITIZE_EMAIL);
                $user['picture'] = filter_var($user['picture'], FILTER_VALIDATE_URL);
                $user['google_token'] = $accessInfo;

                $user_data = json_encode($this->prepUserData($user));            
                header('Location: ' . $app_uri . '?user_data='.$user_data);
                exit();



                $api->getInput('user')->setAll($user, 'save');
                $api->getInput("session")->set('user_goauth_token', json_encode($accessInfo));
                $api->getInput('session')->set('user', $user['email']);


                $message = sprintf($this->getConfig('message_service_read', 'user %s is now associated to session %s'), $user['email'], session_id());
                $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return user info for " . $user['email'], $this->getResourceTrace(__FUNCTION__, false, array('user' => $user, 'answer' => $accessInfo)), 1);
                $api->getOutput()->renderOk($message, $user, count($user));
            } 
            else if (isset($_GET['error'])) {
                switch ($_GET['error']) {
                    case "access_denied":
                        $message = "No user access from google because : " . $_GET['error'];
                        break;
                    default:
                        $message = "No user access from google because : " . $_GET['error'];
                        break;
                }
                $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $message, $exc);
                $api->getOutput()->renderError(910, $message, array(), 401);
            }
            else {
                $this->loadServices();
                $app_uri_ec = urlencode($_GET['app_uri']);
                $app_uri = $_GET['app_uri'];
                $this->client->setRedirectUri("http://localhost/startx/api/auth/oauthnext?app_uri=".$app_uri);
                $authUrl = $this->client->createAuthUrl();
                $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' start oauth request with google ", $this->getResourceTrace(__FUNCTION__, false, array('oauth_url' => $authUrl)), 1);
                $api->getOutput()->renderOk("googleOauth", $authUrl);
            }
        } catch (Exception $exc) {
            $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            $api->getOutput()->renderError($exc->getCode(), $exc->getMessage(), array(), 401);
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
        $data['refresh_token'] = $user['google_token']['refresh_token'];
        return $data;
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