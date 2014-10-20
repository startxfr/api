<?php

/**
 * Plugin used to send mail.
 * Have a look at the plugin configuration for more detail about it configuration
 *
 * @class     mailerPlugin
 * @link      https://github.com/startxfr/sxapi/wiki/Plugins
 */
class mailerPlugin extends defaultPlugin implements IPlugin {

    private $mail;
    /**
     * init the output object
     *
     * @param array configuration of this object
     * @see Configurable
     * @return void
     */
    public function __construct($config) {
        parent::__construct($config);
        Api::logDebug(100, "Load '" . $id . "' " . get_class($this) . " plugin ", $config, 5);
        require_once LIBPATHEXT . 'PHPMailer' . DS .  'PHPMailerAutoload.php';
    }

    public function init() {
        $api = Api::getInstance();
        $api->logDebug(576, "launch mailer init");
        //Create a new PHPMailer instance
        $this->mail = new PHPMailer;
        //Tell PHPMailer to use SMTP
        $this->mail->isSMTP();
        //Enable SMTP debugging
        // 0 = off (for production use)
        // 1 = client messages
        // 2 = client and server messages
        $this->mail->SMTPDebug = 2;
        //Ask for HTML-friendly debug output
        $this->mail->Debugoutput = 'html';
        //Set the hostname of the mail server
        $this->mail->Host = $this->getConfig('host');
        //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
        $this->mail->Port = $this->getConfig('port');
        //Set the encryption system to use - ssl (deprecated) or tls
        $this->mail->SMTPSecure = 'tls';
        //Whether to use SMTP authentication
        $this->mail->SMTPAuth = true;
        //Username to use for SMTP authentication - use full email address for gmail
        $this->mail->Username = $this->getConfig('username');
        //Password to use for SMTP authentication
        $this->mail->Password = $this->getConfig('password');
        $api->logDebug(576, "mailer init");
        return $this;
    }
    
    public function sendMail($to, $subject = "", $body = "") {
        $api = Api::getInstance();
        //Set who the message is to be sent to
        $this->mail->addAddress($to);
        //Set the subject line
        $this->mail->Subject = $subject;        
        //Replace the plain text body with one created manually
        $this->mail->AltBody = $body;        
        //send the message, check for errors
        if (!$this->mail->send()) {
        
        $api->logWarn(572, "mailer plugin error when sending mess " , $this->mail->ErrorInfo);
        }
        else {
            $api->logDebug(576, "msg sent");
        }
    }
    
}

?>
