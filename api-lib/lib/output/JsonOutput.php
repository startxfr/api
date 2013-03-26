<?php

/**
 * Json Output class: renders json formated string
 *
 * @category Output
 * @package  API
 * @author   Mallowtek <mallowtek@gmail.com>
 */
class JsonOutput extends DefaultOutput implements IOutput {

    /**
     * Render the view
     *
     * @param array $content data to be rendered
     *
     * @return bool
     */
    public function render($content) {
        header('Content-Type: application/json; charset=utf8');
        $output = json_encode($content);
        Api::logInfo(350, "Render '" . get_class($this) . "' connector " . strlen($output) . " octets sended", $content, 3);
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
     * @param array $content data to be rendered
     *
     * @return bool
     */
    public function renderError($code, $message = '', $other = array()) {
        header('HTTP/1.1 400 BAD REQUEST');
        $config = array(
            'status' => 'error',
            'success' => false,
            'total' => 0,
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
