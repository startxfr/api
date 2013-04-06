<?php

/**
 * Class used to access application informations of the application associated to the current session
 *
 * @package  SXAPI.Input
 * @author   Dev Team <dev@startx.fr>
 * @see      DefaultInput
 * @link     https://github.com/startxfr/sxapi/wiki/Inputs
 */
class TrackingInput extends DefaultInput implements IInput {

    private $applicationStorage = null;
    private $cachedData = null;

    /**
     * construct the application input object
     *
     * @param array configuration of this object
     * @see Configurable
     * @return void
     */
    public function __construct($config) {
        parent::__construct($config);
        }

    /**
     * initialize this instance and make it available and usable
     * @return self
     */
    public function init() {
        Api::logDebug(210, "Init '" . $this->getConfig("_id") . "' " . get_class($this) . " connector  setting to '" . Api::getInstance()->getInput('session')->get('application') . "'", Api::getInstance()->getInput('session')->get('application'), 4);
        $this->launchTracking();
        return $this;
    }

    protected function launchTracking() {
        $api = Api::getInstance();
        require_once(LIBPATH . 'plugins' . DS . 'php-ga-1.1.1' . DS . 'src' . DS . 'autoload.php');
        // Initilize GA Tracker
        $tracker = new UnitedPrototype\GoogleAnalytics\Tracker($this->getConfig('account_id'), $this->getConfig('domain_name',$api->getInput()->getHost()));
        // Assemble Visitor information
        // (could also get unserialized from database)
        $visitor = new UnitedPrototype\GoogleAnalytics\Visitor();
        $visitor->setIpAddress($_SERVER['REMOTE_ADDR']);
        $visitor->setUserAgent($_SERVER['HTTP_USER_AGENT']);
        $visitor->setScreenResolution('1024x768');
        // Assemble Session information
        // (could also get unserialized from PHP session)
        $session = new UnitedPrototype\GoogleAnalytics\Session();
        // Assemble Page information
        $page = new UnitedPrototype\GoogleAnalytics\Page(DS.$api->getInput()->getPath());
        $page->setTitle('Resource XXXX');
        // Track page view
        $tracker->trackPageview($page, $session, $visitor);
        return $this;
    }
}

?>