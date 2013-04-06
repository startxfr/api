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
        Event::trigger('output.render.before');
        header('Content-Type: '.$this->getConfig("content_type","text/xml").'; charset=utf8');
        $output = wddx_serialize_value($content);
        Api::logInfo(350, "Render '" . get_class($this) . "' connector " . strlen($output) . " octets sended", $output, 3);
        echo $output;
        Event::trigger('output.render.after');
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
        return parent::renderOk($message, $data, $count);
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
        return parent::renderError($code, $message,$other);
    }

}

?>
