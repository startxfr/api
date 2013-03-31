<?php

require_once 'api-lib/lib/plugins/google-api-php-client/src/Google_Client.php';
require_once 'api-lib/lib/plugins/google-api-php-client/src/contrib/Google_CalendarService.php';
require_once 'api-lib/lib/plugins/google-api-php-client/src/contrib/Google_Oauth2Service.php';
session_start();

$client = new Google_Client();
//$client->setApplicationName("Google Calendar PHP Starter Application");
$client->setClientId('703694493039.apps.googleusercontent.com');
$client->setClientSecret('ghpmYHB6pOTB5m1EBpaap2Ju');
$client->setRedirectUri('http://127.0.0.1/github/sxapi/oauth-google2.php');
$cal = new Google_CalendarService($client);
$oauth2 = new Google_Oauth2Service($client);


if (isset($_GET['logout'])) {
    unset($_SESSION['token']);
}


if (isset($_SESSION['token']))
    $client->setAccessToken($_SESSION['token']);

if ($client->getAccessToken()) {
    $user = $oauth2->userinfo->get();
    // These fields are currently filtered through the PHP sanitize filters.
    // See http://www.php.net/manual/en/filter.filters.sanitize.php
    $email = filter_var($user['email'], FILTER_SANITIZE_EMAIL);
    $img = filter_var($user['picture'], FILTER_VALIDATE_URL);
    print "$email<div><img src='$img?sz=50'></div>";
    $calList = $cal->calendarList->listCalendarList();
    print "<h1>Calendar List</h1>";
    foreach ($calList['items'] as $cal)
        echo '<div style="background-color:'.$cal['backgroundColor'].';color:'.$cal['foregroundColor'].';padding:0.33em">'.$cal['summary'].'</div><br/>';
    $_SESSION['token'] = $client->getAccessToken();
} else {
    $authUrl = $client->createAuthUrl();
    print "<a class='login' href='$authUrl'>Connect Me!</a>";
    print "<a class='login' href='http://127.0.0.1/github/sxapi/oauth-google.php?logout=true'>Logout</a>";
}
?>
