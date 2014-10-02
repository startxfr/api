<?php

INIT 
<--->
$client = new Google_Client();
$client->setApplicationName('<APPLICATION_NAME>'));
$client->setClientId('<YOUR_CLIENT_ID>');
$client->setClientSecret('<YOUR_CLIENT_SECRET>');
$client->setRedirectUri('<YOUR_REDIRECT_URI>');
$client->setScopes('<SCOPES>');
<--->

if (FIRST)
<--->
$authUrl = $client->createAuthUrl();
header('Location: ' . $authUrl);
<--->

if (CODE)
<--->
$client->authenticate($_GET['code']);
$_SESSION['access_token'] = $client->getAccessToken();
$redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
<--->

if (ACCESS_TOKEN)
<--->
$_SESSION['access_token'] = $client->getAccessToken();
$token_data = $client->verifyIdToken()->getAttributes();
<RE-AUTHENTICATE>
$client->setAccessToken($_SESSION['access_token']);
<--->
?>
