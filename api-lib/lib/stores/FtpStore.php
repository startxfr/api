<?php

/**
 * This class allow you to interact with a Ftp server
 *
 * @package  SXAPI.Store
 * @author   Dev Team <dev@startx.fr>
 * @see      DefaultStore
 * @link     https://github.com/startxfr/sxapi/wiki/Store
 */
class FtpStore extends DefaultStore implements IStorage {

    protected $ftpHandler = null;
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
            if ($this->getConfig('server') == '')
                throw new StoreException("your storage[type=ftp] tag should contain the 'server' attribute");
            if ($this->getConfig('username') == '')
                throw new StoreException("your storage[type=ftp] tag should contain the 'username' attribute");
            if ($this->getConfig('passwd') == '')
                throw new StoreException("your storage[type=ftp] tag should contain the 'passwd' attribute");
            if ($this->getConfig('file') == '')
                throw new StoreException("your storage[type=ftp] tag should contain the 'file' attribute");
         return $this;
    }

    public function connect() {
        // connexion au server FTP
        $this->setFile($this->getConfig('file'));
        $this->ftpHandler = ftp_connect($this->getConfig('server'));
        if ($this->ftpHandler !== false) {
            // authentification sur le server FTP
            if (@ftp_login($this->ftpHandler, $this->getConfig('username'), $this->getConfig('passwd'))) {
                if (!@ftp_pasv($this->ftpHandler, true))
                    throw new StoreException('ftp storage is not in passive mode');
                 return $this;
            }
            else
                throw new StoreException('we could not authenticate ftp connexion with ' . $this->getConfig('server') . " using " . $this->getConfig('user') . ' user');
        }
        else
            throw new StoreException('we could not open ftp connexion with ' . $this->getConfig('server'));
             return $this;
    }

    public function reconnect() {
        $this->disconnect()->connect();
        return $this;
    }

    public function disconnect() {
        @ftp_close($this->ftpHandler);
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

    public function setFile($filename) {
         // connexion au server FTP
        $filename = sprintf($filename, date($this->getConfig('dateformat')));
        $this->fullPath = $this->getConfig('path') . "/" . $filename;
        return $this;
    }

    protected function recordFile($message) {
        $tempHandle = fopen('php://temp', 'r+b');
        if (fwrite($tempHandle, $message) === false)
            throw new StoreException('we could not write into memory before sending via ftp ');
        rewind($tempHandle);
        if (ftp_fput($this->ftpHandler, $this->fullPath, $tempHandle, FTP_BINARY)) {
             fclose($tempHandle);
        }
        else
            throw new StoreException('we could not write into file ' . $this->fullPath . ' located on FTP server ' . $this->getConfig('server'));
        return $this;
    }

    protected function readFile() {
        if (@ftp_chdir($this->ftpHandler, $this->getConfig('path'))) {
            $tempHandle = fopen('php://temp', 'r+');
            if (@ftp_fget($this->ftpHandler, $tempHandle, $this->fullPath, FTP_BINARY)) {
                rewind($tempHandle);
                $contents = stream_get_contents($tempHandle);
                fclose($tempHandle);
                return $contents;
            } else
                throw new StoreException('we could not read ' . $this->fullPath . " on ftp server " . $this->getConfig('server'));
        }
        else
            throw new StoreException('we could not go to path ' . $this->getConfig('path') . " on ftp server " . $this->getConfig('server'));
    }

    public function getLs($filter = '') {
        if (@ftp_chdir($this->ftpHandler, $this->getConfig('path'))) {
            $fileList = ftp_nlist($this->ftpHandler, '.');
            if ($fileList !== false and is_array($fileList)) {
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
            else
                throw new StoreException('we could not list content inside ' . $this->fullPath . " directory on ftp server " . $this->getConfig('server'));
        }
        else
            throw new StoreException('we could not go to path ' . $this->getConfig('path') . " on ftp server " . $this->getConfig('server'));
    }







    public function search($table,$criteria = array(),$order = array(),$start = 0,$stop = 30) {
        return $this;
    }
    public function insert($table,$data) {
        return $this;
    }
    public function update($table,$key,$id,$data) {
        return $this;
    }
    public function replace($table,$key,$id,$data) {
        return $this;
    }
    public function delete($table,$key,$id) {
        return $this;
    }


}

?>