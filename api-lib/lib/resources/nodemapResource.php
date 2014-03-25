<?php

/**
 * This resource return a list of all child resources for a given node
 *
 * @package  SXAPI.Resource
 * @author   Dev Team <dev@startx.fr>
 * @see      readonlyResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class nodemapResource extends readonlyResource implements IResource {

    public function readAction() {
        $api = Api::getInstance();
        $api->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getConfigs(), 3);
        if ($this->getConfig('root_path', false)) {
            $path = null;
        } else {
            $path = $api->getInput()->getRootUrl() . $this->getConfig('sub_path', $api->getInput()->getPath());
        }
        $tree = $this->extractTreeStructure($path);
        $outputType = $api->getOutput()->getConfig("class");
        $message = sprintf($this->getConfig('message_service_read', 'message service read'), count($tree));
        $tree = ($outputType == "HtmlOutput") ? $this->generateHtmlTree($tree) : $tree[0];
        $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
        $api->getOutput()->renderOk($message, $tree);
        return true;
    }

    private function extractTreeStructure($limitPath = null, $nodes = null, $path = null) {
        $api = Api::getInstance();
        if ($nodes == null and $path == null)
            $nodes = array($api->getConfig("tree"));
        if ($path == null)
            $path = $api->getInput()->getRootUrl();
        $treeOut = array();
        $params = (count($_GET) > 0) ? '?' . http_build_query($_GET) : '';
        if (is_array($nodes)) {
            foreach ($nodes as $node) {
                if ($node['path'] == '/') {
                    $url = $path;
                } else {
                    $url = $path . $node['path'] . '/';
                }
                if (array_key_exists('children', $node)) {
                    $treeOut[] = array(
                        "name" => @$node['path'],
                        "url" => $url . $params,
                        "desc" => @$node['desc'],
                        "children" => $this->extractTreeStructure($limitPath, $node['children'], $url)
                    );
                } else {
                    $treeOut[] = array(
                        "name" => @$node['path'],
                        "url" => $url . $params,
                        "desc" => @$node['desc']
                    );
                }
            }
        }
        return $treeOut;
    }

    private function generateHtmlTree($tree) {
        if (!is_array($tree))
            $tree = array($tree);
        elseif (is_object($tree))
            $tree = (array) $tree;
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
