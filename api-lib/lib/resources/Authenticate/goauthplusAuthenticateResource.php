<?php

/**
 * This resource is used to authenticate using google, and obtaining access to google's services.
 *
 * @package  SXAPI.Resource.Authenticate
 * @author   Dev Team <dev@startx.fr>
 * @see      defaultAuthenticateResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class goauthplusAuthenticateResource extends goauthAuthenticateResource implements IResource {

    public function init() {
        parent::init();
//        $api = Api::getInstance();
//        $input = $api->getInput();
//        if ($this->getConfig('client_id') == '') {
//            $api->logError(906, get_class($this) . " resource config should contain the 'client_id' attribute", $this->getResourceTrace(__FUNCTION__, false));
//            throw new ResourceException(get_class($this) . " resource config should contain the 'client_id' attribute");
//        }
        $this->client->setRedirectUri('postmessage');
        if (is_null($this->getConfig('google_service')))
            $this->setConfig('google_service', "Plus");
        return $this;
    }
               
    public function readAction() {
        $api = Api::getInstance();
        $api->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        try {
            $input = $api->getInput();
            $sessElPosition = $input->getElementPosition($this->getConfig('path'));
            $nextPath = $input->getElement($sessElPosition + 1);
            $user = $api->getInput('user')->getAll();
            switch ($nextPath) {
                case 'close':
                    $token = $api->getInput("session")->get('user_goauth_token');
                    $this->client->revokeToken($token);
                    $token = json_decode($token);
                    $api->getInput("session")->clear('user_goauth_token');
                    $message = sprintf($this->getConfig('message_service_close', 'session %s is disconnected form google api using token %s'), session_id(), $token->access_token);
                    $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' disconnect " . $user['email'] . " from google api", $this->getResourceTrace(__FUNCTION__, false, array('user' => $user, 'answer' => $accessInfo)), 1);
                    return array(true, $message, true);
                    break;
                default:
                    $this->loadServices();
                    $token = $api->getInput("session")->get('user_goauth_token');
                    if (empty($token)) {
                        $code = file_get_contents('php://input');
                        $client = $this->client;
                        $client->authenticate($code);
                        $token = json_decode($client->getAccessToken());
                        $reqUrl = 'https://www.googleapis.com/oauth2/v1/tokeninfo?access_token=' . $token->access_token;
                        $req = new Google_HttpRequest($reqUrl);
                        $tokenInfo = json_decode($client::getIo()->authenticatedRequest($req)->getResponseBody());
                        // If there was an error in the token info, abort.
                        if ($tokenInfo->error) {
                            $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $tokenInfo->error, $tokenInfo);
                            return array(false, 910, $tokenInfo->error,array(),401);
                        } elseif ($tokenInfo->audience != $this->getConfig('client_id')) {
                            $response = 'Token\'s client ID does not match app\'s.';
                            $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $response, $tokenInfo);
                            return array(false, 910, $response,array(),401);
                        } else {



//                            if ($input->isParam('code')) {
//                                $this->client->setRedirectUri($input->getRootUrl() . $input->getPath());
//                                $this->loadServices();
//                                $this->client->authenticate($input->getParam('code'));
//                                $accessInfo = json_decode($this->client->getAccessToken());
//                                $user = $this->services['Oauth2']->userinfo->get();
//                                $user['email'] = filter_var($user['email'], FILTER_SANITIZE_EMAIL);
//                                $user['picture'] = filter_var($user['picture'], FILTER_VALIDATE_URL);
//                                $user['google_token'] = $accessInfo;
//                                $api->getInput("session")->set('user_goauth_token',json_encode($accessInfo));
//                                $api->getInput('session')->set('user', $user['email']);
//                                $api->getInput('user')->setAll($user, 'save');
//                                $message = sprintf($this->getConfig('message_service_read', 'user %s is now associated to session %s'), $user['email'], session_id());
//                                $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return user info for " . $user['email'], $this->getResourceTrace(__FUNCTION__, false, array('user' => $user, 'answer' => $accessInfo)), 1);
//                                return array(true, $message, $user, count($user));
//                            } else {
//                                switch ($_GET['error']) {
//                                    case "access_denied":
//                                        $message = "No user access from google because : " . $_GET['error'];
//                                        break;
//                                    default:
//                                        $message = "No user access from google because : " . $_GET['error'];
//                                        break;
//                                }
//                                $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $message, $exc);
//                                return array(false, 910, $message);
//                            }



                            $api->getInput("session")->set('user_goauth_token', json_encode($token));
                        }
                    } else {
                        $token = json_decode($token);
                    }
                    $message = sprintf($this->getConfig('message_service_read', 'session %s is associated to user %s and have a google access_token '), session_id(), $user['email'], $token->access_token);
                    $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return google access_token " . $token->access_token, $this->getResourceTrace(__FUNCTION__, false, array('user' => $user, 'answer' => $accessInfo)), 1);
                    return array(true, $message, $token);
            }
        } catch (Exception $exc) {
            $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            return array(false, $exc->getCode(), $exc->getMessage(),array(),401);
        }
        return true;
    }

}

?>
