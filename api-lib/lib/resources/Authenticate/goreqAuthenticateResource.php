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
        //$this->client->setRedirectUri($this->getConfig('redirect_uri', $input->getRootUrl() . $input->getPath() . DS . $this->getConfig('callback_path', 'callback')));
        $this->client->setRedirectUri("http://localhost/startx/api/auth/oauthnext");
        return $this;
    }

    public function readAction() {
        $api = Api::getInstance();
     /*   if (isset($_REQUEST['code'])) {
            print_r($this->client);
            print_r($_REQUEST['code']);
            exit(0);
        }*/
        
        $api->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        try {
            $input = $api->getInput();
            $sessElPosition = $input->getElementPosition($this->getConfig('path'));
            $nextPath = $input->getElement($sessElPosition + 1);
//            print_r("input:<br />");
//            var_dump($input);
//            print_r("<br />sessElPosition:<br />");
//            print_r($sessElPosition);
//            print_r("<br />nextPath:<br />");
//            print_r($nextPath);
//            exit(0);
//                if (isset($_REQUEST['code'])) {
//                    $this->loadServices();                    
//                    $this->client->authenticate($_REQUEST['code']);
//                    $_SESSION['access_token'] = $this->client->getAccessToken();
//                    $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
//                    header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
//                }                
//                else if (isset($_REQUEST['access_token'])) {
                if (isset($_GET['code'])) {    
              //      $this->client->setRedirectUri($input->getRootUrl() );

                    $this->loadServices();
                    $this->client->authenticate($_GET['code']);
                    $accessInfo = json_decode($this->client->getAccessToken());
                    $api->getInput("session")->set('user_goauth_token', json_encode($accessInfo));
                    $user = $this->services['Oauth2']->userinfo->get();
                    $user['email'] = filter_var($user['email'], FILTER_SANITIZE_EMAIL);
                    $user['picture'] = filter_var($user['picture'], FILTER_VALIDATE_URL);
                    $user['google_token'] = $accessInfo;
                    $api->getInput("session")->set('user_goauth_token', json_encode($accessInfo));
                    $api->getInput('session')->set('user', $user['email']);
                    $api->getInput('user')->setAll($user, 'save');
                    $message = sprintf($this->getConfig('message_service_read', 'user %s is now associated to session %s'), $user['email'], session_id());
                    $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return user info for " . $user['email'], $this->getResourceTrace(__FUNCTION__, false, array('user' => $user, 'answer' => $accessInfo)), 1);
                    $api->getOutput()->renderOk($message, $user, count($user));
                } else {
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
