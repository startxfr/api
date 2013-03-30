<?php

/**
 * This class allow you to interact with file sytem local storage.
 *
 * @package  SXAPI.Store
 * @author   Dev Team <dev@startx.fr>
 * @see      DefaultStore
 * @link     https://github.com/startxfr/sxapi/wiki/Store
 */
class LocalStore extends DefaultStore implements IStorage {

    protected $fileHandler = null;
    protected $fullPath = null;

    public function __construct($configXml) {
        parent::__construct($configXml);
        $this->init();
    }

    function __destruct() {
        $this->disconnect();
    }

    public function init() {
            if ($this->getConfig('path') == '')
                $this->setConfig('path',getcwd());
            if ($this->getConfig('dateformat') == '')
                $this->setConfig('dateformat','Y-m-d');
            if ($this->getConfig('file') == '')
                throw new StoreException("your storage[type=local] tag should contain the 'file' attribute");
           return $this;
    }

    public function connect() {
        $mode = ($this->getConfig('mode') == 'append') ? 'a+b' : 'r+b';
        $filename = sprintf($this->getConfig('file'), date($this->getConfig('dateformat')));
        $this->fullPath = $this->getConfig('path') . DS . $filename;
        if (!@file_exists($this->fullPath)) {
            if (!@touch($this->fullPath))
                 throw new StoreException("we could not create file " . $filename . " into " . $this->getConfig('path') . DS);
        }
        $this->fileHandler = @fopen($this->fullPath, $mode);
        if ($this->fileHandler === false)
            throw new StoreException('we could not open file ' . $filename . ' in directory ' . $this->getConfig('path') . DS);
            return $this;
    }

    public function reconnect() {
        $this->disconnect()->connect();
        return $this;
    }

    public function disconnect() {
        fclose($this->fileHandler);
        return $this;
    }

    public function get() {
        $result = $this->readFile();
        return $result;
    }

    public function set($data) {
        $result = $this->recordFile($data);
        return $result;
    }

    protected function recordFile($message) {
        if (fwrite($this->fileHandler, $message) === false)
            throw new StoreException('we could not write into file ' . $filename . ' located in ' . $this->fullPath);
        return $this;
    }

    protected function readFile() {
        $contents = @fread($this->fileHandler, filesize($this->fullPath));
        return $contents;
    }

    public function getLs($filter = '') {
        $handle = @opendir($this->getConfig('path'));
        if ($handle) {
            $fileList = array();
            while (false !== ($entry = readdir($handle)))
                if ($entry != "." && $entry != "..")
                    $fileList[$entry] = $entry;
            closedir($handle);
            if (count($fileList) > 0) {
                if ($filter != '') {
                    $fileToAnalyse = array();
                    $prefix = substr($filter, (strpos($filter, '%s') + 2));
                    // parcours de la liste des fichiers dans le répertoire à la recherche des fichiers avec l'extention recherchée
                    foreach ($fileList as $file)
                        if (substr($file, (strlen($prefix) * -1)) == $prefix)
                            $fileToAnalyse[] = $file;
                    return $fileToAnalyse;
                }
                else
                    return $fileList;
            }
        }
        else
            throw new StoreException('we could not go to path ' . $this->getConfig('path') . " on local storage ");
    }



    public function read($table,$criteria = array(),$order = array(),$start = 0,$stop = 30) {
        $this->setConfig('file',$table);
        $this->connect();
        $criteria = $order = $start = $stop = null;
        return $this->readFile();
    }
    public function create($table,$data) {
        $this->setConfig('mode','append');
        $this->setConfig('file',$table);
        $this->connect();
        return $this->recordFile($data);
    }
    public function update($table,$key,$id,$data) {
        $this->setConfig('mode','replace');
        $this->setConfig('file',$table);
        $this->connect();
        $key = $id = null;
        return $this->recordFile($data);
    }
    public function delete($table,$key,$id) {
        $this->setConfig('file',$table);
        $this->connect();
        $key = $id = null;
        return $this->recordFile($data);
    }
}

?>