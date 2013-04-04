<?php

/**
 * Class used to render data in HTML5 document
 *
 * @package  SXAPI.Output
 * @author   Mallowtek <mallowtek@gmail.com>
 * @see      DefaultOutput
 * @link      https://github.com/startxfr/sxapi/wiki/Outputs
 */
class HtmlOutput extends DefaultOutput implements IOutput {

    /**
     * Render the view
     *
     * @param array $content data to be rendered
     * @return void this method echo result and exit program
     */
    protected function render($data) {
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
     * @param   string  message describing the returned data
     * @param   mixed   data to be rendered and returned to the client
     * @param   int     counter to indicate if there is more data that the returned set
     * @return  void
     * @see     self::render();
     */
    public function renderOk($message, $data, $count = null) {
        if ($count == null and is_array($data))
            $count = " (returning " . count($data) . " results)";
        elseif ($count > 0)
            $count = " (returning " . $count . " results)";
        else
            $count = "";
        $otherInfo = '<details open><summary><h3>Answer</h3></summary><p>' . $this->layoutContent($data) . '</p></details>';
        $html = '
                <body id="answer">
                    <header><h1><span>SX</span>API</h1><h2>v 0.1</h2><h3>POSITIVE RESPONSE</h3></header>
                    <article>
                        <header>
                            <h2>RETURN RESPONSE</h2>
                        </header>
                        <details><summary><h3>Message</h3></summary><p>' . $message . $count . '</p></details>
                        ' . $otherInfo . '
                        <details><summary><h3>Further information</h3></summary><p>If you need some other informations, please contact dev@startx.fr or visit <a href="https://github.com/startxfr/sxapi" target="_blank">project page</a> hosted on github. You can also find some useful informations on <a href="https://github.com/startxfr/sxapi/wiki" target="_blank">wiki pages</a></p></details>
                    </article>
                    <footer><p>&copy; 2013 - <a href="https://github.com/startxfr/sxapi" target="_blank">SXAPI</a> by <a href="http://www.startx.fr" target="_blank">STARTX</a></p></footer>
                </body>';
        Api::logDebug(341, "Prepare OK rendering in '" . get_class($this) . "' connector for message : " . $message, $html, 5);
        return $this->render($html);
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
        $otherDebug = "";
        if (DEBUG)
            $otherDebug = '<details><summary><h3>Trace or context</h3></summary><p>' . $this->layoutContent($other) . '</p></details>';
        else
            $otherDebug = '<details><summary><h3>Trace or context</h3></summary><p><i>You must activate DEBUG constant and set it to true in order to see error detail</i></p></details>';
        $html = '
                <body id="error">
                    <header><h1><span>SX</span>API</h1><h2>v 0.1</h2><h3>NEGATIVE RESPONSE</h3></header>
                    <article>
                        <header>
                            <h2>RETURN ERROR CODE N&deg;' . $code . '</h2>
                        </header>
                        <details open><summary><h3>Message</h3></summary><p>' . $message . '</p></details>
                        ' . $otherDebug . '
                         <details><summary><h3>Further information</h3></summary><p>If you need some other informations, please contact dev@startx.fr or visit <a href="https://github.com/startxfr/sxapi" target="_blank">project page</a> hosted on github. You can also find some useful informations on <a href="https://github.com/startxfr/sxapi/wiki" target="_blank">wiki pages</a></p></details>
                   </article>
                   <footer><p>&copy; 2013 - <a href="https://github.com/startxfr/sxapi" target="_blank">SXAPI</a> by <a href="http://www.startx.fr" target="_blank">STARTX</a></p></footer>
               </body>';
        Api::logDebug(345, "Prepare ERROR rendering in '" . get_class($this) . "' connector for message : " . $message, $html, 5);
        return $this->render($html);
    }

    /**
     * Recursively render an array to an HTML list and details for folding results
     *
     * @param   mixed   data to be rendered and returned to the client
     * @return string
     */
    protected function layoutContent($content) {
        if (is_string($content))
            return $content;
        elseif (is_null($content))
            return 'null';
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
            if (is_array($value)) {
                $ech .= "<details open><summary>" . $field . "</summary> ";
                $ech .= $this->layoutContent($value);
                $ech .= "</details>";
            } else {
                $ech .= "<li><strong>" . $field . "</strong> ";
                $value = htmlentities($value, ENT_COMPAT, 'UTF-8');
                if ((strpos($value, 'http://') === 0) || (strpos($value, 'https://') === 0))
                    $ech .= "<a href=\"" . $value . "\">" . $value . "</a>";
                else
                    $ech .= $value;
                $ech .= "</li>\n";
            }
        }
        $ech .= "</ul>\n";
        return $ech;
    }

    /**
     * Render start of HTML page
     *
     * @return void start sending output to client application
     */
    protected function layoutStart() {
        echo '<!DOCTYPE html>
            <html>
            <head>
                <title>SX-API v1 - '.Api::getInstance()->getInput()->getPath().'</title>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                <style>
                    body {font-family: Helvetica, Arial, sans-serif;font-size: 14px;color: #333;padding: 0;margin: 0;}
                    body > header { width: 900px; padding: 1em; margin: 0 auto 1em auto  }
                    body > article {box-shadow: 5px 5px 10px  rgba(0,0,0,0.8); padding: 0 0 .5em 0; border-radius: 5px; width: 900px; margin: 2em auto 0 auto }
                    body > footer { width: 900px; padding: 0em 0 1em 1em; margin: 1.1em auto 1em auto; color: white; font-size: .75em  }
                    h1 { margin: 0; font-size: 3em; line-height: .8em }
                    h2 { text-shadow: 1px 1px 1px rgba(0,0,0,0.25); font-size: 2.2em;margin: .2em;  }
                    h3 { text-shadow: 1px 1px 1px rgba(0,0,0,0.25); font-size: 1.5em;margin: .2em;  }
                    h4 { text-shadow: 1px 1px 1px rgba(0,0,0,0.25); font-size: 1.2em; margin: .2em; }
                    p { text-shadow: 1px 1px 1px rgba(0,0,0,0.25); font-size: 1em; margin: .5em .2em; }
                    ul {
                        padding-bottom: 1em;
                        padding-left: 2em;
                    }
                    a {
                        color: white;
                        text-decoration: none;
                    }
                    body > header h1 { color: #000e44; text-shadow: 0 0 3px  rgb(255,255,255),  0 0 10px  rgba(255,255,255,1),  0 0 20px  rgba(255,255,255,0.6); float: left }
                    body > header h1 span { color: #0c6f5e }
                    body > header h2 { color: white; font-size: .7em; font-weight: normal; margin: 2.2em 0 0 1em; float: left }
                    body > header h3 { color: #0c6f5e; margin: 0; font-size: 2em; float: right }
                    article header { margin: 0;padding: .3em; box-shadow: 0 0 5px  rgba(0,0,0,0.9); border-radius: 5px 5px 0 0; width: 892px; }
                    article h3 { display: inline; }
                    article > details { margin: 1em; }


                    body#error  { background-color : #533;}
                    #error article { color : darkred; background-color : rgba(255,255,255,0.9); clear: both }
                    #error article h2 { color : white; text-shadow: 0 0 2px white, 0 0 15px #600, 3px 3px 8px rgba(50,0,0,0.9); }
                    #error article header { background-color : rgba(85,51,51,0.6); }
                    #error h3, #error article a { color : #744; }
                    #error article a:hover { color : #533; }
                    #error pre.xdebug-var-dump {font-size:0.8em}

                    body#answer  { background-color : #353 }
                    #answer article { color : #575;background-color : rgba(255,255,255,0.9); clear: both }
                    #answer article h2 { color : white; text-shadow: 0 0 2px rgba(11,45,11,0.9), 0 0 10px rgba(11,45,11,0.8); }
                    #answer article header { background-color : rgba(51,85,51,0.6); }
                    #answer h3, #answer article a { color : #686; }
                    #answer article a:hover { color : #353; }
                </style>
            </head>';
    }

    /**
     * Render end of HTML page
     *
     * @return void continue sending output to client application
     */
    protected function layoutStop() {
        echo '</html>';
    }

}

?>
