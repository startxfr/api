<?php

//require_once LIBPATH . 'plugins' . DS . 'google-api-php-client' . DS . 'src' . DS . 'Google_Client.php';
//require_once LIBPATH . 'plugins' . DS . 'google-api-php-client' . DS . 'src' . DS . 'contrib' . DS . 'Google_CalendarService.php';
//require_once LIBPATH . 'plugins' . DS . 'google-api-php-client' . DS . 'src' . DS . 'contrib' . DS . 'Google_Oauth2Service.php';

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

//    protected $client;
//
//    public function __construct($config) {
//        parent::__construct($config);
//        $this->client = new Google_Client();
//    }
//
//    public function init() {
//        parent::init();
//        $api = Api::getInstance();
//        $input = $api->getInput();
//        //$client->setApplicationName("Google Calendar PHP Starter Application");
//        $this->client->setClientId('703694493039.apps.googleusercontent.com');
//        $this->client->setClientSecret('ghpmYHB6pOTB5m1EBpaap2Ju');
//        $this->client->setRedirectUri($input->getRootUrl() . $input->getPath().DS.'callback');
//
////        $api = Api::getInstance();
////        if ($this->getConfig('model', '') == '') {
////            $api->logError(906, get_class($this) . " resource config should contain the 'model' attribute", $this->getResourceTrace(__FUNCTION__, false));
////            throw new ResourceException(get_class($this) . " resource config should contain the 'model' attribute");
////        }
////        $this->model = $api->getModel($this->getConfig('model'));
////        if (is_null($this->getConfig('search_params')))
////            $this->setConfig('search_params', array());
////        elseif (is_string($this->getConfig('search_params')))
////            $this->setConfig('search_params', explode(',', $this->getConfig('search_params')));
//        return $this;
//    }

}

?>
