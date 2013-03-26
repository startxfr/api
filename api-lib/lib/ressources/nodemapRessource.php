<?php

/**
 * This class deliver test method
 * availables url are
 * - http://api.startx.fr/v1/api/test         -> all test methods
 * - http://api.startx.fr/v1/api/test/echo    -> return the input given (use GET with message=xxxx params. POST or PUT)
 * - http://api.startx.fr/v1/api/test/time    -> return the time, only with GET
 * - http://api.startx.fr/v1/api/test/error   -> return a test error message, only with GET
 */
class nodemapRessource extends readonlyRessource implements IRessource {

    public function readAction() {
        $api = Api::getInstance();
        $api->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' ressource", $this->getConfigs(), 3);
        $tree = $this->extractTreeStructure();
        $outputType = $api->getOutput()->getConfig("_id");
        $message = sprintf($this->getConfig('message_service_read','message service read'), count($return));
        if ($outputType == "html")
            $tree = $this->generateHtmlTree($tree);
        $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getRessourceTrace(__FUNCTION__, false), 1);
        $api->getOutput()->renderOk($message, $tree);
        return true;
    }

    private function extractTreeStructure($nodes = null, $path = null) {
        $api = Api::getInstance();
        if ($nodes == null)
            $nodes = array($api->getConfig("tree"));
        if ($path == null)
            $path = $api->getInput()->getProtocol() . $api->getInput()->getHost() . $api->getInput()->getRoot();
        $treeOut = array();
        if (is_array($nodes)) {
            foreach ($nodes as $node) {
                if ($node['path'] == '/')
                    $url = $path;
                else
                    $url = $path . $node['path'] . '/';
                $params = (count($_GET) > 0) ? '?'.http_build_query($_GET) : '';
                if (array_key_exists('children', $node))
                    $treeOut[] = array(
                        "name" => $node['path'],
                        "url" => $url . $params,
                        "desc" => $node['desc'],
                        "children" => $this->extractTreeStructure($node['children'], $url)
                    );
                else
                    $treeOut[] = array(
                        "name" => $node['path'],
                        "url" => $url . $params,
                        "desc" => $node['desc']
                    );
            }
        }
        return $treeOut;
    }

    private function generateHtmlTree($tree) {
        if (is_array($tree)) {
            $html = "<ul>";
            foreach ($tree as $node) {
                $html .= "<li><a href=\"" . $node['url'] . "\"><h4>" . $node['name'] . "</h4><p>" . $node['desc'] . "</p></a>";
                if (array_key_exists('children', $node))
                    $html .= $this->generateHtmlTree($node['children']);
                $html .= "</li>";
            }
            $html .= "</ul>";
        }
        return $html;
    }

}

?>
