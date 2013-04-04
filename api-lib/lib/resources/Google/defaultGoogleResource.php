<?php

/**
 * This resource class is abstract and should not be used as it.
 * Developpers can create a new google resource type by derivating from this class
 *
 * @package  SXAPI.Resource.Google
 * @author   Dev Team <dev@startx.fr>
 * @see      defaultResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
abstract class defaultGoogleResource extends defaultResource implements IResource {

    protected $client;
    protected $tokens;

    public function __construct($config) {
        parent::__construct($config);
        require_once LIBPATH . 'plugins' . DS . 'google-api-php-client' . DS . 'src' . DS . 'Google_Client.php';
        $this->client = new Google_Client();
    }

    public function init() {
        parent::init();
        $api = Api::getInstance();
        $input = $api->getInput();
        // check for config key application_name
        if ($this->getConfig('application_name') != '')
            $this->client->setApplicationName($this->getConfig('application_name'));
        // check for config key client_id
        if ($this->getConfig('client_id') == '') {
            $api->logError(906, get_class($this) . " resource config should contain the 'client_id' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource config should contain the 'client_id' attribute");
        }
        // check for user's
        if ($api->getInput('user')->getId() == '') {
            $api->logError(906, get_class($this) . " resource could not be used as anonymous, please consider login with google account before using this resource ", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource could not be used as anonymous, please consider login with google account before using this resource ");
        } else {
            // check for user's access_token
            if ($api->getInput("session")->get('user_goauth_token') == '') {
                $api->logError(906, get_class($this) . " resource could not find google access token for user " . $api->getInput('user')->get('_id'), $this->getResourceTrace(__FUNCTION__, false));
                throw new ResourceException(get_class($this) . " resource could not find google access token for user " . $api->getInput('user')->get('_id'));
            }
            else
                $this->client->setAccessToken($api->getInput("session")->get('user_goauth_token'));
        }
        // check for config key client_id
        $this->client->setClientId($this->getConfig('client_id'));
        if ($this->getConfig('client_secret') == '') {
            $api->logError(906, get_class($this) . " resource config should contain the 'client_secret' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource config should contain the 'client_secret' attribute");
        }
        // check for config key client_secret
        $this->client->setClientSecret($this->getConfig('client_secret'));
        // check for config key callback_path
        $this->client->setRedirectUri($this->getConfig('redirect_uri', $input->getRootUrl() . $input->getPath() . DS . $this->getConfig('callback_path', 'callback')));

        return $this;
    }

    public function addServices($services = array()) {
        foreach ($services as $serviceName)
            $this->addService($serviceName);
        return true;
    }

    public function addService($serviceName = "Oauth2") {
        $serviceClass = 'Google_' . ucfirst($serviceName) . 'Service';
        require_once LIBPATH . 'plugins' . DS . 'google-api-php-client' . DS . 'src' . DS . 'contrib' . DS . $serviceClass . '.php';
        $this->services[$serviceName] = new $serviceClass($this->client);
        return true;
    }

    public function getService($serviceName = "Oauth2") {
        if (array_key_exists($serviceName, $this->services))
            return $this->services[$serviceName];
        else
            return null;
    }

}

?>
