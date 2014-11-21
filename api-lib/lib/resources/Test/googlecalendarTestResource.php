<?php

/**
 * This resource is used to return an input message
 *
 * @package  SXAPI.Resource.Test
 * @author   Dev Team <dev@startx.fr>
 * @see      readonlyResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class googlecalendarTestResource extends linkableResource implements IResource {

    protected $client = null;
    protected $services = array();

    public function __construct($config) {
        parent::__construct($config);        
        require_once LIBPATHEXT . 'google-api-php-client' . DS . 'src' . DS . 'Google_Client.php';        
        $this->client = new Google_Client();  
    }

    public function init() {
        parent::init();
 
        $this->client->setApplicationName($this->getConfig('application_name')); 
        $this->client->setClientId($this->getConfig('client_id'));       
        $this->client->setClientSecret($this->getConfig('client_secret'));

        $this->client->setRedirectUri("http://localhost/startx/api/calendar");   
        $this->client->setScopes('https://www.googleapis.com/auth/calendar');
        

        return $this;
    }

    public function readAction() {
        $api = Api::getInstance();
        $api->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        try {
            $store = $api->getStore($this->getConfig('store'));
            $data = $store->readOne($this->getConfig('store_dataset'), array($this->getConfig('store_id_key') => $this->getConfig('user_id')));
            $refreshToken = $data['refresh_token'];
            $calendar = $this->loadServices();
            $this->client->refreshToken($refreshToken);
            $calEvents = $calendar->events->listEvents($this->getConfig('session_calendar_id', "formation@startx.fr"));
            return array(true, "Calendar testing", $calEvents); 
        } catch (Exception $exc) {
            $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            return array(false, $exc->getCode(), $exc->getMessage(), array(), 401);
        }
        return true;
    }
    
//    public function readAction() {
//        $api = Api::getInstance();
//        $api->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
//        try {
//            if (isset($_GET['code'])) {                                                                
//                $calendar = $this->loadServices();  
//                $this->client->authenticate($_GET['code']);                
//                $calEvents = $calendar->events->listEvents($this->getConfig('session_calendar_id', "formation@startx.fr"));
//                return array(true, "Calendar testing", $calEvents);                            
//            } 
//            else if (isset($_GET['error'])) {
//                throw new ResourceException("No user access from google because : " . $_GET['error']);
//            }
//            else {                
//                $authUrl = $this->client->createAuthUrl();
//                $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' start oauth request with google ", $this->getResourceTrace(__FUNCTION__, false, array('oauth_url' => $authUrl)), 1);
//                header('Location: ' . $authUrl);
//                exit();
//            }
//        } catch (Exception $exc) {
//            $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
//            return array(false, $exc->getCode(), $exc->getMessage(), array(), 401);
//        }
//        return true;
//    }
    
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
    
    public function loadServices() {
        $api = Api::getInstance();
        $serviceName = $this->getConfig('google_service', "Google_CalendarService");
        try {
            $serviceClass = 'Google_' . ucfirst($serviceName) . 'Service';            
            require_once LIBPATHEXT . 'google-api-php-client' . DS . 'src' . DS . 'contrib' . DS . $serviceClass . '.php';            
            $this->services[$serviceName] = new $serviceClass($this->client);           
        } catch (Exception $exc) {
            $api->logWarn(910, "Warning on '" . __FUNCTION__ . "' for '" . get_class($this) . "' : " . $exc->getMessage(), $exc);
        }
        return $this->services[$serviceName];
    }

    public function authClient() {
        $authUrl = $this->client->createAuthUrl();
        //Request authorization
        print "Please visit:\n$authUrl\n\n";
        print "Please enter the auth code:\n";
        $authCode = trim(fgets(STDIN));
        // Exchange authorization code for access token
        $accessToken = $this->client->authenticate($authCode);
        $this->client->setAccessToken($accessToken);
        return true;
    }

    
}

?>
