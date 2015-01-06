<?php

/**
 * This resource is used to interact (read - write) with nosql data, recorded in a store.
 * Data are returned to the client using the output method.
 *
 * @package  SXAPI.Resource.Model
 * @author   Dev Team <dev@startx.fr>
 * @see      defaultModelResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class produitsCeaStartxResource extends mysqlStoreResource implements IResource {

    static public $ConfDesc = '{"class_name":"produitsCeaStartxResource",
        "desc":"get list of products available for the STARTX-CEA project",
        "properties": [  ]
      }';

    public function init() {
        parent::init();
        return $this;
    }

    public function readAction() {
        $api = Api::getInstance();
        $api->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        try {
            $input = $api->getInput();
            $nextPath = $input->getElement($input->getElementPosition($this->getConfig('path')) + 1);
            if ($nextPath == $this->getConfig('api_subpath_downloadzip')) {
                return $this->uploadLastCatalogueZip();
            } else if ($nextPath == $this->getConfig('api_subpath_downloadfile')) {
                return $this->uploadLastCatalogueFile();
            } else {
                $search = $this->filterParams($input->getParams(), "input");
                $withImages = (array_key_exists('images', $search) and $search['images'] == "yes") ? true : false;
                $withFtp = (array_key_exists('ftp', $search) and $search['ftp'] == "yes") ? true : false;
                $destpath = $this->getConfig('workdir');
                exec("rm -rf $destpath; mkdir $destpath");
                // lancement du téléchargement du pack d'image pour l'ajout dans la sortie
                if ($withImages) {
                    $result = $this->downloadAndUpdateImgLib();
                    if ($result !== true) {
                        return $result;
                    }
                }
                //affichage de toutes les clefs 
                $dblist = $this->getDataFromStorage();
                $csvstring = $this->generateCsvFromData($dblist, $withImages);
                if (!$csvstring[0]) {
                    return $csvstring;
                }
                $this->packAddCatalogue($csvstring[1]);
                if ($withImages) {
                    $this->packAddImg($csvstring[2]);
                    return ($withFtp) ?
                            $this->outputFtpWithImages($csvstring[1]) :
                            $this->outputDownloadWithImages($csvstring[1]);
                } else {
                    return ($withFtp) ?
                            $this->outputFtpWithoutImages($csvstring[1]) :
                            $this->outputDownloadWithoutImages($csvstring[1]);
                }
            }
        } catch (Exception $exc) {
            $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            return array(false, $exc->getCode(), $exc->getMessage(), array(), 500);
        }
        return true;
    }

    public function createAction() {
        return $this->readAction();
    }

    public function updateAction() {
        return $this->readAction();
    }

    public function deleteAction() {
        return $this->readAction();
    }

    protected function outputDownloadWithImages($csvstring) {
        $api = Api::getInstance();
        $input = $api->getInput();
        $destpath = $this->getConfig('workdir');
        $downloadUrl = $input->getRootUrl() . $input->getPath() . '/' . $this->getConfig('api_subpath_downloadzip');
        $ctlgname = $this->getConfig('cataloguepack_filename');
        exec("cd $destpath;  zip -r $ctlgname .; cp $ctlgname " . $this->getConfig('tmpdir'));
        $message = sprintf($this->getConfig('message_download_withimg', 'download catalogue with images'), $downloadUrl);
        $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
        return array(true, $message, $downloadUrl, count($csvstring));
    }

    protected function outputDownloadWithoutImages($csvstring) {
        $api = Api::getInstance();
        $input = $api->getInput();
        $destpath = $this->getConfig('workdir');
        $downloadUrl = $input->getRootUrl() . $input->getPath() . '/' . $this->getConfig('api_subpath_downloadfile');
        $ctlgname = $this->getConfig('catalogue_filename');
        exec("cd $destpath;  cp $ctlgname " . $this->getConfig('tmpdir'));
        $message = sprintf($this->getConfig('message_download_withoutimg', 'download catalogue with images'), $downloadUrl);
        $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
        return array(true, $message, $downloadUrl, count($csvstring));
    }

    protected function outputFtpWithImages($csvstring) {
        return $this->outputFtp($csvstring, 'message_ftp_withimg');
    }

    protected function outputFtpWithoutImages($csvstring) {
        return $this->outputFtp($csvstring, 'message_ftp_withoutimg');
    }

    protected function outputFtp($csvstring, $msg_template = 'message_ftp_withoutimg') {
        $api = Api::getInstance();
        $servername = $this->getConfig('ftp_server');
        $sourcepath = $this->getConfig('workdir');
        $this->ftpcx = @ftp_connect($servername);
        if ($this->ftpcx !== false) {
            if (@ftp_login($this->ftpcx, $this->getConfig('ftp_user'), $this->getConfig('ftp_pwd'))) {
                ftp_pasv($this->ftpcx, true);
                $dir_handle = @opendir($sourcepath);
                if ($dir_handle !== false) {
                    $this->ftp_putAll($sourcepath, $this->getConfig('ftp_dir'));
                    ftp_close($this->ftpcx);
                    $message = sprintf($this->getConfig($msg_template, 'FTP-uploaded catalogue'), $servername, $this->getConfig('ftp_dir'));
                    $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
                    return array(true, $message, true, count($csvstring));
                } else {
                    $message = sprintf($this->getConfig('message_ftp_error_noworkdir', 'could\'t find the working directory with generated file. Please regenerate a new one'), $destpath);
                    $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : could not find working directory $destpath", $this->getConfigs());
                    return array(false, 910, $message, null, 500);
                }
            } else {
                $message = sprintf($this->getConfig('message_ftp_error_auth', 'error in authenticating to the ftp server'), $servername, $this->getConfig('ftp_user'));
                $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : could not authenticate to the ftp server '$servername' with user " . $this->getConfig('ftp_user'), $this->getConfigs());
                return array(false, 910, $message, null, 500);
            }
        } else {
            $message = sprintf($this->getConfig('message_ftp_error_connection', 'error in connecting to the ftp server'), $servername);
            $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : could not connect to the ftp server '$servername'", $this->getConfigs());
            return array(false, 910, $message, null, 500);
        }
    }

    protected function ftp_putAll($src_dir, $dst_dir) {
        $api = Api::getInstance();
        $d = dir($src_dir);
        while ($file = $d->read()) { // do this for each file in the directory
            if ($file != "." && $file != "..") { // to prevent an infinite loop
                if (is_dir($src_dir . "/" . $file)) { // do the following if it is a directory
                    if (!@ftp_chdir($this->ftpcx, $dst_dir . "/" . $file)) {
                        ftp_mkdir($this->ftpcx, $dst_dir . "/" . $file); // create directories that do not yet exist
                    }
                    $this->ftp_putAll($src_dir . "/" . $file, $dst_dir . "/" . $file); // recursive part
                } else {
                    if (ftp_put($this->ftpcx, $dst_dir . "/" . $file, $src_dir . "/" . $file, FTP_BINARY)) {
                        $api->logInfo(910, "Info on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : recorded file '$file' into directory '$dst_dir' on the FTP server ", $this->getConfigs());
                
                    } else {
                        $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : problem when recording file '$file' into directory '$dst_dir' on the FTP server " , $this->getConfigs());
                 }
                }
            }
        }
        $d->close();
    }

    protected function packAddImg($listImg) {
        $destpath = $this->getConfig('workdir');
        $imgsrcpath = $this->getConfig('libimg_dir');
        exec("mkdir $destpath/IMG");
        foreach ($listImg as $filename) {
            exec("cp $imgsrcpath/$filename $destpath/IMG/$filename");
        }
        return true;
    }

    protected function packAddCatalogue($csvstring) {
        $file = fopen($this->getConfig('workdir') . "/" . $this->getConfig('catalogue_filename'), "w+");
        fputs($file, $csvstring);
        fclose($file);
        return true;
    }

    protected function generateCsvFromData($datas, $withImages = true) {
        $csvstring = $listImg = array();
        foreach ($datas as $value) {
            $imgName = '';
            if ($withImages) {
                $imgName = $value['catalogue'] . ".png";
                $listImg[$imgName] = $imgName;
            }
            $entry = array(
                'Action' => "A",
                'Référence article fournisseur' => $this->cleanCsvField($value['id'], 35),
                'Description courte' => $this->cleanCsvField($value['nom'], 40),
                'Description détaillée de l\'article' => $this->cleanCsvField($value['nom'], 512),
                'Table des matières niveau 1' => $this->cleanCsvField($value['nom_prodfam'], 132),
                'Table des matières niveau 2' => $this->cleanCsvField("", 132),
                'Prix' => $value['prix'],
                'Quantité de l\'unité de prix' => 1,
                'UOM' => "PCE",
                'Conditionnement' => "virtuel",
                'Unité de vente' => "",
                'Prix valable pour une commande de' => "",
                'Delai de livraison' => 7,
                'Fabriquant/Marque' => "Red Hat",
                'Img' => $this->cleanCsvField($imgName, 50),
                'Pdf' => "",
                'Url' => "http://cea.startx.fr/produit.html#" . $value['id'],
                'Groupe marchandise' => "XXXX",
                'Surcout' => "",
                'Info complémentaire' => ""
            );
            $csvstring[] = $entry;
        }
        return array(
            true,
            $this->createCsvString($csvstring, true, "\t"),
            $listImg);
    }

    protected function getDataFromStorage() {
        $api = Api::getInstance();
        $search = $this->filterParams($api->getInput()->getParams(), "input");
        $utf = "SET NAMES utf8 ; ";
        $this->getStorage()->execQuery($utf);
        $criteria = "stillAvailable_prod = '1'";
        if (array_key_exists('famille_prod', $search) and $search['famille_prod'] != "") {
            $criteria .= " AND famille_prod IN(" . $search['famille_prod'] . ")";
        }
        $sql = "SELECT * FROM produit
                        LEFT JOIN ref_prodfamille ON ref_prodfamille.id_prodfam = produit.famille_prod
                        WHERE $criteria ORDER BY id_prod ASC";
        $data = $this->getStorage()->execQuery($sql);
        return $this->filterResults($data);
    }

    protected function downloadAndUpdateImgLib() {
        $api = Api::getInstance();
        $filename = $this->getConfig('libimg_zipfilename');
        $sourcefile = $this->getConfig('libimg_sourcefile_url');
        $imgsrcpath = $this->getConfig('libimg_dir');
        exec("rm -rf $imgsrcpath; mkdir $imgsrcpath");
        // download fichier zip
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $sourcefile);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//                curl_setopt($ch, CURLOPT_SSLVERSION, 3);
//                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($ch);
        curl_close($ch);
        if (strlen($data) < 300) {
            $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' because could not download image pack ");
            return array(false, "910", "Impossible de trouver le pack d'image sur le serveur", array($sourcefile), 500);
        }
        // record fichier
        $file = fopen($imgsrcpath . '/' . $filename, "wb");
        fputs($file, $data);
        fclose($file);
        // decompression du fichier
        exec("cd $imgsrcpath; unzip $filename; rm $filename");
        $out = array();
        exec("ls $imgsrcpath", $out);
        if (count($out) <= 2) {
            $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' could not unzip image packaq");
            return array(false, "910", "Impossible d'extraire le pack d'image sur le serveur", array($sourcefile, $imgsrcpath), 500);
        }
        return true;
    }

    protected function uploadLastCatalogueZip() {
        $file_url = $this->getConfig('tmpdir') . $this->getConfig('cataloguepack_filename');
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary");
        header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\"");
        readfile($file_url); // do the double-download-dance (dirty but worky)
        exit;
    }

    protected function uploadLastCatalogueFile() {
        $file_url = $this->getConfig('tmpdir') . $this->getConfig('catalogue_filename');
        header('Content-Type: text/csv');
        header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\"");
        header("Pragma: no-cache");
        header("Expires: 0");
        header("Content-Transfer-Encoding: ISO-8859-1");
        echo mb_convert_encoding(readfile($file_url), 'utf8', 'iso-8859-1'); // do the double-download-dance (dirty but worky)
        exit;
    }

    protected function cleanCsvField($val, $limit = 0) {
        $val1 = str_replace(array("\n", "\r", "\t"), "", $val);
        $val2 = str_replace(array(";", ","), "-", $val1);
        $val3 = str_replace('"', "'", $val2);
        if ($limit > 0) {
            $val3 = substr($val3, 0, $limit);
        }
        return $val3;
    }

    protected function createCsvString($content, $firstline = true, $colsep = ";") {
        $outputBuffer = fopen("php://temp", 'w');
        if ($firstline and is_array($content[0])) {
            $keys = array_keys($content[0]);
            fputcsv($outputBuffer, $keys, $colsep);
        }
        foreach ($content as $val) {
            if (is_array($val)) {
                fputcsv($outputBuffer, $val, $colsep);
            } else {
                fputcsv($outputBuffer, array($val), $colsep);
            }
        }
        rewind($outputBuffer);
        $csvstring = stream_get_contents($outputBuffer);
        fclose($outputBuffer);
        return $csvstring;
    }

}
