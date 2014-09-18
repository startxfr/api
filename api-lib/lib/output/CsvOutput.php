<?php

/**
 * Class used to render data in json formated string
 *
 * @class     CsvOutput
 * @author   Mallowtek <mallowtek@gmail.com>
 * @see      DefaultOutput
 * @link      https://github.com/startxfr/sxapi/wiki/Outputs
 */
class CsvOutput extends TxtOutput implements IOutput {

    /**
     * Render the view
     *
     * @param $content array data to be rendered
     * @param $httpCode int Http response code
     * @return void this method echo result and exit program
     */
    protected function render($content, $httpCode = 200) {
        Event::trigger('output.render.before');
        http_response_code($httpCode);
        header('Content-Type: ' . $this->getConfig("content_type", "text/csv") . '; charset=utf8');
        header("Content-Disposition: attachment; filename=example.csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        Api::logInfo(350, "Render '" . get_class($this) . "' connector " . @count($content) . " lines sended", $content, 3);
        $outputBuffer = fopen("php://output", 'w');
        if ($this->getConfig("first_line", true) and is_array($content[0])) {
            $keys = array_keys($content[0]);
            fputcsv($outputBuffer, $keys);
        }
        foreach ($content as $val) {
            if (is_array($val))
                fputcsv($outputBuffer, $val);
            else
                fputcsv($outputBuffer, array($val));
        }
        fclose($outputBuffer);
        Event::trigger('output.render.after');
        exit;
    }

    /**
     * Render the content exiting normally
     *
     * @param   $message    string describing the returned data
     * @param   $data       mixed data to be rendered and returned to the client
     * @param   $count      int  counter to indicate if there is more data that the returned set
     * @param   $httpCode   int $httpCode Http response code
     * @return  void
     * @see     self::render();
     */
    public function renderOk($message, $data, $count = null, $httpCode = 200) {
        return $this->render($data, $httpCode);
    }

    /**
     * Render the content exiting with error
     *
     * @param   $code       int     error code to render
     * @param   $message    string  message describing the error
     * @param   $other      mixed   other data to be returned to the client
     * @param   $httpCode   int     Http response code
     * @return  void
     * @see     self::render();
     */
    public function renderError($code, $message = '', $other = array(), $httpCode = 400) {
        return parent::renderError($code, $message, $other, $httpCode);
    }

}

?>
