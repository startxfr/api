<?php

/**
 * Class used to render data in json formated string
 *
 * @package  SXAPI.Output
 * @author   Mallowtek <mallowtek@gmail.com>
 * @see      DefaultOutput
 * @link      https://github.com/startxfr/sxapi/wiki/Outputs
 */
class CsvOutput extends TxtOutput implements IOutput {

    /**
     * Render the view
     *
     * @param array $content data to be rendered
     * @return void this method echo result and exit program
     */
    protected function render($content) {
        header('Content-Type: '.$this->getConfig("content_type","text/csv").'; charset=utf8');
        header("Content-Disposition: attachment; filename=example.csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        Api::logInfo(350, "Render '" . get_class($this) . "' connector " . @count($content) . " lines sended", $content, 3);
        $outputBuffer = fopen("php://output", 'w');
        foreach ($content as $val) {
            if(is_array($val))
                fputcsv($outputBuffer, $val);
            else
                fputcsv($outputBuffer, array($val));
        }
        fclose($outputBuffer);
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
        return $this->render($data);
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
        return parent::renderError($code, $message, $other);
    }


}

?>
