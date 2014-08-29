<?php

/**
 * Class used to render data in json formated string
 *
 * @package  SXAPI.Output
 * @author   Mallowtek <mallowtek@gmail.com>
 * @see      DefaultOutput
 * @link      https://github.com/startxfr/sxapi/wiki/Outputs
 */
class JsonOutput extends DefaultOutput implements IOutput {

    /**
     * Render the view
     *
     * @param array $content data to be rendered
     * @param int   $httpCode Http response code
     * @return void this method echo result and exit program
     */
    protected function render($content, $httpCode = 200) {
        Event::trigger('output.render.before');
        http_response_code($httpCode);
        header('Content-Type: '.$this->getConfig("content_type","application/json").'; charset=utf8');
        $output = json_encode($content);
        Api::logInfo(350, "Render '" . get_class($this) . "' connector " . strlen($output) . " octets sended", $content, 3);
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
     * @param   int     $httpCode Http response code
     * @return  void
     * @see     self::render();
     */
    public function renderOk($message, $data, $count = null, $httpCode = 200) {
        return parent::renderOk($message, $data, $count, $httpCode);
    }

    /**
     * Render the content exiting with error
     *
     * @param   int     error code to render
     * @param   string  message describing the error
     * @param   mixed   other data to be returned to the client
     * @param   int     $httpCode Http response code
     * @return  void
     * @see     self::render();
     */
    public function renderError($code, $message = '', $other = array(), $httpCode = 400) {
        return parent::renderError($code, $message,$other, $httpCode);
    }

}

?>
