<?php

class DefaultOutput extends Configurable implements IOutput {

    /**
     * init the rendering object
     *
     */
    public function __construct($config) {
        Api::logDebug(300, "Construct '" . $config["_id"] . "' " . get_class($this) . " connector ", $config, 5);
        parent::__construct($config);
    }

    /**
     * init the rendering object
     *
     */
    public function init() {
        Api::logDebug(310, "Init '" . $this->getConfig("_id") . "' " . get_class($this) . " connector", null, 5);
        return $this;
    }

    /**
     * Render the view
     *
     * @param array $content data to be rendered
     *
     */
    public function render($content) {
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
     * @param array $content data to be rendered
     *
     * @return bool
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
     * @param array $content data to be rendered
     *
     * @return bool
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
