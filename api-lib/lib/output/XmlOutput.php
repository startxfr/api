<?php

/**
 * Class used to render data in wddx xml format
 *
 * @package  SXAPI.Output
 * @author   Mallowtek <mallowtek@gmail.com>
 * @see      DefaultOutput
 * @link      https://github.com/startxfr/sxapi/wiki/Outputs
 */
class XmlOutput extends DefaultOutput implements IOutput {

    /**
     * Render the view
     *
     * @param array $content data to be rendered
     * @return void this method echo result and exit program
     */
    protected function render($content) {
        header('Content-Type: text/xml; charset=utf8');
        $output = wddx_serialize_value($content);
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
    public function renderOk($message, $data, $count = null) {
        if ($count == null and is_array($data))
            $count = count($data);
        elseif ($count == null)
            $count = 1;
        $config = array(
            'status' => 'ok',
            'success' => true,
            'total' => $count,
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
        header('HTTP/1.1 400 BAD REQUEST');
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
