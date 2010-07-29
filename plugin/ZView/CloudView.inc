<?php
loadPlugin(array('ZView/GeneralView', 'ZModels/CloudModel'));
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/

/**
 * Description of CloudView
 *
 * @author nm
 * @version 1.0
 */
class CloudView extends generalView {

    private $cloudLevel; //Le niveau du cloud
    private $tags; //Les tags à afficher
    private $module; //Le(s) module(s) à afficher
    private $tagsmodule; //Le module des tags

    /**
     * Constructeur de la classe qui instancie le javascript par défault
     */
    public function __construct() {
	$this->script = '<script>';
	$this->script .= '</script>';
	$this->cloudLevel = 5;
	$this->tags = array();
	$this->tagsmodule = array();
    }

    /**
     * Méthode qui génère le cloud
     * @return string Le contenu du cloud
     */
    private function WdCreateCloud() {
	if (empty($this->tags)) {
	    return "<li>Cette zone se remplira au fur et à mesure de vos recherches</li>";
	}

	$min = min(array_values($this->tags));
	$max = max(array_values($this->tags));
	$mid = ($max == $min) ? 1 : $max - $min;

	$rc = '';
	foreach ($this->tags as $tag => $value) {
	    $rc .= $this->create_cloud($tag,($value - $min) / $mid);
	}

	return $rc;
    }

    /**
     * Méthode qui génère un élément du cloud
     * @param string $tag Le tag
     * @param int $popularity La popularité du tag (pour l'afficher plus ou moins gros)
     * @return string le code HTML
     */
    private function create_cloud($tag, $popularity) {
	return
		"\t<li class=\"tag". (1 + ceil($popularity * ($this->cloudLevel - 1)))."\">" .
		' <a onclick="makeReplace(\''.$tag.'\', \''.$this->tagsmodule[$tag].'\');" name="'.$tag.'">'.$tag."</a></li>\n";
    }

    /**
     * Méthode qui récupère les informations et lance la génération du cloud tag
     * @param string/array $module Le ou les modules à mettre dans le cloud
     * @return string le code HTML du cloud
     */
    public function cloud($module = "") {
	$this->module = $module;

	$model = new CloudModel();
	$datas = $model->getCloud($this->module);
	if($datas[0]) {
	    foreach($datas[1] as $v) {
		$this->tags[$v['recherche_cloud']] = $v['total_cloud'];
		$this->tagsmodule[$v['recherche_cloud']] = $v['module_cloud'];
	    }
	    ksort($this->tags);

	    return '<div id="tags"><ul>'.$this->WdCreateCloud().'</ul></div>';
	}

    }
}
?>
