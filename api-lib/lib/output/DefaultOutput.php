<?php

/**
 * Base class used for rendering content to the API client. All output class should be derivated form this class or one of its descendant
 *
 * @package  SXAPI.Output
 * @author   Mallowtek <mallowtek@gmail.com>
 * @link      https://github.com/startxfr/sxapi/wiki/Outputs
 */
abstract class DefaultOutput extends Configurable implements IOutput {

    /**
     * init the output object
     *
     * @param array configuration of this object
     * @see Configurable
     * @return void
     */
    public function __construct($config) {
        Api::logDebug(300, "Construct '" . $config["_id"] . "' " . get_class($this) . " connector ", $config, 5);
        parent::__construct($config);
    }

    /**
     * init the rendering object
     *
     * @return self
     */
    public function init() {
        Api::logDebug(310, "Init '" . $this->getConfig("_id") . "' " . get_class($this) . " connector", null, 5);
        return $this;
    }

    /**
     * Render the view
     *
     * @param array $content data to be rendered
     * @return void this method echo result and exit program
     */
    protected function render($content) {
        ob_start();
        print_r($content);
        $output = ob_get_contents();
        ob_end_clean();
        Api::logInfo(350, "Render '" . get_class($this) . "' connector " . strlen($output) . " octets sended", $output, 3);
        echo $output;
        exit;
    }

    /**
     * Render the content exiting normally
     *
     * @param   string  message describing the returned data
     * @param   mixed   data to be rendered and returned to the client
     * @param   int     counter to indicate if there is more data that the returned set
     * @return  void
     * @see     self::render();
     */
    public function renderOk($message, $data) {
        $config = array(
            'status' => 'ok',
            'message' => $message,
            'data' => $data
        );
        Api::logDebug(341, "Prepare OK rendering in '" . get_class($this) . "' connector for message : " . $message, $config, 5);
        return $this->render($config);
    }

    /**
     * Render the content exiting with error
     *
     * @param   int     error code to render
     * @param   string  message describing the error
     * @param   mixed   other data to be returned to the client
     * @return  void
     * @see     self::render();
     */
    public function renderError($code, $message = '', $other = array()) {
        $config = array(
            'status' => 'error',
            'code' => $code,
            'message' => $message
        );
        if (!is_array($other))
            $other = array($other);
        elseif (is_object($other))
            $other = (array) $other;
        if (is_array($other))
            $config = array_merge($config, $other);
        Api::logDebug(345, "Prepare ERROR rendering in '" . get_class($this) . "' connector for message : " . $message, $config, 5);
        return $this->render($config);
    }

}

?>
