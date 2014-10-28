<?php

/**
 * This resource is to be used to sent message
 *
 * @package  SXAPI.Resource
 * @author   Dev Team <dev@startx.fr>
 * @see      defaultResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class mailResource extends linkableResource implements IResource {

    public function __construct($config) {
        parent::__construct($config);    
        require_once(LIBPATHEXT . 'PHPMailer' . DS .  'PHPMailerAutoload.php');
    }
    
    public function createAction() {
        Api::getInstance()->logDebug(930, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getConfigs(), 3);
        return $this->readAction();
    }

    public function readAction() {
        $api = Api::getInstance();
        $api->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getConfigs(), 3);
        $overwrite_params = $this->getConfig('overwrite_params');
        $default_params = $this->getConfig('default_params');
        var_dump($default_params);
        var_dump($overwrite_params);
        $p_to = 'to';
        $p_sub = 'subject';
        $p_body = 'body';
        foreach ($overwrite_params as $param) {
            switch($param['map'])
            {
                case $p_to:
                    $p_to = $param['in'];
                    break;
                case $p_sub:
                    $p_sub = $param['in'];
                    break;
                case $p_body:
                    $p_body = $param['in'];
                    break;               
            }
        }        
        $to = $api->getInput()->getParam($p_to, $default_params['to']); 
        $sub = $api->getInput()->getParam($p_sub, $default_params['sub']);
        $defaultBody = $default_params['body'];
        var_dump($to);
        var_dump($sub);
        var_dump($defaultBody);
        exit(0);
        if (count($this->getPrevOutput()) !== 0)            
            $defaultBody = implode("\n", $this->getPrevOutput());                   
        $body = $api->getInput()->getParam($p_body, $defaultBody);
        $bool = $this->sendMail($to, $sub, $body);
        if ($bool)
            return array($bool, "mail sent", array("to" => $to, "sub" => $sub, "body" => $body));
        return array($bool, 1002, "mail error");
    }
    
    public function updateAction() {
        Api::getInstance()->logDebug(950, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getConfigs(), 3);
        return $this->readAction();
    }

    public function deleteAction() {
        Api::getInstance()->logDebug(970, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getConfigs(), 3);
        return $this->readAction();
    }

    public function init() {
        $api = Api::getInstance();
        $api->logDebug(576, "launch mailer init");
        $server = $this->getConfig('server');
        //Create a new PHPMailer instance
        $this->mail = new PHPMailer;
        //Tell PHPMailer to use SMTP
        $this->mail->isSMTP();
        //Enable SMTP debugging
        // 0 = off (for production use)
        // 1 = client messages
        // 2 = client and server messages
        $this->mail->SMTPDebug = 0;
        //Ask for HTML-friendly debug output
        $this->mail->Debugoutput = 'html';
        //Set the hostname of the mail server
        $this->mail->Host = $server['host'];
        //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
        $this->mail->Port = $server['port'];
        //Set the encryption system to use - ssl (deprecated) or tls
        $this->mail->SMTPSecure = 'tls';
        //Whether to use SMTP authentication
        $this->mail->SMTPAuth = true;
        //Username to use for SMTP authentication - use full email address for gmail
        $this->mail->Username = $server['username'];
        //Password to use for SMTP authentication
        $this->mail->Password = $server['password'];
        $api->logDebug(576, "mailer init");
        return $this;
    }
    
    public function sendMail($to = "", $subject = "", $body = "") {
        $api = Api::getInstance();
        //Set who the message is to be sent to
        $this->mail->addAddress($to);
        //Set the subject line
        $this->mail->Subject = $subject;    
        //Replace the plain text body with one created manually
        //$this->mail->isHTML(false);
        $this->mail->Body = $body;        
        //send the message, check for errors
        if (!$this->mail->send()) {       
            $api->logWarn(572, "mailer res error when sending mess " , $this->mail->ErrorInfo);
            return false;
        }
        else {
            $api->logDebug(576, "msg sent");
            return true;
        }
    }

}

?>
