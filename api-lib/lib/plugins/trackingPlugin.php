<?php

/**
 * Plugin used to track request into the Google Analytics backend.
 * Have a look at the plugin configuration for more detail about it configuration
 *
 * @class    trackingPlugin
 * @author   Mallowtek <mallowtek@gmail.com>
 * @link      https://github.com/startxfr/sxapi/wiki/Plugins
 */
class trackingPlugin extends defaultPlugin implements IPlugin {

    /**
     * init the output object
     *
     * @param array configuration of this object
     * @see Configurable
     * @return void
     */
    public function __construct($config) {
        parent::__construct($config);
        // bind to events
        Event::bind('output.render.before', 'trackingPlugin::beforeApiOutput');
    }

    /**
     * Render the view
     *
     * @param array $content data to be rendered
     * @return void this method echo result and exit program
     */
    public static function beforeApiOutput($content) {
        $api = Api::getInstance();
        $plugin = self::getInstance();
        require_once(LIBPATHEXT . 'php-ga-1.1.1' . DS . 'src' . DS . 'autoload.php');
        // Initilize GA Tracker
        try {
            $tracker = $plugin->initTracker();
        } catch (Exception $e) {
            $api->logError(530, "tracking plugin error when starting tracker " . $e->getMessage(), $e);
            return false;
        }
        try {
            $visitor = $plugin->initVisitor();
        } catch (Exception $e) {
            $api->logError(531, "tracking plugin error obtaining tracking visitor. " . $e->getMessage(), $e);
            return false;
        }
        try {
            $sessionga = $plugin->initSession();
        } catch (Exception $e) {
            $api->logError(532, "tracking plugin error when recovering tracking session into api session " . $e->getMessage(), $e);
            return false;
        }
        try {
            $page = $plugin->initPage();
        } catch (Exception $e) {
            $api->logError(533, "tracking plugin error when creating tracking page " . $e->getMessage(), $e);
            return false;
        }
        // Track page view
        try {
            $tracker->trackPageview($page, $sessionga, $visitor);
        } catch (Exception $e) {
            $api->logError(533, "tracking plugin error when recording this trace. " . $e->getMessage(), $e);
            return false;
        }
        return true;
    }

    /**
     * Render the tracker object
     *
     * @return UnitedPrototype\GoogleAnalytics\Tracker object
     * @throws Exception
     */
    public function initTracker() {
        $api = Api::getInstance();
        $plugin = self::getInstance();
        $tracker = new UnitedPrototype\GoogleAnalytics\Tracker($plugin->getConfig('account_id'), $plugin->getConfig('domain_name', $api->getInput()->getHost()));
        return $tracker;
    }

    /**
     * Render the visitor object
     *
     * @return UnitedPrototype\GoogleAnalytics\Visitor object
     * @throws Exception
     */
    public function initVisitor() {
        $api = Api::getInstance();
        $session = $api->getInput('session');
        if ($session->get('trackingPlugin_visitor') != null) {
            $visitor = unserialize($session->get('trackingPlugin_visitor'));
            if ($visitor === false) {
                $api->logWarn(531, "tracking plugin error when unserializing tracking visitor from api session " . $session->get('_id'), $session->get('trackingPlugin_visitor'));
            } else {
                return $visitor;
            }
        }
        $visitor = new UnitedPrototype\GoogleAnalytics\Visitor();
        $visitor->setIpAddress($_SERVER['REMOTE_ADDR']);
        $visitor->setUserAgent($_SERVER['HTTP_USER_AGENT']);
        $visitor->setScreenResolution('1024x768');
        $session->set('trackingPlugin_visitor', serialize($visitor));
        return $visitor;
    }

    /**
     * Render the session object
     *
     * @return UnitedPrototype\GoogleAnalytics\Session object
     * @throws Exception
     */
    public function initSession() {
        $api = Api::getInstance();
        $session = $api->getInput('session');
        if ($session->get('trackingPlugin_session') != null) {
            $sessionga = unserialize($session->get('trackingPlugin_session'));
            if ($sessionga === false) {
                $api->logWarn(532, "tracking plugin error when recovering tracking session from api session " . $session->get('_id'), $session->get('trackingPlugin_visitor'));
            } else {
                return $sessionga;
            }
        }
        $sessionga = new UnitedPrototype\GoogleAnalytics\Session();
        $session->set('trackingPlugin_session', serialize($sessionga));
        return $sessionga;
    }
    
    
    /**
     * Render the page object
     *
     * @return UnitedPrototype\GoogleAnalytics\Page object
     * @throws Exception
     */
    public function initPage() {
        $api = Api::getInstance();
        $page = new UnitedPrototype\GoogleAnalytics\Page(DS . $api->getInput()->getPath());
            $page->setTitle('Resource ' . $api->getInput()->getPath());
        return $page;
    }

}

?>
