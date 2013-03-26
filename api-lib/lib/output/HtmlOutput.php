<?php

/**
 * HTML Output class: renders HTML 5
 *
 * @category Output
 * @package  API
 * @author   Mallowtek <mallowtek@gmail.com>
 */
class HtmlOutput extends DefaultOutput implements IOutput {

    /**
     * Render the view
     *
     * @param array $content data to be rendered
     *
     * @return bool
     */
    public function render($data) {
        header('Content-Type: text/html; charset=utf8');
        ob_start();
        $this->layoutStart();
        echo $data;
        $this->layoutStop();
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
    public function renderOk($message, $data, $count = null) {
        if ($count == null and is_array($data))
            $count = " (returning ".count($data)." results)";
        elseif($count > 0)
            $count = " (returning ".$count." results)";
        else
            $count = "";
        $otherInfo = '<h3>Answer</h3><p>' . $this->layoutContent($data) . '</p>';
        $html = '
                <body id="answer">
                    <div>
                        <h1>Answer to your sxAPI Query</h1>
                        <h3>Message</h3>
                        <p>' . $message . $count.'</p>
                        ' . $otherInfo . '
                        <h3>Further information</h3>
                        <p>If you need some other informations, please contact dev@startx.fr</p>
                    </div>
                </body>';
        Api::logDebug(341, "Prepare OK rendering in '" . get_class($this) . "' connector for message : " . $message, $html, 5);
        return $this->render($html);
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
        $otherstring = "";
        if (DEBUG)
            $otherDebug = '<h3>Trace or context</h3><p>' . $this->layoutContent($other) . '</p>';
        else
            $otherstring = "<i>You must activate DEBUG constant and set it to true in order to see error detail</i>";
        $html = '
                <body id="error">
                    <div>
                        <h1>FATAL ERROR CODE ' . $code . '</h1>
                        <h3>Message</h3>
                        <p>' . $message . '</p>
                        ' . $otherDebug . '
                        <h3>Further information</h3>
                        <p>If you need some other informations, please contact dev@startx.fr</p>
                    </div>
                </body>';
        Api::logDebug(345, "Prepare ERROR rendering in '" . get_class($this) . "' connector for message : " . $message, $html, 5);
        return $this->render($html);
    }

    /**
     * Recursively render an array to an HTML list
     *
     * @param array $content data to be rendered
     *
     * @return null
     */
    protected function layoutContent($content) {
        if (is_string($content)) {
            return $content;
        }
        $ech = "<ul>\n";
        if (!is_array($content))
            $content = array($content);
        elseif (is_object($content))
            $content = (array) $content;
        foreach ($content as $field => $value) {
            if (is_object($value) and $value instanceof MongoDate)
                $value = date('Y-m-d H:i:s', $value->sec);
            elseif (is_object($value))
                $value = (array) $value;
            $ech .= "<li><strong>" . $field . " : </strong> ";
            if (is_array($value))
                $ech .= $this->layoutContent($value, '');
            else {
                $value = htmlentities($value, ENT_COMPAT, 'UTF-8');
                if ((strpos($value, 'http://') === 0) || (strpos($value, 'https://') === 0))
                    $ech .= "<a href=\"" . $value . "\">" . $value . "</a>";
                else
                    $ech .= $value;
            }
            $ech .= "</li>\n";
        }
        $ech .= "</ul>\n";
        return $ech;
    }

    /**
     * Render start of HTML page
     *
     * @return null
     */
    protected function layoutStart() {
        echo '<!DOCTYPE html>
            <html>
            <head>
                <title>SX-API v1</title>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                <style>
                    body {
                        font-family: Helvetica, Arial, sans-serif;
                        font-size: 14px;
                        color: #333;
                        padding: 1em;
                    }
                    body > div {box-shadow: 5px 5px 10px  rgba(0,0,0,0.8); padding: 1em; border-radius: 5px; width: 900px; margin: 1em auto  }
                    h1 { margin: 0; font-size: 4em; line-height: .8em }
                    h3 { text-shadow: 1px 1px 1px rgba(0,0,0,0.25); font-size: 2.2em;margin: .2em;  }
                    h4 { text-shadow: 1px 1px 1px rgba(0,0,0,0.25); font-size: 1.5em; margin: .2em; }
                    p { text-shadow: 1px 1px 1px rgba(0,0,0,0.25); font-size: 1.2em; margin: .5em .2em; }
                    ul {
                        padding-bottom: 1em;
                        padding-left: 2em;
                    }
                    a {
                        color: darkgreen;
                        text-decoration: none;
                    }
                    #error h1 { color : #fee; text-shadow: 0 0 2px white, 0 0 15px #600, 3px 3px 8px rgba(50,0,0,0.9); }
                    body#error  { background-color : #533 }
                    #error div { background-color : rgba(255,255,255,0.9); }
                    #error p { color : darkred; }
                    #error h3 { color : red; }
                    #error pre.xdebug-var-dump {font-size:0.8em}

                    #answer h1 { color : #efe; text-shadow: 0 0 2px white, 0 0 15px #060, 3px 3px 8px rgba(0,50,0,0.9); }
                    body#answer  { background-color : #353 }
                    #answer div { background-color : rgba(255,255,255,0.9); }
                    #answer p { color : #575; }
                    #answer h3 { color : #686; }
                </style>
            </head>';
    }

    /**
     * Render end of HTML page
     *
     * @return null
     */
    protected function layoutStop() {
        echo '</html>';
    }

}

?>
