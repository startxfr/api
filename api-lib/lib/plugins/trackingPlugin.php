<?php


/**
 * Plugin used to track request into the Google Analytics backend.
 * Have a look at the plugin configuration for more detail about it configuration
 *
 * @package  SXAPI.Plugin
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
        $tracker = new UnitedPrototype\GoogleAnalytics\Tracker($plugin->getConfig('account_id'), $plugin->getConfig('domain_name',$api->getInput()->getHost()));
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
        return true;
    }

}

?>
