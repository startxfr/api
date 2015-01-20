<?php

/**
 * Class used to render data in wddx xml format
 *
 * @class    CxmlOutput
 * @author   Mallowtek <mallowtek@gmail.com>
 * @see      DefaultOutput
 * @link      https://github.com/startxfr/sxapi/wiki/Outputs
 */
class CxmlOutput extends XmlOutput implements IOutput {

    /**
     * Render the view
     *
     * @param array $content data to be rendered
     * @param int   $httpCode Http response code
     * @return void this method echo result and exit program
     */
    protected function render($content, $httpCode = 200) {
        $api = Api::getInstance();
        Event::trigger('output.render.before');
        http_response_code($httpCode);
        if (is_array($content)) {
            if (array_key_exists('message', $content))
                $content = $content['message'];
            else
                $content = implode(', ', $content);
        }
        header('Content-Type: ' . $this->getConfig("content_type", "text/xml") . '; charset=utf8');
        $payload = $api->getInput()->getParam('payload', md5(rand(0, 1000) + time()) . '@cea.startx.fr');
        $output = '<?xml version="1.0" encoding="UTF-8" standalone="no" ?>';
        $output.= '<cXML payloadID="' . $payload . '" timestamp="' . date(DATE_W3C) . '" version="1.2.011" xml:lang="fr">';
        $output.= '<Response><Status code="' . $httpCode . '" text="' . $content . '"/></Response>';
        $output.= '</cXML>';
        Api::logInfo(350, "Render '" . get_class($this) . "' connector " . strlen($output) . " octets sended", $output, 3);
        echo $output;
        Event::trigger('output.render.after');
        exit;
    }

}

?>
