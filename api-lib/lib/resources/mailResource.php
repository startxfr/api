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

    static public $ConfDesc = '{"class_name":"mailResource",
                                "desc":"Send mail",
                                "properties":
	[
		{
			"name":"server",
			"type":"array",
			"mandatory":"true",
			"desc":"mailer configuration. should contain: host, port, username, password"
		},
		{
			"name":"default_params",
			"type":"array",
			"mandatory":"true",
			"desc":"default values for: to, subject, body"
                }
	]
}'
;
    
    public function __construct($config) {
        parent::__construct($config);    
        require_once(LIBPATHEXT . 'PHPMailer' . DS .  'PHPMailerAutoload.php');
        if ($this->getConfig('server', '') == '') {
            Api::getInstance()->logError(906, get_class($this) . " resource config should contain the 'server' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource config should contain the 'server' attribute");
        }
        if ($this->getConfig('default_params', '') == '') {
            Api::getInstance()->logError(906, get_class($this) . " resource config should contain the 'default_params' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource config should contain the 'default_params' attribute");
        }
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
        $this->mail->setFrom($server['username'], 'Startx');
        $api->logDebug(576, "mailer init");
        return $this;
    }
    
    public function createAction() {
        Api::getInstance()->logDebug(930, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getConfigs(), 3);
        return $this->readAction();
    }
    
    public function readAction() {
        $api = Api::getInstance();
        $api->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getConfigs(), 3);
        $default_params = $this->getConfig('default_params');        
        $data = $this->filterParams($api->getInput()->getParams(), "input"); 
        $defaultBody = $default_params['body'];        
        if (count($this->getPrevOutput()) !== 0)            
            $defaultBody = implode("\n", $this->getPrevOutput());          
        $to = (isset($data['to']) ? $data['to'] : $default_params['to']);
        $sub = (isset($data['subject']) ? $data['subject'] : $default_params['subject']);
        $body = (isset($data['body']) ? $data['body'] : $defaultBody);               
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
            $api->logWarn(572, "mailer error when sending message because ".$this->mail->ErrorInfo , $this->getConfigs());
            return false;
        }
        else {
            $api->logDebug(576, "msg sent");
            return true;
        }
    }

}

?>
